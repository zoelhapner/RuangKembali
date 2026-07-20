@extends('tablar::page')

@section('title', 'Welcome - Guest Dashboard')

@section('content')
    {{-- 🔹 Banner Slideshow Statis (Bootstrap 5 + Alpine.js) --}}
    <div 
        x-data="{active: 0, slides: [
            { image: '/images/banner3.jpg', title: 'Selamat Datang di Antosa Architect', desc: 'Desain modern dan inovatif untuk setiap proyek Anda.' },
            { image: '/images/banner2.jpg', title: 'Inspirasi Desain Rumah Impian', desc: 'Temukan ide arsitektur terbaik bersama tim profesional kami.' },
            { image: '/images/banner1.jpg', title: 'Bangun Bersama Kami', desc: 'Kami hadir untuk mewujudkan hunian yang nyaman dan elegan.' }
        ]}"
        x-init="setInterval(() => active = (active + 1) % slides.length, 5000)"
        class="position-relative overflow-hidden rounded-4 shadow mb-4"
        style="height: 280px;"
    >
        {{-- Slide Item --}}
        <template x-for="(slide, index) in slides" :key='index'>
            <div 
                class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center text-center text-white"
                x-show="active === index" 
                x-transition.opacity
                :style="`background: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)), url(${slide.image}) center/cover no-repeat;`"
            >
                <div class="p-4">
                    <h2 class="fw-bold fs-1 mb-2" x-text="slide.title"></h2>
                    <p class="mb-0 fs-3" x-text="slide.desc"></p>
                </div>
            </div>
        </template>

        {{-- Tombol navigasi kiri/kanan --}}
        <button 
            @click="active = (active - 1 + slides.length) % slides.length"
            class="btn btn-light position-absolute top-50 start-0 translate-middle-y ms-3 rounded-circle shadow-sm"
            style="width: 40px; height: 40px; opacity: 0.7;">
            <i class="ti ti-chevron-left"></i>
        </button>
        <button 
            @click="active = (active + 1) % slides.length"
            class="btn btn-light position-absolute top-50 end-0 translate-middle-y me-3 rounded-circle shadow-sm"
            style="width: 40px; height: 40px; opacity: 0.7;">
            <i class="ti ti-chevron-right"></i>
        </button>

        {{-- Indicator bulat --}}
        <div class="position-absolute bottom-3 start-50 translate-middle-x d-flex gap-2 mb-2">
            <template x-for="(slide, i) in slides" :key="i">
                <div 
                    class="rounded-circle"
                    :class="active === i ? 'bg-white' : 'bg-secondary opacity-50'"
                    style="width: 10px; height: 10px;">
                </div>
            </template>
        </div>
    </div>

    {{-- 🔹 Konten Dashboard kamu --}}
    {{-- <div class="bg-dark text-white p-4 rounded mb-4">
        <h2 class="mb-1">Lebih Dekat Dengan Kami</h2>
        <p class="mb-0">Temukan Tips dan Inspirasi yang menarik</p>
        <p class="mb-0">Seputar Desain Konstruksi di akun @antosa_architect</p>
        <small class="d-block mt-2">Terakhir login: Hari ini, {{ now()->format('H:i') }}</small>
    </div> --}}

    <div class="container py-5 text-center">
        <h2 class="fw-bold mb-3">Selamat Datang {{ auth()->user()->fullname ?? 'Admin Utama' }} di Sistem Antosa Architect</h2>
        <p class="text-muted mb-5">
            Anda login sebagai <strong>Guest</strong>.  
            Silakan pilih peran awal Anda untuk melanjutkan ke sistem.
        </p>

        <div class="row justify-content-center g-4">
            {{-- CARD 1: CUSTOMER --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body py-5">
                        <div class="mb-3">
                            <i class="bi bi-person-badge display-4 text-primary"></i>
                        </div>
                        <h5 class="fw-bold">Customer</h5>
                        <p class="text-muted small px-3">
                            Akses fitur pemesanan, pengajuan proyek, dan pelacakan status pekerjaan Anda.
                        </p>
                        <a href="{{ route('guest.activateRole', 'customer') }}" class="btn btn-primary px-4 mt-3">
                            Pilih Peran Ini
                        </a>
                    </div>
                </div>
            </div>

            {{-- CARD 2: AFFILIATOR --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body py-5">
                        <div class="mb-3">
                            <i class="bi bi-people display-4 text-success"></i>
                        </div>
                        <h5 class="fw-bold">Affiliator</h5>
                        <p class="text-muted small px-3">
                            Dapatkan komisi dengan mempromosikan layanan Antosa Architect kepada calon pelanggan.
                        </p>
                        <a href="{{ route('guest.activateRole', 'affiliator') }}" class="btn btn-success px-4 mt-3">
                            Pilih Peran Ini
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panduan --}}
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-light fw-bold">Panduan</div>
                    <div class="card-body text-start">
                        <ol>
                            <li>Pilih salah satu peran di atas sesuai kebutuhan Anda.</li>
                            <li>Lengkapi profil sesuai peran yang dipilih.</li>
                            <li>Tim kami akan melakukan verifikasi akun Anda.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


