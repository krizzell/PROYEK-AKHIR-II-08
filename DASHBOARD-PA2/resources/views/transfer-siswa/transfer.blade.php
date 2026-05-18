@extends('layouts.app')

@section('title', 'Transfer Siswa')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --success-color: #10B981;
        --danger-color: #EF4444;
        --neutral-50: #F9FAFB;
        --neutral-100: #F3F4F6;
        --neutral-200: #E5E7EB;
        --neutral-300: #D1D5DB;
        --neutral-600: #4B5563;
        --neutral-700: #374151;
        --neutral-900: #111827;
    }

    .transfer-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .breadcrumb-custom {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
        font-size: 14px;
        color: var(--neutral-600);
    }

    .breadcrumb-custom a {
        color: var(--primary-color);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .breadcrumb-custom a:hover {
        color: #E85000;
    }

    .header-section {
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 2px solid var(--neutral-200);
    }

    .header-section h1 {
        font-size: 24px;
        font-weight: 800;
        color: var(--neutral-900);
        margin: 0 0 8px 0;
    }

    .header-section p {
        font-size: 14px;
        color: var(--neutral-600);
        margin: 0;
    }

    .siswa-info-card {
        background: linear-gradient(135deg, #F9FAFB 0%, #FAFBFC 100%);
        border: 2px solid var(--neutral-200);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .siswa-info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
    }

    .siswa-info-item:not(:last-child) {
        border-bottom: 1px solid var(--neutral-200);
    }

    .siswa-info-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--neutral-700);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .siswa-info-value {
        font-size: 15px;
        font-weight: 700;
        color: var(--neutral-900);
    }

    .form-section {
        margin-bottom: 24px;
    }

    .form-section label {
        font-size: 13px;
        font-weight: 700;
        color: var(--neutral-900);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: block;
    }

    .select-kelas {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--neutral-200);
        border-radius: 8px;
        font-size: 15px;
        font-family: 'Montserrat', sans-serif;
        color: var(--neutral-900);
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .select-kelas:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
    }

    .select-kelas option {
        padding: 8px;
    }

    .kelas-current-badge {
        display: inline-block;
        background: var(--success-color);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        margin-left: 8px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 32px;
    }

    .btn-transfer {
        flex: 1;
        padding: 14px 24px;
        background: linear-gradient(135deg, #F97316 0%, #E85000 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Montserrat', sans-serif;
    }

    .btn-transfer:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(249, 115, 22, 0.3);
    }

    .btn-transfer:active {
        transform: translateY(0);
    }

    .btn-cancel {
        flex: 1;
        padding: 14px 24px;
        background: white;
        color: var(--neutral-700);
        border: 2px solid var(--neutral-300);
        border-radius: 8px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Montserrat', sans-serif;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-cancel:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .warning-box {
        background: #FEF3C7;
        border-left: 4px solid #F59E0B;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-size: 14px;
        color: #92400e;
    }

    .warning-box i {
        margin-right: 8px;
        color: #F59E0B;
    }
</style>

<div class="transfer-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb-custom">
        <a href="{{ route('transfer-siswa.index') }}">
            <i class="bi bi-arrow-left"></i> Kembali ke Perpindahan Kelas
        </a>
    </div>

    <!-- Header -->
    <div class="header-section">
        <h1>Proses Transfer Siswa</h1>
        <p>Pilih kelas tujuan untuk memindahkan siswa</p>
    </div>

    <!-- Warning Box -->
    <div class="warning-box">
        <i class="bi bi-exclamation-triangle"></i>
        <strong>Perhatian:</strong> Pastikan Anda memilih kelas yang tepat sebelum melakukan transfer. Data siswa akan diperbarui secara otomatis.
    </div>

    <!-- Info Box: Kelas Terkunci -->
    <div style="background: #EFF6FF; border-left: 4px solid #3B82F6; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; color: #1E40AF;">
        <i class="bi bi-info-circle" style="margin-right: 8px; color: #3B82F6;"></i>
        <strong>Informasi:</strong> Kelas {{ $lockedClass }} adalah kelas terkunci dan tidak dapat melakukan perpindahan siswa (minimum 20 siswa, maksimum 30 siswa per kelas).
    </div>

    <!-- Siswa Info Card -->
    <div class="siswa-info-card">
        <div class="siswa-info-item">
            <span class="siswa-info-label">Nama Siswa</span>
            <span class="siswa-info-value">{{ $siswa->nama_siswa }}</span>
        </div>
        <div class="siswa-info-item">
            <span class="siswa-info-label">NIS</span>
            <span class="siswa-info-value">{{ $siswa->nomor_induk_siswa }}</span>
        </div>
        <div class="siswa-info-item">
            <span class="siswa-info-label">Jenis Kelamin</span>
            <span class="siswa-info-value">{{ $siswa->jenis_kelamin }}</span>
        </div>
        <div class="siswa-info-item">
            <span class="siswa-info-label">Kelas Saat Ini</span>
            <span class="siswa-info-value">
                {{ $kelasSekarang->nama_kelas }}
                <span class="kelas-current-badge">Sekarang</span>
            </span>
        </div>
    </div>

    <!-- Form Transfer -->
    <form action="{{ route('transfer-siswa.proses', $siswa->nomor_induk_siswa) }}" method="POST">
        @csrf

        <div class="form-section">
            <label for="id_kelas_tujuan">
                <i class="bi bi-arrow-right-circle"></i> Pilih Kelas Tujuan
            </label>
            <select name="id_kelas_tujuan" id="id_kelas_tujuan" class="select-kelas" required>
                <option value="">-- Pilih Kelas Tujuan --</option>
                @foreach ($kelasAyo as $k)
                    <option value="{{ $k->id_kelas }}">
                        {{ $k->nama_kelas }} ({{ $k->siswa->count() }} siswa)
                    </option>
                @endforeach
            </select>
            @error('id_kelas_tujuan')
                <small class="text-danger"><i class="bi bi-exclamation-circle"></i> {{ $message }}</small>
            @enderror
        </div>

        <!-- Button Group -->
        <div class="button-group">
            <a href="{{ route('transfer-siswa.index') }}" class="btn-cancel">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn-transfer">
                <i class="bi bi-check-circle"></i> Konfirmasi Transfer
            </button>
        </div>
    </form>
</div>

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Terjadi Kesalahan',
                text: '{{ $errors->first() }}',
                icon: 'error',
                confirmButtonColor: '#F97316'
            });
        });
    </script>
@endif

@endsection
