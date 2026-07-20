<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Architect;
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
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class ArchitectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $auth = auth()->user();

        $query = Architect::with(['user.roles']);

        if ($auth->can('lihat data arsitek') && !$auth->can('lihat daftar arsitek')) {
            $query->where('user_id', $auth->id);
        }

        if ($request->ajax()) {
            $architects = $query->get();

        return DataTables::of($architects)
                ->addIndexColumn()

                ->addColumn('fullname', function ($row) {
                    return $row->user->fullname ?? '-';
                })
                ->addColumn('email', function ($row) {
                    return $row->user->email ?? '-';
                })
                ->addColumn('architect_name', fn($row) => $row->architect_name ?? '-')
                ->addColumn('architect_phone', fn($row) => $row->architect_phone ?? '-')
                ->addColumn('architect_address', fn($row) => $row->architect_address ?? '-')
                ->addColumn('province_name', fn($row) => $row->province->name ?? '-')
                ->addColumn('city_name', fn($row) => $row->city->name ?? '-')
                ->addColumn('district_name', fn($row) => $row->district->name ?? '-')
                ->addColumn('sub_district_name', fn($row) => $row->subDistrict->name ?? '-')
                ->addColumn('postal_code', fn($row) => $row->postalCode->postal_code ?? '-')

                ->editColumn('fullname', function ($row) {
                    $url = route('architects.show', $row->id);
                    $name = Str::title($row->user->fullname ?? '-');
                    return '<a href="'.$url.'">'.e($name).'</a>';
                })
                
                ->addColumn('action', function ($architect) {
                    $buttons = '';
                    if (auth()->user()->can('ubah data arsitek')) {
                        $buttons .= '<a href="' . route('architects.edit', $architect->id) . '" class="btn btn-icon btn-sm btn-dark me-1" title="Ubah">
                                        <i class="ti ti-edit"></i>
                                    </a>';
                    }
                    if (auth()->user()->can('lihat data arsitek')) {
                        $buttons .= '<a href="' . route('architects.show', $architect->id) . '" class="btn btn-icon btn-sm btn-dark me-1" title="Lihat">
                                        <i class="ti ti-eye"></i>
                                    </a>';

                    }
                    if (auth()->user()->can('hapus data arsitek')) {
                        $buttons .= '<button data-id="' . $architect->id . '" class="btn btn-icon btn-sm btn-dark delete-architect" title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>';
                    }
                    return $buttons;
                })
                ->rawColumns(['fullname', 'action'])
                ->make(true);
        }

        return view('architects.index');
    }

            public function create()
    {
        $user = auth()->user();
        $religions = Religion::all();
        $provinces = Province::all();
        $roles = Role::all();
        return view('architects.create', compact('user', 'roles', 'religions', 'provinces'));
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

        // --- data architect ---
        'architect_id' => 'required|unique:architects,architect_id',
        'architect_name' => 'required|string|max:255',
        'architect_phone' => 'required|string|max:20',
        'architect_address' => 'required|string|max:255',
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
            $validated['architect_address'] = $validated['address'];
            $validated['architect_name'] = $validated['fullname'];
            $validated['architect_phone'] = $validated['phone'];
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

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('photos', $filename, 'public');
            $validated['photo'] = $filename;
        }

        

        // 🔹 Simpan architect
        Architect::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'architect_id' => $validated['architect_id'],
            'architect_name' => $validated['architect_name'],
            'architect_phone' => $validated['architect_phone'],
            'architect_address' => $validated['architect_address'],
            'province_id' => $validated['province_id'],
            'city_id' => $validated['city_id'],
            'district_id' => $validated['district_id'],
            'sub_district_id' => $validated['sub_district_id'],
            'postal_code_id' => $validated['postal_code_id'],
        ]);
    });

    return redirect()
        ->route('architects.index')
        ->with('success', 'Data architect berhasil ditambahkan.' .
            (session('new_user_password') ? ' Akun user baru dibuat. Password: ' . session('new_user_password') : '')
        );
}


public function generateArchitectIdAjax()
{
    $lastNumber = Architect::selectRaw("MAX(CAST(SUBSTRING(architect_id, 3) AS INTEGER)) as max_architect_id")->value('max_architect_id');
    $newNumber = ($lastNumber ?? 0) + 1;

    $newArchitectId = 'K-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    return response()->json([
        'architect_id' => $newArchitectId
    ]);
}
         public function show(Architect $architect)
    {
        $architect->load('user');
        return view('architects.show', [
            'user' => $architect->user,
            'architect' => $architect
        ]);
    }

   
    public function edit($id)
    {
        $architect = Architect::with(['user.roles', 'user.bank'])->findOrFail($id);
        $user = $architect->user;
        $religions = Religion::all();
        $provinces = Province::all();
        $roles = Role::all();
        $selectedRoles = $user->roles->pluck('name')->toArray();
        $cities = City::where('province_id', $user->province_id)->get();
        $districts = District::where('city_id', $user->city_id)->get();
        $subDistricts = SubDistrict::where('district_id', $user->district_id)->get();
        $postalCodes = PostalCode::where('sub_district_id', $user->sub_district_id)->get();
        
        return view('architects.edit', compact('user', 'roles', 'selectedRoles', 'architect',
        'religions', 'provinces', 'cities', 'districts', 'subDistricts', 'postalCodes'));
    }

   
    public function update(Request $request, Architect $architect)
{
    $validated = $request->validate([
        // --- data user ---
        'fullname' => 'required|string|max:255',
        'nickname' => 'nullable|string|max:100',
        'gender' => 'required|in:1,2',
        'email' => 'required|email|unique:users,email,' . $architect->user_id,
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

        // --- data architect ---
        'architect_id' => 'required|string|max:50|unique:architects,architect_id,' . $architect->id,
        'architect_name' => 'required|string|max:255',
        'architect_phone' => 'required|string|max:20',
        'architect_address' => 'required|string|max:255',
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
            $validated['architect_address'] = $validated['address'];
            $validated['architect_name'] = $validated['fullname'];
            $validated['architect_phone'] = $validated['phone'];
        }

    DB::transaction(function () use ($validated, $architect, $request) {
        $user = $architect->user;

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
            // Pastikan minimal masih punya role architect
            if (!$user->hasRole('Mitra Arsitek')) {
                $user->assignRole('Mitra Arsitek');
            }
        }

        


        // 🔹 Update data architect
        $architect->update([
            'architect_id' => $validated['architect_id'],
            'architect_name' => $validated['architect_name'],
            'architect_phone' => $validated['architect_phone'],
            'architect_address' => $validated['architect_address'],
            'province_id' => $validated['province_id'],
            'city_id' => $validated['city_id'],
            'district_id' => $validated['district_id'],
            'sub_district_id' => $validated['sub_district_id'],
            'postal_code_id' => $validated['postal_code_id'],
            'photo' => $validated['photo'] ?? $architect->photo,
        ]);
    });

    return redirect()
        ->route('architects.show', $architect->id)
        ->with('success', 'Data architect berhasil diperbarui.');
}

       public function destroy(Architect $architect): JsonResponse
{
    DB::transaction(function () use ($architect) {
        $user = $architect->user;

        // 🔹 Hapus foto architect dari storage kalau ada
        if ($architect->photo && Storage::disk('public')->exists('photos/' . $architect->photo)) {
            Storage::disk('public')->delete('photos/' . $architect->photo);
        }

        // 🔹 Hapus record architect
        $architect->delete();

        // 🔹 Cek user yang terhubung
        if ($user) {
            $roles = $user->roles->pluck('name')->toArray();

            // Kalau hanya punya role "architect"
            if (count($roles) === 1 && in_array('Mitra Arsitek', $roles)) {

                // Hapus juga foto user kalau ada
                if ($user->photo && Storage::disk('public')->exists('photos/' . $user->photo)) {
                    Storage::disk('public')->delete('photos/' . $user->photo);
                }

                // Hapus akun user
                $user->delete();

            } else {
                // Kalau user masih punya role lain, hapus hanya role architect-nya
                $user->removeRole('Mitra Arsitek');
            }
        }
    });

    return response()->json([
        'status' => 'success',
        'message' => 'Data architect berhasil dihapus.'
    ]);
}
}
