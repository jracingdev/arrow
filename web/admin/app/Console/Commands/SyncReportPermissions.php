<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;

class SyncReportPermissions extends Command
{
    protected $signature = 'permissions:sync-reports {--role=1 : Role ID (default Super Administrator)}';

    protected $description = 'Garante permissões report/sales e report/tax para o role informado';

    public function handle(): int
    {
        $roleId = (int) $this->option('role');
        $role = Role::find($roleId);

        if (!$role) {
            $this->error("Role {$roleId} não encontrado.");
            return 1;
        }

        $routes = ['sales', 'tax'];
        $created = 0;

        foreach ($routes as $route) {
            $exists = Permission::where('role_id', $roleId)
                ->where('permission', 'report')
                ->where('routes', $route)
                ->exists();

            if (!$exists) {
                Permission::create([
                    'role_id' => $roleId,
                    'permission' => 'report',
                    'routes' => $route,
                ]);
                $created++;
                $this->info("Criado: report / {$route} para role {$role->role_name} (#{$roleId})");
            } else {
                $this->line("Já existe: report / {$route}");
            }
        }

        $this->info("Concluído. Novas permissões: {$created}");
        return 0;
    }
}
