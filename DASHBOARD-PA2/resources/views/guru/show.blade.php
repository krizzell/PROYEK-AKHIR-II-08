@extends('layouts.app')

@section('title', 'Detail Guru')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --primary-light: #FFEDE3;
        --primary-dark: #EA580C;
        --neutral-50: #F9FAFB;
        --neutral-100: #F3F4F6;
        --neutral-200: #E5E7EB;
        --neutral-500: #6B7280;
        --neutral-600: #4B5563;
        --neutral-900: #111827;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .detail-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid var(--neutral-200);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .card-section {
        padding: 1.5rem;
        border-bottom: 1px solid var(--neutral-200);
    }

    .card-section:last-child {
        border-bottom: none;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--neutral-900);
        margin: 0 0 1.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-icon {
        width: 28px;
        height: 28px;
        background: var(--primary-light);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 0.9rem;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .detail-row:last-child {
        margin-bottom: 0;
    }

    .detail-label {
        font-weight: 600;
        color: var(--neutral-600);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        color: var(--neutral-900);
        font-size: 0.95rem;
    }

    .detail-value-muted {
        color: var(--neutral-500);
    }

    .badge {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-admin {
        background: #FEE2E2;
        color: #991B1B;
    }

    .badge-guru {
        background: #DBEAFE;
        color: #1E40AF;
    }

    .badge-role {
        background: #F0F9FF;
        color: #0369A1;
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        margin-right: 0.5rem;
    }

    .kelas-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--neutral-200);
    }

    .btn-premium {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-edit {
        background: var(--primary-color);
        color: white;
    }

    .btn-edit:hover {
        background: var(--primary-dark);
    }

    .btn-back {
        background: white;
        color: var(--neutral-700);
        border: 1px solid var(--neutral-300);
    }

    .btn-back:hover {
        background: var(--neutral-50);
    }

    @media (max-width: 768px) {
        .detail-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <h1><i class="bi bi-person-circle"></i> Detail Guru</h1>
</div>

<div class="detail-card">
    <!-- SECTION 1: Data Pribadi -->
    <div class="card-section">
        <div class="section-title">
            <div class="section-icon"><i class="bi bi-person-fill"></i></div>
            Data Pribadi
        </div>

        <div class="detail-row">
            <div class="detail-label">NIP</div>
            <div class="detail-value">{{ $guru->nip_guru ?? '-' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Nama Lengkap</div>
            <div class="detail-value"><strong>{{ $guru->nama_guru }}</strong></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Jenis Kelamin</div>
            <div class="detail-value">{{ $guru->jenis_kelamin ?? '-' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Tanggal Lahir</div>
            <div class="detail-value">
                @if($guru->tanggal_lahir)
                    {{ \Carbon\Carbon::parse($guru->tanggal_lahir)->format('d-m-Y') }}
                @else
                    -
                @endif
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Alamat</div>
            <div class="detail-value">{{ $guru->alamat ?? '-' }}</div>
        </div>
    </div>

    <!-- SECTION 2: Kontak & Pendidikan -->
    <div class="card-section">
        <div class="section-title">
            <div class="section-icon"><i class="bi bi-telephone-fill"></i></div>
            Kontak & Pendidikan
        </div>

        <div class="detail-row">
            <div class="detail-label">No. HP</div>
            <div class="detail-value">{{ $guru->no_hp }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Email</div>
            <div class="detail-value"><a href="mailto:{{ $guru->email }}" style="color: var(--primary-color); text-decoration: none;">{{ $guru->email }}</a></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Pendidikan</div>
            <div class="detail-value">{{ $guru->pendidikan_terakhir ?? '-' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Jurusan</div>
            <div class="detail-value">{{ $guru->jurusan ?? '-' }}</div>
        </div>
    </div>

    <!-- SECTION 3: Informasi Pekerjaan -->
    <div class="card-section">
        <div class="section-title">
            <div class="section-icon"><i class="bi bi-briefcase-fill"></i></div>
            Informasi Pekerjaan
        </div>

        <div class="detail-row">
            <div class="detail-label">Jabatan</div>
            <div class="detail-value">
                <span class="badge {{ $guru->jabatan === 'Kepala Sekolah' ? 'badge-admin' : 'badge-guru' }}">
                    {{ $guru->jabatan ?? '-' }}
                </span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Role Sistem</div>
            <div class="detail-value">
                @php
                    $akun = $guru->akun()->first();
                    $isAdmin = $akun?->is_super_admin ?? false;
                @endphp
                <span class="badge {{ $isAdmin ? 'badge-admin' : 'badge-guru' }}">
                    {{ $isAdmin ? 'Super Admin' : ucfirst($akun?->role ?? 'User') }}
                </span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Kelas yang Diampu</div>
            <div class="detail-value">
                @php
                    $kelas = $guru->kelasAmpuan()->pluck('nama_kelas')->toArray();
                @endphp
                @if(count($kelas) > 0)
                    <div class="kelas-list">
                        @foreach($kelas as $k)
                            <span class="badge-role">{{ $k }}</span>
                        @endforeach
                    </div>
                @else
                    <span class="detail-value-muted">Belum ada kelas yang diampu</span>
                @endif
            </div>
        </div>
    </div>

    <!-- SECTION 4: Informasi Sistem -->
    <div class="card-section">
        <div class="section-title">
            <div class="section-icon"><i class="bi bi-info-circle-fill"></i></div>
            Informasi Sistem
        </div>

        <div class="detail-row">
            <div class="detail-label">Dibuat Tanggal</div>
            <div class="detail-value">{{ $guru->created_at->format('d-m-Y H:i') }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Diubah Terakhir</div>
            <div class="detail-value">{{ $guru->updated_at->format('d-m-Y H:i') }}</div>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="card-section">
        <div class="action-buttons" style="margin: 0; padding: 0; border: none;">
            <a href="{{ route('guru.edit', $guru->id_guru) }}" class="btn-premium btn-edit">
                <i class="bi bi-pencil"></i> Edit Guru
            </a>
            <a href="{{ route('guru.index') }}" class="btn-premium btn-back">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

@endsection
