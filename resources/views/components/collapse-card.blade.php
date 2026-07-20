<div class="card shadow-sm border-0 mb-4">
    <div class="card-header {{ $sticky ? 'sticky-rab-header' : '' }}">
        <div class="d-flex align-items-center w-100">
            <h3 class="card-title mb-0 fw-bold">
                {{ $title }}
            </h3>
            <div class="ms-auto d-flex align-items-center gap-2">
                {{ $actions ?? '' }}
                <button type="button"
                        class="btn btn-sm btn-icon btn-toggle-card"
                        data-target="#{{ $target }}">
                    <i class="ti ti-chevron-down"></i>
                </button>
            </div>
        </div>
    </div>
    <div id="{{ $target }}" class="collapse-card-body d-none">
        <div class="card-body">
            {{ $slot }}
        </div>
    </div>
</div>