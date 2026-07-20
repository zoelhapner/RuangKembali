@extends('tablar::page')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col d-flex align-items-center">
                <a href="{{ route('job-categories.index') }}" class="btn btn-dark d-flex align-items-center">
                    <i class="ti ti-arrow-left"></i>
                </a>
                    <h2 class="page-title mb-0">Edit Data AHSP</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body px-5 py-4">

                {{-- <h2 class="fw-bold mb-4">Edit Grup: {{ $jobCategory->nama_group }}</h2> --}}
                <form action="{{ route('job-categories.update', $jobCategory->id) }}"
                      method="POST" class="mb-5">

                    @csrf @method('PUT')
                     @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                        @endif
                    <div class="row g-4">
                        <div class="col-md-2 mb-4">
                            <label class="form-label fw-bold">Bidang</label>
                            <input type="text" name="bidang" class="form-control" 
                                value="{{ $jobCategory->bidang }}" required>
                        </div>
                        <div class="col-md-2 mb-4">
                            <label class="form-label fw-bold">Kode Grup</label>
                            <input type="text" name="kode_group" class="form-control" 
                                value="{{ $jobCategory->kode_group }}" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Nama Grup</label>

                            <select name="nama_group" class="form-select select2" required>
                            @foreach($groups as $bidang => $items)
                                
                                    @foreach($items as $g)
                                        <option value="{{ $g->nama_group }}"
                                            {{ $jobCategory->nama_group === $g->nama_group ? 'selected' : '' }}>
                                            {{ $g->nama_group }}
                                        </option>
                                    @endforeach
                                
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-2 mb-4">
                            <label class="form-label fw-bold">Kode</label>
                            <input type="text" name="kode" class="form-control" 
                                value="{{ $jobCategory->kode }}" required>
                        </div>
                        <div class="col-md-2 mb-4">
                            <label class="form-label fw-bold">Kode Urut</label>
                            <input type="text" name="kode_urut" class="form-control" 
                                value="{{ $jobCategory->kode_urut }}" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Nama Pekerjaan</label>
                            <input type="text" name="nama_pekerjaan" class="form-control" 
                                value="{{ $jobCategory->nama_pekerjaan }}" required>
                        </div>
                     
                        <div class="col-md-2 mb-4">
                            <label class="form-label fw-bold">Satuan</label>
                            <input type="text" name="satuan" class="form-control" 
                                value="{{ $jobCategory->satuan }}" required>
                        </div>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-dark px-4">Update Grup</button>
                    </div>
                </form>

                <hr>

                {{-- FORM TAMBAH ITEM --}}
                <h3 class="fw-bold mb-3">Tambah Item Rincian</h3>

                <form action="{{ route('job-categories.items.store', $jobCategory->id) }}"
                      method="POST" class="mb-5">

                    @csrf

                    <div class="row g-4">
                        
                        <div class="col-md-2">
                            <label class="form-label">Kategori</label>
                            <select name="category" id="categorySelect" class="form-select select2" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="product">Produk</option>
                                <option value="labor">Tenaga</option>
                                <option value="equipment">Peralatan</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Item</label>
                            <select id="itemSelect" class="form-select select2" disabled>
                                <option value="">-- Pilih Item --</option>
                            </select>
                            <input type="hidden" name="product_id" id="product_id">
                            <input type="hidden" name="labor_cost_id" id="labor_cost_id">
                            <input type="hidden" name="equipment_cost_id" id="equipment_cost_id">
                            <input type="hidden" name="product_supplier_id" id="product_supplier_id">
                            <input type="hidden" name="name" id="item_name">
                        </div>

                        {{-- <div class="col-md-3">
                            <label class="form-label">Mitra Supplier</label>
                            <select id="supplierSelect" class="form-select select2" disabled>
                                <option value="">-- Pilih Mitra --</option>
                            </select>
                        </div> --}}

                        <div class="col-md-2">
                            <label class="form-label">Kode</label>
                            <input type="text" name="code" id="code" class="form-control" readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="unit" id="unit" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="row g-4 mt-2">
                        <div class="col-md-2">
                            <label class="form-label">Koefisien</label>
                            <input type="number"
                                step="0.0001"
                                name="coefisien"
                                id="coefisien"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Harga Satuan</label>
                            <input type="text"
                                id="price"
                                class="form-control"
                                readonly>
                            <input type="hidden" name="base_unit_price" id="price_raw">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Jumlah Harga</label>
                            <input type="text"
                                id="total_price"
                                class="form-control"
                                readonly>
                        </div>

                    </div>
                        <div class="text-end mt-2">
                            <button class="btn btn-dark px-4">Tambah</button>
                        </div>
                </form>

                <hr>

                <h3 class="fw-bold mb-3">Daftar Analisa</h3>

                @include('job-categories.partials.items-table', [
                    'items' => $jobCategory->items
                ])

            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('.select2').select2({
        width: '100%'
    });
});

$('#categorySelect').on('change', function () {
    const type = this.value;
    const itemSelect = $('#itemSelect');
    const supplierSelect = $('#supplierSelect');

    itemSelect.empty().append('<option value="">-- Pilih Item --</option>').prop('disabled', true);
    supplierSelect.empty().append('<option value="">-- Pilih Mitra --</option>').prop('disabled', true);

    $('#price, #price_raw, #code, #unit, #item_name').val('');
    $('#total_price').val('');
    $('#product_supplier_id').val('');

    if (!type) return;

    fetch(`/ajax/items/${type}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(item => {
                itemSelect.append(new Option(item.name, item.id));
            });
            itemSelect.prop('disabled', false);
        });
});

$('#itemSelect').on('change', function () {
    const type = $('#categorySelect').val();
    const id = $(this).val();

    if (!id) return;

    $('#product_id, #labor_cost_id, #equipment_cost_id').val('');
    $('#product_supplier_id').val('');

    if (type === 'product') {

        fetch(`/ajax/product-supplier/${id}`)
        .then(res => res.json())
        .then(item => {

            $('#product_supplier_id').val(item.id);

            $('#price_raw').val(item.price);
            $('#price').val(formatRp(item.price));
            $('#code').val(item.code);
            $('#unit').val(item.unit);
            $('#item_name').val(item.name);

            hitungTotal();
        });

        return; // 🔥 WAJIB STOP DI SINI
    }

    fetch(`/ajax/item-detail/${type}/${id}`)
        .then(res => res.json())
        .then(item => {

            $('#price_raw').val(item.price);
            $('#price').val(formatRp(item.price));
            $('#code').val(item.code);
            $('#unit').val(item.unit);
            $('#item_name').val(item.name);

            if (type === 'labor') $('#labor_cost_id').val(item.id);
            if (type === 'equipment') $('#equipment_cost_id').val(item.id);

            hitungTotal();
        });
});

// $('#supplierSelect').on('change', function () {
//     const productId  = $('#itemSelect').val();
//     const pivotId = this.value;

//     if (!productId || !pivotId) return;

//     fetch(`/ajax/product-supplier/${pivotId}`)
//         .then(res => res.json())
//         .then(item => {

//             $('#product_supplier_id').val(item.id); 
//             console.log(item.id);
//             $('#price_raw').val(item.price);
//             $('#price').val(formatRp(item.price));
//             $('#code').val(item.code);
//             $('#unit').val(item.unit);
//             $('#item_name').val(item.name);

//             hitungTotal();
//         });
// });

function formatRp(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(num || 0);
}

function hitungTotal() {
    const coef  = parseFloat($('#coefisien').val()) || 0;
    const price = parseFloat($('#price_raw').val()) || 0;

    $('#total_price').val(formatRp(coef * price));
}

$('#coefisien').on('input', hitungTotal);
</script>
@endpush