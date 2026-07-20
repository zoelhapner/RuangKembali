<div class="card mb-3 shadow-sm">
    <div class="card-header bg-light fw-bold">
        Data Pengguna
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center">
                <img src="{{ asset('storage/photos/' . ($user->photo ?? 'default.png')) }}"
                     class="rounded-circle border mb-2" width="100" height="100">
            </div>
            <div class="col-md-9">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th style="width:30%">Nama Lengkap</th><td>{{ $user->name ?? '-' }}</td></tr>
                    <tr><th>Email</th><td>{{ $user->email ?? '-' }}</td></tr>
                    <tr><th>No HP</th><td>{{ $user->phone ?? '-' }}</td></tr>
                    <tr><th>Alamat</th><td>{{ $user->address ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
