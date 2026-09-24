<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard') — ONT-TRACK</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bs-body-font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            --theme-red:        #c0392b;
            --theme-red-hover:  #a93226;
            --theme-red-light:  #fdf2f2;
            --theme-red-border: #f5c6c6;
            --sidebar-w:        190px;
            --sidebar-w-mini:   56px;
            --header-h:         56px;
            --body-bg:          #f5f6fa;
            --card-border:      #e8eaed;
            --text-dark:        #1a1d23;
            --text-muted:       #6b7280;
            --sidebar-transition: 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--bs-body-font-family);
            background: var(--body-bg);
            color: #334155;
            min-height: 100vh;
            display: flex;
            letter-spacing: -0.01em;
        }

        /* ── Sidebar ──────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid var(--card-border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: width var(--sidebar-transition), transform var(--sidebar-transition);
            overflow: hidden;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-brand {
            padding: 1.1rem 1.1rem 0.9rem;
            border-bottom: 1px solid var(--card-border);
            min-height: 64px;
            display: flex;
            align-items: center;
        }

        .sidebar-brand-inner {
            width: 100%;
        }

        .sidebar-brand-name {
            font-size: 1rem;
            font-weight: 800;
            color: var(--theme-red);
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .sidebar-brand-sub {
            font-size: 0.65rem;
            color: #94a3b8;
            font-weight: 400;
            line-height: 1.3;
            margin-top: 0.15rem;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0.65rem;
            overflow: hidden;
        }

        .sidebar-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            padding: 0 0.55rem;
            margin-bottom: 0.45rem;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity var(--sidebar-transition), height var(--sidebar-transition), margin var(--sidebar-transition);
        }



        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.52rem 0.7rem;
            border-radius: 8px;
            font-size: 0.84rem;
            font-weight: 500;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.15s ease;
            margin-bottom: 0.1rem;
            position: relative;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar-link:hover {
            background: #f3f4f6;
            color: var(--theme-red);
        }

        .sidebar-link.active {
            background: var(--theme-red-light);
            color: var(--theme-red);
            font-weight: 600;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: -0.65rem;
            top: 50%;
            transform: translateY(-50%);
            height: 60%;
            width: 3px;
            background: var(--theme-red);
            border-radius: 0 3px 3px 0;
        }

        .sidebar-link i {
            font-size: 0.95rem;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 0.85rem 1.1rem;
            border-top: 1px solid var(--card-border);
            font-size: 0.68rem;
            color: #9ca3af;
        }

        /* ── Main Layout ───────────────────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left var(--sidebar-transition);
        }

        .main-wrapper.sidebar-collapsed {
            margin-left: 0;
        }

        /* ── Top Header ────────────────────────────────────────── */
        .topbar {
            height: var(--header-h);
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem 0 1rem;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Toggle button — selalu tampil */
        .btn-sidebar-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            background: #f8fafc;
            border: 1px solid var(--card-border);
            border-radius: 8px;
            cursor: pointer;
            color: #4b5563;
            font-size: 1.15rem;
            transition: background 0.15s, color 0.15s, border-color 0.15s;
            flex-shrink: 0;
        }

        .btn-sidebar-toggle:hover {
            background: var(--theme-red-light);
            color: var(--theme-red);
            border-color: var(--theme-red-border);
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #f97316;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-info-name {
            font-size: 0.84rem;
            font-weight: 600;
            color: var(--text-dark);
            line-height: 1.1;
        }

        .user-info-role {
            font-size: 0.7rem;
            color: #94a3b8;
        }

        .topbar-divider {
            width: 1px;
            height: 22px;
            background: var(--card-border);
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--theme-red);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            font-family: inherit;
            transition: opacity 0.15s;
        }

        .btn-logout:hover { opacity: 0.75; }

        /* ── Page Content ──────────────────────────────────────── */
        .page-content {
            flex: 1;
            padding: 1.75rem 1.75rem 2rem;
        }

        /* ── Cards ─────────────────────────────────────────────── */
        .card-custom {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .metric-card {
            padding: 1.25rem 1.35rem !important;
            border-radius: 12px !important;
        }

        /* ── Badges ─────────────────────────────────────────────── */
        .badge-soft-success {
            background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-weight: 500;
        }
        .badge-soft-warning {
            background: #fffbeb; color: #92400e; border: 1px solid #fde68a; font-weight: 500;
        }
        .badge-soft-danger {
            background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; font-weight: 500;
        }
        .badge-soft-primary {
            background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 500;
        }
        .badge-soft-secondary {
            background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; font-weight: 500;
        }
        .badge-theme-red {
            background: var(--theme-red-light); color: var(--theme-red); border: 1px solid var(--theme-red-border); font-weight: 500;
        }

        /* ── Tables ─────────────────────────────────────────────── */
        .table-custom { vertical-align: middle; margin-bottom: 0; }
        .table-custom th {
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.05em; color: #6b7280; background: #f9fafb;
            border-bottom: 1px solid var(--card-border); padding: 0.7rem 1rem;
        }
        .table-custom td {
            padding: 0.7rem 1rem; color: #334155; border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }
        .table-custom tr:last-child td { border-bottom: none; }
        .table-custom tr:hover td { background: #fafbfc; }

        /* ── Buttons ────────────────────────────────────────────── */
        .btn-custom-primary {
            background: var(--theme-red); color: #fff; font-weight: 500; font-size: 0.86rem;
            border: 1px solid var(--theme-red); border-radius: 7px; padding: 0.45rem 0.9rem;
            transition: all 0.15s ease;
        }
        .btn-custom-primary:hover { background: var(--theme-red-hover); color: #fff; border-color: var(--theme-red-hover); }

        .btn-outline-theme {
            background: transparent; color: var(--theme-red); font-weight: 500; font-size: 0.86rem;
            border: 1px solid var(--theme-red-border); border-radius: 7px; padding: 0.45rem 0.9rem;
            transition: all 0.15s ease;
        }
        .btn-outline-theme:hover { background: var(--theme-red-light); color: var(--theme-red-hover); }

        /* ── Form controls ──────────────────────────────────────── */
        .form-label { font-weight: 500; font-size: 0.81rem; color: #475569; margin-bottom: 0.35rem; }
        .form-control, .form-select {
            border-radius: 7px; border: 1px solid #e2e8f0; background: #ffffff;
            padding: 0.46rem 0.76rem; font-size: 0.87rem; color: #1e293b;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #f87171; box-shadow: 0 0 0 3px rgba(185,28,28,0.08);
        }
        .input-group-text { background: #fafafa; border: 1px solid #e2e8f0; color: #94a3b8; font-size: 0.86rem; }

        /* Upload dropzone */
        .upload-dropzone {
            border: 1.5px dashed #cbd5e1; background: #fafbfc; border-radius: 8px;
            padding: 1.5rem 1rem; text-align: center; cursor: pointer; transition: all 0.15s ease;
        }
        .upload-dropzone:hover { background: var(--theme-red-light); border-color: #fca5a5; }

        /* ── Mobile Sidebar Overlay ─────────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.35);
            z-index: 99;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-w) !important;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .sidebar-overlay.open { display: block; }
            .main-wrapper { margin-left: 0 !important; }
            .page-content { padding: 1.1rem; }
            .user-info-name, .user-info-role, .topbar-divider { display: none; }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="sidebar-brand-inner">
            <div class="sidebar-brand-name">ONT-TRACK</div>
            <div class="sidebar-brand-sub">Sistem Manajemen ONT dan<br>Work Order Berbasis Web</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Menu Utama</div>

        <a href="{{ url('/') }}"
           class="sidebar-link {{ request()->is('/') ? 'active' : '' }}"
           data-tooltip="Dashboard">
            <i class="bi bi-grid-1x2"></i>
            <span class="link-text">Dashboard</span>
        </a>
        <a href="{{ url('/ont-masuk') }}"
           class="sidebar-link {{ request()->is('ont-masuk*') ? 'active' : '' }}"
           data-tooltip="ONT Masuk">
            <i class="bi bi-arrow-down-circle"></i>
            <span class="link-text">ONT Masuk</span>
        </a>
        <a href="{{ url('/ont-keluar') }}"
           class="sidebar-link {{ request()->is('ont-keluar*') ? 'active' : '' }}"
           data-tooltip="ONT Keluar">
            <i class="bi bi-arrow-up-circle"></i>
            <span class="link-text">ONT Keluar</span>
        </a>
        <a href="{{ url('/reporting-wo') }}"
           class="sidebar-link {{ request()->is('reporting-wo*') ? 'active' : '' }}"
           data-tooltip="Reporting WO">
            <i class="bi bi-file-earmark-text"></i>
            <span class="link-text">Reporting WO</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="sidebar-footer">
        v1.0.0 &bull; {{ date('Y') }}
    </div>
</aside>

<!-- Main Wrapper -->
<div class="main-wrapper" id="mainWrapper">

    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-left">
            <!-- Toggle Sidebar Button (selalu tampil desktop & mobile) -->
            <button class="btn-sidebar-toggle" id="sidebarToggle" title="Sembunyikan / Tampilkan Sidebar" aria-label="Toggle Sidebar">
                <i class="bi bi-layout-sidebar" id="toggleIcon"></i>
            </button>
        </div>

        <div class="topbar-right">
            @auth
            <div class="topbar-user">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="user-info-name">{{ Auth::user()->name }}</div>
                    <div class="user-info-role">Administrator</div>
                </div>
            </div>

            <div class="topbar-divider"></div>

            <form method="POST" action="{{ route('logout') }}" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
            @endauth
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="px-4 pt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 rounded-2 py-2 px-3 border-0 bg-success-subtle text-success-emphasis mb-0" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 rounded-2 py-2 px-3 border-0 bg-info-subtle text-info-emphasis mb-0" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 rounded-2 py-2 px-3 border-0 bg-danger-subtle text-danger-emphasis mb-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Page Content -->
    <main class="page-content">
        @yield('content')
    </main>

</div><!-- /main-wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const sidebar      = document.getElementById('sidebar');
    const mainWrapper  = document.getElementById('mainWrapper');
    const overlay      = document.getElementById('sidebarOverlay');
    const toggleBtn    = document.getElementById('sidebarToggle');
    const toggleIcon   = document.getElementById('toggleIcon');
    const STORAGE_KEY  = 'ont_sidebar_collapsed';

    const isMobile = () => window.innerWidth <= 768;

    // ── Desktop: collapse / expand ──────────────────────────
    function setCollapsed(collapsed, save = true) {
        if (collapsed) {
            sidebar.classList.add('collapsed');
            mainWrapper.classList.add('sidebar-collapsed');
            toggleIcon.className = 'bi bi-layout-sidebar-reverse';
        } else {
            sidebar.classList.remove('collapsed');
            mainWrapper.classList.remove('sidebar-collapsed');
            toggleIcon.className = 'bi bi-layout-sidebar';
        }
        if (save) {
            localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
        }
    }

    // ── Mobile: overlay open / close ────────────────────────
    function openMobileSidebar() {
        sidebar.classList.add('mobile-open');
        overlay.classList.add('open');
    }

    function closeMobileSidebar() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('open');
    }

    // ── Toggle button handler ────────────────────────────────
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            if (isMobile()) {
                sidebar.classList.contains('mobile-open') ? closeMobileSidebar() : openMobileSidebar();
            } else {
                setCollapsed(!sidebar.classList.contains('collapsed'));
            }
        });
    }

    if (overlay) overlay.addEventListener('click', closeMobileSidebar);

    // ── Restore saved state on page load ────────────────────
    (function () {
        if (!isMobile() && localStorage.getItem(STORAGE_KEY) === '1') {
            setCollapsed(true, false);
        }
    })();

    // ── Adapt when resizing across mobile/desktop boundary ──
    window.addEventListener('resize', function () {
        if (isMobile()) {
            sidebar.classList.remove('collapsed');
            mainWrapper.classList.remove('sidebar-collapsed');
        } else {
            closeMobileSidebar();
            setCollapsed(localStorage.getItem(STORAGE_KEY) === '1', false);
        }
    });
</script>

@stack('scripts')
</body>
</html>
