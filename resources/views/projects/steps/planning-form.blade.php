@php
    $projectLocation = [
    'survey_address'  => $project->project_location,
    'province_id'     => $project->province_id,
    'city_id'         => $project->city_id,
    'district_id'     => $project->district_id,
    'sub_district_id' => $project->sub_district_id,
    'postal_code_id'  => $project->postal_code_id,
];
@endphp

@can('lihat daftar proyek')
<form action="{{ route('projects.plannings.store') }}" method="POST">
    @csrf
    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

    <input type="hidden" name="project_id" value="{{ $project->id }}">

    <div class="row g-4">
        <div class="col-md-4">
            <label class="form-label">Rencana Petugas Survei</label>
            <select name="employee_id[]" class="form-select select2" multiple required>
                @foreach($employees as $employee)
                <option value="{{ $employee->id }}"
                    {{ collect(old('employee_id'))->contains($employee->id) ? 'selected' : '' }}>
                    {{ $employee->display_name }}
                </option>

                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Tanggal Survei</label>
            <input type="date" name="planning_date"
                value="{{ old('planning_date') }}" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Waktu Survei</label>
            <input type="time" name="planning_time"
                value="{{ old('planning_time') }}" class="form-control" required>
        </div>
    </div>
        <div class="section-block mb-3 mt-4">
            <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="same_address" id="same_address"
                        {{ old('same_address', true) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="same_address">
                    Lokasi Survei sama dengan Lokasi Proyek
                </label>
            </div>
        </div>

                            <div class="section-block mb-5" id="location-fields">
                                <h3 class="fw-semibold mb-3 mt-3 border-bottom pb-2">Lokasi Proyek</h3>
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label required">Alamat Lengkap</label>
                                        <textarea name="survey_address" rows="3" class="form-control @error('survey_address') is-invalid @enderror" required>{{ old('survey_address') }} </textarea>
                                        @error('survey_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row g-4 mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label required">Provinsi</label>
                                        <select name="province_id" id="survey_province" 
                                                class="form-select select2 @error('province_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Provinsi --</option>
                                            @foreach($provinces as $province)
                                                <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                                    {{ $province->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('province_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label required">Kabupaten/Kota</label>
                                        <select name="city_id" id="survey_city" 
                                                class="form-select select2 @error('city_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Kota --</option>
                                        </select>
                                        @error('city_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-5">
                                        <label class="form-label required">Kecamatan</label>
                                        <select name="district_id" id="survey_district" 
                                                class="form-select select2 @error('district_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </select>
                                        @error('district_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-5">
                                        <label class="form-label required">Kelurahan</label>
                                        <select name="sub_district_id" id="survey_sub_district" 
                                                class="form-select select2 @error('sub_district_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Kelurahan --</option>
                                        </select>
                                        @error('sub_district_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label required">Kode Pos</label>
                                        <select name="postal_code_id" id="survey_postal_code" 
                                                class="form-select select2 @error('postal_code_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Kode Pos --</option>
                                        </select>
                                        @error('postal_code_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
    <div class="mt-3">
        <label class="form-label">Catatan Survei</label>
        <textarea name="planning_notes" class="form-control" rows="3">{{ old('planning_notes') }}</textarea>
        
    </div>
            <div class="section-block mb-5">
                <h3 class="fw-semibold mb-3 mt-3 border-bottom pb-2">
                    Biaya Survei
                </h3>

                <div class="col-md-4">
                    <label class="form-label required">Biaya Survei</label>

                    <input type="text"
                        name="survey_fee"
                        id="survey_fee"
                        class="form-control rupiah @error('survey_fee') is-invalid @enderror"
                        value="{{ old('survey_fee') }}"
                        required>

                    <small class="text-muted">
                        Isi <strong>Rp 0</strong> jika survei gratis
                    </small>

                    @error('survey_fee')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
    <div class="text-end mt-5">
        <button type="submit" class="btn btn-dark px-4">
            <i class="ti ti-device-floppy me-1"></i>Simpan Form Rencana
        </button>
    </div>
</form>
@endcan

@push('js')
<script>
$(function () {
    initLocationCascade({
        prefix: 'survey_',
        oldProvince: "{{ old('province_id') }}",
        oldCity: "{{ old('city_id') }}",
        oldDistrict: "{{ old('district_id') }}",
        oldSub: "{{ old('sub_district_id') }}",
        oldPostal: "{{ old('postal_code_id') }}"
    });
});
</script>

<script>
    window.projectLocation = @json($projectLocation);
</script>
<script>
$('#survey_province').change(function () {
var id = $(this).val();
$('#survey_city').html('<option>Loading...</option>');
$('#survey_district').html('<option value="">-- Pilih kecamatan --</option>');
$('#survey_sub_district').html('<option value="">-- Pilih kelurahan --</option>');

if (id) {
$.get('/api/cities/' + id, function (data) {
$('#survey_city').empty().append('<option value="">-- Pilih city --</option>');
$.each(data, function (i, city) {
    $('#survey_city').append('<option value="' + city.id + '">' + city.name + '</option>');
        });
    });
    }
});

$('#survey_city').change(function () {
var id = $(this).val();
$('#survey_district').html('<option>Loading...</option>');
$('#survey_sub_district').html('<option value="">-- Pilih kelurahan --</option>');

if (id) {
    $.get('/api/districts/' + id, function (data) {
        $('#survey_district').empty().append('<option value="">-- Pilih kecamatan --</option>');
        $.each(data, function (i, district) {
            $('#survey_district').append('<option value="' + district.id + '">' + district.name + '</option>');
                });
            });
        }
    });

$('#survey_district').change(function () {
var id = $(this).val();
$('#survey_sub_district').html('<option>Loading...</option>');

    if (id) {
        $.get('/api/sub_districts/' + id, function (data) {
            $('#survey_sub_district').empty().append('<option value="">-- Pilih kelurahan --</option>');
            $.each(data, function (i, sub_district) {
                $('#survey_sub_district').append('<option value="' + sub_district.id + '">' + sub_district.name + '</option>');
            });
        });
    }
});

$('#survey_sub_district').change(function () {
var id = $(this).val();
$('#survey_postal_code').html('<option>Loading...</option>');

if (id) {
    $.get('/api/postal_codes/' + id, function (data) {
        $('#survey_postal_code').empty().append('<option value="">-- Pilih kode pos --</option>');
        $.each(data, function (i, postal_code) {
            $('#survey_postal_code').append('<option value="' + postal_code.id + '">' + postal_code.postal_code + '</option>');
        });
    });
    }
});
</script>

<script>
$(document).ready(function () {

function fillFromProject() {
    const loc = window.projectLocation;
    if (!loc) return;

    $('textarea[name="survey_address"]').val(loc.survey_address);

    $('#survey_province').val(loc.province_id).trigger('change');

    $.get('/api/cities/' + loc.province_id, function () {
        $('#survey_city').val(loc.city_id).trigger('change');

        $.get('/api/districts/' + loc.city_id, function () {
            $('#survey_district').val(loc.district_id).trigger('change');

            $.get('/api/sub_districts/' + loc.district_id, function () {
                $('#survey_sub_district').val(loc.sub_district_id).trigger('change');

                $.get('/api/postal_codes/' + loc.sub_district_id, function () {
                    $('#survey_postal_code').val(loc.postal_code_id).trigger('change');
                });
            });
        });
    });
}


    function setReadonly(state) {
        $('#location-fields').find('textarea, input')
            .prop('readonly', state);

        $('#location-fields').find('select')
            .prop('disabled', state)
            .trigger('change.select2');
    }


    // toggle checkbox
    $('#same_address').on('change', function () {
        if (this.checked) {
            fillFromProject();
            setReadOnly(true);
        } else {
            setReadOnly(false);
        }
    });

    // initial load
    if ($('#same_address').is(':checked')) {
        fillFromProject();
        setReadOnly(true);
    }
});
</script>
<script>
document.querySelectorAll('.rupiah').forEach(el => {
    el.addEventListener('input', function () {
        let value = this.value.replace(/[^\d]/g, '');
        this.value = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value || 0);
    });
});
</script>
@endpush