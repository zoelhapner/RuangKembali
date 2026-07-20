@php
    $offer = $project->offer;
    $planningLevel = $project->levels->firstWhere('level_order', 2);
    $planningEmployees = $planningLevel ? $planningLevel->employees : collect();
@endphp
<div class="card mb-4">
    <div class="card-header fw-bold"> Edit Data Penawaran</div>
    <div class="card-body">
        <form action="{{ route('offers.update', $offer->id) }}" method="POST">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <input type="hidden" name="project_id" value="{{ $project->id }}">

            <h4 class="fw-bold mb-3">Informasi Penawaran</h4>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Nomor Penawaran</label>
                    <input type="text" name="offer_number" 
                        class="form-control"
                        value="{{ old('offer_number', $offer->offer_number) }}"
                        readonly>
                </div>

                <div class="col-md-4">
                    <label>Tanggal Penawaran</label>
                    <input type="date" name="offer_date" class="form-control"
                        value="{{ old('offer_date', $offer->offer_date) }}">
                </div>

                <div class="col-md-4">
                    <label>Nama Customer</label>
                    <input type="text" name="contact_name"
                        class="form-control"
                        value="{{ old('contact_name', $offer->contact_name) }}">
                </div>
            </div>

            <div class="row mb-4 mt-4">
                <div class="col-md-4">
                    <label class="form-label">Pilih Paket Desain</label>
                    <select name="design_package_id" class="form-select" id="designPackageSelect" required>
                        <option value="">-- Pilih Paket --</option>

                        @foreach($designPackages as $package)
                            <option value="{{ $package->id }}"
                                {{ $offer->design_package_id == $package->id ? 'selected' : '' }}>
                                {{ $package->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Volume</label>
                    <input type="text" name="volume"
                        class="form-control"
                        value="{{ old('volume', $offer->volume) }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Satuan</label>
                    <input type="text" name="satuan" class="form-control"
                        id="satuan"
                        value="{{ old('satuan', $offer->satuan) }}"
                        readonly>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Harga Satuan (Rp)</label>
                    <input type="hidden" name="price_meter" id="priceMeter"
                        value="{{ $offer->price_meter }}">
                    <span id="priceMeterFormatted" class="form-control bg-light">
                        {{ number_format($offer->price_meter, 0, ',', '.') }}
                    </span>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Total Harga (Rp)</label>
                    <input type="hidden" name="total_price" id="totalPrice"
                        value="{{ $offer->total_price }}">
                    <span id="totalPriceFormatted" class="form-control bg-light">
                        {{ number_format($offer->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- ============================= --}}
            {{-- TABLE EDIT ITEMS --}}
            {{-- ============================= --}}
            <div class="row mb-4">

                <h4 class="fw-bold mb-3">Rincian Pekerjaan</h4>

                <table class="table table-bordered align-middle" id="offerItemsTable">
                    <thead>
                        <tr>
                            <th width="50">No.</th>
                            <th>Uraian Pekerjaan</th>
                            <th>Volume</th>
                            <th>Satuan</th>
                            <th>Harga Satuan (Rp)</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>

                    <tbody id="offerItemsBody">

                        {{-- ========================= --}}
                        {{--   LOAD EXISTING ITEMS    --}}
                        {{-- ========================= --}}
                        @php 
                            $row = 1;
                            $index = 0;
                        @endphp

                        @foreach($offer->groupedItems() as $category => $items)

                            {{-- ROW KATEGORI --}}
                            <tr class="table-secondary">
                                <td class="fw-bold">{{ $row++ }}</td>
                                <td class="fw-bold">{{ $category }}</td>
                                <td></td><td></td><td></td><td></td>
                            </tr>

                            {{-- ITEM DI BAWAH KATEGORI --}}
                            @foreach($items as $item)
                                <tr>
                                    <td></td>
                                    <td>- {{ $item->item_name }}</td>
                                    <td>{{ $item->volume }}</td>
                                    <td>{{ $item->satuan }}</td>
                                    {{-- <td>{{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>{{ number_format($item->total, 0, ',', '.') }}</td> --}}

                                    <input type="hidden" name="items[{{ $index }}][item_name]" value="{{ $item->item_name }}">
                                    <input type="hidden" name="items[{{ $index }}][category]" value="{{ $category }}">
                                </tr>
                                @php $index++; @endphp
                            @endforeach

                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">SUBTOTAL</th>
                            <th id="subtotalDisplay">{{ number_format($offer->subtotal, 0, ',', '.') }}</th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">DISCOUNT</th>
                            <th>
                                <input type="number" class="form-control form-control-sm"
                                    name="discount" id="discount"
                                    value="{{ $offer->discount }}">
                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">SUBTOTAL AFTER DISCOUNT</th>
                            <th id="subAfterDiscountDisplay">
                                {{ number_format($offer->subtotal_after_discount, 0, ',', '.') }}
                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">TAX RATE (%)</th>
                            <th>
                                <input type="number" class="form-control form-control-sm"
                                    name="tax_rate" id="tax_rate"
                                    value="{{ $offer->tax_rate }}">
                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">TOTAL TAX</th>
                            <th id="totalTaxDisplay">
                                {{ number_format($offer->tax_total, 0, ',', '.') }}
                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">SHIPPING / HANDLING</th>
                            <th>
                                <input type="number" class="form-control form-control-sm"
                                    name="shipping" id="shipping"
                                    value="{{ $offer->shipping }}">
                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">GRAND TOTAL</th>
                            <th id="grandTotalDisplay">
                                {{ number_format($offer->grand_total, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <h4 class="fw-bold mb-3">Keterangan</h4>
            <textarea name="notes" rows="5" class="form-control">{{ $offer->notes }}</textarea>

            <div class="mt-4">
                <button class="btn btn-dark">Update Penawaran</button>
                <button type="button" class="btn btn-secondary btn-cancel">
                    <i class="ti ti-x"></i> Batal
                </button>

            </div>
        </form>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const packageSelect = document.getElementById('designPackageSelect');
    const priceMeterInput = document.getElementById('priceMeter');
    const priceMeterFormatted = document.getElementById('priceMeterFormatted');
    const volumeInput = document.querySelector('input[name="volume"]');
    const satuanInput = document.getElementById('satuan');
    const totalPriceInput = document.getElementById('totalPrice');
    const totalPriceFormatted = document.getElementById('totalPriceFormatted');

    const tableBody = document.getElementById('offerItemsBody');

    const discountInput = document.getElementById('discount');
    const taxRateInput = document.getElementById('tax_rate');
    const shippingInput = document.getElementById('shipping');

    function formatRp(num) {
        num = parseFloat(num) || 0;
        return "Rp " + num.toLocaleString('id-ID');
    }

    hitungTotal(); 

    // ============================
    // EVENT: Pilih paket desain  
    // ============================
    packageSelect.addEventListener('change', function () {

        let packageId = this.value;

        if (!packageId) {
            priceMeterInput.value = "";
            priceMeterFormatted.innerText = "";
            tableBody.innerHTML = "";
            hitungTotal();
            return;
        }

        fetch(`/design-packages/json/${packageId}`)
            .then(res => res.json())
            .then(data => {

                priceMeterInput.value = data.price_meter ?? 0;
                priceMeterFormatted.innerText = formatRp(data.price_meter);
                satuanInput.value = data.satuan ?? "m2";

                tableBody.innerHTML = "";

                let itemIndex = 0; // <== PENTING
                let rowNumber = 1;

                let grouped = {};

                // GROUP ITEM BY CATEGORY
                data.items.forEach(item => {
                    if (!grouped[item.category]) {
                        grouped[item.category] = [];
                    }
                    grouped[item.category].push(item);
                });

                // LOOP CATEGORY + ITEMS
                Object.keys(grouped).forEach(category => {

                    // Baris kategori (TIDAK ADA INPUT HIDDEN)
                    tableBody.innerHTML += `
                        <tr class="table-secondary">
                            <td class="fw-bold">${rowNumber++}</td>
                            <td class="fw-bold">${category}</td>
                            <td></td><td></td><td></td><td></td>
                        </tr>
                    `;

                    // ITEM DI BAWAH KATEGORI
                    grouped[category].forEach(item => {

                        tableBody.innerHTML += `
                            <tr>
                                <td></td>
                                <td>- ${item.item_name}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                                <input type="hidden" name="items[${itemIndex}][item_name]" value="${item.item_name}">
                                <input type="hidden" name="items[${itemIndex}][category]" value="${category}">
                            </tr>
                        `;

                        itemIndex++; // increment hanya untuk item
                    });
                });

                hitungTotal();
            });
    });

    // ============================
    // EVENT: Hitung total otomatis  
    // ============================
    [volumeInput, discountInput, taxRateInput, shippingInput].forEach(input => {
        input.addEventListener('input', hitungTotal);
    });

    // ============================
    // HITUNG TOTAL  
    // ============================
    function hitungTotal() {

        let volume = parseFloat(volumeInput.value) || 0;
        let price = parseFloat(priceMeterInput.value) || 0;

        let subtotal = volume * price;

        totalPriceInput.value = subtotal;
        totalPriceFormatted.innerText = formatRp(subtotal);

        document.getElementById("subtotalDisplay").innerText = formatRp(subtotal);

        let discount = parseFloat(discountInput.value) || 0;
        let subAfterDiscount = subtotal - discount;
        document.getElementById("subAfterDiscountDisplay").innerText = formatRp(subAfterDiscount);

        let taxRate = parseFloat(taxRateInput.value) || 0;
        let totalTax = subAfterDiscount * (taxRate / 100);
        document.getElementById("totalTaxDisplay").innerText = formatRp(totalTax);

        let shippingCost = parseFloat(shippingInput.value) || 0;

        let grandTotal = subAfterDiscount + totalTax + shippingCost;
        document.getElementById("grandTotalDisplay").innerText = formatRp(grandTotal);
    }

});
</script>
@endpush