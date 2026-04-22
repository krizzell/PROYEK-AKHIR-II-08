@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-key"></i> Ubah Password</h2>
        <p class="text-muted">Perbarui password akun Anda</p>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <strong>Terjadi Kesalahan!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('profile.update-password') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="password_lama" class="form-label">Password Lama <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password_lama') is-invalid @enderror" 
                           id="password_lama" name="password_lama" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_lama')">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password_lama')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Masukkan password Anda saat ini</small>
            </div>

            <hr>

            <div class="mb-3">
                <label for="password_baru" class="form-label">Password Baru <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password_baru') is-invalid @enderror" 
                           id="password_baru" name="password_baru" required
                           onkeyup="checkPasswordMatch()" placeholder="Minimal 8 karakter">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_baru')">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div id="password-length-feedback" style="margin-top: 0.5rem;">
                    <small id="length-text" style="color: #6c757d;"></small>
                </div>
                @error('password_baru')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_baru_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password_baru_confirmation') is-invalid @enderror" 
                           id="password_baru_confirmation" name="password_baru_confirmation" required
                           onkeyup="checkPasswordMatch()" placeholder="Ulangi password baru Anda">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_baru_confirmation')">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div id="password-match-feedback" style="display:none; margin-top: 0.5rem;">
                    <small id="match-text" style="font-weight: bold;"></small>
                </div>
                @error('password_baru_confirmation')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i> 
                <strong>Tips Keamanan:</strong>
                <ul class="mb-0 mt-2">
                    <li>Gunakan kombinasi huruf besar, kecil, dan angka</li>
                    <li>Jangan gunakan password yang mudah ditebak</li>
                    <li>Jangan bagikan password kepada siapapun</li>
                </ul>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" id="submit-btn" class="btn btn-primary" disabled>
                    <i class="bi bi-check-circle"></i> Ubah Password
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.type === 'password' ? 'text' : 'password';
    field.type = type;
}

function checkPasswordMatch() {
    const passwordBaru = document.getElementById('password_baru').value;
    const passwordConfirm = document.getElementById('password_baru_confirmation').value;
    const feedback = document.getElementById('password-match-feedback');
    const matchText = document.getElementById('match-text');
    const lengthFeedback = document.getElementById('password-length-feedback');
    const lengthText = document.getElementById('length-text');
    const submitBtn = document.getElementById('submit-btn');
    
    // Check length
    if (passwordBaru.length < 8 && passwordBaru.length > 0) {
        lengthText.innerHTML = 'Minimal 8 karakter (sekarang ' + passwordBaru.length + ')';
        lengthText.style.color = '#dc3545';
    } else if (passwordBaru.length >= 8) {
        lengthText.innerHTML = 'Oke';
        lengthText.style.color = '#28a745';
    } else {
        lengthText.innerHTML = '';
    }
    
    // Check match
    if (passwordConfirm === '') {
        feedback.style.display = 'none';
        submitBtn.disabled = true;
        return;
    }
    
    feedback.style.display = 'block';
    
    if (passwordBaru === passwordConfirm && passwordBaru.length >= 8) {
        matchText.innerHTML = 'Cocok! Siap disimpan';
        matchText.style.color = '#28a745';
        feedback.style.color = '#28a745';
        submitBtn.disabled = false;
    } else if (passwordBaru === passwordConfirm) {
        matchText.innerHTML = 'Cocok, tapi kurang 8 karakter';
        matchText.style.color = '#ffc107';
        feedback.style.color = '#ffc107';
        submitBtn.disabled = true;
    } else {
        matchText.innerHTML = 'Tidak cocok';
        matchText.style.color = '#dc3545';
        feedback.style.color = '#dc3545';
        submitBtn.disabled = true;
    }
}
</script>

<style>
    .input-group .btn {
        border-color: #dee2e6;
    }

    .input-group .btn:hover {
        background-color: #FFF8F4;
    }

    .form-text {
        display: block;
        margin-top: 0.25rem;
    }
</style>
@endsection
