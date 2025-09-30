# Tour Booking Module for Laravel

The Tour Booking module is a comprehensive system for managing tours, hotels, restaurants, rentals, activities, and car rentals in your Laravel application.

## Features

- **Service Types Management**: Create and manage different types of services (Tours, Hotels, Restaurants, etc.)
- **Services Management**: Add, edit, and manage services with detailed information
- **Media Gallery**: Upload and manage images and videos for each service
- **Itineraries Management**: Create detailed day-by-day itineraries for tours
- **Availability Management**: Set available dates and times for services
- **Pricing Management**: Set different pricing options (per person, child price, etc.)
- **Booking System**: Allow users to book services with a complete booking management system
- **Multi-language Support**: Full translation support for all content

## Installation

1. Ensure you have the Laravel module system installed.
2. Clone or copy this module to your `Modules` directory.
3. Run the migrations:

```bash
php artisan module:migrate TourBooking
```

4. Run the seeders to populate initial data:

```bash
php artisan module:seed TourBooking
```

This will create the initial service types: Tours, Hotels, Restaurants, Rentals, Activities, and Car Rentals.

## Usage

### Admin Panel

Access the admin panel to manage all aspects of the tour booking system:

- **Service Types**: Manage different types of services
- **Services**: Create and manage detailed service listings
- **Media Gallery**: Upload images and videos for services
- **Itineraries**: Create day-by-day itineraries for tours
- **Availability**: Set available dates and times
- **Bookings**: Manage customer bookings

### Front-end

The module provides routes and views for the front-end to:

- Display services listing
- Show detailed service information
- Make bookings
- Manage user bookings

## Customization

You can customize this module by:

1. Editing the views in `resources/views`
2. Modifying the controllers in `App/Http/Controllers`
3. Extending the models in `App/Models`

## License

This module is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). 