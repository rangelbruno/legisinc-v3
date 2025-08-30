<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ScreenPermission;
use App\Models\User;

class DocsPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissões para Admin acessar documentação usando o modelo correto
        $permissions = [
            [
                'role_name' => 'ADMIN',
                'screen_route' => 'admin.docs.fluxo-proposicoes',
                'screen_name' => 'Fluxo de Proposições - Documentação',
                'screen_module' => 'Administração',
                'can_access' => true
            ]
        ];

        foreach ($permissions as $permissionData) {
            // Verificar se já existe
            $existing = ScreenPermission::where('role_name', $permissionData['role_name'])
                ->where('screen_route', $permissionData['screen_route'])
                ->first();
                
            if ($existing) {
                $this->command->info("Permissão já existe: {$permissionData['role_name']} -> {$permissionData['screen_route']}");
                continue;
            }

            ScreenPermission::setScreenAccess(
                $permissionData['role_name'],
                $permissionData['screen_route'],
                $permissionData['screen_name'],
                $permissionData['screen_module'],
                $permissionData['can_access']
            );

            $this->command->info("Permissão criada: {$permissionData['role_name']} -> {$permissionData['screen_route']}");
        }

        $this->command->info('✅ Permissões de documentação configuradas com sucesso!');
    }
}
