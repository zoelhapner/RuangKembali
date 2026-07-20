<x-collapse-card title="Input Laporan Harian" target="input-body">
    <form action="{{ route('build.daily.store') }}" method="POST" enctype="multipart/form-data">
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

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                Informasi Laporan Harian

                <button type="submit"
                    name="is_libur"
                    value="1"
                    class="btn btn-dark btn-sm">
                    Simpan Sebagai Hari Libur
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Proyek</label>
                            <input class="form-control" value="{{ $project->project_name }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Lokasi</label>
                            <input class="form-control" value="{{ $project->city?->name }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label required">Tanggal</label>

                        <input type="text"
                            id="nextDate"
                            name="tanggal"
                            class="form-control"
                            value="{{ old('tanggal', $nextDate->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label>Kontraktor</label>
                            <input name="kontraktor" class="form-control" value="{{ old('kontraktor', 'Antosa Architect') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header fw-bold d-flex justify-content-between">
                <span>Tenaga Kerja & Alat Bantu</span>
                    <button type="button" class="btn btn-sm btn-dark btn-add-icon" id="addTenaga" data-bs-toggle="tooltip" title="Tambah Tenaga">
                        <i class="ti ti-plus"></i>
                    </button>
            </div>
            <div class="card-body p-0">
                <div class="mb-3 mt-4 ps-4">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th width="300">Keahlian</th>
                                    <th width="150">Jumlah (Org)</th>
                                    <th>Alat Bantu</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            @php
                                $workersOld = old('worker_id', ['']);
                            @endphp
                            <tbody id="tenagaTable">
                                @foreach($workersOld as $i => $workerId)
                                    <tr>
                                        <td>
                                            <select name="worker_id[]" class="form-select select2 worker-select">
                                                <option value="">-- Pilih Tenaga Kerja --</option>
                                                    @foreach($workers as $worker)
                                                        <option value="{{ $worker->id }}"
                                                            {{ $workerId == $worker->id ? 'selected' : '' }}>
                                                            {{ $worker->user->fullname }}
                                                        </option>
                                                    @endforeach
                                                <option value="manual"
                                                    {{ $workerId == 'manual' ? 'selected' : '' }}>
                                                    + Manual Input
                                                </option>
                                            </select>

                                            <input
                                                type="text"
                                                name="keahlian[]"
                                                class="form-control mt-2 manual-input {{ $workerId=='manual' ? '' : 'd-none' }}"
                                                value="{{ old('keahlian.'.$i) }}">
                                        </td>

                                        <td>
                                            <input type="number" name="jumlah[]" class="form-control" value="{{ old('jumlah.'.$i) }}">
                                        </td>

                                        <td>
                                            <input name="alat[]" class="form-control" value="{{ old('alat.'.$i) }}">
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm removeTenaga">
                                                ×
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <template id="tenagaTemplate">
                        <tr>
                            <td>
                                <select name="worker_id[]" class="form-select select2 worker-select">
                                    <option value="">-- Pilih Tenaga Kerja --</option>

                                    @foreach($workers as $worker)
                                        <option value="{{ $worker->id }}">
                                            {{ $worker->user->fullname }}
                                        </option>
                                    @endforeach

                                    <option value="manual">+ Manual Input</option>
                                </select>

                                <input type="text"
                                    name="keahlian[]"
                                    class="form-control mt-2 manual-input d-none"
                                    placeholder="Isi keahlian manual">
                            </td>

                            <td>
                                <input type="number" name="jumlah[]" class="form-control">
                            </td>

                            <td>
                                <input name="alat[]" class="form-control">
                            </td>

                            <td>
                                <button type="button" class="btn btn-danger btn-sm removeTenaga">
                                    ×
                                </button>
                            </td>
                        </tr>
                    </template>
                </div>
                <div class="md-6 mb-4 ps-4">
                    <label class="fw-bold">File Upload Foto Tukang</label>
                    <div class="text-muted mb-2">Bisa berupa foto atau dokumen (Maks ukuran 1Mb)</div>
                    <input type="file" id="documentation_tenaga"
                        name="documentation_tenaga[]"
                        class="d-none image-input"
                        data-preview="preview-tenaga"
                        accept="image/*,application/pdf"
                        multiple>
                     <label for="documentation_tenaga"
                        class="btn btn-dark btn-add-icon"
                        data-bs-toggle="tooltip"
                        title="Upload Foto Tukang">
                        <i class="ti ti-photo-plus"></i>
                    </label>
                        <div id="preview-tenaga"
                            class="mt-3 d-flex flex-wrap gap-3"></div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header fw-bold d-flex justify-content-between">
                <span>Pekerjaan Yang Diselenggarakan Hari Ini</span>
                <button type="button" class="btn btn-sm btn-dark btn-add-icon" id="addWork" data-bs-toggle="tooltip" title="Tambah Pekerjaan">
                    <i class="ti ti-plus"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="mb-3 mt-4 ps-4">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th>Uraian Pekerjaan</th>
                                    <th width="120">Satuan</th>
                                    <th width="120">Volume</th>
                                    <th>Keterangan</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            @php
                                $rabOld = old('rab_process_item_id', ['']);
                            @endphp
                            <tbody id="workTable">
                                @foreach($rabOld as $i => $rabId)
                                    <tr>
                                        <td>
                                            {{-- <select name="rab_process_item_id[]" 
                                                    class="form-select select2 rab-select">
                                                <option value="">-- Pilih Dari RAB --</option>

                                                @foreach($rabs as $rab)
                                                    <option value="{{ $rab->id }}"
                                                        data-volume="{{ $rab->volume }}"
                                                        data-satuan="{{ $rab->satuan }}"
                                                        {{ $rabId == $rab->id ? 'selected' : '' }}>
                                                        {{ $rab->job_name }} ({{ $rab->rab->job_location }})
                                                    </option>
                                                @endforeach

                                                <option value="manual"
                                                    {{ $rabId == 'manual' ? 'selected' : '' }}>
                                                    + Manual Input
                                                </option>
                                            </select> --}}
                                            <select name="rab_process_item_id[]" class="form-select select2 rab-select">

                                                <option value="">-- Pilih Dari RAB --</option>

                                                @foreach($categories as $category)

                                                    <option disabled style="font-weight:bold;">
                                                        {{ number_to_letters($category->order_no) }}. {{ strtoupper($category->name) }}
                                                    </option>

                                                    @foreach($category->uraians as $uraian)
                                                        <option disabled>
                                                            &nbsp;&nbsp;{{ $loop->iteration }}. {{ $uraian->name }}
                                                        </option>

                                                        @foreach($uraian->items as $rab)
                                                            <option value="{{ $rab->id }}"
                                                                    data-volume="{{ $rab->volume }}"
                                                                    data-satuan="{{ $rab->satuan }}"
                                                                    {{ $rabId == $rab->id ? 'selected' : '' }}>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                                {{ $loop->parent->iteration }}.{{ $loop->iteration }}
                                                                {{ $rab->job_name }}
                                                            </option>
                                                        @endforeach

                                                    @endforeach

                                                @endforeach

                                                <option value="manual"
                                                    {{ $rabId == 'manual' ? 'selected' : '' }}>
                                                    + Manual Input
                                                </option>

                                            </select>
                                            <input
                                                type="text"
                                                name="uraian_manual[]"
                                                class="form-control mt-2 manual-rab {{ $rabId == 'manual' ? '' : 'd-none' }}"
                                                value="{{ old('uraian_manual.'.$i) }}"
                                                placeholder="Isi uraian manual">
                                        </td>
                                        <td>
                                            <input name="daily[satuan][]" 
                                                type="text"
                                                class="form-control satuan-input"
                                                    value="{{ old('daily.satuan.'.$i) }}">
                                        </td>
                                        <td>
                                            <input name="daily[volume][]" 
                                                type="number" 
                                                step="0.01"
                                                class="form-control volume-input"
                                                value="{{ old('daily.volume.'.$i) }}">
                                        </td>

                                        <td>
                                            <input name="ket[]" class="form-control"     value="{{ old('ket.'.$i) }}">
                                        </td>

                                        <td>
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm removeWork">
                                                ×
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <template id="kerjaTemplate">
                        <tr>
                            <td>
                                <select name="rab_process_item_id[]" class="form-select select2 rab-select">

                                    <option value="">-- Pilih Dari RAB --</option>

                                    @foreach($categories as $category)

                                        <option disabled style="font-weight:bold;">
                                            {{ number_to_letters($category->order_no) }}. {{ strtoupper($category->name) }}
                                        </option>

                                        @foreach($category->uraians as $uraian)

                                            <option disabled>
                                                &nbsp;&nbsp;{{ $loop->iteration }}. {{ $uraian->name }}
                                            </option>
                                            @foreach($uraian->items as $rab)
                                                <option value="{{ $rab->id }}"
                                                        data-volume="{{ $rab->volume }}"
                                                        data-satuan="{{ $rab->satuan }}"
                                                        {{ $rabId == $rab->id ? 'selected' : '' }}>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                    {{ $loop->parent->iteration }}.{{ $loop->iteration }}
                                                    {{ $rab->job_name }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                    <option value="manual"
                                        {{ $rabId == 'manual' ? 'selected' : '' }}>
                                        + Manual Input
                                    </option>
                                </select>
                                <input type="text"
                                    name="uraian_manual[]"
                                    class="form-control mt-2 manual-rab d-none"
                                    placeholder="Isi uraian manual">
                            </td>
                            <td>
                                <input name="daily[satuan][]" 
                                    type="text"
                                    class="form-control satuan-input">
                            </td>
                            <td>
                                <input name="daily[volume][]" 
                                    type="number" 
                                    step="0.01"
                                    class="form-control volume-input">
                            </td>

                            <td>
                                <input name="ket[]" class="form-control">
                            </td>

                            <td>
                                <button type="button" 
                                        class="btn btn-danger btn-sm removeWork">
                                    ×
                                </button>
                            </td>
                        </tr>
                    </template>
                </div>
                <div class="md-6 mb-4 ps-4">
                    <label class="fw-bold">File Upload Foto Pekerjaan</label>
                    <div class="text-muted mb-2">Bisa berupa foto atau dokumen (Maks ukuran 1Mb)</div>
                    <input type="file"
                        id="documentation_pekerjaan"
                        name="documentation_pekerjaan[]"
                        class="d-none image-input"
                        data-preview="preview-pekerjaan"
                        accept="image/*,application/pdf"
                        multiple>

                    <label for="documentation_pekerjaan"
                        class="btn btn-dark btn-add-icon"
                        data-bs-toggle="tooltip"
                        title="Upload Foto Pekerjaan">
                        <i class="ti ti-photo-plus"></i>
                    </label>
                    <div id="preview-pekerjaan" class="mt-3 d-flex flex-wrap gap-3"></div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header fw-bold d-flex justify-content-between">
                <span>Bahan Yang Masuk</span>
                <button type="button" class="btn btn-sm btn-dark btn-add-icon" id="addMaterial" data-bs-toggle="tooltip" title="Tambah Bahan">
                    <i class="ti ti-plus"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="mb-3 mt-4 ps-4">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th>Jenis Bahan</th>
                                    <th width="150">Diterima</th>
                                    <th width="150">Ditolak</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            @php
                                $materials = old('bahan', ['']);
                            @endphp
                            <tbody id="materialTable">
                                @foreach($materials as $i => $x)
                                <tr>
                                    <td>
                                        <input name="bahan[]" class="form-control" value="{{ old('bahan.'.$i) }}">
                                    </td>
                                    <td>
                                        <input type="number" name="diterima[]" class="form-control" value="{{ old('diterima.'.$i) }}">
                                    </td>
                                    <td>
                                        <input type="number" name="ditolak[]" class="form-control" value="{{ old('ditolak.'.$i) }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm removeMaterial">
                                            ×
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="md-6 mb-4 ps-4">
                    <label class="fw-bold">File Upload Foto Bahan</label>
                    <div class="text-muted mb-2">Bisa berupa foto atau dokumen (Maks ukuran 1Mb)</div>
                    <input type="file" id="documentation_material"
                        name="documentation_material[]"
                        class="d-none image-input"
                        data-preview="preview-material"
                        accept="image/*,application/pdf"
                        multiple>
                    <label for="documentation_material"
                        class="btn btn-dark btn-add-icon"
                        data-bs-toggle="tooltip"
                        title="Upload Foto Bahan">
                        <i class="ti ti-photo-plus"></i>
                    </label>
                    <div id="preview-material" class="mt-3 d-flex flex-wrap gap-3"></div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header fw-bold d-flex justify-content-between">
                <span>Jam Kerja & Cuaca</span>

                <button type="button" class="btn btn-sm btn-dark btn-add-icon" data-bs-toggle="tooltip" title="Tambah Baris" id="addJamKerja">
                    <i class="ti ti-plus"></i>
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="180">Jam Mulai</th>
                                <th width="180">Jam Selesai</th>
                                <th width="160">Total Jam</th>
                                <th>Cuaca</th>
                                <th>Keterangan</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        @php
                            $jamMulai = old('jam_mulai', ['']);
                        @endphp
                        <tbody id="jamKerjaTable">
                            @foreach($jamMulai as $i => $jam)
                                <tr>
                                    <td>
                                        <input type="time"
                                            name="jam_mulai[]"
                                            class="form-control jam-mulai" value="{{ old('jam_mulai.'.$i) }}">
                                    </td>

                                    <td>
                                        <input type="time"
                                            name="jam_selesai[]"
                                            class="form-control jam-selesai" value="{{ old('jam_selesai.'.$i) }}">
                                    </td>

                                    <td>
                                        <input type="hidden"
                                            name="total_jam[]"
                                            class="total-jam-value"
                                            value="{{ old('total_jam.'.$i) }}">

                                        <input type="text"
                                            class="form-control total-jam-text"
                                            readonly>
                                    </td>

                                    <td>
                                        <select name="cuaca[]"
                                                class="form-select select2">
                                            <option value="">-- Pilih Cuaca --</option>
                                            <option value="Cerah" {{ old('cuaca.'.$i)=='Cerah' ? 'selected':'' }}>Cerah</option>
                                            <option value="Mendung" {{ old('cuaca.'.$i)=='Mendung' ? 'selected':'' }}>Mendung</option>
                                            <option value="Hujan" {{ old('cuaca.'.$i)=='Hujan' ? 'selected':'' }}>Hujan</option>
                                        </select>
                                    </td>

                                    <td>
                                        <input type="text"
                                            name="cuaca_keterangan[]"
                                            class="form-control"
                                            value="{{ old('cuaca_keterangan.'.$i) }}"
                                            placeholder="Keterangan tambahan">
                                    </td>

                                    <td>
                                        <button type="button"
                                                class="btn btn-danger btn-sm removeJamKerja">
                                            ×
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <template id="jamKerjaTemplate">
                        <tr>

                            <td>
                                <input type="time"
                                    name="jam_mulai[]"
                                    class="form-control jam-mulai">
                            </td>

                            <td>
                                <input type="time"
                                    name="jam_selesai[]"
                                    class="form-control jam-selesai">
                            </td>

                            <td>
                                <input type="hidden"
                                    name="total_jam[]"
                                    class="total-jam-value"
                                    value="{{ old('total_jam.'.$i) }}">

                                <input type="text"
                                    class="form-control total-jam-text"
                                    readonly>
                            </td>

                            <td>
                                <select name="cuaca[]"
                                        class="form-select select2">
                                    <option value="">-- Pilih Cuaca --</option>
                                    <option value="Cerah">Cerah</option>
                                    <option value="Mendung">Mendung</option>
                                    <option value="Hujan">Hujan</option>
                                </select>
                            </td>

                            <td>
                                <input type="text"
                                    name="cuaca_keterangan[]"
                                    class="form-control"
                                    placeholder="Keterangan tambahan">
                            </td>

                            <td>
                                <button type="button"
                                        class="btn btn-danger btn-sm removeJamKerja">
                                    ×
                                </button>
                            </td>

                        </tr>
                    </template>
                </div>
            </div>
        </div>
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header fw-bold">
                    Catatan / Perintah Konsultan MK
                </div>
                <div class="card-body">
                    <textarea name="catatan" class="form-control" rows="5">{{ old('catatan') }}</textarea>
                </div>
            </div>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header fw-bold">
                Pengesahan
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6">
                        Site Manager
                        <br><br><br><br>
                        <select name="mk_id" class="form-select select2">
                            <option value="mk_id">-- Pilih Site Manager --</option>
                            @foreach($employees as $emp)
                                <option
                                    value="{{ $emp->id }}"
                                    {{ old('mk_id')==$emp->id ? 'selected':'' }}>
                                    {{ $emp->user->fullname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        Project Manager
                            <br><br><br><br>
                        <select name="kontraktor_ttd_id" class="form-select select2">
                            <option value="kontraktor_ttd_id">-- Pilih Project Manager --</option>
                            @foreach($employees as $emp)
                                <option
                                    value="{{ $emp->id }}"
                                    {{ old('kontraktor_ttd_id')==$emp->id ? 'selected':'' }}>
                                    {{ $emp->user->fullname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end mt-4">
            <button class="btn btn-dark">Simpan Laporan Harian</button>
        </div>
    </form>
</x-collapse-card>
@push('js')
<script>

flatpickr("#nextDate", {

    locale: "id",

    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d F Y",

    defaultDate: "{{ $nextDate->format('Y-m-d') }}",

    minDate: "{{ \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') }}",

    maxDate: "{{ \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') }}",

    disable: @json($usedDates)

});

</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2').select2({ width:'100%' });
        document.getElementById('addTenaga').addEventListener('click', function() {
            let template = document.getElementById('tenagaTemplate');
            let clone = template.content.cloneNode(true);
            document.querySelector('#tenagaTable').appendChild(clone);
            $('#tenagaTable .select2').last().select2({ width:'100%' });
        });
        document.addEventListener('click', function(e) {

            if(e.target.classList.contains('removeTenaga')) {

                let rows = document.querySelectorAll('#tenagaTable tr');

                if(rows.length > 1){
                    e.target.closest('tr').remove();
                } else {
                    alert('Minimal 1 baris harus ada');
                }
            }

        });
        $(document).on('change', '.worker-select', function(){

            let row = $(this).closest('tr');
            let manualInput = row.find('.manual-input');

            if($(this).val() === 'manual'){
                manualInput.removeClass('d-none');
            } else {
                manualInput.addClass('d-none');
                manualInput.val('');
            }

        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // INIT SELECT2
        $('.select2').select2({ width:'100%' });
        $('.worker-select').trigger('change');
        $('.rab-select').trigger('change');
        document.getElementById('addWork').addEventListener('click', function() {

            let template = document.getElementById('kerjaTemplate');
            let clone = template.content.cloneNode(true);

            document.querySelector('#workTable').appendChild(clone);

            // init select2 hanya yang baru
            $('#workTable .select2').last().select2({ width:'100%' });

        });

        // HAPUS PEKERJAAN (minimal 1 baris)
        document.addEventListener('click', function(e) {

            if(e.target.classList.contains('removeWork')) {

                let rows = document.querySelectorAll('#workTable tr');

                if(rows.length > 1){
                    e.target.closest('tr').remove();
                } else {
                    alert('Minimal 1 baris harus ada');
                }
            }

        });

        $(document).on('change', '.rab-select', function () {

            let row = $(this).closest('tr');
            let selectedOption = $(this).find(':selected');

            let volumeInput = row.find('.volume-input');
            let satuanInput = row.find('.satuan-input');
            let manualInput = row.find('.manual-rab');

            if ($(this).val() === 'manual') {

                manualInput.removeClass('d-none');

            } else {

                manualInput.addClass('d-none');
                manualInput.val('');

                let volume = selectedOption.data('volume');
                let satuan = selectedOption.data('satuan');

                satuanInput.val(satuan ?? '');

                // volume hanya sebagai placeholder
                volumeInput.val('');
                volumeInput.attr('placeholder', volume ?? '');
            }

        });
    });
</script>
<script>
    document.getElementById('addMaterial').addEventListener('click', function() {

        let table = document.querySelector('#materialTable');
        let row = table.querySelector('tr').cloneNode(true);

        row.querySelectorAll('input')
        .forEach(i => i.value='');

        table.appendChild(row);

    });

    document.addEventListener('click', function(e) {
        if(e.target.classList.contains('removeMaterial')) {

            let rows = document.querySelectorAll('#materialTable tr');

            if(rows.length > 1){
                e.target.closest('tr').remove();
            }

        }
    });
</script>
<script>
    document.addEventListener("change", function(e){

        // if(e.target.classList.contains("image-input")){
        //     let previewId = e.target.dataset.preview;
        //     let previewContainer = document.getElementById(previewId);

        //     previewContainer.innerHTML = "";

        //     Array.from(e.target.files).forEach(file => {

        //         let div = document.createElement("div");
        //         div.style.width = "120px";

        //         // JIKA IMAGE
        //         if(file.type.startsWith("image/")){

        //             let reader = new FileReader();

        //             reader.onload = function(event){

        //                 div.innerHTML = `
        //                     <img src="${event.target.result}" 
        //                         class="img-fluid rounded shadow-sm mb-2">
        //                     <small class="d-block text-truncate">${file.name}</small>
        //                 `;

        //                 previewContainer.appendChild(div);
        //             }

        //             reader.readAsDataURL(file);

        //         }
        //         // JIKA PDF
        //         else if(file.type === "application/pdf"){

        //             div.innerHTML = `
        //                 <div class="border rounded p-3 text-center shadow-sm">
        //                     📄
        //                     <div class="small mt-2 text-truncate">${file.name}</div>
        //                 </div>
        //             `;

        //             previewContainer.appendChild(div);
        //         }

        //     });

        // }

    });
</script>
<script>
    document.getElementById('addJamKerja')
        ?.addEventListener('click', function () {

        const template = document
            .getElementById('jamKerjaTemplate')
            .content
            .cloneNode(true);

        document
            .getElementById('jamKerjaTable')
            .appendChild(template);

        $('.select2').select2({
            width: '100%'
        });
    });
    document.addEventListener('click', function(e){

        if (e.target.classList.contains('removeJamKerja')) {
            e.target.closest('tr').remove();
        }
    });
    function hitungTotalJam(row){

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

        const totalDecimal = diffMenit / 60;

        let hasil = '';

        if(jam > 0){
            hasil += `${jam} Jam`;
        }

        if(menit > 0){
            if(hasil !== '') hasil += ' ';
            hasil += `${menit} Menit`;
        }

        if(hasil === ''){
            hasil = '0 Jam';
        }

        row.querySelector('.total-jam-value').value = totalDecimal.toFixed(2);
        row.querySelector('.total-jam-text').value = hasil;
    }
    document.addEventListener('input', function(e){

        if(
            e.target.classList.contains('jam-mulai') ||
            e.target.classList.contains('jam-selesai')
        ){
            hitungTotalJam(e.target.closest('tr'));
        }

    });
    document.querySelectorAll('#jamKerjaTable tr').forEach(function(row){
        hitungTotalJam(row);
    });
</script>
@endpush