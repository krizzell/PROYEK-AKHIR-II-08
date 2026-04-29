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
        background: #FF9800;
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
        background: #F57C00;
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
</style>

<div class="page-header">
    <h1><i class="bi bi-receipt"></i> Data Tagihan</h1>
    <a href="{{ route('tagihan.create') }}" class="btn-add">
        <i class="bi bi-plus-lg"></i> Buat Tagihan
    </a>
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
        Menampilkan <strong>{{ $tagihan->count() }}</strong> data tagihan
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
                            <a href="{{ route('tagihan.edit', $item->id_tagihan) }}" class="btn-action btn-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('tagihan.destroy', $item->id_tagihan) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-action btn-delete" title="Hapus" data-delete-btn data-item-name="tagihan ini">
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
