<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
public function index(Request $request)
{
    if ($request->ajax()) {

        $query = Permission::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($permission) {
                return optional($permission->created_at)->translatedFormat('d F Y H:i');
            })
            ->addColumn('actions', function ($permission) {

                $buttons = '';

                if (auth()->user()->can('ubah data menu')) {
                    $buttons .= '<a href="' . route('permissions.edit', $permission->id) . '" class="btn btn-icon btn-sm btn-warning me-1" title="Ubah">
                        <i class="ti ti-edit"></i>
                    </a>';
                }

                if (auth()->user()->can('hapus data menu')) {
                    $buttons .= '<button
                        " class="btn btn-icon btn-sm btn-danger delete-permissions" title="Hapus"
                        data-id="'.$permission->id.'">
                        <i class="ti ti-trash"></i>
                    </button>';
                }

                return $buttons;
            })

            ->rawColumns(['actions'])
            ->make(true);
    }

    return view('permissions.index');
}

public function create()
{
    return view('permissions.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|unique:permissions,name',
        'guard_name' => 'required',
        'modules' => 'nullable'
    ]);

    Permission::create([
        'id' => Str::uuid(),
        'name' => $request->name,
        'guard_name' => 'web',
        'modules' => $request->modules,
    ]);

    return redirect()
        ->route('permissions.index')
        ->with('success','Permission berhasil ditambahkan.');
}


public function edit(Permission $permission)
{
    return view('permissions.edit', compact('permission'));
}

public function update(Request $request, Permission $permission)
{
    $request->validate([
        'name' => 'required|unique:permissions,name,'.$permission->id,
        'guard_name' => 'required',
        'modules' => 'nullable'
    ]);

    $permission->update([
        'name' => $request->name,
        'guard_name' => 'web',
        'modules' => $request->modules,
    ]);

    return redirect()
        ->route('permissions.index')
        ->with('success','Permission berhasil diubah.');
}
    public function destroy(Permission $permission) 
    {
    
        if ($permission) {
            $permission->delete();
            return response()->json(['status' => 'success', 'message' => 'User deleted successfully']);
        }

        return response()->json(['status' => 'failed', 'message' => 'Unable to delete']);
    }
}
