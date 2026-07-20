@extends('tablar::page')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                {{-- <div class="col">
            
                    <h2 class="page-title">
                        Akun
                    </h2>
                </div> --}}
                <!-- Page title actions -->
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                  <span class="d-none d-sm-inline">
                  
                        {{-- <a href="{{ route("accounts.create") }}" class="btn btn-dark text-white d-none d-sm-inline-block" >
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Tambah Akun
                        </a> --}}
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <p class="text-center mb-4" style="font-size: 1.4rem; font-weight: 400; font-family: 'Montserrat', sans-serif;">
                                 Daftar Akun
                            </p>
                        </div>
                        <div class="table-responsive">
                            <table id="accountsTable" class="table card-table table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Nama Akun</th>
                                        <th>Role</th>                                      
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
$(document).ready(function () {
    let table = $('#accountsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('accounts.index') }}",
        columns: [
            { data: 'fullname', name: 'fullname' },
            { data: 'role_dropdown', name: 'role', orderable: false, searchable: false },
        ],
                language: {
                    search: "",
                    searchPlaceholder: "Cari akun...",
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

    // Saat dropdown role berubah
    $(document).on('change', '.role-dropdown', function() {
        let userId = $(this).data('user-id');
        let selectedRoles = $(this).val();

        $.ajax({
            url: "{{ route('accounts.update-role') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                user_id: userId,
                roles: selectedRoles
            },
            success: function (res) {
                Swal.fire('Berhasil', res.message, 'success');
            },
            error: function (xhr) {
                Swal.fire(
                    'Gagal',
                    xhr.responseJSON?.message || 'Gagal mengubah role',
                    'error'
                );
            }
        });
    });

    $('#accountsTable').on('draw.dt', function() {
$('.select2').select2({
    width: '100%',
    placeholder: "Pilih role",
    allowClear: true
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
@push('css')
<style>
#accountsTable {
    table-layout: fixed;
    width: 100%;
}
.select2-selection__rendered {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

</style>
<style>

#accountsTable th:nth-child(1),
#accountsTable td:nth-child(1) {
    width: 55%;
}
#accountsTable th:nth-child(2),
#accountsTable td:nth-child(2) {
    width: 45%;
}
</style>
@endpush