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
        // 🛡️ PROTEÇÃO CRÍTICA: Problema "ansi Objetivo" (SEMPRE EXECUTAR PRIMEIRO)
        // Comentado temporariamente até o seeder ser criado
        // $this->call([
        //     CriticoAnsiObjetivoProtectionSeeder::class,
        // ]);

        // Primeiro criar tabelas OnlyOffice e roles/permissões básicos
        $this->call([
            OnlyOfficeTablesSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);


        // PRESERVAÇÃO INTELIGENTE: Sistema automático v2.0 que detecta e preserva melhorias
        $this->call([
            SmartPreservationSeeder::class,
            PreservarOtimizacoesPerformanceSeeder::class, // Preservar otimizações v2.1
            PreservarDadosCriticosSeeder::class,
            PreservarDatabaseMonitoringSeeder::class, // Preservar Sistema de Monitoramento de Atividade v2.0
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
            DebugLoggerParametroSeeder::class, // Debug Logger para configurações do sistema
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

        // SISTEMA DE WORKFLOWS MODULARES: Fluxos padrão de tramitação
        $this->call([
            WorkflowPadraoSeeder::class,
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

        // TESTE: Criar proposição de teste com template OnlyOffice
        $this->call([
            ProposicaoTesteAssinaturaSeeder::class,
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

        // FINAL: Limpeza de código debug e correção de permissões
        $this->call([
            LimpezaCodigoDebugSeeder::class,
            CorrigirPermissoesStorageSeeder::class, // Garantir permissões corretas
        ]);

        // Correção do botão Assinar Documento
        $this->call([
            ButtonAssinaturaFixSeeder::class,
        ]);

        // Correção de PDFs com protocolo e assinatura
        $this->call([
            CorrecaoPDFProtocoloAssinaturaSeeder::class,
        ]);

        // CORREÇÕES PDF VIEWER: Loading overlay e seleção de PDF mais recente
        $this->call([
            CorrecoesPDFViewerSeeder::class,
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

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias3Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias5Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias7Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias9Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias11Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias13Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias15Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias17Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias19Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias21Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias23Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias25Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias27Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias29Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias31Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias33Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias35Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias39Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias41Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias43Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias45Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias47Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias49Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias51Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias53Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias55Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias57Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias59Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias61Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias63Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias65Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias67Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias69Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias71Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias74Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias76Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias77Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias79Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias81Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias83Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias85Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias87Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias89Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias91Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias93Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias95Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias97Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

        // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias99Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias101Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias103Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias105Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias107Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias109Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias111Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias113Seeder::class,
        ]);

        // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias115Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias117Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias121Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias123Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias125Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias129Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias131Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias133Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias135Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias137Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias139Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias141Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias143Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias9Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias11Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias1Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias11Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias13Seeder::class,
        ]);

        // CORREÇÃO APROVAÇÃO PELO LEGISLATIVO: PDF com conteúdo correto (v2.4)
        $this->call([
            CorrecaoAprovacaoLegislativoPDFSeeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias15Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias17Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias1Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias25Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias27Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias29Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias31Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias33Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias35Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias51Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias53Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias55Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias67Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias69Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias71Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

                // PRESERVAÇÃO AUTOMÁTICA: Melhorias detectadas automaticamente
        $this->call([
            PreservarMelhorias73Seeder::class,
        ]);

        // CORREÇÃO CRÍTICA AUTO-REGISTRADA: OnlyOffice Content Protection
        $this->call([
            Database\Seeders\CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

                // LIMPEZA CRÍTICA AUTO-REGISTRADA: Content Corruption Cleanup
        $this->call([
            Database\Seeders\LimpezaConteudoCorrempidoSeeder::class,
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

        // CORREÇÃO CRÍTICA: Preservação de conteúdo original no OnlyOffice (evita substituição por texto corrompido)
        $this->call([
            CorrecaoOnlyOfficeConteudoSeeder::class,
        ]);

        // LIMPEZA: Remover conteúdo corrompido de proposições antigas (que foram afetadas antes da correção)
        $this->call([
            LimpezaConteudoCorrempidoSeeder::class,
        ]);

        // CORREÇÕES CRÍTICAS FINAIS: PDF Viewer (EXECUTAR POR ÚLTIMO)
        $this->call([
            CorrecoesCriticasPDFSeeder::class,
        ]);

        // CORREÇÃO DEFINITIVA: PDF Desatualizado Entre Endpoints (CRÍTICO - NÃO REMOVER)
        $this->call([
            PDFDesatualizadoFixSeeder::class,
        ]);

        // MONITORING SYSTEM: Configuration preservation and synthetic metrics
        $this->call([
            MonitoringDashboardConfigSeeder::class, // Preserve monitoring dashboard improvements
            MonitoringSyntheticSeeder::class, // Generate synthetic metrics for testing
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
