<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Http\Controllers\Agency;

use App\Helpers\FileUploadHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\TourBooking\App\Models\Destination;
use Modules\TourBooking\App\Models\Service;

final class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $destinations = Destination::with('translation')
            ->where('user_id', auth()->user()->id)
            ->withCount('services')
            ->latest()
            ->paginate(15);

        return view('tourbooking::agency.destinations.index', compact('destinations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tourbooking::agency.destinations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:destinations,slug|max:255',
            'description' => 'nullable|string',
            'country' => 'required|string|max:100',
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|string|max:30',
            'longitude' => 'nullable|string|max:30',
            'status' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'show_on_homepage' => 'nullable|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = FileUploadHelper::uploadImage($request->file('image'), 'destination');
        }

        if ($request->hasFile('svg')) {
            $validated['svg_image'] = FileUploadHelper::uploadImage($request->file('svg'), 'destination');
        }

        $validated['status'] = $request->has('status');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['show_on_homepage'] = $request->has('show_on_homepage');
        $validated['tags'] = $request->tags ?? null;

        $validated['user_id'] = auth()->user()->id;

        Destination::create($validated);

        return redirect()->route('agency.tourbooking.destinations.index')
            ->with('success', 'Destination created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Destination $destination): View
    {
        $destination->load(['services.serviceType']);

        return view('tourbooking::agency.destinations.show', compact('destination'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Destination $destination): View
    {
        return view('tourbooking::agency.destinations.edit', compact('destination'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Destination $destination): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:destinations,slug,' . $destination->id,
            'description' => 'nullable|string',
            'country' => 'required|string|max:100',
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|string|max:30',
            'longitude' => 'nullable|string|max:30',
            'status' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'show_on_homepage' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($destination?->image !== null) {
                FileUploadHelper::deleteImage($destination->image);
            }
            $validated['image'] = FileUploadHelper::uploadImage($request->file('image'), 'destination');
        }

        if ($request->hasFile('svg')) {
            // Delete old image if exists
            if ($destination?->svg_image !== null) {
                FileUploadHelper::deleteImage($destination->svg_image);
            }
            $validated['svg_image'] = FileUploadHelper::uploadImage($request->file('svg'), 'destination');
        }

        $validated['status'] = $request->has('status');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['show_on_homepage'] = $request->has('show_on_homepage');
        $validated['tags'] = $request->tags ?? null;

        $destination->update($validated);

        return redirect()->route('agency.tourbooking.destinations.index')
            ->with('success', 'Destination updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Destination $destination): RedirectResponse
    {
        // Check if there are any services associated with this destination
        if (Service::where('destination_id', $destination->id)->exists()) {
            return redirect()->route('agency.tourbooking.destinations.index')
                ->with('error', 'Cannot delete destination because it is being used by one or more services.');
        }

        // Delete image if exists
        if ($destination?->image !== null) {
            FileUploadHelper::deleteImage($destination->image);
        }

        // Delete image if exists
        if ($destination?->svg_image !== null) {
            FileUploadHelper::deleteImage($destination?->svg_image);
        }

        $destination->delete();

        return redirect()->route('agency.tourbooking.destinations.index')
            ->with('success', 'Destination deleted successfully.');
    }

    public function updateStatus(Destination $destination): RedirectResponse|JsonResponse
    {
        $destination->update(['status' => !$destination->status]);


        $notify_message = trans('translate.Status updated');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return response()->json($notify_message);
    }

    public function updateFeatured(Destination $destination): RedirectResponse|JsonResponse
    {
        $destination->update(['is_featured' => !$destination->is_featured]);

        $notify_message = trans('translate.Featured updated');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return response()->json($notify_message);
    }
}
