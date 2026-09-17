<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\KlaimController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;
use App\Exports\TestExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/produksi', [ProduksiController::class, 'index'])
    ->name('produksi.index');

Route::get('/produksi/create', [ProduksiController::class, 'create'])
    ->name('produksi.create');

Route::post('/produksi', [ProduksiController::class, 'store'])
    ->name('produksi.store');

Route::get('/produksi/{produksi}/edit', [ProduksiController::class, 'edit'])
    ->name('produksi.edit');

Route::put('/produksi/{produksi}', [ProduksiController::class, 'update'])
    ->name('produksi.update');

Route::delete('/produksi/{produksi}', [ProduksiController::class, 'destroy'])
    ->name('produksi.destroy');

Route::get('/klaim', [KlaimController::class, 'index'])
    ->name('klaim.index');

Route::get('/klaim/create', [KlaimController::class, 'create'])
    ->name('klaim.create');

Route::post('/klaim', [KlaimController::class, 'store'])
    ->name('klaim.store');

Route::get('/klaim/{klaim}/edit', [KlaimController::class, 'edit'])
    ->name('klaim.edit');

Route::put('/klaim/{klaim}', [KlaimController::class, 'update'])
    ->name('klaim.update');

Route::delete('/klaim/{klaim}', [KlaimController::class, 'destroy'])
    ->name('klaim.destroy');

Route::get('/export', [ExportController::class, 'index'])
    ->name('export.index');

Route::get('/export/excel', [ExportController::class, 'excel'])
    ->name('export.excel');

Route::get('/test-excel', function () {
    return Excel::download(
        new TestExport(),
        'test-reasuransi.xlsx'
    );
});    