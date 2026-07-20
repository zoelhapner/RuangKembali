<h1 style="text-align:center; margin-bottom:15px;">
    BOBOT KEMAJUAN PEKERJAAN
</h1>

<table cellspacing="0" cellpadding="5" border="1">
    <thead>

        <tr style="background:#c4c4c4; text-align:center;">

            <th rowspan="2" width="35">
                NO
            </th>

            <th rowspan="2" width="260">
                URAIAN
            </th>

            <th colspan="2" width="80">
                TERKONTRAK
            </th>

            <th colspan="1" width="2">
                BOBOT
            </th>

            @foreach($weeks as $w)

                <th colspan="3">
                    PRESTASI S/D MINGGU LALU
                </th>

                <th colspan="3">
                    PRESTASI MINGGU INI
                </th>

                <th colspan="3">
                    PRESTASI S/D MINGGU INI
                </th>

            @endforeach

        </tr>

        <tr style="background:#d9d9d9; text-align:center;">

            <th width="40">
                SAT
            </th>

            <th width="50">
                VOL
            </th>
            <th width="10">
                %
            </th>

            @foreach($weeks as $w)

                <th>
                    VOL
                </th>

                <th>
                    PROGRESS
                </th>

                <th>
                    BOBOT
                </th>

                <th>
                    VOL
                </th>

                <th>
                    PROGRESS
                </th>

                <th>
                    BOBOT
                </th>
                <th>
                    VOL
                </th>

                <th>
                    PROGRESS
                </th>

                <th>
                    BOBOT
                </th>

            @endforeach

        </tr>

    </thead>

    <tbody>

        @foreach($groupedItems as $category)

            @php

                $items = collect($category['uraians'])->flatMap(fn($u) => $u['items']);

                $subtotalBobot =$items->sum('bobot_percent');

            @endphp

            <tr class="category">

                {{-- NO --}}
                <td align="center">
                    {{ \PhpOffice\PhpSpreadsheet\Cell\Coordinate
                        ::stringFromColumnIndex($loop->iteration) }}
                </td>

                {{-- NAMA CATEGORY --}}
                <td>
                    {{ strtoupper($category['category_name']) }}
                </td>

                {{-- SAT --}}
                <td></td>

                {{-- VOL --}}
                <td></td>

                {{-- BOBOT --}}
                <td align="right">
                    {{ number_format($subtotalBobot,2) }}%
                </td>

                {{-- SISA KOLOM --}}
                @for($i = 0; $i < ($weeks->count() * 9); $i++)
                    <td></td>
                @endfor

            </tr>
            @php $no = 1; @endphp

            @foreach($category['uraians'] as $uraian)

                <tr class="uraian">

                    <td align="center">
                        {{ $no }}
                    </td>

                    <td>
                        {{ ucwords($uraian['uraian_name']) }}
                    </td>

                    @for($i = 0; $i < ($totalCols - 2); $i++)
                        <td></td>
                    @endfor

                </tr>
                @php $itemNo = 1; @endphp

                @foreach($uraian['items'] as $item)

                    <tr>

                        <td align="center">
                            {{ $no }}.{{ $itemNo }}
                        </td>

                        <td style="white-space:normal">
                            {{ $item->uraian }}
                        </td>

                        <td align="center">
                            {{ $item->satuan }}
                        </td>

                        <td align="right">
                            {{ number_format($item->volume,2,',','.') }}
                        </td>

                        <td align="right">
                            {{ number_format($item->bobot_percent,2) }}%
                        </td>

                        @foreach($weeks as $w)
                            @php

                                $volMingguLalu = 0;

                                for($i = 1; $i < $w['week_no']; $i++){

                                    $p =
                                        $item->progress_map[$i]
                                        ?? null;

                                    $volMingguLalu +=
                                        $p->volume ?? 0;

                                }

                                $progressMingguLalu = $item->volume > 0

                                    ? ($volMingguLalu / $item->volume) * 100

                                    : 0;

                                $bobotMingguLalu = $item->volume > 0

                                    ? ($volMingguLalu / $item->volume)
                                        * $item->bobot_percent

                                    : 0;

                                $prog =
                                    $item->progress_map[$w['week_no']]
                                    ?? null;

                                $volMinggu =
                                    $prog->volume ?? 0;

                                $progressMinggu = $item->volume > 0

                                    ? ($volMinggu / $item->volume) * 100

                                    : 0;

                                $bobotMinggu = $item->volume > 0

                                    ? ($volMinggu / $item->volume)
                                        * $item->bobot_percent

                                    : 0;

                                $volKumulatif =
                                    $volMingguLalu + $volMinggu;

                                $progressKumulatif = $item->volume > 0

                                    ? ($volKumulatif / $item->volume) * 100

                                    : 0;

                                $bobotKumulatif = $item->volume > 0

                                    ? ($volKumulatif / $item->volume)
                                        * $item->bobot_percent

                                    : 0;

                            @endphp

                            <td align="right">
                                {{ number_format($volMingguLalu,2) }}
                            </td>

                            <td align="right">
                                {{ number_format($progressMingguLalu,2) }}%
                            </td>

                            <td align="right">
                                {{ number_format($bobotMingguLalu,2) }}%
                            </td>

                            <td align="right">
                                {{ number_format($volMinggu,2) }}
                            </td>

                            <td align="right">
                                {{ number_format($progressMinggu,2) }}%
                            </td>

                            <td align="right">
                                {{ number_format($bobotMinggu,2) }}%
                            </td>

                            <td align="right">
                                {{ number_format($volKumulatif,2) }}
                            </td>

                            <td align="right">
                                {{ number_format($progressKumulatif,2) }}%
                            </td>

                            <td align="right">
                                {{ number_format($bobotKumulatif,2) }}%
                            </td>

                        @endforeach
                    </tr>

                    @php $itemNo++; @endphp

                @endforeach

                @php $no++; @endphp

            @endforeach

        @endforeach

    </tbody>

</table>