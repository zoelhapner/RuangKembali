@extends('tablar::page')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        @can('tambah data customer')       
                            <a href="{{ route("job-categories.create") }}" class="btn btn-dark" >
                                <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Tambah Data AHSP
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
                            <p class="text-center mb-4">
                                    Daftar AHSP
                            </p>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap" id="jobTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bidang</th>
                                        <th>Group</th>
                                        <th>Kode Urut</th>
                                        <th>Nama Pekerjaan</th>
                                        <th>Satuan</th>
                                        <th width="120">Harga</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')

<script>
$(function () {
    $('#jobTable').DataTable({
        scrollY: '500px',
            scrollX: true,
            scrollCollapse: true,
                fixedColumns: {
                    leftColumns: 3
                },
        processing: true,
        serverSide: true,
        ajax: "{{ route('job-categories.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'bidang', name: 'bidang' },
            { data: 'nama_group', name: 'nama_group' },
            { data: 'kode_urut', name: 'kode_urut' },
            { data: 'nama_pekerjaan', name: 'nama_pekerjaan' },
            { data: 'satuan', name: 'satuan' },
            { data: 'grand_total', name: 'grand_total' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
        ],
                language: {
                    search: "",
                    searchPlaceholder: "Cari ahsp...",
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
    $(document).on('click', '.btn-delete', function() {
        let url = $(this).data('url');

        Swal.fire({
            title: 'Yakin hapus?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(res) {
                        $('#jobTable').DataTable().ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message ?? 'Data berhasil dihapus',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Data tidak bisa dihapus'
                        });
                        console.log(xhr.responseText);
                    }
                });
            }
        });
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
                    url: `/job-categories/${id}/duplicate`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        $('#jobTable').DataTable().ajax.reload(null, false);

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
