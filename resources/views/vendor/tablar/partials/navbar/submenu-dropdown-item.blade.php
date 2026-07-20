@inject('navbarItemHelper', 'TakiElias\Tablar\Helpers\NavbarItemHelper')
@php
    $shouldRender = true;

    // Cek permission
    if (isset($item['can']) && !auth()->user()?->can($item['can'])) {
        $shouldRender = false;
    }

    // Cek role
    if (isset($item['role']) && !auth()->user()?->hasRole($item['role'])) {
        $shouldRender = false;
    }
@endphp

@if ($shouldRender)
    @if ($navbarItemHelper->isSubmenu($item))
        @each('tablar::partials.navbar.multilevel', $item['submenu'], 'item')
    @elseif ($navbarItemHelper->isLink($item))
        @include('tablar::partials.navbar.single-item')
    @endif
@endif

@if ($navbarItemHelper->isSubmenu($item))
    @each('tablar::partials.navbar.multilevel', $item['submenu'], 'item')
@elseif ($navbarItemHelper->isLink($item))
    @include('tablar::partials.navbar.single-item')
@endif
