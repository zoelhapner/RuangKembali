@php
    $isReadOnly = !$canEdit;

    $weekCount = count($project->week_labels);
    $colsFixed   = 6;
    $colsNormal  = 3;
    $colsJustek  = 3;
    $colsPerWeek = $colsNormal + $colsJustek;
    $colsTotal   = 4;

    $weekCount = count($project->week_labels);

    $totalCols = $colsFixed + ($weekCount * $colsPerWeek) + $colsTotal;

    $plans = $project->weeklyPlans
        ->keyBy('week_no');
@endphp

        <x-collapse-card title="Tahap Pelaksanaan Proyek" target="proyek-build-body">
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

                <div class="row mb-3 ps-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label">Filter Minggu</label>

                        <select id="filter-week" class="form-select select2">
                            <option value="">-- Semua --</option>

                            @foreach($project->week_labels as $w)
                                <option value="{{ $w['week_no'] }}">
                                    M{{ $w['week_no'] }} ({{ $w['label'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Filter Tanggal</label>

                        <input type="date"
                            id="filter-date"
                            class="form-control">
                    </div>

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
                        <button type="button" id="btn-export-pdf" class="btn btn-dark"> 
                            <i class="ti ti-file-export"></i> Ekspor PDF 
                        </button>

                        <form id="exportPdfForm" method="POST" action="{{ route('projects.export-pdf', $project->id) }}" target="_blank">
                            @csrf
                            <input type="hidden" name="week" id="pdf_week">
                            <input type="hidden" name="date" id="pdf_date">
                            <input type="hidden" name="chart_image" id="chart_image">
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered progress-table">
                            <colgroup>
                                <col style="width:50px;">   
                                <col style="width:260px;">  
                                <col style="width:80px;">   
                                <col style="width:80px;">   
                                <col style="width:140px;">  
                                <col style="width:80px;">  
                                @foreach($project->week_labels as $w)
                                    <col class="week-col" data-week="{{ $w['week_no'] }}" style="width:130px;">
                                    <col class="week-col" data-week="{{ $w['week_no'] }}" style="width:110px;">
                                    <col class="week-col" data-week="{{ $w['week_no'] }}" style="width:110px;">
                                    <col class="week-col just-col" data-week="{{ $w['week_no'] }}" style="width:110px;">
                                    <col class="week-col just-col" data-week="{{ $w['week_no'] }}" style="width:110px;">
                                    <col class="week-col just-col" data-week="{{ $w['week_no'] }}" style="width:120px;">
                                @endforeach
                                <col style="width:140px;">
                                <col style="width:140px;">
                                <col style="width:140px;">
                                <col style="width:140px;">
                            </colgroup>
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="align-middle text-center">No</th>
                                <th rowspan="2" class="align-middle text-center">Uraian Pekerjaan</th>

                                <th colspan="4" class="align-middle text-center">TERKONTRAK</th>

                                @foreach($project->week_labels as $w)
                                    <th colspan="3" class="text-center week-head" data-week="{{ $w['week_no'] }}">
                                            <div>M{{ $w['week_no'] }}</div>
                                            <small class="text-muted">
                                                {{ $w['start'] }} - {{ $w['end'] }}
                                            </small>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-dark ms-1 btn-just-toggle"
                                            data-week="{{ $w['week_no'] }}">
                                            +
                                        </button>
                                    </th>

                                    <th colspan="3"
                                        class="text-center bg-warning-subtle just-head"
                                        data-week="{{ $w['week_no'] }}">
                                        <div>Justek Volume M{{ $w['week_no'] }}</div>
                                        <small class="text-muted">
                                                {{ $w['start'] }} - {{ $w['end'] }}
                                        </small>
                                    </th>
                                @endforeach

                                <th colspan="4" class="align-middle text-center">Perubahan Volume</th>
                            </tr>

                            <tr>
                                <th class="align-middle">Satuan</th>
                                <th class="align-middle text-center">Vol</th>
                                <th class="align-middle">Jumlah<br>Harga</th>
                                <th class="align-middle text-center">Bobot<br>(%)</th>

                                @foreach($project->week_labels as $w)
                                    <th class="align-middle text-center">Vol</th>
                                    <th class="align-middle text-center">Progres<br>(%)</th>
                                    <th class="align-middle text-center">Bobot<br>(%)</th>

                                    <th class="just-col" data-week="{{ $w['week_no'] }}">Kurang</th>
                                    <th class="just-col" data-week="{{ $w['week_no'] }}">Tambah</th>
                                    <th class="just-col" data-week="{{ $w['week_no'] }}">Pek.Baru</th>
                                @endforeach
                                <th class="text-center">Total<br>Justek</th>
                                <th class="text-center">Total<br>Vol</th>
                                <th class="text-center">Harga<br>Satuan</th>
                                <th class="text-center">Harga<br>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedItems as $categoryData)

                                @php
                                    $categoryNo = alphaIndex($loop->index);
                                @endphp
                                <tr class="row-category"> 
                                    <td>
                                        {{ $categoryNo }}
                                    </td> 
                                    <td colspan="5">
                                        {{ strtoupper($categoryData['category_name']) }}
                                    </td> 
                                    <td colspan="{{ $totalCols - 5 }}"></td> 
                                </tr>

                                @foreach($categoryData['uraians'] as $uraianData)
                                    @php
                                        $uraianNo = $loop->iteration;
                                    @endphp
                                    <tr class="row-uraian">
                                        <td>
                                            {{ $uraianNo }}.
                                        </td>
                                        <td colspan="5">
                                            {{ ucwords($uraianData['uraian_name']) }}
                                        </td> 
                                        <td colspan="{{ $totalCols - 5 }}"></td> 
                                    </tr>

                                    @foreach($uraianData['items'] as $item)
                                                @php
                                                    $itemNo = $loop->iteration;
                            
                                                    $volKontrak = $item->volume;

                                                    $volTerpakai = $item->weeklyProgresses->sum('volume');

                                                    $isFull = $volTerpakai >= $volKontrak;
                                                @endphp
                                                <tr
                                                    data-item-id="{{ $item->id }}"
                                                    data-item-vol="{{ $item->volume }}"
                                                    data-item-bobot="{{ $item->bobot_percent }}"
                                                    data-full="{{ $isFull ? 1 : 0 }}">                              
                                                    <td>
                                                        {{ $uraianNo }}.{{ $itemNo }}
                                                    </td>
                                                    <td class="uraian-pekerjaan">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                {{ $item->uraian }}
                                                            </div>

                                                            <button type="button"
                                                                    class="btn btn-sm btn-light btn-add-tambah"
                                                                    data-item="{{ $item->id }}">
                                                                +
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td>{{ $item->satuan }}</td>
                                                    <td>{{ $item->volume }}</td>
                                                    <td class="harga-kontrak"
                                                        data-price="{{ $item->price }}">
                                                        Rp {{ number_format($item->price,0,',','.') }}
                                                    </td>
                                                    <td width="120">
                                                        <input type="number"
                                                            step="0.001"
                                                            class="form-control bobot-input"
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
                                                                    class="form-control week-vol"
                                                                    data-item="{{ $item->id }}"
                                                                    data-week="{{ $w['week_no'] }}"
                                                                    data-last="{{ $prog->volume ?? 0 }}"
                                                                    value="{{ $prog->volume ?? '' }}"
                                                                    {{ $isFull ? 'disabled' : '' }}>
                                                                @else
                                                                <div class="form-control bg-light">
                                                                    {{ $prog->volume ?? '' }}
                                                                </div>
                                                                @endif
                                                            </td>
                                                            <td class="week-progress week-col"
                                                                data-week="{{ $w['week_no'] }}"
                                                                id="prog-{{ $item->id }}-{{ $w['week_no'] }}">
                                                            </td>
                                                            <td class="week-bobot week-col"
                                                                data-week="{{ $w['week_no'] }}"
                                                                id="bobot-{{ $item->id }}-{{ $w['week_no'] }}">
                                                            </td>
                                                            <td class="just-col" data-week="{{ $w['week_no'] }}">
                                                                <input class="form-control just-kurang"
                                                                        
                                                                        data-item="{{ $item->id }}"
                                                                        data-week="{{ $w['week_no'] }}"
                                                                        value="{{ $prog->just_kurang ?? 0 }}">
                                                            </td>
                                                            <td class="just-col" data-week="{{ $w['week_no'] }}">
                                                                <input class="form-control just-tambah"
                                                                        
                                                                        data-item="{{ $item->id }}"
                                                                        data-week="{{ $w['week_no'] }}" value="{{ $prog->just_tambah ?? 0 }}"></td>
                                                            <td class="just-col" data-week="{{ $w['week_no'] }}">
                                                                <input class="form-control just-baru"
                                                                    data-item="{{ $item->id }}"
                                                                    data-week="{{ $w['week_no'] }}"
                                                                    value="{{ $prog->just_baru ?? 0 }}"
                                                                    {{ $item->tambahan->count() ? '' : 'readonly' }}>
                                                            </td>
                                                        @endforeach
                                                        <td class="total-justek"
                                                            data-item="{{ $item->id }}">
                                                            0
                                                        </td>
                                                        <td class="total-pelaksanaan"
                                                            data-item="{{ $item->id }}"
                                                            data-vol-kontrak="{{ $item->volume }}">
                                                            {{ number_format($item->volume, 3) }}
                                                        </td>
                                                        <td class="harga-kontrak" data-price="{{ $item->price }}">Rp {{ number_format($item->price,0,',','.') }}</td>
                                                        <td class="nilai-pelaksanaan">0</td>
                                                </tr>
                                                @foreach($item->tambahan as $sub)

                                                    @php
                                                        $progressMap = $sub->progress_map ?? [];
                                                    @endphp

                                                    <tr class="table-warning row-tambahan-item"
                                                        data-parent="{{ $item->id }}"
                                                        data-item-id="{{ $sub->id }}"
                                                        data-item-vol="{{ $sub->volume }}"
                                                        data-item-bobot="{{ $sub->bobot_percent ?? 0 }}">

                                                        <td></td>

                                                        <td>
                                                            ↳ {{ $sub->uraian }}
                                                            <span class="badge bg-warning text-dark">
                                                                Tambahan
                                                            </span>
                                                        </td>

                                                        <td>{{ $sub->satuan }}</td>

                                                        <td>{{ $sub->volume }}</td>

                                                        <td class="harga-kontrak"
                                                            data-price="{{ $sub->price }}">

                                                            Rp {{ number_format($sub->price,0,',','.') }}
                                                        </td>

                                                        <td></td>

                                                        @foreach($project->week_labels as $w)

                                                            @php
                                                                $prog =
                                                                    $progressMap[$w['week_no']] ?? null;
                                                            @endphp

                                                            <td class="week-col">

                                                                <input type="number"
                                                                    step="0.01"
                                                                    class="form-control week-vol"
                                                                    data-item="{{ $sub->id }}"
                                                                    data-week="{{ $w['week_no'] }}"
                                                                    value="{{ $prog->volume ?? '' }}">
                                                            </td>

                                                            <td class="week-progress"
                                                                data-week="{{ $w['week_no'] }}"
                                                                id="prog-{{ $sub->id }}-{{ $w['week_no'] }}">
                                                            </td>

                                                            <td class="week-bobot"
                                                                data-week="{{ $w['week_no'] }}"
                                                                id="bobot-{{ $sub->id }}-{{ $w['week_no'] }}">
                                                            </td>

                                                            <td class="just-col"
                                                                data-week="{{ $w['week_no'] }}">

                                                                <input class="form-control just-kurang"
                                                                    data-item="{{ $sub->id }}"
                                                                    data-week="{{ $w['week_no'] }}"
                                                                    value="{{ $prog->just_kurang ?? 0 }}">
                                                            </td>

                                                            <td class="just-col"
                                                                data-week="{{ $w['week_no'] }}">

                                                                <input class="form-control just-tambah"
                                                                    data-item="{{ $sub->id }}"
                                                                    data-week="{{ $w['week_no'] }}"
                                                                    value="{{ $prog->just_tambah ?? 0 }}">
                                                            </td>

                                                            <td class="just-col"
                                                                data-week="{{ $w['week_no'] }}">

                                                                <input class="form-control just-baru"
                                                                    data-item="{{ $sub->id }}"
                                                                    data-week="{{ $w['week_no'] }}"
                                                                    value="{{ $prog->just_baru ?? 0 }}">
                                                            </td>

                                                        @endforeach

                                                        <td class="total-justek"
                                                            data-item="{{ $sub->id }}">
                                                            0
                                                        </td>

                                                        <td class="total-pelaksanaan"
                                                            data-item="{{ $sub->id }}"
                                                            data-vol-kontrak="{{ $sub->volume }}">
                                                            {{ number_format($sub->volume,3) }}
                                                        </td>

                                                        <td class="harga-kontrak"
                                                            data-price="{{ $sub->price }}">

                                                            Rp {{ number_format($sub->price,0,',','.') }}
                                                        </td>

                                                        <td class="nilai-pelaksanaan">
                                                            0
                                                        </td>

                                                    </tr>

                                                @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">
                                    Realisasi kumulatif kemajuan Pekerjaan
                                </th>
                                <th>
                                    {{ $isReadOnly ? '' : 'Rp '.number_format($buildItems->sum('total'),0,',','.') }}
                                </th>
                                <th class="totalBobotKontrak">0</th>

                                @foreach($project->week_labels as $w)
                                    <th class="week-foot" data-week="{{ $w['week_no'] }}"></th>
                                    <th class="week-foot" data-week="{{ $w['week_no'] }}"></th>
                                    <th class="week-foot" data-week="{{ $w['week_no'] }}" id="sum-bobot-{{ $w['week_no'] }}">0</th>
                                    <th class="week-foot" data-week="{{ $w['week_no'] }}"></th>
                                    <th class="week-foot" data-week="{{ $w['week_no'] }}"></th>
                                    <th class="week-foot" data-week="{{ $w['week_no'] }}"></th>
                                @endforeach
                                <th></th>
                                <th id="grand-total-pelaksanaan">0</th>
                                <th></th>
                                <th id="grand-total-pelaksanaan-nilai">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </x-collapse-card>
        <x-collapse-card title="Kurva S Progress Proyek" target="kurva-body">
            <div class="chart-wrapper">
                <canvas id="kurvaSChart"></canvas>
            </div>
        </x-collapse-card>
    <div id="invoice-panel">
        @include('projects.partials.invoice_panel')
    </div>
    <div id="daily-reports">
        @include('projects.partials.daily_reports')
    </div>
    <div id="details-daily-reports">
        @include('projects.details.daily_reports')
    </div>
    <div class="offcanvas offcanvas-end"
        tabindex="-1"
        id="pdfSettingCanvas">

        <div class="offcanvas-header">

            <h5 class="offcanvas-title">
                ⚙ Pengaturan Kurva PDF
            </h5>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="offcanvas">
            </button>

        </div>

        <div class="offcanvas-body">

            <div class="mb-3">
                <label class="form-label">
                    Posisi Kiri (mm)
                </label>

                <input type="number"
                    class="form-control"
                    id="curve_left_mm"
                    value="{{ $project->curve_left_mm ?? 110 }}">
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Posisi Atas (mm)
                </label>

                <input type="number"
                    class="form-control"
                    id="curve_top_mm"
                    value="{{ $project->curve_top_mm ?? 80 }}">
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Lebar Kurva (mm)
                </label>

                <input type="number"
                    class="form-control"
                    id="curve_width_mm"
                    value="{{ $project->curve_width_mm ?? 220 }}">
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Tinggi Kurva (mm)
                </label>

                <input type="number"
                    class="form-control"
                    id="curve_height_mm"
                    value="{{ $project->curve_height_mm ?? 50 }}">
            </div>

            <hr>

            <label class="form-label">
                Geser Cepat
            </label>

            <div class="d-flex flex-wrap gap-2">

                <button type="button"
                        class="btn btn-outline-secondary move-curve"
                        data-left="-5"
                        data-top="0">
                    ← Kiri
                </button>

                <button type="button"
                        class="btn btn-outline-secondary move-curve"
                        data-left="5"
                        data-top="0">
                    Kanan →
                </button>

                <button type="button"
                        class="btn btn-outline-secondary move-curve"
                        data-left="0"
                        data-top="-5">
                    ↑ Atas
                </button>

                <button type="button"
                        class="btn btn-outline-secondary move-curve"
                        data-left="0"
                        data-top="5">
                    ↓ Bawah
                </button>

            </div>
        </div>
    </div>
@push('js')
    <script>
            const table = document.querySelector(".progress-table");
            const colgroup = table?.querySelectorAll("colgroup col") || [];
            const freezeCount = 6;
            function applyAutoFreeze() {
                if (!table) return;

                table.querySelectorAll(".sticky-col, .sticky-last").forEach(cell => {
                    cell.classList.remove("sticky-col", "sticky-last");
                    cell.style.left = "";
                    cell.style.width = "";
                });

                if (window.innerWidth < 576) {
                    return;
                }
                const offsets = [];
                let left = 0;
                for (let i = 0; i < freezeCount; i++) {
                    offsets.push(left);
                    left += Math.round(
                        parseFloat(getComputedStyle(colgroup[i]).width)
                    );
                }

                const rowspanMap = [];
                table.querySelectorAll("tr").forEach(row => {
                    let colIndex = 0;
                    Array.from(row.children).forEach(cell => {
                        while (rowspanMap[colIndex] && rowspanMap[colIndex] > 0) {
                            rowspanMap[colIndex]--;
                            colIndex++;
                        }

                        const colspan = parseInt(cell.getAttribute("colspan")) || 1;
                        const rowspan = parseInt(cell.getAttribute("rowspan")) || 1;

                        if (
                                colIndex < freezeCount ||
                                cell.classList.contains('freeze-col')
                            ) {
                            cell.classList.add("sticky-col");
                            if (colIndex === freezeCount - 1) {
                                cell.classList.add("sticky-last");
                            }
                            cell.style.left = Math.round(offsets[colIndex]) + "px";
                            // batasi width jika colspan > 1
                            let width = 0;
                            for (let i = 0; i < colspan && (colIndex + i) < freezeCount; i++) {
                                width += Math.round(parseFloat(
                                    getComputedStyle(colgroup[colIndex + i]).width)
                                );
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

                // row-category: freeze td pertama
                table.querySelectorAll("tr.row-category, tr.row-uraian").forEach(row => {
                    const cell = row.querySelector("td");
                    if (!cell) return;
                    const width = Array.from(colgroup).slice(0, freezeCount).reduce((sum, c) => {
                        return sum + (parseFloat(getComputedStyle(c).width) || 0);
                    }, 0);
                    cell.classList.add("sticky-col");
                    cell.classList.add("sticky-last");

                    cell.style.left = "0px";
                    cell.style.width = Math.round(width) + "px";
                });
                // row tambahan (kuning)
                table.querySelectorAll("tr.row-tambahan-item").forEach(row => {

                    let left = 0;

                    Array.from(row.children).forEach((cell, index) => {

                        if (index < freezeCount) {

                            cell.classList.add("sticky-col");

                            if (index === freezeCount - 1) {
                                cell.classList.add("sticky-last");
                            }

                            cell.style.left = Math.round(left) + "px";

                            cell.style.zIndex = 55;

                            cell.style.background = "#fff3cd";

                            left += Math.round(parseFloat(
                                getComputedStyle(colgroup[index]).width)
                            );
                        }
                    });
                });
            }
        document.addEventListener('DOMContentLoaded', function() {

            const weeks = @json($project->week_labels);
            let activeWeek = null;

            const colsPerWeek = 6; 
            const colsPerubahan = 4; 
            
            function formatRupiah(angka) {
                angka = Number(angka || 0);
                return new Intl.NumberFormat('id-ID').format(angka);
            }
            // UTILITY: recalc total volume & nilai
            function recalcAll() {
                table.querySelectorAll('tr[data-item-id]').forEach(row => {
                    const itemId = row.dataset.itemId;
                    let totalVolume = 0;

                    if (activeWeek) {
                        const input = row.querySelector(`.week-vol[data-week="${activeWeek}"]`);
                        totalVolume = parseFloat(input?.value || 0);
                    } else {
                        row.querySelectorAll('.week-vol').forEach(inp => {
                            totalVolume += parseFloat(inp.value || 0);
                        });
                    }

                    const priceCell = row.querySelector('.harga-kontrak');
                    const price = parseFloat(priceCell?.dataset.price || 0);

                    const totalCell = row.querySelector('.nilai-pelaksanaan');
                    if (totalCell) {
                        totalCell.textContent = formatRupiah(totalVolume * price);
                    }
                });
            }

            // FILTER WEEK
                function filterWeek(weekNo) {
                    activeWeek = weekNo || null;

                    // toggle COLGROUP (ini kunci utama)
                    colgroup.forEach(col => {
                        const w = col.dataset.week;
                        if (!w) return;

                        if (!weekNo || w == weekNo) {
                            col.classList.remove("col-hidden");
                        } else {
                            col.classList.add("col-hidden");
                        }
                    });

                    // toggle header + body cell
                    table.querySelectorAll('[data-week]').forEach(cell => {
                        if (!weekNo || cell.dataset.week == weekNo) {
                            cell.classList.remove("col-hidden");
                        } else {
                            cell.classList.add("col-hidden");
                        }
                    });
                        table.style.display = "none";
                        table.offsetHeight; 
                        table.style.display = "";
                    requestAnimationFrame(() => {
                        // applyAutoFreeze();
                        recalcAll();
                    });
                }

            // FILTER: select week
            const weekSelect = document.getElementById('filter-week');
            if (weekSelect) {
                $('#filter-week').on('change', function () {
                    filterWeek(this.value);
                });
            }
            // helper parse tanggal lokal (ANTI timezone shift)
            function parseLocalDate(dateStr) {

                if (!dateStr) return null;

                // format input date native: yyyy-mm-dd
                if (dateStr.includes("-")) {

                    const [y, m, d] = dateStr.split("-").map(Number);

                    return new Date(y, m - 1, d);
                }

                // format backend: dd/mm/yyyy
                if (dateStr.includes("/")) {

                    const [d, m, y] = dateStr.split("/").map(Number);

                    return new Date(y, m - 1, d);
                }

                return null;
            }

            const dateInput = document.getElementById('filter-date');

            if (dateInput) {

                dateInput.addEventListener('change', function() {

                    const selectedDate = this.value;

                    // reset kalau kosong
                    if (!selectedDate) {

                        $('#filter-week').val('').trigger('change');
                        filterWeek(null);

                        return;
                    }

                    const selected = parseLocalDate(selectedDate);

                    let foundWeek = null;

                    weeks.forEach(w => {

                        const start = parseLocalDate(w.start_date ?? w.start);
                        const end   = parseLocalDate(w.end_date ?? w.end);

                        if (!start || !end) return;

                        if (selected >= start && selected <= end) {
                            foundWeek = w.week_no;
                        }

                    });
                    console.log(weeks);

                    if (foundWeek) {

                        $('#filter-week')
                            .val(foundWeek)
                            .trigger('change');

                        filterWeek(foundWeek);

                    } else {

                        $('#filter-week')
                            .val('')
                            .trigger('change');

                        filterWeek(null);
                    }
                });
            }

            // inisialisasi
            applyAutoFreeze();
        });
    </script>
    <script>
        function updateTotalDisplay(total) {

            document.querySelectorAll('.totalBobotKontrak')
                .forEach(el => {
                    el.innerText = total.toFixed(1);
                });
        }

        function calcTotal() {

            let total = 0;

            document.querySelectorAll('.bobot-input')
                .forEach(el => {

                    const val = parseFloat(el.value) || 0;

                    total += val;
                });

            total = Number(total.toFixed(3));

            updateTotalDisplay(total);

            return total;
        }

        document.addEventListener('DOMContentLoaded', function () {

            calcTotal();
        });
    </script>

    <script>
        document.addEventListener('click', function(e) {

            const btn = e.target.closest('.btn-just-toggle');
            if (!btn) return;

            const week = btn.dataset.week;

            const targets = document.querySelectorAll(
                `.just-col[data-week="${week}"],
                .just-head[data-week="${week}"]`
            );

            const cols = document.querySelectorAll(
                `col.just-col[data-week="${week}"]`
            );

            const isHidden = targets[0].classList.contains('just-hidden');

            targets.forEach(el => el.classList.toggle('just-hidden', !isHidden));
            cols.forEach(el => el.classList.toggle('just-hidden', !isHidden));

            btn.textContent = isHidden ? '−' : '+';
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.just-col, .just-head, col.just-col')
                .forEach(el => el.classList.add('just-hidden'));
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function recalcWeek(itemId, weekNo, itemVol, itemBobot)
            {
                itemVol = parseFloat(itemVol || 0);
                itemBobot = parseFloat(itemBobot || 0);

                const el = document.querySelector(
                    `.week-vol[data-item="${itemId}"][data-week="${weekNo}"]`
                );

                if (!el) return;
                const row = el.closest('tr');
                const vol = parseFloat(el.value || 0);

                const progress = itemVol > 0
                    ? (vol / itemVol) * 100
                    : 0;

                const bobot = progress * itemBobot / 100;
                row.querySelector(`.week-progress[data-week="${weekNo}"]`)
                    .innerText = progress.toFixed(2);

                row.querySelector(`.week-bobot[data-week="${weekNo}"]`)
                    .innerText = bobot.toFixed(3);
            }

            document.addEventListener('input', function(e) {

                if (!e.target.classList.contains('week-vol')) return;

                const tr = e.target.closest('tr');

                const item = e.target.dataset.item;
                const week = e.target.dataset.week;

                const itemVol = tr.dataset.itemVol || 0;
                const itemBobot = tr.dataset.itemBobot || 0;

                recalcWeek(item, week, itemVol, itemBobot);
                autosaveWeek(item, week);
                hitungFooter();
                updateKurvaChartRealtime();
            });

            document.querySelectorAll('.week-vol').forEach(el => {

                const tr = el.closest('tr');

                recalcWeek(
                    el.dataset.item,
                    el.dataset.week,
                    tr.dataset.itemVol || 0,
                    tr.dataset.itemBobot || 0
                );
            });

            hitungFooter();

            let autosaveTimer = {};

            function autosaveWeek(item, week) {

                const key = item + '-' + week;

                clearTimeout(autosaveTimer[key]);

                autosaveTimer[key] = setTimeout(() => {

                    const input = document.querySelector(
                        `.week-vol[data-item="${item}"][data-week="${week}"]`
                    );

                    const oldVal = input.dataset.last || 0;
                    const vol = parseFloat(input.value) || 0;

                    fetch("{{ route('build-weekly.update') }}", {
                        method:"POST",
                        headers:{
                            "Content-Type":"application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({
                            item_id: item,
                            week_no: week,
                            volume: vol,
                        })
                    })
                    .then(async r => {

                        const data = await r.json();

                        if (!r.ok) {
                            input.value = oldVal;
                            showToast(data.error || 'Volume melebihi kontrak');
                            throw new Error("422");
                        }
                        recalcWeek(item, week, 
                            input.closest('tr').dataset.itemVol,
                            input.closest('tr').dataset.itemBobot
                        );

                        hitungFooter();
                        updateKurvaChartRealtime();

                        return data;
                    })
                    .then(res => {

                        input.dataset.last = vol;

                        if (res.full) {
                            lockItemRow(item);
                        }
                        refreshInvoicePanel();
                    })
                    .catch(e => {
                        console.log("autosave rejected:", e.message);
                    });

                }, 400);
            }

            function hitungFooter() {

                let weekCount = {{ $weekCount }};

                for (let w=1; w<=weekCount; w++) {

                    let sumBobot = 0;

                    document.querySelectorAll(`.week-bobot[data-week="${w}"]`)
                        .forEach(el => {
                            sumBobot += parseFloat(el.innerText || 0);
                        });

                    const bobotCell = document.getElementById(`sum-bobot-${w}`);
                    if (bobotCell) {
                        bobotCell.innerText = sumBobot.toFixed(2);
                    }
                }
            }
            function lockItemRow(itemId) {

                document.querySelectorAll(
                    `.week-vol[data-item="${itemId}"]`
                ).forEach(el => {
                    el.disabled = true;
                    el.classList.add('bg-light');
                });
            }
            function showToast(msg) {
                if (typeof toastr !== 'undefined') {
                    toastr.error(msg);
                } else {
                    alert(msg);
                }
            }
            hitungFooter();
            updateKurvaChartRealtime();
        });
        function hitungTotalPelaksanaan() {

            let grandTotalVolume = 0;
            let grandTotalHarga = 0;
            let grandTotalJustek = 0;

            document.querySelectorAll('tr[data-item-id]').forEach(row => {

                const hargaCell = row.querySelector('.harga-kontrak');

                // skip row yang bukan item asli
                if (!hargaCell) return;

                const volKontrak = parseFloat(row.dataset.itemVol) || 0;

                const hargaKontrak =
                    parseFloat(hargaCell.dataset.price) || 0;

                let totalTambah = 0;
                let totalKurang = 0;
                let totalBaru = 0;

                row.querySelectorAll('.just-tambah').forEach(i => {
                    totalTambah += parseFloat(i.value) || 0;
                });

                row.querySelectorAll('.just-kurang').forEach(i => {
                    totalKurang += parseFloat(i.value) || 0;
                });

                row.querySelectorAll('.just-baru').forEach(i => {
                    totalBaru += parseFloat(i.value) || 0;
                });

                const totalJustek =
                    totalTambah - totalKurang + totalBaru;

                const volPelaksanaan =
                    volKontrak + totalJustek;

                let hargaPelaksanaan = 0;

                if (volKontrak > 0) {

                    hargaPelaksanaan =
                        (volPelaksanaan / volKontrak) *
                        hargaKontrak;

                } else {

                    hargaPelaksanaan =
                        totalJustek * hargaKontrak;
                }

                const colTotalJustek =
                    row.querySelector('.total-justek');

                const colVolPelaksanaan =
                    row.querySelector('.total-pelaksanaan');

                const colNilaiPelaksanaan =
                    row.querySelector('.nilai-pelaksanaan');

                if (colTotalJustek) {
                    colTotalJustek.textContent =
                        totalJustek.toFixed(3);
                }

                if (colVolPelaksanaan) {
                    colVolPelaksanaan.textContent =
                        volPelaksanaan.toFixed(3);
                }

                if (colNilaiPelaksanaan) {
                    colNilaiPelaksanaan.textContent =
                        'Rp ' +
                        Math.round(hargaPelaksanaan)
                            .toLocaleString('id-ID');
                }

                grandTotalJustek += totalJustek;
                grandTotalVolume += volPelaksanaan;
                grandTotalHarga += hargaPelaksanaan;
            });

            const grandVol =
                document.getElementById('grand-total-pelaksanaan');

            if (grandVol) {
                grandVol.textContent =
                    grandTotalVolume.toFixed(3);
            }

            const grandNilai =
                document.getElementById('grand-total-pelaksanaan-nilai');

            if (grandNilai) {
                grandNilai.textContent =
                    'Rp ' +
                    Math.round(grandTotalHarga)
                        .toLocaleString('id-ID');
            }

            const footerJustek =
                document.getElementById('grand-total-justek');

            if (footerJustek) {
                footerJustek.textContent =
                    grandTotalJustek.toFixed(3);
            }
        }
        function autosaveJustek(item, week) {

            const kurang = document.querySelector(
                `.just-kurang[data-item="${item}"][data-week="${week}"]`
            )?.value || 0;

            const tambah = document.querySelector(
                `.just-tambah[data-item="${item}"][data-week="${week}"]`
            )?.value || 0;

            const baru = document.querySelector(
                `.just-baru[data-item="${item}"][data-week="${week}"]`
            )?.value || 0;

            fetch("{{ route('build-weekly.update') }}", {
                method: "POST",
                headers:{
                    "Content-Type":"application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    item_id: item,
                    week_no: week,
                    just_kurang: kurang,
                    just_tambah: tambah,
                    just_baru: baru
                })
            })
            .then(r=>r.json())
            .then(res=>{

                fetch("{{ route('projects.invoice.justek.auto',$project->id) }}",{
                    method:"POST",
                    headers:{
                    "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                    }
                })
                .then(()=>{

                    refreshInvoicePanel();

                });

            })
            .catch(e=>{
            console.log("Justek autosave gagal");
            });
        }

        document.addEventListener('input', e => {

            if (
                e.target.classList.contains('just-kurang') ||
                e.target.classList.contains('just-tambah') ||
                e.target.classList.contains('just-baru')
            ) {

                const item = e.target.dataset.item;
                const week = e.target.dataset.week;

                hitungTotalPelaksanaan();
                autosaveJustek(item, week);
            }
        });

        document.addEventListener('DOMContentLoaded', hitungTotalPelaksanaan);

    </script>
    <script>
        const WEEK_LABELS = @json($project->week_labels);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.addEventListener('click', async function(e) {

                const btn = e.target.closest('.btn-add-tambah');
                if (!btn) return;

                const itemId = btn.dataset.item;

                // cek editor
                let editorRow = document.querySelector(
                    `.row-editor[data-parent="${itemId}"]`
                );

                // kalau editor sudah ada → toggle editor saja
                if (editorRow) {

                    editorRow.classList.toggle('d-none');

                    return;
                }

                try {

                    btn.disabled = true;
                    btn.innerHTML = '...';

                    const response = await fetch(
                        `/projects/items/${itemId}/tambahan`
                    );

                    const html = await response.text();

                    const parentRow = document.querySelector(
                        `tr[data-item-id="${itemId}"]`
                    );

                    // cari posisi setelah row tambahan terakhir
                    let insertAfter = parentRow;

                    while (
                        insertAfter.nextElementSibling &&
                        insertAfter.nextElementSibling.classList.contains('row-tambahan-item')
                    ) {
                        insertAfter = insertAfter.nextElementSibling;
                    }

                    insertAfter.insertAdjacentHTML(
                        'afterend',
                        html
                    );

                    editorRow = insertAfter.nextElementSibling;

                    $(editorRow).find('.select2').select2({
                        width: '100%',
                    });

                    setupJustekAccess();

                } catch(err) {

                    console.error(err);

                    alert('Gagal load tambahan');

                } finally {

                    btn.disabled = false;
                    btn.innerHTML = '+';
                }
            });

            function buildWeekColumns(itemId) {
                let cols = '';

                WEEK_LABELS.forEach(w => {
                    cols +=
                    `
                        <td>
                            <input type="number"
                                step="0.01"
                                class="form-control week-vol"
                                data-item="${itemId}"
                                data-week="${w.week_no}"
                                value="">
                        </td>

                        <td class="week-progress"
                            data-week="${w.week_no}"
                            id="prog-${itemId}-${w.week_no}">
                        </td>

                        <td class="week-bobot"
                            data-week="${w.week_no}"
                            id="bobot-${itemId}-${w.week_no}">
                        </td>

                        <td class="just-col" data-week="${w.week_no}">
                            <input class="form-control just-kurang"
                                data-item="${itemId}"
                                data-week="${w.week_no}"
                                value="0">
                        </td>

                        <td class="just-col" data-week="${w.week_no}">
                            <input class="form-control just-tambah"
                                data-item="${itemId}"
                                data-week="${w.week_no}"
                                value="0">
                        </td>

                        <td class="just-col" data-week="${w.week_no}">
                            <input class="form-control just-baru"
                                data-item="${itemId}"
                                data-week="${w.week_no}"
                                value="0">
                        </td>
                    `;
                });

                return cols;
            }
            $(document).on('click', '.btn-simpan-tambahan', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const parentId = this.dataset.item;

                const select = document.querySelector(
                    `.job-tambahan[data-item="${parentId}"]`
                );

                const jobId = select.value;
                if (!jobId) {
                    alert('Pilih pekerjaan tambahan dulu');
                    return;
                }

                fetch("{{ route('build-items.store-tambahan') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        project_id: "{{ $project->id }}",
                        parent_item_id: parentId,
                        job_category_id: jobId
                    })
                })
                .then(r => r.json())
                .then(res => {
                    if (!res.success) return;

                    const data = res.data;
                    const parentId = data.parent_id;

                    // aktifkan Pek.Baru parent
                    document.querySelectorAll(
                        `.just-baru[data-item="${parentId}"]`
                    ).forEach(input => input.readOnly = false);

                    const parentRow = document.querySelector(
                        `tr[data-item-id="${parentId}"]`
                    );

                    if (!parentRow) {
                        console.error('Parent row tidak ditemukan');
                        return;
                    }

                    const rupiah = new Intl.NumberFormat('id-ID');

                    const newRow = document.createElement('tr');
                    newRow.className = 'table-warning row-tambahan-item';
                    newRow.dataset.parent = parentId;
                    newRow.dataset.itemId = data.id;
                    newRow.dataset.itemVol = data.volume;
                    newRow.dataset.itemBobot = data.bobot_percent ?? 0;
                    const weekCols = buildWeekColumns(data.id);

                    newRow.innerHTML = `
                        <td></td>
                        <td>
                            ↳ ${data.uraian}
                            <span class="badge bg-warning text-dark">Tambahan</span>
                        </td>
                        <td>${data.satuan}</td>
                        <td>${data.volume}</td>
                        <td class="harga-kontrak" data-price="${data.price}">
                            Rp ${rupiah.format(data.price)}
                        </td>
                        <td></td>
                        ${weekCols}
                        <td class="total-justek" data-item="${data.id}">0</td>
                        <td class="total-pelaksanaan"
                            data-item="${data.id}"
                            data-vol-kontrak="${data.volume}">
                            ${data.volume}
                        </td>
                        <td class="harga-kontrak" data-price="${data.price}">
                            Rp ${rupiah.format(data.price)}
                        </td>
                        <td class="nilai-pelaksanaan">0</td>
                    `;

                    let insertAfter = parentRow;

                    // cari row tambahan terakhir
                    while (
                        insertAfter.nextElementSibling &&
                        (
                            insertAfter.nextElementSibling.classList.contains('row-tambahan-item') ||
                            insertAfter.nextElementSibling.classList.contains('row-editor')
                        )
                    ) {
                        insertAfter = insertAfter.nextElementSibling;
                    }

                    // insert langsung ke table utama
                    insertAfter.after(newRow);
                    requestAnimationFrame(() => {
                        applyAutoFreeze();
                    });
                    hitungTotalPelaksanaan();
                    setupJustekAccess();

                    $(select).val(null).trigger('change');
                });

                return false;
            });
            function setupJustekAccess() {
                document.querySelectorAll('tr[data-item-id]').forEach(row => {

                    const isTambahan = row.classList.contains('table-warning');

                    const inputKurang = row.querySelectorAll('.just-kurang');
                    const inputTambah = row.querySelectorAll('.just-tambah');
                    const inputBaru   = row.querySelectorAll('.just-baru');
                    const weekInputs  = row.querySelectorAll('.week-vol');
                    if (isTambahan) {
                        inputKurang.forEach(i => {
                            i.value = 0;
                            i.disabled = true;
                            i.classList.add('bg-light');
                        });
                        inputTambah.forEach(i => {
                            i.value = 0;
                            i.disabled = true;
                            i.classList.add('bg-light');
                        });
                        weekInputs.forEach(i => {
                            i.value = 0;
                            i.disabled = true;
                            i.classList.add('bg-light');
                        });
                        inputBaru.forEach(i => {
                            i.disabled = false;
                            i.classList.remove('bg-light');
                        });

                    } else {
                        inputBaru.forEach(i => {
                            i.value = 0;
                            i.disabled = true;
                            i.classList.add('bg-light');
                        });

                        inputKurang.forEach(i => {
                            i.disabled = false;
                            i.classList.remove('bg-light');
                        });

                        inputTambah.forEach(i => {
                            i.disabled = false;
                            i.classList.remove('bg-light');
                        });
                        weekInputs.forEach(i => {
                            i.disabled = false;
                            i.classList.remove('bg-light');
                        });
                    }
                });
            }
            setupJustekAccess();
        });
    </script>
    <script>

    function refreshInvoicePanel() {

        fetch("{{ route('projects.invoice.panel',$project->id) }}")
        .then(res=>res.text())
        .then(html=>{
            document.getElementById('invoice-panel').innerHTML = html;
        });

    }
    </script>
    <script>

        document.getElementById('btn-export-pdf').addEventListener('click', function(e){

            e.preventDefault();

            const week =
                document.getElementById('filter-week').value;

            const date =
                document.getElementById('filter-date').value;

            let url = "{{ route('projects.export-pdf', $project->id) }}";

            const params = new URLSearchParams();

            if(week){
                params.append('week', week);
            }

            if(date){
                params.append('date', date);
            }

            window.open(
                url + '?' + params.toString(),
                '_blank'
            );

        });
    </script>
@endpush