@extends('tablar::page')

@section('content')
<div class="page-body">
    <div class="container-xl">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-5">

                <h2 class="fw-bold mb-4">Edit Paket: {{ $ahspGroup->nama }}</h2>

                {{-- Update paket --}}
                <form action="{{ route('design-packages.update', $ahspGroup->id) }}"
                      method="POST" class="mb-5">

                    @csrf @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Nama Paket</label>
                            <input type="text" name="name" class="form-control" 
                                value="{{ $ahspGroup->nama }}" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Unit Pekerjaan</label>
                            <input type="number" name="nama_pekerjaan" class="form-control" 
                                value="{{ $ahspGroup->nama_pekerjaan }}" required>
                        </div>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-dark px-4">Update Paket</button>
                    </div>
                </form>

                <hr>

                {{-- FORM TAMBAH ITEM --}}
                <h3 class="fw-bold mb-3">Tambah Item Rincian</h3>

                <form action="{{ route('design-packages.items.store', $ahspGroup->id) }}"
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

                @include('ahsp-groups.partials.items-table', [
                    'ahsps' => $ahspGroup->ahsps
                ])

            </div>
        </div>

    </div>
</div>
@endsection
