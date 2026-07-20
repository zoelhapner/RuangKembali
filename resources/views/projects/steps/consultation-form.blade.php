@can('lihat daftar proyek')
<form 
      action="{{ route('projects.consultations.store') }}"
      method="POST"
      enctype="multipart/form-data"
      data-project-type="{{ $project->project_type }}">

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
            <label class="form-label">Nama Customer</label>
            <input type="text" class="form-control"
                   name="contact_name"
                   value="{{ old('contact_name', $project->customer->user->fullname ?? '') }}" readonly>
        </div>

        <div class="col-md-4">
            <label class="form-label">No HP</label>
            <input type="text" class="form-control"
                   name="contact_phone"
                   value="{{ old('contact_phone', $project->customer->user->phone ?? '') }}" readonly>
        </div>

        <div class="col-md-4">
            <label class="form-label required">Karyawan</label>
            <select name="employee_id" class="form-select select2" required>
                <option value="">-- Pilih Karyawan --</option>
                @foreach($employees as $employee)
                <option value="{{ $employee->id }}"
                    {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                    {{ $employee->display_name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Ukuran Tanah</label>
            <input type="text" class="form-control" name="site_area" value="{{ old('site_area') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Ukuran Bangunan</label>
            <input type="text" class="form-control" name="building_area" value="{{ old('building_area') }}">
        </div>
    </div>

    <div class="mb-3 mt-4">
        <label class="form-label">Uraian</label>

        <table class="table table-sm table-bordered" id="consultation-items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Uraian</th>
                    <th>Keterangan</th>
                    <th width="1%"></th>
                </tr>
            </thead>
            <tbody id="consultation-items-body"
                  data-project-type="{{ $project->project_type }}">
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

        <button type="button" data-target="consultation-items-table" class="btn btn-sm btn-dark add-row">+ Tambah Uraian</button>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Upload Dokumen (PDF)</label>
        <input type="file"
            name="documents[]"
            class="form-control pdf-input"
            data-preview="preview-documents"
            accept="application/pdf"
            multiple>
        <div id="preview-documents" class="mt-3 d-flex flex-column gap-2"></div>
    </div> 

    <div class="row mb-3 mt-4">
        <div class="col-md-6">
            <label class="form-label fw-bold">Persetujuan Konsultan</label><br>
            <label>
                <input type="checkbox" name="consultant_signed" value="1"
                    {{ old('consultant_signed') ? 'checked' : '' }}>
                Saya sebagai Konsultan menyetujui hasil konsultasi ini
            </label>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Persetujuan Customer</label><br>
            <label>
                <input type="checkbox" name="client_signed" value="1"
                    {{ old('client_signed') ? 'checked' : '' }}>

                Customer menyetujui hasil konsultasi ini
            </label>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Catatan Tambahan</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>

    </div>

        <div class="md-6 mb-4">
            <label class="fw-bold">Foto Dokumentasi</label>
            <div class="text-muted mb-2">Foto dokumentasi saat kegiatan konsultasi</div>
            <input type="file"
                name="documentation[]"
                class="form-control image-input"
                data-preview="preview-documentation"
                accept="image/*"
                multiple>

            <div id="preview-documentation"
                class="mt-3 d-flex flex-wrap gap-3"></div>
        </div>

    <div class="text-end mt-5">
        <button type="submit" class="btn btn-dark px-4">
            <i class="ti ti-device-floppy me-1"></i>Simpan Konsultasi
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
{{-- <script>
document.addEventListener('DOMContentLoaded', function () {

    const tbody = document.getElementById('consultation-items-body');
    if (!tbody) return;

    const projectType = tbody.dataset.projectType;
    if (projectType != '2') return; // hanya RAB

    // kosongkan default row
    tbody.innerHTML = '';

    const templates = [
        'Desain Denah',
        'Desain 3D',
        'Desain DED'
    ];

    templates.forEach((label, i) => {
        const row = document.createElement('tr');
        row.dataset.fixed = "1"; // 🔒 FLAG FIXED

        row.innerHTML = `
            <td class="row-no text-center">${i + 1}</td>

            <td>
                <input type="hidden"
                       name="items[${i}][description]"
                       value="${label}">
                ${label}
            </td>

            <td>
                <label class="me-3">
                    <input type="radio"
                           name="items[${i}][remark]"
                           value="Ada"> Ada
                </label>
                <label>
                    <input type="radio"
                           name="items[${i}][remark]"
                           value="Tidak"> Tidak
                </label>
            </td>

            <td></td> <!-- ⛔ TANPA REMOVE -->
        `;
        tbody.appendChild(row);
    });
});
</script> --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const tbody = document.getElementById('consultation-items-body');
    if (!tbody) return;

    const projectType = tbody.dataset.projectType;

    let templates = [];

    if (projectType === '2') {
        templates = [
            'Desain Denah',
            'Desain 3D',
            'Desain DED'
        ];
    }

    if (projectType === '3') {
        templates = [
            'Desain Denah',
            'Desain 3D',
            'Desain DED',
            'Desain RAB'
        ];
    }

    // kalau type 1 → tidak pakai template → biarkan default blade
    if (templates.length === 0) return;

    // kosongkan row lama
    tbody.innerHTML = '';

    templates.forEach((label, i) => {
        const row = document.createElement('tr');
        row.dataset.fixed = "1";

        row.innerHTML = `
            <td class="row-no text-center">${i + 1}</td>

            <td>
                <input type="hidden"
                       name="items[${i}][description]"
                       value="${label}">
                <strong>${label}</strong>
            </td>

            <td>
                <label class="me-3">
                    <input type="radio"
                           name="items[${i}][remark]"
                           value="Ada"> Ada
                </label>
                <label>
                    <input type="radio"
                           name="items[${i}][remark]"
                           value="Tidak"> Tidak
                </label>
            </td>

            <td></td>
        `;

        tbody.appendChild(row);
    });
});
</script>
<script>
document.addEventListener('submit', function (e) {

    const form = e.target;
    if (!form.closest('form')) return;

    const tbody = document.getElementById('consultation-items-body');
    if (!tbody) return;

    let valid = true;
    let messageShown = false;

    tbody.querySelectorAll('tr[data-fixed="1"]').forEach((row, index) => {
        const radios = row.querySelectorAll('input[type="radio"]');
        const checked = Array.from(radios).some(r => r.checked);

        if (!checked) {
            valid = false;
            if (!messageShown) {
                alert(`Uraian "${row.querySelector('strong').innerText}" wajib dipilih (Ada / Tidak)`);
                messageShown = true;
            }
        }
    });

    if (!valid) {
        e.preventDefault();
    }
});
</script>


@endpush