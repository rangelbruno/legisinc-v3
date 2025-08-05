<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScreenPermission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MenuPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Iniciando configuração de permissões de menu...');
        
        DB::beginTransaction();
        
        try {
            // Configurar permissões para cada perfil
            $this->configureAdminPermissions();
            $this->configureParlamentarPermissions();
            $this->configureLegislativoPermissions();
            $this->configureProtocoloPermissions();
            $this->configureExpedientePermissions();
            $this->configureAssessorJuridicoPermissions();
            
            DB::commit();
            $this->command->info('✅ Permissões de menu configuradas com sucesso!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Erro ao configurar permissões: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Configurar permissões do ADMIN (acesso total)
     */
    private function configureAdminPermissions(): void
    {
        $this->command->info('👤 Configurando permissões do ADMIN...');
        
        $permissions = [
            // Dashboard
            'dashboard' => true,
            
            // Parlamentares
            'parlamentares.index' => true,
            'parlamentares.create' => true,
            'parlamentares.show' => true,
            'parlamentares.edit' => true,
            'parlamentares.mesa-diretora' => true,
            
            // Partidos
            'partidos.index' => true,
            'partidos.create' => true,
            'partidos.show' => true,
            'partidos.edit' => true,
            
            // Proposições
            'proposicoes.criar' => true,
            'proposicoes.minhas-proposicoes' => true,
            'proposicoes.assinatura' => true,
            'proposicoes.protocolar' => true,
            'proposicoes.show' => true,
            'proposicoes.edit' => true,
            'proposicoes.legislativo.index' => true,
            'proposicoes.relatorio-legislativo' => true,
            'proposicoes.aguardando-protocolo' => true,
            'proposicoes.protocolos-hoje' => true,
            'proposicoes.estatisticas-protocolo' => true,
            'proposicoes.efetivar-protocolo' => true,
            'proposicoes.iniciar-tramitacao' => true,
            
            // Comissões
            'comissoes.index' => true,
            'comissoes.create' => true,
            'comissoes.show' => true,
            'comissoes.edit' => true,
            'comissoes.minhas-comissoes' => true,
            
            // Sessões
            'admin.sessions.index' => true,
            'admin.sessions.create' => true,
            'admin.sessions.show' => true,
            'admin.sessions.edit' => true,
            'sessoes.agenda' => true,
            'sessoes.atas' => true,
            
            // Votações
            'votacoes.index' => true,
            'votacoes.create' => true,
            'votacoes.show' => true,
            'votacoes.edit' => true,
            
            // Usuários
            'usuarios.index' => true,
            'usuarios.create' => true,
            'usuarios.show' => true,
            'usuarios.edit' => true,
            
            // Perfil
            'profile.show' => true,
            'profile.edit' => true,
            
            // Administração
            'admin.parametros' => true,
            'admin.backup' => true,
            'admin.logs' => true,
        ];

        $this->updatePermissions(User::PERFIL_ADMIN, $permissions);
    }

    /**
     * Configurar permissões do PARLAMENTAR
     */
    private function configureParlamentarPermissions(): void
    {
        $this->command->info('👤 Configurando permissões do PARLAMENTAR...');
        
        $permissions = [
            // Dashboard
            'dashboard' => true,
            
            // Proposições (criar e gerenciar próprias)
            'proposicoes.criar' => true,
            'proposicoes.minhas-proposicoes' => true,
            'proposicoes.assinatura' => true,
            'proposicoes.show' => true,
            
            // Comissões (ver lista e suas comissões)
            'comissoes.index' => true,
            'comissoes.minhas-comissoes' => true,
            'comissoes.show' => true,
            
            // Perfil
            'profile.show' => true,
            'profile.edit' => true,
            
            // Negados
            'parlamentares.index' => false,
            'partidos.index' => false,
            'admin.sessions.index' => false,
            'votacoes.index' => false,
            'usuarios.index' => false,
            'admin.parametros' => false,
        ];

        $this->updatePermissions(User::PERFIL_PARLAMENTAR, $permissions);
    }

    /**
     * Configurar permissões do LEGISLATIVO
     */
    private function configureLegislativoPermissions(): void
    {
        $this->command->info('👤 Configurando permissões do LEGISLATIVO...');
        
        $permissions = [
            // Dashboard
            'dashboard' => true,
            
            // Parlamentares (apenas visualização para contexto)
            'parlamentares.index' => true,
            'parlamentares.show' => true,
            
            // Proposições (análise e revisão)
            'proposicoes.show' => true,
            'proposicoes.legislativo.index' => true,
            'proposicoes.relatorio-legislativo' => true,
            'proposicoes.aguardando-protocolo' => true,
            
            // Perfil
            'profile.show' => true,
            'profile.edit' => true,
            
            // Negados (não cria proposições)
            'proposicoes.criar' => false,
            'proposicoes.minhas-proposicoes' => false,
            'proposicoes.assinatura' => false,
            'partidos.index' => false,
            'admin.sessions.index' => false,
            'votacoes.index' => false,
            'comissoes.index' => false,
            'usuarios.index' => false,
        ];

        $this->updatePermissions(User::PERFIL_LEGISLATIVO, $permissions);
    }

    /**
     * Configurar permissões do PROTOCOLO
     */
    private function configureProtocoloPermissions(): void
    {
        $this->command->info('👤 Configurando permissões do PROTOCOLO...');
        
        $permissions = [
            // Dashboard
            'dashboard' => true,
            
            // Proposições (protocolo e tramitação)
            'proposicoes.show' => true,
            'proposicoes.aguardando-protocolo' => true,
            'proposicoes.protocolar' => true,
            'proposicoes.protocolos-hoje' => true,
            'proposicoes.estatisticas-protocolo' => true,
            'proposicoes.efetivar-protocolo' => true,
            'proposicoes.iniciar-tramitacao' => true,
            
            // Perfil
            'profile.show' => true,
            'profile.edit' => true,
            
            // Negados
            'parlamentares.index' => false,
            'partidos.index' => false,
            'proposicoes.criar' => false,
            'proposicoes.minhas-proposicoes' => false,
            'proposicoes.assinatura' => false,
            'admin.sessions.index' => false,
            'votacoes.index' => false,
            'comissoes.index' => false,
            'usuarios.index' => false,
        ];

        $this->updatePermissions(User::PERFIL_PROTOCOLO, $permissions);
    }

    /**
     * Configurar permissões do EXPEDIENTE
     */
    private function configureExpedientePermissions(): void
    {
        $this->command->info('👤 Configurando permissões do EXPEDIENTE...');
        
        $permissions = [
            // Dashboard
            'dashboard' => true,
            
            // Proposições (expediente)
            'proposicoes.show' => true,
            'proposicoes.legislativo.index' => true, // Proposições Protocoladas
            'proposicoes.relatorio-legislativo' => true, // Relatório
            
            // Sessões (organizar pautas)
            'admin.sessions.index' => true,
            'admin.sessions.show' => true,
            'sessoes.agenda' => true,
            'sessoes.atas' => true,
            
            // Perfil
            'profile.show' => true,
            'profile.edit' => true,
            
            // Negados
            'parlamentares.index' => false,
            'partidos.index' => false,
            'proposicoes.criar' => false,
            'proposicoes.minhas-proposicoes' => false,
            'proposicoes.assinatura' => false,
            'proposicoes.protocolar' => false,
            'votacoes.index' => false,
            'comissoes.index' => false,
            'usuarios.index' => false,
        ];

        $this->updatePermissions(User::PERFIL_EXPEDIENTE, $permissions);
    }

    /**
     * Configurar permissões do ASSESSOR_JURIDICO
     */
    private function configureAssessorJuridicoPermissions(): void
    {
        $this->command->info('👤 Configurando permissões do ASSESSOR_JURIDICO...');
        
        $permissions = [
            // Dashboard
            'dashboard' => true,
            
            // Proposições (análise jurídica)
            'proposicoes.show' => true,
            'proposicoes.legislativo.index' => true,
            'proposicoes.parecer-juridico' => true,
            'proposicoes.emitir-parecer' => true,
            
            // Parlamentares (contexto para pareceres)
            'parlamentares.index' => true,
            'parlamentares.show' => true,
            
            // Perfil
            'profile.show' => true,
            'profile.edit' => true,
            
            // Negados
            'proposicoes.criar' => false,
            'proposicoes.minhas-proposicoes' => false,
            'proposicoes.assinatura' => false,
            'proposicoes.protocolar' => false,
            'partidos.index' => false,
            'admin.sessions.index' => false,
            'votacoes.index' => false,
            'comissoes.index' => false,
            'usuarios.index' => false,
        ];

        $this->updatePermissions(User::PERFIL_ASSESSOR_JURIDICO, $permissions);
    }

    /**
     * Atualizar permissões para um perfil
     */
    private function updatePermissions(string $role, array $permissions): void
    {
        foreach ($permissions as $route => $hasAccess) {
            ScreenPermission::updateOrCreate(
                [
                    'role_name' => $role,
                    'screen_route' => $route,
                ],
                [
                    'screen_name' => $this->getDescriptionFromRoute($route),
                    'can_access' => $hasAccess,
                    'can_create' => $hasAccess,
                    'can_edit' => $hasAccess,
                    'can_delete' => $hasAccess,
                    'screen_module' => $this->getModuleFromRoute($route),
                ]
            );
        }
        
        $this->command->line("  ✓ Configuradas " . count($permissions) . " permissões para $role");
    }

    /**
     * Obter módulo a partir da rota
     */
    private function getModuleFromRoute(string $route): string
    {
        $modules = [
            'dashboard' => 'dashboard',
            'parlamentares' => 'parlamentares',
            'partidos' => 'partidos',
            'proposicoes' => 'proposicoes',
            'comissoes' => 'comissoes',
            'sessions' => 'sessoes',
            'sessoes' => 'sessoes',
            'votacoes' => 'votacoes',
            'usuarios' => 'usuarios',
            'profile' => 'perfil',
            'admin' => 'administracao',
        ];

        $prefix = explode('.', $route)[0];
        return $modules[$prefix] ?? 'sistema';
    }

    /**
     * Obter descrição a partir da rota
     */
    private function getDescriptionFromRoute(string $route): string
    {
        $descriptions = [
            'dashboard' => 'Dashboard',
            'parlamentares.index' => 'Lista de Parlamentares',
            'parlamentares.create' => 'Criar Parlamentar',
            'parlamentares.show' => 'Visualizar Parlamentar',
            'parlamentares.edit' => 'Editar Parlamentar',
            'parlamentares.mesa-diretora' => 'Mesa Diretora',
            'partidos.index' => 'Lista de Partidos',
            'partidos.create' => 'Criar Partido',
            'partidos.show' => 'Visualizar Partido',
            'partidos.edit' => 'Editar Partido',
            'proposicoes.criar' => 'Criar Proposição',
            'proposicoes.minhas-proposicoes' => 'Minhas Proposições',
            'proposicoes.assinatura' => 'Assinatura',
            'proposicoes.protocolar' => 'Protocolar',
            'proposicoes.show' => 'Visualizar Proposição',
            'proposicoes.edit' => 'Editar Proposição',
            'proposicoes.legislativo.index' => 'Proposições Recebidas',
            'proposicoes.relatorio-legislativo' => 'Relatório Legislativo',
            'proposicoes.aguardando-protocolo' => 'Aguardando Protocolo',
            'proposicoes.protocolos-hoje' => 'Protocolos Hoje',
            'proposicoes.estatisticas-protocolo' => 'Estatísticas de Protocolo',
            'proposicoes.efetivar-protocolo' => 'Efetivar Protocolo',
            'proposicoes.iniciar-tramitacao' => 'Iniciar Tramitação',
            'proposicoes.parecer-juridico' => 'Parecer Jurídico',
            'proposicoes.emitir-parecer' => 'Emitir Parecer',
            'comissoes.index' => 'Lista de Comissões',
            'comissoes.create' => 'Criar Comissão',
            'comissoes.show' => 'Visualizar Comissão',
            'comissoes.edit' => 'Editar Comissão',
            'comissoes.minhas-comissoes' => 'Minhas Comissões',
            'admin.sessions.index' => 'Lista de Sessões',
            'admin.sessions.create' => 'Criar Sessão',
            'admin.sessions.show' => 'Visualizar Sessão',
            'admin.sessions.edit' => 'Editar Sessão',
            'sessoes.agenda' => 'Agenda',
            'sessoes.atas' => 'Atas',
            'votacoes.index' => 'Lista de Votações',
            'votacoes.create' => 'Criar Votação',
            'votacoes.show' => 'Visualizar Votação',
            'votacoes.edit' => 'Editar Votação',
            'usuarios.index' => 'Lista de Usuários',
            'usuarios.create' => 'Criar Usuário',
            'usuarios.show' => 'Visualizar Usuário',
            'usuarios.edit' => 'Editar Usuário',
            'profile.show' => 'Visualizar Perfil',
            'profile.edit' => 'Editar Perfil',
            'admin.parametros' => 'Parâmetros',
            'admin.backup' => 'Backup',
            'admin.logs' => 'Logs',
        ];

        return $descriptions[$route] ?? ucfirst(str_replace(['.', '_', '-'], ' ', $route));
    }
}