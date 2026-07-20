<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Pendidikan</h3>
    </div>
    <div class="card-body">
        @if ($employee->educations && $employee->educations->count())
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped" style="font-size: 0.9rem; font-weight: 500; font-family: 'Poppins', sans-serif;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jenjang</th>
                            <th>Nama Sekolah</th>
                            <th>Tahun Masuk</th>
                            <th>Tahun Lulus</th>
                            <th>Apakah Lulus?</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employee->educations as $index => $edu)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $edu->education_level }}</td>
                                <td>{{ $edu->institution_name }}</td>
                                <td>{{ $edu->start_year }}</td>
                                <td>{{ $edu->end_year }}</td>
                                <td>{{ $edu->readable_is_graduated }}</td>
                                <td>
                                <a href="{{ route('employee_educations.edit', $edu->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('employee_educations.destroy', $edu->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete(event)">
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
            <p class="text-muted">Belum ada data pendidikan.</p>
        @endif
    </div>

    <div class="mt-4">
            <a href="{{ route('employee_educations.create') }}?employee_id={{ $employee->id }}" class="btn btn-primary">
                Tambah Data
            </a>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                Back to List
            </a>
    </div>
</div>
