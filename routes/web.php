<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UrlController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/invitation/accept/{token}', [InvitationController::class, 'accept']);
    Route::post('/invitation/accept/{token}', [InvitationController::class, 'processAccept']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UrlController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // URL Management
    Route::get('/urls', [UrlController::class, 'list'])->name('urls.index');
    Route::post('/urls', [UrlController::class, 'store'])->name('urls.store');

    // Invitation Management
    Route::get('/team', [InvitationController::class, 'index'])->name('team.index');
    Route::post('/invite', [InvitationController::class, 'invite'])->name('invite');

    // Company Management (SuperAdmin only)
    Route::middleware('role:superadmin')->group(function () {
        Route::resource('companies', CompanyController::class)->except(['show']);
    });
});

// Public Redirection
Route::get('/{shortCode}', [UrlController::class, 'redirect']);
