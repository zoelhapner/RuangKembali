@can('lihat daftar proyek')
<form 
      action="{{ route('projects.surveys.store') }}"
      method="POST"
      enctype="multipart/form-data">

    @csrf
    <input type="hidden" name="project_id" value="{{ $project->id }}">

    <div class="row g-4">
        <div class="col-md-4">
            <label class="form-label required">Nama Customer</label>
            <input type="text" class="form-control"
                   name="contact_name"
                   value="{{ old('contact_name', $project->customer->user->fullname ?? '') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label required">Petugas Survei</label>
            <select name="employee_id[]"
                    class="form-select select2 @error('employee_id') is-invalid @enderror"
                    multiple
                    required>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}"
                        {{ in_array($employee->id, old('employee_id', [])) ? 'selected' : '' }}>
                        {{ $employee->display_name }}
                    </option>
                @endforeach
            </select>

            @error('employee_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-2">
            <label class="form-label required">Tanggal Survei</label>
            <input type="date"
       name="survey_date"
       class="form-control @error('survey_date') is-invalid @enderror"
       value="{{ old('survey_date') }}"
       required>

@error('survey_date')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror

        </div>

        <div class="col-md-2">
            <label class="form-label required">Waktu Survei</label>
            <input type="time"
       name="survey_time"
       class="form-control @error('survey_time') is-invalid @enderror"
       value="{{ old('survey_time') }}"
       required>

@error('survey_time')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror

        </div>

        <div class="col-md-4">
            <label class="form-label required">Ukuran Tanah (Aktual) </label>
            <input type="text"
       class="form-control @error('site_area') is-invalid @enderror"
       name="site_area"
       value="{{ old('site_area') }}" required>

@error('site_area')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror

        </div>

        <div class="col-md-4">
            <label class="form-label required">Ukuran Bangunan (Aktual) </label>
            <input type="text"
       class="form-control @error('building_area') is-invalid @enderror"
       name="building_area"
       value="{{ old('building_area') }}" required>

@error('building_area')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror

        </div>
    </div>
    <div class="section-block mt-4"> 
        <div class="row g-4">
            <div class="col-md-6 mb-4">
                <label class="fw-bold">Foto Hasil Survei / Denah</label>
                <div class="text-muted mb-2">Foto hasil survei seperti sketsa, denah, atau kondisi lapangan</div>
                <input type="file"
                    name="result_images[]"
                    class="form-control image-input"
                    data-preview="preview-result-images"
                    accept="image/*"
                    multiple>

                <div id="preview-result-images"
                    class="mt-3 d-flex flex-wrap gap-3"></div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-bold">Dokumen Denah existing (PDF)</label>
                <div class="text-muted mb-2">Boleh upload lebih dari 1 file PDF</div>
                <input type="file"
                    name="documents[]"
                    class="form-control pdf-input"
                    data-preview="preview-documents"
                    accept="application/pdf"
                    multiple>
                <div id="preview-documents" class="mt-3 d-flex flex-column gap-2"></div>
            </div>    
        </div>
    </div>

    <div class="mb-3 mt-3">
                            <label class="form-label">Uraian</label>

                            <table class="table table-sm table-bordered" id="survey-items-table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Uraian</th>
                                        <th>Keterangan</th>
                                        <th width="1%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(old('items'))
                                        @foreach(old('items', [ ['description' => '', 'remark' => ''] ]) as $i => $it)
                                            <tr>
                                                <td class="row-no text-center">{{ $i + 1 }}</td>

                                                <td>
                                                    <textarea name="items[{{ $i }}][description]" class="form-control" rows="2">{{ data_get($it, 'description') }}</textarea>
                                                </td>

                                                <td>
                                                    <textarea name="items[{{ $i }}][remark]" class="form-control" rows="2">{{ data_get($it, 'remark') }}</textarea>
                                                </td>

                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remove-row">-</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="row-no text-center">1</td>
                                            <td><textarea name="items[0][description]" class="form-control" rows="2"></textarea></td>
                                            <td><textarea name="items[0][remark]" class="form-control" rows="2"></textarea></td>
                                            <td><button type="button" class="btn btn-sm btn-danger remove-row">-</button></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <button type="button" data-target="survey-items-table" class="btn btn-sm btn-dark add-row">+ Tambah Uraian</button>
                        </div>

        <div class="mb-4">
            <label class="fw-bold">Foto Dokumentasi</label>
            <div class="text-muted mb-2">Foto dokumentasi saat kegiatan survei</div>
            <input type="file"
                name="documentation[]"
                class="form-control image-input"
                data-preview="preview-documentation"
                accept="image/*"
                multiple>

            <div id="preview-documentation"
                class="mt-3 d-flex flex-wrap gap-3"></div>
        </div>

    <div class="row mb-3 mt-4">
        <div class="col-md-6">
            <label class="form-label fw-bold">Persetujuan Petugas Survei</label><br>
            <label>
<input type="checkbox"
       name="consultant_signed"
       value="1"
       {{ old('consultant_signed') ? 'checked' : '' }}>

                Saya sebagai Petugas Survei menyetujui hasil survei ini
            </label>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Persetujuan Customer</label><br>
            <label>
<input type="checkbox"
       name="client_signed"
       value="1"
       {{ old('client_signed') ? 'checked' : '' }}>

                Customer menyetujui hasil survei ini
            </label>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Catatan Tambahan</label>
        <textarea name="notes"
                class="form-control @error('notes') is-invalid @enderror"
                rows="3">{{ old('notes') }}</textarea>

        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="text-end mt-5">
        <button type="submit" class="btn btn-dark px-4">
            <i class="ti ti-device-floppy me-1"></i>Simpan Form Survei
        </button>
    </div>
</form>
@endcan

@push('js')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Pilih --",
            width: '100%'
        });
    });
</script>
@endpush



