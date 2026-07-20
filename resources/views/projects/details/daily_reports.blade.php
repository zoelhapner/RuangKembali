@php
    $dailies = $project?->dailyReports;
@endphp

@if($dailies->count())
    <x-collapse-card title="Riwayat Laporan Harian" target="detail-daily-body">
        <div class="card-body">
            <div class="border rounded p-3 mb-4">
                <div id="daily-report-page" data-project="{{ $project->id }}">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="20">No</th>
                                    <th>Tanggal</th>
                                    <th>Minggu</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>

                            @foreach($reports as $minggu => $items)
                                @php
                                    $weeklyReport = $weeklyReports[$minggu] ?? null;
                                @endphp
                                <tbody id="reportTable">
                                    <tr class="table-secondary week-header" data-week="{{ $minggu }}">
                                        <td colspan="3"
                                            class="fw-bold week-header"
                                            {{-- data-week="{{ $minggu }}" --}}
                                            style="cursor:pointer;">

                                            <span class="week-icon">▼</span>
                                            Minggu ke-{{ $minggu }}
                                            (<span class="hari-kerja">
                                                {{ $items->where('is_libur', false)->count() }}
                                            </span> Hari Kerja)
                                        </td>
                                        <td class="text-center">
                                            {{-- @if($items->where('is_libur', true)->count() >= 7) --}}
                                            <button
                                                class="btn btn-sm btn-dark btn-weekly-note"
                                                data-week="{{ $minggu }}"
                                                data-report-id="{{ $weeklyReport?->id }}"
                                                data-capaian="{{ $weeklyReport?->capaian }}"
                                                data-kendala="{{ $weeklyReport?->kendala }}"
                                                data-rencana="{{ $weeklyReport?->rencana }}">
                                                <i class="ti ti-{{ isset($weeklyReports[$minggu]) ? 'pencil' : 'plus' }}"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    @foreach($items as $report)
                                        <tr id="report-row-{{ $report->id }}"
                                                data-week="{{ $minggu }}"
                                                class="week-row week-{{ $minggu }} {{ $report->is_libur ? 'table-warning' : '' }}">
                                            <td>{{ $loop->iteration }}</td>

                                            <td>
                                                {{ \Carbon\Carbon::parse($report->tanggal)->format('d/m/Y') }}

                                                @if($report->is_libur)
                                                    <span class="badge bg-warning text-dark ms-2">
                                                        Libur
                                                    </span>
                                                @endif
                                            </td>

                                            <td>Minggu ke-{{ $report->minggu }}</td>

                                            <td>
                                                <button class="btn btn-sm btn-dark btn-detail" title="Detail"
                                                    data-id="{{ $report->id }}">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-dark btn-hapus" title="Hapus"
                                                    data-id="{{ $report->id }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="modal fade" id="dailyModal" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">

                            <div class="modal-header flex-column">

                                <div class="d-flex w-100 justify-content-between align-items-center">

                                    <h5 class="modal-title mt-2">
                                        Detail Laporan Harian
                                    </h5>

                                    <div class="d-flex align-items-center gap-2">

                                        <button class="btn btn-dark btn-sm" id="btnEditDaily">
                                            <i class="ti ti-edit"></i> Edit
                                        </button>

                                        <button
                                            class="btn-close"
                                            data-bs-dismiss="modal">
                                        </button>

                                    </div>

                                </div>

                                <div class="d-flex gap-2 mt-2 w-100">
                                    <span class="badge bg-dark" id="badgeWeek"></span>
                                        <span class="badge bg-secondary" id="badgeTanggal"></span>
                                        <input type="text" id="editTanggal" class="d-none" name="tanggal" form="editDailyForm">
                                </div>
                            </div>
                            <div class="modal-body" id="dailyModalBody">
                                <div class="text-center py-5">
                                    Loading...
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal fade" id="weeklyModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form id="weeklyForm">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Laporan Mingguan
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                                    <input type="hidden" name="minggu" id="weekly_minggu">
                                    <div class="mb-3">
                                        <label>Capaian Pekerjaan</label>
                                        <textarea name="capaian" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label>Kendala Pekerjaan</label>
                                        <textarea name="kendala" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label>Rencana Kerja</label>
                                        <textarea name="rencana" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-dark">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-collapse-card>
@endif
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function(){

        const modal = new bootstrap.Modal(document.getElementById('dailyModal'));
        const modalBody = document.getElementById('dailyModalBody');

        let workIndex = 0;
        let workerIndex = 0;
        let materialIndex = 0;
        let workTimeIndex = 0;
        let categories = [];
        let workerOptions = [];
        function addWorkRow(){
            
            const tbody = document.querySelector('#worksTable tbody');
            const row = `
            <tr>
                <td class="row-number"></td>

                <td>
                    <select
                        name="works[${workIndex}][rab_process_item_id]"
                        class="form-select select2 rab-select">

                        ${buildRabOptions()}

                    </select>
                    <input
                        type="text"
                        name="works[${workIndex}][uraian_manual]"
                        class="form-control mt-2 manual-work d-none">

                </td>

                <td>
                    <input type="number" step="0.01"
                        name="works[${workIndex}][volume]"
                        class="form-control">
                </td>

                <td>
                    <input type="text"
                        name="works[${workIndex}][satuan]"
                        class="form-control">
                </td>

                <td>
                    <input type="text"
                        name="works[${workIndex}][keterangan]"
                        class="form-control">
                </td>

                <td>
                    <button type="button"
                        class="btn btn-sm btn-danger btn-remove-work">
                        ×
                    </button>
                </td>
            </tr>`;

            tbody.insertAdjacentHTML('beforeend', row);
            const select = tbody.lastElementChild.querySelector('.rab-select');

            $(select).select2({
                width: '100%',
                dropdownParent: $('#dailyModal')
            });
            workIndex++;
            refreshNumber('#worksTable');
        }
        function addWorkerRow() {

            const tbody = document.querySelector('#workersTable tbody');

            const options = workerOptions.map(worker => `
                <option value="${worker.id}">
                    ${worker.user.fullname}
                </option>
            `).join('');

            const row = `
            <tr>
                <td class="row-number"></td>

                <td>

                    <select
                        name="workers[${workerIndex}][worker_id]"
                        class="form-select select2 worker-select">

                        <option value="">-- Pilih Tukang --</option>

                        ${options}

                        <option value="manual">
                            + Manual Input
                        </option>

                    </select>

                    <input
                        type="text"
                        name="workers[${workerIndex}][keahlian]"
                        class="form-control mt-2 manual-input d-none">

                </td>

                <td>
                    <input
                        type="number"
                        name="workers[${workerIndex}][jumlah]"
                        class="form-control"
                        value="1">
                </td>

                <td>
                    <input
                        type="text"
                        name="workers[${workerIndex}][alat]"
                        class="form-control">
                </td>

                <td>
                    <button
                        type="button"
                        class="btn btn-sm btn-danger btn-remove-worker">
                        ×
                    </button>
                </td>
            </tr>`;

            tbody.insertAdjacentHTML('beforeend', row);
            const select = tbody.lastElementChild.querySelector('.worker-select');
            $(select).select2({
                width: '100%',
                dropdownParent: $('#dailyModal')
            });
            workerIndex++;
            refreshNumber('#workersTable');
        }
        function addMaterialRow(){
            const tbody = document.querySelector('#materialsTable tbody');

            const row = `
            <tr>
                <td class="row-number"></td>

                <td>
                    <input type="text"
                        name="materials[${materialIndex}][nama_bahan]"
                        class="form-control">
                </td>

                <td>
                    <input type="number"
                        name="materials[${materialIndex}][diterima]"
                        class="form-control">
                </td>

                <td>
                    <input type="number"
                        name="materials[${materialIndex}][ditolak]"
                        class="form-control">
                </td>

                <td>
                    <button type="button"
                        class="btn btn-sm btn-danger btn-remove-material">
                        ×
                    </button>
                </td>
            </tr>`;

            tbody.insertAdjacentHTML('beforeend', row);
            materialIndex++;
            refreshNumber('#materialsTable');
        }
        function addCuacaRow(){

            const tbody = document.querySelector('#editJamKerjaTable tbody');

            const row = `
            <tr>

                <td>
                    <input type="time"
                        name="jam_kerja[${workTimeIndex}][jam_mulai]"
                        class="form-control jam-mulai">
                </td>

                <td>
                    <input type="time"
                        name="jam_kerja[${workTimeIndex}][jam_selesai]"
                        class="form-control jam-selesai">
                </td>

                <td>
                    <input type="number"
                        name="jam_kerja[${workTimeIndex}][total_jam]"
                        class="form-control total-jam"
                        step="0.01"
                        readonly>
                </td>

                <td>
                    <select
                        name="jam_kerja[${workTimeIndex}][cuaca]"
                        class="form-select select2">
                        <option value="">-- Pilih Cuaca --</option>
                        <option value="Cerah">Cerah</option>
                        <option value="Mendung">Mendung</option>
                        <option value="Hujan">Hujan</option>
                    </select>
                </td>

                <td>
                    <input type="text"
                        name="jam_kerja[${workTimeIndex}][keterangan]"
                        class="form-control">
                </td>

                <td>
                    <button type="button"
                        class="btn btn-danger btn-sm btn-remove-jam">
                        ×
                    </button>
                </td>

            </tr>`;

            tbody.insertAdjacentHTML('beforeend', row);

            workTimeIndex++;
        }
        function handleDelete(e, tableId, urlPrefix){

            const row = e.target.closest('tr');
            const idInput = row.querySelector('input[name*="[id]"]');

            if(idInput){

                const recordId = idInput.value;

                if(confirm('Yakin hapus data ini?')){

                    fetch(`/${urlPrefix}/${recordId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if(res.success){
                            row.remove();
                            refreshNumber(tableId);
                        }
                    });
                }

            }else{
                row.remove();
                refreshNumber(tableId);
            }
        }
        function refreshNumber(tableId){
            document.querySelectorAll(`${tableId} tbody tr`)
                .forEach((row,index)=>{
                    const cell = row.querySelector('.row-number');
                    if(cell){
                        cell.innerText = index + 1;
                    }
                });
        }
        function buildRabOptions(selectedId = null) {

            let html = `
                <option value="">-- Pilih Dari RAB --</option>
            `;

            categories.forEach(category => {

                html += `
                    <option disabled>
                        ${numberToLetters(category.order_no)}. ${category.name.toUpperCase()}
                    </option>
                `;

                category.uraians.forEach((uraian, u) => {

                    html += `
                        <option disabled>
                            &nbsp;&nbsp;${u + 1}. ${uraian.name}
                        </option>
                    `;

                    uraian.items.forEach((item, i) => {

                        html += `
                            <option
                                value="${item.id}"
                                data-volume="${item.volume}"
                                data-satuan="${item.satuan}"
                                ${item.id == selectedId ? 'selected' : ''}
                            >
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                ${u + 1}.${i + 1}
                                ${item.job_name}
                            </option>
                        `;
                    });

                });

            });

            html += `
                <option value="manual"
                    ${selectedId == 'manual' ? 'selected' : ''}>
                    + Manual Input
                </option>
            `;

            return html;
        }

        modalBody.addEventListener('click', function(e){

            if(e.target.closest('#btnAddWork')) addWorkRow();
            if(e.target.closest('#btnAddWorker')) addWorkerRow();
            if(e.target.closest('#btnAddMaterial')) addMaterialRow();
            if(e.target.closest('#btnAddJamKerjaEdit')) addCuacaRow();

            if(e.target.closest('.btn-remove-work')){
                handleDelete(e, '#worksTable', 'daily-work');
            }

            if(e.target.closest('.btn-remove-worker')){
                handleDelete(e, '#workersTable', 'daily-worker');
            }

            if(e.target.closest('.btn-remove-material')){
                handleDelete(e, '#materialsTable', 'daily-material');
            }

            if(e.target.closest('.btn-remove-jam')){
                handleDelete(e, '#editJamKerjaTable', 'daily-work-time');
            }
        });
        document.querySelectorAll('.btn-detail').forEach(btn => {

            btn.addEventListener('click', function(){

                const id = this.dataset.id;
                
                modalBody.innerHTML = "";
                workIndex = 0;
                workerIndex = 0;
                materialIndex = 0;
                workTimeIndex = 0;

                fetch(`/daily/${id}/detail`)
                .then(res => res.json())
                .then(response => {
                    const data = response.daily;
                    workerOptions = response.worker_options;
                    categories = response.categories;
                    document.getElementById('badgeWeek').textContent =
                        `Minggu ${data.minggu}`;

                    document.getElementById('badgeTanggal').textContent =
                        data.tanggal_formatted;
                    document.getElementById('editTanggal').value = data.tanggal;
                    $('#badgeWeek').text(`Minggu ${data.minggu}`);
                    $('#badgeTanggal').text(data.tanggal_formatted);

                    // isi input hidden/edit
                    $('#editTanggal').val(data.tanggal);
                    let workTimeRows = '';

                    (data.work_times || []).forEach((t, i) => {

                        let badge = 'bg-secondary';

                        if(t.cuaca === 'Baik'){
                            badge = 'bg-success';
                        }else if(t.cuaca === 'Mendung'){
                            badge = 'bg-warning text-dark';
                        }else if(t.cuaca === 'Hujan'){
                            badge = 'bg-primary';
                        }

                        workTimeRows += `
                        <tr>

                            <td>${t.jam_mulai ?? '-'}</td>

                            <td>${t.jam_selesai ?? '-'}</td>

                            <td>${formatDurasi(t.total_jam)}</td>

                            <td>
                                <span class="badge ${badge}">
                                    ${t.cuaca ?? '-'}
                                </span>
                            </td>

                            <td>${t.keterangan ?? '-'}</td>
                        </tr>`;
                    });
                    let pekerjaanRows = '';
                    data.works.forEach((w,i)=>{
                        pekerjaanRows += `
                        <tr>
                            <td>${w.rab_process_item 
                                ? w.rab_process_item.job_name 
                                : (w.uraian_manual ?? '-')}</td>
                            <td>${w.volume}</td>
                            <td>${w.satuan ?? '-'}</td>
                            <td>${w.keterangan ?? '-'}</td>
                        </tr>`;
                    });

                    let workerRows = '';
                    data.workers.forEach((w,i)=>{
                        workerRows += `
                        <tr>
                            <td>${w.worker 
                                ? w.worker.user.fullname 
                                : (w.keahlian ?? '-')}</td>
                            <td>${w.jumlah}</td>
                            <td>${w.alat ?? '-'}</td>
                        </tr>`;
                    });

                    let materialRows = '';
                    data.materials.forEach((m,i)=>{
                        materialRows += `
                        <tr>
                            <td>${m.nama_bahan}</td>
                            <td>${m.diterima}</td>
                            <td>${m.ditolak}</td>
                        </tr>`;
                    });
                    
                    modalBody.innerHTML = `

                    <div id="viewMode">
                        <h6>Tenaga Kerja & Alat Bantu</h6>
                            <table class="table table-bordered mb-4">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama / Keahlian</th>
                                        <th>Jumlah</th>
                                        <th>Alat</th>
                                    </tr>
                                </thead>
                                <tbody>${workerRows}</tbody>
                            </table>
                        <h6 class="mt-4">File Upload Foto Tukang</h6>
                        ${renderDocumentation(data, 'tenaga')}
                        <hr>
                        <h6>Pekerjaan Yang Diselenggarakan Hari Ini</h6>
                        <table class="table table-bordered mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th>Uraian</th>
                                    <th>Volume</th>
                                    <th>Satuan</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>${pekerjaanRows}</tbody>
                        </table>
                        <h6 class="mt-4">File Upload Foto Pekerjaan</h6>
                        ${renderDocumentation(data, 'pekerjaan')}
                        <hr>
                        <h6>Bahan Yang Masuk</h6>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Bahan</th>
                                    <th>Diterima</th>
                                    <th>Ditolak</th>
                                </tr>
                            </thead>
                            <tbody>${materialRows}</tbody>
                        </table>
                        <h6 class="mt-4">File Upload Foto Bahan</h6>
                        ${renderDocumentation(data, 'material')}
                        <hr>
                        <h6>Jam Kerja & Cuaca</h6>
                        <table class="table table-bordered mb-4">

                            <thead class="table-light">
                                <tr>
                                    <th width="150">Jam Mulai</th>
                                    <th width="150">Jam Selesai</th>
                                    <th width="120">Total Jam</th>
                                    <th width="150">Cuaca</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>

                            <tbody>
                                ${
                                    workTimeRows || `
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            Tidak ada data jam kerja
                                        </td>
                                    </tr>
                                    `
                                }
                            </tbody>

                        </table>
                        <div class="mt-3">
                            <label class="fw-semibold">Catatan / Perintah Konsultan MK</label>
                            <textarea class="form-control" readonly>${data.catatan ?? ''}</textarea>
                        </div>
                        <hr>
                        <h6 class="mt-4">Pengesahan</h6>

                        <div class="d-flex justify-content-between text-center gap-3">

                            <div class="flex-fill">
                                <p class="fw-semibold">Side Manager</p>
                                <br><br>
                                <u class="pengesahan-name">${
                                    data.mk_employee?.user?.fullname ?? '____________________'
                                }</u>
                            </div>

                            <div class="flex-fill">
                                <p class="fw-semibold">Project Manager</p>
                                <br><br>
                                <u class="pengesahan-name">${
                                    data.kontraktor_employee?.user?.fullname ?? '____________________'
                                }</u>
                            </div>

                        </div>
                    </div>
                    <div id="editMode" style="display:none;">
                        <form id="editDailyForm">
                            <input type="hidden" name="id" value="${data.id}">
                            <!--
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchLibur">
                                    <span class="ms-2">Tandai sebagai Hari Libur</span>
                                </label>

                                <span id="statusHari" class="badge bg-success">Hari Kerja</span>
                            </div> -->
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Tenaga Kerja & Alat Bantu</h6>
                                
                                <button type="button"
                                    class="btn btn-sm btn-dark"
                                    id="btnAddWorker"
                                    title="Tambah Tenaga">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                            <table class="table table-bordered" id="workersTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama / Keahlian</th>
                                        <th width="120">Jumlah</th>
                                        <th width="150">Alat</th>
                                        <th width="80">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                ${data.workers.map((w,i)=>`
                                <tr>
                                    <td class="row-number">${i+1}</td>
                                    <td>

                                    <input type="hidden"
                                        name="workers[${i}][id]"
                                        value="${w.id ?? ''}">

                                    <select name="workers[${i}][worker_id]" class="form-select select2 worker-select">
                                        ${workerOptions.map(worker => `
                                            <option
                                                value="${worker.id}"
                                                ${worker.id == w.worker_id ? 'selected' : ''}
                                            >
                                                ${worker.user.fullname}
                                            </option>
                                        `).join('')}
                                            <option
                                                value="manual"
                                                ${!w.worker_id ? 'selected' : ''}>
                                                + Manual Input
                                            </option>
                                    </select>

                                    <input
                                        type="text"
                                        name="workers[${i}][keahlian]"
                                        value="${w.keahlian ?? ''}"
                                        class="form-control mt-2 manual-input ${w.worker_id ? 'd-none' : ''}">

                                    </td>
                                    <td>
                                        <input type="number"
                                            name="workers[${i}][jumlah]"
                                            class="form-control"
                                            value="${w.jumlah}">
                                    </td>
                                    <td>
                                        <input type="text"
                                            name="workers[${i}][alat]"
                                            class="form-control"
                                            value="${w.alat ?? ''}">
                                    </td>
                                    <td>
                                        <button type="button"
                                            class="btn btn-sm btn-danger btn-remove-worker">
                                            ×
                                        </button>
                                    </td>
                                </tr>
                                `).join('')}
                                </tbody>
                            </table>
                            <h6 class="mt-4">File Upload Foto Tukang</h6>
                            <div id="editDocsTenaga">
                                ${renderDocumentationEdit(data, 'tenaga')}
                            </div>

                            <input type="file" name="new_files_tenaga[]" 
                                class="form-control mt-2" multiple>
                            <div id="previewTenaga"
                                class="d-flex flex-wrap gap-3 mt-3">
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Pekerjaan</h6>
                                <button type="button"
                                    class="btn btn-sm btn-dark"
                                    id="btnAddWork"
                                    title="Tambah Pekerjaan">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                            <table class="table table-bordered" id="worksTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Uraian</th>
                                        <th width="120">Volume</th>
                                        <th width="120">Satuan</th>
                                        <th>Keterangan</th>
                                        <th width="40">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.works.map((w,i)=>`
                                    <tr>
                                        <td class="row-number">${i+1}</td>

                                        <td>
                                            <input type="hidden" name="works[${i}][id]" value="${w.id}">
                                            <select
                                                name="works[${i}][rab_process_item_id]"
                                                class="form-select select2 rab-select">

                                                ${buildRabOptions(w.rab_process_item_id ?? 'manual')}

                                            </select>

                                                    <input
                                                        type="text"
                                                        name="works[${i}][uraian_manual]"
                                                        value="${w.uraian_manual ?? ''}"
                                                        class="form-control mt-2 manual-work ${w.rab_process_item_id ? 'd-none' : ''}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01"
                                                name="works[${i}][volume]"
                                                class="form-control"
                                                value="${w.volume}">
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="works[${i}][satuan]"
                                                class="form-control"
                                                value="${w.satuan ?? '-'}">
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="works[${i}][keterangan]"
                                                class="form-control"
                                                value="${w.keterangan ?? '-'}">
                                        </td>
                                        <td>
                                            <button type="button"
                                                class="btn btn-sm btn-danger btn-remove-work">
                                                ×
                                            </button>
                                        </td>
                                    </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                            <h6 class="mt-4">File Upload Foto Pekerjaan</h6>
                            <div id="editDocsPekerjaan">
                                ${renderDocumentationEdit(data, 'pekerjaan')}
                            </div>

                            <input type="file" name="new_files_pekerjaan[]" id="fileInputPekerjaan"
                                class="form-control mt-2" multiple>
                            <div id="previewPekerjaan"
                                class="d-flex flex-wrap gap-3 mt-3">
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Bahan Yang Masuk</h6>
                                <button type="button"
                                    class="btn btn-sm btn-dark"
                                    id="btnAddMaterial"
                                    title="Tambah Pekerjaan">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                            <table class="table table-bordered" id="materialsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Bahan</th>
                                    <th>Diterima</th>
                                    <th>Ditolak</th>
                                    <th width="80">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            ${data.materials.map((m,i)=>`
                            <tr>
                                <td class="row-number">${i+1}</td>
                                <td>
                                    <input type="hidden" name="materials[${i}][id]" value="${m.id}">

                                    <input type="text"
                                        class="form-control"
                                        name="materials[${i}][nama_bahan]"
                                        value="${m.nama_bahan ?? ''}">
                                </td>
                                <td>
                                    <input type="number"
                                        name="materials[${i}][diterima]"
                                        class="form-control"
                                        value="${m.diterima}">
                                </td>
                                <td>
                                    <input type="number"
                                        name="materials[${i}][ditolak]"
                                        class="form-control"
                                        value="${m.ditolak}">
                                </td>
                                <td>
                                    <button type="button"
                                        class="btn btn-sm btn-danger btn-remove-material">
                                        ×
                                    </button>
                                </td>
                            </tr>
                            `).join('')}
                            </tbody>
                            </table>
                            <h6 class="mt-4">File Upload Foto Bahan</h6>
                            <div id="editDocsMaterial">
                                ${renderDocumentationEdit(data, 'material')}
                            </div>

                            <input type="file" name="new_files_material[]" 
                                class="form-control mt-2" multiple>
                            <div id="previewMaterial"
                                class="d-flex flex-wrap gap-3 mt-3">
                            </div>
                            <div class="mb-3">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                                        <span>Jam Kerja & Cuaca</span>

                                        <button type="button"
                                            class="btn btn-sm btn-dark"
                                            id="btnAddJamKerjaEdit">
                                            <i class="ti ti-plus"></i>
                                        </button>
                                    </div>

                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0" id="editJamKerjaTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="170">Jam Mulai</th>
                                                        <th width="170">Jam Selesai</th>
                                                        <th width="120">Total Jam</th>
                                                        <th width="180">Cuaca</th>
                                                        <th>Keterangan</th>
                                                        <th width="50"></th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                ${
                                                    (data.work_times && data.work_times.length)
                                                    ? data.work_times.map((j,i)=>`
                                                    <tr>
                                                        <td>
                                                            <input type="hidden"
                                                                name="jam_kerja[${i}][id]"
                                                                value="${j.id ?? ''}">

                                                            <input type="time"
                                                                name="jam_kerja[${i}][jam_mulai]"
                                                                class="form-control jam-mulai"
                                                                value="${j.jam_mulai ?? ''}">
                                                        </td>

                                                        <td>
                                                            <input type="time"
                                                                name="jam_kerja[${i}][jam_selesai]"
                                                                class="form-control jam-selesai"
                                                                value="${j.jam_selesai ?? ''}">
                                                        </td>

                                                        <td>
                                                            <input type="hidden"
                                                                name="jam_kerja[${i}][total_jam]"
                                                                class="total-jam-value"
                                                                value="${j.total_jam ?? ''}">

                                                            <input type="text"
                                                                class="form-control total-jam-text"
                                                                readonly>
                                                        </td>

                                                        <td>
                                                            <select
                                                                name="jam_kerja[${i}][cuaca]"
                                                                class="form-select select2">

                                                                <option value="">-- Pilih Cuaca --</option>

                                                                <option value="Cerah"
                                                                    ${j.cuaca=='Cerah'?'selected':''}>
                                                                    Cerah
                                                                </option>

                                                                <option value="Mendung"
                                                                    ${j.cuaca=='Mendung'?'selected':''}>
                                                                    Mendung
                                                                </option>

                                                                <option value="Hujan"
                                                                    ${j.cuaca=='Hujan'?'selected':''}>
                                                                    Hujan
                                                                </option>
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <input type="text"
                                                                name="jam_kerja[${i}][keterangan]"
                                                                class="form-control"
                                                                value="${j.keterangan ?? ''}">
                                                        </td>

                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm btn-remove-jam">
                                                                ×
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    `).join('')
                                                    :
                                                    `
                                                    <tr>
                                                        <td>
                                                            <input type="time"
                                                                name="jam_kerja[0][jam_mulai]"
                                                                class="form-control jam-mulai">
                                                        </td>

                                                        <td>
                                                            <input type="time"
                                                                name="jam_kerja[0][jam_selesai]"
                                                                class="form-control jam-selesai">
                                                        </td>

                                                        <td>
                                                            <input type="hidden"
                                                                name="jam_kerja[${i}][total_jam]"
                                                                class="total-jam-value"
                                                                value="${j.total_jam ?? ''}">

                                                            <input type="text"
                                                                class="form-control total-jam-text"
                                                                readonly>
                                                        </td>

                                                        <td>
                                                            <select
                                                                name="jam_kerja[0][cuaca]"
                                                                class="form-select">
                                                                <option value="">-- Pilih Cuaca --</option>
                                                                <option value="Cerah">Cerah</option>
                                                                <option value="Mendung">Mendung</option>
                                                                <option value="Hujan">Hujan</option>
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <input type="text"
                                                                name="jam_kerja[0][keterangan]"
                                                                class="form-control">
                                                        </td>

                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm btn-remove-jam">
                                                                ×
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    `
                                                }
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Catatan</label>
                                <textarea name="catatan" class="form-control">${data.catatan ?? ''}</textarea>
                            </div>
                            <button type="button" class="btn btn-dark" id="btnSaveAll">
                                Simpan
                            </button>
                        </form>
                    </div>
                    `;
                    $('#btnEditDaily').off('click').on('click', function () {
                        $('#badgeTanggal').addClass('d-none');
                        $('#editTanggal').removeClass('d-none');
                        if ($('#editTanggal')[0]._flatpickr) {
                            $('#editTanggal')[0]._flatpickr.destroy();
                        }
                        flatpickr('#editTanggal', {
                            locale: "id",
                            dateFormat: "Y-m-d",
                            altInput: true,
                            altFormat: "d F Y",
                            defaultDate: data.tanggal,
                            minDate: response.start_date,
                            maxDate: response.end_date,
                            disable: response.used_dates
                        });
                    });
                    $('#workersTable .worker-select').each(function () {
                        $(this).select2({
                            width: '100%',
                            dropdownParent: $('#dailyModal')
                        });

                        $(this).trigger('change');
                    });
                    $('#worksTable .rab-select').each(function () {
                        $(this).select2({
                            width: '100%',
                            dropdownParent: $('#dailyModal')
                        });

                        $(this).trigger('change');
                    });
                    $(document).on('change', '.rab-select', function () {

                        const tr = $(this).closest('tr');

                        const manual = tr.find('.manual-work');
                        const satuan = tr.find('input[name$="[satuan]"]');
                        const volume  = tr.find('input[name$="[volume]"]');

                        if ($(this).val() === 'manual') {

                            manual.removeClass('d-none').focus();

                            satuan.val('');
                            volume.val('');

                            return;
                        }

                        manual.addClass('d-none').val('');

                        const option = $(this).find(':selected');

                        satuan.val(option.data('satuan') || '');
                        volume.val(option.data('volume') || '');
                    }); 

                    // const switchLibur = document.getElementById('switchLibur');
                    // const statusHari = document.getElementById('statusHari');

                    // switchLibur.checked = data.is_libur;

                    // if(data.is_libur){
                    //     statusHari.className = "badge bg-danger";
                    //     statusHari.innerText = "Hari Libur";
                    // }else{
                    //     statusHari.className = "badge bg-success";
                    //     statusHari.innerText = "Hari Kerja";
                    // }
                    
                    // switchLibur.addEventListener('change', function(){

                    //     let isLibur = this.checked ? 1 : 0;

                    //     fetch(`/daily-report/toggle-libur/${dailyId}`,{
                    //         method: "POST",
                    //         headers:{
                    //             "Content-Type":"application/json",
                    //             "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    //         },
                    //         body: JSON.stringify({
                    //             is_libur: isLibur
                    //         })
                    //     })
                    //     .then(res=>res.json())
                    //     .then(res=>{

                    //         if(res.success){

                    //             if(isLibur){
                    //                 statusHari.className = "badge bg-danger";
                    //                 statusHari.innerText = "Hari Libur";
                    //             }else{
                    //                 statusHari.className = "badge bg-success";
                    //                 statusHari.innerText = "Hari Kerja";
                    //             }

                    //         }

                    //     });

                    // });
                    const btnToggle = document.getElementById('btnEditDaily');
                    const viewMode = document.getElementById('viewMode');
                    const editMode = document.getElementById('editMode');

                    btnToggle.addEventListener('click', function(){

                        if(viewMode.style.display !== 'none'){
                            // Masuk mode edit
                            viewMode.style.display = 'none';
                            editMode.style.display = 'block';
                            btnToggle.textContent = 'Kembali';
                            btnToggle.classList.remove('btn-dark');
                            btnToggle.classList.add('btn-secondary');
                        }else{
                            // Kembali ke view
                            editMode.style.display = 'none';
                            viewMode.style.display = 'block';
                            btnToggle.textContent = 'Edit';
                            btnToggle.classList.remove('btn-secondary');
                            btnToggle.classList.add('btn-dark');
                        }

                    });
                    
                    $(document).on('change', '.worker-select', function () {

                        const input = $(this)
                            .closest('td')
                            .find('.manual-input');

                        if ($(this).val() === 'manual') {
                            input.removeClass('d-none').focus();
                        } else {
                            input.addClass('d-none').val('');
                        }

                    });
                    workIndex = data.works.length;
                    workerIndex = data.workers.length;
                    materialIndex = data.materials.length;
                    workTimeIndex = data.work_times.length;
                    modal.show();
                    setupAdvancedPreview('fileInputPekerjaan', 'previewPekerjaan');
                    setupAdvancedPreview('fileInputTenaga', 'previewTenaga');
                    setupAdvancedPreview('fileInputMaterial', 'previewMaterial');
                    document.querySelectorAll('#editJamKerjaTable tbody tr').forEach(function(row){
                        hitungTotalJamEdit(row);
                    });
                });
            });
        });
        function renderDocumentation(data, category) {
                let docs = (data.documentations || []).filter(doc =>
                    doc.category.toLowerCase() === category.toLowerCase()
                );

            if(docs.length === 0){
                return `<p class="text-muted">Tidak ada File Upload</p>`;
            }

            let html = `<div class="d-flex flex-wrap gap-3">`;

            docs.forEach(doc => {

                let fileUrl = "/storage/" + doc.file_path;

                if(doc.file_type.startsWith("image")){

                    html += `
                        <div style="width:120px">
                            <a href="${fileUrl}" target="_blank">
                                <img src="${fileUrl}" 
                                    class="img-fluid rounded shadow-sm mb-1">
                            </a>
                            <small class="d-block text-truncate">${doc.file_name}</small>
                        </div>
                    `;
                }

                else if(doc.file_type === "application/pdf"){

                    html += `
                        <div style="width:120px">
                            <a href="${fileUrl}" 
                            target="_blank"
                            class="text-decoration-none">
                                <div class="border rounded p-3 text-center shadow-sm">
                                    📄
                                </div>
                                <small class="d-block text-truncate mt-1">
                                    ${doc.file_name}
                                </small>
                            </a>
                        </div>
                    `;
                }
            });

            html += `</div>`;

            return html;
        }
        function renderDocumentationEdit(data, category) {

            let docs = (data.documentations || []).filter(doc =>
                doc.category.toLowerCase() === category.toLowerCase()
            );

            if(docs.length === 0){
                return `<p class="text-muted">Tidak ada file</p>`;
            }

            let html = `<div class="d-flex flex-wrap gap-3">`;

            docs.forEach(doc => {

                let fileUrl = "/storage/" + doc.file_path;

                html += `
                <div style="width:120px; position:relative">
                    <input type="hidden" 
                        name="existing_docs[]" 
                        value="${doc.id}">

                    <button type="button"
                            class="btn btn-sm btn-danger btn-delete-doc"
                            data-id="${doc.id}"
                            style="position:absolute; top:-5px; right:-5px;">
                        ×
                    </button>

                    <a href="${fileUrl}" target="_blank">
                        <img src="${fileUrl}"
                            class="img-fluid rounded shadow-sm mb-1">
                    </a>

                    <small class="d-block text-truncate">
                        ${doc.file_name}
                    </small>
                </div>`;
            });

            html += `</div>`;

            return html;
        }
        function setupAdvancedPreview(inputId, previewContainerId){

            const input = document.getElementById(inputId);
            const previewContainer = document.getElementById(previewContainerId);

            if(!input) return;

            let fileStore = []; // ⬅ simpan file aktif disini

            input.addEventListener('change', function(e){

                const newFiles = Array.from(e.target.files);

                // Tambah ke store (tidak replace)
                fileStore = [...fileStore, ...newFiles];

                renderPreview();
                rebuildFileList();
            });

            function renderPreview(){

                previewContainer.innerHTML = '';

                fileStore.forEach((file, index)=>{

                    const reader = new FileReader();

                    reader.onload = function(e){

                        let html = `
                        <div style="width:120px; position:relative">
                            <button type="button"
                                class="btn btn-sm btn-danger remove-file"
                                data-index="${index}"
                                style="position:absolute; top:-5px; right:-5px;">
                                ×
                            </button>`;

                        if(file.type.startsWith('image')){
                            html += `
                                <img src="${e.target.result}"
                                    class="img-fluid rounded shadow-sm mb-1">`;
                        } else if(file.type === "application/pdf"){
                            html += `
                                <div class="border rounded p-4 text-center shadow-sm">
                                    📄 PDF
                                </div>`;
                        } else {
                            html += `
                                <div class="border rounded p-4 text-center shadow-sm">
                                    📎 File
                                </div>`;
                        }

                        html += `
                            <small class="d-block text-truncate">
                                ${file.name}
                            </small>
                        </div>`;

                        previewContainer.insertAdjacentHTML('beforeend', html);
                    };

                    reader.readAsDataURL(file);
                });
            }

            function rebuildFileList(){

                const dataTransfer = new DataTransfer();

                fileStore.forEach(file => {
                    dataTransfer.items.add(file);
                });

                input.files = dataTransfer.files;
            }

            // Hapus individual file
            previewContainer.addEventListener('click', function(e){

                if(e.target.classList.contains('remove-file')){

                    const index = e.target.dataset.index;

                    fileStore.splice(index, 1);

                    renderPreview();
                    rebuildFileList();
                }

            });

        }
        function formatDurasi(totalJam) {

            totalJam = Number(totalJam || 0);

            const jam = Math.floor(totalJam);
            const menit = Math.round((totalJam - jam) * 60);

            if (jam > 0 && menit > 0) {
                return `${jam} Jam ${menit} Menit`;
            }

            if (jam > 0) {
                return `${jam} Jam`;
            }

            return `${menit} Menit`;
        }
        function hitungTotalJamEdit(row){

            const mulai = row.querySelector('.jam-mulai').value;
            const selesai = row.querySelector('.jam-selesai').value;

            if(!mulai || !selesai){
                row.querySelector('.total-jam-value').value = '';
                row.querySelector('.total-jam-text').value = '';
                return;
            }

            const start = new Date(`2000-01-01 ${mulai}`);
            const end   = new Date(`2000-01-01 ${selesai}`);

            let diffMenit = (end - start) / 1000 / 60;

            if(diffMenit < 0){
                diffMenit += 24 * 60;
            }

            const jam = Math.floor(diffMenit / 60);
            const menit = diffMenit % 60;

            const decimal = diffMenit / 60;

            let text = '';

            if(jam > 0){
                text += `${jam} Jam`;
            }

            if(menit > 0){
                if(text !== '') text += ' ';
                text += `${menit} Menit`;
            }

            if(text === ''){
                text = '0 Jam';
            }

            row.querySelector('.total-jam-value').value = decimal.toFixed(2);
            row.querySelector('.total-jam-text').value = text;
        }
        document.addEventListener('input', function(e){

            if(
                e.target.classList.contains('jam-mulai') ||
                e.target.classList.contains('jam-selesai')
            ){
                hitungTotalJamEdit(e.target.closest('tr'));
            }

        });
        document.addEventListener('click', function(e){

            if(e.target.classList.contains('btn-delete-doc')){

                const id = e.target.dataset.id;

                if(confirm('Yakin hapus file ini?')){

                    fetch(`/daily/documentation/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if(res.success){
                            e.target.closest('div').remove();
                        }
                    });

                }
            }

        });
        document.addEventListener('click', function(e){

            if(e.target.closest('#btnSaveAll')){

                console.log('Button Save diklik');

                const form = document.getElementById('editDailyForm');
                
                const formData = new FormData(form);

                const id = formData.get('id');

                fetch(`/daily/${id}/update-all`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async res => {
                    console.log(res.status);

                    const data = await res.json();
                    console.log(data);

                    return data;
                })
                .then(res => {

                    if(res.success){

                        alert('Semua data berhasil diupdate');

                        location.reload();

                    }else{

                        alert(res.message);
                    }

                })
                .catch(err => {

                    console.error(err);

                });
            }
        });
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function(){

    const storageKey = "weeklyCollapseState";

    // ambil state dari localStorage
    let savedState = JSON.parse(localStorage.getItem(storageKey)) || {};

    document.querySelectorAll(".week-header").forEach(function(header){

        let week = header.getAttribute("data-week");
        let rows = document.querySelectorAll(".week-" + week);
        let icon = header.querySelector(".week-icon");

        // 🔹 Restore state saat reload
        if(savedState[week] === true){
            rows.forEach(row => row.style.display = "");
            icon.innerHTML = "▼";
        } else {
            rows.forEach(row => row.style.display = "none");
            icon.innerHTML = "▶";
        }

        // 🔹 Saat diklik
        header.addEventListener("click", function(){

            let isOpen = rows[0].style.display !== "none";

            rows.forEach(function(row){
                row.style.display = isOpen ? "none" : "";
            });

            icon.innerHTML = isOpen ? "▶" : "▼";

            // Simpan ke localStorage
            savedState[week] = !isOpen;
            localStorage.setItem(storageKey, JSON.stringify(savedState));

        });

    });

});
</script>
<script>
$(document).on('click','.btn-hapus',function(){

    let id = $(this).data('id');
    let row = $('#report-row-'+id);
    let week = row.data('week');

    Swal.fire({
        title:'Yakin hapus?',
        icon:'warning',
        showCancelButton:true,
        confirmButtonText:'Ya hapus'
    }).then((result)=>{

        if(result.isConfirmed){

            $.ajax({
                url:'/reports/'+id,
                type:'DELETE',

                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success:function(res){

                    row.remove();

                    updateWeekHeader(week);
                    refreshNextDate();

                    Swal.fire({
                        icon:'success',
                        title:'Terhapus',
                        timer:1500,
                        showConfirmButton:false
                    });

                },

                error:function(xhr){
                    console.log(xhr.responseText);

                    Swal.fire({
                        icon:'error',
                        title:'Gagal hapus'
                    });
                }

            });

        }

    });

});
function refreshNextDate(){

    let projectId = $('#daily-report-page').data('project');

    $.get('/reports/next-date/' + projectId, function(res){

        $('#nextDate').val(res.date);

    });

}
function updateWeekHeader(week){

    let rows = $('tr[data-week="'+week+'"]');

    let header = $('.week-header[data-week="'+week+'"]');

    if(rows.length === 0){

        header.remove();
        return;

    }

    let hariKerja = rows.not('.table-warning').length;

    header.find('.hari-kerja').text(hariKerja);

}
$(document).on('click', '.btn-weekly-note', function () {

    let minggu = $(this).data('week');
    let reportId = $(this).data('report-id');

    $('#weekly_minggu').val(minggu);
    if(reportId){
        $('textarea[name="capaian"]').val($(this).data('capaian'));
        $('textarea[name="kendala"]').val($(this).data('kendala'));
        $('textarea[name="rencana"]').val($(this).data('rencana'));
    }else{
        // reset form
        $('#weeklyForm')[0].reset();
        $('#weekly_minggu').val(minggu);
    }
    $('#weeklyModal .modal-title')
        .text('Laporan Mingguan - Minggu ke-' + minggu);

    $('#weeklyModal').modal('show');
});
$('#weeklyForm').submit(function(e){

    e.preventDefault();

    $.ajax({
        url: "{{ route('weekly-report.store', $project) }}",
        type: 'POST',
        data: $(this).serialize(),

        success: function(res){

            $('#weeklyModal').modal('hide');

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: res.message
            });
        },

        error: function(xhr){

            let message = 'Terjadi kesalahan.';

            if(xhr.status === 422){

                let errors = xhr.responseJSON.errors;

                message = Object.values(errors)
                    .flat()
                    .join('<br>');
            }

            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                html: message
            });
        }
    });

});
</script>
@endpush