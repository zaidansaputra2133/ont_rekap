@extends('layouts.app')

@section('title', 'ONT Keluar')

@section('content')
@php
    $hasFilter = $search || $teknisi || $tanggal || $status;
    $stok = $availableOnts->map(fn ($o) => ['sn' => $o->serial_number, 'brand' => $o->brand])->values();
    $oldSelected = collect(old('serial_number', []))->flatten()->filter()->values();
@endphp

<div class="page page-narrow">
    <div>
        <h1 class="page-title">Penyerahan ONT ke Teknisi</h1>
        <p class="page-sub">Distribusi unit ONT dari gudang ke teknisi lapangan</p>
    </div>

    <div class="split">
        {{-- ═══ Form serah terima ═══ --}}
        <div class="panel panel-pad">
            <h2 class="panel-title pb-3 mb-3 border-bottom">Form Serah Terima Unit</h2>

            <form method="POST" action="{{ route('ont-keluar.store') }}" id="formKeluar" novalidate>
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label for="nama_teknisi" class="lbl">Nama Teknisi <span class="req">*</span></label>
                        <input type="text" id="nama_teknisi" name="nama_teknisi" value="{{ old('nama_teknisi') }}" list="daftarTeknisi"
                               placeholder="Ketik nama teknisi..." autocomplete="off"
                               class="form-control @error('nama_teknisi') is-invalid @enderror">
                        <datalist id="daftarTeknisi">
                            @foreach($daftarTeknisi as $nama)<option value="{{ $nama }}">@endforeach
                        </datalist>
                        @error('nama_teknisi')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label for="tanggal_keluar" class="lbl">Tanggal Penyerahan</label>
                        <input type="date" id="tanggal_keluar" name="tanggal_keluar" value="{{ old('tanggal_keluar', now()->toDateString()) }}"
                               class="form-control @error('tanggal_keluar') is-invalid @enderror">
                        @error('tanggal_keluar')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Picker SN --}}
                <div class="picker">
                    <div class="picker-head">
                        <span class="fw-semibold fs-13 text-dark"><i class="bi bi-box-seam me-2"></i>Pilih Unit ONT dari Stok Gudang</span>
                        <span class="pill pill-gray">Tersedia: <span id="stokCount">{{ $availableOnts->count() }}</span> Unit</span>
                    </div>

                    <div class="picker-filter">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="fs-12 text-muted-2 mb-1" for="pickBrand">Filter Merek:</label>
                                <select id="pickBrand" class="form-select form-select-sm"></select>
                            </div>
                            <div class="col-sm-6">
                                <label class="fs-12 text-muted-2 mb-1" for="pickSearch">Cari SN:</label>
                                <div class="search-wrap">
                                    <i class="bi bi-search"></i>
                                    <input type="text" id="pickSearch" class="form-control form-control-sm" placeholder="Ketik SN..." autocomplete="off">
                                    <button type="button" class="clear-x" id="pickClear" style="display:none;" aria-label="Hapus pencarian">✕</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="pickList"></div>

                    <div class="picker-foot" id="pickPager" style="display:none;">
                        <span class="fs-12 text-muted-2" id="pickInfo"></span>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn-page" id="pickPrev"><i class="bi bi-arrow-left"></i> Prev</button>
                            <span class="fs-12 fw-semibold text-dark px-1" id="pickPage"></span>
                            <button type="button" class="btn-page" id="pickNext">Next <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    <div class="picker-sum" id="pickSum">
                        <span class="sum-label"><i class="bi bi-check2-square me-1"></i>Unit Terpilih (<span id="selCount">0</span>):</span>
                        <span class="fs-12 text-muted-2" id="selNone">Belum ada unit dipilih</span>
                        <div class="d-flex flex-wrap gap-1 flex-grow-1" id="selChips"></div>
                        <button type="button" class="link-reset" id="selReset" style="display:none;">Batal semua</button>
                    </div>
                </div>

                <div id="snInputs"></div>

                @error('serial_number')<p class="field-error">{{ $message }}</p>@enderror
                <p class="field-error" id="clientError" style="display:none;"></p>

                <button type="submit" class="btn btn-brand w-100 mt-3 py-2" id="submitBtn" style="border-radius: 12px; font-weight: 700;">
                    Catat Penyerahan Barang
                </button>
            </form>
        </div>

        {{-- ═══ Kolom kanan ═══ --}}
        <div class="d-flex flex-column gap-3">
            <div class="kpi kpi-green" style="height:auto;">
                <div class="kpi-top"><span class="kpi-label">STOK TERSEDIA</span><span class="kpi-dot"></span></div>
                <div class="kpi-value">{{ number_format($availableOnts->count(), 0, ',', '.') }}</div>
                <div class="fs-12 text-muted-2">unit siap diserahkan</div>
            </div>

            <div class="panel panel-pad d-flex flex-column gap-3" style="padding: 1rem;">
                <h3 class="fs-12 fw-semibold text-uppercase pb-2 border-bottom m-0" style="letter-spacing:.04em; color: var(--ink-2);">Keterangan Kondisi</h3>
                <div class="info-card info-green">
                    <span class="dot-sm" style="background:#22c55e;"></span>
                    <div><b>NORMAL</b><p>Kondisi default saat penyerahan. Teknisi bertanggung jawab atas perangkat selama instalasi.</p></div>
                </div>
                <div class="info-card info-red">
                    <span class="dot-sm" style="background:#ef4444;"></span>
                    <div><b>RUSAK</b><p>Klik <strong>"Ubah Kondisi"</strong> pada baris transaksi untuk melaporkan unit cacat / retur.</p></div>
                </div>
                <a href="{{ route('ont-masuk.index') }}" class="btn btn-soft-blue btn-sm fs-12 fw-medium">
                    <i class="bi bi-arrow-right"></i> Lihat ONT Masuk / Cek Stok
                </a>
            </div>
        </div>
    </div>

    {{-- ═══ Tabel transaksi ═══ --}}
    <form id="filterForm" method="GET" action="{{ route('ont-keluar.index') }}"></form>

    <div class="panel" id="tabelPanel">
        <div class="panel-head">
            <h2 class="panel-title">Daftar Transaksi Keluar</h2>
            <span class="fs-12 text-muted-2">{{ number_format($items->total(), 0, ',', '.') }} record</span>
        </div>

        <div class="table-responsive">
            <table class="table tbl">
                <thead>
                    <tr>
                        <th>No</th><th>Serial Number</th><th>Teknisi</th><th>Tgl Keluar</th><th>Kondisi</th><th>Catatan Kerusakan</th><th>Aksi</th>
                    </tr>
                    <tr class="filter-row">
                        <td></td>
                        <td><input type="text" name="search" value="{{ $search }}" form="filterForm" data-autosubmit class="form-control form-control-xs" placeholder="Cari SN..." style="min-width:140px;" autocomplete="off"></td>
                        <td><input type="text" name="teknisi" value="{{ $teknisi }}" form="filterForm" data-autosubmit class="form-control form-control-xs" placeholder="Cari teknisi..." style="min-width:130px;" autocomplete="off"></td>
                        <td><input type="date" name="tanggal" value="{{ $tanggal }}" form="filterForm" data-autosubmit class="form-control form-control-xs"></td>
                        <td>
                            <select name="status" form="filterForm" data-autosubmit class="form-select form-select-xs">
                                <option value="">Semua</option>
                                <option value="normal" @selected($status === 'normal')>Normal</option>
                                <option value="rusak" @selected($status === 'rusak')>Rusak</option>
                            </select>
                        </td>
                        <td></td>
                        <td>@if($hasFilter)<a href="{{ route('ont-keluar.index') }}" class="link-reset">Reset</a>@endif</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $row)
                        <tr>
                            <td class="cell-no">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="cell-mono">{{ $row->serial_number }}</td>
                            <td class="fw-medium" style="color: var(--ink-2);">{{ $row->nama_teknisi }}</td>
                            <td class="cell-small">{{ $row->tanggal_keluar?->format('d-m-Y') ?? '—' }}</td>
                            <td>
                                @if($row->isRusak())<span class="pill pill-red">Rusak</span>@else<span class="pill pill-green">Normal</span>@endif
                            </td>
                            <td class="cell-small">{{ $row->catatan ?: '—' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-act btn-act-amber js-kondisi text-nowrap"
                                            data-id="{{ $row->id }}" data-sn="{{ $row->serial_number }}"
                                            data-ket="{{ $row->keterangan }}" data-catatan="{{ $row->catatan }}">Ubah Kondisi</button>
                                    <button type="button" class="btn btn-act btn-act-red js-delete-confirm"
                                            data-type="ont-keluar"
                                            data-action="{{ route('ont-keluar.destroy', $row) }}"
                                            data-sn="{{ $row->serial_number }}"
                                            data-teknisi="{{ $row->nama_teknisi }}"
                                            data-tanggal="{{ $row->tanggal_keluar?->format('d-m-Y') ?? '—' }}"
                                            data-kondisi="{{ $row->isRusak() ? 'Rusak' : 'Normal' }}">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty">{{ $hasFilter ? 'Tidak ada transaksi yang sesuai filter' : 'Belum ada transaksi keluar' }}</td></tr>
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

{{-- ═══ Modal ubah kondisi ═══ --}}
<div class="modal fade" id="kondisiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 384px;">
        <form method="POST" action="{{ route('ont-keluar.update-status') }}" class="modal-content">
            @csrf
            <input type="hidden" name="id" id="kondisiId">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">Ubah Kondisi Unit</h2>
                    <div class="fs-12 text-muted-2 font-mono mt-1" id="kondisiSn"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body d-flex flex-column gap-3">
                <div>
                    <label class="lbl" for="kondisiKet">Kondisi</label>
                    <select name="keterangan" id="kondisiKet" class="form-select">
                        <option value="">Normal</option>
                        <option value="Rusak">Rusak</option>
                    </select>
                </div>
                <div>
                    <label class="lbl" for="kondisiCatatan">Catatan Kerusakan</label>
                    <textarea name="catatan" id="kondisiCatatan" rows="3" maxlength="2000" class="form-control" style="resize:none;" placeholder="Deskripsikan kerusakan..."></textarea>
                </div>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-gray" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-amber">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var STOK = @json($stok);
    var PRESELECT = @json($oldSelected);
    var PAGE_SIZE = 5;

    var els = {
        brand: document.getElementById('pickBrand'),
        search: document.getElementById('pickSearch'),
        clear: document.getElementById('pickClear'),
        list: document.getElementById('pickList'),
        pager: document.getElementById('pickPager'),
        info: document.getElementById('pickInfo'),
        page: document.getElementById('pickPage'),
        prev: document.getElementById('pickPrev'),
        next: document.getElementById('pickNext'),
        sum: document.getElementById('pickSum'),
        selCount: document.getElementById('selCount'),
        selNone: document.getElementById('selNone'),
        chips: document.getElementById('selChips'),
        reset: document.getElementById('selReset'),
        inputs: document.getElementById('snInputs'),
        submit: document.getElementById('submitBtn'),
        err: document.getElementById('clientError'),
        form: document.getElementById('formKeluar')
    };

    var valid = {};
    STOK.forEach(function (o) { valid[o.sn] = true; });
    var selected = PRESELECT.filter(function (sn) { return valid[sn]; });
    var page = 1;

    function brandOf(o) { return o.brand || 'Lainnya'; }

    // opsi filter merek (dengan jumlah)
    (function buildBrands() {
        var counts = {};
        STOK.forEach(function (o) { counts[brandOf(o)] = (counts[brandOf(o)] || 0) + 1; });
        els.brand.innerHTML = '';
        var all = document.createElement('option');
        all.value = ''; all.textContent = '-- Semua Merek (' + STOK.length + ') --';
        els.brand.appendChild(all);
        Object.keys(counts).sort().forEach(function (b) {
            var op = document.createElement('option');
            op.value = b; op.textContent = b + ' (' + counts[b] + ')';
            els.brand.appendChild(op);
        });
    })();

    function filtered() {
        var b = els.brand.value, q = els.search.value.trim().toLowerCase();
        return STOK.filter(function (o) {
            return (!b || brandOf(o) === b) && (!q || o.sn.toLowerCase().indexOf(q) !== -1);
        });
    }

    function toggle(sn) {
        var i = selected.indexOf(sn);
        if (i === -1) selected.push(sn); else selected.splice(i, 1);
        els.err.style.display = 'none';
        render();
    }

    function renderList() {
        var items = filtered();
        var pages = Math.max(1, Math.ceil(items.length / PAGE_SIZE));
        page = Math.min(page, pages);
        var start = (page - 1) * PAGE_SIZE;
        var slice = items.slice(start, start + PAGE_SIZE);

        els.list.innerHTML = '';
        if (items.length === 0) {
            var e = document.createElement('div');
            e.className = 'picker-empty';
            e.textContent = STOK.length === 0 ? 'Tidak ada stok tersedia' : 'Tidak ada SN yang sesuai filter';
            els.list.appendChild(e);
        }
        slice.forEach(function (o) {
            var checked = selected.indexOf(o.sn) !== -1;
            var row = document.createElement('label');
            row.className = 'picker-row' + (checked ? ' checked' : '');
            var cb = document.createElement('input');
            cb.type = 'checkbox'; cb.checked = checked;
            cb.addEventListener('change', function () { toggle(o.sn); });
            var sn = document.createElement('span');
            sn.className = 'sn'; sn.textContent = o.sn;
            var tag = document.createElement('span');
            tag.className = 'tag'; tag.textContent = brandOf(o);
            row.appendChild(cb); row.appendChild(sn); row.appendChild(tag);
            els.list.appendChild(row);
        });

        els.pager.style.display = items.length ? '' : 'none';
        els.info.textContent = items.length
            ? 'Menampilkan ' + (start + 1) + '–' + Math.min(start + PAGE_SIZE, items.length) + ' dari ' + items.length + ' unit'
            : '';
        els.page.textContent = page + ' / ' + pages;
        els.prev.disabled = page === 1;
        els.next.disabled = page === pages;
    }

    function renderSelected() {
        var n = selected.length;
        els.selCount.textContent = n;
        els.sum.classList.toggle('has', n > 0);
        els.selNone.style.display = n ? 'none' : '';
        els.reset.style.display = n ? '' : 'none';
        els.submit.textContent = 'Catat Penyerahan Barang' + (n ? ' (' + n + ' Unit)' : '');

        els.chips.innerHTML = '';
        selected.forEach(function (sn) {
            var chip = document.createElement('span');
            chip.className = 'chip';
            chip.appendChild(document.createTextNode(sn));
            var x = document.createElement('button');
            x.type = 'button'; x.setAttribute('aria-label', 'Batalkan ' + sn); x.textContent = '✕';
            x.addEventListener('click', function () { toggle(sn); });
            chip.appendChild(x);
            els.chips.appendChild(chip);
        });

        // hidden input yang dikirim ke backend sebagai serial_number[]
        els.inputs.innerHTML = '';
        selected.forEach(function (sn) {
            var h = document.createElement('input');
            h.type = 'hidden'; h.name = 'serial_number[]'; h.value = sn;
            els.inputs.appendChild(h);
        });
    }

    function render() { renderList(); renderSelected(); }

    els.brand.addEventListener('change', function () { page = 1; renderList(); });
    els.search.addEventListener('input', function () { page = 1; els.clear.style.display = els.search.value ? '' : 'none'; renderList(); });
    els.clear.addEventListener('click', function () { els.search.value = ''; els.clear.style.display = 'none'; page = 1; renderList(); els.search.focus(); });
    els.prev.addEventListener('click', function () { page = Math.max(1, page - 1); renderList(); });
    els.next.addEventListener('click', function () { page += 1; renderList(); });
    els.reset.addEventListener('click', function () { selected = []; render(); });

    els.form.addEventListener('submit', function (e) {
        var msg = '';
        if (!document.getElementById('nama_teknisi').value.trim()) msg = 'Nama Teknisi wajib diisi.';
        else if (selected.length === 0) msg = 'Pilih minimal satu Serial Number.';
        if (msg) {
            e.preventDefault();
            els.err.textContent = msg;
            els.err.style.display = '';
        }
    });

    render();

    // Modal ubah kondisi
    var modal = new bootstrap.Modal(document.getElementById('kondisiModal'));
    var ket = document.getElementById('kondisiKet'), cat = document.getElementById('kondisiCatatan');
    function syncCatatan() {
        var rusak = ket.value === 'Rusak';
        cat.disabled = !rusak;
        cat.style.opacity = rusak ? '' : '.5';
        if (!rusak) cat.value = '';
    }
    ket.addEventListener('change', syncCatatan);
    document.addEventListener('click', function (e) {
        var b = e.target.closest('.js-kondisi');
        if (!b) return;
        document.getElementById('kondisiId').value = b.dataset.id;
        document.getElementById('kondisiSn').textContent = b.dataset.sn;
        ket.value = b.dataset.ket === 'Rusak' ? 'Rusak' : '';
        cat.value = b.dataset.catatan || '';
        syncCatatan();
        modal.show();
    });
})();
</script>
@endpush
