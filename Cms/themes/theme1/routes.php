<?php

use Illuminate\Support\Facades\Route;
use Cms\Themes\Theme1\Src\Http\Controllers\Theme1Controller;

/*
|--------------------------------------------------------------------------
| Theme Routes
|--------------------------------------------------------------------------
|
| Here is where you can register theme-specific routes for your theme.
| These routes are loaded by the ThemeServiceProvider within a theme group which
| contains the "web" middleware. Now create something great!
|
*/

Route::middleware(['web'])->group(function () {
    Route::get('/demo', [Theme1Controller::class, 'demo'])->name('theme1.demo');
});