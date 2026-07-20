@inject('navbarItemHelper', 'TakiElias\Tablar\Helpers\NavbarItemHelper')

@php
    $shouldRender = true;

    if (isset($item['can']) && !auth()->user()?->can($item['can'])) {
        $shouldRender = false;
    }

    if (isset($item['role']) && !auth()->user()?->hasRole($item['role'])) {
        $shouldRender = false;
    }

    $isSubmenuActive = function ($submenu) use (&$isSubmenuActive) {
        foreach ($submenu as $child) {
            $urlPath = trim(parse_url($child['url'] ?? '', PHP_URL_PATH), '/');
            if ($urlPath && request()->is($urlPath . '*')) {
                return true;
            }

            if (isset($child['submenu']) && is_array($child['submenu'])) {
                if ($isSubmenuActive($child['submenu'])) return true;
            }
        }
        return false;

    };

    $isActive = isset($item['submenu']) ? $isSubmenuActive($item['submenu']) : false;
@endphp

@if ($shouldRender)
        @if ($navbarItemHelper->isSubmenu($item))
            <li class="sidebar nav-item dropdown {{ $item['class'] }} {{ $isActive ? 'active show' : '' }}"
                @isset($item['id']) id="{{ $item['id'] }}" @endisset>

                <a class="nav-link dropdown-toggle {{ $item['class'] }} {{ $isActive ? 'active' : '' }}" href="#" data-bs-toggle="dropdown"
                data-bs-auto-close="outside" role="button" aria-expanded="false">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        @if(isset($item['icon']))
                            <i class="{{ $item['icon'] ?? '' }} {{ isset($item['icon_color']) ? 'text-' . $item['icon_color'] : '' }}"></i>
                        @else
                            <i class="ti ti-brand-tabler"></i>
                        @endif
                    </span>
                    <span class="nav-link-title">
                        {{ $item['text'] }}
                    </span>
                </a>

                <div class="sidebar dropdown-menu {{ $isActive ? 'show' : '' }}">
                    <div class="dropdown-menu-columns">
                        <div class="dropdown-menu-column">
                            @if ($navbarItemHelper->isSubmenu($item))
                                @each('tablar::partials.navbar.multilevel', $item['submenu'], 'item')
                            @elseif ($navbarItemHelper->isLink($item))
                                @include('tablar::partials.navbar.submenu-dropdown-item')
                            @endif
                        </div>
                    </div>
                </div>
            </li>

        @elseif ($navbarItemHelper->isLink($item))
            {{-- Single link --}}
            @include('tablar::partials.navbar.dropdown-item-link')
        @endif
@endif
