@extends('layouts.app')

@section('title', 'Dashboard — Reasuransi Excel')

@section('content')
<div class="page-head">
    <h1>Dashboard Laporan Reasuransi</h1>
    <p>Aplikasi pengelolaan laporan reasuransi untuk produksi, klaim, dan export Excel berdasarkan template workbook 3 sheet.</p>
</div>

<div class="grid">
    <div class="card">
        <h3>Total Produksi</h3>
        <div class="value">{{ number_format((int) ($totalProduksi ?? 0), 0, ',', '.') }}</div>
        <div class="sub">Data produksi &amp; premi reasuransi</div>
    </div>
    <div class="card">
        <h3>Total Klaim</h3>
        <div class="value">{{ number_format((int) ($totalKlaim ?? 0), 0, ',', '.') }}</div>
        <div class="sub">Data klaim reasuransi</div>
    </div>
    <div class="card">
        <h3>Total Premi Reasuransi</h3>
        <div class="value">Rp {{ number_format((float) ($totalPremi ?? 0), 2, ',', '.') }}</div>
        <div class="sub">Akumulasi premi reasuransi</div>
    </div>
    <div class="card">
        <h3>Total Recovery Klaim</h3>
        <div class="value">Rp {{ number_format((float) ($totalRecovery ?? 0), 2, ',', '.') }}</div>
        <div class="sub">Akumulasi recovery klaim</div>
    </div>
</div>

<div class="panel">
    <h2>Alur Laporan</h2>
    <p>Workbook Excel hasil export berisi 3 sheet: <strong>Laporan Produksi &amp; Premi Reasur</strong>, <strong>Laporan Klaim Reasuransi</strong>, dan <strong>Ringkasan Akun Keuangan</strong>. Total premi dan saldo pada workbook dihitung otomatis dari data yang tersimpan.</p>
    <div class="btn-row">
        <a class="btn" href="{{ route('produksi.create') }}">+ Tambah Produksi</a>
        <a class="btn" href="{{ route('klaim.create') }}">+ Tambah Klaim</a>
        <a class="btn btn-secondary" href="{{ route('export.excel') }}">Unduh Excel</a>
    </div>
</div>
@endsection
