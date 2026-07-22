<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountingAccount;
use App\Models\Customer;
use App\Models\Team;
use App\Models\Partner;

class AjaxController extends Controller
{
    public function getAccounts()
    {
        $licenseId = config('app.license_id');

        $accounts = AccountingAccount::where('license_id', $licenseId)
            ->where('is_parent', false)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get([
                'id',
                'account_code',
                'account_name',
                'person_type'
            ]);

        return response()->json($accounts);
    }

    public function getMembers()
    {
        $customers = Customer::with('user')
            ->get()
            ->map(function ($cus) {
                return [
                    'id'   => $cus->id,
                    'name' => $cus->user?->fullname ?? '-',
                ];
            });

        return response()->json($customers);
    }

    public function getTeams()
    {
        $teams = Team::with('user')
            ->get()
            ->map(function ($emp) {
                return [
                    'id'   => $emp->id,
                    'name' => $emp->user?->fullname ?? '-',
                ];
            });

        return response()->json($teams);
    }

    public function getPartners()
    {
        $partners = Partner::with('user')
            ->get()
            ->map(function ($work) {
                return [
                    'id'   => $work->id,
                    'name' => $work->user?->fullname ?? '-',
                ];
            });

        return response()->json($partners);
    }
}