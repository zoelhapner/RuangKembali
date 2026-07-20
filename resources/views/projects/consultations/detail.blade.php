<div class="card mb-4">
    <div class="card-header fw-bold">Detail Konsultasi</div>
    <div class="card-body">

        <div class="row g-3">
            <div class="col-md-4">
                <label>Nama Customer</label>
                <input type="text" class="form-control" value="{{ $consultation->contact_name }}" readonly>
            </div>

            <div class="col-md-4">
                <label>No HP</label>
                <input type="text" class="form-control" value="{{ $consultation->contact_phone }}" readonly>
            </div>

            <div class="col-md-4">
                <label>Karyawan</label>
                <input type="text" class="form-control"
                    value="{{ $consultation->employee->display_name }}" readonly>
            </div>
        </div>

        <hr>

        <h6>Uraian</h6>
        <ul>
            @foreach($consultation->items as $item)
                <li>
                    <b>{{ $item->description }}</b>
                    <br>
                    <small>{{ $item->remark }}</small>
                </li>
            @endforeach
        </ul>

    </div>
</div>
