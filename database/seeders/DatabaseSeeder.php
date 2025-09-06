<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Primeiro criar tabelas OnlyOffice e roles/permissões básicos
        $this->call([
            OnlyOfficeTablesSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);

        // BACKUP RTFs OnlyOffice ANTES de qualquer reset
        $this->call([
            BackupRTFsOnlyOfficeSeeder::class,
        ]);

        // PRESERVAÇÃO INTELIGENTE: Sistema automático v2.0 que detecta e preserva melhorias
        $this->call([
            SmartPreservationSeeder::class,
            PreservarDadosCriticosSeeder::class,
        ]);

        // Criar usuários do sistema com roles
        $this->call([
            SystemUsersSeeder::class,
            ParlamentarSeeder::class, // Vincular usuário parlamentar ao cadastro de parlamentar
        ]);

        // Seeders do sistema de parâmetros
        $this->call([
            ParametrosModulosFixedSeeder::class, // Criar módulos com IDs fixos
            TipoParametroSeeder::class,
            GrupoParametroSeeder::class,
            ParametroSeeder::class,
            ParametroPermissionSeeder::class,
            ParametroExemploSeeder::class,
            DadosGeraisParametrosSeeder::class,
            DadosGeraisValoresSeeder::class,
            FixDadosGeraisCamposSeeder::class, // CRÍTICO: Criar campos necessários para Dados Gerais
        ]);

        // Seeder de permissões de menu
        $this->call([
            MenuPermissionsSeeder::class,
            DocsPermissionsSeeder::class, // Permissões para acesso à documentação
        ]);

        // Seeder de tipos de proposição
        $this->call([
            TipoProposicaoCompletoSeeder::class,
        ]);

        // Seeders de templates
        $this->call([
            ParametrosTemplatesSeeder::class,
            DocumentoModeloTemplateSeeder::class,
            TipoProposicaoTemplatesSeeder::class,
        ]);

        // Seeder de permissões de tela por tipo de usuário
        $this->call([
            UserTypeScreenPermissionsSeeder::class,
        ]);

        // Seeder de configurações de IA
        $this->call([
            AIProvidersSeeder::class,
            AIConfigurationSeeder::class,
        ]);

        // Seeder de menus otimizados (deve ser executado por último para limpar e otimizar)
        $this->call([
            OptimizedMenuPermissionsSeeder::class,
        ]);

        // NOVO: Seeder de PDF de Assinatura Otimizado
        $this->call([
            PDFAssinaturaOptimizadoSeeder::class,
        ]);

        // CORREÇÃO DEFINITIVA: Estrutura Word completa (cabeçalho + corpo + rodapé)
        $this->call([
            PDFEstruturaWordSeeder::class,
            PDFErrorLogFixSeeder::class,
        ]);

        // UI: Otimizações de interface do usuário
        $this->call([
            UIOptimizationsSeeder::class,
        ]);

        // TEMPLATE UNIVERSAL: Correção automática do problema de codificação
        $this->call([
            TemplateUniversalFixSeeder::class,
            TemplateUniversalPrioridadeSeeder::class, // ✅ Garantir prioridade sempre
            TemplateUniversalSimplificadoSeeder::class, // Template simplificado com variáveis essenciais
            TemplateUniversalRTFFixSeeder::class, // PERMANENTE: Correções RTF e imagem cabeçalho
            RegenerarRTFProposicoesSeeder::class, // ✅ Regenerar RTFs após reset
            RestaurarRTFsOnlyOfficeSeeder::class, // ✅ Restaurar RTFs OnlyOffice do backup
        ]);

        // ONLYOFFICE CALLBACK: Correção de extração RTF (resolve conteúdo corrompido)
        $this->call([
            OnlyOfficeCallbackRTFFixSeeder::class, // PERMANENTE: Extração RTF sem corrupção de fontes
        ]);

        // UI: Correções de botões OnlyOffice (previne captura incorreta de cliques)
        $this->call([
            UIButtonsFixSeeder::class,
        ]);

        // FINAL: Limpeza de código debug
        $this->call([
            LimpezaCodigoDebugSeeder::class,
        ]);

        // Correção do botão Assinar Documento
        $this->call([
            ButtonAssinaturaFixSeeder::class,
        ]);

        // Correção de PDFs com protocolo e assinatura
        $this->call([
            CorrecaoPDFProtocoloAssinaturaSeeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias2Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias4Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias6Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias12Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias14Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias16Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias18Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias20Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias22Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias24Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias26Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias28Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias30Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias32Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias34Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias36Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias38Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias40Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias42Seeder::class,
        ]);

        // ÚLTIMO: Correções HTML de estrutura de botões (DEVE ser executado POR ÚLTIMO)
        $this->call([
            HTMLButtonsFixSeeder::class,
        ]);

        // Interface Vue.js para proposições (performance e tempo real)
        $this->call([
            VueInterfaceSeeder::class,
        ]);

        // Sistema de permissões por role (FINAL)
        $this->call([
            RolePermissionSystemSeeder::class,
        ]);

        // Melhorias de UI do botão Assinar Documento
        $this->call([
            ButtonAssinaturaUISeeder::class,
        ]);

        // CORREÇÃO CRÍTICA: Garantir que a tag de fechamento </a> esteja presente
        $this->call([
            ButtonAssinaturaTagFixSeeder::class,
        ]);

        // VALIDAÇÃO E CORREÇÃO COMPLETA: Estrutura HTML de todos os botões
        $this->call([
            HTMLStructureValidationSeeder::class,
        ]);

        // CORREÇÃO DEFINITIVA: Botão de assinatura (executar por último)
        $this->call([
            FixAssinaturaButtonSeeder::class,
        ]);

        // Interface Vue.js para tela de assinatura (performance otimizada)
        $this->call([
            AssinaturaVueInterfaceSeeder::class,
        ]);

        // PERFORMANCE: Otimizações OnlyOffice (extração e caracteres especiais)
        $this->call([
            OnlyOfficePerformanceOptimizationSeeder::class,
            OnlyOfficeSalvamentoFixSeeder::class,
            OnlyOfficeCallbackSkipExtractionSeeder::class,
        ]);

        // Correções de status e otimização de PDF
        $this->call([
            CorrecaoStatusPDFSeeder::class,
            PreservarCorrecoesPDFSeeder::class, // ✅ Validar correções críticas v2.1
        ]);

        // Correções de formatação do Legislativo no PDF
        $this->call([
            PDFFormatacaoLegislativoSeeder::class,
        ]);

        // CORREÇÃO: Preservação de parágrafos no OnlyOffice
        $this->call([
            ParagrafosOnlyOfficeSeeder::class,
        ]);

        // CORREÇÃO: Visualização de assinatura e protocolo no PDF
        $this->call([
            CorrecaoPDFAssinaturaSeeder::class,
        ]);

        // LARAVEL BOOST: Correção robusta de validação OnlyOffice (DEFINITIVA)
        $this->call([
            OnlyOfficeRobustValidationSeeder::class,
        ]);

        // Processar imagens dos templates admin
        $this->command->info('');
        $this->command->info('🖼️ Processando imagens dos templates admin...');
        Artisan::call('templates:process-images');
        $this->command->info('✅ Imagens dos templates processadas!');

        $this->command->info('');
        $this->command->info('🎉 ===============================================');
        $this->command->info('✅ SISTEMA LEGISINC CONFIGURADO COM SUCESSO!');
        $this->command->info('🎉 ===============================================');
        $this->command->info('');
        $this->command->info('📄 OnlyOffice DocumentServer: Tabelas inicializadas');
        $this->command->info('🏛️ Dados Gerais da Câmara: Módulos, campos e valores padrão configurados');
        $this->command->info('📝 Templates de Proposições: 23 tipos criados com LC 95/1998');
        $this->command->info('🖼️ Sistema de Imagens RTF: Configurado e funcional');
        $this->command->info('🔤 Codificação UTF-8: Acentuação portuguesa corrigida');
        $this->command->info('🎯 PDF de Assinatura: Sistema otimizado com extração robusta de DOCX');
        $this->command->info('🎨 Interface Otimizada: Botões OnlyOffice e Assinatura com UI moderna');
        $this->command->info('🧹 Código Debug: Automaticamente removido - versão de produção limpa');
        $this->command->info('');
        $this->command->info('🔧 ===== CONFIGURAÇÕES DISPONÍVEIS =====');
        $this->command->info('📊 Dados Gerais: /parametros-dados-gerais-camara');
        $this->command->info('📝 Templates: /admin/templates');
        $this->command->info('✍️ Assinatura & QR Code: /parametros-templates-assinatura-qrcode');
        $this->command->info('⚙️ Parâmetros Avançados: /parametros');
        $this->command->info('');
        $this->command->info('👥 ===== USUÁRIOS DO SISTEMA =====');
        $this->command->info('🔧 Admin: bruno@sistema.gov.br - Senha: 123456');
        $this->command->info('🏛️ Parlamentar: jessica@sistema.gov.br - Senha: 123456');
        $this->command->info('⚖️ Legislativo: joao@sistema.gov.br - Senha: 123456');
        $this->command->info('📋 Protocolo: roberto@sistema.gov.br - Senha: 123456');
        $this->command->info('📝 Expediente: expediente@sistema.gov.br - Senha: 123456');
        $this->command->info('⚖️ Assessor Jurídico: juridico@sistema.gov.br - Senha: 123456');
        $this->command->info('');
        $this->command->info('🏛️ ===== CÂMARA CONFIGURADA =====');
        $this->command->info('📍 Nome: Câmara Municipal de Caraguatatuba');
        $this->command->info('🏠 Endereço: Praça da República, 40, Centro');
        $this->command->info('📞 Telefone: (12) 3882-5588');
        $this->command->info('🌐 Website: www.camaracaraguatatuba.sp.gov.br');
        $this->command->info('');
        $this->command->info('📋 ===== TEMPLATES EDITÁVEIS FUNCIONAIS =====');
        $this->command->info('✅ Templates editáveis no admin (/admin/templates)');
        $this->command->info('✅ Variáveis substituídas automaticamente');
        $this->command->info('✅ Suporte a RTF Unicode do OnlyOffice');
        $this->command->info('✅ Conteúdo do banco prioritário sobre arquivos');
        $this->command->info('✅ Processamento de ${variavel}, $variavel e $\\{variavel\\}');
        $this->command->info('✅ Decoder Unicode para templates RTF complexos');
        $this->command->info('✅ Cabeçalho com imagem automática');
        $this->command->info('✅ Acentuação portuguesa funcionando');
        $this->command->info('✅ OnlyOffice integrado e operacional');
        $this->command->info('✅ Sistema de Assinatura Digital e QR Code configurado');
        $this->command->info('✅ Variáveis de assinatura disponíveis no editor de templates');
        $this->command->info('');
        // RESTAURAÇÃO AUTOMÁTICA: Recuperar melhorias que podem ter sido sobrescritas
        $smartSeeder = new SmartPreservationSeeder();
        $smartSeeder->setCommand($this->command);
        $smartSeeder->restaurarPreservacoes();
        
        $this->command->info('');
        $this->command->info('🛡️ ===== SISTEMA DE PRESERVAÇÃO AUTOMÁTICA =====');
        $this->command->info('✅ Melhorias detectadas e preservadas automaticamente');
        $this->command->info('✅ Sistema inteligente de backup/restore ativado');
        $this->command->info('📋 Use: php artisan migrate:safe --fresh --seed');
        $this->command->info('🔍 Detectar mudanças: php artisan melhorias:generate --auto');
        $this->command->info('');
        $this->command->info('🚀 Sistema pronto para uso! Acesse: http://localhost:8001');
        $this->command->info('');
    }
}
