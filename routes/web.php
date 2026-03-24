<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Language switcher
Route::get('/lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'sw'])) {
        abort(400);
    }
    session(['locale' => $locale]);
    return redirect()->back();
})->name('set-locale');

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::patch('mark-all-read', 'markAllAsRead')->name('mark-all-read');
        Route::patch('{id}/read', 'markAsRead')->name('read');
    });
});
