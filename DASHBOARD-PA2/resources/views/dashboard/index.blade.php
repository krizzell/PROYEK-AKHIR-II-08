@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Selamat datang di School Monitoring System</p>
</div>

<!-- Statistics Cards Section -->
<div class="stats-container">
    <!-- Card 1: Total Siswa -->
    <div class="stat-card-item">
        <div class="stat-card-content">
            <div class="stat-card-left">
                <p class="stat-card-title">Total Siswa</p>
                <h2 class="stat-card-value">{{ $siswa_count }}</h2>
                <p class="stat-card-description">Siswa aktif tahun ajaran 2025/2026</p>
            </div>
            <div class="stat-card-icon icon-blue">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>

    <!-- Card 2: Data Perkembangan Terbaru -->
    <div class="stat-card-item">
        <div class="stat-card-content">
            <div class="stat-card-left">
                <p class="stat-card-title">Data Perkembangan Terbaru</p>
                <h2 class="stat-card-value">{{ $perkembangan_recent }}</h2>
                <p class="stat-card-description">Data baru minggu ini</p>
            </div>
            <div class="stat-card-icon icon-green">
                <i class="bi bi-graph-up"></i>
            </div>
        </div>
    </div>

    <!-- Card 3: Pembayaran SPP Menunggu Verifikasi -->
    <div class="stat-card-item">
        <div class="stat-card-content">
            <div class="stat-card-left">
                <p class="stat-card-title">Pembayaran SPP Menunggu Verifikasi</p>
                <h2 class="stat-card-value">{{ $pembayaran_pending }}</h2>
                <p class="stat-card-description">Perlu segera diverifikasi</p>
            </div>
            <div class="stat-card-icon icon-yellow">
                <i class="bi bi-credit-card"></i>
            </div>
        </div>
    </div>

    <!-- Card 4: Pengumuman Aktif -->
    <div class="stat-card-item">
        <div class="stat-card-content">
            <div class="stat-card-left">
                <p class="stat-card-title">Jumlah Pengumuman Aktif</p>
                <h2 class="stat-card-value">{{ $pengumuman_active }}</h2>
                <p class="stat-card-description">Pengumuman yang sedang aktif</p>
            </div>
            <div class="stat-card-icon icon-purple">
                <i class="bi bi-megaphone"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities Section -->
<div class="activities-section" style="margin-top: 40px;">
    <div class="activities-header">
        <h3 class="activities-title">Aktivitas Terbaru</h3>
        <p class="activities-subtitle">Pembaruan terakhir dari sistem</p>
    </div>

    <div class="activities-card">
        @if($activities->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <p class="empty-state-text">Belum ada aktivitas</p>
            </div>
        @else
            <div class="activity-list">
                @foreach($activities as $activity)
                    <div class="activity-item">
                        <div class="activity-avatar {{ $activity['type'] == 'perkembangan' ? 'avatar-blue' : 'avatar-yellow' }}">
                            {{ strtoupper(substr($activity['name'], 0, 1)) }}
                        </div>
                        <div class="activity-middle">
                            <div class="activity-main">
                                <p class="activity-name">{{ $activity['name'] }}</p>
                                <p class="activity-action">{{ $activity['action'] }}</p>
                            </div>
                        </div>
                        <div class="activity-right">
                            <span class="activity-class-badge">{{ $activity['class'] }}</span>
                            <span class="activity-time">{{ $activity['time']->diffForHumans() }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
    /* Statistics Container */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }

    /* Stat Cards */
    .stat-card-item {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card-item:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-4px);
    }

    .stat-card-content {
        padding: 28px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .stat-card-left {
        flex: 1;
    }

    .stat-card-title {
        font-size: 12px;
        font-weight: 600;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .stat-card-value {
        font-size: 42px;
        font-weight: 700;
        color: #1A1A2E;
        margin: 0;
        line-height: 1;
        margin-bottom: 12px;
    }

    .stat-card-description {
        font-size: 12px;
        color: #6B7280;
        margin: 0;
        font-weight: 500;
    }

    .stat-card-icon {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        flex-shrink: 0;
        margin-left: 20px;
    }

    .icon-blue {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #FF6B1A;
    }

    .icon-green {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #22C55E;
    }

    .icon-yellow {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #F59E0B;
    }

    .icon-purple {
        background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
        color: #FF6B1A;
    }

    /* Activities Section */
    .activities-section {
        margin-top: 40px;
    }

    .activities-header {
        margin-bottom: 20px;
    }

    .activities-title {
        font-size: 20px;
        font-weight: 700;
        color: #1A1A2E;
        margin: 0 0 4px 0;
    }

    .activities-subtitle {
        font-size: 13px;
        color: #9CA3AF;
        margin: 0;
        font-weight: 500;
    }

    .activities-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .empty-state {
        padding: 60px 40px;
        text-align: center;
    }

    .empty-state-icon {
        font-size: 64px;
        color: #9CA3AF;
        margin-bottom: 16px;
    }

    .empty-state-text {
        font-size: 14px;
        color: #6B7280;
        margin: 0;
        font-weight: 500;
    }

    .activity-list {
        padding: 0;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 28px;
        border-bottom: 1px solid #9CA3AF;
        transition: all 0.3s ease;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item:hover {
        background-color: #fafbfc;
    }

    .activity-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        color: white;
        flex-shrink: 0;
    }

    .avatar-blue {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #FF6B1A;
    }

    .avatar-yellow {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #F59E0B;
    }

    .activity-middle {
        flex: 1;
    }

    .activity-main {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .activity-name {
        font-size: 14px;
        font-weight: 600;
        color: #1A1A2E;
        margin: 0;
    }

    .activity-action {
        font-size: 13px;
        color: #9CA3AF;
        margin: 0;
        font-weight: 500;
    }

    .activity-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        min-width: 140px;
    }

    .activity-class-badge {
        display: inline-block;
        padding: 6px 12px;
        background-color: #FFF8F4;
        color: #1A1A2E;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .activity-time {
        font-size: 12px;
        color: #6B7280;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: 1fr;
        }

        .stat-card-content {
            flex-direction: column;
        }

        .stat-card-icon {
            margin-left: 0;
            margin-top: 16px;
        }

        .activity-item {
            flex-wrap: wrap;
        }

        .activity-right {
            align-items: flex-start;
            min-width: auto;
            margin-top: 12px;
        }
    }
</style>
@endsection
