<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Http\Controllers\Admin;

use App\Enums\Language as EnumsLanguage;
use App\Helpers\FileUploadHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Language\App\Models\Language;
use Modules\TourBooking\App\Http\Requests\ServiceRequest;
use Modules\TourBooking\App\Models\Amenity;
use Modules\TourBooking\App\Models\Availability;
use Modules\TourBooking\App\Models\Destination;
use Modules\TourBooking\App\Models\ExtraCharge;
use Modules\TourBooking\App\Models\PickupPoint;
use Modules\TourBooking\App\Models\Review;
use Modules\TourBooking\App\Models\Service;
use Modules\TourBooking\App\Models\ServiceMedia;
use Modules\TourBooking\App\Models\ServiceTranslation;
use Modules\TourBooking\App\Models\ServiceType;
use Modules\TourBooking\App\Models\TourItinerary;
use Modules\TourBooking\App\Models\TripType;
use Modules\TourBooking\App\Repositories\ServiceRepository;
use Modules\TourBooking\App\Repositories\ServiceTypeRepository;

final class ServiceController extends Controller
{
    public function __construct(
        private ServiceRepository $serviceRepository,
        private ServiceTypeRepository $serviceTypeRepository
    ) {}

    /** List */
    public function index(): View
    {
        $services = $this->serviceRepository->getAllFilters();
        return view('tourbooking::admin.services.index', compact('services'));
    }

    /** Create form */
    public function create(): View
    {
        $amenities      = Amenity::where('status', true)->with('translation:id,amenity_id,lang_code,name')->get();
        $serviceTypes   = $this->serviceTypeRepository->getActive();
        $enum_languages = EnumsLanguage::cases();
        $destinations   = Destination::select('id', 'name')->where('status', true)->get();
        $tripTypes      = TripType::select('id', 'name')->where('status', true)->get();

        return view('tourbooking::admin.services.create', compact(
            'serviceTypes', 'amenities', 'enum_languages', 'destinations', 'tripTypes'
        ));
    }

    /** Store */
    public function store(ServiceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // === Age categories (normalize + validate) ===
        $data['age_categories'] = $this->normalizeAgeCategories($request->validated()['age_categories'] ?? []);

        // JSON fields
        foreach (['included','excluded','facilities','rules','safety','social_links'] as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = json_encode($data[$field]);
            }
        }

        // Booleans
        foreach (['deposit_required','is_featured','is_popular','show_on_homepage','status','is_new','is_per_person'] as $field) {
            $data[$field] = isset($data[$field]) ? true : false;
        }

        // Slug implicit
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $service = $this->serviceRepository->create($data);

        // Translation curent-lang
        $this->serviceRepository->saveTranslation($service, admin_lang(), [
            'title'               => $data['title'],
            'description'         => $data['description'] ?? null,
            'short_description'   => $data['short_description'] ?? null,
            'seo_title'           => $data['seo_title'] ?? null,
            'seo_description'     => $data['seo_description'] ?? null,
            'seo_keywords'        => $data['seo_keywords'] ?? null,
            'included'            => $data['included'] ?? null,
            'excluded'            => $data['excluded'] ?? null,
            'amenities'           => $data['amenities'] ?? [],
            'facilities'          => $data['facilities'] ?? null,
            'rules'               => $data['rules'] ?? null,
            'safety'              => $data['safety'] ?? null,
            'cancellation_policy' => $data['cancellation_policy'] ?? null,
        ]);

        $service->tripTypes()->sync($data['trip_types'] ?? []);

        return redirect()
            ->route('admin.tourbooking.services.edit', ['service' => $service->id, 'lang_code' => admin_lang()])
            ->with(['message' => trans('translate.Created successfully'), 'alert-type' => 'success']);
    }

    /** Show */
    public function show(Service $service): View
    {
        $service->load(['translation','serviceType','media','extraCharges','availabilities','itineraries']);
        return view('tourbooking::admin.services.show', compact('service'));
    }

    /** Edit form */
    public function edit(Request $request, Service $service): View
    {
        $lang_code = $request->lang_code ?? admin_lang();

        $service->load([
            'media',
            'serviceType',
            'extraCharges',
            'availabilities',
            'itineraries' => fn($q) => $q->orderBy('day_number'),
            'tripTypes',
        ]);

        $translation = ServiceTranslation::where([
            'service_id' => $service->id,
            'locale'     => $lang_code
        ])->first();

        // Textarea friendly pentru câmpurile JSON
        foreach (['included','excluded','facilities','rules','safety'] as $field) {
            $value = $translation->$field ?? $service->$field ?? null;
            if (!$value) continue;

            if (is_array($value)) {
                if ($translation && isset($translation->$field)) $translation->$field = implode("\n", $value);
                else $service->$field = implode("\n", $value);
            } elseif (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    if ($translation && isset($translation->$field)) $translation->$field = implode("\n", $decoded);
                    else $service->$field = implode("\n", $decoded);
                }
            }
        }

        $serviceTypes    = $this->serviceTypeRepository->getActive();
        $amenities       = Amenity::where('status', true)->with('translation:id,amenity_id,lang_code,name')->get();
        $enum_languages  = EnumsLanguage::cases();
        $destinations    = Destination::select('id','name')->where('status', true)->get();
        $tripTypes       = TripType::select('id','name')->where('status', true)->get();
        $selectedTripIds = $service->tripTypes->pluck('id')->toArray() ?? [];

        return view('tourbooking::admin.services.edit', compact(
            'service','serviceTypes','translation','lang_code','amenities','enum_languages','destinations','tripTypes','selectedTripIds'
        ));
    }

    /** Update */
    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $data      = $request->validated();
        $lang_code = $request->lang_code ?? admin_lang();

        if ($lang_code === admin_lang()) {
            // Age categories
            $data['age_categories'] = $this->normalizeAgeCategories($request->validated()['age_categories'] ?? []);

            // JSON fields
            foreach (['included','excluded','facilities','rules','safety','social_links'] as $field) {
                if (!isset($data[$field])) continue;

                if (is_string($data[$field])) {
                    $decoded = json_decode($data[$field], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $lines = array_filter(array_map('trim', explode("\n", $data[$field])), fn($l) => $l !== '');
                        $data[$field] = json_encode(array_values($lines));
                    }
                } elseif (is_array($data[$field])) {
                    $data[$field] = json_encode($data[$field]);
                }
            }

            // Booleans
            foreach (['deposit_required','is_featured','is_popular','show_on_homepage','status','is_new','is_per_person'] as $field) {
                $data[$field] = isset($data[$field]) ? true : false;
            }

            $data['languages'] = $request->languages ?? [];

            $this->serviceRepository->update($service, $data);
        }

        // Translation
        $translationData = [
            'title'             => $data['title'],
            'description'       => $data['description'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'seo_title'         => $data['seo_title'] ?? null,
            'seo_description'   => $data['seo_description'] ?? null,
            'seo_keywords'      => $data['seo_keywords'] ?? null,
        ];

        foreach (['included','excluded','facilities','rules','safety','cancellation_policy'] as $field) {
            if (!isset($data[$field])) continue;

            if (is_string($data[$field])) {
                $decoded = json_decode($data[$field], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $lines = array_filter(array_map('trim', explode("\n", $data[$field])), fn($l) => $l !== '');
                    $translationData[$field] = json_encode(array_values($lines));
                } else {
                    $translationData[$field] = $data[$field];
                }
            } else {
                $translationData[$field] = $data[$field];
            }
        }

        $translationData['amenities'] = $request->amenities ?? [];

        $this->serviceRepository->saveTranslation($service, $lang_code, $translationData);
        $service->tripTypes()->sync($data['trip_types'] ?? []);

        return back()->with(['message' => trans('translate.Updated successfully'), 'alert-type' => 'success']);
    }

    /** Destroy */
    public function destroy(Service $service): RedirectResponse
    {
        if ($service->bookings()->count() > 0) {
            return back()->with('error', trans('translate.Service has bookings. Cannot delete'));
        }

        foreach ($service->media as $media) {
            FileUploadHelper::deleteImage($media->file_path);
            $media->delete();
        }

        $this->serviceRepository->delete($service);

        return redirect()
            ->route('admin.tourbooking.services.index')
            ->with(['message' => trans('translate.Deleted successfully'), 'alert-type' => 'success']);
    }

    /** Media: list */
    public function showMedia(Service $service): View
    {
        $service->load(['media' => fn($q) => $q->orderBy('display_order')->orderBy('created_at', 'desc')]);
        return view('tourbooking::admin.services.media', compact('service'));
    }

    /** Media: upload */
    public function storeMedia(Request $request, Service $service): RedirectResponse
    {
        $request->validate([
            'file'    => 'required|file|mimes:jpeg,png,jpg,gif,webp,mp4,avi,mov|max:10240',
            'caption' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $fileType = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
            $fileName = $file->getClientOriginalName();
            $filePath = FileUploadHelper::uploadImage($file, $service->slug);
        }

        $isThumbnail = $service->media()->count() === 0;

        ServiceMedia::create([
            'service_id'    => $service->id,
            'file_path'     => $filePath ?? null,
            'file_type'     => $fileType ?? null,
            'file_name'     => $fileName ?? null,
            'caption'       => $request->caption,
            'is_featured'   => $isThumbnail,
            'is_thumbnail'  => $isThumbnail,
            'display_order' => $service->media()->count() + 1,
        ]);

        return back()->with(['message' => trans('translate.Media uploaded successfully'), 'alert-type' => 'success']);
    }

    /** Media: delete */
    public function deleteMedia(ServiceMedia $media): RedirectResponse
    {
        $serviceId = $media->service_id;

        if ($media->is_thumbnail) {
            $newThumb = ServiceMedia::where('service_id', $serviceId)
                ->where('id', '!=', $media->id)
                ->where('file_type', 'image')
                ->first();
            if ($newThumb) $newThumb->update(['is_thumbnail' => true]);
        }

        if ($media?->file_path) {
            FileUploadHelper::deleteImage($media->file_path);
        }

        $media->delete();

        return back()->with(['message' => trans('translate.Media deleted successfully'), 'alert-type' => 'success']);
    }

    /** Media: set thumbnail */
    public function setThumbnail(ServiceMedia $media): RedirectResponse
    {
        if ($media->file_type !== 'image') {
            return back()->with(['message' => trans('translate.Only images can be set as thumbnails'), 'alert-type' => 'error']);
        }

        ServiceMedia::where('service_id', $media->service_id)->update(['is_thumbnail' => false]);
        $media->update(['is_thumbnail' => true]);

        return back()->with(['message' => trans('translate.Thumbnail set successfully'), 'alert-type' => 'success']);
    }

    /** Itineraries: list */
    public function showItineraries(Service $service): View
    {
        $service->load(['itineraries' => fn($q) => $q->orderBy('day_number')]);
        return view('tourbooking::admin.services.itineraries', compact('service'));
    }

    /** Itineraries: store */
    public function storeItinerary(Request $request, Service $service): RedirectResponse
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'day_number'    => 'required|integer|min:1',
            'description'   => 'required|string',
            'location'      => 'nullable|string|max:255',
            'duration'      => 'nullable|string|max:255',
            'meal_included' => 'nullable|string|max:255',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->except('_token', 'image');

        if ($request->hasFile('image')) {
            $data['image'] = FileUploadHelper::uploadImage($request->file('image'), 'itinerary');
        }

        $data['display_order'] = $data['display_order'] ?? ($service->itineraries()->count() + 1);
        $data['service_id']    = $service->id;

        TourItinerary::create($data);

        return back()->with(['message' => trans('translate.Itinerary added successfully'), 'alert-type' => 'success']);
    }

    /** Itineraries: update */
    public function updateItinerary(Request $request, TourItinerary $itinerary): RedirectResponse
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'day_number'    => 'required|integer|min:1',
            'description'   => 'required|string',
            'location'      => 'nullable|string|max:255',
            'duration'      => 'nullable|string|max:255',
            'meal_included' => 'nullable|string|max:255',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->except('_token', '_method', 'image');

        if ($request->hasFile('image')) {
            if ($itinerary?->image) FileUploadHelper::deleteImage($itinerary?->image);
            $data['image'] = FileUploadHelper::uploadImage($request->file('image'), 'itinerary');
        }

        $itinerary->update($data);

        return back()->with(['message' => trans('translate.Itinerary updated successfully'), 'alert-type' => 'success']);
    }

    /** Itineraries: delete */
    public function deleteItinerary(TourItinerary $itinerary): RedirectResponse
    {
        if ($itinerary?->file_path) FileUploadHelper::deleteImage($itinerary->file_path);
        $itinerary->delete();

        return back()->with(['message' => trans('translate.Itinerary deleted successfully'), 'alert-type' => 'success']);
    }

    /** Extra charges: list */
    public function showExtraCharges(Service $service): View
    {
        $service->load('extraCharges');
        return view('tourbooking::admin.services.extra_charges', compact('service'));
    }

    /** Extra charges: store */
    public function storeExtraCharge(Request $request, Service $service): RedirectResponse
    {
        $request->merge([
            'is_mandatory' => $request->boolean('is_mandatory'),
            'is_tax'       => $request->boolean('is_tax'),
            'status'       => $request->boolean('status'),
        ]);

        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'price_type'     => 'required|in:per_booking,per_person,per_adult,per_child,per_infant,per_night,flat',
            'is_mandatory'   => 'boolean',
            'is_tax'         => 'boolean',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'max_quantity'   => 'nullable|integer|min:1',
            'status'         => 'boolean',
        ]);

        $data = $request->all();
        $data['service_id'] = $service->id;

        ExtraCharge::create($data);

        return back()->with(['message' => trans('translate.Extra charge added successfully'), 'alert-type' => 'success']);
    }

    /** Extra charges: update */
    public function updateExtraCharge(Request $request, ExtraCharge $charge): RedirectResponse
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'price_type'     => 'required|in:per_booking,per_person,per_adult,per_child,per_infant,per_night,flat',
            'is_mandatory'   => 'boolean',
            'is_tax'         => 'boolean',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'max_quantity'   => 'nullable|integer|min:1',
            'status'         => 'boolean',
        ]);

        $data = $request->all();
        $data['is_mandatory'] = $request->input('is_mandatory') == '1' || $request->input('is_mandatory') === true;
        $data['is_tax']       = $request->input('is_tax') == '1' || $request->input('is_tax') === true;
        $data['status']       = $request->input('status') == '1' || $request->input('status') === true;

        $charge->update($data);

        return back()->with(['message' => trans('translate.Extra charge updated successfully'), 'alert-type' => 'success']);
    }

    /** Extra charges: delete */
    public function deleteExtraCharge(ExtraCharge $charge): RedirectResponse
    {
        $charge->delete();
        return back()->with(['message' => trans('translate.Extra charge deleted successfully'), 'alert-type' => 'success']);
    }

    /** Availability: list */
    public function showAvailability(Service $service): View
    {
        $service->load('availabilities');
        return view('tourbooking::admin.services.availability', compact('service'));
    }

    /** Availability: store (single / bulk) */
    public function storeAvailability(Request $request, Service $service): RedirectResponse|JsonResponse
    {
        // Bulk
        if ($request->has('bulk') && $request->has('dates')) {
            $request->validate([
                'dates'              => 'required|array',
                'dates.*'            => 'required|date',
                'start_time'         => 'nullable|date_format:H:i',
                'end_time'           => 'nullable|date_format:H:i|after_or_equal:start_time',
                'available_spots'    => 'nullable|integer|min:1',
                'special_price'      => 'nullable|numeric|min:0',
                'per_children_price' => 'nullable|numeric|min:0',
                'is_available'       => 'boolean',
                'notes'              => 'nullable|string',
                // age_categories NU e folosit pe bulk în acest controller
            ]);

            $success = 0; $exists = 0;

            foreach ($request->dates as $date) {
                $already = Availability::where('service_id', $service->id)
                    ->where('date', $date)
                    ->where('start_time', $request->start_time ? date('H:i:s', strtotime($request->start_time)) : null)
                    ->first();

                if ($already) { $exists++; continue; }

                Availability::create([
                    'service_id'         => $service->id,
                    'date'               => $date,
                    'start_time'         => $request->start_time,
                    'end_time'           => $request->end_time,
                    'is_available'       => $request->has('is_available'),
                    'available_spots'    => $request->available_spots,
                    'special_price'      => $request->special_price,
                    'per_children_price' => $request->per_children_price,
                    'notes'              => $request->notes,
                ]);

                $success++;
            }

            $msg = $exists > 0
                ? trans('translate.Created :success availabilities. :error already existed.', ['success' => $success, 'error' => $exists])
                : trans('translate.Created :count availabilities successfully', ['count' => $success]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }

            return back()->with(['message' => $msg, 'alert-type' => ($success > 0 ? 'success' : 'error')]);
        }

        // Single
        $request->validate([
            'date'               => 'required|date',
            'start_time'         => 'nullable|date_format:H:i',
            'end_time'           => 'nullable|date_format:H:i|after_or_equal:start_time',
            'is_available'       => 'boolean',
            'available_spots'    => 'nullable|integer|min:1',
            'special_price'      => 'nullable|numeric|min:0',
            'per_children_price' => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',

            // NEW (opțional, dacă trimiți din form):
            'age_categories'                 => 'nullable|array',
            'age_categories.adult.enabled'   => 'nullable',
            'age_categories.adult.count'     => 'nullable|integer|min:0',
            'age_categories.adult.price'     => 'nullable|numeric|min:0',
            'age_categories.child.enabled'   => 'nullable',
            'age_categories.child.count'     => 'nullable|integer|min:0',
            'age_categories.child.price'     => 'nullable|numeric|min:0',
            'age_categories.baby.enabled'    => 'nullable',
            'age_categories.baby.count'      => 'nullable|integer|min:0',
            'age_categories.baby.price'      => 'nullable|numeric|min:0',
            'age_categories.infant.enabled'  => 'nullable',
            'age_categories.infant.count'    => 'nullable|integer|min:0',
            'age_categories.infant.price'    => 'nullable|numeric|min:0',
        ]);

        $existing = Availability::where('service_id', $service->id)
            ->where('date', $request->date)
            ->where('start_time', $request->start_time ? date('H:i:s', strtotime($request->start_time)) : null)
            ->first();

        if ($existing) {
            return back()->with(['message' => trans('translate.Availability already exists for this date'), 'alert-type' => 'error']);
        }

        $payload = [
            'service_id'         => $service->id,
            'date'               => $request->date,
            'start_time'         => $request->start_time,
            'end_time'           => $request->end_time,
            'is_available'       => $request->has('is_available'),
            'available_spots'    => $request->available_spots,
            'special_price'      => $request->special_price,
            'per_children_price' => $request->per_children_price,
            'notes'              => $request->notes,
        ];

        // NEW: age_categories per-zi (dacă e trimis din form)
        if ($request->has('age_categories')) {
            $payload['age_categories'] = $this->normalizeAvailabilityAgeCategories(
                $request['age_categories'] ?? []
            );
        }

        Availability::create($payload);

        return back()->with(['message' => trans('translate.Availability added successfully'), 'alert-type' => 'success']);
    }

    /** Availability: update */
    public function updateAvailability(Request $request, Availability $availability): RedirectResponse
    {
        $request->validate([
            // 'date'            => 'required|date',
            'start_time'         => 'nullable|date_format:H:i',
            'end_time'           => 'nullable|date_format:H:i|after_or_equal:start_time',
            'is_available'       => 'boolean',
            'available_spots'    => 'nullable|integer|min:1',
            'special_price'      => 'nullable|numeric|min:0',
            'per_children_price' => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',

            // NEW (opțional, dacă trimiți din form):
            'age_categories'                 => 'nullable|array',
            'age_categories.adult.enabled'   => 'nullable',
            'age_categories.adult.count'     => 'nullable|integer|min:0',
            'age_categories.adult.price'     => 'nullable|numeric|min:0',
            'age_categories.child.enabled'   => 'nullable',
            'age_categories.child.count'     => 'nullable|integer|min:0',
            'age_categories.child.price'     => 'nullable|numeric|min:0',
            'age_categories.baby.enabled'    => 'nullable',
            'age_categories.baby.count'      => 'nullable|integer|min:0',
            'age_categories.baby.price'      => 'nullable|numeric|min:0',
            'age_categories.infant.enabled'  => 'nullable',
            'age_categories.infant.count'    => 'nullable|integer|min:0',
            'age_categories.infant.price'    => 'nullable|numeric|min:0',
        ]);

        $exists = Availability::where('service_id', $availability->service_id)
            ->where('date', $request->date)
            ->where('start_time', $request->start_time ?? null)
            ->where('id', '!=', $availability->id)
            ->first();

        if ($exists) {
            return back()->with(['message' => trans('translate.Availability already exists for this date'), 'alert-type' => 'error']);
        }

        // folosim payload explicit (nu $request->all()) ca să nu “scape” câmpuri nepermise
        $data = [
            // 'date'            => $request->date, // dacă permiți editarea datei, decomentează
            'start_time'         => $request->start_time,
            'end_time'           => $request->end_time,
            'is_available'       => $request->has('is_available'),
            'available_spots'    => $request->available_spots,
            'special_price'      => $request->special_price,
            'per_children_price' => $request->per_children_price,
            'notes'              => $request->notes,
        ];

        // NEW: age_categories per-zi (dacă e trimis din form)
        if ($request->has('age_categories')) {
            $data['age_categories'] = $this->normalizeAvailabilityAgeCategories(
                $request['age_categories'] ?? []
            );
        }

        $availability->update($data);

        return back()->with(['message' => trans('translate.Availability updated successfully'), 'alert-type' => 'success']);
    }

    /** Availability: delete */
    public function deleteAvailability(Availability $availability): RedirectResponse
    {
        $availability->delete();
        return back()->with(['message' => trans('translate.Availability deleted successfully'), 'alert-type' => 'success']);
    }

    /** Pickup Points: list */
    public function showPickupPoints(Service $service): View
    {
        $service->load('pickupPoints');
        return view('tourbooking::admin.services.pickup_points', compact('service'));
    }

    /** Pickup Points: store */
    public function storePickupPoint(Request $request, Service $service): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'address'     => 'required|string|max:500',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'extra_charge'=> 'nullable|numeric|min:0',
            'charge_type' => 'required|in:flat,per_person,per_adult,per_child',
            'is_default'  => 'boolean',
            'status'      => 'boolean',
            'notes'       => 'nullable|string',
        ]);

        // Ensure only one default pickup point per service
        if ($request->has('is_default') && $request->is_default) {
            PickupPoint::where('service_id', $service->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        PickupPoint::create([
            'service_id'   => $service->id,
            'name'         => $request->name,
            'description'  => $request->description,
            'address'      => $request->address,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'extra_charge' => $request->extra_charge,
            'charge_type'  => $request->charge_type,
            'is_default'   => $request->has('is_default'),
            'status'       => $request->has('status'),
            'notes'        => $request->notes,
        ]);

        return back()->with(['message' => trans('translate.Pickup point added successfully'), 'alert-type' => 'success']);
    }

    /** Pickup Points: update */
    public function updatePickupPoint(Request $request, PickupPoint $pickupPoint): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'address'     => 'required|string|max:500',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'extra_charge'=> 'nullable|numeric|min:0',
            'charge_type' => 'required|in:flat,per_person,per_adult,per_child',
            'is_default'  => 'boolean',
            'status'      => 'boolean',
            'notes'       => 'nullable|string',
        ]);

        // Ensure only one default pickup point per service
        if ($request->has('is_default') && $request->is_default) {
            PickupPoint::where('service_id', $pickupPoint->service_id)
                ->where('id', '!=', $pickupPoint->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $pickupPoint->update([
            'name'         => $request->name,
            'description'  => $request->description,
            'address'      => $request->address,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'extra_charge' => $request->extra_charge,
            'charge_type'  => $request->charge_type,
            'is_default'   => $request->has('is_default'),
            'status'       => $request->has('status'),
            'notes'        => $request->notes,
        ]);

        return back()->with(['message' => trans('translate.Pickup point updated successfully'), 'alert-type' => 'success']);
    }

    /** Pickup Points: delete */
    public function deletePickupPoint(PickupPoint $pickupPoint): RedirectResponse
    {
        $pickupPoint->delete();
        return back()->with(['message' => trans('translate.Pickup point deleted successfully'), 'alert-type' => 'success']);
    }

    /** Filter by type helpers */
    public function getByType(string $type): View
    {
        $serviceType = ServiceType::where('slug', $type)->firstOrFail();
        $services    = $this->serviceRepository->getByType($serviceType->id, 0);
        return view('tourbooking::admin.service_types.show', compact('serviceType', 'services'));
    }
    public function tours(): View { return $this->getByType('tours'); }
    public function hotels(): View { return $this->getByType('hotels'); }
    public function restaurants(): View { return $this->getByType('restaurants'); }
    public function rentals(): View { return $this->getByType('rentals'); }
    public function activities(): View { return $this->getByType('activities'); }

    /** Reviews (admin) */
    public function review_list(): View
    {
        $reviews = Review::with('service')->latest()->get();
        return view('tourbooking::admin.review.index', ['reviews' => $reviews]);
    }
    public function review_detail($id): View
    {
        $review = Review::with('service')->findOrFail($id);
        return view('tourbooking::admin.review.details', ['review' => $review]);
    }
    public function review_delete($id): RedirectResponse
    {
        Review::findOrFail($id)->delete();
        return redirect()->route('admin.tourbooking.reviews.index')->with('success', 'Review deleted successfully');
    }
    public function review_approve($id): RedirectResponse
    {
        Review::findOrFail($id)->update(['status' => 1]);
        return redirect()->route('admin.tourbooking.reviews.index')->with('success', 'Review approved successfully');
    }

    /**
     * Normalizează inputul age_categories (infant/baby/child/adult),
     * validează coerent min/max & non-suprapunere și întoarce array pentru DB.
     */
    private function normalizeAgeCategories(array $input): array
    {
        $keys = ['infant','baby','child','adult'];
        $out  = [];

        foreach ($keys as $k) {
            $cfg     = $input[$k] ?? [];
            $enabled = isset($cfg['enabled']) && (bool) $cfg['enabled'];

            $out[$k] = [
                'enabled' => $enabled,
                'count'   => (int)($cfg['count']   ?? ($k === 'adult' ? 1 : 0)),
                'price'   => isset($cfg['price']) ? (float)$cfg['price'] : null,
                'min_age' => isset($cfg['min_age']) ? (int)$cfg['min_age'] : null,
                'max_age' => isset($cfg['max_age']) ? (int)$cfg['max_age'] : null,
            ];

            if ($enabled) {
                if ($out[$k]['min_age'] === null || $out[$k]['max_age'] === null) {
                    back()->withErrors(['age_categories' => trans('translate.Min/Max age required for enabled categories')])->throwResponse();
                }
                if ($out[$k]['min_age'] < 0 || $out[$k]['max_age'] < 0) {
                    back()->withErrors(['age_categories' => trans('translate.Age must be >= 0')])->throwResponse();
                }
                if ($out[$k]['max_age'] < $out[$k]['min_age']) {
                    back()->withErrors(['age_categories' => trans('translate.Max age must be greater or equal to Min age')])->throwResponse();
                }
            }
        }

        // fără suprapuneri pe categoriile active
        $ranges = [];
        foreach ($out as $k => $cfg) {
            if (!$cfg['enabled']) continue;
            $ranges[] = ['k' => $k, 'min' => $cfg['min_age'], 'max' => $cfg['max_age']];
        }
        usort($ranges, fn($a,$b) => $a['min'] <=> $b['min']);
        for ($i = 1; $i < count($ranges); $i++) {
            if ($ranges[$i]['min'] <= $ranges[$i - 1]['max']) {
                back()->withErrors(['age_categories' => trans('translate.Age ranges must not overlap')])->throwResponse();
            }
        }

        return $out;
    }

    /**
     * Normalizează age_categories pentru **Availability** (pe o singură zi).
     * Salvează min/max age dacă sunt furnizate în request.
     */
    private function normalizeAvailabilityAgeCategories(array $input): array
    {
        $keys = ['adult','child','baby','infant']; // ordinea vizuală utilizată în admin
        $out  = [];

        foreach ($keys as $k) {
            $cfg = $input[$k] ?? [];

            $enabled = isset($cfg['enabled']) && (bool)$cfg['enabled'];
            $count   = isset($cfg['count'])   ? (int)$cfg['count']   : 0;
            $price   = array_key_exists('price', $cfg) && $cfg['price'] !== '' ? (float)$cfg['price'] : null;
            $min_age = isset($cfg['min_age']) && is_numeric($cfg['min_age']) ? (int)$cfg['min_age'] : null;
            $max_age = isset($cfg['max_age']) && is_numeric($cfg['max_age']) ? (int)$cfg['max_age'] : null;

            $out[$k] = [
                'enabled' => $enabled,
                'count'   => $count,
                'price'   => $price,
                'min_age' => $min_age,
                'max_age' => $max_age,
            ];
        }

        return $out;
    }
}
