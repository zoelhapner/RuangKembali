<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Penawaran {{ $offer->offer_number }}</title>

<style>
/* ================= PAGE ================= */
@page {
    margin: 140px 30px 110px 30px;
}

/* ================= BODY ================= */
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    line-height: 1.5;
    margin: 0;
    padding: 0;
}

/* ================= HEADER & FOOTER ================= */
.header {
    position: fixed;
    top: -110px;
    left: 0;
    right: 0;
    width: 100%;
}

.footer {
    position: fixed;
    bottom: -70px;
    left: 0;
    right: 0;
    width: 100%;
}

/* ================= TABLE HANDLING ================= */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    page-break-inside: auto;
}

thead {
    display: table-header-group; /* penting */
}

tfoot {
    display: table-footer-group;
}

tr {
    page-break-inside: auto;
}

.no-break {
    page-break-inside: avoid; /* hanya untuk kategori */
}

th, td {
    padding: 6px;
    border: 1px solid #444;
}

th {
    background: #efefef;
}

/* ================= UTIL ================= */
.no-border td {
    border: none !important;
    padding: 4px 0;
}

.text-right { text-align: right; }
.bold { font-weight: bold; }

p { margin: 10px 0; }
.closing-block {
    page-break-inside: avoid;
    margin-top: 20px;
}

.signature {
    margin-top: 10px;
}

.signature img {
    height: 140px;
}

</style>
</head>

<body>

<!-- ================= HEADER ================= -->
<div class="header">
    <img src="{{ public_path('images/header-penawaran.jpg') }}" style="width:100%;">
</div>

<!-- ================= FOOTER ================= -->
<div class="footer">
    <img src="{{ public_path('images/footer-penawaran.jpg') }}" style="width:100%;">
</div>

<!-- ================= KONTEN ================= -->
{{-- <div style="height:60px;"></div> --}}

<table class="no-border" width="100%">
    <tr>
        <!-- KIRI -->
        <td width="60%">
            <table class="no-border">
                <tr>
                    <td width="30%">Nomor</td>
                    <td>: {{ $offer->offer_number }}</td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>: -</td>
                </tr>
                <tr>
                    <td>Perihal</td>
                    <td>: Penawaran Harga</td>
                </tr>
            </table>
        </td>

        <!-- KANAN -->
        <td width="40%" style="vertical-align: top; text-align: right;">
            <strong>
                {{ $offer->project->city->name ?? 'Jember' }},
                {{ \Carbon\Carbon::parse($offer->offer_date)->translatedFormat('d F Y') }}
            </strong>
        </td>
    </tr>
</table>

<br>

<p>Kepada Yth.</p>
<p><strong>{{ $offer->contact_name }}</strong></p>
<p>{{ $offer->project->project_location ?? '-' }}</p>
<br>
<p>Dengan hormat,</p>
<p>
Sebagai tindak lanjut dari hasil diskusi pada tanggal
{{ \Carbon\Carbon::parse($offer->offer_date)->subDays(2)->translatedFormat('d F Y') }},
berikut kami sampaikan penawaran harga untuk pelaksanaan pekerjaan:
</p>

<table class="no-border">
<tr><td width="25%">Jenis Pekerjaan</td><td>: {{ $offer->project->project_name ?? '-' }}</td></tr>
<tr><td>Lokasi</td><td>: {{ $offer->project->project_location ?? '-' }}</td></tr>
</table>

<p><strong>Berikut rincian harga yang kami tawarkan:</strong></p>

<!-- ================= TABEL RINCIAN ================= -->
<table>
<thead>
<tr>
<th>NO</th>
<th>URAIAN PEKERJAAN</th>
<th>VOLUME</th>
<th>SATUAN</th>
<th>HARGA SATUAN</th>
<th>TOTAL</th>
</tr>
</thead>

<tbody>
<tr>
<td class="bold">A</td>
<td class="bold">{{ $offer->rabpackage->name }}</td>
<td>{{ $offer->volume }}</td>
<td>{{ $offer->satuan }}</td>
<td class="text-right">{{ number_format($offer->price_meter,0,',','.') }}</td>
<td class="text-right">{{ number_format($offer->total_price,0,',','.') }}</td>
</tr>

@php $row = 1; @endphp
@foreach($offer->groupedItems() as $category => $items)

<tr class="no-break" style="background:#eee;">
<td class="bold">{{ $row++ }}</td>
<td class="bold">{{ $category }}</td>
<td></td><td></td><td></td><td></td>
</tr>

@foreach($items as $item)
<tr>
<td></td>
<td>- {{ $item->item_name }}</td>
<td>{{ $item->volume }}</td>
<td>{{ $item->satuan }}</td>
<td></td>
<td></td>
</tr>
@endforeach

@endforeach
</tbody>

<tfoot>
<tr>
<th colspan="5" class="text-right">GRAND TOTAL</th>
<th class="text-right bold">{{ number_format($offer->grand_total,0,',','.') }}</th>
</tr>
</tfoot>
</table>

<p><strong>TERBILANG :</strong> {{ strtoupper(terbilang($offer->grand_total)) }} RUPIAH</p>

<div class="closing-block">
    <h4>Keterangan:</h4>
    <ol>
    <li>Penawaran berlaku 7 hari.</li>
    <li>Estimasi pengerjaan 10–20 hari.</li>
    </ol>

    <p>Demikian penawaran harga kami sampaikan. Atas perhatiannya kami ucapkan terima kasih.</p>

    <div class="signature">
        <p>Hormat Kami,</p>

        <p>
            <strong>PT. Tosa Ahmad Jaya</strong><br>
            <strong>Antosa Architect</strong>
        </p>

        <img src="{{ public_path('images/ttd-dwiantosa.png') }}">

        <p>
            <strong><u>Ir. Ar. Dwiantosa Ahmad Fathony, IAI., IPP</u></strong><br>
            Direktur Utama
        </p>
    </div>
</div>

</body>
</html>