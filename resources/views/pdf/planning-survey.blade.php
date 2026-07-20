<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">


<style>
@page {
    margin: 150px 40px 120px 40px;
}

/* ================= BODY ================= */
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 11px;
    line-height: 1.6;
}

h3 {
    font-size: 15px;
    letter-spacing: 0.5px;
}

h4 {
    font-size: 12px;
    margin-bottom: 6px;
}
.table-info {
    width: 100%;
    border-collapse: collapse;
}

.table-info td {
    border: 1px solid #000;
    padding: 6px;
    vertical-align: top;
}

.table-info .label {
    width: 30%;
    font-weight: bold;
}

.header {
    position: fixed;
    top: -140px;
    left: 0;
    right: 0;
    width: 100%;
}

.footer {
    position: fixed;
    bottom: -100px;
    left: 0;
    right: 0;
    width: 100%;
}
</style>
</head>

<body>
<div class="header">
    <img src="{{ public_path('images/header-penawaran.jpg') }}" style="width:100%;">
</div>

<div class="footer">
    <img src="{{ public_path('images/footer-penawaran.jpg') }}" style="width:100%;">
</div>

<h3 style="
    text-align:center;
    margin-bottom:25px;
    text-transform:uppercase;
    border-bottom:2px solid #000;
    padding-bottom:6px;
">
    RENCANA SURVEI LAPANGAN
</h3>

<table class="table-info">
                <tr>
                    <td width="30%">Nomor</td>
                    <td> {{ $offer_number }}</td>
                </tr>
    <tr>
        <td class="label">Nama Proyek</td>
        <td>{{ $project->project_name }}</td>
    </tr>
    <tr>
        <td class="label">Tanggal Survei</td>
        <td>{{ \Carbon\Carbon::parse($planning->planning_date)->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td class="label">Waktu Survei</td>
        <td>{{ $planning->planning_time }}</td>
    </tr>
    <tr>
        <td class="label">Petugas Survei</td>
        <td>
            @foreach($planningEmployees as $emp)
                {{ $emp->display_name }}@if(!$loop->last), @endif
            @endforeach
        </td>
    </tr>
</table>


<h4 style="margin-top:20px;">Alamat Survei</h4>

<table class="table-info">
    <tr>
        <td colspan="2">{{ $planning->survey_address }}</td>
    </tr>
    <tr>
        <td class="label">Provinsi</td>
        <td>{{ $planning->province->name ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Kab/Kota</td>
        <td>{{ $planning->city->name ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Kecamatan</td>
        <td>{{ $planning->district->name ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Kelurahan</td>
        <td>{{ $planning->subDistrict->name ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Kode Pos</td>
        <td>{{ $planning->postalCode->postal_code ?? '-' }}</td>
    </tr>
</table>


<h4 style="margin-top:20px;">Biaya Survei</h4>

<table class="table-info">
    <tr>
        <td class="label">Total Biaya</td>
        <td>
            @if($invoice->amount > 0)
                Rp {{ number_format($invoice->amount,0,',','.') }}
            @else
                <strong>GRATIS</strong>
            @endif
        </td>
    </tr>
</table>

@if($planning->planning_notes)
<h4 style="margin-top:20px;">Catatan</h4>
<p>{{ $planning->planning_notes }}</p>
@endif
<table width="100%" style="margin-top:50px;">
    <tr>
<td width="50%" style="text-align:center; vertical-align:top;">
    <p>Disusun oleh,</p>

    <div style="height:120px;">
        @if($invoice->status === 'approved')
            <img src="{{ public_path('images/ttd-dwiantosa.png') }}"
                 style="height:140px;">
        @endif
    </div>

    <strong><u>
        Ir. Ar. Dwiantosa Ahmad Fathony, IAI., IPP
    </strong></u>
</td>
        <td width="50%" style="text-align:center; vertical-align:top;">
            <p>Disetujui oleh Customer,</p>

            <div style="height:120px;">
                {{-- @if($invoice->status === 'approved')
                    <strong><u>{{ $invoice->approve_by_name }}</u></strong>
                @endif --}}
            </div>
            <strong><u>
                {{ $project->customer->user->fullname ?? '................' }}
            </u></strong>

        </td>


    </tr>
</table>
@if($invoice->status === 'waiting_approval')

<p style="text-align:center; font-size:12px; margin-top:20px;">
Silakan menyetujui atau menolak rencana survei melalui tautan berikut:
</p>

<p style="text-align:center;">
    <a href="{{ route('survey.invoice.approve', [$invoice->id, $invoice->approval_token]) }}">
        SETUJUI RENCANA SURVEI
    </a>
    |
    <a href="{{ route('survey.invoice.reject.form', [$invoice->id, $invoice->approval_token]) }}">
        TOLAK RENCANA SURVEI
    </a>
</p>
@endif

</body>
</html>