@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-person-circle"></i> Detail Siswa</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>NIS:</strong>
            </div>
            <div class="col-md-9">
                {{ $siswa->nomor_induk_siswa }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Nama Siswa:</strong>
            </div>
            <div class="col-md-9">
                {{ $siswa->nama_siswa }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Nama Orangtua:</strong>
            </div>
            <div class="col-md-9">
                {{ $siswa->nama_orgtua }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Kelas:</strong>
            </div>
            <div class="col-md-9">
                {{ $siswa->kelas->nama_kelas ?? '-' }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Tanggal Lahir:</strong>
            </div>
            <div class="col-md-9">
                {{ $siswa->tgl_lahir->format('d-m-Y') }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Jenis Kelamin:</strong>
            </div>
            <div class="col-md-9">
                {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Alamat:</strong>
            </div>
            <div class="col-md-9">
                {{ $siswa->alamat }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Dibuat:</strong>
            </div>
            <div class="col-md-9">
                {{ $siswa->created_at->format('d-m-Y H:i') }}
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('siswa.edit', ['nomor_induk_siswa' => $siswa->nomor_induk_siswa]) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('siswa.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
