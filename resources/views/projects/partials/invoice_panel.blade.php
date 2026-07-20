<x-collapse-card title="Invoice Termin Proyek" target="invoice-termin-body">
    <div class="card-body">
        <div class="row">
            @foreach([1,2,3,4] as $t)

                @php
                $inv = $project->invoicebuilds
                        ->where('termin',$t)
                        ->first();
                @endphp

                <div class="col-md-3 mb-3">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body text-center">

                            <h5>Termin {{ $t }}</h5>
                            @if($inv)

                                <span class="badge
                                    @if($inv->status=='approved')
                                    bg-success text-white
                                    @else
                                    bg-warning text-white
                                    @endif
                                    mb-2">

                                    {{ strtoupper($inv->status) }}

                                </span>

                                <br>

                                <a href="{{ route('projects.invoice.build',[$project->id,$t]) }}"
                                class="btn btn-dark btn-sm mb-2" target="_blank">
                                    <i class="ti ti-download"></i>
                                    Lihat Invoice
                                </a>

                                    @if(
                                        $inv->downloaded_at &&
                                        !$inv->approved_at &&
                                        (
                                            $t == 1 ||
                                            optional($project->invoicebuilds->where('termin',$t-1)->first())->approved_at
                                        )
                                    )

                                        <form action="{{ route('projects.invoice.build.approve', [$project->id,$inv->id]) }}"
                                            method="POST">
                                            @csrf
                                            <button class="btn btn-success btn-sm">
                                                Approve Termin {{ $t }}
                                            </button>
                                        </form>

                                    @endif

                            @else

                                <span class="text-muted">
                                Belum tersedia
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            @endforeach
            @php
                $justekInvoice = $project->invoicebuilds
                ->where('invoice_type','justek')
                ->first();
            @endphp
            
            @if($justekInvoice)
                <div class="col-md-3 mb-3">
                    <div class="card border-danger shadow-sm">
                        <div class="card-body text-center">
                            <h5>Invoice Justek</h5>
                            <span class="badge bg-danger text-white mb-2">
                                JUSTEK
                            </span>
                            <br>
                            <a href="{{ route('projects.invoice.build.justek',$project->id) }}"
                            class="btn btn-dark btn-sm" target="_blank">
                                <i class="ti ti-download"></i>
                                Lihat Invoice

                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-collapse-card>