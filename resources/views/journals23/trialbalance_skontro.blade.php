@extends('tablar::page')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">

        <div class="row g-2 align-items-center">

            {{-- LEFT --}}
            <div class="col">
                <h2>
                    Neraca
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

    {{-- 🔹 Filter --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-center">
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
                
                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-dark text-white">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- 🔹 Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">

            <div class="row">
                
                <div class="col-md-6">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-dark text-white">
                            <strong>AKTIVA</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td>Aset Lancar</td>
                                        <td class="text-end">Rp {{ number_format($asetLancar, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Persediaan Barang</td>
                                        <td class="text-end">Rp {{ number_format($persediaan, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Piutang</td>
                                        <td class="text-end">Rp {{ number_format($piutang, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Dana Belum Disetor</td>
                                        <td class="text-end">Rp {{ number_format($dana, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pajak Bayar Dimuka</td>
                                        <td class="text-end">Rp {{ number_format($pajak, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Aset Tetap</td>
                                        <td class="text-end">Rp {{ number_format($asetTetap, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Akumulasi Penyusutan</td>
                                        <td class="text-end text-danger">Rp ({{ number_format($penyusutan, 2, ',', '.') }})</td>
                                    </tr>
                                    
                                    <tr class="fw-bold table-secondary">
                                        <td>Total Aktiva</td>
                                        <td class="text-end">Rp {{ number_format($totalAktiva, 2, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-secondary text-white">
                            <strong>PASSIVA</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td>Kewajiban</td>
                                        <td class="text-end">Rp {{ number_format($kewajiban, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Ekuitas</td>
                                        <td class="text-end">Rp {{ number_format($ekuitas, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                    <td>Laba (Rugi) Berjalan</td>
                                        <td class="text-end">
                                            Rp {{ number_format($labaBerjalan,2,',','.') }}
                                        </td>
                                    </tr>
                                    
                                    <tr class="fw-bold table-secondary">
                                        <td>Total Passiva</td>
                                        <td class="text-end">Rp {{ number_format($totalPassiva, 2, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

                {{-- <div class="d-flex justify-content-start gap-2 mt-3">
                    <a href="{{ route('journals.trial.pdf', [
                            'start_date' => request('start_date'),
                            'end_date' => request('end_date'),
                            'license_id' => request('license_id')
                        ]) }}" 
                        class="btn btn-danger" target="_blank">
                        <i class="ti ti-printer"></i>Cetak
                    </a>
                </div> --}}
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