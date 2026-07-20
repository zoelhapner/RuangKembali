@extends('tablar::page')

@section('content')
<div class="page-body">
    <div class="container-xl">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-5">

                <h2 class="fw-bold mb-4">Edit Paket: {{ $rabPackage->name }}</h2>

                {{-- Update paket --}}
                <form action="{{ route('rab-packages.update', $rabPackage->id) }}"
                      method="POST" class="mb-5">

                    @csrf @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Nama Paket</label>
                            <input type="text" name="name" class="form-control" 
                                value="{{ $rabPackage->name }}" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Harga / m²</label>
                            <input type="number" name="price_meter" class="form-control" 
                                value="{{ $rabPackage->price_meter }}" required>
                        </div>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-dark px-4">Update Paket</button>
                    </div>
                </form>

                <hr>

                {{-- FORM TAMBAH ITEM --}}
                <h3 class="fw-bold mb-3">Tambah Item Rincian</h3>

                <form action="{{ route('rab-packages.items.store', $rabPackage->id) }}"
                      method="POST" class="mb-5">

                    @csrf

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Kategori</label>
                            <input type="text" name="category" class="form-control">
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Nama Item</label>
                            <input type="text" name="item_name" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label d-block">Optional?</label>
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_optional" class="form-check-input">
                                <span class="form-check-label">Ya</span>
                            </label>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-dark w-100">Tambah</button>
                        </div>
                    </div>

                </form>

                <hr>

                {{-- TABLE ITEM --}}
                <h3 class="fw-bold mb-3">Daftar Item Paket</h3>

                @include('rab-packages.partials.items-table', [
                    'items' => $rabPackage->items
                ])

            </div>
        </div>

    </div>
</div>
@endsection
