@extends('tablar::page')

@section('content')

<style>
/* Timeline container */
.timeline {
    position: relative;
    margin-left: 25px;
    margin-top: 10px;
}

.timeline::before {
    content: "";
    position: absolute;
    left: 7px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #d0d4db;
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    position: relative;
    margin-bottom: 25px;
}

.timeline-marker {
    width: 17px;
    height: 17px;
    border-radius: 50%;
    border: 2px solid #6c757d;
    background: #fff;
    margin-right: 15px;
    z-index: 10;
}

/* Marker Colors */
.timeline-marker.done {
    background: #28a745;
    border-color: #28a745;
}
.timeline-marker.current {
    background: #ffc107;
    border-color: #ffc107;
}
.timeline-marker.pending {
    background: #fff;
}

/* Timeline row = 3 columns */
.timeline-row {
    display: flex;
    width: 100%;
    background: #ffffff;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid #e6e8ec;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    gap: 15px;
}

/* 3 equal columns using flex */
.timeline-col {
    flex: 1;
}

.timeline-link {
    text-decoration: none;
    color: #212529;
}
.timeline-link:hover {
    text-decoration: underline;
    color: #0d6efd;
}

/* Column widths can be adjusted */
.level-col { flex: 2; }     /* lebih besar untuk nama level */
.employee-col { flex: 2; }  /* karyawan */
.status-col { flex: 1; }    /* badge */
</style>

<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col d-flex align-items-center">
                <a href="{{ route('projects.index') }}" class="btn btn-dark d-flex align-items-center" style="margin-left: 20px;">
                    <i class="ti ti-arrow-left"></i>
                </a>
                
                    <h2 class="page-title mb-0">Detail Proyek</h2>
                
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-4">
    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Informasi Proyek</h3>
            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-outline-dark btn-sm">
                <i class="ti ti-edit"></i> Ubah Detail
            </a>
    </div>
    <div class="card-body">
        <div class="row g-4">

                                <div class="col-md-4">
                                    <label class="fw-semibold">Nama Proyek</label>
                                    <input type="text" class="form-control" value="{{ $project->project_name }}" disabled>
                                </div>

                                <div class="col-md-4">
                                    <label class="fw-semibold">Customer</label>
                                    <input type="text" class="form-control" value="{{ $project->customer->display_name }}" disabled>
                                </div>

                                <div class="col-md-4">
                                    <label class="fw-semibold">Karyawan</label>
                                    <input type="text" class="form-control" value="{{ $project->employee->display_name }}" disabled>
                                </div>

                                <div class="col-12 mt-3">
                                    <label class="fw-semibold">Alamat Lokasi</label>
                                    <textarea class="form-control" rows="3" disabled>{{ $project->project_location }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-semibold">Provinsi</label>
                                    <input type="text" class="form-control" value="{{ $project->province->name }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-semibold">Kabupaten/Kota</label>
                                    <input type="text" class="form-control" value="{{ $project->city->name }}" disabled>
                                </div>
                                <div class="col-md-5">
                                    <label class="fw-semibold">Kecamatan</label>
                                    <input type="text" class="form-control" value="{{ $project->district->name }}" disabled>
                                </div>
                                <div class="col-md-5">
                                    <label class="fw-semibold">Kelurahan</label>
                                    <input type="text" class="form-control" value="{{ $project->subDistrict->name }}" disabled>
                                </div>
                                <div class="col-md-2">
                                    <label class="fw-semibold">Kode Pos</label>
                                    <input type="text" class="form-control" value="{{ $project->postalCode->postal_code }}" disabled>
                                </div>

                            </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header fw-bold">Progress Tahapan Proyek</div>
        <div class="card-body">

            @php
                $levels = $project->levels->sortBy('level_order');
            @endphp
                <div class="timeline">

                    @foreach($levels as $level)

                        @php
                            $isCurrent = isset($currentLevel) && $currentLevel && $currentLevel->id === $level->id;
                            $isCompleted = $level->is_completed;
                            $employeeName = $level->employee?->user?->fullname ?? 'Belum ditentukan';
                        @endphp

                        <div class="timeline-item">

                            {{-- Marker --}}
                            <div class="timeline-marker
                                {{ $isCompleted ? 'done' : ($isCurrent ? 'current' : 'pending') }}">
                            </div>

                            {{-- Content --}}
                            <div class="timeline-row">

                                {{-- Kolom 1: Nama Level --}}
                                <div class="timeline-col level-col">
                                    <a href="#{{ Str::slug($level->level_name) }}" class="timeline-link">
                                        <strong>{{ $level->level_order }}. {{ $level->level_name }}</strong>
                                    </a>
                                </div>

                                {{-- Kolom 2: Karyawan --}}
                                <div class="timeline-col employee-col">
                                    <span class="{{ $level->employee ? 'fw-semibold' : 'text-muted fst-italic' }}">
                                        {{ $employeeName }}
                                    </span>
                                </div>

                                {{-- Kolom 3: Status Badge --}}
                                <div class="timeline-col status-col text-end">
                                    @if($isCompleted)
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($isCurrent)
                                        <span class="badge bg-warning text-white">Sedang Berjalan</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Dimulai</span>
                                    @endif
                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

        </div>
</div>

<div id="konsultasi" class="card mb-4">
    <div class="card-header fw-bold">Tahap 1: Konsultasi</div>
    <div class="card-body">

        @if(!$consultation)
            {{-- Jika konsultasi belum diisi --}}
            <p class="text-muted">
                Anda belum mengisi Form Konsultasi. Klik tombol di bawah ini untuk memulai.
            </p>

            <a href="{{ route('projects.create', $project->id) }}" class="btn btn-dark btn-lg">
                📝 Isi Form Konsultasi
            </a>

        @else
            {{-- Konsultasi Sudah diisi --}}
            <p><strong>Form Konsultasi sudah diisi.</strong></p>

            <a href="{{ route('consultations.show', $consultation->id) }}" class="btn btn-success me-2">
                ✔ Lihat Form Konsultasi
            </a>

            <a href="{{ route('consultations.pdf', $consultation->id) }}" 
                class="btn btn-outline-primary" target="_blank">
                🖨 Cetak PDF
            </a>
        @endif

    </div>
</div>

<div id="rencana-survei" class="card mb-4">
    <div class="card-header fw-bold">Tahap 2: Rencana Survei</div>
    <div class="card-body">

        @if(!$planning)
            {{-- Jika Rencana Survei belum diisi --}}
            <p class="text-muted">
                Anda belum mengisi Form Rencana Survei. Klik tombol di bawah ini untuk memulai.
            </p>

            <a href="{{ route('projects.create', $project->id) }}" class="btn btn-dark btn-lg">
                📝 Isi Form Rencana Survei
            </a>

        @else
            {{-- Rencana Survei Sudah diisi --}}
            <p><strong>Form Rencana Survei sudah diisi.</strong></p>

            <a href="{{ route('plannings.show', $planning->id) }}" class="btn btn-success me-2">
                ✔ Lihat Form Rencana Survei
            </a>

            <a href="{{ route('plannings.pdf', $planning->id) }}" 
                class="btn btn-outline-primary" target="_blank">
                🖨 Cetak PDF
            </a>
        @endif

    </div>
</div>

<div id="survei" class="card mb-4">
    <div class="card-header fw-bold">Tahap 3: Survei</div>
    <div class="card-body">

        @if(!$planning)
            {{-- Jika Rencana Survei belum diisi --}}
            <p class="text-muted">
                Anda belum mengisi Form Survei. Klik tombol di bawah ini untuk memulai.
            </p>

            <a href="{{ route('projects.create', $project->id) }}" class="btn btn-dark btn-lg">
                📝 Isi Form Survei
            </a>

        @else
            {{--  Survei Sudah diisi --}}
            <p><strong>Form  Survei sudah diisi.</strong></p>

            <a href="{{ route('surveys.show', $planning->id) }}" class="btn btn-success me-2">
                ✔ Lihat Form  Survei
            </a>

            <a href="{{ route('plannings.pdf', $planning->id) }}" 
                class="btn btn-outline-primary" target="_blank">
                🖨 Cetak PDF
            </a>
        @endif

    </div>
</div>

{{-- @if($currentLevel && strtolower($currentLevel->level_name) !== 'konsultasi')
<div class="card mb-4">
    <div class="card-header fw-bold">Aksi Tahap Selanjutnya</div>
    <div class="card-body">

        @php
            $next = $project->levels
                ->where('is_completed', false)
                ->sortBy('level_order')
                ->first();
        @endphp

        @if($next)
            <h5>Tahap berikutnya: <strong>{{ $next->level_name }}</strong></h5>
            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#confirmStartModal">
                → Mulai Tahap {{ $next->level_name }}
            </button>
        @endif

    </div>
</div> --}}

{{-- <div class="modal fade" id="confirmStartModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Mulai Tahap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="fs-5">
                    Apakah Anda yakin ingin memulai tahap:
                    <strong class="text-primary">{{ $next->level_name }}</strong>?
                </p>

                <p class="text-muted">
                    Setelah Anda memulai tahap ini, progress proyek akan berpindah ke tahap berikutnya.
                </p>
            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <form action="{{ route('projects.surveys.create', $next->id) }}" method="GET">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Ya, Lanjutkan
                    </button>
                </form>

            </div>

        </div>
    </div>
</div>

@endif --}}
@endsection

@push('js')
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.timeline-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
        });
    });
});
</script>
@endpush

