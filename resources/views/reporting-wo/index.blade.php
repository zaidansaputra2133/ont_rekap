@extends('layouts.app')

@section('title', 'Reporting WO')

@section('content')
@php
    $hasFilter = $search || $status || $teknisi || $tanggal || $vendor || $match;

    $statusPill = function (?string $s) {
        $v = strtolower(trim((string) $s));
        if (str_contains($v, 'selesai')) return 'pill-green';
        if (str_contains($v, 'gagal') || str_contains($v, 'batal') || str_contains($v, 'cancel')) return 'pill-red';
        if ($v === '') return 'pill-gray';
        return 'pill-amber';
    };
@endphp

<div class="page">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Laporan Work Order Lapangan</h1>
            <p class="page-sub">Rekap status WO instalasi &amp; auto-match serial number</p>
        </div>
        <button type="button" class="btn btn-brand px-3 py-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bi bi-cloud-arrow-up me-1"></i> Upload Laporan WO
        </button>
    </div>

    {{-- Kartu info --}}
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="panel panel-pad h-100">
                <h2 class="fs-13 fw-semibold text-dark mb-3">Logika Status Otomatis</h2>
                <div class="d-flex flex-column gap-3">
                    <div class="info-card info-green d-block">
                        <b style="color: var(--green-dark);">INSTALLED</b>
                        <p style="color: var(--green-dark);">Serial Number terdaftar di laporan WO dengan status <strong>"Work Order Selesai"</strong>. Unit terpasang di pelanggan.</p>
                    </div>
                    <div class="info-card info-amber d-block">
                        <b>NOT INSTALLED</b>
                        <p style="color: #b45309;">Unit pernah diambil oleh teknisi namun belum memiliki laporan WO Selesai.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-pad h-100 d-flex flex-column">
                <h2 class="fs-13 fw-semibold text-dark mb-3">Template Spreadsheet WO</h2>
                <p class="fs-12 text-muted-2 mb-3" style="line-height:1.6;">Gunakan template berikut untuk mengimpor laporan WO. Pastikan header kolom sesuai persis.</p>
                <div class="tpl-box mb-3" style="display:block;">
                    <div class="fs-12 font-mono text-muted-2" style="line-height:1.7;">no_order | cid | serial_number | nama_teknisi | nik_teknisi | status_wo | tanggal_sa | vendor | sektor | cek_match</div>
                </div>
                <a href="{{ route('reporting-wo.template') }}" class="btn btn-soft-green mt-auto w-100">
                    <i class="bi bi-download me-1"></i> Unduh Template WO
                </a>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <form id="filterForm" method="GET" action="{{ route('reporting-wo.index') }}"></form>

    <div class="panel" id="tabelPanel">
        <div class="panel-head">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <h2 class="panel-title">Data Work Order</h2>
                <span class="pill pill-blue">Total Keluar: {{ number_format($totalKeluar, 0, ',', '.') }} unit</span>
            </div>
            <span class="fs-12 text-muted-2">{{ number_format($items->total(), 0, ',', '.') }} record</span>
        </div>

        <div class="table-responsive">
            <table class="table tbl">
                <thead>
                    <tr>
                        <th>No</th><th>No. Order</th><th>CID</th><th>Serial Number (SN)</th><th>Teknisi</th>
                        <th>Status WO</th><th>Tgl Selesai (SA)</th><th>Vendor / Sektor</th><th>Auto-Match</th><th>Aksi</th>
                    </tr>
                    <tr class="filter-row">
                        <td></td>
                        <td colspan="3">
                            <input type="text" name="search" value="{{ $search }}" form="filterForm" data-autosubmit
                                   class="form-control form-control-xs" placeholder="Cari No. Order / CID / SN..." style="min-width: 220px;" autocomplete="off">
                        </td>
                        <td><input type="text" name="teknisi" value="{{ $teknisi }}" form="filterForm" data-autosubmit class="form-control form-control-xs" placeholder="Cari..." style="min-width:100px;" autocomplete="off"></td>
                        <td>
                            <select name="status" form="filterForm" data-autosubmit class="form-select form-select-xs" style="min-width:120px;">
                                <option value="">Semua</option>
                                <option value="selesai" @selected($status === 'selesai')>Work Order Selesai</option>
                                <option value="belum_selesai" @selected($status === 'belum_selesai')>Belum Selesai</option>
                            </select>
                        </td>
                        <td><input type="date" name="tanggal" value="{{ $tanggal }}" form="filterForm" data-autosubmit class="form-control form-control-xs"></td>
                        <td><input type="text" name="vendor" value="{{ $vendor }}" form="filterForm" data-autosubmit class="form-control form-control-xs" placeholder="Cari..." style="min-width:90px;" autocomplete="off"></td>
                        <td>
                            <select name="match" form="filterForm" data-autosubmit class="form-select form-select-xs">
                                <option value="">Semua</option>
                                <option value="sesuai" @selected($match === 'sesuai')>Match</option>
                                <option value="beda" @selected($match === 'beda')>Tidak</option>
                            </select>
                        </td>
                        <td>@if($hasFilter)<a href="{{ route('reporting-wo.index') }}" class="link-reset">Reset</a>@endif</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $row)
                        <tr>
                            <td class="cell-no">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="cell-mono text-nowrap" style="color: #2563eb;">{{ $row->no_order }}</td>
                            <td class="cell-small font-mono text-nowrap">{{ $row->cid ?: '—' }}</td>
                            <td class="cell-mono">{{ $row->serial_number }}</td>
                            <td class="fw-medium" style="color: var(--ink-2);">{{ $row->nama_teknisi }}</td>
                            <td><span class="pill {{ $statusPill($row->status_wo) }}">{{ $row->status_wo ?: '—' }}</span></td>
                            <td class="cell-small text-nowrap">{{ $row->tanggal_sa?->format('d-m-Y') ?? '—' }}</td>
                            <td class="cell-small" style="color: #4b5563;">
                                {{ $row->vendor ?: '—' }}
                                @if($row->sektor)<div class="fs-11 text-muted-2">{{ $row->sektor }}</div>@endif
                            </td>
                            <td>
                                @if(strtoupper((string) $row->cek_match) === 'SESUAI')
                                    <span class="fs-12 fw-semibold tone-green text-nowrap"><i class="bi bi-check2"></i> Match</span>
                                @else
                                    <span class="fs-12 fw-semibold text-muted-2 text-nowrap">— Tidak</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-act btn-act-red js-delete-confirm"
                                        data-type="reporting-wo"
                                        data-action="{{ route('reporting-wo.destroy', $row) }}"
                                        data-no-order="{{ $row->no_order }}"
                                        data-cid="{{ $row->cid ?: '—' }}"
                                        data-teknisi="{{ $row->nama_teknisi }}"
                                        data-status="{{ $row->status_wo ?: '—' }}">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="empty" style="padding: 3rem 1.25rem;">
                                <i class="bi bi-clipboard2 d-block fs-1 mb-2"></i>
                                {{ $hasFilter ? 'Tidak ada data WO yang sesuai filter.' : 'Belum ada data WO. Upload laporan WO untuk memulai.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="panel-foot">
                <span>Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} record</span>
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal upload --}}
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 448px;">
        <form method="POST" action="{{ route('reporting-wo.import') }}" enctype="multipart/form-data" class="modal-content" id="uploadForm">
            @csrf
            <div class="modal-header">
                <h2 class="modal-title">Upload Laporan Work Order</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body d-flex flex-column gap-3">
                <label class="dropzone m-0" style="padding: 2.5rem 1rem;">
                    <i class="bi bi-clipboard2-data" style="font-size: 2.25rem;"></i>
                    <span class="dropzone-name" id="uploadName">Klik untuk pilih file WO</span>
                    <span class="fs-12 text-muted-2 mt-1">.xlsx / .xls / .csv (maks. 20 MB)</span>
                    <input type="file" name="file" id="uploadFile" accept=".xlsx,.xls,.csv" required>
                </label>
                @error('file')<div class="field-error m-0">{{ $message }}</div>@enderror

                <div class="info-card info-blue d-block fs-12" style="line-height:1.6;">
                    Kolom yang dibutuhkan: <span class="font-mono fw-semibold">no_order, serial_number, nama_teknisi, status_wo, tanggal_sa</span>.
                    Kolom opsional: <span class="font-mono">cid, nik_teknisi, vendor, sektor, cek_match</span>.
                    <div class="mt-1">No. Order yang sudah ada akan diperbarui. SN yang belum terdaftar di ONT Masuk dilewati.</div>
                </div>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-gray" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-brand" id="uploadBtn">Proses Import</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var file = document.getElementById('uploadFile'), name = document.getElementById('uploadName');
    file.addEventListener('change', function () { name.textContent = file.files[0] ? file.files[0].name : 'Klik untuk pilih file WO'; });
    document.getElementById('uploadForm').addEventListener('submit', function () {
        var b = document.getElementById('uploadBtn');
        b.disabled = true;
        b.innerHTML = '<span class="spin"></span>Memproses...';
    });
    @if($errors->has('file'))
        new bootstrap.Modal(document.getElementById('uploadModal')).show();
    @endif
})();
</script>
@endpush
