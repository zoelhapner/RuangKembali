<h2 style="text-align:center">
    LAPORAN MINGGUAN PEKERJAAN
</h2>

<table width="100%" cellpadding="5">
    <tr>
        <td width="150"><b>Pekerjaan</b></td>
        <td>: {{ $project->project_name }}</td>
    </tr>

    <tr>
        <td><b>Periode</b></td>
        <td>: {{ $periode }}</td>
    </tr>
</table>

<h4>Ringkasan Kegiatan</h4>

<div style="border:1px solid #000;padding:10px">
    {!! nl2br($summary) !!}
</div>

<h4>Kemajuan Pekerjaan</h4>

<table width="100%" border="1" cellspacing="0" cellpadding="4">
    @foreach($rekap as $item)
    <tr>
        <td>{{ strtoupper($item['category']) }}</td>
        <td>{{ ($item['status']) }}</td>
    </tr>
    @endforeach
    
    <tr>
        <td width="220"><b>{{ $status_progress }}</b></td>
        <td>
            {{ number_format($deviasi,2) }}% dari rencana kerja
        </td>
    </tr>
</table>
<h4>Capaian Pekerjaan</h4>

<div style="border:1px solid #000;padding:10px">
    {!! nl2br($capaian) !!}
</div>
<h4>Kendala Pekerjaan</h4>

<div style="border:1px solid #000;padding:10px">
    {!! nl2br($kendala) !!}
</div>
<h4>Rencana Kerja</h4>

<div style="border:1px solid #000;padding:10px">
    {!! nl2br($rencana) !!}
</div>