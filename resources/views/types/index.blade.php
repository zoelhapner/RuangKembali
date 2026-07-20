@extends('tablar::page')

@section('content')
<div class="container">
    <h2 class="mb-3">Management Type Produk</h2>

    <a href="{{ route('product_types.create') }}" class="btn btn-primary mb-3">+ Add piece</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                {{-- <th>#</th> --}}
                <th>Id</th>
                <th>Tipe Produk</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($product_types as $piece)
                <tr>
                    {{-- <td>{{ $piece+ 1 }}</td> --}}
                    <td>{{ $piece->id }}</td>
                    <td>{{ $piece->name }}</td>
                    <td>
                        <a href="{{ route('product_types.edit', $piece) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('product_types.destroy', $piece) }}" method="POST" style="display:inline-block">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Delete this piece?')" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
