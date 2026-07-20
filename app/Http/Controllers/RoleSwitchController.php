<?php

namespace App\Http\Controllers;

use App\Helpers\ActiveRole;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleSwitchController extends Controller
{
    public function switch(Request $request)
{
    $request->validate([
        'role_id' => 'required|uuid|exists:roles,id'
    ]);

    $user = auth()->user();

    // pastikan user punya role tsb
    if (!$user->roles->contains('id', $request->role_id)) {
        return back()->with('error', 'Anda tidak memiliki role tersebut.');
    }

    $user->active_role = $request->role_id;
    $user->save();

    return back()->with('success', 'Role berhasil diganti!');
}

}
