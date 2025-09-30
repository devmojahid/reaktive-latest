<?php

namespace Modules\TourBooking\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TourBooking\Database\factories\VisaTypeFactory;

class TripType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status',
        'is_featured',
        'show_on_homepage',
        'display_order',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_featured' => 'boolean',
        'show_on_homepage' => 'boolean',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_trip_type');
    }
}