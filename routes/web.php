<?php

use App\Http\Controllers\FormsController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// Public Website Routes
Route::get('/', [PublicController::class, 'home'])->name('home');

// Properties
Route::get('/properties', [PublicController::class, 'properties'])->name('properties.index');
Route::get('/properties/{property}', [PublicController::class, 'propertyShow'])->name('properties.show');

// Agents
Route::get('/agents', [PublicController::class, 'agents'])->name('agents.index');
Route::get('/agents/{agent}', [PublicController::class, 'agentShow'])->name('agents.show');

// Contact
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicController::class, 'storeContact'])->name('contact.store');

// Inquiries
Route::post('/inquiry', [PublicController::class, 'storeInquiry'])->name('inquiry.store');

// Public consultation / listing forms
Route::prefix('forms')->name('forms.')->controller(FormsController::class)->group(function () {
    Route::get('/', 'index')->name('index');

    Route::get('land-purchase', 'landPurchase')->name('land-purchase');
    Route::get('land-sale', 'landSale')->name('land-sale');
    Route::get('rental-consultation', 'rentalConsultation')->name('rental-consultation');
    Route::get('property-listing', 'builtPropertyListing')->name('property-listing');
    Route::get('purchase-build-property', 'purchaseBuildProperty')->name('purchase-build-property');
    Route::get('customer-feedback', 'customerFeedback')->name('customer-feedback');
    Route::get('maintenance-request', 'maintenanceRequest')->name('maintenance-request');
    Route::get('pet-application', 'petApplication')->name('pet-application');
    Route::get('thank-you/{type}', 'thankYou')->name('thank-you');

    // Throttled per IP + path (named limiter "public-form" defined in
    // AppServiceProvider). Each form gets its own bucket so a user
    // submitting two different forms back-to-back isn't blocked.
    Route::middleware('throttle:public-form')->group(function () {
        Route::post('land-purchase', 'storeLandPurchase')->name('land-purchase.store');
        Route::post('land-sale', 'storeLandSale')->name('land-sale.store');
        Route::post('rental-consultation', 'storeRentalConsultation')->name('rental-consultation.store');
        Route::post('property-listing', 'storeBuiltPropertyListing')->name('property-listing.store');
        Route::post('purchase-build-property', 'storePurchaseBuildProperty')->name('purchase-build-property.store');
        Route::post('customer-feedback', 'storeCustomerFeedback')->name('customer-feedback.store');
        Route::post('maintenance-request', 'storeMaintenanceRequest')->name('maintenance-request.store');
        Route::post('pet-application', 'storePetApplication')->name('pet-application.store');
    });
});

// Filament routes are registered automatically
