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
            padding: 30px 0 18px 0;
            position: fixed;
            height: 100vh;
            overflow: hidden;
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
            flex-shrink: 0;
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
            margin-bottom: 0;
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: #CBD5E1 transparent;
        }

        .nav-menu::-webkit-scrollbar {
            width: 6px;
        }

        .nav-menu::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 999px;
        }

        .nav-menu::-webkit-scrollbar-track {
            background: transparent;
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
            position: static;
            flex-shrink: 0;
            padding: 14px 15px 0;
            margin-top: 14px;
            background-color: #FAFBFC;
            border-top: 1px solid #E5E7EB;
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
            line-height: 1.2;
            text-decoration: none;
            background-color: transparent;
            white-space: normal;
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

        .mobile-menu-toggle {
            width: 42px;
            height: 42px;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            background: #FFFFFF;
            color: #111827;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: all 0.2s ease;
        }

        .mobile-menu-toggle:hover {
            border-color: #FF7A00;
            color: #FF7A00;
            background: #FFF7F0;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, 0.45);
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 900;
        }

        body.sidebar-open {
            overflow: hidden;
        }

        body.sidebar-open .sidebar-overlay {
            opacity: 1;
            visibility: visible;
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
            cursor: default;
            user-select: none;
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

        /* ===== PREMIUM PAGINATION ===== */
        nav[aria-label="Pagination Navigation"],
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.35rem;
            margin: 0;
            padding: 0.4rem;
        }

        .pagination-wrapper {
            margin-top: 1.25rem;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            width: 100%;
        }

        .pagination-wrapper nav {
            width: 100%;
        }

        .pagination-wrapper nav > div {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .pagination-wrapper .small,
        .pagination-wrapper p {
            margin: 0;
            color: #64748B;
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 0.92rem;
            font-weight: 500;
        }

        .pagination-wrapper p span,
        .pagination-wrapper p strong {
            color: #111827;
            font-weight: 700;
        }

        .pagination .page-item {
            margin: 0;
        }

        .pagination .page-link {
            min-width: 38px;
            height: 38px;
            padding: 0 0.75rem;
            border: 1px solid #E5E7EB;
            border-radius: 10px !important;
            background: #FFFFFF;
            color: #6B7280;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            line-height: 1;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            border-color: rgba(255, 122, 0, 0.35);
            background: #FFF7F0;
            color: #FF7A00;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(255, 122, 0, 0.12);
        }

        .pagination .page-link:focus {
            border-color: #FF7A00;
            color: #FF7A00;
            box-shadow: 0 0 0 4px rgba(255, 122, 0, 0.14);
        }

        .pagination .page-item.active .page-link {
            border-color: #FF7A00;
            background: linear-gradient(135deg, #FF8A1F 0%, #FF6A00 100%);
            color: #FFFFFF;
            box-shadow: 0 10px 22px rgba(255, 122, 0, 0.24);
        }

        .pagination .page-item.disabled .page-link {
            border-color: #EEF2F7;
            background: #F8FAFC;
            color: #CBD5E1;
            box-shadow: none;
            cursor: not-allowed;
            transform: none;
        }

        /* ===== RESPONSIVE DASHBOARD SHELL ===== */
        @media (max-width: 1200px) {
            .page-content {
                padding: 28px;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                left: -285px;
                width: 280px;
                z-index: 1000;
                transition: left 0.25s ease;
                box-shadow: 8px 0 24px rgba(15, 23, 42, 0.12);
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .top-navbar {
                justify-content: space-between;
                padding: 12px 20px;
                z-index: 800;
            }

            .mobile-menu-toggle {
                display: inline-flex;
            }

            .navbar-right {
                gap: 12px;
                margin-left: 0;
            }

            .page-content {
                padding: 24px 20px;
            }

            .page-header {
                flex-wrap: wrap;
                align-items: flex-start !important;
                gap: 1rem;
            }

            .page-header h1,
            .page-title {
                font-size: 1.55rem !important;
                line-height: 1.25;
            }

            .dashboard-grid,
            .form-grid,
            .info-grid,
            .detail-grid,
            .details-grid,
            .stats-grid,
            .student-grid,
            .class-grid,
            .selection-grid,
            .summary-grid,
            .media-grid,
            .category-grid,
            .criteria-grid,
            .teacher-toolbar,
            .transfer-grid,
            .request-grid {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 768px) {
            .top-navbar {
                padding: 10px 16px;
            }

            .user-profile {
                gap: 8px !important;
                padding: 6px 0 !important;
            }

            .user-avatar {
                width: 36px;
                height: 36px;
                font-size: 14px;
                flex: 0 0 auto;
            }

            .user-info-text .user-name {
                max-width: 135px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .user-info-text .user-role {
                max-width: 135px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .page-content {
                padding: 20px 14px;
            }

            .page-header {
                margin-bottom: 1.35rem !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                justify-content: flex-start !important;
                flex-wrap: wrap !important;
                gap: 0.85rem !important;
                width: 100% !important;
            }

            .page-header h1,
            .page-title {
                font-size: 1.35rem !important;
                max-width: 100% !important;
                min-width: 0 !important;
                flex-shrink: 1 !important;
                white-space: normal !important;
                overflow-wrap: anywhere !important;
            }

            .page-header > div,
            .filter-actions,
            .bulk-actions,
            .akun-toolbar,
            .pengumuman-toolbar {
                width: 100%;
                flex-wrap: wrap;
            }

            .page-header > div {
                display: flex !important;
                gap: 0.75rem !important;
            }

            .page-header a,
            .page-header button,
            .btn-add,
            .btn-secondary,
            .btn-filter,
            .btn-reset,
            .btn-bulk-delete,
            .btn-bulk-clear {
                min-height: 42px;
                max-width: 100% !important;
                white-space: normal !important;
            }

            .page-header .btn-add,
            .page-header .btn-secondary {
                width: 100% !important;
                justify-content: center !important;
                flex-shrink: 1 !important;
                text-align: center !important;
            }

            .akun-toolbar,
            .pengumuman-toolbar {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.85rem !important;
            }

            .search-wrapper,
            .student-search-wrapper {
                width: 100% !important;
                max-width: 100% !important;
            }

            .bulk-actions {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                padding: 1rem !important;
            }

            .bulk-actions > div:last-child {
                width: 100% !important;
                display: flex !important;
                flex-direction: column !important;
            }

            .btn-bulk-delete,
            .btn-bulk-clear {
                width: 100% !important;
                justify-content: center !important;
            }

            .filter-section {
                padding: 1rem !important;
                border-radius: 10px !important;
            }

            .filter-row {
                grid-template-columns: 1fr !important;
                gap: 0.85rem !important;
            }

            .dashboard-grid,
            .form-grid,
            .info-grid,
            .detail-grid,
            .details-grid,
            .stats-grid,
            .student-grid,
            .class-grid,
            .selection-grid,
            .summary-grid,
            .media-grid,
            .category-grid,
            .criteria-grid,
            .teacher-toolbar,
            .transfer-grid,
            .request-grid,
            .cards-grid,
            .students-grid {
                display: grid !important;
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }

            .filter-group,
            .form-group,
            .input-group-custom {
                min-width: 0 !important;
                width: 100% !important;
            }

            .filter-group input,
            .filter-group select,
            .form-group input,
            .form-group select,
            .form-group textarea,
            .student-search-input {
                width: 100% !important;
                max-width: 100% !important;
            }

            .table-container {
                max-width: 100% !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                border-radius: 10px !important;
            }

            .table-container .table {
                min-width: 760px;
            }

            .table th,
            .table td {
                padding: 0.85rem !important;
                font-size: 0.86rem !important;
                white-space: nowrap;
            }

            .action-buttons {
                flex-wrap: nowrap !important;
                width: max-content;
            }

            .pagination {
                flex-wrap: wrap;
                gap: 0.25rem;
            }

            .card,
            .form-card,
            .detail-card,
            .info-card,
            .content-card,
            .confirmation-card,
            .dashboard-main,
            .activities-wrapper {
                max-width: 100% !important;
            }

            .form-actions,
            .modal-actions,
            .action-row,
            .button-group {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.75rem !important;
            }

            .form-actions > *,
            .modal-actions > *,
            .action-row > *,
            .button-group > * {
                width: 100% !important;
                justify-content: center !important;
            }

            [class*="grid"],
            [class*="Grid"] {
                min-width: 0;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: min(86vw, 280px);
                left: calc(-1 * min(86vw, 280px));
            }

            .sidebar.active {
                left: 0;
            }

            .sidebar-header .logo-title {
                font-size: 18px;
            }

            .nav-menu .nav-link {
                font-size: 13.5px;
                padding: 10px 14px;
            }

            .page-content {
                padding: 18px 12px;
            }

            .page-header h1,
            .page-title {
                font-size: 1.2rem !important;
            }

            .btn-add,
            .btn-secondary,
            .btn-filter,
            .btn-reset {
                width: 100%;
                justify-content: center;
            }

            .table-container .table {
                min-width: 680px;
            }

            .table th,
            .table td {
                padding: 0.75rem !important;
                font-size: 0.82rem !important;
            }

            .btn-action {
                width: 34px !important;
                height: 34px !important;
                flex: 0 0 34px !important;
            }

            .user-info-text .user-name,
            .user-info-text .user-role {
                max-width: 105px;
            }
        }

        @media (max-width: 380px) {
            .page-content {
                padding-left: 10px;
                padding-right: 10px;
            }

            .page-header h1,
            .page-title {
                font-size: 1.08rem !important;
            }

            .mobile-menu-toggle {
                width: 38px;
                height: 38px;
            }

            .table-container .table {
                min-width: 640px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-main">
        <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
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
                            <i class="bi bi-box-arrow-left"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Buka menu navigasi" aria-controls="sidebar" aria-expanded="false">
                    <i class="bi bi-list"></i>
                </button>

                <div class="navbar-right" style="margin-left: auto;">

                    <div class="user-profile-menu" style="position: relative;">
                        <div class="user-profile">
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

    <style>
        /* Final responsive overrides: rendered after page-level styles so every admin page follows the same mobile rules. */
        @media (max-width: 992px) {
            .main-content {
                width: 100%;
                min-width: 0;
            }

            .page-content {
                width: 100%;
                max-width: 100vw;
                overflow-x: hidden;
            }

            .dashboard-grid,
            .form-grid,
            .info-grid,
            .detail-grid,
            .details-grid,
            .stats-grid,
            .student-grid,
            .class-grid,
            .selection-grid,
            .summary-grid,
            .media-grid,
            .category-grid,
            .criteria-grid,
            .teacher-toolbar,
            .transfer-grid,
            .request-grid,
            .cards-grid,
            .students-grid {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 768px) {
            .page-content {
                padding: 18px 12px !important;
            }

            .page-header {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                justify-content: flex-start !important;
                flex-wrap: wrap !important;
                gap: 0.85rem !important;
                width: 100% !important;
                margin-bottom: 1.25rem !important;
            }

            .page-header h1,
            .page-title {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                flex: 1 1 auto !important;
                flex-shrink: 1 !important;
                font-size: 1.2rem !important;
                line-height: 1.35 !important;
                white-space: normal !important;
                overflow-wrap: anywhere !important;
            }

            .page-header > div {
                width: 100% !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 0.75rem !important;
            }

            .page-header .btn-add,
            .page-header .btn-secondary,
            .btn-add,
            .btn-secondary {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                min-height: 44px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                flex: 1 1 auto !important;
                flex-shrink: 1 !important;
                padding: 0.72rem 1rem !important;
                text-align: center !important;
                white-space: normal !important;
                overflow: visible !important;
                text-overflow: clip !important;
            }

            .count-info {
                width: 100% !important;
                line-height: 1.6 !important;
            }

            .akun-toolbar,
            .pengumuman-toolbar {
                width: 100% !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.85rem !important;
            }

            .search-wrapper,
            .student-search-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
            }

            .search-input,
            .student-search-input {
                width: 100% !important;
                max-width: 100% !important;
            }

            .bulk-actions {
                width: 100% !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.75rem !important;
                padding: 0.9rem !important;
                overflow: hidden !important;
            }

            .bulk-actions.hidden {
                display: none !important;
            }

            .bulk-actions > div,
            .bulk-actions > div:last-child {
                width: 100% !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 0.65rem !important;
            }

            .btn-bulk-delete,
            .btn-bulk-clear,
            .btn-filter,
            .btn-reset {
                width: 100% !important;
                max-width: 100% !important;
                justify-content: center !important;
                white-space: normal !important;
            }

            .filter-section,
            .table-container,
            .card,
            .form-card,
            .detail-card,
            .info-card,
            .content-card,
            .confirmation-card,
            .dashboard-main,
            .activities-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                border-radius: 10px !important;
            }

            .filter-section {
                padding: 1rem !important;
            }

            .filter-row,
            .form-row,
            .dashboard-grid,
            .form-grid,
            .info-grid,
            .detail-grid,
            .details-grid,
            .stats-grid,
            .student-grid,
            .class-grid,
            .selection-grid,
            .summary-grid,
            .media-grid,
            .category-grid,
            .criteria-grid,
            .teacher-toolbar,
            .transfer-grid,
            .request-grid,
            .cards-grid,
            .students-grid {
                display: grid !important;
                grid-template-columns: 1fr !important;
                gap: 0.9rem !important;
            }

            .filter-group,
            .form-group,
            .input-group-custom {
                width: 100% !important;
                min-width: 0 !important;
            }

            .filter-group input,
            .filter-group select,
            .form-group input,
            .form-group select,
            .form-group textarea {
                width: 100% !important;
                max-width: 100% !important;
            }

            .table-container {
                overflow-x: auto !important;
                overflow-y: hidden !important;
                -webkit-overflow-scrolling: touch;
            }

            .table-container .table {
                width: max-content !important;
                min-width: 680px !important;
            }

            .table th,
            .table td {
                padding: 0.78rem !important;
                font-size: 0.82rem !important;
                white-space: nowrap !important;
            }

            .action-buttons {
                display: flex !important;
                flex-wrap: nowrap !important;
                width: max-content !important;
            }

            .btn-action {
                width: 34px !important;
                height: 34px !important;
                flex: 0 0 34px !important;
            }

            .form-actions,
            .modal-actions,
            .action-row,
            .button-group {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.75rem !important;
            }

            .form-actions > *,
            .modal-actions > *,
            .action-row > *,
            .button-group > * {
                width: 100% !important;
                justify-content: center !important;
            }
        }

        @media (max-width: 380px) {
            .page-content {
                padding: 16px 10px !important;
            }

            .page-header h1,
            .page-title {
                font-size: 1.08rem !important;
            }

            .table-container .table {
                min-width: 620px !important;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    
    <script>
        // Handle delete confirmation with SweetAlert2
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');

            const closeSidebar = () => {
                if (!sidebar || !mobileMenuToggle) return;
                sidebar.classList.remove('active');
                document.body.classList.remove('sidebar-open');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
            };

            const openSidebar = () => {
                if (!sidebar || !mobileMenuToggle) return;
                sidebar.classList.add('active');
                document.body.classList.add('sidebar-open');
                mobileMenuToggle.setAttribute('aria-expanded', 'true');
            };

            if (mobileMenuToggle && sidebar && sidebarOverlay) {
                mobileMenuToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('active')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });

                sidebarOverlay.addEventListener('click', closeSidebar);

                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeSidebar();
                    }
                });

                sidebar.querySelectorAll('.nav-link').forEach((link) => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 992) {
                            closeSidebar();
                        }
                    });
                });

                window.addEventListener('resize', function() {
                    if (window.innerWidth > 992) {
                        closeSidebar();
                    }
                });
            }

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
