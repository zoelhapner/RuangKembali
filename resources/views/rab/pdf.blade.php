<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>RENCANA ANGGARAN BIAYA {{ $project->project_name }}</title>
    <style>
        @page {
            margin: 140px 30px 110px 30px;
        }
        
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

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }

        .text-end {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .group-header {
            background: #ddd;
            font-weight: bold;
        }
        .page-break { page-break-after: always; }
        .thead-dark th {
    background: #999 !important;
    color: #fff !important;
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
    {{-- ================= HALAMAN 1 ================= --}}
    @include('rab.pdf-rekap')

    <div class="page-break"></div>

    {{-- ================= HALAMAN 2 ================= --}}
    @include('rab.pdf-detail')
    </body>
</html>
{{-- <h3 style="text-align:center">RINCIAN ANGGARAN BIAYA</h3>

<table style="margin-bottom:10px">
    <tr>
        <td width="20%">Customer</td>
        <td>{{ $rab->contact_name }}</td>
        <td width="20%">Lokasi</td>
        <td>{{ $rab->job_location }}</td>
        <td>Durasi</td>
        <td>{{ $rab->job_duration }}</td>
    </tr>
</table>

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

        @foreach($grouped as $group)
        <tr style="font-weight:bold;">
            <td>{{ $noGroup }}</td>
            <td colspan="5">{{ strtoupper($group['nama']) }}</td>
        </tr>


        @php $no = 1; @endphp
        @foreach($group['items'] as $item)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $item->job_name }}</td>
            <td align="center">{{ $item->satuan }}</td>
            <td align="right">{{ number_format($item->volume,2,',','.') }}</td>
            <td align="right">
                Rp {{ number_format($item->price,0,',','.') }}
            </td>
            <td align="right">
                Rp {{ number_format($item->total,0,',','.') }}
            </td>
        </tr>
        @endforeach


        <tr style="font-weight:bold;">
            <td colspan="5" align="right">Jumlah {{ $group['nama'] }}</td>
            <td align="right">
                Rp {{ number_format($group['subtotal'],0,',','.') }}
            </td>
        </tr>

        @php $noGroup++; @endphp
        @endforeach

        </tbody>

        <tfoot>
            <tr>
                <th colspan="5" class="text-end">SUBTOTAL</th>
                <th class="text-end">{{ number_format($rab->subtotal,0,',','.') }}</th>
            </tr>
            <tr>
                <th colspan="5" class="text-end">DISCOUNT</th>
                <th class="text-end">{{ number_format($rab->discount,0,',','.') }}</th>
            </tr>
            <tr>
                <th colspan="5" class="text-end">SUBTOTAL AFTER DISCOUNT</th>
                <th class="text-end">{{ number_format($rab->subtotal_after_discount,0,',','.') }}</th>
            </tr>
            <tr>
                <th colspan="5" class="text-end">TAX ({{ $rab->tax_rate }}%)</th>
                <th class="text-end">{{ number_format($rab->tax_total,0,',','.') }}</th>
            </tr>
            <tr>
                <th colspan="5" class="text-end">SHIPPING</th>
                <th class="text-end">{{ number_format($rab->shipping,0,',','.') }}</th>
            </tr>
            <tr>
                <th colspan="5" class="text-end fw-bold">GRAND TOTAL</th>
                <th class="text-end fw-bold">{{ number_format($rab->grand_total,0,',','.') }}</th>
            </tr>
        </tfoot>
    </table> --}}