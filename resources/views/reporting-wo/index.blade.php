@extends('layouts.app')

@section('title', 'Reporting WO')

@section('content')
<!-- Header Page Minimal -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Reporting WO</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Laporan Work Order Lapangan</h4>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <span class="badge bg-white border text-secondary px-3 py-1.5 rounded-2 small fw-normal">
            Total Laporan: <strong class="text-dark">{{ number_format($totalWo) }}</strong> WO
        </span>
        <span class="badge bg-white border text-success px-3 py-1.5 rounded-2 small fw-normal">
            INSTALLED: <strong class="text-success">{{ number_format($totalInstalled) }}</strong>
        </span>
        <span class="badge bg-white border text-muted px-3 py-1.5 rounded-2 small fw-normal">
            NOT INSTALLED: <strong class="text-dark">{{ number_format($totalNotInstalled) }}</strong>
        </span>
    </div>
</div>

<!-- Forms & Specification Section -->
<div class="row g-4 mb-4">
    <!-- Form Upload File Excel Reporting WO -->
    <div class="col-lg-5">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom p-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="brand-icon-wrapper" style="width: 26px; height: 26px; font-size: 0.85rem;">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                    </span>
                    <h6 class="fw-bold mb-0 text-dark">Upload Laporan Work Order</h6>
                </div>
            </div>

            <div class="card-body p-3.5">
                <form action="{{ route('reporting-wo.import') }}" method="POST" enctype="multipart/form-data" id="formUploadWo">
                    @csrf
                    <div class="upload-dropzone mb-3" onclick="document.getElementById('file_wo').click()">
                        <i class="bi bi-cloud-arrow-up fs-2 d-block mb-1" style="color: var(--theme-red);"></i>
                        <span class="d-block fw-medium small text-dark mb-1">Unggah Rekap WO (.xlsx / .csv)</span>
                        <span class="text-muted" style="font-size: 0.75rem;">Klik untuk memilih file spreadsheet laporan WO teknisi</span>
                        <input type="file" class="d-none @error('file') is-invalid @enderror" id="file_wo" name="file" accept=".xlsx,.xls,.csv" onchange="updateFileName(this)">
                    </div>

                    @error('file')
                        <div class="text-danger small mb-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror

                    <div id="selectedFileName" class="text-center small text-muted mb-3 d-none">
                        <i class="bi bi-file-earmark-check text-success me-1"></i> <span class="fw-medium text-dark" id="fileNameDisplay"></span>
                    </div>

                    <div class="p-2.5 rounded-2 mb-3 border" style="background-color: #fafbfc; font-size: 0.73rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted fw-medium">Format Kolom Header Spreadsheet:</span>
                            <a href="{{ route('reporting-wo.template') }}" class="text-decoration-none fw-medium" style="color: var(--theme-red); font-size: 0.72rem;">
                                <i class="bi bi-download"></i> Unduh Template
                            </a>
                        </div>
                        <div class="font-monospace text-dark text-truncate" title="no_order | cid | serial_number | nama_teknisi | nik_teknisi | status_wo | tanggal_sa | vendor | sektor | cek_match">
                            no_order | cid | serial_number | nama_teknisi | nik_teknisi | status_wo | tanggal_sa | vendor | sektor | cek_match
                        </div>
                    </div>

                    <button type="submit" class="btn btn-custom-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-upload"></i> Proses Import Laporan WO
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Panduan Format & Logika Rekonsiliasi PRD -->
    <div class="col-lg-7">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">Ketentuan & Logika Rekonsiliasi (BR-04)</h6>
                <a href="{{ route('reporting-wo.template') }}" class="btn btn-outline-theme btn-sm rounded-2 text-nowrap" style="font-size: 0.78rem;">
                    <i class="bi bi-download me-1"></i> Unduh Template .xlsx
                </a>
            </div>
            <div class="card-body p-3">
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <div class="p-2.5 rounded-2 border h-100" style="background-color: #fafbfc;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge badge-soft-success rounded-1 px-2 py-0.5" style="font-size: 0.72rem;">INSTALLED</span>
                                <span class="fw-semibold text-dark small">Perangkat Terpasang</span>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.76rem; line-height: 1.4;">
                                Serial Number terdaftar di laporan WO dengan status <strong>"Work Order Selesai"</strong>. Unit terpasang di pelanggan.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-2.5 rounded-2 border h-100" style="background-color: #fafbfc;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge badge-soft-secondary rounded-1 px-2 py-0.5" style="font-size: 0.72rem;">NOT INSTALLED</span>
                                <span class="fw-semibold text-dark small">Unit di Lapangan</span>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.76rem; line-height: 1.4;">
                                Unit pernah diambil oleh teknisi namun belum memiliki laporan WO Selesai.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-2 border rounded-2">
                    <table class="table table-sm table-custom mb-0" style="font-size: 0.78rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Header Kolom</th>
                                <th>Sifat</th>
                                <th>Contoh Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-monospace fw-semibold">no_order</td>
                                <td><span class="badge badge-theme-red px-1.5 py-0.5 rounded-1">Wajib</span></td>
                                <td class="font-monospace text-muted">WO20264384690</td>
                            </tr>
                            <tr>
                                <td class="font-monospace fw-semibold">serial_number</td>
                                <td><span class="badge badge-theme-red px-1.5 py-0.5 rounded-1">Wajib (FK)</span></td>
                                <td class="font-monospace text-muted">ZTEGD4CCA770</td>
                            </tr>
                            <tr>
                                <td class="font-monospace fw-semibold">nama_teknisi</td>
                                <td><span class="badge badge-theme-red px-1.5 py-0.5 rounded-1">Wajib</span></td>
                                <td class="text-muted">Ahmad Kurniawan</td>
                            </tr>
                            <tr>
                                <td class="font-monospace fw-semibold">status_wo</td>
                                <td><span class="badge badge-theme-red px-1.5 py-0.5 rounded-1">Wajib</span></td>
                                <td><span class="badge badge-soft-success px-1.5 py-0.5 rounded-1">Work Order Selesai</span></td>
                            </tr>
                            <tr>
                                <td class="font-monospace fw-semibold">cek_match</td>
                                <td><span class="badge badge-soft-secondary px-1.5 py-0.5 rounded-1">Opsional</span></td>
                                <td class="text-muted">SESUAI / Beda</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Section: List Laporan Work Order (Filter & Pagination) -->
<div class="card card-custom">
    <div class="card-header bg-white border-bottom p-3">
        <div class="row g-2 align-items-center">
            <div class="col-lg-3 col-md-4">
                <h6 class="fw-bold mb-0 text-dark">Daftar Laporan Work Order</h6>
            </div>
            <div class="col-lg-9 col-md-8">
                <form action="{{ route('reporting-wo.index') }}" method="GET" class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <!-- Search Input -->
                    <div class="input-group input-group-sm" style="max-width: 220px;">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari WO, SN, CID..." value="{{ $search ?? '' }}">
                    </div>

                    <!-- Filter Status WO -->
                    <select name="status" class="form-select form-select-sm" style="max-width: 170px;">
                        <option value="">Semua Status WO</option>
                        <option value="selesai" {{ ($status ?? '') == 'selesai' ? 'selected' : '' }}>Work Order Selesai</option>
                        <option value="belum_selesai" {{ ($status ?? '') == 'belum_selesai' ? 'selected' : '' }}>Belum Selesai</option>
                    </select>

                    <!-- Filter Teknisi -->
                    <select name="teknisi" class="form-select form-select-sm" style="max-width: 160px;">
                        <option value="">Semua Teknisi</option>
                        @foreach($daftarTeknisi as $t)
                            <option value="{{ $t }}" {{ ($teknisi ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-outline-secondary btn-sm px-2.5">Filter</button>
                    @if($search || $status || $teknisi)
                        <a href="{{ route('reporting-wo.index') }}" class="btn btn-light btn-sm text-muted px-2" title="Reset Filter">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>No. Order</th>
                    <th>CID</th>
                    <th>Serial Number (SN)</th>
                    <th>Teknisi</th>
                    <th>Status WO</th>
                    <th>Tgl Selesai (SA)</th>
                    <th>Vendor / Sektor</th>
                    <th>Auto-Match</th>
                    <th class="text-center" style="width: 60px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td class="text-center text-muted small">{{ $items->firstItem() + $loop->index }}</td>
                    <td>
                        <span class="font-monospace fw-semibold text-dark small">{{ $item->no_order }}</span>
                    </td>
                    <td>
                        @if($item->cid)
                            <span class="font-monospace text-muted small">{{ $item->cid }}</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="font-monospace fw-medium text-dark">{{ $item->serial_number }}</span>
                            <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-1" title="Salin SN"
                                onclick="navigator.clipboard.writeText('{{ $item->serial_number }}'); this.innerHTML='<i class=\'bi bi-clipboard-check\' style=\'font-size:0.75rem;\'></i>'">
                                <i class="bi bi-clipboard" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium text-dark small">{{ $item->nama_teknisi }}</div>
                        @if($item->nik_teknisi)
                            <div class="text-muted" style="font-size: 0.72rem;">NIK: {{ $item->nik_teknisi }}</div>
                        @endif
                    </td>
                    <td>
                        @if($item->isInstalled())
                            <span class="badge badge-soft-success px-2 py-1 rounded-1 small">
                                <i class="bi bi-check2-circle me-1"></i> {{ $item->status_wo }}
                            </span>
                        @else
                            <span class="badge badge-soft-secondary px-2 py-1 rounded-1 small">
                                <i class="bi bi-clock me-1"></i> {{ $item->status_wo }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="text-dark small">
                            {{ $item->tanggal_sa ? $item->tanggal_sa->format('d M Y') : '—' }}
                        </span>
                    </td>
                    <td>
                        <div class="text-dark small">{{ $item->vendor ?? '—' }}</div>
                        @if($item->sektor)
                            <div class="text-muted" style="font-size: 0.72rem;">Sektor: {{ $item->sektor }}</div>
                        @endif
                    </td>
                    <td>
                        @if(strtoupper($item->cek_match) === 'SESUAI')
                            <span class="badge badge-soft-success rounded-1 px-2 py-0.5" style="font-size: 0.72rem;">
                                <i class="bi bi-patch-check me-0.5"></i> SESUAI
                            </span>
                        @elseif($item->cek_match)
                            <span class="badge badge-soft-secondary rounded-1 px-2 py-0.5" style="font-size: 0.72rem;">
                                {{ $item->cek_match }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form action="{{ route('reporting-wo.destroy', $item->id) }}" method="POST"
                            onsubmit="return confirm('Hapus data laporan WO {{ $item->no_order }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Hapus Laporan">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted small">
                        <i class="bi bi-file-earmark-spreadsheet fs-4 d-block mb-2 text-muted opacity-50"></i>
                        Belum ada data laporan Work Order (WO).
                        <div class="mt-2">
                            <span class="text-muted" style="font-size: 0.78rem;">Silakan gunakan form di atas untuk mengunggah file Excel <strong>reporting_wo.xlsx</strong>.</span>
                        </div>
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

    // Auto dismiss alert flash messages
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 4000);
</script>
@endpush
