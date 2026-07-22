<?php

namespace App\Http\Controllers;

use App\Models\Affiliator;
use App\Models\User;
use App\Models\Religion;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\PostalCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;


class AffiliatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $auth = auth()->user();

        $query = Affiliator::with(['user.roles']);

        if ($auth->can('lihat data affiliator') && !$auth->can('lihat daftar affiliator')) {
            $query->where('user_id', $auth->id);
        }

        if ($request->ajax()) {
            $affiliators = $query->get();

        return DataTables::of($affiliators)
                ->addIndexColumn()
                ->addColumn('fullname', function ($row) {
                    return $row->user->fullname ?? '-';
                })
                ->addColumn('email', function ($row) {
                    return $row->user->email ?? '-';
                })
                ->addColumn('membership', function ($row) {
                    $level = ucfirst($row->readableMembership);
                    $color = match ($row->readableMembership) {
                        '1' => 'secondary',
                        '2' => 'warning',
                        '3' => 'info',
                        default => 'secondary',
                    };
                    return '<span class="badge bg-' . $color . '">' . $level . '</span>';
                })
                ->editColumn('fullname', function ($row) {
                    $url = route('affiliators.show', $row->id);
                    $name = Str::title($row->user->fullname ?? '-');
                    return '<a href="'.$url.'">'.e($name).'</a>';
                })
                
                ->addColumn('action', function ($affiliator) {
                    $buttons = '';
                    if (auth()->user()->can('ubah data affiliator')) {
                        $buttons .= '<a href="' . route('affiliators.edit', $affiliator->id) . '" class="btn btn-icon btn-sm btn-warning me-1" title="Ubah">
                                        <i class="ti ti-edit"></i>
                                    </a>';
                    }
                    if (auth()->user()->can('lihat data affiliator')) {
                        $buttons .= '<a href="' . route('affiliators.show', $affiliator->id) . '" class="btn btn-icon btn-sm btn-primary me-1" title="Lihat">
                                        <i class="ti ti-eye"></i>
                                    </a>';

                    }
                    if (auth()->user()->can('hapus data affiliator')) {
                        $buttons .= '<button data-id="' . $affiliator->id . '" class="btn btn-icon btn-sm btn-danger delete-affiliator" title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>';
                    }
                    return $buttons;
                })
                ->rawColumns(['fullname', 'action', 'membership'])
                ->make(true);
        }

        return view('affiliators.index');
    }

    private function readableMembership($value)
    {
        return match ((int) $value) {
            1 => 'Putih',
            2 => 'Biru',
            3 => 'Merah',
            default => '-',
        };
    }

        public function create()
    {
        $user = auth()->user();
        $religions = Religion::all();
        $provinces = Province::all();
        $roles = Role::all();
        $externalRoles = Role::where('role_group', 'Eksternal')
            ->orderBy('name')
            ->pluck('name');
        return view('affiliators.create', compact('user', 'roles', 'religions', 'provinces', 'externalRoles'));
    }

        public function store(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'nullable|exists:users,id',
        'fullname' => 'required|string|max:255',
        'nickname' => 'nullable|string|max:100',
        'gender' => 'nullable|in:1,2',
        'email' => 'required|email|unique:users,email',
        'birth_place' => 'nullable|string|max:100',
        'birth_date' => 'nullable|date_format:d-m-Y',
        'identity_number' => 'nullable|string|max:16',
        'religion_id' => 'nullable|exists:religions,id',
        'npwp' => 'nullable|string|max:30',
        'phone' => 'required|string|max:20',
        'address' => 'nullable|string|max:255',
        'province_id' => 'nullable|exists:provinces,id',
        'city_id' => 'nullable|exists:cities,id',
        'district_id' => 'nullable|exists:districts,id',
        'sub_district_id' => 'nullable|exists:sub_districts,id',
        'postal_code_id' => 'nullable|exists:postal_codes,id',
        'bank_id' => 'nullable|uuid|exists:banks,id',
        'account_number' => 'nullable|string|max:50',
        'account_holder' => 'nullable|string|max:50',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        // --- data affiliators ---
        'nia' => 'required|unique:affiliators,nia',
        'role' => 'required|array',
        'role.*' => 'string|exists:roles,name',
        'membership' => 'nullable|in:1,2,3',
        'saldo' => ['nullable', 'numeric', 'min:0'],
    ]);

    $birth_date = Carbon::createFromFormat('d-m-Y', $validated['birth_date'])->format('Y-m-d');

    // 🔹 Upload foto (jika ada)
    if ($request->hasFile('photo')) {
        $filename = Str::uuid().'.'.$request->file('photo')->getClientOriginalExtension();

        $path = $request->file('photo')->storeAs(
            'photos',
            $filename,
            'public'
        );

        // simpan full relative path
        $validated['photo'] = $path;   // → photos/uuid.jpg
    }

    DB::transaction(function () use ($validated, $birth_date) {

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
                'birth_date' => $birth_date,
                'identity_number' => $validated['identity_number'],
                'npwp' => $validated['npwp'],
                'address' => $validated['address'],
                'religion_id' => $validated['religion_id'],
                'province_id' => $validated['province_id'],
                'city_id' => $validated['city_id'],
                'district_id' => $validated['district_id'],
                'sub_district_id' => $validated['sub_district_id'],
                'postal_code_id' => $validated['postal_code_id'],
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

        Affiliator::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'nia' => $validated['nia'],
            'membership' => $validated['membership'] ?? null,
            'saldo' => $validated['saldo'] ?? null,
            'photo' => $validated['photo'] ?? null,
        ]);
    });

    return redirect()
        ->route('affiliators.index')
        ->with('success', 'Data affiliators berhasil ditambahkan.' .
            (session('new_user_password') ? ' Akun user baru dibuat. Password: ' . session('new_user_password') : '')
        );
}


public function generateNiaAjax()
{
    $lastNumber = Affiliator::selectRaw("MAX(CAST(SUBSTRING(nia, 3) AS INTEGER)) as max_nia")->value('max_nia');
    $newNumber = ($lastNumber ?? 0) + 1;

    $newNia = 'A-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    return response()->json([
        'nia' => $newNia
    ]);
}

     public function show(Affiliator $affiliator)
    {
        $affiliator->load('user');
        return view('sdm.affiliators.show', [
            'user' => $affiliator->user,
            'affiliator' => $affiliator
        ]);
    }

    public function edit($id)
    {
        $affiliator = Affiliator::with(['user.roles', 'user.bank'])->findOrFail($id);
        $user = $affiliator->user;
        $religions = Religion::all();
        $provinces = Province::all();
        $roles = Role::all();
        $externalRoles = Role::where('role_group', 'Eksternal')
            ->orderBy('name')
            ->pluck('name');
        $selectedRoles = $user->roles->pluck('name')->toArray();
        $cities = City::where('province_id', $user->province_id)->get();
        $districts = District::where('city_id', $user->city_id)->get();
        $subDistricts = SubDistrict::where('district_id', $user->district_id)->get();
        $postalCodes = PostalCode::where('sub_district_id', $user->sub_district_id)->get();
        

        return view('affiliators.edit', compact('user', 'roles', 'selectedRoles', 'affiliator', 'externalRoles',
        'religions', 'provinces', 'cities', 'districts', 'subDistricts', 'postalCodes'));
    }

    public function update(Request $request, Affiliator $affiliator)
{
    $validated = $request->validate([
        // --- data user ---
        'fullname' => 'required|string|max:255',
        'nickname' => 'nullable|string|max:100',
        'gender' => 'nullable|in:1,2',
        'email' => 'required|email|unique:users,email,' . $affiliator->user_id,
        'birth_place' => 'nullable|string|max:100',
        'birth_date' => 'nullable|date_format:Y-m-d',
        'identity_number' => 'nullable|string|max:16',
        'religion_id' => 'nullable|exists:religions,id',
        'npwp' => 'nullable|string|max:30',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'user_province_id' => 'nullable|exists:provinces,id',
        'user_city_id' => 'nullable|exists:cities,id',
        'user_district_id' => 'nullable|exists:districts,id',
        'user_sub_district_id' => 'nullable|exists:sub_districts,id',
        'user_postal_code_id' => 'nullable|exists:postal_codes,id',
        'bank_id' => 'nullable|uuid|exists:banks,id',
        'account_number' => 'nullable|string|max:50',
        'account_holder' => 'nullable|string|max:50',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        // --- data affiliator ---
        'nia' => 'required|string|max:50|unique:affiliators,nia,' . $affiliator->id,
        'membership' => 'nullable|in:1,2,3',
        'saldo' => ['required', 'numeric', 'min:0'],
        'role' => 'nullable|array',
        'role.*' => 'string|exists:roles,name',
    ]);

    DB::transaction(function () use ($validated, $affiliator, $request) {
        $user = $affiliator->user;

        // 🔹 Upload foto baru (hapus lama jika ada)
        if ($request->hasFile('photo')) {
            $newPhotoPath = $request->file('photo')->storeAs(
                'photos',
                Str::uuid().'.'.$request->file('photo')->getClientOriginalExtension(),
                'public'
            );

            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $validated['photo'] = $newPhotoPath;
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
            // Pastikan minimal masih punya role affiliator
            if (!$user->hasRole('affiliator')) {
                $user->assignRole('affiliator');
            }
        }

        $affiliator->update([
            'nia' => $validated['nia'],
            'membership' => $validated['membership'] ?? null,
            'saldo' => $validated['saldo'],
            'photo' => $validated['photo'] ?? $affiliator->photo,
        ]);
    });

    return redirect()
        ->route('affiliators.show', $affiliator->id)
        ->with('success', 'Data affiliator berhasil diperbarui.');
}

      public function destroy(Affiliator $affiliator)
    {
            if ($affiliator) {
            $affiliator->delete();
            return response()->json(['status' => 'success', 'message' => 'affiliator deleted successfully']);
        }

        return response()->json(['status' => 'failed', 'message' => 'Unable to delete']);
    }

}
