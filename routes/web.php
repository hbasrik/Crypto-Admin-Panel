<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CryptoController;
use Illuminate\Support\Facades\Auth;

// Authentication routes
Auth::routes();

// Redirect to login if not authenticated
Route::get('/', function () {
    return redirect('login');
});

// Routes protected by 'auth' middleware
Route::middleware(['auth'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [CryptoController::class, 'index'])->name('dashboard');

    // Redirect '/home' to '/dashboard'
    Route::get('/home', function () {
        return redirect('/dashboard');
    });

    // Crypto routes
    Route::get('/crypto', [CryptoController::class, 'index'])->name('crypto.index');
    Route::get('/crypto/chart-data/{id}', [CryptoController::class, 'getChartData'])->name('crypto.chart-data');
});
