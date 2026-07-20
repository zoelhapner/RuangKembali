@extends('tablar::page')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col d-flex align-items-center">
                <a href="{{ route('warehouses.index') }}" class="btn btn-dark d-flex align-items-center" style="margin-left: 30px;">
                    <i class="ti ti-arrow-left"></i>
                </a>
                
                    <h2 class="page-title mb-0">Edit Data Gudang</h2>
                
            </div>
        </div>
    </div>
</div>


<div class="page-body">
    <div class="container-xl">
        <div class="card shadow-sm border-0">
            <div class="card-body px-5 py-4">
                <form action="{{ route('warehouses.update', $warehouse->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                    {{-- <div class="text-center mb-5">
                        <div class="position-relative d-inline-block">
                            @if ($warehouse->photo)
                                <img id="previewImage" src="{{ asset('storage/photos/'.$warehouse->photo) }}" alt="Profile" 
                                    class="rounded-3 shadow-sm border" width="150" height="150"
                                    style="object-fit: cover;">
                            @else
                                <div id="previewImage"
                                    class="rounded-3 shadow-sm bg-light d-flex align-items-center justify-content-center"
                                    style="width:150px; height:150px;">
                                    <i class="ti ti-warehouse" style="font-size: 64px; color:#aaa;"></i>
                                </div>
                            @endif
                            <label for="photo"
                                class="btn btn-sm btn-dark position-absolute bottom-0 end-0 translate-middle rounded-circle"
                                title="Ganti Foto">
                                <i class="ti ti-camera"></i>
                            </label>
                        </div>
                        <input type="file" id="photo" name="photo" class="d-none" accept="image/*">
                    </div> --}}

                    <div class="mb-3">
                        <small class="text-danger fw-semibold">
                            * : Wajib diisi
                        </small>
                    </div>

                    
                        <div class="section-block mb-5">
                            <h3 class="fw-semibold mb-3 border-bottom pb-2">Informasi Gudang</h3>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label required">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"  value="{{ old('name', $warehouse->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Penanggungjawab</label>
                                    <input type="text" name="responsible_person" class="form-control @error('responsible_person') is-invalid @enderror" value="{{ old('responsible_person', $warehouse->responsible_person) }}">
                                    @error('responsible_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Deskripsi</label>
                                    <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description', $warehouse->description) }}">
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="section-block mb-5">
                            <h3 class="fw-semibold mb-3 border-bottom pb-2">📞 Kontak & Alamat</h3>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $warehouse->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $warehouse->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label required">Alamat Lengkap Gudang</label>
                                    <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $warehouse->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label class="required">Provinsi</label>
                                        <select name="province_id" id="province" class="form-select select2" required>
                                            <option value="">-- Pilih Provinsi --</option>
                                            @foreach($provinces as $province)
                                                <option value="{{ $province->id }}"
                                                    {{ $warehouse->province_id == $province->id ? 'selected' : '' }}>
                                                    {{ $province->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="required">Kabupaten/Kota</label>
                                                <select name="city_id" id="city" class="form-select select2" required>
                                                    <option value="">-- Pilih Kota --</option>
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}"
                                                            {{ $warehouse->city_id == $city->id ? 'selected' : '' }}>
                                                            {{ $city->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="required">Kecamatan</label>
                                                <select name="district_id" id="district" class="form-select select2" required>
                                                    <option value="">-- Pilih Kecamatan --</option>
                                                    @foreach($districts as $district)
                                                        <option value="{{ $district->id }}"
                                                            {{ $warehouse->district_id == $district->id ? 'selected' : '' }}>
                                                            {{ $district->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="required">Kelurahan</label>
                                                <select name="sub_district_id" id="sub_district" class="form-select select2" required>
                                                    <option value="">-- Pilih kelurahan --</option>
                                                    @foreach($subDistricts as $sub_district)
                                                        <option value="{{ $sub_district->id }}"
                                                            {{ $warehouse->sub_district_id == $sub_district->id ? 'selected' : '' }}>
                                                            {{ $sub_district->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="required">Kode Pos</label>
                                                <select name="postal_code_id" id="postal_code" class="form-select select2" required>
                                                    <option value="">-- Pilih Kode Pos --</option>
                                                    @foreach($postalCodes as $postal_code)
                                                        <option value="{{ $postal_code->id }}"
                                                            {{ $warehouse->postal_code_id == $postal_code->id ? 'selected' : '' }}>
                                                            {{ $postal_code->postal_code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SUBMIT --}}
                    <div class="text-end mt-5">
                        <button type="submit" class="btn btn-dark px-4">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

                                    @push('js')
                                        <script>
                                            $(document).ready(function() {
                                                $('.select2').select2({
                                                    placeholder: "-- Pilih --",
                                                    width: '100%'
                                                });
                                            });
                                        </script>

                                        <script>
                                        $('#province').change(function () {
                                        var id = $(this).val();
                                        $('#city').html('<option>Loading...</option>');
                                        $('#district').html('<option value="">-- Pilih kecamatan --</option>');
                                        $('#sub_district').html('<option value="">-- Pilih Kelurahan --</option>');

                                            if (id) {
                                            $.get('/api/cities/' + id, function (data) {
                                            $('#city').empty().append('<option value="">-- Pilih Kabupaten --</option>');
                                            $.each(data, function (i, city) {
                                            $('#city').append('<option value="' + city.id + '">' + city.name + '</option>');
                                                        });
                                                    });
                                                }
                                            });

                                            $('#city').change(function () {
                                                var id = $(this).val();
                                                $('#district').html('<option>Loading...</option>');
                                                $('#sub_district').html('<option value="">-- Pilih Kelurahan --</option>');

                                                if (id) {
                                                    $.get('/api/districts/' + id, function (data) {
                                                        $('#district').empty().append('<option value="">-- Pilih Kecamatan --</option>');
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
                                                        $('#sub_district').empty().append('<option value="">-- Pilih Kelurahan --</option>');
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
                                                        $('#postal_code').empty().append('<option value="">-- Pilih Kode Pos --</option>');
                                                        $.each(data, function (i, postal_code) {
                                                            $('#postal_code').append('<option value="' + postal_code.id + '">' + postal_code.postal_code + '</option>');
                                                        });
                                                    });
                                                }
                                            });
                                        </script>
                                    @endpush

                                    

