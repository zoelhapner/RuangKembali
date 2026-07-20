{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}

@extends('tablar::page')

@section('content')
<div class="container-fluid mt-3">
    <div class="row">
        <!-- Form Update Profil -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h3 class="card-title mb-0">
                        <i class="ti ti-user me-2"></i> Informasi Profil
                    </h3>
                </div>
                <div class="card-body">
                    {{-- <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form> --}}

                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <!-- Nama -->
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Nama</label>
                            <input id="fullname" name="fullname" type="text"
                                class="form-control @error('fullname') is-invalid @enderror"
                                value="{{ old('fullname', $user->fullname) }}" required autofocus autocomplete="fullname">
                            @error('fullname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" required autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Saat Ini -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                            <div class="input-group">
                                <input id="current_password" name="current_password" type="password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    placeholder="Masukkan kata sandi lama" autocomplete="current-password">
                                <span class="input-group-text">
                                    <a href="#" class="toggle-password link-secondary"
                                       data-toggle="tooltip" data-target="current_password"
                                       title="Show password" aria-label="Show password">
                                        <svg class="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="2"/>
                                            <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7
                                                     c2.667-4.667 6-7 10-7s7.333 2.333 10 7"/>
                                        </svg>
                                        <svg class="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" style="display:none;"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 3l18 18"/>
                                        </svg>
                                    </a>
                                </span>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Baru -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi Baru</label>
                            <div class="input-group">
                                <input id="password" name="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Masukkan kata sandi baru" autocomplete="new-password">
                                <span class="input-group-text">
                                    <a href="#" class="toggle-password link-secondary"
                                       data-toggle="tooltip" data-target="password"
                                       title="Show password" aria-label="Show password">
                                        <svg class="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="2"/>
                                            <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7
                                                     c2.667-4.667 6-7 10-7s7.333 2.333 10 7"/>
                                        </svg>
                                        <svg class="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" style="display:none;"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 3l18 18"/>
                                        </svg>
                                    </a>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                            <div class="input-group">
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    placeholder="Ulangi kata sandi baru" autocomplete="new-password">
                                <span class="input-group-text">
                                    <a href="#" class="toggle-password link-secondary"
                                       data-toggle="tooltip" data-target="password_confirmation"
                                       title="Show password" aria-label="Show password">
                                        <svg class="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="2"/>
                                            <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7
                                                     c2.667-4.667 6-7 10-7s7.333 2.333 10 7"/>
                                        </svg>
                                        <svg class="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" style="display:none;"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 3l18 18"/>
                                        </svg>
                                    </a>
                                </span>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-dark text-white">Simpan</button>

                        @if (session('status') === 'profile-updated')
                            <div class="alert alert-success mt-3">
                                <i class="ti ti-check"></i> Profil berhasil diperbarui!
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
    $(function () {
        // Tooltip init (Bootstrap 4 pakai data-toggle, bukan data-bs-toggle)
        $('[data-toggle="tooltip"]').tooltip();

        // Show/hide password toggle
        $('.toggle-password').on('click', function (e) {
            e.preventDefault();

            const targetId = $(this).data('target');   // ambil ID input
            const $input = $('#' + targetId);

            const isPassword = $input.attr('type') === 'password';
            $input.attr('type', isPassword ? 'text' : 'password');

            // toggle icon
            $(this).find('.eyeIcon').toggle(!isPassword);
            $(this).find('.eyeOffIcon').toggle(isPassword);

            // update tooltip title
            const title = isPassword ? 'Hide password' : 'Show password';
            $(this)
                .attr('title', title)
                .tooltip('dispose') // reset tooltip lama
                .tooltip();         // aktifkan tooltip baru
        });
    });
</script>
@endpush

