<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DomainController as AdminDomainController;
use App\Http\Controllers\Admin\WafController;
use App\Http\Controllers\Panel\DashboardController as PanelDashboardController;
use App\Http\Controllers\Panel\DomainController as PanelDomainController;
use App\Http\Controllers\Waf\ChallengeController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Auth
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// WAF Challenge (public — no auth required)
Route::get('/waf/challenge', [ChallengeController::class, 'show'])->name('waf.challenge');
Route::post('/waf/verify', [ChallengeController::class, 'verify'])->name('waf.verify');

// Admin Panel
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('/domains', AdminDomainController::class);

    // WAF Administration
    Route::prefix('waf')->name('waf.')->group(function () {
        Route::get('/rules', [WafController::class, 'rules'])->name('rules');
        Route::get('/rules/create', [WafController::class, 'createRule'])->name('rules.create');
        Route::post('/rules', [WafController::class, 'storeRule'])->name('rules.store');
        Route::post('/rules/{rule}/toggle', [WafController::class, 'toggleRule'])->name('rules.toggle');
        Route::delete('/rules/{rule}', [WafController::class, 'destroyRule'])->name('rules.destroy');
        Route::get('/logs', [WafController::class, 'logs'])->name('logs');
    });
});

// User Panel
Route::prefix('panel')->name('panel.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [PanelDashboardController::class, 'index'])->name('dashboard');
    Route::resource('/domains', PanelDomainController::class);
});
