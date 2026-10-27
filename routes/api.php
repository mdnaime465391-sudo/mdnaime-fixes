<?php

use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{slug}', [ProjectController::class, 'show']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/testimonials', [TestimonialController::class, 'index']);
Route::get('/settings', SettingController::class);

Route::post('/inquiries', [InquiryController::class, 'store'])
    ->middleware('throttle:inquiries');
