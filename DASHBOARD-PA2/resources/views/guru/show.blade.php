@extends('layouts.app')

@section('title', 'Detail Guru')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --primary-light: #FFF4EC;
        --primary-dark: #EA580C;
        --accent-color: #0EA5E9;
        --neutral-50: #F8FAFC;
        --neutral-100: #F1F5F9;
        --neutral-200: #E2E8F0;
        --neutral-300: #CBD5E1;
        --neutral-500: #64748B;
        --neutral-600: #475569;
        --neutral-700: #334155;
        --neutral-900: #0F172A;
        --card-bg: rgba(255, 255, 255, 0.78);
        --card-border: rgba(255, 255, 255, 0.65);
        --shadow-soft: 0 24px 70px rgba(15, 23, 42, 0.14);
    }

    .page-shell {
        position: relative;
        padding: 1rem 0 2rem;
    }

    .page-shell::before,
    .page-shell::after {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        filter: blur(20px);
        pointer-events: none;
        opacity: 0.6;
    }

    .page-shell::before {
        top: -70px;
        right: -120px;
        background: radial-gradient(circle, rgba(249, 115, 22, 0.26), rgba(249, 115, 22, 0));
    }

    .page-shell::after {
        left: -140px;
        bottom: 30px;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.18), rgba(14, 165, 233, 0));
    }

    .hero-card,
    .profile-card,
    .info-card {
        border-radius: 1.5rem;
        overflow: hidden;
        border: 1px solid var(--card-border);
        box-shadow: var(--shadow-soft);
        backdrop-filter: blur(18px);
        background: var(--card-bg);
    }

    .guru-show-layout {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 1.5rem;
    }

    .hero-band {
        padding: 1.5rem 1.75rem;
        background: linear-gradient(135deg, #0F172A 0%, #1F2937 42%, #F97316 100%);
        color: white;
    }

    .hero-band-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .hero-eyebrow {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        opacity: 0.82;
        margin-bottom: 0.5rem;
    }

    .hero-title {
        font-size: clamp(1.8rem, 3vw, 2.6rem);
        font-weight: 800;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .hero-subtitle {
        margin: 0.75rem 0 0;
        max-width: 52rem;
        line-height: 1.7;
        color: rgba(255, 255, 255, 0.82);
    }

    .hero-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        margin-top: 1.1rem;
    }

    .glass-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 0.85rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.16);
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 380px minmax(0, 1fr);
        gap: 1.5rem;
        align-items: start;
    }

    .profile-card {
        position: sticky;
        top: 1.25rem;
    }

    .profile-visual {
        padding: 1.5rem;
        background:
            radial-gradient(circle at top left, rgba(249, 115, 22, 0.18), transparent 38%),
            radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.14), transparent 35%),
            linear-gradient(180deg, rgba(255,255,255,0.92), rgba(248,250,252,0.84));
    }

    .portrait-frame {
        padding: 1rem;
        border-radius: 1.35rem;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(226, 232, 240, 0.85);
    }

    .portrait-media {
        aspect-ratio: 1 / 1.08;
        border-radius: 1.15rem;
        overflow: hidden;
        background: linear-gradient(135deg, #F97316, #0EA5E9);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .portrait-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .portrait-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        background:
            radial-gradient(circle at top, rgba(255,255,255,0.18), transparent 35%),
            linear-gradient(135deg, #F97316 0%, #EA580C 48%, #0EA5E9 100%);
    }

    .portrait-initials {
        width: 132px;
        height: 132px;
        border-radius: 50%;
        border: 6px solid rgba(255,255,255,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        background: rgba(255,255,255,0.14);
    }

    .portrait-meta {
        margin-top: 1rem;
        display: grid;
        gap: 0.55rem;
    }

    .portrait-name {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--neutral-900);
    }

    .portrait-role {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        width: fit-content;
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        background: rgba(249, 115, 22, 0.12);
        color: #C2410C;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .quick-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
        margin-top: 0.9rem;
    }

    .stat-pill {
        padding: 0.85rem 1rem;
        border-radius: 1rem;
        background: rgba(255,255,255,0.72);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .stat-label {
        display: block;
        margin-bottom: 0.35rem;
        color: var(--neutral-500);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .stat-value {
        color: var(--neutral-900);
        font-weight: 700;
        word-break: break-word;
        line-height: 1.4;
    }

    .info-card {
        padding: 1.35rem;
    }

    .info-section {
        padding: 1.1rem 0;
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
        margin: 0 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--neutral-900);
        font-size: 1rem;
        font-weight: 700;
    }

    .section-icon {
        width: 30px;
        height: 30px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        background: linear-gradient(135deg, #FFF7ED, #E0F2FE);
        border: 1px solid rgba(251, 146, 60, 0.15);
        font-size: 0.95rem;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr);
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
        line-height: 1.65;
    }

    .detail-value-muted {
        color: var(--neutral-500);
    }

    .badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
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
        padding: 0.3rem 0.6rem;
        border-radius: 0.5rem;
        font-size: 0.8rem;
    }

    .kelas-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-premium {
        padding: 0.8rem 1.3rem;
        border-radius: 0.95rem;
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
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        box-shadow: 0 12px 24px rgba(249, 115, 22, 0.22);
    }

    .btn-edit:hover {
        transform: translateY(-1px);
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.86);
        color: var(--neutral-700);
        border: 1px solid var(--neutral-300);
    }

    .btn-back:hover {
        background: white;
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .profile-card {
            position: static;
        }

        .hero-band {
            padding: 1.25rem;
        }

        .detail-row {
            grid-template-columns: 1fr;
        }

        .quick-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-shell">
    <div class="guru-show-layout">
        @php
            $photoPath = data_get($guru, 'foto_guru')
                ?? data_get($guru, 'foto')
                ?? data_get($guru, 'gambar')
                ?? data_get($guru, 'photo')
                ?? data_get($guru, 'avatar');
            $guruInitials = collect(explode(' ', trim($guru->nama_guru ?? 'G')))
                ->filter()
                ->map(fn ($part) => mb_substr($part, 0, 1))
                ->take(2)
                ->implode('');
            $akun = $guru->akun()->first();
            $isAdmin = $akun?->is_super_admin ?? false;
            $kelas = $guru->kelasAmpuan()->pluck('nama_kelas')->toArray();
        @endphp

        <section class="hero-card">
            <div class="hero-band">
                <div class="hero-band-top">
                    <div>
                        <div class="hero-eyebrow">Dashboard Guru</div>
                        <h1 class="hero-title"><i class="bi bi-person-badge-fill"></i> Detail Guru</h1>
                        <p class="hero-subtitle">Informasi lengkap profil dan riwayat profesional tenaga pendidik</p>
                    </div>
                    <div class="hero-badges">
                        <span class="glass-badge"><i class="bi bi-shield-check"></i> {{ $isAdmin ? 'Super Admin' : ucfirst($akun?->role ?? 'User') }}</span>
                        <span class="glass-badge"><i class="bi bi-mortarboard-fill"></i> {{ $guru->jabatan ?? 'Guru' }}</span>
                        <span class="glass-badge"><i class="bi bi-collection-fill"></i> {{ count($kelas) }} Kelas</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="content-grid">
            <aside class="profile-card">
                <div class="profile-visual">
                    <div class="portrait-frame">
                        <div class="portrait-media">
                            @if ($photoPath)
                                <img src="{{ asset('storage/' . $photoPath) }}" alt="Foto {{ $guru->nama_guru }}">
                            @else
                                <div class="portrait-fallback">
                                    <div class="portrait-initials">{{ $guruInitials ?: 'G' }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="portrait-meta">
                        <h2 class="portrait-name">{{ $guru->nama_guru }}</h2>
                        <div class="portrait-role">
                            <i class="bi bi-geo-alt-fill"></i>
                            {{ $guru->jenis_kelamin ?? 'Jenis kelamin belum diisi' }}
                        </div>

                        <div class="quick-stats">
                            <div class="stat-pill">
                                <span class="stat-label">NIP</span>
                                <div class="stat-value">{{ $guru->nip_guru ?? '-' }}</div>
                            </div>
                            <div class="stat-pill">
                                <span class="stat-label">Status</span>
                                <div class="stat-value">{{ $isAdmin ? 'Super Admin' : 'Aktif' }}</div>
                            </div>
                            <div class="stat-pill">
                                <span class="stat-label">No. HP</span>
                                <div class="stat-value">{{ $guru->no_hp ?? '-' }}</div>
                            </div>
                            <div class="stat-pill">
                                <span class="stat-label">Kelas</span>
                                <div class="stat-value">{{ count($kelas) }} kelas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <section class="info-card">
                <div class="info-section">
                    <div class="section-title">
                        <div class="section-icon"><i class="bi bi-person-fill"></i></div>
                        Data Pribadi
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Nama Lengkap</div>
                        <div class="detail-value"><strong>{{ $guru->nama_guru }}</strong></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">NIP</div>
                        <div class="detail-value">{{ $guru->nip_guru ?? '-' }}</div>
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

                <div class="info-section">
                    <div class="section-title">
                        <div class="section-icon"><i class="bi bi-telephone-fill"></i></div>
                        Kontak & Pendidikan
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">No. HP</div>
                        <div class="detail-value">{{ $guru->no_hp ?? '-' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Email</div>
                        <div class="detail-value">
                            <a href="mailto:{{ $guru->email }}" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">{{ $guru->email ?? '-' }}</a>
                        </div>
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

                <div class="info-section">
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
                            <span class="badge {{ $isAdmin ? 'badge-admin' : 'badge-guru' }}">
                                {{ $isAdmin ? 'Super Admin' : ucfirst($akun?->role ?? 'User') }}
                            </span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Kelas yang Diampu</div>
                        <div class="detail-value">
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

                <div class="info-section">
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

                    <div class="action-buttons" style="margin-top: 1.5rem;">
                        <a href="{{ route('guru.edit', $guru->id_guru) }}" class="btn-premium btn-edit">
                            <i class="bi bi-pencil"></i> Edit Guru
                        </a>
                        <a href="{{ route('guru.index') }}" class="btn-premium btn-back">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

@endsection
