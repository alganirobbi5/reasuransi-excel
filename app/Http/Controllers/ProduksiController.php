<?php

namespace App\Http\Controllers;

use App\Models\Produksi;

class ProduksiController extends Controller
{
    public function index()
    {
        $produksis = Produksi::latest()->get();

        return view('produksi.index', compact('produksis'));
    }
}