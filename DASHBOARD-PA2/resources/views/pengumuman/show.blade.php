@extends('layouts.app')

@section('title', 'Detail Pengumuman')

@section('content')
<style>
    /* ===== DESIGN SYSTEM ===== */
    :root {
        --primary-color: #F97316;
        --bg-page: #f9fafb;
        --bg-card: #ffffff;
        --bg-secondary: #f3f4f6;
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --border-light: #e5e7eb;
    }

    /* ===== RESET SWIPER DEFAULT ===== */
    .swiper-container .swiper-button-next,
    .swiper-container .swiper-button-prev,
    .pengumuman-slider .swiper-button-next,
    .pengumuman-slider .swiper-button-prev {
        all: unset;
    }
    
    /* ===== SLIDER CONTAINER ===== */
    .pengumuman-slider-container {
        position: relative;
        width: 100%;
        max-width: 800px;
        margin: 0 auto 32px;
        border-radius: 20px;
        overflow: hidden;
        background: #f3f4f6;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    /* ===== SWIPER CORE ===== */
    .swiper.pengumuman-slider {
        width: 100%;
        height: auto;
        padding-bottom: 0;
        overflow: hidden !important;
    }
    
    .pengumuman-slider .swiper-wrapper {
        display: flex;
    }
    
    .pengumuman-slider .swiper-slide {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 380px;
        background: #f3f4f6;
        flex-shrink: 0;
    }
    
    .pengumuman-slider .swiper-slide img {
        width: auto;
        height: auto;
        max-width: 90%;
        max-height: 380px;
        object-fit: contain;
    }
    
    /* ===== PAGINATION DOTS ===== */
    .pengumuman-slider .swiper-pagination {
        position: absolute !important;
        bottom: 16px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        width: auto !important;
        display: flex !important;
        gap: 6px !important;
        z-index: 20 !important;
    }
    
    .pengumuman-slider .swiper-pagination-bullet {
        width: 8px !important;
        height: 8px !important;
        background: rgba(0, 0, 0, 0.25) !important;
        border-radius: 50% !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        margin: 0 !important;
    }
    
    .pengumuman-slider .swiper-pagination-bullet:hover {
        background: rgba(0, 0, 0, 0.4) !important;
    }
    
    .pengumuman-slider .swiper-pagination-bullet-active {
        background: rgba(0, 0, 0, 0.6) !important;
        width: 8px !important;
        border-radius: 50% !important;
    }
    
    /* ===== NAVIGATION BUTTONS ===== */
    .pengumuman-slider .swiper-button-prev,
    .pengumuman-slider .swiper-button-next {
        position: absolute !important;
        top: 50% !important;
        width: 44px !important;
        height: 44px !important;
        background: rgba(107, 114, 128, 0.7) !important;
        border: none !important;
        border-radius: 8px !important;
        color: white !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 20px !important;
        transition: all 0.3s ease !important;
        z-index: 20 !important;
        transform: translateY(-50%) !important;
        margin-top: 0 !important;
        padding: 0 !important;
        outline: none !important;
    }
    
    .pengumuman-slider .swiper-button-prev::after,
    .pengumuman-slider .swiper-button-next::after {
        content: '' !important;
    }
    
    .pengumuman-slider .swiper-button-prev {
        left: 12px !important;
    }
    
    .pengumuman-slider .swiper-button-next {
        right: 12px !important;
    }
    
    .pengumuman-slider .swiper-button-prev:hover,
    .pengumuman-slider .swiper-button-next:hover {
        background: rgba(75, 85, 99, 0.9) !important;
        transform: translateY(-50%) scale(1.05) !important;
    }
    
    .pengumuman-slider .swiper-button-prev:active,
    .pengumuman-slider .swiper-button-next:active {
        transform: translateY(-50%) scale(0.95) !important;
    }
    
    /* ===== COUNTER ===== */
    .slide-counter {
        position: absolute !important;
        top: 16px !important;
        right: 16px !important;
        background: rgba(0, 0, 0, 0.6) !important;
        color: white !important;
        padding: 8px 14px !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        z-index: 20 !important;
        backdrop-filter: blur(10px) !important;
    }
</style>

<div class="row mb-3">
    <div class="col-md-8">
        <h2><i class="bi bi-megaphone"></i> Detail Pengumuman</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Foto/Media Slider -->
        @if(!empty($mediaUrls) && count($mediaUrls) > 0)
            <div class="pengumuman-slider-container">
                <div class="swiper pengumuman-slider">
                    <div class="swiper-wrapper">
                        @foreach($mediaUrls as $index => $mediaUrl)
                            <div class="swiper-slide">
                                <img src="{{ $mediaUrl }}" alt="Foto Pengumuman {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                    
                    @if(count($mediaUrls) > 1)
                        <!-- Counter -->
                        <div class="slide-counter">
                            <span class="current-slide">1</span> / {{ count($mediaUrls) }}
                        </div>
                        
                        <!-- Navigation -->
                        <button class="swiper-button-prev" type="button" aria-label="Previous slide">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="swiper-button-next" type="button" aria-label="Next slide">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                        
                        <!-- Pagination -->
                        <div class="swiper-pagination"></div>
                    @endif
                </div>
            </div>
        @elseif(!empty($pengumuman->media))
            <div class="pengumuman-slider-container" style="display: flex; align-items: center; justify-content: center;">
                <img src="{{ asset('storage/' . ltrim($pengumuman->media, '/')) }}" alt="Foto Pengumuman" 
                     style="max-width: 90%; max-height: 450px; object-fit: contain;">
            </div>
        @endif

        <!-- Judul -->
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Judul:</strong>
            </div>
            <div class="col-md-9">
                <h5 class="mb-0">{{ $pengumuman->judul }}</h5>
            </div>
        </div>

        <!-- Guru -->
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Guru:</strong>
            </div>
            <div class="col-md-9">
                <span class="badge bg-light text-dark">{{ $pengumuman->guru->nama_guru ?? '-' }}</span>
            </div>
        </div>

        <!-- Waktu Unggah -->
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Waktu Unggah:</strong>
            </div>
            <div class="col-md-9">
                <i class="bi bi-calendar3"></i> {{ $pengumuman->waktu_unggah->isoFormat('dddd, D MMMM YYYY H:mm') }}
            </div>
        </div>

        <!-- Deskripsi -->
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Deskripsi:</strong>
            </div>
            <div class="col-md-9">
                <div class="alert alert-light border">
                    {{ $pengumuman->deskripsi }}
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-2 mt-4">
            <a href="{{ route('pengumuman.edit', $pengumuman->id_pengumuman) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('pengumuman.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mediaCount = document.querySelectorAll('.pengumuman-slider .swiper-slide').length;
    
    if (mediaCount > 1) {
        const swiper = new Swiper('.pengumuman-slider', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                type: 'bullets'
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev'
            },
            speed: 600,
            spaceBetween: 0,
            on: {
                slideChange: function() {
                    const currentSlide = this.realIndex + 1;
                    const counterEl = document.querySelector('.current-slide');
                    if (counterEl) {
                        counterEl.textContent = currentSlide;
                    }
                }
            }
        });
    }
});
</script>

@endsection
