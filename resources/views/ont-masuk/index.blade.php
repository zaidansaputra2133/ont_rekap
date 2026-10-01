@extends('layouts.app')

@section('title', 'ONT Masuk')

@section('content')
@php
    $brandOptions = ['ZTE', 'Huawei', 'Fiberhome', 'Nokia', 'Lainnya'];
    $isEditing = old('_method') === 'PUT' && old('_edit_id');
    $hasFilter = $search || $brand || $tanggal;
@endphp

<div class="page page-narrow">
    <div>
        <h1 class="page-title">Inventaris ONT Masuk</h1>
        <p class="page-sub">Pencatatan penerimaan unit ONT dari supplier / gudang pusat</p>
    </div>

    {{-- ═══ Form input ═══ --}}
    <div class="panel panel-pad">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <h2 class="panel-title">Input Data ONT</h2>
            <button type="button" class="btn btn-line-green btn-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Import Spreadsheet
            </button>
        </div>

        <form method="POST" action="{{ route('ont-masuk.store') }}" id="formTambah">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="serial_number" class="lbl">Serial Number (SN)</label>
                    <input type="text" id="serial_number" name="serial_number" value="{{ $isEditing ? '' : old('serial_number') }}"
                           autofocus autocomplete="off" placeholder="Scan barcode atau ketik manual..."
                           class="form-control @if(!$isEditing) @error('serial_number') is-invalid @enderror @endif">
                    <p class="hint"><i class="bi bi-check2"></i> Mendukung input manual &amp; scan barcode</p>
                </div>
                <div class="col-md-4">
                    <label for="brand" class="lbl d-flex align-items-center justify-content-between">
                        <span>Merek / Vendor</span>
                        <span id="brandAutoBadge" class="badge bg-success-subtle text-success border border-success-subtle py-0 px-2 fw-medium fs-11 d-none">
                            <i class="bi bi-magic me-1"></i><span id="brandAutoName"></span>
                        </span>
                    </label>
                    <select id="brand" name="brand" class="form-select">
                        <option value="">Otomatis (deteksi dari SN)</option>
                        @foreach($brandOptions as $b)
                            <option value="{{ $b }}" @selected(!$isEditing && old('brand') === $b)>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tanggal_masuk" class="lbl">Tanggal Penerimaan</label>
                    <input type="date" id="tanggal_masuk" name="tanggal_masuk"
                           value="{{ $isEditing ? now()->toDateString() : old('tanggal_masuk', now()->toDateString()) }}"
                           class="form-control @if(!$isEditing) @error('tanggal_masuk') is-invalid @enderror @endif">
                </div>
            </div>

            @if(!$isEditing)
                @foreach(['serial_number', 'tanggal_masuk', 'brand'] as $f)
                    @error($f)<p class="field-error">{{ $message }}</p>@enderror
                @endforeach
            @endif

            <button type="submit" class="btn btn-brand mt-3 px-4"><i class="bi bi-plus-lg me-1"></i> Tambah Unit</button>
        </form>
    </div>

    {{-- ═══ Catatan import massal ═══ --}}
    <div class="collapse-card" id="noteCard">
        <button type="button" class="collapse-toggle" data-bs-toggle="collapse" data-bs-target="#noteBody" aria-expanded="false" aria-controls="noteBody">
            <span class="d-flex align-items-center gap-3">
                <i class="bi bi-clipboard2-check fs-5" style="color: var(--green);"></i>
                <span>
                    <span class="fw-semibold fs-13" style="color: var(--green-dark);">Catatan Input Masal Ribuan Unit ONT</span>
                    <span class="ms-2 fs-12 note-hint" style="color: #22c55e;">— klik untuk lihat panduan</span>
                </span>
            </span>
            <i class="bi bi-chevron-down chev"></i>
        </button>
        <div class="collapse" id="noteBody">
            <div class="collapse-body">
                <p class="fs-13 fw-semibold mt-3 mb-3" style="color: #166534;">
                    Untuk input masal ribuan unit ONT sekaligus, pastikan file spreadsheet Anda mengikuti struktur baku berikut agar tidak terjadi penolakan baris data:
                </p>
                <div class="table-responsive mb-3">
                    <table class="tbl-green">
                        <thead><tr><th>Nama Header</th><th>Tipe Data</th><th>Sifat</th><th>Contoh Nilai</th></tr></thead>
                        <tbody>
                            <tr><td>serial_number</td><td>Teks</td><td>Wajib Unik</td><td>ZTEGC3FA7280</td></tr>
                            <tr><td>brand</td><td>Teks</td><td>Opsional</td><td>ZTE / Huawei / Fiberhome</td></tr>
                            <tr><td>tanggal_masuk</td><td>Tanggal</td><td>Wajib</td><td>2026-09-14 (YYYY-MM-DD)</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="callout-green">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Baris dengan Serial Number yang sudah pernah dicatat di sistem akan dilewati secara otomatis untuk menjaga integritas data tanpa menghentikan proses baris lainnya.
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Tabel ═══ --}}
    <form id="filterForm" method="GET" action="{{ route('ont-masuk.index') }}"></form>

    <div class="panel" id="tabelPanel">
        <div class="panel-head">
            <h2 class="panel-title">Daftar ONT Masuk</h2>
            <span class="fs-12 text-muted-2">{{ number_format($items->total(), 0, ',', '.') }} unit</span>
        </div>

        <div class="table-responsive">
            <table class="table tbl">
                <thead>
                    <tr>
                        <th>No</th><th>Serial Number (SN)</th><th>Merek</th><th>Tgl Masuk</th><th>Waktu Catat</th><th>Aksi</th>
                    </tr>
                    <tr class="filter-row">
                        <td></td>
                        <td>
                            <input type="text" name="search" value="{{ $search }}" form="filterForm" data-autosubmit
                                   class="form-control form-control-xs" placeholder="Cari SN..." style="min-width: 150px;" autocomplete="off">
                        </td>
                        <td>
                            <select name="brand" form="filterForm" data-autosubmit class="form-select form-select-xs">
                                <option value="">Semua</option>
                                @foreach($brandOptions as $b)
                                    <option value="{{ $b }}" @selected($brand === $b)>{{ $b }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="date" name="tanggal" value="{{ $tanggal }}" form="filterForm" data-autosubmit class="form-control form-control-xs"></td>
                        <td></td>
                        <td>@if($hasFilter)<a href="{{ route('ont-masuk.index') }}" class="link-reset">Reset</a>@endif</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $row)
                        <tr>
                            <td class="cell-no">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="cell-mono">{{ $row->serial_number }}</td>
                            <td>
                                @if($row->brand)<span class="pill pill-sm pill-blue">{{ $row->brand }}</span>@else<span class="cell-no">—</span>@endif
                            </td>
                            <td class="cell-small">{{ $row->tanggal_masuk?->format('d-m-Y') ?? '—' }}</td>
                            <td class="cell-no">{{ $row->created_at?->format('d-m-Y H:i') }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-act btn-act-amber js-edit"
                                            data-id="{{ $row->id }}"
                                            data-sn="{{ $row->serial_number }}"
                                            data-brand="{{ $row->brand }}"
                                            data-tgl="{{ $row->tanggal_masuk?->toDateString() }}">Edit</button>
                                    <button type="button" class="btn btn-act btn-act-red js-delete-confirm"
                                            data-type="ont-masuk"
                                            data-action="{{ route('ont-masuk.destroy', $row) }}"
                                            data-sn="{{ $row->serial_number }}"
                                            data-brand="{{ $row->brand ?: '—' }}"
                                            data-tanggal="{{ $row->tanggal_masuk?->format('d-m-Y') ?? '—' }}">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">{{ $hasFilter ? 'Tidak ada data yang sesuai filter' : 'Belum ada data ONT masuk' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="panel-foot">
                <span>Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} unit</span>
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ═══ Modal Edit ═══ --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 384px;">
        <form method="POST" id="editForm" class="modal-content" action="#">
            @csrf @method('PUT')
            <input type="hidden" name="_edit_id" id="editId">
            <div class="modal-header">
                <h2 class="modal-title">Edit Data ONT</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body d-flex flex-column gap-3">
                <div>
                    <label class="lbl" for="editSn">Serial Number (SN)</label>
                    <input type="text" name="serial_number" id="editSn" class="form-control font-mono" required autocomplete="off">
                </div>
                <div>
                    <label class="lbl" for="editBrand">Merek / Vendor</label>
                    <select name="brand" id="editBrand" class="form-select">
                        <option value="">Otomatis (deteksi dari SN)</option>
                        @foreach($brandOptions as $b)<option value="{{ $b }}">{{ $b }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="lbl" for="editTgl">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="editTgl" class="form-control" required>
                </div>
                <div class="field-error" id="editError" style="display:none;"></div>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-gray" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-amber">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ Modal Import ═══ --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 448px;">
        <form method="POST" action="{{ route('ont-masuk.import') }}" enctype="multipart/form-data" class="modal-content" id="importForm">
            @csrf
            <div class="modal-header">
                <h2 class="modal-title">Import Spreadsheet</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body d-flex flex-column gap-3">
                <label class="dropzone m-0">
                    <i class="bi bi-folder2-open"></i>
                    <span class="dropzone-name" id="importName">Klik untuk pilih file</span>
                    <span class="fs-12 text-muted-2 mt-1">.xlsx / .xls / .csv (maks. 10 MB)</span>
                    <input type="file" name="file" id="importFile" accept=".xlsx,.xls,.csv" required>
                </label>
                @error('file')<div class="field-error m-0">{{ $message }}</div>@enderror

                <div class="tpl-box">
                    <div>
                        <div class="fs-12 fw-semibold text-muted-2 mb-1">Unduh Template</div>
                        <div class="fs-12 text-muted-2 font-mono">serial_number | brand | tanggal_masuk</div>
                    </div>
                    <a href="{{ route('ont-masuk.template') }}" class="btn btn-soft-green btn-sm text-nowrap"><i class="bi bi-download me-1"></i> Unduh</a>
                </div>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-gray" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-brand" id="importBtn">Proses Import</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var UPDATE_URL = {{ Js::from(route('ont-masuk.update', ['ontMasuk' => '__ID__'])) }};
    var editModalEl = document.getElementById('editModal');
    var editModal = new bootstrap.Modal(editModalEl);
    var f = {
        form: document.getElementById('editForm'),
        id: document.getElementById('editId'),
        sn: document.getElementById('editSn'),
        brand: document.getElementById('editBrand'),
        tgl: document.getElementById('editTgl'),
        err: document.getElementById('editError')
    };

    // Auto-detect merek/vendor berdasarkan awalan Serial Number standar:
    // - ZTE -> ZTE
    // - FHTT -> Fiberhome
    // - ALCL -> Nokia
    // - 48575443 atau HWTC -> Huawei
    function detectBrandFromSN(sn) {
        if (!sn) return '';
        var s = sn.trim().toUpperCase();
        if (s.startsWith('ZTE')) return 'ZTE';
        if (s.startsWith('FHTT')) return 'Fiberhome';
        if (s.startsWith('ALCL')) return 'Nokia';
        if (s.startsWith('48575443') || s.startsWith('HWTC')) return 'Huawei';
        return '';
    }

    var snInput = document.getElementById('serial_number');
    var brandSelect = document.getElementById('brand');
    var badge = document.getElementById('brandAutoBadge');
    var badgeName = document.getElementById('brandAutoName');

    function applyBrandDetection() {
        if (!snInput || !brandSelect) return;
        var detected = detectBrandFromSN(snInput.value);
        if (detected) {
            brandSelect.value = detected;
            brandSelect.dataset.autoSelected = '1';
            if (badge && badgeName) {
                badgeName.textContent = detected;
                badge.classList.remove('d-none');
            }
        } else {
            if (brandSelect.dataset.autoSelected === '1') {
                brandSelect.value = '';
                delete brandSelect.dataset.autoSelected;
            }
            if (badge) {
                badge.classList.add('d-none');
            }
        }
    }

    if (snInput && brandSelect) {
        snInput.addEventListener('input', applyBrandDetection);
        snInput.addEventListener('change', applyBrandDetection);
        snInput.addEventListener('paste', function () {
            setTimeout(applyBrandDetection, 30);
        });
        brandSelect.addEventListener('change', function () {
            delete brandSelect.dataset.autoSelected;
            if (badge) badge.classList.add('d-none');
        });
        if (snInput.value) {
            applyBrandDetection();
        }
    }

    var formTambah = document.getElementById('formTambah');
    if (formTambah && snInput) {
        formTambah.addEventListener('submit', function () {
            snInput.value = snInput.value.trim().toUpperCase();
            if (!brandSelect.value) {
                var detected = detectBrandFromSN(snInput.value);
                if (detected) brandSelect.value = detected;
            }
        });
    }

    // Modal Edit auto-detect
    var editSn = document.getElementById('editSn');
    if (editSn) {
        function applyEditDetection() {
            var detected = detectBrandFromSN(editSn.value);
            if (detected) setBrand(detected);
        }
        editSn.addEventListener('input', applyEditDetection);
        editSn.addEventListener('paste', function () {
            setTimeout(applyEditDetection, 30);
        });
    }

    function setBrand(val) {
        val = val || '';
        var found = Array.prototype.some.call(f.brand.options, function (o) { return o.value === val; });
        if (!found) {
            var o = document.createElement('option');
            o.value = val; o.textContent = val;
            f.brand.appendChild(o);
        }
        f.brand.value = val;
    }

    function openEdit(d, error) {
        f.form.action = UPDATE_URL.replace('__ID__', d.id);
        f.id.value = d.id;
        f.sn.value = d.sn || '';
        f.tgl.value = d.tgl || '';
        var brandVal = d.brand || detectBrandFromSN(d.sn);
        setBrand(brandVal);
        f.err.style.display = error ? '' : 'none';
        f.err.textContent = error || '';
        editModal.show();
    }

    // Delegasi klik tombol Edit agar tetap aktif meski tabel diperbarui secara dinamis (AJAX)
    document.addEventListener('click', function (e) {
        var b = e.target.closest('.js-edit');
        if (!b) return;
        openEdit({ id: b.dataset.id, sn: b.dataset.sn, brand: b.dataset.brand, tgl: b.dataset.tgl }, '');
    });

    @if($isEditing)
        openEdit(
            {{ Js::from(['id' => old('_edit_id'), 'sn' => old('serial_number'), 'brand' => old('brand'), 'tgl' => old('tanggal_masuk')]) }},
            {{ Js::from($errors->first()) }}
        );
    @endif

    // Import
    var file = document.getElementById('importFile');
    var name = document.getElementById('importName');
    file.addEventListener('change', function () { name.textContent = file.files[0] ? file.files[0].name : 'Klik untuk pilih file'; });
    document.getElementById('importForm').addEventListener('submit', function () {
        var btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spin"></span>Memproses...';
    });
    @if($errors->has('file'))
        new bootstrap.Modal(document.getElementById('importModal')).show();
    @endif

    // Kartu catatan: sinkronkan gaya saat dibuka/ditutup
    var noteBody = document.getElementById('noteBody'), noteCard = document.getElementById('noteCard');
    noteBody.addEventListener('show.bs.collapse', function () { noteCard.classList.add('is-open'); noteCard.querySelector('.note-hint').style.display = 'none'; });
    noteBody.addEventListener('hide.bs.collapse', function () { noteCard.classList.remove('is-open'); noteCard.querySelector('.note-hint').style.display = ''; });
})();
</script>
@endpush
