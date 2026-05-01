@extends('layouts.app')

@section('title', 'Edit Tagihan')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --primary-light: #FFEDE3;
        --primary-dark: #EA580C;
        --success-color: #10B981;
        --danger-color: #EF4444;
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

    .status-display {
        padding: 0.875rem 1rem;
        border: 1.5px solid var(--neutral-300);
        border-radius: 0.75rem;
        background: var(--neutral-100);
        color: var(--neutral-900);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .status-display i {
        font-size: 1.1rem;
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
        margin: 1.5rem 0;
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
                    <h1><i class="bi bi-pencil-square"></i> Edit Tagihan</h1>
                    <p class="breadcrumb-text">Perbarui informasi tagihan pada form di bawah</p>
                </div>
                <form action="{{ route('tagihan.update', $tagihan->id_tagihan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Informasi Tagihan Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-info-circle-fill"></i></div>
                            Informasi Tagihan
                        </div>

                        <div class="form-group">
                            <label for="nomor_induk_siswa" class="form-label">Pilih Siswa <span class="text-danger">*</span></label>
                            <select class="form-control @error('nomor_induk_siswa') is-invalid @enderror" id="nomor_induk_siswa" name="nomor_induk_siswa" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach ($siswa as $s)
                                    <option value="{{ $s->nomor_induk_siswa }}" {{ old('nomor_induk_siswa', $tagihan->nomor_induk_siswa) == $s->nomor_induk_siswa ? 'selected' : '' }}>
                                        {{ $s->nama_siswa }}
                                    </option>
                                @endforeach
                            </select>
                            @error('nomor_induk_siswa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="jumlah_tagihan" class="form-label">Jumlah Tagihan SPP <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('jumlah_tagihan') is-invalid @enderror" 
                                   id="jumlah_tagihan" name="jumlah_tagihan" value="{{ old('jumlah_tagihan', $tagihan->jumlah_tagihan) }}" step="0.01" required placeholder="Masukkan jumlah dalam rupiah">
                            @error('jumlah_tagihan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="periode" class="form-label">Periode Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('periode') is-invalid @enderror" 
                                   id="periode" name="periode" value="{{ old('periode', $tagihan->periode) }}" required placeholder="Contoh: Januari 2026, Februari 2026">
                            @error('periode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Status Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-shield-check"></i></div>
                            Status Pembayaran
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status Pembayaran Saat Ini</label>
                            <div class="status-display">
                                <i class="bi bi-check-circle-fill"></i>
                                <strong>{{ ucfirst(str_replace('_', ' ', $tagihan->payment_status)) }}</strong>
                            </div>
                            <span class="form-text"><i class="bi bi-info-circle"></i> Status berubah otomatis ketika orangtua melakukan pembayaran melalui aplikasi mobile. Anda tidak dapat mengubah status secara manual.</span>
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Information Section -->
                    <div class="form-section">
                        <div class="info-box">
                            <p class="info-box-title">
                                <i class="bi bi-info-circle-fill"></i> Informasi Penting
                            </p>
                            <p class="info-box-text">
                                Perubahan status pembayaran dilakukan secara otomatis melalui sistem pembayaran mobile orang tua. Jika terdapat kekeliruan data, silakan hubungi administrator untuk bantuan lebih lanjut.
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn-premium btn-save">
                            <i class="bi bi-check-circle-fill"></i> Perbarui Tagihan
                        </button>
                        <a href="{{ route('tagihan.index') }}" class="btn-premium btn-cancel">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
