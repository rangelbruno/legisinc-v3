<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScreenPermission;
use Illuminate\Support\Facades\DB;

class OptimizedMenuPermissionsSeeder extends Seeder
{
    /**
     * Configurar menus otimizados por tipo de usuário
     * Remove acessos desnecessários e foca no essencial de cada perfil
     */
    public function run(): void
    {
        $this->command->info('🎯 Configurando Menus Otimizados por Perfil de Usuário');
        
        // Limpar configurações antigas que causam confusão
        $this->cleanOldPermissions();
        
        // Configurar menus otimizados
        $this->configureParlamentar();
        $this->configureLegislativo();
        $this->configureProtocolo();
        $this->configureExpediente();
        $this->configureAssessorJuridico();
        
        $this->command->info('✅ Menus otimizados configurados com sucesso!');
        $this->showSummary();
    }

    /**
     * Limpar permissões antigas que causam confusão
     */
    private function cleanOldPermissions(): void
    {
        $this->command->info('🧹 Limpando permissões antigas...');
        
        // Remover permissões desnecessárias para PARLAMENTAR
        $removedParlamentar = ScreenPermission::where('role_name', 'PARLAMENTAR')
            ->whereIn('screen_module', ['parlamentares', 'comissoes', 'partidos', 'usuarios', 'sessoes', 'votacoes', 'documentos', 'administracao', 'parametros'])
            ->orWhere(function($query) {
                $query->where('role_name', 'PARLAMENTAR')
                      ->where('screen_route', 'like', 'admin.%');
            })
            ->delete();
        
        // Remover permissões desnecessárias para LEGISLATIVO  
        $removedLegislativo = ScreenPermission::where('role_name', 'LEGISLATIVO')
            ->whereIn('screen_module', ['partidos', 'usuarios', 'sessoes', 'votacoes', 'documentos', 'comissoes', 'administracao', 'parametros'])
            ->delete();
            
        // Remover permissões específicas problemáticas para LEGISLATIVO
        $removedEspecificas = ScreenPermission::where('role_name', 'LEGISLATIVO')
            ->whereIn('screen_route', [
                // Parlamentares - apenas index e show são permitidos
                'parlamentares.create', 'parlamentares.edit', 'parlamentares.mesa-diretora',
                // Proposições - não pode criar novas
                'proposicoes.create', 'proposicoes.criar',
                // Administrativas
                'admin.parametros.create', 'admin.parametros.index', 'admin.parametros.show',
                'admin.sessions.create', 'admin.sessions.edit',
                // Assinaturas genéricas (deve ter só as específicas do legislativo)
                'proposicoes.assinatura', 'proposicoes.minhas-proposicoes',
                // Outras funcionalidades não relacionadas
                'proposicoes.index', 'proposicoes.protocolo.index',
                'relatorios.index', 'relatorios.proposicoes', 'relatorios.tramitacao'
            ])
            ->delete();
            
        // Remover permissões administrativas e desnecessárias para PROTOCOLO
        $removedProtocolo = ScreenPermission::where('role_name', 'PROTOCOLO')
            ->whereIn('screen_module', ['usuarios', 'administracao', 'parametros', 'comissoes', 'partidos', 'votacoes'])
            ->orWhere(function($query) {
                $query->where('role_name', 'PROTOCOLO')
                      ->whereIn('screen_route', [
                          // Administrativas
                          'admin.parametros.create', 'admin.parametros.index', 'admin.parametros.show', 'admin.parametros.edit',
                          'admin.sessions.create', 'admin.sessions.edit',
                          // Proposições que não são do protocolo
                          'proposicoes.criar', 'proposicoes.minhas-proposicoes', 'proposicoes.assinatura',
                          'proposicoes.index', // Não precisa ver todas, só as aguardando protocolo
                          'proposicoes.efetivar-protocolo', 'proposicoes.iniciar-tramitacao'
                      ]);
            })
            ->delete();
        
        // Remover permissões administrativas para EXPEDIENTE
        $removedExpediente = ScreenPermission::where('role_name', 'EXPEDIENTE')
            ->whereIn('screen_module', ['usuarios', 'administracao', 'parametros'])
            ->orWhere(function($query) {
                $query->where('role_name', 'EXPEDIENTE')
                      ->where('screen_route', 'like', 'admin.%')
                      ->where('screen_route', '!=', 'admin.sessions.index'); // Manter visualização de sessões
            })
            ->delete();
            
        // Remover permissões administrativas para ASSESSOR_JURIDICO  
        $removedAssessorJuridico = ScreenPermission::where('role_name', 'ASSESSOR_JURIDICO')
            ->whereIn('screen_module', ['usuarios', 'administracao', 'parametros'])
            ->orWhere(function($query) {
                $query->where('role_name', 'ASSESSOR_JURIDICO')
                      ->where('screen_route', 'like', 'admin.%');
            })
            ->delete();

        $this->command->info("   Removidas {$removedParlamentar} permissões desnecessárias do PARLAMENTAR");
        $this->command->info("   Removidas {$removedLegislativo} permissões desnecessárias do LEGISLATIVO (módulos)");
        $this->command->info("   Removidas {$removedEspecificas} permissões específicas do LEGISLATIVO");
        $this->command->info("   Removidas {$removedProtocolo} permissões administrativas do PROTOCOLO");
        $this->command->info("   Removidas {$removedExpediente} permissões administrativas do EXPEDIENTE");
        $this->command->info("   Removidas {$removedAssessorJuridico} permissões administrativas do ASSESSOR_JURIDICO");
    }

    /**
     * PARLAMENTAR: Foco EXCLUSIVO em suas proposições
     */
    private function configureParlamentar(): void
    {
        $this->command->info('🏛️  PARLAMENTAR: Configurando menu focado em proposições...');
        
        $permissions = [
            // Core essencial
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard'],
            ['route' => 'profile.show', 'name' => 'Visualizar Perfil', 'module' => 'profile'],
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile'],
            
            // FOCO PRINCIPAL: Proposições próprias
            ['route' => 'proposicoes.criar', 'name' => 'Criar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.create', 'name' => 'Nova Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.minhas-proposicoes', 'name' => 'Minhas Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.index', 'name' => 'Listar Minhas Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.edit', 'name' => 'Editar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.salvar-rascunho', 'name' => 'Salvar Rascunho', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.buscar-modelos', 'name' => 'Buscar Modelos', 'module' => 'proposicoes'],
            
            // Assinatura - Processo essencial
            ['route' => 'proposicoes.assinatura', 'name' => 'Assinar Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.assinatura.index', 'name' => 'Assinaturas Pendentes', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.assinatura.assinar', 'name' => 'Efetuar Assinatura', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.historico-assinaturas', 'name' => 'Histórico de Assinaturas', 'module' => 'proposicoes'],
            
            // Relatórios próprios
            ['route' => 'relatorios.proposicoes', 'name' => 'Relatório das Minhas Proposições', 'module' => 'relatorios'],
        ];

        $this->applyPermissions('PARLAMENTAR', $permissions);
        $this->command->info("   ✅ {$this->countActivePermissions('PARLAMENTAR')} permissões configuradas para PARLAMENTAR");
    }

    /**
     * LEGISLATIVO: Análise e processamento de proposições com consulta limitada
     */
    private function configureLegislativo(): void
    {
        $this->command->info('⚖️  LEGISLATIVO: Configurando menu focado em análise de proposições...');
        
        $permissions = [
            // Core essencial
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard'],
            ['route' => 'profile.show', 'name' => 'Visualizar Perfil', 'module' => 'profile'],
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile'],
            
            // FOCO PRINCIPAL: Proposições - Análise e processamento
            ['route' => 'proposicoes.legislativo.index', 'name' => 'Proposições Recebidas', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.legislativo.editar', 'name' => 'Editar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.aprovar', 'name' => 'Aprovar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.devolver', 'name' => 'Devolver Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.relatorio-legislativo', 'name' => 'Relatório Legislativo', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.aguardando-protocolo', 'name' => 'Aguardando Protocolo', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.revisar', 'name' => 'Revisar Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.salvar-analise', 'name' => 'Salvar Análise', 'module' => 'proposicoes'],
            
            // Parlamentares - APENAS consulta (sem criar/editar)
            ['route' => 'parlamentares.index', 'name' => 'Lista de Parlamentares', 'module' => 'parlamentares'],
            ['route' => 'parlamentares.show', 'name' => 'Visualizar Parlamentar', 'module' => 'parlamentares'],
            
            // Relatórios específicos
            ['route' => 'relatorios.legislativo', 'name' => 'Relatórios do Legislativo', 'module' => 'relatorios'],
        ];

        $this->applyPermissions('LEGISLATIVO', $permissions);
        $this->command->info("   ✅ {$this->countActivePermissions('LEGISLATIVO')} permissões configuradas para LEGISLATIVO");
    }

    /**
     * PROTOCOLO: Foco em protocolo e tramitação
     */
    private function configureProtocolo(): void
    {
        $this->command->info('📋 PROTOCOLO: Configurando menu de protocolo e tramitação...');
        
        $permissions = [
            // Core
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard'],
            ['route' => 'profile.show', 'name' => 'Visualizar Perfil', 'module' => 'profile'],
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile'],
            
            // Protocolo - Funcionalidade principal
            ['route' => 'proposicoes.aguardando-protocolo', 'name' => 'Aguardando Protocolo', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.protocolar', 'name' => 'Protocolar', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.protocolos-hoje', 'name' => 'Protocolos de Hoje', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.estatisticas-protocolo', 'name' => 'Estatísticas de Protocolo', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes'],
            
            // Consultas básicas necessárias para o trabalho
            ['route' => 'parlamentares.index', 'name' => 'Lista de Parlamentares', 'module' => 'parlamentares'],
            ['route' => 'parlamentares.show', 'name' => 'Visualizar Parlamentar', 'module' => 'parlamentares'],
            
            // Relatórios específicos do protocolo
            ['route' => 'relatorios.protocolo', 'name' => 'Relatório de Protocolo', 'module' => 'relatorios'],
        ];

        $this->applyPermissions('PROTOCOLO', $permissions);
        $this->command->info("   ✅ {$this->countActivePermissions('PROTOCOLO')} permissões configuradas para PROTOCOLO");
    }

    /**
     * EXPEDIENTE: Foco em organização de pautas
     */
    private function configureExpediente(): void
    {
        $this->command->info('📋 EXPEDIENTE: Configurando menu de expediente e pautas...');
        
        $permissions = [
            // Core
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard'],
            ['route' => 'profile.show', 'name' => 'Visualizar Perfil', 'module' => 'profile'],
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile'],
            
            // Expediente - Funcionalidade principal
            ['route' => 'expediente.index', 'name' => 'Painel do Expediente', 'module' => 'expediente'],
            ['route' => 'expediente.aguardando-pauta', 'name' => 'Aguardando Pauta', 'module' => 'expediente'],
            ['route' => 'expediente.relatorio', 'name' => 'Relatório do Expediente', 'module' => 'expediente'],
            
            // Proposições - Consulta para pauta
            ['route' => 'proposicoes.legislativo.index', 'name' => 'Consultar Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes'],
            
            // Sessões - Gestão básica
            ['route' => 'sessoes.index', 'name' => 'Lista de Sessões', 'module' => 'sessoes'],
            ['route' => 'sessoes.agenda', 'name' => 'Agenda de Sessões', 'module' => 'sessoes'],
        ];

        $this->applyPermissions('EXPEDIENTE', $permissions);
        $this->command->info("   ✅ {$this->countActivePermissions('EXPEDIENTE')} permissões configuradas para EXPEDIENTE");
    }

    /**
     * ASSESSOR_JURIDICO: Foco em análise jurídica
     */
    private function configureAssessorJuridico(): void
    {
        $this->command->info('⚖️  ASSESSOR_JURIDICO: Configurando menu de análise jurídica...');
        
        $permissions = [
            // Core
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard'],
            ['route' => 'profile.show', 'name' => 'Visualizar Perfil', 'module' => 'profile'],
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile'],
            
            // Assessoria Jurídica - Funcionalidade principal
            ['route' => 'proposicoes.legislativo.index', 'name' => 'Proposições para Análise', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes'],
            ['route' => 'pareceres.index', 'name' => 'Pareceres Jurídicos', 'module' => 'pareceres'],
            ['route' => 'pareceres.create', 'name' => 'Emitir Parecer', 'module' => 'pareceres'],
        ];

        $this->applyPermissions('ASSESSOR_JURIDICO', $permissions);
        $this->command->info("   ✅ {$this->countActivePermissions('ASSESSOR_JURIDICO')} permissões configuradas para ASSESSOR_JURIDICO");
    }

    /**
     * Aplicar permissões para um perfil específico
     */
    private function applyPermissions(string $role, array $permissions): void
    {
        foreach ($permissions as $permission) {
            ScreenPermission::updateOrCreate(
                [
                    'role_name' => $role,
                    'screen_route' => $permission['route'],
                ],
                [
                    'screen_name' => $permission['name'],
                    'screen_module' => $permission['module'],
                    'can_access' => true,
                    'can_create' => in_array('create', explode('.', $permission['route'])),
                    'can_edit' => in_array('edit', explode('.', $permission['route'])) || in_array('editar', explode('.', $permission['route'])),
                    'can_delete' => false, // Por segurança, delete sempre false por padrão
                ]
            );
        }
    }

    /**
     * Contar permissões ativas de um perfil
     */
    private function countActivePermissions(string $role): int
    {
        return ScreenPermission::where('role_name', $role)
            ->where('can_access', true)
            ->count();
    }

    /**
     * Mostrar resumo das configurações
     */
    private function showSummary(): void
    {
        $this->command->info('');
        $this->command->info('📊 RESUMO DOS MENUS OTIMIZADOS:');
        $this->command->info('');
        
        $roles = ['PARLAMENTAR', 'LEGISLATIVO', 'PROTOCOLO', 'EXPEDIENTE', 'ASSESSOR_JURIDICO'];
        
        foreach ($roles as $role) {
            $count = $this->countActivePermissions($role);
            $this->command->info("   {$role}: {$count} permissões ativas");
        }
        
        $this->command->info('');
        $this->command->info('🎯 FOCO POR PERFIL:');
        $this->command->info('   • PARLAMENTAR: Apenas Dashboard + Proposições próprias');
        $this->command->info('   • LEGISLATIVO: Gestão completa do processo legislativo');
        $this->command->info('   • PROTOCOLO: Protocolo e tramitação');
        $this->command->info('   • EXPEDIENTE: Organização de pautas e sessões');
        $this->command->info('   • ASSESSOR_JURIDICO: Análise jurídica e pareceres');
        $this->command->info('');
    }
}