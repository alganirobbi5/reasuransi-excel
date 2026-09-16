<?php

namespace App\Http\Controllers;

use App\Exports\ReasuransiExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function index()
    {
        return view('export.index');
    }

    public function excel(): BinaryFileResponse
    {
        return Excel::download(
            new ReasuransiExport(),
            'laporan-reasuransi.xlsx'
        );
    }
}