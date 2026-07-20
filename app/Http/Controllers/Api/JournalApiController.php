<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{AccountingJournal, License};

class JournalApiController extends Controller
{
    private function resolveLicenseId(?string $licenseId): string
    {
        $user = Auth::user();

        if ($user->hasRole('Super-Admin')) {
            return $licenseId ?? session('active_license_id') ?? License::value('id');
        }

        $owned = $user->hasRole('Pemilik Lisensi')
            ? $user->licenses()->pluck('licenses.id')->all()
            : optional($user->employee)->licenses()->pluck('licenses.id')->all();

        $target = $licenseId ?? session('active_license_id') ?? ($owned[0] ?? null);

        if (!$target || !in_array($target, $owned, true)) {
            abort(403, 'Lisensi tidak valid untuk user.');
        }

        return $target;
    }

    /** versi aman untuk PostgreSQL */
    private function generateNextJournalCode(string $licenseId): string
    {
        $license = License::findOrFail($licenseId);

        // Ambil angka terbesar di belakang pola IJ-<license_id>-
        $last = AccountingJournal::where('license_id', $license->id)
            ->where('journal_code', 'LIKE', 'IJ-' . $license->license_id . '-%')
            ->selectRaw("
                MAX(
                    CAST(
                        REGEXP_REPLACE(journal_code, '^IJ-' || ? || '-', '') AS INTEGER
                    )
                ) AS last_number
            ", [$license->license_id])
            ->value('last_number');

        $next = ($last ?? 0) + 1;
        $code = 'IJ-' . $license->license_id . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);

        // retry kalau bentrok (race condition)
        while (AccountingJournal::where('journal_code', $code)->exists()) {
            $next++;
            $code = 'IJ-' . $license->license_id . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function nextCode(Request $request, ?string $licenseId = null)
    {
        $target = $this->resolveLicenseId($licenseId);

        return response()->json([
            'license_id' => $target,
            'next_code'  => $this->generateNextJournalCode($target),
        ]);
    }
}
