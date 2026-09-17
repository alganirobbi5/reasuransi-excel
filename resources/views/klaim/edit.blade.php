@extends('layouts.app')

@section('title', 'Ubah Klaim — Reasuransi Excel')

@section('content')
<div class="page-head">
    <h1>Ubah Data Klaim</h1>
    <p>Klaim: <strong>{{ $klaim->no_klaim }}</strong> — {{ $klaim->nama_tertanggung }}</p>
</div>

<div class="panel">
    <form method="POST" action="{{ route('klaim.update', $klaim) }}">
        @csrf
        @method('PUT')
        @include('klaim._form', ['klaim' => $klaim])
        <div class="form-actions">
            <a class="btn btn-secondary" href="{{ route('klaim.index') }}">Batal</a>
            <button class="btn" type="submit">Perbarui</button>
        </div>
    </form>
</div>
@endsection
