<?php

namespace App\Http\Middleware;

use App\Helpers\FirestoreHelper;
use App\Models\VendorUsers;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSubscriptionPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $vendorUser = VendorUsers::where('user_id', $user->id)->first();
            if ($vendorUser) {
                $userDoc = FirestoreHelper::getDocument('users/' . $vendorUser->uuid);
                if (!empty($userDoc) && (($userDoc['role'] ?? '') === 'provider')) {
                    return $next($request);
                }
            }

            if ($user->isSubscribed=='false') {
                return redirect()->route('subscription-plan.show');
            }
        }

        return $next($request);
    }
}
