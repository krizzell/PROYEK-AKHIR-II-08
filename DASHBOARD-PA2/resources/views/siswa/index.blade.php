@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h2><i class="bi bi-person-badge"></i> Data Siswa</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('siswa.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Siswa
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-funnel"></i> Filter Data Siswa</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('siswa.index') }}" method="GET" class="row g-3">
            <!-- Filter NIS -->
            <div class="col-md-3">
                <label for="nis" class="form-label">NIS</label>
                <input type="text" class="form-control" id="nis" name="nis" 
                       value="{{ request('nis') }}" placeholder="Cari NIS...">
            </div>

            <!-- Filter Nama -->
            <div class="col-md-3">
                <label for="nama" class="form-label">Nama Siswa</label>
                <input type="text" class="form-control" id="nama" name="nama" 
                       value="{{ request('nama') }}" placeholder="Cari nama...">
            </div>

            <!-- Filter Kelas -->
            <div class="col-md-3">
                <label for="kelas" class="form-label">Kelas</label>
                <select class="form-select" id="kelas" name="kelas">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" {{ request('kelas') == $k->id_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Jenis Kelamin -->
            <div class="col-md-3">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                    <option value="">-- Semua --</option>
                    <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>
                <a href="{{ route('siswa.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

@if ($siswa->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> 
        @if(request()->hasAny(['nis', 'nama', 'kelas', 'jenis_kelamin']))
            Tidak ada data siswa yang sesuai dengan filter Anda
        @else
            Tidak ada data siswa
        @endif
    </div>
@else
    <div class="alert alert-success mb-3">
        <i class="bi bi-check-circle"></i> 
        Ditemukan <strong>{{ $siswa->count() }}</strong> siswa
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Nama Orangtua</th>
                        <th>Kelas</th>
                        <th>Tempat Tinggal</th>
                        <th>Jenis Kelamin</th>
                        <th>Tgl Lahir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($siswa as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>
                                {{ $item->nomor_induk_siswa }}
                                @if(empty($item->nomor_induk_siswa))
                                    <span class="badge bg-danger">NULL/EMPTY</span>
                                @endif
                            </strong>
                        </td>
                        <td>{{ $item->nama_siswa }}</td>
                        <td>{{ $item->nama_orgtua }}</td>
                        <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
                        <td>{{ $item->alamat ?? '-' }}</td>
                        <td>{{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        <td>{{ $item->tgl_lahir->format('d-m-Y') }}</td>
                        <td>
                            <a href="{{ route('siswa.show', ['nomor_induk_siswa' => $item->nomor_induk_siswa]) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                            <a href="{{ route('siswa.edit', ['nomor_induk_siswa' => $item->nomor_induk_siswa]) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('siswa.destroy', ['nomor_induk_siswa' => $item->nomor_induk_siswa]) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" data-delete-btn data-item-name="siswa ini">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
