@extends('tablar::page')

@section('content')
<div class="container-fluid">

    <div class="row">

        {{-- PANEL KIRI --}}
        <div class="col-md-3">

            <div class="card">
                <div class="card-header">
                    Pengaturan Kurva
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <label>Posisi Kiri (mm)</label>
                        <input type="number"
                            id="curve_left_mm"
                            class="form-control"
                            value="{{ $project->curve_left_mm ?? 110 }}">
                    </div>

                    <div class="mb-3">
                        <label>Posisi Atas (mm)</label>
                        <input type="number"
                            id="curve_top_mm"
                            class="form-control"
                            value="{{ $project->curve_top_mm ?? 80 }}">
                    </div>

                    <div class="mb-3">
                        <label>Lebar Kurva (mm)</label>
                        <input type="number"
                            id="curve_width_mm"
                            class="form-control"
                            value="{{ $project->curve_width_mm ?? 220 }}">
                    </div>

                    <div class="mb-3">
                        <label>Tinggi Kurva (mm)</label>
                        <input type="number"
                            id="curve_height_mm"
                            class="form-control"
                            value="{{ $project->curve_height_mm ?? 50 }}">
                    </div>

                    <div class="d-grid gap-2">

                        <button
                            type="button"
                            class="btn btn-outline-secondary move-curve"
                            data-left="-5"
                            data-top="0">
                            ← Geser Kiri
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary move-curve"
                            data-left="5"
                            data-top="0">
                            → Geser Kanan
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary move-curve"
                            data-left="0"
                            data-top="-5">
                            ↑ Geser Atas
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary move-curve"
                            data-left="0"
                            data-top="5">
                            ↓ Geser Bawah
                        </button>

                    </div>

                    <hr>

                    <button
                        class="btn btn-success w-100"
                        id="save-curve-setting">
                        Simpan Pengaturan
                    </button>

                </div>
            </div>

        </div>

        {{-- PREVIEW --}}
        <div class="col-md-9">

            <div class="card">

                <div class="card-header">
                    Preview Time Schedule
                </div>

                <div class="card-body">

                    <div id="preview-wrapper"
                        style="
                            position:relative;
                            background:#fff;
                            overflow:auto;
                            border:1px solid #ddd;
                            padding:20px;
                        ">

                        {{-- TABEL --}}
                        @include('build.pdf-kurvas')

                        {{-- KURVA --}}
                        <div id="curve-preview"
                            style="
                                position:absolute;
                                left:110mm;
                                top:80mm;
                                width:220mm;
                                height:50mm;
                                border:1px dashed red;
                                background:rgba(255,255,255,.8);
                            ">

                            @include('build.kurva-svg')

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
@endsection