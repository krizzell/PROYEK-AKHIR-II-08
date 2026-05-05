@extends('layouts.app')

@section('title', 'Apply All Tagihan')

@section('content')
<style>
    :root {
        --text-primary: #111827;
        --text-secondary: #6B7280;
        --border-color: #E5E7EB;
        --hover-bg: #F9FAFB;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        font-size: 0.95rem;
        color: var(--text-primary);
        background: white;
        transition: all 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: #F97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    }

    .radio-group {
        display: flex;
        gap: 2rem;
        margin-top: 0.75rem;
    }

    .radio-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .radio-item input[type="radio"] {
        cursor: pointer;
    }

    .radio-item label {
        cursor: pointer;
        margin: 0;
        font-weight: 500;
        color: var(--text-primary);
    }

    .preview-box {
        background: #ECFDF5;
        border-left: 4px solid #06B6D4;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        display: none;
    }

    .preview-box.active {
        display: block;
    }

    .preview-box strong {
        color: #047857;
    }

    .preview-box small {
        color: var(--text-secondary);
        display: block;
        margin-top: 0.5rem;
    }

    .button-group {
        display: flex;
        gap: 0.75rem;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        border: none;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        font-size: 0.95rem;
    }

    .btn-primary {
        background: #F97316;
        color: white;
    }

    .btn-primary:hover {
        background: #E85000;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: white;
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: #F3F4F6;
        border-color: #D1D5DB;
        color: var(--text-primary);
        transform: translateY(-2px);
    }

    .error-message {
        background: #FEE2E2;
        border: 1px solid #FCA5A5;
        border-left: 4px solid #EF4444;
        color: #991B1B;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .error-message strong {
        display: block;
        margin-bottom: 0.5rem;
    }

    .error-message ul {
        margin: 0;
        padding-left: 1.5rem;
    }

    .error-message li {
        margin: 0.25rem 0;
    }

    #kelasDiv {
        display: none;
    }
</style>

<div class="page-header">
    <h1><i class="bi bi-lightning-fill"></i> Apply All Tagihan</h1>
</div>

<!-- Pesan Error -->
@if ($errors->any())
    <div class="error-message">
        <strong><i class="bi bi-exclamation-triangle"></i> Validasi Gagal</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-card">
    <form action="{{ route('tagihan.bulkCreateStore') }}" method="POST">
        @csrf

        <!-- Target -->
        <div class="form-group">
            <label class="form-label">Target Pembuat Tagihan</label>
            <div class="radio-group">
                <div class="radio-item">
                    <input type="radio" id="tipe_semua" name="tipe_target" value="semua_siswa" 
                           checked onchange="updatePreview()">
                    <label for="tipe_semua">Semua Siswa</label>
                </div>
                @if($isSuperAdmin)
                <div class="radio-item">
                    <input type="radio" id="tipe_kelas" name="tipe_target" value="per_kelas"
                           onchange="updatePreview()">
                    <label for="tipe_kelas">Per Kelas</label>
                </div>
                @endif
            </div>
        </div>

        <!-- Pilih Kelas -->
        <div class="form-group" id="kelasDiv">
            <label for="id_kelas" class="form-label">Pilih Kelas</label>
            <select name="id_kelas" id="id_kelas" class="form-select @error('id_kelas') is-invalid @enderror"
                    onchange="updatePreview()">
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id_kelas }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
            @error('id_kelas')
                <div style="color: #DC2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Jumlah Tagihan -->
        <div class="form-group">
            <label for="jumlah_tagihan" class="form-label">Jumlah Tagihan (Rp)</label>
            <input type="number" name="jumlah_tagihan" id="jumlah_tagihan" 
                   class="form-control @error('jumlah_tagihan') is-invalid @enderror"
                   value="{{ old('jumlah_tagihan') }}" placeholder="Contoh: 250000" min="1" onchange="updatePreview()">
            @error('jumlah_tagihan')
                <div style="color: #DC2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Periode: Bulan dan Tahun -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="bulan" class="form-label">Bulan</label>
                <select name="bulan" id="bulan" class="form-select @error('bulan') is-invalid @enderror"
                        onchange="updatePreview()">
                    <option value="">-- Pilih Bulan --</option>
                    <option value="1" {{ old('bulan') == 1 ? 'selected' : '' }}>Januari</option>
                    <option value="2" {{ old('bulan') == 2 ? 'selected' : '' }}>Februari</option>
                    <option value="3" {{ old('bulan') == 3 ? 'selected' : '' }}>Maret</option>
                    <option value="4" {{ old('bulan') == 4 ? 'selected' : '' }}>April</option>
                    <option value="5" {{ old('bulan') == 5 ? 'selected' : '' }}>Mei</option>
                    <option value="6" {{ old('bulan') == 6 ? 'selected' : '' }}>Juni</option>
                    <option value="7" {{ old('bulan') == 7 ? 'selected' : '' }}>Juli</option>
                    <option value="8" {{ old('bulan') == 8 ? 'selected' : '' }}>Agustus</option>
                    <option value="9" {{ old('bulan') == 9 ? 'selected' : '' }}>September</option>
                    <option value="10" {{ old('bulan') == 10 ? 'selected' : '' }}>Oktober</option>
                    <option value="11" {{ old('bulan') == 11 ? 'selected' : '' }}>November</option>
                    <option value="12" {{ old('bulan') == 12 ? 'selected' : '' }}>Desember</option>
                </select>
                @error('bulan')
                    <div style="color: #DC2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="tahun" class="form-label">Tahun</label>
                <select name="tahun" id="tahun" class="form-select @error('tahun') is-invalid @enderror"
                        onchange="updatePreview()">
                    <option value="">-- Pilih Tahun --</option>
                    @for($y = 2024; $y <= 2030; $y++)
                        <option value="{{ $y }}" {{ old('tahun', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                @error('tahun')
                    <div style="color: #DC2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Preview -->
        <div class="preview-box" id="previewBox">
            <strong><i class="bi bi-info-circle"></i> Preview</strong><br>
            Akan membuat <strong id="previewCount">0</strong> tagihan untuk
            <strong id="previewTarget">semua siswa</strong>
            <small>⚠️ Duplikat tagihan dengan periode yang sama akan dilewati</small>
        </div>

        <!-- Buttons -->
        <div class="button-group">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Apply All
            </button>
            <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
        </div>
    </form>
</div>

<script>
    // Data untuk preview
    const kelasData = {!! json_encode($kelas->mapWithKeys(function($k) {
        return [$k->id_kelas => $k->siswa->count()];
    })->toArray()) !!};
    
    const bulanNama = [
        '', 'Januari', 'Februari', 'Maret', 'April',
        'Mei', 'Juni', 'Juli', 'Agustus',
        'September', 'Oktober', 'November', 'Desember'
    ];

    function updatePreview() {
        const tipeTarget = document.querySelector('input[name="tipe_target"]:checked').value;
        const kelasDivElement = document.getElementById('kelasDiv');
        const kelasInputElement = document.getElementById('id_kelas');
        const previewBox = document.getElementById('previewBox');
        const previewCount = document.getElementById('previewCount');
        const previewTarget = document.getElementById('previewTarget');
        const bulanSelect = document.getElementById('bulan');
        const tahunSelect = document.getElementById('tahun');

        // Tampilkan/sembunyikan pilihan kelas
        if (tipeTarget === 'per_kelas') {
            kelasDivElement.style.display = 'block';
        } else {
            kelasDivElement.style.display = 'none';
            kelasInputElement.value = '';
        }

        // Hitung jumlah siswa
        let count = 0;
        let targetText = '';

        if (tipeTarget === 'semua_siswa') {
            count = {{ $kelas->sum(fn($k) => $k->siswa->count()) }};
            targetText = 'semua siswa';
        } else {
            const selectedKelas = kelasInputElement.value;
            count = selectedKelas ? (kelasData[selectedKelas] || 0) : 0;
            const selectedText = document.querySelector('#id_kelas option:checked')?.textContent || 'kelas';
            targetText = selectedText;
        }

        // Update preview dengan periode
        const bulan = bulanSelect.value ? bulanNama[parseInt(bulanSelect.value)] : '?';
        const tahun = tahunSelect.value || '?';
        const periodeTeks = `SPP ${bulan} ${tahun}`;
        
        previewCount.textContent = count;
        previewTarget.textContent = `${targetText} (${periodeTeks})`;
        
        if (count > 0 && bulanSelect.value && tahunSelect.value) {
            previewBox.classList.add('active');
        } else {
            previewBox.classList.remove('active');
        }
    }

    // Initialize preview
    document.addEventListener('DOMContentLoaded', updatePreview);
</script>

@endsection
