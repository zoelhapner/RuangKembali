@php
    $survey = $project->survey;
    $planningLevel = $project->levels->firstWhere('level_order', 3);

    $surveyEmployees = $planningLevel ? $planningLevel->employees : collect();
@endphp

@can('lihat data proyek')
@if(isset($survey))
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header fw-bold">
        Detail Survei
    </div>

    <div class="card-body">
        <div class="row g-4">

            <div class="col-md-4">
                <label class="fw-semibold">Nama Customer</label>
                <input type="text" class="form-control"
                       value="{{ $survey->contact_name }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="fw-semibold">Tanggal Survei</label>
                <input type="text" class="form-control"
                    value="{{ \Carbon\Carbon::parse($survey->survey_date)->format('d/m/Y') }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="fw-semibold">Waktu Survei</label>
                <input type="text" class="form-control"
                       value="{{ $survey->survey_time }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="fw-semibold">Petugas Survei</label>

                <div class="d-flex flex-wrap gap-2 mt-1">
                    @forelse($surveyEmployees as $emp)
                        <span class="badge bg-dark text-white px-3 py-2">
                            {{ $emp->display_name }}
                        </span>
                    @empty
                        <span class="text-muted">Tidak ada petugas survei</span>
                    @endforelse
                </div>
            </div>

            <div class="col-md-4">
                <label class="fw-semibold">Ukuran Tanah</label>
                <input type="text" class="form-control"
                       value="{{ $survey->site_area }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="fw-semibold">Ukuran Bangunan </label>
                <input type="text" class="form-control"
                       value="{{ $survey->building_area }}" readonly>
            </div>

            <div class="section-block mt-4">
                <div class="row g-4">
                    <div class="col-md-6 mb-4">
                        <h5 class="fw-bold">Foto Hasil Survei (Denah, Kondisi Lapangan, dll)</h5>
                        @if($survey->images->count())
                            <div class="row g-3 mt-2">
                                @foreach($survey->images as $img)
                                    <div class="col-6 col-md-3">
                                        <div class="border rounded shadow-sm p-1">
                                    <img src="{{ asset('storage/'.$img->file_path) }}"
                                        data-src="{{ asset('storage/'.$img->file_path) }}"
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
                    <div class="col-md-6 mb-4">
                        <h5 class="fw-bold">Dokumen Denah Existing</h5>
                        @if($survey->documents->count())
                            <ul class="list-group">
                                @foreach($survey->documents as $docs)
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
                </div>
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
                        @foreach($survey->items as $item)
                        <tr>
                            <td class="text-center">{{ $item->order_no }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->remark }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <h5 class="fw-bold">Foto Dokumentasi</h5>
                @if($survey->documentations->count())
                    <div class="row g-3 mt-2">
                        @foreach($survey->documentations as $doc)
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
                <textarea class="form-control" rows="3" readonly>{{ $survey->notes }}</textarea>
            </div>
            <div class="col-12 mt-3 d-flex justify-content-around text-center">
                <div>
                    <label class="form-label fw-bold d-block">Persetujuan Surveyor</label>
                    <i class="ti {{ $survey->consultant_signed ? 'ti-check text-success' : 'ti-x text-danger' }}"
                    style="font-size: 28px"></i>
                </div>

                <div>
                    <label class="form-label fw-bold d-block">Persetujuan Customer</label>
                    <i class="ti {{ $survey->client_signed ? 'ti-check text-success' : 'ti-x text-danger' }}"
                    style="font-size: 28px"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="imageModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.8); justify-content:center; align-items:center;">
    <span id="closeModal" style="position:absolute; top:20px; right:30px; color:white; font-size:30px; cursor:pointer;">&times;</span>
    <img id="modalImage" style="max-width:90%; max-height:90%;">
</div>

@endif
@endcan
