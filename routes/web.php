<?php

use App\Http\Controllers\Admin\AboutPageController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectMediaController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Site\AboutController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\ProjectController as SiteProjectController;
use App\Http\Controllers\Site\ServiceController as SiteServiceController;
use App\Http\Controllers\Site\SitemapController;
use App\Http\Controllers\Site\WorkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/projects', [WorkController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [SiteProjectController::class, 'show'])->name('projects.show');
Route::get('/services', [SiteServiceController::class, 'index'])->name('services');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/inquiries', [ContactController::class, 'store'])
    ->middleware('throttle:inquiries')
    ->name('contact.store');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

/*
|--------------------------------------------------------------------------
| Admin authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'create'])->name('login');
    Route::post('/admin/login', [LoginController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('login.store');

    Route::get('/admin/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/admin/forgot-password', [ForgotPasswordController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/admin/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/admin/reset-password', [ResetPasswordController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.store');
});

Route::post('/admin/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('projects', ProjectController::class)->except('show');
    Route::patch('projects/{project}/publish', [ProjectController::class, 'togglePublish'])->name('projects.publish');

    Route::post('projects/{project}/media', [ProjectMediaController::class, 'store'])->name('projects.media.store');
    Route::patch('projects/{project}/media/reorder', [ProjectMediaController::class, 'reorder'])->name('projects.media.reorder');
    Route::patch('media/{media}', [ProjectMediaController::class, 'update'])->name('media.update');
    Route::delete('media/{media}', [ProjectMediaController::class, 'destroy'])->name('media.destroy');

    Route::resource('services', ServiceController::class)->except('show');
    Route::patch('services/{service}/publish', [ServiceController::class, 'togglePublish'])->name('services.publish');

    Route::resource('testimonials', TestimonialController::class)->except('show');
    Route::patch('testimonials/{testimonial}/publish', [TestimonialController::class, 'togglePublish'])->name('testimonials.publish');

    Route::get('inquiries/export', [InquiryController::class, 'export'])->name('inquiries.export');
    Route::patch('inquiries/{inquiry}/status', [InquiryController::class, 'status'])->name('inquiries.status');
    Route::resource('inquiries', InquiryController::class)->only(['index', 'show', 'destroy']);

    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('about', [AboutPageController::class, 'edit'])->name('about.edit');
    Route::put('about', [AboutPageController::class, 'update'])->name('about.update');

    Route::get('account/password', [AccountController::class, 'edit'])->name('account.password.edit');
    Route::put('account/password', [AccountController::class, 'update'])->name('account.password.update');
});
