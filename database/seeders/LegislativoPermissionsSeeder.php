<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ScreenPermission;

class LegislativoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $roleName = 'LEGISLATIVO';
        
        // Permissões para o módulo Dashboard
        ScreenPermission::updateOrCreate(
            ['role_name' => $roleName, 'screen_route' => 'dashboard'],
            [
                'screen_name' => 'Dashboard',
                'screen_module' => 'dashboard',
                'can_access' => true,
            ]
        );

        // Permissões para o módulo Proposições
        // Nota: Legislativo não pode criar proposições, apenas revisar e processar
        $proposicoesPermissions = [
            'proposicoes.show' => 'Visualizar Proposição',
            'proposicoes.legislativo.index' => 'Lista Legislativo',
            'proposicoes.legislativo.editar' => 'Editar Legislativo',
            'proposicoes.legislativo.salvar-edicao' => 'Salvar Edição Legislativo',
            'proposicoes.legislativo.enviar-parlamentar' => 'Enviar para Parlamentar',
            'proposicoes.revisar' => 'Revisar Proposições',
            'proposicoes.revisar.show' => 'Revisar Proposição',
            'proposicoes.salvar-analise' => 'Salvar Análise',
            'proposicoes.aprovar' => 'Aprovar Proposição',
            'proposicoes.devolver' => 'Devolver Proposição',
            'proposicoes.relatorio-legislativo' => 'Relatório Legislativo',
            'proposicoes.aguardando-protocolo' => 'Aguardando Protocolo',
        ];

        foreach ($proposicoesPermissions as $route => $name) {
            ScreenPermission::updateOrCreate(
                ['role_name' => $roleName, 'screen_route' => $route],
                [
                    'screen_name' => $name,
                    'screen_module' => 'proposicoes',
                    'can_access' => true,
                ]
            );
        }

        // Permissões para visualizar parlamentares (necessário para trabalhar com proposições)
        $parlamentaresPermissions = [
            'parlamentares.index' => 'Lista de Parlamentares',
            'parlamentares.show' => 'Visualizar Parlamentar',
        ];

        foreach ($parlamentaresPermissions as $route => $name) {
            ScreenPermission::updateOrCreate(
                ['role_name' => $roleName, 'screen_route' => $route],
                [
                    'screen_name' => $name,
                    'screen_module' => 'parlamentares',
                    'can_access' => true,
                ]
            );
        }

        // Permissões básicas do sistema
        $systemPermissions = [
            'user.profile' => 'Meu Perfil',
            'user.update-last-access' => 'Atualizar Último Acesso',
        ];

        foreach ($systemPermissions as $route => $name) {
            ScreenPermission::updateOrCreate(
                ['role_name' => $roleName, 'screen_route' => $route],
                [
                    'screen_name' => $name,
                    'screen_module' => 'sistema',
                    'can_access' => true,
                ]
            );
        }

        $this->command->info('Permissões para perfil LEGISLATIVO configuradas com sucesso!');
    }
}