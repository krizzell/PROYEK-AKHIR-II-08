@extends('layouts.app')

@section('title', 'Detail Tagihan')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-receipt"></i> Detail Tagihan</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>ID Tagihan:</strong>
            </div>
            <div class="col-md-9">
                {{ $tagihan->id_tagihan }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Siswa:</strong>
            </div>
            <div class="col-md-9">
                {{ $tagihan->siswa->nama_siswa ?? '-' }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Periode:</strong>
            </div>
            <div class="col-md-9">
                {{ $tagihan->periode }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Jumlah Tagihan:</strong>
            </div>
            <div class="col-md-9">
                Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Status:</strong>
            </div>
            <div class="col-md-9">
                @php
                    $statusLabels = [
                        'belum_bayar' => 'Belum Bayar',
                        'lunas' => 'Lunas'
                    ];
                    $badgeColor = $tagihan->status == 'lunas' ? 'bg-success' : 'bg-warning';
                @endphp
                <span class="badge {{ $badgeColor }}">
                    {{ $statusLabels[$tagihan->status] ?? ucfirst(str_replace('_', ' ', $tagihan->status)) }}
                </span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Tanggal Pembayaran:</strong>
            </div>
            <div class="col-md-9">
                @if ($tagihan->status === 'lunas' && $tanggalPembayaranFormatted)
                    {{ $tanggalPembayaranFormatted }}
                @else
                    <span class="text-muted">-</span>
                @endif
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Dibuat:</strong>
            </div>
            <div class="col-md-9">
                {{ $tagihan->created_at->format('d-m-Y H:i') }}
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
