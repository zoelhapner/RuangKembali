@can('lihat daftar proyek')
<form action="{{ route('projects.offer.store') }}" method="POST">
    @csrf
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
            <input type="text" name="offer_number" class="form-control" value="{{ old('offer_number') ?? '' }}" placeholder="Auto Generate" readonly>

        </div>
        <div class="col-md-4">
            <label>Tanggal Penawaran</label>
            <input type="date" name="offer_date" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Nama Customer</label>
            <input type="text" name="contact_name" value="{{ $project->customer->user->fullname }}" class="form-control">
        </div>
    </div>

    {{-- <div class="mb-3">
        <label>Alamat / Lokasi</label>
        <input type="text" name="kepada_alamat" class="form-control" value="{{ $project->city->name ?? '' }}">
    </div>

    <div class="mb-3">
        <label>Jenis Pekerjaan</label>
        <input type="text" name="jenis_pekerjaan" class="form-control" value="{{ $project->project_name ?? '' }}">
    </div>

    <div class="mb-3">
        <label>Lokasi Pekerjaan</label>
        <input type="text" name="lokasi" class="form-control" value="{{ $project->project_location ?? '' }}">
    </div> --}}

    <div class="row mb-4 mt-4">
        <div class="col-md-4">
            <label class="form-label">Pilih Paket RAB</label>
            <select name="rab_package_id" class="form-select select2" id="rabPackageSelect" required>
                <option value="">-- Pilih Paket --</option>
                @foreach($rabPackages as $package)
                    <option value="{{ $package->id }}">{{ $package->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Volume</label>
            <input type="text" name="volume" class="form-control">
            <small class="text-muted">
                Minimal order 100 m2
            </small>
        </div>
        <div class="col-md-2">
            <label class="form-label">Satuan</label>
            <input type="text" name="satuan" class="form-control" id="satuan" readonly>
        </div>
        <div class="col-md-2">
            <label class="form-label">Harga Satuan (Rp)</label>
            <input type="hidden" name="price_meter" id="priceMeter">
            <span id="priceMeterFormatted" class="form-control bg-light"></span>

        </div>
        <div class="col-md-2">
            <label class="form-label">Total Harga (Rp)</label>
            <input type="hidden" name="total_price" id="totalPrice">
            <span id="totalPriceFormatted" class="form-control bg-light"></span>
                <small class="text-warning d-none" id="minOrderNote">
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

                <tbody id="rabItemsBody">
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">SUBTOTAL</th>
                        <th id="subtotalDisplay">Rp 0</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">DISCOUNT</th>
                        <th>
                            <input type="number" class="form-control"
                                name="discount" id="discount" value="0">
                        </th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">SUBTOTAL AFTER DISCOUNT</th>
                        <th id="subAfterDiscountDisplay">Rp 0</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">TAX RATE (%)</th>
                        <th>
                            <input type="number" class="form-control"
                                name="tax_rate" id="tax_rate" value="0">
                        </th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">TOTAL TAX</th>
                        <th id="totalTaxDisplay">Rp 0</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">SHIPPING / HANDLING</th>
                        <th>
                            <input type="number" class="form-control"
                                name="shipping" id="shipping" value="0">
                        </th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">GRAND TOTAL</th>
                        <th id="grandTotalDisplay">Rp 0</th>
                    </tr>
                </tfoot>
            </table>
        </div>

    <h4 class="fw-bold mb-3">Keterangan</h4>

    <textarea name="notes" rows="5" class="form-control"></textarea>

    <div class="text-end mt-5">
        <button type="submit" class="btn btn-dark px-4">
            <i class="ti ti-device-floppy me-1"></i>Simpan Penawaran RAB
        </button>
    </div>
</form>
@endcan

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const packageSelect = document.getElementById('rabPackageSelect');
    if (!packageSelect) return;
    const priceMeterInput = document.getElementById('priceMeter');
    const priceMeterFormatted = document.getElementById('priceMeterFormatted');
    const volumeInput = document.querySelector('input[name="volume"]');
    const satuanInput = document.getElementById('satuan');
    const totalPriceInput = document.getElementById('totalPrice');
    const totalPriceFormatted = document.getElementById('totalPriceFormatted');

    const tableBody = document.getElementById('rabItemsBody');
    if (!tableBody) return;
    const discountInput = document.getElementById('discount');
    const taxRateInput = document.getElementById('tax_rate');
    const shippingInput = document.getElementById('shipping');

    function formatRp(num) {
        num = parseFloat(num) || 0;
        return "Rp " + num.toLocaleString('id-ID');
    }

    $('#rabPackageSelect').on('select2:select', function (e) {

        let packageId = e.target.value;

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
        const minNote = document.getElementById('minOrderNote');
        if (inputVolume > 0 && inputVolume < 100) {
            minNote.classList.remove('d-none');
        } else {
            minNote.classList.add('d-none');
        }

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