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

Route::get('/klaim', [KlaimController::class, 'index'])
    ->name('klaim.index');

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