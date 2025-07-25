<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProposicaoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar permissões específicas para proposições
        $permissions = [
            // Permissões do Parlamentar
            'proposicoes.create' => 'Criar proposições',
            'proposicoes.edit_own' => 'Editar próprias proposições',
            'proposicoes.sign' => 'Assinar proposições',
            'proposicoes.view_own' => 'Visualizar próprias proposições',
            'proposicoes.send_legislative' => 'Enviar para análise legislativa',
            'proposicoes.correct' => 'Corrigir proposições devolvidas',
            
            // Permissões do Legislativo
            'proposicoes.review' => 'Revisar proposições',
            'proposicoes.approve' => 'Aprovar proposições',
            'proposicoes.reject' => 'Devolver proposições para correção',
            'proposicoes.view_all' => 'Visualizar todas as proposições',
            'proposicoes.reports' => 'Visualizar relatórios legislativos',
            
            // Permissões do Protocolo
            'proposicoes.protocol' => 'Protocolar proposições',
            'proposicoes.distribute' => 'Distribuir para comissões',
            'proposicoes.statistics' => 'Visualizar estatísticas de protocolo',
            'proposicoes.start_process' => 'Iniciar tramitação',
            
            // Permissões Administrativas
            'proposicoes.admin' => 'Administrar sistema de proposições',
            'proposicoes.force_edit' => 'Editar qualquer proposição',
            'proposicoes.delete' => 'Excluir proposições',
            'proposicoes.restore' => 'Restaurar proposições',
        ];

        // Criar as permissões
        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
        }

        // Buscar ou criar roles
        $parlamentar = Role::firstOrCreate(['name' => 'PARLAMENTAR']);
        $legislativo = Role::firstOrCreate(['name' => 'LEGISLATIVO']);  
        $protocolo = Role::firstOrCreate(['name' => 'PROTOCOLO']);
        $admin = Role::firstOrCreate(['name' => 'ADMIN']);

        // Atribuir permissões aos roles

        // PARLAMENTAR - Permissões padrão: Criar Proposição, Minhas Proposições e Assinatura
        $parlamentar->syncPermissions([
            'proposicoes.create',           // Criar Proposição
            'proposicoes.edit_own',
            'proposicoes.sign',             // Assinatura
            'proposicoes.view_own',         // Minhas Proposições  
            'proposicoes.send_legislative',
            'proposicoes.correct',
        ]);

        // LEGISLATIVO - Pode revisar, aprovar/rejeitar e ver relatórios
        $legislativo->syncPermissions([
            'proposicoes.review',
            'proposicoes.approve',
            'proposicoes.reject',
            'proposicoes.view_all',
            'proposicoes.reports',
            'proposicoes.protocol', // Pode também protocolar
            'proposicoes.distribute',
            'proposicoes.statistics',
            'proposicoes.start_process',
        ]);

        // PROTOCOLO - Pode protocolar e distribuir
        $protocolo->syncPermissions([
            'proposicoes.protocol',
            'proposicoes.distribute',
            'proposicoes.statistics',
            'proposicoes.start_process',
            'proposicoes.view_all',
        ]);

        // ADMIN - Tem todas as permissões
        $admin->syncPermissions(Permission::all());

        $this->command->info('Permissões de proposições criadas e atribuídas com sucesso!');
    }
}