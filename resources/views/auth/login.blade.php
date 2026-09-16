<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Admin — SIM-ONT</title>
    <meta name="description" content="Halaman login admin SIM-ONT. Sistem Rekapitulasi ONT Masuk & Keluar Telkom Akses.">

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
            --theme-red: #b91c1c;
            --theme-red-hover: #991b1b;
            --theme-red-dark: #7f1d1d;
            --theme-red-light: #fef2f2;
            --theme-red-border: #fee2e2;
            --font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--font);
            margin: 0;
            min-height: 100vh;
            display: flex;
            background: #f8fafc;
            letter-spacing: -0.01em;
        }

        /* ─── Left Panel ───────────────────────────────────── */
        .login-panel-left {
            width: 42%;
            background: linear-gradient(145deg, #7f1d1d 0%, #b91c1c 45%, #dc2626 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        .login-panel-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Decorative circles */
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
        }
        .deco-circle-1 { width: 300px; height: 300px; top: -80px; right: -80px; }
        .deco-circle-2 { width: 200px; height: 200px; bottom: 60px; left: -60px; }
        .deco-circle-3 { width: 120px; height: 120px; bottom: 40%; right: 10%; }

        .panel-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            position: relative;
            z-index: 2;
        }

        .panel-brand-icon {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.2);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
        }

        .panel-brand-text {
            color: #fff;
            font-weight: 800;
            font-size: 1.3rem;
            letter-spacing: -0.02em;
        }

        .panel-brand-sub {
            color: rgba(255,255,255,0.6);
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .panel-main {
            position: relative;
            z-index: 2;
        }

        .panel-headline {
            color: #fff;
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.03em;
            margin-bottom: 1rem;
        }

        .panel-headline span {
            color: rgba(255,255,255,0.55);
        }

        .panel-desc {
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
            line-height: 1.65;
            max-width: 300px;
        }

        .panel-features {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        .panel-feature-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            color: rgba(255,255,255,0.8);
            font-size: 0.84rem;
            font-weight: 500;
        }

        .panel-feature-item .fi-icon {
            width: 28px;
            height: 28px;
            background: rgba(255,255,255,0.12);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.9);
            flex-shrink: 0;
        }

        .panel-footer {
            position: relative;
            z-index: 2;
            color: rgba(255,255,255,0.4);
            font-size: 0.75rem;
        }

        /* ─── Right Panel ───────────────────────────────────── */
        .login-panel-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f8fafc;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #f0f0f2;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04);
            padding: 2.5rem 2.25rem;
            width: 100%;
            max-width: 420px;
            animation: slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-card-header {
            margin-bottom: 1.75rem;
        }

        .login-card-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.03em;
            margin: 0 0 0.3rem;
        }

        .login-card-sub {
            color: #94a3b8;
            font-size: 0.86rem;
            margin: 0;
        }

        .form-label-custom {
            font-weight: 600;
            font-size: 0.8rem;
            color: #475569;
            margin-bottom: 0.4rem;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.95rem;
            pointer-events: none;
            transition: color 0.15s;
        }

        .form-control-custom {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            padding: 0.65rem 0.85rem 0.65rem 2.5rem;
            font-size: 0.88rem;
            color: #1e293b;
            font-family: var(--font);
            transition: all 0.2s ease;
            outline: none;
            -webkit-appearance: none;
        }

        .form-control-custom::placeholder {
            color: #cbd5e1;
            font-weight: 400;
        }

        .form-control-custom:focus {
            border-color: #f87171;
            background: #fff;
            box-shadow: 0 0 0 3.5px rgba(185,28,28,0.08);
        }

        .form-control-custom:focus ~ .input-icon {
            color: var(--theme-red);
        }

        .form-control-custom.is-invalid-custom {
            border-color: #f87171;
            background-color: #fff5f5;
        }

        /* Toggle password visibility */
        .toggle-pw {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            color: #94a3b8;
            font-size: 0.95rem;
            transition: color 0.15s;
            line-height: 1;
        }

        .toggle-pw:hover { color: var(--theme-red); }

        .error-msg {
            font-size: 0.78rem;
            color: #dc2626;
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* Remember me */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-check-input-custom {
            width: 16px;
            height: 16px;
            border: 1.5px solid #cbd5e1;
            border-radius: 4px;
            background: #fff;
            cursor: pointer;
            accent-color: var(--theme-red);
        }

        .form-check-label-custom {
            font-size: 0.82rem;
            color: #64748b;
            font-weight: 500;
            cursor: pointer;
        }

        /* Submit Button */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, var(--theme-red) 0%, var(--theme-red-hover) 100%);
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            border: none;
            border-radius: 10px;
            padding: 0.72rem 1rem;
            cursor: pointer;
            font-family: var(--font);
            letter-spacing: -0.01em;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(185, 28, 28, 0.35);
        }

        .btn-login:hover::after { opacity: 1; }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(185, 28, 28, 0.25);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Alert */
        .alert-login-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            color: #166534;
            font-size: 0.83rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        /* Divider */
        .form-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 1.25rem 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-panel-left { display: none; }
            .login-panel-right { padding: 1.5rem; background: #fff; }
            .login-card {
                border: none;
                box-shadow: none;
                padding: 1.5rem 0.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- Left Panel -->
    <div class="login-panel-left">
        <div class="deco-circle deco-circle-1"></div>
        <div class="deco-circle deco-circle-2"></div>
        <div class="deco-circle deco-circle-3"></div>

        <div class="panel-brand">
            <div class="panel-brand-icon">
                <i class="bi bi-router-fill"></i>
            </div>
            <div>
                <div class="panel-brand-text">SIM-ONT</div>
                <div class="panel-brand-sub">Sistem Informasi Manajemen ONT</div>
            </div>
        </div>

        <div class="panel-main">
            <h1 class="panel-headline">
                Kelola Alur ONT<br>
                <span>Gudang & Teknisi</span><br>
                Lebih Efisien.
            </h1>
            <p class="panel-desc">
                Platform internal manajemen perangkat ONT (modem) untuk Telkom Akses. Catat, pantau, dan rekap data secara real-time.
            </p>

            <div class="panel-features">
                <div class="panel-feature-item">
                    <div class="fi-icon"><i class="bi bi-upc-scan"></i></div>
                    Barcode Scanner USB — Auto-Submit Instan
                </div>
                <div class="panel-feature-item">
                    <div class="fi-icon"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                    Import Excel Masal dengan Auto-Detect Merek
                </div>
                <div class="panel-feature-item">
                    <div class="fi-icon"><i class="bi bi-grid-1x2"></i></div>
                    Dashboard & Rekap Performa per Teknisi
                </div>
                <div class="panel-feature-item">
                    <div class="fi-icon"><i class="bi bi-check2-circle"></i></div>
                    Auto-Match Work Order vs Data Gudang
                </div>
            </div>
        </div>

        <div class="panel-footer">
            &copy; {{ date('Y') }} SIM-ONT &bull; Sistem Internal &bull; Telkom Akses
        </div>
    </div>

    <!-- Right Panel -->
    <div class="login-panel-right">
        <div class="login-card">

            <!-- Header -->
            <div class="login-card-header">
                <h2 class="login-card-title">Masuk ke SIM-ONT</h2>
                <p class="login-card-sub">Masukkan kredensial admin Anda untuk melanjutkan.</p>
            </div>

            <!-- Success flash (misal setelah logout) -->
            @if(session('success'))
                <div class="alert-login-success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Login -->
            <form method="POST" action="{{ route('login.post') }}" id="loginForm" novalidate>
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label-custom">Alamat Email</label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope input-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control-custom {{ $errors->has('email') ? 'is-invalid-custom' : '' }}"
                            value="{{ old('email') }}"
                            placeholder="admin@simONT.local"
                            autocomplete="email"
                            autofocus
                            required
                        >
                    </div>
                    @error('email')
                        <div class="error-msg">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label-custom">Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control-custom {{ $errors->has('password') ? 'is-invalid-custom' : '' }}"
                            placeholder="Masukkan password Anda"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-pw" id="togglePw" aria-label="Tampilkan password">
                            <i class="bi bi-eye" id="togglePwIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-msg">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-3 remember-row">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input-custom">
                    <label for="remember" class="form-check-label-custom">Ingat saya di perangkat ini</label>
                </div>

                <div class="form-divider"></div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="btnLogin">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Masuk ke Dashboard
                </button>

            </form>

            <!-- Hint -->
            <p class="text-center mt-3 mb-0" style="font-size:0.75rem; color:#cbd5e1;">
                <i class="bi bi-shield-lock"></i>
                Akses hanya untuk admin yang berwenang
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle password visibility
        const togglePw   = document.getElementById('togglePw');
        const pwInput    = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePwIcon');

        togglePw.addEventListener('click', function () {
            const isPassword = pwInput.type === 'password';
            pwInput.type = isPassword ? 'text' : 'password';
            toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });

        // Loading state on submit
        const loginForm = document.getElementById('loginForm');
        const btnLogin  = document.getElementById('btnLogin');

        loginForm.addEventListener('submit', function () {
            btnLogin.disabled = true;
            btnLogin.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
        });
    </script>

</body>
</html>
