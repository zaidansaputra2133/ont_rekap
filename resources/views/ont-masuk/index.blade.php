@extends('layouts.app')

@section('title', 'ONT Masuk')

@section('content')
<!-- Header Page Minimal -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">ONT Masuk</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Inventaris ONT Masuk</h4>
    </div>
    <div>
        <span class="badge bg-white border text-secondary px-3 py-1.5 rounded-2 small fw-normal">
            Total Gudang: <strong class="text-dark">{{ $totalCount ?? 142 }}</strong> Unit
        </span>
    </div>
</div>

<!-- Forms Section (Manual & Bulk Import) -->
<div class="row g-4 mb-4">
    <!-- Card Input: Tabs Manual vs Import Excel -->
    <div class="col-lg-5">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom p-2.5">
                <ul class="nav nav-pills nav-fill" id="ontMasukTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-1.5 rounded-2 small" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual-pane" type="button" role="tab">
                            <i class="bi bi-pencil me-1"></i> Input Manual
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-1.5 rounded-2 small" id="excel-tab" data-bs-toggle="tab" data-bs-target="#excel-pane" type="button" role="tab">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Import Spreadsheet
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-3.5">
                <div class="tab-content" id="ontMasukTabContent">
                    <!-- Tab 1: Input Manual Single Item -->
                    <div class="tab-pane fade show active" id="manual-pane" role="tabpanel">
                        <form action="{{ url('/ont-masuk') }}" method="POST" id="formManualInput">
                            @csrf
                            <div class="mb-3">
                                <label for="serial_number" class="form-label">Serial Number (SN) <span class="text-muted small">*</span></label>
                                <input type="text" class="form-control font-monospace" id="serial_number" name="serial_number" placeholder="Contoh: ZTEGC3FA7280" required autofocus>
                                <div class="form-text text-muted" style="font-size: 0.74rem;">Mendukung input manual atau barcode scanner USB.</div>
                            </div>

                            <div class="mb-3">
                                <label for="brand" class="form-label">Merek / Vendor</label>
                                <select class="form-select" id="brand" name="brand">
                                    <option value="" selected>-- Pilih Merek (Opsional) --</option>
                                    <option value="ZTE">ZTE</option>
                                    <option value="Huawei">Huawei</option>
                                    <option value="Fiberhome">Fiberhome</option>
                                    <option value="Nokia">Nokia</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="tanggal_masuk" class="form-label">Tanggal Penerimaan <span class="text-muted small">*</span></label>
                                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <button type="button" class="btn btn-custom-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-check2"></i> Simpan Unit ONT
                            </button>
                        </form>
                    </div>

                    <!-- Tab 2: Upload Excel/CSV Bulk -->
                    <div class="tab-pane fade" id="excel-pane" role="tabpanel">
                        <form action="{{ url('/ont-masuk/import') }}" method="POST" enctype="multipart/form-data" id="formImportExcel">
                            @csrf
                            <div class="upload-dropzone mb-3" onclick="document.getElementById('file_excel').click()">
                                <i class="bi bi-cloud-arrow-up fs-2 d-block mb-1" style="color: var(--theme-red);"></i>
                                <span class="d-block fw-medium small text-dark mb-1">Unggah File .xlsx / .csv</span>
                                <span class="text-muted" style="font-size: 0.75rem;">Klik untuk memilih spreadsheet</span>
                                <input type="file" class="d-none" id="file_excel" name="file" accept=".xlsx,.xls,.csv" onchange="updateFileName(this)">
                            </div>
                            <div id="selectedFileName" class="text-center small text-muted mb-3 d-none">
                                <i class="bi bi-file-earmark-check text-success me-1"></i> <span class="fw-medium text-dark" id="fileNameDisplay"></span>
                            </div>

                            <div class="p-2.5 rounded-2 mb-3 border" style="background-color: #fafbfc; font-size: 0.75rem;">
                                <div class="text-muted mb-1">Header Kolom Spreadsheet:</div>
                                <div class="font-monospace text-dark">serial_number | brand | tanggal_masuk</div>
                            </div>

                            <button type="button" class="btn btn-custom-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-upload"></i> Proses Import
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Spesifikasi Impor & Petunjuk Pengelolaan -->
    <div class="col-lg-7">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">Panduan Format & Ketentuan Impor</h6>
                <a href="#" class="btn btn-outline-theme btn-sm rounded-2 text-nowrap" style="font-size: 0.78rem;">
                    <i class="bi bi-download me-1"></i> Unduh Format .xlsx
                </a>
            </div>
            <div class="card-body p-3">
                <p class="text-muted small mb-3" style="font-size: 0.8rem;">
                    Untuk input masal ribuan unit ONT sekaligus, pastikan file spreadsheet Anda mengikuti struktur baku berikut agar tidak terjadi penolakan baris data:
                </p>

                <div class="table-responsive mb-3 border rounded-2">
                    <table class="table table-sm table-custom mb-0" style="font-size: 0.78rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Header</th>
                                <th>Tipe Data</th>
                                <th>Sifat</th>
                                <th>Contoh Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-monospace fw-semibold">serial_number</td>
                                <td>Teks</td>
                                <td><span class="badge badge-theme-red px-1.5 py-0.5 rounded-1">Wajib Unik</span></td>
                                <td class="font-monospace text-muted">ZTEGC3FA7280</td>
                            </tr>
                            <tr>
                                <td class="font-monospace fw-semibold">brand</td>
                                <td>Teks</td>
                                <td><span class="badge badge-soft-secondary px-1.5 py-0.5 rounded-1">Opsional</span></td>
                                <td class="text-muted">ZTE / Huawei / Fiberhome</td>
                            </tr>
                            <tr>
                                <td class="font-monospace fw-semibold">tanggal_masuk</td>
                                <td>Tanggal</td>
                                <td><span class="badge badge-theme-red px-1.5 py-0.5 rounded-1">Wajib</span></td>
                                <td class="font-monospace text-muted">2026-09-14 (YYYY-MM-DD)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-2.5 rounded-2 border text-muted" style="background-color: #fafbfc; font-size: 0.76rem; line-height: 1.4;">
                    <strong class="text-dark d-block mb-1">Catatan Validasi:</strong>
                    Baris dengan Serial Number yang sudah pernah dicatat di sistem akan dilewati secara otomatis untuk menjaga integritas data tanpa menghentikan proses baris lainnya.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Section: List ONT Masuk (Search & Pagination) -->
<div class="card card-custom">
    <div class="card-header bg-white border-bottom p-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <h6 class="fw-bold mb-0 text-dark">Daftar ONT Masuk</h6>
            </div>
            <div class="col-md-7">
                <form action="{{ url('/ont-masuk') }}" method="GET" class="d-flex gap-2 justify-content-md-end">
                    <div class="input-group input-group-sm" style="max-width: 240px;">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari SN atau merek..." value="{{ request('search') }}">
                    </div>
                    <select name="brand" class="form-select form-select-sm" style="max-width: 130px;">
                        <option value="">Semua Merek</option>
                        <option value="ZTE">ZTE</option>
                        <option value="Huawei">Huawei</option>
                        <option value="Fiberhome">Fiberhome</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-2.5">Filter</button>
                </form>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Serial Number (SN)</th>
                    <th>Merek</th>
                    <th>Tgl Masuk</th>
                    <th>Waktu Catat</th>
                    <th>Status Alur</th>
                    <th class="text-center" style="width: 70px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $mockItems = [
                        ['id' => 1, 'sn' => 'ZTEGD4CCA770', 'brand' => 'ZTE', 'tgl' => '2026-09-12', 'created' => '12 Sep 2026', 'status' => 'Dibawa Teknisi', 'status_type' => 'keluar'],
                        ['id' => 2, 'sn' => 'HWTC8820B192', 'brand' => 'Huawei', 'tgl' => '2026-09-12', 'created' => '12 Sep 2026', 'status' => 'Di Gudang', 'status_type' => 'gudang'],
                        ['id' => 3, 'sn' => 'FHDR7300A114', 'brand' => 'Fiberhome', 'tgl' => '2026-09-13', 'created' => '13 Sep 2026', 'status' => 'Dibawa Teknisi', 'status_type' => 'keluar'],
                        ['id' => 4, 'sn' => 'ZTEGD5BBA991', 'brand' => 'ZTE', 'tgl' => '2026-09-13', 'created' => '13 Sep 2026', 'status' => 'Di Gudang', 'status_type' => 'gudang'],
                        ['id' => 5, 'sn' => 'HWTC9940C553', 'brand' => 'Huawei', 'tgl' => '2026-09-14', 'created' => '14 Sep 2026', 'status' => 'Di Gudang', 'status_type' => 'gudang'],
                    ];
                @endphp

                @forelse($items ?? $mockItems as $index => $item)
                <tr>
                    <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="font-monospace fw-medium text-dark">{{ $item['sn'] }}</span>
                            <button class="btn btn-sm btn-link text-muted p-0 ms-1" title="Salin SN" onclick="navigator.clipboard.writeText('{{ $item['sn'] }}')">
                                <i class="bi bi-clipboard" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-soft-secondary px-2 py-0.5 rounded-1 small">{{ $item['brand'] }}</span>
                    </td>
                    <td>
                        <span class="text-dark small">{{ date('d M Y', strtotime($item['tgl'])) }}</span>
                    </td>
                    <td>
                        <span class="text-muted small">{{ $item['created'] }}</span>
                    </td>
                    <td>
                        @if($item['status_type'] == 'keluar')
                            <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-1 small">
                                Dibawa Teknisi
                            </span>
                        @else
                            <span class="badge badge-soft-success px-2 py-0.5 rounded-1 small">
                                Di Gudang
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-link text-muted p-0" title="Hapus">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted small">
                        Belum ada data ONT Masuk yang terdata.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="card-footer bg-white border-top p-2.5 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <span class="text-muted small" style="font-size: 0.78rem;">Menampilkan 1 - 5 dari 142 data</span>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item active"><a class="page-link" href="#" style="background-color: var(--theme-red); border-color: var(--theme-red);">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
            </ul>
        </nav>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('fileNameDisplay').textContent = input.files[0].name;
            document.getElementById('selectedFileName').classList.remove('d-none');
        }
    }
</script>
@endpush
