@extends('tablar::page')

@section('content')
{{-- <div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col d-flex align-items-center">
                <a href="{{ route('projects.show', $project->id) }}" class="btn btn-dark d-flex align-items-center" style="margin-left: 20px;">
                    <i class="ti ti-arrow-left"></i>
                </a>
                
                    <h2 class="page-title mb-0">Detail Konsultasi</h2>
                
            </div>
        </div>
    </div>
</div> --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between">
        <div>Detail Form Konsultasi</div>
        <div>
            <a href="{{ route('plannings.pdf', $planning->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank">Download PDF</a>
        </div>
    </div>
    <div class="card-body">
        <h2>Nama Proyek: {{ $planning->project->project_name }}</h2>
        <p><strong>Tanggal Rencana Survei:</strong> {{ $planning->planning_date }}</p>
        <p><strong>Jam Rencana Survei:</strong> {{ $planning->planning_time }}</p>
        <p><strong>Alamat :</strong> {{ $planning->survey_address }}</p>
        <p><strong>Provinsi :</strong> {{ $planning->province->name }}</p>
        <p><strong>Kabupaten/Kota :</strong> {{ $planning->city->name }}</p>
        <p><strong>Kecamatan :</strong> {{ $planning->district->name }}</p>
        <p><strong>Kelurahan :</strong> {{ $planning->subDistrict->name }}</p>
        <p><strong>Kode Pos :</strong> {{ $planning->postalCode->name }}</p>
        <p><strong>Nama Karyawan :</strong> {{ $planning->project->employee->user->fullname }}</p>

    </div>
</div>
@endsection
