<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\Klaim;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProduksi = Produksi::count();
        $totalKlaim = Klaim::count();

        $totalPremi = Produksi::sum('premi_reasuransi');
        $totalRecovery = Klaim::sum('recovery');

        return view('dashboard', compact(
            'totalProduksi',
            'totalKlaim',
            'totalPremi',
            'totalRecovery'
        ));
    }
}