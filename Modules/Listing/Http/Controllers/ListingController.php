<?php

namespace Modules\Listing\Http\Controllers;

use Image, File;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Listing\Entities\Listing;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\SubCategory;
use Modules\Language\App\Models\Language;
use Modules\Listing\Entities\ListingTranslation;
use Modules\Listing\Http\Requests\ListingRequest;
use Modules\Ecommerce\Entities\ProductReview;

class ListingController extends Controller
{

    public function index()
    {
        $listings = Listing::with('translate','category')->latest()->get();

        return view('listing::index', compact('listings'));
    }


    public function create(Request $request)
    {
        $categories = Category::with('translate')->where('status', 'enable')->get();

        return view('listing::create', compact('categories', ));
    }

    public function store(ListingRequest $request)
    {
        $listing = new Listing();


        if($request->thumb_image){
            $image_name = 'category-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$request->thumb_image->getClientOriginalExtension();
            $image_name ='uploads/custom-images/'.$image_name;
            $request->thumb_image->move(public_path('uploads/custom-images'), $image_name);
            $listing->thumb_image = $image_name;
        }

        if($request->background_image){
            $image_name = 'listing'.date('-Y-m-d-h-i-s-').rand(999,9999).'.webp';
            $image_name ='uploads/custom-images/'.$image_name;
            Image::make($request->background_image)
                ->encode('webp', 80)
                ->save(public_path().'/'.$image_name);
            $listing->background_image = $image_name;
        }

        $listing->category_id = $request->category_id;
        $listing->sub_category_id = $request->sub_category_id;
        $listing->slug = $request->slug;
        $listing->status = 'enable';
        $listing->seo_title = $request->seo_title ? $request->seo_title : $request->title;
        $listing->seo_description = $request->seo_description ? $request->seo_description : $request->title;
        $listing->save();


        $languages = Language::all();
        foreach($languages as $language){
            $listing_translate = new ListingTranslation();
            $listing_translate->lang_code = $language->lang_code;
            $listing_translate->listing_id = $listing->id;
            $listing_translate->title = $request->title;
            $listing_translate->description = $request->description;
            $listing_translate->short_description = $request->short_description;
            $listing_translate->save();
        }


        $notify_message= trans('translate.Created Successfully');
        $notify_message=array('message'=>$notify_message,'alert-type'=>'success');
        return redirect()->route('admin.listings.edit', ['listing' => $listing->id, 'lang_code' => admin_lang()] )->with($notify_message);
    }

    public function edit(Request $request, $id)
    {

        $listing = Listing::findOrFail($id);

        $listing_translate = ListingTranslation::where(['listing_id' => $id, 'lang_code' => $request->lang_code])->first();

        $categories = Category::with('translate')->where('status', 'enable')->get();

        $subcategories = SubCategory::where('category_id', $listing->category_id)->with('translate')->get();


        return view('listing::edit', compact('categories', 'listing', 'listing_translate', 'subcategories'));
    }

    public function update(ListingRequest $request, $id)
    {

        $listing = Listing::findOrFail($id);

        if($request->lang_code == admin_lang()) {


            if($request->thumb_image){
                $old_image = $listing->thumb_image;
                $image_name = 'category-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$request->thumb_image->getClientOriginalExtension();
                $image_name ='uploads/custom-images/'.$image_name;
                $request->thumb_image->move(public_path('uploads/custom-images'), $image_name);
                $listing->thumb_image = $image_name;

                $listing->save();

                if($old_image) {
                    if(File::exists(public_path().'/'.$old_image)) unlink(public_path().'/'.$old_image);
                }

            }


            if($request->background_image){
                $old_image = $listing->background_image;
                $image_name = 'listing'.date('-Y-m-d-h-i-s-').rand(999,9999).'.webp';
                $image_name ='uploads/custom-images/'.$image_name;
                Image::make($request->background_image)
                    ->encode('webp', 80)
                    ->save(public_path().'/'.$image_name);
                $listing->background_image = $image_name;
                $listing->save();

                if($old_image) {
                    if(File::exists(public_path().'/'.$old_image)) unlink(public_path().'/'.$old_image);
                }
            }



            $listing->category_id = $request->category_id;
            $listing->sub_category_id = $request->sub_category_id;
            $listing->slug = $request->slug;
            $listing->seo_title = $request->seo_title ? $request->seo_title : $request->title;
            $listing->seo_description = $request->seo_description ? $request->seo_description : $request->title;
            $listing->save();

        }

        $listing_translate = ListingTranslation::findOrFail($request->translate_id);
        $listing_translate->title = $request->title;
        $listing_translate->description = $request->description;
        $listing_translate->short_description = $request->short_description;
        $listing_translate->save();

        $notify_message = trans('translate.Updated Successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->back()->with($notify_message);
    }

    public function destroy($id)
    {

        $listing = Listing::findOrFail($id);
        $old_image = $listing->thumb_image;

        if($old_image){
            if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
        }

        ListingTranslation::where('listing_id',$id)->delete();

        $listing->delete();

        $notify_message=  trans('translate.Delete Successfully');
        $notify_message=array('message'=>$notify_message,'alert-type'=>'success');
        return redirect()->route('admin.listings.index')->with($notify_message);
    }

    public function setup_language($lang_code){
        $listing_translates = ListingTranslation::where('lang_code', admin_lang())->get();
        foreach($listing_translates as $listing_translate){
            $translate = new ListingTranslation();
            $translate->listing_id = $listing_translate->listing_id;
            $translate->lang_code = $lang_code;
            $translate->title = $listing_translate->title;
            $translate->description = $listing_translate->description;
            $translate->short_description = $listing_translate->short_description;
            $translate->save();
        }
    }


    public function getSubcategories($categoryId)
    {
        $subcategories = SubCategory::where('category_id', $categoryId)
                                    ->with('translate')
                                    ->get();

        return response()->json($subcategories);
    }


    public function review_detail($id){

        $review = ProductReview::with('user')->findOrFail($id);

        return view('listing::review_show', ['review' => $review]);
    }


    public function review_delete($id){

        $review = ProductReview::findOrFail($id);
        $review->delete();

        $notify_message=  trans('translate.Deleted Successfully');
        $notify_message=array('message'=>$notify_message,'alert-type'=>'success');
        return redirect()->route('admin.product.review.list')->with($notify_message);
    }

}
