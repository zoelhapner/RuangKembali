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
                    @if ($errors->any())
                            <div class="alert alert-danger">
                                <div class="fw-bold mb-2">
                                    Terdapat kesalahan:
                                </div>

                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- ============================= --}}
                        {{-- INFORMASI EVENT --}}
                        {{-- ============================= --}}
                        <div class="mb-4">
                            <h4 class="mb-1">Informasi Event</h4>
                            <div class="text-secondary small">
                                Informasi dasar mengenai event yang akan dibuat.
                            </div>
                        </div>

                        <div class="row g-3 mb-4">

                            {{-- Nama Event --}}
                            <div class="col-12">
                                <label class="form-label required">Nama Event</label>
                                <input type="text"
                                    name="name"
                                    class="form-control"
                                    value="{{ old('name') }}"
                                    placeholder="Masukkan nama event"
                                    required>
                            </div>

                            {{-- Kategori --}}
                            <div class="col-md-6">
                                <label class="form-label required">Kategori</label>
                                <select name="event_category_id"
                                        class="form-select select2"
                                        required>
                                    <option value="">Pilih Kategori</option>

                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('event_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Jenis Event --}}
                            <div class="col-md-6">
                                <label class="form-label required">Jenis Event</label>
                                <select id="event_type"
                                        name="event_type"
                                        class="form-select select2"
                                        required>
                                    <option value="free"
                                        {{ old('event_type', 'free') == 'free' ? 'selected' : '' }}>
                                        Gratis
                                    </option>

                                    <option value="paid"
                                        {{ old('event_type') == 'paid' ? 'selected' : '' }}>
                                        Berbayar
                                    </option>
                                </select>
                            </div>

                        </div>


                        {{-- ============================= --}}
                        {{-- JADWAL EVENT --}}
                        {{-- ============================= --}}
                        <div class="mb-4">
                            <h4 class="mb-1">Jadwal Event</h4>
                            <div class="text-secondary small">
                                Atur periode pendaftaran dan waktu pelaksanaan event.
                            </div>
                        </div>

                        <div class="row g-3 mb-4">

                            {{-- Registrasi Dibuka --}}
                            <div class="col-md-6">
                                <label class="form-label">Registrasi Dibuka</label>
                                <input type="date"
                                    name="registration_open"
                                    class="form-control"
                                    value="{{ old('registration_open') }}">
                            </div>

                            {{-- Registrasi Ditutup --}}
                            <div class="col-md-6">
                                <label class="form-label">Registrasi Ditutup</label>
                                <input type="date"
                                    name="registration_close"
                                    class="form-control"
                                    value="{{ old('registration_close') }}">
                            </div>

                            {{-- Mulai Event --}}
                            <div class="col-md-6">
                                <label class="form-label required">Mulai Event</label>
                                <input type="datetime-local"
                                    name="start_at"
                                    class="form-control"
                                    value="{{ old('start_at') }}"
                                    required>
                            </div>

                            {{-- Selesai Event --}}
                            <div class="col-md-6">
                                <label class="form-label required">Selesai Event</label>
                                <input type="datetime-local"
                                    name="end_at"
                                    class="form-control"
                                    value="{{ old('end_at') }}"
                                    required>
                            </div>

                        </div>


                        {{-- ============================= --}}
                        {{-- LOKASI & TIKET --}}
                        {{-- ============================= --}}
                        <div class="mb-4">
                            <h4 class="mb-1">Lokasi & Tiket</h4>
                            <div class="text-secondary small">
                                Informasi lokasi, harga tiket, dan kapasitas peserta.
                            </div>
                        </div>

                        <div class="row g-3 mb-4">

                            {{-- Lokasi --}}
                            <div class="col-md-6">
                                <label class="form-label">Lokasi</label>
                                <input type="text"
                                    name="location"
                                    class="form-control"
                                    value="{{ old('location') }}"
                                    placeholder="Contoh: Hotel Fortuna Grande">
                            </div>

                            {{-- Harga --}}
                            <div class="col-md-3">
                                <label class="form-label">Harga Tiket</label>
                                <input type="number"
                                    name="price"
                                    id="price"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('price', 0) }}"
                                    placeholder="0">
                            </div>

                            {{-- Kuota --}}
                            <div class="col-md-3">
                                <label class="form-label">Kuota Peserta</label>
                                <input type="number"
                                    name="quota"
                                    class="form-control"
                                    min="1"
                                    value="{{ old('quota') }}"
                                    placeholder="Tidak terbatas">
                            </div>

                        </div>


                        {{-- ============================= --}}
                        {{-- AUDIENCE & PUBLIKASI --}}
                        {{-- ============================= --}}
                        <div class="mb-4">
                            <h4 class="mb-1">Audience & Publikasi</h4>
                            <div class="text-secondary small">
                                Tentukan siapa yang dapat mengikuti event dan status publikasinya.
                            </div>
                        </div>

                        <div class="row g-3 mb-4">

                            {{-- Audience --}}
                            <div class="col-md-6">
                                <label class="form-label">Audience</label>
                                <select name="audience_type"
                                        class="form-select select2"
                                        required>

                                    <option value="public"
                                        {{ old('audience_type', 'public') == 'public' ? 'selected' : '' }}>
                                        Umum
                                    </option>

                                    <option value="gender"
                                        {{ old('audience_type') == 'gender' ? 'selected' : '' }}>
                                        Berdasarkan Gender
                                    </option>

                                    <option value="age"
                                        {{ old('audience_type') == 'age' ? 'selected' : '' }}>
                                        Berdasarkan Usia
                                    </option>

                                </select>
                            </div>

                            {{-- Publish --}}
                            <div class="col-md-6">
                                <label class="form-label">Status Publikasi</label>
                                <select name="is_published"
                                        class="form-select select2"
                                        required>

                                    <option value="1"
                                        {{ old('is_published', '0') == '1' ? 'selected' : '' }}>
                                        Ya, Publish
                                    </option>

                                    <option value="0"
                                        {{ old('is_published', '0') == '0' ? 'selected' : '' }}>
                                        Belum Publish
                                    </option>

                                </select>
                            </div>

                        </div>


                        {{-- ============================= --}}
                        {{-- MEDIA --}}
                        {{-- ============================= --}}
                        <div class="mb-4">
                            <h4 class="mb-1">Media Event</h4>
                            <div class="text-secondary small">
                                Upload poster dan thumbnail yang digunakan untuk menampilkan event.
                            </div>
                        </div>

                        <div class="row g-3 mb-4">

                            {{-- Poster --}}
                            <div class="col-md-6">
                                <label class="form-label">Poster</label>
                                <input type="file"
                                    name="poster"
                                    class="form-control"
                                    accept="image/*">

                                <div class="form-hint">
                                    Format gambar: JPG, JPEG, PNG, WEBP.
                                </div>
                            </div>

                            {{-- Thumbnail --}}
                            <div class="col-md-6">
                                <label class="form-label">Thumbnail</label>
                                <input type="file"
                                    name="thumbnail"
                                    class="form-control"
                                    accept="image/*">

                                <div class="form-hint">
                                    Gunakan gambar dengan rasio yang sesuai untuk thumbnail.
                                </div>
                            </div>
                            {{-- YouTube --}}
                            <div class="col-md-6">

                                <label class="form-label">
                                    Video YouTube
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="ti ti-brand-youtube"></i>
                                    </span>

                                    <input type="url"
                                        name="youtube_url"
                                        class="form-control"
                                        value="{{ old('youtube_url', $event->youtube_url ?? '') }}"
                                        placeholder="https://www.youtube.com/watch?v=...">

                                </div>

                                <div class="form-hint">
                                    Masukkan link video YouTube event.
                                </div>

                            </div>
                        </div>


                        {{-- ============================= --}}
                        {{-- DESKRIPSI --}}
                        {{-- ============================= --}}
                        <div class="mb-4">
                            <h4 class="mb-1">Deskripsi Event</h4>
                            <div class="text-secondary small">
                                Jelaskan informasi lengkap mengenai event.
                            </div>
                        </div>

                        <div class="row mb-4">

                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>

                                <textarea name="description"
                                    rows="7"
                                    class="form-control"
                                    placeholder="Tuliskan deskripsi lengkap mengenai event...">{{ old('description') }}</textarea>
                            </div>

                        </div>


                        {{-- ============================= --}}
                        {{-- ACTION --}}
                        {{-- ============================= --}}
                        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 pt-3 border-top">

                            <a href="{{ route('events.index') }}"
                            class="btn btn-secondary">
                                Batal
                            </a>

                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>
                                Simpan Event
                            </button>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
<script>
    $(document).ready(function () {

        // ==========================================
        // SELECT2
        // ==========================================
        $('.select2').select2({
            width: '100%'
        });


        // ==========================================
        // EVENT TYPE → PRICE
        // ==========================================
        const $eventType = $('#event_type');
        const $price = $('#price');

        function handleEventType() {

            const type = $eventType.val();

            if (type === 'free') {

                // Event gratis → harga selalu 0
                $price.val(0);

                // Disable input harga
                $price.prop('disabled', true);

            } else {

                // Event berbayar → harga bisa diisi
                $price.prop('disabled', false);

                // Jika sebelumnya 0, kosongkan agar user langsung mengisi
                if ($price.val() === '0') {
                    $price.val('');
                }
            }
        }

        // Jalankan saat halaman pertama kali dibuka
        handleEventType();


        // Jalankan ketika jenis event berubah
        $eventType.on('change', function () {
            handleEventType();
        });


        // ==========================================
        // SEBELUM SUBMIT
        // ==========================================
        $('form').on('submit', function () {

            // Pastikan event gratis selalu mengirim price = 0
            if ($eventType.val() === 'free') {
                $price.prop('disabled', false);
                $price.val(0);
            }

        });

    });
</script>
@endpush