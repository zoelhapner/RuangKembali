@can('lihat daftar proyek')
<form action="{{ route('projects.offerbuild.store') }}" method="POST">
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
            <label class="form-label">Nomor Penawaran</label>
            <input type="text" name="offer_number" class="form-control" value="{{ old('offer_number') ?? '' }}" placeholder="AUTO GENARATE" readonly>

        </div>
        <div class="col-md-4">
            <label class="form-label required">Tanggal Penawaran</label>
            <input type="date" name="offer_date" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Nama Customer</label>
            <input type="text" name="contact_name" value="{{ $project->customer->user->fullname }}" class="form-control" readonly>
        </div>
    </div>

    <div class="row mb-4 mt-4">
        <div class="col-md-4">
            <label class="form-label">Pilih RAB</label>
            <select name="rab_process_id"
                    id="rab_process_id"
                    class="form-select select2"
                    required>

                <option value="">-- pilih RAB --</option>

                @foreach($rabProcesses as $rab)
                    <option value="{{ $rab->id }}">
                        {{ $rab->project->project_name ?? '-' }} — {{ $rab->project->customer->user->fullname ?? '-' }}
                    </option>
                @endforeach

            </select>
        </div>
    </div>

        <div class="row mb-4">
            <h4 class="fw-bold mb-3">Rincian Pekerjaan</h4>

            <table class="table table-bordered align-middle" id="offerItemsTable">
                <thead>
                    <tr>
                        <th width="50">NO</th>
                        <th>URAIAN PEKERJAAN</th>
                        <th>SAT</th>
                        <th>VOL</th>
                        <th>HARGA SATUAN</th>
                        <th>JUMLAH HARGA</th>            
                    </tr>
                </thead>

                <tbody id="buildItemsBody">
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">SUBTOTAL</th>
                        <th id="subtotalDisplay">Rp 0</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">DISCOUNT</th>
                        <th>
                        <input type="text" class="form-control rupiah" id="discount_display" readonly>
                        <input type="hidden" name="discount" id="discount">

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
                                name="tax_rate" id="tax_rate" readonly>
                        </th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">TOTAL TAX</th>
                        <th id="totalTaxDisplay">Rp 0</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">SHIPPING / HANDLING</th>
                        <th>
                        <input type="text" class="form-control rupiah" id="shipping_display" readonly>
                        <input type="hidden" name="shipping" id="shipping">
                        </th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">GRAND TOTAL</th>
                        <th id="grandTotalDisplay">Rp 0</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- @if(optional($rab)->notes)
            <div class="mt-4">
                <h5 class="fw-bold">Keterangan</h5>
                <div class="border p-3">{{ $rab->notes }}</div>
            </div>
        @endif --}}

    <div class="text-end mt-4">
        <button class="btn btn-dark">Simpan Penawaran</button>
    </div>
</form>
@endcan

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

function loadRabItems(){

    let rabId = $('#rab_process_id').val()
    if(!rabId) return

    $.get(`/rab-process/${rabId}/items`,function(res){

        const tbody = $('#buildItemsBody')
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
        $('#tax_rate').val(res.header.tax_rate)

        setRupiah('#discount_display',res.header.discount)
        setRupiah('#shipping_display',res.header.shipping)

        $('#discount').val(res.header.discount)
        $('#shipping').val(res.header.shipping)

        $('#subtotalDisplay').text(formatRupiah(res.header.subtotal))
        $('#subAfterDiscountDisplay').text(formatRupiah(res.header.subtotal_after_discount))
        $('#totalTaxDisplay').text(formatRupiah(res.header.tax_total))
        $('#grandTotalDisplay').text(formatRupiah(res.header.grand_total))

    })

}

// trigger select
$('#rab_process_id').on('change',loadRabItems)

// auto load jika edit
$(document).ready(function(){

    if($('#rab_process_id').val()){
        loadRabItems()
    }

})

</script>
@endpush