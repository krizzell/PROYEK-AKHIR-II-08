@extends('layouts.app')

@section('title', 'Detail Pengajuan Perpindahan')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --success-color: #10B981;
        --danger-color: #EF4444;
        --neutral-100: #F3F4F6;
        --neutral-200: #E5E7EB;
        --neutral-600: #4B5563;
        --neutral-700: #374151;
        --neutral-900: #111827;
    }

    .detail-container {
        max-width: 960px;
        margin: 0 auto;
        font-family: 'Montserrat', sans-serif;
    }

    .detail-container * {
        font-family: 'Montserrat', sans-serif;
    }

    .detail-card {
        background: white;
        border: 1px solid var(--neutral-200);
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    }

    .breadcrumb-custom {
        margin-bottom: 20px;
        font-size: 14px;
    }

    .breadcrumb-custom a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
    }

    .header-section {
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--neutral-200);
    }

    .header-section h1 {
        font-size: 26px;
        font-weight: 700;
        margin: 0 0 8px 0;
        color: var(--neutral-900);
    }

    .header-section p {
        color: var(--neutral-600);
        margin: 0;
        font-size: 14px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .info-item {
        background: #F9FAFB;
        border: 1px solid var(--neutral-200);
        border-radius: 12px;
        padding: 16px;
    }

    .info-label {
        display: block;
        color: var(--neutral-600);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 8px;
    }

    .info-value {
        color: var(--neutral-900);
        font-size: 15px;
        font-weight: 500;
        margin: 0;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: capitalize;
    }

    .status-menunggu {
        background: #FEF3C7;
        color: #92400E;
    }

    .status-disetujui {
        background: #D1FAE5;
        color: #047857;
    }

    .status-ditolak {
        background: #FEE2E2;
        color: #B91C1C;
    }

    .text-box {
        background: white;
        border: 1px solid var(--neutral-200);
        border-radius: 12px;
        padding: 16px;
        color: var(--neutral-700);
        line-height: 1.6;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="detail-container">
    <div class="breadcrumb-custom">
        <a href="{{ route('transfer-siswa.index') }}">
            <i class="bi bi-arrow-left"></i> Kembali ke Pengajuan Perpindahan
        </a>
    </div>

    <div class="detail-card">
        <div class="header-section">
            <h1>Detail Pengajuan Perpindahan</h1>
            <p>Informasi lengkap pengajuan dan kapasitas kelas saat ini.</p>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Nama Siswa</span>
                <p class="info-value">{{ $pengajuan->siswa->nama_siswa ?? '-' }}</p>
            </div>
            <div class="info-item">
                <span class="info-label">Status</span>
                <span class="status-badge status-{{ $pengajuan->status }}">{{ $pengajuan->status }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Kelas Asal</span>
                <p class="info-value">{{ $pengajuan->kelasAsal->nama_kelas ?? '-' }} ({{ $jumlahKelasAsal }} siswa saat ini)</p>
            </div>
            <div class="info-item">
                <span class="info-label">Kelas Tujuan</span>
                <p class="info-value">{{ $pengajuan->kelasTujuan->nama_kelas ?? '-' }} ({{ $jumlahKelasTujuan }} siswa saat ini)</p>
            </div>
            <div class="info-item">
                <span class="info-label">Guru Pengaju</span>
                <p class="info-value">{{ $pengajuan->guruPengaju->nama_guru ?? '-' }}</p>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Pengajuan</span>
                <p class="info-value">{{ $pengajuan->tanggal_pengajuan->format('d-m-Y H:i') }}</p>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Diproses</span>
                <p class="info-value">{{ $pengajuan->tanggal_diproses ? $pengajuan->tanggal_diproses->format('d-m-Y H:i') : '-' }}</p>
            </div>
            <div class="info-item">
                <span class="info-label">Diproses Oleh</span>
                <p class="info-value">{{ $pengajuan->guruPemroses->nama_guru ?? '-' }}</p>
            </div>
        </div>

        <span class="info-label">Alasan Perpindahan</span>
        <div class="text-box">{{ $pengajuan->alasan }}</div>

        @if($pengajuan->alasan_penolakan)
            <span class="info-label">Alasan Penolakan</span>
            <div class="text-box">{{ $pengajuan->alasan_penolakan }}</div>
        @endif

    </div>
</div>
@endsection
