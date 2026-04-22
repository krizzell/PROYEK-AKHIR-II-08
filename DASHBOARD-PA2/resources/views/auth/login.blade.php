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

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-wrapper {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .login-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 50px 40px 40px;
            text-align: center;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #fbb92f 0%, #ffd89b 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
            box-shadow: 0 8px 20px rgba(251, 185, 47, 0.2);
        }

        .login-header h1 {
            font-size: 32px;
            margin: 15px 0 8px;
            font-weight: 700;
            color: #2c3e50;
        }

        .login-header p {
            margin: 0;
            font-size: 14px;
            color: #95a5a6;
            font-weight: 500;
        }

        .login-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .form-control {
            border: none;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: #fbb92f;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(251, 185, 47, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #bdc3c7;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.1);
        }

        .btn-login {
            background: linear-gradient(135deg, #fbb92f 0%, #ffb900 100%);
            border: none;
            color: white;
            padding: 14px 20px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 15px;
            box-shadow: 0 8px 20px rgba(251, 185, 47, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(251, 185, 47, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 24px;
            border: none;
            padding: 14px 16px;
            font-size: 13px;
        }

        .alert-danger {
            background-color: #fff5f5;
            border-color: #fecaca;
            color: #991b1b;
        }

        .alert-success {
            background-color: #f0fdf4;
            border-color: #bcf0da;
            color: #166534;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .login-footer {
            margin-top: 24px;
            padding-top: 24px;
            text-align: center;
            color: #95a5a6;
            font-size: 13px;
        }

        .login-footer p {
            margin: 0;
        }

        .demo-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            padding: 16px;
            border-radius: 10px;
            font-size: 12px;
            color: #1565c0;
            margin-top: 16px;
        }

        .demo-info i {
            margin-right: 6px;
        }

        /* Animation */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container {
            animation: slideIn 0.5s ease;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
            pointer-events: none;
        }

        .input-icon .form-control {
            padding-left: 42px;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <div class="logo-icon">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <h1>TK Swasta Mutiara Balige</h1>
                <p>Admin Dashboard PA-2</p>
            </div>

            <div class="login-body">
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
                            <i class="bi bi-person-circle"></i>
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
                        <div class="input-icon">
                            <i class="bi bi-key"></i>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Masukkan password"
                                   required>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
