@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .chart-mode-btn {
        font-size: 0.78rem;
        font-weight: 500;
        padding: 0.3rem 0.7rem;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #4b5563;
        transition: all 0.15s ease;
    }
    .chart-mode-btn:hover {
        background: #fdf2f2;
        color: #c0392b;
        border-color: #f5c6c6;
    }
    .chart-mode-btn.active {
        background: #c0392b;
        color: #fff;
        border-color: #c0392b;
    }
    .chart-mode-btn:first-child { border-radius: 7px 0 0 7px; }
    .chart-mode-btn:last-child  { border-radius: 0 7px 7px 0; }

    .chart-filter-checkbox {
        cursor: pointer;
        width: 1.15em;
        height: 1.15em;
    }
    .chart-filter-checkbox.masuk:checked {
        background-color: #2563eb;
        border-color: #2563eb;
    }
    .chart-filter-checkbox.keluar:checked {
        background-color: #c0392b;
        border-color: #c0392b;
    }
    .chart-bar-indicator {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        display: inline-block;
    }
    .chart-checkbox-label {
        font-size: 0.82rem;
        cursor: pointer;
        user-select: none;
    }
</style>
@endpush

@section('content')

{{-- Page Heading --}}
<div class="mb-4">
    <h4 class="fw-bold mb-0" style="font-size: 1.45rem; color: #1a1d23; letter-spacing: -0.02em;">Dashboard</h4>
    <p class="text-muted mb-0" style="font-size: 0.83rem;">Rekap stok & performa instalasi lapangan</p>
</div>

{{-- ── Metric Cards ─────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- INSTALLED --}}
    <div class="col-6 col-xl-3">
        <div class="card-custom metric-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="fw-700" style="font-size: 0.72rem; letter-spacing: 0.06em; color: #16a34a;">INSTALLED</span>
                <span style="width:10px;height:10px;border-radius:50%;background:#16a34a;display:inline-block;margin-top:2px;"></span>
            </div>
            <div class="mb-1" style="font-size: 2.4rem; font-weight: 800; color: #16a34a; line-height: 1; letter-spacing: -0.03em;">
                {{ number_format($totalInstalled ?? 0) }}
            </div>
            <div style="font-size: 0.75rem; color: #94a3b8;">Work Order Selesai</div>
        </div>
    </div>

    {{-- NOT INSTALLED --}}
    <div class="col-6 col-xl-3">
        <div class="card-custom metric-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="fw-700" style="font-size: 0.72rem; letter-spacing: 0.06em; color: #d97706;">NOT INSTALLED</span>
                <span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;margin-top:2px;"></span>
            </div>
            <div class="mb-1" style="font-size: 2.4rem; font-weight: 800; color: #d97706; line-height: 1; letter-spacing: -0.03em;">
                {{ number_format($totalNotInstalled ?? 0) }}
            </div>
            <div style="font-size: 0.75rem; color: #94a3b8;">Unit di Lapangan</div>
        </div>
    </div>

    {{-- RUSAK --}}
    <div class="col-6 col-xl-3">
        <div class="card-custom metric-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="fw-700" style="font-size: 0.72rem; letter-spacing: 0.06em; color: #c0392b;">RUSAK</span>
                <span style="width:10px;height:10px;border-radius:50%;background:#c0392b;display:inline-block;margin-top:2px;"></span>
            </div>
            <div class="mb-1" style="font-size: 2.4rem; font-weight: 800; color: #c0392b; line-height: 1; letter-spacing: -0.03em;">
                {{ number_format($totalRusak ?? 0) }}
            </div>
            <div style="font-size: 0.75rem; color: #94a3b8;">Unit Retur / Cacat</div>
        </div>
    </div>

    {{-- TOTAL --}}
    <div class="col-6 col-xl-3">
        <div class="card-custom metric-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="fw-700" style="font-size: 0.72rem; letter-spacing: 0.06em; color: #2563eb;">TOTAL</span>
                <span style="width:10px;height:10px;border-radius:50%;background:#2563eb;display:inline-block;margin-top:2px;"></span>
            </div>
            <div class="mb-1" style="font-size: 2.4rem; font-weight: 800; color: #2563eb; line-height: 1; letter-spacing: -0.03em;">
                {{ number_format($totalKeluar ?? 0) }}
            </div>
            <div style="font-size: 0.75rem; color: #94a3b8;">Total Dibawa</div>
        </div>
    </div>

</div>

{{-- ── Chart ONT Masuk vs Keluar ────────────────────────────────────────────── --}}
<div class="card-custom mb-4">
    {{-- Header --}}
    <div class="p-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2"
         style="border-bottom: 1px solid #e8eaed;">
        <div>
            <h6 class="fw-bold mb-0" style="font-size: 0.95rem; color: #1a1d23;">
                <i class="bi bi-bar-chart-line me-1" style="color: #c0392b;"></i>
                Tren ONT Masuk & Keluar
            </h6>
            <p class="mb-0" style="font-size: 0.74rem; color: #94a3b8;">Perbandingan stok masuk dan keluar gudang</p>
        </div>

        {{-- Controls --}}
        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Mode toggle --}}
            <div class="btn-group btn-group-sm" role="group" id="chartModeGroup">
                <button type="button" class="btn chart-mode-btn active" data-mode="daily">Harian</button>
                <button type="button" class="btn chart-mode-btn" data-mode="weekly">Mingguan</button>
                <button type="button" class="btn chart-mode-btn" data-mode="monthly">Bulanan</button>
            </div>

            {{-- Filter: bulan (untuk daily) --}}
            <input type="month" id="filterMonth"
                   class="form-control form-control-sm"
                   value="{{ now()->format('Y-m') }}"
                   title="Pilih bulan"
                   style="max-width: 145px; display:block;">

            {{-- Filter: tahun (untuk monthly & weekly) --}}
            <select id="filterYear" class="form-select form-select-sm" style="max-width: 90px; display:none;">
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>

    {{-- Chart canvas --}}
    <div class="p-3 p-sm-4" style="position: relative;">
        <div id="chartLoadingState" class="d-flex align-items-center justify-content-center gap-2"
             style="height: 260px; display:none !important;">
            <div class="spinner-border spinner-border-sm text-danger" role="status"></div>
            <span style="font-size: 0.82rem; color: #94a3b8;">Memuat data...</span>
        </div>
        <canvas id="ontTrendChart" style="max-height: 280px;"></canvas>
    </div>

    {{-- Legend & Checkbox toggle summary bawah --}}
    <div class="px-4 pb-3 d-flex flex-wrap align-items-center gap-4 border-top pt-3" style="border-color: #f1f5f9 !important;">
        <div class="form-check d-flex align-items-center gap-2 mb-0">
            <input class="form-check-input chart-filter-checkbox masuk" type="checkbox" id="toggleMasuk" checked>
            <label class="form-check-label chart-checkbox-label d-flex align-items-center gap-2" for="toggleMasuk">
                <span class="chart-bar-indicator" style="background:#2563eb;"></span>
                <span style="color: #334155; font-weight: 500;">ONT Masuk:</span>
                <strong id="summaryMasuk" style="color: #2563eb;">—</strong>
            </label>
        </div>
        <div class="form-check d-flex align-items-center gap-2 mb-0">
            <input class="form-check-input chart-filter-checkbox keluar" type="checkbox" id="toggleKeluar" checked>
            <label class="form-check-label chart-checkbox-label d-flex align-items-center gap-2" for="toggleKeluar">
                <span class="chart-bar-indicator" style="background:#c0392b;"></span>
                <span style="color: #334155; font-weight: 500;">ONT Keluar:</span>
                <strong id="summaryKeluar" style="color: #c0392b;">—</strong>
            </label>
        </div>
        <small class="text-muted ms-auto d-none d-md-inline" style="font-size: 0.72rem;">* Centang checkbox untuk menampilkan/menyembunyikan batang grafik</small>
    </div>
</div>

{{-- ── Rekapitulasi Per Teknisi ─────────────────────────────────────────────── --}}
<div class="card-custom mb-4">
    <div class="p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2"
         style="border-bottom: 1px solid #e8eaed;">
        <h6 class="fw-bold mb-0" style="font-size: 0.95rem; color: #1a1d23;">Rekapitulasi Per Teknisi</h6>
        <div class="input-group input-group-sm" style="max-width: 210px;">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted" style="font-size: 0.8rem;"></i>
            </span>
            <input type="text" id="searchTeknisi" class="form-control border-start-0 ps-0"
                   placeholder="Cari nama teknisi..." style="font-size: 0.82rem;">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom align-middle" id="tabelTeknisi">
            <thead>
                <tr>
                    <th style="width:48px;">NO</th>
                    <th>NAMA TEKNISI</th>
                    <th class="text-center">INSTALLED</th>
                    <th class="text-center">NOT INSTALLED</th>
                    <th class="text-center">RUSAK</th>
                    <th class="text-center">TOTAL DIBAWA</th>
                    <th style="min-width: 180px;">RASIO TERPASANG</th>
                </tr>
            </thead>
            <tbody id="tabelTeknisiBody">
                @forelse($rekapTeknisi as $index => $tek)
                @php
                    $rasio = $tek['total_dibawa'] > 0
                        ? round(($tek['installed'] / $tek['total_dibawa']) * 100)
                        : 0;
                    $barColor = $rasio >= 80 ? '#16a34a' : ($rasio >= 50 ? '#f59e0b' : ($rasio > 0 ? '#c0392b' : '#e5e7eb'));
                @endphp
                <tr class="teknisi-row">
                    <td class="text-muted" style="font-size: 0.82rem;">{{ $index + 1 }}</td>
                    <td>
                        <span class="fw-semibold" style="font-size: 0.875rem; color: #1a1d23;">{{ $tek['nama'] }}</span>
                    </td>
                    <td class="text-center fw-bold" style="color: #16a34a; font-size: 0.9rem;">
                        {{ $tek['installed'] }}
                    </td>
                    <td class="text-center fw-bold" style="color: #d97706; font-size: 0.9rem;">
                        {{ $tek['not_installed'] }}
                    </td>
                    <td class="text-center fw-bold" style="color: #c0392b; font-size: 0.9rem;">
                        {{ $tek['rusak'] }}
                    </td>
                    <td class="text-center fw-bold" style="color: #2563eb; font-size: 0.9rem;">
                        {{ $tek['total_dibawa'] }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="flex:1; height:8px; background:#f1f5f9; border-radius:99px; overflow:hidden;">
                                <div style="width: {{ $rasio }}%; height:100%; background:{{ $barColor }}; border-radius:99px; transition: width 0.5s ease;"></div>
                            </div>
                            <span style="font-size: 0.78rem; font-weight: 600; color: {{ $barColor }}; min-width: 36px; text-align: right;">
                                {{ $rasio }}%
                            </span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted" style="font-size: 0.85rem;">
                        <i class="bi bi-people fs-3 d-block mb-2 opacity-25"></i>
                        Belum ada data teknisi.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if(count($rekapTeknisi) > 0)
            @php
                $gtInstalled     = collect($rekapTeknisi)->sum('installed');
                $gtNotInstalled  = collect($rekapTeknisi)->sum('not_installed');
                $gtRusak         = collect($rekapTeknisi)->sum('rusak');
                $gtTotal         = collect($rekapTeknisi)->sum('total_dibawa');
                $gtRasio         = $gtTotal > 0 ? round(($gtInstalled / $gtTotal) * 100) : 0;
                $gtColor         = $gtRasio >= 80 ? '#16a34a' : ($gtRasio >= 50 ? '#f59e0b' : ($gtRasio > 0 ? '#c0392b' : '#e5e7eb'));
            @endphp
            <tfoot>
                <tr style="background: #f9fafb; border-top: 2px solid #e8eaed;">
                    <td colspan="2" style="font-size: 0.78rem; font-weight: 700; color: #374151; padding: 0.7rem 1rem;">
                        GRAND TOTAL ({{ count($rekapTeknisi) }} teknisi)
                    </td>
                    <td class="text-center fw-bold" style="color: #16a34a;">{{ $gtInstalled }}</td>
                    <td class="text-center fw-bold" style="color: #d97706;">{{ $gtNotInstalled }}</td>
                    <td class="text-center fw-bold" style="color: #c0392b;">{{ $gtRusak }}</td>
                    <td class="text-center fw-bold" style="color: #2563eb;">{{ $gtTotal }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="flex:1; height:8px; background:#f1f5f9; border-radius:99px; overflow:hidden;">
                                <div style="width: {{ $gtRasio }}%; height:100%; background:{{ $gtColor }}; border-radius:99px;"></div>
                            </div>
                            <span style="font-size: 0.78rem; font-weight: 700; color: {{ $gtColor }}; min-width: 36px; text-align: right;">
                                {{ $gtRasio }}%
                            </span>
                        </div>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- ── Catatan Definisi Kolom ───────────────────────────────────────────────── --}}
<div class="card-custom">
    <div class="p-3" style="border-bottom: 1px solid #e8eaed;">
        <h6 class="fw-bold mb-0" style="font-size: 0.9rem; color: #1a1d23;">
            <span style="color: #c0392b;">📌</span> Catatan Definisi Kolom
        </h6>
    </div>
    <div class="p-3">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-3 rounded-2" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                    <div class="mb-1">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #16a34a; letter-spacing: 0.04em;">INSTALLED</span>
                        <span style="font-size: 0.78rem; color: #374151; font-weight: 500;"> — Perangkat Terpasang</span>
                    </div>
                    <p class="mb-0" style="font-size: 0.78rem; color: #4b7a56; line-height: 1.5;">
                        Jumlah unit teknisi yang terdata pada laporan WO dengan status <strong>"Work Order Selesai"</strong>.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded-2" style="background: #fffbeb; border: 1px solid #fde68a;">
                    <div class="mb-1">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #d97706; letter-spacing: 0.04em;">NOT INSTALLED</span>
                        <span style="font-size: 0.78rem; color: #374151; font-weight: 500;"> — Unit di Lapangan</span>
                    </div>
                    <p class="mb-0" style="font-size: 0.78rem; color: #7c6320; line-height: 1.5;">
                        Selisih unit yang dibawa teknisi namun belum memiliki laporan WO Selesai (Total Dibawa – INSTALLED).
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded-2" style="background: #fef2f2; border: 1px solid #fecaca;">
                    <div class="mb-1">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #c0392b; letter-spacing: 0.04em;">RUSAK</span>
                        <span style="font-size: 0.78rem; color: #374151; font-weight: 500;"> — Unit Retur / Cacat</span>
                    </div>
                    <p class="mb-0" style="font-size: 0.78rem; color: #7f3030; line-height: 1.5;">
                        Jumlah unit milik teknisi yang ditandai kondisi <strong>"Rusak"</strong> pada tabel ONT Keluar.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded-2" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                    <div class="mb-1">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #2563eb; letter-spacing: 0.04em;">TOTAL</span>
                        <span style="font-size: 0.78rem; color: #374151; font-weight: 500;"> — Total Dibawa</span>
                    </div>
                    <p class="mb-0" style="font-size: 0.78rem; color: #2d4a80; line-height: 1.5;">
                        Total akumulasi fisik modem ONT yang pernah diserahkan kepada teknisi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // ── Chart ONT Tren ──────────────────────────────────────────────────────────
    const CHART_URL   = '{{ route("dashboard.chart-data") }}';
    let   ontChart    = null;
    let   currentMode = 'daily';

    const filterMonth = document.getElementById('filterMonth');
    const filterYear  = document.getElementById('filterYear');
    const modeButtons = document.querySelectorAll('.chart-mode-btn');

    function buildParams() {
        const p = new URLSearchParams({ mode: currentMode });
        if (currentMode === 'daily' || currentMode === 'weekly') {
            if (filterMonth.value) {
                p.set('month', filterMonth.value);
            }
        } else if (currentMode === 'monthly') {
            p.set('year', filterYear.value);
        }
        return p.toString();
    }

    const toggleMasuk  = document.getElementById('toggleMasuk');
    const toggleKeluar = document.getElementById('toggleKeluar');

    function renderChart(data) {
        const ctx = document.getElementById('ontTrendChart').getContext('2d');

        const totalMasuk  = data.masuk.reduce((a, b) => a + b, 0);
        const totalKeluar = data.keluar.reduce((a, b) => a + b, 0);
        document.getElementById('summaryMasuk').textContent  = totalMasuk.toLocaleString('id-ID');
        document.getElementById('summaryKeluar').textContent = totalKeluar.toLocaleString('id-ID');

        if (ontChart) ontChart.destroy();

        ontChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'ONT Masuk',
                        data: data.masuk,
                        backgroundColor: '#2563eb',
                        borderColor: '#2563eb',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                        maxBarThickness: 32,
                        hidden: !toggleMasuk.checked,
                    },
                    {
                        label: 'ONT Keluar',
                        data: data.keluar,
                        backgroundColor: '#c0392b',
                        borderColor: '#c0392b',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                        maxBarThickness: 32,
                        hidden: !toggleKeluar.checked,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: { duration: 400, easing: 'easeOutQuart' },
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: '600' },
                        bodyFont:  { family: 'Plus Jakarta Sans', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('id-ID')} unit`,
                        }
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            color: '#94a3b8',
                            maxRotation: 45,
                        },
                        border: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font:      { family: 'Plus Jakarta Sans', size: 11 },
                            color:     '#94a3b8',
                            precision: 0,
                        },
                        border: { display: false },
                    }
                }
            }
        });
    }

    function loadChart() {
        fetch(`${CHART_URL}?${buildParams()}`)
            .then(r => r.json())
            .then(data => renderChart(data))
            .catch(() => {});
    }

    // Toggle dataset visibility via checkboxes
    toggleMasuk.addEventListener('change', function () {
        if (ontChart) {
            ontChart.setDatasetVisibility(0, this.checked);
            ontChart.update();
        }
    });

    toggleKeluar.addEventListener('change', function () {
        if (ontChart) {
            ontChart.setDatasetVisibility(1, this.checked);
            ontChart.update();
        }
    });

    // Mode switch
    modeButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            modeButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentMode = this.dataset.mode;

            // Tampilkan filter sesuai mode
            if (currentMode === 'daily' || currentMode === 'weekly') {
                filterMonth.style.display = 'block';
                filterYear.style.display  = 'none';
            } else {
                filterMonth.style.display = 'none';
                filterYear.style.display  = 'block';
            }
            loadChart();
        });
    });

    filterMonth.addEventListener('change', loadChart);
    filterYear.addEventListener('change', loadChart);

    // Initial load
    loadChart();

    // ── Live search teknisi ─────────────────────────────────────────────────────
    const searchInput = document.getElementById('searchTeknisi');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#tabelTeknisiBody .teknisi-row').forEach(row => {
                const nama = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                row.style.display = nama.includes(q) ? '' : 'none';
            });
        });
    }

    // Auto dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            try { bootstrap.Alert.getOrCreateInstance(el).close(); } catch(e) {}
        });
    }, 4000);
</script>
@endpush
