@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
<style>
    /* SaaS Dashboard Styling */
    :root {
        --primary-color: #FF7A00;
        --primary-light: #FFF7ED;
        --secondary-color: #111827;
        --neutral-50: #F9FAFB;
        --neutral-100: #F3F4F6;
        --neutral-200: #E5E7EB;
        --neutral-500: #6B7280;
        --neutral-700: #374151;
        --neutral-900: #111827;
    }

    .premium-header {
        margin-bottom: 2rem;
    }

    .premium-header h1 {
        font-size: 1.875rem;
        font-weight: 800;
        color: var(--neutral-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .btn-premium {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-edit {
        background: #F59E0B;
        color: #FFFFFF;
    }
    .btn-edit:hover { background: #D97706; transform: translateY(-2px); color: #fff; }

    .btn-back {
        background: #FFFFFF;
        color: var(--neutral-700);
        border: 1px solid var(--neutral-300);
    }
    .btn-back:hover { background: var(--neutral-50); color: var(--neutral-900); }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 2.5fr;
        gap: 2rem;
    }
    @media (max-width: 992px) {
        .dashboard-grid { grid-template-columns: 1fr; }
    }

    .premium-card {
        background: #FFFFFF;
        border-radius: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(229, 231, 235, 0.5);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .card-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--neutral-100);
        background: #FFFFFF;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0;
    }

    .card-body {
        padding: 2rem;
    }

    /* Bio Sidebar */
    .profile-sidebar {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: var(--primary-light);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1.25rem;
        border: 4px solid #FFF;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .profile-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0 0 0.25rem 0;
    }

    .profile-role {
        font-size: 0.95rem;
        color: var(--neutral-500);
        margin: 0;
        padding: 0.25rem 0.75rem;
        background: var(--neutral-100);
        border-radius: 1rem;
        display: inline-block;
    }

    .profile-stats {
        margin-top: 1.5rem;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        border-top: 1px solid var(--neutral-100);
        padding-top: 1.5rem;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        background: var(--neutral-50);
        border-radius: 0.75rem;
    }

    .stat-label {
        font-size: 0.9rem;
        color: var(--neutral-500);
        font-weight: 500;
    }

    .stat-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--neutral-900);
    }

    /* Info Sections */
    .info-section {
        padding: 1.5rem 0;
        border-bottom: 1px solid var(--neutral-200);
    }

    .info-section:first-child {
        padding-top: 0;
    }

    .info-section:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .section-title {
        margin: 0 0 1.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--neutral-900);
        font-size: 1.1rem;
        font-weight: 700;
    }

    .section-icon {
        width: 36px;
        height: 36px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        background: var(--primary-light);
        font-size: 1.1rem;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .detail-label {
        font-weight: 600;
        color: var(--neutral-500);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        color: var(--neutral-900);
        font-size: 1rem;
        font-weight: 500;
    }

    .badge-kelas {
        display: inline-block;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 600;
        background: #EEF2FF;
        color: #4F46E5;
    }

    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-wrapper">
    @php
        $siswaInitials = collect(explode(' ', trim($siswa->nama_siswa ?? 'S')))
            ->filter()
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');
        $jenisKelaminFull = $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan';
    @endphp

    <div class="premium-header">
        <h1>
            <div style="width: 48px; height: 48px; background: var(--primary-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                <i class="bi bi-person-bounding-box"></i>
            </div>
            Detail Siswa: {{ $siswa->nama_siswa }}
        </h1>
        <div class="header-actions">
            <a href="{{ route('siswa.index') }}" class="btn-premium btn-back">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
            <a href="{{ route('siswa.edit', ['nomor_induk_siswa' => $siswa->nomor_induk_siswa]) }}" class="btn-premium btn-edit">
                <i class="bi bi-pencil-square"></i> Edit Siswa
            </a>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Sidebar: Profile Overview -->
        <div class="sidebar-column">
            <div class="premium-card">
                <div class="card-body profile-sidebar">
                    <div class="profile-avatar" style="{{ $siswa->foto_siswa ? 'background: transparent; border: none; padding: 0;' : '' }}">
                        @if($siswa->foto_siswa)
                            <img src="{{ asset('storage/' . $siswa->foto_siswa) }}" alt="Foto Siswa" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 4px solid #FFF; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                        @else
                            {{ $siswaInitials ?: 'S' }}
                        @endif
                    </div>
                    <h3 class="profile-name">{{ $siswa->nama_siswa }}</h3>
                    <p class="profile-role">Siswa PAUD</p>

                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-label">NISN</span>
                            <span class="stat-value">{{ $siswa->nomor_induk_siswa }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Kelas Saat Ini</span>
                            <span class="stat-value">
                                <span class="badge-kelas">
                                    {{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                                </span>
                            </span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Jenis Kelamin</span>
                            <span class="stat-value">
                                @if($siswa->jenis_kelamin == 'L')
                                    <span style="color: #3B82F6;"><i class="bi bi-gender-male"></i> Laki-laki</span>
                                @else
                                    <span style="color: #EC4899;"><i class="bi bi-gender-female"></i> Perempuan</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content: Detailed Info -->
        <div class="main-column">
            <div class="premium-card">
                <div class="card-header">
                    <i class="bi bi-card-checklist" style="color: var(--primary-color); font-size: 1.25rem;"></i>
                    <h2 class="card-title">Informasi Lengkap</h2>
                </div>
                <div class="card-body">
                    <div class="info-section">
                        <div class="section-title">
                            <div class="section-icon"><i class="bi bi-person"></i></div>
                            Data Diri Siswa
                        </div>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Nama Lengkap</span>
                                <span class="detail-value">{{ $siswa->nama_siswa }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Nomor Induk Siswa Nasional</span>
                                <span class="detail-value">{{ $siswa->nomor_induk_siswa }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Jenis Kelamin</span>
                                <span class="detail-value">{{ $jenisKelaminFull }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Tanggal Lahir</span>
                                <span class="detail-value">
                                    @if($siswa->tgl_lahir)
                                        {{ \Carbon\Carbon::parse($siswa->tgl_lahir)->locale('id')->isoFormat('D MMMM Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <div class="section-title">
                            <div class="section-icon"><i class="bi bi-people"></i></div>
                            Data Orangtua & Alamat
                        </div>
                        <div class="detail-grid">
                            <div class="detail-item" style="grid-column: span 2;">
                                <span class="detail-label">Nama Orangtua / Wali</span>
                                <span class="detail-value">{{ $siswa->nama_orgtua ?? '-' }}</span>
                            </div>
                            <div class="detail-item" style="grid-column: span 2;">
                                <span class="detail-label">Alamat Tempat Tinggal</span>
                                <span class="detail-value">{{ $siswa->alamat ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <div class="section-title">
                            <div class="section-icon"><i class="bi bi-info-circle"></i></div>
                            Informasi Sistem
                        </div>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Kelas Terdaftar</span>
                                <span class="detail-value">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Data Dibuat Tanggal</span>
                                <span class="detail-value">{{ $siswa->created_at ? $siswa->created_at->format('d-m-Y H:i') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
