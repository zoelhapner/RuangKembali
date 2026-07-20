<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountingAccount;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class AccountingAccountController extends Controller
{

public function index(Request $request)
{
    $user = Auth::user();

    $query = AccountingAccount::with(['parent'])
        ->orderBy('account_code', 'asc');

    if ($user->hasRole('Super-Admin')) {
        // semua data
    } elseif ($user->hasRole('Pemilik Lisensi')) {

        $licenses = optional($user->licenses);

        if ($licenses?->isNotEmpty()) {
            $query->whereIn('license_id', $licenses->pluck('id'));
        } else {
            abort(403, 'Lisensi tidak ditemukan untuk pemilik lisensi.');
        }

    } elseif ($user->hasRole('Akuntan')) {

        $licenses = optional($user->employee)->licenses;

        if ($licenses && $licenses->count() > 0) {
            $query->whereIn('license_id', $licenses->pluck('id'));
        } else {
            abort(403, 'Lisensi tidak ditemukan.');
        }

    } else {
        abort(403, 'Role Tidak diizinkan');
    }

    if (!$user->hasRole('Super-Admin')) {

        $activeLicenseId = session('active_license_id'); // ⬅️ biasanya ini

        if (!$activeLicenseId) {
            abort(403, 'Silakan pilih lisensi aktif terlebih dahulu.');
        }

        $query->where('license_id', $activeLicenseId);
    }

    if ($request->ajax()) {

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('parent_name', function ($row) {
                return optional($row->parent)->account_name;
            })

            ->addColumn('is_parent', function ($row) {
                return $row->is_parent ? 'Ya' : 'Tidak';
            })

            ->addColumn('status', function ($row) {
                return $row->is_active ? 'Aktif' : 'Nonaktif';
            })
            ->editColumn('account_name', function ($row) {
                    $url = route('accounting.edit', $row->id);
                    $name = Str::title($row->account_name ?? '-');
                    return '<a href="'.$url.'">'.e($name).'</a>';
            })

            ->addColumn('aksi', function ($row) {
                $buttons = '';

                if (auth()->user()->can('ubah akun-akuntansi')) {
                    $buttons .= '<a href="' . route('accounting.edit', $row->id) . '" 
                                class="btn btn-icon btn-sm btn-dark me-1">
                                <i class="ti ti-edit"></i></a>';
                }
                //  if (auth()->user()->can('lihat data proyek')) {
                //             $buttons .= '<a href="' . route('projects.show', $row->id) . '" class="btn btn-icon btn-sm btn-dark me-1" title="Lihat">
                //                             <i class="ti ti-eye"></i>
                //                         </a>';

                // }
                if (auth()->user()->can('hapus akun-akuntansi')) {
                    $buttons .= '<button data-id="' . $row->id . '" 
                                class="btn btn-icon btn-sm btn-dark delete-accounts">
                                <i class="ti ti-trash"></i></button>';
                }

                return $buttons;
            })

            ->rawColumns(['aksi', 'account_name'])
            ->make(true);
    }

    return view('accounting.index');
}


    public function create(AccountingAccount $account)
{
    $user = Auth::user();

    if ($user->hasRole('Super-Admin')) {
        // $licenses = License::all();
    } elseif ($user->hasRole('Akuntan')) {
        $licenses = $user->employee?->licenses;

        if (!$licenses || $licenses->count() === 0) {
            abort(403, 'Lisensi tidak ditemukan.');
        }
    } else {
        abort(403, 'Role tidak diizinkan.');
    }
    $categories = config('accounting.categories');
    $subCategories = config('accounting.sub_categories');
    $parentAccounts = AccountingAccount::where('is_parent', true)->get();

    return view('accounting.create', compact('account', 'parentAccounts', 'categories', 'subCategories'));
}


public function store(Request $request)
{
    $request->validate([
        'account_name' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'sub_category' => 'required|string|max:255',
        'initial_balance' => 'nullable|numeric',
        'is_parent' => 'nullable|boolean',
        'parent_id' => 'nullable|uuid|exists:accounting_accounts,id',
        'person_type' => 'nullable|string',
    ]);

    $isParent = $request->boolean('is_parent');

    if (!$isParent && !$request->parent_id) {
        return back()->withErrors([
            'parent_id' => 'Akun child wajib punya akun induk'
        ]);
    }

    $code = $this->generateAccountCode(
        $request->category,
        $request->parent_id
    );
    $licenseId = config('app.license_id');

    if (AccountingAccount::where('account_code', $code)->exists()) {
        return back()->withErrors([
            'account_code' => 'Kode akun sudah digunakan, silakan ulangi.'
        ]);
    }

    AccountingAccount::create([
        'id' => Str::uuid(),
        'license_id'   => $licenseId,
        'account_code' => $code,
        'account_name' => $request->account_name,
        'category' => $request->category,
        'sub_category' => $request->sub_category,
        'initial_balance' => $isParent ? null : $request->initial_balance,
        'is_parent' => $isParent,
        'parent_id' => $isParent ? null : $request->parent_id,
        'person_type' => $request->person_type,
        'is_active' => true,
    ]);

    return redirect()->route('accounting.index')
        ->with('success', 'Akun berhasil ditambahkan.');
}

    public function edit(AccountingAccount $account)
    {
        $user = Auth::user();

    if ($user->hasRole('Super-Admin')) {
        // $licenses = License::all();
    } elseif ($user->hasRole('Akuntan')) {
        $licenses = $user->employee?->licenses;

        if (!$licenses || $licenses->count() === 0) {
            abort(403, 'Lisensi tidak ditemukan.');
        }
    } else {
        abort(403, 'Role tidak diizinkan.');
    }

    $categories = config('accounting.categories');
    $subCategories = config('accounting.sub_categories');
    $parentAccounts = AccountingAccount::where('is_parent', true)->get();

    return view('accounting.edit', compact('account', 'parentAccounts', 'categories', 'subCategories'));

    }

    public function update(Request $request, AccountingAccount $account)
{
    $licenseId = config('app.license_id');

    $request->validate([
        'account_code' => [
            'required',
            'string',
            'max:255',
            Rule::unique('accounting_accounts', 'account_code')
                ->ignore($account->id),
        ],

        'account_name' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'sub_category' => 'required|string|max:255',
        'initial_balance' => 'nullable|numeric',
        'is_parent' => 'nullable|boolean',

        'parent_id' => [
            'nullable',
            'uuid',
            Rule::exists('accounting_accounts', 'id')
                ->where('license_id', $licenseId),
        ],

        'person_type' => 'nullable|string',
    ]);

    $isParent = $request->boolean('is_parent');

    if ($isParent && $request->parent_id) {
        return back()->withErrors([
            'parent_id' => 'Akun parent tidak boleh memiliki induk.'
        ]);
    }

    // Child wajib punya parent
    if (!$isParent && !$request->parent_id) {
        return back()->withErrors([
            'parent_id' => 'Akun child wajib punya akun induk.'
        ]);
    }

    if ($request->parent_id == $account->id) {
        return back()->withErrors([
            'parent_id' => 'Tidak boleh memilih diri sendiri sebagai parent.'
        ]);
    }

    $parent = null;

    if ($request->parent_id) {

        $parent = AccountingAccount::where('license_id', $licenseId)
            ->find($request->parent_id);

        if ($parent && $parent->category !== $request->category) {
            return back()->withErrors([
                'parent_id' => 'Kategori parent harus sama dengan akun.'
            ]);
        }

        if ($parent && $parent->parent_id == $account->id) {
            return back()->withErrors([
                'parent_id' => 'Circular parent tidak diperbolehkan.'
            ]);
        }
    }

    $account->update([
        'account_code' => $request->account_code,
        'account_name' => $request->account_name,
        'category' => $request->category,
        'sub_category' => $request->sub_category,
        'initial_balance' => $isParent ? null : ($request->initial_balance ?? 0),
        'is_parent' => $isParent,
        'parent_id' => $isParent ? null : $request->parent_id,
        'is_active' => true,
        'person_type' => $request->person_type,
        'license_id' => $licenseId,
    ]);

    return redirect()
        ->route('accounting.index')
        ->with('success', 'Akun berhasil diubah.');
}

    public function destroy($id)
    {
        $account = AccountingAccount::findOrFail($id);
        $account->delete();

        return response()->json(['status' => 'success']);
    }
public function generateCode(Request $request)
{
    $category = $request->category;
    $parentId = $request->parent_id;

    if (!$category) {
        return response()->json(['code' => '-']);
    }

    $code = $this->generateAccountCode($category, $parentId);

    return response()->json(['code' => $code]);
}

    private function getCategoryPrefix($category)
{
    return match (strtoupper($category)) {
        'AKTIVA' => '1',
        'KEWAJIBAN' => '2',
        'EKUITAS' => '3',
        'PENDAPATAN' => '4',
        'BEBAN' => '5',
        default => '0',
    };
}

private function generateAccountCode($category, $parentId = null)
{
    // LEVEL 1 → KATEGORI
    if (!$parentId) {

        $prefix = $this->getCategoryPrefix($category);

        // cek apakah root sudah ada
        $root = AccountingAccount::where('account_code', "{$prefix}-000-000")->first();

        if ($root) {
            // berarti ini level 2
            return $this->generateAccountCode($category, $root->id);
        }

        // kalau belum ada → buat root
        return "{$prefix}-000-000";
    }

    $parent = AccountingAccount::findOrFail($parentId);
    $parts = explode('-', $parent->account_code);

    // LEVEL 2 → SUB KATEGORI
    if ($parts[1] === '000') {

        $last = AccountingAccount::where('parent_id', $parentId)
            ->orderBy('account_code', 'desc')
            ->first();

        if ($last) {
            $lastMid = (int) explode('-', $last->account_code)[1];
            $nextMid = str_pad($lastMid + 10, 3, '0', STR_PAD_LEFT);
        } else {
            $nextMid = '100';
        }

        return "{$parts[0]}-{$nextMid}-000";
    }

    // LEVEL 3 → AKUN DETAIL
    $last = AccountingAccount::where('parent_id', $parentId)
        ->orderBy('account_code', 'desc')
        ->first();

    if ($last) {
        $lastEnd = (int) substr($last->account_code, -3);
        $nextEnd = str_pad($lastEnd + 1, 3, '0', STR_PAD_LEFT);
    } else {
        $nextEnd = '001';
    }

    return "{$parts[0]}-{$parts[1]}-{$nextEnd}";
}

private function generateSubCategoryCode($subCategory)
{
    return match ($subCategory) {
        'Aset Lancar - Kas & Bank' => '100',
        'Aset Lancar - Piutang' => '110',
        'Aset Lancar - Persediaan Barang' => '120',
        'Aset Tetap' => '200',

        'Hutang' => '100',
        'Pajak' => '200',

        'Modal' => '100',

        'Pendapatan Desain' => '100',
        'Pendapatan RAB' => '200',
        'Pendapatan Build' => '300',

        'Biaya Desain' => '100',
        'Biaya RAB' => '200',
        'Biaya Build' => '300',

        default => '999',
    };
}

}
