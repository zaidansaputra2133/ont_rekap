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
            Total Keluar: <strong class="text-dark">{{ $totalKeluar ?? 98 }}</strong> Unit
        </span>
    </div>
</div>

<!-- Input Form & Real Handover Protocol -->
<div class="row g-4 mb-4">
    <!-- Form Penyerahan ke Teknisi -->
    <div class="col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white border-bottom p-3">
                <h6 class="fw-bold mb-0 text-dark">Form Serah Terima Unit</h6>
            </div>
            <div class="card-body p-3.5">
                <form action="{{ url('/ont-keluar') }}" method="POST" id="formOntKeluar">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nama_teknisi" class="form-label">Nama Teknisi <span class="text-muted small">*</span></label>
                            <input type="text" class="form-control" id="nama_teknisi" name="nama_teknisi" placeholder="Nama lengkap personil teknisi" required>
                        </div>

                        <div class="col-md-6">
                            <label for="tanggal_keluar" class="form-label">Tanggal Penyerahan <span class="text-muted small">*</span></label>
                            <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="serial_number" class="form-label">Serial Number (SN) <span class="text-muted small">*</span></label>
                            <input type="text" class="form-control font-monospace" id="serial_number" name="serial_number" placeholder="Contoh: ZTEGD4CCA770" required>
                        </div>

                        <div class="col-12">
                            <div class="text-muted" style="font-size: 0.74rem;">
                                *Hanya Serial Number yang terdaftar di stok gudang yang dapat diproses.
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="button" class="btn btn-custom-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-check2"></i> Catat Penyerahan Barang
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
                    <a href="{{ url('/ont-masuk') }}" class="fw-medium text-decoration-none" style="color: var(--theme-red);">Buka ONT Masuk &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Section: List Transaksi ONT Keluar -->
<div class="card card-custom">
    <div class="card-header bg-white border-bottom p-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <h6 class="fw-bold mb-0 text-dark">Daftar Transaksi Keluar</h6>
            </div>
            <div class="col-md-7">
                <form action="{{ url('/ont-keluar') }}" method="GET" class="d-flex gap-2 justify-content-md-end">
                    <div class="input-group input-group-sm" style="max-width: 240px;">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari SN atau teknisi..." value="{{ request('search') }}">
                    </div>
                    <select name="status" class="form-select form-select-sm" style="max-width: 130px;">
                        <option value="">Semua Kondisi</option>
                        <option value="normal">Normal</option>
                        <option value="rusak">Rusak</option>
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
                    <th>Teknisi</th>
                    <th>Tgl Keluar</th>
                    <th>Kondisi</th>
                    <th>Catatan Kerusakan</th>
                    <th class="text-center" style="width: 110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $mockKeluar = [
                        [
                            'id' => 1,
                            'sn' => 'ZTEGD4CCA770',
                            'teknisi' => 'Rian Hidayat',
                            'tgl' => '2026-09-13',
                            'keterangan' => 'Rusak',
                            'catatan' => 'Port PON LOS merah terus, adaptor normal'
                        ],
                        [
                            'id' => 2,
                            'sn' => 'FHDR7300A114',
                            'teknisi' => 'Ahmad Fajar',
                            'tgl' => '2026-09-13',
                            'keterangan' => null,
                            'catatan' => null
                        ],
                        [
                            'id' => 3,
                            'sn' => 'HWTC8820B192',
                            'teknisi' => 'Rian Hidayat',
                            'tgl' => '2026-09-14',
                            'keterangan' => null,
                            'catatan' => null
                        ],
                        [
                            'id' => 4,
                            'sn' => 'ZTEGC9912A04',
                            'teknisi' => 'Bagus Prakoso',
                            'tgl' => '2026-09-14',
                            'keterangan' => 'Rusak',
                            'catatan' => 'Mati total setelah petir di lokasi pelanggan'
                        ],
                        [
                            'id' => 5,
                            'sn' => 'HWTC4100X881',
                            'teknisi' => 'Dedi Kurniawan',
                            'tgl' => '2026-09-14',
                            'keterangan' => null,
                            'catatan' => null
                        ]
                    ];
                @endphp

                @forelse($keluarItems ?? $mockKeluar as $index => $row)
                <tr>
                    <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="font-monospace fw-medium text-dark">{{ $row['sn'] }}</span>
                            <button class="btn btn-sm btn-link text-muted p-0 ms-1" title="Salin SN" onclick="navigator.clipboard.writeText('{{ $row['sn'] }}')">
                                <i class="bi bi-clipboard" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                    </td>
                    <td>
                        <span class="fw-medium text-dark small">{{ $row['teknisi'] }}</span>
                    </td>
                    <td>
                        <span class="text-dark small">{{ date('d M Y', strtotime($row['tgl'])) }}</span>
                    </td>
                    <td>
                        @if($row['keterangan'] === 'Rusak')
                            <span class="badge badge-theme-red px-2 py-0.5 rounded-1 small">
                                Rusak
                            </span>
                        @else
                            <span class="badge badge-soft-success px-2 py-0.5 rounded-1 small">
                                Normal
                            </span>
                        @endif
                    </td>
                    <td>
                        @if(!empty($row['catatan']))
                            <span class="text-muted small">"{{ $row['catatan'] }}"</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-theme py-1 px-2 rounded-2" style="font-size: 0.78rem;"
                                onclick="openEditModal('{{ $row['id'] }}', '{{ $row['sn'] }}', '{{ $row['teknisi'] }}', '{{ $row['keterangan'] ?? '' }}', '{{ addslashes($row['catatan'] ?? '') }}')">
                            Ubah Kondisi
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted small">
                        Belum ada data penyerahan teknisi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="card-footer bg-white border-top p-2.5 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <span class="text-muted small" style="font-size: 0.78rem;">Menampilkan 1 - 5 dari 98 data</span>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item active"><a class="page-link" href="#" style="background-color: var(--theme-red); border-color: var(--theme-red);">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
            </ul>
        </nav>
    </div>
</div>

<!-- Modal Update Status & Catatan Kerusakan Minimal -->
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" aria-labelledby="modalUpdateStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm rounded-3">
            <div class="modal-header bg-white border-bottom px-3 py-2.5">
                <h6 class="modal-title fw-bold text-dark" id="modalUpdateStatusLabel">
                    Ubah Status Kondisi ONT
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/ont-keluar/update-status') }}" method="POST" id="formUpdateStatus">
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
                    <button type="button" class="btn btn-custom-primary btn-sm rounded-2" data-bs-dismiss="modal">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

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
</script>
@endpush
