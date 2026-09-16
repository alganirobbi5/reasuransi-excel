@extends('layouts.app')

@section('title', 'Laporan Klaim Reasuransi')

@section('content')
<div class="page-head">
    <h1>Laporan Klaim Reasuransi</h1>
    <p>Halaman dasar data klaim reasuransi. Jumlah data: <strong>{{ $klaims->count() }}</strong>. Form input klaim (scope William) belum dibuat pada phase ini.</p>
</div>

<div class="table-wrap">
    @if($klaims->isEmpty())
        <div class="empty">
            <p><strong>Belum ada data klaim.</strong></p>
            <p>Data akan tampil di sini setelah modul input Klaim tersedia.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Klaim</th>
                    <th>No Polis</th>
                    <th>Nama Tertanggung</th>
                    <th>Penyebab Klaim</th>
                    <th>Tanggal Lahir</th>
                    <th>Total Nilai Klaim</th>
                    <th>Recovery</th>
                    <th>UP Ceded</th>
                    <th>Status Klaim</th>
                </tr>
            </thead>
            <tbody>
                @foreach($klaims as $index => $k)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $k->no_klaim }}</td>
                        <td>{{ $k->no_polis }}</td>
                        <td>{{ $k->nama_tertanggung }}</td>
                        <td>{{ $k->penyebab_klaim }}</td>
                        <td>{{ $k->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
                        <td class="num">{{ number_format((float) $k->total_nilai_klaim, 2, ',', '.') }}</td>
                        <td class="num">{{ number_format((float) $k->recovery, 2, ',', '.') }}</td>
                        <td class="num">{{ number_format((float) $k->up_ceded, 2, ',', '.') }}</td>
                        <td><span class="badge">{{ $k->status_klaim }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
