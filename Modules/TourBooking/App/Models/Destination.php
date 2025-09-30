<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class Destination extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'country',
        'region',
        'city',
        'latitude',
        'longitude',
        'status',
        'is_featured',
        'show_on_homepage',
        'ordering',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'user_id',
        'svg_image',
        'tags'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
        'is_featured' => 'boolean',
        'show_on_homepage' => 'boolean',
        'ordering' => 'integer',
    ];

    /**
     * Get services for this destination.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the translation for this destination.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(DestinationTranslation::class)
            ->where('lang_code', app()->getLocale());
    }

    /**
     * Get active destinations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get featured destinations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get destinations for homepage.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForHomepage($query)
    {
        return $query->where('show_on_homepage', true);
    }

    /**
     * Get tours for this destination.
     */
    public function tours(): HasMany
    {
        return $this->hasMany(Service::class, 'destination_id')
            ->where('type', 'tour');
    }
}
