@if($products->count() == 0)

    <div class="alert alert-warning text-center mb-0">
        <i class="ti ti-alert-circle"></i> Produk tidak ditemukan.
    </div>

@else

    <div class="list-group">

        @foreach($products as $product)

            <div class="list-group-item list-group-item-action py-3 product-item"
                data-id="{{ $product->id }}"
                data-name="{{ $product->name }}"
                 style="cursor:pointer;">

                <div class="d-flex align-items-center">
                    @if ($product->photo)
                        <img src="{{ asset('storage/' . $product->photo) }}"
                            class="rounded me-3"
                            style="width: 60px; height: 60px; object-fit: cover;">
                    @else
                        <div class="rounded me-3 bg-light d-flex align-items-center justify-content-center"
                            style="width:60px; height:60px;">
                            <i class="ti ti-photo" style="font-size:28px; color:#999;"></i>
                        </div>
                    @endif


                    <div class="flex-fill">

                        {{-- NAMA PRODUK --}}
                        <h6 class="mb-1 fw-bold">{{ $product->name }}</h6>

                        {{-- SKU --}}
                        <small class="text-muted d-block">SKU: {{ $product->sku_code }}</small>

                        {{-- MEREK / KATEGORI --}}
                        <small class="text-muted">
                            {{ $product->brand->name ?? 'Tanpa Merek' }} · 
                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                        </small>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

@endif
