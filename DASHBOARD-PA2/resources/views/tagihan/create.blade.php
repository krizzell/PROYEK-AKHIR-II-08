@extends('layouts.app')

@section('title', 'Buat Tagihan')

@section('content')
<style>
    :root {
        --primary-color: #FF7A00;
        --primary-light: #FEF3C7;
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
            <h1><i class="bi bi-receipt"></i> Buat Tagihan Baru</h1>
            <p class="breadcrumb-text">Isi form di bawah untuk membuat tagihan SPP baru</p>
        </div>

        <!-- ERROR ALERT DISPLAY -->
        @if (session('error'))
            <div style="padding: 1rem; border-radius: 0.5rem; background: #FEE2E2; border: 1px solid #FECACA; margin-bottom: 1.5rem; color: #991B1B; font-weight: 500; font-size: 0.95rem;">
                {!! session('error') !!}
            </div>
        @elseif ($errors->any())
            <div style="padding: 1rem; border-radius: 0.5rem; background: #FEE2E2; border: 1px solid #FECACA; margin-bottom: 1.5rem; color: #991B1B; font-weight: 500; font-size: 0.95rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- MAIN FORM -->
        <form action="{{ route('tagihan.store') }}" method="POST">
            @csrf

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
                                    <option value="{{ $s->nomor_induk_siswa }}" {{ old('nomor_induk_siswa') == $s->nomor_induk_siswa ? 'selected' : '' }}>
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
                                   id="jumlah_tagihan" name="jumlah_tagihan" value="{{ old('jumlah_tagihan') }}" step="0.01" required placeholder="Masukkan jumlah dalam rupiah">
                            @error('jumlah_tagihan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="periode" class="form-label">Periode Pembayaran <span class="text-danger">*</span></label>
                            @php
                                $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $periodeDisplay = 'SPP ' . $bulanNama[now()->month] . ' ' . now()->year;
                            @endphp
                            <div style="padding: 0.75rem; background: #F3F4F6; border: 1px solid var(--neutral-300); border-radius: 0.5rem; font-weight: 500;">
                                <i class="bi bi-calendar-event"></i> 
                                <strong>{{ $periodeDisplay }}</strong>
                            </div>
                            <!-- Hidden input dengan periode otomatis -->
                            <input type="hidden" name="periode" value="{{ $periodeDisplay }}">
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
                                ⚠️ <strong>Satu siswa hanya dapat memiliki satu tagihan per periode.</strong> Jika Anda mencoba membuat tagihan untuk siswa yang sudah memiliki tagihan di periode yang sama, sistem akan menolak dan menampilkan pesan error.<br><br>
                                Status pembayaran akan berubah otomatis menjadi "Lunas" ketika orangtua melakukan pembayaran melalui aplikasi mobile. Anda tidak dapat mengubah status pembayaran secara manual dari sini.
                            </p>
                        </div>
                    </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('tagihan.index') }}" class="btn-premium btn-cancel">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
                <button type="submit" class="btn-premium btn-save">
                    <i class="bi bi-check-circle"></i> Simpan Tagihan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Live check untuk duplikat tagihan
    document.getElementById('nomor_induk_siswa').addEventListener('change', async function() {
        const siswaId = this.value;
        const periodeInput = document.querySelector('input[name="periode"]');
        const periode = periodeInput.value;
        const duplikatWarning = document.getElementById('duplikat-warning');

        if (!siswaId || !periode) return;

        try {
            // Check apakah sudah ada tagihan untuk siswa + periode ini
            const response = await fetch(`/api/tagihan/check-duplikat?siswa=${siswaId}&periode=${encodeURIComponent(periode)}`);
            const data = await response.json();

            if (data.exists) {
                // Tampilkan warning jika sudah ada
                if (!duplikatWarning) {
                    const warningDiv = document.createElement('div');
                    warningDiv.id = 'duplikat-warning';
                    warningDiv.style.cssText = 'padding: 1rem; background: #FEF3C7; border: 1px solid #FCD34D; border-radius: 0.5rem; margin-bottom: 1.5rem; color: #92400E; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;';
                    warningDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> <span>⚠️ Siswa ini sudah memiliki tagihan untuk periode <strong>' + periode + '</strong>. Tidak dapat membuat tagihan duplikat.</span>';
                    const formSection = document.querySelector('.form-section');
                    formSection.parentNode.insertBefore(warningDiv, formSection);
                }
                document.querySelector('button[type="submit"]').disabled = true;
                document.querySelector('button[type="submit"]').style.opacity = '0.5';
            } else {
                // Hapus warning jika tidak ada duplikat
                const duplikatWarning = document.getElementById('duplikat-warning');
                if (duplikatWarning) {
                    duplikatWarning.remove();
                }
                document.querySelector('button[type="submit"]').disabled = false;
                document.querySelector('button[type="submit"]').style.opacity = '1';
            }
        } catch (error) {
            console.error('Error checking duplikat:', error);
        }
    });
</script>

@endsection
