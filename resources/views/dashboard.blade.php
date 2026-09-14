@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Header & Quick Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Rekapitulasi Persediaan & Performa Teknisi</h4>
        <p class="text-muted small mb-0">Integrasi pemantauan stok gudang, distribusi penyerahan teknisi, dan status instalasi lapangan.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ url('/ont-masuk') }}" class="btn btn-outline-theme btn-sm rounded-2 px-3">
            <i class="bi bi-box-arrow-in-down me-1"></i> ONT Masuk
        </a>
        <a href="{{ url('/ont-keluar') }}" class="btn btn-outline-theme btn-sm rounded-2 px-3">
            <i class="bi bi-box-arrow-up-right me-1"></i> Penyerahan Unit
        </a>
        <a href="{{ url('/reporting-wo') }}" class="btn btn-custom-primary btn-sm rounded-2 px-3">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Upload Laporan WO
        </a>
    </div>
</div>

<!-- 3 Metric Cards: Installed, Not Installed, Unit Rusak -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total INSTALLED -->
    <div class="col-12 col-md-4">
        <div class="card card-custom metric-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-medium" style="font-size: 0.76rem; letter-spacing: 0.02em;">INSTALLED</span>
                <span class="badge badge-soft-success px-2 py-0.5 rounded-2" style="font-size: 0.72rem;">Work Order Selesai</span>
            </div>
            <div class="py-2.5 my-1 d-flex align-items-baseline gap-1.5">
                <span class="fs-1 fw-bold text-success lh-1">{{ number_format($totalInstalled ?? 0) }}</span>
                <span class="text-muted small">unit</span>
            </div>
            <div class="pt-2 mt-1 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 0.78rem;">
                <span>Terpasang di pelanggan:</span>
                <span class="fw-semibold text-success">
                    {{ $totalKeluar > 0 ? round((($totalInstalled ?? 0) / $totalKeluar) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>
    </div>

    <!-- Card 2: Total NOT INSTALLED -->
    <div class="col-12 col-md-4">
        <div class="card card-custom metric-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-medium" style="font-size: 0.76rem; letter-spacing: 0.02em;">NOT INSTALLED</span>
                <span class="badge badge-soft-secondary px-2 py-0.5 rounded-2" style="font-size: 0.72rem;">Unit di Teknisi</span>
            </div>
            <div class="py-2.5 my-1 d-flex align-items-baseline gap-1.5">
                <span class="fs-1 fw-bold text-dark lh-1">{{ number_format($totalNotInstalled ?? 0) }}</span>
                <span class="text-muted small">unit</span>
            </div>
            <div class="pt-2 mt-1 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 0.78rem;">
                <span>Belum ada WO selesai:</span>
                <span class="fw-semibold text-dark">
                    {{ $totalKeluar > 0 ? round((($totalNotInstalled ?? 0) / $totalKeluar) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Rusak -->
    <div class="col-12 col-md-4">
        <div class="card card-custom metric-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-medium" style="font-size: 0.76rem; letter-spacing: 0.02em;">UNIT RUSAK</span>
                <span class="badge badge-theme-red px-2 py-0.5 rounded-2" style="font-size: 0.72rem;">Cacat / Retur</span>
            </div>
            <div class="py-2.5 my-1 d-flex align-items-baseline gap-1.5">
                <span class="fs-1 fw-bold lh-1" style="color: var(--theme-red);">{{ number_format($totalRusak) }}</span>
                <span class="text-muted small">unit</span>
            </div>
            <div class="pt-2 mt-1 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 0.78rem;">
                <span>Rasio kerusakan lapangan:</span>
                <span class="fw-semibold" style="color: var(--theme-red);">
                    {{ round(($totalRusak / max($totalKeluar, 1)) * 100, 1) }}%
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Row: Tabel Rekapitulasi per Teknisi (PRD 4.D #2) & Info Status -->
<div class="row g-4">
    <!-- Left Column: Tabel Rekapitulasi Performa & Persediaan per Teknisi -->
    <div class="col-lg-8">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom py-3 px-3.5">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Tabel Rekapitulasi per Teknisi</h6>
                        <span class="text-muted small" style="font-size: 0.78rem;">Akumulasi status unit ONT berdasarkan penyerahan dan laporan WO lapangan</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" id="filterTeknisiInput" class="form-control form-control-sm" placeholder="Cari teknisi..." style="max-width: 180px;" onkeyup="filterTableTeknisi()">
                        <a href="{{ url('/reporting-wo') }}" class="btn btn-outline-theme btn-sm rounded-2 text-nowrap" style="font-size: 0.78rem;">
                            <i class="bi bi-journal-text me-1"></i> Detail WO
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0" id="tableRekapTeknisi">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Teknisi</th>
                            <th class="text-center">Installed</th>
                            <th class="text-center">Not Installed</th>
                            <th class="text-center">Rusak</th>
                            <th class="text-center">Total Dibawa</th>
                            <th class="text-center" style="width: 140px;">Rasio Terpasang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandInstalled = 0;
                            $grandNotInstalled = 0;
                            $grandRusak = 0;
                            $grandTotalDibawa = 0;
                        @endphp

                        @forelse($rekapTeknisi ?? [] as $index => $teknisi)
                        @php
                            $installed = $teknisi['installed'] ?? 0;
                            $notInstalled = $teknisi['not_installed'] ?? max(0, ($teknisi['total_dibawa'] ?? $teknisi['total_diambil'] ?? 0) - $installed);
                            $rusak = $teknisi['rusak'] ?? 0;
                            $totalDibawa = $teknisi['total_dibawa'] ?? $teknisi['total_diambil'] ?? 0;
                            $pctInstalled = $totalDibawa > 0 ? round(($installed / $totalDibawa) * 100) : 0;

                            $grandInstalled += $installed;
                            $grandNotInstalled += $notInstalled;
                            $grandRusak += $rusak;
                            $grandTotalDibawa += $totalDibawa;
                        @endphp
                        <tr>
                            <td class="text-muted text-center small">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle fw-semibold d-flex align-items-center justify-content-center small" 
                                         style="width: 30px; height: 30px; font-size: 0.75rem; background-color: var(--theme-red-light); color: var(--theme-red); border: 1px solid var(--theme-red-border);">
                                        {{ strtoupper(substr($teknisi['nama'], 0, 2)) }}
                                    </div>
                                    <span class="fw-medium text-dark teknisi-nama">{{ $teknisi['nama'] }}</span>
                                </div>
                            </td>
                            <!-- INSTALLED -->
                            <td class="text-center">
                                @if($installed > 0)
                                    <span class="badge badge-soft-success px-2.5 py-1 rounded-1 small fw-semibold">
                                        {{ $installed }}
                                    </span>
                                @else
                                    <span class="text-muted small">0</span>
                                @endif
                            </td>
                            <!-- NOT INSTALLED -->
                            <td class="text-center">
                                @if($notInstalled > 0)
                                    <span class="badge badge-soft-secondary px-2.5 py-1 rounded-1 small fw-semibold">
                                        {{ $notInstalled }}
                                    </span>
                                @else
                                    <span class="text-muted small">0</span>
                                @endif
                            </td>
                            <!-- RUSAK -->
                            <td class="text-center">
                                @if($rusak > 0)
                                    <span class="badge badge-theme-red px-2.5 py-1 rounded-1 small fw-semibold">
                                        {{ $rusak }}
                                    </span>
                                @else
                                    <span class="text-muted small">0</span>
                                @endif
                            </td>
                            <!-- TOTAL DIBAWA -->
                            <td class="text-center">
                                <span class="fw-bold text-dark fs-6">{{ $totalDibawa }}</span>
                            </td>
                            <!-- PROGRESS / RASIO TERPASANG -->
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; background-color: #f1f5f9; border-radius: 4px;">
                                        <div class="progress-bar rounded-1" role="progressbar" style="width: {{ $pctInstalled }}%; background-color: {{ $pctInstalled >= 70 ? '#166534' : ($pctInstalled > 0 ? '#b91c1c' : '#cbd5e1') }};"></div>
                                    </div>
                                    <span class="small fw-medium text-muted" style="font-size: 0.75rem; min-width: 32px; text-align: right;">{{ $pctInstalled }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted small">
                                <i class="bi bi-people fs-4 d-block mb-2 text-muted opacity-50"></i>
                                Belum ada data transaksi penyerahan teknisi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                    <!-- Baris Grand Total Sesuai PRD 4.D #2 -->
                    @if(!empty($rekapTeknisi) && count($rekapTeknisi) > 0)
                    <tfoot class="border-top-2" style="background-color: #fafbfc;">
                        <tr class="fw-bold text-dark">
                            <td colspan="2" class="py-3 px-3 text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.03em;">
                                <i class="bi bi-calculator me-1 text-muted"></i> Grand Total (Semua Teknisi)
                            </td>
                            <td class="text-center py-3">
                                <span class="badge badge-soft-success px-2.5 py-1 rounded-1 fs-6">
                                    {{ $grandInstalled }}
                                </span>
                            </td>
                            <td class="text-center py-3">
                                <span class="badge badge-soft-secondary px-2.5 py-1 rounded-1 fs-6">
                                    {{ $grandNotInstalled }}
                                </span>
                            </td>
                            <td class="text-center py-3">
                                <span class="badge badge-theme-red px-2.5 py-1 rounded-1 fs-6">
                                    {{ $grandRusak }}
                                </span>
                            </td>
                            <td class="text-center py-3">
                                <span class="fw-bold text-dark fs-5">{{ $grandTotalDibawa }}</span>
                            </td>
                            <td class="text-center py-3">
                                @php
                                    $grandPct = $grandTotalDibawa > 0 ? round(($grandInstalled / $grandTotalDibawa) * 100) : 0;
                                @endphp
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; background-color: #e2e8f0; border-radius: 4px;">
                                        <div class="progress-bar rounded-1" role="progressbar" style="width: {{ $grandPct }}%; background-color: #166534;"></div>
                                    </div>
                                    <span class="small fw-bold text-dark" style="font-size: 0.78rem; min-width: 32px; text-align: right;">{{ $grandPct }}%</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Panduan Aturan Rekapitulasi & Alur Sistem -->
    <div class="col-lg-4">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom py-3 px-3">
                <h6 class="fw-bold mb-0 text-dark">Keterangan Kolom Rekapitulasi</h6>
                <span class="text-muted small" style="font-size: 0.78rem;">Formula dan acuan data sesuai PRD</span>
            </div>
            <div class="card-body p-3">
                <!-- Penjelasan Kolom -->
                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="d-flex gap-2.5 align-items-start">
                        <span class="badge badge-soft-success rounded-1 px-2 py-0.5" style="font-size: 0.72rem; min-width: 65px; text-align: center;">INSTALLED</span>
                        <div>
                            <span class="d-block fw-semibold text-dark small">Perangkat Terpasang</span>
                            <span class="text-muted d-block" style="font-size: 0.76rem; line-height: 1.4;">
                                Jumlah unit teknisi yang terdata pada laporan WO dengan status <strong>"Work Order Selesai"</strong>.
                            </span>
                        </div>
                    </div>

                    <div class="d-flex gap-2.5 align-items-start">
                        <span class="badge badge-soft-secondary rounded-1 px-2 py-0.5" style="font-size: 0.72rem; min-width: 65px; text-align: center;">NOT INSTALLED</span>
                        <div>
                            <span class="d-block fw-semibold text-dark small">Unit di Lapangan</span>
                            <span class="text-muted d-block" style="font-size: 0.76rem; line-height: 1.4;">
                                Selisih unit yang dibawa teknisi namun belum memiliki laporan WO Selesai (Total Dibawa &minus; INSTALLED).
                            </span>
                        </div>
                    </div>

                    <div class="d-flex gap-2.5 align-items-start">
                        <span class="badge badge-theme-red rounded-1 px-2 py-0.5" style="font-size: 0.72rem; min-width: 65px; text-align: center;">RUSAK</span>
                        <div>
                            <span class="d-block fw-semibold text-dark small">Unit Retur / Cacat</span>
                            <span class="text-muted d-block" style="font-size: 0.76rem; line-height: 1.4;">
                                Jumlah unit milik teknisi yang ditandai kondisi <strong>"Rusak"</strong> pada tabel ONT Keluar.
                            </span>
                        </div>
                    </div>

                    <div class="d-flex gap-2.5 align-items-start">
                        <span class="badge bg-light border text-dark rounded-1 px-2 py-0.5" style="font-size: 0.72rem; min-width: 65px; text-align: center;">TOTAL</span>
                        <div>
                            <span class="d-block fw-semibold text-dark small">Total Dibawa</span>
                            <span class="text-muted d-block" style="font-size: 0.76rem; line-height: 1.4;">
                                Total akumulasi fisik modem ONT yang pernah diserahkan kepada teknisi.
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigasi Cepat Modul -->
                <div class="p-3 rounded-2 border" style="background-color: #fafbfc;">
                    <span class="fw-semibold text-dark small d-block mb-2">Tindakan Cepat:</span>
                    <div class="d-flex flex-column gap-2" style="font-size: 0.8rem;">
                        <a href="{{ url('/reporting-wo') }}" class="text-decoration-none d-flex justify-content-between align-items-center text-muted">
                            <span><i class="bi bi-upload me-1 text-danger"></i> Impor Rekap WO (.xlsx)</span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                        <a href="{{ url('/ont-keluar') }}" class="text-decoration-none d-flex justify-content-between align-items-center text-muted">
                            <span><i class="bi bi-arrow-up-right-circle me-1 text-danger"></i> Form Penyerahan Teknisi</span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                        <a href="{{ url('/ont-masuk') }}" class="text-decoration-none d-flex justify-content-between align-items-center text-muted">
                            <span><i class="bi bi-box-seam me-1 text-danger"></i> Kelola Stok Master Gudang</span>
                            <i class="bi bi-chevron-right small"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Live filter tabel teknisi
    function filterTableTeknisi() {
        const input = document.getElementById('filterTeknisiInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('tableRekapTeknisi');
        const trs = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let i = 0; i < trs.length; i++) {
            const tdNama = trs[i].querySelector('.teknisi-nama');
            if (tdNama) {
                const txtValue = tdNama.textContent || tdNama.innerText;
                trs[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
            }
        }
    }
</script>
@endpush
