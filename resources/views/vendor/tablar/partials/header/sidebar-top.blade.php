<header class="navbar d-lg-flex d-none d-print-none">
    <div class="topbar">
        {{-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
                aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button> --}}
        
        <div class="navbar-nav flex-row">
                <div class="d-flex align-items-center">
                @include('tablar::partials.header.notifications')
                <div class="nav-item dropdown me-2">
                    <form id="switchRoleForm" action="{{ route('switch.role') }}" method="POST" class="px-3 mb-2">
                        @csrf

                        <select name="role_id" class="form-select" data-input="true" data-custom="true"
                            onchange="document.getElementById('switchRoleForm').submit()">

                            @foreach(auth()->user()->roles as $role)
                                @php
                                    $isActive = auth()->user()->activeRole?->id === $role->id;
                                @endphp

                                <option value="{{ $role->id }}" {{ $isActive ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach

                        </select>
                    </form>

                </div>
                {{-- @include('tablar::partials.header.theme-mode') --}}
                 {{-- Tambahkan dropdown switch license di sini --}}
                {{-- @role('Pemilik Lisensi|Karyawan|Akuntan') {{-- selain Super Admin
                    @php
                        $user = Auth::user();

                        if ($user->hasRole('Pemilik Lisensi')) {
                            $licenses = $user->licenses;
                        } elseif ($user->hasRole(['Karyawan', 'Akuntan'])) {
                            $licenses = $user->employee?->licenses ?? collect();
                        } else {
                            $licenses = collect();
                        }

                        $activeLicense = session('active_license_name', $licenses->first()->name ?? 'Pilih Lisensi');
                    @endphp

                    <div class="nav-item dropdown me-2">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-2" data-bs-toggle="dropdown" aria-label="Switch License">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-building"></i>
                            </span>
                            <div class="d-none d-xl-block ps-2">
                                <div>{{ $activeLicense }}</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            @forelse($licenses as $license)
                                <a class="dropdown-item {{ session('active_license_id') == $license->id ? 'active fw-normal text-white' : '' }}" 
                                href="{{ route('switch.license', $license->id) }}">
                                    @if(session('active_license_id') == $license->id)
                                        <i class="ti ti-check me-1"></i>
                                    @endif
                                    {{ $license->name }}
                                </a>
                            @empty
                                <span class="dropdown-item text-muted">Tidak ada lisensi</span>
                            @endforelse
                        </div>
                    </div>
                @endrole --}}
                @include('tablar::partials.header.top-right')
            </div>
            
        </div>
    </div>
</header>
<header class="mobile-header d-flex d-lg-none">

    <!-- LEFT -->
    <div class="mobile-left">
        <button id="mobileSidebarToggle">
            <i class="ti ti-menu-2"></i>
        </button>
    </div>
    <div class="mobile-right">

        @include('tablar::partials.header.notifications')

        <!-- switch role -->
        <div class="mobile-role-switch">
            <form id="switchRoleFormMobile"
                action="{{ route('switch.role') }}"
                method="POST">

                @csrf

                <select name="role_id"
                        class="form-select form-select-sm"
                        onchange="document.getElementById('switchRoleFormMobile').submit()">

                    @foreach(auth()->user()->roles as $role)

                        <option value="{{ $role->id }}"
                            {{ auth()->user()->activeRole?->id === $role->id ? 'selected' : '' }}>

                            {{ $role->name }}

                        </option>

                    @endforeach

                </select>

            </form>
        </div>

        @include('tablar::partials.header.top-right')

    </div>

</header>
<div class="sidebar-overlay"></div>
@push('js')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const btn = document.getElementById("mobileSidebarToggle");
    const sidebar = document.querySelector(".navbar-vertical");
    const overlay = document.querySelector(".sidebar-overlay");

    if(btn && sidebar){

        btn?.addEventListener("click", function () {

            sidebar.classList.toggle("show-sidebar");
            console.log(sidebar.className);
            if(overlay){
                overlay.classList.toggle("show");
            }

        });

    }

    if(overlay && sidebar){

        overlay.addEventListener("click", function(){

            sidebar.classList.remove("show-sidebar");
            overlay.classList.remove("show");

        });

    }

});
</script>
@endpush