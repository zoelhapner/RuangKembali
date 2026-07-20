<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice DP</title>

<style>
@page {
    margin: 120px 30px 100px 30px;
}

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    line-height: 1.5;
}

/* HEADER & FOOTER */
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
    padding: 0px 0;
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

<table width="100%" class="no-border" style="margin-top:15px;">
<tr>
    <!-- KIRI -->
    <td width="60%" valign="top">
        <table class="no-border">
            <tr><td>CP</td><td>: +62 851-8952-3863</td></tr>
            <tr><td>Email</td><td>: antosaarchitect@gmail.com</td></tr>
            <tr><td>Website</td><td>: antosaarchitect.com</td></tr>
        </table>
    </td>

    <!-- KANAN -->
    <td width="40%" valign="top" align="right">
        <table class="no-border" align="right">
            <tr>
                <td style="padding-right:10px;">Invoice No</td>
                <td><strong>{{ $invoice->invoice_number  }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>{{ $invoice->invoice_date->format('d F Y') }}</td>
            </tr>
        </table>
    </td>
</tr>
</table>



<table width="100%" class="no-border" style="margin-top:15px;">
<tr>
    <!-- KIRI -->
    <td width="50%" valign="top">
        <p class="bold">Tagihan Kepada</p>
        <p>
            <strong>{{ $offer->contact_name }}</strong><br>
            {{ optional($project->customer->user)->address }}<br>
            Telp: {{ optional($project->customer->user)->phone }}
        </p>

    </td>

    <!-- KANAN -->
    <td width="50%" valign="top">
        <p class="bold">Informasi Pembayaran</p>
        <table class="no-border">
            <tr>
                <td width="45%">Metode pembayaran</td>
                <td>: Cash / Transfer</td>
            </tr>
            <tr>
                <td>Nama Bank</td>
                <td>: BCA Cabang Jember</td>
            </tr>
            <tr>
                <td>No. Rekening</td>
                <td>: 0241575429</td>
            </tr>
            <tr>
                <td>Atas Nama</td>
                <td>: Dwiantosa Ahmad Fathony</td>
            </tr>
            <tr>
                <td colspan="2">
                    <em>Harap mengirimkan bukti pembayaran</em>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>


<br>

{{-- TABEL --}}
<table>
<thead>
<tr>
    <th>Deskripsi</th>
    <th>%</th>
    <th>Total Harga Proyek (Rp)</th>
    <th>Total Yang Harus Dibayar (Rp)</th>
</tr>
</thead>

<tbody>
<tr>
    <td>
        Pembayaran I Proyek {{ $project->project_name }}
    </td>
    <td class="text-center">70%</td>
    <td class="text-right">{{ number_format($offer->grand_total,0,',','.') }}</td>
    <td class="text-right">{{ number_format($invoice->amount,0,',','.') }}</td>
</tr>
</tbody>

<tfoot>
    @if(isset($total_price))
    <tr>
        <th colspan="3" class="text-right">SUBTOTAL</th>
        <th class="text-right">
            {{ number_format($offer->total_price,0,',','.') }}
        </th>
    </tr>
    @endif

    <tr>
        <th colspan="3" class="text-right bold">TOTAL DP (70%)</th>
        <th class="text-right bold">
            {{ number_format($invoice->amount,0,',','.') }}
        </th>
    </tr>
</tfoot>
</table>

<br>

<p><strong>Terbilang :</strong><br>
{{ terbilang($invoice->amount) }} Rupiah
</p>

@php
    $remaining = $offer->grand_total - $invoice->amount;
@endphp

<p class="bold">Keterangan :</p>
<ul>
    <li>
        Sisa pembayaran sebesar 30%
        senilai Rp {{ number_format($remaining, 0, ',', '.') }} ({{ terbilang($remaining) }} rupiah) 
        akan ditagihkan pada pembayaran berikutnya.
    </li>
</ul>
<div style="
    page-break-inside: avoid;
    page-break-before: avoid;
    margin-top:15px;
">

    <p>PT. Tosa Ahmad Jaya<br>
       <strong>Antosa Architect</strong>
    </p>
    <div style="height:120px;">
        <img src="{{ public_path('images/ttd-dwiantosa.png') }}"
             style="height:140px;">
    </div>
    <p>
        <strong><u>Ir. Ar. Dwiantosa Ahmad Fathony, IAI., IPP</u></strong><br>
        Direktur Utama
    </p>

</div>
</body>
</html>
