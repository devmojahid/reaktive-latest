<?php

namespace Modules\TourBooking\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\TourBooking\App\Models\Destination;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define popular travel destinations with their attributes
        $destinations = [
            [
                'name' => 'Paris',
                'slug' => 'paris',
                'description' => 'The City of Light, famous for the Eiffel Tower, Louvre, and charming streets',
                'country' => 'France',
                'region' => 'Île-de-France',
                'city' => 'Paris',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 1,
                'meta_title' => 'Paris - City of Light | Travel Destination',
                'meta_keywords' => 'paris, eiffel tower, louvre, france, travel, destination',
                'meta_description' => 'Discover the beauty of Paris, France. Book tours, hotels, and experiences in the City of Light.',
            ],
            [
                'name' => 'Rome',
                'slug' => 'rome',
                'description' => 'The Eternal City, home to the Colosseum, Roman Forum, and Vatican City',
                'country' => 'Italy',
                'region' => 'Lazio',
                'city' => 'Rome',
                'latitude' => 41.9028,
                'longitude' => 12.4964,
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 2,
                'meta_title' => 'Rome - The Eternal City | Travel Destination',
                'meta_keywords' => 'rome, colosseum, vatican, italy, travel, destination',
                'meta_description' => 'Explore Rome, Italy. Book tours, hotels, and experiences in the Eternal City.',
            ],
            [
                'name' => 'New York City',
                'slug' => 'new-york-city',
                'description' => 'The Big Apple, known for Times Square, Central Park, and the Statue of Liberty',
                'country' => 'United States',
                'region' => 'New York',
                'city' => 'New York',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 3,
                'meta_title' => 'New York City - The Big Apple | Travel Destination',
                'meta_keywords' => 'new york, times square, central park, usa, travel, destination',
                'meta_description' => 'Visit New York City. Book tours, hotels, and experiences in the city that never sleeps.',
            ],
            [
                'name' => 'Tokyo',
                'slug' => 'tokyo',
                'description' => 'Japan\'s vibrant capital, blending ultramodern and traditional aspects',
                'country' => 'Japan',
                'region' => 'Kanto',
                'city' => 'Tokyo',
                'latitude' => 35.6762,
                'longitude' => 139.6503,
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 4,
                'meta_title' => 'Tokyo - Japan\'s Capital | Travel Destination',
                'meta_keywords' => 'tokyo, japan, shibuya, travel, destination',
                'meta_description' => 'Experience Tokyo, Japan. Book tours, hotels, and experiences in this ultramodern city.',
            ],
            [
                'name' => 'London',
                'slug' => 'london',
                'description' => 'The capital of England, famous for Big Ben, Buckingham Palace, and the London Eye',
                'country' => 'United Kingdom',
                'region' => 'England',
                'city' => 'London',
                'latitude' => 51.5074,
                'longitude' => -0.1278,
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 5,
                'meta_title' => 'London - England\'s Capital | Travel Destination',
                'meta_keywords' => 'london, big ben, buckingham palace, uk, travel, destination',
                'meta_description' => 'Discover London, UK. Book tours, hotels, and experiences in England\'s historic capital.',
            ],
            [
                'name' => 'Barcelona',
                'slug' => 'barcelona',
                'description' => 'The cosmopolitan capital of Spain\'s Catalonia region, known for Sagrada Familia and Park Güell',
                'country' => 'Spain',
                'region' => 'Catalonia',
                'city' => 'Barcelona',
                'latitude' => 41.3851,
                'longitude' => 2.1734,
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 6,
                'meta_title' => 'Barcelona - Catalonia\'s Gem | Travel Destination',
                'meta_keywords' => 'barcelona, sagrada familia, gaudi, spain, travel, destination',
                'meta_description' => 'Visit Barcelona, Spain. Book tours, hotels, and experiences in this vibrant Catalan city.',
            ],
            [
                'name' => 'Dubai',
                'slug' => 'dubai',
                'description' => 'A city of superlatives, with the world\'s tallest building and luxury shopping',
                'country' => 'United Arab Emirates',
                'region' => 'Dubai',
                'city' => 'Dubai',
                'latitude' => 25.2048,
                'longitude' => 55.2708,
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 7,
                'meta_title' => 'Dubai - City of Gold | Travel Destination',
                'meta_keywords' => 'dubai, burj khalifa, palm jumeirah, uae, travel, destination',
                'meta_description' => 'Experience Dubai, UAE. Book tours, hotels, and experiences in this ultramodern desert city.',
            ],
            [
                'name' => 'Sydney',
                'slug' => 'sydney',
                'description' => 'Australia\'s largest city, known for its Opera House, Harbour Bridge, and beautiful beaches',
                'country' => 'Australia',
                'region' => 'New South Wales',
                'city' => 'Sydney',
                'latitude' => -33.8688,
                'longitude' => 151.2093,
                'status' => true,
                'is_featured' => true,
                'show_on_homepage' => true,
                'ordering' => 8,
                'meta_title' => 'Sydney - Australia\'s Gem | Travel Destination',
                'meta_keywords' => 'sydney, opera house, harbour bridge, australia, travel, destination',
                'meta_description' => 'Discover Sydney, Australia. Book tours, hotels, and experiences in this beautiful harbor city.',
            ],
        ];

        // Insert the destinations
        foreach ($destinations as $destination) {
            // Check if the destination already exists by slug
            $existingDestination = Destination::where('slug', $destination['slug'])->first();
            
            if (!$existingDestination) {
                Destination::create($destination);
            }
        }
    }
} 