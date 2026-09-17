@extends('layouts.app')

@section('title', 'Ubah Produksi — Reasuransi Excel')

@section('content')
<div class="page-head">
    <h1>Ubah Data Produksi</h1>
    <p>Polis: <strong>{{ $produksi->no_polis }}</strong> — {{ $produksi->nama_tertanggung }}</p>
</div>

<div class="panel">
    <form method="POST" action="{{ route('produksi.update', $produksi) }}">
        @csrf
        @method('PUT')
        @include('produksi._form', ['produksi' => $produksi])
        <div class="form-actions">
            <a class="btn btn-secondary" href="{{ route('produksi.index') }}">Batal</a>
            <button class="btn" type="submit">Perbarui</button>
        </div>
    </form>
</div>
@endsection
