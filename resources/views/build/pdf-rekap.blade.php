<h1>
    REKAPITULASI BOBOT KEMAJUAN
    PEKERJAAN MINGGUAN
</h1>

<table width="100%" style="border:none; margin-bottom:15px;">
    <tr>

        {{-- KOLOM KIRI --}}
        <td width="60%" valign="top" style="border:none;">

            <table style="border:none;" width="100%">

                <tr>
                    <td style="border:none;width:40%">
                        Pemilik Pekerjaan
                    </td>
                    <td style="border:none">
                        : {{ $project->customer->display_name }}
                    </td>
                </tr>

                <tr>
                    <td style="border:none">
                        Pelaksana Pekerjaan
                    </td>
                    <td style="border:none">
                        : Antosa Architect
                    </td>
                </tr>

                <tr>
                    <td style="border:none">
                        Tahun
                    </td>
                    <td style="border:none">
                        : {{ \Carbon\Carbon::parse($project->start_date)->format('Y') }}
                    </td>
                </tr>

                <tr>
                    <td style="border:none">
                        Lokasi
                    </td>
                    <td style="border:none">
                        : {{ $project->project_location }}
                    </td>
                </tr>

                <tr>
                    <td style="border:none">
                        Waktu Pelaksanaan
                    </td>
                    <td style="border:none">
                        : {{ $project->job_duration ?? '-' }} Hari Kerja
                    </td>
                </tr>

            </table>

        </td>

        {{-- KOLOM KANAN --}}
        <td width="40%" valign="top" style="border:none;">

            <table width="100%" style="border:none;">

                <tr>
                    <td colspan="2"
                        style="border:none;
                        text-align:right;
                        padding-bottom:10px;">
                        PRESTASI PEKERJAAN MINGGU INI
                    </td>
                </tr>

                <tr>
                    <td style="border:none">
                        Minggu Ke : 
                    </td>
                    <td style="border:none;text-align:right">
                        {{ $weekNow }}
                    </td>
                </tr>

                <tr>
                    <td style="border:none">
                        Schedule Kerja : 
                    </td>
                    <td style="border:none;text-align:right">
                        {{ number_format($rencanaKumulatif,2) }}%
                    </td>
                </tr>

                <tr>
                    <td style="border:none">
                        Realisasi Fisik : 
                    </td>
                    <td style="border:none;text-align:right">
                        {{ number_format($realisasiKumulatif,2) }}%
                    </td>
                </tr>

                <tr>
                    <td style="border:none">
                        CEPAT (+) / LAMBAT (-) : 
                    </td>
                    <td style="border:none;text-align:right">
                        {{ number_format($deviasi,2) }}%
                    </td>
                </tr>

            </table>

        </td>

    </tr>
</table>
<table>

    <thead>

        <tr style="background:#e5e5e5; text-align:center;">
            <th rowspan="3" width="4%">
                NO
            </th>
            <th rowspan="3">
                Jenis Pekerjaan
            </th>
            <th rowspan="3" width="7%">
                Bobot (%)
            </th>
            <th rowspan="3" width="8%">
                Rencana s/d Minggu ini (%)
            </th>

            <th colspan="6">
                Realisasi
            </th>

            <th rowspan="3" width="8%">
                Deviasi (%)
            </th>

        </tr>

        <tr style="background:#f2f2f2; text-align:center; font-weight:bold;">

            <th colspan="2">
                s/d Minggu Lalu
            </th>

            <th colspan="2">
                Minggu ini
            </th>

            <th colspan="2">
                s/d Minggu ini
            </th>

        </tr>

        <tr style="background:#f9f9f9; text-align:center; font-weight:bold;">

            <th width="7%">
                Prestasi (%)
            </th>

            <th width="7%">
                Bobot (%)
            </th>

            <th width="7%">
                Prestasi (%)
            </th>

            <th width="7%">
                Bobot (%)
            </th>

            <th width="7%">
                Prestasi (%)
            </th>

            <th width="7%">
                Bobot (%)
            </th>

        </tr>

    </thead>


<tbody>

@php
$totalBobot = 0;
$totalRencana = 0;

$totalPrestasiLalu = 0;
$totalBobotLalu = 0;

$totalPrestasiMingguIni = 0;
$totalBobotMingguIni = 0;

$totalPrestasiSdMingguIni = 0;
$totalRealisasiSdMingguIni = 0;
@endphp

@foreach($rekap as $r)

@php

$deviasi = $r['realisasi_sd_minggu_ini'] - $r['rencana'];

$totalBobot += $r['bobot'];
$totalRencana += $r['rencana'];

$totalPrestasiLalu += $r['prestasi_lalu'];
$totalBobotLalu += $r['bobot_lalu'];

$totalPrestasiMingguIni += $r['prestasi_minggu_ini'];
$totalBobotMingguIni += $r['bobot_minggu_ini'];

$totalPrestasiSdMingguIni += $r['prestasi_sd_minggu_ini'];
$totalRealisasiSdMingguIni += $r['realisasi_sd_minggu_ini'];

@endphp

<tr>
    <td align="center">
    {{ \PhpOffice\PhpSpreadsheet\Cell\Coordinate
        ::stringFromColumnIndex($loop->iteration) }}
    </td>
    {{-- JENIS --}}
    <td>
        {{ strtoupper($r['category']) }}
    </td>

    {{-- BOBOT KONTRAK --}}
    <td class="text-end">
        {{ number_format($r['bobot'],2) }}
    </td>

    {{-- RENCANA KUMULATIF --}}
    <td class="text-end">
        {{ number_format($r['rencana'],2) }}
    </td>

    {{-- REALISASI SD MINGGU LALU --}}
    <td class="text-end">
        {{ number_format($r['prestasi_lalu'],2) }}
    </td>

    <td class="text-end">
        {{ number_format($r['bobot_lalu'],2) }}
    </td>

    {{-- REALISASI MINGGU INI --}}
    <td class="text-end">
        {{ number_format($r['prestasi_minggu_ini'],2) }}
    </td>

    <td class="text-end">
        {{ number_format($r['bobot_minggu_ini'],2) }}
    </td>

    {{-- REALISASI SD MINGGU INI --}}
    <td class="text-end">
        {{ number_format($r['prestasi_sd_minggu_ini'],2) }}
    </td>

    <td class="text-end">
        {{ number_format($r['realisasi_sd_minggu_ini'],2) }}
    </td>

    {{-- DEVIASI --}}
    <td class="text-end">
        {{ number_format($deviasi,2) }}
    </td>

</tr>

@endforeach

</tbody>

    {{-- <tfoot>

        <tr>

            <th colspan="2">
            </th>

            <th class="text-end">
                {{ number_format($totalBobot,2) }}
            </th>

            <th class="text-end">
                {{ number_format($totalRencana,2) }}
            </th>

            <th colspan="4"></th>

            <th></th>

            <th class="text-end">
                {{ number_format($totalRealisasi,2) }}
            </th>

            <th class="text-end">
                {{ number_format(
                    $totalRealisasi - $totalRencana,
                    2
                ) }}
            </th>

        </tr>

    </tfoot> --}}
    <tfoot>

        <tr>

            <th colspan="2">
            </th>

            <th class="text-end">
                {{ number_format($totalBobot,2) }}
            </th>

            <th class="text-end">
                {{ number_format($totalRencana,2) }}
            </th>

            <th class="text-end">
                {{-- {{ number_format($totalPrestasiLalu,2) }} --}}
            </th>

            <th class="text-end">
                {{ number_format($totalBobotLalu,2) }}
            </th>

            <th class="text-end">
                {{-- {{ number_format($totalPrestasiMingguIni,2) }} --}}
            </th>

            <th class="text-end">
                {{ number_format($totalBobotMingguIni,2) }}
            </th>

            <th class="text-end">
                {{-- {{ number_format($totalPrestasiSdMingguIni,2) }} --}}
            </th>

            <th class="text-end">
                {{ number_format($totalRealisasiSdMingguIni,2) }}
            </th>

            <th class="text-end">
                {{ number_format(
                    $totalRealisasiSdMingguIni - $totalRencana,
                    2
                ) }}
            </th>

        </tr>

    </tfoot>

</table>