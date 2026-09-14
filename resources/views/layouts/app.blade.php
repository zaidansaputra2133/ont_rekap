<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'SIM-ONT') - Rekapitulasi ONT Masuk & Keluar</title>

    <!-- Google Fonts (Plus Jakarta Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bs-body-font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --body-bg: #fafafa;
            --card-border: #f0f0f2;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            
            /* Theme Merah Elegan (Crimson / Ruby Minimalis) */
            --theme-red: #b91c1c;
            --theme-red-hover: #991b1b;
            --theme-red-light: #fef2f2;
            --theme-red-border: #fee2e2;
            --theme-red-subtle: #fca5a5;

            /* Input Calm Variables (Tidak Jegrak) */
            --input-border: #e2e8f0;
            --input-bg: #ffffff;
            --input-focus-border: #f87171;
            --input-focus-ring: rgba(185, 28, 28, 0.08);
        }

        body {
            font-family: var(--bs-body-font-family);
            background-color: var(--body-bg);
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            letter-spacing: -0.01em;
        }

        /* Minimalist Red Theme Navbar */
        .navbar-custom {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 0.75rem 1.5rem;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.55rem;
        }

        .brand-icon-wrapper {
            background-color: var(--theme-red);
            color: #ffffff;
            width: 32px;
            height: 32px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .brand-badge {
            font-size: 0.68rem;
            font-weight: 600;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            background: var(--theme-red-light);
            color: var(--theme-red);
            border: 1px solid var(--theme-red-border);
        }

        .nav-link {
            font-weight: 500;
            font-size: 0.88rem;
            color: var(--text-muted);
            padding: 0.45rem 0.85rem !important;
            border-radius: 6px;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        .nav-link:hover {
            color: var(--theme-red);
            background-color: var(--theme-red-light);
        }

        .nav-link.active {
            color: var(--theme-red) !important;
            background-color: var(--theme-red-light);
            font-weight: 600;
        }

        /* Minimalist Cards with smooth radius */
        .card-custom {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 14px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.03);
            transition: border-color 0.15s ease;
        }

        .card-custom:hover {
            border-color: #e2e8f0;
        }

        .metric-card {
            padding: 1.5rem 1.6rem !important;
            border-radius: 14px !important;
        }

        .stat-icon-wrapper {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        /* Soft Minimal Badges */
        .badge-theme-red {
            background-color: var(--theme-red-light);
            color: var(--theme-red);
            border: 1px solid var(--theme-red-border);
            font-weight: 500;
        }

        .badge-soft-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #dcfce7;
            font-weight: 500;
        }

        .badge-soft-secondary {
            background-color: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
            font-weight: 500;
        }

        /* Tables - Clean & Minimal */
        .table-custom {
            vertical-align: middle;
            margin-bottom: 0;
        }

        .table-custom th {
            font-size: 0.74rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            background-color: #fafbfc;
            border-bottom: 1px solid var(--card-border);
            padding: 0.75rem 1rem;
        }

        .table-custom td {
            padding: 0.75rem 1rem;
            color: #334155;
            border-bottom: 1px solid #f8fafc;
            font-size: 0.88rem;
        }

        .table-custom tr:last-child td {
            border-bottom: none;
        }

        .table-custom tr:hover td {
            background-color: #fcfcfd;
        }

        /* Buttons Red Minimal Theme */
        .btn-custom-primary {
            background-color: var(--theme-red);
            color: #ffffff;
            font-weight: 500;
            font-size: 0.86rem;
            border: 1px solid var(--theme-red);
            border-radius: 7px;
            padding: 0.48rem 0.95rem;
            transition: all 0.15s ease;
        }

        .btn-custom-primary:hover {
            background-color: var(--theme-red-hover);
            color: #ffffff;
            border-color: var(--theme-red-hover);
        }

        .btn-outline-theme {
            background-color: transparent;
            color: var(--theme-red);
            font-weight: 500;
            font-size: 0.86rem;
            border: 1px solid var(--theme-red-border);
            border-radius: 7px;
            padding: 0.48rem 0.95rem;
            transition: all 0.15s ease;
        }

        .btn-outline-theme:hover {
            background-color: var(--theme-red-light);
            color: var(--theme-red-hover);
            border-color: var(--theme-red-subtle);
        }

        /* Form Controls: Halus, Tenang, Tidak Jegrak */
        .form-label {
            font-weight: 500;
            font-size: 0.81rem;
            color: #475569;
            margin-bottom: 0.35rem;
        }

        .form-control, .form-select {
            border-radius: 7px;
            border: 1px solid var(--input-border);
            background-color: var(--input-bg);
            padding: 0.48rem 0.78rem;
            font-size: 0.87rem;
            color: #1e293b;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--input-focus-border);
            box-shadow: 0 0 0 3px var(--input-focus-ring);
            background-color: #ffffff;
        }

        .input-group-text {
            background-color: #fafafa;
            border: 1px solid var(--input-border);
            color: #94a3b8;
            font-size: 0.86rem;
        }

        /* Upload Area Halus */
        .upload-dropzone {
            border: 1.5px dashed #cbd5e1;
            background-color: #fafbfc;
            border-radius: 8px;
            padding: 1.5rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .upload-dropzone:hover {
            background-color: var(--theme-red-light);
            border-color: var(--theme-red-subtle);
        }

        /* Nav Pills Red Theme Minimal */
        .nav-pills .nav-link {
            color: #64748b;
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--theme-red);
            color: #ffffff !important;
        }

        /* Minimal Footer */
        footer {
            margin-top: auto;
            border-top: 1px solid var(--card-border);
            background: #ffffff;
            color: #94a3b8;
            font-size: 0.8rem;
            padding: 1rem 0;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container-fluid px-lg-4">
            <a class="navbar-brand" href="{{ url('/') }}">
                <span class="brand-icon-wrapper">
                    <i class="bi bi-router"></i>
                </span>
                <span>SIM-ONT</span>
                <span class="brand-badge d-none d-sm-inline-block">Gudang & Teknisi</span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto ms-lg-3 mb-2 mb-lg-0 gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            <i class="bi bi-grid-1x2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('ont-masuk*') ? 'active' : '' }}" href="{{ url('/ont-masuk') }}">
                            <i class="bi bi-box-arrow-in-down"></i> ONT Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('ont-keluar*') ? 'active' : '' }}" href="{{ url('/ont-keluar') }}">
                            <i class="bi bi-box-arrow-up-right"></i> ONT Keluar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reporting-wo*') ? 'active' : '' }}" href="{{ url('/reporting-wo') }}">
                            <i class="bi bi-file-earmark-text"></i> Reporting WO
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="py-4">
        <div class="container-fluid px-lg-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 rounded-2 mb-4 py-2 px-3 border-0 bg-success-subtle text-success-emphasis" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 rounded-2 mb-4 py-2 px-3 border-0 bg-info-subtle text-info-emphasis" role="alert">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>{{ session('info') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 rounded-2 mb-4 py-2 px-3 border-0 bg-danger-subtle text-danger-emphasis" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Minimalist Footer -->
    <footer>
        <div class="container-fluid px-lg-4 d-flex justify-content-between align-items-center">
            <div>
                &copy; {{ date('Y') }} <strong>SIM-ONT</strong> &bull; Sistem Rekapitulasi ONT Masuk & Keluar
            </div>
            <div class="text-muted small">
                Sistem Internal
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 Bundle JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
