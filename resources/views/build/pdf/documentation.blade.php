<h2 style="text-align:center; margin-bottom:20px;">
    Dokumentasi Proyek
</h2>

@foreach($dailyReports as $report)

    <h4 style="margin-bottom:5px;">
        {{ $report->tanggal_formatted }}
        (Minggu {{ $report->minggu }})
    </h4>

    <p style="margin-top:0; margin-bottom:10px;">
        {{ $report->pekerjaan }}
    </p>

    @if($report->documentations->count())
        <div style="overflow:hidden;">

            @foreach($report->documentations->take(3) as $doc)

                <div style="
                    float:left;
                    width:32%;
                    margin-right:2%;
                ">

                    <img
                        src="file://{{ storage_path('app/public/'.$doc->file_path) }}"
                        style="
                            width:165px;
                            height:120px;
                            object-fit:cover;
                        ">

                </div>

            @endforeach

        </div>
    @else
        <div style="
            text-align:center;
            padding:25px;
            color:#666;
            font-size:12px;
            border:1px dashed #999;
            background:#f8f8f8;
        ">
            <strong>Dokumentasi Belum Tersedia</strong><br><br>
            Mohon maaf, foto dokumentasi untuk tanggal
            <strong>{{ $report->tanggal_formatted }}</strong>
            masih belum tersedia.
        </div>
    @endif

    <br>

@endforeach