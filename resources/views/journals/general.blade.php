@extends('tablar::page')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">

            <div class="row g-2 align-items-center">

                {{-- LEFT --}}
                <div class="col">
                    <h2>
                        Jurnal Umum
                    </h2>

                    <div class="text-muted mt-1">
                        Daftar transaksi jurnal perusahaan
                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">

                        <a href="{{ route('general.export', request()->all()) }}"
                           class="btn btn-primary"
                           target="_blank">

                            <i class="ti ti-file-export"></i>
                            Ekspor
                        </a>

                        <a href="{{ route('journals.export.pdf', request()->all()) }}"
                           class="btn btn-outline-dark"
                           target="_blank">

                            <i class="ti ti-printer"></i>
                            Cetak
                        </a>

                    </div>
                </div>

            </div>

        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('journals.general') }}" class="row g-3 align-items-end">
                        
                        <div class="col-md-3">
                            <label class="form-label">Dari</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Sampai</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary text-black w-100">
                                <i class="ti ti-filter"></i> Filter
                            </button>
                        </div>

                    </form>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle gen-table">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>No Jurnal</th>
                                <th>Deskripsi</th>
                                <th>No. Akun</th>
                                <th>Nama Akun</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Kredit</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($journals as $journal)
                                @php $rowCount = $journal->details->count(); @endphp

                                @foreach ($journal->details as $i => $detail)
                                    <tr>

                                        @if($i == 0)
                                            <td rowspan="{{ $rowCount }}" class="align-top text-muted small">
                                                {{ \Carbon\Carbon::parse($journal->transaction_date)->format('d M Y') }}
                                            </td>

                                            <td rowspan="{{ $rowCount }}" class="align-top">
                                                <a href="{{ route('journals.show', $journal->id) }}" 
                                                    class="fw-bold text-primary text-decoration-none">
                                                    {{ $journal->journal_code }}
                                                </a>
                                            </td>
                                        @endif

                                        <td title="{{ $detail->description ?? '-' }}">
                                            {{ Str::limit($detail->description ?? '-', 35) }}
                                        </td>
                                        <td>{{ $detail->account->account_code }}</td>
                                        <td title="{{ $detail->account->account_name ?? '-' }}">
                                            {{ Str::limit($detail->account->account_name ?? '-', 40) }}
                                        </td>

                                        <td class="text-end text-success fw-semibold">
                                            Rp {{ number_format($detail->debit, 0, ',', '.') }}
                                        </td>

                                        <td class="text-end text-danger fw-semibold">
                                            Rp {{ number_format($detail->credit, 0, ',', '.') }}
                                        </td>

                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>

                        <tfoot class="border-top fw-bold bg-light">
                            <tr>
                                <td colspan="5">Total</td>
                                <td class="text-end text-success">
                                    Rp {{ number_format($totalDebit, 0, ',', '.') }}
                                </td>
                                <td class="text-end text-danger">
                                    Rp {{ number_format($totalCredit, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection