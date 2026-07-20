@extends('tablar::page')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col d-flex align-items-center">
                <a href="{{ route('products.index') }}" class="btn btn-dark d-flex align-items-center">
                    <i class="ti ti-arrow-left"></i>
                </a>
                
                    <h2 class="page-title mb-0">Edit Data Produk</h2>
                
            </div>
        </div>
    </div>
</div>


<div class="page-body">
    <div class="container-xl">
        <div class="card shadow-sm border-0">
            <div class="card-body px-5 py-4">
                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                    <div class="text-center mb-5">
                    <div class="position-relative d-inline-block">
                        @if ($product->photo)
                            <img id="previewImage" src="{{ asset('storage/'.$product->photo) }}" alt="Profile" 
                                 class="rounded-3 shadow-sm border" width="150" height="150"
                                 style="object-fit: cover;">
                        @else
                            <div id="previewImage"
                                 class="rounded-3 shadow-sm bg-light d-flex align-items-center justify-content-center"
                                 style="width:150px; height:150px;">
                                 <i class="ti ti-user" style="font-size: 64px; color:#aaa;"></i>
                            </div>
                        @endif
                        <label for="photo"
                               class="btn btn-sm btn-dark position-absolute bottom-0 end-0 translate-middle rounded-circle"
                               title="Ganti Foto">
                            <i class="ti ti-camera"></i>
                        </label>
                    </div>
                    <input type="file" id="photo" name="photo" class="d-none" accept="image/*">
                </div>
                    <div class="mb-3">
                        <small class="text-danger fw-semibold">
                            * : Wajib diisi
                        </small>
                    </div>

                    <div class="section-block mb-5">
                        <h3 class="fw-semibold mb-3 border-bottom pb-2">Informasi Produk</h3>
                        <div class="row g-4">
                            <div class="col-md-5">
                                <label class="form-label required">Nama Produk</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"  value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                    <label class="form-label">SKU</label>
                                    <input type="text" id="sku" name="sku_code" class="form-control" readonly>
                            </div>
                            <div class="col-md-3">
                                            <label class="form-label">Status Barang :</label>
                                            <select name="status" class="form-select">
                                                <option value="">-- Pilih Status --</option>
                                                <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Tersedia</option>
                                                <option value="2" {{ $product->status == 2 ? 'selected' : '' }}>Stok Terbatas</option>
                                                <option value="3" {{ $product->status == 3 ? 'selected' : '' }}>Habis</option>
                                                <option value="4" {{ $product->status == 4 ? 'selected' : '' }}>Pre-Order</option>
                                            </select>
                            </div>
                            <div class="col-12">
                                    <label class="form-label required">Deskripsi Produk</label>
                                    <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label required">Merk</label>
                                <select name="brand_id" class="form-select select2">
                                    <option value="">Pilih Brand</option>
                                    @foreach ($brands as $b)
                                        <option value="{{ $b->id }}"
                                            {{ $b->id == old('brand_id', $product->brand_id) ? 'selected' : '' }}>
                                            {{ $b->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Kategori</label>
                                <select name="category_id" class="form-select select2">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}"
                                            {{ $c->id == old('category_id', $product->category_id) ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Tipe Produk</label>
                                <select name="type_id" class="form-select select2">
                            <option value="">Pilih Tipe</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}"
                                    {{ $t->id == old('type_id', $product->type_id) ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                                @error('type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Ukuran Produk</label>
                                <input type="text" name="size" class="form-control @error('size') is-invalid @enderror" value="{{ old('size', $product->size) }}">
                                @error('size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Volume</label>
                                <input type="text" name="volume" class="form-control @error('volume') is-invalid @enderror" value="{{ old('volume', $product->volume) }}">
                                @error('volume')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            
                            
                            <div class="col-md-4">
                                <label class="form-label">Warna Produk</label>
                                <select name="colors[]" class="form-select select2" multiple>
                                    @foreach ($colors as $color)
                                        <option value="{{ $color->id }}"
                                            {{ in_array($color->id, old('colors', $productColors ?? [])) ? 'selected' : '' }}>
                                            {{ $color->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>
                    </div>

                        <div class="section-block mb-4">
                            <h3 class="fw-semibold mb-3 border-bottom pb-2">Pengaturan Satuan Unit</h3>

                            <div class="row g-2">

                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Satuan Level 1</label>
                                        <input type="text" name="unit_1_name" 
                                            class="form-control form-control-md" 
                                            value="{{ old('unit_1_name', $product->unit_1_name ?? 'PCS') }}" >
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Nilai Unit 1</label>
                                        <input type="number" name="unit_1_value" 
                                            class="form-control form-control-md" 
                                            value="1" readonly>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Satuan Level 2</label>
                                        <input type="text" name="unit_2_name" 
                                            class="form-control form-control-md"
                                            value="{{ old('unit_2_name', $product->unit_2_name) }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Konversi Unit 2</label>
                                        <input type="number" name="unit_2_value" 
                                            class="form-control form-control-md"
                                            value="{{ old('unit_2_value', $product->unit_2_value) }}">
                                        <small class="text-muted" id="unit_2_preview"></small>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Satuan Level 3</label>
                                        <input type="text" name="unit_3_name" 
                                            class="form-control form-control-md"
                                            value="{{ old('unit_3_name', $product->unit_3_name) }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Konversi Unit 3</label>
                                        <input type="number" name="unit_3_value" 
                                            class="form-control form-control-md"
                                            value="{{ old('unit_3_value', $product->unit_3_value) }}">
                                        <small class="text-muted" id="unit_3_preview"></small>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Satuan Level 4</label>
                                        <input type="text" name="unit_4_name" 
                                            class="form-control form-control-md"
                                            value="{{ old('unit_4_name', $product->unit_4_name) }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Konversi Unit 4</label>
                                        <input type="number" name="unit_4_value" 
                                            class="form-control form-control-md"
                                            value="{{ old('unit_4_value', $product->unit_4_value) }}">
                                        <small class="text-muted" id="unit_4_preview"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-block mb-5">
                            <h3 class="fw-semibold mb-3 border-bottom pb-2">Perhitungan Profit</h3>

                            <div class="row g-3">

                                <div class="mb-3">
                                    <label class="form-label">Harga Modal (HPP Otomatis)</label>

                                    <input type="number"
                                        id="base_price"
                                        class="form-control"
                                        value="{{ $hpp ?? old('base_price') }}"
                                        readonly>

                                    @if(!$hpp)
                                        <small class="text-danger">
                                            Produk ini belum memiliki harga dari supplier
                                        </small>
                                    @else
                                        <small class="text-muted">
                                            Diambil otomatis dari harga supplier termurah
                                        </small>
                                    @endif
                                </div>


                                <div class="col-md-3">
                                    <label class="form-label">Tipe Profit</label>
                                    <select id="profit_type" class="form-select">
                                        <option value="percent" {{ $product->profit_type == "percent" ? 'selected' : '' }}>Persen (%)</option>
                                        <option value="nominal" {{ $product->profit_type == "nominal" ? 'selected' : '' }}>Rupiah (Rp)</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Nilai Profit</label>
                                    <input type="number" id="profit_value" class="form-control" placeholder="Contoh: 20 atau 50000" value="{{ old('profit_value', $product->profit_value) }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Harga Jual Estimasi</label>
                                    <input type="text" id="selling_preview" class="form-control" readonly value="{{ old('selling_preview', $product->selling_preview) }}">
                                </div>
                            </div>

                            <div class="mt-3 p-3 bg-light rounded">
                                <strong>Rumus:</strong>
                                <div id="profit_formula" class="mt-1 text-muted">-</div>
                            </div>
                        </div>

                    {{-- SUBMIT --}}
                    <div class="text-end mt-5">
                        <button type="submit" class="btn btn-dark px-4">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Data
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
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: "-- Pilih --",
                    width: '100%'
                });
            });
    </script>

<script>
function generateSKU() {
    $.ajax({
        url: "{{ route('products.generateSku') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            category_id: $('#category_id').val(),
            brand_id: $('#brand_id').val(),
            type_id: $('#type_id').val(),
            size: $('#size').val(),
            volume: $('#volume').val(),
            colors: $('#colors').val()
        },
        success: function(res) {
            $('#sku').val(res.sku);
        }
    });
}

// Trigger generate saat input berubah
$('#category_id, #brand_id, #type_id, #ukuran, #volume, #colors').on('change input', function() {
    generateSKU();
});

// Generate awal saat halaman dibuka
$(document).ready(function() {
    generateSKU();
});
</script>

<script>
        document.getElementById('photo').addEventListener('change', function (event) {
        const input = event.target;
        const file = input.files[0];
        const previewContainer = document.getElementById('previewImage');

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                // Jika sebelumnya preview berupa ikon <div>, ganti jadi <img>
                if (previewContainer.tagName.toLowerCase() === 'div') {
                    const img = document.createElement('img');
                    img.id = 'previewImage';
                    img.src = e.target.result;
                    img.className = 'border rounded-3 shadow-sm';
                    img.width = 150;
                    img.height = 150;
                    img.style.objectFit = 'cover';
                    previewContainer.replaceWith(img);
                } else {
                    previewContainer.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<script>
    function updateUnitPreview() {
        let u1 = $('input[name="unit_1_name"]').val() || 'PCS';
        let v1 = parseInt($('input[name="unit_1_value"]').val()) || 1;

        let u2 = $('input[name="unit_2_name"]').val();
        let v2 = parseInt($('input[name="unit_2_value"]').val());

        let u3 = $('input[name="unit_3_name"]').val();
        let v3 = parseInt($('input[name="unit_3_value"]').val());

        let u4 = $('input[name="unit_4_name"]').val();
        let v4 = parseInt($('input[name="unit_4_value"]').val());

        // Unit 2
        if (u2 && v2) {
            let result2 = v2 * v1;
            $('#unit_2_preview').text(`1 ${u2} = ${result2} ${u1}`);
        } else {
            $('#unit_2_preview').text('');
        }

        // Unit 3
        if (u3 && v3 && u2 && v2) {
            let result3 = v3 * v2 * v1;
            $('#unit_3_preview').text(`1 ${u3} = ${result3} ${u1}`);
        } else {
            $('#unit_3_preview').text('');
        }

        // Unit 4
        if (u4 && v4 && u3 && v3 && u2 && v2) {
            let result4 = v4 * v3 * v2 * v1;
            $('#unit_4_preview').text(`1 ${u4} = ${result4} ${u1}`);
        } else {
            $('#unit_4_preview').text('');
        }
    }

    // Jalankan setiap kali input berubah
    $('input[name="unit_1_name"], input[name="unit_1_value"], ' +
    'input[name="unit_2_name"], input[name="unit_2_value"], ' +
    'input[name="unit_3_name"], input[name="unit_3_value"], ' +
    'input[name="unit__4_name"], input[name="unit_4_value"]'
    ).on('keyup change', updateUnitPreview);
</script>
<script>
$(document).ready(function () {
    hitungProfit(); // auto hitung saat edit dibuka
});
</script>
<script>
function hitungProfit() {
    let base   = parseFloat($('#base_price').val()) || 0;
    let type   = $('#profit_type').val();
    let value  = parseFloat($('#profit_value').val()) || 0;

    let profitRp = 0;
    let selling  = base;

    if (type === 'percent') {
        profitRp = base * (value / 100);
    } else {
        profitRp = value;
    }

    selling = base + profitRp;

    $('#selling_preview').val(
        'Rp ' + selling.toLocaleString('id-ID')
    );

    let persen = base > 0 ? ((profitRp / base) * 100) : 0;

    $('#profit_preview').val(
        'Rp ' + profitRp.toLocaleString('id-ID') + 
        ' (' + persen.toFixed(2) + '%)'
    );
}

$('#base_price, #profit_value, #profit_type').on('keyup change', hitungProfit);
</script>


@endpush
