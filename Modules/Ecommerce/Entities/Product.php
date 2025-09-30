<?php

namespace Modules\Ecommerce\Entities;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Wishlist\App\Models\Wishlist;

class Product extends Model
{
    protected $guarded = [];

    protected $appends = ['name', 'description', 'seo_title', 'seo_description'];

    // Relationship with main Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    // Define the relationship with ProductTranslation
    public function translate()
    {
        return $this->belongsTo(ProductTranslation::class, 'id', 'product_id')->where('lang_code', admin_lang());
    }

    public function front_translate()
    {
        return $this->belongsTo(ProductTranslation::class, 'id', 'product_id')->where('lang_code', front_lang());
    }

    public function galleries()
    {
        return $this->hasMany(ProductGallery::class, 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews->avg('rating');
    }

    public function getFinalPriceAttribute()
    {
        if ($this->offer_price) {
            $discount = ($this->price * $this->offer_price) / 100;
            $finalPrice = $this->price - $discount;
        } else {
            $finalPrice = $this->price;
        }

        return number_format((float) $finalPrice, 2, '.', '');
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function scopeBestSelling($query)
    {
        return $query->where('is_best_selling', 1);
    }

    public function getPriceDisplayAttribute()
    {
        if ($this->offer_price) {
            return '<del>' . currency($this->price) . '</del>' . currency($this->final_price);
        }

        return  currency($this->final_price);
    }

    public function myWishlist()
    {
        return $this->hasOne(Wishlist::class, 'item_id', 'id')->where('user_id', auth()->id());
    }

    public function wishlists()
    {
        return $this->morphMany(Wishlist::class, 'wishable');
    }
}
