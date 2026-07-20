{{-- Penting --}}
@extends('tablar::page')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                  
                        <a href=" {{ route("labor_costs.index") }} " class="btn btn-dark d-none d-sm-inline-block" >
                            Kembali
                        </a>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <p class="text-center mb-4" style="font-size: 1.5rem; font-weight: 400; font-family: 'Poppins', sans-serif;">
                                Tambah Data Harga Tenaga
                            </p>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('labor_costs.store', ) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Kode</label>
                                    <input type="text" class="form-control" name="code">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jenis Pekerjaan</label>
                                    <input type="text" name="description" class="form-control" value="{{ old('description') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Unit</label>
                                    <input type="text" name="unit" class="form-control" value="{{ old('unit') }}">
                                </div>

                                 <div class="mb-3">
                                    <label class="form-label">Harga dasar</label>
                                    <input type="number" name="base_unit_price" class="form-control" value="{{ old('base_unit_price') }}">
                                </div>

                                 <div class="mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-dark">Simpan Perubahan</button>
                                </div>
                            </form>
 

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection