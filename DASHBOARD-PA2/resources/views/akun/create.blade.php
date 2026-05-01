@extends('layouts.app')

@section('title', 'Generate Akun')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --primary-light: #FFEDE3;
        --primary-dark: #EA580C;
        --success-color: #10B981;
        --danger-color: #EF4444;
        --warning-color: #F59E0B;
        --info-color: #06B6D4;
        --neutral-100: #F9FAFB;
        --neutral-200: #F3F4F6;
        --neutral-300: #E5E7EB;
        --neutral-600: #4B5563;
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
        position: relative;
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
        padding: 0.75rem 1rem;
        border: 1px solid var(--neutral-300);
        border-radius: 0.5rem;
        font-size: 0.95rem;
        color: var(--neutral-900);
        transition: all 0.2s ease;
        font-family: inherit;
        background: var(--neutral-50);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-control::placeholder {
        color: #6B7280;
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

    .form-check {
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        background: var(--neutral-50);
        border: 1px solid var(--neutral-200);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        width: fit-content;
    }

    .form-check:hover {
        border-color: var(--primary-color);
        background: white;
    }

    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid var(--neutral-300);
        border-radius: 0.25rem;
        cursor: pointer;
        transition: all 0.2s ease;
        accent-color: var(--primary-color);
        margin: 0;
    }

    .form-check-label {
        cursor: pointer;
        font-weight: 500;
        color: var(--neutral-800);
        font-size: 0.95rem;
        margin: 0;
    }

    .info-box {
        padding: 1.25rem;
        border-radius: 0.875rem;
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
        border: 1px solid rgba(6, 182, 212, 0.2);
        margin: 1rem 0;
    }

    .warning-box {
        padding: 1.25rem;
        border-radius: 0.875rem;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(251, 146, 60, 0.05) 100%);
        border: 1px solid rgba(245, 158, 11, 0.2);
        margin: 1rem 0;
    }

    .info-box-title,
    .warning-box-title {
        font-weight: 600;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }

    .info-box-title {
        color: var(--info-color);
    }

    .warning-box-title {
        color: var(--warning-color);
    }

    .info-box-text,
    .warning-box-text {
        margin-top: 0.5rem;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .info-box-text {
        color: var(--neutral-600);
    }

    .warning-box-text {
        color: var(--neutral-600);
    }

    .code-badge {
        background: var(--neutral-200);
        color: var(--danger-color);
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-family: monospace;
        font-size: 0.85rem;
        font-weight: 600;
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
        background: #ea580c;
        border-color: #ea580c;
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
</style>

<div class="page-wrapper">
    <div class="container-lg">
        <div class="premium-header">
            <h1><i class="bi bi-shield-lock"></i> Generate Akun Baru</h1>
            <p class="breadcrumb-text">Buat akun login baru untuk guru atau orang tua siswa</p>
        </div>

        <!-- MAIN FORM -->
        <form action="{{ route('akun.store') }}" method="POST">
            @csrf

                    <!-- Role Selection Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-person-check-fill"></i></div>
                            Pilih Tipe Pengguna
                        </div>

                        <div class="form-group">
                            <label for="role" class="form-label">Role Akun <span class="text-danger">*</span></label>
                            <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required onchange="updateOptions()">
                                <option value="">-- Pilih Role --</option>
                                <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                                <option value="orangtua" {{ old('role') == 'orangtua' ? 'selected' : '' }}>Orang Tua</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group" id="super-admin-check" style="display:none;">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_super_admin" name="is_super_admin" value="1" {{ old('is_super_admin') ? 'checked' : '' }}>
                                <span class="form-check-label">Set sebagai Super Admin (dapat mengelola data guru dan akun)</span>
                            </label>
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Guru Selection Section -->
                    <div class="form-section" id="guru-select" style="display:none;">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-person-fill"></i></div>
                            Data Guru
                        </div>

                        <div class="form-group">
                            <label for="id_guru" class="form-label">Pilih Guru <span class="text-danger">*</span></label>
                            <select class="form-control @error('id_guru') is-invalid @enderror" id="id_guru" name="id_guru">
                                <option value="">-- Pilih Guru --</option>
                                @foreach ($guru as $g)
                                    <option value="{{ $g->id_guru }}" {{ old('id_guru') == $g->id_guru ? 'selected' : '' }}>
                                        {{ $g->nama_guru }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_guru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Student Selection Section -->
                    <div class="form-section" id="siswa-select" style="display:none;">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-people-fill"></i></div>
                            Data Siswa & Orang Tua
                        </div>

                        <div class="form-group">
                            <label for="nomor_induk_siswa" class="form-label">Pilih Siswa <span class="text-danger">*</span></label>
                            <select class="form-control @error('nomor_induk_siswa') is-invalid @enderror" id="nomor_induk_siswa" name="nomor_induk_siswa">
                                <option value="">-- Pilih Siswa --</option>
                                @foreach ($siswa as $s)
                                    <option value="{{ $s->nomor_induk_siswa }}" {{ old('nomor_induk_siswa') == $s->nomor_induk_siswa ? 'selected' : '' }}>
                                        {{ $s->nama_siswa }} ({{ $s->nama_orgtua }})
                                    </option>
                                @endforeach
                            </select>
                            @error('nomor_induk_siswa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Information Section -->
                    <div class="form-section">
                        <div class="info-box">
                            <p class="info-box-title">
                                <i class="bi bi-info-circle-fill"></i> Username Otomatis
                            </p>
                            <p class="info-box-text">
                                Username akan dibuat secara otomatis berdasarkan nama Guru/Siswa. Jika ada duplikat, sistem akan menambahkan suffix (01, 02, dst) untuk memastikan keunikan.
                            </p>
                        </div>

                        <div class="warning-box">
                            <p class="warning-box-title">
                                <i class="bi bi-key-fill"></i> Password Default
                            </p>
                            <p class="warning-box-text">
                                Password akun baru akan otomatis di-generate dengan password <span class="code-badge">password123</span>. Pengguna dapat mengubah password setelah login.
                            </p>
                        </div>
                    </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('akun.index') }}" class="btn-premium btn-cancel">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
                <button type="submit" class="btn-premium btn-save">
                    <i class="bi bi-check-circle"></i> Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateOptions() {
    const role = document.getElementById('role').value;
    document.getElementById('guru-select').style.display = role === 'guru' ? 'block' : 'none';
    document.getElementById('siswa-select').style.display = role === 'orangtua' ? 'block' : 'none';
    document.getElementById('super-admin-check').style.display = role === 'guru' ? 'block' : 'none';
}
updateOptions();
</script>

@endsection
