@php
    $planning = $project->planning;
    $planningLevel = $project->levels->firstWhere('level_order', 2);
    $planningEmployees = $planningLevel ? $planningLevel->employees : collect();
    $surveyInvoice = $project?->latestSurveyInvoice();
@endphp

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body px-5 py-4">
        <h3 class="mb-4 fw-bold">Edit Data Rencana Survei</h3>
        <form action="{{ route('plannings.update', $planning->id) }}" method="POST">
            @csrf
            @method('put')

            <input type="hidden" name="project_id" value="{{ $project->id }}">

            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label">Rencana Petugas Survei</label>
<select name="employee_id[]" class="form-select select2" multiple required>
    @foreach($employees as $emp)
        <option value="{{ $emp->id }}"
            {{ $planningEmployees->contains('id', $emp->id) ? 'selected' : '' }}>
            {{ $emp->display_name }}
        </option>
    @endforeach
</select>



                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal Survei</label>
                    <input type="date" name="planning_date" class="form-control" required
                    value="{{ old('planning_date', $planning->planning_date) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Waktu Survei</label>
                    <input type="time" name="planning_time" class="form-control" required
                    value="{{ old('planning_time', $planning->planning_time) }}">
                </div>
            </div>

            <div class="section-block mb-3 mt-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="same_address" name="same_address">
                                        <label class="form-check-label fw-semibold" for="same_address">
                                            Lokasi Survei sama dengan Lokasi proyek?
                                        </label>
                                    </div>
                                </div>

            <div class="section-block mb-5">
                                        <h3 class="fw-semibold mb-3 border-bottom pb-2">Lokasi Proyek</h3>
                                        <div class="row g-4">
                                            <div class="col-12">
                                                <label class="form-label required">Alamat Lengkap</label>
                                                <textarea name="survey_address" rows="3" class="form-control @error('survey_address') is-invalid @enderror" required>{{ old('survey_address', $planning->survey_address) }} </textarea>
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
                                                        <option value="{{ $province->id }}" 
                                                            {{ old('province_id', $planning->province_id) == $province->id ? 'selected' : '' }}>>
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
                                                    <select name="city_id" id="survey_city" class="form-select select2" required>
                                                        @if($planning->city_id)
                                                            <option value="{{ $planning->city_id }}" selected>
                                                                {{ $planning->city->name }}
                                                            </option>
                                                        @else
                                                            <option value="">-- Pilih Kota --</option>
                                                        @endif
                                                    </select>
                                                @error('city_id')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="col-md-5">
                                                <label class="form-label required">Kecamatan</label>
                                                    <select name="district_id" id="survey_district" class="form-select select2" required>
                                                        @if($planning->district_id)
                                                            <option value="{{ $planning->district_id }}" selected>
                                                                {{ $planning->district->name }}
                                                            </option>
                                                        @else
                                                            <option value="">-- Pilih Kota --</option>
                                                        @endif
                                                    </select>
                                                @error('district_id')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="col-md-5">
                                                <label class="form-label required">Kelurahan</label>
                                                    <select name="sub_district_id" id="survey_sub_district" class="form-select select2" required>
                                                        @if($planning->sub_district_id)
                                                            <option value="{{ $planning->sub_district_id }}" selected>
                                                                {{ $planning->subDistrict->name }}
                                                            </option>
                                                        @else
                                                            <option value="">-- Pilih Kota --</option>
                                                        @endif
                                                    </select>
                                                @error('sub_district_id')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label required">Kode Pos</label>
                                                    <select name="postal_code_id" id="survey_postal_code" class="form-select select2" required>
                                                        @if($planning->postal_code_id)
                                                            <option value="{{ $planning->postal_code_id }}" selected>
                                                                {{ $planning->postalCode->postal_code }}
                                                            </option>
                                                        @else
                                                            <option value="">-- Pilih Kota --</option>
                                                        @endif
                                                    </select>
                                                @error('postal_code_id')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
            <div class="mt-3">
                <label class="form-label">Catatan Survei</label>
                <textarea name="planning_notes" class="form-control" rows="3">{{ old('planning_notes', $planning->planning_notes) }}</textarea>
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
                        value="{{ $surveyInvoice ? 'Rp '.number_format($surveyInvoice->amount,0,',','.') : '-' }}"
                        required>

                    <small class="text-muted">
                        Isi <strong>Rp 0</strong> jika survei gratis
                    </small>

                    @error('survey_fee')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="text-end mt-4">
                <button class="btn btn-dark">Simpan Perubahan</button>
                <button type="button" id="btn-cancel-planning" class="btn btn-light">Batal</button>
            </div>
        </form>
    </div>
</div>

@push('js')
    <script>
$('#survey_province').change(function () 
{
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
