@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $tahunIni = now()->year;
    $bulanIni = now()->month;

    $cards = [
        ['label' => 'INSTALLED',     'value' => $totalInstalled,    'tone' => 'green'],
        ['label' => 'NOT INSTALLED', 'value' => $totalNotInstalled, 'tone' => 'amber'],
        ['label' => 'RUSAK',         'value' => $totalRusak,        'tone' => 'red'],
        ['label' => 'TOTAL',         'value' => $totalKeluar,       'tone' => 'blue'],
    ];

    $notes = [
        ['INSTALLED', 'var(--green)', 'Jumlah unit yang terdata pada laporan WO dengan status "Work Order Selesai".'],
        ['NOT INSTALLED', 'var(--amber)', 'Selisih unit yang dibawa teknisi namun belum memiliki laporan WO Selesai (Total − INSTALLED).'],
        ['RUSAK', 'var(--brand)', 'Jumlah unit milik teknisi yang ditandai kondisi "Rusak" pada tabel ONT Keluar.'],
        ['TOTAL', 'var(--blue)', 'Total akumulasi fisik modem ONT yang pernah diserahkan kepada teknisi.'],
    ];
@endphp

<div class="page">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-sub">Rekap stok &amp; performa instalasi lapangan</p>
    </div>

    {{-- KPI --}}
    <div class="row g-3">
        @foreach($cards as $c)
            <div class="col-6 col-lg-3">
                <div class="kpi kpi-{{ $c['tone'] }}">
                    <div class="kpi-top">
                        <span class="kpi-label">{{ $c['label'] }}</span>
                        <span class="kpi-dot"></span>
                    </div>
                    <div class="kpi-value">{{ number_format($c['value'], 0, ',', '.') }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Grafik --}}
    <div class="panel panel-pad">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-3">
            <h2 class="panel-title">Grafik ONT Masuk &amp; Keluar</h2>

            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="seg" id="periodTabs">
                    <button type="button" data-mode="daily" class="active">Harian</button>
                    <button type="button" data-mode="weekly">Mingguan</button>
                    <button type="button" data-mode="monthly">Bulanan</button>
                </div>

                <select id="yearSelect" class="form-select form-select-xs w-auto" aria-label="Tahun">
                    @for($y = $tahunIni - 2; $y <= $tahunIni + 2; $y++)
                        <option value="{{ $y }}" @selected($y === $tahunIni)>{{ $y }}</option>
                    @endfor
                </select>

                <select id="monthSelect" class="form-select form-select-xs w-auto" aria-label="Bulan">
                    @foreach($bulan as $i => $nama)
                        <option value="{{ $i + 1 }}" @selected($i + 1 === $bulanIni)>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="fs-12 text-muted-2 mb-2" id="chartCaption"></div>

        <div class="chart-box" id="chartBox"><canvas id="ontChart"></canvas></div>
        <div class="chart-empty" id="chartEmpty"></div>

        <div class="d-flex align-items-center flex-wrap gap-2 mt-3 pt-3 border-top">
            <span class="fs-12 text-muted-2 me-1">Tampilkan:</span>
            <button type="button" class="series-btn on-blue" id="toggleMasuk"><span class="sw"></span>ONT Masuk</button>
            <button type="button" class="series-btn on-red" id="toggleKeluar"><span class="sw"></span>ONT Keluar</button>
        </div>
    </div>

    {{-- Rekap teknisi --}}
    <div class="panel">
        <div class="panel-head">
            <h2 class="panel-title">Rekapitulasi Per Teknisi</h2>
            <input type="search" id="teknisiSearch" class="form-control form-control-sm" style="width: 14rem;" placeholder="Cari nama teknisi..." autocomplete="off">
        </div>
        <div class="table-responsive">
            <table class="table tbl" id="teknisiTable">
                <thead>
                    <tr>
                        <th>No</th><th>Nama Teknisi</th><th>Installed</th><th>Not Installed</th>
                        <th>Rusak</th><th>Total Dibawa</th><th>Rasio Terpasang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapTeknisi as $t)
                        @php $tone = $t['rasio'] >= 80 ? 'green' : ($t['rasio'] >= 50 ? 'amber' : 'red'); @endphp
                        <tr data-nama="{{ strtolower($t['nama']) }}">
                            <td class="cell-no"></td>
                            <td class="fw-medium text-dark">{{ $t['nama'] }}</td>
                            <td class="fw-semibold tone-green">{{ $t['installed'] }}</td>
                            <td class="fw-semibold tone-amber">{{ $t['not_installed'] }}</td>
                            <td class="fw-semibold tone-red">{{ $t['rusak'] }}</td>
                            <td class="fw-semibold" style="color: var(--blue);">{{ $t['total_dibawa'] }}</td>
                            <td style="width: 13rem;">
                                <div class="meter">
                                    <div class="meter-track"><div class="meter-fill bg-tone-{{ $tone }}" style="width: {{ $t['rasio'] }}%;"></div></div>
                                    <span class="meter-val tone-{{ $tone }}">{{ $t['rasio'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr id="teknisiEmpty" @if(count($rekapTeknisi) > 0) style="display:none" @endif>
                        <td colspan="7" class="empty">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Keterangan --}}
    <div class="panel px-4 py-3">
        <div class="fs-12 fw-semibold text-muted-2 mb-2"><i class="bi bi-pin-angle me-1"></i>Keterangan</div>
        <div class="d-flex flex-column gap-1">
            @foreach($notes as [$label, $color, $desc])
                <div class="note-row"><b style="color: {{ $color }};">{{ $label }}</b><span>— {{ $desc }}</span></div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    var CHART_URL = @json(route('dashboard.chart-data'));
    var BULAN = @json($bulan);

    var mode = 'daily';
    var yearSel = document.getElementById('yearSelect');
    var monthSel = document.getElementById('monthSelect');
    var caption = document.getElementById('chartCaption');
    var box = document.getElementById('chartBox');
    var empty = document.getElementById('chartEmpty');
    var tabs = document.querySelectorAll('#periodTabs button');
    var btnMasuk = document.getElementById('toggleMasuk');
    var btnKeluar = document.getElementById('toggleKeluar');
    var show = { masuk: true, keluar: true };

    var chart = new Chart(document.getElementById('ontChart'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [
                { label: 'ONT Masuk', data: [], backgroundColor: '#1d4ed8', borderRadius: 4, maxBarThickness: 28 },
                { label: 'ONT Keluar', data: [], backgroundColor: '#E31E24', borderRadius: 4, maxBarThickness: 28 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 250 },
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#fff', titleColor: '#374151', bodyColor: '#374151', borderColor: '#e5e7eb', borderWidth: 1, padding: 10 }
            },
            scales: {
                x: { grid: { display: false }, border: { display: false }, ticks: { color: '#9ca3af', autoSkip: false, maxRotation: 0, font: { size: 10 } } },
                y: { beginAtZero: true, border: { display: false }, grid: { color: '#f3f4f6' }, ticks: { color: '#9ca3af', precision: 0, font: { size: 11 } } }
            }
        }
    });

    function params() {
        var p = new URLSearchParams({ mode: mode });
        var y = yearSel.value;
        if (mode === 'monthly') {
            p.set('year', y);
        } else {
            p.set('month', y + '-' + String(monthSel.value).padStart(2, '0'));
        }
        return p.toString();
    }

    function updateCaption() {
        var m = BULAN[monthSel.value - 1], y = yearSel.value;
        caption.textContent =
            mode === 'daily' ? m + ' ' + y + ' — menampilkan semua tanggal dalam bulan' :
            mode === 'weekly' ? m + ' ' + y + ' — per minggu' :
            'Tahun ' + y + ' — semua 12 bulan ditampilkan';
    }

    function applySeries() {
        chart.setDatasetVisibility(0, show.masuk);
        chart.setDatasetVisibility(1, show.keluar);
        btnMasuk.classList.toggle('on-blue', show.masuk);
        btnKeluar.classList.toggle('on-red', show.keluar);
        chart.update();
        if (!show.masuk && !show.keluar) {
            box.style.display = 'none';
            empty.style.display = 'flex';
            empty.textContent = 'Aktifkan minimal satu seri untuk menampilkan grafik';
        } else if (empty.dataset.reason !== 'nodata') {
            box.style.display = '';
            empty.style.display = 'none';
        }
    }

    function load() {
        updateCaption();
        monthSel.style.display = mode === 'monthly' ? 'none' : '';
        fetch(CHART_URL + '?' + params(), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
            .then(function (d) {
                chart.data.labels = d.labels;
                chart.data.datasets[0].data = d.masuk;
                chart.data.datasets[1].data = d.keluar;
                var total = d.masuk.concat(d.keluar).reduce(function (a, b) { return a + b; }, 0);

                if (mode === 'weekly' && total === 0) {
                    empty.dataset.reason = 'nodata';
                    box.style.display = 'none';
                    empty.style.display = 'flex';
                    empty.textContent = 'Tidak ada data transaksi pada ' + BULAN[monthSel.value - 1] + ' ' + yearSel.value;
                } else {
                    empty.dataset.reason = '';
                    box.style.display = '';
                    empty.style.display = 'none';
                }
                applySeries();
            })
            .catch(function () {
                empty.dataset.reason = 'nodata';
                box.style.display = 'none';
                empty.style.display = 'flex';
                empty.textContent = 'Gagal memuat data grafik. Muat ulang halaman.';
            });
    }

    tabs.forEach(function (b) {
        b.addEventListener('click', function () {
            mode = b.dataset.mode;
            tabs.forEach(function (x) { x.classList.toggle('active', x === b); });
            load();
        });
    });
    yearSel.addEventListener('change', load);
    monthSel.addEventListener('change', load);
    btnMasuk.addEventListener('click', function () { show.masuk = !show.masuk; applySeries(); });
    btnKeluar.addEventListener('click', function () { show.keluar = !show.keluar; applySeries(); });

    // Pencarian teknisi (tanpa reload)
    var search = document.getElementById('teknisiSearch');
    var rows = document.querySelectorAll('#teknisiTable tbody tr[data-nama]');
    var emptyRow = document.getElementById('teknisiEmpty');
    function filterRows() {
        var q = search.value.trim().toLowerCase(), n = 0;
        rows.forEach(function (tr) {
            var ok = tr.dataset.nama.indexOf(q) !== -1;
            tr.style.display = ok ? '' : 'none';
            if (ok) { n++; tr.querySelector('.cell-no').textContent = n; }
        });
        emptyRow.style.display = n === 0 ? '' : 'none';
    }
    search.addEventListener('input', filterRows);
    filterRows();

    load();
})();
</script>
@endpush
