@extends('layouts.app')

@section('title', 'Ajukan Perpindahan Siswa')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --success-color: #10B981;
        --neutral-100: #F3F4F6;
        --neutral-200: #E5E7EB;
        --neutral-300: #D1D5DB;
        --neutral-600: #4B5563;
        --neutral-700: #374151;
        --neutral-900: #111827;
    }

    .transfer-container {
        max-width: 820px;
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
    }

    .header-section {
        margin-bottom: 28px;
        padding-bottom: 22px;
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
        gap: 16px;
        padding: 12px 0;
    }

    .siswa-info-item:not(:last-child) {
        border-bottom: 1px solid var(--neutral-200);
    }

    .siswa-info-label {
        font-size: 13px;
        font-weight: 700;
        color: var(--neutral-700);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .siswa-info-value {
        font-size: 15px;
        font-weight: 800;
        color: var(--neutral-900);
        text-align: right;
    }

    .kelas-current-badge {
        display: inline-block;
        background: var(--success-color);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        margin-left: 8px;
        text-transform: uppercase;
    }

    .form-section {
        margin-bottom: 22px;
    }

    .form-section label {
        font-size: 13px;
        font-weight: 800;
        color: var(--neutral-900);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: block;
    }

    .select-kelas,
    .textarea-alasan {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--neutral-200);
        border-radius: 8px;
        font-size: 15px;
        font-family: 'Montserrat', sans-serif;
        color: var(--neutral-900);
        background: white;
        transition: all 0.2s ease;
    }

    .textarea-alasan {
        min-height: 130px;
        resize: vertical;
    }

    .select-kelas:focus,
    .textarea-alasan:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
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

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 32px;
    }

    .btn-submit,
    .btn-cancel {
        flex: 1;
        padding: 14px 24px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 800;
        font-family: 'Montserrat', sans-serif;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #F97316 0%, #E85000 100%);
        color: white;
        border: none;
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-cancel {
        background: white;
        color: var(--neutral-700);
        border: 2px solid var(--neutral-300);
    }
</style>

<div class="transfer-container">
    <div class="breadcrumb-custom">
        <a href="{{ route('transfer-siswa.index') }}">
            <i class="bi bi-arrow-left"></i> Kembali ke Pengajuan Perpindahan
        </a>
    </div>

    <div class="header-section">
        <h1>Ajukan Perpindahan Siswa</h1>
        <p>Pengajuan akan dikirim ke kepala sekolah / super admin untuk diproses.</p>
    </div>

    <div class="warning-box">
        <i class="bi bi-exclamation-triangle"></i>
        <strong>Perhatian:</strong> Guru hanya mengajukan perpindahan. Kelas siswa tidak berubah sampai pengajuan disetujui.
    </div>

    @if($pengajuanAktif)
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle"></i>
            Siswa ini masih memiliki pengajuan aktif berstatus menunggu.
        </div>
    @endif

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
                {{ $kelasSekarang->nama_kelas ?? '-' }}
                <span class="kelas-current-badge">Asal</span>
            </span>
        </div>
    </div>

    <form action="{{ route('transfer-siswa.proses', $siswa->nomor_induk_siswa) }}" method="POST">
        @csrf

        <div class="form-section">
            <label for="id_kelas_tujuan">
                <i class="bi bi-arrow-right-circle"></i> Pilih Kelas Tujuan
            </label>
            <select name="id_kelas_tujuan" id="id_kelas_tujuan" class="select-kelas" required {{ $pengajuanAktif ? 'disabled' : '' }}>
                <option value="">-- Pilih Kelas Tujuan --</option>
                @foreach ($kelasAyo as $k)
                    <option value="{{ $k->id_kelas }}" {{ old('id_kelas_tujuan') == $k->id_kelas ? 'selected' : '' }}>
                        {{ $k->nama_kelas }} ({{ $k->siswa_count }} siswa)
                    </option>
                @endforeach
            </select>
            @error('id_kelas_tujuan')
                <small class="text-danger"><i class="bi bi-exclamation-circle"></i> {{ $message }}</small>
            @enderror
        </div>

        <div class="form-section">
            <label for="alasan">
                <i class="bi bi-chat-left-text"></i> Alasan Perpindahan
            </label>
            <textarea name="alasan" id="alasan" class="textarea-alasan" required placeholder="Tuliskan alasan perpindahan kelas siswa..." {{ $pengajuanAktif ? 'disabled' : '' }}>{{ old('alasan') }}</textarea>
            @error('alasan')
                <small class="text-danger"><i class="bi bi-exclamation-circle"></i> {{ $message }}</small>
            @enderror
        </div>

        <div class="button-group">
            <a href="{{ route('transfer-siswa.index') }}" class="btn-cancel">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn-submit" {{ $pengajuanAktif ? 'disabled' : '' }}>
                <i class="bi bi-send"></i> Kirim Pengajuan
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
