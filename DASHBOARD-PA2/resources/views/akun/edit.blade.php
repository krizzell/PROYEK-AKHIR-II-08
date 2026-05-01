@extends('layouts.app')

@section('title', 'Edit Akun')

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
        color: #4B5563;
        font-size: 0.95rem;
        margin-top: 0.5rem;
    }

    .premium-card {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.6);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08), 0 4px 8px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
        animation: fadeIn 0.6s ease-out;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
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
        padding: 1rem;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, rgba(0, 102, 255, 0.02) 0%, rgba(59, 130, 246, 0.02) 100%);
        border: 1px solid var(--neutral-300);
        transition: all 0.3s ease;
    }

    .form-check:hover {
        background: linear-gradient(135deg, rgba(0, 102, 255, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
    }

    .form-check-input {
        width: 1.5rem;
        height: 1.5rem;
        border: 2px solid var(--neutral-300);
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.3s ease;
        accent-color: var(--primary-color);
    }

    .form-check-label {
        cursor: pointer;
        font-weight: 500;
        color: var(--neutral-900);
        margin-left: 0.75rem;
    }

    .form-text {
        font-size: 0.85rem;
        color: var(--neutral-600);
        margin-top: 0.5rem;
        display: block;
    }

    .info-box {
        padding: 1.25rem;
        border-radius: 0.875rem;
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
        border: 1px solid rgba(6, 182, 212, 0.2);
        margin: 1rem 0;
    }

    .info-box-title {
        font-weight: 600;
        color: var(--info-color);
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }

    .info-box-text {
        margin-top: 0.5rem;
        color: var(--neutral-600);
        font-size: 0.9rem;
        line-height: 1.5;
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

    .btn-cancel {
        background: white;
        color: #4B5563;
        border: 2px solid var(--neutral-300);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .btn-cancel:hover {
        background: var(--neutral-100);
        border-color: var(--neutral-400);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="page-wrapper">
    <div style="max-width: 900px; margin: 0 auto; padding: 0 1.5rem;">
        <div class="premium-card">
            <div class="premium-card-body">
                <div class="premium-header">
                    <h1><i class="bi bi-pencil-square"></i> Edit Akun</h1>
                    <p class="breadcrumb-text">Perbarui informasi akun pengguna</p>
                </div>
                <form action="{{ route('akun.update', $akun->id_akun) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Role & Association Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-person-check-fill"></i></div>
                            Informasi Akun
                        </div>

                        <div class="form-group">
                            <label for="role" class="form-label">Role Akun <span class="text-danger">*</span></label>
                            <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required onchange="updateOptions()">
                                <option value="">-- Pilih Role --</option>
                                <option value="guru" {{ old('role', $akun->role) == 'guru' ? 'selected' : '' }}>Guru</option>
                                <option value="orangtua" {{ old('role', $akun->role) == 'orangtua' ? 'selected' : '' }}>Orang Tua</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group" id="super-admin-check" style="display:none;">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_super_admin" name="is_super_admin" value="1" {{ old('is_super_admin', $akun->is_super_admin) ? 'checked' : '' }}>
                                <span class="form-check-label">Set sebagai Super Admin (dapat mengelola data guru dan akun)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Guru Association Section -->
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
                                    <option value="{{ $g->id_guru }}" {{ old('id_guru', $akun->id_guru) == $g->id_guru ? 'selected' : '' }}>
                                        {{ $g->nama_guru }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_guru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Student Association Section -->
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
                                    <option value="{{ $s->nomor_induk_siswa }}" {{ old('nomor_induk_siswa', $akun->nomor_induk_siswa) == $s->nomor_induk_siswa ? 'selected' : '' }}>
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

                    <!-- Credentials Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-lock-fill"></i></div>
                            Kredensial Login
                        </div>

                        <div class="form-group">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username', $akun->username) }}" required placeholder="Masukkan username">
                            <span class="form-text"><i class="bi bi-info-circle"></i> Username bisa diedit jika diperlukan</span>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                            <span class="form-text"><i class="bi bi-info-circle"></i> Kosongkan field ini jika tidak ingin mengubah password</span>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Information Section -->
                    <div class="form-section">
                        <div class="info-box">
                            <p class="info-box-title">
                                <i class="bi bi-info-circle-fill"></i> Catatan Penting
                            </p>
                            <p class="info-box-text">
                                Perubahan pada Role atau asosiasi pengguna akan mempengaruhi akses pengguna di sistem. Pastikan perubahan sudah benar sebelum menyimpan.
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn-premium btn-save">
                            <i class="bi bi-check-circle-fill"></i> Perbarui Akun
                        </button>
                        <a href="{{ route('akun.index') }}" class="btn-premium btn-cancel">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
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
