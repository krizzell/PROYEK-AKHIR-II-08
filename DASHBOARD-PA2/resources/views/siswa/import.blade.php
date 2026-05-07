@extends('layouts.app')

@section('title', 'Import Data Siswa')

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

    .content-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .card h2 {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-top: 0;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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
    .file-input {
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
    .file-input:focus {
        outline: none;
        border-color: #F97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    }

    .file-input {
        cursor: pointer;
        padding: 0.75rem;
    }

    .info-box {
        background: #ECFDF5;
        border-left: 4px solid #06B6D4;
        padding: 1rem;
        border-radius: 0.5rem;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }

    .info-box strong {
        display: block;
        margin-bottom: 0.5rem;
        color: #047857;
    }

    .info-box ul {
        margin: 0;
        padding-left: 1.5rem;
    }

    .info-box li {
        margin: 0.25rem 0;
        color: var(--text-secondary);
    }

    .template-list {
        list-style: none;
        padding: 0;
        margin: 1rem 0;
    }

    .template-list li {
        padding: 0.75rem;
        background: #FAFBFC;
        border-left: 2px solid #F97316;
        margin-bottom: 0.5rem;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        color: var(--text-secondary);
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
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .content-wrapper {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <h1><i class="bi bi-upload"></i> Import Data Siswa</h1>
</div>

<div class="content-wrapper">
    <!-- Upload Form -->
    <div class="card">
        <h2><i class="bi bi-file-earmark-excel"></i> Upload File CSV</h2>

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

        <div class="info-box">
            <strong>Format File yang Didukung:</strong>
            <ul>
                <li>CSV UTF-8 (.csv)</li>
                <li>Excel 2007+ (.xlsx)</li>
                <li>Excel 97-2003 (.xls)</li>
            </ul>
        </div>

        <form action="{{ route('siswa.importStore') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="file" class="form-label">Pilih File CSV / Excel</label>
                <input type="file" name="file" id="file" class="file-input @error('file') is-invalid @enderror"
                       accept=".csv,.xlsx,.xls" required onchange="updateFileName(this)">
                @error('file')
                    <div style="color: #DC2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
                <small style="color: var(--text-secondary); display: block; margin-top: 0.5rem;">
                    Format: CSV, XLSX, XLS | Ukuran maksimal: 5MB
                </small>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cloud-arrow-up"></i> Upload & Import
                </button>
                <a href="{{ route('siswa.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Format Template -->
    <div class="card">
        <h2><i class="bi bi-table"></i> Format File Template</h2>

        <div class="info-box">
            <strong>Struktur Kolom Excel:</strong>
        </div>

        <div style="overflow-x: auto; margin-bottom: 1.5rem;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #F3F4F6; border-bottom: 2px solid var(--border-color);">
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">Kolom</th>
                        <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary);">Contoh</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 600;">NISN</td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);">0001234567</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 600;">Nama Siswa</td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);">Adi Pratama</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 600;">Kelas</td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);">Kelas A</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 600;">Orang Tua</td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);">Bapak Ahmad</td>
                    </tr>
                    <tr>
                        <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 600;">Jenis Kelamin</td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);">L atau P</td>
                    </tr>
                    <tr>
                        <td style="padding: 0.75rem; color: var(--text-primary); font-weight: 600;">Alamat</td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);">Jl. Gatot Subroto No. 123</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="info-box">
            <strong>Catatan Penting:</strong>
            <ul>
                <li>NISN harus unik (tidak boleh duplikat)</li>
                <li>Jenis Kelamin hanya: <strong>L</strong> (Laki-laki) atau <strong>P</strong> (Perempuan)</li>
                <li>Nama Kelas harus sesuai dengan kelas yang sudah dibuat</li>
                <li>Jika NISN sudah ada, data akan diperbarui</li>
                <li>Tanggal lahir akan diisi dengan tanggal hari ini</li>
                <li>Alamat akan diisi dengan "-" (bisa diupdate manual setelah import)</li>
            </ul>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
            console.log('File: ' + fileName + ' (' + fileSize + 'MB)');
        }
    }
</script>

@endsection
