@extends('layouts.app')

@section('title', 'Laporan Produksi & Premi Reasuransi')

@section('content')
<div class="page-head page-head-row">
    <div>
        <h1>Laporan Produksi &amp; Premi Reasuransi</h1>
        <p>Jumlah data: <strong>{{ $produksis->count() }}</strong>. Total premi dihitung dinamis dari data tersimpan pada Sheet 1 hasil export.</p>
    </div>
    <a class="btn" href="{{ route('produksi.create') }}">+ Tambah Produksi</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-wrap">
    @if($produksis->isEmpty())
        <div class="empty">
            <p><strong>Belum ada data produksi.</strong></p>
            <p><a class="link" href="{{ route('produksi.create') }}">Tambah data pertama</a> untuk mulai mengisi laporan.</p>
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
                    <th>Aksi</th>
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
                        <td class="actions">
                            <a class="link" href="{{ route('produksi.edit', $p) }}">Ubah</a>
                            <form method="POST" action="{{ route('produksi.destroy', $p) }}" onsubmit="return confirm('Hapus data polis {{ $p->no_polis }}?')">
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
