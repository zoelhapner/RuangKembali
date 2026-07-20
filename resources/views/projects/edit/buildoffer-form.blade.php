@php
$offer = $project->offer;
$items = $offer?->items ?? collect();

$grouped = [];

foreach ($items as $item) {

    $category = $item->category_name ?? 'Tanpa Kategori';
    $uraian   = $item->uraian_name ?? 'Tanpa Uraian';

    if (!isset($grouped[$category])) {
        $grouped[$category] = [
            'items' => [],
            'subtotal' => 0
        ];
    }

    if (!isset($grouped[$category]['items'][$uraian])) {
        $grouped[$category]['items'][$uraian] = [
            'items' => [],
            'subtotal' => 0
        ];
    }

    $grouped[$category]['items'][$uraian]['items'][] = $item;

    $grouped[$category]['items'][$uraian]['subtotal'] += $item->total;
    $grouped[$category]['subtotal'] += $item->total;
}
@endphp

<div class="card mb-4">
    <div class="card-header fw-bold"> Edit Data Penawaran</div>
    <div class="card-body">
        <form action="{{ route('offer-build.update', $offer->id) }}" method="POST">
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
                        class="form-control" readonly
                        value="{{ old('contact_name', $offer->contact_name) }}">
                </div>
            </div>

            <div class="row mb-4 mt-4">
                <div class="col-md-4">
                    <label class="form-label">Pilih RAB</label>

                    <select name="rab_process_id"
                            id="rab_process_idd"
                            class="form-select select2"
                            required>

                        <option value="">-- pilih RAB --</option>

                        @foreach($rabProcesses as $rab)
                            <option value="{{ $rab->id }}"
                                {{ old('rab_process_id', $offer->rab_process_id) == $rab->id ? 'selected' : '' }}>
                                
                                {{ $rab->project->project_name ?? '-' }}
                                —
                                {{ $rab->project->customer->user->fullname ?? '-' }}

                            </option>
                        @endforeach

                    </select>
                </div>
            </div>

            <div class="row mb-4">

                <h4 class="fw-bold mb-3">Rincian Pekerjaan</h4>

                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="50">NO</th>
                            <th>URAIAN PEKERJAAN</th>
                            <th>SAT</th>
                            <th>VOL</th>
                            <th>HARGA SATUAN</th>
                            <th>JUMAH HARGA</th>
                        </tr>
                    </thead>

                    <tbody id="buildItemsBodyEdit">
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">SUBTOTAL</th>
                            <th id="subtotalDisplayBuild">Rp 0</th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">DISCOUNT</th>
                            <th>
                            <input type="text" class="form-control rupiah" id="discount_display_build" readonly>
                            <input type="hidden" name="discount" id="discount_build">

                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">SUBTOTAL AFTER DISCOUNT</th>
                            <th id="subAfterDiscountDisplayBuild">Rp 0</th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">TAX RATE (%)</th>
                            <th>
                                <input type="number" class="form-control"
                                    name="tax_rate" id="tax_rate_build" readonly>
                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">TOTAL TAX</th>
                            <th id="totalTaxDisplayBuild">Rp 0</th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">SHIPPING / HANDLING</th>
                            <th>
                            <input type="text" class="form-control rupiah" id="shipping_display_build" readonly>
                            <input type="hidden" name="shipping" id="shipping_build">
                            </th>
                        </tr>

                        <tr>
                            <th colspan="5" class="text-end">GRAND TOTAL</th>
                            <th id="grandTotalDisplayBuild">Rp 0</th>
                        </tr>
                    </tfoot>
                </table>
                <input type="hidden" name="subtotal" id="subtotal_build">
                <input type="hidden" name="subtotal_after_discount" id="subtotal_after_discount_build">
                <input type="hidden" name="tax_total" id="tax_total_build">
                <input type="hidden" name="grand_total" id="grand_total_build">
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

function formatRupiah(n){
    return 'Rp ' + Number(n || 0).toLocaleString('id-ID')
}

function formatNumber(n){
    return Number(n || 0).toLocaleString('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })
}

function setRupiah(selector,val){
    $(selector).val(formatRupiah(val))
}
    function numberToLetters(num){
        let letters = ''
        num = num + 1 // karena A = 1, bukan 0

        while(num > 0){
            let rem = (num - 1) % 26
            letters = String.fromCharCode(65 + rem) + letters
            num = Math.floor((num - 1) / 26)
        }

        return letters
    }
function loadRabItemsEdit(){

    let rabId = $('#rab_process_idd').val()
    if(!rabId) return

    $.get(`/rab-process/${rabId}/items`,function(res){

        const tbody = $('#buildItemsBodyEdit')
        tbody.empty()

        let categoryLetter
        let uraianNo
        let itemNo

        res.categories.forEach((category,cIndex)=>{

            categoryLetter = numberToLetters(cIndex)

            // CATEGORY
            tbody.append(`
                <tr class="table-secondary fw-bold">
                    <td style="font-weight:600" colspan="6">
                        ${categoryLetter}. ${category.name}
                    </td>
                </tr>
            `)

            category.uraians.forEach((uraian,uIndex)=>{

                uraianNo = uIndex + 1

                // URAIAN
                tbody.append(`
                    <tr class="table-light fw-semibold">
                        <td>${uraianNo}</td>
                        <td colspan="5">
                            ${uraian.name}
                        </td>
                    </tr>
                `)

                itemNo = 1

                uraian.items.forEach(item=>{

                    tbody.append(`
                        <tr>
                            <td>${uraianNo}.${itemNo}</td>
                            <td style="padding-left:30px">
                                ${item.job_name}
                            </td>
                            <td>${item.satuan}</td>
                            <td>${formatNumber(item.volume)}</td>
                            <td>${formatRupiah(item.price)}</td>
                            <td>${formatRupiah(item.total)}</td>
                        </tr>
                    `)

                    itemNo++
                })

            })

        })

        // HEADER RAB
        $('#tax_rate_build').val(res.header.tax_rate)

        setRupiah('#discount_display_build',res.header.discount)
        setRupiah('#shipping_display_build',res.header.shipping)

        $('#discount_build').val(res.header.discount)
        $('#shipping_build').val(res.header.shipping)

        $('#subtotalDisplayBuild').text(formatRupiah(res.header.subtotal))
        $('#subAfterDiscountDisplayBuild').text(formatRupiah(res.header.subtotal_after_discount))
        $('#totalTaxDisplayBuild').text(formatRupiah(res.header.tax_total))
        $('#grandTotalDisplayBuild').text(formatRupiah(res.header.grand_total))
        $('#subtotal_build').val(res.header.subtotal)
        $('#subtotal_after_discount_build').val(res.header.subtotal_after_discount)
        $('#tax_total_build').val(res.header.tax_total)
        $('#grand_total_build').val(res.header.grand_total)

    })

}

// trigger select
$('#rab_process_idd').on('change',loadRabItemsEdit)

// auto load jika edit
$(document).ready(function(){

    if($('#rab_process_idd').val()){
        loadRabItemsEdit()
    }

})
</script>
@endpush