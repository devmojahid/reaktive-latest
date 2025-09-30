<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Http\Controllers\Agency;

use App\Enums\Language as EnumsLanguage;
use App\Helpers\FileUploadHelper;
use App\Http\Controllers\Controller;
use Faker\Core\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\TourBooking\App\Http\Requests\ServiceRequest;
use Modules\TourBooking\App\Models\Availability;
use Modules\TourBooking\App\Models\ExtraCharge;
use Modules\TourBooking\App\Models\Service;
use Modules\TourBooking\App\Models\ServiceMedia;
use Modules\TourBooking\App\Models\ServiceTranslation;
use Modules\TourBooking\App\Models\ServiceType;
use Modules\TourBooking\App\Models\TourItinerary;
use Modules\TourBooking\App\Repositories\ServiceRepository;
use Modules\TourBooking\App\Repositories\ServiceTypeRepository;
use Modules\Language\App\Models\Language;
use Illuminate\Http\JsonResponse;
use Modules\TourBooking\App\Models\Amenity;
use Modules\TourBooking\App\Models\Destination;

final class ServiceController extends Controller
{
    public function __construct(
        private ServiceRepository $serviceRepository,
        private ServiceTypeRepository $serviceTypeRepository
    ) {}

    /**
     * Display a listing of services.
     */
    public function index(): View
    {
        $services = $this->serviceRepository->getAllFilters(['user_id' => auth()->user()->id]);

        return view('tourbooking::agency.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(): View
    {
        $amenities = Amenity::where('status', true)->with('translation:id,amenity_id,lang_code,name')->get();

        $serviceTypes = $this->serviceTypeRepository->getActive();

        $enum_languages = EnumsLanguage::cases();

        $destinations = Destination::select('id', 'name')->where('status', true)->get();

        return view('tourbooking::agency.services.create', compact('serviceTypes', 'amenities', 'enum_languages', 'destinations'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(ServiceRequest $request): RedirectResponse
    {

        $data = $request->validated();

        // Handle JSON fields
        $jsonFields = ['included', 'excluded', 'facilities', 'rules', 'safety', 'social_links'];
        foreach ($jsonFields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = json_encode($data[$field]);
            }
        }

        // Convert checkbox values
        $booleanFields = ['deposit_required', 'is_featured', 'is_popular', 'show_on_homepage', 'status', 'is_new', 'is_per_person'];
        foreach ($booleanFields as $field) {
            $data[$field] = isset($data[$field]) ? true : false;
        }

        // Create slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $data['user_id'] = auth()->user()->id;

        // Save the service
        $service = $this->serviceRepository->create($data);

        // Save the translation for current language
        $this->serviceRepository->saveTranslation(
            $service,
            admin_lang(),
            [
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
                'seo_keywords' => $data['seo_keywords'] ?? null,
                'included' => $data['included'] ?? null,
                'excluded' => $data['excluded'] ?? null,
                'amenities' => $data['amenities'] ?? [],
                'facilities' => $data['facilities'] ?? null,
                'rules' => $data['rules'] ?? null,
                'safety' => $data['safety'] ?? null,
                'cancellation_policy' => $data['cancellation_policy'] ?? null,
            ]
        );

        $notify_message = trans('translate.Created successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return redirect()->route('agency.tourbooking.services.edit', ['service' => $service->id, 'lang_code' => admin_lang()])->with($notify_message);
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): View
    {
        $service->load(['translation', 'serviceType', 'media', 'extraCharges', 'availabilities', 'itineraries']);

        return view('tourbooking::agency.services.show', compact('service'));
    }


    /**
     * Show the form for editing the specified service.
     */
    public function edit(Request $request, Service $service): View
    {

        if ($service->user_id != auth()->user()->id) {
            abort(404);
        }

        $lang_code = $request->lang_code ?? admin_lang();

        $service->load([
            'media',
            'serviceType',
            'extraCharges',
            'availabilities',
            'itineraries' => function ($query) {
                $query->orderBy('day_number');
            }
        ]);

        $translation = ServiceTranslation::where([
            'service_id' => $service->id,
            'locale' => $lang_code
        ])->first();

        // Convert JSON fields back to newline-separated strings for textarea display
        $jsonFields = ['included', 'excluded', 'facilities', 'rules', 'safety'];

        foreach ($jsonFields as $field) {
            // Check translation first, then service
            $value = $translation->$field ?? $service->$field ?? null;

            if ($value) {
                if (is_array($value)) {
                    // Already an array, convert to newline-separated string
                    if ($translation && isset($translation->$field)) {
                        $translation->$field = implode("\n", $value);
                    } else {
                        $service->$field = implode("\n", $value);
                    }
                } elseif (is_string($value)) {
                    // Try to decode JSON
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        // Convert array back to newline-separated string
                        if ($translation && isset($translation->$field)) {
                            $translation->$field = implode("\n", $decoded);
                        } else {
                            $service->$field = implode("\n", $decoded);
                        }
                    }
                    // If it's already a plain string, leave it as is
                }
            }
        }

        $serviceTypes = $this->serviceTypeRepository->getActive();

        $amenities = Amenity::where('status', true)->with('translation:id,amenity_id,lang_code,name')->get();

        $enum_languages = EnumsLanguage::cases();

        $destinations = Destination::select('id', 'name')->where('status', true)->get();

        return view('tourbooking::agency.services.edit', compact('service', 'serviceTypes', 'translation', 'lang_code', 'amenities', 'enum_languages', 'destinations'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {

        if ($service->user_id !== auth()->user()->id) {
            abort(404);
        }

        $data = $request->validated();
        $lang_code = $request->lang_code ?? admin_lang();

        // Handle main service update only if we're editing in the admin language
        if ($lang_code === admin_lang()) {
            // Handle JSON fields - convert newline-separated strings to arrays
            $jsonFields = ['included', 'excluded', 'facilities', 'rules', 'safety', 'social_links'];
            foreach ($jsonFields as $field) {
                if (isset($data[$field])) {
                    if (is_string($data[$field])) {
                        // Check if it's already valid JSON
                        $decoded = json_decode($data[$field], true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            // Not JSON, treat as newline-separated string
                            $lines = array_filter(
                                array_map('trim', explode("\n", $data[$field])),
                                function ($line) {
                                    return $line !== '';
                                }
                            );
                            $data[$field] = json_encode(array_values($lines));
                        }
                        // If it's already valid JSON, keep it as is
                    } elseif (is_array($data[$field])) {
                        $data[$field] = json_encode($data[$field]);
                    }
                }
            }

            // Convert checkbox values
            $booleanFields = ['deposit_required', 'is_featured', 'is_popular', 'show_on_homepage', 'status', 'is_per_person'];
            foreach ($booleanFields as $field) {
                $data[$field] = isset($data[$field]) ? true : false;
            }

            // Update the service
            $this->serviceRepository->update($service, $data);
        }

        // Update or create translation
        $translationData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'seo_keywords' => $data['seo_keywords'] ?? null,
        ];

        // Add JSON fields to translation if they exist in the request
        $translationJsonFields = ['included', 'excluded', 'facilities', 'rules', 'safety', 'cancellation_policy'];
        foreach ($translationJsonFields as $field) {
            if (isset($data[$field])) {
                if (is_string($data[$field])) {
                    // Check if it's already valid JSON
                    $decoded = json_decode($data[$field], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        // Not JSON, treat as newline-separated string
                        $lines = array_filter(
                            array_map('trim', explode("\n", $data[$field])),
                            function ($line) {
                                return $line !== '';
                            }
                        );
                        $translationData[$field] = json_encode(array_values($lines));
                    } else {
                        // Already valid JSON, use as is
                        $translationData[$field] = $data[$field];
                    }
                } else {
                    $translationData[$field] = $data[$field];
                }
            }
        }

        $translationData['amenities'] = $request->amenities ?? [];

        $this->serviceRepository->saveTranslation($service, $lang_code, $translationData);

        $notify_message = trans('translate.Updated successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }
    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {

        if ($service->user_id !== auth()->user()->id) {
            abort(404);
        }

        if ($service->bookings()->count() > 0) {
            return redirect()->back()->with('error', trans('translate.Service has bookings. Cannot delete'));
        }

        // Delete all associated media files
        foreach ($service->media as $media) {
           FileUploadHelper::deleteImage($media->file_path);
            $media->delete();
        }

        $this->serviceRepository->delete($service);

        $notify_message = trans('translate.Deleted successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return redirect()->route('agency.tourbooking.services.index')->with($notify_message);
    }

    /**
     * Upload and store media for a service.
     */
    public function storeMedia(Request $request, Service $service): RedirectResponse
    {
        if ($service->user_id !== auth()->user()->id) {
            abort(404);
        }

        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,webp,mp4,avi,mov|max:10240',
            'caption' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileType = explode('/', $file->getMimeType())[0] === 'video' ? 'video' : 'image';
            $fileName = $file->getClientOriginalName();
            $filePath = FileUploadHelper::uploadImage($file, $service->slug);
        }

        // Check if this is the first media item, set it as thumbnail if so
        $isThumbnail = $service->media()->count() === 0;

        ServiceMedia::create([
            'service_id' => $service->id,
            'file_path' => $filePath ?? null,
            'file_type' => $fileType ?? null,
            'file_name' => $fileName ?? null,
            'caption' => $request->caption,
            'is_featured' => $isThumbnail,
            'is_thumbnail' => $isThumbnail,
            'display_order' => $service->media()->count() + 1,
        ]);

        $notify_message = trans('translate.Media uploaded successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }

    /**
     * Delete a media item.
     */
    public function deleteMedia(ServiceMedia $media): RedirectResponse
    {
        $serviceId = $media->service_id;

        // Check if this is the thumbnail, reassign if needed
        if ($media->is_thumbnail) {
            $newThumbnail = ServiceMedia::where('service_id', $serviceId)
                ->where('id', '!=', $media->id)
                ->where('file_type', 'image')
                ->first();

            if ($newThumbnail) {
                $newThumbnail->update(['is_thumbnail' => true]);
            }
        }

        if ($media?->file_path) {
            FileUploadHelper::deleteImage($media->file_path);
        }

        // Delete the record
        $media->delete();

        $notify_message = trans('translate.Media deleted successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }

    /**
     * Set a media item as the thumbnail.
     */
    public function setThumbnail(ServiceMedia $media): RedirectResponse
    {
        // Ensure this is an image
        if ($media->file_type !== 'image') {
            $notify_message = trans('translate.Only images can be set as thumbnails');
            $notify_message = array('message' => $notify_message, 'alert-type' => 'error');

            return back()->with($notify_message);
        }

        // Reset all thumbnails for this service
        ServiceMedia::where('service_id', $media->service_id)
            ->update(['is_thumbnail' => false]);

        // Set this one as thumbnail
        $media->update(['is_thumbnail' => true]);

        $notify_message = trans('translate.Thumbnail set successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }

    /**
     * Show itineraries for a service.
     */
    public function showItineraries(Service $service): View
    {
        if ($service->user_id !== auth()->user()->id) {
            abort(404);
        }

        $service->load(['itineraries' => function ($query) {
            $query->orderBy('day_number');
        }]);

        return view('tourbooking::agency.services.itineraries', compact('service'));
    }

    /**
     * Store a new itinerary for a service.
     */
    public function storeItinerary(Request $request, Service $service): RedirectResponse
    {

        if ($service->user_id !== auth()->user()->id) {
            abort(404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'day_number' => 'required|integer|min:1',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'meal_included' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->except('_token', 'image');

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = FileUploadHelper::uploadImage($request->file('image'), 'itinerary');
        }

        // Set display order if not provided
        if (!isset($data['display_order'])) {
            $data['display_order'] = $service->itineraries()->count() + 1;
        }

        $data['service_id'] = $service->id;

        TourItinerary::create($data);

        $notify_message = trans('translate.Itinerary added successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }

    /**
     * Update an existing itinerary.
     */
    public function updateItinerary(Request $request, TourItinerary $itinerary): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'day_number' => 'required|integer|min:1',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'meal_included' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->except('_token', '_method', 'image');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($itinerary->image) {
                FileUploadHelper::deleteImage($itinerary?->image);
            }
            $data['image'] = FileUploadHelper::uploadImage($request->file('image'), 'itinerary');
        }

        $itinerary->update($data);

        $notify_message = trans('translate.Itinerary updated successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }

    /**
     * Delete an itinerary.
     */
    public function deleteItinerary(TourItinerary $itinerary): RedirectResponse
    {
        // Delete image if exists
        if ($itinerary?->image) {
            FileUploadHelper::deleteImage($itinerary?->image);
        }

        $itinerary->delete();

        $notify_message = trans('translate.Itinerary deleted successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }

    /**
     * Show extra charges for a service.
     */
    public function showExtraCharges(Service $service): View
    {

        if ($service->user_id !== auth()->user()->id) {
            abort(404);
        }

        $service->load('extraCharges');

        return view('tourbooking::agency.services.extra_charges', compact('service'));
    }

    /**
     * Store a new extra charge for a service.
     */
    public function storeExtraCharge(Request $request, Service $service): RedirectResponse
    {

        if ($service->user_id !== auth()->user()->id) {
            abort(404);
        }

        // Transform checkbox values before validation
        $request->merge([
            'is_mandatory' => $request->boolean('is_mandatory'),
            'is_tax' => $request->boolean('is_tax'),
            'status' => $request->boolean('status'),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:per_booking,per_person,per_adult,per_child,per_infant,per_night,flat',
            'is_mandatory' => 'boolean',
            'is_tax' => 'boolean',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'max_quantity' => 'nullable|integer|min:1',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['service_id'] = $service->id;

        ExtraCharge::create($data);

        $notify_message = trans('translate.Extra charge added successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }

    /**
     * Update an existing extra charge.
     */
    public function updateExtraCharge(Request $request, ExtraCharge $charge): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:per_booking,per_person,per_adult,per_child,per_infant,per_night,flat',
            'is_mandatory' => 'boolean',
            'is_tax' => 'boolean',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'max_quantity' => 'nullable|integer|min:1',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_mandatory'] = $request->input('is_mandatory') == '1' || $request->input('is_mandatory') === true;
        $data['is_tax'] = $request->input('is_tax') == '1' || $request->input('is_tax') === true;
        $data['status'] = $request->input('status') == '1' || $request->input('status') === true;

        $charge->update($data);

        $notify_message = trans('translate.Extra charge updated successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }

    /**
     * Delete an extra charge.
     */
    public function deleteExtraCharge(ExtraCharge $charge): RedirectResponse
    {
        $charge->delete();

        $notify_message = trans('translate.Extra charge deleted successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }

    /**
     * Show availability for a service.
     */
    public function showAvailability(Service $service): View
    {

        if ($service->user_id !== auth()->user()->id) {
            abort(404);
        }

        $service->load('availabilities');

        return view('tourbooking::agency.services.availability', compact('service'));
    }

    /**
     * Store a new availability for a service.
     *
     * @return RedirectResponse|JsonResponse
     */
    public function storeAvailability(Request $request, Service $service): RedirectResponse|JsonResponse
    {

        if ($service->user_id !== auth()->user()->id) {
            abort(404);
        }

        // Check if this is a bulk request with dates array (from AJAX)
        if ($request->has('bulk') && $request->has('dates')) {
            // Validate bulk data
            $request->validate([
                'dates' => 'required|array',
                'dates.*' => 'date',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
                'available_spots' => 'nullable|integer|min:1',
                'special_price' => 'nullable|numeric|min:0',
                'is_available' => 'boolean',
                'notes' => 'nullable|string',
            ]);

            $successCount = 0;
            $errorCount = 0;

            foreach ($request->dates as $date) {
                // Check for existing availability on this date
                $existingAvailability = Availability::where('service_id', $service->id)
                    ->where('date', $date)
                    ->where('start_time', $request->start_time ? date('H:i:s', strtotime($request->start_time)) : null)
                    ->first();

                if ($existingAvailability) {
                    $errorCount++;
                    continue;
                }

                Availability::create([
                    'service_id' => $service->id,
                    'date' => $date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'is_available' => $request->has('is_available'),
                    'available_spots' => $request->available_spots,
                    'special_price' => $request->special_price,
                    'notes' => $request->notes,
                ]);

                $successCount++;
            }

            if ($request->ajax()) {
                if ($errorCount > 0) {
                    return response()->json([
                        'success' => true,
                        'message' => trans('translate.Created :success availabilities. :error already existed.', [
                            'success' => $successCount,
                            'error' => $errorCount
                        ])
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => trans('translate.Created :count availabilities successfully', [
                        'count' => $successCount
                    ])
                ]);
            }

            $notify_message = trans('translate.Created :success availabilities. :error already existed.', [
                'success' => $successCount,
                'error' => $errorCount
            ]);
            $notify_type = ($successCount > 0) ? 'success' : 'error';
            $notify_message = array('message' => $notify_message, 'alert-type' => $notify_type);

            return back()->with($notify_message);
        }

        // Single availability creation
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'is_available' => 'boolean',
            'available_spots' => 'nullable|integer|min:1',
            'special_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['service_id'] = $service->id;
        $data['is_available'] = $request->has('is_available');

        // Check for existing availability on the same date
        $existingAvailability = Availability::where('service_id', $service->id)
            ->where('date', $request->date)
            ->where('start_time', $request->start_time ? date('H:i:s', strtotime($request->start_time)) : null)
            ->first();

        if ($existingAvailability) {
            $notify_message = trans('translate.Availability already exists for this date');
            $notify_message = array('message' => $notify_message, 'alert-type' => 'error');

            return back()->with($notify_message);
        }

        Availability::create($data);

        $notify_message = trans('translate.Availability added successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }

    /**
     * Update an existing availability.
     */
    public function updateAvailability(Request $request, Availability $availability): RedirectResponse
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'is_available' => 'boolean',
            'available_spots' => 'nullable|integer|min:1',
            'special_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['is_available'] = $request->has('is_available');

        // Check for existing availability on the same date (excluding this one)
        $existingAvailability = Availability::where('service_id', $availability->service_id)
            ->where('date', $request->date)
            ->where('start_time', $request->start_time ?? null)
            ->where('id', '!=', $availability->id)
            ->first();

        if ($existingAvailability) {
            $notify_message = trans('translate.Availability already exists for this date');
            $notify_message = array('message' => $notify_message, 'alert-type' => 'error');

            return back()->with($notify_message);
        }

        $availability->update($data);

        $notify_message = trans('translate.Availability updated successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }

    /**
     * Delete an availability.
     */
    public function deleteAvailability(Availability $availability): RedirectResponse
    {
        $availability->delete();

        $notify_message = trans('translate.Availability deleted successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return back()->with($notify_message);
    }

    /**
     * Filter services by type.
     */
    public function getByType(string $type): View
    {
        $serviceType = ServiceType::where('slug', $type)->firstOrFail();

        $services = $this->serviceRepository->getByType($serviceType->id, 0);

        return view('tourbooking::agency.service_types.show', compact('serviceType', 'services'));
    }

    /**
     * Filter services by predefined types.
     */
    public function tours(): View
    {
        return $this->getByType('tours');
    }

    public function hotels(): View
    {
        return $this->getByType('hotels');
    }

    public function restaurants(): View
    {
        return $this->getByType('restaurants');
    }

    public function rentals(): View
    {
        return $this->getByType('rentals');
    }

    public function activities(): View
    {
        return $this->getByType('activities');
    }

    /**
     * Show media management for a service.
     */
    public function showMedia(Service $service): View
    {
        $service->load(['media' => function ($query) {
            $query->orderBy('display_order')->orderBy('created_at', 'desc');
        }]);

        return view('tourbooking::agency.services.media', compact('service'));
    }
}
