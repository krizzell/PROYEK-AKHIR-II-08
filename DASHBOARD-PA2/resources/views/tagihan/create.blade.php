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

    .student-search-wrapper {
        position: relative;
        margin-bottom: 0.75rem;
    }

    .student-search-wrapper i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--neutral-600);
        font-size: 0.95rem;
        pointer-events: none;
    }

    .student-search-input {
        padding-left: 2.75rem;
    }

    .student-search-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 0.5rem;
        color: var(--neutral-600);
        font-size: 0.82rem;
        font-weight: 500;
    }

    .student-search-empty {
        display: none;
        margin-top: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        background: #FFF7ED;
        border: 1px solid #FED7AA;
        color: #9A3412;
        font-size: 0.88rem;
        font-weight: 500;
    }

    .student-options {
        display: none;
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 0.35rem);
        max-height: 260px;
        overflow-y: auto;
        background: #FFFFFF;
        border: 1px solid var(--neutral-300);
        border-radius: 0.75rem;
        box-shadow: var(--shadow-lg);
        z-index: 20;
        padding: 0.35rem;
    }

    .student-options.show {
        display: block;
    }

    .student-option {
        width: 100%;
        border: 0;
        background: transparent;
        padding: 0.75rem 0.85rem;
        border-radius: 0.55rem;
        text-align: left;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .student-option:hover,
    .student-option.active {
        background: #FFF7ED;
    }

    .student-option-name {
        display: block;
        color: var(--neutral-900);
        font-weight: 700;
        font-size: 0.92rem;
    }

    .student-option-meta {
        display: block;
        color: var(--neutral-600);
        font-size: 0.8rem;
        margin-top: 0.2rem;
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
                            <div class="student-search-wrapper">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control student-search-input @error('nomor_induk_siswa') is-invalid @enderror" id="siswa-search" autocomplete="off" placeholder="Cari dan pilih nama siswa...">
                                <input type="hidden" id="nomor_induk_siswa" name="nomor_induk_siswa" value="{{ old('nomor_induk_siswa') }}" required>
                                <div class="student-options" id="student-options">
                                    @foreach ($siswa as $s)
                                        @php
                                            $kelasNama = $s->kelas->nama_kelas ?? 'Tanpa kelas';
                                            $label = $s->nama_siswa . ' - ' . $kelasNama . ' (NIS: ' . $s->nomor_induk_siswa . ')';
                                            $searchText = strtolower($label . ' ' . $s->nama_siswa . ' ' . $s->nomor_induk_siswa . ' ' . $kelasNama);
                                        @endphp
                                        <button type="button" class="student-option" data-value="{{ $s->nomor_induk_siswa }}" data-label="{{ $label }}" data-search="{{ $searchText }}">
                                            <span class="student-option-name">{{ $s->nama_siswa }}</span>
                                            <span class="student-option-meta">{{ $kelasNama }} - NIS: {{ $s->nomor_induk_siswa }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="student-search-meta">
                                <span id="student-search-count">Menampilkan {{ $siswa->count() }} siswa</span>
                                <button type="button" id="student-search-clear" style="display: none; border: none; background: transparent; color: var(--primary-color); font-weight: 700; padding: 0;">
                                    Bersihkan pencarian
                                </button>
                            </div>
                            <div class="student-search-empty" id="student-search-empty">
                                Tidak ada siswa yang cocok dengan pencarian.
                            </div>
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
                                <strong>Satu siswa hanya dapat memiliki satu tagihan per periode.</strong> Jika Anda mencoba membuat tagihan untuk siswa yang sudah memiliki tagihan di periode yang sama, sistem akan menolak dan menampilkan pesan error.<br><br>
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
    const siswaSearchInput = document.getElementById('siswa-search');
    const siswaSelect = document.getElementById('nomor_induk_siswa');
    const siswaSearchCount = document.getElementById('student-search-count');
    const siswaSearchClear = document.getElementById('student-search-clear');
    const siswaSearchEmpty = document.getElementById('student-search-empty');
    const siswaOptionsWrapper = document.getElementById('student-options');
    const siswaOptions = Array.from(document.querySelectorAll('.student-option'));

    function setSelectedSiswa(option) {
        siswaSelect.value = option.dataset.value;
        siswaSearchInput.value = option.dataset.label;
        siswaSearchInput.classList.remove('is-invalid');
        siswaOptionsWrapper.classList.remove('show');
        siswaSearchClear.style.display = 'inline-flex';
        siswaSearchEmpty.textContent = 'Tidak ada siswa yang cocok dengan pencarian.';
        siswaOptions.forEach((item) => item.classList.remove('active'));
        option.classList.add('active');
        siswaSelect.dispatchEvent(new Event('change'));
    }

    function clearSelectedSiswa() {
        siswaSelect.value = '';
        siswaOptions.forEach((option) => option.classList.remove('active'));
        siswaSelect.dispatchEvent(new Event('change'));
    }

    function filterSiswaOptions() {
        const keyword = siswaSearchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        siswaOptions.forEach((option) => {
            const isMatch = option.dataset.search.includes(keyword);
            option.style.display = isMatch ? 'block' : 'none';
            if (isMatch) visibleCount += 1;
        });

        const selectedOption = siswaOptions.find((option) => option.dataset.value === siswaSelect.value);
        const selectedLabel = selectedOption ? selectedOption.dataset.label.toLowerCase() : '';

        if (keyword === '') {
            clearSelectedSiswa();
        } else if (keyword !== selectedLabel) {
            clearSelectedSiswa();
        }

        siswaSearchCount.textContent = keyword
            ? `Menampilkan ${visibleCount} siswa yang cocok`
            : `Menampilkan ${siswaOptions.length} siswa`;
        siswaSearchClear.style.display = keyword ? 'inline-flex' : 'none';
        siswaSearchEmpty.textContent = 'Tidak ada siswa yang cocok dengan pencarian.';
        siswaSearchEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
        siswaOptionsWrapper.classList.toggle('show', visibleCount > 0);
    }

    siswaSearchInput.addEventListener('focus', filterSiswaOptions);
    siswaSearchInput.addEventListener('input', filterSiswaOptions);
    siswaSearchInput.addEventListener('keydown', function(event) {
        if (event.key !== 'Enter' || !siswaOptionsWrapper.classList.contains('show')) {
            return;
        }

        const firstVisibleOption = siswaOptions.find((option) => option.style.display !== 'none');
        if (firstVisibleOption) {
            event.preventDefault();
            setSelectedSiswa(firstVisibleOption);
        }
    });
    siswaSearchClear.addEventListener('click', function() {
        siswaSearchInput.value = '';
        filterSiswaOptions();
        siswaSearchInput.focus();
    });
    siswaOptions.forEach((option) => {
        option.addEventListener('click', function() {
            setSelectedSiswa(option);
        });
    });
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.student-search-wrapper')) {
            siswaOptionsWrapper.classList.remove('show');
        }
    });

    const oldSelectedSiswa = siswaSelect.value;
    if (oldSelectedSiswa) {
        const selectedOption = siswaOptions.find((option) => option.dataset.value === oldSelectedSiswa);
        if (selectedOption) {
            setSelectedSiswa(selectedOption);
        }
    }

    siswaSearchInput.closest('form').addEventListener('submit', function(event) {
        if (siswaSelect.value) return;

        event.preventDefault();
        siswaSearchInput.focus();
        siswaSearchInput.classList.add('is-invalid');
        siswaSearchEmpty.style.display = 'block';
        siswaSearchEmpty.textContent = 'Pilih salah satu siswa dari hasil pencarian terlebih dahulu.';
    });

    // Live check untuk duplikat tagihan
    document.getElementById('nomor_induk_siswa').addEventListener('change', async function() {
        const siswaId = this.value;
        const periodeInput = document.querySelector('input[name="periode"]');
        const periode = periodeInput.value;
        const duplikatWarning = document.getElementById('duplikat-warning');

        if (!siswaId || !periode) {
            if (duplikatWarning) {
                duplikatWarning.remove();
            }
            document.querySelector('button[type="submit"]').disabled = false;
            document.querySelector('button[type="submit"]').style.opacity = '1';
            return;
        }

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
