@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<style>
    :root {
        --primary-color: #F97316;
        --primary-light: #FFEDE3;
        --primary-dark: #EA580C;
        --success-color: #10B981;
        --danger-color: #EF4444;
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

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    .form-text {
        font-size: 0.85rem;
        color: var(--neutral-600);
        margin-top: 0.5rem;
        display: block;
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

    .current-media-box {
        padding: 1.5rem;
        border: 1px solid var(--neutral-200);
        border-radius: 0.875rem;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.02) 0%, rgba(34, 197, 94, 0.02) 100%);
        margin-bottom: 1.5rem;
    }

    .current-media-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--neutral-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
        display: block;
    }

    .current-media-img {
        max-width: 100%;
        max-height: 300px;
        object-fit: contain;
        border-radius: 0.5rem;
        display: block;
        margin: 0 auto;
    }

    .media-upload-area {
        position: relative;
        border: 2px dashed var(--neutral-300);
        border-radius: 0.875rem;
        padding: 2.5rem;
        text-align: center;
        background: linear-gradient(135deg, rgba(0, 102, 255, 0.02) 0%, rgba(16, 185, 129, 0.02) 100%);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .media-upload-area:hover {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, rgba(0, 102, 255, 0.08) 0%, rgba(16, 185, 129, 0.08) 100%);
    }

    .media-upload-area input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .media-upload-area::before {
        content: '🖼️ Drag & drop foto di sini atau klik untuk memilih';
        display: block;
        color: var(--neutral-600);
        font-weight: 500;
        font-size: 1rem;
    }

    .media-preview {
        margin-top: 1.5rem;
        padding: 1.5rem;
        border: 1px solid var(--neutral-200);
        border-radius: 0.875rem;
        background: var(--neutral-100);
        text-align: center;
    }

    .media-preview-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--neutral-600);
        margin-bottom: 1rem;
        display: block;
    }

    .media-preview img {
        max-width: 100%;
        max-height: 400px;
        object-fit: contain;
        border-radius: 0.5rem;
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

    .btn-small {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
</style>

<div class="page-wrapper">
    <div style="max-width: 1000px; margin: 0 auto; padding: 0 1.5rem;">
        <div class="premium-card">
            <div class="premium-card-body">
                <div class="premium-header">
                    <h1><i class="bi bi-pencil-square"></i> Edit Pengumuman</h1>
                    <p class="breadcrumb-text">Perbarui informasi pengumuman pada form di bawah</p>
                </div>
                <form action="{{ route('pengumuman.update', $pengumuman->id_pengumuman) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Bagian 1: Konten Pengumuman -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-pencil-fill"></i></div>
                            Konten Pengumuman
                        </div>

                        <div class="form-group">
                            <label for="judul" class="form-label">Judul Pengumuman <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                                   id="judul" name="judul" value="{{ old('judul', $pengumuman->judul) }}" required placeholder="Masukkan judul pengumuman">
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="deskripsi" class="form-label">Deskripsi / Isi Pengumuman <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" name="deskripsi" required placeholder="Tuliskan isi pengumuman dengan detail...">{{ old('deskripsi', $pengumuman->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Bagian 2: Media -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-image-fill"></i></div>
                            Media Pengumuman <span style="font-size: 0.85rem; color: #6B7280; font-weight: 500; margin-left: 0.5rem;">Opsional</span>
                        </div>

                        <!-- Current Media Display -->
                        @if($pengumuman->media)
                            <div class="current-media-box">
                                <span class="current-media-label">✓ Foto Pengumuman Saat Ini</span>
                                <img src="{{ asset('storage/' . $pengumuman->media) }}" alt="Current Media" class="current-media-img">
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="media" class="form-label">Ganti Foto / Gambar</label>
                            <div class="media-upload-area">
                                <input type="file" class="form-control @error('media') is-invalid @enderror" 
                                       id="media" name="media" accept="image/*" onchange="previewMedia(this)">
                            </div>
                            <span class="form-text"><i class="bi bi-info-circle"></i> Format: JPEG, PNG, JPG, GIF | Ukuran maksimal: 10MB | Biarkan kosong jika tidak ingin mengganti</span>

                            <!-- Preview Media -->
                            <div id="media-preview" class="media-preview" style="display: none;">
                                <span class="media-preview-label">🖼️ Preview Foto Baru</span>
                                <img id="preview-img" src="" alt="Preview">
                                <div style="margin-top: 1rem;">
                                    <button type="button" class="btn-premium btn-small" style="background: #EF4444; color: white;" onclick="removeMedia()">
                                        <i class="bi bi-trash"></i> Batal Upload
                                    </button>
                                </div>
                            </div>

                            @error('media')
                                <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Hidden field untuk waktu_unggah -->
                    <input type="hidden" id="waktu_unggah" name="waktu_unggah" value="{{ old('waktu_unggah', $pengumuman->waktu_unggah->format('Y-m-d\TH:i')) }}">

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn-premium btn-save">
                            <i class="bi bi-check-circle-fill"></i> Perbarui Pengumuman
                        </button>
                        <a href="{{ route('pengumuman.index') }}" class="btn-premium btn-cancel">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Set current datetime on page load
window.addEventListener('DOMContentLoaded', function() {
    const waktuInput = document.getElementById('waktu_unggah');
    if (!waktuInput.value) {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const date = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        const currentDateTime = `${year}-${month}-${date}T${hours}:${minutes}`;
        waktuInput.value = currentDateTime;
    }
});

function previewMedia(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('media-preview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeMedia() {
    document.getElementById('media').value = '';
    document.getElementById('media-preview').style.display = 'none';
}
</script>

@endsection
