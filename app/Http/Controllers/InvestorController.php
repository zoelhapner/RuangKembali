<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\Religion;
use App\Models\Employee;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\PostalCode;
use Illuminate\Support\Carbon;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class InvestorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $auth = auth()->user();

        $query = Investor::with(['user.roles']);

        if ($auth->can('lihat data investor') && !$auth->can('lihat daftar investor')) {
            $query->where('user_id', $auth->id);
        }

        if ($request->ajax()) {
            $investors = $query->get();

        return DataTables::of($investors)
                ->addIndexColumn()
                ->addColumn('fullname', function ($row) {
                    return $row->user->fullname ?? '-';
                })
                ->addColumn('email', function ($row) {
                    return $row->user->email ?? '-';
                })
                ->addColumn('phone', function ($row) {
                    return $row->user->phone ?? '-';
                })

                // ->addColumn('name', function ($row) {
                //     return $row->user->name ?? '-';
                // })

                ->addColumn('registered_date', fn($row) => Carbon::parse($row->registered_date)->format('d/m/Y'))
                ->addColumn('investor_id', fn($row) => $row->investor_id ?? '-')
                ->addColumn('jenis_investor', fn($row) => $row->jenis_investor ?? '-')
                ->addColumn('status', fn($row) => $row->status ?? '-')


                // ->addColumn('product_catalog', function ($row) {
                //     return $row->user->product_catalog ?? '-';
                // })
                
                ->editColumn('fullname', function ($row) {
                    $url = route('investors.show', $row->id);
                    $name = Str::title($row->user->fullname ?? '-');
                    return '<a href="'.$url.'">'.e($name).'</a>';
                })
                
                ->addColumn('action', function ($investor) {
                    $buttons = '';
                    if (auth()->user()->can('ubah data investor')) {
                        $buttons .= '<a href="' . route('investors.edit', $investor->id) . '" class="btn btn-icon btn-sm btn-dark me-1" title="Ubah">
                                        <i class="ti ti-edit"></i>
                                    </a>';
                    }
                    if (auth()->user()->can('lihat data investor')) {
                        $buttons .= '<a href="' . route('investors.show', $investor->id) . '" class="btn btn-icon btn-sm btn-dark me-1" title="Lihat">
                                        <i class="ti ti-eye"></i>
                                    </a>';

                    }
                    if (auth()->user()->can('hapus data investor')) {
                        $buttons .= '<button data-id="' . $investor->id . '" class="btn btn-icon btn-sm btn-dark delete-investor" title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>';
                    }
                    return $buttons;
                })
                ->rawColumns(['fullname', 'action'])
                ->make(true);
        }

        return view('investors.index');
    }

       public function create()
    {
        $user = auth()->user();
        $religions = Religion::all();
        $provinces = Province::all();
        $roles = Role::all();
        return view('investors.create', compact('user', 'roles', 'religions', 'provinces'));
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
        'npwp' => 'nullable|string|max:30',
        'phone' => 'required|string|max:20',
        'address' => 'nullable|string|max:255',
        'user_province_id' => 'required|exists:provinces,id',
        'user_city_id' => 'required|exists:cities,id',
        'user_district_id' => 'required|exists:districts,id',
        'user_sub_district_id' => 'required|exists:sub_districts,id',
        'user_postal_code_id' => 'required|exists:postal_codes,id',
        'bank_id' => 'nullable|uuid|exists:banks,id',
        'account_number' => 'nullable|string|max:50',
        'account_holder' => 'nullable|string|max:50',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        // --- data investor ---
        'investor_id' => 'required|unique:investors,investor_id',
        'role' => 'required|array',
        'role.*' => 'string|exists:roles,name',
        
    ]);

    // 🔹 Upload foto (jika ada)
    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('photos', $filename, 'public');
        $validated['photo'] = $filename;
    }

    // Jika alamat pengiriman sama dengan domisili user
        

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

        

        // 🔹 Simpan investor
        Investor::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'investor_id' => $validated['investor_id'],
            'photo' => $validated['photo'] ?? null,
        ]);
    });

    return redirect()
        ->route('investors.index')
        ->with('success', 'Data investor berhasil ditambahkan.' .
            (session('new_user_password') ? ' Akun user baru dibuat. Password: ' . session('new_user_password') : '')
        );
}


public function generateinvestorIdAjax()
{
    $lastNumber = Investor::selectRaw("MAX(CAST(SUBSTRING(investor_id, 3) AS INTEGER)) as max_investor_id")->value('max_investor_id');
    $newNumber = ($lastNumber ?? 0) + 1;

    $newinvestor_id = 'I-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    return response()->json([
        'investor_id' => $newinvestor_id
    ]);
}

 public function show(investor $investor)
    {
        $investor->load('user');
        return view('investors.show', [
            'user' => $investor->user,
            'investor' => $investor
        ]);
    }

public function edit($id)
    {
        $investor = Investor::with(['user.roles', 'user.bank'])->findOrFail($id);
        $user = $investor->user;
        $religions = Religion::all();
        $provinces = Province::all();
        $roles = Role::all();
        $selectedRoles = $user->roles->pluck('name')->toArray();
        $cities = City::where('province_id', $user->province_id)->get();
        $districts = District::where('city_id', $user->city_id)->get();
        $subDistricts = SubDistrict::where('district_id', $user->district_id)->get();
        $postalCodes = PostalCode::where('sub_district_id', $user->sub_district_id)->get();
        

        return view('investors.edit', compact('user', 'roles', 'selectedRoles', 'investor',
        'religions', 'provinces', 'cities', 'districts', 'subDistricts', 'postalCodes'));
    }

public function update(Request $request, investor $investor)
{
    $validated = $request->validate([
        // --- data user ---
        'fullname' => 'required|string|max:255',
        'nickname' => 'nullable|string|max:100',
        'gender' => 'required|in:1,2',
        'email' => 'required|email|unique:users,email,' . $investor->user_id,
        'birth_place' => 'nullable|string|max:100',
        'birth_date' => 'nullable|date_format:Y-m-d',
        'identity_number' => 'nullable|string|max:16',
        'religion_id' => 'nullable|exists:religions,id',
        'npwp' => 'nullable|string|max:30',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'user_province_id' => 'required|exists:provinces,id',
        'user_city_id' => 'required|exists:cities,id',
        'user_district_id' => 'required|exists:districts,id',
        'user_sub_district_id' => 'required|exists:sub_districts,id',
        'user_postal_code_id' => 'required|exists:postal_codes,id',
        'bank_id' => 'nullable|uuid|exists:banks,id',
        'account_number' => 'nullable|string|max:50',
        'account_holder' => 'nullable|string|max:50',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        // --- data investor ---
        'investor_id' => 'required|string|max:50|unique:investors,investor_id,' . $investor->id,
        'role' => 'nullable|array',
        'role.*' => 'string|exists:roles,name',
    ]);


    DB::transaction(function () use ($validated, $investor, $request) {
        $user = $investor->user;

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
            // Pastikan minimal masih punya role investor
            if (!$user->hasRole('Investor')) {
                $user->assignRole('Investor');
            }
        }

        


        // 🔹 Update data investor
        $investor->update([
            'investor_id' => $validated['investor_id'],
            'photo' => $validated['photo'] ?? $investor->photo,
        ]);
    });

    return redirect()
        ->route('investors.show', $investor->id)
        ->with('success', 'Data investor berhasil diperbarui.');
}


public function destroy(Investor $investor): JsonResponse
{
    DB::transaction(function () use ($investor) {
        $user = $investor->user;

        // 🔹 Hapus foto investor dari storage kalau ada
        if ($investor->photo && Storage::disk('public')->exists('photos/' . $investor->photo)) {
            Storage::disk('public')->delete('photos/' . $investor->photo);
        }

        // 🔹 Hapus record investor
        $investor->delete();

        // 🔹 Cek user yang terhubung
        if ($user) {
            $roles = $user->roles->pluck('name')->toArray();

            // Kalau hanya punya role "investor"
            if (count($roles) === 1 && in_array('Investor', $roles)) {

                // Hapus juga foto user kalau ada
                if ($user->photo && Storage::disk('public')->exists('photos/' . $user->photo)) {
                    Storage::disk('public')->delete('photos/' . $user->photo);
                }

                // Hapus akun user
                $user->delete();

            } else {
                // Kalau user masih punya role lain, hapus hanya role investor-nya
                $user->removeRole('Investor');
            }
        }
    });

    return response()->json([
        'status' => 'success',
        'message' => 'Data investor berhasil dihapus.'
    ]);
}
    
}
