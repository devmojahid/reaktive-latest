<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Modules\Language\App\Models\Language;
use Modules\TourBooking\App\Models\Amenity;
use Modules\TourBooking\App\Models\AmenityTranslation;
use Modules\TourBooking\App\Models\Destination;
use Modules\TourBooking\App\Models\Service;

final class AmenitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $amenities = Amenity::with('translation')
            ->latest()
            ->paginate(15);

        return view('tourbooking::admin.amenity.index', compact('amenities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tourbooking::admin.amenity.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:amenities,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|boolean',
        ]);

        $aminity = new Amenity();

        if ($request->image) {
            $image_name = 'aminity-' . date('-Y-m-d-h-i-s-') . rand(999, 9999) . '.' . $request->image->getClientOriginalExtension();
            $image_name = 'uploads/custom-images/' . $image_name;
            $request->image->move(public_path('uploads/custom-images'), $image_name);
            $aminity->image = $image_name ?? null;
        }

        $aminity->slug = $request->slug;
        $aminity->status = $request->status ? true : false;
        $aminity->save();

        $languages = Language::all();
        foreach ($languages as $language) {
            $sub_translation = new AmenityTranslation();
            $sub_translation->amenity_id = $aminity->id;
            $sub_translation->lang_code = $language->lang_code;
            $sub_translation->name = $request->name;
            $sub_translation->description = $request->description;
            $sub_translation->meta_title = $request->meta_title;
            $sub_translation->meta_keywords = $request->meta_keywords;
            $sub_translation->meta_description = $request->meta_description;
            $sub_translation->save();
        }

        $notify_message = trans('translate.Created Successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->route('admin.tourbooking.amenities.index')->with($notify_message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Amenity $amenity): View
    {
        $amenity->translation = AmenityTranslation::where('amenity_id', $amenity->id)
            ->where('lang_code', $request->lang_code)
            ->first();

        return view('tourbooking::admin.amenity.edit', compact('amenity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Amenity $amenity): RedirectResponse
    {

        if ($request->lang_code == admin_lang()) {

            if ($request->image) {

                if ($amenity->image) {
                    if (File::exists(public_path() . '/' . $amenity->image)) unlink(public_path() . '/' . $amenity->image);
                }

                $image_name = 'aminity-' . date('-Y-m-d-h-i-s-') . rand(999, 9999) . '.' . $request->image->getClientOriginalExtension();
                $image_name = 'uploads/custom-images/' . $image_name;
                $request->image->move(public_path('uploads/custom-images'), $image_name);
                $amenity->image = $image_name ?? null;

            }

            $amenity->slug = $request->slug;
            $amenity->status = $request->status ? true : false;
            $amenity->save();

            $sub_translation = AmenityTranslation::where('amenity_id', $amenity->id)->where('lang_code', $request->lang_code)->first();
            $sub_translation->amenity_id = $amenity->id;
            $sub_translation->lang_code = $request->lang_code;
            $sub_translation->name = $request->name;
            $sub_translation->description = $request->description;
            $sub_translation->meta_title = $request->meta_title;
            $sub_translation->meta_keywords = $request->meta_keywords;
            $sub_translation->meta_description = $request->meta_description;
            $sub_translation->save();
        } else {

            $sub_translation = AmenityTranslation::where('amenity_id', $amenity->id)->where('lang_code', $request->lang_code)->first();
            $sub_translation->amenity_id = $amenity->id;
            $sub_translation->lang_code = $request->lang_code;
            $sub_translation->name = $request->name;
            $sub_translation->description = $request->description;
            $sub_translation->meta_title = $request->meta_title;
            $sub_translation->meta_keywords = $request->meta_keywords;
            $sub_translation->meta_description = $request->meta_description;
            $sub_translation->save();
        }

        $notify_message = trans('translate.Update Successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->route('admin.tourbooking.amenities.index')->with($notify_message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Amenity $amenity): RedirectResponse
    {
        // Delete image if exists
        if ($amenity->image) {
            unlink($amenity->image);
        }

        $amenity->translations()->delete();

        $amenity->delete();

        $notify_message = trans('translate.Delete Successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->route('admin.tourbooking.amenities.index')->with($notify_message);
    }

    public function updateStatus(Amenity $amenity): RedirectResponse|JsonResponse
    {
        $amenity->update(['status' => !$amenity->status]);

        $notify_message = trans('translate.Status updated');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return response()->json($notify_message);
    }
}
