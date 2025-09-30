<?php

namespace Modules\TourBooking\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\TourBooking\App\Models\ServiceType;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the service types with their attributes
        $serviceTypes = [
            [
                'name' => 'Tours',
                'slug' => 'tours',
                'description' => 'Guided tours and excursions to various destinations',
                'icon' => 'fas fa-route',
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 1,
                'display_order' => 1,
            ],
            [
                'name' => 'Hotels',
                'slug' => 'hotels',
                'description' => 'Accommodation options for travelers',
                'icon' => 'fas fa-hotel',
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 2,
                'display_order' => 2,
            ],
            [
                'name' => 'Restaurants',
                'slug' => 'restaurants',
                'description' => 'Dining options for travelers',
                'icon' => 'fas fa-utensils',
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 3,
                'display_order' => 3,
            ],
            [
                'name' => 'Rentals',
                'slug' => 'rentals',
                'description' => 'Apartment and vacation home rentals',
                'icon' => 'fas fa-home',
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 4,
                'display_order' => 4,
            ],
            [
                'name' => 'Activities',
                'slug' => 'activities',
                'description' => 'Recreational activities and adventures',
                'icon' => 'fas fa-hiking',
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 5,
                'display_order' => 5,
            ],
            [
                'name' => 'Car Rentals',
                'slug' => 'car-rentals',
                'description' => 'Car and vehicle rental services',
                'icon' => 'fas fa-car',
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 6,
                'display_order' => 6,
            ],
        ];

        // Insert the service types
        foreach ($serviceTypes as $serviceType) {
            // Check if the service type already exists by slug
            $existingType = ServiceType::where('slug', $serviceType['slug'])->first();
            
            if (!$existingType) {
                ServiceType::create($serviceType);
            }
        }
    }
} 