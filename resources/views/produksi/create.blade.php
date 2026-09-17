@extends('layouts.app')

@section('title', 'Tambah Produksi — Reasuransi Excel')

@section('content')
<div class="page-head">
    <h1>Tambah Data Produksi</h1>
    <p>Isi data polis dan premi reasuransi. Data tersimpan akan masuk Sheet 1 saat export Excel.</p>
</div>

<div class="panel">
    <form method="POST" action="{{ route('produksi.store') }}">
        @csrf
        @include('produksi._form')
        <div class="form-actions">
            <a class="btn btn-secondary" href="{{ route('produksi.index') }}">Batal</a>
            <button class="btn" type="submit">Simpan</button>
        </div>
    </form>
</div>
@endsection
