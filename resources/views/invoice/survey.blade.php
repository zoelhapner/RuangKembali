<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice Rencana Survei</title>

<style>
@page {
    margin: 120px 30px 100px 30px;
}

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    line-height: 1.5;
}

/* HEADER */
.header {
    position: fixed;
    top: -100px;
    left: 0;
    right: 0;
}

table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    border: 1px solid #333;
    padding: 6px;
}
.no-border td {
    border: none;
    padding: 2px 0;
}

th {
    background: #000;
    color: #fff;
}

.text-right { text-align: right; }
.text-center { text-align: center; }
.bold { font-weight: bold; }

p {
    margin: 0 0 6px 0;
}
</style>
</head>

<body>

{{-- HEADER --}}
<div class="header">
    <img src="{{ public_path('images/header-invoice.jpg') }}" style="width:100%;">
</div>

<div style="height:20px;"></div>

{{-- INFO ATAS --}}
<table class="no-border" style="margin-top:15px;">
<tr>
    <td width="60%" valign="top">
        <table class="no-border">
            <tr><td>CP</td><td>: +62 851-8952-3863</td></tr>
            <tr><td>Email</td><td>: antosaarchitect@gmail.com</td></tr>
            <tr><td>Website</td><td>: antosaarchitect.com</td></tr>
        </table>
    </td>

    <td width="40%" valign="top" align="right">
        <table class="no-border" align="right">
            <tr>
                <td style="padding-right:10px;">Invoice No</td>
                <td><strong>{{ $invoice->invoice_number ?? $invoice->id }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>{{ $invoice->created_at->format('d M Y') }}</td>
            </tr>
        </table>
    </td>
</tr>
</table>

{{-- DATA CUSTOMER --}}
<table class="no-border" style="margin-top:15px;">
<tr>
    <td width="50%" valign="top">
        <p class="bold">Tagihan Kepada</p>
        <p>
            <strong>{{ $invoice->project->customer->user->fullname }}</strong><br>
            {{ $invoice->project->planning->survey_address }}<br>
            Telp: {{ $invoice->project->customer->user->phone ?? '-' }}
        </p>
    </td>

    <td width="50%" valign="top">
        <p class="bold">Informasi Pembayaran</p>
        <table class="no-border">
            <tr><td width="45%">Metode</td><td>: Transfer / Cash</td></tr>
            <tr><td>Nama Bank</td><td>: BCA Cabang Jember</td></tr>
            <tr><td>No. Rekening</td><td>: 0241575429</td></tr>
            <tr><td>Atas Nama</td><td>: Dwiantosa Ahmad Fathony</td></tr>
            <tr>
                <td colspan="2"><em>Mohon konfirmasi setelah pembayaran</em></td>
            </tr>
        </table>
    </td>
</tr>
</table>

<br>

{{-- TABEL BIAYA --}}
<table>
<thead>
<tr>
    <th>Deskripsi</th>
    <th class="text-right">Jumlah (Rp)</th>
</tr>
</thead>

<tbody>
<tr>
    <td>
        Biaya Rencana Survei Proyek<br>
        <strong>{{ $invoice->project->project_name }}</strong><br>
        <small>
            Lokasi: {{ $invoice->project->planning->city->name }},
            {{ $invoice->project->planning->province->name }}
        </small>
    </td>
    <td class="text-right">
        {{ number_format($invoice->amount ?? 0,0,',','.') }}
    </td>
</tr>
</tbody>

<tfoot>
<tr>
    <th class="text-right">TOTAL</th>
    <th class="text-right">
        {{ number_format($invoice->amount,0,',','.') }}
    </th>
</tr>
</tfoot>
</table>

<br>

{{-- TERBILANG --}}
<p style="margin-bottom:4px;">
    <strong>Terbilang :</strong><br>
    {{ ucwords(trim(terbilang($invoice->amount))) }} Rupiah
</p>

{{-- CATATAN --}}
<p class="bold">Catatan :</p>
<ul style="margin:4px 0 6px 18px;">
    <li>Invoice ini merupakan persetujuan biaya rencana survei.</li>
    <li>Tim survei akan dijadwalkan setelah invoice dibayarkan.</li>
</ul>

<div style="
    page-break-inside: avoid;
    page-break-before: avoid;
    margin-top:15px;
">

    <p style="margin-bottom:4px;">PT. Tosa Ahmad Jaya<br>
       <strong>Antosa Architect</strong>
    </p>

    @if($invoice->status === 'approved')
        <img src="{{ public_path('images/ttd-dwiantosa.png') }}"
             style="height:100px; margin:6px 0;">
    @else
        <br><br><br>
    @endif

    <p style="margin-top:4px;">
        <strong><u>Ir. Ar. Dwiantosa Ahmad Fathony, IAI., IPP</u></strong><br>
        Direktur Utama
    </p>

</div>

</body>
</html>
