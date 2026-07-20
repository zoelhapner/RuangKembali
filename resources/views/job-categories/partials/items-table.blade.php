<table class="table table-bordered table-sm align-middle">
    <thead class="table-light text-center">
        <tr>
            <th width="40">No</th>
            <th>Uraian</th>
            <th width="100">Supplier</th>
            <th width="80">Kode</th>
            <th width="80">Satuan</th>
            <th width="90">Koefisien</th>
            <th width="120">Harga Satuan</th>
            <th width="140">Jumlah Harga</th>
            <th width="60">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $groups = [
                'labor'     => 'TENAGA KERJA',
                'product'   => 'HARGA BAHAN',
                'equipment' => 'HARGA ALAT',
            ];
        @endphp
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @foreach($groups as $key => $label)

            <tr class="table-secondary fw-bold">
                <td colspan="9">{{ $label }}</td>
            </tr>

            @php
                $no = 1;
                $subtotal = 0;
            @endphp

            @foreach($items->where('category', $key) as $item)
                @php $subtotal += $item->total_price; @endphp
                <tr 
                    data-item-id="{{ $item->id }}"
                    data-category="{{ $item->category }}"
                    data-total="{{ (float) $item->total_price }}"
                >

                    <td class="text-center">{{ $no++ }}</td>
                    <td>
                        <select class="form-select select2 uraian-change"
                                data-item-id="{{ $item->id }}"
                                data-category="{{ $item->category }}">

                            @if($item->category == 'labor')
                                @foreach($laborCosts as $lab)
                                    <option value="labor_{{ $lab->id }}"
                                        @selected($item->labor_cost_id == $lab->id)>
                                        {{ $lab->description }}
                                    </option>
                                @endforeach
                            @endif

                            @if($item->category == 'product')
                                @foreach($productSuppliers as $ps)
                                    <option value="product_{{ $ps->id }}"
                                        @selected($item->product_supplier_id == $ps->id)>

                                        {{ $ps->product->name }}
                                        {{-- - Rp {{ number_format($ps->selling_prices) }} --}}

                                        @if($ps->label)
                                            ({{ $ps->label }})
                                        @endif

                                    </option>
                                @endforeach
                            @endif

                            @if($item->category == 'equipment')
                                @foreach($equipments as $eq)
                                    <option value="equipment_{{ $eq->id }}"
                                        @selected($item->equipment_cost_id == $eq->id)>
                                        {{ $eq->description }}
                                    </option>
                                @endforeach
                            @endif

                        </select>
                    </td>
                    <td>
                        @if($item->product_id)
                        <select class="form-select select2 supplier-change"
                                data-item-id="{{ $item->id }}"
                                data-product-id="{{ $item->product_id }}">
                            @foreach($item->product->suppliers as $sup)
                                <option value="{{ $sup->id }}"
                                    @selected($item->supplier_id == $sup->id)>
                                    {{ $sup->name }}
                                </option>
                            @endforeach
                        </select>
                        @endif
                    </td>

                    <td class="text-center" id="code_{{ $item->id }}">
                        {{ $item->code }}
                    </td>
                    <td class="text-center" id="unit_{{ $item->id }}">
                        {{ $item->unit }}
                    </td>
                    <td class="text-end">{{ number_format($item->coefisien, 4) }}</td>
                    <td class="text-end" id="unit_price_{{ $item->id }}">
                        Rp {{ number_format($item->base_unit_price, 3, ',', '.') }}
                    </td>
                    <td class="text-end" id="total_price_{{ $item->id }}">
                        Rp {{ number_format($item->total_price, 3, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <form action="{{ route('job-categories.items.delete', $item->id) }}"
                            method="POST"
                            onsubmit="return confirm('Hapus item ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-dark">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach

            <tr class="fw-bold">
                <td colspan="7" class="text-end">
                    JUMLAH {{ strtoupper(str_replace('.', '', $label)) }}
                </td>
                <td class="text-end">
                    Rp {{ number_format($subtotal, 2, ',', '.') }}
                </td>
                <td></td>
            </tr>

            <tr class="fw-bold">
                <td colspan="7" class="text-end">
                    INPUT HARGA MANUAL {{ strtoupper(str_replace('.', '', $label)) }}
                </td>
                <td>
                    <input type="text"
                        class="form-control form-control-sm text-end format-rp"
                        
                        @if($key == 'labor') id="effective_labor" value="{{ $jobCategory->effective_labor 
                            ? number_format($jobCategory->effective_labor, 2, ',', '.') 
                            : '' }}" 
                        @endif
                        @if($key == 'product') id="effective_product" value="{{ $jobCategory->effective_product 
                            ? number_format($jobCategory->effective_product, 2, ',', '.') 
                            : '' }}" 
                        @endif
                        @if($key == 'equipment') id="effective_equipment" value="{{ $jobCategory->effective_equipment 
                            ? number_format($jobCategory->effective_equipment, 2, ',', '.') 
                            : '' }}" 
                        @endif

                        placeholder="Kosong = otomatis">
                </td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>

@php
    $totalLabor = $items->where('category','labor')->sum('total_price');
    $totalProduct = $items->where('category','product')->sum('total_price');
    $totalEquipment = $items->where('category','equipment')->sum('total_price');

    $subTotal = $totalLabor + $totalProduct + $totalEquipment;

    // default overhead profit (misal 5%)
    $overheadPercent = $jobCategory->overhead_percent ?? 0;

    $overheadValue = $subTotal * ($overheadPercent / 100);
    $profitPercent = $jobCategory->profit_percent ?? 0;

    $profitValue = $subTotal * ($profitPercent / 100);
    $grandTotal = $subTotal + $overheadValue + $profitValue;
@endphp


<table class="table table-bordered table-sm mt-4">
    <tbody>
        {{-- SUBTOTAL --}}
        <tr>
            <td colspan="6" class="text-end fw-bold">JUMLAH (A + B + C)</td>
            <td class="text-end fw-bold" id="subtotal">
                Rp {{ number_format($subTotal, 3, ',', '.') }}
            </td>
        </tr>

        {{-- OVERHEAD --}}
        <tr>
            <td colspan="5" class="text-end fw-bold">Overhead</td>
            <td width="100">
                <input type="number"
                       step="0.01"
                       id="overhead_percent"
                       value="{{ $overheadPercent }}"
                       class="form-control form-control-md text-end">
            </td>
            <td class="text-end fw-bold" id="overhead_value">
                Rp {{ number_format($overheadValue, 0, ',', '.') }}
            </td>
        </tr>

        {{-- PROFIT --}}
        <tr>
            <td colspan="5" class="text-end fw-bold">Profit</td>
            <td width="100">
                <input type="number"
                       step="0.01"
                       id="profit_percent"
                       value="{{ $profitPercent }}"
                       class="form-control form-control-md text-end">
            </td>
            <td class="text-end fw-bold" id="profit_value">
                Rp {{ number_format($profitValue, 0, ',', '.') }}
            </td>
        </tr>

        {{-- GRAND TOTAL --}}
        <tr class="table-success">
            <td colspan="6" class="text-end fw-bold">
                HARGA SATUAN PEKERJAAN
            </td>
            <td class="text-end fw-bold" id="grand_total">
                Rp {{ number_format($grandTotal, 3, ',', '.') }}
            </td>
        </tr>
    </tbody>
</table>

@push('js')
<script>
    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "-- Pilih --",
            width: '100%'
        });
        let isUpdatingFromServer = false;
        function formatRp(num) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num || 0);
        }
        function parseRp(val) {
            if (!val) return null;

            return parseFloat(
                val
                    .replace(/\./g, '')  // hapus ribuan
                    .replace(',', '.')  // koma → titik
            );
        }

        function recalcAll() {
            if (isUpdatingFromServer) return;

            let totalLabor = 0;
            let totalProduct = 0;
            let totalEquipment = 0;

            document.querySelectorAll('tr[data-item-id]').forEach(row => {
                let total = parseFloat(row.dataset.total || 0);
                let category = row.dataset.category;

                if (category === 'labor') {
                    totalLabor += total;
                } else if (category === 'product') {
                    totalProduct += total;
                } else if (category === 'equipment') {
                    totalEquipment += total;
                }
            });

            let effectiveLabor     = parseRp($('#effective_labor').val());
            let effectiveProduct   = parseRp($('#effective_product').val());
            let effectiveEquipment = parseRp($('#effective_equipment').val());

            effectiveLabor     = (effectiveLabor == null)     ? totalLabor     : effectiveLabor;
            effectiveProduct   = (effectiveProduct == null)   ? totalProduct   : effectiveProduct;
            effectiveEquipment = (effectiveEquipment == null) ? totalEquipment : effectiveEquipment;

            let subtotal = effectiveLabor + effectiveProduct + effectiveEquipment;

            const overheadPercent = parseFloat($('#overhead_percent').val()) || 0;
            const profitPercent   = parseFloat($('#profit_percent').val()) || 0;

            const overheadValue = subtotal * (overheadPercent / 100);
            const profitValue   = subtotal * (profitPercent / 100);
            const grandTotal    = subtotal + overheadValue + profitValue;

            $('#subtotal').text(formatRp(subtotal));
            $('#overhead_value').text(formatRp(overheadValue));
            $('#profit_value').text(formatRp(profitValue));
            $('#grand_total').text(formatRp(grandTotal));

            return {
                subtotal,
                overheadValue,
                profitValue,
                grandTotal
            };
        }
        $(document).on('input', '.format-rp', function () {
            if (isUpdatingFromServer) return;
            let input = this;

            let start = input.selectionStart;
            let end   = input.selectionEnd;

            let oldValue = input.value;

            let clean = oldValue.replace(/[^\d,]/g, '');

            let parts = clean.split(',');

            let integerPart = parts[0] || '';
            let decimalPart = parts[1] || '';

            decimalPart = decimalPart.substring(0, 2);

            let formattedInteger = integerPart
                .replace(/^0+(?=\d)/, '') 
                .replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            let newValue = formattedInteger;

            if (parts.length > 1) {
                newValue += ',' + decimalPart;
            }

            let diff = newValue.length - oldValue.length;

            input.value = newValue;

            let newPos = start + diff;
            input.setSelectionRange(newPos, newPos);

            recalcAll();

            clearTimeout(window.saveTimer);
            window.saveTimer = setTimeout(() => {
                saveEffective();
                autoSave();
            }, 500);
        });
        $('#effective_labor, #effective_product, #effective_equipment').on('input', function () {
            if (isUpdatingFromServer) return;
            recalcAll();

            clearTimeout(window.saveTimer);
            window.saveTimer = setTimeout(() => {
                saveEffective();
                autoSave(); // biar subtotal ikut ke-save juga
            }, 500);
        });
        function saveEffective() {
            $.post(
                "{{ route('job-categories.update-effective', $jobCategory->id) }}",
                {
                    _token: "{{ csrf_token() }}",
                    effective_labor: parseRp($('#effective_labor').val()) ?? null,
                    effective_product: parseRp($('#effective_product').val()) ?? null,
                    effective_equipment: parseRp($('#effective_equipment').val()) ?? null
                }
            );
        }
        function autoSave() {
            if (isUpdatingFromServer) return;
            const result = recalcAll();
            if (!result) return;
            $.post(
                "{{ route('job-categories.save-overhead-profit', $jobCategory->id) }}",
                {
                    _token: "{{ csrf_token() }}",
                    overhead_percent: parseFloat($('#overhead_percent').val()) || 0,
                    profit_percent: parseFloat($('#profit_percent').val()) || 0,
                    overhead_value: result.overheadValue,
                    profit_value: result.profitValue,
                    subtotal: result.subtotal,
                    grand_total: result.grandTotal
                }
            );
        }

        $('#overhead_percent, #profit_percent').on('input', function () {
            recalcAll();
            clearTimeout(window.saveTimer);
            window.saveTimer = setTimeout(autoSave, 500);
        });

        recalcAll();

        $(document).on('change', '.supplier-change', function () {

            let itemId = this.dataset.itemId;
            let supplierId = this.value;

            fetch(`/job-items/${itemId}/change-supplier`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    supplier_id: supplierId
                })
            })
            .then(res => res.json())
            .then(data => {

                // Update row
                document.getElementById('unit_price_' + itemId).innerText = formatRp(data.item.base_unit_price);
                document.getElementById('total_price_' + itemId).innerText = formatRp(data.item.total_price);

                document.getElementById('subtotal').innerText = formatRp(data.summary.subtotal);
                document.getElementById('overhead_value').innerText = formatRp(data.summary.overhead_value);
                document.getElementById('profit_value').innerText = formatRp(data.summary.profit_value);
                document.getElementById('grand_total').innerText = formatRp(data.summary.grand_total);
            });   
        });
        $(document).on('change', '.uraian-change', function () {
            isUpdatingFromServer = true;
            let el = $(this);
            let itemId = el.data('item-id');
            let value = el.val();

            fetch(`/job-items/${itemId}/change-uraian`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    value: value,
                    effective_labor: parseRp($('#effective_labor').val()) ?? null,
                    effective_product: parseRp($('#effective_product').val()) ?? null,
                    effective_equipment: parseRp($('#effective_equipment').val()) ?? null
                })
            })
            .then(res => res.json())
            .then(data => {

                if (!data.success) return;

                $('#unit_price_' + itemId).text(formatRp(data.item.base_unit_price));
                $('#total_price_' + itemId).text(formatRp(data.item.total_price));
                $('#code_' + itemId).text(data.item.code);
                $('#unit_' + itemId).text(data.item.unit);
                let row = document.querySelector(`tr[data-item-id="${itemId}"]`);
                if (row) {
                    row.dataset.total = parseFloat(data.item.total_price || 0);
                }
                $('#subtotal').text(formatRp(data.summary.subtotal));
                $('#overhead_value').text(formatRp(data.summary.overhead_value));
                $('#profit_value').text(formatRp(data.summary.profit_value));
                $('#grand_total').text(formatRp(data.summary.grand_total));
                isUpdatingFromServer = false;
            });

        });
    });
</script>
@endpush