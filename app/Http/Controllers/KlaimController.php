<?php

namespace App\Http\Controllers;

use App\Models\Klaim;

class KlaimController extends Controller
{
    public function index()
    {
        $klaims = Klaim::latest()->get();

        return view('klaim.index', compact('klaims'));
    }
}