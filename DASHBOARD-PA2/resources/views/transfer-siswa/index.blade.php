@extends('layouts.app')

@section('title', 'Pengajuan Perpindahan Kelas')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --success-color: #10B981;
        --danger-color: #EF4444;
        --neutral-50: #F9FAFB;
        --neutral-100: #F3F4F6;
        --neutral-200: #E5E7EB;
        --neutral-600: #4B5563;
        --neutral-700: #374151;
        --neutral-900: #111827;
    }

    .transfer-container,
    .transfer-container * {
        font-family: 'Montserrat', sans-serif;
    }

    .transfer-container {
        background: transparent;
        border-radius: 0;
        padding: 0;
        box-shadow: none;
    }

    .header-section {
        margin-bottom: 24px;
    }

    .header-section h1 {
        font-size: 30px;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-section p {
        font-size: 14px;
        color: var(--neutral-600);
        margin: 0;
    }

    .info-box {
        background: #EFF6FF;
        border-left: 4px solid #3B82F6;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-size: 14px;
        color: #1E40AF;
    }

    .kelas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .kelas-card,
    .section-card {
        background: white;
        border: 1px solid var(--neutral-200);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }

    .kelas-card {
        background: linear-gradient(135deg, #F9FAFB 0%, #FAFBFC 100%);
        border-width: 2px;
    }

    .kelas-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 8px 24px rgba(249, 115, 22, 0.12);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }

    .kelas-header,
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--neutral-200);
    }

    .kelas-info h3,
    .section-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0 0 4px 0;
    }

    .kelas-info p,
    .section-header p {
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
        gap: 12px;
        padding: 12px;
        background: white;
        border-radius: 8px;
        border: 1px solid var(--neutral-200);
    }

    .siswa-nama {
        font-size: 13px;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0 0 2px 0;
    }

    .siswa-nis {
        font-size: 11px;
        color: var(--neutral-600);
        margin: 0;
    }

    .btn-action-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        background: linear-gradient(135deg, #F97316 0%, #E85000 100%);
        color: white;
        border: none;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-action-primary:hover {
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);
    }

    .btn-outline-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        background: white;
        color: var(--neutral-700);
        border: 1px solid var(--neutral-200);
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-outline-action:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .inline-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-icon-action {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        transition: all 0.2s ease;
    }

    .btn-icon-action:hover {
        transform: translateY(-1px);
    }

    .btn-icon-approve {
        background: #D1FAE5;
        color: #047857;
    }

    .btn-icon-approve:hover {
        background: #10B981;
        color: white;
    }

    .btn-icon-reject {
        background: #FEE2E2;
        color: #B91C1C;
    }

    .btn-icon-reject:hover {
        background: #EF4444;
        color: white;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        font-size: 0.78rem;
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

    .table {
        margin: 0;
    }

    .table th {
        font-size: 12px;
        color: var(--neutral-700);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .table td {
        vertical-align: middle;
        font-size: 13px;
    }

    .empty-state {
        text-align: center;
        padding: 32px 20px;
        color: var(--neutral-600);
    }

    .empty-state i {
        font-size: 42px;
        margin-bottom: 12px;
        opacity: 0.45;
    }

    .teacher-workspace {
        background: white;
        border: 1px solid var(--neutral-200);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
        margin-bottom: 32px;
    }

    .teacher-hero {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 24px;
        padding: 24px;
        background: #FFFFFF;
        border-bottom: 1px solid var(--neutral-200);
    }

    .teacher-hero h3 {
        font-size: 22px;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0 0 8px 0;
    }

    .teacher-hero p {
        color: var(--neutral-600);
        font-size: 14px;
        margin: 0;
        line-height: 1.6;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .summary-card {
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(229, 231, 235, 0.95);
        border-radius: 12px;
        padding: 14px;
    }

    .summary-label {
        display: block;
        color: var(--neutral-600);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 8px;
    }

    .summary-value {
        display: block;
        color: var(--neutral-900);
        font-size: 24px;
        font-weight: 700;
        line-height: 1;
    }

    .teacher-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 24px;
        border-bottom: 1px solid var(--neutral-200);
        background: #FFFFFF;
    }

    .search-field {
        position: relative;
        flex: 1;
        max-width: 520px;
    }

    .search-field i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--neutral-600);
        font-size: 15px;
    }

    .search-field input {
        width: 100%;
        height: 44px;
        border: 1px solid var(--neutral-200);
        border-radius: 10px;
        background: #F9FAFB;
        padding: 0 14px 0 42px;
        font-family: 'Montserrat', sans-serif;
        font-size: 14px;
        color: var(--neutral-900);
        outline: none;
    }

    .search-field input:focus {
        border-color: var(--primary-color);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.10);
    }

    .class-chip-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .class-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 11px;
        background: #F9FAFB;
        border: 1px solid var(--neutral-200);
        border-radius: 999px;
        color: var(--neutral-700);
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .class-chip strong {
        color: var(--primary-color);
    }

    .teacher-student-list {
        padding: 0 24px 22px;
    }

    .student-row {
        display: grid;
        grid-template-columns: 1fr 160px 130px;
        align-items: center;
        gap: 18px;
        padding: 16px 0;
        border-bottom: 1px solid var(--neutral-200);
    }

    .student-row:last-child {
        border-bottom: none;
    }

    .student-main {
        display: flex;
        align-items: center;
        gap: 13px;
        min-width: 0;
    }

    .student-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #FFF7ED;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .student-meta {
        min-width: 0;
    }

    .student-name {
        margin: 0 0 4px 0;
        font-size: 14px;
        font-weight: 600;
        color: var(--neutral-900);
    }

    .student-nis {
        margin: 0;
        color: var(--neutral-600);
        font-size: 12px;
        font-weight: 500;
    }

    .student-class-badge {
        justify-self: start;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 12px;
        background: #F3F4F6;
        color: var(--neutral-700);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .student-action {
        justify-self: end;
    }

    @media (max-width: 1100px) {
        .teacher-hero,
        .teacher-toolbar {
            grid-template-columns: 1fr;
            flex-direction: column;
            align-items: stretch;
        }

        .summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .class-chip-row {
            justify-content: flex-start;
        }
    }

    @media (max-width: 768px) {
        .teacher-hero,
        .teacher-toolbar,
        .teacher-student-list {
            padding-left: 16px;
            padding-right: 16px;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .student-row {
            grid-template-columns: 1fr;
            gap: 10px;
            align-items: start;
        }

        .student-action {
            justify-self: stretch;
        }

        .student-action .btn-action-primary {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="transfer-container">
    <div class="header-section">
        <h1>
            <i class="bi bi-arrow-left-right"></i>
            Pengajuan Perpindahan Kelas Siswa
        </h1>
        <p>
            @if($isSuperAdmin)
                Tinjau pengajuan guru, setujui atau tolak sesuai kapasitas kelas.
            @else
                Ajukan perpindahan siswa dari kelas yang Anda pegang untuk diproses kepala sekolah.
            @endif
        </p>
    </div>

    <div class="info-box">
        <i class="bi bi-info-circle" style="margin-right: 8px;"></i>
        <strong>Aturan Perpindahan:</strong>
        <ul style="margin: 8px 0 0 24px; padding: 0;">
            <li>Urutan kelas: Tulip, Melati, Anggrek, Ros, Sakura, Mawar.</li>
            <li>Setiap kelas minimal 20 siswa dan maksimal 30 siswa.</li>
            <li>Perpindahan hanya terjadi jika pengajuan disetujui kepala sekolah / super admin.</li>
        </ul>
    </div>

    @if($isSuperAdmin)
        <div class="section-card" style="margin-bottom: 24px;">
            <div class="section-header">
                <div>
                    <h3>Pengajuan Menunggu Approval</h3>
                    <p>{{ $pendingPengajuan->count() }} pengajuan perlu diproses</p>
                </div>
            </div>

            @if($pendingPengajuan->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p style="margin: 0;">Belum ada pengajuan menunggu</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Kelas Asal</th>
                                <th>Kelas Tujuan</th>
                                <th>Guru Pengaju</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingPengajuan as $pengajuan)
                                <tr>
                                    <td><strong>{{ $pengajuan->siswa->nama_siswa ?? '-' }}</strong></td>
                                    <td>{{ $pengajuan->kelasAsal->nama_kelas ?? '-' }}</td>
                                    <td>{{ $pengajuan->kelasTujuan->nama_kelas ?? '-' }}</td>
                                    <td>{{ $pengajuan->guruPengaju->nama_guru ?? '-' }}</td>
                                    <td>{{ $pengajuan->tanggal_pengajuan->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <div class="inline-actions">
                                            <form action="{{ route('transfer-siswa.approve', $pengajuan->id_pengajuan) }}" method="POST" style="margin: 0;">
                                                @csrf
                                                <button type="submit" class="btn-icon-action btn-icon-approve" title="Setujui pengajuan">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn-icon-action btn-icon-reject" title="Tolak pengajuan" data-reject-button data-reject-url="{{ route('transfer-siswa.reject', $pengajuan->id_pengajuan) }}">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                            <a href="{{ route('transfer-siswa.show', $pengajuan->id_pengajuan) }}" class="btn-outline-action">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        @php
            $totalKelasGuru = $kelas->count();
            $totalSiswaGuru = $kelas->sum('siswa_count');
            $studentRows = $kelas->flatMap(function ($item) {
                return $item->siswa->map(function ($siswa) use ($item) {
                    $siswa->kelas_nama_display = $item->nama_kelas;
                    return $siswa;
                });
            });
        @endphp

        <div class="teacher-workspace">
            <div class="teacher-hero">
                <div>
                    <h3>Daftar Siswa Kelas Saya</h3>
                    <p>Pilih siswa dari kelas yang Anda pegang, lalu kirim pengajuan perpindahan untuk ditinjau kepala sekolah.</p>
                </div>
                <div class="summary-grid">
                    <div class="summary-card">
                        <span class="summary-label">Kelas Anda</span>
                        <span class="summary-value">{{ $totalKelasGuru }}</span>
                    </div>
                    <div class="summary-card">
                        <span class="summary-label">Total Siswa</span>
                        <span class="summary-value">{{ $totalSiswaGuru }}</span>
                    </div>
                    <div class="summary-card">
                        <span class="summary-label">Batas Kelas</span>
                        <span class="summary-value">20-30</span>
                    </div>
                </div>
            </div>

            @if($kelas->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-building-x"></i>
                    <h3>Belum Ada Kelas</h3>
                    <p>Anda belum ditugaskan mengajar di kelas manapun.</p>
                </div>
            @else
                <div class="teacher-toolbar">
                    <div class="search-field">
                        <i class="bi bi-search"></i>
                        <input type="text" id="studentSearch" placeholder="Cari nama siswa atau NIS...">
                    </div>
                    <div class="class-chip-row">
                        @foreach($kelas as $k)
                            <span class="class-chip">
                                <i class="bi bi-building"></i>
                                {{ $k->nama_kelas }} <strong>{{ $k->siswa_count }}</strong>
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="teacher-student-list" id="studentList">
                    @forelse($studentRows as $s)
                        <div class="student-row" data-student-row data-search="{{ strtolower($s->nama_siswa . ' ' . $s->nomor_induk_siswa . ' ' . $s->kelas_nama_display) }}">
                            <div class="student-main">
                                <div class="student-avatar">{{ strtoupper(substr($s->nama_siswa, 0, 1)) }}</div>
                                <div class="student-meta">
                                    <p class="student-name">{{ $s->nama_siswa }}</p>
                                    <p class="student-nis">NIS: {{ $s->nomor_induk_siswa }}</p>
                                </div>
                            </div>
                            <span class="student-class-badge">
                                <i class="bi bi-building"></i> {{ $s->kelas_nama_display }}
                            </span>
                            <div class="student-action">
                                <a href="{{ route('transfer-siswa.transfer', $s->nomor_induk_siswa) }}" class="btn-action-primary">
                                    <i class="bi bi-send"></i> Ajukan
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p style="margin: 0;">Tidak ada siswa pada kelas yang Anda pegang.</p>
                        </div>
                    @endforelse

                    <div class="empty-state" id="studentEmptySearch" style="display: none;">
                        <i class="bi bi-search"></i>
                        <p style="margin: 0;">Siswa tidak ditemukan.</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="section-card">
        <div class="section-header">
            <div>
                <h3>Riwayat Perpindahan</h3>
                <p>{{ $riwayatPengajuan->count() }} pengajuan tersimpan</p>
            </div>
        </div>

        @if($riwayatPengajuan->isEmpty())
            <div class="empty-state">
                <i class="bi bi-clock-history"></i>
                <p style="margin: 0;">Belum ada riwayat pengajuan</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Kelas Asal</th>
                            <th>Kelas Tujuan</th>
                            <th>Guru Pengaju</th>
                            <th>Status</th>
                            <th>Diajukan</th>
                            <th>Diproses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatPengajuan as $pengajuan)
                            <tr>
                                <td><strong>{{ $pengajuan->siswa->nama_siswa ?? '-' }}</strong></td>
                                <td>{{ $pengajuan->kelasAsal->nama_kelas ?? '-' }}</td>
                                <td>{{ $pengajuan->kelasTujuan->nama_kelas ?? '-' }}</td>
                                <td>{{ $pengajuan->guruPengaju->nama_guru ?? '-' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $pengajuan->status }}">
                                        {{ $pengajuan->status }}
                                    </span>
                                </td>
                                <td>{{ $pengajuan->tanggal_pengajuan->format('d-m-Y H:i') }}</td>
                                <td>{{ $pengajuan->tanggal_diproses ? $pengajuan->tanggal_diproses->format('d-m-Y H:i') : '-' }}</td>
                                <td>
                                    <a href="{{ route('transfer-siswa.show', $pengajuan->id_pengajuan) }}" class="btn-outline-action">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('studentSearch');
        const rows = Array.from(document.querySelectorAll('[data-student-row]'));
        const emptySearch = document.getElementById('studentEmptySearch');

        if (!searchInput || rows.length === 0 || !emptySearch) {
            return;
        }

        searchInput.addEventListener('input', function () {
            const keyword = this.value.trim().toLowerCase();
            let visibleCount = 0;

            rows.forEach((row) => {
                const isVisible = row.dataset.search.includes(keyword);
                row.style.display = isVisible ? 'grid' : 'none';
                if (isVisible) {
                    visibleCount += 1;
                }
            });

            emptySearch.style.display = visibleCount === 0 ? 'block' : 'none';
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const rejectButtons = Array.from(document.querySelectorAll('[data-reject-button]'));

        rejectButtons.forEach((button) => {
            button.addEventListener('click', function () {
                Swal.fire({
                    title: 'Tolak pengajuan?',
                    input: 'textarea',
                    inputLabel: 'Alasan penolakan',
                    inputPlaceholder: 'Tuliskan alasan penolakan...',
                    inputAttributes: {
                        'aria-label': 'Alasan penolakan'
                    },
                    inputValidator: (value) => {
                        if (!value || value.trim().length < 5) {
                            return 'Alasan penolakan minimal 5 karakter.';
                        }
                    },
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Tolak',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = this.dataset.rejectUrl;
                    form.style.display = 'none';

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';

                    const reason = document.createElement('input');
                    reason.type = 'hidden';
                    reason.name = 'alasan_penolakan';
                    reason.value = result.value.trim();

                    form.appendChild(csrf);
                    form.appendChild(reason);
                    document.body.appendChild(form);
                    form.submit();
                });
            });
        });
    });
</script>
@endsection
