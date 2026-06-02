@extends('layouts.app')

@section('title', 'Generate Akun Siswa Bulk')

@section('content')
<style>
    :root {
        --text-primary: #111827;
        --text-secondary: #6B7280;
        --border-color: #E5E7EB;
        --success-color: #10B981;
        --warning-color: #F59E0B;
        --danger-color: #EF4444;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .breadcrumb {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .breadcrumb a {
        color: #F97316;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .card h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-top: 0;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-box {
        background: #F3F4F6;
        border-left: 4px solid #F97316;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .info-box strong {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }

    .info-box p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #FAFBFC;
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        border-radius: 0.75rem;
        text-align: center;
    }

    .stat-card .label {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .stat-card .value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .stat-card.warning .value {
        color: var(--warning-color);
    }

    .stat-card.success .value {
        color: var(--success-color);
    }

    .button-group {
        display: flex;
        gap: 0.75rem;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        border: none;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        font-size: 0.95rem;
    }

    .btn-primary {
        background: var(--success-color);
        color: white;
    }

    .btn-primary:hover {
        background: #059669;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: white;
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: #F3F4F6;
        border-color: #D1D5DB;
        color: var(--text-primary);
        transform: translateY(-2px);
    }

    .warning-message {
        background: #FEF3C7;
        border-left: 4px solid var(--warning-color);
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        color: #92400E;
    }

    .success-message {
        background: #D1FAE5;
        border-left: 4px solid var(--success-color);
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        color: #065F46;
    }

    .checklist {
        list-style: none;
        padding: 0;
        margin: 1rem 0;
    }

    .checklist li {
        padding: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .checklist li:last-child {
        border-bottom: none;
    }

    .checklist i {
        color: var(--success-color);
        font-size: 1.2rem;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <h1><i class="bi bi-people-fill"></i> Generate Akun Siswa</h1>
    <div class="breadcrumb">
        <a href="{{ route('akun.index') }}">Kelola Akun</a> / Generate Bulk Siswa
    </div>
</div>

@if ($siswaWithoutAccount > 0)
    <div class="card">
        <h2><i class="bi bi-info-circle"></i> Konfirmasi Generate Akun</h2>
        
        <div class="info-box">
            <strong><i class="bi bi-exclamation-triangle"></i> Perhatian</strong>
            <p>Sistem akan membuat akun untuk semua siswa yang belum memiliki akun dengan kredensial default.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card warning">
                <div class="label">Siswa Tanpa Akun</div>
                <div class="value">{{ $siswaWithoutAccount }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Role Akun</div>
                <div class="value" style="font-size: 1rem; color: #666;">Orangtua/Siswa</div>
            </div>
        </div>

        <h3 style="font-size: 1rem; margin: 2rem 0 1rem 0; color: var(--text-primary); font-weight: 600;">
            <i class="bi bi-check3-all"></i> Sistem akan:
        </h3>
        <ul class="checklist">
            <li><i class="bi bi-check-circle-fill"></i> Membuat akun untuk <strong>{{ $siswaWithoutAccount }} siswa</strong></li>
            <li><i class="bi bi-check-circle-fill"></i> Generate username dari nama siswa secara otomatis</li>
            <li><i class="bi bi-check-circle-fill"></i> Set password default: <strong>password123</strong></li>
            <li><i class="bi bi-check-circle-fill"></i> Role: <strong>Orangtua/Siswa</strong></li>
            <li><i class="bi bi-check-circle-fill"></i> Status: Regular (bukan Super Admin)</li>
        </ul>

        <h3 style="font-size: 1rem; margin: 2rem 0 1rem 0; color: var(--text-primary); font-weight: 600;">
            <i class="bi bi-shield-lock"></i> Catatan Keamanan:
        </h3>
        <div style="background: #EEF2FF; border-left: 4px solid #6366F1; padding: 1rem; border-radius: 0.5rem; color: #312E81; font-size: 0.95rem;">
            <strong>Pastikan password default sudah diubah oleh siswa saat login pertama kali!</strong><br>
            Anda dapat mengatur policy untuk force change password on first login di sistem authentication.
        </div>

        <form action="{{ route('akun.bulkGenerateSiswaStore') }}" method="POST" style="margin-top: 2rem;">
            @csrf
            <div class="button-group">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Ya, Generate Sekarang
                </button>
                <a href="{{ route('akun.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
            </div>
        </form>
    </div>
@else
    <div class="card">
        <div class="success-message">
            <i class="bi bi-check-circle-fill"></i> Semua siswa sudah memiliki akun. Tidak ada siswa baru yang perlu di-generate akun.
        </div>

        <div class="button-group">
            <a href="{{ route('akun.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Kelola Akun
            </a>
        </div>
    </div>
@endif

@endsection
