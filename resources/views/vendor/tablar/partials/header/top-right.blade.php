@auth

    <div class="nav-item dropdown">
        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
           aria-label="Open user menu">
            <span class="avatar">
                @if (auth()->user()->photo_url )
                    <img src="{{ auth()->user()->photo_url }}" 
                        alt="Foto Profil" 
                        style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <i class="ti ti-users text-gray-400 text-3xl"></i>
                @endif
            </span>
            <div class="d-none d-xl-block ps-2">
                @php
                    $positions = optional(auth()->user()->employee)->position;
                    $positionText = is_array($positions) ? implode(', ', $positions) : ($positions ?? auth()->user()->getRoleNames()->first());
                @endphp

                <div>{{ auth()->user()->fullname }}</div>
                {{-- {{ $positionText }} --}}
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
            

            @php( $logout_url = View::getSection('logout_url') ?? config('tablar.logout_url', 'logout') )
            @php( $profile_url = View::getSection('profile_url') ?? config('tablar.profile_url', 'logout') )
            @php( $setting_url = View::getSection('setting_url') ?? config('tablar.setting_url', 'home') )

            @if (config('tablar.use_route_url', true))
                @php( $profile_url = $profile_url ? route($profile_url) : '' )
                @php( $logout_url = $logout_url ? route($logout_url) : '' )
                @php( $setting_url = $setting_url ? route($setting_url) : '' )
            @else
                @php( $profile_url = $profile_url ? url($profile_url) : '' )
                @php( $logout_url = $logout_url ? url($logout_url) : '' )
                @php( $setting_url = $setting_url ? url($setting_url) : '' )
            @endif

            
            <a href="{{ $profile_url }}" class="dropdown-item">
                <i class="ti ti-users"></i> Edit Profil
            </a>
            <a class="dropdown-item"
               href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="ti ti-logout"></i>
                {{ __('Keluar') }}
            </a>

            <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
                @if(config('tablar.logout_method'))
                    {{ method_field(config('tablar.logout_method')) }}
                @endif
                {{ csrf_field() }}
            </form>

        </div>
    </div>
@endauth
