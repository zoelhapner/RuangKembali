<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Pekerjaan</h3>
    </div>
    <div class="card-body">
        @if ($employee->workers && $employee->workers->count())
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped" style="font-size: 0.9rem; font-weight: 500; font-family: 'Poppins', sans-serif;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Perusahaan</th>
                            <th>Jabatan Terakhir</th>
                            <th>Tanggal Mulai Kerja</th>
                            <th>Tanggal Berakhir Kerja</th>
                            <th>Jabatan Terakhir</th>
                            <th>Gaji Terakhir</th>
                            <th>Alasan Keluar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employee->workers as $index => $work)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $work->company_name }}</td>                                                               
                                <td>{{ $work->last_position }}</td>
                                <td>{{ $work->start_date_formatted }}</td>
                                <td>{{ $work->end_date_formatted }}</td>
                                <td>{{ $work->last_position }}</td>
                                <td>{{ $work->last_salary }}</td>
                                <td>{{ $work->reason_for_leaving }}</td>
                                <td>
                                <a href="{{ route('employee_workers.edit', $work->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('employee_workers.destroy', $work->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                     <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                             </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted">Belum ada data pekerjaan.</p>
        @endif
    </div>

    <div class="mt-4">
            <a href="{{ route('employee_workers.create') }}?employee_id={{ $employee->id }}" class="btn btn-primary">
                Tambah Data
            </a>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                Back to List
            </a>
    </div>
</div>
