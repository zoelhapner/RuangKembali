@inject('navbarItemHelper', 'TakiElias\Tablar\Helpers\NavbarItemHelper')
@php
    // Buat cek apakah URL sekarang cocok dengan URL item
    $isActiveLink = isset($item['url']) && request()->is(trim(parse_url($item['url'], PHP_URL_PATH), '/').'*');
@endphp
@if ($navbarItemHelper->isSubmenu($item))
    <div class="dropend {{ $isActiveLink ? 'active' : '' }}">
        <a class="dropdown-item dropdown-toggle {{ $isActiveLink ? 'active' : '' }}" href=""
           data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
           aria-expanded="false">

            @if(isset($item['icon']))
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <!-- Download SVG icon from http://tabler-icons.io/i/package -->
                    <i class="{{ $item['icon'] ?? '' }} {{ isset($item['icon_color']) ? 'text-' . $item['icon_color'] : '' }}"></i>
                </span>
            @endif


            {{ $item['text'] }}
            {{-- Label (optional) --}}
            @isset($item['label'])
                    <span class="badge badge-sm bg-{{ $item['label_color'] ?? 'primary' }} text-uppercase ms-2">{{ $item['label'] }}</span>
            @endisset
        </a>
        <div class="dropdown-menu  {{ $isActiveLink ? 'show' : '' }}">
            @each('tablar::partials.navbar.dropend', $item['submenu'], 'item')
        </div>
    </div>
@elseif ($navbarItemHelper->isLink($item))
    @include('tablar::partials.navbar.single-item')
@endif
