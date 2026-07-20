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
                {{ $offer->project->city?->name ?? 'Jember' }},
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
<table width="100%" cellspacing="0" cellpadding="6" border="1">
<thead style="background:#eee; font-weight:bold; text-align:center;">
<tr>
    <th width="4%">NO</th>
    <th>URAIAN PEKERJAAN</th>
    <th width="6%">SAT</th>
    <th width="8%">VOL</th>
    <th width="15%">HARGA SATUAN</th>
    <th width="17%">JUMLAH HARGA</th>
</tr>
</thead>

<tbody>
@php $noGroup = 'A'; @endphp

    @foreach($rab->categories as $category)
            @php
                $subtotal = $category->uraians->flatMap->items->sum('total');
            @endphp
            <tr style="font-weight:bold; background:#f5f5f5;">
                <td align="center">{{ $noGroup }}</td>
                <td colspan="4">{{ strtoupper($category->name) }}</td>

                <td align="right">
                    Rp {{ number_format($subtotal,0,',','.') }}
                </td>
            </tr>

        @php $no = 1; @endphp

        @foreach($category->uraians as $uraian)

            <tr style="font-weight:bold;">
                <td align="center">{{ $no }}</td>
                <td colspan="5">{{ $uraian->name }}</td>
            </tr>

            @php $itemNo = 1; @endphp

            @foreach($uraian->items as $item)

                <tr>
                    <td align="center">{{ $no }}.{{ $itemNo }}</td>
                    <td>{{ $item->job_name }}</td>
                    <td align="center">{{ $item->satuan }}</td>
                    <td align="right">{{ number_format($item->volume,2,',','.') }}</td>
                    <td align="right">Rp {{ number_format($item->price,0,',','.') }}</td>
                    <td align="right">Rp {{ number_format($item->total,0,',','.') }}</td>
                </tr>

                @php $itemNo++; @endphp
            @endforeach

            @php $no++; @endphp
        @endforeach

        @php $noGroup++; @endphp
    @endforeach
</tbody>
<tfoot>
<tr>
    <th colspan="5" align="right">SUBTOTAL</th>
    <th align="right">{{ number_format($rab->subtotal,0,',','.') }}</th>
</tr>
<tr>
    <th colspan="5" align="right">DISCOUNT</th>
    <th align="right">{{ number_format($rab->discount,0,',','.') }}</th>
</tr>
<tr>
    <th colspan="5" align="right">SUBTOTAL AFTER DISCOUNT</th>
    <th align="right">{{ number_format($rab->subtotal_after_discount,0,',','.') }}</th>
</tr>
<tr>
    <th colspan="5" align="right">TAX ({{ $rab->tax_rate }}%)</th>
    <th align="right">{{ number_format($rab->tax_total,0,',','.') }}</th>
</tr>
<tr>
    <th colspan="5" align="right">SHIPPING</th>
    <th align="right">{{ number_format($rab->shipping,0,',','.') }}</th>
</tr>
@php
    $dibulatkan = round($rab->grand_total, -5);
@endphp
<tr style="font-weight:bold;">
    <th colspan="5" align="right">GRAND TOTAL</th>
    <th align="right">{{ number_format($rab->grand_total,0,',','.') }}</th>
</tr>
<tr style="font-weight:bold;">
    <th colspan="5" align="right">DIBULATKAN</th>
    <th align="right">
        {{ number_format(round($rab->grand_total,-5),0,',','.') }}
    </th>
</tr>
</tfoot>

</table>

<p><strong>TERBILANG :</strong> {{ strtoupper(terbilang($dibulatkan)) }} RUPIAH</p>

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