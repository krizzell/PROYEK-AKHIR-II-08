@extends('layouts.app')

@section('title', 'Data Guru')

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

    .guru-checkbox,
    #selectAllGuru {
        width: 18px;
        height: 18px;
        accent-color: #EF4444;
        cursor: pointer;
    }
</style>

<div class="page-header">
    <h1><i class="bi bi-person-workspace"></i> Data Guru</h1>
    <a href="{{ route('guru.create') }}" class="btn-add">
        <i class="bi bi-plus-lg"></i> Tambah Guru
    </a>
</div>

@if ($guru->isEmpty())
    <div class="table-container">
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>Belum ada data guru</p>
        </div>
    </div>
@else
    <div class="count-info">
        Menampilkan <strong>{{ $guru->count() }}</strong> data guru
    </div>
    <form id="bulkDeleteForm" action="{{ route('guru.bulkDestroy') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <div class="bulk-actions">
        <div class="bulk-actions-info">
            <span id="selectedGuruCount" style="font-weight: 600; color: var(--text-primary); font-size: 0.95rem;"></span>
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
                            <input type="checkbox" id="selectAllGuru" aria-label="Pilih semua guru">
                        </div>
                    </th>
                    <th style="width: 50px;">No</th>
                    <th>Nama Guru</th>
                    <th>NIP</th>
                    <th>Role</th>
                    <th>Kelas</th>
                    <th>Email</th>
                    <th style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($guru as $item)
                <tr>
                    <td>
                        <div class="row-checkbox-wrapper">
                            <input type="checkbox" class="guru-checkbox" form="bulkDeleteForm" name="selected_guru[]" value="{{ $item->id_guru }}" aria-label="Pilih guru {{ $item->nama_guru }}">
                        </div>
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $item->nama_guru }}</strong></td>
                    <td>{{ $item->nip_guru }}</td>
                    <td>
                        @php
                            $jabatan = $item->jabatan ?? 'Guru';
                        @endphp
                        <span style="
                            display: inline-block;
                            padding: 0.25rem 0.75rem;
                            border-radius: 9999px;
                            font-size: 0.85rem;
                            font-weight: 600;
                            background: {{ $jabatan === 'Kepala Sekolah' ? '#FEE2E2' : '#DBEAFE' }};
                            color: {{ $jabatan === 'Kepala Sekolah' ? '#991B1B' : '#1E40AF' }};
                        ">
                            {{ $jabatan }}
                        </span>
                    </td>
                    <td>
                        @php
                            $kelas = $item->kelasAmpuan()->pluck('nama_kelas')->toArray();
                        @endphp
                        @if(count($kelas) > 0)
                            {{ implode(', ', $kelas) }}
                        @else
                            <span style="color: #9CA3AF;">-</span>
                        @endif
                    </td>
                    <td>{{ $item->email }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('guru.show', $item->id_guru) }}" class="btn-action btn-view" title="Lihat">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('guru.edit', $item->id_guru) }}" class="btn-action btn-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('guru.destroy', $item->id_guru) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-action btn-delete" title="Hapus" data-delete-btn data-item-name="guru ini">
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
        const selectAllCheckbox = document.getElementById('selectAllGuru');
        const rowCheckboxes = Array.from(document.querySelectorAll('.guru-checkbox'));
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
        const selectedGuruCount = document.getElementById('selectedGuruCount');
        const clearSelectionBtn = document.getElementById('clearSelectionBtn');
        const bulkActionsContainer = document.querySelector('.bulk-actions');

        if (!selectAllCheckbox || !bulkDeleteBtn || !bulkDeleteForm || !selectedGuruCount || !clearSelectionBtn || !bulkActionsContainer) {
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

            selectedGuruCount.innerHTML = selectedCount > 0
                ? `<i class="bi bi-check-circle-fill" style="color: #10B981; margin-right: 0.5rem;"></i> ${selectedCount} guru dipilih`
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
                    title: 'Belum ada guru dipilih',
                    text: 'Centang minimal satu guru terlebih dahulu.',
                });
                return;
            }

            Swal.fire({
                title: 'Hapus ' + selectedCount + ' guru?',
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
