@extends('tablar::page')

@section('content')
<div class="container-xl">

    {{-- 🔹 Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
        <h2 class="page-title">Detail Gudang</h2>
        <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- 🔹 Tabs --}}
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#tab-personal" role="tab">
                <i class="ti ti-user"></i> Informasi Gudang
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-catalogue" role="tab">
                <i class="ti ti-shopping-cart"></i> Stok Gudang
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-product-in" role="tab">
                <i class="ti ti-check"></i> Transaksi Barang Masuk
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-product-out" role="tab">
                <i class="ti ti-x"></i> Transaksi Barang Keluar
            </a>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ================= TAB 1: PERSONAL ================= --}}
        <div class="tab-pane fade show active" id="tab-personal" role="tabpanel">

            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Informasi Personal</h3>
                    <a href="{{ route('warehouses.edit', $warehouse->id) }}" class="btn btn-outline-dark btn-sm">
                        <i class="ti ti-edit"></i> Ubah Detail
                    </a>
                </div>

                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-3 text-center">
                            @if ($warehouse->photo)
                            <img id="profilePreviewImage" src="{{ asset('storage/photos/'.$warehouse->photo) }}" alt="Profile" 
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
                                    <div class="text-muted small">Nama Gudang</div>
                                    <div class="fw-bold">{{ $warehouse->name ?? '-' }}</div>
                                </div>
                                <div class="col-md">
                                    <div class="text-muted small">Email</div>
                                    <div class="fw-bold">{{ $warehouse->email ?? '-' }}</div>
                                </div>
                                <div class="col-md">
                                    <div class="text-muted small mt-2">Penanggung Jawab</div>
                                    <div class="fw-bold">{{ $warehouse->responsible_person ?? '-' }}</div>
                                </div>
                                <div class="col-md">
                                    <div class="text-muted small">Telepon</div>
                                    <div class="fw-bold">{{ $warehouse->phone ?? '-' }}</div>
                                </div>
                                
                                {{-- <div class="col-md-4">
                                    <div class="text-muted small mt-2">Status</div>
                                    <span class="badge {{ $warehouse->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $warehouse->status_text }}
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
                            <div class="fw-bold">{{ $warehouse->address ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Kelurahan</div>
                            <div class="fw-bold">{{ $warehouse->subDistrict->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Kecamatan</div>
                            <div class="fw-bold">{{ $warehouse->district->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Kabupaten/Kota</div>
                            <div class="fw-bold">{{ $warehouse->city->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Provinsi</div>
                            <div class="fw-bold">{{ $warehouse->province->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Kode Pos</div>
                            <div class="fw-bold">{{ $warehouse->postalCode->postal_code ?? '-' }}</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab-catalogue" role="tabpanel">

            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">Stok Gudang</h3>
                </div>

                <div class="card-body p-0">

                    <table class="table table-hover table-vcenter card-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Foto</th>
                                <th>Produk</th>
                                <th>Kode SKU</th>
                                <th>Stok</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($stocks as $s)

                                @php
                                    $product = $s->product;
                                    $img = $product->photo
                                        ? asset('storage/' . $product->photo)
                                        : asset('images/no-image.png');

                                    $qty = $s->stock;

                                    if ($qty <= 0) {
                                        $status = ['badge bg-danger', 'Habis'];
                                    } elseif ($qty <= 5) {
                                        $status = ['badge bg-warning text-dark', 'Hampir Habis'];
                                    } else {
                                        $status = ['badge bg-success', 'Tersedia'];
                                    }
                                @endphp

                                <tr>
                                    <td width="80">
                                        <img src="{{ $img }}"
                                            class="rounded shadow-sm"
                                            style="width:70px;height:70px;object-fit:cover;">
                                    </td>

                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $product->brand->name ?? '' }}
                                        </small>
                                    </td>

                                    <td>{{ $product->sku ?? '-' }}</td>

                                    <td class="fw-bold">{{ $qty }}</td>

                                    <td>
                                        <span class="{{ $status[0] }}">
                                            {{ $status[1] }}
                                        </span>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-4 text-muted">
                                        <i class="ti ti-info-circle"></i>
                                        Tidak ada stok untuk gudang ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>

        </div>


        <div class="tab-pane fade" id="tab-product-in" role="tabpanel">
            <div class="card">
                <div class="card-body text-center text-muted">
                    <em>Belum ada data riwayat pembayaran.</em>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab-product-out" role="tabpanel">
            <div class="card">
                <div class="card-body text-center text-muted">
                    <em>Belum ada data riwayat pembayaran.</em>
                </div>
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

        $('#warehouseFormArea, #warehouseselectedProduct').hide();
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
            $('#warehouseFormArea').hide();
            $("#product_id").val("");
            return;
        }

        $.get("{{ route('warehouse.searchProduct') }}", { keyword }, function (res) {
            console.log(res);

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
                $('#warehouseFormArea').show();
                $("#product_id").val("");

                $("#selectedProductName").text("Produk Baru");
                $("#warehouseselectedProduct").show();
                return;
            }

            // PRODUK DITEMUKAN
            $('#formCreateProduct').hide();
            $('#warehouseFormArea').hide();
            $('#warehouseselectedProduct').hide();

            $('#searchResult').html(res.html).show();
        });
    });

    $(document).on("click", ".product-item", function () {

        let id = $(this).data("id");

        $.get("/warehouse/product-detail/" + id, function (p) {

            // SET PRODUCT_ID
            $("#product_id").val(p.id);

            $("#selectedProductName").text(p.name);
            $("#warehouseselectedProduct").show();

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


            // FILL warehouse DEFAULTS
            $("[name='buying_prices']").val(p.default_buying_prices);
            $("[name='discount']").val(p.default_discount);
            $("[name='tax_percentage']").val(p.tax_percentage);

            $('#base_price').val(formatRupiah(p.default_buying_prices));
            $('#tax').val(p.tax_percentage);
            $('#discount').val(p.default_discount);

            setTimeout(() => recalculatePrice(), 200);


            $('#warehouseFormArea').show();
        });
    });

    $("#btnSavewarehouseProduct").click(function (e) {
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

                // LANJUTKAN KE SIMPAN warehouse
                submitwarehouseProduct();
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert("Gagal membuat produk.");
            }
        });

    } else {
        // ✅ INI YANG HILANG DI KODE KAMU
        submitwarehouseProduct();
    }
});


    function submitwarehouseProduct() {

        let form = $("#formwarehouse");

        $.ajax({
            url: "{{ route('warehouse.products.store') }}",
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
    return new Intl.NumberFormat('id-ID').format(angka);
}

function cleanNumber(value) {
    return parseFloat(value.replace(/[^\d]/g, '')) || 0;
}

function recalculatePrice() {
    let basePriceRaw = $('#base_price').val();
    let basePrice    = cleanNumber(basePriceRaw);

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

    let finalRounded = Math.round(finalPrice);

    // ✅ TAMPILAN (PAKAI RUPIAH)
    $('#final_price_display').val(formatRupiah(finalRounded));

    // ✅ NILAI REAL KE DB (TANPA TITIK)
    $('#final_price').val(finalRounded);

    $('#priceFormulaPreview')
        .html(`<strong>Rumus:</strong><br>${formulaText} = <strong>Rp ${formatRupiah(finalRounded)}</strong>`)
        .hide();

}

// ✅ FORMAT RUPIAH SAAT KETIK
$('#base_price').on('keyup', function () {
    let val = cleanNumber($(this).val());
    $(this).val(formatRupiah(val));
    recalculatePrice();
});

// ✅ TRIGGER OTOMATIS
$(document).on('keyup change', '#tax, #discount', function () {
    recalculatePrice();
});
</script>


<script>
document.addEventListener('click', function(e) {

    // ✅ MODE EDIT
    if (e.target.closest('.btn-edit-price')) {
        const wrapper = e.target.closest('.price-wrapper');
        wrapper.querySelector('.price-text').classList.add('d-none');
        wrapper.querySelector('.price-edit').classList.remove('d-none');
    }

    // ✅ BATAL
    if (e.target.closest('.btn-cancel-price')) {
        const wrapper = e.target.closest('.price-wrapper');
        wrapper.querySelector('.price-edit').classList.add('d-none');
        wrapper.querySelector('.price-text').classList.remove('d-none');
    }

    // ✅ SIMPAN VIA AJAX
    if (e.target.closest('.btn-save-price')) {
        const wrapper    = e.target.closest('.price-wrapper');
        const url        = wrapper.dataset.url;
        const warehouseId = wrapper.dataset.warehouse;
        const productId  = wrapper.dataset.product;
        const value      = wrapper.querySelector('.price-input').value;

        fetch(url, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                warehouse_id: warehouseId,
                product_id: productId,
                price: value
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                wrapper.querySelector('.price-label').innerText = 'Rp ' + res.price;

                wrapper.querySelector('.price-edit').classList.add('d-none');
                wrapper.querySelector('.price-text').classList.remove('d-none');
            } else {
                alert('Gagal update harga!');
            }
        })
        .catch(() => alert('Terjadi kesalahan server'));
    }

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
