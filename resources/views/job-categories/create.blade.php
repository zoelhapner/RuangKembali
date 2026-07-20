@extends('tablar::page')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col d-flex align-items-center">
                <a href="{{ route('job-categories.index') }}" class="btn btn-dark d-flex align-items-center">
                    <i class="ti ti-arrow-left"></i>
                </a>
                    <h2 class="page-title mb-0">Tambah Data AHSP</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="card shadow-sm border-0">
            <div class="card-body px-5 py-4">
                <form action="{{ route('job-categories.store') }}" method="POST">
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
                    <div class="row g-4">

                        {{-- Bidang --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Bidang</label>
                            <input type="text"
                                   name="bidang"
                                   class="form-control @error('bidang') is-invalid @enderror"
                                   value="{{ old('bidang') }}"
                                   required>
                            @error('bidang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kode Group --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Kode Group</label>
                            <input type="text"
                                   name="kode_group"
                                   class="form-control @error('kode_group') is-invalid @enderror"
                                   value="{{ old('kode_group') }}"
                                   required>
                            @error('kode_group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama Group --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Nama Group</label>

                            {{-- <select name="nama_group"
                                    class="form-select @error('nama_group') is-invalid @enderror"
                                    required>

                                <option value="">-- Pilih Group --</option>

                                @foreach($groups as $group)
                                    <option value="{{ $group }}"
                                        {{ old('nama_group') === $group ? 'selected' : '' }}>
                                        {{ $group }}
                                    </option>
                                @endforeach
                            </select> --}}
                                <input type="text"
                                   name="nama_group"
                                   class="form-control @error('nama_group') is-invalid @enderror"
                                   value="{{ old('nama_group') }}"
                                   required>

                            @error('nama_group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kode --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Kode</label>
                            <input type="text"
                                   name="kode"
                                   class="form-control @error('kode') is-invalid @enderror"
                                   value="{{ old('kode') }}"
                                   required>
                            @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kode Urut --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Kode Urut</label>
                            <input type="text"
                                   name="kode_urut"
                                   class="form-control @error('kode_urut') is-invalid @enderror"
                                   value="{{ old('kode_urut') }}"
                                   required>
                            @error('kode_urut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Satuan --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Satuan</label>
                            <input type="text"
                                   name="satuan"
                                   class="form-control @error('satuan') is-invalid @enderror"
                                   value="{{ old('satuan') }}"
                                   required>
                            @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama Pekerjaan --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nama Pekerjaan</label>
                            <input type="text"
                                   name="nama_pekerjaan"
                                   class="form-control @error('nama_pekerjaan') is-invalid @enderror"
                                   value="{{ old('nama_pekerjaan') }}"
                                   required>
                            @error('nama_pekerjaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="text-end mt-5">
                        <button type="submit" class="btn btn-dark px-4">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Data
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
