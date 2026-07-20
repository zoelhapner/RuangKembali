@php
    $latest = \Illuminate\Support\Facades\Cache::get('job_category_last_updated', 0);

    $needRefresh = $rab->analisa_version < $latest;

@endphp

<form action="{{ route('projects.rab.update', [$project->id, $rab->id]) }}" method="POST">
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
        @if($needRefresh)
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <div>
                ⚠️ Harga analisa sudah berubah dari versi terakhir RAB ini dibuat.
            </div>
            <button type="button" class="btn btn-dark" id="btnRefreshRab">
                🔄 Refresh Harga RAB
            </button>
        </div>
        @endif
    <h4 class="fw-bold mb-3">Informasi Pembuatan Rab</h4>

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Nama Customer</label>
            <input type="text" name="contact_name" value="{{ old('contact_name', $rab->contact_name) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Lokasi Pekerjaan</label>
            <input type="text" name="job_location" value="{{ old('job_location', $rab->job_location) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label required">Durasi Pekerjaan</label>
            <input type="text" name="job_duration" class="form-control" value="{{ old('job_duration', $rab->job_duration) }}" placeholder="175 Hari Kerja">
        </div>
        <div class="col-md-2">
            <label class="form-label">Profit</label>
            <input type="number" class="form-control" id="rab_profit_display_edit" value="{{ old('profit', $rab->profit) }}" step="0.01" min="0">
            <input type="hidden" name="profit" id="rab_profit_edit">
        </div>
        <div class="col-md-2">
            <label class="form-label">Overhead</label>
            <input type="number" class="form-control" id="rab_overhead_display_edit" value="{{ old('overhead', $rab->overhead) }}" step="0.01" min="0">
            <input type="hidden" name="overhead" id="rab_overhead_edit">
        </div>
    </div>
    <select style="display:none" id="jobCategorySelectEdit">
        <option value="">-- Pilih AHSP --</option>
        @foreach($jobCategories as $job) 
        <option value="{{ $job->id }}" > 
            {{ $job->nama_pekerjaan }} 
        </option> 
        @endforeach
    </select>
  
    <div class="row mb-4 mt-3">
        <h4 class="fw-bold mb-3">Rincian Pekerjaan</h4>
        <div class="mb-2 d-flex gap-2">
            <button type="button" id="btnEditMode" class="btn btn-dark btn-sm">
                ✏️ Mode Edit
            </button>

            <button type="button" id="btnDragMode" class="btn btn-outline-secondary btn-sm">
                🔀 Urutkan Daftar Pekerjaan
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="rabItemsTableEdit">
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
                <tbody id="rab_offerItemsBody_edit">
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <button type="button"
                                class="btn btn-link fw-bold text-decoration-none"
                                onclick="addCategoryEdit()">
                                + Kategori Pekerjaan
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-end">SUBTOTAL</th>
                        <th id="rab_subtotalDisplay_edit">Rp 0</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">DISCOUNT</th>
                        <th>
                            <input type="text" class="form-control"
                                id="rab_discount_display_edit"
                                value="{{ number_format($rab->discount,3,',','.') }}">
                            <input type="hidden" name="discount" id="rab_discount_edit" value="{{ $rab->discount }}">
                        </th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">SUBTOTAL AFTER DISCOUNT</th>
                        <th id="rab_subAfterDiscountDisplay_edit">Rp 0</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">TAX RATE (%)</th>
                        <th>
                            <input type="number" class="form-control"
                                id="rab_tax_rate_edit"
                                value="{{ $rab->tax_rate }}">
                        </th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">TOTAL TAX</th>
                        <th id="rab_totalTaxDisplay_edit">Rp 0</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">SHIPPING / HANDLING</th>
                        <th>
                            <input type="text" class="form-control"
                                id="rab_shipping_display_edit"
                                value="{{ number_format($rab->shipping,3,',','.') }}">
                            <input type="hidden" name="shipping" id="rab_shipping_edit" value="{{ $rab->shipping }}">
                        </th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">GRAND TOTAL</th>
                        <th id="rab_grandTotalDisplay_edit">Rp 0</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="modal fade" id="uraianGalleryModalEdit">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content gambar-modal">

                <div class="modal-header border-0">
                    <div>
                    <h5 class="modal-title fw-semibold" id="modalTitleEdit"></h5>
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
                        id="uraianImageInputEdit">
                    </div>

                    <div id="uraianGalleryEdit" class="gambar-preview">
                    </div>

                </div>

            </div>
        </div>
    </div>
        <input type="hidden" name="subtotal" id="rab_subtotal" value="{{ $rab->subtotal }}">
        <input type="hidden" name="subtotal_after_discount" id="rab_subAfterDiscount" value="{{ $rab->subtotal_after_discount }}">
        <input type="hidden" name="tax_total" id="rab_tax_total" value="{{ $rab->tax_total }}">
        <input type="hidden" name="grand_total" id="rab_grand_total" value="{{ $rab->grand_total }}">

    <h4 class="fw-bold mb-3">Keterangan</h4>

    <textarea name="notes" rows="3" class="form-control"></textarea>
</form>

@push('js')
<script>
    window.currentRabId = "{{ $rab->id ?? '' }}";

    let enterLock = false

    document.addEventListener('keydown', function(e){

        if($(e.target).closest('.select2-container').length){
            return
        }

        if(e.key !== 'Enter') return
        if(enterLock) return

        enterLock = true

        setTimeout(() => {
            enterLock = false
        }, 300)

        const el = e.target

        if(!el || el.disabled) return

        if(el.classList.contains('uraian-input')){

            e.preventDefault()

            if(el.dataset.saving === '1') return

            el.dataset.saving = '1'

            const row = el.closest('.uraian-row')

            if(row){
                saveUraianEdit(row.id)
            }

            setTimeout(() => {
                delete el.dataset.saving
            }, 300)

            return
        }

        if(el.classList.contains('category-input')){

            e.preventDefault()

            const row = el.closest('.category-row')

            if(row){
                saveCategoryEdit(row.id)
            }

            return
        }
    })

    document.addEventListener('blur', function(e){

        const el = e.target

        if(!el.classList.contains('uraian-input')) return

        if(el.dataset.saving === '1') return

        el.dataset.saving = '1'

        const row = el.closest('.uraian-row')

        if(row){
            saveUraianEdit(row.id)
        }

        setTimeout(() => {
            delete el.dataset.saving
        }, 300)

    }, true)

    document.addEventListener('blur', function(e){

        const el = e.target

        if(!el.classList.contains('category-input')) return

        const row = el.closest('.category-row')

        if(row){
            saveCategoryEdit(row.id)
        }

    }, true)

    let isSaving = false
    let autosaveTimer = null
    let isDragging = false
    let currentRabJob = null
    let rabItems = {}
    let currentBasePrice = 0
    let globalProfit = 0
    let globalOverhead = 0
    let categoryIndex = 0
    let uraianIndex = {}
    let uraianGlobalIndex = 0
    let jobIndex = 0
    let draggedGroup = []
    let uraianImages = {}
    let activeUraian = null
    let currentMode = 'edit'
    let sortableInstance = null
    let globalIndex = 0
    let draftLoaded = false
    let isLoadingDraft = false

    function collectCategories(){

        let data = []

        document.querySelectorAll('.category-row').forEach((cat, catIndex) => {

            const catId = cat.id

            let catData = {
                id: catId,
                db_id: cat.dataset.id || null,
                name: cat.dataset.name || '',
                order: catIndex, 
                uraians: []
            }

            document.querySelectorAll(`.uraian-row[data-category="${catId}"]`)
            .forEach((uraian, uraianIndex) => {

                let uraianData = {
                    id: uraian.id,
                    db_id: uraian.dataset.id || null,
                    name: uraian.dataset.name || '',
                    order: uraianIndex,
                    jobs: []
                }

                catData.uraians.push(uraianData)

            })

            data.push(catData)
        })

        return data
    }
    function initRabEdit(){

        $('.select2-row').each(function(){
            if($(this).hasClass("select2-hidden-accessible")){
                $(this).select2('destroy')
            }
        })

        $('.select2-row').select2({
            width: '100%',
            dropdownAutoWidth: true
        })

        recalcAfterDrag()
        updateHargaSemua()
    }

    function parseRupiah(val){
        if(!val) return 0

        val = val.replace(/[^\d.,]/g,'')
        val = val.replace(/\./g,'')
        val = val.replace(',', '.')

        return Number(val) || 0
    }

    function formatRupiah(number){

        number = Number(number) || 0;

        return 'Rp ' + number.toLocaleString('id-ID',{
            maximumFractionDigits:3
        });

    }

    function rupiahInput(el){

        let number = parseRupiah(el.value)

        if(isNaN(number)) number = 0

        el.dataset.value = number

        el.value = number
            ? formatRupiah(number)
            : ''

    }
    function parsePercent(value){

        if(!value) return 0

        return Number(
            value
            .toString()
            .replace(',', '.')
            .replace('%','')
        )

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
    function round(num){
        return Math.round(num)
    }

    function setMode(mode){

        currentMode = mode
        const btnEdit = document.getElementById('btnEditMode')
        const btnDrag = document.getElementById('btnDragMode')

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

            initSortable()
        }
    }
    let reorderTimer = null

    function initSortable(){

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

                clearTimeout(autosaveTimer)
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
    function isDragMode(){
        return currentMode === 'drag'
    }
    
    function loadExistingRab(data){

        uraianImages = {}
        rabItems = {}
        const tbody = document.getElementById('rab_offerItemsBody_edit')
        tbody.innerHTML = ''

        categoryIndex = 0
        jobIndex = 0
        uraianIndex = {}
        uraianGlobalIndex = 0

        globalProfit = parseFloat(data.meta.profit) || 0
        globalOverhead = parseFloat(data.meta.overhead) || 0
        document.getElementById('rab_discount_edit').value =
            data.meta.discount ?? 0;

        document.getElementById('rab_discount_display_edit').value =
            formatRupiah(data.meta.discount ?? 0);

        document.getElementById('rab_shipping_edit').value =
            data.meta.shipping ?? 0;

        document.getElementById('rab_shipping_display_edit').value =
            formatRupiah(data.meta.shipping ?? 0);
        uraianGlobalIndex = Date.now()
        data.categories.forEach(cat => {

            const catId = 'cat_'+categoryIndex
            uraianIndex[catId] = 1

            // CATEGORY
            tbody.insertAdjacentHTML('beforeend',`
            <tr class="table-secondary fw-bold category-row"
                id="${catId}"
                data-id="${cat.id}"
                data-category="${catId}"
                data-name="${cat.name}">

                <td>
                    <span class="drag-handle me-2">
                        <i class="ti ti-grip-vertical"></i>
                    </span>
                    ${numberToLetters(categoryIndex)}
                </td>

                <td colspan="4" class="form-input fw-bold">
                    <span class="category-text"
                        onclick="editCategory('${catId}')">

                        ${cat.name}

                    </span>
                </td>

                <td>
                    <input class="form-control subtotal-category"
                        data-category="${catId}"
                        value="Rp 0"
                        readonly>
                </td>

                <td>
                    <button type="button" class="btn btn-sm btn-secondary"
                        onclick="removeCat('${catId}')">
                        -
                    </button>
                </td>
            </tr>
            `)

            cat.uraians.forEach(uraian => {
                
                const uraianId = 'uraian_' + uraian.id
                const uraianKey = uraianId 
                if(!uraianImages[uraianKey]){
                    uraianImages[uraianKey] = []
                }

                if(Array.isArray(uraian.images)){

                    uraian.images.forEach(img => {
                        uraianImages[uraianKey].push({
                            id: img.id,
                            url: img.image ? img.image.url : null
                        })

                    })
                }

                tbody.insertAdjacentHTML('beforeend',`

                <tr class="uraian-row"
                    id="${uraianId}"
                    data-id="${uraian.id}" 
                    data-category="${catId}"
                    data-name="${uraian.name}">

                    <td class="text-center fw-bold">
                        ${uraianIndex[catId]++}
                    </td>

                    <td colspan="5">

                        <div class="d-flex align-items-center gap-2">

                            <span class="drag-handle">
                                <i class="ti ti-grip-vertical"></i>
                            </span>

                            <span class="uraian-text"
                                onclick="editUraian('${uraianId}')">

                                ${uraian.name}

                            </span>

                            <button type="button" class="btn btn-sm btn-gambar-edit"
                                onclick="openUraianGalleryEdit('${uraianId}','${uraian.name}')">

                                <i class="ti ti-photo"></i>

                            </button>

                        </div>

                    </td>

                    <td>
                        <button type="button" class="btn btn-sm btn-dark"
                            onclick="addJobRowEdit('${uraianId}')">
                            +
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary"
                            onclick="removeUraianEdit('${uraianId}')">
                            -
                        </button>
                    </td>

                </tr>
                `)

                uraian.items.forEach(job => {

                    const jobId = 'job_'+jobIndex++

                    tbody.insertAdjacentHTML('beforeend',`

                    <tr class="job-row"
                        id="${jobId}"

                        data-id="${job.id ?? ''}"

                        data-parent="${uraianId}"
                        data-parent-id="${uraian.id}"

                        data-category="${catId}"
                        data-category-id="${cat.id}">

                        <td></td>

                        <td>

                            <div class="d-flex align-items-center">

                                <span class="drag-ahsp me-2">
                                    <i class="ti ti-grip-vertical"></i>
                                </span>

                                <select class="form-select select2-row job-select"
                                    onchange="loadJobEdit('${jobId}',this.value)">

                                    ${document.getElementById('jobCategorySelectEdit').innerHTML}

                                </select>

                            </div>

                        </td>

                        <td>
                            <span class="sat">${job.satuan}</span>
                        </td>

                        <td>
                            <input type="number"
                                class="form-control vol"
                                step="0.0000000001"
                                data-value="${job.volume}"
                                value="${(job.volume)}"
                                oninput="rabEditCalculate('${jobId}')">
                        </td>

                        <td>
                            <input class="form-control harga"
                                data-value="${job.base_price}"
                                value="${formatRupiah(job.base_price)}"
                                readonly>
                        </td>

                        <td>
                            <input class="form-control total"
                                data-value="${job.total}"
                                value="${formatRupiah(job.total)}"
                                readonly>
                        </td>

                        <td>

                            <button type="button" class="btn btn-sm btn-dark"
                                onclick="addJobRowEdit('${uraianId}')">+</button>

                            <button type="button" class="btn btn-sm btn-secondary"
                                onclick="removeJob('${jobId}')">-</button>

                        </td>

                    </tr>
                    `)

                    rabItems[jobId] = {
                        volume: job.volume,
                        base_price: job.base_price,
                        harga: job.price,
                        total: job.total
                    }

                    setTimeout(() => {

                        const select = $(`#${jobId} .job-select`)

                        select.select2({
                            width: '100%',
                            dropdownAutoWidth: true
                        })

                        select.val(job.job_category_id).trigger('change')

                    }, 100)

                })
            })

            tbody.insertAdjacentHTML('beforeend',`

            <tr class="no-drag" id="addUraianEdit_${catId}">
                <td></td>
                <td colspan="6">

                    <button type="button"
                        class="btn btn-sm btn-link"
                        onclick="addUraianEdit('${catId}')">

                        + Uraian Pekerjaan

                    </button>

                </td>
            </tr>
            `)

            categoryIndex++

        })

        $('.select2-row').select2()

        setTimeout(()=>{
            rabEditCalculateSummary()
        },300)

    }

    function collectUraianImages(){

        let result = {}

        document.querySelectorAll('.uraian-row').forEach(row => {

            const tempKey = row.id
            const dbId = row.dataset.id

            if(!dbId) return

            result[dbId] =
                (uraianImages[tempKey] || [])
                .map(img => img.id)
                .filter(Boolean)
        })

        return result
    }
    function addCategoryEdit(){
        if(isDragMode()) return
        const tbody = document.getElementById('rab_offerItemsBody_edit')

        let letter = numberToLetters(categoryIndex)
        let catId = 'cat_'+categoryIndex

        uraianIndex[catId] = 1

        tbody.insertAdjacentHTML('beforeend',`

        <tr class="table-secondary fw-bold category-row editing" id="${catId}" data-category="${catId}">
            <td>
                <span class="drag-handle me-2" style="cursor:move">
                    <i class="ti ti-grip-vertical"></i>
                </span>
                ${letter}
            </td>

            <td colspan="5">
                <input type="text" class="form-control fw-bold category-input"
                    placeholder="Nama kategori pekerjaan">
            </td>

            <td></td>
        </tr>

        <tr class="no-drag" id="addUraianEdit_${catId}">
            <td></td>
            <td colspan="6">
                <button type="button" class="btn btn-sm btn-link"
                    onclick="addUraianEdit('${catId}')">
                    + Uraian Pekerjaan
                </button>
            </td>
        </tr>
        `)

        categoryIndex++
    }

    function saveCategoryEdit(catId){

        const row = document.getElementById(catId)
        let input = row.querySelector('.category-input')

        row.classList.remove('editing')

        let name

        if(input){
            name = input.value.trim()
        }else{
            // mode edit ulang
            input = row.querySelector('.category-text')
            name = input.innerText.trim()
        }

        if(!name){
            alert('Nama kategori tidak boleh kosong')
            return
        }

        row.dataset.name = name

        // SIMPAN huruf kategori dulu
        const letter = row.cells[0].innerText.trim()

        row.innerHTML = `
            <td>
                <span class="drag-handle me-2" style="cursor:move">
                    <i class="ti ti-grip-vertical"></i>
                </span>
                ${letter}
            </td>


            <td colspan="4" class="fw-bold">

                <span class="category-text"
                    onclick="editCategory('${catId}')">

                    ${name}

                </span>

            </td>

            <td>
                <input type="text"
                    class="form-control subtotal-category"
                    data-category="${catId}"
                    value="Rp 0"
                    readonly>
            </td>

            <td>
                <button type="button" class="btn btn-sm btn-secondary"
                    onclick="removeCat('${catId}')">
                    -
                </button>
            </td>
        `
    }

    function editCategory(catId){
        if(isDragMode()) return
        const row = document.getElementById(catId)
        row.classList.add('editing')

        const name = row.dataset.name || ''
        const letter = row.cells[0].innerText.trim()

        row.innerHTML = `
            <td>
                <span class="drag-handle me-2">
                    <i class="ti ti-grip-vertical"></i>
                </span>
                ${letter}
            </td>

            <td colspan="5">

                <input type="text"
                    class="form-control fw-bold category-input"
                    value="${name}">

            </td>

            <td></td>
        `

        setTimeout(()=>{
            row.querySelector('.category-input').focus()
        },50)

    }

    function addUraianEdit(catId){
        if(isDragMode()) return
        const addRow = document.getElementById('addUraianEdit_'+catId)
        if(!addRow){
            console.error('addRow tidak ditemukan:', 'addUraianEdit_'+catId)
            return
        }
        if(!uraianIndex[catId]) uraianIndex[catId] = 1
        let uraianNo = uraianIndex[catId]++
        let uraianId = 'uraian_'+(uraianGlobalIndex++)

        addRow.insertAdjacentHTML('beforebegin',`

        <tr class="uraian-row" id="${uraianId}" data-category="${catId}">
            <td class="text-center fw-bold">${uraianNo}</td>

            <td colspan="5">
                <div class="d-flex align-items-center gap-2">

                    <span class="drag-handle" style="cursor:move">
                        <i class="ti ti-grip-vertical"></i>
                    </span>

                <input class="form-control uraian-input"
                    placeholder="Uraian pekerjaan">
                </div>
            </td>

            <td>
                <button type="button" class="btn btn-sm btn-dark"
                    onclick="addJobRowEdit('${uraianId}')">
                    +
                </button>
                <button type="button" class="btn btn-sm btn-secondary"
                    onclick="removeUraianEdit('${uraianId}')">
                    -
                </button>
            </td>
        </tr>

        `)
        setTimeout(()=>{
            const input = document.querySelector(`#${uraianId} .uraian-input`)

            if(input){
                input.focus()
                input.select()
                input.dispatchEvent(new Event('input', { bubbles: true }))
            }
        },100)
        renumberUraian(catId)
    }

    function saveUraianEdit(uraianId){

        const row = document.getElementById(uraianId)
        if(!row) return

        const input = row.querySelector('.uraian-input')

        if(!input) return
        
        const name = input.value.trim()

        if(!name){
            alert('Uraian tidak boleh kosong')
            input.focus()
            return
        }

        row.dataset.name = name

        row.cells[1].innerHTML = `
            <div class="d-flex align-items-center gap-2">

                <span class="drag-handle">
                    <i class="ti ti-grip-vertical"></i>
                </span>

                <span class="uraian-text"
                    onclick="editUraian('${uraianId}')">

                    ${name}

                </span>

                <button type="button"
                    class="btn btn-sm btn-gambar-edit"
                    onclick="openUraianGalleryEdit('${uraianId}','${name}')">

                    <i class="ti ti-photo"></i>

                </button>

            </div>
        `
        row.classList.remove('editing')
        const jobs = document.querySelectorAll(`.job-row[data-parent="${uraianId}"]`)

        if(jobs.length === 0){
            addJobRowEdit(uraianId)
        }
        setTimeout(() => {
            delete row.dataset.processing
        }, 300)
    }
    function editUraian(uraianId){

        if(isDragMode()) return

        const row = document.getElementById(uraianId)

        row.classList.add('editing')

        const name = row.dataset.name || ''

        row.cells[1].innerHTML = `
            <div class="d-flex align-items-center gap-2">

                <span class="drag-handle">
                    <i class="ti ti-grip-vertical"></i>
                </span>

                <input
                    class="form-control uraian-input"
                    value="${name}">

            </div>
        `

        setTimeout(()=>{

            const input = row.querySelector('.uraian-input')

            input.focus()
            input.select()

        },50)
    }

    function addJobRowEdit(uraianId){
        if(isDragMode()) return
        const originalSelect = document.getElementById('jobCategorySelectEdit')
        const options = originalSelect.innerHTML

        const idx = jobIndex++
        const jobId = 'job_'+idx

        const uraian = document.getElementById(uraianId);
        const category = document.getElementById(uraian.dataset.category);
        const relatedRows = [
            ...document.querySelectorAll(`.job-row[data-parent="${uraianId}"]`)
        ]

        const lastRow = relatedRows.length
            ? relatedRows[relatedRows.length - 1]
            : uraian

        lastRow.insertAdjacentHTML('afterend', `
       <tr class="job-row"
            id="${jobId}"
            data-id=""
            data-parent="${uraianId}"
            data-parent-id="${uraian.dataset.id || ''}"
            data-category="${uraian.dataset.category}"
            data-category-id="${category.dataset.id || ''}"
            data-index="${idx}">

            <td></td>

            <td>
                <div class="d-flex align-items-center">

                    <span class="drag-ahsp me-2" style="cursor:move">
                        <i class="ti ti-grip-vertical"></i>
                    </span>

                    <div class="flex-grow-1">

                        <select class="form-select select2-row job-select w-100"
                            onchange="loadJobEdit('${jobId}', this.value)">
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
                    step="0.0000000001"
                    class="form-control vol"
                    oninput="rabEditCalculate('${jobId}')">
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
                    onclick="addJobRowEdit('${uraianId}')">
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
        setTimeout(() => {

            const select = $(`#${jobId} .job-select`)

            if(select.length){

                if(select.hasClass('select2-hidden-accessible')){
                    select.select2('destroy')
                }

                select.select2({
                    width: '100%',
                    dropdownAutoWidth: true,
                    dropdownParent: $(`#${jobId}`)
                })

                select.select2('open')
            }

        }, 100)
    }

    function loadJobEdit(rowId, jobId){

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

            rabEditCalculate(rowId)
        })
    }

    function rabEditCalculate(rowId, triggerSave = true){

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

        updateCategorySubtotal(row.dataset.category)
        if(triggerSave){
            rabEditCalculateSummary()
        }
    }
    function updateCategorySubtotal(catId){

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
    function rabEditCalculateSummary(){

        let subtotal = 0

        document.querySelectorAll('.total').forEach(el=>{
            subtotal += Number(el.dataset.value || 0)
        })

        // tampilkan subtotal
        document.getElementById('rab_subtotal').value = subtotal
        document.getElementById('rab_subtotalDisplay_edit').innerText = formatRupiah(subtotal)

        console.log(document.getElementById('rab_discount_edit').value);
        let discount = Number(document.getElementById('rab_discount_edit').value || 0)

        let subAfterDiscount = Math.max(0, subtotal - discount)

        document.getElementById('rab_subAfterDiscount').value = subAfterDiscount
        document.getElementById('rab_subAfterDiscountDisplay_edit').innerText = formatRupiah(subAfterDiscount)

        // tax
        let taxRate = Number(document.getElementById('rab_tax_rate_edit').value || 0)

        let taxTotal = round(subAfterDiscount * taxRate / 100)

        document.getElementById('rab_tax_total').value = taxTotal
        document.getElementById('rab_totalTaxDisplay_edit').innerText = formatRupiah(taxTotal)

        // shipping
        let shipping = Number(document.getElementById('rab_shipping_edit').value)

        // grand total
        let grand = subAfterDiscount + taxTotal + shipping

        const grandEl = document.getElementById('rab_grandTotalDisplay_edit')

        grandEl.dataset.value = grand
        grandEl.innerText = formatRupiah(grand)

        document.getElementById('rab_grand_total').value = grand
        console.log({
            subtotal,
            discount,
            subAfterDiscount,
            shipping
        });
    }
    function removeJob(id){

        const row = document.getElementById(id)

        if(!row) return

        const catId = row.dataset.category || null

        row.remove()

        if(catId){
            updateCategorySubtotal(catId)
        }

        rabEditCalculateSummary()
    }
    function removeUraianEdit(id){
        const row = document.getElementById(id)
        if(!row) return

        const catId = row.dataset.category

        document.querySelectorAll(`[data-parent="${id}"]`).forEach(e=>e.remove())

        row.remove()

        renumberUraian(catId)

        updateCategorySubtotal(catId)

        rabEditCalculateSummary()
    }
    function removeCat(catId){
        const catRow = document.getElementById(catId)

        if(!catRow) return

        document.querySelectorAll(`.uraian-row[data-category="${catId}"]`)
        .forEach(uraian=>{

            const uraianId = uraian.id

            document.querySelectorAll(`[data-parent="${uraianId}"]`)
            .forEach(job=>job.remove())

            uraian.remove()
        })

        const addRow = document.getElementById('addUraianEdit_'+catId)
        if(addRow) addRow.remove()

        catRow.remove()

        renumberCategory()
        rabEditCalculateSummary()
    }
    function renumberCategory(){

        const categories = document.querySelectorAll('.category-row')

        categories.forEach((cat,i)=>{

            const letter = numberToLetters(i)

            cat.querySelector('td').innerHTML = `
                <span class="drag-handle me-2" style="cursor:move">
                    <i class="ti ti-grip-vertical"></i>
                </span>
                ${letter}
            `
        })

        categoryIndex = categories.length
    }
    function renumberUraian(catId){
        let rows = document.querySelectorAll(`.uraian-row[data-category="${catId}"]`)
        rows.forEach((row,i)=>{
            row.querySelector('td').innerText = i+1
        })
        uraianIndex[catId] = rows.length + 1
    }
    function renumberAll(){

        document.querySelectorAll('.category-row').forEach((cat, i)=>{

            const catId = cat.id;

            // 🔥 renumber kategori (A, B, C)
            const letter = numberToLetters(i)

            cat.querySelector('td').innerHTML = `
                <span class="drag-handle me-2" style="cursor:move">
                    <i class="ti ti-grip-vertical"></i>
                </span>
                ${letter}
            `

            // 🔥 renumber uraian
            const uraianRows = document.querySelectorAll(`.uraian-row[data-category="${catId}"]`)

            uraianRows.forEach((row, index)=>{
                row.querySelector('td:first-child').innerText = index + 1
            })

            uraianIndex[catId] = uraianRows.length + 1
        })

        categoryIndex = document.querySelectorAll('.category-row').length
    }
    function recalcAfterDrag(){

        document.querySelectorAll('.job-row').forEach(row=>{
            rabEditCalculate(row.id, false)
        })
        rabEditCalculateSummary()
    }
    function openUraianGalleryEdit(rowId, uraianName){
  
        const row = document.getElementById(rowId)
        activeUraian = row.id

        $("#modalTitleEdit").text(uraianName)

        if(!uraianImages[activeUraian]){
            uraianImages[activeUraian] = []
        }

        console.log('OPEN GALLERY', activeUraian)
        console.log(uraianImages)

        renderGalleryEdit()

        const modal = new bootstrap.Modal(
            document.getElementById('uraianGalleryModalEdit')
        )
        modal.show()
    }

    function renderGalleryEdit(){

        const gallery = document.getElementById('uraianGalleryEdit')

        gallery.innerHTML = ''

        const images = uraianImages[activeUraian] || []

        if(images.length === 0){
            gallery.innerHTML = '<div class="text-muted">Belum ada gambar</div>'
            return
        }

        images.forEach((img,index)=>{
            if(!img.url) return

            gallery.insertAdjacentHTML('beforeend',`

            <div class="preview-item">

                <img src="${img.url}" class="img-thumbnail">

                <button type="button" class="btn btn-sm remove-img"
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

        renderGalleryEdit()

    }
    function updateHargaSemua(){

        const profit = parseFloat(document.getElementById('rab_profit_display_edit').value) || 0
        const overhead = parseFloat(document.getElementById('rab_overhead_display_edit').value) || 0

        document.querySelectorAll('.job-row').forEach(row=>{

            const hargaInput = row.querySelector('.harga')

            const basePrice = parseFloat(hargaInput.dataset.value) || 0

            const newPrice =
                basePrice +
                (basePrice * profit / 100) +
                (basePrice * overhead / 100)

            hargaInput.value = formatRupiah(newPrice)

            rabEditCalculate(row.id, false)

        })

    }
    $(document).on("click",".btn-gambar-edit",function(){

        let uraian = $(this).data("uraian");

        $("#modalTitleEdit").text(uraian);

    });
            document.getElementById('uraianImageInputEdit')
            .addEventListener('change', function(){

                const files = this.files

                const uraianRow = document.getElementById(activeUraian)

                if(!uraianRow){
                    alert('Uraian tidak ditemukan')
                    return
                }

                if(!uraianRow.dataset.id){
                    alert('Tunggu autosave selesai dulu sebelum upload gambar')
                    return
                }

                Array.from(files).forEach(file=>{

                    const formData = new FormData()

                    formData.append('image', file)

                    formData.append(
                        'uraian_id',
                        uraianRow.dataset.id
                    )

                    formData.append(
                        'rab_id',
                        window.currentRabId
                    )

                    fetch('/rab-images/upload',{

                        method:'POST',

                        headers:{
                            'X-CSRF-TOKEN':
                                document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content
                        },

                        body:formData
                    })

                    .then(res=>res.json())

                    .then(img => {

                        if(!img.url){
                            alert('URL gambar kosong')
                            return
                        }

                        if(!uraianImages[activeUraian]){
                            uraianImages[activeUraian] = []
                        }

                        uraianImages[activeUraian].push({
                            id: img.id,
                            url: img.url
                        })

                        renderGalleryEdit()

                    })

                    .catch(err=>{
                        console.error(err)
                        alert('Upload gambar gagal')
                    })

                })

                this.value = ''
            })
            document.getElementById('rab_profit_display_edit').addEventListener('input', function(){
                globalProfit = Number(this.value) || 0
                updateHargaSemua()
            })

            document.getElementById('rab_overhead_display_edit').addEventListener('input', function(){
                globalOverhead = Number(this.value) || 0
                updateHargaSemua()
            })
            const discountEl = document.getElementById('rab_discount_display_edit')

            discountEl.addEventListener('input', function(){

                let raw = parseRupiah(this.value)

                document.getElementById('rab_discount_edit').value = raw

                rabEditCalculateSummary()
            })

            discountEl.addEventListener('blur', function(){
                this.value = formatRupiah(parseRupiah(this.value))
            })

            const shippingEl = document.getElementById('rab_shipping_display_edit')

            shippingEl.addEventListener('input', function(){

                let raw = parseRupiah(this.value)

                document.getElementById('rab_shipping_edit').value = raw

                rabEditCalculateSummary()
            })

            shippingEl.addEventListener('blur', function(){
                this.value = formatRupiah(parseRupiah(this.value))
            })

    document.getElementById('rab_tax_rate_edit').addEventListener('input', function () {
        rabEditCalculateSummary()
    });

    function collectItems(){

        let items = [];

        document.querySelectorAll('.uraian-row').forEach(uraian => {

            const uraianId = uraian.id;

            document.querySelectorAll(`.job-row[data-parent="${uraianId}"]`)
            .forEach((row, itemOrder) => {

                const jobSelect = row.querySelector('.job-select');
                if(!jobSelect || !jobSelect.value) return;

                const volume = Number(
                    row.querySelector('.vol')?.value || 0
                );

                const hargaInput = row.querySelector('.harga');

                const basePrice = Number(hargaInput.dataset.value || 0);

                const price =
                    basePrice +
                    (basePrice * globalProfit / 100) +
                    (basePrice * globalOverhead / 100);

                const total = volume * price;

                const categoryRow = document.getElementById(uraian.dataset.category);

                items.push({

                    id: row.dataset.id || null,

                    job_category_id: jobSelect.value,

                    order: itemOrder,

                    job_name: jobSelect.options[jobSelect.selectedIndex].text,
                    satuan: row.querySelector('.sat').innerText,

                    volume: volume,
                    base_price: basePrice,
                    price: price,
                    total: total,

                    uraian_key: uraian.id,
                    category_key: uraian.dataset.category,

                    uraian_db_id: row.dataset.parentId,
                    category_db_id: row.dataset.categoryId,

                    uraian_name: uraian.dataset.name || '',
                    category_name: categoryRow?.dataset.name || ''

                });

            });

        });

        return items;
    }

    document.getElementById('btnEditMode').addEventListener('click',()=>{
        setMode('edit')
    })

    document.getElementById('btnDragMode').addEventListener('click',()=>{
        setMode('drag')
    })

    const needRefresh = @json($needRefresh)

    const btnSubmit = document.getElementById('btn-save-rab')

    if(btnSubmit){
        btnSubmit.addEventListener('click', function(e){

            e.preventDefault()
            
            const categories = collectCategories();
            const items = collectItems()

            if(items.length === 0){
                Swal.fire({
                    icon:'warning',
                    title:'Belum ada item pekerjaan'
                })
                return
            }
            const formData = new FormData()
            categories.forEach((cat, i) => {

                if(cat.db_id){
                    formData.append(`categories[${i}][id]`, cat.db_id);
                }

                formData.append(`categories[${i}][temp_id]`, cat.id);
                formData.append(`categories[${i}][name]`, cat.name);
                formData.append(`categories[${i}][order]`, cat.order);

                cat.uraians.forEach((u, j) => {

                    if(u.db_id){
                        formData.append(`categories[${i}][uraians][${j}][id]`, u.db_id);
                    }

                    formData.append(`categories[${i}][uraians][${j}][temp_id]`, u.id);
                    formData.append(`categories[${i}][uraians][${j}][name]`, u.name);
                    formData.append(`categories[${i}][uraians][${j}][order]`, u.order);

                });

            });
            formData.append('contact_name', document.querySelector('[name=contact_name]')?.value || '')
            formData.append('job_location', document.querySelector('[name=job_location]')?.value || '')
            formData.append('job_duration', document.querySelector('[name=job_duration]')?.value || 0)
            formData.append('profit', parsePercent(document.getElementById('rab_profit_display_edit').value))
            formData.append('overhead', parsePercent(document.getElementById('rab_overhead_display_edit').value))
            formData.append('discount', document.getElementById('rab_discount_edit').value)
            formData.append('tax_rate', parsePercent(document.getElementById('rab_tax_rate_edit').value))
            formData.append('shipping', document.getElementById('rab_shipping_edit').value)

            items.forEach((item,i)=>{
                if(item.id){
                    formData.append(`items[${i}][id]`, item.id)
                }
                formData.append(`items[${i}][job_category_id]`, item.job_category_id)
                formData.append(`items[${i}][job_name]`, item.job_name)
                formData.append(`items[${i}][order]`, item.order ?? i)
                formData.append(`items[${i}][satuan]`, item.satuan)
                formData.append(`items[${i}][volume]`, item.volume)
                formData.append(`items[${i}][base_price]`, item.base_price)
                formData.append(`items[${i}][price]`, item.price)
                formData.append(`items[${i}][total]`, item.total)
                formData.append(`items[${i}][uraian_key]`, item.uraian_key)
                formData.append(`items[${i}][category_key]`, item.category_key)
                formData.append(`items[${i}][uraian_name]`, item.uraian_name)
                formData.append(`items[${i}][category_name]`, item.category_name)
            })
            Object.keys(uraianImages).forEach(key => {

                uraianImages[key].forEach(img => {

                    formData.append(`uraian_images[${key}][]`, img.id)

                })

            })
            formData.append('_method', 'PUT');
            fetch(`/projects/{{ $project->id }}/rab/{{ $rab->id }}`,{
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,
                    'Accept':'application/json'
                },
                body: formData
            })
            .then(async res => {

                const text = await res.text();

                console.log("STATUS:", res.status);
                console.log("RAW RESPONSE:", text);

                if (!res.ok) {
                    throw new Error(text);
                }

                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error("Bukan JSON:", text);
                    throw e;
                }
            })
            .then(res=>{
                location.reload()
            })
            .catch(err => {
                console.error(err);

                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Error',
                    html: `<pre style="text-align:left">${err.message}</pre>`
                });
            });
        })
    }

    const btnRefresh = document.getElementById('btnRefreshRab')

    if(btnRefresh){
        btnRefresh.addEventListener('click', function(){

            Swal.fire({
                title: 'Refresh harga dari master?',
                text: 'Dengan merefresh ini, harga RAB akan mengikuti harga analisa terbaru.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Refresh',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (!result.isConfirmed) return

                fetch("{{ route('rab.refreshFromMaster', $rab->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        location.reload()
                    }
                })
            })
        })
    }
</script>
@endpush