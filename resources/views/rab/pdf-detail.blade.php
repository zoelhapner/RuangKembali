<h3 style="text-align:center; margin-bottom:15px;">
    RINCIAN RENCANA ANGGARAN BIAYA
</h3>

<table width="100%" cellspacing="0" cellpadding="6" border="1">
<thead style="background:#c4c4c4; font-weight:bold; text-align:center;">
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
            <tr style="font-weight:bold; background:#c4c4c4;">
                <td align="center">{{ $noGroup }}</td>
                <td colspan="4">{{ strtoupper($category->name) }}</td>

                <td align="right">
                    Rp {{ number_format($subtotal,0,',','.') }}
                </td>
            </tr>

        @php $no = 1; @endphp

        @foreach($category->uraians as $uraian)

            <tr style="font-size:14px;">
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
<tr style="font-weight:bold;">
    <th colspan="5" align="right">GRAND TOTAL</th>
    <th align="right">{{ number_format($rab->grand_total,0,',','.') }}</th>
</tr>
<tr style="font-weight:bold;">
    <th colspan="5" align="right">DIBULATKAN</th>
    <th align="right">
        Rp {{ number_format(floor($rab->grand_total / 100000) * 100000,0,',','.') }}
    </th>
</tr>
</tfoot>

</table>
