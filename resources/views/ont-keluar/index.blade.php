@extends('layouts.app')

@section('title', 'ONT Keluar')

@section('content')
<!-- Header Page Minimal -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">ONT Keluar</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Penyerahan ONT ke Teknisi</h4>
    </div>
    <div>
        <span class="badge bg-white border text-secondary px-3 py-1.5 rounded-2 small fw-normal">
            Total Keluar: <strong class="text-dark">{{ $totalKeluar }}</strong> Unit
        </span>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 rounded-2 mb-4 py-2 px-3" role="alert" style="font-size:0.85rem;">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 rounded-2 mb-4 py-2 px-3" role="alert" style="font-size:0.85rem;">
    <i class="bi bi-exclamation-circle me-1"></i>
    @foreach($errors->all() as $error)
        {{ $error }}<br>
    @endforeach
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Input Form & Real Handover Protocol -->
<div class="row g-4 mb-4">
    <!-- Form Penyerahan ke Teknisi -->
    <div class="col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom p-3">
                <h6 class="fw-bold mb-0 text-dark">Form Serah Terima Unit</h6>
            </div>
            <div class="card-body p-3.5">
                <form action="{{ route('ont-keluar.store') }}" method="POST" id="formOntKeluar" autocomplete="off">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nama_teknisi" class="form-label">Nama Teknisi <span class="text-muted small">*</span></label>
                            <input type="text"
                                list="daftar_teknisi_list"
                                autocomplete="off"
                                class="form-control @error('nama_teknisi') is-invalid @enderror"
                                id="nama_teknisi" name="nama_teknisi"
                                placeholder="Nama personil teknisi"
                                value="{{ old('nama_teknisi') }}"
                                required autofocus>
                            <datalist id="daftar_teknisi_list">
                                @foreach($daftarTeknisi as $nama)
                                    <option value="{{ $nama }}">
                                @endforeach
                            </datalist>
                            @error('nama_teknisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="tanggal_keluar" class="form-label">Tanggal Penyerahan <span class="text-muted small">*</span></label>
                            <input type="date"
                                class="form-control @error('tanggal_keluar') is-invalid @enderror"
                                id="tanggal_keluar" name="tanggal_keluar"
                                value="{{ old('tanggal_keluar', date('Y-m-d')) }}"
                                required>
                            @error('tanggal_keluar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Hidden Input untuk Serial Number dari Checkbox --}}
                        <input type="hidden" id="serial_number" name="serial_number" value="{{ old('serial_number') }}">

                        @error('serial_number')
                        <div class="col-12">
                            <div class="alert alert-danger py-2 px-3 mb-0 rounded-2 d-flex align-items-center gap-2" style="font-size: 0.8rem;">
                                <i class="bi bi-exclamation-circle-fill fs-6 flex-shrink-0"></i>
                                <span>{{ $message }}</span>
                            </div>
                        </div>
                        @enderror

                        @if($availableOnts->count() > 0)
                        <div class="col-12">
                            <div class="card border rounded-3 bg-white shadow-2xs overflow-hidden">
                                <!-- Card Header -->
                                <div class="card-header bg-light-subtle p-3 border-bottom">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-box-seam text-danger fs-6"></i>
                                            <span class="fw-bold text-dark small">Pilih Unit ONT dari Stok Gudang</span>
                                        </div>
                                        <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1 rounded-2" style="font-size:0.72rem;">
                                            Tersedia: <strong>{{ $availableOnts->count() }}</strong> Unit
                                        </span>
                                    </div>
                                </div>

                                <!-- Filter Controls (Filter Merek & Cari SN) -->
                                <div class="p-3 bg-white border-bottom">
                                    <div class="row g-2.5 align-items-center">
                                        <div class="col-sm-6 col-12">
                                            <label for="filter_brand_select" class="form-label mb-1 text-secondary fw-medium" style="font-size: 0.74rem;">Filter Merek:</label>
                                            <select id="filter_brand_select" class="form-select form-select-sm py-1.5 rounded-2" style="font-size: 0.8rem;">
                                                <option value="">-- Semua Merek ({{ $availableOnts->count() }}) --</option>
                                                @foreach($availableBrands as $bName)
                                                    @php
                                                        $bCount = $availableOnts->where('brand', $bName)->count();
                                                    @endphp
                                                    <option value="{{ $bName }}">{{ $bName }} ({{ $bCount }} unit)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-6 col-12">
                                            <label for="search_sn_input" class="form-label mb-1 text-secondary fw-medium" style="font-size: 0.74rem;">Cari SN:</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light text-muted px-2.5 border-end-0"><i class="bi bi-search" style="font-size: 0.75rem;"></i></span>
                                                <input type="text" id="search_sn_input" class="form-control font-monospace py-1.5 px-2.5 border-start-0" placeholder="Ketik SN..." style="font-size: 0.8rem;">
                                                <button type="button" class="btn btn-outline-secondary btn-sm px-2" id="btn_clear_sn_search" title="Reset cari" style="font-size:0.75rem;"><i class="bi bi-x-lg"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Scrollable Checkbox List Area (1 Column Full Width per item) -->
                                <div class="p-3" style="max-height: 250px; overflow-y: auto; overflow-x: hidden; background-color: #fafafa;" id="ont_checkbox_container">
                                    <div class="d-flex flex-column gap-2" id="ont_checkbox_grid">
                                        @foreach($availableOnts as $ont)
                                        <div class="ont-item-wrapper w-100" data-brand="{{ $ont->brand }}" data-sn="{{ strtoupper($ont->serial_number) }}">
                                            <label class="d-flex align-items-center gap-2.5 p-2 px-3 rounded-2 border bg-white cursor-pointer w-100 mb-0 ont-item-label transition-all" style="font-size: 0.8rem;">
                                                <input type="checkbox" class="form-check-input flex-shrink-0 chk-ont-item my-0" value="{{ $ont->serial_number }}" data-brand="{{ $ont->brand }}" style="width: 1.05rem; height: 1.05rem; cursor: pointer;">
                                                <span class="font-monospace fw-semibold text-dark text-truncate me-auto" title="{{ $ont->serial_number }}">{{ $ont->serial_number }}</span>
                                                <span class="badge bg-secondary-subtle text-secondary px-2 py-0.5 rounded-1 fw-normal flex-shrink-0" style="font-size:0.7rem;">{{ $ont->brand }}</span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div id="no_ont_found_msg" class="text-center text-muted py-3 d-none" style="font-size:0.78rem;">
                                        <i class="bi bi-exclamation-circle me-1"></i> Tidak ada unit yang cocok dengan filter.
                                    </div>
                                </div>

                                <!-- Minimalist & Spacious Pagination Bar -->
                                <div class="d-flex flex-wrap align-items-center justify-content-between p-2.5 px-3 bg-light-subtle border-top border-bottom" id="ont_pagination_bar" style="font-size: 0.78rem;">
                                    <span class="text-muted fw-medium py-1" id="ont_page_info">Menampilkan 0 dari 0 unit</span>
                                    <div class="d-flex align-items-center gap-2.5 py-1" id="ont_pagination_controls">
                                        <button type="button" class="btn btn-sm btn-outline-secondary border-secondary-subtle px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1 transition-all" id="btn_ont_prev_page" style="font-size:0.75rem;" disabled>
                                            <i class="bi bi-arrow-left" style="font-size: 0.75rem;"></i> <span>Prev</span>
                                        </button>
                                        <span class="badge bg-white text-dark border shadow-2xs px-2.5 py-1.5 rounded-pill font-monospace fw-bold" id="ont_page_indicator" style="font-size:0.75rem; min-width: 48px; text-align: center;">1 / 1</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary border-secondary-subtle px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1 transition-all" id="btn_ont_next_page" style="font-size:0.75rem;" disabled>
                                            <span>Next</span> <i class="bi bi-arrow-right" style="font-size: 0.75rem;"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Selected Summary / Pills Container -->
                                <div class="p-3 bg-light border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                                        <span class="fw-semibold text-dark" style="font-size: 0.78rem;">
                                            <i class="bi bi-check2-square text-success me-1 fs-6"></i>Unit Terpilih (<strong id="selected_total_count">0</strong>):
                                        </span>
                                        <button type="button" class="btn btn-link text-danger p-0 border-0 text-decoration-none fw-medium" id="btn_clear_all_selected" style="font-size:0.72rem; display:none;">
                                            <i class="bi bi-trash me-0.5"></i> Reset Pilihan
                                        </button>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1.5 align-items-center" id="selected_pills_container" style="max-height: 80px; overflow-y: auto;">
                                        <span class="text-muted fst-italic" id="empty_selected_hint" style="font-size: 0.74rem;">Belum ada unit yang dicentang.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="col-12">
                            <div class="alert alert-warning py-2.5 px-3 mb-0 rounded-2" style="font-size: 0.82rem;">
                                <i class="bi bi-exclamation-triangle me-1"></i> Stok ONT di gudang kosong. Harap tambahkan unit pada menu <strong>ONT Masuk</strong>.
                            </div>
                        </div>
                        @endif

                        <div class="col-12 mt-1">
                            <button type="submit" class="btn btn-custom-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2" id="btnSubmitOntKeluar">
                                <i class="bi bi-check2-all fs-6"></i> Catat Penyerahan Barang
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Prosedur Serah Terima & Retur Unit Lapangan -->
    <div class="col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom p-3">
                <h6 class="fw-bold mb-0 text-dark">Prosedur Serah Terima & Retur</h6>
            </div>
            <div class="card-body p-3.5">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-3 align-items-start">
                        <span class="badge badge-soft-success rounded-1 px-2 py-0.5" style="font-size: 0.72rem; min-width: 54px; text-align: center;">Normal</span>
                        <div>
                            <span class="d-block fw-semibold text-dark small">Kondisi Penyerahan Standar</span>
                            <span class="text-muted d-block" style="font-size: 0.78rem; line-height: 1.4;">
                                Barang yang diserahkan otomatis tercatat normal. Teknisi bertanggung jawab mengamankan perangkat selama instalasi lapangan.
                            </span>
                        </div>
                    </div>

                    <div class="d-flex gap-3 align-items-start">
                        <span class="badge badge-theme-red rounded-1 px-2 py-0.5" style="font-size: 0.72rem; min-width: 54px; text-align: center;">Rusak</span>
                        <div>
                            <span class="d-block fw-semibold text-dark small">Pelaporan Unit Cacat / Retur</span>
                            <span class="text-muted d-block" style="font-size: 0.78rem; line-height: 1.4;">
                                Jika unit mengalami gangguan di pelanggan (petir, port mati, adaptor loss), klik <strong>"Ubah Kondisi"</strong> pada baris transaksi dan catat kendalanya sebagai arsip klaim garansi vendor.
                            </span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 mt-3 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 0.78rem;">
                    <span>Periksa data fisik gudang?</span>
                    <a href="{{ route('ont-masuk.index') }}" class="fw-medium text-decoration-none" style="color: var(--theme-red);">Buka ONT Masuk &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Section: List Transaksi ONT Keluar -->
<div class="card card-custom" id="tabel-rekap-keluar">
    <div class="card-header bg-white border-bottom p-3">
        <div class="row g-2 align-items-center">
            <div class="col-xl-4 col-lg-3 col-md-12">
                <h6 class="fw-bold mb-0 text-dark">Daftar Transaksi Keluar</h6>
            </div>
            <div class="col-xl-8 col-lg-9 col-md-12">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                    <!-- Quick Status Pills -->
                    <div class="btn-group btn-group-sm status-pills-group" role="group" aria-label="Filter Kondisi">
                        <button type="button" class="btn btn-status-pill {{ empty($status) ? 'active' : '' }}" data-status="">Semua</button>
                        <button type="button" class="btn btn-status-pill {{ ($status ?? '') == 'normal' ? 'active' : '' }}" data-status="normal">Normal</button>
                        <button type="button" class="btn btn-status-pill {{ ($status ?? '') == 'rusak' ? 'active' : '' }}" data-status="rusak">Rusak</button>
                    </div>

                    <!-- Dropdown Status (Synced) -->
                    <select id="filterStatusSelect" class="form-select form-select-sm d-none d-sm-block" style="max-width: 125px;">
                        <option value="" {{ empty($status) ? 'selected' : '' }}>Semua Kondisi</option>
                        <option value="normal" {{ ($status ?? '') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="rusak" {{ ($status ?? '') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>

                    <form action="{{ route('ont-keluar.index') }}" method="GET" id="formFilterKeluar" class="d-flex gap-1 align-items-center m-0">
                        <input type="hidden" name="status" id="filterStatusInput" value="{{ $status ?? '' }}">
                        <div class="input-group input-group-sm" style="max-width: 200px;">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" id="filterSearchInput" class="form-control border-start-0" placeholder="Cari SN atau teknisi..." value="{{ $search ?? '' }}">
                        </div>
                        @if($search || $status)
                            <button type="button" id="btnResetFilterKeluar" class="btn btn-light btn-sm text-muted px-2 border" title="Reset Filter">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Serial Number (SN)</th>
                    <th>Teknisi</th>
                    <th>Tgl Keluar</th>
                    <th>Kondisi</th>
                    <th>Catatan Kerusakan</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $row)
                <tr>
                    <td class="text-center text-muted small">{{ $items->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="font-monospace fw-medium text-dark">{{ $row->serial_number }}</span>
                            <button class="btn btn-sm btn-link text-muted p-0 ms-1" title="Salin SN"
                                onclick="navigator.clipboard.writeText('{{ $row->serial_number }}'); this.innerHTML='<i class=\'bi bi-clipboard-check\' style=\'font-size:0.75rem;\'></i>'">
                                <i class="bi bi-clipboard" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                    </td>
                    <td>
                        <span class="fw-medium text-dark small">{{ $row->nama_teknisi }}</span>
                    </td>
                    <td>
                        <span class="text-dark small">{{ $row->tanggal_keluar->format('d M Y') }}</span>
                    </td>
                    <td>
                        @if($row->keterangan === 'Rusak')
                            <span class="badge badge-theme-red px-2 py-0.5 rounded-1 small">Rusak</span>
                        @else
                            <span class="badge badge-soft-success px-2 py-0.5 rounded-1 small">Normal</span>
                        @endif
                    </td>
                    <td>
                        @if($row->catatan)
                            <span class="text-muted small">"{{ Str::limit($row->catatan, 50) }}"</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <button type="button"
                                class="btn btn-sm btn-outline-theme py-1 px-2 rounded-2"
                                style="font-size: 0.78rem;"
                                onclick="openEditModal(
                                    '{{ $row->id }}',
                                    '{{ $row->serial_number }}',
                                    '{{ $row->nama_teknisi }}',
                                    '{{ $row->keterangan ?? '' }}',
                                    '{{ addslashes($row->catatan ?? '') }}'
                                )">
                                Ubah Kondisi
                            </button>
                            <form action="{{ route('ont-keluar.destroy', $row->id) }}" method="POST"
                                onsubmit="return confirm('Hapus transaksi SN {{ $row->serial_number }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0 ms-1" title="Hapus">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted small">
                        <i class="bi bi-inbox fs-4 d-block mb-2 text-muted opacity-50"></i>
                        Belum ada data penyerahan teknisi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="card-footer bg-white border-top p-2.5 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <span class="text-muted small" style="font-size: 0.78rem;">
            @if($items->total() > 0)
                Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} data
            @else
                Tidak ada data ditemukan
            @endif
        </span>
        <nav aria-label="Page navigation">
            {{ $items->links('pagination::bootstrap-5') }}
        </nav>
    </div>
</div>

<!-- Modal Update Status & Catatan Kerusakan -->
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" aria-labelledby="modalUpdateStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm rounded-3">
            <div class="modal-header bg-white border-bottom px-3 py-2.5">
                <h6 class="modal-title fw-bold text-dark" id="modalUpdateStatusLabel">
                    Ubah Status Kondisi ONT
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ont-keluar.update-status') }}" method="POST" id="formUpdateStatus">
                @csrf
                <input type="hidden" id="modal_id" name="id">

                <div class="modal-body p-3">
                    <!-- Unit Info Summary -->
                    <div class="p-2.5 rounded-2 mb-3 border small" style="background-color: #fafbfc;">
                        <div class="row g-2">
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.74rem;">Serial Number:</span>
                                <span class="font-monospace fw-medium text-dark" id="modal_sn">-</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.74rem;">Teknisi:</span>
                                <span class="fw-medium text-dark" id="modal_teknisi">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Selection -->
                    <div class="mb-3">
                        <label for="modal_keterangan" class="form-label">Kondisi Perangkat <span class="text-muted small">*</span></label>
                        <select class="form-select" id="modal_keterangan" name="keterangan" onchange="toggleCatatanInput(this.value)">
                            <option value="">Normal (Tidak Rusak / Kosong)</option>
                            <option value="Rusak">Rusak (Perangkat Bermasalah)</option>
                        </select>
                    </div>

                    <!-- Catatan Kerusakan -->
                    <div class="mb-2" id="wrapperCatatan">
                        <label for="modal_catatan" class="form-label">Catatan Kerusakan</label>
                        <textarea class="form-control" id="modal_catatan" name="catatan" rows="3" placeholder="Rincian kendala kerusakan..."></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top px-3 py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-custom-primary btn-sm rounded-2">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-status-pill {
        font-size: 0.78rem;
        font-weight: 500;
        padding: 0.28rem 0.68rem;
        border-color: #dee2e6;
        color: #475569;
        background-color: #fff;
        transition: all 0.15s ease-in-out;
    }
    .btn-status-pill:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .btn-status-pill.active {
        background-color: var(--theme-red, #b91c1c) !important;
        border-color: var(--theme-red, #b91c1c) !important;
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(185, 28, 28, 0.25);
    }
    .table-loading-fade {
        opacity: 0.45;
        pointer-events: none;
        transition: opacity 0.15s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    function openEditModal(id, sn, teknisi, keterangan, catatan) {
        document.getElementById('modal_id').value = id;
        document.getElementById('modal_sn').textContent = sn;
        document.getElementById('modal_teknisi').textContent = teknisi;
        document.getElementById('modal_keterangan').value = keterangan;
        document.getElementById('modal_catatan').value = catatan;

        toggleCatatanInput(keterangan);

        const modal = new bootstrap.Modal(document.getElementById('modalUpdateStatus'));
        modal.show();
    }

    function toggleCatatanInput(keterangan) {
        const wrapper = document.getElementById('wrapperCatatan');
        if (keterangan === 'Rusak') {
            wrapper.classList.remove('opacity-50');
            document.getElementById('modal_catatan').focus();
        } else {
            wrapper.classList.add('opacity-50');
        }
    }

    // Auto-dismiss flash alerts setelah 4 detik
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 4000);

    // ==============================================================
    // AJAX FILTERING ONT KELUAR (LANGSUNG FILTER & HALAMAN TIDAK NAIK)
    // ==============================================================
    function initTabelKeluarFilter() {
        const tableCard = document.getElementById('tabel-rekap-keluar');
        if (!tableCard) return;

        // Klik Pill Kondisi (Normal, Rusak, Semua) -> langsung filter
        const pills = tableCard.querySelectorAll('.btn-status-pill');
        pills.forEach(pill => {
            pill.addEventListener('click', function (e) {
                e.preventDefault();
                const status = this.getAttribute('data-status') || '';
                const search = document.getElementById('filterSearchInput')?.value || '';
                applyKeluarFilter({ status: status, search: search });
            });
        });

        // Pilihan Dropdown Kondisi -> langsung filter saat diganti
        const statusSelect = document.getElementById('filterStatusSelect');
        if (statusSelect) {
            statusSelect.addEventListener('change', function () {
                const status = this.value;
                const search = document.getElementById('filterSearchInput')?.value || '';
                applyKeluarFilter({ status: status, search: search });
            });
        }

        // Form Submit Search (Enter pada input pencarian)
        const formFilter = document.getElementById('formFilterKeluar');
        if (formFilter) {
            formFilter.addEventListener('submit', function (e) {
                e.preventDefault();
                const status = document.getElementById('filterStatusInput')?.value || 
                               document.getElementById('filterStatusSelect')?.value || '';
                const search = document.getElementById('filterSearchInput')?.value || '';
                applyKeluarFilter({ status: status, search: search });
            });
        }

        // Tombol Reset Filter
        const resetBtn = document.getElementById('btnResetFilterKeluar');
        if (resetBtn) {
            resetBtn.addEventListener('click', function (e) {
                e.preventDefault();
                applyKeluarFilter({ status: '', search: '' });
            });
        }

        // Intercept Pagination Links agar halaman tidak reload & tidak loncat ke atas
        const paginationLinks = tableCard.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                if (this.href) {
                    applyKeluarFilterFromUrl(this.href);
                }
            });
        });
    }

    function applyKeluarFilter(params) {
        const baseUrl = "{{ route('ont-keluar.index') }}";
        const url = new URL(baseUrl, window.location.origin);

        if (params.status) {
            url.searchParams.set('status', params.status);
        }
        if (params.search) {
            url.searchParams.set('search', params.search);
        }

        applyKeluarFilterFromUrl(url.toString());
    }

    function applyKeluarFilterFromUrl(fullUrl) {
        const tableCard = document.getElementById('tabel-rekap-keluar');
        if (!tableCard) {
            window.location.href = fullUrl;
            return;
        }

        // Simpan posisi scroll sebelum AJAX agar halaman TIDAK LOMPAT KE ATAS
        const currentScrollY = window.scrollY;

        const tableContent = tableCard.querySelector('.table-responsive') || tableCard;
        tableContent.classList.add('table-loading-fade');

        fetch(fullUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newCard = doc.getElementById('tabel-rekap-keluar');

            if (newCard) {
                tableCard.innerHTML = newCard.innerHTML;
                window.history.pushState(null, '', fullUrl);

                // Pertahankan posisi scroll user tanpa berpindah
                window.scrollTo({ top: currentScrollY, behavior: 'instant' });

                // Re-bind event listener pada elemen baru
                initTabelKeluarFilter();
            } else {
                window.location.href = fullUrl;
            }
        })
        .catch(err => {
            console.error('Filter error:', err);
            window.location.href = fullUrl;
        })
        .finally(() => {
            tableContent.classList.remove('table-loading-fade');
        });
    }

    // Tangani tombol browser back / forward
    window.addEventListener('popstate', function () {
        applyKeluarFilterFromUrl(window.location.href);
    });

    // Inisialisasi saat load pertama kali
    document.addEventListener('DOMContentLoaded', function () {
        initTabelKeluarFilter();

        const snInput = document.getElementById('serial_number');
        const snCounter = document.getElementById('sn_counter');
        const filterBrandSelect = document.getElementById('filter_brand_select');
        const searchSnInput = document.getElementById('search_sn_input');
        const btnClearSearch = document.getElementById('btn_clear_sn_search');
        const chkItems = document.querySelectorAll('.chk-ont-item');
        const selectedPillsContainer = document.getElementById('selected_pills_container');
        const emptySelectedHint = document.getElementById('empty_selected_hint');
        const selectedTotalCount = document.getElementById('selected_total_count');
        const btnClearAllSelected = document.getElementById('btn_clear_all_selected');
        const noOntFoundMsg = document.getElementById('no_ont_found_msg');

        // Set penampung SN terpilih
        let selectedSns = new Set();
        // Map untuk menyimpan brand tiap SN
        const snBrandMap = {};
        chkItems.forEach(chk => {
            const sn = chk.value.toUpperCase();
            const brand = chk.getAttribute('data-brand') || 'ONT';
            snBrandMap[sn] = brand;
        });

        // 1. Fungsi sinkronisasi dari Set ke Hidden Input & Pills & Checkbox States
        function updateUIFromSelection() {
            const currentList = Array.from(selectedSns);

            if (selectedTotalCount) selectedTotalCount.textContent = currentList.length;
            if (snCounter) snCounter.textContent = currentList.length + ' Unit';

            // Sync Hidden Input
            if (snInput) {
                snInput.value = currentList.join('\n');
            }

            // Sync Checkbox States
            chkItems.forEach(chk => {
                const sn = chk.value.toUpperCase();
                const isChecked = selectedSns.has(sn);
                chk.checked = isChecked;
                const label = chk.closest('.ont-item-label');
                if (label) {
                    if (isChecked) {
                        label.classList.add('border-danger', 'bg-danger-subtle', 'shadow-2xs');
                    } else {
                        label.classList.remove('border-danger', 'bg-danger-subtle', 'shadow-2xs');
                    }
                }
            });

            // Render Pills Ringkasan
            if (selectedPillsContainer) {
                selectedPillsContainer.innerHTML = '';
                if (currentList.length === 0) {
                    if (emptySelectedHint) emptySelectedHint.style.display = 'inline';
                    if (btnClearAllSelected) btnClearAllSelected.style.display = 'none';
                } else {
                    if (emptySelectedHint) emptySelectedHint.style.display = 'none';
                    if (btnClearAllSelected) btnClearAllSelected.style.display = 'inline';

                    currentList.forEach(sn => {
                        const brand = snBrandMap[sn] || 'ONT';
                        const pill = document.createElement('span');
                        pill.className = 'badge bg-white text-dark border shadow-2xs d-inline-flex align-items-center gap-1.5 font-monospace px-2.5 py-1.5 rounded-2';
                        pill.style.fontSize = '0.75rem';
                        pill.innerHTML = `<span class="badge bg-secondary-subtle text-secondary py-0.5 px-1.5 me-0.5 rounded-1" style="font-size:0.68rem;">${brand}</span>${sn} <button type="button" class="btn-close ms-1 btn-remove-pill" data-sn="${sn}" style="font-size:0.58rem;" aria-label="Remove"></button>`;
                        selectedPillsContainer.appendChild(pill);
                    });
                }
            }
        }

        // 2. Event: Checkbox Clicked
        chkItems.forEach(chk => {
            chk.addEventListener('change', function () {
                const sn = this.value.toUpperCase();
                if (this.checked) {
                    selectedSns.add(sn);
                } else {
                    selectedSns.delete(sn);
                }
                updateUIFromSelection();
            });
        });

        // 3. Inisialisasi awal jika ada value lama (misal dari old input)
        if (snInput && snInput.value.trim().length > 0) {
            const items = snInput.value.split(/[\r\n,;\s]+/).map(s => s.trim().toUpperCase()).filter(s => s.length > 0);
            selectedSns = new Set(items);
            updateUIFromSelection();
        }

        // 4. Event: Klik tombol hapus (x) di Pill Ringkasan
        if (selectedPillsContainer) {
            selectedPillsContainer.addEventListener('click', function (e) {
                const removeBtn = e.target.closest('.btn-remove-pill');
                if (removeBtn) {
                    const sn = removeBtn.getAttribute('data-sn');
                    if (sn) {
                        selectedSns.delete(sn);
                        updateUIFromSelection();
                    }
                }
            });
        }

        // 5. Event: Reset Semua Pilihan
        if (btnClearAllSelected) {
            btnClearAllSelected.addEventListener('click', function () {
                selectedSns.clear();
                updateUIFromSelection();
            });
        }

        // Form Submit Validation (Harap pilih minimal 1 checkbox)
        const formOntKeluar = document.getElementById('formOntKeluar');
        if (formOntKeluar) {
            formOntKeluar.addEventListener('submit', function (e) {
                if (selectedSns.size === 0) {
                    e.preventDefault();
                    alert('Harap pilih setidaknya 1 unit ONT dengan mencentang checkbox.');
                }
            });
        }

        // 6. Pagination Client-Side (Maksimal 5 ONT per Halaman)
        let currentOntPage = 1;
        const ontPageSize = 5;
        let matchingOntWrappers = [];

        function renderOntPagination() {
            const selectedBrand = filterBrandSelect ? filterBrandSelect.value : '';
            const searchKeyword = searchSnInput ? searchSnInput.value.trim().toUpperCase() : '';

            matchingOntWrappers = [];
            const allWrappers = document.querySelectorAll('.ont-item-wrapper');
            allWrappers.forEach(wrapper => {
                const itemBrand = wrapper.getAttribute('data-brand') || '';
                const itemSn = wrapper.getAttribute('data-sn') || '';

                const matchesBrand = !selectedBrand || itemBrand === selectedBrand;
                const matchesSearch = !searchKeyword || itemSn.includes(searchKeyword);

                if (matchesBrand && matchesSearch) {
                    matchingOntWrappers.push(wrapper);
                } else {
                    wrapper.classList.add('d-none');
                }
            });

            const totalMatching = matchingOntWrappers.length;
            const totalPages = Math.max(1, Math.ceil(totalMatching / ontPageSize));

            if (currentOntPage > totalPages) {
                currentOntPage = 1;
            }

            const startIndex = (currentOntPage - 1) * ontPageSize;
            const endIndex = Math.min(startIndex + ontPageSize, totalMatching);

            matchingOntWrappers.forEach((wrapper, idx) => {
                if (idx >= startIndex && idx < endIndex) {
                    wrapper.classList.remove('d-none');
                } else {
                    wrapper.classList.add('d-none');
                }
            });

            if (noOntFoundMsg) {
                if (totalMatching === 0 && allWrappers.length > 0) {
                    noOntFoundMsg.classList.remove('d-none');
                } else {
                    noOntFoundMsg.classList.add('d-none');
                }
            }

            const pageInfo = document.getElementById('ont_page_info');
            const pageIndicator = document.getElementById('ont_page_indicator');
            const btnPrev = document.getElementById('btn_ont_prev_page');
            const btnNext = document.getElementById('btn_ont_next_page');

            if (pageInfo) {
                if (totalMatching === 0) {
                    pageInfo.textContent = 'Tidak ada unit';
                } else {
                    pageInfo.textContent = `Menampilkan ${startIndex + 1}-${endIndex} dari ${totalMatching} unit`;
                }
            }

            if (pageIndicator) {
                pageIndicator.textContent = `${currentOntPage} / ${totalPages}`;
            }

            if (btnPrev) {
                btnPrev.disabled = (currentOntPage <= 1);
            }

            if (btnNext) {
                btnNext.disabled = (currentOntPage >= totalPages);
            }
        }

        const btnPrevPage = document.getElementById('btn_ont_prev_page');
        const btnNextPage = document.getElementById('btn_ont_next_page');

        if (btnPrevPage) {
            btnPrevPage.addEventListener('click', function () {
                if (currentOntPage > 1) {
                    currentOntPage--;
                    renderOntPagination();
                }
            });
        }

        if (btnNextPage) {
            btnNextPage.addEventListener('click', function () {
                const totalPages = Math.ceil(matchingOntWrappers.length / ontPageSize);
                if (currentOntPage < totalPages) {
                    currentOntPage++;
                    renderOntPagination();
                }
            });
        }

        if (filterBrandSelect) {
            filterBrandSelect.addEventListener('change', function () {
                currentOntPage = 1;
                renderOntPagination();
            });
        }

        if (searchSnInput) {
            searchSnInput.addEventListener('input', function () {
                currentOntPage = 1;
                renderOntPagination();
            });
        }

        if (btnClearSearch) {
            btnClearSearch.addEventListener('click', function () {
                if (searchSnInput) {
                    searchSnInput.value = '';
                    currentOntPage = 1;
                    renderOntPagination();
                }
            });
        }

        // Inisialisasi awal pagination
        renderOntPagination();

        // Jika halaman dibuka langsung via URL berparameter filter / pagination, scroll ke tabel
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('status') || urlParams.has('search') || urlParams.has('page') || window.location.hash === '#tabel-rekap-keluar') {
            const tableElement = document.getElementById('tabel-rekap-keluar');
            if (tableElement) {
                tableElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
</script>
@endpush
