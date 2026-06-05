@extends('layouts.app')

@section('title', 'Perkembangan Siswa')

@section('content')
<style>
    :root {
        --text-primary: #111827;
        --text-secondary: #6B7280;
        --border-color: #E5E7EB;
        --hover-bg: #F9FAFB;
        --button-gray: #6B7280;
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
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-add {
        background: #FF7A00;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-add:hover {
        background: #E65E00;
        transform: translateY(-2px);
    }

    .table-container {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .table thead {
        background: white;
        border-bottom: 2px solid var(--border-color);
    }

    .table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background: var(--hover-bg);
    }

    .table td {
        padding: 1rem;
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .table td strong {
        font-weight: 600;
    }

    .teacher-info {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 0.35rem;
        font-size: 0.85rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-bb {
        background: #FEE2E2;
        color: #991B1B;
    }

    .status-mb {
        background: #FEF3C7;
        color: #92400E;
    }

    .status-bsh {
        background: #CFFAFE;
        color: #164E63;
    }

    .status-bsb {
        background: #DCFCE7;
        color: #166534;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 0.5rem;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 1rem;
        text-decoration: none;
    }

    .btn-view {
        color: #000000;
        background: white;
        border: 1px solid #000000;
    }

    .btn-view:hover {
        background: #000000;
        color: white;
    }

    .btn-edit {
        color: #F59E0B;
        background: white;
        border: 1px solid #F59E0B;
    }

    .btn-edit:hover {
        background: #F59E0B;
        color: white;
    }

    .btn-delete {
        color: #EF4444;
        background: white;
        border: 1px solid #EF4444;
    }

    .btn-delete:hover {
        background: #EF4444;
        color: white;
    }

    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 3rem;
        opacity: 0.3;
        display: block;
        margin-bottom: 1rem;
    }

    .count-info {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }

    .count-info strong {
        color: var(--text-primary);
    }

    .filter-section {
        background: #FFFFFF;
        margin-bottom: 1.5rem;
        padding: 1.5rem;
        border: 1px solid var(--border-color);
        border-radius: 12px;
    }

    .filter-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .filter-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-primary);
        display: block;
        margin-bottom: 0.5rem;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 0.625rem;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        font-size: 0.95rem;
        color: var(--text-primary);
        background: white;
        transition: all 0.2s ease;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #FF7A00;
        background-color: white;
        box-shadow: 0 0 0 2px rgba(255, 122, 0, 0.1);
    }

    .filter-group select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23111827' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        padding-right: 2rem;
    }

    .filter-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-filter {
        padding: 0.625rem 1.25rem;
        background: #FF7A00;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-filter:hover {
        background: #E65E00;
        transform: translateY(-1px);
    }

    .btn-reset {
        padding: 0.625rem 1.25rem;
        background: white;
        color: var(--button-gray);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-reset:hover {
        background: var(--hover-bg);
        border-color: var(--button-gray);
    }

    .bulk-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1rem 1.25rem;
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        background: #fff;
    }

    .bulk-actions.hidden {
        display: none;
    }

    .bulk-actions-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .btn-bulk-delete {
        background: #EF4444;
        color: white;
        padding: 0.75rem 1.25rem;
        border-radius: 0.75rem;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-bulk-delete:hover {
        background: #DC2626;
        transform: translateY(-1px);
    }

    .btn-bulk-delete:disabled {
        background: #FCA5A5;
        cursor: not-allowed;
        transform: none;
        opacity: 0.8;
    }

    .btn-bulk-clear {
        background: white;
        color: var(--button-gray);
        padding: 0.75rem 1.25rem;
        border-radius: 0.75rem;
        border: 1px solid var(--border-color);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-bulk-clear:hover {
        background: var(--hover-bg);
        border-color: var(--button-gray);
    }

    .select-all-wrapper,
    .row-checkbox-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .perkembangan-checkbox,
    #selectAllPerkembangan {
        width: 18px;
        height: 18px;
        accent-color: #EF4444;
        cursor: pointer;
    }
</style>

<div class="page-header">
    <h1><i class="bi bi-graph-up"></i> Perkembangan Siswa</h1>
    @php
        // Show create button only for guru biasa (is_super_admin = 0)
        // Hide for kepala sekolah (is_super_admin = 1)
        $canCreate = !session('is_super_admin');
        $canDelete = false;
    @endphp
    
    @if($canCreate)
        <a href="{{ route('perkembangan.create') }}" class="btn-add">
            <i class="bi bi-plus-lg"></i> Tambah Perkembangan
        </a>
    @endif
</div>

@php
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $statusOptions = [
        'BB' => 'BB - Belum Berkembang',
        'MB' => 'MB - Mulai Berkembang',
        'BSH' => 'BSH - Berkembang Sesuai Harapan',
        'BSB' => 'BSB - Berkembang Sangat Baik',
    ];
@endphp

<div class="filter-section">
    <div class="filter-title">
        <i class="bi bi-funnel"></i> Filter Data
    </div>
    <form action="{{ route('perkembangan.index') }}" method="GET">
        <div class="filter-row">
            <div class="filter-group">
                <label for="nis">NISN</label>
                <input type="text" id="nis" name="nis" value="{{ request('nis') }}" placeholder="Cari NISN...">
            </div>
            <div class="filter-group">
                <label for="nama">Nama Siswa</label>
                <input type="text" id="nama" name="nama" value="{{ request('nama') }}" placeholder="Cari nama...">
            </div>
            <div class="filter-group">
                <label for="kelas">Kelas</label>
                <select id="kelas" name="kelas">
                    <option value="">-- Semua Kelas --</option>
                    @foreach ($kelasOptions as $kelas)
                        <option value="{{ $kelas->id_kelas }}" {{ request('kelas') == $kelas->id_kelas ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="periode">Periode</label>
                <select id="periode" name="periode">
                    <option value="">-- Semua Periode --</option>
                    @foreach ($periodeOptions as $periodeOption)
                        @php
                            $periodeValue = $periodeOption->bulan . '-' . $periodeOption->tahun;
                            $periodeLabel = ($bulan[$periodeOption->bulan] ?? '-') . ' ' . $periodeOption->tahun;
                        @endphp
                        <option value="{{ $periodeValue }}" {{ request('periode') == $periodeValue ? 'selected' : '' }}>
                            {{ $periodeLabel }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="status">Status Capaian</label>
                <select id="status" name="status">
                    <option value="">-- Semua Status --</option>
                    @foreach ($statusOptions as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}" {{ request('status') == $statusValue ? 'selected' : '' }}>
                            {{ $statusLabel }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-filter">
                <i class="bi bi-search"></i> Cari
            </button>
            <a href="{{ route('perkembangan.index') }}" class="btn-reset">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
        </div>
    </form>
</div>

@if ($perkembangan->isEmpty())
    <div class="table-container">
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>Belum ada data perkembangan</p>
        </div>
    </div>
@else
    <div class="count-info">
        Menampilkan <strong>{{ $perkembangan->firstItem() }}</strong> - <strong>{{ $perkembangan->lastItem() }}</strong> dari <strong>{{ $perkembangan->total() }}</strong> data perkembangan
    </div>
    
    @if($canDelete)
        <form id="bulkDeleteForm" action="{{ route('perkembangan.bulkDestroy') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <div class="bulk-actions hidden">
            <div class="bulk-actions-info">
                <span id="selectedPerkembanganCount" style="font-weight: 600; color: var(--text-primary); font-size: 0.95rem;"></span>
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <button type="button" class="btn-bulk-clear" id="clearSelectionBtn">
                    <i class="bi bi-x-circle"></i> Batal Pilih
                </button>
                <button type="submit" class="btn-bulk-delete" id="bulkDeleteBtn" form="bulkDeleteForm" disabled>
                    <i class="bi bi-trash"></i> Hapus yang Ditandai
                </button>
            </div>
        </div>
    @endif

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    @if($canDelete)
                        <th style="width: 50px;">
                            <div class="select-all-wrapper">
                                <input type="checkbox" id="selectAllPerkembangan" aria-label="Pilih semua perkembangan">
                            </div>
                        </th>
                    @endif
                    <th style="width: 50px;">No</th>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Periode</th>
                    <th>Kategori</th>
                    <th>Status Capaian</th>
                    <th style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($perkembangan as $item)
                @php
                    $kelasName = $item->siswa?->kelas?->nama_kelas ?? '';
                    $siswaName = $item->siswa?->nama_siswa ?? 'Siswa Hilang';
                    $nisn = $item->nomor_induk_siswa ?? '';
                    $guruName = $item->guru?->nama_guru ?? '';
                    $periode = ($item->bulan ? ($bulan[$item->bulan] ?? '-') : '-') . ' ' . ($item->tahun ?? '-');
                    $statusValue = $item->status_utama ?? '';
                    $searchText = implode(' ', [
                        $siswaName,
                        $guruName,
                        $kelasName,
                        $periode,
                        $item->kategori,
                        $statusValue,
                    ]);
                @endphp
                <tr
                    data-search-text="{{ \Illuminate\Support\Str::lower($searchText) }}"
                    data-nis="{{ \Illuminate\Support\Str::lower($nisn) }}"
                    data-nama="{{ \Illuminate\Support\Str::lower($siswaName) }}"
                    data-kelas="{{ \Illuminate\Support\Str::lower($kelasName) }}"
                    data-periode="{{ \Illuminate\Support\Str::lower($periode) }}"
                    data-status="{{ \Illuminate\Support\Str::lower($statusValue) }}"
                >
                    @if($canDelete)
                        <td>
                            <div class="row-checkbox-wrapper">
                                <input type="checkbox" class="perkembangan-checkbox" form="bulkDeleteForm" name="selected_perkembangan[]" value="{{ $item->id_perkembangan }}" aria-label="Pilih perkembangan {{ $item->siswa->nama_siswa ?? 'Siswa Hilang' }}">
                            </div>
                        </td>
                    @endif
                    <td>{{ $perkembangan->firstItem() + $loop->index }}</td>
                    <td>
                        @if($item->siswa)
                            <strong>{{ $item->siswa->nama_siswa }}</strong>
                            <div class="teacher-info">{{ $item->guru->nama_guru ?? '-' }}</div>
                        @else
                            <strong style="color: #EF4444;">Siswa Hilang</strong>
                            <div class="teacher-info">{{ $item->guru->nama_guru ?? '-' }}</div>
                        @endif
                    </td>
                    <td>
                        @if($item->siswa && $item->siswa->kelas)
                            {{ $item->siswa->kelas->nama_kelas }}
                        @else
                            <span style="color: #EF4444;">-</span>
                        @endif
                    </td>
                    <td>
                        {{ $periode }}
                    </td>
                    <td>{{ $item->kategori }}</td>
                    <td>
                        @if ($item->status_utama)
                            @php
                                $statusMap = [
                                    'BB' => ['label' => 'BB', 'class' => 'status-bb'],
                                    'MB' => ['label' => 'MB', 'class' => 'status-mb'],
                                    'BSH' => ['label' => 'BSH', 'class' => 'status-bsh'],
                                    'BSB' => ['label' => 'BSB', 'class' => 'status-bsb']
                                ];
                                $status = $statusMap[$item->status_utama] ?? null;
                            @endphp
                            @if ($status)
                                <span class="status-badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                            @endif
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('perkembangan.show', $item->id_perkembangan) }}" class="btn-action btn-view" title="Lihat">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($canCreate)
                                <a href="{{ route('perkembangan.edit', $item->id_perkembangan) }}" class="btn-action btn-edit" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif
                            @if($canDelete)
                                <form action="{{ route('perkembangan.destroy', $item->id_perkembangan) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action btn-delete" title="Hapus" data-delete-btn data-item-name="perkembangan ini">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $perkembangan->links('pagination::bootstrap-5') }}
    </div>
@endif

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('selectAllPerkembangan');
        const rowCheckboxes = Array.from(document.querySelectorAll('.perkembangan-checkbox'));
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
        const selectedPerkembanganCount = document.getElementById('selectedPerkembanganCount');
        const clearSelectionBtn = document.getElementById('clearSelectionBtn');
        const bulkActionsContainer = document.querySelector('.bulk-actions');

        if (!selectAllCheckbox || !bulkDeleteBtn || !bulkDeleteForm || !selectedPerkembanganCount || !clearSelectionBtn || !bulkActionsContainer) {
            return;
        }

        bulkActionsContainer.classList.add('hidden');

        const syncButtonState = () => {
            const selectedCount = rowCheckboxes.filter((checkbox) => checkbox.checked).length;

            if (selectedCount > 0) {
                bulkActionsContainer.classList.remove('hidden');
            } else {
                bulkActionsContainer.classList.add('hidden');
            }
            
            bulkDeleteBtn.disabled = selectedCount === 0;
            bulkDeleteBtn.innerHTML = selectedCount > 0
                ? `<i class="bi bi-trash"></i> Hapus yang Ditandai (${selectedCount})`
                : '<i class="bi bi-trash"></i> Hapus yang Ditandai';

            selectedPerkembanganCount.innerHTML = selectedCount > 0
                ? `<i class="bi bi-check-circle-fill" style="color: #10B981; margin-right: 0.5rem;"></i> ${selectedCount} perkembangan dipilih`
                : '';

            selectAllCheckbox.checked = selectedCount > 0 && selectedCount === rowCheckboxes.length;
            selectAllCheckbox.indeterminate = selectedCount > 0 && selectedCount < rowCheckboxes.length;
        };

        selectAllCheckbox.addEventListener('change', function () {
            rowCheckboxes.forEach((checkbox) => {
                checkbox.checked = this.checked;
            });
            syncButtonState();
        });

        clearSelectionBtn.addEventListener('click', function () {
            rowCheckboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
            syncButtonState();
        });

        rowCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', syncButtonState);
        });

        bulkDeleteForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const selectedCount = rowCheckboxes.filter((checkbox) => checkbox.checked).length;
            if (selectedCount === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum ada perkembangan dipilih',
                    text: 'Centang minimal satu perkembangan terlebih dahulu.',
                });
                return;
            }

            Swal.fire({
                title: 'Hapus ' + selectedCount + ' perkembangan?',
                text: 'Data yang dihapus tidak dapat dipulihkan',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                backdrop: true,
                allowOutsideClick: true,
                allowEscapeKey: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    bulkDeleteForm.submit();
                }
            });
        });

        syncButtonState();
    });
</script>
@endsection
