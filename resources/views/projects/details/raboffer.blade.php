@php
    $offer = $project->offer;
    $groupedItems = $offer?->items?->groupBy('category') ?? collect();
@endphp

@can('lihat data proyek')
@if($offer)
<div class="card shadow-sm border-0 mb-4">

    <div class="card-body">

        <div class="row g-4">

            {{-- Informasi utama --}}
            <div class="col-md-4">
                <label class="fw-semibold">Nomor Penawaran</label>
                <input type="text" class="form-control" readonly
                       value="{{ $offer->offer_number }}">
            </div>

            <div class="col-md-4">
                <label class="fw-semibold">Tanggal Penawaran</label>
                <input type="text" class="form-control" readonly
                       value="{{ \Carbon\Carbon::parse($offer->offer_date)->format('d/m/Y') }}">
            </div>

            <div class="col-md-4">
                <label class="fw-semibold">Nama Customer</label>
                <input type="text" class="form-control" readonly
                       value="{{ $offer->contact_name }}">
            </div>

        </div>

        <div class="row mt-4 g-4">

            <div class="col-md-4">
                <label class="fw-semibold">Paket RAB</label>
                <input type="text" class="form-control" readonly
                       value="{{ $offer->rabpackage->name ?? '-' }}">
            </div>

            <div class="col-md-2">
                <label class="fw-semibold">Volume</label>
                <input type="text" class="form-control" readonly value="{{ $offer->volume }}">
            </div>

            <div class="col-md-2">
                <label class="fw-semibold">Satuan</label>
                <input type="text" class="form-control" readonly value="{{ $offer->satuan }}">
            </div>

            <div class="col-md-2">
                <label class="fw-semibold">Harga Satuan</label>
                <input type="text" class="form-control" disabled
                       value="Rp {{ number_format($offer->price_meter, 0, ',', '.') }}">
            </div>

            <div class="col-md-2">
                <label class="fw-semibold">Total Harga</label>
                <input type="text" class="form-control" disabled
                       value="Rp {{ number_format($offer->total_price, 0, ',', '.') }}">
            </div>
        </div>

        {{-- ======================= --}}
        {{--        Rincian Item      --}}
        {{-- ======================= --}}

        <h5 class="fw-bold mt-5 mb-3">Rincian Pekerjaan</h5>

        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Uraian Pekerjaan</th>
                    <th>Volume</th>
                    <th>Satuan</th>
                    <th>Harga Satuan (Rp)</th>
                    <th>Total Harga</th>
                </tr>
            </thead>

                <tbody>

                    @forelse($groupedItems as $category => $items)

                        {{-- BARIS KATEGORI --}}
                        <tr class="table-secondary">
                            <td> {{ $loop->iteration }}</td>
                            <td colspan="4" class="fw-bold">
                                {{ $category ?: 'Tanpa Kategori' }}
                            </td>
                            <td></td>
                        </tr>

                        {{-- ITEM DI DALAM KATEGORI --}}
                        @foreach($items as $item)
                            <tr>
                                <td></td>
                                <td>{{ $item->item_name }}</td>
                                <td></td><td></td><td></td><td></td>
                            </tr>
                        @endforeach

                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                Tidak ada rincian pekerjaan.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">SUBTOTAL</th>
                    <th>Rp {{ number_format($offer->total_price, 0, ',', '.') }}</th>
                </tr>

                <tr>
                    <th colspan="5" class="text-end">DISCOUNT</th>
                    <th>Rp {{ number_format($offer->discount, 0, ',', '.') }}</th>
                </tr>

                <tr>
                    <th colspan="5" class="text-end">SUBTOTAL AFTER DISCOUNT</th>
                    <th>
                        Rp {{ number_format($offer->total_price - $offer->discount, 0, ',', '.') }}
                    </th>
                </tr>

                <tr>
                    <th colspan="5" class="text-end">TAX RATE (%)</th>
                    <th>{{ $offer->tax_rate }}%</th>
                </tr>

                <tr>
                    <th colspan="5" class="text-end">TOTAL TAX</th>
                    <th>Rp {{ number_format($offer->total_tax, 0, ',', '.') }}</th>
                </tr>

                <tr>
                    <th colspan="5" class="text-end">SHIPPING / HANDLING</th>
                    <th>Rp {{ number_format($offer->shipping, 0, ',', '.') }}</th>
                </tr>

                <tr>
                    <th colspan="5" class="text-end">GRAND TOTAL</th>
                    <th class="fw-bold">
                        Rp {{ number_format($offer->grand_total, 0, ',', '.') }}
                    </th>
                </tr>
            </tfoot>

        </table>

        {{-- Notes --}}
        @if($offer->notes)
        <div class="mt-4">
            <h5 class="fw-bold">Keterangan</h5>
            <div class="border p-3">{{ $offer->notes }}</div>
        </div>
        @endif
        <div class="d-flex align-items-center gap-2">
            @if($project->offer?->id)
                @if($project->project_type == 2)
                <a href="{{ route('projects.offers.rab.pdf', $project->offer->id) }}"
                    class="btn btn-dark"
                    target="_blank"
                    title="Download PDF">
                        <i class="ti ti-file-text"></i>Download PDF
                </a>
                @endif
            @endif
        </div>
    </div>
</div>
@endif
@endcan