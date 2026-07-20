@php
$offer = $project->offer;
$rab = $offer?->rab;
function numberToLetters($num) {
    $letters = '';
    $num = $num + 1;

    while ($num > 0) {
        $rem = ($num - 1) % 26;
        $letters = chr(65 + $rem) . $letters;
        $num = intdiv(($num - 1), 26);
    }

    return $letters;
}
@endphp

@can('lihat data proyek')
@if($offer)
<div class="card shadow-sm border-0 mb-4">
    {{-- <div class="card-header fw-bold">Detail Penawaran</div> --}}

    <div class="card-body">

        <h2 class="fw-bold mb-4">{{ $offer->project->project_name }}</h2>

        <div class="row g-4">

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
                <label class="fw-semibold">Pilihan RAB</label>
                <input type="text" class="form-control" readonly
                       value="{{ $project->project_name ?? '-' }}">
            </div>
        </div>

        <h5 class="fw-bold mt-5 mb-3">Rincian Pekerjaan</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th width="50">NO</th>
                        <th>URAIAN PEKERJAAN</th>
                        <th>SAT</th>
                        <th>VOL</th>
                        <th>HARGA SATUAN</th>
                        <th>JUMAH HARGA</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($rab->categories as $category)

                    @php
                        $categoryLetter = numberToLetters($loop->index);

                        $categoryTotal = $category->uraians
                            ->flatMap(fn($u) => $u->items)
                            ->sum('total');

                        $uraianNo = 1;
                    @endphp

                    <tr class="table-secondary">
                        <th>{{ $categoryLetter }}</th>

                        <th colspan="4">
                            {{ strtoupper($category->name) }}
                        </th>

                        <th class="text-end">
                            Rp {{ number_format($categoryTotal,0,',','.') }}
                        </th>
                    </tr>

                    @foreach($category->uraians as $uraian)

                        <tr class="fw-bold">
                            <td>{{ $uraianNo }}</td>

                            <td colspan="5">
                                {{ ucwords($uraian->name) }}
                            </td>
                        </tr>

                        @php $itemNo = 1; @endphp

                        @foreach($uraian->items as $item)

                            <tr>
                                <td>
                                    {{ $uraianNo.'.'.$itemNo }}
                                </td>

                                <td>{{ $item->job_name }}</td>

                                <td>{{ $item->satuan }}</td>

                                <td>{{ number_format($item->volume,2,',','.') }}</td>

                                <td>
                                    Rp {{ number_format($item->price,0,',','.') }}
                                </td>

                                <td class="text-end">
                                    Rp {{ number_format($item->total,0,',','.') }}
                                </td>
                            </tr>

                            @php $itemNo++; @endphp

                        @endforeach

                        @php $uraianNo++; @endphp

                    @endforeach

                @endforeach

                </tbody>
                @php
                    $subtotal = $rab->categories
                        ->flatMap(fn($c) => $c->uraians)
                        ->flatMap(fn($u) => $u->items)
                        ->sum(fn($i) => $i->volume * $i->price);
                    $discount = $offer->discount ?? 0;
                    $subtotalAfterDiscount = $subtotal - $discount;
                    $taxRate = $offer->tax_rate ?? 0;
                    $totalTax = $subtotalAfterDiscount * ($taxRate / 100);
                    $shipping = $offer->shipping ?? 0;
                    $grandTotal = $subtotalAfterDiscount + $totalTax + $shipping;
                @endphp
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">SUBTOTAL</th>
                        <th>Rp {{ number_format($subtotal, 0, ',', '.') }}</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">DISCOUNT</th>
                        <th>Rp {{ number_format($discount, 0, ',', '.') }}</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">SUBTOTAL AFTER DISCOUNT</th>
                        <th>
                            Rp {{ number_format($subtotalAfterDiscount, 0, ',', '.') }}
                        </th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">TAX RATE (%)</th>
                        <th>{{ $taxRate }}%</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">TOTAL TAX</th>
                        <th>Rp {{ number_format($totalTax, 0, ',', '.') }}</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">SHIPPING / HANDLING</th>
                        <th>Rp {{ number_format($shipping, 0, ',', '.') }}</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">GRAND TOTAL</th>
                        <th class="fw-bold">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if($offer->notes)
        <div class="mt-4">
            <h5 class="fw-bold">Keterangan</h5>
            <div class="border p-3">{{ $offer->notes }}</div>
        </div>
        @endif
        <div class="d-flex align-items-center mt-2">
            @if($project->offer?->id)
                @if($project->project_type == 3)
                <a href="{{ route('projects.offers.build.pdf', $project->id) }}"
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