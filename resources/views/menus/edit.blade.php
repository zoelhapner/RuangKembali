@extends('tablar::page')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col d-flex align-items-center">
                <a href="{{ route('menus.index') }}" class="btn btn-primary d-flex align-items-center">
                    <i class="ti ti-arrow-left"></i>
                </a>
                
                    <h2 class="page-title mb-0">Ubah Data Menu</h2>
                
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
    <div class="card shadow-sm border-0">
        <div class="card-body px-5 py-4">
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
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i> Simpan Menu
                        </button>
                    </div>
                </div>
            </form>
        </div>
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
