@extends('tablar::page')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">

            <div class="row g-2 align-items-center">

                {{-- LEFT --}}
                <div class="col">
                    <h2>
                        Neraca Saldo
                    </h2>

                    <div class="text-muted mt-1">
                        Laporan per akun dengan saldo berjalan
                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">

                        <a href="{{ route('trial.export', request()->all()) }}"
                           class="btn btn-dark"
                           target="_blank">

                            <i class="ti ti-file-export"></i>
                            Ekspor Excel
                        </a>

                        <a href="{{ route('journals.trial.pdf', request()->all()) }}"
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
<div class="container-fluid mt-3">

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ $endDate }}">
                </div>

                <div class="col-md-2">
                    <label for="view" class="form-label">Tampilan</label>
                    <select name="view" id="view" class="form-select select2">
                        <option value="default" {{ $viewType == 'default' ? 'selected' : '' }}>Default</option>
                        <option value="skontro" {{ $viewType == 'skontro' ? 'selected' : '' }}>Skontro</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark text-white w-100">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- 🔹 Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">Kode Akun</th>
                        <th class="text-center">Nama Akun</th>
                        <th class="text-center">Debit</th>
                        <th class="text-center">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedAccounts as $category => $subs)
                        <tr class="bg-light">
                            <td colspan="4" class="text-center fw-bold">{{ strtoupper($category) }}</td>
                        </tr>
                        @foreach($subs as $subCat => $data)
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-semibold fst-italic"> {{ $subCat }}</td>
                            </tr>
                            
                            @foreach($data['accounts'] as $acc)
                                    <tr>
                                        <td class="text-center">{{ $acc['account_code'] }}</td>
                                        <td>{{ $acc['account_name'] }}</td>
                                        <td class="text-end">Rp {{ number_format($acc['debit'], 2, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($acc['credit'], 2, ',', '.') }}</td>
                                    </tr>
                            @endforeach

                            <tr class="table-secondary fw-bold">
                                <td colspan="2" class="text-end">Subtotal {{ $subCat }}</td>
                                <td class="text-end">Rp {{ number_format($data['subtotalDebit'], 2, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($data['subtotalCredit'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="2" class="text-end">Total</td>
                        <td class="text-end">Rp {{ number_format($totalDebit, 2, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($totalCredit, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            {{-- 🔹 Status keseimbangan --}}
            @if($totalDebit === $totalCredit)
                <div class="alert alert-success mt-3">
                    ✅ Neraca Saldo seimbang (Debit: Rp {{ number_format($totalDebit, 2, ',', '.') }} | 
                    Kredit: Rp {{ number_format($totalCredit, 2, ',', '.') }})
                </div>
            @else
                <div class="alert alert-warning mt-3">
                    ⚠️ Neraca Saldo tidak seimbang! Debit: Rp {{ number_format($totalDebit, 2, ',', '.') }} | 
                    Kredit: Rp {{ number_format($totalCredit, 2, ',', '.') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
 $('.select2').select2({
            placeholder: "-- Pilih --",
            width: '100%'
        });
</script>
@endpush