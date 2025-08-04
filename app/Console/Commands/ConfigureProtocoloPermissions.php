<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;

class ConfigureProtocoloPermissions extends Command
{
    protected $signature = 'permissions:configure-protocolo';
    protected $description = 'Configura as permissões corretas para o perfil PROTOCOLO';

    public function handle()
    {
        $this->info('Configurando permissões para o perfil PROTOCOLO...');

        $permissions = [
            // Dashboard - permitido
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard', 'access' => true],
            
            // Parlamentares - NEGAR ACESSO (não precisa gerenciar parlamentares)
            ['route' => 'parlamentares.index', 'name' => 'Lista de Parlamentares', 'module' => 'parlamentares', 'access' => false],
            ['route' => 'parlamentares.show', 'name' => 'Perfil do Parlamentar', 'module' => 'parlamentares', 'access' => false],
            ['route' => 'parlamentares.mesa-diretora', 'name' => 'Mesa Diretora', 'module' => 'parlamentares', 'access' => false],
            ['route' => 'parlamentares.create', 'name' => 'Novo Parlamentar', 'module' => 'parlamentares', 'access' => false],
            ['route' => 'parlamentares.edit', 'name' => 'Editar Parlamentar', 'module' => 'parlamentares', 'access' => false],
            
            // Proposições - foco em protocolo
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.aguardando-protocolo', 'name' => 'Aguardando Protocolo', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.protocolar', 'name' => 'Protocolar', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.protocolar.show', 'name' => 'Detalhes Protocolo', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.efetivar-protocolo', 'name' => 'Efetivar Protocolo', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.protocolos-hoje', 'name' => 'Protocolos Hoje', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.estatisticas-protocolo', 'name' => 'Estatísticas Protocolo', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.iniciar-tramitacao', 'name' => 'Iniciar Tramitação', 'module' => 'proposicoes', 'access' => true],
            
            // PROTOCOLO NÃO PODE criar ou editar proposições
            ['route' => 'proposicoes.criar', 'name' => 'Nova Proposição', 'module' => 'proposicoes', 'access' => false],
            ['route' => 'proposicoes.create', 'name' => 'Criar Proposição', 'module' => 'proposicoes', 'access' => false],
            ['route' => 'proposicoes.edit', 'name' => 'Editar Proposição', 'module' => 'proposicoes', 'access' => false],
            ['route' => 'proposicoes.minhas-proposicoes', 'name' => 'Minhas Proposições', 'module' => 'proposicoes', 'access' => false],
            ['route' => 'proposicoes.assinatura', 'name' => 'Assinar Proposições', 'module' => 'proposicoes', 'access' => false],
            ['route' => 'proposicoes.revisar', 'name' => 'Revisar Proposições', 'module' => 'proposicoes', 'access' => false],
            
            // Sessões - NEGAR ACESSO (não gerencia sessões)
            ['route' => 'admin.sessions.index', 'name' => 'Lista de Sessões', 'module' => 'sessoes', 'access' => false],
            ['route' => 'sessoes.index', 'name' => 'Lista de Sessões', 'module' => 'sessoes', 'access' => false],
            ['route' => 'sessoes.agenda', 'name' => 'Agenda de Sessões', 'module' => 'sessoes', 'access' => false],
            ['route' => 'sessoes.atas', 'name' => 'Atas das Sessões', 'module' => 'sessoes', 'access' => false],
            ['route' => 'admin.sessions.create', 'name' => 'Nova Sessão', 'module' => 'sessoes', 'access' => false],
            ['route' => 'sessoes.create', 'name' => 'Nova Sessão', 'module' => 'sessoes', 'access' => false],
            
            // Comissões - NEGAR ACESSO (não gerencia comissões)
            ['route' => 'comissoes.index', 'name' => 'Lista de Comissões', 'module' => 'comissoes', 'access' => false],
            ['route' => 'comissoes.show', 'name' => 'Detalhes da Comissão', 'module' => 'comissoes', 'access' => false],
            ['route' => 'comissoes.create', 'name' => 'Nova Comissão', 'module' => 'comissoes', 'access' => false],
            ['route' => 'comissoes.edit', 'name' => 'Editar Comissão', 'module' => 'comissoes', 'access' => false],
            
            // Votações - NEGAR ACESSO (não gerencia votações)
            ['route' => 'votacoes.index', 'name' => 'Lista de Votações', 'module' => 'votacoes', 'access' => false],
            ['route' => 'votacoes.create', 'name' => 'Nova Votação', 'module' => 'votacoes', 'access' => false],
            
            // Perfil - acesso total
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile', 'access' => true],
            ['route' => 'profile.show', 'name' => 'Ver Perfil', 'module' => 'profile', 'access' => true],
            
            // Relatórios - acesso a relatórios de protocolo
            ['route' => 'relatorios.proposicoes', 'name' => 'Relatório de Proposições', 'module' => 'relatorios', 'access' => true],
            
            // Documentos - NEGAR ACESSO (não precisa para protocolo)
            ['route' => 'documentos.instancias.index', 'name' => 'Documentos em Tramitação', 'module' => 'documentos', 'access' => false],
            
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
            
            // Testes - NEGAR ACESSO
            ['route' => 'tests.index', 'name' => 'Testes do Sistema', 'module' => 'tests', 'access' => false],
            
            // Permissões - NEGAR ACESSO
            ['route' => 'admin.screen-permissions.index', 'name' => 'Gerenciar Permissões', 'module' => 'admin', 'access' => false],
            
            // Tipo Proposições - NEGAR ACESSO
            ['route' => 'admin.tipo-proposicoes.index', 'name' => 'Tipos de Proposição', 'module' => 'admin', 'access' => false],
        ];

        $this->info('Removendo permissões existentes para PROTOCOLO...');
        ScreenPermission::where('role_name', 'PROTOCOLO')->delete();

        $this->info('Aplicando novas permissões...');
        foreach ($permissions as $permission) {
            ScreenPermission::setScreenAccess(
                'PROTOCOLO',
                $permission['route'],
                $permission['name'],
                $permission['module'],
                $permission['access']
            );
        }

        $this->info('✅ Permissões configuradas com sucesso para o perfil PROTOCOLO!');
        
        // Mostrar resumo
        $allowed = collect($permissions)->where('access', true)->count();
        $denied = collect($permissions)->where('access', false)->count();
        
        $this->info("📊 Resumo: {$allowed} rotas permitidas, {$denied} rotas negadas");
        
        return 0;
    }
}