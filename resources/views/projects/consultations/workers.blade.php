<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Pekerjaan</h3>
    </div>
    <div class="card-body">
        @if ($license_holder->workers && $license_holder->workers->count())
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped" style="font-size: 0.9rem; font-weight: 500; font-family: 'Poppins', sans-serif;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Perusahaan</th>
                            <th>Kota Perusahaan</th>
                            <th>Nomor telepon Perusahaan</th>
                            <th>Posisi</th>
                            <th>Status Karyawan</th>
                            <th>Tanggal Mulai Kerja</th>
                            <th>Tanggal Berakhir Kerja</th>
                            <th>Masih Kerja</th>
                            <th>Kompetensi Utama</th>
                            <th>Deskripsi Tanggungjawab</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($license_holder->workers as $index => $work)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $work->company_name }}</td>
                                <td>{{ $work->city }}</td>
                                <td>{{ $work->phone }}</td>
                                <td>{{ $work->position }}</td>
                                <td>{{ $work->readable_employment_type }}</td>
                                <td>{{ $work->start_date_formatted }}</td>
                                <td>{{ $work->end_date_formatted }}</td>
                                <td>{{ $work->readable_is_current }}</td>
                                <td>{{ $work->skills_used }}</td>
                                <td>{{ $work->job_description }}</td>
                                <td>
                                <a href="{{ route('license_holder_workers.edit', $work->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('license_holder_workers.destroy', $work->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete(event)">
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
            <a href="{{ route('license_holder_workers.create') }}?license_holder_id={{ $license_holder->id }}" class="btn btn-primary">
                Tambah Data
            </a>
            <a href="{{ route('license_holders.index') }}" class="btn btn-outline-secondary">
                Back to List
            </a>
    </div>
</div>
