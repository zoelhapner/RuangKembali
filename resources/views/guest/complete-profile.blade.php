@extends('tablar::page')

@section('content')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card border-0 shadow rounded-4 overflow-hidden">
                    
                    {{-- Foto Profil di Atas --}}
                    <div class="text-center bg-light py-4 position-relative">
                        <div class="position-relative d-inline-block">
                            <img src="{{ auth()->user()->photo_url ?? asset('images/default-avatar.png') }}"
                                 class="rounded-circle border border-3 border-white shadow-sm"
                                 alt="Foto Profil"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                            <label for="photo"
                                   class="btn btn-sm btn-primary position-absolute bottom-0 end-0 translate-middle rounded-circle"
                                   title="Ganti Foto">
                                <i class="ti ti-camera-fill"></i>
                            </label>
                        </div>
                        <input type="file" name="photo" id="photo" class="d-none" accept="image/*">
                        <p class="small text-muted mt-2 mb-0">Format: JPG/PNG, maks. 2MB</p>
                    </div>

                    <div class="card-body px-4 pb-4">
                        <form action="{{ route('guest.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Informasi Pribadi --}}
                            <section class="pb-3 border-bottom mb-4">
                                <h6 class="fw-bold text-primary mb-3">Informasi Pribadi</h6>
                                <div class="row g-3">
                                    <div class="col-sm-auto flex-grow-1">
                                        <label for="fullname" class="form-label">Nama Lengkap</label>
                                        <input type="text" name="fullname" id="fullname" class="form-control"
                                               value="{{ old('fullname', auth()->user()->fullname) }}" required>
                                    </div>
                                    <div class="col-sm-auto flex-grow-1">
                                        <label for="nickname" class="form-label">Nama Panggilan</label>
                                        <input type="text" name="nickname" id="nickname" class="form-control"
                                               value="{{ old('nickname') }}">
                                    </div>
                                    <div class="col-sm-auto flex-grow-1">
                                        <label for="birth_place" class="form-label">Tempat Lahir</label>
                                        <input type="text" name="birth_place" id="birth_place" class="form-control"
                                               value="{{ old('birth_place') }}">
                                    </div>
                                    <div class="col-sm-auto flex-grow-1">
                                        <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                        <input type="date" name="birth_date" id="birth_date" class="form-control"
                                               value="{{ old('birth_date') }}">
                                    </div>
                                    <div class="col-sm-auto flex-grow-1">
                                        <label for="identity_number" class="form-label">Nomor KTP</label>
                                        <input type="text" name="identity_number" id="identity_number" class="form-control"
                                               value="{{ old('identity_number') }}">
                                    </div>
                                    <div class="col-sm-auto flex-grow-1">
                                        <label for="npwp" class="form-label">NPWP</label>
                                        <input type="text" name="npwp" id="npwp" class="form-control"
                                               value="{{ old('npwp') }}">
                                    </div>
                                </div>
                            </section>

                            {{-- Kontak & Alamat --}}
                            <section class="pb-3 border-bottom mb-4">
                                <h6 class="fw-bold text-primary mb-3">Kontak & Alamat</h6>
                                <div class="row g-3">
                                    <div class="col-sm-auto flex-grow-1">
                                        <label for="phone" class="form-label">Nomor Telepon</label>
                                        <input type="text" name="phone" id="phone" class="form-control"
                                               value="{{ old('phone') }}">
                                    </div>
                                    <div class="col-sm-auto flex-grow-1">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                               value="{{ old('email', auth()->user()->email) }}" readonly>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label for="address" class="form-label">Alamat Lengkap</label>
                                    <textarea name="address" id="address" rows="2" class="form-control">{{ old('address') }}</textarea>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-sm-auto flex-grow-1">
                                        <label for="province" class="required">Provinsi</label>
                                        <select name="province_id" id="province" class="form-select select2" required>
                                                <option value="">-- Pilih Provinsi --</option>
                                                @foreach($provinces as $province)
                                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="col-sm-auto flex-grow-1">
                                        <label class="required">Kabupaten/Kota</label>
                                            <select name="city_id" id="city" class="form-select select2" required>
                                                <option value="city">-- Pilih Kota --</option>
                                            </select>
                                    </div>
                                    <div class="col-sm-auto flex-grow-1">
                                        <label class="required">Kecamatan</label>
                                            <select name="district_id" id="district" class="form-select select2" required>
                                                <option value="district">-- Pilih Kecamatan --</option>
                                            </select>
                                    </div>
                                    <div class="col-sm-auto flex-grow-1">
                                       <label class="required">Kelurahan</label>
                                            <select name="sub_district_id" id="sub_district" class="form-select select2" required>
                                                <option value="sub_district">-- Pilih Desa --</option>
                                            </select>
                                    </div>
                                    <div class="col-sm-auto flex-grow-1">
                                        <label class="required">Kode Pos</label>
                                            <select name="postal_code_id" id="postal_code" class="form-select select2" required>
                                                <option value="postal_code">-- Pilih Desa --</option>
                                            </select>
                                    </div>
                                </div>
                            </section>

                            {{-- Data Bank --}}
                            <section class="pb-3 mb-4">
                                <h6 class="fw-bold text-primary mb-3">Data Bank</h6>
                                <div class="row g-3">
                                    <div class="col-sm-auto flex-grow-1">
                                        <label for="bank_name" class="form-label">Nama Bank</label>
                                        <input type="text" name="bank_name" id="bank_name" class="form-control"
                                               value="{{ old('bank_name') }}">
                                    </div>
                                    <div class="col-sm-auto flex-grow-1">
                                        <label for="account_number" class="form-label">Nomor Rekening</label>
                                        <input type="number" name="account_number" id="account_number" class="form-control"
                                               value="{{ old('account_number') }}">
                                    </div>
                                    <div class="col-sm-auto flex-grow-1">
                                        <label for="account_holder" class="form-label">Atas Nama</label>
                                        <input type="text" name="account_holder" id="account_holder" class="form-control"
                                               value="{{ old('account_holder') }}">
                                    </div>
                                </div>
                            </section>

                            {{-- Tombol Aksi --}}
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="bi bi-check-circle me-1"></i> Simpan & Kirim
                                </button>
                                <a href="{{ route('guest.dashboard') }}" class="btn btn-outline-secondary px-4 ms-2">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
$('.select2').select2({
            placeholder: "-- Pilih --",
            width: '100%'
        });
</script>

 <script>
    $('#province').change(function () {
    var id = $(this).val();
    $('#city').html('<option>Loading...</option>');
    $('#district').html('<option value="">-- Pilih kecamatan --</option>');
    $('#sub_district').html('<option value="">-- Pilih kelurahan --</option>');

    if (id) {
    $.get('/api/cities/' + id, function (data) {
    $('#city').empty().append('<option value="">-- Pilih city --</option>');
    $.each(data, function (i, city) {
        $('#city').append('<option value="' + city.id + '">' + city.name + '</option>');
            });
        });
        }
    });

    $('#city').change(function () {
    var id = $(this).val();
    $('#district').html('<option>Loading...</option>');
    $('#sub_district').html('<option value="">-- Pilih kelurahan --</option>');

    if (id) {
        $.get('/api/districts/' + id, function (data) {
            $('#district').empty().append('<option value="">-- Pilih kecamatan --</option>');
            $.each(data, function (i, district) {
                $('#district').append('<option value="' + district.id + '">' + district.name + '</option>');
                    });
                });
            }
        });

    $('#district').change(function () {
    var id = $(this).val();
    $('#sub_district').html('<option>Loading...</option>');

        if (id) {
            $.get('/api/sub_districts/' + id, function (data) {
                $('#sub_district').empty().append('<option value="">-- Pilih kelurahan --</option>');
                $.each(data, function (i, sub_district) {
                    $('#sub_district').append('<option value="' + sub_district.id + '">' + sub_district.name + '</option>');
                });
            });
        }
    });

    $('#sub_district').change(function () {
    var id = $(this).val();
    $('#postal_code').html('<option>Loading...</option>');

    if (id) {
        $.get('/api/postal_codes/' + id, function (data) {
            $('#postal_code').empty().append('<option value="">-- Pilih kode pos --</option>');
            $.each(data, function (i, postal_code) {
                $('#postal_code').append('<option value="' + postal_code.id + '">' + postal_code.postal_code + '</option>');
            });
        });
        }
    });
</script>
@endpush

