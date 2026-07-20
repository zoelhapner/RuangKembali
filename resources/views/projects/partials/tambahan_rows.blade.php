@php
    $colsFixed   = 6;
    $colsPerWeek = 6;
    $colsTotal   = 4;

    $totalCols = $colsFixed + (count($weekLabels) * $colsPerWeek) + $colsTotal;
@endphp

<tr class="row-editor bg-light"
    data-parent="{{ $item->id }}">

    <td colspan="{{ $colsFixed }}" class="p-2 sticky-editor">

        <div class="d-flex gap-2 align-items-end">

            <div style="min-width:300px; max-width:500px; width:100%;">
                <label class="form-label mb-1">
                    Pekerjaan Tambahan
                </label>

                <select class="form-select select2 job-tambahan"
                        data-item="{{ $item->id }}">

                    <option value="">
                        -- Pilih Pekerjaan --
                    </option>

                    @foreach($jobCategories as $job)
                        <option value="{{ $job->id }}">
                            {{ $job->nama_pekerjaan }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div>
                <button type="button"
                        class="btn btn-dark btn-simpan-tambahan"
                        data-item="{{ $item->id }}">
                    Tambahkan ke Kontrak
                </button>
            </div>

        </div>

    </td>

    <td colspan="{{ $totalCols - $colsFixed }}"></td>
</tr>