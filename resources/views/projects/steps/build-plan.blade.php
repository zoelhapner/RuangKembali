@php
    $isReadOnly = !$canEdit;

    $weekCount = count($project->week_labels);
    $colsFixed   = 6;
    $colsPerWeek = 1;
    $colsTotal   = 0;

    $weekCount = count($project->week_labels);

    $totalCols = $colsFixed + ($weekCount * $colsPerWeek);

    $plans = $project->weeklyPlans
        ->keyBy('week_no');
@endphp

    <x-collapse-card title="Tahap Perencanaan Proyek" target="proyek-build-plan-body">
            <div class="card-body">
                <table width="100%" style="margin-bottom:20px; margin-left:20px;">
                    <tr>
                        <td width="20%">PEKERJAAN</td>
                        <td>: {{ $project->project_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>LOKASI</td>
                        <td>: {{ $project->city->name ?? '-' }}</td>
                    </tr>
                </table>

                    <div class="col-md-3 d-flex gap-2">
                        @if(!$isReadOnly)
                            <form action="{{ route('projects.sync-build-plan', $project->id) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Update form kemajuan pekerjaan dengan RAB terbaru?')">

                                @csrf

                                <button type="submit" class="btn btn-secondary">
                                    <i class="ti ti-refresh"></i>
                                    Update Form
                                </button>

                            </form>
                        @endif
                    </div>
                    <div class="table-scroll-top">
                        <div></div>
                    </div>
                    <div class="table-plan">
                        <table id="buildPlanTable" class="table card-table table-vcenter text-nowrap">
                            <colgroup>
                                <col style="width:50px">
                                <col style="width:2600px">
                                <col style="width:80px">
                                <col style="width:80px">
                                <col style="width:140px">
                                <col style="width:80px">

                                @foreach($project->week_labels as $w)
                                    <col style="width:95px">
                                @endforeach
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="align-middle text-center">No</th>
                                    <th rowspan="2" class="align-middle text-center">Uraian Pekerjaan</th>

                                    <th colspan="4" class="align-middle text-center">TERKONTRAK</th>
                                    @foreach($project->week_labels as $w)
                                        <th class="text-center" data-week="{{ $w['week_no'] }}">
                                                <div>M{{ $w['week_no'] }}</div>
                                                {{-- <small class="text-muted d-block text-nowrap">
                                                    {{ $w['start'] }} - {{ $w['end'] }}
                                                </small> --}}
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="align-middle">Sat</th>
                                    <th class="align-middle text-center">Vol</th>
                                    <th class="align-middle">Jumlah<br>Harga</th>
                                    <th class="align-middle text-center">Bobot<br>(%)</th>

                                    @foreach($project->week_labels as $w)
                                        <th class="align-middle text-center">Bobot<br>(%)</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">
                                        Total Pekerjaan (%)
                                    </th>
                                    <th>
                                        {{ $isReadOnly ? '' : 'Rp '.number_format($buildPlans->sum('total'),0,',','.') }}
                                    </th>
                                    <th class="text-center">
                                        {{ number_format($buildPlans->sum('bobot_percent'),2) }}
                                    </th>

                                    @foreach($project->week_labels as $w)
                                        <th class="week-foot text-center fw-bold"
                                            data-week="{{ $w['week_no'] }}"
                                            id="total-plan-{{ $w['week_no'] }}">
                                            0
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end">
                                        Kumulatif (%)
                                    </th>
                                    <th>
                                        {{ $isReadOnly ? '' : 'Rp '.number_format($buildPlans->sum('total'),0,',','.') }}
                                    </th>
                                    <th class="text-center">
                                        {{ number_format($buildPlans->sum('bobot_percent'),2) }}
                                    </th>
                                    @foreach($project->week_labels as $w)
                                        <th class="week-foot text-center fw-bold"
                                            id="kumulatif-plan-{{ $w['week_no'] }}">

                                            0
                                        </th>
                                    @endforeach
                                </tr>
                            
                            </tfoot>
                        </table>
                    </div>
            </div>
    </x-collapse-card>
    <div id="build-process">
        @include('projects.steps.build-process')
    </div>
    {{-- <x-collapse-card
    title="Tahap Pelaksanaan Proyek"
    target="proyek-build-body">

    <div id="build-process-container">
    </div>

    </x-collapse-card> --}}
@push('js')
<script>

    const buildPlanTable = document.querySelector("#buildPlanTable");

    const freezeCounts = 6;
    function applyFreeze() {
        if (!buildPlanTable) return;
            const firstColgroup = buildPlanTable.querySelector('colgroup:first-of-type');
            const allColgroups = buildPlanTable.querySelectorAll('colgroup');
            if (allColgroups.length > 1 && firstColgroup) {
                for (let i = 1; i < allColgroups.length; i++) {
                    const secondCols = allColgroups[i].querySelectorAll('col');
                    const firstCols = firstColgroup.querySelectorAll('col');
                    firstCols.forEach((col, idx) => {
                        if (secondCols[idx]) {
                            secondCols[idx].style.width = col.style.width;
                        }
                    });
                }
            }
            const cols =
                buildPlanTable.querySelectorAll(
                    'colgroup:last-of-type col'
                );
            buildPlanTable.style.width = "";
            buildPlanTable.style.minWidth = "";

            const totalColWidth = Array.from(cols).reduce((sum, col) => {
                return sum + (parseFloat(col.style.width) || 0);
            }, 0);

            buildPlanTable.style.width = "";
            buildPlanTable.style.minWidth = totalColWidth + "px";

            buildPlanTable.querySelectorAll(".sticky-col").forEach(cell => {
                cell.classList.remove("sticky-col");
                cell.style.left = "";
                cell.style.width = "";
            });

            if (window.innerWidth < 576) {
                return;
            }

        const offsets = [];
        let left = 0;
        for (let i = 0; i < freezeCounts; i++) {
            offsets.push(left);
            left += Math.round(parseFloat(getComputedStyle(cols[i]).width));
        }

        const rowspanMap = [];
        buildPlanTable.querySelectorAll("tr").forEach(row => {
            let colIndex = 0;
            Array.from(row.children).forEach(cell => {
                while (rowspanMap[colIndex] && rowspanMap[colIndex] > 0) {
                    rowspanMap[colIndex]--;
                    colIndex++;
                }

                const colspan = parseInt(cell.getAttribute("colspan")) || 1;
                const rowspan = parseInt(cell.getAttribute("rowspan")) || 1;

                if (colIndex < freezeCounts || cell.classList.contains('freeze-col')) {
                    cell.classList.add("sticky-col");
                    cell.style.left = Math.round(offsets[colIndex]) + "px";

                    let width = 0;
                    for (let i = 0; i < colspan && (colIndex + i) < freezeCounts; i++) {
                        width += Math.round(parseFloat(getComputedStyle(cols[colIndex + i]).width));
                    }
                    cell.style.width = width + "px";
                }

                if (rowspan > 1) {
                    for (let i = 0; i < colspan; i++) {
                        rowspanMap[colIndex + i] = rowspan - 1;
                    }
                }
                colIndex += colspan;
            });
        });

        // row-category & row-uraian
        buildPlanTable.querySelectorAll("tr.row-category, tr.row-uraian").forEach(row => {
            const cells = row.querySelectorAll("td");

            cells.forEach(cell => {
                cell.classList.remove("sticky-col");
                cell.style.left = "";
                cell.style.width = "";
                cell.style.zIndex = "";
                cell.style.background = "";
            });

            const firstCell = cells[0];
            if (!firstCell) return;

            const width = Array.from(cols).slice(0, freezeCounts).reduce((sum, c) => {
                return sum + (parseFloat(c.style.width) || 0); // pakai style.width bukan getComputedStyle
            }, 0);

            firstCell.classList.add("sticky-col");
            firstCell.style.left = "0px";
            firstCell.style.width = width + "px";
            firstCell.style.zIndex = "20";
            firstCell.style.background = "#fff";
        });

        const HEADER_ROW_HEIGHT = 45;

        const headerRows = buildPlanTable.querySelectorAll("thead tr");
        const firstRow = headerRows[0];
        const secondRow = headerRows[1];

        Array.from(firstRow.children).forEach(th => {
            th.style.position = "sticky";
            th.style.top = "0px";
            th.style.zIndex = th.classList.contains("sticky-col") ? "155" : "102";
            th.style.background = "#f8f9fa";
        });

        Array.from(secondRow.children).forEach(th => {
            th.style.position = "sticky";
            th.style.top = HEADER_ROW_HEIGHT + "px";
            th.style.zIndex = th.classList.contains("sticky-col") ? "155" : "101";
            th.style.background = "#f8f9fa";
        });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        $(document).on('focus', '.week-plan', function(){
            this.dataset.old = this.value || 0;
        });

        $(document).on('input', '.week-plan', function(){
            const target = this;
            const itemId = target.dataset.item;
            const week = target.dataset.week;
            const bobot = parseFloat(target.dataset.bobot) || 0;
            const persen = parseFloat(target.value) || 0;

            // Tampilkan nilai hasil kalkulasi langsung
            const nilaiHasil = (persen / 100) * bobot;
            const nilaiEl = document.querySelector(`.week-plan-nilai[data-item="${itemId}"][data-week="${week}"]`);
            if(nilaiEl) nilaiEl.innerText = `= ${nilaiHasil.toFixed(3)}`;

            const isValid = validateItemPlan(itemId);
            if(!isValid){
                target.value = target.dataset.old || '';
                return;
            }

            autosavePlan(itemId, week, persen);  // kirim nilai persen ke server
            calculateFooterPlan();
            updateKurvaPlanRealtime();
        });
    });

    function initKurvaChart(){
        const ctx = document.getElementById('kurvaSChart');
        ctx.width = Math.max({{ $weekCount }} * 50, 900);
        if(!ctx || typeof Chart === 'undefined') {

            console.error('Chart.js belum load');
            return;

        }
        const dataAwal = @json($project->getKurvaSData() ?? []);
        const labels = []; for(let i = 1; i <= {{ $weekCount }}; i++){ labels.push('M' + i); }
        const realisasi = []; for(let i = 1; i <= {{ $weekCount }}; i++){ const found = dataAwal.find(d => d.week == i); realisasi.push( found ? found.progress : 0 ); }

        window.kurvaChart = new Chart(ctx, {

            type: 'line',

            data: {

                labels: labels,

                datasets: [

                    {
                        label:'Realisasi (%)',
                        data: realisasi,
                        tension:0.3
                    },

                    {
                        label:'Rencana (%)',
                        data:getPlanKumulatif(),
                        tension:0.3
                    }

                ]

            },

            options:{
                animation:false,
                responsive:true,
                maintainAspectRatio:false,

                scales:{
                    x:{
                        ticks:{
                            autoSkip:true,
                            maxTicksLimit:5
                        }
                    },

                    y:{
                        beginAtZero:true,
                        max:100
                    }
                }
            }

        });
    }
    function getWeeklyPlanTotal(week) {
        let total = 0;
        document.querySelectorAll(`.week-plan[data-week="${week}"]`).forEach(el => {
            const val = el.tagName === 'INPUT' ? el.value : el.innerText;
            const persen = parseFloat(val) || 0;
            const bobot = parseFloat(el.dataset.bobot) || 0;
            total += (persen / 100) * bobot;  // <-- konversi persen ke nilai bobot
        });
        return total;
    }

    function getPlanKumulatif(){

        let weekCount = {{ $weekCount }};
        let jalan = 0;
        let data = [];

        for(let w = 1; w <= weekCount; w++){

            jalan += getWeeklyPlanTotal(w);

            data.push(jalan);

        }

        return data;

    }

    function calculateFooterPlan(){

        let weekCount = {{ $weekCount }};

        let kumulatif = 0;

        for(let w = 1; w <= weekCount; w++){

            let total = getWeeklyPlanTotal(w);

            kumulatif += total;

            // mingguan
            const mingguanEl =
                document.getElementById(`total-plan-${w}`);

            if(mingguanEl){

                mingguanEl.innerText =
                    total.toFixed(3);

            }

            // kumulatif
            const kumulatifEl =
                document.getElementById(`kumulatif-plan-${w}`);

            if(kumulatifEl){

                kumulatifEl.innerText =
                    kumulatif.toFixed(3);

            }

        }

        validatePlanTotal();

    }

    function updateKurvaPlanRealtime(){

        if(!window.kurvaChart) return;

        window.kurvaChart.data.datasets[1].data =
            getPlanKumulatif();

        window.kurvaChart.update();

    }

    function rebuildKurvaFromTable(){

        let weekCount = {{ $weekCount }};
        let kumulatif = [];
        let jalan = 0;

        for(let w = 1; w <= weekCount; w++){

            let sumBobot = 0;

            document.querySelectorAll(
                `.week-bobot[data-week="${w}"]`
            ).forEach(el => {

                sumBobot += parseFloat(el.innerText) || 0;

            });

            jalan += sumBobot;

            kumulatif.push(jalan);

        }

        return kumulatif;

    }

    function updateKurvaChartRealtime(){

        if(!window.kurvaChart) return;

        window.kurvaChart.data.datasets[0].data =
            rebuildKurvaFromTable();

        window.kurvaChart.update();

    }

    function validatePlanTotal(){

        let total = 0;

        let weekCount = {{ $weekCount }};

        for(let w = 1; w <= weekCount; w++){

            total += getWeeklyPlanTotal(w);

        }

        console.log('Total Plan:', total);

        if(Math.abs(total - 100) > 0.01){

            console.warn("Total rencana ≠ 100%");

        }

    }

    function autosavePlan(itemId, week, val){
        console.log({
            item: itemId,
            week: week,
            val: val
        });
        fetch("{{ route('build-week-plan.update') }}", {

            method: "POST",

            headers: {

                "Content-Type":"application/json",

                "X-CSRF-TOKEN":
                    document.querySelector(
                        'meta[name=csrf-token]'
                    ).content

            },

            body: JSON.stringify({

                project_id: "{{ $project->id }}",
                build_plan_id: itemId,
                week_no: week,
                plan_percent: val

            })

        })
        .then(async r => {

            const res = await r.json();

            console.log(res);

            if(!r.ok){

                console.error(res);

            }

        })
        .catch(err => {

            console.error(err);

        });

    }
    function validateItemPlan(itemId)
    {
        let totalPersen = 0;

        document.querySelectorAll(`.week-plan[data-item="${itemId}"]`)
        .forEach(el => {
            totalPersen += parseFloat(el.value) || 0;
        });

        // Total persen semua minggu tidak boleh melebihi 100%
        if(totalPersen > 100.001){
            alert(`Total plan item melebihi 100% (sekarang: ${totalPersen.toFixed(3)}%)`);
            return false;
        }

        return true;
    }
</script>
<script>
    let weeks = @json($project->week_labels);

    let columns = [
        {
            data:'DT_RowIndex',
            name:'DT_RowIndex',
            orderable:false,
            searchable:false,
        },
        {
            data:'item_name',
            name:'item_name',
        },
        {
            data:'satuan',
            name:'satuan',
        },
        {
            data:'volume',
            name:'volume',
        },
        {
            data:'total_format',
            name:'total_format',
        },
        {
            data:'bobot_format',
            name:'bobot_format',
        }
    ];

    weeks.forEach(function(w){
        columns.push({
            data:'week_values.'+w.week_no,
            width:"95px",
            className:"text-center",
            defaultContent:'',
            orderable:false,
            render:function(data,type,row){
                const persen = data ?? 0;
                const nilaiBobot = ((parseFloat(persen) || 0) / 100) * parseFloat(row.bobot_percent || 0);
                return `
                <div class="week-plan-wrapper">
                    <input type="number" step="0.001" class="form-control week-plan"
                        data-item="${row.id}"
                        data-week="${w.week_no}"
                        data-bobot="${row.bobot_percent}"
                        value="${persen}"
                        placeholder="%"
                    >
                    <small class="text-muted week-plan-nilai" data-item="${row.id}" data-week="${w.week_no}">
                        = ${nilaiBobot.toFixed(3)}
                    </small>
                </div>
                `;
            }
        });
    });
    function alphaIndex(n) {
        let result = '';

        while (n >= 0) {
            result = String.fromCharCode((n % 26) + 65) + result;
            n = Math.floor(n / 26) - 1;
        }

        return result;
    }
    document.addEventListener('DOMContentLoaded', function () {
        const topScroll = document.querySelector('.table-scroll-top');
        const topContent = topScroll.querySelector('div');
        const bottomScroll = document.querySelector('.table-plan');

        // Sync scroll
        topScroll.addEventListener('scroll', () => {
            bottomScroll.scrollLeft = topScroll.scrollLeft;
        });
        bottomScroll.addEventListener('scroll', () => {
            topScroll.scrollLeft = bottomScroll.scrollLeft;
        });
    });
    $('#buildPlanTable').DataTable({
        processing:true,
        serverSide:true,
        searching: false,
        lengthChange: false,
        paging: false,
        info: false,
        ordering: false,
        autoWidth: false,
        scrollX: false,
        dom: 't',
        columnDefs: [
            { targets: 0, width: '50px' },
            { targets: 1, width: '260px' },
            { targets: 2, width: '80px' },
            { targets: 3, width: '80px' },
            { targets: 4, width: '140px' },
            { targets: 5, width: '80px' },
        ],
        ajax:{
            url:"{{ route('build-plan.data',$project->id) }}",
            type:"POST",
            headers:{
                'X-CSRF-TOKEN':
                '{{ csrf_token() }}'
            },
            dataSrc:function(json){
                window.weekTotal = json.week_total ?? {};
                window.weekKumulatif = json.week_kumulatif ?? {};
                return json.data;
            }
        },
        columns:columns,
        drawCallback: function () {

            let api = this.api();
            let data = api.rows({ page: 'current' }).data();
            let rows = api.rows({ page: 'current' }).nodes();

            let lastCategory = null;
            let lastUraian = null;

            let categoryIndex = 0;
            let uraianIndex = 0;
            let itemIndex = 0;

            data.each(function (row, i) {

                // CATEGORY ROW (SAFE: ONLY INSERT OUTSIDE TABLE BODY LOGIC)
                if (row.category_name !== lastCategory) {
                    categoryIndex++;
                    uraianIndex = 0;
                    itemIndex = 0;
                    lastUraian = null;

                    $(rows[i]).before(`
                        <tr class="table-secondary fw-bold row-category">
                            <td colspan="${columns.length}">
                                ${alphaIndex(categoryIndex - 1)}. ${row.category_name.toUpperCase()}
                            </td>
                        </tr>
                    `);

                    lastCategory = row.category_name;
                }

                // URAIAN ROW
                if (row.uraian_name !== lastUraian) {
                    uraianIndex++;
                    itemIndex = 0;

                    $(rows[i]).before(`
                        <tr class="table-light row-uraian"> 
                            <td colspan="${columns.length}">
                                ${uraianIndex}. ${row.uraian_name}
                            </td>
                        </tr>
                    `);

                    lastUraian = row.uraian_name;
                }

                itemIndex++;

                $('td:eq(0)', rows[i]).html(
                    uraianIndex + '.' + itemIndex
                );
            });

            Object.entries(window.weekTotal || {}).forEach(([week, total]) => {
                $('#total-plan-' + week).html(Number(total).toFixed(3));
            });

            Object.entries(window.weekKumulatif || {}).forEach(([week, total]) => {
                $('#kumulatif-plan-' + week).html(Number(total).toFixed(3));
            });

            if (!window.chartInitialized) {
                initKurvaChart();
                window.chartInitialized = true;
            }
            setTimeout(() => {
                applyFreeze(); // cukup ini saja, width sudah di-handle di dalam applyFreeze()

                // Sync scrollbar atas tetap di sini
                const cols = document.querySelectorAll('#buildPlanTable colgroup:first-of-type col');
                const totalWidth = Array.from(cols).reduce((sum, col) => {
                    return sum + (parseFloat(col.style.width) || 0);
                }, 0);

                const topContent = document.querySelector('.table-scroll-top div');
                if (topContent) {
                    topContent.style.width = Math.ceil(totalWidth) + 'px';
                }
                const observer = new MutationObserver(() => {
                    const tbl = document.querySelector('#buildPlanTable');
                    if (tbl && Math.round(parseFloat(tbl.style.width)) !== totalWidth) {
                        tbl.style.width = totalWidth + "px";
                        tbl.style.minWidth = totalWidth + "px";
                    }
                });

                observer.observe(buildPlanTable, {
                    attributes: true,
                    attributeFilter: ['style']
                });

                // Stop setelah 2 detik
                setTimeout(() => observer.disconnect(), 2000);
            }, 200);
        }
    });
</script>
@endpush