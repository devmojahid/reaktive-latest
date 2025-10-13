<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Http\Controllers\Front;

use App\Enums\Language;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\GlobalSetting\App\Models\GlobalSetting;
use Modules\TourBooking\App\Models\Amenity;
use Modules\TourBooking\App\Models\AmenityTranslation;
use Modules\TourBooking\App\Models\Availability;
use Modules\TourBooking\App\Models\Booking;
use Modules\TourBooking\App\Models\Destination;
use Modules\TourBooking\App\Models\PickupPoint;
use Modules\TourBooking\App\Models\Review;
use Modules\TourBooking\App\Models\Service;
use Modules\TourBooking\App\Models\ServiceType;
use Modules\TourBooking\App\Repositories\ServiceRepository;
use Modules\TourBooking\App\Repositories\ServiceTypeRepository;
use Modules\TourBooking\App\Models\TripType;

final class FrontServiceController extends Controller
{
    public function __construct(
        private ServiceRepository $serviceRepository,
        private ServiceTypeRepository $serviceTypeRepository,
    ) {}

    /**
     * NEW (canonical):
     * API: availability + prețuri pe categorii pentru o dată selectată.
     * Acceptă: service_id, date (Y-m-d) sau check_in_date (Y-m-d)
     */
    public function getAvailityByDate(Request $request)
{
    $request->validate([
        'service_id'    => 'required|integer|exists:services,id',
        'date'          => 'nullable|date_format:Y-m-d',
        'check_in_date' => 'nullable|date_format:Y-m-d',
    ]);

    $date = $request->input('date') ?? $request->input('check_in_date');
    if (!$date) {
        return response()->json([
            'success' => false,
            'message' => 'Missing date parameter.',
            'data'    => null,
        ], 422);
    }

    /** @var Service $service */
    $service = Service::with('availabilities')->findOrFail((int) $request->service_id);
    /** @var Availability|null $availability */
    $availability = $service->availabilities()
        ->whereDate('date', $date)
        ->first();

    // UNICA sursă de calcul a prețurilor (din model):
    $prices = $service->effectivePriceSetForDate($date);

    // Age groups active? (availability SAU service), indiferent dacă au price explicit sau moștenesc
    $svcCats = $service->normalizedAgeCategories();
    $avCats  = $availability?->normalizedAgeCategories() ?? [];
    $hasAgeGroups = collect([$avCats, $svcCats])
        ->flatMap(fn($arr) => $arr)
        ->contains(fn($cfg) => !empty($cfg['enabled']));

    // „Legacy” doar dacă nu avem age groups și există special/per_children pe availability
    $usesLegacy = !$hasAgeGroups && ($availability?->special_price !== null || $availability?->per_children_price !== null);

    // Sursa informativă (utile pentru debugging/UI)
    $source = $availability?->hasAnyAgeCategoryEnabled() ? 'availability'
            : (collect($svcCats)->contains(fn($c)=>!empty($c['enabled'])) ? 'service'
            : ($usesLegacy ? 'legacy' : 'base'));

    return response()->json([
        'success' => true,
        'data' => [
            'id'              => $availability?->id,
            'date'            => $date,
            'is_available'    => (bool) ($availability?->is_available ?? false),
            'available_spots' => $availability?->available_spots,
            'start_time'      => $availability?->start_time?->format('H:i'),
            'end_time'        => $availability?->end_time?->format('H:i'),
            'notes'           => $availability?->notes,
            'age_categories'  => $availability?->normalizedAgeCategories() ?? [],
            'prices'          => $prices,  // adult/child/baby/infant

            // compat vechi
            'special_price'      => $prices['adult'] ?? null,
            'per_children_price' => $prices['child'] ?? null,

            'has_age_groups'  => $hasAgeGroups,
            'uses_legacy'     => $usesLegacy,
            'source'          => $source,
        ],
    ]);
}


    /**
     * CANONICAL METHOD (for new API calls):
     * Get availability by date with proper method name.
     */
    public function getAvailabilityByDate(Request $request)
    {
        return $this->getAvailityByDate($request);
    }

    /**
     * LEGACY PROXY:
     * Păstrat pentru compatibilitate. Delegă spre metoda canonică.
     */
    

    /**
     * Calculează prețurile unitare pe categorii pentru o dată (Availability) cu fallback în Service.
     *
     * Reguli de prioritate:
     * 1) Dacă există cel puțin o categorie activă în Availability.age_categories (și are price numeric) → folosește acelea,
     *    pentru lipsuri completează din Service.age_categories, apoi din baza Service (price_per_person/discount/full).
     * 2) Altfel, dacă există cel puțin o categorie activă în Service.age_categories → folosește-le pe acelea
     *    și completezi lipsurile din baza Service.
     * 3) Dacă NU există niciun age-group activ (Availability sau Service),
     *    folosește LEGACY la nivel de Availability (special_price/adult, per_children_price/child) dacă există,
     *    altfel baza Service.
     *
     * @return array{prices: array<string,float>, has_age_groups: bool, uses_legacy: bool, source: string}
     */
    protected function computeAgeCategoryPrices(Service $service, ?Availability $availability): array
    {
        $keys = ['adult', 'child', 'baby', 'infant'];
        $out  = array_fill_keys($keys, null);

        // Normalize JSON -> array
        $svcCats = $this->parseAgeCategories($service->age_categories ?? null);
        $avCats  = $this->parseAgeCategories($availability->age_categories ?? null);

        // Flags pentru existență de age groups active
        $avHasActive  = $this->hasActiveAgeGroup($avCats);
        $svcHasActive = $this->hasActiveAgeGroup($svcCats);

        // Baza de preț din service (fallback final)
        $baseAdult  = $this->firstNumeric([$service->price_per_person, $service->discount_price, $service->full_price, 0]);
        $baseChild  = $this->firstNumeric([$service->child_price ?? null, $baseAdult]);
        $baseInfant = $this->firstNumeric([$service->infant_price ?? null, $baseAdult]);
        $baseMap    = ['adult' => $baseAdult, 'child' => $baseChild, 'baby' => $baseChild, 'infant' => $baseInfant];

        // Caz A: Availability age-groups (prioritar)
        if ($avHasActive) {
            foreach ($keys as $k) {
                if (!empty($avCats[$k]['enabled']) && $this->isNumeric($avCats[$k]['price'])) {
                    $out[$k] = (float)$avCats[$k]['price'];
                } elseif (!empty($svcCats[$k]['enabled']) && $this->isNumeric($svcCats[$k]['price'])) {
                    // completează cu service age-group dacă availability nu a setat prețul
                    $out[$k] = (float)$svcCats[$k]['price'];
                } else {
                    $out[$k] = (float)$baseMap[$k];
                }
            }
            return [
                'prices'        => $out,
                'has_age_groups'=> true,
                'uses_legacy'   => false,
                'source'        => 'availability',
            ];
        }

        // Caz B: Service age-groups
        if ($svcHasActive) {
            foreach ($keys as $k) {
                if (!empty($svcCats[$k]['enabled']) && $this->isNumeric($svcCats[$k]['price'])) {
                    $out[$k] = (float)$svcCats[$k]['price'];
                } else {
                    $out[$k] = (float)$baseMap[$k];
                }
            }
            return [
                'prices'        => $out,
                'has_age_groups'=> true,
                'uses_legacy'   => false,
                'source'        => 'service',
            ];
        }

        // Caz C: Legacy pe Availability (dacă există), altfel doar baza Service
        $legacyAdult = $availability?->special_price;
        $legacyChild = $availability?->per_children_price;

        $out['adult'] = $this->isNumeric($legacyAdult) ? (float)$legacyAdult : (float)$baseAdult;
        $out['child'] = $this->isNumeric($legacyChild) ? (float)$legacyChild : (float)$baseChild;
        // baby/infant nu au legacy separat → baza
        $out['baby']   = (float)$baseChild;
        $out['infant'] = (float)$baseInfant;

        $usesLegacy = $this->isNumeric($legacyAdult) || $this->isNumeric($legacyChild);

        return [
            'prices'        => $out,
            'has_age_groups'=> false,
            'uses_legacy'   => $usesLegacy,
            'source'        => $usesLegacy ? 'legacy' : 'base',
        ];
    }

    /**
     * Display the home page of the tour booking module.
     */
    public function index(): View
    {
        $featuredServices = Service::where('status', true)
            ->where('is_featured', true)
            ->with('thumbnail')
            ->take(8)
            ->get();

        $popularServices = Service::where('status', true)
            ->where('is_popular', true)
            ->with('thumbnail')
            ->take(8)
            ->get();

        $serviceTypes = ServiceType::where('status', true)
            ->with('thumbnail')
            ->take(6)
            ->get();

        $popularDestinations = Destination::where('status', true)
            ->where('is_popular', true)
            ->with('thumbnail')
            ->take(6)
            ->get();

        $latestReviews = Review::where('status', true)
            ->with(['service', 'user'])
            ->latest()
            ->take(6)
            ->get();

        return view('tourbooking::front.index', compact(
            'featuredServices',
            'popularServices',
            'serviceTypes',
            'popularDestinations',
            'latestReviews'
        ));
    }

    /**
     * Search for services.
     */
    public function search(Request $request): View
    {
        $query = Service::where('status', true)
            ->with(['thumbnail', 'serviceType', 'reviews']);

        // filters …
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('location', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('service_type')) {
            $query->where('service_type_id', $request->input('service_type'));
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%'.$request->input('location').'%');
        }

        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_price', '>=', $request->input('min_price'))
                    ->orWhere('discount_price', '>=', $request->input('min_price'))
                    ->orWhere('price_per_person', '>=', $request->input('min_price'));
            });
        }

        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_price', '<=', $request->input('max_price'))
                    ->orWhere('discount_price', '<=', $request->input('max_price'))
                    ->orWhere('price_per_person', '<=', $request->input('max_price'));
            });
        }

        // sort …
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_low': $query->orderBy('discount_price', 'asc'); break;
            case 'price_high': $query->orderBy('discount_price', 'desc'); break;
            case 'rating':
                $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'); break;
            case 'oldest': $query->oldest(); break;
            case 'newest':
            default: $query->latest(); break;
        }

        $services = $query->paginate(12)->withQueryString();

        $serviceTypes = ServiceType::where('status', true)->get();
        $destinations = Destination::where('status', true)->get();

        return view('tourbooking::front.search', compact('services', 'serviceTypes', 'destinations'));
    }

    public function serviceTypes(): View
    {
        $serviceTypes = ServiceType::where('status', true)
            ->with('thumbnail')
            ->paginate(15);

        return view('tourbooking::front.service-types', compact('serviceTypes'));
    }

    public function serviceTypeDetail(string $slug): View
    {
        $serviceType = ServiceType::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $services = Service::where('service_type_id', $serviceType->id)
            ->where('status', true)
            ->with(['thumbnail', 'reviews'])
            ->paginate(12);

        return view('tourbooking::front.service-type-detail', compact('serviceType', 'services'));
    }

    public function allServices(Request $request)
    {
        $selected_service_layout = GlobalSetting::where('key', 'booking_service_theme')?->first()?->value;
        $breadcrumb_title = trans('translate.All Services');
        $requestView = $request->view;

        if ($requestView == 'hotel_grid' || $selected_service_layout == 'hotel_grid') {
            $serviceView = 'tourbooking::front.services.services';
        } elseif ($requestView == 'tour_grid_one' || $selected_service_layout == 'tour_grid_one') {
            $serviceView = 'tourbooking::front.services.services2';
        } elseif ($requestView == 'tour_grid_two' || $selected_service_layout == 'tour_grid_two') {
            $serviceView = 'tourbooking::front.services.services3';
        } elseif ($requestView == 'hotel_listing' || $selected_service_layout == 'hotel_listing') {
            $serviceView = 'tourbooking::front.services.services4';
        } else {
            $serviceView = 'tourbooking::front.services.services';
        }

        $serviceTypes = $this->serviceTypeRepository->getActiveNameId();

        $amenities     = Amenity::where('status', true)->with('translation:id,amenity_id,lang_code,name')->get();
        $languages     = Language::cases();
        $destinations  = Destination::where('status', true)->get();
        $tripTypes     = TripType::withCount('services')->where('status', true)->get();

        return view($serviceView, compact('serviceTypes', 'amenities', 'languages', 'destinations', 'breadcrumb_title', 'tripTypes'));
    }

    public function loadServicesAjax(Request $request)
    {
        $isListView = $request->isListView;
        $style = $request->style;

        $allServices = Service::select('id', 'price_per_person', 'slug', 'location', 'is_featured', 'full_price', 'discount_price', 'is_new', 'duration', 'group_size')
            ->withExists('myWishlist')
            ->where('status', true)
            ->with(['thumbnail:id,service_id,caption,file_path', 'translation:id,service_id,locale,title,short_description'])
            ->withCount('activeReviews')
            ->withAvg('activeReviews', 'rating')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('translation', function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%");
                })
                ->orWhere('location', 'like', "%{$request->search}%");
            })
            ->when($request->filled('service_type_ids') && is_array($request->service_type_ids), function ($query) use ($request) {
                return $query->whereIn('service_type_id', $request->service_type_ids);
            })
            ->when($request->filled('service_type_id') && $request->service_type_id != 'Type', function ($query) use ($request) {
                return $query->where('service_type_id', $request->service_type_id);
            })
            ->when($request->filled('max_price'), function ($query) use ($request) {
                return $query->where('full_price', '<=', $request->max_price);
            })
            ->when($request->filled('min_price'), function ($query) use ($request) {
                return $query->where('full_price', '>=', $request->min_price);
            })
            ->when($request->filled('trip_id'), function ($query) use ($request) {
                return $query->whereHas('tripTypes', function ($q) use ($request) {
                    $q->where('trip_types.id', $request->trip_id);
                });
            })
            ->when($request->filled('amenity_ids') && is_array($request->amenity_ids), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    foreach ($request->amenity_ids as $amenityId) {
                        $q->orWhereJsonContains('amenities', $amenityId);
                    }
                });
            })
            ->when($request->filled('amenity_id') && $request->amenity_id != 'Amenities', function ($query) use ($request) {
                $query->whereJsonContains('amenities', $request->amenity_id);
            })
            ->when($request->filled('languages') && is_array($request->languages), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    foreach ($request->languages as $language) {
                        $q->orWhereJsonContains('languages', $language);
                    }
                });
            })
            ->when($request->filled('language') && $request->language != 'All Languages', function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->orWhereJsonContains('languages', $request->language);
                });
            })
            ->when($request->filled('destination_id'), function ($query) use ($request) {
                return $query->where('destination_id', $request->destination_id);
            })
            ->when($request->filled('ratings') && is_array($request->ratings), function ($query) use ($request) {
                $minRating = min($request->ratings);
                $query->having('active_reviews_avg_rating', '>=', $minRating);
            })
            ->when($request->filled('ratting') && $request->ratting != 'default', function ($query) use ($request) {
                $query->having('active_reviews_avg_rating', '>=', $request->ratting);
            })
            ->when($request->filled('sort_by'), function ($query) use ($request) {
                switch ($request->sort_by) {
                    case 'price_low':  $query->orderBy('price_per_person', 'asc'); break;
                    case 'price_high': $query->orderBy('price_per_person', 'desc'); break;
                    case 'trending':   $query->orderBy('is_featured', 'desc'); break;
                    case 'popular':    $query->orderBy('is_popular', 'desc'); break;
                    case 'latest':     $query->orderBy('created_at', 'desc'); break;
                    case 'oldest':     $query->orderBy('created_at', 'asc'); break;
                    case 'location_asc':  $query->orderBy('location', 'asc'); break;
                    case 'location_desc': $query->orderBy('location', 'desc'); break;
                    default: $query->orderBy('created_at', 'desc');
                }
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate(9);

        if ($style == 'style2') {
            $view = view('tourbooking::front.services.services-item2', compact('allServices', 'isListView'))->render();
        } elseif ($style == 'style3') {
            $view = view('tourbooking::front.services.services-item3', compact('allServices', 'isListView'))->render();
        } elseif ($style == 'style4') {
            $view = view('tourbooking::front.services.services-item4', compact('allServices', 'isListView'))->render();
        } else {
            $view = view('tourbooking::front.services.services-item', compact('allServices', 'isListView'))->render();
        }

        $customPaginationCount = customPaginationCount($allServices);

        return response()->json([
            'success' => true,
            'message' => 'Services loaded successfully',
            'view'    => $view,
            'customPaginationCount' => $customPaginationCount,
        ]);
    }

    /**
     * Detalii serviciu.
     */
    public function serviceDetail(Request $request, string $slug): View
    {
        $selected_service_layout = GlobalSetting::where('key', 'booking_service_detail_theme')?->first()?->value;
        $requestView = $request->view;

        if ($requestView == 'tour_detail_one' || $selected_service_layout == 'tour_detail_one') {
            $serviceView = 'tourbooking::front.services.service-detail';
        } elseif ($requestView == 'tour_detail_two' || $selected_service_layout == 'tour_detail_two') {
            $serviceView = 'tourbooking::front.services.service-detail2';
        } else {
            $serviceView = 'tourbooking::front.services.service-detail';
        }

        $service = Service::where('slug', $slug)
            ->where('status', true)
            ->with([
                'translation',
                'media:id,service_id,file_name,file_path,is_thumbnail',
                'serviceType:id,name',
                'extraCharges' => function ($query) { $query->where('status', true); },
                'availabilities',
                'itineraries' => function ($query) { $query->orderBy('day_number'); },
                'availabilitieByDate',
            ])
            ->withCount('activeReviews')
            ->withAvg('activeReviews', 'rating')
            ->withExists('myWishlist')
            ->firstOrFail();

        $amenities = [];
        if (is_array($service->amenities) && $service->amenities) {
            $amenities = AmenityTranslation::select('id', 'name')->whereIn('id', $service->amenities ?? [])->get();
        }

        $reviews = Review::select('id', 'service_id', 'rating_attributes')
            ->where('service_id', $service->id)
            ->where('status', true)
            ->get();

        $avgRating = Review::where('service_id', $service->id)
            ->where('status', true)
            ->avg('rating');

        $categories = [];
        foreach ($reviews as $review) {
            $attributes = $review->rating_attributes;
            if (!is_array($attributes)) continue;
            foreach ($attributes as $attr) {
                $category = $attr['category'];
                $rating   = (float) $attr['rating'];
                if (!isset($categories[$category])) $categories[$category] = ['total' => 0, 'count' => 0];
                $categories[$category]['total'] += $rating;
                $categories[$category]['count']++;
            }
        }

        $averageRatings = collect($categories)->map(function ($data, $category) {
            $avg = $data['total'] / max(1, $data['count']);
            return [
                'category' => $category,
                'average'  => round($avg, 1),
                'percent'  => round(($avg / 5) * 100),
            ];
        })->values()->toArray();

        $paginatedReviews = Review::where('service_id', $service->id)
            ->where('status', true)
            ->with('user:id,name,image')
            ->latest()
            ->paginate(14);

        $popularServices = $this->popularServices();

        // Prepare availability map for frontend
        $availabilityMap = [];
        foreach ($service->availabilities as $availability) {
            $date = $availability->date->format('Y-m-d');
            $availabilityMap[$date] = [
                'id' => $availability->id,
                'spots' => $availability->available_spots,
                'special_price' => $availability->special_price,
                'per_children_price' => $availability->per_children_price,
                'notes' => $availability->notes,
                'start_time' => $availability->start_time ? $availability->start_time->format('H:i') : null,
                'end_time' => $availability->end_time ? $availability->end_time->format('H:i') : null,
                'is_available' => (bool) $availability->is_available,
                'age_categories' => $availability->normalizedAgeCategories(),
            ];
        }

        return view($serviceView, compact(
            'service',
            'paginatedReviews',
            'averageRatings',
            'reviews',
            'avgRating',
            'popularServices',
            'amenities',
            'availabilityMap'
        ));
    }

    public function popularServices()
    {
        return Service::select('id', 'service_type_id', 'price_per_person', 'slug', 'location', 'is_featured', 'full_price', 'discount_price', 'is_new', 'duration', 'group_size')
            ->where('is_popular', true)
            ->withExists('myWishlist')
            ->where('status', true)
            ->with([
                'thumbnail:id,service_id,caption,file_path',
                'translation:id,service_id,locale,title,short_description',
            ])
            ->withCount('activeReviews')
            ->withAvg('activeReviews', 'rating')
            ->latest()
            ->take(6)
            ->get();
    }

    private function getServicesByType(string $type): View
    {
        $serviceType = ServiceType::where('slug', $type)->firstOrFail();

        $services = Service::where('service_type_id', $serviceType->id)
            ->where('status', true)
            ->with(['thumbnail', 'reviews'])
            ->latest()
            ->paginate(12);

        $title = ucfirst($type);

        return view('tourbooking::front.services-by-type', compact('services', 'serviceType', 'title'));
    }

    public function tours(): View   { return $this->getServicesByType('tours'); }
    public function hotels(): View  { return $this->getServicesByType('hotels'); }
    public function restaurants(): View { return $this->getServicesByType('restaurants'); }
    public function rentals(): View { return $this->getServicesByType('rentals'); }
    public function activities(): View { return $this->getServicesByType('activities'); }

    public function destinations(): View
    {
        $destinations = Destination::where('status', true)
            ->with('thumbnail')
            ->paginate(12);

        return view('tourbooking::front.destinations', compact('destinations'));
    }

    public function destinationDetail(string $slug): View
    {
        $destination = Destination::where('slug', $slug)
            ->where('status', true)
            ->with('thumbnail')
            ->firstOrFail();

        $services = Service::where('status', true)
            ->where('location', 'like', "%{$destination->name}%")
            ->with(['thumbnail', 'serviceType', 'reviews'])
            ->paginate(12);

        return view('tourbooking::front.destination-detail', compact('destination', 'services'));
    }

    public function storeReview(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'You must be logged in to submit a review.']);
        }

        $existingBooking = Booking::where('service_id', $request->service_id)
            ->where('user_id', Auth::id())
            ->where('booking_status', 'confirmed')
            ->first();

        if (!$existingBooking) {
            return response()->json(['success' => false, 'message' => 'You must have a completed booking to submit a review.']);
        }

        if (Review::where('service_id', $request->service_id)->where('user_id', Auth::id())->exists()) {
            return response()->json(['success' => false, 'message' => 'You have already submitted a review for this service.']);
        }

        if (count($request->ratings) != 5) {
            return response()->json(['success' => false, 'message' => 'You must rate all categories.']);
        }

        $request->validate([
            'message' => 'required|string',
            'ratings' => 'required|array',
            'ratings.*.category' => 'required|string',
            'ratings.*.rating'   => 'required|numeric|min:0|max:5',
        ]);

        $allRating = 0.0;
        foreach ($request->ratings as $rating) {
            $allRating += (float)$rating['rating'];
        }

        Review::create([
            'service_id' => $request->service_id,
            'user_id'    => Auth::id(),
            'booking_id' => null,
            'review'     => $request->message,
            'rating'     => $allRating / 5,
            'rating_attributes' => $request->ratings,
            'status'     => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Your review has been submitted and is pending approval.']);
    }

    /* =======================
     * Helpers (age groups, time formatting)
     * ======================= */

    /**
     * Acceptă JSON string/array/null și normalizează în shape:
     * ['adult'=>['enabled'=>bool,'price'=>?float,'min_age'=>?int,'max_age'=>?int,'count'=>int], ...]
     */
    private function parseAgeCategories($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            } else {
                $value = [];
            }
        } elseif (!is_array($value)) {
            $value = [];
        }

        $keys = ['adult','child','baby','infant'];
        $out  = [];
        foreach ($keys as $k) {
            $row = $value[$k] ?? [];
            $out[$k] = [
                'enabled' => (bool)($row['enabled'] ?? false),
                'price'   => $row['price'] ?? null,
                'min_age' => $row['min_age'] ?? null,
                'max_age' => $row['max_age'] ?? null,
                'count'   => $row['count']   ?? 0,
            ];
        }
        return $out;
    }

    private function hasActiveAgeGroup(array $cats): bool
    {
        foreach ($cats as $row) {
            if (!empty($row['enabled']) && $this->isNumeric($row['price'])) {
                return true;
            }
        }
        return false;
    }

    private function isNumeric($v): bool
    {
        return $v !== null && $v !== '' && is_numeric($v);
    }

    private function firstNumeric(array $candidates): float
    {
        foreach ($candidates as $c) {
            if ($this->isNumeric($c)) return (float)$c;
        }
        return 0.0;
    }

    private function formatTimeNullable($time): ?string
    {
        if (!$time) return null;
        if ($time instanceof \DateTimeInterface) {
            return $time->format('H:i');
        }
        // string "HH:MM:SS" -> "HH:MM"
        if (is_string($time) && preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time)) {
            return substr($time, 0, 5);
        }
        return (string)$time; // fallback
    }

    /**
     * Get pickup points for a service (API endpoint for frontend)
     */
    public function getPickupPoints(Request $request)
    {
        $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'user_lat'   => 'nullable|numeric|between:-90,90',
            'user_lng'   => 'nullable|numeric|between:-180,180',
        ]);

        $service = Service::findOrFail($request->service_id);
        
        $pickupPoints = $service->activePickupPoints()
            ->select('id', 'name', 'description', 'address', 'latitude', 'longitude', 'extra_charge', 'charge_type', 'is_default')
            ->get();

        // Calculate distances if user location provided
        if ($request->user_lat && $request->user_lng) {
            $userLat = (float) $request->user_lat;
            $userLng = (float) $request->user_lng;

            $pickupPoints = $pickupPoints->map(function ($pickup) use ($userLat, $userLng) {
                $pickup->distance = $pickup->distanceFrom($userLat, $userLng);
                return $pickup;
            })->sortBy('distance');
        }

        $response = $pickupPoints->map(function ($pickup) {
            return [
                'id'           => $pickup->id,
                'name'         => $pickup->name,
                'description'  => $pickup->description,
                'address'      => $pickup->address,
                'coordinates'  => $pickup->coordinates,
                'extra_charge' => $pickup->extra_charge,
                'charge_type'  => $pickup->charge_type,
                'formatted_charge' => $pickup->hasExtraCharge() ? 
                    currency($pickup->extra_charge) . ($pickup->charge_type !== 'flat' ? ' / ' . ucfirst(str_replace('per_', '', $pickup->charge_type)) : '') : 
                    'Free',
                'is_default'   => $pickup->is_default,
                'distance'     => $pickup->distance ?? null,
                'has_charge'   => $pickup->hasExtraCharge(),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $response,
        ]);
    }

    /**
     * Calculate pickup point extra charge
     */
    public function calculatePickupCharge(Request $request)
    {
        $request->validate([
            'pickup_point_id' => 'required|integer|exists:pickup_points,id',
            'age_quantities'  => 'required|array',
            'age_quantities.adult'  => 'integer|min:0',
            'age_quantities.child'  => 'integer|min:0',
            'age_quantities.baby'   => 'integer|min:0',
            'age_quantities.infant' => 'integer|min:0',
        ]);

        $pickupPoint = PickupPoint::findOrFail($request->pickup_point_id);
        $quantities = $request->age_quantities;

        $extraCharge = $pickupPoint->calculateExtraCharge($quantities);

        return response()->json([
            'success'      => true,
            'extra_charge' => $extraCharge,
            'formatted'    => currency($extraCharge),
            'pickup_name'  => $pickupPoint->name,
            'charge_type'  => $pickupPoint->charge_type,
        ]);
    }
}
