<div class="mb-3">
    <label for="license_id" class="form-label">Lisensi</label>

    @if ($licenses->count() === 1)
        {{-- Dropdown disabled --}}
        <select class="form-control" disabled>
            <option value="{{ $licenses->first()->id }}">
                {{ $licenses->first()->name }}
            </option>
        </select>

        {{-- Hidden input dikirim ke server --}}
        <input type="hidden" name="license_id" value="{{ $licenses->first()->id }}">
    @else
        {{-- Dropdown normal --}}
        <select name="license_id" id="license_id" class="form-control @error('license_id') is-invalid @enderror" required>
            <option value="">-- Pilih Lisensi --</option>
            @foreach ($licenses as $license)
                <option value="{{ $license->id }}"
                    {{ old('license_id', $selectedLicenseId ?? '') == $license->id ? 'selected' : '' }}>
                    {{ $license->name }}
                </option>
            @endforeach
        </select>

        @error('license_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    @endif
</div>
