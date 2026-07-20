@php
    $consultation = $project->consultation;
@endphp

@can('lihat data proyek')
@if(isset($consultation))
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header fw-bold">
        Detail Konsultasi
    </div>

    <div class="card-body">
        <div class="row g-4">

            <div class="col-md-4">
                <label class="fw-semibold">Nama Customer</label>
                <input type="text" class="form-control"
                       value="{{ $consultation->contact_name }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="fw-semibold">No HP</label>
                <input type="text" class="form-control"
                       value="{{ $consultation->contact_phone }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="fw-semibold">Karyawan Konsultan</label>
                <input type="text" class="form-control"
                       value="{{ $consultation->employee->display_name ?? '-' }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="fw-semibold">Ukuran Tanah</label>
                <input type="text" class="form-control"
                       value="{{ $consultation->site_area }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="fw-semibold">Ukuran Bangunan</label>
                <input type="text" class="form-control"
                       value="{{ $consultation->building_area }}" readonly>
            </div>

            <div class="col-md-12 mt-3">
                <label class="fw-semibold mb-2">Daftar Uraian</label>

                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Uraian</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consultation->items as $item)
                        <tr>
                            <td class="text-center">{{ $item->order_no }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->remark }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-6 mb-4">
                <h5 class="fw-bold">Dokumen Upload</h5>
                @if($consultation->documents->count())
                    <ul class="list-group">
                        @foreach($consultation->documents as $docs)
                            <li class="list-group-item d-flex justify-content-between">
                                <a href="{{ asset('storage/'.$docs->file_path) }}"
                                target="_blank"
                                class="btn btn-sm btn-outline-dark">
                                    Lihat File
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <em class="text-muted">Tidak ada dokumen.</em>
                @endif
            </div>
            <div class="mt-4">
                <h5 class="fw-bold">Foto Dokumentasi</h5>
                @if($consultation->documentations->count())
                    <div class="row g-3 mt-2">
                        @foreach($consultation->documentations as $doc)
                            <div class="col-6 col-md-3">
                                <div class="border rounded shadow-sm p-1">
                                        <img src="{{ asset('storage/'.$doc->file_path) }}"
                                            data-src="{{ asset('storage/'.$doc->file_path) }}"
                                            class="img-fluid rounded preview-image"
                                            style="height:150px; object-fit:cover; cursor:pointer;">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                <p class="text-muted">Tidak ada foto hasil survei.</p>
                @endif
            </div>

            <div class="col-md-12 mt-3">
                <label class="fw-semibold">Catatan Tambahan</label>
                <textarea class="form-control" rows="3" readonly>{{ $consultation->notes }}</textarea>
            </div>

            <div class="col-12 mt-3 d-flex justify-content-around text-center">
                <div>
                    <label class="form-label fw-bold d-block">Persetujuan Konsultan</label>
                    <i class="ti {{ $consultation->consultant_signed ? 'ti-check text-success' : 'ti-x text-danger' }}"
                    style="font-size: 28px"></i>
                </div>

                <div>
                    <label class="form-label fw-bold d-block">Persetujuan Customer</label>
                    <i class="ti {{ $consultation->client_signed ? 'ti-check text-success' : 'ti-x text-danger' }}"
                    style="font-size: 28px"></i>
                </div>
            </div>

        </div>
    </div>
</div>
@endif
@endcan
