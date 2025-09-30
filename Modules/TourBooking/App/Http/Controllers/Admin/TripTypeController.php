<?php
declare(strict_types=1);

namespace Modules\TourBooking\App\Http\Controllers\Admin;

use App\Helpers\FileUploadHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Modules\TourBooking\App\Models\TripType;

final class TripTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tripTypes = TripType::latest()
            ->get();

        return view('tourbooking::admin.trip-type.index', compact('tripTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tourbooking::admin.trip-type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->all();

        $data['status'] = $request->has('status') ? true : false;
        $data['is_featured'] = $request->has('is_featured') ? true : false;

        $validated = Validator::make($data, [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:trip_types,slug|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean'
        ])->validate();

        if ($request->hasFile('image')) {
            $validated['image'] = FileUploadHelper::uploadImage($request->file('image'), 'visa_type');
        }

        TripType::create($validated);

        return redirect()->route('admin.tourbooking.trip-type.index')
            ->with('success', 'Trip type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TripType $tripType): View
    {
        return view('tourbooking::admin.trip-type.show', compact('tripType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TripType $tripType): View
    {
        return view('tourbooking::admin.trip-type.edit', compact('tripType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TripType $tripType): RedirectResponse
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:trip_types,slug,' . $tripType->id,
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {

            // Delete old image if exists
            if ($tripType?->image) {
                FileUploadHelper::deleteImage($tripType?->image);
            }

            $validated['image'] = FileUploadHelper::uploadImage($request->file('image'), 'destination');
        }

        $validated['status'] = $request->has('status') ? true : false;
        $validated['is_featured'] = $request->has('is_featured') ? true : false;

        $tripType->update($validated);

        return redirect()->route('admin.tourbooking.trip-type.index')
            ->with('success', 'Trip type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TripType $tripType): RedirectResponse
    {

        // Delete old image if exists
        if ($tripType?->image) {
            FileUploadHelper::deleteImage($tripType?->image);
        }

        $tripType->delete();

        return redirect()->route('admin.tourbooking.trip-type.index')
            ->with('success', 'Trip type deleted successfully.');
    }
}
