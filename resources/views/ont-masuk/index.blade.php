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
            Total Gudang: <strong class="text-dark">{{ $totalCount }}</strong> Unit
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

<!-- Forms Section (Manual & Bulk Import) -->
<div class="row g-4 mb-4">
    <!-- Card Input: Tabs Manual vs Import Excel -->
    <div class="col-lg-5">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom p-2.5">
                <ul class="nav nav-pills nav-fill" id="ontMasukTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $errors->hasAny(['serial_number','tanggal_masuk','brand']) || old('_tab') == 'manual' ? 'active' : 'active' }} py-1.5 rounded-2 small" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual-pane" type="button" role="tab">
                            <i class="bi bi-pencil me-1"></i> Input Manual
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-1.5 rounded-2 small {{ old('_tab') == 'excel' ? 'active' : '' }}" id="excel-tab" data-bs-toggle="tab" data-bs-target="#excel-pane" type="button" role="tab">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Import Spreadsheet
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-3.5">
                <div class="tab-content" id="ontMasukTabContent">
                    <!-- Tab 1: Input Manual Single Item -->
                    <div class="tab-pane fade show active" id="manual-pane" role="tabpanel">
                        <form action="{{ route('ont-masuk.store') }}" method="POST" id="formManualInput" autocomplete="off">
                            @csrf
                            <div class="mb-3">
                                <label for="serial_number" class="form-label">Serial Number (SN) <span class="text-muted small">*</span></label>
                                <input type="text"
                                    autocomplete="off"
                                    class="form-control font-monospace @error('serial_number') is-invalid @enderror"
                                    id="serial_number" name="serial_number"
                                    placeholder="Contoh: ZTEGC3FA7280"
                                    value="{{ old('serial_number') }}"
                                    required autofocus>
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted" style="font-size: 0.74rem;">Mendukung input manual atau barcode scanner USB.</div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="brand" class="form-label mb-0">Merek / Vendor</label>
                                    <span id="brandAutoBadge" class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 rounded-1 d-none" style="font-size: 0.72rem;">
                                        <i class="bi bi-magic me-1"></i>Otomatis: <span id="brandAutoName"></span>
                                    </span>
                                </div>
                                <select class="form-select @error('brand') is-invalid @enderror" id="brand" name="brand">
                                    <option value="" {{ old('brand') == '' ? 'selected' : '' }}>-- Pilih Merek (Opsional) --</option>
                                    <option value="ZTE" {{ old('brand') == 'ZTE' ? 'selected' : '' }}>ZTE</option>
                                    <option value="Huawei" {{ old('brand') == 'Huawei' ? 'selected' : '' }}>Huawei</option>
                                    <option value="Fiberhome" {{ old('brand') == 'Fiberhome' ? 'selected' : '' }}>Fiberhome</option>
                                    <option value="Nokia" {{ old('brand') == 'Nokia' ? 'selected' : '' }}>Nokia</option>
                                    <option value="Lainnya" {{ old('brand') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="tanggal_masuk" class="form-label">Tanggal Penerimaan <span class="text-muted small">*</span></label>
                                <input type="date"
                                    class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                    id="tanggal_masuk" name="tanggal_masuk"
                                    value="{{ old('tanggal_masuk', date('Y-m-d')) }}"
                                    required>
                                @error('tanggal_masuk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-custom-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-check2"></i> Simpan Unit ONT
                            </button>
                        </form>
                    </div>

                    <!-- Tab 2: Upload Excel/CSV Bulk -->
                    <div class="tab-pane fade" id="excel-pane" role="tabpanel">
                        <form action="{{ route('ont-masuk.import') }}" method="POST" enctype="multipart/form-data" id="formImportExcel">
                            @csrf
                            <div class="upload-dropzone mb-3" onclick="document.getElementById('file_excel').click()">
                                <i class="bi bi-cloud-arrow-up fs-2 d-block mb-1" style="color: var(--theme-red);"></i>
                                <span class="d-block fw-medium small text-dark mb-1">Unggah File .xlsx / .csv</span>
                                <span class="text-muted" style="font-size: 0.75rem;">Klik untuk memilih spreadsheet</span>
                                <input type="file" class="d-none @error('file') is-invalid @enderror" id="file_excel" name="file" accept=".xlsx,.xls,.csv" onchange="updateFileName(this)">
                            </div>
                            @error('file')
                                <div class="text-danger small mb-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                            <div id="selectedFileName" class="text-center small text-muted mb-3 d-none">
                                <i class="bi bi-file-earmark-check text-success me-1"></i> <span class="fw-medium text-dark" id="fileNameDisplay"></span>
                            </div>

                            <div class="p-2.5 rounded-2 mb-3 border" style="background-color: #fafbfc; font-size: 0.75rem;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted">Header Kolom Spreadsheet:</span>
                                    <a href="{{ route('ont-masuk.template') }}" class="text-decoration-none fw-medium" style="color: var(--theme-red); font-size: 0.72rem;">
                                        <i class="bi bi-download"></i> Unduh Template
                                    </a>
                                </div>
                                <div class="font-monospace text-dark">serial_number | brand | tanggal_masuk</div>
                            </div>

                            <button type="submit" class="btn btn-custom-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
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
                <a href="{{ route('ont-masuk.template') }}" class="btn btn-outline-theme btn-sm rounded-2 text-nowrap" style="font-size: 0.78rem;">
                    <i class="bi bi-download me-1"></i> Unduh Template .xlsx
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
                <form action="{{ route('ont-masuk.index') }}" method="GET" class="d-flex gap-2 justify-content-md-end">
                    <div class="input-group input-group-sm" style="max-width: 240px;">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari SN atau merek..." value="{{ $search ?? '' }}">
                    </div>
                    <select name="brand" class="form-select form-select-sm" style="max-width: 130px;">
                        <option value="">Semua Merek</option>
                        <option value="ZTE" {{ ($brand ?? '') == 'ZTE' ? 'selected' : '' }}>ZTE</option>
                        <option value="Huawei" {{ ($brand ?? '') == 'Huawei' ? 'selected' : '' }}>Huawei</option>
                        <option value="Fiberhome" {{ ($brand ?? '') == 'Fiberhome' ? 'selected' : '' }}>Fiberhome</option>
                        <option value="Nokia" {{ ($brand ?? '') == 'Nokia' ? 'selected' : '' }}>Nokia</option>
                    </select>
                    <button type="submit" class="btn btn-outline-secondary btn-sm px-2.5">Filter</button>
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
                    <th class="text-center" style="width: 70px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td class="text-center text-muted small">{{ $items->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="font-monospace fw-medium text-dark">{{ $item->serial_number }}</span>
                            <button class="btn btn-sm btn-link text-muted p-0 ms-1" title="Salin SN"
                                onclick="navigator.clipboard.writeText('{{ $item->serial_number }}'); this.innerHTML='<i class=\'bi bi-clipboard-check\' style=\'font-size:0.75rem;\'></i>'">
                                <i class="bi bi-clipboard" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                    </td>
                    <td>
                        @if($item->brand)
                            <span class="badge badge-soft-secondary px-2 py-0.5 rounded-1 small">{{ $item->brand }}</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-dark small">{{ $item->tanggal_masuk->format('d M Y') }}</span>
                    </td>
                    <td>
                        <span class="text-muted small">{{ $item->created_at->format('d M Y, H:i') }}</span>
                    </td>
                    <td class="text-center">
                        <form action="{{ route('ont-masuk.destroy', $item->id) }}" method="POST"
                            onsubmit="return confirm('Hapus unit ONT {{ $item->serial_number }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Hapus">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted small">
                        <i class="bi bi-inbox fs-4 d-block mb-2 text-muted opacity-50"></i>
                        Belum ada data ONT Masuk yang terdata.
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
@endsection

@push('scripts')
<script>
    function updateFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('fileNameDisplay').textContent = input.files[0].name;
            document.getElementById('selectedFileName').classList.remove('d-none');
        }
    }

    // Auto-dismiss flash alerts setelah 4 detik
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 4000);

    // ==========================================
    // LOGIKA SCANNER BARCODE & AUTO-DETECT BRAND
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
        const snInput = document.getElementById('serial_number');
        const brandSelect = document.getElementById('brand');
        const brandAutoBadge = document.getElementById('brandAutoBadge');
        const brandAutoName = document.getElementById('brandAutoName');
        const formManual = document.getElementById('formManualInput');
        const manualTab = document.getElementById('manual-tab');

        // Fungsi identifikasi merek dari prefix Serial Number
        function detectBrandFromSN(sn) {
            if (!sn) return null;
            const s = sn.trim().toUpperCase();
            if (s.startsWith('ZTE')) return 'ZTE';
            if (s.startsWith('FHTT')) return 'Fiberhome';
            if (s.startsWith('ALCL')) return 'Nokia';
            if (s.startsWith('48575443') || s.startsWith('HWTC')) return 'Huawei';
            return null;
        }

        function applyBrandDetection() {
            if (!snInput || !brandSelect) return;
            const detected = detectBrandFromSN(snInput.value);
            if (detected) {
                brandSelect.value = detected;
                if (brandAutoName && brandAutoBadge) {
                    brandAutoName.textContent = detected;
                    brandAutoBadge.classList.remove('d-none');
                }
            } else {
                if (brandAutoBadge) {
                    brandAutoBadge.classList.add('d-none');
                }
            }
        }

        if (snInput) {
            // Deteksi real-time saat scanner mengetik teks barcode atau user paste/input
            snInput.addEventListener('input', applyBrandDetection);
            snInput.addEventListener('change', applyBrandDetection);

            // Jika user scan dan menekan Enter, pastikan deteksi dieksekusi sebelum submit
            snInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    applyBrandDetection();
                    // Biarkan submit form default berjalan otomatis
                }
            });

            // Fokus otomatis ke input Serial Number agar langsung siap scan tanpa klik
            snInput.focus();
        }

        if (manualTab && snInput) {
            manualTab.addEventListener('shown.bs.tab', function () {
                snInput.focus();
            });
        }

        if (formManual) {
            formManual.addEventListener('submit', function () {
                // Jalankan deteksi sekali lagi sebelum payload terkirim
                applyBrandDetection();

                if (snInput) {
                    snInput.value = snInput.value.trim().toUpperCase();
                }

                // Tampilkan efek loading pada tombol simpan
                const submitBtn = formManual.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan Unit...';
                }
            });
        }
    });
</script>
@endpush
