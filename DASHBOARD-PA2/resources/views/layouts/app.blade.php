<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - School Monitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #FFFFFF;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-main {
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR STYLING ===== */
        .sidebar {
            width: 280px;
            background-color: #FFFFFF;
            padding: 30px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
            border-right: 1px solid #FFEDE3;
        }

        .sidebar-header {
            padding: 0 25px 40px;
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar-header .logo-title {
            font-size: 22px;
            font-weight: 700;
            color: #F97316;
            margin-bottom: 5px;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
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
            padding: 12px 18px;
            color: #6B7280;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
        }

        .nav-menu .nav-link i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .nav-menu .nav-link:hover {
            background-color: #FFEDE3;
            color: #F97316;
            padding-left: 22px;
        }

        .nav-menu .nav-link.active {
            background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);
            color: #FFFFFF;
            font-weight: 600;
            position: relative;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .nav-menu .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background: #FFFFFF;
            border-radius: 0 4px 4px 0;
        }

        .admin-section-title {
            padding: 15px 25px 10px;
            font-size: 11px;
            font-weight: 700;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .admin-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #9CA3AF, transparent);
            margin: 15px 0;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            padding: 0 15px;
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            background-color: #FFEBEE;
            color: #EF4444;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .logout-btn:hover {
            background-color: #FFCDD2;
            color: #DC2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.2);
        }

        /* ===== MAIN CONTENT STYLING ===== */
        .main-content {
            flex: 1;
            margin-left: 280px;
            background-color: #FFFFFF;
        }

        .top-navbar {
            background: white;
            padding: 20px 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
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
            background: linear-gradient(135deg, #F97316 0%, #FFEDE3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #F97316;
            font-size: 16px;
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.2);
        }

        .user-info-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-info-text .user-name {
            font-weight: 600;
            color: #000000;
            font-size: 13px;
        }

        .user-info-text .user-role {
            font-size: 11px;
            color: #9CA3AF;
            font-weight: 500;
        }

        .user-dropdown-menu {
            animation: slideDown 0.2s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item-link {
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .dropdown-item-link:hover {
            background-color: #FFF8F4 !important;
            padding-left: 20px;
        }

        .dropdown-item-link:last-child:hover {
            background-color: #fff5f5 !important;
        }

        /* ===== PAGE CONTENT ===== */
        .page-content {
            padding: 35px;
        }

        .page-header {
            margin-bottom: 35px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #1A1A2E;
            margin-bottom: 8px;
        }

        .page-subtitle {
            font-size: 14px;
            color: #9CA3AF;
            font-weight: 500;
        }

        /* ===== ALERTS ===== */
        .alert {
            border: none;
            border-radius: 10px;
            border-left: 4px solid;
            margin-bottom: 20px;
            animation: slideIn 0.3s ease;
        }

        .alert-success {
            background-color: #F0FDF4;
            border-left-color: #22C55E;
            color: #15803D;
        }

        .alert-danger {
            background-color: #FEF2F2;
            border-left-color: #EF4444;
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
            background: #9CA3AF;
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
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-main">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="logo-title">TK Swasta Mutiara Balige</div>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('perkembangan.index') }}" class="nav-link {{ Route::currentRouteName() == 'perkembangan.index' ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Data Perkembangan Anak
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
                                <i class="bi bi-people-fill"></i> Data Guru
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('akun.index') }}" class="nav-link {{ Route::currentRouteName() == 'akun.index' ? 'active' : '' }}">
                                <i class="bi bi-key-fill"></i> Kelola Akun
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('kelas.index') }}" class="nav-link {{ Route::currentRouteName() == 'kelas.index' ? 'active' : '' }}">
                            <i class="bi bi-building"></i> Kelas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('tagihan.index') }}" class="nav-link {{ Route::currentRouteName() == 'tagihan.index' ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text"></i> Tagihan
                        </a>
                    </li>
                @endif
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <div class="navbar-right" style="margin-left: auto;">

                    <div class="user-profile-menu" style="position: relative;">
                        <div class="user-profile" onclick="toggleUserMenu()" style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 8px 12px; border-radius: 8px; transition: all 0.3s ease;">
                            <div class="user-avatar">{{ strtoupper(substr(session('username'), 0, 1)) }}</div>
                            <div class="user-info-text">
                                <div class="user-name">{{ session('username') }}</div>
                                <div class="user-role">{{ ucfirst(session('role')) }}</div>
                            </div>
                            <i class="bi bi-chevron-down" style="margin-left: 5px; font-size: 12px;"></i>
                        </div>

                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="user-dropdown-menu" style="position: absolute; top: 100%; right: 0; background: white; border-radius: 8px; min-width: 220px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 1000; display: none; margin-top: 5px;">
                            <a href="{{ route('profile.edit-password') }}" class="dropdown-item-link" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: #6B7280; text-decoration: none; transition: all 0.3s ease;">
                                <i class="bi bi-key" style="color: #2196F3;"></i> Ubah Password
                            </a>
                            
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="dropdown-item-link" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: #EF4444; text-decoration: none; width: 100%; border: none; background: none; cursor: pointer; transition: all 0.3s ease;">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
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
        // Toggle user dropdown menu
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userDropdown = document.getElementById('userDropdown');
            const userProfile = event.target.closest('.user-profile');
            
            if (!userProfile && userDropdown.style.display === 'block') {
                userDropdown.style.display = 'none';
            }
        });

        // Add hover effect to dropdown items
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownItems = document.querySelectorAll('.dropdown-item-link');
            dropdownItems.forEach(item => {
                item.addEventListener('mouseover', function() {
                    this.style.backgroundColor = '#FFF8F4';
                });
                item.addEventListener('mouseout', function() {
                    this.style.backgroundColor = 'transparent';
                });
            });
        });

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
                        cancelButtonColor: '#9CA3AF',
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
