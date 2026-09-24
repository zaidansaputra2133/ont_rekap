# Product Requirement Document (PRD)
## Sistem Rekapitulasi ONT & Reporting Work Order (SIM-ONT)

**Versi:** 2.0 — *Diperbarui sesuai kondisi project aktual (22 September 2026)*

---

### 1. Ringkasan Eksekutif & Tujuan Sistem

**SIM-ONT** adalah aplikasi web internal berbasis **Laravel 12** yang berfungsi untuk mencatat, mengelola, dan memantau alur keluar-masuk perangkat ONT (modem fiber optik), serta melakukan rekapitulasi otomatis laporan status *Work Order* (WO) teknisi Telkom Akses di lapangan.

**Masalah yang Diselesaikan:**
- Menghilangkan pencatatan manual yang rentan *human error*
- Mencegah duplikasi *Serial Number* (SN)
- Mempercepat proses input gudang via barcode scanner USB dengan identifikasi merek instan
- Mempermudah penyerahan unit ke teknisi murni berbasis checkbox dari stok gudang (tanpa ketik/scan manual di form keluar)
- Memverifikasi kesesuaian unit yang dibawa teknisi dengan laporan penyelesaian WO secara *real-time*
- Menyediakan diagram analitik visual tren keluar-masuk ONT (harian, mingguan, bulanan) dengan filter tanggal/bulan/tahun fleksibel

**Prinsip Arsitektur:** Sederhana, Cepat, Responsif, dan Minimalis. Laravel Blade + Bootstrap 5.3 + Chart.js via CDN (100% tanpa kompilasi build-tools yang rumit).

---

### 2. Arsitektur Teknis & Tech Stack

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Framework** | Laravel 12 (PHP 8.3) | MVC, Form Request, Eloquent, Blade |
| **Database** | MySQL | Database: `ont_rekap` |
| **Frontend** | Bootstrap 5.3 + Bootstrap Icons (CDN) | Responsif, layout sidebar toggleable |
| **Chart Library**| Chart.js v4.4.3 (CDN) | Visualisasi tren ONT Masuk vs Keluar |
| **Font** | Plus Jakarta Sans (Google Fonts) | Weight 400/500/600/700/800 |
| **Excel** | `maatwebsite/excel` (PhpSpreadsheet) | Import `.xlsx`/`.csv`, download template |
| **Auth** | Laravel Built-in Auth (`Auth` facade) | Session-based, middleware `auth`/`guest` |
| **Barcode** | USB HID Keyboard Emulation | Plug & play untuk menu ONT Masuk |
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
| `created_at` | TIMESTAMP | Nullable | Waktu pencatatan |
| `updated_at` | TIMESTAMP | Nullable | Waktu pembaruan |

#### Tabel 2: `ont_keluars` — Data Penyerahan ke Teknisi

| Field | Type | Attributes | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT | PK, Auto Increment | ID Unik |
| `serial_number` | VARCHAR(100) | FK → `ont_masuks.serial_number`, Not Null | SN unit yang diserahkan |
| `nama_teknisi` | VARCHAR(150) | Not Null | Nama teknisi penerima |
| `tanggal_keluar` | DATE | Not Null | Tanggal penyerahan |
| `keterangan` | VARCHAR(50) | Nullable | Diisi `"Rusak"` jika cacat, null jika normal |
| `catatan` | TEXT | Nullable | Rincian kerusakan / alasan retur |
| `created_at` | TIMESTAMP | Nullable | Waktu penyerahan |
| `updated_at` | TIMESTAMP | Nullable | Waktu pembaruan |

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
| `tanggal_sa` | DATE | Nullable | Tanggal penyelesaian / SA (*Service Activation*) |
| `vendor` | VARCHAR(100) | Nullable | Nama vendor/mitra |
| `sektor` | VARCHAR(50) | Nullable | Kode sektor area |
| `cek_match` | VARCHAR(50) | Nullable | `SESUAI` / `Beda` (auto-rekonsiliasi) |
| `created_at` | TIMESTAMP | Nullable | Waktu pencatatan |
| `updated_at` | TIMESTAMP | Nullable | Waktu pembaruan |

#### Tabel 4: `users` — Akun Admin

| Field | Type | Deskripsi |
| :--- | :--- | :--- |
| `id` | BIGINT | PK |
| `name` | VARCHAR | Nama admin |
| `email` | VARCHAR | Email login (unique) |
| `password` | VARCHAR | Bcrypt hash |
| `remember_token` | VARCHAR | Session remember token |
| `created_at` / `updated_at` | TIMESTAMP | Timestamps |

---

### 4. Modul & Fitur yang Sudah Diimplementasikan

#### A. Layout & Navigasi Sidebar Toggleable
- **Sidebar Vertikal Minimalis:** Menyediakan menu navigasi (*Dashboard*, *ONT Masuk*, *ONT Keluar*, *Reporting WO*).
- **Hide / Show Toggle Button:** Sidebar dapat disembunyikan sepenuhnya (*collapse*) untuk memperluas area kerja layar dan dibuka kembali melalui tombol toggle.
- **State Persistensi:** Posisi sidebar tersimpan di `localStorage` peramban.

---

#### B. Autentikasi (`AuthController`)

**Routes:**
- `GET /login` → Halaman login (guest only)
- `POST /login` → Proses autentikasi
- `POST /logout` → Proses logout (auth required)

**Fitur:**
- Login dengan email + password menggunakan `Auth::attempt()`
- Opsi *"Remember Me"*
- Session regeneration saat login/logout untuk keamanan
- Dilindungi middleware `auth` untuk seluruh rute internal

---

#### C. Dashboard & Visualisasi Tren (`DashboardController`)

**Routes:**
- `GET /` → Tampilan dashboard utama
- `GET /dashboard/chart-data` → Endpoint JSON untuk data analitik diagram

**1. Metric Cards Utama:**
| Metric | Formula |
| :--- | :--- |
| **INSTALLED** | `COUNT DISTINCT serial_number di reporting_wos WHERE status_wo LIKE '%selesai%'` |
| **NOT INSTALLED** | `Total Keluar - INSTALLED` |
| **RUSAK** | `COUNT(ont_keluars WHERE keterangan = 'Rusak')` |
| **TOTAL** | `COUNT(ont_keluars)` (Total fisik dibawa teknisi) |

**2. Diagram Batang Analitik (Bar Chart):**
- **Mode Periode:**
  - **Harian (`Harian`)**: Agregasi stok per hari dalam 1 bulan penuh (01 s/d akhir bulan).
  - **Mingguan (`Mingguan`)**: Agregasi mingguan (Minggu 1 s/d Minggu 5) pada bulan yang dipilih.
  - **Bulanan (`Bulanan`)**: Agregasi bulanan (Jan s/d Des) sepanjang tahun yang dipilih.
- **Filter Fleksibel:** Input pemilih Bulan & Tahun (`<input type="month">`) untuk mode Harian/Mingguan dan pemilih Tahun (`<select>`) untuk mode Bulanan.
- **Warna Solid:**
  - 🔵 **ONT Masuk**: Batang warna solid Biru (`#2563eb`)
  - 🔴 **ONT Keluar**: Batang warna solid Merah (`#c0392b`)
- **Interactive Checkbox:** Checkbox interaktif di bawah diagram untuk mematikan / menyalakan batang ONT Masuk dan ONT Keluar secara instan.

**3. Tabel Rekapitulasi per Teknisi:**
- Menghitung akumulasi unit per teknisi: INSTALLED, NOT INSTALLED, RUSAK, TOTAL DIBAWA.
- Live search filter nama teknisi secara instan tanpa reload halaman.
- Diurutkan berdasarkan akumulasi unit terbanyak.

---

#### D. Modul ONT Masuk (`OntMasukController`)

**Routes:**
- `GET  /ont-masuk` → Daftar inventaris + search + filter brand + pagination
- `POST /ont-masuk` → Simpan 1 unit (barcode scan / manual)
- `POST /ont-masuk/import` → Import massal Excel/CSV (maks 10MB)
- `GET  /ont-masuk/template` → Download template Excel (.xlsx)
- `DELETE /ont-masuk/{id}` → Hapus data unit

**Fitur:**
1. **Input Barcode Scanner USB** — `autofocus` permanen pada field input SN, auto-submit saat Enter.
2. **Auto-Detect Merek** dari prefix SN (`ZTE`, `FHTT` → Fiberhome, `ALCL` → Nokia, `48575443`/`HWTC` → Huawei).
3. **Import Excel/CSV** — Otomatis lewati SN duplikat dan laporkan ringkasan hasil impor.
4. **Tabel Inventaris** — Paginasi 15 data, pencarian SN, filter dropdown brand, copy SN, dan hapus data.

---

#### E. Modul ONT Keluar (`OntKeluarController`)

**Routes:**
- `GET  /ont-keluar` → Daftar transaksi penyerahan + search + filter status + pagination
- `POST /ont-keluar` → Catat penyerahan unit baru ke teknisi
- `POST /ont-keluar/update-status` → Ubah kondisi unit (Normal / Rusak + catatan) via modal
- `DELETE /ont-keluar/{id}` → Hapus transaksi penyerahan

**Fitur:**
1. **Seleksi Murni Checkbox (Tanpa Textarea Manual):**
   - Penyerahan unit ke teknisi dilakukan dengan mencentang unit ONT yang tersedia di stok gudang.
   - Kotak textarea dan scan manual telah dihapus untuk memastikan hanya unit fisik yang valid yang diserahkan.
2. **Paginasi Daftar Checkbox (Maksimal 5 Unit per Halaman):**
   - Navigasi halaman checkbox ringkas dengan tombol *Prev* dan *Next* yang lega dan rapi.
3. **Filter Merek & Pencarian SN di Area Checkbox:** Memudahkan petugas menemukan unit tertentu dalam stok gudang.
4. **Ringkasan Unit Terpilih (*Pills*):**
   - Menampilkan badge unit yang dicentang beserta tombol hapus per-item dan tombol *"Reset Pilihan"*.
5. **Modal Update Kondisi (Retur / Rusak):** Menandai unit yang cacat/rusak beserta catatan kendala.

---

#### F. Modul Reporting WO (`ReportingWoController`)

**Routes:**
- `GET    /reporting-wo` → Monitoring daftar WO + search + filter status & teknisi + pagination
- `POST   /reporting-wo/import` → Upload & import file Excel laporan kerja WO (maks 20MB)
- `GET    /reporting-wo/template` → Download template Excel laporan WO (10 kolom)
- `DELETE /reporting-wo/{id}` → Hapus data laporan WO

**Fitur:**
1. **Import File Excel Laporan Kerja:**
   - Diambil dari laporan ekspor aplikasi ticketing lapangan.
   - Kolom fleksibel (`no_order`, `serial_number`, `nama_teknisi`, `status_wo`, `tanggal_sa`, dll.).
   - **Upsert by `no_order`**: Jika nomor WO sudah ada maka diperbarui, jika baru maka ditambahkan.
   - **Validasi SN Gudang (BR-01)**: Unit yang tidak pernah terdaftar di `ont_masuks` dilewati secara otomatis.
2. **Auto-Match Rekonsiliasi (`cek_match`):**
   - Membandingkan nama teknisi yang memasang di WO dengan nama teknisi yang mengambil unit di `ont_keluars`.
   - `SESUAI`: Teknisi sama | `Beda`: Terjadi tukar unit antar teknisi di lapangan.
3. **Tabel Monitoring & AJAX Filtering:** Filter status WO dan nama teknisi secara langsung tanpa reload.

---

### 5. Aturan Bisnis (Business Rules)

| Kode | Aturan |
| :--- | :--- |
| **BR-01** | SN pada `ont_keluars` dan `reporting_wos` wajib terdaftar di `ont_masuks`. |
| **BR-02** | Satu SN hanya boleh diserahkan satu kali ke teknisi (cek `ont_keluars`). |
| **BR-03** | Kolom `keterangan` di `ont_keluars` diisi `"Rusak"` atau dibiarkan `null` (Normal). |
| **BR-04** | Formula Status: **INSTALLED** = `status_wo LIKE '%selesai%'`; **NOT INSTALLED** = `Total Dibawa − INSTALLED`. |
| **BR-05** | Deteksi merek otomatis: `ZTE` → ZTE, `FHTT` → Fiberhome, `ALCL` → Nokia, `HWTC`/`48575443` → Huawei. |
| **BR-06** | SN diisi dari pemindaian fisik perangkat oleh teknisi di rumah pelanggan saat menutup tiket WO. |
| **BR-07** | Koreksi data Reporting WO dilakukan via import ulang (upsert by `no_order`), bukan edit manual per baris. |

---

### 6. Peta Navigasi & Route Map

```
[GET /login]                        → Halaman login (guest only)
[POST /login]                       → Proses autentikasi
[POST /logout]                      → Logout

── Semua route di bawah dilindungi middleware auth ──

[GET /]                             → Dashboard Utama (Metric cards, Diagram Batang Tren, Tabel Rekap Teknisi)
├─ [GET /dashboard/chart-data]      → Endpoint data JSON untuk Chart Tren ONT Masuk vs Keluar
│
├─ [GET /ont-masuk]                 → Halaman ONT Masuk
│  ├─ [POST /ont-masuk]             → Simpan 1 unit (barcode scan / manual)
│  ├─ [POST /ont-masuk/import]      → Import massal Excel/CSV
│  ├─ [GET  /ont-masuk/template]    → Download template .xlsx
│  └─ [DELETE /ont-masuk/{id}]      → Hapus unit
│
├─ [GET /ont-keluar]                → Halaman ONT Keluar
│  ├─ [POST /ont-keluar]            → Catat penyerahan unit (berbasis checkbox)
│  ├─ [POST /ont-keluar/update-status] → Ubah kondisi Normal/Rusak via modal
│  └─ [DELETE /ont-keluar/{id}]     → Hapus transaksi
│
└─ [GET /reporting-wo]              → Halaman Data Report Work Order
   ├─ [POST /reporting-wo/import]   → Import Excel laporan WO (upsert by no_order)
   ├─ [GET  /reporting-wo/template] → Download template .xlsx
   └─ [DELETE /reporting-wo/{id}]   → Hapus laporan
```

---

### 7. Design System & UI

| Token | Nilai | Deskripsi |
| :--- | :--- | :--- |
| **Font** | Plus Jakarta Sans | Font utama (400, 500, 600, 700, 800) |
| **Background Body** | `#f8fafc` / `#f4f6f9` | Latar belakang bersih dan sejuk |
| **Primary Red** | `#c0392b` / `#b91c1c` | Warna aksen brand Telkom Akses / SIM-ONT |
| **Primary Blue** | `#2563eb` | Warna representasi ONT Masuk |
| **Success Green** | `#16a34a` | Warna representasi status INSTALLED / Normal |
| **Warning Orange** | `#d97706` | Warna representasi status NOT INSTALLED |
| **Card UI** | Border `#e8eaed`, radius `10px`, soft shadow | Tampilan kartu informasi modern |
| **Icons** | Bootstrap Icons (`bi bi-*`) | Ikonografi standar |
| **Layout** | Vertical Collapsible Sidebar | Sidebar kiri dengan toggle hide/show |

---

### 8. Fitur yang Belum Diimplementasikan (Backlog)

| # | Fitur | Prioritas | Catatan |
| :--- | :--- | :--- | :--- |
| 1 | Halaman Profil Admin | Low | Update nama, email, password akun admin |
| 2 | Export Excel Data Rekapitulasi | Medium | Export rekapitulasi data per teknisi & per periode |
| 3 | Multi-User & Role Permissions | Low | Hak akses bertingkat (Superadmin, Admin Gudang, Viewer) |