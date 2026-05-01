@extends('layouts.app')

@section('title', 'Buat Pengumuman')

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

    .form-label .badge {
        margin-left: 0.5rem;
        font-size: 0.75rem;
        padding: 0.35rem 0.6rem;
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

    .media-upload-area {
        position: relative;
        border: 1px dashed var(--neutral-300);
        border-radius: 0.5rem;
        padding: 2.5rem;
        text-align: center;
        background: var(--neutral-50);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .media-upload-area:hover {
        border-color: var(--primary-color);
        background: white;
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
        color: var(--neutral-500);
        font-weight: 500;
        font-size: 0.95rem;
    }

    .media-preview {
        margin-top: 1rem;
        padding: 1rem;
        border: 1px solid var(--neutral-200);
        border-radius: 0.5rem;
        background: var(--neutral-50);
        text-align: center;
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

    .btn-small {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
</style>

<div class="page-wrapper">
    <div class="container-lg">
        <div class="premium-header">
            <h1><i class="bi bi-megaphone-fill"></i> Buat Pengumuman Baru</h1>
            <p class="breadcrumb-text">Buat dan publikasikan pengumuman untuk para orangtua/wali siswa</p>
        </div>

        <!-- MAIN FORM -->
        <form action="{{ route('pengumuman.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

                    <!-- Bagian 1: Konten Pengumuman -->
                    <div class="form-section">
                        <div class="section-title">
                            <div class="section-title-icon"><i class="bi bi-pencil-fill"></i></div>
                            Konten Pengumuman
                        </div>

                        <div class="form-group">
                            <label for="judul" class="form-label">Judul Pengumuman <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                                   id="judul" name="judul" value="{{ old('judul') }}" placeholder="Masukkan judul pengumuman yang menarik" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="deskripsi" class="form-label">Deskripsi / Isi Pengumuman <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" name="deskripsi" placeholder="Tuliskan isi pengumuman dengan detail..." required>{{ old('deskripsi') }}</textarea>
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

                        <div class="form-group">
                            <label for="media" class="form-label">Foto / Gambar</label>
                            <div class="media-upload-area">
                                <input type="file" class="form-control @error('media') is-invalid @enderror" 
                                       id="media" name="media" accept="image/*" onchange="previewMedia(this)">
                            </div>
                            <span class="form-text"><i class="bi bi-info-circle"></i> Format: JPEG, PNG, JPG, GIF | Ukuran maksimal: 10MB</span>

                            <!-- Preview Media -->
                            <div id="media-preview" class="media-preview" style="display: none;">
                                <img id="preview-img" src="" alt="Preview">
                                <div style="margin-top: 1rem;">
                                    <button type="button" class="btn-premium btn-small" style="background: #EF4444; color: white;" onclick="removeMedia()">
                                        <i class="bi bi-trash"></i> Hapus Foto
                                    </button>
                                </div>
                            </div>

                            @error('media')
                                <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Hidden field untuk waktu_unggah -->
                    <input type="hidden" id="waktu_unggah" name="waktu_unggah" value="">

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('pengumuman.index') }}" class="btn-premium btn-cancel">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
                <button type="submit" class="btn-premium btn-save">
                    <i class="bi bi-check-circle"></i> Publikasikan Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Set current datetime on page load
window.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const date = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    
    const currentDateTime = `${year}-${month}-${date}T${hours}:${minutes}`;
    document.getElementById('waktu_unggah').value = currentDateTime;
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
