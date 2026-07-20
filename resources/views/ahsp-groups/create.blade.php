{{-- @extends('tablar::page')

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
@endsection --}}



@extends('tablar::page')

@section('content')
<div class="container">
    <h4>Tambah Group AHSP</h4>

    <form method="POST" action="{{ route('ahsp-groups.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Bidang</label>
            <input type="text" name="bidang" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kode</label>
            <input type="text" name="kode" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Group</label>
            <textarea name="nama" class="form-control" rows="2" required></textarea>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('ahsp-groups.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
