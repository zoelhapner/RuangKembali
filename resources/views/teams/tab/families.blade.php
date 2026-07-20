<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Keluarga</h3>
    </div>
    <div class="card-body">
        @if ($employee->families && $employee->families->count())
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped" style="font-size: 0.9rem; font-weight: 500; font-family: 'Poppins', sans-serif;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Hubungan dengan karyawan</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir</th>
                            <th>Pekerjaan</th>
                            <th>Telepon Kantor Pekerjaan</th>
                            <th>Pendidikan Terakhir</th>
                            <th>Nama Sekolah</th>
                            <th>Nama Perusahaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employee->families as $index => $fam)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $fam->name }}</td>
                                <td>{{ $fam->readable_relationship }}</td>
                                <td>{{ $fam->readable_gender }}</td>
                                <td>{{ $fam->birth_date_formatted }}</td>
                                <td>{{ $fam->job }}</td>
                                <td>{{ $fam->job_phone }}</td>
                                <td>{{ $fam->last_education_level }}</td>
                                <td>{{ $fam->institution_name }}</td>
                                <td>{{ $fam->company_name }}</td>
                                <td>
                                <a href="{{ route('employee_families.edit', $fam->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('employee_families.destroy', $fam->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete(event)">
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
            <p class="text-muted">Belum ada data keluarga.</p>
        @endif
    </div>

    <div class="mt-4">
            <a href="{{ route('employee_families.create') }}?employee_id={{ $employee->id }}" class="btn btn-primary">
                Tambah Data
            </a>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                Back to List
            </a>
    </div>
</div>
