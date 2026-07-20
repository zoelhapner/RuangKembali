@extends('tablar::page')

@section('content')

<div class="container-xl mt-4">

    <h2 class="page-title">Katalog Produk</h2>

    <div class="row">

        @forelse($items as $item)

            @php
                // Foto fallback
                $img = $item['photo']
                        ? asset('storage/' . $item['photo'])
                        : asset('images/no-image.png');

                // Badge stok
                $stock = $item['stock'];

                if ($stock <= 0) {
                    $badge = ['bg-danger', 'Habis'];
                } elseif ($stock <= 5) {
                    $badge = ['bg-warning text-dark', 'Hampir Habis'];
                } else {
                    $badge = ['bg-success', 'Tersedia'];
                }

                // Sumber barang
                // $sourceLabel = $item['source'] === 'warehouse'
                //     ? 'Gudang Antosa Architect'
                //     : 'Supplier: ' . $item['supplier'];
            @endphp


            <div class="col-6 col-md-4 col-xl-3 mb-4">
                <div class="card h-100 shadow-sm border-0">

                    {{-- FOTO PRODUK --}}
                    <div class="p-3 text-center position-relative">

                        <img src="{{ $img }}"
                             class="rounded shadow-sm"
                             style="width: 180px; height: 180px; object-fit: cover;">

                        {{-- BADGE STOK --}}
                        <span class="badge position-absolute top-0 start-0 m-2 {{ $badge[0] }}">
                            {{ $badge[1] }}
                        </span>

                    </div>

                    {{-- CARD BODY --}}
                    <div class="card-body">

                        {{-- NAMA PRODUK --}}
                        <h3 class="fw-bold mb-1" style="font-size: 1rem;">
                            {{ $item['name'] }}
                        </h3>

                        {{-- SKU --}}
                        @if($item['sku'])
                            <div class="text-muted small mb-2">
                                SKU: {{ $item['sku'] }}
                            </div>
                        @endif

                        {{-- KATEGORI & BRAND --}}
                        <div class="small text-muted mb-3">
                            {{ $item['brand'] }} · {{ $item['category'] }}
                        </div>

                        {{-- HARGA --}}
                        <div class="mb-2">

                            <span class="fw-bold text-dark"
                                style="font-size: 1.1rem;">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </span>

                            {{-- Jika ada original price, tampilkan strikethrough --}}
                            @if($item['selling_price'])
                                <span class="text-muted text-decoration-line-through ms-1">
                                    Rp {{ number_format($item['selling_price'], 0, ',', '.') }}
                                </span>
                            @endif
                        </div>

                        {{-- SUMBER PRODUK --}}
                        {{-- <div class="small text-muted">
                            <i class="ti ti-building-store"></i> {{ $sourceLabel }}
                        </div> --}}

                    </div>

                </div>
            </div>

        @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="ti ti-info-circle" style="font-size: 32px;"></i>
                <p class="mt-2">Belum ada produk di katalog.</p>
            </div>
        @endforelse
    </div>

</div>

@endsection
