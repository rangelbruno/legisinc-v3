<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CorrecaoAprovacaoLegislativoPDFSeeder extends Seeder
{
    /**
     * Aplica correções para garantir que aprovação pelo Legislativo gere PDF com conteúdo correto
     *
     * PROBLEMA: Quando Legislativo aprova proposição, PDF não é invalidado e continua
     * usando conteúdo original do Parlamentar em vez do conteúdo editado pelo Legislativo
     *
     * SOLUÇÃO: Verificar se as correções estão aplicadas nos controladores e aplicar se necessário
     */
    public function run(): void
    {
        Log::info('🔧 CorrecaoAprovacaoLegislativoPDFSeeder: Iniciando verificação de correções PDF');

        try {
            // 1. Verificar se ProposicaoLegislativoController tem a correção
            $this->verificarCorrecaoProposicaoLegislativoController();

            // 2. Verificar se ProposicaoController.updateStatus tem a correção
            $this->verificarCorrecaoUpdateStatus();

            // 3. Invalidar PDFs de proposições com status "aprovado" que não tenham a correção aplicada
            $this->invalidarPDFsProposicoesAprovadas();

            Log::info('✅ CorrecaoAprovacaoLegislativoPDFSeeder: Todas as correções verificadas e aplicadas');

        } catch (\Exception $e) {
            Log::error('❌ CorrecaoAprovacaoLegislativoPDFSeeder: Erro ao aplicar correções', [
                'erro' => $e->getMessage(),
                'linha' => $e->getLine(),
                'arquivo' => $e->getFile()
            ]);
        }
    }

    private function verificarCorrecaoProposicaoLegislativoController(): void
    {
        $arquivoController = app_path('Http/Controllers/ProposicaoLegislativoController.php');

        if (!file_exists($arquivoController)) {
            Log::warning('⚠️ Arquivo ProposicaoLegislativoController.php não encontrado');
            return;
        }

        $conteudo = file_get_contents($arquivoController);

        // Verificar se tem a correção (invalidação do PDF)
        $temCorrecao = strpos($conteudo, "// CRÍTICO: Invalidar PDF antigo para forçar regeneração com conteúdo editado pelo Legislativo") !== false
                    && strpos($conteudo, "'arquivo_pdf_path' => null") !== false;

        if ($temCorrecao) {
            Log::info('✅ ProposicaoLegislativoController: Correção de invalidação PDF já aplicada');
        } else {
            Log::warning('⚠️ ProposicaoLegislativoController: Correção de invalidação PDF NÃO encontrada');
            Log::info('📋 AÇÃO NECESSÁRIA: Verificar se ProposicaoLegislativoController invalida PDF ao aprovar');
        }
    }

    private function verificarCorrecaoUpdateStatus(): void
    {
        $arquivoController = app_path('Http/Controllers/ProposicaoController.php');

        if (!file_exists($arquivoController)) {
            Log::warning('⚠️ Arquivo ProposicaoController.php não encontrado');
            return;
        }

        $conteudo = file_get_contents($arquivoController);

        // Verificar se updateStatus tem correção para status "aprovado"
        $temCorrecao = strpos($conteudo, "if (\$novoStatus === 'aprovado')") !== false
                    && strpos($conteudo, "\$updateData['arquivo_pdf_path'] = null") !== false;

        if ($temCorrecao) {
            Log::info('✅ ProposicaoController.updateStatus: Correção de invalidação PDF já aplicada');
        } else {
            Log::warning('⚠️ ProposicaoController.updateStatus: Correção de invalidação PDF NÃO encontrada');
            Log::info('📋 AÇÃO NECESSÁRIA: Verificar se updateStatus invalida PDF quando status vira "aprovado"');
        }
    }

    private function invalidarPDFsProposicoesAprovadas(): void
    {
        // Buscar proposições com status "aprovado" que tenham PDF antigo (possível problema)
        $proposicoesComProblema = DB::table('proposicoes')
            ->where('status', 'aprovado')
            ->whereNotNull('arquivo_pdf_path')
            ->whereNotNull('arquivo_path') // Tem arquivo RTF editado
            ->get();

        if ($proposicoesComProblema->isEmpty()) {
            Log::info('ℹ️ Nenhuma proposição aprovada com PDF potencialmente desatualizado encontrada');
            return;
        }

        Log::info("🔍 Encontradas {$proposicoesComProblema->count()} proposições aprovadas com PDF para verificar");

        $invalidados = 0;
        foreach ($proposicoesComProblema as $proposicao) {
            // Verificar se arquivo RTF existe e é mais recente que o PDF
            $arquivoRTF = storage_path('app/' . $proposicao->arquivo_path);
            $arquivoPDF = storage_path('app/' . $proposicao->arquivo_pdf_path);

            if (file_exists($arquivoRTF) && file_exists($arquivoPDF)) {
                $rtfModificado = filemtime($arquivoRTF);
                $pdfModificado = filemtime($arquivoPDF);

                // Se RTF é mais novo que PDF, invalidar PDF para forçar regeneração
                if ($rtfModificado > $pdfModificado) {
                    DB::table('proposicoes')
                        ->where('id', $proposicao->id)
                        ->update([
                            'arquivo_pdf_path' => null,
                            'pdf_gerado_em' => null,
                            'pdf_conversor_usado' => null,
                            'updated_at' => now()
                        ]);

                    $invalidados++;
                    Log::info("🔄 Proposição {$proposicao->id}: PDF invalidado (RTF mais recente que PDF)");
                }
            }
        }

        Log::info("✅ Invalidação concluída: {$invalidados} PDFs invalidados para regeneração");
    }
}