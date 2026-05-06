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
        display: none;
    }

    .radio-item {
        display: none;
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

        <!-- Jumlah Tagihan -->
        <div class="form-group">
            <label for="jumlah_tagihan" class="form-label">Jumlah Tagihan (Rp) <span style="color: #EF4444;">*</span></label>
            <input type="number" name="jumlah_tagihan" id="jumlah_tagihan" 
                   class="form-control @error('jumlah_tagihan') is-invalid @enderror"
                   value="{{ old('jumlah_tagihan') }}" placeholder="Contoh: 250000" min="1" step="1" required>
            @error('jumlah_tagihan')
                <div style="color: #DC2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Periode: Bulan dan Tahun (Auto Current) -->
        <div class="form-group">
            <label class="form-label">Periode</label>
            <div style="padding: 0.75rem; background: #F3F4F6; border-radius: 0.5rem; font-weight: 500;">
                <i class="bi bi-calendar-event"></i> 
                <strong id="periodeDisplay">SPP {{ now()->format('F Y') }}</strong>
            </div>
            <!-- Hidden inputs untuk bulan dan tahun current -->
            <input type="hidden" name="bulan" value="{{ now()->month }}">
            <input type="hidden" name="tahun" value="{{ now()->year }}">
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
    // No additional scripts needed
</script>

@endsection
