{{-- <div class="modal fade" id="modalAddProduct" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <form action="{{ route('supplier.products.store', $supplier->id) }}" 
                  method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Foto Produk</label>
                            <input type="file" name="photo" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kode SKU</label>
                            <input type="text" name="sku_code" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Merk</label>
                            <select class="form-select" name="brand_id">
                                <option value="">-- Pilih --</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kategori</label>
                            <select class="form-select" name="category_id">
                                <option value="">-- Pilih --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipe Produk</label>
                            <select class="form-select" name="type_id">
                                <option value="">-- Pilih --</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Deskripsi Produk</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>

                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold">Harga Produk (Supplier)</h5>

                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <label class="form-label">Harga Beli</label>
                            <input type="number" step="0.01" name="buying_price" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Harga Jual</label>
                            <input type="number" step="0.01" name="selling_price" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Harga Spesial</label>
                            <input type="number" step="0.01" name="special_price" class="form-control">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-dark">Simpan Produk</button>
                </div>

            </form>

        </div>
    </div>
</div> --}}

<!-- MODAL ADD PRODUCT -->
<div class="modal fade" id="modalAddProduct" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Produk Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                {{-- SEARCH SECTION --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Cari Nama Produk</label>
                    <input type="text" class="form-control" id="addSearchProductInput" placeholder="Ketik nama produk...">
                </div>

                <div id="addSearchResults" class="mb-4"></div>

                <hr>

                {{-- SELECTED PRODUCT SECTION --}}
                <div id="selectedProductBox" class="d-none">
                    <h5 class="fw-bold mb-3">Produk Ditemukan</h5>

                    <div class="border rounded p-3 bg-light mb-3">
                        <div class="d-flex">
                            <img id="selectedProductImage" src="" class="rounded me-3"
                                 style="width: 80px; height:80px; object-fit:cover;">
                            <div>
                                <h6 id="selectedProductName" class="fw-bold mb-1"></h6>
                                <small id="selectedProductSku" class="text-muted"></small>
                            </div>
                        </div>
                    </div>

                    <form id="attachPriceForm" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Harga Beli</label>
                                <input type="number" name="buying_price" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Harga Jual</label>
                                <input type="number" name="selling_price" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Harga Spesial (Opsional)</label>
                                <input type="number" name="special_price" class="form-control">
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button class="btn btn-dark">Simpan Harga</button>
                        </div>
                    </form>

                </div>

                {{-- FORM CREATE PRODUCT --}}
                <div id="createProductBox" class="d-none mt-4">

                    <h5 class="fw-bold">Produk Tidak Ditemukan — Tambah Baru</h5>

                    <form id="createProductForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3 mt-2">

                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">SKU</label>
                                <input type="text" name="sku_code" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>

                            {{-- FOTO --}}
                            <div class="col-12">
                                <label class="form-label">Foto Produk</label>
                                <input type="file" name="photo" class="form-control">
                            </div>

                            {{-- SATUAN BERJENJANG SINGKAT --}}
                            <div class="col-md-4">
                                <label class="form-label">Unit Dasar</label>
                                <input type="text" name="unit_1_name" value="PCS" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Unit Level 2</label>
                                <input type="text" name="unit_2_name" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Isi Level 2</label>
                                <input type="number" name="unit_2_value" class="form-control">
                            </div>

                        </div>

                        <div class="text-end mt-4">
                            <button class="btn btn-dark">Tambah Produk Baru</button>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
// ========== SEARCH PRODUK ==========
$('#addSearchProductInput').on('keyup', function () {
    let q = $(this).val();

    if (q.length < 2) {
        $('#addSearchResults').html('');
        return;
    }

    $.get("{{ route('supplier.products.search') }}", { q: q }, function (res) {
        $('#addSearchResults').html(res.html);
    });
});


// ========== KETIKA PILIH PRODUK DARI HASIL SEARCH ==========
$(document).on('click', '.select-product-btn', function () {

    let product = $(this).data('product');
    let supplierId = "{{ $supplier->id }}";

    // Tampilkan box selected product
    $('#selectedProductBox').removeClass('d-none');
    $('#createProductBox').addClass('d-none');

    // Isi info produk
    $('#selectedProductName').text(product.name);
    $('#selectedProductSku').text("SKU: " + product.sku_code);
    $('#selectedProductImage').attr('src', product.photo_url);

    // Update form attach harga
    $('#attachPriceForm').attr('action',
        `/suppliers/${supplierId}/products/${product.id}/attach`
    );
});


// ========== TAMPILKAN FORM CREATE JIKA TIDAK ADA HASIL ==========
$(document).on('click', '#showCreateFormBtn', function () {
    $('#selectedProductBox').addClass('d-none');
    $('#createProductBox').removeClass('d-none');
});
</script>
