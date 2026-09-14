@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Header & Quick Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Rekapitulasi ONT</h4>
        <p class="text-muted small mb-0">Pemantauan persediaan perangkat modem dan riwayat penyerahan teknisi.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ url('/ont-masuk') }}" class="btn btn-outline-theme btn-sm rounded-2 px-3">
            <i class="bi bi-plus-lg me-1"></i> Input Masuk
        </a>
        <a href="{{ url('/ont-keluar') }}" class="btn btn-custom-primary btn-sm rounded-2 px-3">
            <i class="bi bi-box-arrow-up-right me-1"></i> Penyerahan Teknisi
        </a>
    </div>
</div>

<!-- 3 Metric Cards (Clean, Professional, Spacious, Smooth Radius) -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total ONT Masuk -->
    <div class="col-12 col-md-4">
        <div class="card card-custom metric-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-medium" style="font-size: 0.78rem; letter-spacing: 0.02em;">TOTAL ONT MASUK</span>
                <span class="badge badge-soft-secondary px-2 py-0.5 rounded-2" style="font-size: 0.72rem;">Master Gudang</span>
            </div>
            <div class="py-2.5 my-1 d-flex align-items-baseline gap-1.5">
                <span class="fs-1 fw-bold text-dark lh-1">{{ $totalMasuk ?? 142 }}</span>
                <span class="text-muted small">unit</span>
            </div>
            <div class="pt-3 mt-1 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 0.78rem;">
                <span>Sisa fisik di gudang:</span>
                <span class="fw-semibold text-dark">{{ ($totalMasuk ?? 142) - ($totalKeluar ?? 98) }} Unit</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Total ONT Keluar -->
    <div class="col-12 col-md-4">
        <div class="card card-custom metric-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-medium" style="font-size: 0.78rem; letter-spacing: 0.02em;">TOTAL ONT KELUAR</span>
                <span class="badge badge-soft-secondary px-2 py-0.5 rounded-2" style="font-size: 0.72rem;">Di Lapangan</span>
            </div>
            <div class="py-2.5 my-1 d-flex align-items-baseline gap-1.5">
                <span class="fs-1 fw-bold text-dark lh-1">{{ $totalKeluar ?? 98 }}</span>
                <span class="text-muted small">unit</span>
            </div>
            <div class="pt-3 mt-1 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 0.78rem;">
                <span>Total teknisi pembawa:</span>
                <span class="fw-semibold text-dark">{{ count($rekapTeknisi ?? [1,2,3,4]) }} Orang</span>
            </div>
        </div>
    </div>

    <!-- Card 3: Total ONT Rusak -->
    <div class="col-12 col-md-4">
        <div class="card card-custom metric-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-medium" style="font-size: 0.78rem; letter-spacing: 0.02em;">TOTAL UNIT RUSAK</span>
                <span class="badge badge-theme-red px-2 py-0.5 rounded-2" style="font-size: 0.72rem;">Perlu Retur</span>
            </div>
            <div class="py-2.5 my-1 d-flex align-items-baseline gap-1.5">
                <span class="fs-1 fw-bold lh-1" style="color: var(--theme-red);">{{ $totalRusak ?? 6 }}</span>
                <span class="text-muted small">unit</span>
            </div>
            <div class="pt-3 mt-1 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 0.78rem;">
                <span>Rasio kerusakan lapangan:</span>
                <span class="fw-semibold" style="color: var(--theme-red);">
                    {{ round((($totalRusak ?? 6) / max(($totalKeluar ?? 98), 1)) * 100, 1) }}%
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Row: Rekapitulasi per Teknisi & SOP Penanganan Unit -->
<div class="row g-4">
    <!-- Left Column: Tabel Rekapitulasi per Teknisi -->
    <div class="col-lg-8">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom py-3 px-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Rekapitulasi Unit per Teknisi</h6>
                    <span class="text-muted small" style="font-size: 0.78rem;">Rincian unit yang sedang dibawa serta tingkat kerusakan per personil</span>
                </div>
                <a href="{{ url('/ont-keluar') }}" class="text-decoration-none small fw-medium" style="color: var(--theme-red); font-size: 0.8rem;">
                    Buka Transaksi &rarr;
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Teknisi</th>
                            <th class="text-center">Diambil</th>
                            <th class="text-center">Normal</th>
                            <th class="text-center">Rusak</th>
                            <th class="text-center" style="width: 130px;">Rasio Cacat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapTeknisi ?? [] as $index => $teknisi)
                        <tr>
                            <td class="text-muted text-center small">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle fw-semibold d-flex align-items-center justify-content-center small" 
                                         style="width: 28px; height: 28px; font-size: 0.75rem; background-color: var(--theme-red-light); color: var(--theme-red); border: 1px solid var(--theme-red-border);">
                                        {{ strtoupper(substr($teknisi['nama'], 0, 2)) }}
                                    </div>
                                    <span class="fw-medium text-dark">{{ $teknisi['nama'] }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="fw-semibold text-dark">{{ $teknisi['total_diambil'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-soft-success px-2 py-0.5 rounded-1 small">{{ $teknisi['normal'] }}</span>
                            </td>
                            <td class="text-center">
                                @if($teknisi['rusak'] > 0)
                                    <span class="badge badge-theme-red px-2 py-0.5 rounded-1 small">
                                        {{ $teknisi['rusak'] }}
                                    </span>
                                @else
                                    <span class="text-muted small">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $percentage = $teknisi['total_diambil'] > 0 ? round(($teknisi['rusak'] / $teknisi['total_diambil']) * 100) : 0;
                                @endphp
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 4px; background-color: #f1f5f9;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%; background-color: {{ $percentage > 0 ? 'var(--theme-red)' : '#166534' }};"></div>
                                    </div>
                                    <span class="small text-muted" style="font-size: 0.74rem;">{{ $percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">
                                Belum ada data penyerahan teknisi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: SOP Operasional & Panduan Alur Kerja -->
    <div class="col-lg-4">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom py-3 px-3">
                <h6 class="fw-bold mb-0 text-dark">SOP Penanganan Unit</h6>
                <span class="text-muted small" style="font-size: 0.78rem;">Prosedur sirkulasi barang gudang & lapangan</span>
            </div>
            <div class="card-body p-3">
                <!-- Timeline Langkah Operasional -->
                <div class="d-flex flex-column gap-3">
                    <!-- Langkah 1 -->
                    <div class="d-flex gap-3 align-items-start">
                        <div class="fw-bold text-muted" style="font-size: 0.8rem; min-width: 20px;">01</div>
                        <div>
                            <span class="d-block fw-semibold text-dark small">Penerimaan & Input SN</span>
                            <span class="text-muted d-block" style="font-size: 0.78rem; line-height: 1.4;">
                                Fisik barang tiba di gudang. Catat Serial Number secara manual atau unggah file Excel untuk volume besar.
                            </span>
                        </div>
                    </div>

                    <!-- Langkah 2 -->
                    <div class="d-flex gap-3 align-items-start">
                        <div class="fw-bold text-muted" style="font-size: 0.8rem; min-width: 20px;">02</div>
                        <div>
                            <span class="d-block fw-semibold text-dark small">Penyerahan ke Teknisi</span>
                            <span class="text-muted d-block" style="font-size: 0.78rem; line-height: 1.4;">
                                Teknisi mengambil unit. Petugas mencatat nama & tanggal. Hanya SN yang sah terdaftar di gudang yang dapat diserahkan.
                            </span>
                        </div>
                    </div>

                    <!-- Langkah 3 -->
                    <div class="d-flex gap-3 align-items-start">
                        <div class="fw-bold text-muted" style="font-size: 0.8rem; min-width: 20px;">03</div>
                        <div>
                            <span class="d-block fw-semibold text-dark small">Retur / Pelaporan Cacat</span>
                            <span class="text-muted d-block" style="font-size: 0.78rem; line-height: 1.4;">
                                Jika unit bermasalah di lokasi, ubah status menjadi <strong style="color: var(--theme-red);">"Rusak"</strong> dan sertakan catatan kendala teknis (port mati, adaptor, LOS merah).
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card Catatan Validasi Ringkas -->
                <div class="p-2.5 rounded-2 mt-4 border" style="background-color: #fafbfc;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge badge-theme-red px-1.5 py-0.5 rounded-1" style="font-size: 0.68rem;">Validasi Otomatis</span>
                    </div>
                    <span class="text-muted d-block" style="font-size: 0.75rem; line-height: 1.35;">
                        Sistem memblokir pencatatan Serial Number ganda dan melarang transaksi keluar atas SN yang tidak terdata.
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
