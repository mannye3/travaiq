<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\TravelController;
use Illuminate\Support\Facades\Route;

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return "Cache cleared successfully!";
});

Route::get('/', function () {
    return view('pages.welcome');
});

Route::get('/create-plan', function () {
    return view('pages.createPlan');
})->name('createPlan');

// Admin Section
Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('google.redirect');

// Authentication Routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
Route::post('/google-onetap-login', [App\Http\Controllers\Auth\GoogleOneTapController::class, 'login']);
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

Route::get('/admin-login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login-post', [AuthController::class, 'loginPost'])->name('loginPost');
Route::post('/forget-password', [AuthController::class, 'userForgetpassword'])->name('userForgetpassword');
Route::get('/forget-password', [AuthController::class, 'showForgetPasswordForm'])->name('forgetPassword');
Route::get('/reset-password/{token}', [AuthController::class, 'resetpassword']);
Route::post('/reset-password-submit', [AuthController::class, 'resetpasswordsubmit'])->name('resetpasswordsubmit');
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth');

// Travel Routes
Route::get('/travel/form', [TravelController::class, 'showForm'])->name('travel.form');
Route::post('/travel/generate', [TravelController::class, 'generateTravelPlan'])->name('travel.generate');
Route::get('/my-trips', [TravelController::class, 'myTrips'])->name('my.trips')->middleware('auth');
Route::get('/download-itinerary/{tripId}', [TravelController::class, 'downloadTrip'])->name('download.itinerary');

// Specific routes first
Route::get('/trips/temp', [TravelController::class, 'showTemp'])->name('trips.show.temp');
Route::get('/trips/reference/{referenceCode}', [TravelController::class, 'showByReference'])->name('trips.show.reference');
Route::get('/update-missing-images', [TravelController::class, 'updateMissingImages'])->name('update.missing.images');

// Parameterized routes last
Route::get('/trips/{tripId}', [TravelController::class, 'show'])->name('trips.show');

// Route for generating travel plan (assuming you already have this)
Route::post('/generate-travel-plan', [TravelController::class, 'generateTravelPlan'])->name('travel.generate');

