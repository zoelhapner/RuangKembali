@extends('tablar::page')

@section('content')
<div class="container-xl">

    <div class="d-flex justify-content-between mb-3">
        <h2>Stock Adjustment - {{ $warehouse->name }}</h2>
        <a href="{{ route('warehouses.adjustments.create', $warehouse->id) }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Buat Penyesuaian
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table card-table table-vcenter">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($adjustments as $adj)
                    <tr>
                        <td>#{{ $adj->id }}</td>
                        <td>{{ $adj->reason }}</td>
                        <td>
                            <span class="badge {{ $adj->status === 'draft' ? 'bg-warning' : 'bg-success' }}">
                                {{ ucfirst($adj->status) }}
                            </span>
                        </td>
                        <td>{{ $adj->created_at->format('d M Y') }}</td>
                        <td>
                            @if($adj->status === 'draft')
                                <form action="{{ route('warehouses.adjustments.finalize', [$warehouse->id, $adj->id]) }}"
                                      method="POST">
                                    @csrf
                                    <button class="btn btn-success btn-sm">
                                        <i class="ti ti-check"></i> Finalize
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection
