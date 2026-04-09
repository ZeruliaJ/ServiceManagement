<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TVS\TvsWebController;
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

    // TVS Service Management System
    Route::prefix('tvs')->name('tvs.')->controller(TvsWebController::class)->group(function () {
        Route::get('/', 'dashboard')->name('dashboard');

        // Vehicles
        Route::get('vehicles', 'vehicles')->name('vehicles');
        Route::get('vehicles/{id}', 'vehicleShow')->name('vehicles.show');

        // Parties / Customers
        Route::get('parties', 'parties')->name('parties');
        Route::get('parties/create', 'partyCreate')->name('parties.create');
        Route::get('parties/{id}', 'partyShow')->name('parties.show');

        // Job Cards
        Route::get('job-cards', 'jobCards')->name('job-cards');
        Route::get('job-cards/create', 'jobCardCreate')->name('job-cards.create');
        Route::get('job-cards/{id}', 'jobCardShow')->name('job-cards.show');

        // Gate Passes
        Route::get('gate-passes', 'gatePasses')->name('gate-passes');

        // Warranties
        Route::get('warranties', 'warranties')->name('warranties');

        // Reports
        Route::get('reports', 'reports')->name('reports');
    });
});
