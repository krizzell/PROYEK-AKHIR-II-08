@extends('layouts.app')

@section('title', 'Apply All Tagihan')

@section('content')
<style>
    :root {
        --text-primary: #111827;
        --text-secondary: #6B7280;
        --border-color: #E5E7EB;
        --success-color: #10B981;
        --warning-color: #F59E0B;
        --danger-color: #EF4444;
        --primary-color: #F97316;
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
        color: var(--primary-color);
        text-decoration: none;
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
        border-left: 4px solid var(--primary-color);
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
        grid-template-columns: repeat(3, 1fr);
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

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        font-size: 0.95rem;
        color: var(--text-primary);
        background: white;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
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
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: #E85000;
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

    .error-message {
        background: #FEE2E2;
        border: 1px solid #FCA5A5;
        border-left: 4px solid var(--danger-color);
        color: #991B1B;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .success-message {
        background: #D1FAE5;
        border-left: 4px solid var(--success-color);
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        color: #065F46;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <h1><i class="bi bi-lightning-fill"></i> Apply All Tagihan</h1>
    <div class="breadcrumb">
        <a href="{{ route('tagihan.index') }}">Tagihan SPP</a> / Apply All
    </div>
</div>

@if ($errors->any())
    <div class="error-message">
        <strong><i class="bi bi-exclamation-triangle"></i> Validasi Gagal</strong>
        <ul style="margin: 0.5rem 0 0 1.25rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($siswaBelumPunyaTagihan > 0)
    <div class="card">
        <h2><i class="bi bi-info-circle"></i> Konfirmasi Apply All Tagihan</h2>

        <div class="info-box">
            <strong><i class="bi bi-exclamation-triangle"></i> Perhatian</strong>
            <p>Sistem akan membuat tagihan <strong>{{ $periode }}</strong> hanya untuk siswa yang belum memiliki tagihan pada periode ini.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Siswa</div>
                <div class="value">{{ $totalSiswa }}</div>
            </div>
            <div class="stat-card success">
                <div class="label">Sudah Punya Tagihan</div>
                <div class="value">{{ $siswaSudahPunyaTagihan }}</div>
            </div>
            <div class="stat-card warning">
                <div class="label">Siswa Belum Punya Tagihan</div>
                <div class="value">{{ $siswaBelumPunyaTagihan }}</div>
            </div>
        </div>

        <form action="{{ route('tagihan.bulkCreateStore') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="jumlah_tagihan" class="form-label">Jumlah Tagihan SPP per Siswa <span style="color: var(--danger-color);">*</span></label>
                <input type="number" name="jumlah_tagihan" id="jumlah_tagihan"
                       class="form-control @error('jumlah_tagihan') is-invalid @enderror"
                       value="{{ old('jumlah_tagihan') }}" placeholder="Contoh: 250000" min="1" step="1" required>
                @error('jumlah_tagihan')
                    <div style="color: #DC2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <h3 style="font-size: 1rem; margin: 2rem 0 1rem 0; color: var(--text-primary); font-weight: 600;">
                <i class="bi bi-check3-all"></i> Sistem akan:
            </h3>
            <ul class="checklist">
                <li><i class="bi bi-check-circle-fill"></i> Membuat tagihan untuk <strong>{{ $siswaBelumPunyaTagihan }} siswa</strong></li>
                <li><i class="bi bi-check-circle-fill"></i> Menggunakan periode otomatis: <strong>{{ $periode }}</strong></li>
                <li><i class="bi bi-check-circle-fill"></i> Mengatur status awal menjadi <strong>Belum Bayar</strong></li>
                <li><i class="bi bi-check-circle-fill"></i> Melewati siswa yang sudah memiliki tagihan periode ini</li>
            </ul>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Apply Sekarang
                </button>
                <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
            </div>
        </form>
    </div>
@else
    <div class="card">
        <div class="success-message">
            <i class="bi bi-check-circle-fill"></i> Semua siswa sudah memiliki tagihan untuk periode <strong>{{ $periode }}</strong>. Tidak ada tagihan baru yang perlu dibuat.
        </div>

        <div class="stats-grid" style="margin-bottom: 0;">
            <div class="stat-card">
                <div class="label">Total Siswa</div>
                <div class="value">{{ $totalSiswa }}</div>
            </div>
            <div class="stat-card success">
                <div class="label">Sudah Punya Tagihan</div>
                <div class="value">{{ $siswaSudahPunyaTagihan }}</div>
            </div>
            <div class="stat-card warning">
                <div class="label">Siswa Belum Punya Tagihan</div>
                <div class="value">0</div>
            </div>
        </div>

        <div class="button-group">
            <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Tagihan SPP
            </a>
        </div>
    </div>
@endif

@endsection
