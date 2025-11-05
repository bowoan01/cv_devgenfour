<?php

use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectImageController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Site\AboutController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\PortfolioController;
use App\Http\Controllers\Site\ServiceController as SiteServiceController;
use Illuminate\Support\Facades\Route;

// Public website
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/services', [SiteServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [SiteServiceController::class, 'show'])->name('services.show');
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [PortfolioController::class, 'show'])->name('portfolio.show');
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Authentication (Laravel Breeze)
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Admin panel (no prefix, middleware protected)
Route::middleware(['auth', 'verified', 'role:Admin|Editor'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/services/manage', [AdminServiceController::class, 'index'])->name('admin.services.index');
    Route::post('/services', [AdminServiceController::class, 'store'])->name('admin.services.store');
    Route::put('/services/{id}', [AdminServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/services/{id}', [AdminServiceController::class, 'destroy'])->name('admin.services.destroy');

    Route::get('/projects/manage', [ProjectController::class, 'index'])->name('admin.projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
    Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('admin.projects.update');
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('admin.projects.destroy');
    Route::put('/projects/{id}/reorder', [ProjectController::class, 'reorder'])->name('admin.projects.reorder');

    Route::post('/projects/{id}/images', [ProjectImageController::class, 'store'])->name('admin.projects.images.store');
    Route::delete('/projects/images/{imageId}', [ProjectImageController::class, 'destroy'])->name('admin.projects.images.destroy');

    Route::get('/teams/manage', [TeamController::class, 'index'])->name('admin.teams.index');
    Route::post('/teams', [TeamController::class, 'store'])->name('admin.teams.store');
    Route::put('/teams/{id}', [TeamController::class, 'update'])->name('admin.teams.update');
    Route::delete('/teams/{id}', [TeamController::class, 'destroy'])->name('admin.teams.destroy');

    Route::get('/contacts', [ContactMessageController::class, 'index'])->name('admin.contacts.index');
    Route::get('/contacts/{id}', [ContactMessageController::class, 'show'])->name('admin.contacts.show');
    Route::patch('/contacts/{id}/read', [ContactMessageController::class, 'markAsRead'])->name('admin.contacts.read');
    Route::delete('/contacts/{id}', [ContactMessageController::class, 'destroy'])->name('admin.contacts.destroy');

    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/profile/change-password', [AdminProfileController::class, 'changePassword'])->name('admin.profile.password');
});
