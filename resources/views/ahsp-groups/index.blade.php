@extends('tablar::page')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Daftar Kelompok AHSP
                </h2>
            </div>

            <div class="col-12 col-md-auto ms-auto d-print-none">
                <a href="{{ route('ahsp-groups.create') }}" class="btn btn-dark">
                    <i class="ti ti-plus"></i> Tambah Group
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th>Unit Pekerjaan</th>
                            <th>Satuan</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ahsps as $ahsp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            
                            <td>
                                <div>
                                    {{ $ahsp->group->nama }}
                                </div>
                                
                            </td>

                            
                            <td>
                                {{ $ahsp->nama_pekerjaan }}
                            </td>

                            
                            <td>
                                {{ $ahsp->satuan }}
                    
                            </td>

                            <td>
                                <a href="{{ route('ahsp-groups.edit', $ahsp->ahsp_group_id) }}"
                                   class="btn btn-sm btn-dark">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <form action="{{ route('ahsps.destroy', $ahsp) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Hapus AHSP?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-dark">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Belum ada data AHSP
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
