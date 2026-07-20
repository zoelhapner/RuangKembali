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
            <a href="{{ route('consultations.pdf', $consultation->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank">Download PDF</a>
        </div>
    </div>
    <div class="card-body">
        <h2>Nama Proyek: {{ $consultation->project->project_name }}</h2>
        <p><strong>Nama Customer:</strong> {{ $consultation->contact_name ?? $consultation->project->customer->user->fullname }}</p>
        
        <p><strong>Alamat :</strong> {{ $consultation->project->project_location }}</p>
        <p><strong>No. HP :</strong> {{ $consultation->contact_phone ?? $consultation->project->customer->user->phone }}</p>
        <p><strong>Ukuran tanah :</strong> {{ $consultation->site_area }}</p>
        <p><strong>Ukuran bangunan :</strong> {{ $consultation->building_area }}</p>
        <p><strong>Nama Karyawan :</strong> {{ $consultation->project->employee->user->fullname }}</p>


        <table class="table table-bordered">
            <thead><tr><th>No</th><th>Uraian</th><th>Keterangan</th></tr></thead>
            <tbody>
                @foreach($consultation->items as $i => $it)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $it->description }}</td>
                        <td>{{ $it->remark }}</td>
                    </tr>
                    <tr>
                        <td>{{ $it->notes }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($consultation->documentation)
            <img src="{{ asset('storage/'.$consultation->documentation) }}" width="150">
        @endif

    </div>
</div>
@endsection
