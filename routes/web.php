<?php

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

// Filament routes are registered automatically
