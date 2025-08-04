<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;

class ConfigureParlamentarPermissions extends Command
{
    protected $signature = 'permissions:configure-parlamentar';
    protected $description = 'Configura as permissões corretas para o perfil PARLAMENTAR';

    public function handle()
    {
        $this->info('Configurando permissões para o perfil PARLAMENTAR...');

        $permissions = [
            // Dashboard - permitido
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard', 'access' => true],
            
            // Parlamentares - NEGAR ACESSO (não precisa ver lista geral)
            ['route' => 'parlamentares.index', 'name' => 'Lista de Parlamentares', 'module' => 'parlamentares', 'access' => false],
            ['route' => 'parlamentares.show', 'name' => 'Perfil do Parlamentar', 'module' => 'parlamentares', 'access' => false],
            ['route' => 'parlamentares.mesa-diretora', 'name' => 'Mesa Diretora', 'module' => 'parlamentares', 'access' => false],
            ['route' => 'parlamentares.create', 'name' => 'Novo Parlamentar', 'module' => 'parlamentares', 'access' => false],
            ['route' => 'parlamentares.edit', 'name' => 'Editar Parlamentar', 'module' => 'parlamentares', 'access' => false],
            
            // Partidos - NEGAR ACESSO (não precisa ver lista geral)
            ['route' => 'partidos.index', 'name' => 'Lista de Partidos', 'module' => 'partidos', 'access' => false],
            ['route' => 'partidos.show', 'name' => 'Detalhes do Partido', 'module' => 'partidos', 'access' => false],
            
            // Proposições - acesso total para parlamentar
            ['route' => 'proposicoes.criar', 'name' => 'Nova Proposição', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.create', 'name' => 'Criar Proposição', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.edit', 'name' => 'Editar Proposição', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.index', 'name' => 'Minhas Proposições', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.minhas-proposicoes', 'name' => 'Minhas Proposições', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.assinatura', 'name' => 'Assinar Proposições', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.assinatura.index', 'name' => 'Assinaturas Pendentes', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.assinatura.assinar', 'name' => 'Efetuar Assinatura', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.historico-assinaturas', 'name' => 'Histórico de Assinaturas', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.buscar-modelos', 'name' => 'Buscar Modelos', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.salvar-rascunho', 'name' => 'Salvar Rascunho', 'module' => 'proposicoes', 'access' => true],
            
            // Comissões - acesso apenas às que faz parte (será implementado lógica específica)
            ['route' => 'comissoes.index', 'name' => 'Lista de Comissões', 'module' => 'comissoes', 'access' => true],
            ['route' => 'comissoes.show', 'name' => 'Detalhes da Comissão', 'module' => 'comissoes', 'access' => true],
            ['route' => 'comissoes.minhas-comissoes', 'name' => 'Minhas Comissões', 'module' => 'comissoes', 'access' => true],
            ['route' => 'comissoes.create', 'name' => 'Nova Comissão', 'module' => 'comissoes', 'access' => false],
            ['route' => 'comissoes.edit', 'name' => 'Editar Comissão', 'module' => 'comissoes', 'access' => false],
            
            // Sessões - NEGAR ACESSO (não precisa ver todas as sessões)
            ['route' => 'admin.sessions.index', 'name' => 'Lista de Sessões', 'module' => 'sessoes', 'access' => false],
            ['route' => 'sessoes.index', 'name' => 'Lista de Sessões', 'module' => 'sessoes', 'access' => false],
            ['route' => 'sessoes.agenda', 'name' => 'Agenda de Sessões', 'module' => 'sessoes', 'access' => false],
            ['route' => 'sessoes.atas', 'name' => 'Atas das Sessões', 'module' => 'sessoes', 'access' => false],
            ['route' => 'admin.sessions.create', 'name' => 'Nova Sessão', 'module' => 'sessoes', 'access' => false],
            ['route' => 'sessoes.create', 'name' => 'Nova Sessão', 'module' => 'sessoes', 'access' => false],
            
            // Votações - acesso específico para suas proposições
            ['route' => 'votacoes.minhas-proposicoes', 'name' => 'Votações das Minhas Proposições', 'module' => 'votacoes', 'access' => true],
            ['route' => 'votacoes.index', 'name' => 'Lista de Votações', 'module' => 'votacoes', 'access' => false],
            ['route' => 'votacoes.create', 'name' => 'Nova Votação', 'module' => 'votacoes', 'access' => false],
            
            // Perfil - acesso total
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile', 'access' => true],
            ['route' => 'profile.show', 'name' => 'Ver Perfil', 'module' => 'profile', 'access' => true],
            
            // Relatórios - acesso limitado
            ['route' => 'relatorios.proposicoes', 'name' => 'Relatório de Proposições', 'module' => 'relatorios', 'access' => true],
            
            // Administração - NEGAR ACESSO
            ['route' => 'usuarios.index', 'name' => 'Usuários do Sistema', 'module' => 'usuarios', 'access' => false],
            ['route' => 'usuarios.show', 'name' => 'Perfil do Usuário', 'module' => 'usuarios', 'access' => false],
            ['route' => 'usuarios.create', 'name' => 'Novo Usuário', 'module' => 'usuarios', 'access' => false],
            ['route' => 'usuarios.edit', 'name' => 'Editar Usuário', 'module' => 'usuarios', 'access' => false],
            
            // Parâmetros - NEGAR ACESSO
            ['route' => 'admin.parametros.index', 'name' => 'Parâmetros - Listagem', 'module' => 'parametros', 'access' => false],
            ['route' => 'admin.parametros.create', 'name' => 'Parâmetros - Criar', 'module' => 'parametros', 'access' => false],
            ['route' => 'admin.parametros.edit', 'name' => 'Parâmetros - Editar', 'module' => 'parametros', 'access' => false],
            ['route' => 'admin.parametros.show', 'name' => 'Parâmetros - Visualizar', 'module' => 'parametros', 'access' => false],
            
            // Documentos - NEGAR ACESSO
            ['route' => 'documentos.instancias.index', 'name' => 'Documentos em Tramitação', 'module' => 'documentos', 'access' => false],
            
            // Testes - NEGAR ACESSO
            ['route' => 'tests.index', 'name' => 'Testes do Sistema', 'module' => 'tests', 'access' => false],
            
            // Permissões - NEGAR ACESSO
            ['route' => 'admin.screen-permissions.index', 'name' => 'Gerenciar Permissões', 'module' => 'admin', 'access' => false],
            
            // Tipo Proposições - NEGAR ACESSO
            ['route' => 'admin.tipo-proposicoes.index', 'name' => 'Tipos de Proposição', 'module' => 'admin', 'access' => false],
        ];

        $this->info('Removendo permissões existentes para PARLAMENTAR...');
        ScreenPermission::where('role_name', 'PARLAMENTAR')->delete();

        $this->info('Aplicando novas permissões...');
        foreach ($permissions as $permission) {
            ScreenPermission::setScreenAccess(
                'PARLAMENTAR',
                $permission['route'],
                $permission['name'],
                $permission['module'],
                $permission['access']
            );
        }

        $this->info('✅ Permissões configuradas com sucesso para o perfil PARLAMENTAR!');
        
        // Mostrar resumo
        $allowed = collect($permissions)->where('access', true)->count();
        $denied = collect($permissions)->where('access', false)->count();
        
        $this->info("📊 Resumo: {$allowed} rotas permitidas, {$denied} rotas negadas");
        
        return 0;
    }
}