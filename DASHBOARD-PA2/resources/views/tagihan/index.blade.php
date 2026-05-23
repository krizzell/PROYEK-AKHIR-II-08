@extends('layouts.app')

@section('title', 'Data Tagihan')

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

    .btn-secondary {
        background: white;
        color: var(--button-gray);
        border: 1px solid var(--border-color);
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .btn-secondary:hover {
        background: var(--hover-bg);
        border-color: var(--button-gray);
    }

    .info-section {
        margin-bottom: 1.5rem;
        padding: 1rem;
        border-left: 4px solid #06B6D4;
        background: #ECFDF5;
    }

    .info-section strong {
        color: var(--text-primary);
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

    .badge {
        padding: 0.35rem 0.75rem;
        border-radius: 0.35rem;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-success {
        background: #DCFCE7;
        color: #166534;
    }

    .badge-warning {
        background: #FEF3C7;
        color: #92400E;
    }

    .badge-info {
        background: #CFFAFE;
        color: #164E63;
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
        transition: all 0.3s ease;
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
        transition: all 0.3s ease;
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
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-reset:hover {
        background: var(--hover-bg);
        border-color: var(--button-gray);
    }
</style>

<div class="page-header">
    <h1><i class="bi bi-receipt"></i> Data Tagihan</h1>
    @if($isSuperAdmin)
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('tagihan.bulkCreate') }}" class="btn-add" style="background: #06B6D4;">
            <i class="bi bi-lightning-fill"></i> Apply All
        </a>
        <a href="{{ route('tagihan.create') }}" class="btn-add">
            <i class="bi bi-plus-lg"></i> Buat Tagihan
        </a>
    </div>
    @endif
</div>

<!-- Filter Section -->
<div class="filter-section">
    <div class="filter-title">
        <i class="bi bi-funnel"></i> Filter Data
    </div>
    <form action="{{ route('tagihan.index') }}" method="GET">
        <div class="filter-row">
            <div class="filter-group">
                <label for="nis">NIS</label>
                <input type="text" id="nis" name="nis" value="{{ request('nis') }}" placeholder="Cari NIS...">
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
                <label for="periode">Periode</label>
                <select id="periode" name="periode">
                    <option value="">-- Semua Periode --</option>
                    @foreach($periode as $p)
                        <option value="{{ $p }}" {{ request('periode') == $p ? 'selected' : '' }}>
                            {{ $p }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">-- Semua Status --</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-filter">
                <i class="bi bi-search"></i> Cari
            </button>
            <a href="{{ route('tagihan.index') }}" class="btn-reset">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Info Box -->
<div class="info-section">
    <i class="bi bi-info-circle"></i> <strong>Perhatian:</strong> Status pembayaran berubah otomatis menjadi "Lunas" ketika orangtua melakukan pembayaran melalui aplikasi mobile.
</div>

@if ($tagihan->isEmpty())
    <div class="table-container">
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>Belum ada data tagihan</p>
        </div>
    </div>
@else
    <div class="count-info">
        Menampilkan <strong>{{ $tagihan->count() }}</strong> dari total data tagihan
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Periode</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tagihan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($item->siswa)
                            <strong>{{ $item->siswa->nama_siswa }}</strong><br>
                            <small style="color: var(--text-secondary);">{{ $item->siswa->nomor_induk_siswa }}</small>
                        @else
                            <span style="color: #EF4444;">Data Siswa Hilang</span>
                        @endif
                    </td>
                    <td>
                        @if($item->siswa && $item->siswa->kelas)
                            {{ $item->siswa->kelas->nama_kelas }}
                        @else
                            <span style="color: #EF4444;">-</span>
                        @endif
                    </td>
                    <td>{{ $item->periode }}</td>
                    <td><strong>Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</strong></td>
                    <td>
                        @php
                            $paymentStatus = $item->payment_status ?? $item->status;
                            $badgeClass = $paymentStatus == 'lunas' ? 'badge-success' : ($paymentStatus == 'pending' ? 'badge-info' : 'badge-warning');
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            @if($paymentStatus == 'lunas')
                                Lunas
                            @elseif($paymentStatus == 'pending')
                                Pending
                            @else
                                Belum Bayar
                            @endif
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('tagihan.show', $item->id_tagihan) }}" class="btn-action btn-view" title="Lihat">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
