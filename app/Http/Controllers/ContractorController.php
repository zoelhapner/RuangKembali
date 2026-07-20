<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contractor;
use App\Models\User;
use App\Models\Religion;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\PostalCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class ContractorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $auth = auth()->user();

        $query = Contractor::with(['user.roles']);

        if ($auth->can('lihat data kontraktor') && !$auth->can('lihat daftar kontraktor')) {
            $query->where('user_id', $auth->id);
        }

        if ($request->ajax()) {
            $contractors = $query->get();

        return DataTables::of($contractors)
                ->addIndexColumn()
                ->addColumn('fullname', function ($row) {
                    return $row->user->fullname ?? '-';
                })
                ->addColumn('email', function ($row) {
                    return $row->user->email ?? '-';
                })
                // ->addColumn('membership', function ($row) {
                //     $level = ucfirst($row->readableMembership);
                //     $color = match ($row->readableMembership) {
                //         '1' => 'secondary',
                //         '2' => 'warning',
                //         '3' => 'info',
                //         default => 'secondary',
                //     };
                //     return '<span class="badge bg-' . $color . '">' . $level . '</span>';
                // })
                ->editColumn('fullname', function ($row) {
                    $url = route('contractors.show', $row->id);
                    $name = Str::title($row->user->fullname ?? '-');
                    return '<a href="'.$url.'">'.e($name).'</a>';
                })
                
                ->addColumn('action', function ($contractor) {
                    $buttons = '';
                    if (auth()->user()->can('ubah data kontraktor')) {
                        $buttons .= '<a href="' . route('contractors.edit', $contractor->id) . '" class="btn btn-icon btn-sm btn-dark me-1" title="Ubah">
                                        <i class="ti ti-edit"></i>
                                    </a>';
                    }
                    if (auth()->user()->can('lihat data kontraktor')) {
                        $buttons .= '<a href="' . route('contractors.show', $contractor->id) . '" class="btn btn-icon btn-sm btn-dark me-1" title="Lihat">
                                        <i class="ti ti-eye"></i>
                                    </a>';

                    }
                    if (auth()->user()->can('hapus data kontraktor')) {
                        $buttons .= '<button data-id="' . $contractor->id . '" class="btn btn-icon btn-sm btn-dark delete-kontraktor" title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>';
                    }
                    return $buttons;
                })
                ->rawColumns(['fullname', 'action', 'membership'])
                ->make(true);
        }

        return view('contractors.index');
    }
    
        public function create()
    {
        $user = auth()->user();
        $religions = Religion::all();
        $provinces = Province::all();
        $roles = Role::all();
        return view('contractors.create', compact('user', 'roles', 'religions', 'provinces'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'nullable|exists:users,id',
        'fullname' => 'required|string|max:255',
        'nickname' => 'nullable|string|max:100',
        'gender' => 'required|in:1,2',
        'email' => 'required|email|unique:users,email',
        'birth_place' => 'nullable|string|max:100',
        'birth_date' => 'required|date_format:Y-m-d',
        'identity_number' => 'required|string|max:16',
        'religion_id' => 'required|exists:religions,id',
        'npwp' => 'nullable|numeric|max:30',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'user_province_id' => 'required|exists:provinces,id',
        'user_city_id' => 'required|exists:cities,id',
        'user_district_id' => 'required|exists:districts,id',
        'user_sub_district_id' => 'required|exists:sub_districts,id',
        'user_postal_code_id' => 'required|exists:postal_codes,id',
        'bank_id' => 'nullable|uuid|exists:banks,id',
        'account_number' => 'nullable|string|max:50',
        'account_holder' => 'nullable|string|max:50',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        // --- data contractor ---
        'contractor_id' => 'required|unique:contractors,contractor_id',
        'contractor_name' => 'required|string|max:255',
        'contractor_phone' => 'required|string|max:20',
        'contractor_address' => 'required|string|max:255',
        'province_id' => 'required|exists:provinces,id',
        'city_id' => 'required|exists:cities,id',
        'district_id' => 'required|exists:districts,id',
        'sub_district_id' => 'required|exists:sub_districts,id',
        'postal_code_id' => 'required|exists:postal_codes,id',
        'role' => 'required|array',
        'role.*' => 'string|exists:roles,name',
    ]);

    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
        $file->storeAs('photos', $filename, 'public');
        $validated['photo'] = $filename;
    }

    // Jika alamat pengiriman sama dengan domisili user
        if ($request->has('same_address')) {
            $validated['province_id'] = $validated['user_province_id'];
            $validated['city_id'] = $validated['user_city_id'];
            $validated['district_id'] = $validated['user_district_id'];
            $validated['sub_district_id'] = $validated['user_sub_district_id'];
            $validated['postal_code_id'] = $validated['user_postal_code_id'];
            $validated['contractor_address'] = $validated['address'];
            $validated['contractor_name'] = $validated['fullname'];
            $validated['contractor_phone'] = $validated['phone'];
        }

    DB::transaction(function () use ($validated, $request) {

        // 🔹 Cek user atau buat baru
        if (!empty($validated['user_id'])) {
            $user = User::find($validated['user_id']);
        } else {
            $password = '12345678';
            $user = User::create([
                'id' => Str::uuid(),
                'fullname' => $validated['fullname'],
                'nickname' => $validated['nickname'],
                'birth_place' => $validated['birth_place'],
                'birth_date' => $validated['birth_date'],
                'identity_number' => $validated['identity_number'],
                'npwp' => $validated['npwp'],
                'address' => $validated['address'],
                'religion_id' => $validated['religion_id'],
                'province_id' => $validated['user_province_id'],
                'city_id' => $validated['user_city_id'],
                'district_id' => $validated['user_district_id'],
                'sub_district_id' => $validated['user_sub_district_id'],
                'postal_code_id' => $validated['user_postal_code_id'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'photo' => $validated['photo'] ?? null,
                'bank_id' => $validated['bank_id'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'account_holder' => $validated['account_holder'] ?? null,
            ]);

            session()->flash('new_user_password', $password);
        }

        // 🔹 Assign role tanpa hapus role lama
        if (!empty($validated['role'])) {
            foreach ($validated['role'] as $r) {
                if (!$user->hasRole($r)) {
                    $user->assignRole($r);
                }
            }
        }

        

        // 🔹 Simpan contractor
        Contractor::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'contractor_id' => $validated['contractor_id'],
            'contractor_name' => $validated['contractor_name'],
            'contractor_phone' => $validated['contractor_phone'],
            'contractor_address' => $validated['contractor_address'],
            'province_id' => $validated['province_id'],
            'city_id' => $validated['city_id'],
            'district_id' => $validated['district_id'],
            'sub_district_id' => $validated['sub_district_id'],
            'postal_code_id' => $validated['postal_code_id'],
        ]);
    });

    return redirect()
        ->route('contractors.index')
        ->with('success', 'Data contractor berhasil ditambahkan.' .
            (session('new_user_password') ? ' Akun user baru dibuat. Password: ' . session('new_user_password') : '')
        );
}


public function generateContractorIdAjax()
{
    $lastNumber = Contractor::selectRaw("MAX(CAST(SUBSTRING(contractor_id, 3) AS INTEGER)) as max_contractor_id")->value('max_contractor_id');
    $newNumber = ($lastNumber ?? 0) + 1;

    $newContractorId = 'K-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    return response()->json([
        'contractor_id' => $newContractorId
    ]);
}
         public function show(Contractor $contractor)
    {
        $contractor->load('user');
        return view('contractors.show', [
            'user' => $contractor->user,
            'contractor' => $contractor
        ]);
    }

   
    public function edit($id)
    {
        $contractor = contractor::with(['user.roles', 'user.bank'])->findOrFail($id);
        $user = $contractor->user;
        $religions = Religion::all();
        $provinces = Province::all();
        $roles = Role::all();
        $selectedRoles = $user->roles->pluck('name')->toArray();
        $cities = City::where('province_id', $user->province_id)->get();
        $districts = District::where('city_id', $user->city_id)->get();
        $subDistricts = SubDistrict::where('district_id', $user->district_id)->get();
        $postalCodes = PostalCode::where('sub_district_id', $user->sub_district_id)->get();
        
        return view('contractors.edit', compact('user', 'roles', 'selectedRoles', 'contractor',
        'religions', 'provinces', 'cities', 'districts', 'subDistricts', 'postalCodes'));
    }

   
    public function update(Request $request, Contractor $contractor)
{
    $validated = $request->validate([
        // --- data user ---
        'fullname' => 'required|string|max:255',
        'nickname' => 'nullable|string|max:100',
        'gender' => 'required|in:1,2',
        'email' => 'required|email|unique:users,email,' . $contractor->user_id,
        'birth_place' => 'nullable|string|max:100',
        'birth_date' => 'nullable|date_format:Y-m-d',
        'identity_number' => 'required|string|max:16',
        'religion_id' => 'required|exists:religions,id',
        'npwp' => 'nullable|string|max:30',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'user_province_id' => 'required|exists:provinces,id',
        'user_city_id' => 'required|exists:cities,id',
        'user_district_id' => 'required|exists:districts,id',
        'user_sub_district_id' => 'required|exists:sub_districts,id',
        'user_postal_code_id' => 'required|exists:postal_codes,id',
        'bank_id' => 'nullable|uuid|exists:banks,id',
        'account_number' => 'nullable|string|max:50',
        'account_holder' => 'nullable|string|max:50',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        // --- data contractor ---
        'contractor_id' => 'required|string|max:50|unique:contractors,contractor_id,' . $contractor->id,
        'contractor_name' => 'required|string|max:255',
        'contractor_phone' => 'required|string|max:20',
        'contractor_address' => 'required|string|max:255',
        'province_id' => 'required|exists:provinces,id',
        'city_id' => 'required|exists:cities,id',
        'district_id' => 'required|exists:districts,id',
        'sub_district_id' => 'required|exists:sub_districts,id',
        'postal_code_id' => 'required|exists:postal_codes,id',
        'role' => 'nullable|array',
        'role.*' => 'string|exists:roles,name',
    ]);

    if ($request->has('same_address')) {
            $validated['province_id'] = $validated['user_province_id'];
            $validated['city_id'] = $validated['user_city_id'];
            $validated['district_id'] = $validated['user_district_id'];
            $validated['sub_district_id'] = $validated['user_sub_district_id'];
            $validated['postal_code_id'] = $validated['user_postal_code_id'];
            $validated['contractor_address'] = $validated['address'];
            $validated['contractor_name'] = $validated['fullname'];
            $validated['contractor_phone'] = $validated['phone'];
        }

    DB::transaction(function () use ($validated, $contractor, $request) {
        $user = $contractor->user;

        // 🔹 Upload foto baru (hapus lama jika ada)
        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists('photos/' . $user->photo)) {
                Storage::disk('public')->delete('photos/' . $user->photo);
            }
            $file = $request->file('photo');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('photos', $filename, 'public');
            $validated['photo'] = $filename;
        }

        // 🔹 Update user data
        $user->update([
            'fullname' => $validated['fullname'],
            'nickname' => $validated['nickname'],
            'birth_place' => $validated['birth_place'],
            'birth_date' => $validated['birth_date'],
            'identity_number' => $validated['identity_number'],
            'religion_id' => $validated['religion_id'],
            'npwp' => $validated['npwp'],
            'address' => $validated['address'],
            'province_id' => $validated['user_province_id'],
            'city_id' => $validated['user_city_id'],
            'district_id' => $validated['user_district_id'],
            'sub_district_id' => $validated['user_sub_district_id'],
            'postal_code_id' => $validated['user_postal_code_id'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'bank_id' => $validated['bank_id'] ?? null,
            'account_number' => $validated['account_number'] ?? null,
            'account_holder' => $validated['account_holder'] ?? null,
            'photo' => $validated['photo'] ?? $user->photo,
        ]);

        // 🔹 Role management: tetap multi-role aman
        if (!empty($validated['role'])) {
            foreach ($validated['role'] as $r) {
                if (!$user->hasRole($r)) {
                    $user->assignRole($r);
                }
            }
        } else {
            // Pastikan minimal masih punya role contractor
            if (!$user->hasRole('Mitra Kontraktor')) {
                $user->assignRole('Mitra Kontraktor');
            }
        }

        


        // 🔹 Update data contractor
        $contractor->update([
            'contractor_id' => $validated['contractor_id'],
            'contractor_name' => $validated['contractor_name'],
            'contractor_phone' => $validated['contractor_phone'],
            'contractor_address' => $validated['contractor_address'],
            'province_id' => $validated['province_id'],
            'city_id' => $validated['city_id'],
            'district_id' => $validated['district_id'],
            'sub_district_id' => $validated['sub_district_id'],
            'postal_code_id' => $validated['postal_code_id'],
            'photo' => $validated['photo'] ?? $contractor->photo,
        ]);
    });

    return redirect()
        ->route('contractors.show', $contractor->id)
        ->with('success', 'Data contractor berhasil diperbarui.');
}

       public function destroy(Contractor $contractor): JsonResponse
{
    DB::transaction(function () use ($contractor) {
        $user = $contractor->user;

        // 🔹 Hapus foto contractor dari storage kalau ada
        if ($contractor->photo && Storage::disk('public')->exists('photos/' . $contractor->photo)) {
            Storage::disk('public')->delete('photos/' . $contractor->photo);
        }

        // 🔹 Hapus record contractor
        $contractor->delete();

        // 🔹 Cek user yang terhubung
        if ($user) {
            $roles = $user->roles->pluck('name')->toArray();

            // Kalau hanya punya role "contractor"
            if (count($roles) === 1 && in_array('Mitra Kontraktor', $roles)) {

                // Hapus juga foto user kalau ada
                if ($user->photo && Storage::disk('public')->exists('photos/' . $user->photo)) {
                    Storage::disk('public')->delete('photos/' . $user->photo);
                }

                // Hapus akun user
                $user->delete();

            } else {
                // Kalau user masih punya role lain, hapus hanya role contractor-nya
                $user->removeRole('Mitra Kontraktor');
            }
        }
    });

    return response()->json([
        'status' => 'success',
        'message' => 'Data contractor berhasil dihapus.'
    ]);
}
}
