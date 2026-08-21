<?php

namespace App\Http\Middleware;

use App\Helpers\FirestoreHelper;
use App\Models\VendorUsers;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FirebaseMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        $maintenance_settings = FirestoreHelper::getDocument('settings/maintenance_settings');
        if (empty($maintenance_settings)) {
            return $next($request);
        }

        if (Auth::check()) {
            $vendorUser = VendorUsers::where('user_id', Auth::id())->first();
            $role = 'vendor';
            if ($vendorUser) {
                $userDoc = FirestoreHelper::getDocument('users/' . $vendorUser->uuid);
                if (!empty($userDoc) && !empty($userDoc['role'])) {
                    $role = $userDoc['role'];
                }
            }
            if ($role === 'provider') {
                if (!empty($maintenance_settings['isMaintenanceModeForProvider'])) {
                    return response()->view('maintenance');
                }
                return $next($request);
            }
            if (!empty($maintenance_settings['isMaintenanceModeForVendor'])) {
                return response()->view('maintenance');
            }
            return $next($request);
        }

        return $next($request);
    }
}
