@php
        $isActiveLink = isset($item['url']) && request()->is(trim(parse_url($item['url'], PHP_URL_PATH), '/').'*');
@endphp

<a class="dropdown-item  {{ $isActiveLink ? 'active' : '' }}" href="{{ $item['url'] ?? '#' }}">
    @if(isset($item['icon']))
        <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <!-- Download SVG icon from http://tabler-icons.io/i/package -->
              <i class="{{ $item['icon'] ?? '' }} {{
                isset($item['icon_color']) ? 'text-' . $item['icon_color'] : ''
            }}"></i>
        </span>
    @endif
    {{ $item['text']??'' }}
</a>
