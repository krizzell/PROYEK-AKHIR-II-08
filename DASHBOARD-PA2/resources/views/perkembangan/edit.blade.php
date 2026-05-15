@extends('layouts.app')

@section('title', 'Edit Perkembangan Anak')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --primary-light: #FEF3C7;
        --primary-dark: #F97316;
        --success-color: #10B981;
        --warning-color: #F59E0B;
        --danger-color: #EF4444;
        --info-color: #0EA5E9;
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
        background: transparent;
        min-height: 100vh;
        padding: 2.5rem 0;
    }

    .container-lg {
        max-width: 1000px;
        margin: 0 auto;
        background: #FFFFFF;
        border-radius: 16px;
        padding: 24px 3rem;
        border: 1px solid rgba(226, 232, 240, 0.6);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08), 0 4px 8px rgba(0, 0, 0, 0.04);
        position: relative;
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

    .premium-header {
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--neutral-200);
    }

    .premium-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .premium-header .breadcrumb-text {
        color: var(--neutral-500);
        font-size: 0.95rem;
        margin-top: 0.5rem;
    }

    .form-section {
        margin-bottom: 3.5rem;
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

    /* STUDENT SELECTION CLEAN */
    .student-search-wrapper {
        position: relative;
        margin-bottom: 1.5rem;
        max-width: 500px;
    }

    .student-search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid var(--neutral-300);
        border-radius: 0.5rem;
        font-size: 0.95rem;
        color: var(--neutral-900);
        background: var(--neutral-50);
        transition: all 0.2s ease;
    }

    .student-search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .student-search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--neutral-400);
        font-size: 0.9rem;
        pointer-events: none;
    }

    .student-cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1rem;
        max-height: 500px;
        overflow-y: auto;
        padding-right: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .student-cards-container::-webkit-scrollbar {
        width: 4px;
    }
    .student-cards-container::-webkit-scrollbar-thumb {
        background: var(--neutral-300);
        border-radius: 4px;
    }

    .student-card {
        background: white;
        border: 1px solid var(--neutral-200);
        border-radius: 0.75rem;
        padding: 1rem;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
    }

    .student-card:hover {
        border-color: var(--neutral-300);
        background: var(--neutral-50);
    }

    .student-card.selected {
        border-color: var(--primary-color);
        background: var(--primary-light);
    }

    .student-card-content {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--neutral-100);
        color: var(--neutral-600);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
        flex-shrink: 0;
    }
    
    .student-card.selected .student-avatar {
        background: var(--primary-color);
        color: white;
    }

    .student-info {
        flex: 1;
        min-width: 0;
    }

    .student-name {
        font-weight: 600;
        color: var(--neutral-900);
        margin: 0;
        margin-bottom: 0.25rem;
        font-size: 0.95rem;
    }

    .student-meta {
        font-size: 0.8rem;
        color: var(--neutral-500);
        margin: 0;
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .student-check {
        color: var(--primary-color);
        opacity: 0;
        transition: all 0.2s ease;
        font-size: 1.25rem;
    }

    .student-card.selected .student-check {
        opacity: 1;
    }

    .selected-student-badge {
        display: none;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: var(--neutral-50);
        border-radius: 0.5rem;
        border: 1px solid var(--neutral-200);
        margin-bottom: 1.5rem;
        width: fit-content;
    }

    .selected-student-badge.active {
        display: flex;
    }

    .selected-student-badge-icon {
        color: var(--success-color);
        font-size: 1.25rem;
    }

    .selected-student-badge-text {
        font-size: 0.9rem;
        color: var(--neutral-700);
    }

    .selected-student-badge-name {
        font-weight: 600;
        color: var(--neutral-900);
        margin: 0;
        display: inline;
    }

    /* PERIODE SECTION CLEAN */
    .periode-container {
        display: flex;
        gap: 1rem;
        margin-bottom: 0.5rem;
        align-items: center;
    }

    .periode-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: var(--neutral-50);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid var(--neutral-200);
    }

    .periode-label {
        font-size: 0.85rem;
        color: var(--neutral-500);
    }

    .periode-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--neutral-900);
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

    /* KATEGORI ITEMS CLEAN */
    .kategori-cards-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
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
        border: 1px solid var(--neutral-200);
        border-radius: 0.5rem;
        padding: 1rem 1.25rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .kategori-card:hover {
        background: var(--neutral-50);
        border-color: var(--neutral-300);
    }

    .kategori-checkbox-input:checked + .kategori-card {
        border-color: var(--primary-color);
        background: var(--primary-light);
    }

    .kategori-card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 0;
    }

    .kategori-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--neutral-900);
        margin: 0;
    }
    
    .kategori-desc {
        font-size: 0.85rem; 
        color: var(--neutral-500); 
        margin: 0;
    }

    .kategori-check {
        color: var(--primary-color);
        opacity: 0;
        transition: all 0.2s ease;
        font-size: 1.25rem;
    }

    .kategori-checkbox-input:checked + .kategori-card .kategori-check {
        opacity: 1;
    }

    .nilai-wrapper {
        display: none;
        padding: 1rem 0 1.5rem 1.25rem;
        border-left: 2px solid var(--neutral-200);
        margin-left: 1.5rem;
        margin-top: 0.5rem;
    }

    .kategori-checkbox-input:checked ~ .nilai-wrapper {
        display: block;
        animation: slideDown 0.3s ease-out;
    }

    .nilai-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--neutral-700);
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .nilai-display {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 0.75rem;
        background: var(--primary-light);
        color: var(--primary-color);
        border-radius: 0.5rem;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .nilai-slider-container {
        display: none;
    }

    .nilai-slider {
        display: none;
    }

    .nilai-scale-labels {
        display: none;
    }

    .rating-stars {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .rating-star {
        width: 40px;
        height: 40px;
        border-radius: 0.5rem;
        border: 1px solid var(--neutral-300);
        background: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--neutral-700);
        transition: all 0.2s ease;
    }

    .rating-star:hover {
        border-color: var(--primary-color);
        background: var(--neutral-50);
        color: var(--primary-color);
    }

    .rating-star.active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        font-weight: 600;
    }

    .rating-stars-labels {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: var(--neutral-500);
        padding: 0 0.5rem;
        max-width: 445px;
    }

    .rating-label-item {
        padding: 0;
        border: none;
        text-align: center;
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

    /* ACTION BUTTONS CLEAN */
    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid var(--neutral-200);
    }

    .btn-premium {
        padding: 0.75rem 1.5rem !important;
        border-radius: 0.5rem !important;
        font-size: 0.95rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.5rem !important;
        text-decoration: none !important;
    }

    .btn-save {
        background: var(--primary-color) !important;
        color: white !important;
        border: 1px solid var(--primary-color) !important;
    }

    .btn-save:hover {
        background: #ea580c !important; /* Tailwind orange-600 */
        border-color: #ea580c !important;
    }

    .btn-cancel {
        background: white !important;
        color: var(--neutral-700) !important;
        border: 1px solid var(--neutral-300) !important;
    }

    .btn-cancel:hover {
        background: var(--neutral-50) !important;
        color: var(--neutral-900) !important;
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
                Edit Perkembangan Anak
            </h1>
            <div class="breadcrumb-text">
                <i class="bi bi-info-circle"></i>
                Dokumentasikan perkembangan akademik, sosial, dan emosional siswa dengan detail
            </div>
        </div>

        <!-- MAIN FORM -->
        <form action="{{ route('perkembangan.update', $perkembangan->id_perkembangan) }}" method="POST" id="form-perkembangan">
            @csrf
            @method('PUT')

                    <!-- ===== SECTION 1: PILIH SISWA ===== -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-person-fill"></i></div>
                            Pilih Siswa
                        </div>

                        
                        <div class="selected-student-badge active" style="display:flex; width:100%; border-color:var(--primary-color);">
                            <div class="selected-student-badge-content" style="display:flex; align-items:center; gap: 1rem;">
                                <div class="student-avatar" style="width: 48px; height: 48px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.25rem;">
                                    {{ substr($perkembangan->siswa->nama_siswa ?? 'S', 0, 1) }}
                                </div>
                                <div class="selected-student-badge-text">
                                    <p class="selected-student-badge-label" style="font-size:0.85rem; color:var(--neutral-500); margin:0 0 0.25rem 0;">Siswa yang sedang dinilai</p>
                                    <p class="selected-student-badge-name" style="font-weight:600; font-size:1.1rem; color:var(--neutral-900); margin:0;">
                                        {{ $perkembangan->siswa->nama_siswa ?? '-' }} <span style="color:var(--neutral-500); font-size:0.9rem; font-weight:normal;">({{ $perkembangan->nomor_induk_siswa }})</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="nomor_induk_siswa" name="nomor_induk_siswa" 
                               value="{{ $perkembangan->nomor_induk_siswa }}">


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

                    <!-- ===== SECTION 3: KATEGORI PERKEMBANGAN ===== -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-bar-chart"></i></div>
                            Kategori Perkembangan
                        </div>

                        <div class="kategori-cards-container">
                            @php
                                $oldScores = [];
                                if (isset($perkembangan) && $perkembangan->kategoriDetails) {
                                    foreach($perkembangan->kategoriDetails as $detail) {
                                        $oldScores[strtolower($detail->nama_kategori)] = $detail->nilai;
                                    }
                                }

                                $categories = ['Akademik', 'Sosial', 'Emosional'];
                                $selectedCategories = old('kategori', []);
                                if(empty(old('_token'))) {
                                    if(isset($oldScores['akademik'])) $selectedCategories[] = 'Akademik';
                                    if(isset($oldScores['sosial'])) $selectedCategories[] = 'Sosial';
                                    if(isset($oldScores['emosional'])) $selectedCategories[] = 'Emosional';
                                }
                            @endphp

                            @foreach($categories as $category)
                                <div class="kategori-card-wrapper">
                                    <input type="checkbox" class="kategori-checkbox-input kategori-checkbox" 
                                           id="checkbox_{{ $category }}" name="kategori[]" value="{{ $category }}"
                                           data-kategori="{{ strtolower($category) }}"
                                           {{ in_array($category, (array)$selectedCategories) ? 'checked' : '' }}>
                                    <label for="checkbox_{{ $category }}" class="kategori-card">
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
                                    </label>

                                    <div class="nilai-wrapper">
                                        <label class="nilai-label">
                                            Nilai Perkembangan
                                            <span class="nilai-display">
                                                <i class="bi bi-graph-up"></i>
                                                <span id="nilai_display_{{ strtolower($category) }}">{{ old('nilai_' . strtolower($category), $oldScores[strtolower($category)] ?? '') ?: '-' }}</span>/10
                                            </span>
                                        </label>

                                        <div class="rating-stars" id="rating_{{ strtolower($category) }}">
                                            @for ($i = 1; $i <= 10; $i++)
                                                <button type="button" class="rating-star" data-value="{{ $i }}" 
                                                        data-kategori="{{ strtolower($category) }}"
                                                        title="{{ $i }} - {{ $i <= 3 ? 'Rendah' : ($i <= 6 ? 'Sedang' : ($i <= 8 ? 'Tinggi' : 'Sangat Tinggi')) }}">
                                                    {{ $i }}
                                                </button>
                                            @endfor
                                        </div>



                                        <!-- Deskripsi dinamis yang muncul saat pilih angka -->
                                        <div id="deskripsi_{{ strtolower($category) }}" 
                                             style="color: var(--neutral-600); font-size: 0.9rem; font-weight: 500; margin-top: 1rem; min-height: 20px; padding: 0.75rem; background: var(--neutral-50); border-radius: 0.5rem; display: none;">
                                            
                                        </div>

                                        <input type="hidden" id="nilai_{{ strtolower($category) }}" 
                                               name="nilai_{{ strtolower($category) }}"
                                               value="{{ old('nilai_' . strtolower($category), $oldScores[strtolower($category)] ?? '') }}"
                                               data-kategori="{{ strtolower($category) }}">

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

                    <div class="section-divider"></div>

                    <!-- ===== SECTION 4: HASIL PERHITUNGAN OTOMATIS ===== -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-calculator"></i></div>
                            Hasil Perhitungan Nilai Akhir
                        </div>

                        <div style="background: var(--neutral-50); border: 1px solid var(--neutral-200); border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1.5rem;">
                            <div style="display: flex; gap: 1.5rem; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                                <!-- Rata-rata Nilai -->
                                <div style="display: flex; gap: 1rem; align-items: center;">
                                    <div style="font-size: 0.9rem; color: var(--neutral-500); font-weight: 600;">
                                        Rata-rata Nilai:
                                    </div>
                                    <div style="display: flex; align-items: baseline; gap: 0.25rem;">
                                        <span id="average-display" style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);">
                                            -
                                        </span>
                                        <span style="font-size: 0.85rem; color: var(--neutral-500);">
                                            / 10
                                        </span>
                                    </div>
                                </div>

                                <!-- Status Indikator -->
                                <div style="display: flex; gap: 1rem; align-items: center;">
                                    <div style="font-size: 0.9rem; color: var(--neutral-500); font-weight: 600;">
                                        Status Capaian:
                                    </div>
                                    <div id="status-badge-display" style="font-weight: 600; font-size: 0.95rem; color: var(--neutral-600);">
                                        <i class="bi bi-dash"></i> Isi kategori terlebih dahulu
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden input untuk status_utama (auto-filled) -->
                        <input type="hidden" id="hidden_status_utama" name="status_utama">

                        <div class="template-description" id="template-deskripsi" style="display: none;">
                            <div class="template-description-label">
                                <i class="bi bi-lightbulb"></i> Deskripsi
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

                    <div class="section-divider"></div>

                    <!-- ===== SECTION 5: DESKRIPSI TAMBAHAN ===== -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-pencil-square"></i></div>
                            Deskripsi & Catatan Tambahan
                        </div>
                        <div class="textarea-wrapper">
                            <textarea class="textarea-premium" id="deskripsi_tambahan" name="deskripsi_tambahan" 
                                      placeholder="Tambahkan observasi khusus atau catatan penting tentang perkembangan siswa...">{{ old('deskripsi_tambahan', $perkembangan->deskripsi_tambahan) }}</textarea>
                        </div>
                        <div class="textarea-hint">
                            <i class="bi bi-info-circle"></i> Opsional - Tuliskan detail khusus atau catatan penting
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- ACTION BUTTONS -->
                    <div class="action-buttons">
                        <a href="{{ route('perkembangan.index') }}" class="btn-premium btn-cancel">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                        <button type="submit" class="btn-premium btn-save">
                            <i class="bi bi-check-circle"></i> Perbarui Perkembangan
                        </button>
                    </div>
                </form>
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

    const deskripsiPerKategori = {
        'akademik': {
            1: 'Belum mengenali materi dasar',
            2: 'Mulai mengenali materi dengan bantuan',
            3: 'Mulai mencoba memahami materi',
            4: 'Cukup memahami materi sederhana',
            5: 'Mulai berkembang dalam pembelajaran',
            6: 'Memahami materi cukup baik',
            7: 'Mampu belajar mandiri',
            8: 'Memahami materi dengan baik dan konsisten',
            9: 'Sangat aktif dalam pembelajaran',
            10: 'Sangat optimal dan melampaui target belajar'
        },
        'sosial': {
            1: 'Kesulitan berinteraksi',
            2: 'Mulai mengenali interaksi sosial',
            3: 'Mulai berinteraksi sederhana',
            4: 'Cukup mampu berinteraksi',
            5: 'Mulai berkembang dalam kerja sama',
            6: 'Berinteraksi cukup baik',
            7: 'Mampu bekerja sama mandiri',
            8: 'Sangat baik dan konsisten bersosialisasi',
            9: 'Sangat aktif dan percaya diri',
            10: 'Menjadi contoh positif bagi teman'
        },
        'emosional': {
            1: 'Belum mampu mengontrol emosi',
            2: 'Mulai mengenali emosi',
            3: 'Mulai mencoba mengendalikan emosi',
            4: 'Cukup mampu mengontrol emosi',
            5: 'Mulai berkembang secara emosional',
            6: 'Emosi cukup stabil',
            7: 'Mampu mengendalikan emosi mandiri',
            8: 'Stabil dan konsisten dalam pengendalian diri',
            9: 'Sangat baik dalam regulasi emosi',
            10: 'Sangat matang dan positif secara emosional'
        }
    };

    // Select Student Function
    function selectStudent(element) {
        // Jika sudah terpilih, maka batalkan pilihan (toggle off)
        if (element.classList.contains('selected')) {
            element.classList.remove('selected');
            document.getElementById('nomor_induk_siswa').value = '';
            
            const badge = document.getElementById('selected-student-badge');
            badge.classList.remove('active');
            return;
        }

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

    // Handle rating star clicks (Consolidated)
    document.querySelectorAll('.rating-star').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const value = button.dataset.value;
            const kategori = button.dataset.kategori;
            const containerEl = document.getElementById(`rating_${kategori}`);
            const deskripsiEl = document.getElementById(`deskripsi_${kategori}`);
            const nilaiInputEl = document.getElementById(`nilai_${kategori}`);
            const nilaiDisplayEl = document.getElementById(`nilai_display_${kategori}`);

            // Toggle logic: If the clicked button is already the exact selected value, clear it
            if (nilaiInputEl.value === value) {
                nilaiInputEl.value = '';
                nilaiDisplayEl.textContent = '-';
                deskripsiEl.style.display = 'none';

                containerEl.querySelectorAll('.rating-star').forEach(btn => {
                    btn.classList.remove('active');
                });
            } else {
                // Select logic
                nilaiInputEl.value = value;
                nilaiDisplayEl.textContent = value;

                // Make stars active up to the selected value
                containerEl.querySelectorAll('.rating-star').forEach(btn => {
                    if (parseInt(btn.dataset.value) <= parseInt(value)) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });

                const deskripsi = deskripsiPerKategori[kategori][value];
                deskripsiEl.innerHTML = `<strong>${value}.</strong> ${deskripsi}`;
                deskripsiEl.style.display = 'block';
                deskripsiEl.style.borderLeft = '3px solid var(--primary-color)';
            }
            
            // Auto-calculate status
            calculateAndUpdateStatus();
        });
    });

    // Restore active state on page load if value exists
    document.querySelectorAll('[data-kategori]').forEach(input => {
        if(input.id.startsWith('nilai_')) {
            const value = input.value;
            if (value) {
                const kategori = input.dataset.kategori;
                const button = document.querySelector(`.rating-star[data-value="${value}"][data-kategori="${kategori}"]`);
                if (button) {
                    // Trigger the UI updates manually to avoid click toggling on load
                    const containerEl = document.getElementById(`rating_${kategori}`);
                    const deskripsiEl = document.getElementById(`deskripsi_${kategori}`);
                    const nilaiDisplayEl = document.getElementById(`nilai_display_${kategori}`);
                    
                    nilaiDisplayEl.textContent = value;
                    
                    containerEl.querySelectorAll('.rating-star').forEach(btn => {
                        if (parseInt(btn.dataset.value) <= parseInt(value)) {
                            btn.classList.add('active');
                        } else {
                            btn.classList.remove('active');
                        }
                    });

                    const deskripsi = deskripsiPerKategori[kategori][value];
                    deskripsiEl.innerHTML = `<strong>${value}.</strong> ${deskripsi}`;
                    deskripsiEl.style.display = 'block';
                    deskripsiEl.style.borderLeft = '3px solid var(--primary-color)';
                }
            }
        }
    });

    // Function to calculate average and auto-generate status
    function calculateAndUpdateStatus() {
        const akademikInput = document.getElementById('nilai_akademik');
        const sosialInput = document.getElementById('nilai_sosial');
        const emosionalInput = document.getElementById('nilai_emosional');

        if (!akademikInput || !sosialInput || !emosionalInput) return;

        const akademik = akademikInput.value ? parseInt(akademikInput.value) : null;
        const sosial = sosialInput.value ? parseInt(sosialInput.value) : null;
        const emosional = emosionalInput.value ? parseInt(emosionalInput.value) : null;

        if (akademik === null || sosial === null || emosional === null) {
            document.getElementById('average-display').textContent = '-';
            document.getElementById('status-badge-display').innerHTML = '<i class="bi bi-dash"></i> Isi kategori terlebih dahulu';
            document.getElementById('hidden_status_utama').value = '';
            
            const templateDiv = document.getElementById('template-deskripsi');
            if(templateDiv) templateDiv.style.display = 'none';
            return;
        }

        const average = (akademik + sosial + emosional) / 3;
        
        let status, statusText, badgeColor;
        if (average <= 3) {
            status = 'BB';
            statusText = 'Belum Berkembang';
            badgeColor = '#EF4444';
        } else if (average <= 6) {
            status = 'MB';
            statusText = 'Mulai Berkembang';
            badgeColor = '#F59E0B';
        } else if (average <= 8) {
            status = 'BSH';
            statusText = 'Berkembang Sesuai Harapan';
            badgeColor = '#0EA5E9';
        } else {
            status = 'BSB';
            statusText = 'Berkembang Sangat Baik';
            badgeColor = '#10B981';
        }

        document.getElementById('average-display').textContent = average.toFixed(2);
        
        const statusBadge = `
            <i class="bi bi-check-circle-fill"></i> 
            <span style="display: inline-block; padding: 0.25rem 0.75rem; background: ${badgeColor}15; color: ${badgeColor}; border-radius: 0.375rem; font-weight: 700; margin: 0 0.25rem;">
                ${status}
            </span> 
            ${statusText}
        `;
        document.getElementById('status-badge-display').innerHTML = statusBadge;
        document.getElementById('hidden_status_utama').value = status;

        const templateDiv = document.getElementById('template-deskripsi');
        const templateText = document.getElementById('template-text');
        if (templateDescriptions[status] && templateDiv && templateText) {
            templateText.textContent = templateDescriptions[status];
            templateDiv.style.display = 'block';
        }
    }

    // Kategori Checkbox Handler
    document.querySelectorAll('.kategori-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const wrapper = this.closest('.kategori-card-wrapper');
            const nilaiWrapper = wrapper.querySelector('.nilai-wrapper');

            if (this.checked && nilaiWrapper) {
                nilaiWrapper.style.display = 'block';
            } else if (nilaiWrapper) {
                nilaiWrapper.style.display = 'none';
                
                // Also clear the value when checkbox is unchecked
                const kategoriLower = this.dataset.kategori;
                const nilaiInputEl = document.getElementById(`nilai_${kategoriLower}`);
                const nilaiDisplayEl = document.getElementById(`nilai_display_${kategoriLower}`);
                const containerEl = document.getElementById(`rating_${kategoriLower}`);
                const deskripsiEl = document.getElementById(`deskripsi_${kategoriLower}`);
                
                if(nilaiInputEl) nilaiInputEl.value = '';
                if(nilaiDisplayEl) nilaiDisplayEl.textContent = '-';
                if(deskripsiEl) deskripsiEl.style.display = 'none';
                
                if(containerEl) {
                    containerEl.querySelectorAll('.rating-star').forEach(btn => {
                        btn.classList.remove('active');
                    });
                }
                
                calculateAndUpdateStatus();
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
            const nilaiInputEl = document.getElementById(`nilai_${kategoriLower}`);

            if (!nilaiInputEl || !nilaiInputEl.value) {
                e.preventDefault();
                alert('Isi nilai untuk kategori ' + checkbox.value);
                return false;
            }
        }
    });

    // Restore selected student on page load (Removed student card search)
    window.addEventListener('DOMContentLoaded', function() {

        // Show nilai wrappers for checked categories
        document.querySelectorAll('.kategori-checkbox:checked').forEach(checkbox => {
            const wrapper = checkbox.closest('.kategori-card-wrapper');
            const nilaiWrapper = wrapper.querySelector('.nilai-wrapper');
            if (nilaiWrapper) {
                nilaiWrapper.style.display = 'block';
            }
        });

        // Calculate and display status on page load if values already exist
        calculateAndUpdateStatus();
    });
</script>
@endsection
