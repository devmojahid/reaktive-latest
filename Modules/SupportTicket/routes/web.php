<?php

use Illuminate\Support\Facades\Route;
use Modules\SupportTicket\App\Http\Controllers\ServiceQuery\User\ServiceQueryController as UserServiceQueryController;
use Modules\SupportTicket\App\Http\Controllers\ServiceQuery\Seller\ServiceQueryController as SellerServiceQueryController;
use Modules\SupportTicket\App\Http\Controllers\Support\Admin\SupportTicketController as AdminSupportTicketController;
use Modules\SupportTicket\App\Http\Controllers\Support\User\SupportTicketController as UserSupportTicketController;
use Modules\SupportTicket\App\Http\Controllers\Support\Seller\SupportTicketController as SellerSupportTicketController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/





Route::group(['as' => 'user.', 'prefix' => 'user', 'middleware' => ['auth:web', 'HtmlSpecialchars', 'MaintenanceMode']], function () {

    Route::resource('support-ticket', UserSupportTicketController::class);
    Route::post('support-ticket-message/{id}', [UserSupportTicketController::class, 'support_ticket_message'])->name('support-ticket-message');

    Route::resource('agency-support', UserServiceQueryController::class);
    Route::post('agency-support-message/{id}', [UserServiceQueryController::class, 'support_ticket_message'])->name('agency-support-message');
});


Route::group(['as' => 'agency.', 'prefix' => 'agency', 'middleware' => ['auth:web', 'HtmlSpecialchars', 'MaintenanceMode']], function () {

    Route::resource('support-ticket', SellerSupportTicketController::class);
    Route::post('support-ticket-message/{id}', [SellerSupportTicketController::class, 'support_ticket_message'])->name('support-ticket-message');

    Route::get('agency-supports', [SellerServiceQueryController::class, 'index'])->name('agency-supports');
    Route::get('agency-support/{id}', [SellerServiceQueryController::class, 'show'])->name('agency-support');
    Route::post('agency-support-message/{id}', [SellerServiceQueryController::class, 'support_ticket_message'])->name('agency-support-message');
    Route::put('agency-support-close/{id}', [SellerServiceQueryController::class, 'close'])->name('agency-support-close');
});


Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'HtmlSpecialchars', 'MaintenanceMode']], function () {

    Route::get('support-tickets', [AdminSupportTicketController::class, 'index'])->name('support-tickets');
    Route::get('support-ticket/{id}', [AdminSupportTicketController::class, 'show'])->name('support-ticket');
    Route::post('support-ticket-message/{id}', [AdminSupportTicketController::class, 'support_ticket_message'])->name('support-ticket-message');
    Route::delete('support-ticket-delete/{id}', [AdminSupportTicketController::class, 'destroy'])->name('support-ticket-delete');
    Route::put('support-ticket-close/{id}', [AdminSupportTicketController::class, 'close'])->name('support-ticket-close');
});
