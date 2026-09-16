<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OntMasukController;
use App\Http\Controllers\OntKeluarController;
use App\Http\Controllers\ReportingWoController;

/*
|--------------------------------------------------------------------------
| SIM-ONT Web Routes
|--------------------------------------------------------------------------
*/

// ─── Autentikasi (Guest Only) ────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Logout (Auth Required)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Route Terproteksi (Admin Harus Login) ───────────────────────────────────
Route::middleware('auth')->group(function () {

    // 1. Dashboard Utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Modul ONT Masuk (Gudang) - Full Backend
    Route::get('/ont-masuk', [OntMasukController::class, 'index'])->name('ont-masuk.index');
    Route::get('/ont-masuk/template', [OntMasukController::class, 'downloadTemplate'])->name('ont-masuk.template');
    Route::post('/ont-masuk', [OntMasukController::class, 'store'])->name('ont-masuk.store');
    Route::post('/ont-masuk/import', [OntMasukController::class, 'import'])->name('ont-masuk.import');
    Route::delete('/ont-masuk/{ontMasuk}', [OntMasukController::class, 'destroy'])->name('ont-masuk.destroy');

    // 3. Modul ONT Keluar (Penyerahan Teknisi) - Full Backend
    Route::get('/ont-keluar', [OntKeluarController::class, 'index'])->name('ont-keluar.index');
    Route::post('/ont-keluar', [OntKeluarController::class, 'store'])->name('ont-keluar.store');
    Route::post('/ont-keluar/update-status', [OntKeluarController::class, 'updateStatus'])->name('ont-keluar.update-status');
    Route::delete('/ont-keluar/{ontKeluar}', [OntKeluarController::class, 'destroy'])->name('ont-keluar.destroy');

    // 4. Modul Reporting Work Order (Laporan Lapangan)
    Route::get('/reporting-wo', [ReportingWoController::class, 'index'])->name('reporting-wo.index');
    Route::get('/reporting-wo/template', [ReportingWoController::class, 'downloadTemplate'])->name('reporting-wo.template');
    Route::post('/reporting-wo/import', [ReportingWoController::class, 'import'])->name('reporting-wo.import');
    Route::delete('/reporting-wo/{reportingWo}', [ReportingWoController::class, 'destroy'])->name('reporting-wo.destroy');

});
