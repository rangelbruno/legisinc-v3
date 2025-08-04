<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;

class ConfigureAdminPermissions extends Command
{
    protected $signature = 'permissions:configure-admin';
    protected $description = 'Configura as permissões para o perfil ADMIN com acesso total';

    public function handle()
    {
        $this->info('Configurando permissões para o perfil ADMIN...');

        $permissions = [
            // Dashboard - permitido
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard', 'access' => true],
            
            // Parlamentares - acesso total
            ['route' => 'parlamentares.index', 'name' => 'Lista de Parlamentares', 'module' => 'parlamentares', 'access' => true],
            ['route' => 'parlamentares.show', 'name' => 'Perfil do Parlamentar', 'module' => 'parlamentares', 'access' => true],
            ['route' => 'parlamentares.mesa-diretora', 'name' => 'Mesa Diretora', 'module' => 'parlamentares', 'access' => true],
            ['route' => 'parlamentares.create', 'name' => 'Novo Parlamentar', 'module' => 'parlamentares', 'access' => true],
            ['route' => 'parlamentares.edit', 'name' => 'Editar Parlamentar', 'module' => 'parlamentares', 'access' => true],
            
            // Partidos - acesso total
            ['route' => 'partidos.index', 'name' => 'Lista de Partidos', 'module' => 'partidos', 'access' => true],
            ['route' => 'partidos.show', 'name' => 'Detalhes do Partido', 'module' => 'partidos', 'access' => true],
            ['route' => 'partidos.create', 'name' => 'Novo Partido', 'module' => 'partidos', 'access' => true],
            ['route' => 'partidos.edit', 'name' => 'Editar Partido', 'module' => 'partidos', 'access' => true],
            
            // Proposições - acesso total
            ['route' => 'proposicoes.criar', 'name' => 'Nova Proposição', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.create', 'name' => 'Criar Proposição', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.edit', 'name' => 'Editar Proposição', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.index', 'name' => 'Todas as Proposições', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.minhas-proposicoes', 'name' => 'Minhas Proposições', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.assinatura', 'name' => 'Assinar Proposições', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.assinatura.index', 'name' => 'Assinaturas Pendentes', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.assinatura.assinar', 'name' => 'Efetuar Assinatura', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.historico-assinaturas', 'name' => 'Histórico de Assinaturas', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.buscar-modelos', 'name' => 'Buscar Modelos', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.salvar-rascunho', 'name' => 'Salvar Rascunho', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.legislativo.index', 'name' => 'Proposições Recebidas', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.legislativo.editar', 'name' => 'Editar via Legislativo', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.revisar', 'name' => 'Revisar Proposições', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.relatorio-legislativo', 'name' => 'Relatório Legislativo', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.aguardando-protocolo', 'name' => 'Aguardando Protocolo', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.protocolar', 'name' => 'Protocolar', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.protocolos-hoje', 'name' => 'Protocolos Hoje', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.estatisticas-protocolo', 'name' => 'Estatísticas Protocolo', 'module' => 'proposicoes', 'access' => true],
            
            // Comissões - acesso total
            ['route' => 'comissoes.index', 'name' => 'Lista de Comissões', 'module' => 'comissoes', 'access' => true],
            ['route' => 'comissoes.show', 'name' => 'Detalhes da Comissão', 'module' => 'comissoes', 'access' => true],
            ['route' => 'comissoes.create', 'name' => 'Nova Comissão', 'module' => 'comissoes', 'access' => true],
            ['route' => 'comissoes.edit', 'name' => 'Editar Comissão', 'module' => 'comissoes', 'access' => true],
            ['route' => 'comissoes.minhas-comissoes', 'name' => 'Minhas Comissões', 'module' => 'comissoes', 'access' => true],
            
            // Sessões - acesso total
            ['route' => 'admin.sessions.index', 'name' => 'Lista de Sessões', 'module' => 'sessoes', 'access' => true],
            ['route' => 'sessoes.index', 'name' => 'Lista de Sessões', 'module' => 'sessoes', 'access' => true],
            ['route' => 'sessoes.agenda', 'name' => 'Agenda de Sessões', 'module' => 'sessoes', 'access' => true],
            ['route' => 'sessoes.atas', 'name' => 'Atas das Sessões', 'module' => 'sessoes', 'access' => true],
            ['route' => 'admin.sessions.create', 'name' => 'Nova Sessão', 'module' => 'sessoes', 'access' => true],
            ['route' => 'sessoes.create', 'name' => 'Nova Sessão', 'module' => 'sessoes', 'access' => true],
            
            // Votações - acesso total
            ['route' => 'votacoes.index', 'name' => 'Lista de Votações', 'module' => 'votacoes', 'access' => true],
            ['route' => 'votacoes.create', 'name' => 'Nova Votação', 'module' => 'votacoes', 'access' => true],
            ['route' => 'votacoes.show', 'name' => 'Detalhes da Votação', 'module' => 'votacoes', 'access' => true],
            ['route' => 'votacoes.edit', 'name' => 'Editar Votação', 'module' => 'votacoes', 'access' => true],
            
            // Perfil - acesso total
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile', 'access' => true],
            ['route' => 'profile.show', 'name' => 'Ver Perfil', 'module' => 'profile', 'access' => true],
            
            // Relatórios - acesso total
            ['route' => 'relatorios.proposicoes', 'name' => 'Relatório de Proposições', 'module' => 'relatorios', 'access' => true],
            
            // Administração - ACESSO TOTAL
            ['route' => 'usuarios.index', 'name' => 'Usuários do Sistema', 'module' => 'usuarios', 'access' => true],
            ['route' => 'usuarios.show', 'name' => 'Perfil do Usuário', 'module' => 'usuarios', 'access' => true],
            ['route' => 'usuarios.create', 'name' => 'Novo Usuário', 'module' => 'usuarios', 'access' => true],
            ['route' => 'usuarios.edit', 'name' => 'Editar Usuário', 'module' => 'usuarios', 'access' => true],
            
            // Parâmetros - ACESSO TOTAL
            ['route' => 'admin.parametros.index', 'name' => 'Parâmetros - Listagem', 'module' => 'parametros', 'access' => true],
            ['route' => 'admin.parametros.create', 'name' => 'Parâmetros - Criar', 'module' => 'parametros', 'access' => true],
            ['route' => 'admin.parametros.edit', 'name' => 'Parâmetros - Editar', 'module' => 'parametros', 'access' => true],
            ['route' => 'admin.parametros.show', 'name' => 'Parâmetros - Visualizar', 'module' => 'parametros', 'access' => true],
            
            // Documentos - ACESSO TOTAL
            ['route' => 'documentos.instancias.index', 'name' => 'Documentos em Tramitação', 'module' => 'documentos', 'access' => true],
            
            // Testes - ACESSO TOTAL
            ['route' => 'tests.index', 'name' => 'Testes do Sistema', 'module' => 'tests', 'access' => true],
            
            // Permissões - ACESSO TOTAL
            ['route' => 'admin.screen-permissions.index', 'name' => 'Gerenciar Permissões', 'module' => 'admin', 'access' => true],
            
            // Tipo Proposições - ACESSO TOTAL
            ['route' => 'admin.tipo-proposicoes.index', 'name' => 'Tipos de Proposição', 'module' => 'admin', 'access' => true],
            
            // APIs - ACESSO TOTAL
            ['route' => 'user-api.index', 'name' => 'API de Usuários', 'module' => 'api', 'access' => true],
            ['route' => 'user-api.health', 'name' => 'Status da API', 'module' => 'api', 'access' => true],
        ];

        $this->info('Removendo permissões existentes para ADMIN...');
        ScreenPermission::where('role_name', 'ADMIN')->delete();

        $this->info('Aplicando novas permissões (ACESSO TOTAL)...');
        foreach ($permissions as $permission) {
            ScreenPermission::setScreenAccess(
                'ADMIN',
                $permission['route'],
                $permission['name'],
                $permission['module'],
                $permission['access']
            );
        }

        $this->info('✅ Permissões configuradas com sucesso para o perfil ADMIN!');
        
        // Mostrar resumo
        $allowed = collect($permissions)->where('access', true)->count();
        $denied = collect($permissions)->where('access', false)->count();
        
        $this->info("📊 Resumo: {$allowed} rotas permitidas, {$denied} rotas negadas");
        $this->warn('🔑 ADMIN agora tem ACESSO TOTAL a todas as funcionalidades do sistema!');
        
        return 0;
    }
}