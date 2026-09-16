@extends('layouts.app')

@section('title', 'Laporan Produksi & Premi Reasuransi')

@section('content')
<div class="page-head">
    <h1>Laporan Produksi &amp; Premi Reasuransi</h1>
    <p>Halaman dasar data produksi reasuransi. Jumlah data: <strong>{{ $produksis->count() }}</strong>. Form input produksi (scope Renno) belum dibuat pada phase ini.</p>
</div>

<div class="table-wrap">
    @if($produksis->isEmpty())
        <div class="empty">
            <p><strong>Belum ada data produksi.</strong></p>
            <p>Data akan tampil di sini setelah modul input Produksi tersedia.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Polis</th>
                    <th>Nama Tertanggung</th>
                    <th>Tanggal Lahir</th>
                    <th>UP Utama</th>
                    <th>Retention</th>
                    <th>UP Ceded</th>
                    <th>Jenis Reasuransi</th>
                    <th>Premi Reasuransi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produksis as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $p->no_polis }}</td>
                        <td>{{ $p->nama_tertanggung }}</td>
                        <td>{{ $p->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
                        <td class="num">{{ number_format((float) $p->up_utama, 2, ',', '.') }}</td>
                        <td class="num">{{ number_format((float) $p->retention, 2, ',', '.') }}</td>
                        <td class="num">{{ number_format((float) $p->up_ceded, 2, ',', '.') }}</td>
                        <td>{{ $p->jenis_reasuransi }}</td>
                        <td class="num">{{ number_format((float) $p->premi_reasuransi, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
