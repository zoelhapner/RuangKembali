@extends('tablar::page')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        @can('tambah data tenaga')       
                                <a href="{{ route("labor_costs.create") }}" class="btn btn-dark">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="12" y1="5" x2="12" y2="19"/>
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                    Tambah Data Harga Tenaga
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
                                Daftar Harga Tenaga
                            </p>
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                        </div>
                        <div class="table-responsive">
                            <table id="laborCostTable" class="table card-table table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Jenis Pekerjaan</th>
                                        <th>Satuan</th>
                                        <th>harga Satuan Dasar (Rp.)</th>
                                        <th>Keterangan</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                {{-- <tbody>
                                    @foreach($laborCosts as $key => $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->code }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->unit }}</td>
                                        <td>Rp {{ number_format($item->base_unit_price, 0, ',', '.') }}</td>
                                        <td>{{ $item->notes }}</td>
                                        <td>
                                            <a href="{{ route('labor_costs.edit', $item->id) }}" 
                                            class="btn btn-dark btn-sm">
                                                Edit
                                            </a>

                                            <button class="btn btn-dark btn-sm btn-delete"
                                                data-id="{{ $item->id }}"
                                                data-url="{{ route('labor_costs.destroy', $item->id) }}">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody> --}}
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDelete" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="formDelete">
                @csrf
                @method('DELETE')

                <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Anda yakin mau menghapus data ini?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(function () {
        $('#laborCostTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('labor_costs.index') }}",

            columns: [
                { data: 'DT_RowIndex', orderable:false, searchable:false },
                { data: 'code' },
                { data: 'description' },
                { data: 'unit' },
                {
                    data: 'base_unit_price',
                    render: function(data, type) {
                        if (type === 'display') {
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                        }
                        return data; // untuk sorting tetap angka
                    }
                },
                { data: 'notes' },
                { data: 'action', orderable:false, searchable:false },
            ],
                        language: {
                            search: "",
                            searchPlaceholder: "Cari tukang...",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada data",
                            infoFiltered: "(difilter dari _MAX_ total data)",
                            zeroRecords: "Data tidak ditemukan",
                            paginate: {
                                first: "Awal",
                                last: "Akhir",
                                next: "›",
                                previous: "‹"
                            }
                        },

                        initComplete: function () {
                            const input = $('.dt-search input');
                            input.removeClass('form-control-sm')
                                .addClass('form-control');
                        }
        });

                // modal delete
        $(document).on('click', '.btn-delete', function() {
            const url = $(this).data('url');
            $('#formDelete').attr('action', url);
            $('#modalDelete').modal('show');
        });
        
        $(document).on('click', '.btn-duplicate', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Duplikat data?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, duplikat!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/labor_costs/${id}/duplicate`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            $('#laborCostTable').DataTable().ajax.reload(null, false);

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
