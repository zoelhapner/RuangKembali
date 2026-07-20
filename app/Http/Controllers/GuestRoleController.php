<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuestRoleController extends Controller
{
    public function activate($role)
    {
        $user = auth()->user();

        // Pastikan hanya Guest yang bisa ganti
        if (!$user->hasRole('Guest')) {
            return redirect()->route('guest.complete-profile')->with('error', 'Anda sudah memiliki peran aktif.');
        }

        if ($role === 'customer') {
            $user->syncRoles(['Customer']);
            return redirect()->route('customer.dashboard')
                ->with('success', 'Selamat, Anda sekarang berperan sebagai Customer!');
        }

        if ($role === 'affiliator') {
            $user->syncRoles(['Affiliator']);
            return redirect()->route('affiliator.dashboard')
                ->with('success', 'Selamat, Anda sekarang berperan sebagai Affiliator!');
        }

        return redirect()->route('guest.dashboard')->with('error', 'Peran tidak dikenali.');
    }
}
