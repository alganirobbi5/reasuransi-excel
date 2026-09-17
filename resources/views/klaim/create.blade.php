@extends('layouts.app')

@section('title', 'Tambah Klaim — Reasuransi Excel')

@section('content')
<div class="page-head">
    <h1>Tambah Data Klaim</h1>
    <p>Isi data klaim reasuransi. Data tersimpan akan masuk Sheet 2 saat export Excel.</p>
</div>

<div class="panel">
    <form method="POST" action="{{ route('klaim.store') }}">
        @csrf
        @include('klaim._form')
        <div class="form-actions">
            <a class="btn btn-secondary" href="{{ route('klaim.index') }}">Batal</a>
            <button class="btn" type="submit">Simpan</button>
        </div>
    </form>
</div>
@endsection
