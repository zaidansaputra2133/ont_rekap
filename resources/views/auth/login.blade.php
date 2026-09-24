<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login — ONT-TRACK</title>
    <meta name="description" content="Halaman login admin SIM-ONT. Sistem Manajemen ONT dan Work Order Berbasis Web.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red-dark:  #7f1d1d;
            --red-main:  #b91c1c;
            --red-mid:   #c91c1c;
            --red-light: #dc2626;
            --font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            font-family: var(--font);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--red-main);
            position: relative;
            overflow: hidden;
            letter-spacing: -0.01em;
        }

        /* ── Abstract Wave Background ─────────────────────── */
        .bg-waves {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        /* Large wave top-right */
        .wave-1 {
            position: absolute;
            top: -120px;
            right: -100px;
            width: 520px;
            height: 520px;
            background: radial-gradient(ellipse at center, rgba(255,255,255,0.07) 0%, transparent 70%);
            border-radius: 50% 30% 60% 40% / 50% 60% 40% 50%;
            transform: rotate(-20deg);
        }

        /* Medium wave bottom-left */
        .wave-2 {
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 380px;
            height: 380px;
            background: radial-gradient(ellipse at center, rgba(0,0,0,0.12) 0%, transparent 70%);
            border-radius: 60% 40% 30% 70% / 40% 50% 60% 50%;
            transform: rotate(15deg);
        }

        /* Small accent top-left */
        .wave-3 {
            position: absolute;
            top: 40px;
            left: -60px;
            width: 240px;
            height: 240px;
            background: rgba(0,0,0,0.08);
            border-radius: 40% 60% 70% 30% / 60% 40% 50% 50%;
            transform: rotate(-10deg);
        }

        /* Small accent bottom-right */
        .wave-4 {
            position: absolute;
            bottom: 60px;
            right: -40px;
            width: 180px;
            height: 180px;
            background: rgba(255,255,255,0.05);
            border-radius: 50% 30% 40% 60% / 40% 60% 50% 50%;
        }

        /* Diagonal dark band (matches design) */
        .wave-band {
            position: absolute;
            bottom: -200px;
            left: -200px;
            width: 800px;
            height: 600px;
            background: rgba(0,0,0,0.18);
            border-radius: 50% 40% 30% 50% / 40% 60% 50% 40%;
            transform: rotate(-15deg);
        }

        /* ── Login Card ───────────────────────────────────── */
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 1.25rem;
            animation: cardIn 0.45s cubic-bezier(0.34, 1.4, 0.64, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(28px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 2.25rem 2rem 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25), 0 4px 16px rgba(0,0,0,0.15);
        }

        /* ── Brand Header ─────────────────────────────────── */
        .brand-block {
            text-align: center;
            margin-bottom: 1.6rem;
        }

        .brand-name {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--red-main);
            letter-spacing: -0.03em;
            line-height: 1;
            margin-bottom: 0.35rem;
        }

        .brand-name span { color: var(--red-light); }

        .brand-sub {
            font-size: 0.78rem;
            color: #94a3b8;
            font-weight: 400;
        }

        .brand-sub strong { color: var(--red-main); font-weight: 600; }

        /* ── Section Heading ──────────────────────────────── */
        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.2rem;
            letter-spacing: -0.02em;
        }

        .section-sub {
            font-size: 0.78rem;
            color: #94a3b8;
            margin-bottom: 1.4rem;
        }

        /* ── Form Fields ──────────────────────────────────── */
        .field-group {
            margin-bottom: 1rem;
        }

        .field-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            color: #475569;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
        }

        .field-wrap {
            position: relative;
        }

        .field-input {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            background: #f8fafc;
            padding: 0.65rem 0.9rem;
            font-size: 0.88rem;
            color: #1e293b;
            font-family: var(--font);
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
            -webkit-appearance: none;
        }

        .field-input::placeholder {
            color: #b0bec5;
            font-weight: 400;
        }

        .field-input:focus {
            border-color: #f87171;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(185,28,28,0.09);
        }

        .field-input.is-error {
            border-color: #f87171;
            background: #fff5f5;
        }

        /* Password field has padding-right for toggle button */
        .field-input.has-toggle {
            padding-right: 2.6rem;
        }

        .toggle-pw {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            color: #94a3b8;
            font-size: 1rem;
            line-height: 1;
            transition: color 0.15s;
        }

        .toggle-pw:hover { color: var(--red-main); }

        .error-msg {
            font-size: 0.76rem;
            color: #dc2626;
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* ── Submit Button ────────────────────────────────── */
        .btn-masuk {
            width: 100%;
            background: var(--red-main);
            color: #fff;
            font-weight: 700;
            font-size: 0.92rem;
            border: none;
            border-radius: 9px;
            padding: 0.72rem 1rem;
            cursor: pointer;
            font-family: var(--font);
            letter-spacing: -0.01em;
            transition: background 0.18s, transform 0.15s, box-shadow 0.18s;
            margin-top: 0.5rem;
        }

        .btn-masuk:hover {
            background: #991b1b;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(185,28,28,0.35);
        }

        .btn-masuk:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .btn-masuk:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* ── Alert Flash ──────────────────────────────────── */
        .alert-success-custom {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 9px;
            padding: 0.6rem 0.85rem;
            color: #166534;
            font-size: 0.82rem;
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin-bottom: 1.1rem;
        }

        /* ── Footer note ──────────────────────────────────── */
        .login-footer-note {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.72rem;
            color: rgba(255,255,255,0.55);
        }

        /* ── Responsive ───────────────────────────────────── */
        @media (max-width: 480px) {
            .login-card { padding: 1.75rem 1.35rem 1.5rem; }
            .brand-name { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <!-- Abstract wave background shapes -->
    <div class="bg-waves">
        <div class="wave-band"></div>
        <div class="wave-1"></div>
        <div class="wave-2"></div>
        <div class="wave-3"></div>
        <div class="wave-4"></div>
    </div>

    <!-- Login Card -->
    <div class="login-wrapper">
        <div class="login-card">

            <!-- Brand -->
            <div class="brand-block">
                <div class="brand-name">ONT-TRACK</div>
                <div class="brand-sub">
                    Sistem Manajemen <strong>ONT</strong> dan Work Order Berbasis Web
                </div>
            </div>

            <!-- Section Heading -->
            <div class="section-title">Masuk ke Sistem</div>
            <div class="section-sub">Gunakan akun yang diberikan oleh administrator</div>

            <!-- Flash success -->
            @if(session('success'))
                <div class="alert-success-custom">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.post') }}" id="loginForm" novalidate>
                @csrf

                <!-- Username / Email -->
                <div class="field-group">
                    <label for="email" class="field-label">Username</label>
                    <div class="field-wrap">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="field-input {{ $errors->has('email') ? 'is-error' : '' }}"
                            value="{{ old('email') }}"
                            placeholder="Masukkan username"
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
                <div class="field-group">
                    <label for="password" class="field-label">Password</label>
                    <div class="field-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="field-input has-toggle {{ $errors->has('password') ? 'is-error' : '' }}"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-pw" id="togglePw" aria-label="Tampilkan password">
                            <i class="bi bi-eye-slash" id="togglePwIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-msg">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-masuk" id="btnLogin">
                    Masuk
                </button>

            </form>

        </div>

        <!-- Footer note -->
        <div class="login-footer-note">
            <i class="bi bi-shield-lock"></i>
            Akses hanya untuk admin yang berwenang &bull; &copy; {{ date('Y') }} SIM-ONT
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePw   = document.getElementById('togglePw');
        const pwInput    = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePwIcon');

        togglePw.addEventListener('click', function () {
            const isPassword = pwInput.type === 'password';
            pwInput.type = isPassword ? 'text' : 'password';
            toggleIcon.className = isPassword ? 'bi bi-eye' : 'bi bi-eye-slash';
        });

        // Loading state on submit
        const loginForm = document.getElementById('loginForm');
        const btnLogin  = document.getElementById('btnLogin');

        loginForm.addEventListener('submit', function () {
            btnLogin.disabled = true;
            btnLogin.innerHTML = '<span style="display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,0.4);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite;vertical-align:middle;margin-right:6px;"></span>Memproses...';
        });
    </script>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

</body>
</html>
