@extends('tablar::page')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col d-flex align-items-center">
                <a href="{{ route('journals.index') }}" class="btn btn-dark d-flex align-items-center">
                    <i class="ti ti-arrow-left"></i>
                </a>      
                    <h2 class="page-title mb-0">Jurnal</h2> 
            </div>
        </div>
    </div>
</div>


<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @php
                            $ext = $journal->enclosure 
                                ? strtolower(pathinfo($journal->enclosure, PATHINFO_EXTENSION)) 
                                : null;
                        @endphp
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        {{-- HEADER INFO --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h3 class="mb-0 fw-bold">Detail Jurnal</h3>
                                <div class="text-muted small">Informasi transaksi</div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('journals.export', $journal->id) }}" class="btn btn-dark">
                                    <i class="ti ti-file-export"></i> Ekspor
                                </a>
                                <a href="{{ route('journals.print', $journal->id) }}" target="_blank" class="btn btn-outline-dark">
                                    <i class="ti ti-printer"></i> Cetak
                                </a>
                            </div>
                        </div>

                        {{-- INFO BOX --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="text-muted small">No. Transaksi</div>
                                        <div class="fw-bold">{{ $journal->journal_code }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="text-muted small">Tanggal</div>
                                        <div class="fw-bold">
                                            {{ \Carbon\Carbon::parse($journal->transaction_date)->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-hover align-middle jurnal-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode Akun</th>
                                        <th>Nama Akun</th>
                                        <th>Deskripsi</th>
                                        <th>User</th>
                                        <th class="text-end">Debit</th>
                                        <th class="text-end">Kredit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalDebit = 0;
                                        $totalCredit = 0;
                                    @endphp

                                    @foreach($journal->details as $detail)
                                        @php
                                            $totalDebit += $detail->debit;
                                            $totalCredit += $detail->credit;
                                        @endphp

                                        <tr>
                                            <td>{{ $detail->account->account_code ?? '-' }}</td>
                                            <td title="{{ $detail->account->account_name ?? '-' }}">
                                                {{ Str::limit($detail->account->account_name ?? '-', 45) }}
                                            </td>

                                            <td title="{{ $detail->description ?? '-' }}">
                                                {{ Str::limit($detail->description ?? '-', 35) }}
                                            </td>
                                            <td>{{ $detail->person_name ?? '-' }}</td>
                                            <td class="text-end text-success">
                                                Rp {{ number_format($detail->debit, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end text-danger">
                                                Rp {{ number_format($detail->credit, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot class="fw-bold border-top">
                                    <tr>
                                        <td colspan="4">Total</td>
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
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Keterangan</label>
                            <div class="border rounded p-3 bg-light">
                                {{ $journal->description ?? '-' }}
                            </div>
                        </div>

                        {{-- LAMPIRAN (FULL WIDTH DI BAWAH) --}}
                        @if ($journal->enclosure)
                            <div class="mb-4">
                                <label class="form-label fw-bold">Lampiran Jurnal</label>

                                <div class="card shadow-sm">
                                    <div class="card-body text-center">

                                        @if(in_array($ext, ['jpg','jpeg','png','gif']))
                                            <img src="{{ asset('storage/' . $journal->enclosure) }}"
                                                class="img-fluid rounded cursor-pointer"
                                                style="max-height: 250px; cursor:pointer;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#previewModal">

                                        @elseif($ext === 'pdf')
                                            <div class="text-center">
                                                <i class="ti ti-file-type-pdf fs-1 text-danger"></i>
                                                <p class="mb-2">Preview PDF</p>

                                                <button class="btn btn-dark btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#previewModal">
                                                    Lihat PDF
                                                </button>
                                            </div>
                                        
                                        {{-- @elseif($ext === 'pdf')
                                            <embed src="{{ asset('storage/' . $journal->enclosure) }}"
                                                type="application/pdf"
                                                width="100%"
                                                height="500px"
                                                class="rounded border"/>

                                            <div class="mt-3">
                                                <a href="{{ asset('storage/' . $journal->enclosure) }}"
                                                target="_blank"
                                                class="btn btn-dark btn-sm">
                                                    Buka PDF
                                                </a>
                                            </div> --}}

                                        @else
                                            <a href="{{ asset('storage/' . $journal->enclosure) }}"
                                            target="_blank"
                                            class="btn btn-secondary">
                                                Download Lampiran
                                            </a>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ACTION --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('journals.edit', $journal->id) }}" class="btn btn-dark">
                                <i class="ti ti-pencil"></i> Ubah
                            </a>
                        </div>

                        {{-- FOOTER --}}
                        <div class="text-end text-muted small mt-4">
                            Terakhir diubah oleh 
                            <strong>{{ $journal->creator->fullname ?? 'Sistem' }}</strong> 
                            pada {{ \Carbon\Carbon::parse($journal->updated_at)->translatedFormat('d F Y H:i') }} GMT+7
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if ($journal->enclosure)
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Preview Lampiran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">

                @if(in_array($ext, ['jpg','jpeg','png','gif']))
                    <img src="{{ asset('storage/' . $journal->enclosure) }}"
                        class="img-fluid rounded">

                @elseif($ext === 'pdf')
                    <embed src="{{ asset('storage/' . $journal->enclosure) }}"
                        type="application/pdf"
                        width="100%"
                        height="600px">
                @endif

            </div>

        </div>
    </div>
</div>
@endif
@endsection