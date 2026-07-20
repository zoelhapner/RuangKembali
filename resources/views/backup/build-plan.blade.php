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
                            <form action="{{ route('projects.sync-build', $project->id) }}"
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
                
            <div class="table-responsive">
                <table class="table table-bordered build-plan-table">
                    <colgroup>
                        <col style="width:60px">
                        <col style="width:320px">
                        <col style="width:80px">
                        <col style="width:90px">
                        <col style="width:140px">
                        <col style="width:100px">

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
                                <th class="text-center week-head" data-week="{{ $w['week_no'] }}">
                                        <div>M{{ $w['week_no'] }}</div>
                                        {{-- <small class="text-muted d-block text-nowrap">
                                            {{ $w['start'] }} - {{ $w['end'] }}
                                        </small> --}}
                                </th>
                            @endforeach
                        </tr>

                        <tr>
                            <th class="align-middle">Satuan</th>
                            <th class="align-middle text-center">Vol</th>
                            <th class="align-middle">Jumlah<br>Harga</th>
                            <th class="align-middle text-center">Bobot<br>(%)</th>

                            @foreach($project->week_labels as $w)
                                <th class="align-middle text-center">Bobot<br>(%)</th>
                            @endforeach
                        </tr>
                    </thead>
                    @php
                    function alphaIndex($n) {
                        $result = '';
                        while ($n >= 0) {
                            $result = chr(($n % 26) + 65) . $result;
                            $n = intdiv($n, 26) - 1;
                        }
                        return $result;
                    }
                    @endphp
                    <tbody>
                        @foreach($groupedPlans as $categoryData)

                            @php
                                $categoryNo = alphaIndex($loop->index);
                            @endphp
                            <tr class="row-category">
                                <td colspan="6">
                                    {{ $categoryNo }}. {{ strtoupper($categoryData['category_name']) }}
                                </td>

                                <td colspan="{{ $totalCols - 6 }}"></td>
                            </tr>

                            @foreach($categoryData['uraians'] as $uraianData)
                                @php
                                    $uraianNo = $loop->iteration;
                                @endphp
                                <tr class="row-uraian">
                                    <td colspan="6">
                                        {{ $uraianNo }}. {{ ucwords($uraianData['uraian_name']) }}
                                    </td>

                                    <td colspan="{{ $totalCols - 6 }}"></td>
                                </tr>

                                @foreach($uraianData['items'] as $item)
                                            @php
                                                $itemNo = $loop->iteration;
                                            @endphp
                                            <tr
                                                data-item-id="{{ $item->id }}"
                                                data-item-vol="{{ $item->volume }}"
                                                data-item-bobot="{{ $item->bobot_percent }}">                              
                                                <td>
                                                    {{ $uraianNo }}.{{ $itemNo }}
                                                </td>
                                                <td class="uraian-pekerjaan">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            {{ $item->item_name }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $item->satuan }}</td>
                                                <td class="text-center">
                                                    {{ number_format($item->volume,2) }}
                                                </td>

                                                <td class="text-end">
                                                    Rp {{ number_format($item->total,0,',','.') }}
                                                </td>
                                                <td width="120">
                                                    <input type="number"
                                                        step="0.001"
                                                        class="form-control"
                                                        data-id="{{ $item->id }}"
                                                        value="{{ number_format($item->bobot_percent, 3, '.', '') }}"
                                                        readonly>
                                                </td>
                                                    @foreach($project->week_labels as $w)
                                                        @php
                                                            $prog = $item->progress_map[$w['week_no']] ?? null;
                                                        @endphp
                                                        <td class="week-col">
                                                            @if(!$isReadOnly)
                                                            <input type="number"
                                                                step="0.01"
                                                                min="0"
                                                                max="{{ $item->bobot_percent }}"
                                                                class="form-control week-plan"
                                                                data-item="{{ $item->id }}"
                                                                data-week="{{ $w['week_no'] }}"
                                                                value="{{ $prog->plan_percent ?? '' }}">
                                                            @else
                                                            <div class="form-control bg-light">
                                                                {{ $prog->plan_percent ?? '' }}
                                                            </div>
                                                            @endif
                                                        </td>                           
                                                    @endforeach
                                            </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">
                                Total Pekerjaan (%)
                            </th>
                            <th>
                                {{ $isReadOnly ? '' : 'Rp '.number_format($buildPlans->sum('total'),0,',','.') }}
                            </th>
                            <th class="text-center">
                                100.0
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
                                100.0
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
    <div id="build-plan">
        @include('projects.steps.build-process')
    </div>
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const table = document.querySelector('.build-plan-table');

    if (!table) return;

    const freezeCount = 6;

    function applyFreeze() {

        // reset
        table.querySelectorAll('.sticky-col,.sticky-last')
            .forEach(el => {

                el.classList.remove(
                    'sticky-col',
                    'sticky-last'
                );

                el.style.left = '';
                el.style.width = '';
            });

        const cols =
            table.querySelectorAll('colgroup col');

        // hitung posisi kiri tiap kolom fixed
        const offsets = [];
        let left = 0;

        for(let i = 0; i < freezeCount; i++){

            offsets.push(left);

            left += parseFloat(
                getComputedStyle(cols[i]).width
            );
        }

        const headerRow1 =
            table.querySelector('thead tr:first-child');

        if(headerRow1){

            const ths =
                headerRow1.children;

            // No
            ths[0].classList.add('sticky-col');
            ths[0].style.left = offsets[0] + 'px';

            // Uraian
            ths[1].classList.add('sticky-col');
            ths[1].style.left = offsets[1] + 'px';

            // TERKONTRAK
            ths[2].classList.add(
                'sticky-col',
                'sticky-last'
            );

            ths[2].style.left =
                offsets[2] + 'px';

            let kontrakWidth = 0;

            for(let i = 2; i < 6; i++){

                kontrakWidth += parseFloat(
                    getComputedStyle(cols[i]).width
                );
            }

            ths[2].style.width =
                kontrakWidth + 'px';
        }

        const headerRow2 =
            table.querySelector('thead tr:last-child');

        if(headerRow2){

            Array.from(headerRow2.children)
                .slice(0,4)
                .forEach((th,index)=>{

                    th.classList.add(
                        'sticky-col'
                    );

                    th.style.left =
                        offsets[index + 2] + 'px';

                    if(index === 3){

                        th.classList.add(
                            'sticky-last'
                        );
                    }
                });
        }

        table.querySelectorAll(
            'tbody tr:not(.row-category):not(.row-uraian)'
        ).forEach(row => {

            const cells = row.children;

            for(let i = 0; i < freezeCount; i++){

                const cell = cells[i];

                if(!cell) continue;

                cell.classList.add(
                    'sticky-col'
                );

                cell.style.left =
                    offsets[i] + 'px';

                if(i === freezeCount - 1){

                    cell.classList.add(
                        'sticky-last'
                    );
                }
            }
        });

        table.querySelectorAll('.row-category,.row-uraian').forEach(row => {

            const td =
                row.querySelector('td');

            if(!td) return;

            let width = 0;

            for(let i = 0; i < freezeCount; i++){

                width += parseFloat(
                    getComputedStyle(cols[i]).width
                );
            }

            td.classList.add(
                'sticky-col',
                'sticky-last'
            );

            td.style.left = '0px';
            td.style.width = width + 'px';
            td.style.zIndex = '40';
        });

        table.querySelectorAll(
            'thead .sticky-col'
        ).forEach(th => {

            th.style.zIndex = '100';
        });
    }

    applyFreeze();

    window.addEventListener(
        'resize',
        applyFreeze
    );
});
</script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            document.querySelectorAll('.week-plan').forEach(el => {

                // simpan nilai lama
                el.dataset.old = el.value || 0;

                el.addEventListener('focus', function () {

                    this.dataset.old = this.value || 0;

                });

                el.addEventListener('input', e => {

                    const target = e.target;

                    const itemId = target.dataset.item;

                    const isValid = validateItemPlan(itemId);

                    if(!isValid){

                        // rollback ke nilai sebelumnya
                        target.value = target.dataset.old || '';

                        return;
                    }

                    autosavePlan(
                        itemId,
                        target.dataset.week,
                        parseFloat(target.value) || 0
                    );

                    calculateFooterPlan();
                    updateKurvaPlanRealtime();

                });

            });

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

            calculateFooterPlan();
            updateKurvaPlanRealtime();

        });

        function getWeeklyPlanTotal(week){

            let total = 0;

            document.querySelectorAll(
                `.week-plan[data-week="${week}"]`
            ).forEach(el => {

                total += parseFloat(el.value) || 0;

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
            let total = 0;

            document.querySelectorAll(
                `.week-plan[data-item="${itemId}"]`
            ).forEach(el => {

                total += parseFloat(el.value) || 0;

            });

            const row = document.querySelector(
                `tr[data-item-id="${itemId}"]`
            );

            const bobot =
                parseFloat(row.dataset.itemBobot) || 0;

            console.log({
                itemId,
                total,
                bobot
            });

            // toleransi decimal
            if(total > (bobot + 0.001)){

                alert(
                    `Total plan item melebihi bobot ${bobot}%`
                );

                return false;
            }

            return true;
        }
    </script>
@endpush