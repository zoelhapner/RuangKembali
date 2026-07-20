@extends('tablar::page')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Form Konsultasi</h5>
            <small class="text-muted">Project: {{ $project->project_name }}</small>
        </div>
        <div>
            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('projects.consultations.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="project_id" value="{{ $project->id }}">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control" name="contact_name" value="{{ old('contact_name', $project->customer->user->fullname ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">No HP</label>
                    <input type="text" class="form-control" name="contact_phone" value="{{ old('contact_phone', $project->customer->user->phone ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Ukuran Tanah / Bangunan</label>
                    <input type="text" class="form-control" name="site_area" value="{{ old('site_area') }}">
                </div>
            </div>

            {{-- tabel uraian dinamis --}}
            <div class="mb-3">
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
                        @if(old('items'))
                            @foreach(old('items') as $i => $it)
                                <tr>
                                    <td class="row-no text-center">{{ $i + 1 }}</td>
                                    <td><textarea name="items[{{ $i }}][description]" class="form-control" rows="2">{{ $it['description'] }}</textarea></td>
                                    <td><input name="items[{{ $i }}][remark]" class="form-control" value="{{ $it['remark'] ?? '' }}"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-row">-</button></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="row-no text-center">1</td>
                                <td><textarea name="items[0][description]" class="form-control" rows="2"></textarea></td>
                                <td><input name="items[0][remark]" class="form-control"></td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-row">-</button></td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <button type="button" id="add-row" class="btn btn-sm btn-primary">+ Tambah Uraian</button>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tanda Tangan Konsultan (upload PNG/JPG)</label>
                    <input type="file" name="consultant_signature" class="form-control" accept="image/*" id="consultant-sign">
                    <img id="consultant-preview" class="mt-2" style="max-height:120px; display:none;" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanda Tangan Client (upload PNG/JPG)</label>
                    <input type="file" name="client_signature" class="form-control" accept="image/*" id="client-sign">
                    <img id="client-preview" class="mt-2" style="max-height:120px; display:none;" />
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Catatan Tambahan</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Simpan Konsultasi</button>
                <a href="{{ route('consultations.pdf', ['consultation' => 0]) }}" id="print-preview" class="btn btn-outline-secondary" style="display:none;" target="_blank">Cetak / Preview PDF</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // add row
    const addBtn = document.getElementById('add-row');
    const table = document.querySelector('#items-table tbody');

    function renumber() {
        table.querySelectorAll('tr').forEach((tr, idx) => {
            tr.querySelector('.row-no').textContent = idx + 1;
            // update input names
            tr.querySelectorAll('textarea, input[type="text"]').forEach(el => {
                if (el.name.includes('items')) {
                    const field = el.name.split(']')[1]; // like [description] or [remark]
                    el.name = `items[${idx}]${field}`;
                }
            });
        });
    }

    addBtn.addEventListener('click', function () {
        const idx = table.querySelectorAll('tr').length;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="row-no text-center">${idx + 1}</td>
            <td><textarea name="items[${idx}][description]" class="form-control" rows="2"></textarea></td>
            <td><input name="items[${idx}][remark]" class="form-control"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">-</button></td>
        `;
        table.appendChild(tr);
    });

    // remove row
    table.addEventListener('click', function (e) {
        if (e.target.matches('.remove-row')) {
            const tr = e.target.closest('tr');
            tr.remove();
            renumber();
        }
    });

    // signature previews
    function readPreview(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }

    document.getElementById('consultant-sign').addEventListener('change', function () {
        readPreview(this, 'consultant-preview');
    });

    document.getElementById('client-sign').addEventListener('change', function () {
        readPreview(this, 'client-preview');
    });

});
</script>
@endsection
