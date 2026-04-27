@extends('layouts.app')

@section('title', 'Tambah Perkembangan Anak')

@section('content')
<style>
    :root {
        --primary-color: #0066FF;
        --primary-light: #E6F0FF;
        --primary-dark: #0052CC;
        --success-color: #10B981;
        --warning-color: #F59E0B;
        --danger-color: #EF4444;
        --info-color: #06B6D4;
        --neutral-100: #F9FAFB;
        --neutral-200: #F3F4F6;
        --neutral-300: #E5E7EB;
        --neutral-400: #D1D5DB;
        --neutral-600: #4B5563;
        --neutral-700: #374151;
        --neutral-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    * {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .page-wrapper {
        background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
        min-height: 100vh;
        padding: 2.5rem 0;
    }

    .premium-header {
        margin-bottom: 2rem;
        animation: slideDown 0.5s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .premium-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .premium-header .breadcrumb-text {
        color: var(--neutral-600);
        font-size: 0.95rem;
        margin-top: 0.5rem;
    }

    .premium-card {
        background: white;
        border: 1px solid var(--neutral-200);
        border-radius: 1rem;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .premium-card-body {
        padding: 3rem;
    }

    .form-section {
        margin-bottom: 3rem;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--neutral-300), transparent);
        margin: 2.5rem 0;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--neutral-900);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title-icon {
        width: 32px;
        height: 32px;
        background: var(--primary-light);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 1rem;
    }

    /* STUDENT SELECTION PREMIUM */
    .student-search-wrapper {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .student-search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.75rem;
        border: 1.5px solid var(--neutral-300);
        border-radius: 0.875rem;
        font-size: 1rem;
        color: var(--neutral-900);
        background: white;
        transition: all 0.3s ease;
    }

    .student-search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px var(--primary-light);
        background: white;
    }

    .student-search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--neutral-400);
        pointer-events: none;
    }

    .student-cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
        max-height: 700px;
        overflow-y: auto;
        padding: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .student-cards-container::-webkit-scrollbar {
        width: 6px;
    }

    .student-cards-container::-webkit-scrollbar-track {
        background: var(--neutral-100);
        border-radius: 0.5rem;
    }

    .student-cards-container::-webkit-scrollbar-thumb {
        background: var(--neutral-300);
        border-radius: 0.5rem;
    }

    .student-cards-container::-webkit-scrollbar-thumb:hover {
        background: var(--neutral-400);
    }

    .student-card {
        background: white;
        border: 2px solid var(--neutral-200);
        border-radius: 1rem;
        padding: 1.25rem;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .student-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--info-color));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .student-card:hover {
        border-color: var(--neutral-300);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .student-card.selected {
        border-color: var(--primary-color);
        background: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
    }

    .student-card.selected::before {
        opacity: 1;
    }

    .student-card-content {
        display: flex;
        gap: 1rem;
    }

    .student-avatar {
        width: 56px;
        height: 56px;
        border-radius: 0.875rem;
        background: linear-gradient(135deg, var(--primary-color), var(--info-color));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .student-info {
        flex: 1;
        min-width: 0;
    }

    .student-name {
        font-weight: 600;
        color: var(--neutral-900);
        margin: 0;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .student-meta {
        font-size: 0.85rem;
        color: var(--neutral-600);
        margin: 0.25rem 0;
    }

    .student-check {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 24px;
        height: 24px;
        background: var(--success-color);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s ease;
    }

    .student-card.selected .student-check {
        opacity: 1;
        transform: scale(1);
    }

    .selected-student-badge {
        display: none;
        background: linear-gradient(135deg, var(--primary-light), rgba(0, 102, 255, 0.05));
        border: 1.5px solid var(--primary-color);
        border-radius: 0.875rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
        animation: slideDown 0.3s ease-out;
    }

    .selected-student-badge.active {
        display: block;
    }

    .selected-student-badge-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .selected-student-badge-icon {
        width: 36px;
        height: 36px;
        background: var(--primary-color);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .selected-student-badge-text {
        flex: 1;
    }

    .selected-student-badge-label {
        font-size: 0.875rem;
        color: var(--neutral-600);
        margin: 0;
    }

    .selected-student-badge-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--neutral-900);
        margin: 0;
    }

    /* PERIODE SECTION */
    .periode-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .periode-item {
        background: var(--neutral-100);
        border: 1.5px solid var(--neutral-200);
        border-radius: 0.875rem;
        padding: 1.25rem;
        text-align: center;
    }

    .periode-label {
        font-size: 0.85rem;
        color: var(--neutral-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .periode-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    /* STATUS PILLS PREMIUM */
    .status-pills-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .status-pill-wrapper {
        position: relative;
    }

    .status-pill-input {
        display: none;
    }

    .status-pill-label {
        display: block;
        padding: 1rem;
        border: 2px solid var(--neutral-300);
        border-radius: 0.875rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .status-pill-label:hover {
        border-color: var(--neutral-400);
        background: var(--neutral-100);
    }

    .status-pill-input:checked + .status-pill-label {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .status-badge {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge-bb {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    .status-badge-mb {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .status-badge-bsh {
        background: rgba(6, 182, 212, 0.1);
        color: var(--info-color);
    }

    .status-badge-bsb {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .status-pill-text {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--neutral-900);
    }

    .status-input:checked + .status-pill-label {
        border-color: var(--primary-color);
        background: var(--primary-light);
    }

    /* TEMPLATE DESCRIPTION */
    .template-description {
        display: none;
        background: linear-gradient(135deg, rgba(0, 102, 255, 0.05), rgba(6, 182, 212, 0.05));
        border: 1.5px solid var(--primary-light);
        border-radius: 0.875rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        animation: slideDown 0.3s ease-out;
    }

    .template-description.active {
        display: block;
    }

    .template-description-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--primary-color);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }

    .template-description-text {
        font-size: 0.95rem;
        color: var(--neutral-700);
        line-height: 1.6;
        margin: 0;
    }

    /* TEXTAREA PREMIUM */
    .textarea-wrapper {
        position: relative;
    }

    .textarea-premium {
        width: 100%;
        padding: 1rem;
        border: 1.5px solid var(--neutral-300);
        border-radius: 0.875rem;
        font-size: 1rem;
        color: var(--neutral-900);
        font-family: inherit;
        background: white;
        resize: vertical;
        min-height: 120px;
    }

    .textarea-premium:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px var(--primary-light);
        background: white;
    }

    .textarea-hint {
        font-size: 0.85rem;
        color: var(--neutral-600);
        margin-top: 0.5rem;
    }

    /* KATEGORI CARDS */
    .kategori-cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .kategori-card-wrapper {
        position: relative;
    }

    .kategori-checkbox-input {
        display: none;
    }

    .kategori-card {
        background: white;
        border: 2px solid var(--neutral-200);
        border-radius: 1rem;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .kategori-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--info-color));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .kategori-card:hover {
        border-color: var(--neutral-300);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .kategori-checkbox-input:checked + .kategori-card {
        border-color: var(--primary-color);
        background: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
    }

    .kategori-checkbox-input:checked + .kategori-card::before {
        opacity: 1;
    }

    .kategori-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .kategori-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0;
    }

    .kategori-check {
        width: 24px;
        height: 24px;
        background: var(--success-color);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s ease;
    }

    .kategori-checkbox-input:checked + .kategori-card .kategori-check {
        opacity: 1;
        transform: scale(1);
    }

    .nilai-wrapper {
        display: none;
    }

    .kategori-checkbox-input:checked ~ .nilai-wrapper {
        display: block;
        animation: slideDown 0.3s ease-out;
    }

    .nilai-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--neutral-700);
        margin-bottom: 0.75rem;
        display: block;
    }

    .nilai-scale {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .nilai-button {
        flex: 1;
        padding: 0.75rem;
        border: 2px solid var(--neutral-300);
        background: white;
        border-radius: 0.75rem;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--neutral-600);
        transition: all 0.2s ease;
    }

    .nilai-button:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .nilai-button.active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    .nilai-select {
        width: 100%;
        padding: 0.75rem;
        border: 1.5px solid var(--neutral-300);
        border-radius: 0.75rem;
        font-size: 1rem;
        color: var(--neutral-900);
        background: white;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%234B5563' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        padding-right: 2.5rem;
    }

    .nilai-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    /* ACTION BUTTONS */
    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2.5rem;
        justify-content: flex-end;
    }

    .btn-premium {
        padding: 0.875rem 2rem;
        border: none;
        border-radius: 0.875rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        letter-spacing: 0.3px;
    }

    .btn-save {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        box-shadow: 0 4px 12px rgba(0, 102, 255, 0.3);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 102, 255, 0.4);
    }

    .btn-save:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(0, 102, 255, 0.2);
    }

    .btn-cancel {
        background: white;
        color: var(--neutral-700);
        border: 1.5px solid var(--neutral-300);
    }

    .btn-cancel:hover {
        background: var(--neutral-100);
        border-color: var(--neutral-400);
    }

    /* EMPTY STATE */
    .empty-state {
        display: none;
        text-align: center;
        padding: 2rem;
        background: var(--neutral-100);
        border-radius: 0.875rem;
        animation: fadeIn 0.3s ease-out;
    }

    .empty-state.active {
        display: block;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .empty-state-text {
        color: var(--neutral-600);
        font-size: 0.95rem;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .premium-card-body {
            padding: 1.5rem;
        }

        .student-cards-container {
            grid-template-columns: 1fr;
        }

        .kategori-cards-container {
            grid-template-columns: 1fr;
        }

        .status-pills-container {
            grid-template-columns: repeat(2, 1fr);
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-premium {
            justify-content: center;
            width: 100%;
        }

        .premium-header h1 {
            font-size: 1.5rem;
        }
    }

    /* ANIMATIONS */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    .loading {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>

<div class="page-wrapper">
    <div class="container-lg">
        <!-- HEADER -->
        <div class="premium-header">
            <h1>
                <i class="bi bi-plus-circle"></i>
                Tambah Perkembangan Anak
            </h1>
            <div class="breadcrumb-text">
                <i class="bi bi-info-circle"></i>
                Dokumentasikan perkembangan akademik, sosial, dan emosional siswa dengan detail
            </div>
        </div>

        <!-- MAIN FORM CARD -->
        <div class="premium-card">
            <div class="premium-card-body">
                <form action="{{ route('perkembangan.store') }}" method="POST" id="form-perkembangan">
                    @csrf

                    <!-- ===== SECTION 1: PILIH SISWA ===== -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-person-fill"></i></div>
                            Pilih Siswa
                        </div>

                        @if($filterType === 'kelas')
                            <p style="color: var(--neutral-600); font-size: 0.9rem; margin-bottom: 1.5rem;">
                                <i class="bi bi-info-circle" style="color: var(--info-color); margin-right: 0.5rem;"></i>
                                Menampilkan siswa dari kelas Anda
                            </p>
                        @endif

                        <div class="student-search-wrapper">
                            <i class="bi bi-search student-search-icon"></i>
                            <input type="text" class="student-search-input" id="siswa-search" 
                                   placeholder="Cari nama siswa atau nomor induk..." autocomplete="off">
                        </div>

                        <div class="student-cards-container" id="siswa-list">
                            @forelse($siswa as $s)
                                <div class="student-card-wrapper" data-nama="{{ strtolower($s->nama_siswa) }}" 
                                     data-nomor="{{ $s->nomor_induk_siswa }}">
                                    <div class="student-card" onclick="selectStudent(this)">
                                        <div class="student-card-content">
                                            <div class="student-avatar">{{ substr($s->nama_siswa, 0, 1) }}</div>
                                            <div class="student-info">
                                                <p class="student-name">{{ $s->nama_siswa }}</p>
                                                <div class="student-meta">ID: {{ $s->nomor_induk_siswa }}</div>
                                                <div class="student-meta"><i class="bi bi-bookmark"></i> {{ $s->kelas->nama_kelas ?? '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="student-check">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state active" style="grid-column: 1/-1;">
                                    <div class="empty-state-icon">📭</div>
                                    <p class="empty-state-text">Tidak ada siswa yang tersedia</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="selected-student-badge" id="selected-student-badge">
                            <div class="selected-student-badge-content">
                                <div class="selected-student-badge-icon">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="selected-student-badge-text">
                                    <p class="selected-student-badge-label">Siswa Terpilih</p>
                                    <p class="selected-student-badge-name" id="selected-student-name"></p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="nomor_induk_siswa" name="nomor_induk_siswa" 
                               value="{{ old('nomor_induk_siswa') }}" required>

                        @error('nomor_induk_siswa')
                            <div style="background: rgba(239, 68, 68, 0.1); border: 1.5px solid var(--danger-color); 
                                        border-radius: 0.875rem; padding: 1rem; color: var(--danger-color); 
                                        margin-top: 1rem; font-size: 0.9rem;">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="section-divider"></div>

                    <!-- ===== SECTION 2: PERIODE ===== -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-calendar2-check"></i></div>
                            Periode Pelaporan
                        </div>

                        @php
                            $currentMonth = \Carbon\Carbon::now()->month;
                            $currentYear = \Carbon\Carbon::now()->year;
                            $monthNames = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp

                        <div class="periode-container">
                            <div class="periode-item">
                                <div class="periode-label">Bulan</div>
                                <div class="periode-value">{{ $monthNames[$currentMonth] }}</div>
                                <input type="hidden" name="bulan" id="bulan" value="{{ $currentMonth }}">
                            </div>
                            <div class="periode-item">
                                <div class="periode-label">Tahun</div>
                                <div class="periode-value">{{ $currentYear }}</div>
                                <input type="hidden" name="tahun" id="tahun" value="{{ $currentYear }}">
                            </div>
                        </div>
                        <p style="font-size: 0.85rem; color: var(--neutral-600); margin-top: 0.75rem;">
                            <i class="bi bi-info-circle"></i> Periode otomatis diisi berdasarkan tanggal hari ini
                        </p>
                    </div>

                    <div class="section-divider"></div>

                    <!-- ===== SECTION 3: INDIKATOR CAPAIAN ===== -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-graph-up"></i></div>
                            Indikator Capaian Keseluruhan
                        </div>

                        <div class="status-pills-container">
                            <div class="status-pill-wrapper">
                                <input type="radio" class="status-pill-input status-radio" id="status_bb" 
                                       name="status_utama" value="BB" {{ old('status_utama') == 'BB' ? 'checked' : '' }}>
                                <label class="status-pill-label" for="status_bb">
                                    <div class="status-badge status-badge-bb">BB</div>
                                    <div class="status-pill-text">Belum Berkembang</div>
                                </label>
                            </div>

                            <div class="status-pill-wrapper">
                                <input type="radio" class="status-pill-input status-radio" id="status_mb" 
                                       name="status_utama" value="MB" {{ old('status_utama') == 'MB' ? 'checked' : '' }}>
                                <label class="status-pill-label" for="status_mb">
                                    <div class="status-badge status-badge-mb">MB</div>
                                    <div class="status-pill-text">Mulai Berkembang</div>
                                </label>
                            </div>

                            <div class="status-pill-wrapper">
                                <input type="radio" class="status-pill-input status-radio" id="status_bsh" 
                                       name="status_utama" value="BSH" {{ old('status_utama') == 'BSH' ? 'checked' : '' }}>
                                <label class="status-pill-label" for="status_bsh">
                                    <div class="status-badge status-badge-bsh">BSH</div>
                                    <div class="status-pill-text">Sesuai Harapan</div>
                                </label>
                            </div>

                            <div class="status-pill-wrapper">
                                <input type="radio" class="status-pill-input status-radio" id="status_bsb" 
                                       name="status_utama" value="BSB" {{ old('status_utama') == 'BSB' ? 'checked' : '' }}>
                                <label class="status-pill-label" for="status_bsb">
                                    <div class="status-badge status-badge-bsb">BSB</div>
                                    <div class="status-pill-text">Sangat Baik</div>
                                </label>
                            </div>
                        </div>

                        <div class="template-description" id="template-deskripsi">
                            <div class="template-description-label">
                                <i class="bi bi-lightbulb"></i> Template Deskripsi
                            </div>
                            <p class="template-description-text" id="template-text"></p>
                        </div>

                        @error('status_utama')
                            <div style="background: rgba(239, 68, 68, 0.1); border: 1.5px solid var(--danger-color); 
                                        border-radius: 0.875rem; padding: 1rem; color: var(--danger-color); 
                                        font-size: 0.9rem; margin-top: 1rem;">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- DESKRIPSI TAMBAHAN -->
                    <div style="margin-top: 1.5rem;">
                        <label style="font-weight: 600; color: var(--neutral-900); margin-bottom: 0.75rem; display: block;">
                            Deskripsi & Catatan Tambahan
                        </label>
                        <div class="textarea-wrapper">
                            <textarea class="textarea-premium" id="deskripsi_tambahan" name="deskripsi_tambahan" 
                                      placeholder="Tambahkan observasi khusus atau catatan penting tentang perkembangan siswa...">{{ old('deskripsi_tambahan') }}</textarea>
                        </div>
                        <div class="textarea-hint">
                            <i class="bi bi-info-circle"></i> Opsional - Tuliskan detail khusus atau catatan penting
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- ===== SECTION 4: KATEGORI PERKEMBANGAN ===== -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-bar-chart"></i></div>
                            Kategori Perkembangan
                        </div>

                        <div class="kategori-cards-container">
                            @php
                                $categories = ['Akademik', 'Sosial', 'Emosional'];
                                $selectedCategories = old('kategori', []);
                            @endphp

                            @foreach($categories as $category)
                                <div class="kategori-card-wrapper">
                                    <input type="checkbox" class="kategori-checkbox-input kategori-checkbox" 
                                           id="checkbox_{{ $category }}" name="kategori[]" value="{{ $category }}"
                                           data-kategori="{{ strtolower($category) }}"
                                           {{ in_array($category, (array)$selectedCategories) ? 'checked' : '' }}>
                                    <div class="kategori-card">
                                        <div class="kategori-card-header">
                                            <h3 class="kategori-title">{{ $category }}</h3>
                                            <div class="kategori-check">
                                                <i class="bi bi-check-lg"></i>
                                            </div>
                                        </div>

                                        @if($category === 'Akademik')
                                            <p style="font-size: 0.85rem; color: var(--neutral-600); margin: 0;">
                                                Kemampuan kognitif dan akademis
                                            </p>
                                        @elseif($category === 'Sosial')
                                            <p style="font-size: 0.85rem; color: var(--neutral-600); margin: 0;">
                                                Interaksi dan kerjasama dengan teman
                                            </p>
                                        @else
                                            <p style="font-size: 0.85rem; color: var(--neutral-600); margin: 0;">
                                                Regulasi emosi dan pengendalian diri
                                            </p>
                                        @endif
                                    </div>

                                    <div class="nilai-wrapper">
                                        <label class="nilai-label">Nilai Perkembangan (1-10)</label>
                                        <select class="nilai-select" id="nilai_{{ strtolower($category) }}" 
                                                name="nilai_{{ strtolower($category) }}">
                                            <option value="">Pilih nilai...</option>
                                            @for ($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{ old('nilai_' . strtolower($category)) == $i ? 'selected' : '' }}>
                                                    {{ $i }} - {{ $i <= 3 ? 'Rendah' : ($i <= 6 ? 'Sedang' : ($i <= 8 ? 'Tinggi' : 'Sangat Tinggi')) }}
                                                </option>
                                            @endfor
                                        </select>

                                        @error('nilai_' . strtolower($category))
                                            <div style="color: var(--danger-color); font-size: 0.85rem; margin-top: 0.5rem;">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('kategori')
                            <div style="background: rgba(239, 68, 68, 0.1); border: 1.5px solid var(--danger-color); 
                                        border-radius: 0.875rem; padding: 1rem; color: var(--danger-color); 
                                        font-size: 0.9rem; margin-top: 1.5rem;">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="action-buttons">
                        <a href="{{ route('perkembangan.index') }}" class="btn-premium btn-cancel">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                        <button type="submit" class="btn-premium btn-save">
                            <i class="bi bi-check-circle"></i> Simpan Perkembangan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Template descriptions
    const templateDescriptions = {
        'BB': 'Anak belum menunjukkan kemampuan dalam aspek ini. Perlu dukungan dan bimbingan intensif dari guru untuk mengembangkan kompetensi ini.',
        'MB': 'Anak mulai menunjukkan kemampuan dalam aspek ini namun masih memerlukan bimbingan. Perlu terus didukung untuk mencapai perkembangan yang lebih baik.',
        'BSH': 'Anak menunjukkan kemampuan yang sesuai dengan harapan untuk usia/tingkatannya. Anak mampu melaksanakan tugas dengan cukup baik.',
        'BSB': 'Anak menunjukkan kemampuan yang sangat menonjol dalam aspek ini. Anak mampu melaksanakan tugas dengan sangat baik dan melampaui harapan.'
    };

    // Select Student Function
    function selectStudent(element) {
        document.querySelectorAll('.student-card').forEach(card => {
            card.classList.remove('selected');
        });

        element.classList.add('selected');

        const wrapper = element.closest('.student-card-wrapper');
        const nomorInduk = wrapper.dataset.nomor;
        const namaSiswa = element.querySelector('.student-name').textContent;

        document.getElementById('nomor_induk_siswa').value = nomorInduk;

        const badge = document.getElementById('selected-student-badge');
        badge.classList.add('active');
        document.getElementById('selected-student-name').textContent = namaSiswa + ' (' + nomorInduk + ')';
    }

    // Search Students
    const searchInput = document.getElementById('siswa-search');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const wrappers = document.querySelectorAll('.student-card-wrapper');
            let visibleCount = 0;

            wrappers.forEach(wrapper => {
                const nama = wrapper.dataset.nama;
                const nomor = wrapper.dataset.nomor.toLowerCase();
                const match = nama.includes(searchTerm) || nomor.includes(searchTerm);

                wrapper.style.display = match ? 'block' : 'none';
                if (match) visibleCount++;
            });

            if (visibleCount === 0) {
                if (!document.getElementById('no-results')) {
                    const noResults = document.createElement('div');
                    noResults.id = 'no-results';
                    noResults.className = 'empty-state active';
                    noResults.style.gridColumn = '1/-1';
                    noResults.innerHTML = '<div class="empty-state-icon">🔍</div><p class="empty-state-text">Tidak ada siswa yang cocok dengan pencarian</p>';
                    document.getElementById('siswa-list').appendChild(noResults);
                }
            } else {
                const noResults = document.getElementById('no-results');
                if (noResults) noResults.remove();
            }
        });
    }

    // Status Radio Handler
    document.querySelectorAll('.status-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const templateDiv = document.getElementById('template-deskripsi');
            const templateText = document.getElementById('template-text');

            if (this.checked && templateDescriptions[this.value]) {
                templateText.textContent = templateDescriptions[this.value];
                templateDiv.classList.add('active');
            } else {
                templateDiv.classList.remove('active');
            }
        });
    });

    // Kategori Checkbox Handler
    document.querySelectorAll('.kategori-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const kategoriLower = this.dataset.kategori;
            const nilaiWrapper = document.querySelector('[data-kategori="' + kategoriLower + '"]')?.closest('.kategori-card-wrapper')?.querySelector('.nilai-wrapper');

            if (this.checked && nilaiWrapper) {
                nilaiWrapper.style.display = 'block';
            } else if (nilaiWrapper) {
                nilaiWrapper.style.display = 'none';
                const select = nilaiWrapper.querySelector('select');
                select.value = '';
            }
        });
    });

    // Form Submission
    document.getElementById('form-perkembangan').addEventListener('submit', function(e) {
        const selectedStudent = document.getElementById('nomor_induk_siswa').value;
        if (!selectedStudent) {
            e.preventDefault();
            alert('Pilih siswa terlebih dahulu');
            return false;
        }

        const selectedCategories = document.querySelectorAll('.kategori-checkbox:checked');
        if (selectedCategories.length !== 3) {
            e.preventDefault();
            alert('Harus mengisi ketiga kategori perkembangan (Akademik, Sosial, Emosional)');
            return false;
        }

        for (let checkbox of selectedCategories) {
            const kategoriLower = checkbox.dataset.kategori;
            const wrapper = checkbox.closest('.kategori-card-wrapper');
            const select = wrapper.querySelector('select');

            if (!select.value) {
                e.preventDefault();
                alert('Isi nilai untuk kategori ' + checkbox.value);
                return false;
            }
        }
    });

    // Restore selected student on page load
    window.addEventListener('DOMContentLoaded', function() {
        const selectedNomor = document.getElementById('nomor_induk_siswa').value;
        if (selectedNomor) {
            const card = document.querySelector('[data-nomor="' + selectedNomor + '"] .student-card');
            if (card) {
                selectStudent(card);
            }
        }

        // Show template if status already selected
        const checkedStatus = document.querySelector('.status-radio:checked');
        if (checkedStatus) {
            const templateDiv = document.getElementById('template-deskripsi');
            const templateText = document.getElementById('template-text');
            templateText.textContent = templateDescriptions[checkedStatus.value];
            templateDiv.classList.add('active');
        }

        // Show nilai wrappers for checked categories
        document.querySelectorAll('.kategori-checkbox:checked').forEach(checkbox => {
            const wrapper = checkbox.closest('.kategori-card-wrapper');
            const nilaiWrapper = wrapper.querySelector('.nilai-wrapper');
            if (nilaiWrapper) {
                nilaiWrapper.style.display = 'block';
            }
        });
    });
</script>
@endsection
