@extends('tablar::auth.layout')
@section('title', 'Ruang Kembali')
@section('content')
<style>
    body {
        background: url('{{ asset('images/coba-hero.jpeg') }}') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Poppins', sans-serif !important;
        font-weight: 400 !important;
        background-color: #f8f9fc;
    }
    ::placeholder {
    font-family: 'Poppins', sans-serif !important;
    font-weight: 400 !important;
    font-style: italic;
    font-size: 12px;
}
input, select, textarea, button {
    font-family: 'Poppins', sans-serif !important;
    font-weight: 400 !important;
    font-size: 14px !important;
}

/* Label form juga */
label {
    font-weight: 400 !important;
}
    .auth-wrapper {
        min-height: 100vh;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        position: relative;
    }

    .overlay-dark {
        position: absolute;
        inset: 0;
        z-index: 1;
    }

    .logo-left {
        position: absolute;
        left: 10%;
        top: 50%;
        transform: translateY(-30%);
        color: #fff;
        text-align: left;
    }

    .logo-left img {
        width: clamp(180px, 18vw, 260px);
        height: auto;
    }

    .register-panel {

        position: relative;
        z-index: 2;

        width: 100%;
        max-width: 400px;

        margin-right: clamp(30px, 5vw, 80px);

        padding: 32px 30px;

        border-radius: 24px;

        background:
            linear-gradient(
                145deg,
                rgba(255,255,255,0.96),
                rgba(255,255,255,0.90)
            );

        backdrop-filter: blur(18px);

        box-shadow:
            0 8px 30px rgba(0,0,0,0.10),
            0 2px 10px rgba(0,0,0,0.05);

        border: 1px solid rgba(255,255,255,0.35);
    }

    .register-panel h3 {
        font-weight: 800;
        font-size: 24px;
        line-height: 1.2;
        margin-bottom: 1rem;
        color: #111;
        letter-spacing: -0.3px;
    }
    .mb-3 {
        margin-bottom: 10px !important;
    }

    .form-label {

        font-weight: 400;

        margin-bottom: 0.55rem;

        color: #222;
    }
    .form-control {
        border-radius: 12px;
        padding: 10px 10px;
        border: 1px solid #e5e7eb;
        font-size: 14px;
    }
    .form-control:focus {

        border-color: #111;

        box-shadow:
            0 0 0 4px rgba(0,0,0,0.05);
    }
    .btn-register {
        /* background: linear-gradient(180deg,#E7D7AE,#D2B57C); */
        background:#DCCBA8;
        border: none;
        color:#111;
        border-radius:10px;
        padding:12px 20px;
        font-weight:600;
        transition:.3s;
    }

    .btn-register:hover {
        background:#c3bbb0;
        transform:translateY(-2px);
        box-shadow:0 10px 20px rgba(0,0,0,.15);
    }
    .website-header{
        display:none;
    }
    @media (max-width: 1200px) {
        body {
            background-attachment: scroll;
            background-position: center;
        }

        .auth-wrapper {
            align-items: center;
            justify-content: center;
            padding: 120px 24px 40px;
            flex-direction: column;
            gap: 28px;
        }

        .logo-left {
            position: absolute;
            left: 50%;
            top: 7%;
            transform: translateX(-50%);
            text-align: center;
            z-index: 2;
        }

        .logo-left img {
            width: clamp(180px, 28vw, 260px);
            height: auto;
            margin-bottom: 10px;
        }
        .register-panel {
            max-width: 600px;
            margin-right: 0;
            margin-top: 0;
            padding: 35px 30px;
        }
        .register-panel h3 {
            font-size: 24px;
            margin-bottom: 1.3rem;
            text-align: center;
        }
        .website-header{
            display:block;
        }
    }

    /* MOBILE */
    @media (max-width: 576px) {
        .page {
            margin-left: 0 !important;
        }
        body {
            background: url('{{ asset('LOGIN-mobile.jpeg') }}') no-repeat center center fixed;
            background-size: cover;
            background-attachment: scroll;
            background-position: center;
        }

        .auth-wrapper {
            min-height: 100dvh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .overlay-dark {
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        /* tampilkan logo di mobile */
        .logo-left {
            display: flex;
            position: absolute;
            left: 50%;
            top: 24px;
            transform: translateX(-50%, -50%);
            justify-content: center;
            align-items: center;
            z-index: 2;
        }

        .logo-left img {
            width: clamp(280px, 28vw, 460px);
            height: auto;
        }

        .register-panel {
            width: 100%;
            max-width: 360px;
            margin: 0;
            padding: 22px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,0.96);
            backdrop-filter: blur(10px);
        }
        .register-panel h3 {
            font-size: 20px;
            margin-bottom: 1.3rem;
            text-align: center;
        }
        .form-control {
            padding: 0.85rem 1rem;
            font-size: 15px;
        }
        .input-group-text {
            padding-left: 14px;
            padding-right: 14px;
        }
        .btn-register {
            padding: 0.85rem;
            border-radius: 12px;
            font-size: 15px;
        }
        .small {
            font-size: 12px !important;
        }
        .website-header{
            display:block;
        }
        .user-dropdown{
            display:none;
        }
    }
</style>
    <div class="auth-wrapper">
        <div class="overlay-dark"> </div>

        <div class="register-panel">
            <h3>Silahkan buat akun baru</h3>

            <form action="{{route('register')}}" method="post" autocomplete="off" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="fullname" class="form-label">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="ti ti-user"></i>
                        </span>
                        <input type="text" name="fullname"
                            class="form-control border-start-0 @error('fullname') is-invalid @enderror"
                            placeholder="Masukkan nama Anda" autofocus>
                    </div>
                    @error('fullname')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">No HP</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="ti ti-user"></i>
                        </span>
                        <input type="tel" name="phone"
                            class="form-control border-start-0 @error('fullname') is-invalid @enderror"
                            placeholder="Masukkan nomor HP Anda" autofocus>
                    </div>
                    @error('fullname')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="ti ti-mail"></i>
                        </span>
                        <input id="email" type="text" name="email"
                            class="form-control border-start-0 @error('email') is-invalid @enderror"
                            placeholder="Masukkan alamat email Anda" autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Kata kunci</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="ti ti-lock"></i>
                        </span>
                        <input id="password" type="password" name="password"
                            class="form-control border-start-0 @error('password') is-invalid @enderror"
                            placeholder="Masukkan kata kunci">
                        <button type="button" class="btn btn-light border toggle-password">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Kata kunci</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="ti ti-lock"></i>
                        </span>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            class="form-control border-start-0 @error('password_confirmation') is-invalid @enderror"
                            placeholder="Masukkan kata kunci kembali">
                        <button type="button" class="btn btn-light border toggle-password"">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-register text-white w-100">Daftar</button>

                <div class="text-center mt-3 small">
                    Sudah punya akun? <a href="{{ route('login') }}" class="fw-semibold text-dark">Silakan masuk</a>
                </div>

                <div class="text-center text-muted mt-4 small">
                    Ruang Kembali © {{ date('Y') }} Semua Hak Dilindungi
                </div>
            </form>
        </div>
    </div>
    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function () {

                const input = this.closest('.input-group').querySelector('input');

                const type = input.type === 'password' ? 'text' : 'password';

                input.type = type;

                this.innerHTML = type === 'password'
                    ? '<i class="ti ti-eye"></i>'
                    : '<i class="ti ti-eye-off"></i>';
            });
        });
    </script>
@endsection
        
                 

                
                {{-- <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input"/>
                        <span class="form-check-label">Agree the <a href="#" tabindex="-1">terms and policy</a>.</span>
                    </label>
                </div> --}}
                
         

