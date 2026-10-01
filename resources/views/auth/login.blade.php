<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ONT-TRACK</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/ont-track.css') }}">
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="text-center mb-4">
            <h1 class="login-brand">ONT-TRACK</h1>
            <p class="fs-12 text-muted-2 mt-1 mb-0">Sistem Manajemen ONT dan Work Order Berbasis Web</p>
            <div class="mt-4 text-start">
                <h2 class="fs-6 fw-bold text-dark mb-0">Masuk ke Sistem</h2>
                <p class="fs-12 text-muted-2 mt-1 mb-0">Gunakan akun yang diberikan oleh administrator</p>
            </div>
        </div>

        @if(session('success'))
            <div class="flash flash-success mb-3"><i class="bi bi-check-circle-fill"></i><div>{{ session('success') }}</div></div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" id="loginForm" class="d-flex flex-column gap-3" novalidate>
            @csrf

            <div>
                <label for="email" class="lbl">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus autocomplete="username"
                       placeholder="Masukkan email"
                       class="form-control login-input @error('email') is-invalid @enderror">
            </div>

            <div>
                <label for="password" class="lbl">Password</label>
                <div class="pass-wrap">
                    <input type="password" id="password" name="password" autocomplete="current-password"
                           placeholder="Masukkan password"
                           class="form-control login-input @error('password') is-invalid @enderror">
                    <button type="button" class="pass-toggle" id="passToggle" aria-label="Tampilkan password">
                        <i class="bi bi-eye-slash" id="passIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-check m-0">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" style="accent-color: var(--brand);">
                <label class="form-check-label fs-12 text-muted-2" for="remember">Ingat saya</label>
            </div>

            @if($errors->any())
                <div class="flash flash-error">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <button type="submit" id="loginBtn" class="btn btn-brand btn-login mt-1">Masuk</button>
        </form>
    </div>
</div>

<script>
    (function () {
        var pass = document.getElementById('password');
        var icon = document.getElementById('passIcon');
        document.getElementById('passToggle').addEventListener('click', function () {
            var show = pass.type === 'password';
            pass.type = show ? 'text' : 'password';
            icon.className = show ? 'bi bi-eye' : 'bi bi-eye-slash';
        });

        document.getElementById('loginForm').addEventListener('submit', function () {
            var btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spin"></span>Memverifikasi...';
        });
    })();
</script>
</body>
</html>
