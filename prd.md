# Product Requirement Document (PRD)
## Sistem Rekapitulasi ONT Masuk & Keluar (SIM-ONT)

---

### 1. Ringkasan Eksekutif & Tujuan Sistem

**SIM-ONT** adalah aplikasi web internal berbasis **Laravel** yang berfungsi untuk mencatat, mengelola, dan memantau alur keluar-masuk perangkat ONT (modem) antara persediaan gudang dan penyerahan kepada teknisi lapangan.

* **Masalah yang Diselesaikan:** Menghilangkan pengerjaan rekap manual yang rentan *human error*, mencegah duplikasi pencatatan *Serial Number* (SN), serta mempermudah identifikasi unit ONT yang mengalami kerusakan.
* **Prinsip Tampilan & Arsitektur:** **Sederhana, Cepat, dan Bebas Build-Tools.** Aplikasi menggunakan Laravel Blade dengan antarmuka Bootstrap 5 via CDN (100% tanpa Node.js, NPM, Vite, atau framework JavaScript tambahan).

---

### 2. Arsitektur Teknis & Environment (Tech Stack)

| Komponen | Teknologi / Spesifikasi | Keterangan |
| :--- | :--- | :--- |
| **Local Environment** | **Laragon** | Server lokal (Apache/Nginx + MySQL + PHP) |
| **Backend Framework** | **Laravel (PHP)** | Arsitektur MVR (Model, View, Route) & Form Handling standar |
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