<?php

namespace Modules\Wishlist\App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use League\OAuth1\Client\Server\Server;
use Modules\Course\App\Models\Course;
use Modules\Ecommerce\Entities\Product;
use Modules\TourBooking\App\Models\Service;
use Modules\Wishlist\App\Models\Wishlist;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $wishlistIds = Wishlist::where('user_id', Auth::user()->id)
            ->where('wishable_type', Product::class)
            ->pluck('wishable_id')
            ->toArray();

        $products = Product::with('translate')
            ->withCount('reviews')
            ->withExists('myWishlist')
            ->withAvg('reviews', 'rating')
            ->where(['status' => 1])
            ->whereIn('id', $wishlistIds)
            ->latest()
            ->get();

        return view('wishlist::index', ['products' => $products]);
    }


    /**
     * Display a listing of the resource.
     */
    public function serviceWishlist()
    {

        $wishlistIds = Wishlist::where('user_id', Auth::user()->id)
            ->where('wishable_type', Service::class)
            ->pluck('wishable_id')
            ->toArray();

        $services = Service::select('id', 'price_per_person', 'slug', 'location', 'is_featured', 'full_price', 'discount_price', 'is_new', 'duration', 'group_size')
            ->whereIn('id', $wishlistIds)
            ->withExists('myWishlist')
            ->where('status', true)
            ->with(['thumbnail:id,service_id,caption,file_path', 'translation:id,service_id,locale,title,short_description'])
            ->withCount('activeReviews')
            ->withAvg('activeReviews', 'rating')
            ->get();

        return view('wishlist::services-list', ['services' => $services]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $user = Auth::guard('web')->user();

        $itemId = $request->item_id;
        $itemType = $request->item_type;

        // Resolve full class name based on type
        if ($itemType === 'service') {
            $modelClass = Service::class;
        } else {
            $modelClass = Product::class;
        }

        // Check if item already in wishlist
        $existing = Wishlist::where('user_id', $user->id)
            ->where('wishable_id', $itemId)
            ->where('wishable_type', $modelClass)
            ->first();

        if (!$existing) {
            // Add to wishlist
            $wishlist = new Wishlist();
            $wishlist->user_id = $user->id;
            $wishlist->item_id = $itemId;
            $wishlist->wishable_id = $itemId;
            $wishlist->wishable_type = $modelClass;
            $wishlist->save();

            $notify_message = trans('translate.Item added to wishlist');
            return response()->json(['message' => $notify_message, 'type' => 'added']);
        } else {
            // Remove from wishlist
            $existing->delete();

            $notify_message = trans('translate.Item removed from wishlist');
            return response()->json(['message' => $notify_message, 'type' => 'removed']);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::guard('web')->user();

        Wishlist::where('user_id', $user->id)->where('item_id', $id)->delete();

        $notify_message = trans('translate.Item removed to wishlist');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->back()->with($notify_message);
    }
}
