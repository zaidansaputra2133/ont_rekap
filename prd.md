Berikut adalah dokumen **`PRD.md`** utuh terbaru yang sudah mencakup seluruh penyesuaian: arsitektur Laravel tanpa JS build tools, database MySQL Laragon (`ont_rekap`), modul *ONT Masuk*, *ONT Keluar*, *Reporting Work Order*, hingga logika pencocokan rekapitulasi **INSTALLED** dan **NOT INSTALLED** per teknisi.

---

```markdown
# Product Requirement Document (PRD)
## Sistem Rekapitulasi ONT & Reporting Work Order (SIM-ONT)

---

### 1. Ringkasan Eksekutif & Tujuan Sistem

**SIM-ONT** adalah aplikasi web internal berbasis **Laravel** yang berfungsi untuk mencatat, mengelola, dan memantau alur keluar-masuk perangkat ONT (modem) serta melakukan rekapitulasi otomatis laporan status *Work Order* (WO) teknisi di lapangan.

* **Masalah yang Diselesaikan:** Menghilangkan pencatatan rekap manual yang rentan *human error*, mencegah duplikasi *Serial Number* (SN), mempermudah pendataan unit ONT yang mengalami kerusakan, serta memverifikasi kesesuaian ONT yang dibawa teknisi dengan status penyelesaian WO secara *real-time*.
* **Prinsip Tampilan & Arsitektur:** **Sederhana, Cepat, dan Bebas Build-Tools.** Aplikasi menggunakan Laravel Blade dengan antarmuka Bootstrap 5 via CDN (100% tanpa Node.js, NPM, Vite, atau framework JavaScript tambahan).

---

### 2. Arsitektur Teknis & Environment (Tech Stack)

| Komponen | Teknologi / Spesifikasi | Keterangan |
| :--- | :--- | :--- |
| **Local Environment** | **Laragon** | Server lokal (Apache/Nginx + MySQL + PHP) |
| **Backend Framework** | **Laravel (PHP)** | Arsitektur MVC & Form Handling standar |
| **Database Engine** | **MySQL** | Running di Laragon (Database Name: **`ont_rekap`**) |
| **Frontend UI** | **Bootstrap 5 (via CDN)** | Dimuat via `<link rel="stylesheet">` pada header HTML |
| **Excel Processor** | **`maatwebsite/excel`** | Di-install via Composer (PHP) untuk membaca file `.xlsx`/`.csv` |

#### **Konfigurasi Environment (`.env`)**
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ont_rekap
DB_USERNAME=root
DB_PASSWORD=

```

---

### 3. Skema Basis Data (Database Schema - MySQL)

#### **Tabel 1: `ont_masuks` (Data Master ONT Gudang)**

*Menyimpan seluruh riwayat Serial Number ONT yang pernah diterima di gudang.*

| Field Name | Type Data MySQL | Attributes | Deskripsi / Aturan |
| --- | --- | --- | --- |
| `id` | `BIGINT` | Primary Key, Auto Increment | ID Unik Transaksi |
| `serial_number` | `VARCHAR(100)` | Unique, Indexed, Not Null | Serial Number ONT (misal: `ZTEGD4CCA770`) |
| `brand` | `VARCHAR(50)` | Nullable | Merek/Vendor (ZTE, Huawei, Fiberhome) |
| `tanggal_masuk` | `DATE` | Not Null | Tanggal penerimaan barang di gudang |
| `created_at` | `TIMESTAMP` | Nullable | Default Laravel |
| `updated_at` | `TIMESTAMP` | Nullable | Default Laravel |

#### **Tabel 2: `ont_keluars` (Data Transaksi Pengeluaran Teknisi)**

*Menyimpan riwayat penyerahan ONT ke teknisi beserta status kondisinya.*

| Field Name | Type Data MySQL | Attributes | Deskripsi / Aturan |
| --- | --- | --- | --- |
| `id` | `BIGINT` | Primary Key, Auto Increment | ID Unik Transaksi |
| `serial_number` | `VARCHAR(100)` | Foreign Key, Not Null | Terhubung ke `ont_masuks(serial_number)` |
| `nama_teknisi` | `VARCHAR(150)` | Not Null | Nama teknisi penerima |
| `tanggal_keluar` | `DATE` | Not Null | Tanggal penyerahan barang |
| `keterangan` | `VARCHAR(50)` | **Nullable** | Hanya diisi string `"Rusak"` (kosong jika normal) |
| `catatan` | `TEXT` | Nullable | Detail kerusakan atau alasan retur |
| `created_at` | `TIMESTAMP` | Nullable | Default Laravel |
| `updated_at` | `TIMESTAMP` | Nullable | Default Laravel |

#### **Tabel 3: `reporting_wos` (Data Laporan Work Order Teknisi)**

*Menyimpan data impor laporan pengoperasian WO dari sistem lapangan.*

| Field Name | Type Data MySQL | Attributes | Deskripsi / Aturan |
| --- | --- | --- | --- |
| `id` | `BIGINT` | Primary Key, Auto Increment | ID Unik Transaksi |
| `no_order` | `VARCHAR(100)` | Indexed, Not Null | Nomor Work Order (misal: `WO20264384690`) |
| `cid` | `VARCHAR(50)` | Nullable | ID Pelanggan / Circuit ID |
| `serial_number` | `VARCHAR(100)` | Foreign Key, Not Null | Terhubung ke `ont_masuks(serial_number)` |
| `nama_teknisi` | `VARCHAR(150)` | Not Null | Nama teknisi pada laporan WO |
| `nik_teknisi` | `VARCHAR(50)` | Nullable | NIK Teknisi |
| `status_wo` | `VARCHAR(50)` | Not Null | Status WO (misal: `Work Order Selesai`) |
| `tanggal_sa` | `DATE` | Nullable | Tanggal Service Advice / Penyelesaian |
| `vendor` | `VARCHAR(100)` | Nullable | Nama Vendor/Mitra (misal: `Telkom Akses PT`) |
| `sektor` | `VARCHAR(50)` | Nullable | Kode sektor area pengerjaan |
| `cek_match` | `VARCHAR(50)` | Nullable | Status kesesuaian (`SESUAI` / `Beda`) |
| `created_at` | `TIMESTAMP` | Nullable | Default Laravel |
| `updated_at` | `TIMESTAMP` | Nullable | Default Laravel |

---

### 4. Spesifikasi Fitur Utama (Functional Requirements)

#### **A. Modul ONT Masuk**

1. **Import File Excel/CSV:** Fitur untuk mengunggah file spreadsheet berkapasitas besar agar banyak *Serial Number* dapat di-input secara masal sekaligus.
2. **Input Manual Single-Item:** Form input cepat untuk menambahkan satu unit ONT jika ada barang susulan.
3. **Halaman Daftar Data ONT Masuk:** Halaman terpisah yang menampilkan seluruh tabel inventaris ONT Masuk, dilengkapi fitur pencarian (*Search*) dan pembagian halaman (*Pagination*).
4. **Validasi Anti-Duplikat:** Sistem menolak penginputan secara otomatis jika *Serial Number* sudah pernah terdaftar di tabel `ont_masuks`.

#### **B. Modul ONT Keluar**

1. **Input Penyerahan Barang:** Form untuk mencatat penyerahan unit ke teknisi dengan parameter: *Nama Teknisi*, *Tanggal Keluar*, dan *Serial Number ONT*.
2. **Validasi Keberadaan Barang:** Sistem hanya mengizinkan pengeluaran *Serial Number* yang sudah terdaftar di data `ont_masuks`.
3. **Update Status Kerusakan (Editable):** Admin dapat membuka baris data ONT Keluar untuk memperbarui kolom `keterangan` menjadi **"Rusak"** serta menambah `catatan` detail kerusakan jika barang dikembalikan dalam keadaan cacat.
4. **Status Default (Nullable):** Kolom `keterangan` bernilai kosong (`null`) secara default, yang menandakan perangkat dibawa teknisi dalam kondisi normal.

#### **C. Modul Reporting Work Order**

1. **Import Excel Reporting WO:** Modul khusus untuk mengunggah file rekap laporan WO teknisi (`reporting_wo.xlsx`).
2. **Pencocokan Data (Auto-Match):** Sistem memeriksa apakah `serial_number` pada laporan WO cocok dengan data `ont_keluars` yang pernah diambil teknisi tersebut.
3. **Halaman Monitoring WO:** Tabel daftar laporan WO yang dapat difilter berdasarkan status (*Work Order Selesai* vs *Belum Selesai*) dan Nama Teknisi.

#### **D. Modul Dashboard & Rekapitulasi Auto**

1. **Rangkuman Metric Cards:** Menampilkan 4 statistik utama secara *real-time*:
* Total ONT Masuk (Gudang).
* Total ONT Keluar (Dibawa Teknisi).
* Total INSTALLED (Perangkat terpasang di lokasi pelanggan).
* Total NOT INSTALLED (Perangkat belum terpasang / masih di teknisi).
* Total ONT Rusak.


2. **Tabel Rekapitulasi Per Teknisi (Performa Teknisi):**
Tabel rekapitulasi otomatis yang mengelompokkan data per *Nama Teknisi* dengan kolom:
* **NAMA TEKNISI**
* **INSTALLED:** Jumlah unit ONT milik teknisi yang tercatat di `reporting_wos` dengan `status_wo = 'Work Order Selesai'`.
* **NOT INSTALLED:** Selisih unit yang dibawa teknisi namun belum berstatus *Work Order Selesai* ($\text{Total Dibawa} - \text{INSTALLED}$).
* **RUSAK:** Jumlah unit milik teknisi yang ditandai status `"Rusak"` pada tabel `ont_keluars`.
* **TOTAL DIBAWA:** Total akumulasi fisik unit ONT yang diambil oleh teknisi.
* **Baris Grand Total:** Baris akumulasi akhir di bagian bawah tabel untuk menghitung total seluruh teknisi secara otomatis.



---

### 5. Aturan Bisnis & Validasi (Business Rules)

* **BR-01 (Integritas Serial Number):** Pengeluaran barang (`ont_keluars`) & Laporan WO (`reporting_wos`) wajib terikat pada *Serial Number* yang terdaftar di `ont_masuks`.
* **BR-02 (Satu Transaksi Keluar per SN):** Satu *Serial Number* yang sama tidak boleh dicatat keluar dua kali kecuali data sebelumnya sudah dihapus/dibatalkan.
* **BR-03 (Nilai Keterangan):** Kolom `keterangan` pada `ont_keluars` bersifat *nullable* dan khusus diisi string `"Rusak"`.
* **BR-04 (Rekonsiliasi Status Work Order):**
* Unit ONT berstatus **INSTALLED** jika `serial_number` tersebut ada di tabel `reporting_wos` dengan `status_wo = 'Work Order Selesai'`.
* Jika belum ada laporan WO Selesai untuk SN tersebut, maka unit otomatis dikategorikan **NOT INSTALLED**.



---

### 6. Peta Navigasi Halaman (Information Architecture)

```
[Dashboard Utama]
  ├── Card Metric (Total Masuk | Total Keluar | Total INSTALLED | Total NOT INSTALLED | Total Rusak)
  └── Tabel Rekapitulasi per Teknisi (Nama Teknisi | Installed | Not Installed | Rusak | Total Dibawa)
  │
  ├── [Menu ONT Masuk]
  │     ├── Form Upload Excel / Input Manual
  │     └── Tabel Daftar ONT Masuk (Search & Pagination)
  │
  ├── [Menu ONT Keluar]
  │     ├── Form Penyerahan ke Teknisi
  │     ├── Tabel Daftar ONT Keluar
  │     └── Modal Update Status "Rusak" & Catatan
  │
  └── [Menu Reporting WO]
        ├── Form Upload Excel Laporan WO
        └── Tabel Daftar Laporan Work Order (Filter Status WO & Teknisi)

```

```

---

Dokumen `PRD.md` di atas sudah mencakup seluruh kebutuhan teknis dan alur bisnis sistem.

```