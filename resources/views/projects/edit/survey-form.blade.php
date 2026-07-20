@php
    $survey = $project->survey;

    // Ambil employees dari survey level (level 3)
    $surveyLevel = $project->levels->firstWhere('level_order', 3);
    $surveyEmployees = $surveyLevel ? $surveyLevel->employees : collect();
@endphp

<div class="card mb-4">
    <div class="card-header fw-bold">Edit Data Survei</div>
    <div class="card-body">
        <form 
            action="{{ route('surveys.update', $survey->id) }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <input type="hidden" name="project_id" value="{{ $project->id }}">

            <div class="row g-4">

                {{-- CUSTOMER --}}
                <div class="col-md-4">
                    <label class="form-label">Nama Customer</label>
                    <input type="text" class="form-control"
                        name="contact_name"
                        value="{{ old('contact_name', $survey->contact_name) }}">
                </div>

                {{-- PETUGAS SURVEI --}}
                <div class="col-md-4">
                    <label class="form-label">Petugas Survei</label>
                    <select name="employee_id[]" class="form-select select2" multiple required>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}"
                                {{ $surveyEmployees->contains('id', $emp->id) ? 'selected' : '' }}>
                                {{ $emp->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TANGGAL --}}
                <div class="col-md-2">
                    <label class="form-label">Tanggal Survei</label>
                    <input type="date" 
                           name="survey_date" 
                           class="form-control" 
                           required
                           value="{{ old('survey_date', $survey->survey_date) }}">
                </div>

                {{-- WAKTU --}}
                <div class="col-md-2">
                    <label class="form-label">Waktu Survei</label>
                    <input type="time" 
                           name="survey_time" 
                           class="form-control" 
                           required
                           value="{{ old('survey_time', $survey->survey_time) }}">
                </div>

                {{-- SITE AREA --}}
                <div class="col-md-4">
                    <label class="form-label">Ukuran Tanah (Aktual)</label>
                    <input type="text" class="form-control" name="site_area"
                           value="{{ old('site_area', $survey->site_area) }}">
                </div>

                {{-- BUILDING --}}
                <div class="col-md-4">
                    <label class="form-label">Ukuran Bangunan (Aktual)</label>
                    <input type="text" class="form-control" name="building_area"
                           value="{{ old('building_area', $survey->building_area) }}">
                </div>

            </div>

            <div class="section-block mt-4">
                <div class="row g-4">
                    <div class="col-md-6 mb-4">
                        <label class="fw-bold required">Foto Hasil Survei / Denah</label>
                        <div class="text-muted mb-2">Sketsa, denah, kondisi lapangan</div>

                        {{-- EXISTING FILES (hanya muncul di edit) --}}
                        @isset($survey)
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                @foreach($survey->images as $img)
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/'.$img->file_path) }}"
                                            class="rounded border"
                                            style="width:120px;height:120px;object-fit:cover">

                                        <button type="button"
                                            class="btn btn-sm btn-danger position-absolute top-0 end-0 btn-delete"
                                            data-id="{{ $img->id }}"
                                            data-type="image">×</button>
                                    </div>
                                @endforeach
                            </div>
                        @endisset

                        {{-- INPUT UPLOAD --}}
                        <input type="file"
                            name="result_images[]"
                            class="form-control image-input"
                            data-preview="preview-result-images"
                            accept="image/*"
                            multiple>

                        <div id="preview-result-images"
                            class="mt-3 d-flex flex-wrap gap-3"></div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="fw-bold">Dokumen Denah Existing (PDF)</label>

                        @isset($survey)
                            @if($survey->documents->count())
                            <ul class="list-group mb-3">
                                @foreach($survey->documents as $doc)
                                <li class="list-group-item d-flex justify-content-between">
                                    <a href="{{ asset('storage/'.$doc->file_path) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-dark">
                                    Lihat File
                                    </a>

                                    <button type="button"
                                        class="btn btn-sm btn-danger btn-delete"
                                        data-id="{{ $doc->id }}"
                                        data-type="document">
                                        Hapus
                                    </button>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        @endisset

                        <input type="file"
                            name="documents[]"
                            class="form-control pdf-input"
                            data-preview="preview-documents"
                            accept="application/pdf"
                            multiple>

                        <div id="preview-documents"
                            class="mt-3 d-flex flex-column gap-2"></div>
                    </div>
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label class="form-label">Uraian</label>

                <table class="table table-sm table-bordered" id="items-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Uraian</th>
                            <th>Keterangan</th>
                            <th width="1%"></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($survey->items as $i => $item)
                            <tr>
                                <td class="row-no text-center">{{ $i + 1 }}</td>
                                <td>
                                    <textarea name="items[{{ $i }}][description]" 
                                              class="form-control" rows="2">{{ $item->description }}</textarea>
                                </td>
                                <td>
                                    <textarea name="items[{{ $i }}][remark]" 
                                              class="form-control" rows="2">{{ $item->remark }}</textarea>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-row">−</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="button" id="tambah-bariss" class="btn btn-sm btn-dark">+ Tambah Uraian</button>
            </div>

            <div class="mt-4">
                <label class="fw-bold">Foto Dokumentasi</label>

                @isset($survey)
                <div class="d-flex flex-wrap gap-3 mb-3">
                    @foreach($survey->documentations as $doc)
                    <div class="position-relative">
                        <img src="{{ asset('storage/'.$doc->file_path) }}"
                            class="rounded border"
                            style="width:120px;height:120px;object-fit:cover">

                        <button type="button"
                            class="btn btn-sm btn-danger position-absolute top-0 end-0 btn-delete"
                            data-id="{{ $doc->id }}"
                            data-type="documentation">
                            ×
                        </button>
                    </div>
                    @endforeach
                </div>
                @endisset

                <input type="file"
                    name="documentation[]"
                    class="form-control image-input"
                    data-preview="preview-documentation"
                    accept="image/*"
                    multiple>

                <div id="preview-documentation"
                    class="mt-3 d-flex flex-wrap gap-3"></div>
            </div>
            {{-- TTD --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">Tanda Tangan Surveyor</label><br>
                    <input type="checkbox" name="consultant_signed" value="1"
                        {{ $survey->consultant_signed ? 'checked' : '' }}>
                    <span class="ms-2">Saya menyetujui</span>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tanda Tangan Client</label><br>
                    <input type="checkbox" name="client_signed" value="1"
                        {{ $survey->client_signed ? 'checked' : '' }}>
                    <span class="ms-2">Saya menyetujui</span>
                </div>
            </div>

            {{-- CATATAN --}}
            <div class="mb-3 mt-3">
                <label class="form-label">Catatan Tambahan</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $survey->notes) }}</textarea>    
            </div>

            {{-- SUBMIT --}}
            <div class="mt-4">
                <button class="btn btn-dark">Simpan</button>
                <button type="button" id="btn-cancel-survey" class="btn btn-light">Batal</button>
            </div>

        </form>
    </div>
</div>

@push('js')
{{-- <script>
document.getElementById('tambah-bariss').addEventListener('click', function () {
    let table = document.querySelector("#items-table tbody");
    let index = table.rows.length;

    table.insertAdjacentHTML('beforeend', `
        <tr>
            <td class="row-no text-center">${index + 1}</td>
            <td><textarea name="items[${index}][description]" class="form-control" rows="2"></textarea></td>
            <td><textarea name="items[${index}][remark]" class="form-control" rows="2"></textarea></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">−</button></td>
        </tr>
    `);
});

// remove row
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
        document.querySelectorAll("#items-table tbody tr").forEach((tr, i) => {
            tr.querySelector('.row-no').innerText = i + 1;
        });
    }
});
</script> --}}

{{-- <script>
document.getElementById('result-images-input')
    .addEventListener('change', function(e) {
  document.getElementById('old-result-images')?.remove();
    const preview = document.getElementById('preview-result-images');
    preview.innerHTML = ""; // hapus preview gambar lama

    const files = e.target.files;
    [...files].forEach(file => {
        let reader = new FileReader();
        reader.onload = function(ev) {
            preview.insertAdjacentHTML(
                'beforeend',
                `<img src="${ev.target.result}" width="120" height="120" class="rounded border me-2 mb-2" style="object-fit:cover;">`
            );
        };
        reader.readAsDataURL(file);
    });
});
</script>

<script>
document.getElementById('documentation-input')
    .addEventListener('change', function(e) {
          document.getElementById('old-documentation')?.remove();
    const preview = document.getElementById('preview-documentation');
    preview.innerHTML = ""; // hapus preview lama

    const files = e.target.files;
    [...files].forEach(file => {
        let reader = new FileReader();
        reader.onload = function(ev) {
            preview.insertAdjacentHTML(
                'beforeend',
                `<img src="${ev.target.result}" width="120" height="120" class="rounded border me-2 mb-2" style="object-fit:cover;">`
            );
        };
        reader.readAsDataURL(file);
    });
});
</script> --}}

@endpush
