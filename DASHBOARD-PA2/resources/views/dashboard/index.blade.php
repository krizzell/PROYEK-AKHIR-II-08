@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-layout">
    <!-- Kiri: Main Content -->
    <div class="dashboard-main">
        <div class="page-header">
            <h1 class="page-title">Overview</h1>
            <p class="page-subtitle">Ringkasan operasional dan statistik sekolah hari ini.</p>
        </div>

        <!-- Statistics Cards Section -->
        <div class="stats-container">
            <!-- Card 1: Total Siswa -->
            <div class="stat-card-item">
                <div class="stat-card-content">
                    <div class="stat-card-info">
                        <p class="stat-card-title">Total Siswa</p>
                        <h2 class="stat-card-value">{{ $siswa_count }}</h2>
                    </div>
                    <div class="stat-card-icon icon-emerald">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2: Data Perkembangan -->
            <div class="stat-card-item">
                <div class="stat-card-content">
                    <div class="stat-card-info">
                        <p class="stat-card-title">Data Perkembangan</p>
                        <h2 class="stat-card-value">{{ $perkembangan_recent }}</h2>
                    </div>
                    <div class="stat-card-icon icon-blue">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>

            <!-- Card 3: Tagihan Belum Dibayar -->
            <div class="stat-card-item">
                <div class="stat-card-content">
                    <div class="stat-card-info">
                        <p class="stat-card-title">Tagihan Belum Dibayar</p>
                        <h2 class="stat-card-value">{{ $pembayaran_pending }}</h2>
                    </div>
                    <div class="stat-card-icon icon-orange">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>

            <!-- Card 4: Pengumuman -->
            <div class="stat-card-item">
                <div class="stat-card-content">
                    <div class="stat-card-info">
                        <p class="stat-card-title">Pengumuman Aktif</p>
                        <h2 class="stat-card-value">{{ $pengumuman_active }}</h2>
                    </div>
                    <div class="stat-card-icon icon-yellow">
                        <i class="bi bi-megaphone"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanan: Sidebar Aktivitas -->
    <div class="dashboard-sidebar">
        <div class="activities-wrapper">
            <div class="activities-header">
                <h3 class="activities-title">Aktivitas Terbaru</h3>
                <span class="activities-badge">Hari ini</span>
            </div>

            <div class="activities-card">
                @if($activities->isEmpty())
                    <div class="empty-state">
                        <i class="bi bi-inbox empty-icon"></i>
                        <p>Belum ada aktivitas baru</p>
                    </div>
                @else
                    <div class="activity-list">
                        @foreach($activities as $index => $activity)
                            <div class="activity-item">
                                @if($index < 2)
                                    <div class="glowing-dot"></div>
                                @endif
                                <div class="activity-avatar">
                                    {{ strtoupper(substr($activity['name'], 0, 1)) }}
                                </div>
                                <div class="activity-content">
                                    <div class="activity-text">
                                        <span class="activity-name">{{ $activity['name'] }}</span> {{ strtolower($activity['action']) }}
                                    </div>
                                    <div class="activity-meta">
                                        <span class="activity-time">{{ $activity['time']->diffForHumans() }}</span>
                                        <span class="activity-class">{{ $activity['class'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    /* Dashboard Asymmetric Layout */
    .dashboard-layout {
        display: grid;
        grid-template-columns: 1fr 450px;
        gap: 32px;
        align-items: start;
    }

    .dashboard-main {
        background: #FFFFFF;
        border-radius: 16px;
        padding: 32px;
        border: 1px solid #E5E7EB;
    }

    @media (max-width: 1100px) {
        .dashboard-layout {
            grid-template-columns: 1fr;
        }
    }

    /* Page Header */
    .page-header {
        margin-bottom: 40px;
    }

    /* Stats Grid */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: 1fr;
        }
    }

    /* Stat Cards */
.stat-card-item {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid rgba(226, 232, 240, 0.6);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08),
                0 4px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

    .stat-card-item:hover {
        box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
        border-color: rgba(226, 232, 240, 1);
    }

    .stat-card-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-card-info {
        display: flex;
        flex-direction: column;
    }

    .stat-card-title {
        font-size: 13px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-card-value {
        font-size: 36px;
        font-weight: 700;
        color: #111827;
        margin: 0;
        line-height: 1.2;
        letter-spacing: -1px;
    }

    /* Soft Duotone Icons */
    .stat-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .icon-emerald { background: rgba(34, 197, 94, 0.1); color: #22C55E; }
    .icon-blue { background: rgba(59, 130, 246, 0.1); color: #3B82F6; }
    .icon-orange { background: rgba(255, 122, 0, 0.1); color: #FF7A00; }
    .icon-yellow { background: rgba(255, 122, 0, 0.1); color: #FF7A00; }

    /* Activities Sidebar */
    .activities-wrapper {
        background: #FFFFFF;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #E5E7EB;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        position: sticky;
        top: 100px;
    }

    .activities-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .activities-title {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .activities-badge {
        background: #F3F4F6;
        color: #6B7280;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        position: relative;
        padding-bottom: 16px;
        border-bottom: 1px solid #F1F5F9;
    }

    .activity-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .glowing-dot {
        position: absolute;
        top: 0;
        left: -4px;
        width: 8px;
        height: 8px;
        background-color: #FF7A00;
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(249, 115, 22, 0.6);
        animation: pulse-glow 2s infinite;
    }

    @keyframes pulse-glow {
        0% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4); }
        70% { box-shadow: 0 0 0 6px rgba(249, 115, 22, 0); }
        100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
    }

    .activity-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        color: #000000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .activity-text {
        font-size: 14px;
        color: #6B7280;
        line-height: 1.4;
    }

    .activity-name {
        font-weight: 600;
        color: #000000;
    }

    .activity-meta {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .activity-time {
        font-size: 12px;
        color: #000000;
    }

    .activity-class {
        font-size: 12px;
        font-weight: 600;
        color: #FF7A00;
        background: rgba(249, 115, 22, 0.1);
        padding: 2px 6px;
        border-radius: 4px;
    }

    .empty-state {
        text-align: center;
        padding: 40px 0;
        color: #6B7280;
    }

    .empty-icon {
        font-size: 32px;
        margin-bottom: 8px;
        color:   #CBD5E1;
    }
    .page-subtitle {
        color: #6B7280;
    }
</style>
@endsection
