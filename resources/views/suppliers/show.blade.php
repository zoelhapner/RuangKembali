@extends('tablar::page')

@section('content')
<div class="container-xl" style="padding-top:80px">

    <div class="row align-items-center" style="padding-bottom:20px">
        <div class="col d-flex align-items-center">
            <a href="{{ route('suppliers.index') }}" class="btn btn-dark d-flex align-items-center">
                <i class="ti ti-arrow-left"></i>
            </a>
                <h2 class="page-title">Detail Mitra Supplier</h2>
        </div>
    </div>
    {{-- <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="page-title">Detail Mitra Supplier</h2>
        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div> --}}

    <div class="tabs-mobile-wrapper">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-personal" role="tab">
                    <i class="ti ti-user"></i> Detail Pribadi
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-loyalty" role="tab">
                    <i class="ti ti-map-pin"></i> Detail Usaha
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-catalogue" role="tab">
                    <i class="ti ti-shopping-cart"></i> Katalog Produk
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-employment" role="tab">
                    <i class="ti ti-briefcase"></i> Riwayat Pembayaran
                </a>
            </li>
        </ul>
    </div>
    <div class="tab-content">

        {{-- ================= TAB 1: PERSONAL ================= --}}
        <div class="tab-pane fade show active" id="tab-personal" role="tabpanel">

            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Informasi Personal</h3>
                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-outline-dark btn-sm" data-bs-toggle="tooltip" title="Ubah Detail">
                        <i class="ti ti-edit"></i>
                    </a>
                </div>

                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-3 text-center">
                            @if ($user->photo)
                            <img id="profilePreviewImage" src="{{ asset('storage/'.$user->photo) }}" alt="Profile" 
                                 class="rounded-3 shadow-sm border" width="150" height="150"
                                 style="object-fit: cover;">
                        @else
                            <div id="profilePreviewImage"
                                 class="rounded-3 shadow-sm bg-light d-flex align-items-center justify-content-center"
                                 style="width:150px; height:150px;">
                                 <i class="ti ti-user" style="font-size: 64px; color:#aaa;"></i>
                            </div>
                        @endif
                        </div>
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-md">
                                    <div class="text-muted small">Nama Lengkap</div>
                                    <div class="fw-bold">{{ $user->fullname ?? '-' }}</div>
                                </div>
                                <div class="col-md">
                                    <div class="text-muted small">Email</div>
                                    <div class="fw-bold">{{ $user->email ?? '-' }}</div>
                                </div>
                                {{-- <div class="col-md-6">
                                    <div class="text-muted small mt-2">Kategori supplier</div>
                                    <div class="fw-bold">{{ $supplier->readable_category ?? '-' }}</div>
                                </div> --}}
                                <div class="col-md">
                                    <div class="text-muted small">Telepon</div>
                                    <div class="fw-bold">{{ $user->phone ?? '-' }}</div>
                                </div>
                                
                                {{-- <div class="col-md-4">
                                    <div class="text-muted small mt-2">Status</div>
                                    <span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $supplier->status_text }}
                                    </span>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section: Address --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">Informasi Alamat</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="text-muted small">Alamat Lengkap</div>
                            <div class="fw-bold">{{ $user->address ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Kelurahan</div>
                            <div class="fw-bold">{{ $user->subDistrict->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Kecamatan</div>
                            <div class="fw-bold">{{ $user->district->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Kabupaten/Kota</div>
                            <div class="fw-bold">{{ $user->city->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Provinsi</div>
                            <div class="fw-bold">{{ $user->province->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Kode Pos</div>
                            <div class="fw-bold">{{ $user->postalCode->postal_code ?? '-' }}</div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Section: Bank Information (ganti HR section lama) --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">Informasi Bank</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-muted small">Nama Bank</div>
                            <div class="fw-bold">{{ $user->bank->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Nomor Rekening</div>
                            <div class="fw-bold">{{ $user->account_number ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Atas Nama</div>
                            <div class="fw-bold">{{ $user->account_holder ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab-catalogue" role="tabpanel">
            <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Katalog Produk</h3>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-3 mb-3">
                        <div class="d-flex gap-2 align-items-center">
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalAddProduct">
                                <i class="ti ti-plus"></i> Tambah Produk
                            </button>

                            <input type="text" id="searchCatalogueProduct"
                                class="form-control"
                                placeholder="🔍 Cari produk... (nama / SKU)"
                                style="width:260px;">
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary active" id="btnGrid">
                                <i class="ti ti-layout-grid"></i>
                            </button>
                            <button class="btn btn-outline-secondary" id="btnTable">
                                <i class="ti ti-list"></i>
                            </button>
                        </div>
                    </div>

                <div id="productViewWrapper">
                    <div class="row" id="productCardContainer">
                        @foreach($products as $product)
                            <div class="col-md-4 col-lg-3 mb-4 product-card"
                                data-name="{{ strtolower($product->name) }}"
                                data-sku="{{ strtolower($product->sku_code ?? '') }}">
                                <div class="card shadow-sm border-0 h-100">

                                    <div class="position-relative p-3 text-center product-image-wrapper">

                                        @if ($product->pivot->stock == 0)
                                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                                Stok Habis
                                            </span>
                                        @elseif ($product->pivot->stock <= 10)
                                            <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">
                                                Stok Terbatas
                                            </span>
                                        @endif

                                        @if ($product->photo)
                                            <img src="{{ asset('storage/' . $product->photo) }}"
                                                class="img-fluid rounded shadow-sm product-image"
                                                style="width:180px;height:180px;object-fit:cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center shadow-sm product-image"
                                                style="width:180px;height:180px;margin:auto;">
                                                <i class="ti ti-photo" style="font-size:32px;color:#999;"></i>
                                            </div>
                                        @endif

                                        <div class="product-hover-action">

                                            <form class="form-delete">
                                                @csrf
                                                <button type="button" 
                                                    data-pivot="{{ $product->pivot->id }}"
                                                    class="btn btn-sm btn-danger btn-delete">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>


                                    <div class="card-body">

                                        {{-- NAMA PRODUK --}}
                                        <h2 class="fw-bold">
                                            {{ $product->name }}
                                                    @if($product->pivot->label)
                                                        <span class="badge bg-warning text-dark ms-2">copy</span>
                                                    @endif
                                        </h2>

                                        @if($product->sku_code)
                                            <small class="text-muted">SKU: {{ $product->sku_code }}</small>
                                        @endif

                                        {{-- HARGA FINAL --}}
                                        <div class="mt-3">
                                            @php
                                                $pivot = $product->pivot;
                                                $buy   = $pivot->buying_prices;
                                                $sell  = $pivot->selling_prices;
                                                $spec  = $pivot->special_prices;

                                                // PRIORITAS HARGA:
                                                // 1. Harga Spesial
                                                // 2. Harga Jual
                                                // 3. Harga Beli (fallback)
                                                $final = $spec ?: ($sell ?: $buy);
                                            @endphp

                                            <div class="price-wrapper"
                                                data-supplier="{{ $supplier->id }}"
                                                data-product="{{ $product->id }}"
                                                data-pivot="{{ $product->pivot->id }}"
                                                data-url="{{ route('supplier-product.update-price') }}">

                                                {{-- MODE TAMPIL --}}
                                                <p class="mb-1 price-text">
                                                    <strong
                                                        class="text-dark price-label"
                                                        data-price="{{ $product->pivot->selling_prices }}">
                                                        Rp {{ number_format($product->pivot->selling_prices, 4, ',', '.') }}
                                                    </strong>

                                                    <button type="button"
                                                            class="btn btn-sm btn-dark ms-2 btn-edit-price">
                                                        <i class="ti ti-pencil"></i>
                                                    </button>
                                                </p>

                                                {{-- MODE EDIT --}}
                                                <div class="price-edit d-none">
                                                    <input type="text"
                                                        class="form-control price-input"
                                                        value="{{ $product->pivot->selling_prices }}">

                                                    <div class="mt-1 d-flex gap-1">
                                                        <button type="button" class="btn btn-xs btn-success btn-save-price">
                                                            <i class="ti ti-check"></i>
                                                        </button>

                                                        <button type="button" class="btn btn-xs btn-danger btn-cancel-price">
                                                            <i class="ti ti-x"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <p class="mb-1">
                                                <strong class="text-dark">Stok: {{ $product->pivot->stock }}</strong>
                                            </p>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div id="productTableContainer" class="d-none px-3">

                        <table class="table table-hover align-middle" id="productTable">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Produk</th>
                                <th>Kode SKU</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Label</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- @foreach($products as $product)
                                <tr>
                                    {{-- <td width="80">
                                        @if ($product->photo)
                                            <img src="{{ asset('storage/' . $product->photo) }}" width="60" class="rounded">
                                        @else
                                            <div class="bg-light rounded text-center" style="width:60px;height:60px;line-height:60px;">
                                                <i class="ti ti-photo"></i>
                                            </div>
                                        @endif
                                    </td> 
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->sku_code }}</td>
                                    <td>Rp {{ number_format($product->pivot->selling_prices) }}</td>
                                    <td>{{ $product->pivot->stock }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
                                        
                                        {{-- <div class="small text-muted">
                                            @if($sell)
                                                Harga Jual: Rp {{ number_format($sell) }} <br>
                                            @endif

                                            @if($spec)
                                                Harga Spesial: Rp {{ number_format($spec) }} <br>
                                            @endif

                                            Harga Beli: Rp {{ number_format($buy) }}
                                        </div> --}}

        <div class="tab-pane fade" id="tab-loyalty" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">Detail Usaha</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-muted small">Nama Usaha</div>
                            <div class="fw-bold">{{ $supplier->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Nomor Handphone</div>
                            <div class="fw-bold">{{ $supplier->phone ?? '-' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">Alamat Lengkap</div>
                            <div class="fw-bold">{{ $supplier->address ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted small">Kelurahan</div>
                            <div class="fw-bold">{{ $supplier->subDistrict->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Kecamatan</div>
                            <div class="fw-bold">{{ $supplier->district->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Kabupaten/Kota</div>
                            <div class="fw-bold">{{ $supplier->city->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Provinsi</div>
                            <div class="fw-bold">{{ $supplier->province->name ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted small">Kode Pos</div>
                            <div class="fw-bold">{{ $supplier->postalCode->postal_code ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Catatan</div>
                            <div class="fw-bold">{{ $supplier->notes ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <div class="tab-pane fade" id="tab-employment" role="tabpanel">
            <div class="card">
                <div class="card-body text-center text-muted">
                    <em>Belum ada data riwayat pembayaran.</em>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade" id="modalAddProduct" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- PENCARIAN -->
                <label class="form-label">Cari Produk</label>
                <input type="text" id="searchProduct" class="form-control" placeholder="Ketik nama produk...">

                <!-- LIST HASIL CARI -->
                <div id="searchResult" class="border rounded mt-2 p-2"
                     style="max-height:230px; overflow-y:auto; display:none;">
                </div>

                <!-- FORM PRODUK BARU -->
                <form id="formCreateProduct"  enctype="multipart/form-data" style="display:none;">
                    @csrf
                    @include('products.partials.product-form')
                </form>

                <hr>

                <!-- FORM PRODUK SUPPLIER -->
                <form id="formSupplier">
                    @csrf

                    <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                    <input type="hidden" name="product_id" id="product_id">

                    <div id="supplierSelectedProduct" class="alert alert-info" style="display:none;">
                        Produk dipilih: <strong id="selectedProductName"></strong>
                    </div>

                    <div id="supplierFormArea" style="display:none;">
                        <div class="mb-3">
                            <label>Stok</label>
                            <input type="number" class="form-control" name="stock" required>
                        </div>

                        <div class="mb-3">
                            <label>Harga Dasar Supplier</label>
                            <input type="text" class="form-control" id="base_price"
                                placeholder="Masukkan harga dasar">
                        </div>

                        <div class="mb-3">
                            <label>PPN (%)</label>
                            <input type="number" class="form-control" id="tax" name="tax_percentage">
                        </div>

                        <div class="mb-3">
                            <label>Diskon</label>
                            <input type="number" class="form-control" id="discount" name="discount">
                            <small class="text-muted">
                                ≤ 100 = persen | > 100 = rupiah
                            </small>
                        </div>

                        <div class="mb-3">
                            <label>Harga Final</label>
                            <input type="text"
                            class="form-control bg-light fw-bold"
                            id="final_price_display"
                            readonly>

                        <input type="hidden"
                            id="final_price"
                            name="selling_prices"
                            required>
                        </div>

                        <div class="alert alert-secondary small" id="priceFormulaPreview" style="display:none;"></div>

                        <button type="button" class="btn btn-dark w-100" id="btnSaveSupplierProduct">
                            Simpan Produk Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')

<script>
$(document).ready(function () {
    function initSelect2InModal() {
        $('#modalAddProduct .select2').each(function () {

            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }

            $(this).select2({
                dropdownParent: $('#modalAddProduct .modal-content'),
                width: '100%'
            });
        });
    }

    $('#modalAddProduct').on('shown.bs.modal', function () {
        initSelect2InModal();

        $('#searchProduct').val('');
        $('#searchResult').hide().html('');

        let form = document.getElementById('formCreateProduct');
        if (form) {
            $(form).hide();
            form.reset();
        }

        $('#supplierFormArea, #supplierSelectedProduct').hide();
        $("#product_id").val('');

        $('#formCreateProduct input, #formCreateProduct select, #formCreateProduct textarea')
            .prop("readonly", false)
            .prop("disabled", false);

        $('#previewImage').replaceWith(`
            <div id="previewImage"
                class="rounded-3 shadow-sm bg-light d-flex align-items-center justify-content-center"
                style="width:150px;height:150px;">
                <i class="ti ti-photo" style="font-size:64px;color:#aaa;"></i>
            </div>
        `);
    });

    $('#searchProduct').on('keyup', function () {
        let keyword = $(this).val().trim();

        if (keyword.length < 1) {
            $('#searchResult').hide();
            $('#formCreateProduct').hide();
            $('#supplierFormArea').hide();
            $("#product_id").val("");
            return;
        }

        $.get("{{ route('supplier.searchProduct') }}", { keyword }, function (res) {

            if (!res.found) {
                $('#formCreateProduct')[0].reset();
                $('#previewImage').replaceWith(`
                    <div id="previewImage"
                        class="rounded-3 shadow-sm bg-light d-flex align-items-center justify-content-center"
                        style="width:150px;height:150px;">
                        <i class="ti ti-photo" style="font-size:64px;color:#aaa;"></i>
                    </div>
                `);
                $("#formCreateProduct")
                .find("input, select, textarea")
                .not("[name='unit_1_value']")
                .prop("readonly", false)
                .prop("disabled", false);
                $('#searchResult').hide();
                $('#formCreateProduct').show();
                $('#supplierFormArea').show();
                $("#product_id").val("");

                $("#selectedProductName").text("Produk Baru");
                $("#supplierSelectedProduct").show();
                return;
            }

            // PRODUK DITEMUKAN
            $('#formCreateProduct').hide();
            $('#supplierFormArea').hide();
            $('#supplierSelectedProduct').hide();

            $('#searchResult').html(res.html).show();
        });
    });

    $(document).on("click", "#searchResult .product-item", function () {

        let id = $(this).data("id");
        $("#searchResult").hide().html("");
        $("#searchProduct").val("");

        $.get("/supplier/product-detail/" + id, function (p) {

            // SET PRODUCT_ID
            $("#product_id").val(p.id);

            $("#selectedProductName").text(p.name);
            $("#supplierSelectedProduct").show();

            // DETAIL FORM PRODUCT (readonly)
            $('#formCreateProduct').show();

            $("[name='name']").val(p.name).prop("readonly", true);
            $("[name='description']").val(p.description).prop("readonly", true);
            $("[name='sku_code']").val(p.sku_code).prop("readonly", true);
            $("[name='status']").val(p.status).prop("readonly", true);
            $("[name='brand_id']").val(p.brand_id).trigger('change').prop("disabled", true);
            $("[name='category_id']").val(p.category_id).trigger('change').prop("disabled", true);
            $("[name='type_id']").val(p.type_id).trigger('change').prop("disabled", true);
            $("[name='colors']").val(p.colors).trigger('change').prop("disabled", true);


            $("[name='size']").val(p.size).prop("readonly", true);
            $("[name='volume']").val(p.volume).prop("readonly", true);

            $("[name='unit_1_name']").val(p.unit_1_name).prop("readonly", true);
            $("[name='unit_1_value']").val(p.unit_1_value).prop("readonly", true);

            $("[name='unit_2_name']").val(p.unit_2_name).prop("readonly", true);
            $("[name='unit_2_value']").val(p.unit_2_value).prop("readonly", true);

            $("[name='unit_3_name']").val(p.unit_3_name).prop("readonly", true);
            $("[name='unit_3_value']").val(p.unit_3_value).prop("readonly", true);

            $("[name='unit_4_name']").val(p.unit_4_name).prop("readonly", true);
            $("[name='unit_4_value']").val(p.unit_4_value).prop("readonly", true);

            if ($("#previewImage").is("img")) {
                $("#previewImage").attr("src", p.photo_url);
            } else {
                $("#previewImage").replaceWith(`
                    <img class="previewImage"
                        src="${p.photo_url}"
                        class="rounded-3 shadow-sm border"
                        width="150" height="150"
                        style="object-fit:cover;">
                `);
            }

            $("[name='selling_prices']").val(p.default_selling_prices);
            $("[name='discount']").val(p.default_discount);
            $("[name='tax_percentage']").val(p.tax_percentage);

            $('#base_price').val(formatRupiah(p.default_selling_prices));
            $('#tax').val(p.tax_percentage);
            $('#discount').val(p.default_discount);

            setTimeout(() => recalculatePrice(), 200);

            $('#supplierFormArea').show();
        });
    });

    $("#btnSaveSupplierProduct").click(function (e) {
    e.preventDefault();

    let productId = $("#product_id").val();

    if (!productId || productId === "") {

        let formCreate = document.getElementById("formCreateProduct");
        let formData = new FormData(formCreate);

        $.ajax({
            url: "{{ route('products.store.ajax') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {

                if (!res.success) {
                    alert("Gagal membuat produk baru.");
                    return;
                }
                // SET ID PRODUK BARU
                $("#product_id").val(res.product_id);

                submitSupplierProduct();
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert("Gagal membuat produk.");
            }
        });

    } else {
        // ✅ INI YANG HILANG DI KODE KAMU
        submitSupplierProduct();
    }
    });
    function submitSupplierProduct() {
        let form = $("#formSupplier");

        $.ajax({
            url: "{{ route('supplier.products.store') }}",
            method: "POST",
            data: form.serialize(),
            success: function (res) {
                if (res.success) location.reload();
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message ?? "Gagal menyimpan");
            }
        });
    }
});
</script>

<script>

function formatRupiah(angka) {

    angka = Number(angka);

    if (isNaN(angka)) return '0';

    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 4,
        maximumFractionDigits: 4
    }).format(angka);
}
function formatInputNumber(value) {

    if (!value) return '';

    // Hapus semua selain angka dan koma
    value = value.replace(/[^\d,]/g, '');

    // Hanya boleh ada satu koma
    const parts = value.split(',');

    let integer = parts[0];
    let decimal = parts.length > 1 ? parts.slice(1).join('') : '';

    // Maksimal 4 digit desimal
    decimal = decimal.substring(0, 4);

    // Format ribuan
    integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    return decimal !== ''
        ? integer + ',' + decimal
        : integer;
}
function cleanNumber(value) {

    if (!value) return 0;

    value = value.toString();

    value = value.replace(/\./g, '');

    value = value.replace(',', '.');

    return parseFloat(value) || 0;
}

function recalculatePrice() {
    let basePrice = cleanNumber($('#base_price').val());

    let tax      = parseFloat($('#tax').val()) || 0;
    let discount = parseFloat($('#discount').val()) || 0;

    let formulaText = `Harga Dasar: Rp ${formatRupiah(basePrice)}`;
    let finalPrice  = basePrice;

    let taxValue = 0;
    let discountValue = 0;

    // ✅ TAMBAH PPN
    if (tax > 0) {
        taxValue = (tax / 100) * basePrice;
        finalPrice += taxValue;
        formulaText += ` + PPN (${tax}%) Rp ${formatRupiah(Math.round(taxValue))}`;
    }

    // ✅ KURANGI DISKON (CAMPURAN)
    if (discount > 0) {
        if (discount <= 100) {
            discountValue = (discount / 100) * finalPrice;
            formulaText += ` - Diskon (${discount}%) Rp ${formatRupiah(Math.round(discountValue))}`;
        } else {
            discountValue = discount;
            formulaText += ` - Diskon Rp ${formatRupiah(discountValue)}`;
        }

        // ✅ VALIDASI DISKON
        if (discountValue > finalPrice) {
            discountValue = finalPrice;
            $('#discount').val(finalPrice);
        }

        finalPrice -= discountValue;
    }

    // ✅ SAFETY
    if (finalPrice < 0) finalPrice = 0;

    let finalRounded = finalPrice;

    // ✅ TAMPILAN (PAKAI RUPIAH)
    $('#final_price_display').val(formatRupiah(finalRounded));

    // ✅ NILAI REAL KE DB (TANPA TITIK)
    $('#final_price').val(finalRounded);

    $('#priceFormulaPreview')
        .html(`<strong>Rumus:</strong><br>${formulaText} = <strong>Rp ${formatRupiah(finalRounded)}</strong>`)
        .hide();

}

// ✅ FORMAT RUPIAH SAAT KETIK
$('#base_price').on('input', function () {
    $(this).val(formatInputNumber($(this).val()));
    recalculatePrice();
});

// ✅ TRIGGER OTOMATIS
$(document).on('input change', '#tax, #discount', function () {
    recalculatePrice();
});
</script>

<script>
$(document).ready(function () {
    window.productDataTable = $('#productTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('supplier.products.datatable', $supplier->id) }}",

        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'products.name' },
            { data: 'sku_code', name: 'products.sku_code' },
            { data: 'selling_prices', name: 'product_supplier.selling_prices' },
            { data: 'stock', name: 'product_supplier.stock' },
            { data: 'name_with_label', name: 'products.name' },
            { data: 'aksi', orderable: false, searchable: false }
        ],
                language: {
                    search: "",
                    searchPlaceholder: "Cari produk...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "›",
                        previous: "‹"
                    }
                },

                initComplete: function () {
                    const input = $('.dt-search input');
                    input.removeClass('form-control-sm')
                        .addClass('form-control');
                }
    });
});
</script>
<script>

$(document).on('click', '.btn-edit-price', function () {
    
    let wrapper = $(this).closest('.price-wrapper');

    let input = wrapper.find('.price-input');

    // ambil angka asli dari text (misal: Rp 10.000 → 10000)
    let raw = wrapper.find('.price-label').data('price');
    if (raw == null || raw === '') {
        raw = cleanNumber(wrapper.find('.price-label').text());
    }
    input.val(formatInputNumber(
        raw.toString().replace('.', ',')
    ));

    wrapper.find('.price-text').addClass('d-none');
    wrapper.find('.price-edit').removeClass('d-none');
});

$(document).on('click', '.btn-cancel-price', function () {
    let wrapper = $(this).closest('.price-wrapper');

    wrapper.find('.price-edit').addClass('d-none');
    wrapper.find('.price-text').removeClass('d-none');
});

$(document).on('click', '.btn-save-price', function () {

    let btn = $(this);
    let wrapper = btn.closest('.price-wrapper');

    let productId = wrapper.data('product');
    let supplierId = wrapper.data('supplier');
    let pivotId   = wrapper.data('pivot');

    let newPrice  = cleanNumber(wrapper.find('.price-input').val());

    btn.prop('disabled', true);

    fetch(wrapper.data('url'), {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            pivot_id: pivotId,
            price: newPrice
        })
    })
    .then(res => res.json())
    .then(res => {

        if (res.success) {
            wrapper.find('.price-label')
                .data('price', res.price)
                .text('Rp ' + formatRupiah(res.price));
        }

        wrapper.find('.price-edit').addClass('d-none');
        wrapper.find('.price-text').removeClass('d-none');
    })
    .finally(() => {
        btn.prop('disabled', false);
    });
});
$(document).on('click', '.btn-duplicate', function() {
    let url = $(this).data('url');

    $.post(url, {
        _token: '{{ csrf_token() }}'
    }, function(res) {

        let table = $('#productTable').DataTable();

        table.ajax.reload(function() {

            if (res.highlight) {

                // cari row yang ada "-copy"
                $('#productTable tbody tr').each(function() {
                    let text = $(this).text();

                    if (text.includes('-copy')) {
                        $(this).addClass('table-success');

                        // efek glow sebentar
                        setTimeout(() => {
                            $(this).removeClass('table-success');
                        }, 3000);
                    }
                });

            }

        }, false);

    });
});
// buka edit
$(document).on('click', '.btn-edit-label', function () {
    let wrapper = $(this).closest('.label-wrapper');

    wrapper.find('.label-text').addClass('d-none');
    wrapper.find('.label-edit').removeClass('d-none');
});

// cancel
$(document).on('click', '.btn-cancel-label', function () {
    let wrapper = $(this).closest('.label-wrapper');

    wrapper.find('.label-edit').addClass('d-none');
    wrapper.find('.label-text').removeClass('d-none');
});

// save
$(document).on('click', '.btn-save-label', function () {

    let wrapper = $(this).closest('.label-wrapper');
    let pivotId = wrapper.data('pivot');
    let newLabel = wrapper.find('.label-input').val();

    fetch(wrapper.data('url'), {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            pivot_id: pivotId,
            label: newLabel
        })
    })
    .then(res => res.json())
    .then(res => {

        if (res.success) {
            wrapper.find('.label-text').html(
                (res.label || '-') + `
                <button class="btn btn-sm btn-dark ms-1 btn-edit-label">
                    <i class="ti ti-pencil"></i>
                </button>
                `
            );
        }

        wrapper.find('.label-edit').addClass('d-none');
        wrapper.find('.label-text').removeClass('d-none');
    });

});
</script>


<script>
let lastDeleted = null;

$(document).on("click", ".btn-delete", function(){

    let btn = $(this);
    let pivotId = btn.data("pivot");

    let row = btn.closest("tr");
    let card = btn.closest(".product-card");

    if(!pivotId){
        Swal.fire("Error", "ID tidak ditemukan", "error");
        return;
    }

    Swal.fire({
        title: "Hapus produk?",
        text: "Data yang dihapus tidak bisa dikembalikan",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, hapus",
        cancelButtonText: "Batal",
        reverseButtons: true
    }).then((result) => {

        if (!result.isConfirmed) return;

        // 🔥 simpan untuk undo
        lastDeleted = {
            pivotId,
            row: row.clone(),
            card: card.clone()
        };

        // 🔥 hapus dari UI dulu (optimistic UI)
        if(row.length && window.productDataTable){
            window.productDataTable.row(row).remove().draw();
        }

        if(card.length){
            card.remove();
        }

        // 🔥 request delete
        $.ajax({
            url: "/supplier-product/" + pivotId,
            type: "DELETE",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res){

                // 🔥 toast undo
                Swal.fire({
                    icon: 'success',
                            title: 'Berhasil!',
                            text: res.message ?? 'Data berhasil dihapus',
                            timer: 1500,
                            showConfirmButton: false
                }).then((undo) => {

                    if (undo.isConfirmed && lastDeleted) {

                        // 🔥 restore UI
                        if(lastDeleted.row && window.productDataTable){
                            window.productDataTable.row.add(lastDeleted.row).draw();
                        }

                        if(lastDeleted.card){
                            $("#productCardContainer").append(lastDeleted.card);
                        }

                        // 🔥 optional: kirim request restore ke backend
                        // $.post("/supplier-product/restore/" + pivotId, {
                        //     _token: "{{ csrf_token() }}"
                        // });

                        // lastDeleted = null;
                    }
                });

            },
            error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Data tidak bisa dihapus'
                        });
                        console.log(xhr.responseText);
                    }
        });

    });

});
</script>
<script>
document.getElementById('searchCatalogueProduct').addEventListener('keyup', function () {
    let keyword = this.value.toLowerCase();

    let isTableMode = !document.getElementById('productTableContainer').classList.contains('d-none');

    if (isTableMode) {
        return;
    }

    document.querySelectorAll('.product-card').forEach(function(card) {
        let name = (card.dataset.name || '').toLowerCase();
        let sku  = (card.dataset.sku || '').toLowerCase();

        if (name.includes(keyword) || sku.includes(keyword)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>


<script>
document.getElementById('btnGrid').addEventListener('click', function () {
    this.classList.add('active');
    btnTable.classList.remove('active');

    document.getElementById('productCardContainer').classList.remove('d-none');
    document.getElementById('productTableContainer').classList.add('d-none');
});

document.getElementById('btnTable').addEventListener('click', function () {
    this.classList.add('active');
    btnGrid.classList.remove('active');

    document.getElementById('productCardContainer').classList.add('d-none');
    document.getElementById('productTableContainer').classList.remove('d-none');

    setTimeout(() => {
        window.productDataTable.columns.adjust().draw();
    }, 100);
});
</script>

@endpush

@push('css')
    <style>
    .nav nav-tabs .nav-item .nav-link {
    align-items: center;
    gap: 10px;
    margin: 0 10px;
}

    .nav-link i {
    align-items: center;
    gap: 5px;
    margin: 0 10px;
}
    .page-title {
    margin: 0 10px;
}
</style>
@endpush
