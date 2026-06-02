@extends('layouts.app')

@section('title', 'Perpindahan Kelas Siswa')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --success-color: #10B981;
        --neutral-50: #F9FAFB;
        --neutral-100: #F3F4F6;
        --neutral-200: #E5E7EB;
        --neutral-300: #D1D5DB;
        --neutral-600: #4B5563;
        --neutral-700: #374151;
        --neutral-900: #111827;
    }

    .transfer-container {
        background: transparent;
        border-radius: 0;
        padding: 0;
        box-shadow: none;
    }

    .header-section {
        margin-bottom: 24px;
        border-bottom: none;
        padding-bottom: 0;
    }

    .header-section h1 {
        font-size: 30px;
        font-weight: 800;
        color: var(--neutral-900);
        margin: 0 0 16px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-section p {
        font-size: 14px;
        color: var(--neutral-600);
        margin: 0;
    }

    .kelas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 24px;
    }

    .kelas-card {
        background: linear-gradient(135deg, #F9FAFB 0%, #FAFBFC 100%);
        border: 2px solid var(--neutral-200);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s ease;
    }

    .kelas-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 8px 24px rgba(249, 115, 22, 0.12);
        transform: translateY(-2px);
    }

    .kelas-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--neutral-200);
    }

    .kelas-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #F97316 0%, #E85000 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        flex-shrink: 0;
    }

    .kelas-info h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0 0 4px 0;
    }

    .kelas-info p {
        font-size: 12px;
        color: var(--neutral-600);
        margin: 0;
    }

    .siswa-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 400px;
        overflow-y: auto;
    }

    .siswa-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px;
        background: white;
        border-radius: 8px;
        border: 1px solid var(--neutral-200);
        transition: all 0.2s ease;
        text-decoration: none;
        color: inherit;
    }

    .siswa-item:hover {
        background: #FFF7F0;
        border-color: var(--primary-color);
    }

    .siswa-info {
        flex: 1;
    }

    .siswa-nama {
        font-size: 13px;
        font-weight: 600;
        color: var(--neutral-900);
        margin: 0 0 2px 0;
    }

    .siswa-nis {
        font-size: 11px;
        color: var(--neutral-600);
        margin: 0;
    }

    .siswa-action {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background: linear-gradient(135deg, #F97316 0%, #E85000 100%);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .siswa-action:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--neutral-600);
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .alert {
        border-radius: 10px;
        border: none;
        margin-bottom: 24px;
    }

    .alert-success {
        background: #ECFDF5;
        color: #065F46;
    }

    .alert-success i {
        margin-right: 8px;
    }

    @media (max-width: 768px) {
        .kelas-grid {
            grid-template-columns: 1fr;
        }

        .header-section h1 {
            font-size: 24px;
        }
    }
</style>

<div class="transfer-container">
    <!-- Header -->
    <div class="header-section">
        <h1>
            <i class="bi bi-arrow-left-right"></i>
            Perpindahan Kelas Siswa
        </h1>
        <p>Pilih siswa untuk memindahkan ke kelas lain</p>
    </div>

    <!-- Info Box -->
    <div style="background: #EFF6FF; border-left: 4px solid #3B82F6; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; color: #1E40AF;">
        <i class="bi bi-info-circle" style="margin-right: 8px; color: #3B82F6;"></i>
        <strong>Aturan Perpindahan:</strong>
        <ul style="margin: 8px 0 0 24px; padding: 0;">
            <li>Setiap kelas minimal 20 siswa dan maksimal 30 siswa</li>
            <li>Kelas <strong>{{ $lockedClass }}</strong> adalah kelas terkunci (tidak dapat melakukan/menerima perpindahan)</li>
        </ul>
    </div>

    <!-- Kelas Grid -->
    <div class="kelas-grid">
        @forelse($kelas as $k)
            <div class="kelas-card">
                <!-- Kelas Header -->
                <div class="kelas-header">
                    <div class="kelas-info">
                        <h3>{{ $k->nama_kelas }}</h3>
                        <p>{{ $k->siswa_count }} siswa</p>
                    </div>
                </div>

                <!-- Siswa List -->
                <div class="siswa-list">
                    @forelse($k->siswa as $s)
                        <div class="siswa-item">
                            <div class="siswa-info">
                                <p class="siswa-nama">{{ $s->nama_siswa }}</p>
                                <p class="siswa-nis">NIS: {{ $s->nomor_induk_siswa }}</p>
                            </div>
                            @if($k->nama_kelas === $lockedClass)
                                <button class="siswa-action" style="background: #E5E7EB; color: #9CA3AF; cursor: not-allowed;" disabled title="Kelas terkunci">
                                    <i class="bi bi-lock"></i>
                                    Terkunci
                                </button>
                            @else
                                <a href="{{ route('transfer-siswa.transfer', $s->nomor_induk_siswa) }}" class="siswa-action">
                                    <i class="bi bi-arrow-right"></i>
                                    Pindah
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="empty-state" style="padding: 20px;">
                            <i class="bi bi-inbox"></i>
                            <p style="margin: 0; font-size: 13px;">Tidak ada siswa</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1;">
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h3>Tidak ada kelas</h3>
                    <p>Silakan buat kelas terlebih dahulu</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
