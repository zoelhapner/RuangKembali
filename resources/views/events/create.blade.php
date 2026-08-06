@extends('tablar::page')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col d-flex align-items-center">
                <a href="{{ route('events.index') }}" class="btn btn-primary d-flex align-items-center">
                    <i class="ti ti-arrow-left"></i>
                </a>
                
                    <h2 class="page-title mb-0">Tambah Data Event</h2>
                
            </div>
        </div>
    </div>
</div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card shadow-sm border-0">
                <div class="card-body px-5 py-4">
                    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">

                            {{-- Nama Event --}}
                            <div class="col-md-6">
                                <label class="form-label required">Nama Event</label>
                                <input type="text"
                                    name="name"
                                    class="form-control"
                                    value="{{ old('name') }}"
                                    placeholder="Masukkan nama event"
                                    required>
                            </div>

                            {{-- Kategori --}}
                            <div class="col-md-3">
                                <label class="form-label required">Kategori</label>
                                <select name="event_category_id" class="form-select select2" required>
                                    <option value="">Pilih Kategori</option>

                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('event_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Jenis --}}
                            <div class="col-md-3">
                                <label class="form-label required">Jenis Event</label>
                                <select name="event_type" class="form-select">
                                    <option value="free">Gratis</option>
                                    <option value="paid">Berbayar</option>
                                </select>
                            </div>

                            {{-- Registrasi --}}
                            <div class="col-md-3">
                                <label class="form-label">Registrasi Dibuka</label>
                                <input type="date"
                                    name="registration_open"
                                    class="form-control"
                                    value="{{ old('registration_open') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Registrasi Ditutup</label>
                                <input type="date"
                                    name="registration_close"
                                    class="form-control"
                                    value="{{ old('registration_close') }}">
                            </div>

                            {{-- Event --}}
                            <div class="col-md-3">
                                <label class="form-label required">Mulai Event</label>
                                <input type="datetime-local"
                                    name="start_at"
                                    class="form-control"
                                    value="{{ old('start_at') }}"
                                    required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label required">Selesai Event</label>
                                <input type="datetime-local"
                                    name="end_at"
                                    class="form-control"
                                    value="{{ old('end_at') }}"
                                    required>
                            </div>

                            {{-- Lokasi --}}
                            <div class="col-md-6">
                                <label class="form-label">Lokasi</label>
                                <input type="text"
                                    name="location"
                                    class="form-control"
                                    value="{{ old('location') }}"
                                    placeholder="Masukkan lokasi event">
                            </div>

                            {{-- Harga --}}
                            <div class="col-md-3">
                                <label class="form-label">Harga Tiket</label>
                                <input type="number"
                                    name="price"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('price',0) }}">
                            </div>

                            {{-- Kuota --}}
                            <div class="col-md-3">
                                <label class="form-label">Kuota</label>
                                <input type="number"
                                    name="quota"
                                    class="form-control"
                                    min="1"
                                    value="{{ old('quota') }}">
                            </div>

                            {{-- Audience --}}
                            <div class="col-md-4">
                                <label class="form-label">Audience</label>
                                <select name="audience_type" class="form-select">
                                    <option value="public">Umum</option>
                                    <option value="gender">Berdasarkan Gender</option>
                                    <option value="age">Berdasarkan Usia</option>
                                </select>
                            </div>

                            {{-- Publish --}}
                            <div class="col-md-4">
                                <label class="form-label">Publish</label>
                                <select name="is_published" class="form-select">
                                    <option value="1">Ya</option>
                                    <option value="0">Belum</option>
                                </select>
                            </div>

                            {{-- Poster --}}
                            <div class="col-md-4">
                                <label class="form-label">Poster</label>
                                <input type="file"
                                    name="poster"
                                    class="form-control"
                                    accept="image/*">
                            </div>

                            {{-- Thumbnail --}}
                            <div class="col-md-4">
                                <label class="form-label">Thumbnail</label>
                                <input type="file"
                                    name="thumbnail"
                                    class="form-control"
                                    accept="image/*">
                            </div>

                            {{-- Deskripsi --}}
                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description"
                                        rows="6"
                                        class="form-control">{{ old('description') }}</textarea>
                            </div>

                            {{-- Submit --}}
                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('events.index') }}" class="btn btn-secondary">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i>
                                    Simpan Event
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
            width: '100%'
        });
    });
</script>  
@endpush