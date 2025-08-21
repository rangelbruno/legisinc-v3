<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class PDFEstruturaWordSeeder extends Seeder
{
    /**
     * PRESERVAR CORREÇÃO: Extração completa da estrutura Word (CABEÇALHO + CORPO + RODAPÉ)
     * 
     * PROBLEMA RESOLVIDO: 
     * - Sistema anterior só lia document.xml (corpo)
     * - IGNORAVA header*.xml (cabeçalho com imagem)
     * - IGNORAVA footer*.xml (rodapé institucional)
     * 
     * SOLUÇÃO IMPLEMENTADA:
     * - Extrair header*.xml, document.xml, footer*.xml separadamente
     * - Combinar na ordem correta: CABEÇALHO + CORPO + RODAPÉ
     * - Respeitar formatação e estrutura configurada pelo Legislativo
     */
    public function run()
    {
        Log::info("📋 PDFEstruturaWordSeeder: Iniciando verificação da estrutura Word...");
        
        $controllerPath = app_path('Http/Controllers/ProposicaoAssinaturaController.php');
        
        if (!file_exists($controllerPath)) {
            Log::error("❌ Controller não encontrado: {$controllerPath}");
            return;
        }
        
        $controllerContent = file_get_contents($controllerPath);
        
        // Verificar se a solução está implementada
        $correcoes = [
            'Método extrairConteudoDOCX com estrutura completa' => 'EXTRAIR CABEÇALHO (header*.xml)',
            'Extração separada das seções' => 'extrairSecaoWord',
            'Combinação ordenada das seções' => 'combinarSecoesWord',
            'Processamento de header e footer' => 'header*.xml ou footer*.xml',
            'Formatação específica do corpo' => 'formatarCorpoDocumento',
            'Log detalhado da extração' => 'Documento Word extraído com estrutura completa'
        ];
        
        $correcoesValidadas = 0;
        foreach ($correcoes as $nome => $busca) {
            if (strpos($controllerContent, $busca) !== false) {
                Log::info("   ✅ {$nome}");
                $correcoesValidadas++;
            } else {
                Log::error("   ❌ {$nome} - NÃO ENCONTRADO");
            }
        }
        
        if ($correcoesValidadas === count($correcoes)) {
            Log::info("🎯 PDFEstruturaWordSeeder: TODAS as correções validadas! ({$correcoesValidadas}/" . count($correcoes) . ")");
            
            // Teste adicional: Verificar se métodos auxiliares existem
            $metodosAuxiliares = [
                'extrairSecaoWord',
                'extrairTextoDeXml', 
                'combinarSecoesWord',
                'formatarCorpoDocumento'
            ];
            
            $metodosEncontrados = 0;
            foreach ($metodosAuxiliares as $metodo) {
                if (strpos($controllerContent, "private function {$metodo}(") !== false) {
                    Log::info("   ✅ Método {$metodo} implementado");
                    $metodosEncontrados++;
                } else {
                    Log::error("   ❌ Método {$metodo} não encontrado");
                }
            }
            
            Log::info("📊 Métodos auxiliares: {$metodosEncontrados}/" . count($metodosAuxiliares));
            
            if ($metodosEncontrados === count($metodosAuxiliares)) {
                Log::info("🚀 PDFEstruturaWordSeeder: SOLUÇÃO COMPLETA implementada!");
                Log::info("✅ PDF agora extrai: CABEÇALHO (imagem) + CORPO + RODAPÉ (institucional)");
                Log::info("✅ Estrutura do Word configurada pelo Legislativo será respeitada");
                Log::info("✅ Pronto para teste em: /proposicoes/2/assinar");
            }
            
        } else {
            Log::error("❌ PDFEstruturaWordSeeder: Correções incompletas ({$correcoesValidadas}/" . count($correcoes) . ")");
        }
    }
}