@extends('layouts.app')

@section('title', 'Detail Kelas')

@section('content')
<style>
    /* Add SaaS styling similar to perkembangan/show */
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
        --success-color: #10B981;
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
        grid-template-columns: 1fr 2fr;
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

    /* Teacher Bio */
    .teacher-profile {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .teacher-avatar {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: #FDE68A;
        color: #D97706;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        border: 4px solid #FEF3C7;
    }

    .teacher-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0 0 0.25rem 0;
    }

    .teacher-role {
        font-size: 0.95rem;
        color: var(--neutral-500);
        margin: 0;
        padding: 0.25rem 0.75rem;
        background: var(--neutral-100);
        border-radius: 1rem;
        display: inline-block;
    }

    .teacher-stats {
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
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stat-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--neutral-900);
    }

    /* Table styles */
    .student-table {
        width: 100%;
        border-collapse: collapse;
    }

    .student-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--neutral-500);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--neutral-200);
        background: var(--neutral-50);
    }

    .student-table td {
        padding: 1rem;
        color: var(--neutral-900);
        font-size: 0.95rem;
        border-bottom: 1px solid var(--neutral-100);
        vertical-align: middle;
    }

    .student-table tr:hover {
        background: var(--neutral-50);
    }

    .student-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-light);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
    }

    .student-info {
        display: flex;
        flex-direction: column;
    }

    .student-name {
        font-weight: 600;
        color: var(--neutral-900);
    }

    .student-id {
        font-size: 0.85rem;
        color: var(--neutral-500);
    }

    .empty-state {
        padding: 3rem;
        text-align: center;
        color: var(--neutral-500);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--neutral-300);
    }
</style>

<div class="page-wrapper">
    <div class="premium-header">
        <h1>
            <div style="width: 48px; height: 48px; background: var(--primary-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                <i class="bi bi-building"></i>
            </div>
            Kelas: {{ $kelas->nama_kelas }}
        </h1>
        <div class="header-actions">
            <a href="{{ route('kelas.index') }}" class="btn-premium btn-back">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
            @if(session('is_super_admin'))
            <a href="{{ route('kelas.edit', $kelas->id_kelas) }}" class="btn-premium btn-edit">
                <i class="bi bi-pencil-square"></i> Edit Kelas
            </a>
            @endif
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Sidebar: Teacher & Class Stats -->
        <div class="sidebar-column">
            <div class="premium-card">
                <div class="card-header">
                    <i class="bi bi-person-badge-fill" style="color: var(--primary-color); font-size: 1.25rem;"></i>
                    <h2 class="card-title">Wali Kelas</h2>
                </div>
                <div class="card-body teacher-profile">
                    @if($kelas->guru)
                        <div class="teacher-avatar">
                            {{ substr($kelas->guru->nama_guru, 0, 1) }}
                        </div>
                        <h3 class="teacher-name">{{ $kelas->guru->nama_guru }}</h3>
                        <p class="teacher-role">Guru Pengampu</p>

                        <div class="teacher-stats">
                            <div class="stat-item">
                                <span class="stat-label"><i class="bi bi-telephone"></i> Kontak</span>
                                <span class="stat-value">{{ $kelas->guru->nomor_telepon ?? '-' }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label"><i class="bi bi-envelope"></i> Email</span>
                                <span class="stat-value">{{ $kelas->guru->email ?? '-' }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label"><i class="bi bi-calendar-check"></i> Kelas Dibuat</span>
                                <span class="stat-value">{{ $kelas->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="empty-state" style="padding: 1rem;">
                            <i class="bi bi-person-x"></i>
                            <p style="margin: 0;">Belum ada guru yang ditugaskan ke kelas ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content: Student List -->
        <div class="main-column">
            <div class="premium-card">
                <div class="card-header" style="justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <i class="bi bi-people-fill" style="color: var(--primary-color); font-size: 1.25rem;"></i>
                        <h2 class="card-title">Daftar Siswa</h2>
                    </div>
                    <div style="background: #EEF2FF; color: #4F46E5; padding: 0.35rem 1rem; border-radius: 2rem; font-weight: 700; font-size: 0.9rem;">
                        Total: {{ $kelas->siswa->count() ?? 0 }} Siswa
                    </div>
                </div>
                <div class="card-body" style="padding: 0;">
                    @if($kelas->siswa && $kelas->siswa->count() > 0)
                        <div style="overflow-x: auto;">
                            <table class="student-table">
                                <thead>
                                    <tr>
                                        <th style="padding-left: 2rem;">Profil Siswa</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Tanggal Lahir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kelas->siswa as $siswa)
                                        <tr>
                                            <td style="padding-left: 2rem;">
                                                <a href="{{ route('siswa.show', $siswa->nomor_induk_siswa) }}" style="text-decoration: none; color: inherit; display: block;">
                                                    <div class="student-item" style="transition: transform 0.2s ease;">
                                                        <div class="student-avatar">
                                                            {{ substr($siswa->nama_siswa, 0, 1) }}
                                                        </div>
                                                        <div class="student-info">
                                                            <span class="student-name">{{ $siswa->nama_siswa }}</span>
                                                            <span class="student-id">NIS: {{ $siswa->nomor_induk_siswa }}</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                            <td>
                                                @if($siswa->jenis_kelamin == 'Laki-laki' || $siswa->jenis_kelamin == 'L')
                                                    <span style="color: #3B82F6;"><i class="bi bi-gender-male"></i> Laki-laki</span>
                                                @else
                                                    <span style="color: #EC4899;"><i class="bi bi-gender-female"></i> Perempuan</span>
                                                @endif
                                            </td>
                                            <td>{{ $siswa->tgl_lahir ? \Carbon\Carbon::parse($siswa->tgl_lahir)->locale('id')->isoFormat('D MMMM Y') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-person-dash"></i>
                            <p style="font-weight: 500;">Belum ada siswa yang terdaftar di kelas ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
