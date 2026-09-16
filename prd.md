# Product Requirement Document (PRD)
## Sistem Rekapitulasi ONT & Reporting Work Order (SIM-ONT)

**Versi:** 1.1 — *Diperbarui sesuai kondisi project aktual (September 2026)*

---

### 1. Ringkasan Eksekutif & Tujuan Sistem

**SIM-ONT** adalah aplikasi web internal berbasis **Laravel 12** yang berfungsi untuk mencatat, mengelola, dan memantau alur keluar-masuk perangkat ONT (modem fiber optik), serta melakukan rekapitulasi otomatis laporan status *Work Order* (WO) teknisi Telkom Akses di lapangan.

**Masalah yang Diselesaikan:**
- Menghilangkan pencatatan manual yang rentan *human error*
- Mencegah duplikasi *Serial Number* (SN)
- Mempercepat proses input gudang via barcode scanner USB dengan identifikasi merek instan
- Mempermudah pendataan unit ONT rusak/retur
- Memverifikasi kesesuaian unit yang dibawa teknisi dengan laporan penyelesaian WO secara *real-time*

**Prinsip Arsitektur:** Sederhana, Cepat, dan Bebas Build-Tools. Laravel Blade + Bootstrap 5.3 via CDN (100% tanpa Node.js/NPM/Vite).

---

### 2. Arsitektur Teknis & Tech Stack

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Framework** | Laravel 12 (PHP 8.2+) | MVC, Form Request, Blade |
| **Database** | MySQL | DB: `ont_rekap` |
| **Frontend** | Bootstrap 5.3 + Bootstrap Icons (CDN) | Responsif, tanpa build tools |
| **Font** | Plus Jakarta Sans (Google Fonts) | weight 400/500/600/700/800 |
| **Excel** | `maatwebsite/excel` (PhpSpreadsheet) | Import `.xlsx`/`.csv`, download template |
| **Auth** | Laravel Built-in Auth (`Auth` facade) | Session-based, middleware `auth`/`guest` |
| **Barcode** | USB HID Keyboard Emulation | Plug & play, kompatibel semua scanner |
| **Dev Server** | `php artisan serve` | Port 8000 |

#### Konfigurasi `.env`
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ont_rekap
DB_USERNAME=root
DB_PASSWORD=
```

---

### 3. Skema Basis Data (MySQL)

#### Tabel 1: `ont_masuks` — Data Master ONT Gudang

| Field | Type | Attributes | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT | PK, Auto Increment | ID Unik |
| `serial_number` | VARCHAR(100) | Unique, Indexed, Not Null | SN ONT (misal: `ZTEGC3FA7280`) |
| `brand` | VARCHAR(50) | Nullable | ZTE / Fiberhome / Nokia / Huawei |
| `tanggal_masuk` | DATE | Not Null | Tanggal penerimaan gudang |
| `created_at` | TIMESTAMP | Nullable | — |
| `updated_at` | TIMESTAMP | Nullable | — |

#### Tabel 2: `ont_keluars` — Data Penyerahan ke Teknisi

| Field | Type | Attributes | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT | PK, Auto Increment | ID Unik |
| `serial_number` | VARCHAR(100) | FK → `ont_masuks.serial_number`, Not Null | SN unit yang diserahkan |
| `nama_teknisi` | VARCHAR(150) | Not Null | Nama teknisi penerima |
| `tanggal_keluar` | DATE | Not Null | Tanggal penyerahan |
| `keterangan` | VARCHAR(50) | Nullable | Diisi `"Rusak"` jika cacat, null jika normal |
| `catatan` | TEXT | Nullable | Rincian kerusakan / alasan retur |
| `created_at` | TIMESTAMP | Nullable | — |
| `updated_at` | TIMESTAMP | Nullable | — |

#### Tabel 3: `reporting_wos` — Data Laporan Work Order

| Field | Type | Attributes | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT | PK, Auto Increment | ID Unik |
| `no_order` | VARCHAR(100) | Indexed, Not Null | Nomor WO (misal: `WO20264384690`) |
| `cid` | VARCHAR(50) | Nullable | Circuit ID / ID Pelanggan |
| `serial_number` | VARCHAR(100) | FK → `ont_masuks.serial_number`, Not Null | SN unit yang dipasang |
| `nama_teknisi` | VARCHAR(150) | Not Null | Nama teknisi pada laporan WO |
| `nik_teknisi` | VARCHAR(50) | Nullable | NIK Teknisi |
| `status_wo` | VARCHAR(50) | Not Null | misal: `Work Order Selesai` |
| `tanggal_sa` | DATE | Nullable | Tanggal penyelesaian / SA |
| `vendor` | VARCHAR(100) | Nullable | Nama vendor/mitra |
| `sektor` | VARCHAR(50) | Nullable | Kode sektor area |
| `cek_match` | VARCHAR(50) | Nullable | `SESUAI` / `Beda` (auto-rekonsiliasi) |
| `created_at` | TIMESTAMP | Nullable | — |
| `updated_at` | TIMESTAMP | Nullable | — |

#### Tabel 4: `users` — Akun Admin

| Field | Type | Deskripsi |
| :--- | :--- | :--- |
| `id` | BIGINT | PK |
| `name` | VARCHAR | Nama admin |
| `email` | VARCHAR | Email login (unique) |
| `password` | VARCHAR | Bcrypt hash |
| `remember_token` | VARCHAR | Session remember |
| `created_at` / `updated_at` | TIMESTAMP | — |

---

### 4. Modul & Fitur yang Sudah Diimplementasikan

#### A. Autentikasi (`AuthController`)

**Route:**
- `GET /login` → halaman login (guest only)
- `POST /login` → proses login
- `POST /logout` → proses logout (auth required)

**Fitur:**
- Login dengan email + password menggunakan `Auth::attempt()`
- Opsi "Remember Me" (session persist)
- Redirect ke halaman yang dituju setelah login (`intended`)
- Semua route selain login dilindungi middleware `auth`
- Session regenerate saat login/logout untuk keamanan

---

#### B. Dashboard (`DashboardController`)

**Route:** `GET /` (auth)

**Metric Cards yang ditampilkan:**
| Metric | Formula |
| :--- | :--- |
| Total ONT Masuk | `COUNT(ont_masuks)` |
| Total ONT Keluar | `COUNT(ont_keluars)` |
| Sisa Stok Gudang | `Total Masuk - Total Keluar` |
| INSTALLED | `COUNT DISTINCT serial_number di reporting_wos WHERE status_wo LIKE '%selesai%'` |
| NOT INSTALLED | `Total Keluar - INSTALLED` |
| Unit Rusak | `COUNT(ont_keluars WHERE keterangan = 'Rusak')` |

**Tabel Rekapitulasi per Teknisi:**
- Menggabungkan data dari `ont_keluars` dan `reporting_wos`
- Per teknisi: INSTALLED, NOT INSTALLED, RUSAK, TOTAL DIBAWA, Rasio % (progress bar)
- Grand total row di footer tabel
- Live search filter nama teknisi (JavaScript)
- Diurutkan berdasarkan total unit terbanyak

---

#### C. Modul ONT Masuk (`OntMasukController`)

**Routes:**
```
GET  /ont-masuk           → index (daftar + search + filter brand + pagination)
POST /ont-masuk           → store (input manual 1 unit)
POST /ont-masuk/import    → import massal Excel/CSV
GET  /ont-masuk/template  → download template .xlsx
DELETE /ont-masuk/{id}    → hapus 1 unit
```

**Fitur:**
1. **Input Barcode Scanner USB** — `autofocus` permanen di field SN, auto-submit saat Enter
2. **Auto-Detect Merek** dari prefix SN:
   - `ZTE` → ZTE | `FHTT` → Fiberhome | `ALCL` → Nokia | `48575443`/`HWTC` → Huawei
   - Berlaku di form manual maupun import Excel (`OntMasuk::detectBrand()`)
3. **Import Massal Excel/CSV** — maks 10MB, skip baris duplikat SN, laporan ringkasan hasil import
4. **Download Template** `.xlsx` berisi header + 3 baris contoh, header styling merah
5. **Tabel Inventaris** — paginate 15, search SN/brand, filter dropdown brand, copy SN ke clipboard, hapus data
6. **Validasi Backend** — SN unique, tanggal wajib, brand nullable

---

#### D. Modul ONT Keluar (`OntKeluarController`)

**Routes:**
```
GET  /ont-keluar               → index (daftar + search + filter kondisi + pagination)
POST /ont-keluar               → store (catat penyerahan baru)
POST /ont-keluar/update-status → updateStatus (ubah kondisi via modal)
DELETE /ont-keluar/{id}        → destroy (hapus transaksi)
```

**Fitur:**
1. **Form Penyerahan** — Nama Teknisi, Tanggal, Serial Number dengan datalist autocomplete dari stok tersedia
2. **Datalist SN Tersedia** — hanya menampilkan unit yang ada di `ont_masuks` dan belum pernah diserahkan
3. **Validasi Backend** (BR-01 & BR-02):
   - SN wajib ada di `ont_masuks`
   - SN belum pernah diserahkan (cek `ont_keluars`)
4. **Modal Ubah Kondisi** — update status Normal/Rusak + catatan kerusakan tanpa reload halaman
5. **Tabel Transaksi** — paginate 15, search SN/teknisi, filter kondisi Normal/Rusak, badge status, copy SN, hapus

---

#### E. Modul Reporting WO (`ReportingWoController`)

**Routes:**
```
GET  /reporting-wo          → index (daftar + filter + pagination)
POST /reporting-wo/import   → import Excel laporan WO (maks 20MB)
GET  /reporting-wo/template → download template .xlsx (10 kolom)
DELETE /reporting-wo/{id}   → destroy
```

**Fitur:**
1. **Import Excel Laporan WO** dari app ticketing lapangan (Sigma/SIAPDEH/dll)
   - Kolom fleksibel (mendukung alias nama kolom: `no_wo`, `sn`, dll.)
   - Skip baris kosong / SN tidak terdaftar di `ont_masuks` (BR-01)
   - Upsert: jika `no_order` sudah ada → UPDATE, baru → INSERT
   - Parsing tanggal otomatis (Excel serial number & string format)
   - Laporan ringkasan: imported, updated, unregistered SN, skipped
2. **Auto-Match Rekonsiliasi** (`cek_match`):
   - Cek apakah SN di laporan WO cocok dengan nama teknisi di `ont_keluars`
   - `SESUAI` → teknisi sama | `Beda` → beda personil
3. **Download Template** `.xlsx` 10 kolom dengan header merah + 3 baris contoh
4. **Tabel Monitoring** — paginate 15, search WO/SN/CID, filter status WO, filter teknisi dropdown, badge Auto-Match, copy SN, hapus
5. **Header Summary Badges** — Total WO, INSTALLED, NOT INSTALLED

---

### 5. Aturan Bisnis (Business Rules)

| Kode | Aturan |
| :--- | :--- |
| **BR-01** | SN di `ont_keluars` & `reporting_wos` wajib terdaftar di `ont_masuks` |
| **BR-02** | Satu SN hanya boleh diserahkan satu kali (cek `ont_keluars`) |
| **BR-03** | Kolom `keterangan` di `ont_keluars` diisi `"Rusak"` atau dibiarkan null (Normal) |
| **BR-04** | INSTALLED = `status_wo LIKE '%selesai%'`; NOT INSTALLED = Total Dibawa − INSTALLED |
| **BR-05** | Deteksi merek otomatis: `ZTE` → ZTE, `FHTT` → Fiberhome, `ALCL` → Nokia, `48575443`/`HWTC` → Huawei |
| **BR-06** | Laporan WO berasal dari export app ticketing lapangan; SN diisi otomatis oleh sistem saat teknisi scan di lokasi pelanggan |

---

### 6. Peta Navigasi & Route Map

```
[GET /login]                    → Halaman login (guest only)
[POST /login]                   → Proses autentikasi
[POST /logout]                  → Logout

── Semua route di bawah dilindungi middleware auth ──

[GET /]                         → Dashboard (metric cards + tabel rekap teknisi)
│
├─ [GET /ont-masuk]             → Halaman ONT Masuk
│  ├─ [POST /ont-masuk]         → Simpan 1 unit (barcode scan / manual)
│  ├─ [POST /ont-masuk/import]  → Import massal Excel/CSV
│  ├─ [GET  /ont-masuk/template]→ Download template .xlsx
│  └─ [DELETE /ont-masuk/{id}]  → Hapus unit
│
├─ [GET /ont-keluar]              → Halaman ONT Keluar
│  ├─ [POST /ont-keluar]          → Catat penyerahan ke teknisi
│  ├─ [POST /ont-keluar/update-status] → Ubah kondisi Normal/Rusak (modal)
│  └─ [DELETE /ont-keluar/{id}]   → Hapus transaksi
│
└─ [GET /reporting-wo]            → Halaman Reporting WO
   ├─ [POST /reporting-wo/import] → Import Excel laporan WO
   ├─ [GET  /reporting-wo/template] → Download template .xlsx
   └─ [DELETE /reporting-wo/{id}]   → Hapus laporan

── Fitur Rencana (Belum Diimplementasikan) ──

[ ] [GET /profil]               → Halaman Profil Admin
[ ] [POST /profil]              → Update nama/email/password
```

---

### 7. Design System & UI

| Token | Nilai |
| :--- | :--- |
| Font | Plus Jakarta Sans (400/500/600/700/800) |
| Body background | `#fafafa` |
| Warna aksen | `#b91c1c` (hover: `#991b1b`) |
| Merah light | bg `#fef2f2`, border `#fee2e2` |
| Card | border `#f0f0f2`, radius `14px`, shadow minimal |
| Input | border `#e2e8f0`, radius `7px`, focus border `#f87171` |
| Badge hijau | bg `#f0fdf4` text `#166534` border `#dcfce7` |
| Badge abu | bg `#f8fafc` text `#64748b` border `#e2e8f0` |
| Icon library | Bootstrap Icons (`bi bi-*`) |
| Layout | Top navbar horizontal (bukan sidebar) |
| Nav active | bg `#fef2f2`, text `#b91c1c`, weight 600 |

---

### 8. Fitur yang Belum Diimplementasikan (Backlog)

| # | Fitur | Prioritas |
| :--- | :--- | :--- |
| 1 | Halaman Profil Admin (edit nama, email, password) | Medium |
| 2 | Layout sidebar vertikal (mengganti top navbar) | Low |
| 3 | Export Excel data ONT Masuk / ONT Keluar / Reporting WO | Medium |
| 4 | Multi-user / role management | Low |