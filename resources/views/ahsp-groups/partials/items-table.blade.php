<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th width="50">#</th>
            <th>Kategori</th>
            <th>Nama Item</th>
            <th width="120">Status</th>
            <th width="120">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ahsps as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->category ?? '-' }}</td>
            <td>{{ $item->item_name }}</td>
            <td>
                @if($item->is_optional)
                    <span class="badge bg-warning">Optional</span>
                @else
                    <span class="badge bg-success">Include</span>
                @endif
            </td>
            <td>
                {{-- DELETE item --}}
                <form action="{{ route('design-packages.items.delete', $item->id) }}"
                      method="POST" class="d-inline"
                      onsubmit="return confirm('Hapus item ini?')">

                    @csrf @method('DELETE')

                    <button class="btn btn-sm btn-dark">
                        <i class="ti ti-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach

        @if($ahsps->count() == 0)
        <tr>
            <td colspan="5" class="text-center text-muted">
                Belum ada item pekerjaan.
            </td>
        </tr>
        @endif
    </tbody>
</table>
