@extends('layouts.app')

@section('title', 'Perkembangan Siswa')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h2><i class="bi bi-graph-up"></i> Perkembangan Siswa</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('perkembangan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Perkembangan
        </a>
    </div>
</div>

@if ($perkembangan->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Tidak ada data perkembangan
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Periode</th>
                        <th>Kategori</th>
                        <th style="width: 180px;">Status Capaian</th>
                        <th style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($perkembangan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($item->siswa)
                                <strong>{{ $item->siswa->nama_siswa }}</strong>
                                <br>
                                <small class="text-muted">{{ $item->guru->nama_guru ?? '-' }}</small>
                            @else
                                <span style="color: red;"><strong>ERROR: Siswa tidak ditemukan ({{ $item->nomor_induk_siswa }})</strong></span>
                                <br>
                                <small class="text-muted">{{ $item->guru->nama_guru ?? '-' }}</small>
                            @endif
                        </td>
                        <td>
                            @if($item->siswa && $item->siswa->kelas)
                                {{ $item->siswa->kelas->nama_kelas }}
                            @else
                                <span style="color: red;">{{ $item->siswa ? '-' : 'N/A' }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $periode = ($item->bulan ? $bulan[$item->bulan] : '-') . ' ' . ($item->tahun ?? '-');
                            @endphp
                            {{ $periode }}
                        </td>
                        <td>{{ $item->kategori }}</td>
                        <td>
                            @if ($item->status_utama)
                                @php
                                    $statusColors = [
                                        'BB' => ['label' => 'BB - Belum Berkembang', 'badge' => 'danger'],
                                        'MB' => ['label' => 'MB - Mulai Berkembang', 'badge' => 'warning'],
                                        'BSH' => ['label' => 'BSH - Sesuai Harapan', 'badge' => 'info'],
                                        'BSB' => ['label' => 'BSB - Sangat Baik', 'badge' => 'success']
                                    ];
                                    $status = $statusColors[$item->status_utama] ?? null;
                                @endphp
                                @if ($status)
                                    <span class="badge bg-{{ $status['badge'] }} rounded-pill">{{ $status['label'] }}</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('perkembangan.show', $item->id_perkembangan) }}" class="btn btn-info btn-sm" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('perkembangan.edit', $item->id_perkembangan) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('perkembangan.destroy', $item->id_perkembangan) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus" data-delete-btn data-item-name="perkembangan ini">
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
    </div>
@endif

<style>
    .action-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .action-buttons form {
        margin: 0;
        display: flex;
    }

    .badge {
        font-size: 11px;
        padding: 4px 8px;
        margin: 2px;
    }
</style>
@endsection
