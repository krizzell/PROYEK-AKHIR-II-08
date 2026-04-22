@extends('layouts.app')

@section('title', 'Buat Pengumuman')

@section('content')
<div class="row">
    <div class="col-md-10">
        <h2><i class="bi bi-megaphone"></i> Buat Pengumuman Baru</h2>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <form action="{{ route('pengumuman.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="judul" class="form-label">Judul <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                       id="judul" name="judul" value="{{ old('judul') }}" placeholder="Masukkan judul pengumuman" required>
                @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="media" class="form-label">
                    <i class="bi bi-image"></i> Foto/Media <span class="badge bg-info">Opsional</span>
                </label>
                <div class="media-upload-area">
                    <input type="file" class="form-control @error('media') is-invalid @enderror" 
                           id="media" name="media" accept="image/*" onchange="previewMedia(this)">
                    <small class="form-text text-muted d-block">Format: JPEG, PNG, JPG, GIF | Ukuran maksimal: 10MB</small>
                </div>
                
                <!-- Preview Media -->
                <div id="media-preview" class="mt-3" style="display: none;">
                    <img id="preview-img" src="" alt="Preview" class="rounded" style="max-width: 100%; max-height: 400px; object-fit: contain; display: block; margin: 0 auto;">
                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeMedia()">
                        <i class="bi bi-trash"></i> Hapus Foto
                    </button>
                </div>

                @error('media')
                    <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Hidden field untuk waktu_unggah - auto-populated dengan current time -->
            <input type="hidden" id="waktu_unggah" name="waktu_unggah" value="">

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                          id="deskripsi" name="deskripsi" rows="5" placeholder="Tuliskan isi pengumuman..." required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Simpan Pengumuman
                </button>
                <a href="{{ route('pengumuman.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    .media-upload-area {
        position: relative;
        border: 2px dashed #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        background-color: #fafbfc;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .media-upload-area:hover {
        border-color: #FF6B1A;
        background-color: #fffbf5;
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
        content: '📸 Drag & drop foto di sini atau klik untuk memilih';
        display: block;
        color: #9CA3AF;
        font-weight: 500;
    }
</style>

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
