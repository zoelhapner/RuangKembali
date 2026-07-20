<div class="col-md-9">
    <div class="card">
        <div class="card-body">
            {{-- Foto & Nama --}}
            <div class="d-flex align-items-center mb-4">
                @if ($employee->photo)
                    <span class="avatar avatar-xl me-3 rounded" style="background-image: url('{{ asset('storage/photos/' . $employee->photo) }}')"></span>
                @else
                    <span class="avatar avatar-xl me-3 avatar-rounded bg-secondary-lt">?</span>
                @endif
                <div>
                    <h3 class="card-title mb-1">{{ $employee->fullname }}</h3>
                    <div class="text-muted">{{ $employee->job ?? 'No Job Title' }}</div>
                </div>
            </div>

           {{-- Informasi Pribadi --}}
            <h4 class="subheader">🧾 Informasi Pribadi</h4>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <span class="text-secondary fw-normal">Lisensi:</span>
                            {{ $employee->licenses->pluck('name')->implode(', ') ?: '-' }}
                        </div>
                        <div class="list-group-item">
                            <span class="text-secondary fw-normal">Telepon:</span> {{ $employee->phone ?? '-' }}
                        </div>
                        <div class="list-group-item">
                            <span class="text-secondary fw-normal">Agama:</span> {{ $employee->religion->name ?? '-' }}
                        </div>
                        <div class="list-group-item">
                            <span class="text-secondary fw-normal">Tempat Lahir:</span> {{ $employee->birth_place ?? '-' }}
                        </div>
                        <div class="list-group-item">
                            <span class="text-secondary fw-normal">Alamat:</span> {{ $employee->address ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <span class="text-secondary fw-normal">Nomor KTP:</span> {{ $employee->identity_number ?? '-' }}
                        </div>
                        <div class="list-group-item">
                            <span class="text-secondary fw-normal">Tanggal Lahir:</span> {{ $employee->birth_date_formatted ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>


            {{-- Pernikahan --}}
            <h4 class="subheader">💍 Pernikahan</h4>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item"><span class="text-secondary fw-normal">Status:</span> {{ $employee->readable_marital_status }}</div>
                        <div class="list-group-item"><span class="text-secondary fw-normal">Tanggal Nikah:</span> {{ $employee->married_date_formatted }}</div>
                    </div>
                </div>
            </div>

            {{-- Bahasa 
            <h4 class="subheader">🌐 Kemampuan Bahasa</h4>
            <div class="row mb-3">
                @php
                    function badgeColor($value) {
                        return $value === 'Lancar' ? 'green' : 'yellow';
                    }
                @endphp
                @foreach ($employee->readable_languages as $label => $value)
                    <div class="col-md-6 mb-2 d-flex align-items-center">
                
                        <div>
                            <span class="text-muted">{{ $label }}</span> :
                            <span class="badge bg-{{ badgeColor($value) }}-lt text-{{ badgeColor($value) }}">
                                {{ $value }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div> --}}

            {{-- Aksi --}}
            <div class="mt-4">
                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary">
                    Edit Profile
                </a>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>