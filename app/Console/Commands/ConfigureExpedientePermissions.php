<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;

class ConfigureExpedientePermissions extends Command
{
    protected $signature = 'permissions:configure-expediente';
    protected $description = 'Configura as permissões corretas para o perfil EXPEDIENTE';

    public function handle()
    {
        $this->info('Configurando permissões para o perfil EXPEDIENTE...');

        $permissions = [
            // Dashboard - permitido
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard', 'access' => true],
            
            // Parlamentares - NEGAR ACESSO (não gerencia parlamentares)
            ['route' => 'parlamentares.index', 'name' => 'Lista de Parlamentares', 'module' => 'parlamentares', 'access' => false],
            ['route' => 'parlamentares.show', 'name' => 'Perfil do Parlamentar', 'module' => 'parlamentares', 'access' => false],
            ['route' => 'parlamentares.mesa-diretora', 'name' => 'Mesa Diretora', 'module' => 'parlamentares', 'access' => false],
            ['route' => 'parlamentares.create', 'name' => 'Novo Parlamentar', 'module' => 'parlamentares', 'access' => false],
            ['route' => 'parlamentares.edit', 'name' => 'Editar Parlamentar', 'module' => 'parlamentares', 'access' => false],
            
            // Proposições - acesso específico para expediente (proposições protocoladas)
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.legislativo.index', 'name' => 'Proposições Protocoladas', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.relatorio-legislativo', 'name' => 'Relatório', 'module' => 'proposicoes', 'access' => true],
            
            // Acesso às proposições protocoladas pelo PROTOCOLO
            ['route' => 'proposicoes.protocoladas', 'name' => 'Proposições Protocoladas pelo Protocolo', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.aguardando-pauta', 'name' => 'Aguardando Inclusão em Pauta', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.incluir-pauta', 'name' => 'Incluir em Pauta', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.gerenciar-pautas', 'name' => 'Gerenciar Pautas', 'module' => 'proposicoes', 'access' => true],
            
            // Novas rotas do sistema de Expediente
            ['route' => 'expediente.index', 'name' => 'Painel do Expediente', 'module' => 'expediente', 'access' => true],
            ['route' => 'expediente.show', 'name' => 'Visualizar Proposição no Expediente', 'module' => 'expediente', 'access' => true],
            ['route' => 'expediente.classificar', 'name' => 'Classificar Momento da Sessão', 'module' => 'expediente', 'access' => true],
            ['route' => 'expediente.reclassificar', 'name' => 'Reclassificar Proposições', 'module' => 'expediente', 'access' => true],
            ['route' => 'expediente.enviar-votacao', 'name' => 'Enviar para Votação', 'module' => 'expediente', 'access' => true],
            ['route' => 'expediente.aguardando-pauta', 'name' => 'Proposições Aguardando Pauta', 'module' => 'expediente', 'access' => true],
            ['route' => 'expediente.relatorio', 'name' => 'Relatório do Expediente', 'module' => 'expediente', 'access' => true],
            
            // EXPEDIENTE NÃO PODE criar, assinar ou protocolar
            ['route' => 'proposicoes.criar', 'name' => 'Nova Proposição', 'module' => 'proposicoes', 'access' => false],
            ['route' => 'proposicoes.create', 'name' => 'Criar Proposição', 'module' => 'proposicoes', 'access' => false],
            ['route' => 'proposicoes.edit', 'name' => 'Editar Proposição', 'module' => 'proposicoes', 'access' => false],
            ['route' => 'proposicoes.minhas-proposicoes', 'name' => 'Minhas Proposições', 'module' => 'proposicoes', 'access' => false],
            ['route' => 'proposicoes.assinatura', 'name' => 'Assinar Proposições', 'module' => 'proposicoes', 'access' => false],
            ['route' => 'proposicoes.protocolar', 'name' => 'Protocolar', 'module' => 'proposicoes', 'access' => false],
            ['route' => 'proposicoes.aguardando-protocolo', 'name' => 'Aguardando Protocolo', 'module' => 'proposicoes', 'access' => false],
            
            // Sessões - acesso para organizar pautas com proposições protocoladas
            ['route' => 'admin.sessions.index', 'name' => 'Lista de Sessões', 'module' => 'sessoes', 'access' => true],
            ['route' => 'sessoes.index', 'name' => 'Lista de Sessões', 'module' => 'sessoes', 'access' => true],
            ['route' => 'sessoes.agenda', 'name' => 'Agenda de Sessões', 'module' => 'sessoes', 'access' => true],
            ['route' => 'sessoes.atas', 'name' => 'Atas das Sessões', 'module' => 'sessoes', 'access' => true],
            ['route' => 'sessoes.pautas', 'name' => 'Gerenciar Pautas', 'module' => 'sessoes', 'access' => true],
            ['route' => 'sessoes.incluir-proposicao', 'name' => 'Incluir Proposição em Pauta', 'module' => 'sessoes', 'access' => true],
            ['route' => 'admin.sessions.create', 'name' => 'Nova Sessão', 'module' => 'sessoes', 'access' => false], // Não cria sessões
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
            
            // Relatórios - acesso a relatórios de expediente
            ['route' => 'relatorios.proposicoes', 'name' => 'Relatório de Proposições', 'module' => 'relatorios', 'access' => true],
            
            // Documentos - NEGAR ACESSO
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

        $this->info('Removendo permissões existentes para EXPEDIENTE...');
        ScreenPermission::where('role_name', 'EXPEDIENTE')->delete();

        $this->info('Aplicando novas permissões...');
        foreach ($permissions as $permission) {
            ScreenPermission::setScreenAccess(
                'EXPEDIENTE',
                $permission['route'],
                $permission['name'],
                $permission['module'],
                $permission['access']
            );
        }

        $this->info('✅ Permissões configuradas com sucesso para o perfil EXPEDIENTE!');
        
        // Mostrar resumo
        $allowed = collect($permissions)->where('access', true)->count();
        $denied = collect($permissions)->where('access', false)->count();
        
        $this->info("📊 Resumo: {$allowed} rotas permitidas, {$denied} rotas negadas");
        $this->warn('📋 EXPEDIENTE foca em: Gerenciar pautas de sessões com proposições protocoladas');
        
        return 0;
    }
}