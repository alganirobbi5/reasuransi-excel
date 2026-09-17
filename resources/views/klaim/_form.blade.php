{{-- Shared Klaim form fields. Expects optional $klaim for edit repopulation. --}}
@if(isset($errors) && $errors->any())
    <div class="alert alert-error">
        <strong>Data belum tersimpan. Periksa kembali isian berikut:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="form-grid">
    <div class="form-group">
        <label for="no_klaim">No Klaim</label>
        <input type="text" id="no_klaim" name="no_klaim" required maxlength="50"
            value="{{ old('no_klaim', $klaim->no_klaim ?? '') }}" placeholder="cth: KLM-000123">
        @error('no_klaim')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="no_polis">No Polis</label>
        <input type="text" id="no_polis" name="no_polis" required maxlength="50"
            value="{{ old('no_polis', $klaim->no_polis ?? '') }}" placeholder="cth: AJ-000123">
        @error('no_polis')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="nama_tertanggung">Nama Tertanggung</label>
        <input type="text" id="nama_tertanggung" name="nama_tertanggung" required maxlength="255"
            value="{{ old('nama_tertanggung', $klaim->nama_tertanggung ?? '') }}">
        @error('nama_tertanggung')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="penyebab_klaim">Penyebab Klaim</label>
        <input type="text" id="penyebab_klaim" name="penyebab_klaim" required maxlength="255"
            value="{{ old('penyebab_klaim', $klaim->penyebab_klaim ?? '') }}" placeholder="cth: Sakit">
        @error('penyebab_klaim')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="tanggal_lahir">Tanggal Lahir</label>
        <input type="date" id="tanggal_lahir" name="tanggal_lahir" required max="{{ date('Y-m-d') }}"
            value="{{ old('tanggal_lahir', isset($klaim) ? $klaim->tanggal_lahir?->format('Y-m-d') : '') }}">
        @error('tanggal_lahir')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="status_klaim">Status Klaim</label>
        <input type="text" id="status_klaim" name="status_klaim" required maxlength="50" list="status-klaim-list"
            value="{{ old('status_klaim', $klaim->status_klaim ?? '') }}" placeholder="cth: Dalam Investigasi">
        <datalist id="status-klaim-list">
            <option value="Dalam Investigasi"></option>
            <option value="Approved/Siap Dibayarkan"></option>
        </datalist>
        @error('status_klaim')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="total_nilai_klaim">Total Nilai Klaim (Rp)</label>
        <input type="number" id="total_nilai_klaim" name="total_nilai_klaim" required min="0" step="0.01"
            value="{{ old('total_nilai_klaim', $klaim->total_nilai_klaim ?? '') }}">
        @error('total_nilai_klaim')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="up_utama">UP Utama (Rp)</label>
        <input type="number" id="up_utama" name="up_utama" required min="0" step="0.01"
            value="{{ old('up_utama', $klaim->up_utama ?? '') }}">
        @error('up_utama')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="recovery">Recovery (Rp)</label>
        <input type="number" id="recovery" name="recovery" required min="0" step="0.01"
            value="{{ old('recovery', $klaim->recovery ?? '') }}">
        @error('recovery')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="up_ceded">UP Ceded (Rp)</label>
        <input type="number" id="up_ceded" name="up_ceded" required min="0" step="0.01"
            value="{{ old('up_ceded', $klaim->up_ceded ?? '') }}">
        @error('up_ceded')<span class="field-error">{{ $message }}</span>@enderror
    </div>
</div>
