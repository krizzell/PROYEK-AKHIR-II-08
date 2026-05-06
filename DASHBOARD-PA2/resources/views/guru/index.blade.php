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
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
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
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $item->nama_guru }}</strong></td>
                    <td>{{ $item->nip_guru }}</td>
                    <td>
                        @php
                            $role = $item->akun()->first()?->role ?? 'N/A';
                            $isAdmin = $item->akun()->first()?->is_super_admin;
                        @endphp
                        <span style="
                            display: inline-block;
                            padding: 0.25rem 0.75rem;
                            border-radius: 9999px;
                            font-size: 0.85rem;
                            font-weight: 600;
                            background: {{ $isAdmin ? '#FEE2E2' : '#DBEAFE' }};
                            color: {{ $isAdmin ? '#991B1B' : '#1E40AF' }};
                        ">
                            {{ $isAdmin ? 'Super Admin' : ucfirst($role) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $kelas = $item->kelasAmpuan()->pluck('nama_kelas')->toArray();
                        @endphp
                        @if(count($kelas) > 0)
                            <div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
                                @foreach($kelas as $k)
                                    <span style="
                                        display: inline-block;
                                        padding: 0.25rem 0.5rem;
                                        background: #F0F9FF;
                                        border: 1px solid #0EA5E9;
                                        border-radius: 0.25rem;
                                        font-size: 0.8rem;
                                        color: #0369A1;
                                    ">{{ $k }}</span>
                                @endforeach
                            </div>
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
