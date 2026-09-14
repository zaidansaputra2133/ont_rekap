<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SIM-ONT Web Routes (Front-End Prototype Phase)
|--------------------------------------------------------------------------
*/

// 1. Dashboard Utama
Route::get('/', function () {
    $totalMasuk = 142;
    $totalKeluar = 98;
    $totalRusak = 6;

    $rekapTeknisi = [
        ['nama' => 'Rian Hidayat', 'total_diambil' => 32, 'normal' => 30, 'rusak' => 2],
        ['nama' => 'Ahmad Fajar', 'total_diambil' => 28, 'normal' => 27, 'rusak' => 1],
        ['nama' => 'Bagus Prakoso', 'total_diambil' => 22, 'normal' => 20, 'rusak' => 2],
        ['nama' => 'Dedi Kurniawan', 'total_diambil' => 16, 'normal' => 15, 'rusak' => 1],
    ];

    return view('dashboard', compact('totalMasuk', 'totalKeluar', 'totalRusak', 'rekapTeknisi'));
})->name('dashboard');

// 2. Modul ONT Masuk (Gudang)
Route::get('/ont-masuk', function () {
    $totalCount = 142;
    return view('ont-masuk.index', compact('totalCount'));
})->name('ont-masuk.index');

// 3. Modul ONT Keluar (Penyerahan Teknisi)
Route::get('/ont-keluar', function () {
    $totalKeluar = 98;
    return view('ont-keluar.index', compact('totalKeluar'));
})->name('ont-keluar.index');
