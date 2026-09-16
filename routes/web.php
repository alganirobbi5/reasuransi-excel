<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\KlaimController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/produksi', [ProduksiController::class, 'index'])
    ->name('produksi.index');

Route::get('/klaim', [KlaimController::class, 'index'])
    ->name('klaim.index');

Route::get('/export', [ExportController::class, 'index'])
    ->name('export.index');