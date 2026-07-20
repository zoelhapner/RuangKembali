@extends('tablar::page')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            
            <div class="col-12 col-md-auto ms-auto d-print-none">
                <div class="btn-list">
                @can('tambah data karyawan')       
                    <a href="{{ route("design-packages.create") }}" class="btn btn-dark">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Tambah Data Paket
                    </a>
                @endcan
                    
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <p class="text-center mb-4" style="font-size: 1.5rem; font-weight: 400; font-family: 'Poppins', sans-serif;">
                                Daftar Paket Desain
                        </p>
                    </div>

                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Paket</th>
                                        <th>Harga</th>
                                        <th>Satuan</th>
                                        <th>Jumlah Item</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($packages as $p)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $p->name }}</td>
                                        <td>Rp {{ number_format($p->price_meter) }}</td>
                                        <td>{{ $p->satuan }}</td>
                                        <td>{{ $p->items_count }}</td>
                                        <td>
                                            <a href="{{ route('design-packages.edit', $p->id) }}" 
                                            class="btn btn-sm btn-dark" title="Ubah">
                                                <i class="ti ti-edit"></i>
                                            </a>

                                            <form action="{{ route('design-packages.destroy', $p->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Hapus paket?')">

                                                @csrf @method('DELETE')

                                                <button class="btn btn-sm btn-dark" title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @if($packages->count() == 0)
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Belum ada data paket
                                        </td>
                                    </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection