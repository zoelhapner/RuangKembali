<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h2, h4 {
            text-align: center;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f2f2f2;
        }
        tfoot td {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Laporan Laba Rugi</h2>
    <h4>Periode: {{ $startDate }} s/d {{ $endDate }}</h4>

    <table>
        <thead>
            <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Kategori</th>
                <th>Sub Kategori</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalIncome = 0;
                $totalExpense = 0;
            @endphp
            @foreach($accounts as $acc)
                <tr>
                    <td>{{ $acc->account_code }}</td>
                    <td>{{ $acc->account_name }}</td>
                    <td>{{ $acc->category }}</td>
                    <td>{{ $acc->sub_category }}</td>
                    <td style="text-align: right;">{{ number_format($acc->balance, 2, ',', '.') }}</td>
                </tr>
                @if($acc->category === 'Pendapatan')
                    @php $totalIncome += $acc->balance; @endphp
                @else
                    @php $totalExpense += $acc->balance; @endphp
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" align="right">Total Pendapatan</td>
                <td style="text-align: right;">{{ number_format($totalIncome, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" align="right">Total Beban</td>
                <td style="text-align: right;">{{ number_format($totalExpense, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" align="right">Laba Bersih</td>
                <td style="text-align: right;">{{ number_format($totalIncome - $totalExpense, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
