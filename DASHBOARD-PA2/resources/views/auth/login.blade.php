<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - School Monitor</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* ===== BODY ===== */
body {
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #eaf0fb 0%, #f8fbff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
}

/* ===== CONTAINER ===== */
.login-page {
    width: 100%;
    max-width: 1080px;
    min-height: 540px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: #ffffff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 24px 70px rgba(15, 23, 42, 0.14);
}

/* ===== LEFT SIDE ===== */
.login-left {
    position: relative;
    background:
        radial-gradient(circle at 45% 45%, rgba(251, 185, 47, 0.14), transparent 28%),
        radial-gradient(circle at 62% 48%, rgba(59, 130, 246, 0.10), transparent 34%),
        #ffffff;
    border-right: 1px solid #e5e7eb;
    overflow: hidden;
}

.left-content {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px;
    color: #1e293b;
    text-align: center;
}

/* ===== LOGO FIX (INI YANG PENTING) ===== */
.left-logo {
    margin-bottom: 20px;
    text-align: center;
}

.left-logo img {
    width: 185px; 
    height: auto;
    object-fit: contain;
}

/* ===== TEXT LEFT ===== */
.left-content h2 {
    font-size: 30px;
    font-weight: 800;
    margin-bottom: 15px;
}

.left-content p {
    font-size: 18px;
    color: #64748b;
    max-width: 340px;
    margin: 0 auto;
}

/* ===== RIGHT SIDE ===== */
.login-right {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 54px 64px;
    background: #ffffff;
}

.login-card {
    width: 100%;
    max-width: 390px;
}

/* ===== HEADER ===== */
.brand {
    margin-bottom: 34px;
}

.logo-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #fbb92f 0%, #ffd166 100%);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: #fff;
    box-shadow: 0 14px 30px rgba(251, 185, 47, 0.28);
    margin-bottom: 22px;
}

.brand h1 {
    font-size: 30px;
    font-weight: 800;
    color: #172033;
    margin-bottom: 8px;
}

.brand p {
    color: #64748b;
    font-size: 14px;
}

/* ===== FORM ===== */
.form-group {
    margin-bottom: 22px;
}

.form-label {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 9px;
    display: flex;
    align-items: center;
    gap: 7px;
}

.input-icon {
    position: relative;
}

/* ICON KIRI (KUNCI) */
.input-icon-left {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

/* ICON KANAN (MATA) */
.toggle-password {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #94a3b8;
    font-size: 18px;
}

.form-control {
    height: 52px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 12px 16px 12px 46px;
    font-size: 14px;
}

.form-control:focus {
    background: #ffffff;
    border-color: #fbb92f;
    box-shadow: 0 0 0 4px rgba(251, 185, 47, 0.14);
}

/* ===== BUTTON ===== */
.btn-login {
    width: 100%;
    height: 52px;
    border: none;
    border-radius: 14px;
    background: linear-gradient(135deg, #fbb92f 0%, #f59e0b 100%);
    color: white;
    font-weight: 700;
}

/* ===== FOOTER ===== */
.login-footer {
    margin-top: 28px;
    color: #94a3b8;
    font-size: 12.5px;
    text-align: center;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 992px) {
    .login-page {
        grid-template-columns: 1fr;
        max-width: 430px;
    }

    .login-left {
        display: none;
    }

    .login-right {
        padding: 38px 28px;
    }
}

/* ===== PASSWORD TOGGLE ===== */
.password-wrapper {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #94a3b8;
    font-size: 18px;
}

.toggle-password:hover {
    color: #f59e0b;
}

/* Biar icon tidak nabrak text */
.form-control {
    height: 52px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 12px 45px 12px 46px; 
    /* kiri 46px buat icon kunci */
    /* kanan 45px buat icon mata */
    font-size: 14px;
}



    </style>
</head>
<body>
    <main class="login-page">
        <section class="login-left">
            <div class="left-content">
                <div>
                    <!-- LOGO -->
                    <div class="left-logo">
                        <img src="{{ asset('images/logo_tk_mutiara.png') }}" alt="Logo Sekolah">
                    </div>

                    <h2>Selamat datang di School Monitor</h2>
                    <p>Akses dashboard untuk mengelola data dan aktivitas sekolah secara efisien.</p>
                </div>
            </div>
        </section>

        <section class="login-right">
            <div class="login-card">
                <div class="brand">
                    <div class="logo-icon">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <h1>TK Swasta Mutiara Balige</h1>
                    <p>Masuk ke Admin Dashboard PA-2</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" novalidate>
                    @csrf

                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="bi bi-person"></i>
                            Username
                        </label>
                        <div class="input-icon">
                            <i class="bi bi-person-circle input-icon-left"></i>
                            <input type="text"
                                   class="form-control @error('username') is-invalid @enderror"
                                   id="username"
                                   name="username"
                                   value="{{ old('username') }}"
                                   placeholder="Masukkan username"
                                   required autofocus>
                        </div>
                        @error('username')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i>
                            Password
                        </label>
                       <div class="input-icon password-wrapper">
                            <!-- icon kiri -->
                            <i class="bi bi-key input-icon-left"></i>

                            <input type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="password" 
                                name="password" 
                                placeholder="Masukkan password"
                                required>

                            <!-- icon kanan -->
                            <i class="bi bi-eye toggle-password" id="togglePassword"></i>
                        </div>
                        @error('password')
                            <div class="error-message">
                                <i class="bi bi-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </button>
                </form>

                <div class="demo-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>Demo:</strong> Hubungi administrator untuk mendapatkan akun Anda
                </div>

                <div class="login-footer">
                    <p>&copy; 2026 School Monitor - Early Childhood Education Dashboard</p>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        // Toggle type
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Ganti icon
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });
</script>
</body>
</html>