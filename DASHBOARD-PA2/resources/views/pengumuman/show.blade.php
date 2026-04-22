@extends('layouts.app')

@section('title', 'Detail Pengumuman')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h2><i class="bi bi-megaphone"></i> Detail Pengumuman</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Foto/Media -->
        @if($pengumuman->media)
            <div class="mb-4">
                <div class="media-display rounded overflow-hidden" style="background-color: #FFF8F4;">
                    <img src="{{ asset('storage/' . $pengumuman->media) }}" alt="Foto Pengumuman" 
                         class="img-fluid" style="max-height: 500px; object-fit: contain; display: block; margin: 0 auto;">
                </div>
            </div>
        @endif

        <!-- Judul -->
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Judul:</strong>
            </div>
            <div class="col-md-9">
                <h5 class="mb-0">{{ $pengumuman->judul }}</h5>
            </div>
        </div>

        <!-- Guru -->
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Guru:</strong>
            </div>
            <div class="col-md-9">
                <span class="badge bg-light text-dark">{{ $pengumuman->guru->nama_guru ?? '-' }}</span>
            </div>
        </div>

        <!-- Waktu Unggah -->
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Waktu Unggah:</strong>
            </div>
            <div class="col-md-9">
                <i class="bi bi-calendar3"></i> {{ $pengumuman->waktu_unggah->isoFormat('dddd, D MMMM YYYY H:mm') }}
            </div>
        </div>

        <!-- Deskripsi -->
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Deskripsi:</strong>
            </div>
            <div class="col-md-9">
                <div class="alert alert-light border">
                    {{ $pengumuman->deskripsi }}
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-2 mt-4">
            <a href="{{ route('pengumuman.edit', $pengumuman->id_pengumuman) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('pengumuman.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<style>
    .media-display {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
    }
</style>
@endsection
