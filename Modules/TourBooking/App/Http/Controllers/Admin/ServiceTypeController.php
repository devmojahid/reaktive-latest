<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Http\Controllers\Admin;

use App\Helpers\FileUploadHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\TourBooking\App\Models\Service;
use Modules\TourBooking\App\Models\ServiceType;
use Illuminate\Support\Facades\Validator;

final class ServiceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $serviceTypes = ServiceType::with('translation')
            ->latest()
            ->paginate(15);

        return view('tourbooking::admin.service_types.index', compact('serviceTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tourbooking::admin.service_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->all();

        $data['status'] = $request->has('status') ? true : false;
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $data['show_on_homepage'] = $request->has('show_on_homepage') ? true : false;

        $validated = Validator::make($data, [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:service_types,slug|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'status' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'show_on_homepage' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ])->validate();

        if ($request->hasFile('image')) {
            $validated['image'] = FileUploadHelper::uploadImage($request->file('image'), 'destination');
        }

        ServiceType::create($validated);

        return redirect()->route('admin.tourbooking.service-types.index')
            ->with('success', 'Service type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceType $serviceType): View
    {
        $serviceType->load('services');

        return view('tourbooking::admin.service_types.show', compact('serviceType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceType $serviceType): View
    {
        return view('tourbooking::admin.service_types.edit', compact('serviceType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceType $serviceType): RedirectResponse
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:service_types,slug,' . $serviceType->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {

            // Delete old image if exists
            if ($serviceType?->image) {
                FileUploadHelper::deleteImage($serviceType?->image);
            }

            $validated['image'] = FileUploadHelper::uploadImage($request->file('image'), 'destination');
        }

        $validated['status'] = $request->has('status') ? true : false;
        $validated['is_featured'] = $request->has('is_featured') ? true : false;
        $validated['show_on_homepage'] = $request->has('show_on_homepage') ? true : false;

        $serviceType->update($validated);

        return redirect()->route('admin.tourbooking.service-types.index')
            ->with('success', 'Service type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceType $serviceType): RedirectResponse
    {
        // Check if there are any services using this service type
        if (Service::where('service_type_id', $serviceType->id)->exists()) {
            return redirect()->route('admin.tourbooking.service-types.index')
                ->with('error', 'Cannot delete service type because it is being used by one or more services.');
        }

        // Delete old image if exists
        if ($serviceType?->image) {
            FileUploadHelper::deleteImage($serviceType?->image);
        }

        $serviceType->delete();

        return redirect()->route('admin.tourbooking.service-types.index')
            ->with('success', 'Service type deleted successfully.');
    }
}
