{{-- Penting --}}
@extends('tablar::page')

@section('title', 'Edit Pengguna')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle">
                        Overview
                    </div>
                    <h2 class="page-title">
                        user
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                  
                        <a href=" {{ route("accounts.index") }} " class="btn btn-primary text-white d-none d-sm-inline-block" >
                            Kembali
                        </a>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <p class="text-center mb-4">
                                Edit Data Pengguna
                            </p>
                        </div>

                        <div class="card-body">
                            <form class="font-normal" action="{{ route('accounts.update', $user) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('put')

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="required">Nama: </label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}"
                                         @if(auth()->user()->hasRole('Pemilik Lisensi')) readonly @endif>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                

                                    <div class="col-md-6 mb-3">
                                        <label for="email">Email:</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}"
                                         @if(auth()->user()->hasRole('Pemilik Lisensi')) readonly @endif>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <label for="password">Password:</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{ old('password', $user->password) }}"
                                         @if(auth()->user()->hasRole('Pemilik Lisensi')) readonly @endif>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="role" class="required">Role: </label>
                                        <select name="role" class="form-select" required>
                                            <option value="">-- Pilih Role --</option>
                                            @foreach (\Spatie\Permission\Models\Role::all() as $role)
                                                <option value="{{ $role->name }}"
                                                    {{ $user->roles->first() && $user->roles->first()->name == $role->name ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary text-white mt-4">Simpan</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection