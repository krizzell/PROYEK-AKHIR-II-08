<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - TK Swasta Mutiara Balige</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_sekolah_favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_sekolah_favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #F8FAFC;
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #1F2937;
            overflow-y: scroll;
        }

        .container-main {
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR STYLING ===== */
        .sidebar {
            width: 260px;
            background-color: #FAFBFC;
            padding: 30px 0 150px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: -2px 0 8px rgba(0, 0, 0, 0.03);
            border-right: 1px solid #E5E7EB;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 0 25px 15px;
            text-align: center;
            margin-bottom: 15px;
        }

        .sidebar-header .logo-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 5px;
            letter-spacing: -0.3px;
        }
        
        .sidebar-header .logo-title span {
            color: #FF7A00;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 0 15px;
            margin-bottom: 30px;
        }

        .nav-menu .nav-item {
            position: relative;
        }

        .nav-menu .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: #6B7280;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-menu .nav-link i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .nav-menu .nav-link:hover {
            background-color: #F3F4F6;
            color: #FF7A00;
        }

        .nav-menu .nav-link.active {
            background: #FFF7F0;
            color: #FF7A00;
            font-weight: 600;
            position: relative;
        }

        .nav-menu .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: #FF7A00;
            border-radius: 0 3px 3px 0;
        }

        .admin-section-title {
            padding: 15px 25px 10px;
            font-size: 11px;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .admin-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #6B7280, transparent);
            margin: 15px 0;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            padding: 0 15px;
        }

        .sidebar-footer::before {
            content: none;
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px;
            background-color: transparent;
            color: #DC2626;
            border: 1px solid #FEE2E2;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .logout-btn:hover {
            background-color: #FEE2E2;
            color: #DC2626;
        }

        .sidebar-footer-buttons {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .sidebar-footer-buttons form {
            width: 100%;
        }

        .sidebar-footer-btn {
            width: 100%;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 10px 14px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 13.5px;
            line-height: 1;
            text-decoration: none;
            background-color: transparent;
        }

        .sidebar-footer-btn i {
            font-size: 14px;
            line-height: 1;
        }

        .sidebar-footer-btn.change-password {
            color: #1D4ED8;
            border-color: #BFDBFE;
        }

        .sidebar-footer-btn.change-password:hover {
            background-color: #EFF6FF;
            border-color: #93C5FD;
            color: #1E40AF;
        }

        .sidebar-footer-btn.logout {
            color: #DC2626;
            border-color: #FECACA;
        }

        .sidebar-footer-btn.logout:hover {
            background-color: #FEF2F2;
            border-color: #FCA5A5;
            color: #B91C1C;
        }

        .sidebar-footer-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
        }

        /* ===== MAIN CONTENT STYLING ===== */
        .main-content {
            flex: 1;
            margin-left: 260px;
            background-color: transparent;
            display: flex;
            flex-direction: column;
        }

        .top-navbar {
            background: rgba(248, 250, 252, 0.95);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 14px 35px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            border-bottom: 1px solid #E5E7EB;
            position: sticky;
            top: 0;
            z-index: 50;
        }



        .navbar-right {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-left: 30px;
        }



        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #92400E;
            font-size: 15px;
            border: 1px solid #FCD34D;
        }

        .user-info-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-info-text .user-name {
            font-weight: 600;
            color: #111827;
            font-size: 13px;
        }

        .user-info-text .user-role {
            font-size: 11px;
            color: #6B7280;
            font-weight: 500;
        }

        /* ===== PAGE CONTENT ===== */
        .page-content {
            padding: 35px;
            background: linear-gradient(180deg, #F8FAFC 0%, #FAFBFC 100%);
            min-height: calc(100vh - 100px);
        }

        .page-header {
            margin-bottom: 35px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            font-size: 14px;
            color: #9CA3AF;
            font-weight: 400;
        }

        /* ===== ALERTS ===== */
        .alert {
            border: none;
            border-radius: 8px;
            border-left: 3px solid;
            margin-bottom: 20px;
            animation: slideIn 0.3s ease;
            font-size: 14px;
        }

        .alert-success {
            background-color: #ECFDF5;
            border-left-color: #10B981;
            color: #065F46;
        }

        .alert-danger {
            background-color: #FEF2F2;
            border-left-color: #DC2626;
            color: #7C2D12;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #6B7280;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #6B7280;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.active {
                left: 0;
            }
        }

        /* ===== TABLES & CARDS ===== */
        .table-container {
            background: #FFFFFF;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .table thead {
            background: #F9FAFB;
            border-bottom: 1px solid #E5E7EB;
        }

        .table tbody tr:hover {
            background-color: #F9FAFB;
        }

        .filter-section {
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
        }

        .filter-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .filter-group input,
        .filter-group select {
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
            color: #111827;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            border-color: #FF7A00;
            background-color: #FFFBF5;
        }

        .btn-filter {
            background: #FF7A00;
            color: #FFFFFF;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-filter:hover {
            background: #E65E00;
            transform: translateY(-1px);
        }

        .btn-reset {
            background: transparent;
            color: #6B7280;
            border: 1px solid #E5E7EB;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-reset:hover {
            background: #F9FAFB;
            border-color: #D1D5DB;
        }

        /* ===== BUTTON OVERRIDES ===== */
        .btn-primary {
            background-color: #FF7A00;
            border-color: #FF7A00;
            color: #FFFFFF;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: #E65E00 !important;
            border-color: #E65E00 !important;
            color: #FFFFFF !important;
        }
        .text-primary {
            color: #FF7A00 !important;
        }
        .bg-primary {
            background-color: #FF7A00 !important;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-main">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="logo-title">TK Swasta Mutiara <span>Balige</span></div>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2"></i> Dashboard
                    </a>
                </li>   
                <li class="nav-item">
                    <a href="{{ route('perkembangan.index') }}" class="nav-link {{ Route::currentRouteName() == 'perkembangan.index' ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Perkembangan Siswa
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pengumuman.index') }}" class="nav-link {{ Route::currentRouteName() == 'pengumuman.index' ? 'active' : '' }}">
                        <i class="bi bi-megaphone"></i> Pengumuman Sekolah
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('siswa.index') }}" class="nav-link {{ Route::currentRouteName() == 'siswa.index' ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Data Siswa
                    </a>
                </li>

                @if (session('role') === 'guru')
                    @if (session('is_super_admin'))
                        <li class="nav-item">
                            <a href="{{ route('guru.index') }}" class="nav-link {{ Route::currentRouteName() == 'guru.index' ? 'active' : '' }}">
                                <i class="bi bi-person-workspace"></i> Data Guru
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('akun.index') }}" class="nav-link {{ Route::currentRouteName() == 'akun.index' ? 'active' : '' }}">
                                <i class="bi bi-key"></i> Kelola Akun
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('kelas.index') }}" class="nav-link {{ Route::currentRouteName() == 'kelas.index' ? 'active' : '' }}">
                            <i class="bi bi-building"></i> Data Kelas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('tagihan.index') }}" class="nav-link {{ Route::currentRouteName() == 'tagihan.index' ? 'active' : '' }}">
                            <i class="bi bi-receipt"></i> Tagihan SPP
                        </a>
                    </li>
                @endif

                @if (session('role') === 'guru')
                    <li class="nav-item">
                        <a href="{{ route('transfer-siswa.index') }}" class="nav-link {{ str_starts_with(Route::currentRouteName(), 'transfer-siswa.') ? 'active' : '' }}">
                            <i class="bi bi-arrow-left-right"></i> Perpindahan Kelas
                        </a>
                    </li>
                @endif
            </ul>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="sidebar-footer-buttons">
                    <a href="{{ route('profile.edit-password') }}" class="sidebar-footer-btn change-password">
                        <i class="bi bi-key"></i> Ubah Password
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="sidebar-footer-btn logout">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <div class="navbar-right" style="margin-left: auto;">

                    <div class="user-profile-menu" style="position: relative;">
                        <div class="user-profile" style="display: flex; align-items: center; gap: 12px; padding: 8px 12px; border-radius: 8px;">
                            <div class="user-avatar">{{ strtoupper(substr(session('username'), 0, 1)) }}</div>
                            <div class="user-info-text">
                                <div class="user-name">{{ session('username') }}</div>
                                <div class="user-role">{{ ucfirst(session('role_display', session('role'))) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="page-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    
    <script>
        // Handle delete confirmation with SweetAlert2
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('[data-delete-btn]');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const itemName = this.getAttribute('data-item-name') || 'item ini';
                    
                    Swal.fire({
                        title: 'Hapus ' + itemName + '?',
                        text: 'Data yang dihapus tidak dapat dipulihkan',
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#EF4444',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        backdrop: true,
                        allowOutsideClick: true,
                        allowEscapeKey: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });

        // Check session status setiap 1 menit
        setInterval(function() {
            fetch('{{ route("dashboard") }}', {
                method: 'HEAD',
                credentials: 'same-origin'
            }).then(response => {
                if (response.status === 401 || response.status === 302) {
                    // Session expired atau redirect to login
                    window.location.href = '{{ route("login") }}';
                }
            }).catch(err => {
                console.log('Session check failed');
            });
        }, 60000); // Check setiap 1 menit
    </script>
    
    @yield('scripts')
</body>
</html>
