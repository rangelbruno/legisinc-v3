<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DynamicPermissionService;

class ApplyDefaultPermissions extends Command
{
    protected $signature = 'permissions:apply-defaults {role?}';
    protected $description = 'Apply default permissions for a role or all roles';

    public function handle(DynamicPermissionService $permissionService)
    {
        $role = $this->argument('role');
        
        if ($role) {
            $this->info("Applying default permissions for role: $role");
            $result = $permissionService->applyDefaultPermissions($role);
            
            if ($result) {
                $this->info("✓ Default permissions applied successfully for $role");
            } else {
                $this->error("✗ Failed to apply default permissions for $role");
            }
        } else {
            $this->info("Applying default permissions for all roles...");
            $results = $permissionService->initializeDefaultPermissions();
            
            foreach ($results as $roleName => $success) {
                if ($success) {
                    $this->info("✓ $roleName - Default permissions applied");
                } else {
                    $this->error("✗ $roleName - Failed to apply permissions");
                }
            }
        }
        
        return Command::SUCCESS;
    }
}