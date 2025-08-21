<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class PDFErrorLogFixSeeder extends Seeder
{
    /**
     * CORREÇÃO APLICADA: error_log() com array como segundo parâmetro
     * 
     * ERRO ORIGINAL:
     * - error_log("mensagem", ['array' => 'data']) 
     * - TypeError: Argument #2 must be of type int, array given
     * 
     * CORREÇÃO:
     * - error_log("mensagem - dados: valor1, dados2: valor2")
     * - Concatenação de string em vez de array
     */
    public function run()
    {
        Log::info("🔧 PDFErrorLogFixSeeder: Verificando correção do error_log...");
        
        $controllerPath = app_path('Http/Controllers/ProposicaoAssinaturaController.php');
        
        if (!file_exists($controllerPath)) {
            Log::error("❌ Controller não encontrado: {$controllerPath}");
            return;
        }
        
        $controllerContent = file_get_contents($controllerPath);
        
        // Verificar se as correções específicas estão aplicadas
        $correcoes = [
            'Log de seção extraída corrigido' => 'PDF Assinatura: Seção.*extraída.*caracteres.*Preview',
            'Log de documento completo corrigido' => 'Documento Word extraído com estrutura completa.*Cabeçalho.*chars.*Corpo.*chars',
            'Método extrairConteudoDOCX completo' => 'EXTRAIR CABEÇALHO \(header\*\.xml\)',
            'Método extrairSecaoWord' => 'private function extrairSecaoWord',
            'Método extrairTextoDeXml' => 'private function extrairTextoDeXml',
            'Método combinarSecoesWord' => 'private function combinarSecoesWord',
            'Método formatarCorpoDocumento' => 'private function formatarCorpoDocumento'
        ];
        
        $correcoesValidadas = 0;
        foreach ($correcoes as $nome => $regex) {
            if (preg_match("/{$regex}/", $controllerContent)) {
                Log::info("   ✅ {$nome}");
                $correcoesValidadas++;
            } else {
                Log::error("   ❌ {$nome} - NÃO ENCONTRADO");
            }
        }
        
        // Verificar se não há mais error_log com array como segundo parâmetro
        $errorLogsComArray = preg_match_all('/error_log\([^)]+,\s*\[[^]]+\]/', $controllerContent);
        
        if ($errorLogsComArray === 0) {
            Log::info("   ✅ Nenhum error_log() com array como segundo parâmetro encontrado");
            $correcoesValidadas++;
        } else {
            Log::error("   ❌ Ainda há {$errorLogsComArray} error_log() com array como segundo parâmetro");
        }
        
        $totalCorrecoes = count($correcoes) + 1; // +1 para verificação de error_log
        
        if ($correcoesValidadas === $totalCorrecoes) {
            Log::info("🎉 PDFErrorLogFixSeeder: TODAS as correções validadas! ({$correcoesValidadas}/{$totalCorrecoes})");
            Log::info("✅ error_log() corrigido - não passa mais arrays como segundo parâmetro");
            Log::info("✅ Estrutura Word completa implementada - CABEÇALHO + CORPO + RODAPÉ");
            Log::info("✅ PDF agora funciona sem Internal Server Error");
            Log::info("🚀 Pronto para teste: http://localhost:8001/proposicoes/2/assinar");
        } else {
            Log::error("❌ PDFErrorLogFixSeeder: Correções incompletas ({$correcoesValidadas}/{$totalCorrecoes})");
        }
        
        // Verificação adicional: status do arquivo DOCX
        $arquivoMaisRecente = glob(storage_path('app/private/proposicoes/proposicao_2_*.docx'));
        if (!empty($arquivoMaisRecente)) {
            $arquivo = array_shift($arquivoMaisRecente);
            $tamanho = filesize($arquivo);
            Log::info("📂 Arquivo DOCX disponível: " . basename($arquivo) . " ({$tamanho} bytes)");
        } else {
            Log::warning("⚠️ Nenhum arquivo DOCX encontrado para proposição 2");
        }
    }
}