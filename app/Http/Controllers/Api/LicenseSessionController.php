<?
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Support\Facades\Auth;

class LicenseSessionController extends Controller
{
    public function set(Request $request)
    {
        $request->validate(['license_id' => 'required|uuid']);

        $user = Auth::user();

        // super admin bebas, selain itu harus punya akses ke lisensi tsb
        if (!$user->hasRole('Super-Admin')) {
            $allowed = $user->hasRole('Pemilik Lisensi')
                ? $user->licenses()->where('licenses.id', $request->license_id)->exists()
                : optional($user->employee)->licenses()->where('licenses.id', $request->license_id)->exists();

            if (!$allowed) {
                abort(403, 'Anda tidak memiliki akses ke lisensi ini.');
            }
        }

        session(['active_license_id' => $request->license_id]);

        return back()->with('success', 'Lisensi aktif diperbarui.');
    }
}
