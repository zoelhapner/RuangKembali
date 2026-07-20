<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Religion;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\PostalCode;

class GuestProfileController extends Controller
{
    /**
     * Tampilkan form edit profil guest
     */
    public function edit()
    {
        $user = auth()->user();
        $religions = Religion::all();
        $provinces = Province::all();
        $cities = City::where('province_id', $user->province_id)->get();
        $districts = District::where('city_id', $user->city_id)->get();
        $subDistricts = SubDistrict::where('district_id', $user->district_id)->get();
        $postalCodes = PostalCode::where('sub_district_id', $user->sub_district_id)->get();
        return view('guest.complete-profile', compact('user', 'religions', 'provinces', 'cities', 'districts', 'subDistricts', 'postalCodes'));
    }

    /**
     * Proses update profil guest
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // 🧩 Validasi input
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'identity_number' => 'nullable|string|max:16',
            'npwp' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 🖼️ Upload foto jika ada
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }

        // 🔁 Update data user
        $user->update($validated);

        // ✅ (Opsional) Update status kelengkapan profil
        if ($this->isProfileComplete($user)) {
            $user->update(['is_verified' => false]); 
            // Tetap false sampai diverifikasi admin
        }

        return redirect()->route('customer.dashboard')
            ->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Hitung apakah profil sudah lengkap
     */
    private function isProfileComplete($user)
    {
        $required = [
            'name', 'birthdate', 'ktp_number', 'phone',
            'address', 'province', 'city', 'bank_name', 'account_number'
        ];

        foreach ($required as $field) {
            if (empty($user->$field)) return false;
        }

        return true;
    }
}
