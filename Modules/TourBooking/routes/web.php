<?php

use Illuminate\Support\Facades\Route;
use Modules\TourBooking\App\Http\Controllers\Admin\AmenitiesController;
use Modules\TourBooking\App\Http\Controllers\Admin\ServiceTypeController;
use Modules\TourBooking\App\Http\Controllers\Admin\ServiceController;
use Modules\TourBooking\App\Http\Controllers\Admin\BookingController;
use Modules\TourBooking\App\Http\Controllers\Admin\DestinationController;
use Modules\TourBooking\App\Http\Controllers\Admin\CouponController;
use Modules\TourBooking\App\Http\Controllers\Admin\ReviewController;
use Modules\TourBooking\App\Http\Controllers\Admin\ReportController;
use Modules\TourBooking\App\Http\Controllers\Agency\ServiceController as AgencyServiceController;
use Modules\TourBooking\App\Http\Controllers\Front\FrontServiceController;
use Modules\TourBooking\App\Http\Controllers\Front\FrontBookingController;
use Modules\TourBooking\App\Http\Controllers\Front\PaymentController;
use Modules\TourBooking\App\Http\Controllers\User\BookingController as UserBookingController;
use Modules\TourBooking\App\Http\Controllers\Agency\BookingController as AgencyBookingController;
use Modules\TourBooking\App\Http\Controllers\Agency\DestinationController as AgencyDestinationController;
use Modules\TourBooking\App\Http\Controllers\Admin\TripTypeController;
use Modules\TourBooking\App\Http\Controllers\Admin\ServiceImportExportController;
use Modules\TourBooking\App\Http\Controllers\User\BookingInvoiceController;
// (opțional) importul ăsta nu e folosit aici, îl putem lăsa sau șterge
use Modules\TourBooking\App\Http\Controllers\Front\ServiceAvailabilityController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within the "web" middleware group.
|
*/

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin/tourbooking/services')
    ->name('admin.tourbooking.services.')
    ->middleware(['auth'])
    ->group(function () {
        Route::post('import', [ServiceImportExportController::class, 'import'])->name('import');
        Route::get('export/manual', [ServiceImportExportController::class, 'exportManual'])->name('export.manual');
        Route::get('export/template', [ServiceImportExportController::class, 'exportTemplate'])->name('export.template');
        Route::get('import/report', [ServiceImportExportController::class, 'downloadReport'])->name('import.report');
        Route::get('/import-instructions', [ServiceImportExportController::class, 'instructions'])->name('import.instructions');
    });

Route::group(['as' => 'admin.tourbooking.', 'prefix' => 'admin/tourbooking', 'middleware' => ['auth:admin']], function () {
    // Service Types
    Route::resource('service-types', ServiceTypeController::class);
    Route::resource('trip-type', TripTypeController::class);

    // Services
    Route::resource('services', ServiceController::class);
    Route::get('services/type/{type}', [ServiceController::class, 'getByType'])->name('services.by-type');
    Route::get('services/tours', [ServiceController::class, 'tours'])->name('services.tours');
    Route::get('services/hotels', [ServiceController::class, 'hotels'])->name('services.hotels');
    Route::get('services/restaurants', [ServiceController::class, 'restaurants'])->name('services.restaurants');
    Route::get('services/rentals', [ServiceController::class, 'rentals'])->name('services.rentals');
    Route::get('services/activities', [ServiceController::class, 'activities'])->name('services.activities');

    // Service Media
    Route::post('services/{service}/media', [ServiceController::class, 'storeMedia'])->name('services.media.store');
    Route::delete('services/media/{media}', [ServiceController::class, 'deleteMedia'])->name('services.media.destroy');
    Route::post('services/media/{media}/set-thumbnail', [ServiceController::class, 'setThumbnail'])->name('services.media.set-thumbnail');
    Route::get('services/{service}/media', [ServiceController::class, 'showMedia'])->name('services.media');

    // Itineraries
    Route::get('services/{service}/itineraries', [ServiceController::class, 'showItineraries'])->name('services.itineraries');
    Route::post('services/{service}/itineraries', [ServiceController::class, 'storeItinerary'])->name('services.itineraries.store');
    Route::put('services/itineraries/{itinerary}', [ServiceController::class, 'updateItinerary'])->name('services.itineraries.update');
    Route::delete('services/itineraries/{itinerary}', [ServiceController::class, 'deleteItinerary'])->name('services.itineraries.destroy');

    // Extra Charges
    Route::get('services/{service}/extra-charges', [ServiceController::class, 'showExtraCharges'])->name('services.extra-charges');
    Route::post('services/{service}/extra-charges', [ServiceController::class, 'storeExtraCharge'])->name('services.extra-charges.store');
    Route::put('services/extra-charges/{charge}', [ServiceController::class, 'updateExtraCharge'])->name('services.extra-charges.update');
    Route::delete('services/extra-charges/{charge}', [ServiceController::class, 'deleteExtraCharge'])->name('services.extra-charges.destroy');

    // Availability
    Route::get('services/{service}/availability', [ServiceController::class, 'showAvailability'])->name('services.availability');
    Route::post('services/{service}/availability', [ServiceController::class, 'storeAvailability'])->name('services.availability.store');
    Route::put('services/availability/{availability}', [ServiceController::class, 'updateAvailability'])->name('services.availability.update');
    Route::delete('services/availability/{availability}', [ServiceController::class, 'deleteAvailability'])->name('services.availability.destroy');

    // Pickup Points
    Route::get('services/{service}/pickup-points', [ServiceController::class, 'showPickupPoints'])->name('services.pickup-points');
    Route::post('services/{service}/pickup-points', [ServiceController::class, 'storePickupPoint'])->name('services.pickup-points.store');
    Route::put('services/pickup-points/{pickupPoint}', [ServiceController::class, 'updatePickupPoint'])->name('services.pickup-points.update');
    Route::delete('services/pickup-points/{pickupPoint}', [ServiceController::class, 'deletePickupPoint'])->name('services.pickup-points.destroy');

    // Booking Management
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post('bookings/{booking}/payment-status', [BookingController::class, 'updatePaymentStatus'])->name('bookings.payment-status');
    Route::get('bookings/{booking}/invoice', [BookingController::class, 'invoice'])->name('bookings.invoice');
    Route::get('bookings/{booking}/download-invoice', [BookingController::class, 'downloadInvoicePdf'])->name('bookings.download-invoice');
    Route::get('bookings/status/{status}', [BookingController::class, 'getByStatus'])->name('bookings.status');

    Route::post('bookings/confirm', [BookingController::class, 'bookingConfirm'])->name('bookings.confirm');
    Route::post('bookings/cancel', [BookingController::class, 'bookingCancel'])->name('bookings.cancel');
    Route::post('bookings/add-note', [BookingController::class, 'bookingAddNote'])->name('bookings.add-note');

    // Destinations
    Route::resource('destinations', DestinationController::class);
    Route::put('destinations/{destination}/status', [DestinationController::class, 'updateStatus'])->name('destinations.update-status');
    Route::put('destinations/{destination}/featured', [DestinationController::class, 'updateFeatured'])->name('destinations.update-featured');

    // Amenities
    Route::resource('amenities', AmenitiesController::class);
    Route::put('amenities/{amenity}/status', [AmenitiesController::class, 'updateStatus'])->name('amenities.update-status');

    // Coupons (dezactivat momentan)
    // Route::resource('coupons', CouponController::class);

    // Reviews
    Route::get('reviews', [ServiceController::class, 'review_list'])->name('reviews.index');
    Route::get('review/detail/{id}', [ServiceController::class, 'review_detail'])->name('reviews.detail');
    Route::delete('review/delete/{id}', [ServiceController::class, 'review_delete'])->name('reviews.delete');
    Route::put('review/approve/{id}', [ServiceController::class, 'review_approve'])->name('reviews.approve');
});

/*
|--------------------------------------------------------------------------
| Agency Routes
|--------------------------------------------------------------------------
*/
Route::group(['as' => 'agency.tourbooking.', 'prefix' => 'agency/tourbooking', 'middleware' => ['auth', 'CheckAgency']], function () {

    // Services
    Route::resource('services', AgencyServiceController::class);

    // Service Media
    Route::post('services/{service}/media', [AgencyServiceController::class, 'storeMedia'])->name('services.media.store');
    Route::delete('services/media/{media}', [AgencyServiceController::class, 'deleteMedia'])->name('services.media.destroy');
    Route::post('services/media/{media}/set-thumbnail', [AgencyServiceController::class, 'setThumbnail'])->name('services.media.set-thumbnail');
    Route::get('services/{service}/media', [AgencyServiceController::class, 'showMedia'])->name('services.media');

    // Itineraries
    Route::get('services/{service}/itineraries', [AgencyServiceController::class, 'showItineraries'])->name('services.itineraries');
    Route::post('services/{service}/itineraries', [AgencyServiceController::class, 'storeItinerary'])->name('services.itineraries.store');
    Route::put('services/itineraries/{itinerary}', [AgencyServiceController::class, 'updateItinerary'])->name('services.itineraries.update');
    Route::delete('services/itineraries/{itinerary}', [AgencyServiceController::class, 'deleteItinerary'])->name('services.itineraries.destroy');

    // Extra Charges
    Route::get('services/{service}/extra-charges', [AgencyServiceController::class, 'showExtraCharges'])->name('services.extra-charges');
    Route::post('services/{service}/extra-charges', [AgencyServiceController::class, 'storeExtraCharge'])->name('services.extra-charges.store');
    Route::put('services/extra-charges/{charge}', [AgencyServiceController::class, 'updateExtraCharge'])->name('services.extra-charges.update');
    Route::delete('services/extra-charges/{charge}', [AgencyServiceController::class, 'deleteExtraCharge'])->name('services.extra-charges.destroy');

    // Availability
    Route::get('services/{service}/availability', [AgencyServiceController::class, 'showAvailability'])->name('services.availability');
    Route::post('services/{service}/availability', [AgencyServiceController::class, 'storeAvailability'])->name('services.availability.store');
    Route::put('services/availability/{availability}', [AgencyServiceController::class, 'updateAvailability'])->name('services.availability.update');
    Route::delete('services/availability/{availability}', [AgencyServiceController::class, 'deleteAvailability'])->name('services.availability.destroy');

    // Booking Management
    Route::get('bookings', [AgencyBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/create', [AgencyBookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings', [AgencyBookingController::class, 'store'])->name('bookings.store');
    Route::get('bookings/{booking}', [AgencyBookingController::class, 'show'])->name('bookings.show');
    Route::get('bookings/{booking}/edit', [AgencyBookingController::class, 'edit'])->name('bookings.edit');
    Route::put('bookings/{booking}', [AgencyBookingController::class, 'update'])->name('bookings.update');
    Route::delete('bookings/{booking}', [AgencyBookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post('bookings/{booking}/payment-status', [AgencyBookingController::class, 'updatePaymentStatus'])->name('bookings.payment-status');
    Route::get('bookings/{booking}/invoice', [AgencyBookingController::class, 'invoice'])->name('bookings.invoice');
    Route::get('bookings/{booking}/download-invoice', [AgencyBookingController::class, 'downloadInvoicePdf'])->name('bookings.download-invoice');
    Route::get('bookings/status/{status}', [AgencyBookingController::class, 'getByStatus'])->name('bookings.status');

    Route::post('bookings/confirm', [AgencyBookingController::class, 'bookingConfirm'])->name('bookings.confirm');
    Route::post('bookings/cancel', [AgencyBookingController::class, 'bookingCancel'])->name('bookings.cancel');
    Route::post('bookings/add-note', [AgencyBookingController::class, 'bookingAddNote'])->name('bookings.add-note');

    // Destinations
    Route::resource('destinations', AgencyDestinationController::class);
    Route::put('destinations/{destination}/status', [AgencyDestinationController::class, 'updateStatus'])->name('destinations.update-status');
    Route::put('destinations/{destination}/featured', [AgencyDestinationController::class, 'updateFeatured'])->name('destinations.update-featured');
});

/*
|--------------------------------------------------------------------------
| Front Routes
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['HtmlSpecialchars', 'MaintenanceMode']], function () {

    Route::get('tourbookings', [FrontServiceController::class, 'index'])->name('tourbooking');
    Route::get('tourbookings/{slug}', [FrontServiceController::class, 'show'])->name('tourbooking.show');

    Route::group(['as' => 'payment.', 'prefix' => 'payment', 'middleware' => ['auth:web']], function () {
        Route::post('/stripe', [PaymentController::class, 'stripe_payment'])->name('stripe');
        Route::post('/bank', [PaymentController::class, 'bank_payment'])->name('bank');

        Route::get('/paypal', [PaymentController::class, 'paypal_payment'])->name('paypal');
        Route::get('/paypal-success-payment', [PaymentController::class, 'paypal_success_payment'])->name('paypal-success-payment');
        Route::get('/paypal-faild-payment', [PaymentController::class, 'paypal_faild_payment'])->name('paypal-faild-payment');

        Route::post('/razorpay', [PaymentController::class, 'razorpay_payment'])->name('razorpay');
        Route::post('/flutterwave', [PaymentController::class, 'flutterwave_payment'])->name('flutterwave');
        Route::post('/paystack', [PaymentController::class, 'paystack_payment'])->name('paystack');

        Route::get('/mollie', [PaymentController::class, 'mollie_payment'])->name('mollie');
        Route::get('/mollie-callback', [PaymentController::class, 'mollie_callback'])->name('mollie-callback');

        Route::get('/instamojo', [PaymentController::class, 'instamojo_payment'])->name('instamojo');
        Route::get('/instamojo-callback', [PaymentController::class, 'instamojo_callback'])->name('instamojo-callback');

        Route::get('/wallet', [PaymentController::class, 'wallet_payment'])->name('wallet');
    });
});

Route::group(['as' => 'front.tourbooking.', 'prefix' => 'tour-booking', 'middleware' => ['web']], function () {
    // Home/Search Page
    Route::get('/', [FrontServiceController::class, 'index'])->name('home');
    Route::get('/search', [FrontServiceController::class, 'search'])->name('search');

    // Service Types
    Route::get('/types', [FrontServiceController::class, 'serviceTypes'])->name('service-types');
    Route::get('/types/{slug}', [FrontServiceController::class, 'serviceTypeDetail'])->name('service-types.show');

    // Services
    Route::get('/services', [FrontServiceController::class, 'allServices'])->name('services');
    Route::get('/services/load', [FrontServiceController::class, 'loadServicesAjax'])->name('services.load.ajax');
    Route::get('/service/{slug}', [FrontServiceController::class, 'serviceDetail'])->name('services.show');

    /**
     * Availability (noul endpoint + alias legacy)
     * - Canonical: front.tourbooking.availability.by-date  -> /tour-booking/availability/by-date
     * - Legacy   : get.availity.by.date                    -> /tour-booking/get-availability-by-date
     */
    Route::get('/availability/by-date', [FrontServiceController::class, 'getAvailabilityByDate'])
        ->name('availability.by-date');

    Route::get('/get-availability-by-date', [FrontServiceController::class, 'getAvailityByDate'])
        ->name('get.availity.by.date');

    // Pickup Points API
    Route::get('/pickup-points', [FrontServiceController::class, 'getPickupPoints'])
        ->name('pickup-points.get');
    Route::post('/pickup-points/calculate-charge', [FrontServiceController::class, 'calculatePickupCharge'])
        ->name('pickup-points.calculate-charge');

    // Categories
    Route::get('/tours', [FrontServiceController::class, 'tours'])->name('tours');
    Route::get('/hotels', [FrontServiceController::class, 'hotels'])->name('hotels');
    Route::get('/restaurants', [FrontServiceController::class, 'restaurants'])->name('restaurants');
    Route::get('/rentals', [FrontServiceController::class, 'rentals'])->name('rentals');
    Route::get('/activities', [FrontServiceController::class, 'activities'])->name('activities');

    // Destinations
    Route::get('/destinations', [FrontServiceController::class, 'destinations'])->name('destinations');
    Route::get('/destinations/{slug}', [FrontServiceController::class, 'destinationDetail'])->name('destinations.show');

    // Booking
    Route::get('/book/checkout/view', [FrontBookingController::class, 'bookingCheckoutView'])
        ->name('book.checkout.view')->middleware('auth:web');

    // Reviews
    Route::post('/services/reviews', [FrontServiceController::class, 'storeReview'])->name('reviews.store');

    // Availability Check (checkout)
    Route::post('/check-availability', [FrontBookingController::class, 'checkAvailability'])->name('check-availability');

    // Coupons
    Route::post('/validate-coupon', [FrontBookingController::class, 'validateCoupon'])->name('validate-coupon');
});

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::group([
    'as'        => 'user.',
    'prefix'    => 'user',
    'middleware'=> ['auth:web']
], function () {

    // INVOICE (user)
    Route::get('/bookings/invoice/{booking}', [BookingInvoiceController::class, 'show'])
        ->whereNumber('booking')
        ->name('bookings.invoice');

    Route::get('/bookings/invoice/{booking}/download', [BookingInvoiceController::class, 'download'])
        ->whereNumber('booking')
        ->name('bookings.invoice.download');

    // Bookings (user)
    Route::get('/bookings', [UserBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/details/{id}', [UserBookingController::class, 'details'])
        ->whereNumber('id')
        ->name('bookings.details');
    Route::post('/bookings/cancel/{id}', [UserBookingController::class, 'cancelBooking'])
        ->whereNumber('id')
        ->name('bookings.cancel');
});
