@extends('layouts.app')

@section('title', 'Tambah Perkembangan')

@section('content')
<div class="row">
    <div class="col-md-10">
        <h2><i class="bi bi-plus-circle"></i> Tambah Perkembangan Anak</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <form action="{{ route('perkembangan.store') }}" method="POST">
            @csrf

            <!-- Info Anak & Guru -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <label for="nomor_induk_siswa" class="form-label">Siswa / Anak <span class="text-danger">*</span></label>
                    <select class="form-control @error('nomor_induk_siswa') is-invalid @enderror" id="nomor_induk_siswa" name="nomor_induk_siswa" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach ($siswa as $s)
                            <option value="{{ $s->nomor_induk_siswa }}" {{ old('nomor_induk_siswa') == $s->nomor_induk_siswa ? 'selected' : '' }}>
                                {{ $s->nama_siswa }} ({{ $s->kelas->nama_kelas ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('nomor_induk_siswa')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Periode Laporan (Auto-generate dari current date) -->
            @php
                $currentMonth = \Carbon\Carbon::now()->month;
                $currentYear = \Carbon\Carbon::now()->year;
                $monthNames = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
            @endphp
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="bulan_display" class="form-label">Bulan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="bulan_display" disabled value="{{ $monthNames[$currentMonth] }}">
                    <input type="hidden" name="bulan" id="bulan" value="{{ $currentMonth }}">
                    <small class="text-muted">Auto-generate berdasarkan tanggal sekarang</small>
                </div>

                <div class="col-md-6">
                    <label for="tahun_display" class="form-label">Tahun <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="tahun_display" disabled value="{{ $currentYear }}">
                    <input type="hidden" name="tahun" id="tahun" value="{{ $currentYear }}">
                    <small class="text-muted">Auto-generate berdasarkan tanggal sekarang</small>
                </div>
            </div>

            <hr>

            <!-- Indikator Capaian (Global) -->
            <div class="mb-4">
                <label class="form-label">Indikator Capaian <span class="text-danger">*</span></label>
                @php
                    $statusDescriptions = [
                        'BB' => 'Anak belum menunjukkan kemampuan dalam aspek ini. Perlu dukungan dan bimbingan intensif dari guru untuk mengembangkan kompetensi ini.',
                        'MB' => 'Anak mulai menunjukkan kemampuan dalam aspek ini namun masih memerlukan bimbingan. Perlu terus didukung untuk mencapai perkembangan yang lebih baik.',
                        'BSH' => 'Anak menunjukkan kemampuan yang sesuai dengan harapan untuk usia/tingkatannya. Anak mampu melaksanakan tugas dengan cukup baik.',
                        'BSB' => 'Anak menunjukkan kemampuan yang sangat menonjol dalam aspek ini. Anak mampu melaksanakan tugas dengan sangat baik dan melampaui harapan.'
                    ];
                @endphp
                <div class="d-flex gap-3 mb-3">
                    <div class="form-check">
                        <input class="form-check-input status-radio" type="radio" id="status_bb" name="status_utama" value="BB" 
                               {{ old('status_utama') == 'BB' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="status_bb">
                            <span class="badge bg-danger">BB</span> Belum Berkembang
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input status-radio" type="radio" id="status_mb" name="status_utama" value="MB" 
                               {{ old('status_utama') == 'MB' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="status_mb">
                            <span class="badge bg-warning text-dark">MB</span> Mulai Berkembang
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input status-radio" type="radio" id="status_bsh" name="status_utama" value="BSH" 
                               {{ old('status_utama') == 'BSH' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="status_bsh">
                            <span class="badge bg-info">BSH</span> Sesuai Harapan
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input status-radio" type="radio" id="status_bsb" name="status_utama" value="BSB" 
                               {{ old('status_utama') == 'BSB' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="status_bsb">
                            <span class="badge bg-success">BSB</span> Sangat Baik
                        </label>
                    </div>
                </div>
                @error('status_utama')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                <!-- Template Deskripsi Auto-generate -->
                <div id="template-deskripsi" class="alert alert-info mb-3" style="display: none;">
                    <strong>Template Deskripsi:</strong>
                    <p id="template-text" class="mb-0 mt-2"></p>
                </div>

                <!-- Form Deskripsi Tambahan -->
                <div class="mb-3">
                    <label for="deskripsi_tambahan" class="form-label">Deskripsi Tambahan</label>
                    <textarea class="form-control" 
                              id="deskripsi_tambahan" 
                              name="deskripsi_tambahan" 
                              rows="3" 
                              placeholder="Tambahkan catatan atau observasi tambahan (opsional)...">{{ old('deskripsi_tambahan') }}</textarea>
                    <small class="text-muted">Tambahkan detail khusus atau catatan penting yang ingin dicatat.</small>
                </div>
            </div>

            <hr>

            <!-- Kategori Perkembangan dengan Nilai (1-10) -->
            <div class="mb-4">
                <label class="form-label">Kategori Perkembangan <span class="text-danger">*</span></label>
                <div class="row">
                    @php
                        $categories = ['Akademik', 'Sosial', 'Emosional'];
                        $selectedCategories = old('kategori', []);
                    @endphp
                    @foreach($categories as $category)
                        <div class="col-md-6 mb-3">
                            <div class="card p-3" style="background-color: #FFF8F4;">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input kategori-checkbox" type="checkbox" 
                                                   id="checkbox_{{ $category }}" name="kategori[]" value="{{ $category }}"
                                                   data-kategori="{{ strtolower($category) }}"
                                                   {{ in_array($category, (array)$selectedCategories) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="checkbox_{{ $category }}">
                                                {{ $category }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <label for="nilai_{{ strtolower($category) }}" class="form-label mb-1">Nilai (1-10)</label>
                                        <select class="form-select @error('nilai_' . strtolower($category)) is-invalid @enderror" 
                                                id="nilai_{{ strtolower($category) }}" 
                                                name="nilai_{{ strtolower($category) }}"
                                                style="display: none;">
                                            <option value="">Pilih nilai...</option>
                                            @for ($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{ old('nilai_' . strtolower($category)) == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('nilai_' . strtolower($category))
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('kategori')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>


            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Simpan
                </button>
                <a href="{{ route('perkembangan.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .border-danger { }
    .border-warning { }
    .border-info { }
    .border-success { }
</style>

<script>
    // Template descriptions untuk setiap indikator (ini disesuaikan dengan kebutuhan)
    const templateDescriptions = {
        'BB': 'Anak belum menunjukkan kemampuan dalam aspek ini. Perlu dukungan dan bimbingan intensif dari guru untuk mengembangkan kompetensi ini.',
        'MB': 'Anak mulai menunjukkan kemampuan dalam aspek ini namun masih memerlukan bimbingan. Perlu terus didukung untuk mencapai perkembangan yang lebih baik.',
        'BSH': 'Anak menunjukkan kemampuan yang sesuai dengan harapan untuk usia/tingkatannya. Anak mampu melaksanakan tugas dengan cukup baik.',
        'BSB': 'Anak menunjukkan kemampuan yang sangat menonjol dalam aspek ini. Anak mampu melaksanakan tugas dengan sangat baik dan melampaui harapan.'
    };

    // Handle status radio untuk show/hide template
    document.querySelectorAll('.status-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const selectedStatus = this.value;
            const templateDiv = document.getElementById('template-deskripsi');
            const templateText = document.getElementById('template-text');
            
            if (selectedStatus && templateDescriptions[selectedStatus]) {
                templateText.textContent = templateDescriptions[selectedStatus];
                templateDiv.style.display = 'block';
            } else {
                templateDiv.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.kategori-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const kategoriLower = this.dataset.kategori;
            const select = document.getElementById('nilai_' + kategoriLower);
            
            if (this.checked) {
                select.style.display = 'block';
                select.required = true;
            } else {
                select.style.display = 'none';
                select.required = false;
                select.value = '';
            }
        });
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        const selectedCategories = document.querySelectorAll('.kategori-checkbox:checked');
        if (selectedCategories.length !== 3) {
            e.preventDefault();
            alert('Harus mengisi ketiga kategori perkembangan (Akademik, Sosial, Emosional)');
            return false;
        }

        for (let checkbox of selectedCategories) {
            const kategoriLower = checkbox.dataset.kategori;
            const nilaiInput = document.getElementById('nilai_' + kategoriLower);

            if (!nilaiInput.value) {
                e.preventDefault();
                alert('Isi nilai untuk kategori ' + checkbox.value);
                return false;
            }
        }
    });

    // Initialize on page load
    window.addEventListener('DOMContentLoaded', function() {
        // Show template jika status sudah dipilih
        const checkedStatus = document.querySelector('.status-radio:checked');
        if (checkedStatus) {
            const selectedStatus = checkedStatus.value;
            const templateDiv = document.getElementById('template-deskripsi');
            const templateText = document.getElementById('template-text');
            
            if (selectedStatus && templateDescriptions[selectedStatus]) {
                templateText.textContent = templateDescriptions[selectedStatus];
                templateDiv.style.display = 'block';
            }
        }

        // Show selects untuk kategori yang sudah di-check
        document.querySelectorAll('.kategori-checkbox:checked').forEach(checkbox => {
            const kategoriLower = checkbox.dataset.kategori;
            const select = document.getElementById('nilai_' + kategoriLower);
            select.style.display = 'block';
            select.required = true;
        });

        updateKategoriHidden();
    });
</script>
@endsection
