@extends('tablar::page')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header fw-bold">
            Import Upah Pekerjaan
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('job-categories.import-upah') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="mb-3">
                    <label class="form-label required">
                        File Excel Upah
                    </label>
                    <input type="file"
                           name="file"
                           class="form-control"
                           accept=".xls,.xlsx,.csv"
                           required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Import
                    </button>

                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
