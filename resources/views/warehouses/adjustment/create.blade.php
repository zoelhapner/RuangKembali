@extends('tablar::page')

@section('content')
<div class="container-xl">

    <h2 class="mb-3">Buat Stock Adjustment</h2>

    <form action="{{ route('warehouses.adjustments.store', $warehouse->id) }}" method="POST">
        @csrf

        <div class="card mb-3">
            <div class="card-body">
                <label class="form-label">Alasan</label>
                <input type="text" name="reason" class="form-control" placeholder="Contoh: Stock opname" required>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">

                <table class="table card-table table-vcenter mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Stok Saat Ini</th>
                            <th>Stok Baru</th>
                            <th>Selisih</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($stocks as $s)
                            <tr>
                                <td>
                                    {{ $s->product->name }}
                                    <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $s->product->id }}">
                                </td>

                                <td>
                                    {{ $s->stock }}
                                    <input type="hidden" name="items[{{ $loop->index }}][current_stock]" value="{{ $s->stock }}">
                                </td>

                                <td>
                                    <input type="number"
                                           name="items[{{ $loop->index }}][adjusted_stock]"
                                           class="form-control"
                                           value="{{ $s->stock }}">
                                </td>

                                <td class="text-muted difference-cell">0</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>

        <button class="btn btn-primary mt-3">Simpan Draft</button>
    </form>

</div>

<script>
document.querySelectorAll('input[name*="adjusted_stock"]').forEach(input => {
    input.addEventListener('input', function () {
        let row = input.closest('tr');
        let current = parseInt(row.querySelector('input[name*="current_stock"]').value);
        let diffCell = row.querySelector('.difference-cell');

        let newStock = parseInt(input.value) || 0;
        diffCell.innerHTML = newStock - current;
    });
});
</script>
@endsection
