@extends('tablar::page')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Ubah Menu</h2>
                <div class="text-muted mt-1">Kelola struktur navigasi sistem Antosa Architect</div>
            </div>
        </div>
    </div>

    <div class="card mt-3 shadow-sm">
        <div class="card-body">
            <form action="{{ route('menus.update', $menu->id) }}" method="POST">
                @csrf
                @method('put')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Judul Menu</label>
                        <input type="text" name="text" class="form-control" placeholder="Misal: Beranda" value="{{ old('text', $menu->text) }}" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-select">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="route" {{ $menu->type == 'route' ? 'selected' : '' }}>Route</option>
                            <option value="url" {{ $menu->type == 'url' ? 'selected' : '' }}>URL</option>
                            <option value="label" {{ $menu->type == 'label' ? 'selected' : '' }}>Label</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="order" class="form-control" value="{{ old('order', $menu->order) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">URL / Route Name</label>
                        <input type="text" name="url" class="form-control" placeholder="dashboard.index atau https://..." value="{{ old('url', $menu->url) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent Menu</label>
                                            <select name="parent_id" class="form-select select2">
                        <option value="">-- None --</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}"
                                {{ old('parent_id', $menu->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->text }}
                            </option>
                        @endforeach
                    </select>
                    </div>


                    <div class="col-md-4 mb-3">
                        <label class="form-label">Key</label>
                        <input type="text" name="key" class="form-control" value="{{ old('key', $menu->key) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Icon (class)</label>
                        <input type="text" name="icon" class="form-control" placeholder="e.g., ti ti-home" value="{{ old('icon', $menu->icon) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Aktif</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ $menu->is_active == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ $menu->is_active == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="permission_name">Permission (opsional)</label>
                        @php
                        $selectedPermissions = collect(
                            old('permission_name',
                                $menu->permission_name
                                    ? explode('|', $menu->permission_name)
                                    : []
                            )
                        )->filter()->toArray();
                        @endphp

                        <select name="permission_name[]" class="form-select select2" multiple>
                            @foreach ($permissions as $perm)
                                <option value="{{ $perm->name }}"
                                    {{ in_array($perm->name, $selectedPermissions) ? 'selected' : '' }}>
                                    {{ $perm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 text-end mt-3">
                        <a href="{{ route('menus.index') }}" class="btn btn-light">
                            <i class="ti ti-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-dark">
                            <i class="ti ti-check me-1"></i> Simpan Menu
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
  <script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Pilih --",
            width: '100%'
        });
    });
</script>  
@endpush
