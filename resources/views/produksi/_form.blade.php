{{-- Shared Produksi form fields. Expects optional $produksi for edit repopulation. --}}
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
        <label for="no_polis">No Polis</label>
        <input type="text" id="no_polis" name="no_polis" required maxlength="50"
            value="{{ old('no_polis', $produksi->no_polis ?? '') }}" placeholder="cth: AJ-000123">
        @error('no_polis')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="nama_tertanggung">Nama Tertanggung</label>
        <input type="text" id="nama_tertanggung" name="nama_tertanggung" required maxlength="255"
            value="{{ old('nama_tertanggung', $produksi->nama_tertanggung ?? '') }}">
        @error('nama_tertanggung')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="tanggal_lahir">Tanggal Lahir</label>
        <input type="date" id="tanggal_lahir" name="tanggal_lahir" required max="{{ date('Y-m-d') }}"
            value="{{ old('tanggal_lahir', isset($produksi) ? $produksi->tanggal_lahir?->format('Y-m-d') : '') }}">
        @error('tanggal_lahir')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="jenis_reasuransi">Jenis Reasuransi</label>
        <input type="text" id="jenis_reasuransi" name="jenis_reasuransi" required maxlength="50" list="jenis-reasuransi-list"
            value="{{ old('jenis_reasuransi', $produksi->jenis_reasuransi ?? '') }}" placeholder="cth: Surplus">
        <datalist id="jenis-reasuransi-list">
            <option value="Surplus"></option>
            <option value="Quota Share"></option>
            <option value="Facultative"></option>
            <option value="Fac/Surplus"></option>
        </datalist>
        @error('jenis_reasuransi')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="up_utama">UP Utama (Rp)</label>
        <input type="number" id="up_utama" name="up_utama" required min="0" step="0.01"
            value="{{ old('up_utama', $produksi->up_utama ?? '') }}">
        @error('up_utama')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="retention">Retention / Sendiri (Rp)</label>
        <input type="number" id="retention" name="retention" required min="0" step="0.01"
            value="{{ old('retention', $produksi->retention ?? '') }}">
        @error('retention')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="up_ceded">UP Ceded (Rp)</label>
        <input type="number" id="up_ceded" name="up_ceded" required min="0" step="0.01"
            value="{{ old('up_ceded', $produksi->up_ceded ?? '') }}">
        @error('up_ceded')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="premi_reasuransi">Premi Reasuransi (Rp)</label>
        <input type="number" id="premi_reasuransi" name="premi_reasuransi" required min="0" step="0.01"
            value="{{ old('premi_reasuransi', $produksi->premi_reasuransi ?? '') }}">
        @error('premi_reasuransi')<span class="field-error">{{ $message }}</span>@enderror
    </div>
</div>
