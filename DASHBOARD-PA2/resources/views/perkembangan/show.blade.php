@extends('layouts.app')

@section('title', 'Detail Perkembangan')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --primary-light: #FFEDE3;
        --primary-dark: #EA580C;
        --success-color: #10B981;
        --success-light: #D1FAE5;
        --warning-color: #F59E0B;
        --warning-light: #FEF3C7;
        --danger-color: #EF4444;
        --danger-light: #FEE2E2;
        --info-color: #0EA5E9;
        --info-light: #E0F2FE;
        --neutral-50: #F9FAFB;
        --neutral-100: #F3F4F6;
        --neutral-200: #E5E7EB;
        --neutral-300: #D1D5DB;
        --neutral-500: #6B7280;
        --neutral-600: #4B5563;
        --neutral-800: #1F2937;
        --neutral-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .page-wrapper {
        background: transparent;
        min-height: 100vh;
        padding: 2rem 0;
        font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
    }

    .container-lg {
        max-width: 1000px;
        margin: 0 auto;
    }

    .premium-card {
        background: #FFFFFF;
        border-radius: 1.25rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .premium-card-header {
        padding: 2rem 2.5rem 1.5rem;
        border-bottom: 1px solid var(--neutral-100);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .premium-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .premium-subtitle {
        color: var(--neutral-500);
        font-size: 0.95rem;
        margin-top: 0.5rem;
    }

    .premium-card-body {
        padding: 2.5rem;
    }

    /* Student Profile Header */
    .profile-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        background: var(--neutral-50);
        padding: 1.5rem;
        border-radius: 1rem;
        border: 1px solid var(--neutral-200);
        margin-bottom: 2.5rem;
    }

    .profile-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .profile-label {
        font-size: 0.85rem;
        color: var(--neutral-500);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .profile-value {
        font-size: 1.1rem;
        color: var(--neutral-900);
        font-weight: 700;
        margin: 0;
    }

    /* Section Typography */
    .section-label {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--neutral-800);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Category Cards */
    .category-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2.5rem;
    }

    .category-item {
        background: #FFFFFF;
        border: 1px solid var(--neutral-200);
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: all 0.2s ease;
    }

    .category-item:hover {
        border-color: var(--primary-color);
        box-shadow: var(--shadow-sm);
        transform: translateY(-2px);
    }

    .category-info {
        flex: 1;
    }

    .category-name {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin-bottom: 0.25rem;
    }

    .category-desc {
        font-size: 0.9rem;
        color: var(--neutral-500);
        margin: 0;
        line-height: 1.5;
    }

    .category-score {
        background: var(--primary-light);
        color: var(--primary-dark);
        font-weight: 700;
        font-size: 1.1rem;
        padding: 0.5rem 1.25rem;
        border-radius: 2rem;
        white-space: nowrap;
    }

    /* Status Pencapaian */
    .status-container {
        text-align: center;
        padding: 2.5rem 2rem;
        border-radius: 1rem;
        margin-bottom: 2.5rem;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .status-container.status-bb { background: var(--danger-light); border-color: rgba(239, 68, 68, 0.2); }
    .status-container.status-mb { background: var(--warning-light); border-color: rgba(245, 158, 11, 0.2); }
    .status-container.status-bsh { background: var(--info-light); border-color: rgba(14, 165, 233, 0.2); }
    .status-container.status-bsb { background: var(--success-light); border-color: rgba(16, 185, 129, 0.2); }

    .status-badge-lg {
        display: inline-block;
        padding: 0.75rem 2rem;
        border-radius: 3rem;
        font-size: 1.25rem;
        font-weight: 800;
        margin-bottom: 1rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .status-bb .status-badge-lg { background: #FFFFFF; color: var(--danger-color); }
    .status-mb .status-badge-lg { background: #FFFFFF; color: var(--warning-color); }
    .status-bsh .status-badge-lg { background: #FFFFFF; color: var(--info-color); }
    .status-bsb .status-badge-lg { background: #FFFFFF; color: var(--success-color); }

    .status-description {
        font-size: 1rem;
        color: var(--neutral-700);
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* Notes Section */
    .notes-box {
        background: #FFFFFF;
        border: 1px solid var(--neutral-200);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .notes-content {
        font-size: 0.95rem;
        color: var(--neutral-600);
        line-height: 1.6;
        margin: 0;
        white-space: pre-wrap;
    }

    /* Meta Info */
    .meta-info {
        display: flex;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        background: var(--neutral-50);
        border-radius: 0.75rem;
        color: var(--neutral-500);
        font-size: 0.85rem;
        margin-bottom: 2rem;
    }

    /* Action Buttons */
    .action-bar {
        display: flex;
        gap: 1rem;
        justify-content: flex-start;
        align-items: center;
        border-top: 1px solid var(--neutral-100);
        padding-top: 2rem;
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
        background: var(--warning-color);
        color: #FFFFFF;
        box-shadow: 0 4px 6px rgba(245, 158, 11, 0.2);
    }
    .btn-edit:hover { background: #D97706; transform: translateY(-2px); color: #fff; }

    .btn-delete {
        background: var(--danger-color);
        color: #FFFFFF;
        box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2);
    }
    .btn-delete:hover { background: #DC2626; transform: translateY(-2px); color: #fff; }

    .btn-back {
        background: #FFFFFF;
        color: var(--neutral-700);
        border: 1px solid var(--neutral-300);
        margin-left: auto;
    }
    .btn-back:hover { background: var(--neutral-50); color: var(--neutral-900); }

</style>

<div class="page-wrapper">
    <div class="container-lg">
        <div class="premium-card">
            
            <div class="premium-card-header">
                <div>
                    <h1 class="premium-title">
                        <i class="bi bi-person-lines-fill" style="color: var(--primary-color);"></i>
                        Detail Perkembangan Anak
                    </h1>
                    <div class="premium-subtitle">
                        Laporan komprehensif kemampuan akademik, sosial, dan emosional siswa
                    </div>
                </div>
            </div>

            <div class="premium-card-body">
                
                <!-- PROFILE GRID -->
                <div class="profile-grid">
                    <div class="profile-item">
                        <span class="profile-label">Nama Siswa</span>
                        <p class="profile-value">{{ $perkembangan->siswa->nama_siswa ?? '-' }}</p>
                    </div>
                    <div class="profile-item">
                        <span class="profile-label">Kelas</span>
                        <p class="profile-value">{{ $perkembangan->siswa->kelas->nama_kelas ?? '-' }}</p>
                    </div>
                    <div class="profile-item">
                        <span class="profile-label">Guru Penilai</span>
                        <p class="profile-value">{{ $perkembangan->guru->nama_guru ?? '-' }}</p>
                    </div>
                    <div class="profile-item">
                        <span class="profile-label">Periode Laporan</span>
                        @php
                            $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            $periode = ($perkembangan->bulan ? $bulan[$perkembangan->bulan] : '-') . ' ' . ($perkembangan->tahun ?? '-');
                        @endphp
                        <p class="profile-value">{{ $periode }}</p>
                    </div>
                </div>

                <!-- CATEGORY SCORES -->
                <h3 class="section-label">
                    <i class="bi bi-bar-chart-fill" style="color: var(--primary-color);"></i>
                    Skor Kategori
                </h3>
                
                <div class="category-list">
                    @php 
                        $perkembangan->load('kategoriDetails'); 
                        // Map specific icons to categories
                        $icons = [
                            'Akademik' => 'bi-book-half',
                            'Sosial' => 'bi-people-fill',
                            'Emosional' => 'bi-emoji-smile-fill'
                        ];

                        $deskripsiPerKategori = [
                            'akademik' => [
                                1 => 'Belum mengenali materi dasar',
                                2 => 'Mulai mengenali materi dengan bantuan',
                                3 => 'Mulai mencoba memahami materi',
                                4 => 'Cukup memahami materi sederhana',
                                5 => 'Mulai berkembang dalam pembelajaran',
                                6 => 'Memahami materi cukup baik',
                                7 => 'Mampu belajar mandiri',
                                8 => 'Memahami materi dengan baik dan konsisten',
                                9 => 'Sangat aktif dalam pembelajaran',
                                10 => 'Sangat optimal dan melampaui target belajar'
                            ],
                            'sosial' => [
                                1 => 'Kesulitan berinteraksi',
                                2 => 'Mulai mengenali interaksi sosial',
                                3 => 'Mulai berinteraksi sederhana',
                                4 => 'Cukup mampu berinteraksi',
                                5 => 'Mulai berkembang dalam kerja sama',
                                6 => 'Berinteraksi cukup baik',
                                7 => 'Mampu bekerja sama mandiri',
                                8 => 'Sangat baik dan konsisten bersosialisasi',
                                9 => 'Sangat aktif dan percaya diri',
                                10 => 'Menjadi contoh positif bagi teman'
                            ],
                            'emosional' => [
                                1 => 'Belum mampu mengontrol emosi',
                                2 => 'Mulai mengenali emosi',
                                3 => 'Mulai mencoba mengendalikan emosi',
                                4 => 'Cukup mampu mengontrol emosi',
                                5 => 'Mulai berkembang secara emosional',
                                6 => 'Emosi cukup stabil',
                                7 => 'Mampu mengendalikan emosi mandiri',
                                8 => 'Stabil dan konsisten dalam pengendalian diri',
                                9 => 'Sangat baik dalam regulasi emosi',
                                10 => 'Sangat matang dan positif secara emosional'
                            ]
                        ];
                    @endphp
                    
                    @if ($perkembangan->kategoriDetails && count($perkembangan->kategoriDetails) > 0)
                        @foreach ($perkembangan->kategoriDetails as $detail)
                            @php 
                                $icon = $icons[$detail->nama_kategori] ?? 'bi-check-circle-fill'; 
                                $kategoriLower = strtolower($detail->nama_kategori);
                                $nilai = intval($detail->nilai);
                                $deskripsiText = $deskripsiPerKategori[$kategoriLower][$nilai] ?? $detail->deskripsi;
                            @endphp
                            <div class="category-item">
                                <div style="font-size: 1.5rem; color: var(--neutral-400);">
                                    <i class="bi {{ $icon }}"></i>
                                </div>
                                <div class="category-info">
                                    <h4 class="category-name">{{ $detail->nama_kategori }}</h4>
                                    <p class="category-desc"><strong>{{ $nilai }}.</strong> {{ $deskripsiText }}</p>
                                </div>
                                <div class="category-score">
                                    {{ $detail->nilai }}<span style="font-size: 0.8em; font-weight: 500; opacity: 0.8;">/10</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div style="text-align: center; padding: 2rem; color: var(--neutral-500); background: var(--neutral-50); border-radius: 1rem;">
                            Belum ada detail nilai per kategori.
                        </div>
                    @endif
                </div>

                <!-- MAIN STATUS -->
                @php
                    $statusConfig = [
                        'BB' => ['label' => 'Belum Berkembang', 'class' => 'status-bb'],
                        'MB' => ['label' => 'Mulai Berkembang', 'class' => 'status-mb'],
                        'BSH' => ['label' => 'Berkembang Sesuai Harapan', 'class' => 'status-bsh'],
                        'BSB' => ['label' => 'Berkembang Sangat Baik', 'class' => 'status-bsb']
                    ];
                    $currentStatus = $statusConfig[$perkembangan->status_utama] ?? null;
                    
                    $templateDescriptions = [
                        'BB' => 'Anak belum menunjukkan kemampuan dalam aspek ini. Perlu dukungan dan bimbingan intensif dari guru untuk mengembangkan kompetensi ini.',
                        'MB' => 'Anak mulai menunjukkan kemampuan dalam aspek ini namun masih memerlukan bimbingan. Perlu terus didukung untuk mencapai perkembangan yang lebih baik.',
                        'BSH' => 'Anak menunjukkan kemampuan yang sesuai dengan harapan untuk usia/tingkatannya. Anak mampu melaksanakan tugas dengan cukup baik.',
                        'BSB' => 'Anak menunjukkan kemampuan yang sangat menonjol dalam aspek ini. Anak mampu melaksanakan tugas dengan sangat baik dan melampaui harapan.'
                    ];
                @endphp

                @if($currentStatus)
                <div class="status-container {{ $currentStatus['class'] }}">
                    <div class="status-badge-lg">
                        {{ $perkembangan->status_utama }} - {{ $currentStatus['label'] }}
                    </div>
                    <p class="status-description">
                        {{ $templateDescriptions[$perkembangan->status_utama] ?? 'Tidak ada deskripsi tersedia.' }}
                    </p>
                </div>
                @endif

                <!-- TEACHER NOTES -->
                @if ($perkembangan->deskripsi)
                <h3 class="section-label">
                    <i class="bi bi-journal-text" style="color: var(--primary-color);"></i>
                    Catatan Tambahan Guru
                </h3>
                <div class="notes-box">
                    <p class="notes-content">{{ $perkembangan->deskripsi }}</p>
                </div>
                @endif

                <!-- META INFO -->
                <div class="meta-info">
                    <div>
                        <i class="bi bi-clock"></i> <strong>Dibuat:</strong> {{ $perkembangan->created_at->format('d M Y, H:i') }}
                    </div>
                    <div>
                        <i class="bi bi-arrow-clockwise"></i> <strong>Diperbarui:</strong> {{ $perkembangan->updated_at->format('d M Y, H:i') }}
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="action-bar">
                    <a href="{{ route('perkembangan.edit', $perkembangan->id_perkembangan) }}" class="btn-premium btn-edit">
                        <i class="bi bi-pencil-square"></i> Edit Data
                    </a>
                    <form action="{{ route('perkembangan.destroy', $perkembangan->id_perkembangan) }}" method="POST" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-premium btn-delete" data-delete-btn data-item-name="Laporan Perkembangan ini">
                            <i class="bi bi-trash3"></i> Hapus
                        </button>
                    </form>
                    <a href="{{ route('perkembangan.index') }}" class="btn-premium btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- SweetAlert Delete Confirmation Setup (assuming global script handles data-delete-btn) -->
<script>
    document.querySelectorAll('[data-delete-btn]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const itemName = this.getAttribute('data-item-name') || 'item ini';
            const form = this.closest('form');
            
            if(window.Swal) {
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: `Anda akan menghapus ${itemName}. Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if(confirm(`Apakah Anda yakin ingin menghapus ${itemName}?`)) {
                    form.submit();
                }
            }
        });
    });
</script>
@endsection
