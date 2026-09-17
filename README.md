# Reasuransi Excel

## 1. Deskripsi Project

Aplikasi web Laravel untuk mengelola data laporan reasuransi dan menghasilkan
laporan Excel berpatokan pada template `BRF_Laporan Reas`:

- **Data Produksi & Premi** — input, ubah, hapus, dan daftar data polis
  beserta premi reasuransi.
- **Data Klaim** — input, ubah, hapus, dan daftar data klaim reasuransi
  beserta recovery dan status klaim.
- **Export Excel** — mengunduh satu workbook `laporan-reasuransi.xlsx`
  berisi 3 sheet: Sheet 1 data produksi, Sheet 2 data klaim, dan
  Sheet 3 Ringkasan Akun Keuangan berisi rantai formula dari kedua sheet.
- **Dashboard** — ringkasan jumlah data, total premi, dan total recovery.

Yang belum/tidak tersedia: autentikasi user, role, dan penjadwalan
laporan otomatis.

## 2. Tech Stack

| Komponen | Versi terverifikasi |
|---|---|
| Laravel Framework | 13.32.0 |
| PHP | 8.3.30 |
| Database | MySQL (`reasuransi_excel` di development) |
| maatwebsite/excel | 4.0.3 |
| PhpSpreadsheet (transitif via maatwebsite/excel) | 5.9.0 |
| PHPUnit | 12.5.35 |
| Composer | 2.x |
| Environment development | Laragon (Windows) |

UI memakai Blade + CSS bawaan di layout; Tailwind/Vite hanya ada sebagai
aset opsional dan **tidak wajib** dibangun agar aplikasi berjalan
(satu-satunya pemakaian `@vite` ada di `welcome.blade.php` bawaan
Laravel yang tidak dipakai route mana pun).

## 3. Requirements

- PHP ^8.3 dengan ekstensi: `mbstring`, `openssl`, `pdo`, `pdo_mysql`,
  `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `zip` (+ `gd`)
- Composer 2.x
- MySQL berjalan (Laragon atau setara) + database kosong, mis. `reasuransi_excel`
- Node/npm **hanya** jika ingin mengubah aset frontend (`resources/css`, `resources/js`)

## 4. Installation

```bash
git clone <url-repo> reasuransi-excel
cd reasuransi-excel

composer install

copy .env.example .env        # Windows; di Linux/macOS: cp .env.example .env

php artisan key:generate
```

Atur koneksi database di `.env` (contoh MySQL Laragon):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reasuransi_excel
DB_USERNAME=root
DB_PASSWORD=
```

Lalu:

```bash
php artisan migrate
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Alternatif singkat memakai script bawaan `composer.json`:

```bash
composer setup   # install + key:generate + migrate + npm build
composer dev     # server + queue + log + vite sekaligus
composer test    # config:clear + artisan test
```

> Catatan: `phpunit.xml` memakai SQLite `:memory:` sehingga test tidak
> menyentuh MySQL development.

## 5. Database

### `produksis` — satu baris per polis produksi

| Field | Keterangan |
|---|---|
| `no_polis` | Nomor polis |
| `nama_tertanggung` | Nama tertanggung |
| `tanggal_lahir` | Tanggal lahir tertanggung |
| `up_utama` | Uang pertanggungan utama (desimal 15,2) |
| `retention` | Porsi sendiri / retensi (desimal 15,2) |
| `up_ceded` | UP yang direasuransikan / ceded (desimal 15,2) |
| `jenis_reasuransi` | Jenis reasuransi, cth. Surplus, Quota Share, Fac/Surplus |
| `premi_reasuransi` | Premi reasuransi (desimal 15,2) |

### `klaims` — satu baris per klaim reasuransi

| Field | Keterangan |
|---|---|
| `no_klaim` | Nomor klaim |
| `no_polis` | Nomor polis terkait |
| `nama_tertanggung` | Nama tertanggung |
| `penyebab_klaim` | Penyebab klaim |
| `tanggal_lahir` | Tanggal lahir tertanggung |
| `total_nilai_klaim` | Total nilai klaim (desimal 15,2) |
| `up_utama` | Uang pertanggungan utama (desimal 15,2) |
| `recovery` | Porsi klaim yang ditanggung reasuransi (desimal 15,2) |
| `up_ceded` | UP yang direasuransikan (desimal 15,2) |
| `status_klaim` | Status, cth. `Dalam Investigasi`, `Approved/Siap Dibayarkan` |

Nilai uang disimpan sebagai `decimal(15,2)`; tanggal di-cast ke `date`
di model.

## 6. Application Flow

```text
Dashboard (/) — ringkasan angka
    ↓
Produksi & Premi (/produksi) — kelola data polis
    ↓
Klaim (/klaim) — kelola data klaim
    ↓
Export Excel (/export → /export/excel) — unduh workbook
    ↓
3 Sheets: Produksi | Klaim | Ringkasan Akun Keuangan
```

- **Dashboard**: melihat total produksi, total klaim, total premi
  reasuransi, dan total recovery klaim.
- **Produksi**: menambah/mengubah/menghapus polis lewat form, melihat
  daftar dalam tabel.
- **Klaim**: menambah/mengubah/menghapus klaim lewat form, melihat
  daftar dalam tabel beserta status.
- **Export**: membuka halaman info workbook lalu mengunduh file
  `laporan-reasuransi.xlsx` yang datanya diambil langsung dari database.

## 7. Produksi

- **Tambah**: `/produksi/create` → simpan (`POST /produksi`).
- **Ubah**: `/produksi/{produksi}/edit` → perbarui (`PUT /produksi/{produksi}`).
- **Hapus**: tombol Hapus di tabel (`DELETE /produksi/{produksi}`, konfirmasi browser).
- **Validasi**: teks wajib (maks. 50/255 karakter), `tanggal_lahir`
  wajib tanggal tidak melebihi hari ini, 4 nominal wajib angka ≥ 0.
- **Daftar**: tabel di `/produksi` (format tanggal `d/m/Y`, angka `Rp` 2 desimal).
- **Hubungan ke Sheet 1**: seluruh baris `produksis` (urut `id`) ditulis
  mulai baris 4; total premi dihitung **dinamis** sebagai
  `=SUM(H4:H{baris_akhir})` tepat di bawah data terakhir
  (0 data → total statis 0 di baris 4, tanpa formula range invalid).

## 8. Klaim

- **Tambah**: `/klaim/create` → simpan (`POST /klaim`).
- **Ubah**: `/klaim/{klaim}/edit` → perbarui (`PUT /klaim/{klaim}`).
- **Hapus**: tombol Hapus di tabel (`DELETE /klaim/{klaim}`, konfirmasi browser).
- **Validasi**: sama polanya dengan Produksi untuk 10 field klaim.
- **Status klaim**: teks bebas dengan saran bawaan template
  (`Dalam Investigasi`, `Approved/Siap Dibayarkan`); tidak ada daftar
  tertutup di kode.
- **Recovery / UP Ceded**: kolom uang di tabel dan di Sheet 2
  (kolom H dan I) dengan format akuntansi tanpa desimal.
- **Hubungan ke Sheet 2**: seluruh baris `klaims` ditulis mulai baris 4;
  sesuai template, Sheet 2 **tidak memiliki baris total maupun formula**.

## 9. Excel Export

Route unduhan: `GET /export/excel` (nama `export.excel`) →
`ExportController@excel` → `Excel::download(new ReasuransiExport(), ...)`.
Route lama `GET /test-excel` masih ada sebagai smoke test.

### Sheet 1 — `Laporan Produksi & Premi Reasur`
Judul merge `A1:H1`, header baris 3 (8 kolom template), data dari baris 4,
total dinamis `=SUM(H4:H{akhir})`, format akuntansi untuk kolom nominal.

### Sheet 2 — `Laporan Klaim Reasuransi`
Judul merge `A1:J1`, header baris 3 (10 kolom template, verbatim),
data klaim dari baris 4 (Recovery = kolom H, UP Ceded = kolom I,
Status = kolom J), tanpa total dan tanpa formula sesuai template.

### Sheet 3 — `Ringkasan Akun Keuangan`
Formulir `A1:O12` (23 merged cells) dengan rantai formula hidup:

| Cell | Isi aktual |
|---|---|
| B11 | `='Laporan Produksi & Premi Reasur'!H{total}` (referensi dinamis mengikuti total Sheet 1) |
| E11 | `0.1` (konstanta template, format persen) |
| E12 | `=B11*E11` |
| H11 | `=B11-E12` |
| K11 | **kosong** (belum ada sumber nilai yang dikonfirmasi) |
| N11 | `=K11-H11` |
| B5/E5/H5/K5/N5 | cermin `=B11`, `=E12`, `=H11`, `=K11`, `=N11` |

> K11 **bukan** total recovery otomatis. Nilai `700000000` di template
> adalah contoh tanpa bukti formula dan tidak diimplementasikan.

## 10. Known Business Decisions / Pending Confirmation

Item di bawah ini **bukan bug teknis** — implementasi mengikuti template
apa adanya dan menunggu keputusan pemilik kebutuhan/template:

1. **Sumber K11 belum dikonfirmasi** — kandidat (`SUM(recovery)`,
   `SUM(total_nilai_klaim)`, `SUM(up_ceded)`, atau ketik manual) tidak
   didukung bukti di file template; cell dibiarkan kosong.
2. **Tarif komisi 10% (E11)** masih mengikuti angka template; belum
   diputuskan apakah tetap, per jenis reasuransi, atau input user.
3. **Nama Sheet 2** di implementasi tanpa trailing whitespace template
   (`Laporan Klaim Reasuransi`) agar referensi formula/integrasi aman.
4. **Typo template dipertahankan verbatim** (`Reinsurance Commision`,
   `Porsi Klaim Reasuansi`, `Dikurangi Total Klaim Reasunasi `, kurung
   tak berpasangan di catatan Sheet 3).

## 11. Testing

Hasil audit final (`php artisan test`, SQLite `:memory:`):

- **38 passed, 156 assertions, 0 failures**
- Cakupan: CRUD Produksi (8), CRUD Klaim (8), export Sheet 1 (5),
  Sheet 2 (6), Sheet 3 (9), contoh bawaan (2).

Realistic test terverifikasi (5 Produksi + 3 Klaim via form HTTP):

- Total premi reasuransi = **13.250.000** (`=SUM(H4:H8)`, terhitung)
- Rantai Sheet 3: B11 = 13.250.000, E12 = 1.325.000,
  H11 = 11.925.000, N11 = −11.925.000 — semua sebagai formula hidup
- Export valid, 3 sheet, **0 formula error** (`#REF!` dkk. tidak ada)
- Export kondisi data kosong juga valid (total statis 0, tanpa `#REF!`)

## 12. Routes

| Method | URI | Nama | Fungsi |
|---|---|---|---|
| GET | `/` | `dashboard` | Ringkasan angka |
| GET | `/produksi` | `produksi.index` | Daftar produksi |
| GET | `/produksi/create` | `produksi.create` | Form tambah |
| POST | `/produksi` | `produksi.store` | Simpan |
| GET | `/produksi/{produksi}/edit` | `produksi.edit` | Form ubah |
| PUT | `/produksi/{produksi}` | `produksi.update` | Perbarui |
| DELETE | `/produksi/{produksi}` | `produksi.destroy` | Hapus |
| GET | `/klaim` (+ create/store/edit/update/destroy) | `klaim.*` | Sama untuk klaim |
| GET | `/export` | `export.index` | Info workbook |
| GET | `/export/excel` | `export.excel` | Unduh xlsx |
| GET | `/test-excel` | — | Smoke test lawas |

## 13. Project Structure

```text
app/
  Exports/            ReasuransiExport (WithMultipleSheets) + Sheet 1/2/3
  Http/Controllers/   Dashboard, Produksi (CRUD), Klaim (CRUD), Export
  Models/             Produksi, Klaim (+ User bawaan)
database/
  migrations/         users/cache/jobs + create_produksis_table + create_klaims_table
resources/
  views/              layouts/app + dashboard + produksi/* + klaim/* + export/*
routes/
  web.php             seluruh route aplikasi (telah didokumentasikan di §12)
tests/
  Feature/            Example + ProduksiCrud + KlaimCrud + 3 sheet-export tests
  Unit/               contoh bawaan
```

- `app/Exports`: arsitektur export — parent multi-sheet + satu class
  per sheet (layout/format/formula via event `AfterSheet`).
- `resources/views`: Blade + CSS bawaan; partial `_form` dipakai
  bersama oleh halaman create/edit.
- `tests/Feature`: kode pola `RefreshDatabase` (wajib untuk test yang
  menyentuh database karena testing memakai SQLite `:memory:`).

## 14. Demo Flow

1. Buka Dashboard (`/`) — tunjukkan 4 kartu ringkasan (masih 0 saat kosong).
2. Tambah data Produksi (`/produksi/create`) — isi 1–2 polis, simpan,
   tunjukkan pesan sukses dan baris baru di tabel.
3. Tampilkan daftar Produksi — tunjukkan format tanggal/angka.
4. Tambah data Klaim (`/klaim/create`) — simpan, tunjukkan badge status.
5. Tampilkan daftar Klaim.
6. Buka halaman Export (`/export`) — jelaskan isi 3 sheet.
7. Unduh Excel (`/export/excel`) — file `laporan-reasuransi.xlsx`.
8. Buka Sheet 1 — data mulai baris 4, total dinamis di bawah data.
9. Buka Sheet 2 — data klaim, Recovery (H), UP Ceded (I), Status (J).
10. Buka Sheet 3 — tunjukkan rantai B11 → E12 → H11 → N11 dan
    referensi lintas-sheet ke total Sheet 1.
11. Tunjukkan formula (bukan nilai beku) dan hubungan antar-sheet;
    jelaskan bahwa K11 masih kosong menunggu keputusan bisnis (§10).

## 15. Troubleshooting

| Gejala | Penyebab umum & perbaikan |
|---|---|
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL belum jalan / kredensial `.env` salah — nyalakan MySQL (Laragon), samakan `DB_*` dengan database yang ada |
| `No application encryption key` | `APP_KEY` kosong — jalankan `php artisan key:generate` |
| `no such table: produksis` (saat test) | Test Feature tanpa trait `RefreshDatabase` — test project ini sudah memakainya; jangan dihapus |
| `could not find driver` (pdo_mysql) | Ekstensi PHP belum aktif — aktifkan `pdo_mysql` di `php.ini` Laragon lalu restart |
| `composer install` gagal (zip/ext) | Ekstensi `zip`/`fileinfo` belum aktif di PHP yang dipakai Composer |
| Port 8000 dipakai | `php artisan serve --port=8001` |
| Download Excel kosong/rusak | Pastikan dependencies terinstal (`vendor/maatwebsite` ada); ulangi `composer install` |
| Perubahan `.env` tidak terbaca saat test | `composer test` otomatis `config:clear` dahulu — tiru langkah itu |

## 16. Catatan Dokumentasi

README ini ditulis dari kondisi kode aktual (diperiksa: routes,
controllers, models, migrations, views, `app/Exports`, tests,
`composer.json`, `.env.example`, riwayat Git). Klaim yang belum pasti
ditandai eksplisit — terutama seluruh isi §10.
