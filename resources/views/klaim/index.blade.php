@extends('layouts.app')

@section('title', 'Laporan Klaim Reasuransi')

@section('content')
<div class="page-head page-head-row">
    <div>
        <h1>Laporan Klaim Reasuransi</h1>
        <p>Jumlah data: <strong>{{ $klaims->count() }}</strong>. Data tersimpan akan masuk Sheet 2 saat export Excel.</p>
    </div>
    <a class="btn" href="{{ route('klaim.create') }}">+ Tambah Klaim</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-wrap">
    @if($klaims->isEmpty())
        <div class="empty">
            <p><strong>Belum ada data klaim.</strong></p>
            <p><a class="link" href="{{ route('klaim.create') }}">Tambah data pertama</a> untuk mulai mengisi laporan.</p>
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
                    <th>UP Utama</th>
                    <th>Recovery</th>
                    <th>UP Ceded</th>
                    <th>Status Klaim</th>
                    <th>Aksi</th>
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
                        <td class="num">{{ number_format((float) $k->up_utama, 2, ',', '.') }}</td>
                        <td class="num">{{ number_format((float) $k->recovery, 2, ',', '.') }}</td>
                        <td class="num">{{ number_format((float) $k->up_ceded, 2, ',', '.') }}</td>
                        <td><span class="badge">{{ $k->status_klaim }}</span></td>
                        <td class="actions">
                            <a class="link" href="{{ route('klaim.edit', $k) }}">Ubah</a>
                            <form method="POST" action="{{ route('klaim.destroy', $k) }}" onsubmit="return confirm('Hapus data klaim {{ $k->no_klaim }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="link link-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
