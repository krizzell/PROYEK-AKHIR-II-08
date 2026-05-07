@extends('layouts.app')

@section('title', 'Tambah Guru')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --primary-light: #FFEDE3;
        --primary-dark: #EA580C;
        --success-color: #10B981;
        --danger-color: #EF4444;
        --neutral-50: #F9FAFB;
        --neutral-100: #F3F4F6;
        --neutral-200: #E5E7EB;
        --neutral-300: #D1D5DB;
        --neutral-500: #6B7280;
        --neutral-600: #4B5563;
        --neutral-700: #374151;
        --neutral-900: #111827;
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .page-wrapper {
        background: transparent;
        min-height: 100vh;
        padding: 2.5rem 0;
    }

    .container-lg {
        max-width: 1000px;
        margin: 0 auto;
        background: #FFFFFF;
        border-radius: 16px;
        padding: 24px 3rem;
        border: 1px solid rgba(226, 232, 240, 0.6);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08), 0 4px 8px rgba(0, 0, 0, 0.04);
    }

    .premium-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--neutral-200);
    }

    .premium-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .premium-header .breadcrumb-text {
        color: var(--neutral-500);
        font-size: 0.95rem;
        margin-top: 0.5rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section:last-of-type {
        margin-bottom: 0;
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

    .form-label .text-muted {
        color: var(--neutral-500);
        font-weight: 400;
        font-size: 0.85rem;
        display: block;
        margin-top: 0.25rem;
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--neutral-300);
        border-radius: 0.5rem;
        font-size: 0.95rem;
        color: var(--neutral-900);
        transition: all 0.2s ease;
        font-family: inherit;
        background: var(--neutral-50);
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-control::placeholder {
        color: #9CA3AF;
    }

    .form-select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%234B5563' d='M10.293 3.293L6 7.586 1.707 3.293A1 1 0 00.293 4.707l5 5a1 1 0 001.414 0l5-5a1 1 0 10-1.414-1.414z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        padding-right: 2.5rem;
    }

    .invalid-feedback {
        display: block;
        color: var(--danger-color);
        font-size: 0.85rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: var(--danger-color);
        background-color: rgba(239, 68, 68, 0.02);
    }

    .form-control.is-invalid:focus,
    .form-select.is-invalid:focus {
        border-color: var(--danger-color);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .form-grid,
        .form-grid-3 {
            grid-template-columns: 1fr;
        }
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--neutral-200);
    }

    .btn-premium {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-save {
        background: var(--primary-color);
        color: white;
        border: 1px solid var(--primary-color);
    }

    .btn-save:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
    }

    .btn-cancel {
        background: white;
        color: var(--neutral-700);
        border: 1px solid var(--neutral-300);
    }

    .btn-cancel:hover {
        background: var(--neutral-50);
        color: var(--neutral-900);
    }

    .multiselect-container {
        border: 1px solid var(--neutral-300);
        border-radius: 0.5rem;
        background: var(--neutral-50);
        max-height: 200px;
        overflow-y: auto;
        padding: 0.5rem;
    }

    .multiselect-option {
        padding: 0.5rem;
        margin: 0.25rem 0;
        border-radius: 0.25rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .multiselect-option:hover {
        background: var(--primary-light);
    }

    .multiselect-option input[type="checkbox"] {
        cursor: pointer;
    }
</style>

<div class="page-wrapper">
    <div class="container-lg">
        <div class="premium-header">
            <h1><i class="bi bi-person-plus-fill"></i> Tambah Guru Baru</h1>
            <p class="breadcrumb-text">Isi semua informasi guru dengan lengkap dan benar</p>
        </div>

        <!-- MAIN FORM -->
        <form action="{{ route('guru.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- SECTION 1: Data Pribadi -->
            <div class="form-section">
                <div class="section-title">
                    <div class="section-title-icon"><i class="bi bi-person-fill"></i></div>
                    Data Pribadi
                </div>

                <div class="form-group">
                    <label for="foto_guru" class="form-label">Foto Guru <span class="text-muted">(opsional)</span></label>
                    <input type="file" class="form-control @error('foto_guru') is-invalid @enderror"
                           id="foto_guru" name="foto_guru" accept="image/*">
                    <small class="text-muted">Format: JPG, PNG, WEBP. Maksimal 4 MB.</small>
                    @error('foto_guru')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nip_guru" class="form-label">NIP / ID Guru <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nip_guru') is-invalid @enderror" 
                           id="nip_guru" name="nip_guru" value="{{ old('nip_guru') }}" 
                           placeholder="Contoh: 123456789012" required>
                    @error('nip_guru')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nama_guru" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_guru') is-invalid @enderror" 
                           id="nama_guru" name="nama_guru" value="{{ old('nama_guru') }}" 
                           placeholder="Masukkan nama lengkap guru" required>
                    @error('nama_guru')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select @error('jenis_kelamin') is-invalid @enderror" 
                                id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                               id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <select class="form-select @error('jabatan') is-invalid @enderror" 
                                id="jabatan" name="jabatan" required>
                            <option value="">-- Pilih Jabatan --</option>
                            <option value="Guru" {{ old('jabatan') === 'Guru' ? 'selected' : '' }}>Guru</option>
                            <option value="Kepala Sekolah" {{ old('jabatan') === 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                        </select>
                        @error('jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('alamat') is-invalid @enderror" 
                              id="alamat" name="alamat" rows="3" 
                              placeholder="Masukkan alamat lengkap guru" required>{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- SECTION 2: Kontak & Pendidikan -->
            <div class="form-section">
                <div class="section-title">
                    <div class="section-title-icon"><i class="bi bi-telephone-fill"></i></div>
                    Kontak & Pendidikan
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="no_hp" class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" 
                               id="no_hp" name="no_hp" value="{{ old('no_hp') }}" 
                               placeholder="Contoh: 081234567890" required>
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" 
                               placeholder="Contoh: guru@email.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir <span class="text-danger">*</span></label>
                        <select class="form-select @error('pendidikan_terakhir') is-invalid @enderror" 
                                id="pendidikan_terakhir" name="pendidikan_terakhir" required>
                            <option value="">-- Pilih Pendidikan --</option>
                            <option value="SMA / SMK" {{ old('pendidikan_terakhir') === 'SMA / SMK' ? 'selected' : '' }}>SMA / SMK</option>
                            <option value="D3" {{ old('pendidikan_terakhir') === 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="S1" {{ old('pendidikan_terakhir') === 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('pendidikan_terakhir') === 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('pendidikan_terakhir') === 'S3' ? 'selected' : '' }}>S3</option>
                        </select>
                        @error('pendidikan_terakhir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jurusan" class="form-label">Jurusan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('jurusan') is-invalid @enderror" 
                               id="jurusan" name="jurusan" value="{{ old('jurusan') }}" 
                               placeholder="Contoh: Pendidikan Matematika" required>
                        @error('jurusan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Kelas yang Diampu -->
            <div class="form-section">
                <div class="section-title">
                    <div class="section-title-icon"><i class="bi bi-book-fill"></i></div>
                    Kelas yang Diampu (Opsional)
                </div>

                <div class="form-group">
                    <label class="form-label">Pilih Kelas</label>
                    <div class="multiselect-container">
                        @forelse($kelas as $k)
                            <div class="multiselect-option">
                                <input type="checkbox" name="kelas_ampuan[]" value="{{ $k->id_kelas }}" 
                                       id="kelas_{{ $k->id_kelas }}"
                                       {{ in_array($k->id_kelas, old('kelas_ampuan', [])) ? 'checked' : '' }}>
                                <label for="kelas_{{ $k->id_kelas }}" style="margin: 0; cursor: pointer;">
                                    {{ $k->nama_kelas }}
                                </label>
                            </div>
                        @empty
                            <p style="color: var(--neutral-500); padding: 1rem;">Tidak ada kelas tersedia</p>
                        @endforelse
                    </div>
                    @error('kelas_ampuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="action-buttons">
                <a href="{{ route('guru.index') }}" class="btn-premium btn-cancel">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
                <button type="submit" class="btn-premium btn-save">
                    <i class="bi bi-check-circle"></i> Simpan Guru
                </button>
            </div>
        </form>
    </div>
</div>

@endsection