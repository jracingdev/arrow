<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission = null, $routes = null)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super Administrator (role_id=1 ou nome) tem acesso total
        if ($this->isSuperAdministrator($user)) {
            return $next($request);
        }

        // Relatórios: menu libera ambos se houver permission "report"; resolve type em runtime
        if ($permission === 'report') {
            if ($routes === null || $routes === '' || str_contains((string) $routes, '://')) {
                $routes = $request->route('type');
            }
            // Com permission "report", libera sales/tax (mesma regra do menu)
            $role_has_permissions = Permission::where('role_id', $user->role_id)->pluck('permission')->toArray();
            if (in_array('report', array_unique($role_has_permissions), true)) {
                return $next($request);
            }
            abort(403, 'unauthorized access');
        }

        $role_has_permissions = Permission::where('role_id', $user->role_id)->pluck('permission')->toArray();
        $role_has_permissions = array_unique($role_has_permissions);

        if (in_array($permission, $role_has_permissions, true)) {
            if ($routes === null || $routes === '') {
                return $next($request);
            }

            $permission_has_routes = Permission::where('role_id', $user->role_id)
                ->where('permission', $permission)
                ->pluck('routes')
                ->toArray();

            if (in_array($routes, $permission_has_routes, true)) {
                return $next($request);
            }

            abort(403, 'unauthorized access');
        }

        abort(403, 'unauthorized access');
    }

    private function isSuperAdministrator($user): bool
    {
        if ((int) $user->role_id === 1) {
            return true;
        }

        $roleName = Role::where('id', $user->role_id)->value('role_name');

        return $roleName === 'Super Administrator';
    }
}
