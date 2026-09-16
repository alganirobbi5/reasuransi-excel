@extends('layouts.app')

@section('title', 'Export Laporan Excel')

@section('content')
<div class="page-head">
    <h1>Export Laporan Excel</h1>
    <p>Aplikasi akan menghasilkan satu workbook Excel berdasarkan template <strong>BRF_Laporan Reas(1).xlsx</strong> dengan 3 sheet berikut.</p>
</div>

<div class="panel">
    <h2>Sheet Workbook</h2>
    <ol>
        <li>Laporan Produksi &amp; Premi Reasur</li>
        <li>Laporan Klaim Reasuransi</li>
        <li>Ringkasan Akun Keuangan</li>
    </ol>
    <p>
        <button class="btn" type="button" disabled>Export Excel — Akan tersedia setelah modul export selesai</button>
    </p>
    <p class="sub" style="color: var(--muted); font-size: 0.85rem;">Implementasi export penuh (scope Robbi, integrasi modul Renno &amp; William) dikerjakan setelah fondasi stabil.</p>
</div>
@endsection
