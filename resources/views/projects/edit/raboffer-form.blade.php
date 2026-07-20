@php
    $offer = $project->offer;
    $planningLevel = $project->levels->firstWhere('level_order', 2);
    $planningEmployees = $planningLevel ? $planningLevel->employees : collect();
@endphp
<div class="card mb-4">
    <div class="card-header fw-bold"> Edit Data Penawaran</div>
    <div class="card-body">
        <form action="{{ route('offer-rab.update', $offer->id) }}" method="POST">
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
                    <label class="form-label">Pilih Paket RAB</label>
                    <select name="rab_package_id" class="form-select select2" id="rabPackageSelect" required>
                        <option value="">-- Pilih Paket --</option>
                        @foreach($rabPackages as $package)
                            <option value="{{ $package->id }}"
                                {{ old('rab_package_id', $offer->rab_package_id) == $package->id ? 'selected' : '' }}>
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
                    <input type="hidden" name="price_meter" id="edit_priceMeter"
                        value="{{ $offer->price_meter }}">
                    <span id="edit_priceMeterFormatted" class="form-control bg-light">
                        {{ number_format($offer->price_meter, 0, ',', '.') }}
                    </span>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Total Harga (Rp)</label>
                    <input type="hidden" name="total_price" id="edit_totalPrice"
                        value="{{ $offer->total_price }}">
                    <span id="edit_totalPriceFormatted" class="form-control bg-light">
                        {{ number_format($offer->total_price, 0, ',', '.') }}
                    </span>
                <small class="text-warning d-none" id="edit_minOrderNote">
                    * Volume < 100 m² dihitung sebagai 100 m²
                </small>
                </div>
            </div>

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

                    <tbody id="edit_offerItemsBody">
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
                                    name="discount" id="edit_discount"
                                    value="{{ $offer->discount }}">
                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">SUBTOTAL AFTER DISCOUNT</th>
                            <th id="edit_subAfterDiscountDisplay">
                                {{ number_format($offer->subtotal_after_discount, 0, ',', '.') }}
                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">TAX RATE (%)</th>
                            <th>
                                <input type="number" class="form-control form-control-sm"
                                    name="tax_rate" id="edit_tax_rate"
                                    value="{{ $offer->tax_rate }}">
                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">TOTAL TAX</th>
                            <th id="edit_totalTaxDisplay">
                                {{ number_format($offer->tax_total, 0, ',', '.') }}
                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">SHIPPING / HANDLING</th>
                            <th>
                                <input type="number" class="form-control form-control-sm"
                                    name="shipping" id="edit_shipping"
                                    value="{{ $offer->shipping }}">
                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">GRAND TOTAL</th>
                            <th id="edit_grandTotalDisplay">
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

    const packageSelect = document.getElementById('rabPackageSelect');
    const priceMeterInput = document.getElementById('edit_priceMeter');
    const priceMeterFormatted = document.getElementById('edit_priceMeterFormatted');
    const volumeInput = document.querySelector('input[name="volume"]');
    const satuanInput = document.getElementById('satuan');
    const totalPriceInput = document.getElementById('edit_totalPrice');
    const totalPriceFormatted = document.getElementById('edit_totalPriceFormatted');

    const tableBody = document.getElementById('edit_offerItemsBody');

    const discountInput = document.getElementById('edit_discount');
    const taxRateInput = document.getElementById('edit_tax_rate');
    const shippingInput = document.getElementById('edit_shipping');

    function formatRp(num) {
        num = parseFloat(num) || 0;
        return "Rp " + num.toLocaleString('id-ID');
    }

    packageSelect.addEventListener('change', function () {

        let packageId = this.value;

        if (!packageId) {
            priceMeterInput.value = "";
            priceMeterFormatted.innerText = "";
            tableBody.innerHTML = "";
            hitungTotal();
            return;
        }

        fetch(`/rab-packages/json/${packageId}`)
            .then(res => res.json())
            .then(data => {

                priceMeterInput.value = data.price_meter ?? 0;
                priceMeterFormatted.innerText = formatRp(data.price_meter);
                satuanInput.value = data.satuan ?? "m2";

                tableBody.innerHTML = "";

                let itemIndex = 0;
                let rowNumber = 1;

                let grouped = {};

                data.items.forEach(item => {
                    if (!grouped[item.category]) {
                        grouped[item.category] = [];
                    }
                    grouped[item.category].push(item);
                });

                Object.keys(grouped).forEach(category => {

                    tableBody.innerHTML += `
                        <tr class="table-secondary">
                            <td class="fw-bold">${rowNumber++}</td>
                            <td class="fw-bold">${category}</td>
                            <td></td><td></td><td></td><td></td>
                        </tr>
                    `;

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

                        itemIndex++;
                    });
                });

                hitungTotal();
            });
    });

    [volumeInput, discountInput, taxRateInput, shippingInput].forEach(input => {
        input.addEventListener('input', hitungTotal);
    });

    function hitungTotal() {

        let inputVolume = parseFloat(volumeInput.value) || 0;

        // 🚨 Minimum order 100 m2
        let volume = inputVolume > 0 && inputVolume < 100 ? 100 : inputVolume;

        let price = parseFloat(priceMeterInput.value) || 0;
        let subtotal = volume * price;

        totalPriceInput.value = subtotal;
        totalPriceFormatted.innerText = formatRp(subtotal);

        document.getElementById("subtotalDisplay").innerText = formatRp(subtotal);
        const minNote = document.getElementById('edit_minOrderNote');
        if (minNote) {
            if (inputVolume > 0 && inputVolume < 100) {
                minNote.classList.remove('d-none');
            } else {
                minNote.classList.add('d-none');
            }
        }


        let discount = parseFloat(discountInput.value) || 0;
        let subAfterDiscount = subtotal - discount;
        document.getElementById("edit_subAfterDiscountDisplay").innerText = formatRp(subAfterDiscount);

        let taxRate = parseFloat(taxRateInput.value) || 0;
        let totalTax = subAfterDiscount * (taxRate / 100);
        document.getElementById("edit_totalTaxDisplay").innerText = formatRp(totalTax);

        let shippingCost = parseFloat(shippingInput.value) || 0;
        let grandTotal = subAfterDiscount + totalTax + shippingCost;
        document.getElementById("edit_grandTotalDisplay").innerText = formatRp(grandTotal);
    }
            if (packageSelect.value) {
            packageSelect.dispatchEvent(new Event('change'));
        }

        hitungTotal();

});
</script>
@endpush