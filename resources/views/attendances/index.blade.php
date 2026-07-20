@extends('tablar::page')

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col-12 col-md-auto ms-auto d-print-none">
                {{-- <div class="btn-list">

                    @can('tambah data absensi')
                            <a href="{{ route('attendances.create') }}" class="btn btn-dark">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>

                                Tambah Menu Baru
                            </a>
                    @endcan

                </div> --}}
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
                            <h3 class="mb-0 fw-semibold">
                                Daftar Absensi Karyawan
                            </h3>
                    </div>
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table id="absenTable" class="table table-bordered table-striped align-middle w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Divisi</th>
                                        <th>H</th>
                                        <th>TL A</th>
                                        <th>TL B</th>
                                        <th>TL C</th>
                                        <th>DL</th>
                                        <th>I</th>
                                        <th>S</th>
                                        <th>C</th>
                                        <th>A</th>
                                        <th>Hari Kerja</th>
                                        <th>Kehadiran</th>
                                        <th>% Hadir</th>
                                        <th>% Tepat Waktu</th>
                                        <th>Lembur</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- @can('tambah data absensi')
<a href="{{ route('attendances.create') }}"
   class="mobile-fab d-md-none">

    <svg xmlns="http://www.w3.org/2000/svg"
         width="26"
         height="26"
         viewBox="0 0 24 24"
         stroke-width="2"
         stroke="currentColor"
         fill="none"
         stroke-linecap="round"
         stroke-linejoin="round">

        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <line x1="12" y1="5" x2="12" y2="19"/>
        <line x1="5" y1="12" x2="19" y2="12"/>

    </svg>

</a>
@endcan --}}
@endsection
@push('js')
    <script>
        $(function() {
            const isMobile = window.innerWidth < 576;
            const table = $('#absenTable').DataTable({
                scrollY: '500px',
                scrollX: true,
                scrollCollapse: true,
                fixedColumns: !isMobile ? {
                    leftColumns: 4
                } : false,
                serverSide: true,
                processing: true,
                responsive: false,
                
                ajax: {

                    url: "{{ route('attendances.datatable') }}",

                    data: function(d){

                        const month = $('#month').val();

                        if(month){

                            const split = month.split('-');

                            d.year = split[0];

                            d.month = split[1];

                        }

                    }

                },

                columns:[

                    {
                        data:'DT_RowIndex',
                        name:'DT_RowIndex',
                        searchable:false,
                        orderable:false
                    },

                    {
                        data:'fullname',
                        name:'fullname'
                    },

                    {
                        data:'position.name',
                        defaultContent:'-'
                    },

                    {
                        data:'division.name',
                        defaultContent:'-'
                    },

                    {
                        data:'h'
                    },

                    {
                        data:'tla'
                    },

                    {
                        data:'tlb'
                    },

                    {
                        data:'tlc'
                    },

                    {
                        data:'dl'
                    },

                    {
                        data:'izin'
                    },

                    {
                        data:'sakit'
                    },

                    {
                        data:'cuti'
                    },

                    {
                        data:'alpha'
                    },

                    {
                        data:'total_hari_kerja'
                    },

                    {
                        data:'total_hari_kehadiran'
                    },

                    {
                        data:'kehadiran'
                    },

                    {
                        data:'ketepatan_waktu'
                    },

                    {
                        data:'lembur'
                    },

                    {
                        data:'keterangan'
                    }

                ],
                language: {
                    search: "",
                    searchPlaceholder: "Cari dafta absen...",
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

            // Delete user functionally
            $('#absenTable').on('click', '.delete-menu', function () {
            const menuId = $(this).data('id');

            Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data akan hilang secara permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'

            }).then((result) => {

                if (result.isConfirmed) {
                    $.ajax({

                        url: `/menus/${menuId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },

                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'User telah dihapus.',
                                    timer: 2000,
                                    showConfirmButton: false
                            });

                        table.ajax.reload(null, false); // refresh datatable
                        } else {

                            Swal.fire('Gagal', response.message || 'Tidak bisa menghapus data.', 'error');
                        }
                        },

                    error: function () {

                    Swal.fire('Error', 'Terjadi kesalahan saat menghapus.', 'error');
                    }

                    });
                }
            });
            });


           
        });
    </script>

    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Sukses!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    @endif
@endpush
{{-- @push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

$('#menuTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('menus.index') }}",

    columns: [
        { data: 'DT_RowIndex', orderable:false, searchable:false },
        { data: 'text' },
        { data: 'url' },
        { data: 'parent_name' },
        { data: 'order' },
        { data: 'active_badge', orderable:false, searchable:false },
        { data: 'permission_name' },
        { data: 'actions', orderable:false, searchable:false },
    ],
                language: {
                    search: "",
                    searchPlaceholder: "Cari menu...",
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

});
</script>
@endpush --}}