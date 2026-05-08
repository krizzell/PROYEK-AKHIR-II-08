@extends('layouts.app')

@section('title', 'Data Siswa')

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
        background: #F97316;
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
        background: #E85000;
        transform: translateY(-2px);
    }

    .filter-section {
        margin-bottom: 1.5rem;
        padding: 1.5rem;
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
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
        transition: all 0.3s ease;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: var(--button-gray);
        box-shadow: 0 0 0 2px rgba(107, 114, 128, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-filter {
        padding: 0.625rem 1.25rem;
        background: var(--button-gray);
        color: white;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-filter:hover {
        background: #4B5563;
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
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-reset:hover {
        background: var(--hover-bg);
        border-color: var(--button-gray);
    }

    .table-container {
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        overflow: hidden;
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

    .siswa-checkbox,
    #selectAllSiswa {
        width: 18px;
        height: 18px;
        accent-color: #EF4444;
        cursor: pointer;
    }
</style>

<div class="page-header">
    <h1><i class="bi bi-people"></i> Data Siswa</h1>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('siswa.importForm') }}" class="btn-add" style="background: #06B6D4;">
            <i class="bi bi-download"></i> Import Excel
        </a>
        <a href="{{ route('siswa.create') }}" class="btn-add">
            <i class="bi bi-plus-lg"></i> Tambah Siswa
        </a>
    </div>
</div>

<!-- Filter Section -->
@if(session('success') || session('error') || session('warning') || session('import_errors'))
    <div style="margin-bottom: 1rem;">
        @if(session('success'))
            <div style="background:#ECFDF5;border-left:4px solid #10B981;padding:0.75rem;border-radius:0.5rem;margin-bottom:0.5rem;color:#065F46;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="background:#FEE2E2;border-left:4px solid #EF4444;padding:0.75rem;border-radius:0.5rem;margin-bottom:0.5rem;color:#7F1D1D;">{{ session('error') }}</div>
        @endif
        @if(session('warning'))
            <div style="background:#FEF3C7;border-left:4px solid #F59E0B;padding:0.75rem;border-radius:0.5rem;margin-bottom:0.5rem;color:#92400E;">{{ session('warning') }}</div>
        @endif
        @if(session('import_errors'))
            <div style="background:#FEE2E2;border-left:4px solid #EF4444;padding:0.75rem;border-radius:0.5rem;color:#7F1D1D;">
                <strong>Baris gagal diimpor:</strong>
                <ul style="margin:0.5rem 0 0 1.25rem;">
                    @foreach(session('import_errors') as $ie)
                        <li>{{ $ie }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
<div class="filter-section">
    <div class="filter-title">
        <i class="bi bi-funnel"></i> Filter Data
    </div>
    <form action="{{ route('siswa.index') }}" method="GET">
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
                    @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" {{ request('kelas') == $k->id_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin">
                    <option value="">-- Semua --</option>
                    <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-filter">
                <i class="bi bi-search"></i> Cari
            </button>
            <a href="{{ route('siswa.index') }}" class="btn-reset">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
        </div>
    </form>
</div>

@if ($siswa->isEmpty())
    <div class="table-container">
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            @if(request()->hasAny(['nis', 'nama', 'kelas', 'jenis_kelamin']))
                <p>Tidak ada data siswa yang sesuai dengan filter</p>
            @else
                <p>Belum ada data siswa</p>
            @endif
        </div>
    </div>
@else
    <div class="count-info">
        Menampilkan <strong>{{ $siswa->count() }}</strong> dari total data siswa
    </div>
    <form id="bulkDeleteForm" action="{{ route('siswa.bulkDestroy') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <div class="bulk-actions">
        <div class="bulk-actions-info">
            <span id="selectedSiswaCount" style="font-weight: 600; color: var(--text-primary); font-size: 0.95rem;"></span>
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

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 50px;">
                        <div class="select-all-wrapper">
                            <input type="checkbox" id="selectAllSiswa" aria-label="Pilih semua siswa">
                        </div>
                    </th>
                    <th style="width: 50px;">No</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Orang Tua</th>
                    <th>Jenis Kelamin</th>
                    <th>Alamat</th>
                    <th style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($siswa as $item)
                <tr>
                    <td>
                        <div class="row-checkbox-wrapper">
                            <input type="checkbox" class="siswa-checkbox" form="bulkDeleteForm" name="selected_siswa[]" value="{{ $item->nomor_induk_siswa }}" aria-label="Pilih siswa {{ $item->nama_siswa }}">
                        </div>
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $item->nomor_induk_siswa ?? '-' }}</strong></td>
                    <td>{{ $item->nama_siswa }}</td>
                    <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $item->nama_orgtua }}</td>
                    <td>{{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>{{ $item->alamat ?? '-' }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('siswa.show', $item->nomor_induk_siswa) }}" class="btn-action btn-view" title="Lihat">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('siswa.edit', $item->nomor_induk_siswa) }}" class="btn-action btn-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('siswa.destroy', $item->nomor_induk_siswa) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-action btn-delete" title="Hapus" data-delete-btn data-item-name="siswa ini">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('selectAllSiswa');
        const rowCheckboxes = Array.from(document.querySelectorAll('.siswa-checkbox'));
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
        const selectedSiswaCount = document.getElementById('selectedSiswaCount');
        const clearSelectionBtn = document.getElementById('clearSelectionBtn');
        const bulkActionsContainer = document.querySelector('.bulk-actions');

        if (!selectAllCheckbox || !bulkDeleteBtn || !bulkDeleteForm || !selectedSiswaCount || !clearSelectionBtn || !bulkActionsContainer) {
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

            selectedSiswaCount.innerHTML = selectedCount > 0
                ? `<i class="bi bi-check-circle-fill" style="color: #10B981; margin-right: 0.5rem;"></i> ${selectedCount} siswa dipilih`
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
                    title: 'Belum ada siswa dipilih',
                    text: 'Centang minimal satu siswa terlebih dahulu.',
                });
                return;
            }

            Swal.fire({
                title: 'Hapus ' + selectedCount + ' siswa?',
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


