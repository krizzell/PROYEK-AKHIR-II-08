@extends('layouts.app')

@section('title', 'Detail Perkembangan')

@section('content')
<div class="row mb-3">
    <div class="col-md-10">
        <h2><i class="bi bi-graph-up"></i> Detail Perkembangan Anak</h2>
    </div>
</div>

<!-- Info Kartu Anak -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <small class="text-muted">Nama Anak</small>
                    <h5 class="mb-0">{{ $perkembangan->siswa->nama_siswa ?? '-' }}</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <small class="text-muted">Kelas</small>
                    <h5 class="mb-0">{{ $perkembangan->siswa->kelas->nama_kelas ?? '-' }}</h5>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <small class="text-muted">Guru</small>
                    <p class="mb-0">{{ $perkembangan->guru->nama_guru ?? '-' }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <small class="text-muted">Periode Laporan</small>
                    @php
                        $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $periode = ($perkembangan->bulan ? $bulan[$perkembangan->bulan] : '-') . ' ' . ($perkembangan->tahun ?? '-');
                    @endphp
                    <h5 class="mb-0">{{ $periode }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kategori dengan Nilai -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0">Kategori Perkembangan</h6>
    </div>
    <div class="card-body">
        @php $perkembangan->load('kategoriDetails'); @endphp
        @if ($perkembangan->kategoriDetails && count($perkembangan->kategoriDetails) > 0)
            <div class="row">
                @foreach ($perkembangan->kategoriDetails as $detail)
                    <div class="col-md-12 mb-3">
                        <div class="card border-light">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <h6 class="mb-0">{{ $detail->nama_kategori }}</h6>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="badge bg-primary">Nilai: {{ $detail->nilai }}/10</span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">{{ $detail->deskripsi }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted">-</p>
        @endif
    </div>
</div>

<!-- Status Pencapaian Utama -->
<div class="card mb-4">
    <div class="card-body text-center">
        <h6 class="text-muted mb-3">Status Pencapaian</h6>
        @php
            $statusLabels = [
                'BB' => ['label' => 'Belum Berkembang', 'badge' => 'danger'],
                'MB' => ['label' => 'Mulai Berkembang', 'badge' => 'warning'],
                'BSH' => ['label' => 'Berkembang Sesuai Harapan', 'badge' => 'info'],
                'BSB' => ['label' => 'Berkembang Sangat Baik', 'badge' => 'success']
            ];
            $status = $statusLabels[$perkembangan->status_utama] ?? null;
        @endphp
        @if ($status)
            <span class="badge bg-{{ $status['badge'] }} rounded-pill" style="padding: 15px 30px; font-size: 18px;">
                {{ $perkembangan->status_utama }} - {{ $status['label'] }}
            </span>
        @endif
    </div>
</div>

<!-- Template Deskripsi Indikator -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0">Deskripsi Indikator</h6>
    </div>
    <div class="card-body">
        @php
            $templateDescriptions = [
                'BB' => 'Anak belum menunjukkan kemampuan dalam aspek ini. Perlu dukungan dan bimbingan intensif dari guru untuk mengembangkan kompetensi ini.',
                'MB' => 'Anak mulai menunjukkan kemampuan dalam aspek ini namun masih memerlukan bimbingan. Perlu terus didukung untuk mencapai perkembangan yang lebih baik.',
                'BSH' => 'Anak menunjukkan kemampuan yang sesuai dengan harapan untuk usia/tingkatannya. Anak mampu melaksanakan tugas dengan cukup baik.',
                'BSB' => 'Anak menunjukkan kemampuan yang sangat menonjol dalam aspek ini. Anak mampu melaksanakan tugas dengan sangat baik dan melampaui harapan.'
            ];
        @endphp
        <p class="mb-0" style="line-height: 1.6;">
            {{ $templateDescriptions[$perkembangan->status_utama] ?? '-' }}
        </p>
    </div>
</div>

<!-- Catatan Tambahan -->
@if ($perkembangan->deskripsi)
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0">Catatan Tambahan</h6>
    </div>
    <div class="card-body">
        <p class="mb-0" style="line-height: 1.6;">{{ $perkembangan->deskripsi }}</p>
    </div>
</div>
@endif

<!-- Info Tanggal -->
<div class="card mb-4 bg-light">
    <div class="card-body">
        <p class="mb-1"><small class="text-muted">Dibuat:</small> {{ $perkembangan->created_at->format('d M Y H:i') }}</p>
        <p class="mb-0"><small class="text-muted">Diperbarui:</small> {{ $perkembangan->updated_at->format('d M Y H:i') }}</p>
    </div>
</div>

<!-- Tombol Aksi -->
<div class="d-flex gap-2">
    <a href="{{ route('perkembangan.edit', $perkembangan->id_perkembangan) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit
    </a>
    <form action="{{ route('perkembangan.destroy', $perkembangan->id_perkembangan) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-danger" data-delete-btn data-item-name="perkembangan ini">
            <i class="bi bi-trash"></i> Hapus
        </button>
    </form>
    <a href="{{ route('perkembangan.index') }}" class="btn btn-secondary ms-auto">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<style>
    .card {
        border: 1px solid #6B7280;
        border-radius: 8px;
    }
    .border-danger { }
    .border-warning { }
    .border-info { }
    .border-success { }
</style>
@endsection
