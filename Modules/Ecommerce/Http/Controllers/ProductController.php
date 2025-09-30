<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Ecommerce\Entities\Product;
use Modules\Ecommerce\Entities\ProductReview;
use Modules\SeoSetting\App\Models\SeoSetting;

class ProductController extends Controller
{
    public function shop(Request $request)
    {
        try {
            $products = Product::with('translate')
                ->withCount('reviews')
                ->withExists('myWishlist')
                ->withAvg('reviews', 'rating')
                ->when($request->filled('search'), function ($q) use ($request) {
                    $q->whereHas('translate', function ($sq) use ($request) {
                        $sq->where('name', 'LIKE', "%{$request->search}%");
                    });
                })
                ->when($request->filled('brands'), function ($q) use ($request) {
                    $q->whereIn('brand_id', $request->brands);
                })
                ->when($request->filled('categories'), function ($q) use ($request) {
                    $q->whereIn('category_id', $request->categories);
                })
                ->active()
                ->latest()
                ->paginate(9);

            // Get brands with translation
            $brands = Brand::with('translate')->withCount('products')->get();

            // Get categories with translation and product count
            $categories = Category::with('translate')
                ->withCount(['products' => function ($query) {
                    $query->active();
                }])
                ->get();

            // best selling products
            $bestSellingProducts = Product::with('translate:id,product_id,name,lang_code')
                ->bestSelling()
                ->active()
                ->withAvg('reviews', 'rating')
                ->latest()
                ->limit(4)
                ->get();

            $seo_setting = SeoSetting::where('id', 11)->first();

            $breadcrumb_title = trans('translate.Shop Page');

            return view('ecommerce::frontend.shop.index', compact(
                'seo_setting',
                'breadcrumb_title',
                'products',
                'brands',
                'categories',
                'bestSellingProducts',
            ));
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while loading the shop page.');
        }
    }

    public function product($slug)
    {
        $breadcrumb_title = trans('translate.Shop Page');

        $product = Product::where('slug', $slug)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->with(['translate', 'galleries', 'reviews'])
            ->withExists('myWishlist')
            ->firstOrFail();

        $relatedProducts = Product::with('translate')
            ->whereNot('id', $product->id)
            ->where('category_id', $product->category_id)
            ->withCount('reviews')
            ->withExists('myWishlist')
            ->withAvg('reviews', 'rating')
            ->active()
            ->latest()
            ->take(4)
            ->get();

        $productReviews = ProductReview::with('user')
            ->where('product_id', $product->id)
            ->latest()
            ->get();

        return view('ecommerce::frontend.shop.single_product', compact(
            'product',
            'relatedProducts',
            'productReviews',
            'breadcrumb_title'
        ));
    }
}
