<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">

    <title>{{ $title ?? 'Antosa Architect' }}</title>

    <!-- Fonts & Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ time() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light" style="font-family: 'Poppins', sans-serif;">

    {{-- HEADER MINI --}}
    @auth
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-white shadow-sm" 
         style="position: sticky; top: 0; z-index: 100;">
        
        {{-- Logo --}}
        <div class="d-flex align-items-center">
            <a href="/" class="text-decoration-none d-flex align-items-center">
                <img src="{{ asset('logo.png') }}" alt="Antosa Architect" width="40" class="me-2">
                <span class="fw-semibold text-dark">Antosa Architect</span>
            </a>
        </div>

        {{-- User Info + Logout --}}
        <div class="d-flex align-items-center gap-3">
            {{-- Avatar --}}
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border" 
                 style="width: 40px; height: 40px;">
                @if (auth()->user()->profile_photo_path ?? false)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" 
                         alt="Foto Profil" class="rounded-circle" width="40" height="40">
                @else
                    <i class="bi bi-person fs-4 text-secondary"></i>
                @endif
            </div>

            {{-- Nama dan Role --}}
            
            <div class="text-end">
                @php
                    $roles = auth()->user()->getRoleNames();
                    $mainRole = $roles->contains('Customer') ? 'Customer' : $roles->first();
                @endphp
                <div class="fw-semibold text-dark small mb-0">{{ auth()->user()->fullname }}</div>
                <div class="text-muted small">{{ $mainRole }}</div>
            </div>

            {{-- Tombol Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            </form>
        </div>
    </div>
    @endauth

    {{-- MAIN CONTENT --}}
    <div class="container py-5">
        <div class="text-center mb-4">
            @guest
                <img src="{{ asset('logo.png') }}" alt="Antosa Architect" width="80" class="mb-3">
            @endguest
        </div>

        <div class="d-flex flex-column align-items-center justify-content-center">
            <div class="bg-white shadow-sm rounded-4 p-4 w-100" style="max-width: 900px;">
                {{ $slot }}
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center text-muted small mt-5">
            © {{ date('Y') }} Antosa Architect
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('js')
</body>
</html>
