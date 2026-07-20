@can('lihat daftar proyek')
<form id="rabForm" action="{{ route('projects.rab.store') }}" method="POST" enctype="multipart/form-data">
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

    <h4 class="fw-bold mb-3">Informasi Pembuatan Rab</h4>

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Nama Customer</label>
            <input type="text" name="contact_name" value="{{ old('contact_name', $project->customer->user->fullname ?? '') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Lokasi Pekerjaan</label>
            <input type="text" name="job_location" value="{{ old('job_location', $project->city->name ?? '-') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Durasi Pekerjaan</label>
            <input type="text" name="job_duration" class="form-control" value="{{ old('job_duration') }}" placeholder="Total rencana pengerjaan berdasarkan hari kerja">
        </div>
        <div class="col-md-2">
            <label class="form-label">Profit</label>
            <input type="number" class="form-control" id="rab_profit_display" name="profit">
        </div>

        <div class="col-md-2">
            <label class="form-label">Overhead</label>
            <input type="number" class="form-control" id="rab_overhead_display" name="overhead">
        </div>
    </div>
    <select style="display:none" id="jobCategorySelect">
        <option value="">-- Tambah AHSP --</option>
        @foreach($jobCategories as $job)
            <option value="{{ $job->id }}">
                {{ $job->nama_pekerjaan }}
            </option>
        @endforeach
    </select>
  
    <div class="row mb-4 mt-3">
        <h4 class="fw-bold mb-3">Rincian Pekerjaan</h4>
        <div class="mb-2 d-flex gap-2">
            <button type="button" id="tombolUbah" class="btn btn-dark btn-sm">
                ✏️ Mode Edit
            </button>

            <button type="button" id="tombolGeser" class="btn btn-outline-secondary btn-sm">
                🔀 Urutkan Daftar Pekerjaan
            </button>
        </div>
        <table class="table table-bordered align-middle" id="rabItemsTable">
            <colgroup>
                <col><col><col><col><col><col><col>
            </colgroup>
            <thead>
                <tr>
                    <th width="50">NO</th>
                    <th>URAIAN PEKERJAAN</th>
                    <th>SAT</th>
                    <th>VOL</th>
                    <th>HARGA SATUAN</th>
                    <th>JUMLAH HARGA</th>
                    <th width="1%"></th>
                </tr>
            </thead>
            <tbody id="rab_offerItemsBody">
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
                        <button type="button"
                            class="btn btn-link fw-bold text-decoration-none"
                            onclick="addCategory()">
                            + Kategori Pekerjaan
                        </button>
                    </td>
                </tr>
                <tr>
                    <th colspan="5" class="text-end">SUBTOTAL</th>
                    <th id="rab_subtotalDisplay">Rp 0</th>
                </tr>
                <tr>
                    <th colspan="5" class="text-end">DISCOUNT</th>
                    <th>
                        <input type="text" class="form-control" id="rab_discount_display">
                        <input type="hidden" name="discount" id="rab_discount">
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="text-end">SUBTOTAL AFTER DISCOUNT</th>
                    <th id="rab_subAfterDiscountDisplay">Rp 0</th>
                </tr>
                <tr>
                    <th colspan="5" class="text-end">TAX RATE (%)</th>
                    <th>
                        <input type="number" class="form-control"
                            name="tax_rate" id="rab_tax_rate">
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="text-end">TOTAL TAX</th>
                    <th id="rab_totalTaxDisplay">Rp 0</th>
                </tr>
                <tr>
                    <th colspan="5" class="text-end">SHIPPING / HANDLING</th>
                    <th>
                        <input type="text" class="form-control" id="rab_shipping_display">
                        <input type="hidden" name="shipping" id="rab_shipping">
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="text-end">GRAND TOTAL</th>
                    <th id="rab_grandTotalDisplay">Rp 0</th>
                </tr>

            </tfoot>
        </table>
    </div>
    <div class="modal fade" id="uraianGalleryModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content gambar-modal">

                <div class="modal-header border-0">
                    <div>
                    <h5 class="modal-title fw-semibold" id="modalTitle"></h5>
                    <small class="text-muted">Upload dokumentasi pekerjaan</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class= "upload-area mb-3">
                    <input type="file"
                        multiple
                        accept="image/*"
                        class="form-control mb-3"
                        id="uraianImageInput">
                    </div>

                    <div id="uraianGallery" class="gambar-preview">
                    </div>

                </div>

            </div>
        </div>
    </div>
        <input type="hidden" name="subtotal" id="rab_subtotal">
        <input type="hidden" name="subtotal_after_discount" id="rab_subAfterDiscount">
        <input type="hidden" name="tax_total" id="rab_tax_total">
        <input type="hidden" name="grand_total" id="rab_grand_total">                  
    <div id="rabItemsContainer"></div>
    <h4 class="fw-bold mb-3">Keterangan</h4>

    <textarea name="notes" rows="3" class="form-control"></textarea>

    {{-- <div class="text-end mt-4">
        <button type="submit" class="btn btn-dark px-4">
            <i class="ti ti-device-floppy me-1"></i>Simpan RAB
        </button>
    </div> --}}
</form>
@endcan

@push('js')
<script>
    let globalProfit = 0
    let globalOverhead = 0
    let currentBasePrice = 0
    let currentRabJob = null
    let rabItems = {}
    let categoryIndex = 0
    let uraianIndex = {}
    let jobIndex = 0;
    let draggedGroup = []
    let uraianImages = {}
    let activeUraian = null
    let currentMode = 'edit'
    let sortableInstance = null

    function parseRupiah(value){

        if(!value) return 0

        return Number(
            value
            .toString()
            .replace(/[^0-9]/g,'')
        )

    }

    function formatRupiah(number){

        number = Number(number) || 0

        return 'Rp ' + number.toLocaleString('id-ID')

    }
    function rupiahInput(el){

        let number = parseRupiah(el.value)

        el.dataset.value = number

        el.value = formatRupiah(number)

    }

    function numberToLettersrab(num){
        let letters = ''
        num = num + 1 // karena A = 1, bukan 0

        while(num > 0){
            let rem = (num - 1) % 26
            letters = String.fromCharCode(65 + rem) + letters
            num = Math.floor((num - 1) / 26)
        }

        return letters
    }

    function setModeCreate(mode){

        currentMode = mode
        const btnEdit = document.getElementById('tombolUbah')
        const btnDrag = document.getElementById('tombolGeser')

        // RESET dulu
        btnEdit.classList.remove('btn-dark')
        btnEdit.classList.add('btn-outline-secondary')

        btnDrag.classList.remove('btn-dark')
        btnDrag.classList.add('btn-outline-secondary')

        if(mode === 'edit'){
            btnEdit.classList.remove('btn-outline-secondary')
            btnEdit.classList.add('btn-dark')
        }

        if(mode === 'drag'){
            btnDrag.classList.remove('btn-outline-secondary')
            btnDrag.classList.add('btn-dark')
        }

        if(mode === 'edit'){

            document.body.classList.remove('drag-mode')

            document.querySelectorAll('input, select, textarea').forEach(el=>{
                el.disabled = false
            })

            if(sortableInstance){
                sortableInstance.destroy()
                sortableInstance = null
            }

            // bersihin sisa drag
            document.querySelectorAll('.job-row, .uraian-row, .category-row')
            .forEach(el => {
                el.style.transform = ''
                el.style.transition = ''
                el.classList.remove('sortable-chosen','sortable-ghost','sortable-drag')
            })

            // reinit select2
            $('.select2-row').each(function(){
                if($(this).hasClass("select2-hidden-accessible")){
                    $(this).select2('destroy')
                }
            })

            $('.select2-row').select2({
                width: '100%',
                dropdownAutoWidth: true
            })

        }

        if(mode === 'drag'){

            document.body.classList.add('drag-mode')

            initSortableCreate()
        }
    }
    let reorderTimer = null

    function initSortableCreate(){

        const tbody = document.getElementById('rab_offerItemsBody_edit')

        sortableInstance = new Sortable(tbody,{
            animation:150,
            handle:'.drag-handle,.drag-ahsp',
            draggable:'.category-row, .uraian-row, .job-row',

            onStart:function(evt){
                isDragging = true
                const row = evt.item
                draggedGroup = [row]

                if(row.classList.contains('category-row')){
                    let next = row.nextElementSibling
                    while(next && !next.classList.contains('category-row')){
                        draggedGroup.push(next)
                        next = next.nextElementSibling
                    }
                }

                if(row.classList.contains('uraian-row')){
                    const uraianId = row.id
                    document.querySelectorAll(`[data-parent="${uraianId}"]`)
                        .forEach(r=>draggedGroup.push(r))
                }
            },

            onEnd:function(evt){
                isDragging = false
                const row = evt.item

                if(draggedGroup.length > 1){
                    let insertPoint = row.nextElementSibling
                    draggedGroup.slice(1).forEach(r=>{
                        tbody.insertBefore(r, insertPoint)
                    })
                }

                draggedGroup = []

                renumberAll()

                setTimeout(()=>{
                    saveOrderToServerrab()
                },100)
            },

            onMove: function(evt){
                const dragged = evt.dragged
                const related = evt.related

                if(dragged.classList.contains('job-row')){
                    return dragged.dataset.parent === related.dataset.parent
                }

                return true
            }
        })
    }
    function isDragModeCreate(){
        return currentMode === 'drag'
    }
    function collectOrderCreate(){

        let data = []

        document.querySelectorAll('.category-row').forEach((cat, catIndex) => {

            const catId = cat.dataset.id
            if(!catId) return

            let catData = {
                id: catId,
                order: catIndex,
                uraians: []
            }

            document.querySelectorAll(`.uraian-row[data-category="${cat.id}"]`)
            .forEach((uraian, uraianIndex) => {

                const uraianId = uraian.dataset.id
                if(!uraianId) return

                let uraianData = {
                    id: uraianId,
                    order: uraianIndex,
                    items: []
                }

                document.querySelectorAll(`.job-row[data-parent="${uraian.id}"]`)
                .forEach((row, itemIndex) => {

                    const itemId = row.dataset.id
                    if(!itemId) return

                    uraianData.items.push({
                        id: itemId,
                        order: itemIndex
                    })
                })

                catData.uraians.push(uraianData)
            })

            data.push(catData)
        })

        return data
    }
    let isReordering = false

    function saveOrderToServerrab(){

        if(isReordering) return

        isReordering = true

        fetch(`/rab/reorder/${window.currentRabId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                structure: collectOrderCreate()
            })
        })
        .catch(err => console.error('Reorder error:', err))
        .finally(() => {
            isReordering = false
        })
    }
    function addCategory(){
        if(isDragModeCreate()) return
        const tbody = document.getElementById('rab_offerItemsBody')

        let letter = numberToLettersrab(categoryIndex)
        let catId = 'cat_'+categoryIndex

        uraianIndex[catId] = 1

        tbody.insertAdjacentHTML('beforeend',`

        <tr class="table-secondary fw-bold category-row" id="${catId}" data-category="${catId}">
            <td>
                <span class="drag-handle me-2" style="cursor:move">
                    <i class="ti ti-grip-vertical"></i>
                </span>
                ${letter}
            </td>

            <td colspan="5">
                <input type="text" class="form-control fw-bold"
                    placeholder="Nama kategori pekerjaan"
                    onkeydown="if(event.key==='Enter') saveCategory('${catId}')">
            </td>

            <td></td>
        </tr>

        <tr class="no-drag" id="addUraian_${catId}">
            <td></td>
            <td colspan="6">
                <button type="button" class="btn btn-sm btn-link"
                    onclick="addUraian('${catId}')">
                    + Uraian Pekerjaan
                </button>
            </td>
        </tr>
        `)

        categoryIndex++
    }

    function saveCategory(catId){

        const row = document.getElementById(catId)
        const input = row.querySelector('input');

        const name = input.value || 'Kategori Baru';

        row.innerHTML = `
            <td>
                <span class="drag-handle me-2" style="cursor:move">
                    <i class="ti ti-grip-vertical"></i>
                </span>
                ${row.cells[0].innerText}
            </td>

            <td colspan="4" class="fw-bold">
                ${name}
            </td>

            <td>
                <input type="text"
                    class="form-control subtotal-category" data-category="${catId}"
                    value="Rp 0"
                    readonly>
            </td>

            <td>
                <button class="btn btn-sm btn-secondary"
                    onclick="removeCat('${catId}')">
                    -
                </button>
            </td>
        `;
    }

    function addUraian(catId){
        if(isDragModeCreate()) return
        const addRow = document.getElementById('addUraian_'+catId)

        let uraianNo = uraianIndex[catId]++
        let uraianId = 'uraian_'+(jobIndex++)

        addRow.insertAdjacentHTML('beforebegin',`

        <tr class="uraian-row" id="${uraianId}" data-category="${catId}">
            <td class="text-center fw-bold">${uraianNo}</td>

            <td colspan="5">
                <div class="d-flex align-items-center gap-2">

                    <span class="drag-handle" style="cursor:move">
                        <i class="ti ti-grip-vertical"></i>
                    </span>

                    <input class="form-control uraian-input"
                        placeholder="Uraian pekerjaan"
                        onkeydown="if(event.key==='Enter') saveUraian('${uraianId}')">
                </div>
            </td>

            <td>
                <button class="btn btn-sm btn-secondary"
                    onclick="removeUraian('${uraianId}')">
                    -
                </button>
            </td>
        </tr>

        `)
        renumberUraian(catId)
    }

    function saveUraian(uraianId){

        const row = document.getElementById(uraianId)
        if(!row) return
        
        const input = row.querySelector('.uraian-input')
        
        if(!input){
            console.warn('Input uraian tidak ditemukan', uraianId)
            return
        }

        const name = input.value || 'Uraian Baru'

        row.dataset.name = name

        row.cells[1].innerHTML = `
        <div class="d-flex align-items-center gap-2">

            <span class="drag-handle" style="cursor:move">
                <i class="ti ti-grip-vertical"></i>
            </span>

            <span>${name}</span>
            <button type="button"
                class="btn btn-sm btn-gambar"
                data-uraian="${name}"
                onclick="openUraianGallery('${uraianId}', '${name}')">

                <i class="ti ti-photo"></i>
            </button>

        </div>
        `

        addJobRow(uraianId)
    }

    function addJobRow(uraianId){
        if(isDragModeCreate()) return
        const originalSelect = document.getElementById('jobCategorySelect')
        const options = originalSelect.innerHTML

        const idx = jobIndex++
        const jobId = 'job_'+idx

        const uraianRow = document.getElementById(uraianId)

        let lastRow = uraianRow

        document.querySelectorAll(`[data-parent="${uraianId}"]`)
            .forEach(row => lastRow = row)

        lastRow.insertAdjacentHTML('afterend',`

        <tr class="job-row"
            id="${jobId}"
            data-parent="${uraianId}"
            data-category="${document.getElementById(uraianId).dataset.category}"
            data-index="${idx}">

            <td></td>

            <td>
                <div class="d-flex align-items-center">

                    <span class="drag-ahsp me-2" style="cursor:move">
                        <i class="ti ti-grip-vertical"></i>
                    </span>

                    <div class="flex-grow-1">

                        <select class="form-select select2-row job-select w-100"
                            onchange="loadJob('${jobId}', this.value)">
                        ${options}
                        </select>
                    </div>
                </div>
            </td>

            <td>
                <span class="sat"></span>
            </td>

            <td>
                <input type="number"
                    step="0.01"
                    class="form-control vol"
                    oninput="calculate('${jobId}')">
            </td>

            <td>
                <input type="text"
                    class="form-control harga"
                    readonly>

            </td>

            <td>
                <input type="text"
                    class="form-control total"
                    readonly>
            </td>

            <td>

                <button type="button"
                    class="btn btn-sm btn-dark"
                    onclick="addJobRow('${uraianId}')">
                +
                </button>

                <button type="button"
                    class="btn btn-sm btn-secondary"
                    onclick="removeJob('${jobId}')">
                -
                </button>

            </td>

        </tr>
        `)

        $('.select2-row').select2()
    }

    function loadJob(rowId, jobId){

        if(!jobId) return

        fetch(`/job-categories/${jobId}/simple`)
        .then(res => res.json())
        .then(job => {

            const row = document.getElementById(rowId)

            const sat = row.querySelector('.sat')
            if(sat) sat.innerText = job.satuan
            
            const satInput = row.querySelector('.satuan')
            if(satInput) satInput.value = job.satuan

            const jobName = row.querySelector('.job_name')
            if(jobName) jobName.value = job.name

            const basePrice = row.querySelector('.base_price')
            if(basePrice) basePrice.value = job.harga

            const hargaInput = row.querySelector('.harga')
            if(hargaInput){
                hargaInput.dataset.value = job.harga
                hargaInput.value = formatRupiah(job.harga)
            }

            calculate(rowId)
            updateHargaSemua()
        })
    }

    function calculate(rowId){

        const row = document.getElementById(rowId)

        let vol = Number(row.querySelector('.vol').value) || 0

        let hargaInput = row.querySelector('.harga')

        let basePrice = Number(hargaInput.dataset.value || 0)

        let profitValue   = basePrice * (globalProfit / 100)
        let overheadValue = basePrice * (globalOverhead / 100)

        let hargaFinal = basePrice + profitValue + overheadValue

        let total = vol * hargaFinal

        const hargaEl = row.querySelector('.harga')
        const totalEl = row.querySelector('.total')

        hargaEl.value = formatRupiah(hargaFinal)

        totalEl.dataset.value = total
        totalEl.value = formatRupiah(total)

        rabItems[rowId] = {
            volume: vol,
            base_price: basePrice,
            harga: hargaFinal,
            total: total
        }

        updateCategorySubtotal(row.dataset.parent)

        calculateSummary()
    }
    function updateCategorySubtotal(uraianId){

        const uraianRow = document.getElementById(uraianId)

        if(!uraianRow) return

        const catId = uraianRow.dataset.category

        let subtotal = 0
        document.querySelectorAll(`.job-row[data-category="${catId}"]`)
        .forEach(row=>{

            const totalInput = row.querySelector('.total')

            subtotal += Number(totalInput.dataset.value || 0)

        })

        const subtotalInput = document.querySelector(
            `.subtotal-category[data-category="${catId}"]`
        )

        if(subtotalInput){

            subtotalInput.dataset.value = subtotal
            subtotalInput.value = formatRupiah(subtotal)

        }

    }
    function calculateSummary(){

        let subtotal = 0

        document.querySelectorAll('.total').forEach(el=>{
            subtotal += Number(el.dataset.value || 0)
        })

        // tampilkan subtotal
        document.getElementById('rab_subtotal').value = subtotal
        document.getElementById('rab_subtotalDisplay').innerText = formatRupiah(subtotal)

        // discount
        let discount = Number(document.getElementById('rab_discount').value || 0)

        let subAfterDiscount = subtotal - discount

        document.getElementById('rab_subAfterDiscount').value = subAfterDiscount
        document.getElementById('rab_subAfterDiscountDisplay').innerText = formatRupiah(subAfterDiscount)

        // tax
        let taxRate = Number(document.getElementById('rab_tax_rate').value || 0)

        let taxTotal = subAfterDiscount * taxRate / 100

        document.getElementById('rab_tax_total').value = taxTotal
        document.getElementById('rab_totalTaxDisplay').innerText = formatRupiah(taxTotal)

        // shipping
        let shipping = Number(document.getElementById('rab_shipping').value || 0)

        // grand total
        let grand = subAfterDiscount + taxTotal + shipping

        const grandEl = document.getElementById('rab_grandTotalDisplay')

        grandEl.dataset.value = grand
        grandEl.innerText = formatRupiah(grand)

        document.getElementById('rab_grand_total').value = grand
    }
    function removeCat(catId){

        const catRow = document.getElementById(catId)

        if(!catRow) return

        // hapus semua uraian + job dalam kategori
        document.querySelectorAll(`.uraian-row[data-category="${catId}"]`)
        .forEach(uraian=>{

            const uraianId = uraian.id

            // hapus job dalam uraian
            document.querySelectorAll(`[data-parent="${uraianId}"]`)
            .forEach(job=>job.remove())

            uraian.remove()
        })

        // hapus tombol + uraian
        const addRow = document.getElementById('addUraian_'+catId)
        if(addRow) addRow.remove()

        // hapus kategori
        catRow.remove()

        // reset numbering
        renumberCategory()

        // hitung ulang
        calculateSummary()
    }
    function removeJob(id){

        const row = document.getElementById(id)

        if(!row) return

        const uraianId = row.dataset.parent

        row.remove()

        updateCategorySubtotal(uraianId)
        calculateSummary()

    }
    function removeUraian(id){
        const row = document.getElementById(id)
        const catId = row.dataset.category
        document.querySelectorAll(`[data-parent="${id}"]`).forEach(e=>e.remove())
        row.remove()
        renumberUraian(catId)
        calculateSummary()
    }
    function renumberUraian(catId){
        let rows = document.querySelectorAll(`.uraian-row[data-category="${catId}"]`)
        rows.forEach((row,i)=>{
            row.querySelector('td').innerText = i+1
        })
        uraianIndex[catId] = rows.length + 1
    }
    function renumberAll(){
        document.querySelectorAll('.category-row').forEach(cat=>{
            const catId = cat.dataset.category
            const uraianRows = document.querySelectorAll(`.uraian-row[data-category="${catId}"]`)
            uraianRows.forEach((row,i)=>{
                row.querySelector('td:first-child').innerText = i+1
            })
            uraianIndex[catId] = uraianRows.length + 1
        })
    }
    function renumberCategory(){

        const categories = document.querySelectorAll('.category-row')

        categories.forEach((cat,i)=>{

            const letter = numberToLettersrab(i)

            cat.querySelector('td').innerHTML = `
                <span class="drag-handle me-2" style="cursor:move">
                    <i class="ti ti-grip-vertical"></i>
                </span>
                ${letter}
            `
        })

        categoryIndex = categories.length
    }
    function recalcAfterDrag(){

        document.querySelectorAll('.job-row').forEach(row=>{
            calculate(row.id)
        })

    }
    function openUraianGallery(uraianId, uraianName){

        activeUraian = uraianId

        $("#modalTitle").text(uraianName)

        if(!uraianImages[uraianId]){
            uraianImages[uraianId] = []
        }

        renderGallery()

        const modal = new bootstrap.Modal(
            document.getElementById('uraianGalleryModal')
        )

        modal.show()
    }
    function renderGallery(){

        const gallery = document.getElementById('uraianGallery')

        gallery.innerHTML = ''

        const images = uraianImages[activeUraian] || []

        if(images.length === 0){
            gallery.innerHTML = '<div class="text-muted">Belum ada gambar</div>'
            return
        }

        images.forEach((img,index)=>{

            gallery.insertAdjacentHTML('beforeend',`

            <div class="preview-item">

                <img src="${img.url}" class="img-thumbnail">

                <button type="button"
                    class="btn btn-sm remove-img"
                    onclick="removeUraianImage(${index})">

                    ×

                </button>

            </div>
            `)

        })
    }
    function removeUraianImage(index){

        const img = uraianImages[activeUraian][index]

        fetch('/rab-images/'+img.id,{
            method:'DELETE',
            headers:{
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
            }
        })

        uraianImages[activeUraian].splice(index,1)

        renderGallery()

    }
    function updateHargaSemua(){

        const profit = parseFloat(document.getElementById('rab_profit_display').value) || 0
        const overhead = parseFloat(document.getElementById('rab_overhead_display').value) || 0

        document.querySelectorAll('.job-row').forEach(row=>{

            const hargaInput = row.querySelector('.harga')

            const basePrice = parseFloat(hargaInput.dataset.value) || 0

            const newPrice =
                basePrice +
                (basePrice * profit / 100) +
                (basePrice * overhead / 100)

            hargaInput.value = formatRupiah(newPrice)

            calculate(row.id)

        })

    }

    document.getElementById('uraianImageInput').addEventListener('change',function(){

        const files = this.files

        Array.from(files).forEach(file=>{

            const formData = new FormData()
            formData.append('image', file)

            fetch('/rab-images/upload',{
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
                },
                body:formData
            })
            .then(res=>res.json())
            .then(img=>{

                if(!uraianImages[activeUraian]){
                    uraianImages[activeUraian] = []
                }

                uraianImages[activeUraian].push(img)

                renderGallery()

            })

        })

    })
    $(document).on("click",".btn-gambar",function(){

        let uraian = $(this).data("uraian");

        $("#modalTitle").text(uraian);

    });
    document.getElementById('rab_profit_display').addEventListener('input', function(){
        globalProfit = Number(this.value) || 0
        updateHargaSemua()
    })

    document.getElementById('rab_overhead_display').addEventListener('input', function(){
        globalOverhead = Number(this.value) || 0
        updateHargaSemua()
    })

    document.getElementById('rab_discount_display').addEventListener('input',function(){

        rupiahInput(this)

        document.getElementById('rab_discount').value =
            parseRupiah(this.value)

        calculateSummary()

    })

    document.getElementById('rab_shipping_display').addEventListener('input',function(){

        rupiahInput(this)

        document.getElementById('rab_shipping').value =
            parseRupiah(this.value)

        calculateSummary()

    })

    document.getElementById('rab_tax_rate').addEventListener('input', function () {
        calculateSummary();
    });
    document.querySelector('#rab_offerItemsBody').addEventListener('keydown', function(e){

        if(e.key === 'Enter'){

            if(
                e.target.classList.contains('uraian-input') ||
                e.target.closest('.category-row')
            ){
                return
            }

            e.preventDefault()
        }

    })
        document.getElementById('tombolUbah').addEventListener('click',()=>{
            setModeCreate('edit')
        })

        document.getElementById('tombolGeser').addEventListener('click',()=>{
            setModeCreate('drag')
        })
    document.addEventListener('DOMContentLoaded', function(){
        document.getElementById('rabForm').addEventListener('submit', function () {
            console.log("submit jalan")
            const container = document.getElementById('rabItemsContainer')
            container.innerHTML = ''

            let index = 0
        document.querySelectorAll('.category-row').forEach((row,i)=>{

            const name = row.querySelector('td:nth-child(2)')?.innerText || ''

            const input = document.createElement('input')
            input.type = 'hidden'
            input.name = `categories[${i}][key]`
            input.value = row.dataset.category

            container.appendChild(input)

            const input2 = document.createElement('input')
            input2.type = 'hidden'
            input2.name = `categories[${i}][name]`
            input2.value = name

            container.appendChild(input2)

        })
            document.querySelectorAll('.job-row').forEach(row => {

                const rowId = row.id
                const jobSelect = row.querySelector('.job-select')

                if(!jobSelect || !jobSelect.value) return
                const uraianId = row.dataset.parent
                const uraianRow = document.getElementById(uraianId)
                const categoryKey = uraianRow?.dataset.category
                const uraianName = uraianRow?.dataset.name || ''
                const jobName = jobSelect.options[jobSelect.selectedIndex].text
                const satuan = row.querySelector('.sat')?.innerText || ''

                const item = rabItems[rowId]
                if(!item) return

                const fields = {
                    category_key: categoryKey,
                    uraian_key: uraianId,
                    uraian_name: uraianName,
                    job_category_id: jobSelect.value,
                    job_name: jobName,
                    satuan: satuan,
                    volume: item.volume,
                    base_price: item.base_price,
                    price: item.harga,
                    total: item.total
                }

                Object.entries(fields).forEach(([key,val]) => {

                    const input = document.createElement('input')

                    input.type = 'hidden'
                    input.name = `items[${index}][${key}]`
                    input.value = val

                    container.appendChild(input)

                })

                index++

            })
            Object.entries(uraianImages).forEach(([uraianId,images])=>{

                images.forEach((img,i)=>{

                    const input = document.createElement('input')

                    input.type = 'hidden'
                    input.name = `uraian_images[${uraianId}][]`
                    input.value = img.id

                    container.appendChild(input)

                })

            })
        })
    })
</script>
@endpush