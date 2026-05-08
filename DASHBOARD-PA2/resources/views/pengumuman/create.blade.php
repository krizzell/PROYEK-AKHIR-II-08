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

    .container-lg::before {
        content: '';
        position: absolute;
        inset: 0 0 auto 0;
        height: 6px;
        border-radius: 16px 16px 0 0;
        background: linear-gradient(90deg, var(--primary-color), #fb923c 55%, #f59e0b);
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
        border: 1.5px dashed #FDBA74;
        border-radius: 1rem;
        padding: 1.25rem;
        background: linear-gradient(180deg, #FFF7ED 0%, #FFFFFF 100%);
        transition: all 0.2s ease;
        overflow: hidden;
    }

    .media-upload-area:hover {
        border-color: var(--primary-color);
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.08);
    }

    .media-upload-area.is-dragover {
        border-color: var(--primary-color);
        background: linear-gradient(180deg, #FFEDD5 0%, #FFFFFF 100%);
        box-shadow: 0 14px 30px rgba(249, 115, 22, 0.14);
    }

    .hidden-file-input {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .dropzone-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        cursor: pointer;
    }

    .dropzone-icon {
        width: 60px;
        height: 60px;
        border-radius: 1rem;
        background: var(--primary-light);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        font-size: 1.5rem;
    }

    .dropzone-copy {
        flex: 1;
    }

    .dropzone-copy strong {
        display: block;
        font-size: 1rem;
        color: var(--neutral-900);
        margin-bottom: 0.25rem;
    }

    .dropzone-copy span {
        display: block;
        color: var(--neutral-600);
        font-size: 0.88rem;
        line-height: 1.5;
    }

    .dropzone-actions {
        margin-top: 1rem;
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .upload-trigger-btn {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border: none;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .upload-trigger-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.18);
    }

    .upload-hint {
        font-size: 0.85rem;
        color: var(--neutral-600);
    }

    .selected-media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .selected-media-card {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        border: 1px solid var(--neutral-200);
        background: white;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }

    .selected-media-card img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        display: block;
    }

    .selected-media-card-body {
        padding: 0.9rem;
    }

    .selected-media-card-body strong {
        display: block;
        color: var(--neutral-900);
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
        word-break: break-word;
    }

    .selected-media-card-body span {
        display: block;
        color: var(--neutral-600);
        font-size: 0.8rem;
    }

    .selected-media-remove {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        width: 2rem;
        height: 2rem;
        border: none;
        border-radius: 999px;
        background: rgba(239, 68, 68, 0.95);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 18px rgba(239, 68, 68, 0.22);
        cursor: pointer;
    }

    .selected-media-empty {
        margin-top: 1rem;
        padding: 1rem 1.25rem;
        border: 1px dashed var(--neutral-300);
        border-radius: 1rem;
        background: var(--neutral-50);
        color: var(--neutral-600);
        font-size: 0.9rem;
    }

    .media-error-list {
        margin-top: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: 0.75rem;
        background: rgba(239, 68, 68, 0.06);
        border: 1px solid rgba(239, 68, 68, 0.16);
        color: #B91C1C;
        font-size: 0.88rem;
    }

    .media-error-list ul {
        margin: 0;
        padding-left: 1.1rem;
    }

    .media-error-list li + li {
        margin-top: 0.25rem;
    }

    .submit-loading {
        opacity: 0.85;
        pointer-events: none;
    }

    .submit-spinner {
        width: 0.95rem;
        height: 0.95rem;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: white;
        border-radius: 999px;
        display: inline-block;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
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
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        pointer-events: none;
    }

    .upload-panel {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .upload-icon {
        width: 56px;
        height: 56px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-light);
        color: var(--primary-color);
        flex: 0 0 auto;
        font-size: 1.4rem;
    }

    .upload-copy {
        text-align: left;
    }

    .upload-copy strong {
        display: block;
        font-size: 1rem;
        color: var(--neutral-900);
        margin-bottom: 0.25rem;
    }

    .upload-copy span {
        display: block;
        color: var(--neutral-600);
        font-size: 0.88rem;
        line-height: 1.5;
    }

    .upload-meta {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 0.75rem;
        font-size: 0.82rem;
        color: var(--neutral-600);
        flex-wrap: wrap;
    }

    .upload-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .media-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .media-preview-card {
        border: 1px solid var(--neutral-200);
        border-radius: 1rem;
        overflow: hidden;
        background: white;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }

    .media-preview-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    .media-preview-card-body {
        padding: 0.75rem;
    }

    .media-preview-card-body strong {
        display: block;
        font-size: 0.88rem;
        color: var(--neutral-900);
        margin-bottom: 0.25rem;
        word-break: break-word;
    }

    .media-preview-card-body span {
        display: block;
        color: var(--neutral-600);
        font-size: 0.8rem;
    }

    .media-empty-state {
        margin-top: 1rem;
        border: 1px dashed var(--neutral-300);
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        color: var(--neutral-600);
        background: var(--neutral-50);
        font-size: 0.9rem;
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
        <form id="pengumuman-form" action="{{ route('pengumuman.store') }}" method="POST" enctype="multipart/form-data">
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
                            <label class="form-label">Foto / Gambar</label>
                            <div class="media-upload-area" id="media-dropzone">
                                <input type="file"
                                       class="hidden-file-input @error('media') is-invalid @enderror"
                                       id="media"
                                       name="media[]"
                                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                       multiple>
                                <div class="dropzone-card" role="button" tabindex="0" aria-label="Pilih gambar pengumuman">
                                    <div class="dropzone-icon"><i class="bi bi-cloud-arrow-up-fill"></i></div>
                                    <div class="dropzone-copy">
                                        <strong>Upload beberapa foto sekaligus</strong>
                                        <span>Seret file ke area ini atau klik tombol pilih gambar. JPG, JPEG, PNG, dan WEBP diperbolehkan, maksimal 5 MB per file.</span>
                                        <div class="dropzone-actions">
                                            <button type="button" class="upload-trigger-btn" id="choose-media-btn">
                                                <i class="bi bi-images"></i> Pilih Gambar
                                            </button>
                                            <span class="upload-hint">Preview muncul otomatis sebelum disimpan.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="upload-meta">
                                <span><i class="bi bi-info-circle"></i> Format: JPG, JPEG, PNG, WEBP</span>
                                <span><i class="bi bi-stack"></i> Bisa pilih banyak foto sekaligus</span>
                            </div>

                            <div id="media-error-wrap" class="media-error-list" style="display: none;"></div>
                            <div id="media-error-data" data-errors='@json($errors->getMessages())' hidden></div>

                            <div id="media-empty" class="selected-media-empty">
                                Belum ada gambar dipilih. Gunakan tombol di atas untuk menambahkan foto pengumuman.
                            </div>

                            <div id="media-preview" class="selected-media-grid" style="display: none;"></div>

                            <div style="margin-top: 1rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                <button type="button" class="btn-premium btn-small" style="background: #EF4444; color: white;" id="clear-media-btn">
                                    <i class="bi bi-trash"></i> Hapus Semua Pilihan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden field untuk waktu_unggah -->
                    <input type="hidden" id="waktu_unggah" name="waktu_unggah" value="">

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('pengumuman.index') }}" class="btn-premium btn-cancel">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
                <button type="submit" class="btn-premium btn-save" id="submit-btn">
                    <i class="bi bi-check-circle"></i> Publikasikan Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('pengumuman-form');
    const mediaInput = document.getElementById('media');
    const dropzone = document.getElementById('media-dropzone');
    const chooseButton = document.getElementById('choose-media-btn');
    const clearButton = document.getElementById('clear-media-btn');
    const previewContainer = document.getElementById('media-preview');
    const emptyState = document.getElementById('media-empty');
    const errorWrap = document.getElementById('media-error-wrap');
    const submitButton = document.getElementById('submit-btn');
    const originalButtonHtml = submitButton.innerHTML;

    let selectedFiles = [];

    function fileKey(file) {
        return [file.name, file.size, file.lastModified].join('__');
    }

    function syncInputFiles() {
        const transfer = new window.DataTransfer();
        selectedFiles.forEach(function(file) {
            transfer.items.add(file);
        });
        mediaInput.files = transfer.files;
    }

    function renderErrors() {
        const mediaErrors = [];
        const errorDataElement = document.getElementById('media-error-data');
        const messages = errorDataElement && errorDataElement.dataset.errors
            ? JSON.parse(errorDataElement.dataset.errors)
            : {};

        Object.keys(messages).forEach(function(key) {
            if (key === 'media' || key.indexOf('media.') === 0) {
                messages[key].forEach(function(message) {
                    mediaErrors.push(message);
                });
            }
        });

        if (!mediaErrors.length) {
            errorWrap.style.display = 'none';
            errorWrap.innerHTML = '';
            return;
        }

        errorWrap.style.display = 'block';
        errorWrap.innerHTML = '<ul>' + mediaErrors.map(function(message) {
            return '<li>' + message + '</li>';
        }).join('') + '</ul>';
    }

    function renderPreview() {
        previewContainer.innerHTML = '';

        if (!selectedFiles.length) {
            previewContainer.style.display = 'none';
            emptyState.style.display = 'block';
            clearButton.disabled = true;
            return;
        }

        emptyState.style.display = 'none';
        previewContainer.style.display = 'grid';
        clearButton.disabled = false;

        selectedFiles.forEach(function(file, index) {
            const reader = new window.FileReader();

            reader.onload = function(event) {
                const card = document.createElement('div');
                card.className = 'selected-media-card';
                card.innerHTML = [
                    '<button type="button" class="selected-media-remove" aria-label="Hapus ' + file.name.replace(/"/g, '&quot;') + '" data-remove-index="' + index + '"><i class="bi bi-x-lg"></i></button>',
                    '<img src="' + event.target.result + '" alt="Preview ' + file.name.replace(/"/g, '&quot;') + '">',
                    '<div class="selected-media-card-body">',
                    '<strong>' + file.name + '</strong>',
                    '<span>' + Math.max(1, Math.round(file.size / 1024)) + ' KB</span>',
                    '</div>'
                ].join('');

                card.querySelector('.selected-media-remove').addEventListener('click', function() {
                    selectedFiles.splice(index, 1);
                    syncInputFiles();
                    renderPreview();
                });

                previewContainer.appendChild(card);
            };

            reader.readAsDataURL(file);
        });
    }

    function addFiles(fileList) {
        const incomingFiles = Array.from(fileList || []);
        if (!incomingFiles.length) {
            return;
        }

        const existingKeys = new window.Set(selectedFiles.map(fileKey));

        incomingFiles.forEach(function(file) {
            const key = fileKey(file);
            if (!existingKeys.has(key)) {
                selectedFiles.push(file);
                existingKeys.add(key);
            }
        });

        syncInputFiles();
        renderPreview();
    }

    function openPicker() {
        mediaInput.click();
    }

    chooseButton.addEventListener('click', openPicker);
    dropzone.addEventListener('click', function(event) {
        if (event.target === chooseButton) {
            return;
        }

        openPicker();
    });
    dropzone.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            openPicker();
        }
    });
    dropzone.addEventListener('dragover', function(event) {
        event.preventDefault();
        dropzone.classList.add('is-dragover');
    });
    dropzone.addEventListener('dragleave', function() {
        dropzone.classList.remove('is-dragover');
    });
    dropzone.addEventListener('drop', function(event) {
        event.preventDefault();
        dropzone.classList.remove('is-dragover');
        addFiles(event.dataTransfer.files);
    });

    mediaInput.addEventListener('change', function(event) {
        addFiles(event.target.files);
    });

    clearButton.addEventListener('click', function() {
        selectedFiles = [];
        syncInputFiles();
        mediaInput.value = '';
        renderPreview();
    });

    form.addEventListener('submit', function() {
        submitButton.disabled = true;
        submitButton.classList.add('submit-loading');
        submitButton.innerHTML = '<span class="submit-spinner" aria-hidden="true"></span><span>Menyimpan...</span>';
    });

    renderErrors();
    renderPreview();
});
// Set current datetime on page load
window.addEventListener('DOMContentLoaded', function() {
    const now = new window.Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const date = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    
    const currentDateTime = `${year}-${month}-${date}T${hours}:${minutes}`;
    document.getElementById('waktu_unggah').value = currentDateTime;
});
</script>

@endsection
