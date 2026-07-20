@extends('tablar::page')

@section('content')
<div class="page-body">
    <div class="container-xl">

        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <h2 class="fw-bold mb-4">Tambah Paket RAB</h2>

                <form action="{{ route('rab-packages.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Paket</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Harga / m²</label>
                            <input type="number" name="price_meter" class="form-control" required>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button class="btn btn-dark px-4">Simpan</button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection
