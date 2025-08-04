<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScreenPermission;

class UserTypeScreenPermissionsSeeder extends Seeder
{
    /**
     * Aplicar permissões de tela específicas por tipo de usuário
     */
    public function run(): void
    {
        $this->command->info('🚀 Configurando Permissões de Tela por Tipo de Usuário');
        
        // Configurar permissões para cada tipo de usuário
        $this->configureParlamentarScreens();
        $this->configureLegislativoScreens();
        $this->configureExpedienteScreens();
        $this->configureAssessorJuridicoScreens();
        $this->configureProtocoloScreens();
        $this->configureRelatorScreens();
        $this->configureAssessorScreens();
        
        $this->command->info('✅ Permissões de tela configuradas com sucesso!');
    }

    /**
     * Configurar telas para PARLAMENTAR
     * Foco: Criação e acompanhamento de proposições próprias
     */
    private function configureParlamentarScreens(): void
    {
        $this->command->info('🏛️  Configurando telas para PARLAMENTAR...');
        
        $screens = [
            // Core - Acesso básico
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard'],
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile'],
            
            // Proposições - Criação e gerenciamento das próprias
            ['route' => 'proposicoes.index', 'name' => 'Minhas Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.create', 'name' => 'Criar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.edit', 'name' => 'Editar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.criar', 'name' => 'Nova Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.minhas-proposicoes', 'name' => 'Minhas Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.salvar-rascunho', 'name' => 'Salvar Rascunho', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.buscar-modelos', 'name' => 'Buscar Modelos', 'module' => 'proposicoes'],
            
            // Assinatura - Processo de assinatura
            ['route' => 'proposicoes.assinatura', 'name' => 'Assinar Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.assinatura.index', 'name' => 'Assinaturas Pendentes', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.assinatura.assinar', 'name' => 'Efetuar Assinatura', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.historico-assinaturas', 'name' => 'Histórico de Assinaturas', 'module' => 'proposicoes'],
            
            // Consultas - Visualização de informações relevantes
            ['route' => 'parlamentares.index', 'name' => 'Lista de Parlamentares', 'module' => 'parlamentares'],
            ['route' => 'parlamentares.show', 'name' => 'Perfil do Parlamentar', 'module' => 'parlamentares'],
            ['route' => 'parlamentares.mesa-diretora', 'name' => 'Mesa Diretora', 'module' => 'parlamentares'],
            ['route' => 'partidos.index', 'name' => 'Partidos', 'module' => 'partidos'],
            ['route' => 'partidos.show', 'name' => 'Detalhes do Partido', 'module' => 'partidos'],
            ['route' => 'usuarios.index', 'name' => 'Usuários do Sistema', 'module' => 'usuarios'],
            ['route' => 'usuarios.show', 'name' => 'Perfil do Usuário', 'module' => 'usuarios'],
            
            // Relatórios - Apenas das próprias proposições
            ['route' => 'relatorios.proposicoes', 'name' => 'Relatório de Proposições', 'module' => 'relatorios'],
        ];

        $this->applyScreenPermissions('PARLAMENTAR', $screens);
        $this->command->info('  ✅ ' . count($screens) . ' telas configuradas para PARLAMENTAR');
    }

    /**
     * Configurar telas para LEGISLATIVO
     * Foco: Gestão completa do processo legislativo
     */
    private function configureLegislativoScreens(): void
    {
        $this->command->info('⚖️  Configurando telas para LEGISLATIVO...');
        
        $screens = [
            // Core - Acesso básico
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard'],
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile'],
            
            // Proposições - Gestão completa do fluxo
            ['route' => 'proposicoes.index', 'name' => 'Todas as Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.create', 'name' => 'Criar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.edit', 'name' => 'Editar Proposição', 'module' => 'proposicoes'],
            
            // Fluxo Legislativo - Revisão e processamento
            ['route' => 'proposicoes.legislativo.index', 'name' => 'Proposições para Análise', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.legislativo.revisar', 'name' => 'Revisar Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.legislativo.aprovar', 'name' => 'Aprovar Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.legislativo.devolver', 'name' => 'Devolver Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.revisar', 'name' => 'Área de Revisão', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.relatorio-legislativo', 'name' => 'Relatório Legislativo', 'module' => 'proposicoes'],
            
            // Assinaturas - Gerenciamento geral
            ['route' => 'proposicoes.assinatura.index', 'name' => 'Gerenciar Assinaturas', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.historico-assinaturas', 'name' => 'Histórico de Assinaturas', 'module' => 'proposicoes'],
            
            // Protocolo - Acompanhamento
            ['route' => 'proposicoes.protocolo.index', 'name' => 'Acompanhar Protocolo', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.aguardando-protocolo', 'name' => 'Aguardando Protocolo', 'module' => 'proposicoes'],
            
            // Gestão de Usuários
            ['route' => 'usuarios.index', 'name' => 'Gerenciar Usuários', 'module' => 'usuarios'],
            ['route' => 'usuarios.show', 'name' => 'Perfil do Usuário', 'module' => 'usuarios'],
            ['route' => 'usuarios.create', 'name' => 'Criar Usuário', 'module' => 'usuarios'],
            ['route' => 'usuarios.edit', 'name' => 'Editar Usuário', 'module' => 'usuarios'],
            
            // Parlamentares
            ['route' => 'parlamentares.index', 'name' => 'Gerenciar Parlamentares', 'module' => 'parlamentares'],
            ['route' => 'parlamentares.show', 'name' => 'Perfil do Parlamentar', 'module' => 'parlamentares'],
            ['route' => 'parlamentares.create', 'name' => 'Cadastrar Parlamentar', 'module' => 'parlamentares'],
            ['route' => 'parlamentares.edit', 'name' => 'Editar Parlamentar', 'module' => 'parlamentares'],
            
            // Partidos
            ['route' => 'partidos.index', 'name' => 'Gerenciar Partidos', 'module' => 'partidos'],
            ['route' => 'partidos.show', 'name' => 'Detalhes do Partido', 'module' => 'partidos'],
            ['route' => 'partidos.create', 'name' => 'Cadastrar Partido', 'module' => 'partidos'],
            ['route' => 'partidos.edit', 'name' => 'Editar Partido', 'module' => 'partidos'],
            
            // Relatórios - Acesso completo
            ['route' => 'relatorios.index', 'name' => 'Central de Relatórios', 'module' => 'relatorios'],
            ['route' => 'relatorios.proposicoes', 'name' => 'Relatório de Proposições', 'module' => 'relatorios'],
            ['route' => 'relatorios.tramitacao', 'name' => 'Relatório de Tramitação', 'module' => 'relatorios'],
        ];

        $this->applyScreenPermissions('LEGISLATIVO', $screens);
        $this->command->info('  ✅ ' . count($screens) . ' telas configuradas para LEGISLATIVO');
    }

    /**
     * Configurar telas para PROTOCOLO
     * Foco: Controle de entrada e distribuição
     */
    private function configureProtocoloScreens(): void
    {
        $this->command->info('📋 Configurando telas para PROTOCOLO...');
        
        $screens = [
            // Core - Acesso básico
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard'],
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile'],
            
            // Protocolo - Funcionalidades principais
            ['route' => 'proposicoes.protocolo.index', 'name' => 'Central de Protocolo', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.protocolo.protocolar', 'name' => 'Protocolar Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.protocolo.distribuir', 'name' => 'Distribuir Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.protocolo.numerar', 'name' => 'Numerar Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.aguardando-protocolo', 'name' => 'Aguardando Protocolo', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.protocolar', 'name' => 'Protocolar', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.protocolos-hoje', 'name' => 'Protocolos de Hoje', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.estatisticas-protocolo', 'name' => 'Estatísticas do Protocolo', 'module' => 'proposicoes'],
            
            // Consultas - Visualização necessária para o trabalho
            ['route' => 'proposicoes.index', 'name' => 'Consultar Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes'],
            ['route' => 'parlamentares.index', 'name' => 'Lista de Parlamentares', 'module' => 'parlamentares'],
            ['route' => 'parlamentares.show', 'name' => 'Perfil do Parlamentar', 'module' => 'parlamentares'],
            ['route' => 'usuarios.index', 'name' => 'Usuários do Sistema', 'module' => 'usuarios'],
            ['route' => 'usuarios.show', 'name' => 'Perfil do Usuário', 'module' => 'usuarios'],
            
            // Relatórios - Específicos do protocolo
            ['route' => 'relatorios.protocolo', 'name' => 'Relatório de Protocolo', 'module' => 'relatorios'],
            ['route' => 'relatorios.tramitacao', 'name' => 'Relatório de Tramitação', 'module' => 'relatorios'],
        ];

        $this->applyScreenPermissions('PROTOCOLO', $screens);
        $this->command->info('  ✅ ' . count($screens) . ' telas configuradas para PROTOCOLO');
    }

    /**
     * Configurar telas para RELATOR
     * Foco: Análise e relatoria de proposições atribuídas
     */
    private function configureRelatorScreens(): void
    {
        $this->command->info('📝 Configurando telas para RELATOR...');
        
        $screens = [
            // Core - Acesso básico
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard'],
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile'],
            
            // Proposições - Análise e relatoria
            ['route' => 'proposicoes.index', 'name' => 'Proposições Atribuídas', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.relator.index', 'name' => 'Área de Relatoria', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.relator.parecer', 'name' => 'Emitir Parecer', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.relator.aprovar', 'name' => 'Aprovar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.relator.rejeitar', 'name' => 'Rejeitar Proposição', 'module' => 'proposicoes'],
            
            // Consultas - Informações necessárias
            ['route' => 'parlamentares.index', 'name' => 'Lista de Parlamentares', 'module' => 'parlamentares'],
            ['route' => 'parlamentares.show', 'name' => 'Perfil do Parlamentar', 'module' => 'parlamentares'],
            ['route' => 'usuarios.index', 'name' => 'Usuários do Sistema', 'module' => 'usuarios'],
            ['route' => 'usuarios.show', 'name' => 'Perfil do Usuário', 'module' => 'usuarios'],
            ['route' => 'partidos.index', 'name' => 'Partidos', 'module' => 'partidos'],
            ['route' => 'partidos.show', 'name' => 'Detalhes do Partido', 'module' => 'partidos'],
            
            // Relatórios - Específicos da relatoria
            ['route' => 'relatorios.relatoria', 'name' => 'Relatório de Relatoria', 'module' => 'relatorios'],
        ];

        $this->applyScreenPermissions('RELATOR', $screens);
        $this->command->info('  ✅ ' . count($screens) . ' telas configuradas para RELATOR');
    }

    /**
     * Configurar telas para ASSESSOR
     * Foco: Suporte aos parlamentares na criação de proposições
     */
    private function configureAssessorScreens(): void
    {
        $this->command->info('👥 Configurando telas para ASSESSOR...');
        
        $screens = [
            // Core - Acesso básico
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard'],
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile'],
            
            // Proposições - Suporte limitado ao parlamentar assessorado
            ['route' => 'proposicoes.index', 'name' => 'Proposições do Parlamentar', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.create', 'name' => 'Criar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.edit', 'name' => 'Editar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.assessor.rascunho', 'name' => 'Rascunhos', 'module' => 'proposicoes'],
            
            // Consultas - Informações básicas
            ['route' => 'parlamentares.index', 'name' => 'Lista de Parlamentares', 'module' => 'parlamentares'],
            ['route' => 'parlamentares.show', 'name' => 'Perfil do Parlamentar', 'module' => 'parlamentares'],
            ['route' => 'usuarios.index', 'name' => 'Usuários do Sistema', 'module' => 'usuarios'],
            ['route' => 'usuarios.show', 'name' => 'Perfil do Usuário', 'module' => 'usuarios'],
            ['route' => 'partidos.index', 'name' => 'Partidos', 'module' => 'partidos'],
            ['route' => 'partidos.show', 'name' => 'Detalhes do Partido', 'module' => 'partidos'],
        ];

        $this->applyScreenPermissions('ASSESSOR', $screens);
        $this->command->info('  ✅ ' . count($screens) . ' telas configuradas para ASSESSOR');
    }

    /**
     * Configurar telas para EXPEDIENTE
     * Foco: Organização de pautas e gestão de sessões plenárias
     */
    private function configureExpedienteScreens(): void
    {
        $this->command->info('📋 Configurando telas para EXPEDIENTE...');
        
        $screens = [
            // Core - Acesso básico
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard'],
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile'],
            
            // Proposições - Visualização para organização de pautas
            ['route' => 'proposicoes.legislativo.index', 'name' => 'Consultar Proposições', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.aguardando-protocolo', 'name' => 'Aguardando Protocolo', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.protocolar', 'name' => 'Proposições para Protocolo', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.protocolos-hoje', 'name' => 'Protocolos de Hoje', 'module' => 'proposicoes'],
            
            // Gestão de Pautas - Funcionalidade principal
            ['route' => 'pautas.index', 'name' => 'Gerenciar Pautas', 'module' => 'pautas'],
            ['route' => 'pautas.create', 'name' => 'Criar Pauta', 'module' => 'pautas'],
            ['route' => 'pautas.edit', 'name' => 'Editar Pauta', 'module' => 'pautas'],
            ['route' => 'pautas.show', 'name' => 'Visualizar Pauta', 'module' => 'pautas'],
            ['route' => 'pautas.organizar', 'name' => 'Organizar Pauta', 'module' => 'pautas'],
            ['route' => 'pautas.publicar', 'name' => 'Publicar Pauta', 'module' => 'pautas'],
            
            // Sessões Plenárias - Gestão completa
            ['route' => 'sessoes.index', 'name' => 'Gerenciar Sessões', 'module' => 'sessoes'],
            ['route' => 'sessoes.create', 'name' => 'Criar Sessão', 'module' => 'sessoes'],
            ['route' => 'sessoes.edit', 'name' => 'Editar Sessão', 'module' => 'sessoes'],
            ['route' => 'sessoes.show', 'name' => 'Visualizar Sessão', 'module' => 'sessoes'],
            ['route' => 'sessoes.iniciar', 'name' => 'Iniciar Sessão', 'module' => 'sessoes'],
            ['route' => 'sessoes.encerrar', 'name' => 'Encerrar Sessão', 'module' => 'sessoes'],
            ['route' => 'sessoes.controlar-votacao', 'name' => 'Controlar Votação', 'module' => 'sessoes'],
            
            // Tramitação - Acompanhamento do fluxo
            ['route' => 'tramitacao.index', 'name' => 'Acompanhar Tramitação', 'module' => 'tramitacao'],
            ['route' => 'tramitacao.logs', 'name' => 'Logs de Tramitação', 'module' => 'tramitacao'],
            ['route' => 'tramitacao.relatorio', 'name' => 'Relatório de Tramitação', 'module' => 'tramitacao'],
            
            // Consultas - Informações necessárias
            ['route' => 'parlamentares.index', 'name' => 'Lista de Parlamentares', 'module' => 'parlamentares'],
            ['route' => 'parlamentares.show', 'name' => 'Perfil do Parlamentar', 'module' => 'parlamentares'],
            ['route' => 'usuarios.index', 'name' => 'Usuários do Sistema', 'module' => 'usuarios'],
            ['route' => 'usuarios.show', 'name' => 'Perfil do Usuário', 'module' => 'usuarios'],
            
            // Relatórios - Específicos da área
            ['route' => 'relatorios.expediente', 'name' => 'Relatório do Expediente', 'module' => 'relatorios'],
            ['route' => 'relatorios.sessoes', 'name' => 'Relatório de Sessões', 'module' => 'relatorios'],
            ['route' => 'relatorios.pautas', 'name' => 'Relatório de Pautas', 'module' => 'relatorios'],
        ];

        $this->applyScreenPermissions('EXPEDIENTE', $screens);
        $this->command->info('  ✅ ' . count($screens) . ' telas configuradas para EXPEDIENTE');
    }

    /**
     * Configurar telas para ASSESSOR_JURIDICO
     * Foco: Emissão de pareceres jurídicos sobre proposições
     */
    private function configureAssessorJuridicoScreens(): void
    {
        $this->command->info('⚖️  Configurando telas para ASSESSOR_JURIDICO...');
        
        $screens = [
            // Core - Acesso básico
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard'],
            ['route' => 'profile.edit', 'name' => 'Editar Perfil', 'module' => 'profile'],
            
            // Proposições - Análise jurídica (proposições protocoladas)
            ['route' => 'proposicoes.legislativo.index', 'name' => 'Proposições para Análise', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.protocolar', 'name' => 'Proposições Protocoladas', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.protocolos-hoje', 'name' => 'Protocolos de Hoje', 'module' => 'proposicoes'],
            ['route' => 'proposicoes.aguardando-protocolo', 'name' => 'Aguardando Protocolo', 'module' => 'proposicoes'],
            
            // Pareceres Jurídicos - Funcionalidade principal
            ['route' => 'pareceres.index', 'name' => 'Gerenciar Pareceres', 'module' => 'pareceres'],
            ['route' => 'pareceres.create', 'name' => 'Emitir Parecer', 'module' => 'pareceres'],
            ['route' => 'pareceres.edit', 'name' => 'Editar Parecer', 'module' => 'pareceres'],
            ['route' => 'pareceres.show', 'name' => 'Visualizar Parecer', 'module' => 'pareceres'],
            ['route' => 'pareceres.favoravel', 'name' => 'Parecer Favorável', 'module' => 'pareceres'],
            ['route' => 'pareceres.contrario', 'name' => 'Parecer Contrário', 'module' => 'pareceres'],
            ['route' => 'pareceres.emendas', 'name' => 'Sugerir Emendas', 'module' => 'pareceres'],
            ['route' => 'pareceres.finalizar', 'name' => 'Finalizar Parecer', 'module' => 'pareceres'],
            
            // Análise Jurídica - Ferramentas de apoio
            ['route' => 'juridico.biblioteca', 'name' => 'Biblioteca Jurídica', 'module' => 'juridico'],
            ['route' => 'juridico.jurisprudencia', 'name' => 'Jurisprudência', 'module' => 'juridico'],
            ['route' => 'juridico.legislacao', 'name' => 'Legislação de Referência', 'module' => 'juridico'],
            ['route' => 'juridico.precedentes', 'name' => 'Precedentes', 'module' => 'juridico'],
            
            // Tramitação - Acompanhamento específico
            ['route' => 'tramitacao.juridica', 'name' => 'Tramitação Jurídica', 'module' => 'tramitacao'],
            ['route' => 'tramitacao.prazos', 'name' => 'Controle de Prazos', 'module' => 'tramitacao'],
            
            // Consultas - Informações necessárias
            ['route' => 'parlamentares.index', 'name' => 'Lista de Parlamentares', 'module' => 'parlamentares'],
            ['route' => 'parlamentares.show', 'name' => 'Perfil do Parlamentar', 'module' => 'parlamentares'],
            ['route' => 'usuarios.index', 'name' => 'Usuários do Sistema', 'module' => 'usuarios'],
            ['route' => 'usuarios.show', 'name' => 'Perfil do Usuário', 'module' => 'usuarios'],
            
            // Relatórios - Específicos da área jurídica
            ['route' => 'relatorios.juridico', 'name' => 'Relatório Jurídico', 'module' => 'relatorios'],
            ['route' => 'relatorios.pareceres', 'name' => 'Relatório de Pareceres', 'module' => 'relatorios'],
            ['route' => 'relatorios.analise-legal', 'name' => 'Análise Legal', 'module' => 'relatorios'],
        ];

        $this->applyScreenPermissions('ASSESSOR_JURIDICO', $screens);
        $this->command->info('  ✅ ' . count($screens) . ' telas configuradas para ASSESSOR_JURIDICO');
    }

    /**
     * Aplicar permissões de tela para um tipo de usuário
     */
    private function applyScreenPermissions(string $roleName, array $screens): void
    {
        foreach ($screens as $screen) {
            ScreenPermission::updateOrCreate(
                [
                    'role_name' => $roleName,
                    'screen_route' => $screen['route'],
                ],
                [
                    'screen_name' => $screen['name'],
                    'screen_module' => $screen['module'],
                    'can_access' => true,
                ]
            );
        }
    }
}