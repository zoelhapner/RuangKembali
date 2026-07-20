@can('lihat daftar proyek')
@if(!$final)
<form action="{{ route('projects.finals.store', $project->id) }}"
      method="POST"
      enctype="multipart/form-data">

    @csrf

    <div class="section-block mt-4"> 
        <div class="row g-4">
            <div class="col-md-6 mb-3">
                <label class="required">Upload Hasil File Keseluruhan</label>
                <div class="text-muted mb-2">
                    Dokumen hasil keseluruhan proyek desain
                </div>

                <input type="file"
                       name="document"
                       class="form-control"
                       accept=".zip,.rar,.pdf"
                       required>

                @error('document')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>

    <div class="text-end mt-4">
        <button class="btn btn-dark">
            <i class="ti ti-upload"></i> Simpan Hasil Proyek
        </button>
    </div>
</form>
@endif
@endcan
