<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScreenPermission;

class ParlamentarScreenPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Configurar permissões de tela padrão para PARLAMENTAR
        // Foco: Criar Proposição, Minhas Proposições e Assinatura
        $proposicoesRoutes = [
            ['route' => 'proposicoes.criar', 'name' => 'Criar Proposição', 'access' => true],
            ['route' => 'proposicoes.minhas-proposicoes', 'name' => 'Minhas Proposições', 'access' => true],
            ['route' => 'proposicoes.assinatura', 'name' => 'Assinatura', 'access' => true],
            ['route' => 'proposicoes.historico-assinaturas', 'name' => 'Histórico de Assinaturas', 'access' => true],
        ];

        foreach ($proposicoesRoutes as $routeData) {
            ScreenPermission::updateOrCreate(
                [
                    'role_name' => 'PARLAMENTAR',
                    'screen_route' => $routeData['route'],
                    'screen_module' => 'proposicoes'
                ],
                [
                    'screen_name' => $routeData['name'],
                    'can_access' => $routeData['access']
                ]
            );
        }

        $this->command->info('Permissões de tela para PARLAMENTAR configuradas:');
        $this->command->info('✅ Criar Proposição');
        $this->command->info('✅ Minhas Proposições');
        $this->command->info('✅ Assinatura');
        $this->command->info('✅ Histórico de Assinaturas');
    }
}