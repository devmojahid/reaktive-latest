<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Admin\ProfileController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Auth\LoginController;

use App\Http\Controllers\Admin\FrontEndManagementController;
use App\Http\Controllers\Auth\LoginController as UserLoginController;
use App\Http\Controllers\Auth\RegisterController as UserRegisterController;


use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\Agency\ProfileController as AgencyProfileController;
use App\Http\Controllers\User\OrderController;
use Illuminate\Support\Facades\Artisan;

Route::group(['middleware' => ['HtmlSpecialchars', 'MaintenanceMode']], function () {

    Route::get('migrate', function () {
        Artisan::call('migrate');
    });

    Route::get('clear', function () {
        Artisan::call('optimize:clear');
    });

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/theme/{theme}', [HomeController::class, 'switchTheme'])->name('theme.switch');
    Route::get('/home', [HomeController::class, 'themeVariation'])->name('theme.variation');

    Route::get('/about-us', [HomeController::class, 'about_us'])->name('about-us');

    Route::get('/blogs', [HomeController::class, 'blogs'])->name('blogs');
    Route::get('/blog/{slug}', [HomeController::class, 'blog'])->name('blog');
    Route::post('/store-blog-comment/{id}', [HomeController::class, 'store_blog_comment'])->name('store-blog-comment');

    Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
    Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');

    Route::get('/privacy-policy', [HomeController::class, 'privacy_policy'])->name('privacy-policy');
    Route::get('/terms-conditions', [HomeController::class, 'terms_conditions'])->name('terms-conditions');

    Route::get('/custom-page/{slug}', [HomeController::class, 'custom_page'])->name('custom-page');

    Route::get('/contact-us', [HomeController::class, 'contact_us'])->name('contact-us');

    Route::get('/language-switcher', [HomeController::class,  'language_switcher'])->name('language-switcher');
    Route::get('/currency-switcher', [HomeController::class, 'currency_switcher'])->name('currency-switcher');


    Route::get('/download-file/{file}', [HomeController::class, 'download_file'])->name('download-file');


    Route::get('/teams', [HomeController::class, 'teams'])->name('teams');
    Route::get('/team/{slug}', [HomeController::class, 'teamPerson'])->name('teamPerson');

    Auth::routes();

    Route::group(['as' => 'user.', 'prefix' => 'user'], function () {
        Route::controller(UserLoginController::class)->group(function () {

            Route::get('/login', 'custom_login_page')->name('login');
            Route::post('/store-login', 'store_login')->name('store-login');
            Route::get('/logout', 'student_logout')->name('logout');

            Route::get('login/google', 'redirect_to_google')->name('login-google');
            Route::get('/callback/google', 'google_callback')->name('callback-google');

            Route::get('login/facebook', 'redirect_to_facebook')->name('login-facebook');
            Route::get('/callback/facebook', 'facebook_callback')->name('callback-facebook');

            Route::get('/forget-password', 'custom_forget_page')->name('forget-password');

            Route::post('/send-forget-password', 'send_custom_forget_pass')->name('send-forget-password');
            Route::get('/reset-password', 'custom_reset_password')->name('reset-password');
            Route::post('/store-reset-password/{token}', 'store_reset_password')->name('store-reset-password');

            Route::controller(UserRegisterController::class)->group(function () {
                Route::get('/register', 'custom_register_page')->name('register');
                Route::post('/store-register', 'store_register')->name('store-register');
                Route::get('/register-verification', 'register_verification')->name('register-verification');
            });
        });
    });


    Route::group(['as' => 'user.', 'prefix' => 'user'], function () {

        Route::group(['middleware' => 'auth:web'], function () {

            Route::get('/dashboard', [UserProfileController::class, 'dashboard'])->name('dashboard');

            Route::get('/edit-profile', [UserProfileController::class, 'edit_profile'])->name('edit-profile');
            Route::put('/update-profile', [UserProfileController::class, 'update_profile'])->name('update-profile');

            Route::get('/change-password', [UserProfileController::class, 'change_password'])->name('change-password');
            Route::put('/update-password', [UserProfileController::class, 'update_password'])->name('update-password');


            Route::get('/create-agency', [UserProfileController::class, 'create_agency'])->name('create-agency');
            Route::post('/agency-application', [UserProfileController::class, 'agency_application'])->name('agency-application');

            Route::get('/account-delete', [UserProfileController::class, 'account_delete'])->name('account-delete');
            Route::delete('/confirm-account-delete', [UserProfileController::class, 'confirm_account_delete'])->name('confirm-account-delete');


            // orders
            Route::get('/orders', [OrderController::class, 'orders'])->name('orders');
            Route::get('/order/details/{order_id}', [OrderController::class, 'order_show'])->name('order_show');
            Route::get('/transactions/history', [OrderController::class, 'transactions'])->name('transactions');
        });
    });


    Route::group(['as' => 'agency.', 'prefix' => 'agency'], function () {

        Route::group(['middleware' => ['auth:web', 'CheckAgency']], function () {

            Route::get('/migrate', function () {
                Artisan::call('migrate');
                return 'Migration completed!';
            });

            Route::get('/clear', function () {
                Artisan::call('optimize:clear');
                return 'Cache cleared!';
            });

            Route::get('/link', function () {
                Artisan::call('storage:link');
                return 'Storage link created!';
            });

            Route::get('/dashboard', [AgencyProfileController::class, 'dashboard'])->name('dashboard');

            Route::get('/edit-profile', [AgencyProfileController::class, 'edit_profile'])->name('edit-profile');
            Route::put('/update-profile', [AgencyProfileController::class, 'update_profile'])->name('update-profile');

            Route::get('/agency-profile', [AgencyProfileController::class, 'agency_profile'])->name('agency-profile');
            Route::put('/update-agency-profile', [AgencyProfileController::class, 'update_agency_profile'])->name('update-agency-profile');

            Route::get('/change-password', [AgencyProfileController::class, 'change_password'])->name('change-password');
            Route::put('/update-password', [AgencyProfileController::class, 'update_password'])->name('update-password');

            Route::get('/account-delete', [AgencyProfileController::class, 'account_delete'])->name('account-delete');
            Route::delete('/confirm-account-delete', [AgencyProfileController::class, 'confirm_account_delete'])->name('confirm-account-delete');
        });
    });
});



Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {

    Route::get('login', [LoginController::class, 'custom_login_page'])->name('login');
    Route::post('store-login', [LoginController::class, 'store_login'])->name('store-login');
    Route::post('store-register', [LoginController::class, 'store_register'])->name('store-register');
    Route::post('logout', [LoginController::class, 'admin_logout'])->name('logout');


    Route::group(['middleware' => ['auth:admin']], function () {

        Route::get('/', [DashboardController::class, 'dashboard']);
        Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

        Route::controller(ProfileController::class)->group(function () {
            Route::get('edit-profile', 'edit_profile')->name('edit-profile');
            Route::put('profile-update', 'profile_update')->name('profile-update');
            Route::put('update-password', 'update_password')->name('update-password');
        });

        // Menu Management System
        Route::controller(App\Http\Controllers\Admin\MenuController::class)->group(function () {
            Route::get('menus', 'index')->name('menus.index');
            Route::get('menus/create', 'create')->name('menus.create');
            Route::post('menus', 'store')->name('menus.store');
            Route::get('menus/{id}/edit', 'edit')->name('menus.edit');
            Route::put('menus/{id}', 'update')->name('menus.update');
            Route::delete('menus/{id}', 'destroy')->name('menus.destroy');

            // Menu Items
            Route::post('menus/{id}/add-item', 'addMenuItem')->name('menus.add-item');
            Route::put('menu-items/{id}', 'updateMenuItem')->name('menu-items.update');
            Route::delete('menu-items/{id}', 'deleteMenuItem')->name('menu-items.destroy');

            // Get menu item data for editing
            Route::get('menu-items/{id}/edit', 'getMenuItem')->name('menu-items.edit');

            // Update menu structure (order and hierarchy)
            Route::post('menus/update-structure', 'updateMenuStructure')->name('menus.update-structure');
        });

        Route::controller(UserController::class)->group(function () {
            Route::get('user-list', 'user_list')->name('user-list');
            Route::get('pending-user', 'pending_user')->name('pending-user');
            Route::get('user-show/{id}', 'user_show')->name('user-show');
            Route::delete('user-delete/{id}', 'user_destroy')->name('user-delete');
            Route::put('user-status/{id}', 'user_status')->name('user-status');
            Route::put('user-update/{id}', 'update')->name('user-update');

            Route::get('seller-list', 'seller_list')->name('seller-list');
            Route::get('pending-seller', 'pending_seller')->name('pending-seller');
            Route::get('seller-show/{id}', 'seller_show')->name('seller-show');

            Route::get('seller-joining-request', 'seller_joining_request')->name('seller-joining-request');
            Route::get('seller-joining-detail/{id}', 'seller_joining_detail')->name('seller-joining-detail');
            Route::put('seller-joining-approval/{id}', 'seller_joining_approval')->name('seller-joining-approval');
            Route::put('seller-joining-reject/{id}', 'seller_joining_reject')->name('seller-joining-reject');
        });

        // Theme Management
        Route::controller(App\Http\Controllers\Admin\ThemeController::class)->group(function () {
            Route::get('themes', 'index')->name('themes.index');
            Route::get('themes/create', 'create')->name('themes.create');
            Route::get('themes/{theme}', 'show')->name('themes.show');
            Route::post('themes/{theme}/activate', 'activate')->name('themes.activate');
            Route::delete('themes/{theme}', 'destroy')->name('themes.destroy');
        });

        // Frontend Management
        Route::controller(FrontEndManagementController::class)->name('front-end.')->group(function () {
            Route::get('/frontend-section', 'index')->name('frontend-section');
            Route::get('/section/{id}', 'section')->name('section');
            Route::put('store/{key}/{id?}', 'store')->name('store');
            Route::get('/frontend-field-template', 'getFieldTemplate')->name('field-template');
            Route::post('/upload-image', [App\Http\Controllers\Admin\UploadController::class, 'editorImage'])->name('upload-image');
        });
    });
});
