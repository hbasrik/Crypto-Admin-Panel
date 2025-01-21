<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CryptoController;


Route::get('/', [CryptoController::class, 'index'])->name('crypto.index');
Route::get('/crypto/chart-data/{id}', [CryptoController::class, 'getChartData'])->name('crypto.chart-data');
