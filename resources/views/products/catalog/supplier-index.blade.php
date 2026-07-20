@extends('tablar::page')

@section('content')

<div class="container-fluid">

    <h4 class="mb-4">📦 Katalog Produk Supplier (Semua Supplier)</h4>

    {{-- FILTER SECTION --}}
    <div class="card mb-4">
        <div class="card-body">

            <form method="GET" action="{{ route('catalog.supplier') }}">

                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Cari Produk</label>
                        <input type="text" name="search" class="form-control"
                               placeholder="Nama produk / SKU"
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">Semua Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-select">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select">
                            <option value="">Semua Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="mt-3 text-end">
                    <button class="btn btn-primary">
                        <i class="ti ti-search"></i> Filter
                    </button>
                    <a href="{{ route('catalog.supplier') }}" class="btn btn-secondary">Reset</a>
                </div>

            </form>

        </div>
    </div>

    {{-- TABLE SECTION --}}
    <div class="card">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="70">Foto</th>
                            <th>Nama Produk</th>
                            <th>SKU</th>
                            <th>Supplier</th>
                            <th>Harga Supplier</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th>Brand</th>
                            <th>Tipe</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td>
                                <img 
                                    src="{{ $item->product->photo ? asset('storage/' . $item->product->photo) : asset('noimage.png') }}"
                                    class="rounded"
                                    width="150" height="150"
                                >   
                            </td>

                            <td>
                                <strong>{{ $item->product->name }}</strong>
                            </td>

                            <td>{{ $item->product->sku_code }}</td>

                            <td>{{ $item->supplier->name }}</td>

                            <td>
                                Rp {{ number_format($item->selling_prices, 0, ',', '.') }}
                            </td>

                            <td>{{ $item->stock ?? 0 }}</td>

                            <td>{{ $item->product->category->name ?? '-' }}</td>

                            <td>{{ $item->product->brand->name ?? '-' }}</td>

                            <td>{{ $item->product->type->name ?? '-' }}</td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="ti ti-alert-circle"></i> Tidak ada produk ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    <div class="mt-3">
        {{ $items->withQueryString()->links() }}
    </div>

</div>

@endsection
