<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — ONT-TRACK</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/ont-track.css') }}">

    @stack('styles')
</head>
<body>
<script>
    // Terapkan state sidebar sebelum render supaya tidak berkedip.
    (function () {
        var saved = null;
        try { saved = localStorage.getItem('ont_sidebar_open'); } catch (e) {}
        var open = saved === null ? window.innerWidth >= 768 : saved === '1';
        if (open) document.body.classList.add('sidebar-open');
    })();
</script>

<div class="app-shell">
    {{-- ═══ NAVBAR ═══ --}}
    <header class="app-header">
        <div class="d-flex align-items-center gap-3">
            <button type="button" class="burger" id="sidebarToggle" aria-label="Tampilkan / sembunyikan sidebar">
                <span></span><span></span><span></span>
            </button>
            <div>
                <div class="brand-name">ONT-TRACK</div>
                <div class="brand-sub">Sistem Manajemen ONT dan Work Order Berbasis Web</div>
            </div>
        </div>

        @auth
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar">{{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}</div>
                <div class="user-meta">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <div class="header-divider"></div>
            <button type="button" class="btn-logout" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </div>
        @endauth
    </header>

    <div class="app-body">
        {{-- ═══ SIDEBAR ═══ --}}
        <aside class="app-sidebar" id="sidebar">
            <div class="sidebar-inner">
                <nav class="sidebar-nav">
                    <div class="sidebar-label">Menu Utama</div>
                    <a href="{{ route('dashboard') }}" class="nav-item-ui {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2"></i> Dashboard
                    </a>
                    <a href="{{ route('ont-masuk.index') }}" class="nav-item-ui {{ request()->routeIs('ont-masuk.*') ? 'active' : '' }}">
                        <i class="bi bi-box-arrow-in-down"></i> ONT Masuk
                    </a>
                    <a href="{{ route('ont-keluar.index') }}" class="nav-item-ui {{ request()->routeIs('ont-keluar.*') ? 'active' : '' }}">
                        <i class="bi bi-box-arrow-up"></i> ONT Keluar
                    </a>
                    <a href="{{ route('reporting-wo.index') }}" class="nav-item-ui {{ request()->routeIs('reporting-wo.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard2-check"></i> Reporting WO
                    </a>
                </nav>
                <div class="sidebar-foot">v1.0.0 · {{ date('Y') }}</div>
            </div>
        </aside>
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        {{-- ═══ KONTEN ═══ --}}
        <main class="app-main">
            @if(session('success') || session('info') || session('error'))
                <div class="px-4 pt-3">
                    @foreach(['success' => 'check-circle-fill', 'info' => 'info-circle-fill', 'error' => 'exclamation-triangle-fill'] as $type => $icon)
                        @if(session($type))
                            <div class="flash flash-{{ $type }} alert alert-dismissible fade show mb-2" role="alert">
                                <i class="bi bi-{{ $icon }}"></i>
                                <div>{{ session($type) }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- ═══ MODAL KONFIRMASI LOGOUT ═══ --}}
@auth
<div class="modal fade modal-logout" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 320px;">
        <div class="modal-content p-4 text-center">
            <div class="logout-icon mx-auto mb-3"><i class="bi bi-box-arrow-right"></i></div>
            <h3 class="fs-6 fw-bold text-dark">Konfirmasi Logout</h3>
            <p class="fs-12 text-muted-2 mt-2 mb-4" style="line-height: 1.6;">
                Apakah Anda yakin ingin keluar dari sistem?<br>Sesi aktif Anda akan diakhiri.
            </p>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary flex-fill" style="border-radius: 12px;" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('logout') }}" class="flex-fill m-0">
                    @csrf
                    <button type="submit" class="btn btn-brand w-100" style="border-radius: 12px;">Ya, Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        var body = document.body;

        function setSidebar(open) {
            body.classList.toggle('sidebar-open', open);
            try { localStorage.setItem('ont_sidebar_open', open ? '1' : '0'); } catch (e) {}
        }

        document.getElementById('sidebarToggle').addEventListener('click', function () {
            setSidebar(!body.classList.contains('sidebar-open'));
        });
        document.getElementById('sidebarBackdrop').addEventListener('click', function () { setSidebar(false); });

        // Simpan dan pulihkan posisi scroll agar tidak melompat ke atas saat reload / filter
        function saveTableScroll() {
            try { sessionStorage.setItem('ont_table_scroll_y', window.scrollY.toString()); } catch (e) {}
        }

        function restoreTableScroll() {
            try {
                var savedY = sessionStorage.getItem('ont_table_scroll_y');
                if (savedY !== null) {
                    sessionStorage.removeItem('ont_table_scroll_y');
                    var y = parseInt(savedY, 10);
                    window.scrollTo({ top: y, behavior: 'instant' });
                    requestAnimationFrame(function () {
                        window.scrollTo({ top: y, behavior: 'instant' });
                    });
                }
            } catch (e) {}
        }
        restoreTableScroll();
        window.addEventListener('DOMContentLoaded', restoreTableScroll);

        // Jika halaman dibuka dengan parameter query filter dan tanpa saved scroll, arahkan fokus ke tabel
        window.addEventListener('DOMContentLoaded', function () {
            var urlParams = new URLSearchParams(window.location.search);
            var hasFilterParams = urlParams.has('brand') || urlParams.has('search') || urlParams.has('status') ||
                                  urlParams.has('tanggal') || urlParams.has('teknisi') || urlParams.has('vendor') ||
                                  urlParams.has('match') || urlParams.has('page');
            if (hasFilterParams && !sessionStorage.getItem('ont_table_scroll_y') && window.scrollY < 80) {
                var panel = document.getElementById('tabelPanel') || document.querySelector('.panel');
                if (panel) {
                    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });

        // Form konfirmasi (hapus data, dll.) & simpan posisi scroll sebelum form dikirim
        document.addEventListener('submit', function (e) {
            saveTableScroll();
            var msg = e.target.getAttribute('data-confirm');
            if (msg && !window.confirm(msg)) {
                e.preventDefault();
            }
        });

        // Bangun URL filter dari form dan elemen dengan atribut form="..."
        function buildFilterUrl(form) {
            var url = new URL(form.action || window.location.href, window.location.origin);
            var params = new URLSearchParams();
            var selector = '[form="' + form.id + '"], #' + form.id + ' input, #' + form.id + ' select';
            document.querySelectorAll(selector).forEach(function (input) {
                if (!input.name || input.disabled) return;
                if (input.type === 'checkbox' || input.type === 'radio') {
                    if (input.checked) params.append(input.name, input.value);
                } else if (input.value !== '') {
                    params.append(input.name, input.value);
                }
            });
            url.search = params.toString();
            return url.toString();
        }

        var activeFilterController = null;
        function updateTableAjax(form, targetUrl, focusedName) {
            saveTableScroll();
            var panel = document.getElementById('tabelPanel') || (form ? form.parentElement.querySelector('.panel') : null) || document.querySelector('.panel');
            if (!panel) {
                if (form) form.submit();
                else window.location.href = targetUrl;
                return;
            }

            if (activeFilterController) {
                activeFilterController.abort();
            }
            activeFilterController = new AbortController();

            panel.style.transition = 'opacity 0.15s ease';
            panel.style.opacity = '0.55';
            panel.style.pointerEvents = 'none';

            fetch(targetUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: activeFilterController.signal
            })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP error ' + res.status);
                return res.text();
            })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var newPanel = doc.getElementById('tabelPanel') || doc.querySelector('.panel');
                if (newPanel && panel) {
                    panel.replaceWith(newPanel);
                    window.history.pushState(null, '', targetUrl);
                    initAutoSubmit();

                    if (focusedName) {
                        var refocused = document.querySelector('[name="' + focusedName + '"][data-autosubmit]');
                        if (refocused) {
                            refocused.focus();
                            if (refocused.type === 'text' && refocused.setSelectionRange) {
                                var len = refocused.value.length;
                                refocused.setSelectionRange(len, len);
                            }
                        }
                    }
                } else {
                    window.location.href = targetUrl;
                }
            })
            .catch(function (err) {
                if (err.name === 'AbortError') return;
                saveTableScroll();
                if (form) form.submit();
                else window.location.href = targetUrl;
            });
        }

        function initAutoSubmit() {
            document.querySelectorAll('[data-autosubmit]').forEach(function (el) {
                if (el.dataset.boundAutosubmit) return;
                el.dataset.boundAutosubmit = '1';

                var form = document.getElementById(el.getAttribute('form'));
                if (!form) return;

                if (el.tagName === 'INPUT' && el.type === 'text') {
                    var t;
                    el.addEventListener('input', function () {
                        clearTimeout(t);
                        t = setTimeout(function () {
                            var targetUrl = buildFilterUrl(form);
                            updateTableAjax(form, targetUrl, el.name);
                        }, 400);
                    });
                    el.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            clearTimeout(t);
                            var targetUrl = buildFilterUrl(form);
                            updateTableAjax(form, targetUrl, el.name);
                        }
                    });
                } else {
                    el.addEventListener('change', function () {
                        var targetUrl = buildFilterUrl(form);
                        updateTableAjax(form, targetUrl, el.name);
                    });
                }
            });
        }
        initAutoSubmit();

        // Tangani klik pagination dan tombol reset agar tetap tanpa reload layar ke atas
        document.addEventListener('click', function (e) {
            var link = e.target.closest('#tabelPanel .pagination a, .panel .pagination a, #tabelPanel a.link-reset, .panel a.link-reset');
            if (!link) return;
            var form = document.getElementById('filterForm');
            e.preventDefault();
            updateTableAjax(form, link.href);
        });

        // Tangani tombol browser back / forward
        window.addEventListener('popstate', function () {
            var form = document.getElementById('filterForm');
            if (form) {
                updateTableAjax(form, window.location.href);
            } else {
                window.location.reload();
            }
        });
    })();
</script>
@stack('scripts')
</body>
</html>
