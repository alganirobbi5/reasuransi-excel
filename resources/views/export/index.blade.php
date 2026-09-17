@extends('layouts.app')

@section('title', 'Export Laporan Excel')

@section('content')
<div class="page-head">
    <h1>Export Laporan Excel</h1>
    <p>Unduh satu workbook Excel berdasarkan template <strong>BRF_Laporan Reas(1).xlsx</strong> dengan 3 sheet berikut. Data diambil langsung dari database saat ini.</p>
</div>

<div class="panel">
    <h2>Sheet Workbook</h2>
    <ol>
        <li>Laporan Produksi &amp; Premi Reasur</li>
        <li>Laporan Klaim Reasuransi</li>
        <li>Ringkasan Akun Keuangan</li>
    </ol>
    <p>
        <a class="btn" href="{{ route('export.excel') }}">Unduh laporan-reasuransi.xlsx</a>
    </p>
    <p class="sub" style="color: var(--muted); font-size: 0.85rem;">Total premi dan saldo dihitung otomatis dengan formula Excel dari data Produksi dan Klaim yang tersimpan.</p>
</div>
@endsection
