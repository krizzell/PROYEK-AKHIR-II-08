@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --primary-light: #FFEDE3;
        --primary-dark: #EA580C;
        --success-color: #10B981;
        --danger-color: #EF4444;
        --neutral-100: #F9FAFB;
        --neutral-200: #F3F4F6;
        --neutral-300: #E5E7EB;
        --neutral-600: #4B5563;
        --neutral-700: #374151;
        --neutral-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .page-wrapper {
        background: #FFFFFF;
        min-height: 100vh;
        padding: 2.5rem 0;
    }

    .premium-header {
        margin-bottom: 2rem;
        animation: slideDown 0.5s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .premium-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .premium-header .breadcrumb-text {
        color: var(--neutral-600);
        font-size: 0.95rem;
        margin-top: 0.5rem;
    }

    .premium-card {
        background: white;
        border: 1px solid var(--neutral-200);
        border-radius: 1rem;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .premium-card-body {
        padding: 3rem;
    }

    .form-section {
        margin-bottom: 2.5rem;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--neutral-300), transparent);
        margin: 2.5rem 0;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--neutral-900);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title-icon {
        width: 32px;
        height: 32px;
        background: var(--primary-light);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 1rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--neutral-900);
        margin-bottom: 0.75rem;
        display: block;
        font-size: 0.95rem;
    }

    .form-label .text-danger {
        color: var(--danger-color);
        margin-left: 0.25rem;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 1.5px solid var(--neutral-300);
        border-radius: 0.75rem;
        font-size: 1rem;
        color: var(--neutral-900);
        background: white;
        transition: all 0.3s ease;
        font-family: inherit;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px var(--primary-light);
        background: white;
    }

    .form-control::placeholder {
        color: #9CA3AF;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .form-text {
        font-size: 0.85rem;
        color: var(--neutral-600);
        margin-top: 0.5rem;
        display: block;
    }

    .invalid-feedback {
        display: block;
        color: var(--danger-color);
        font-size: 0.85rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }

    .form-control.is-invalid {
        border-color: var(--danger-color);
        background-color: rgba(239, 68, 68, 0.02);
    }

    .form-control.is-invalid:focus {
        border-color: var(--danger-color);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 1px solid var(--neutral-200);
    }

    .btn-premium {
        padding: 0.875rem 2rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-save {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 8px 24px rgba(249, 115, 22, 0.4), 0 0 1px rgba(249, 115, 22, 0.5);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(249, 115, 22, 0.5), 0 0 2px rgba(249, 115, 22, 0.6);
    }

    .btn-save:active {
        transform: translateY(0);
    }

    .btn-cancel {
        background: white;
        color: var(--neutral-600);
        border: 2px solid var(--neutral-300);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .btn-cancel:hover {
        background: var(--neutral-100);
        border-color: var(--neutral-400);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-cancel:active {
        transform: translateY(0);
    }
</style>

<div class="page-wrapper">
    <div style="max-width: 900px; margin: 0 auto; padding: 0 1.5rem;">
        <div class="premium-header">
            <h1><i class="bi bi-pencil-square"></i> Edit Siswa</h1>
            <p class="breadcrumb-text">Perbarui informasi siswa pada form di bawah</p>
        </div>

        <div class="premium-card">
            <div class="premium-card-body">
                <form action="{{ route('siswa.update', ['nomor_induk_siswa' => $siswa->nomor_induk_siswa]) }}" method="POST" id="form-siswa">
                    @csrf
                    @method('PUT')

                    <!-- Bagian 1: Informasi Dasar -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-person-fill"></i></div>
                            Informasi Dasar
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="id_kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                                <select class="form-control @error('id_kelas') is-invalid @enderror" id="id_kelas" name="id_kelas" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id_kelas }}" {{ old('id_kelas', $siswa->id_kelas) == $k->id_kelas ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_kelas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="nomor_induk_siswa" class="form-label">Nomor Induk Siswa (NIS) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nomor_induk_siswa') is-invalid @enderror" 
                                       id="nomor_induk_siswa" name="nomor_induk_siswa" value="{{ old('nomor_induk_siswa', $siswa->nomor_induk_siswa) }}" 
                                       pattern="[0-9]*" maxlength="20" required>
                                @error('nomor_induk_siswa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <span class="form-text"><i class="bi bi-info-circle"></i> Nomor induk tidak boleh diubah</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nama_siswa" class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_siswa') is-invalid @enderror" 
                                   id="nama_siswa" name="nama_siswa" value="{{ old('nama_siswa', $siswa->nama_siswa) }}" required placeholder="Masukkan nama lengkap siswa">
                            @error('nama_siswa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nama_orgtua" class="form-label">Nama Orangtua <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_orgtua') is-invalid @enderror" 
                                   id="nama_orgtua" name="nama_orgtua" value="{{ old('nama_orgtua', $siswa->nama_orgtua) }}" required placeholder="Masukkan nama orangtua / wali">
                            @error('nama_orgtua')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Bagian 2: Data Pribadi -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-calendar-check"></i></div>
                            Data Pribadi
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="tgl_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tgl_lahir') is-invalid @enderror" 
                                       id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir', $siswa->tgl_lahir->format('Y-m-d')) }}" required>
                                @error('tgl_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-control @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>👦 Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>👧 Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                      id="alamat" name="alamat" required placeholder="Masukkan alamat lengkap siswa">{{ old('alamat', $siswa->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn-premium btn-save">
                            <i class="bi bi-check-circle-fill"></i> Perbarui Siswa
                        </button>
                        <a href="{{ route('siswa.index') }}" class="btn-premium btn-cancel">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
