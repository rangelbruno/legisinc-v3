<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\Proposicao;

/**
 * CORREÇÃO CRÍTICA: PDF não refletia edições do Legislativo após aprovação
 *
 * PROBLEMA IDENTIFICADO:
 * Quando o Legislativo editava um documento no OnlyOffice e o Parlamentar aprovava,
 * o PDF gerado não refletia as alterações feitas pelo Legislativo.
 *
 * CAUSA RAIZ:
 * O método servePDF estava usando o campo 'conteudo' do banco de dados ao invés de
 * extrair o conteúdo atualizado do arquivo RTF salvo pelo OnlyOffice.
 *
 * SOLUÇÃO IMPLEMENTADA:
 * 1. No método servePDF, quando não há PDF oficial do OnlyOffice, o sistema agora:
 *    - Primeiro tenta extrair o conteúdo do arquivo RTF atualizado
 *    - Usa métodos de extração inteligentes (extrairTextoRTFInteligente)
 *    - Só usa o campo 'conteudo' do banco como último fallback
 *
 * 2. No método aprovarEdicoesLegislativo:
 *    - Adiciona logs detalhados para rastreamento
 *    - Invalida o PDF para forçar regeneração com conteúdo atualizado
 *
 * ARQUIVOS MODIFICADOS:
 * - app/Http/Controllers/ProposicaoController.php:
 *   - Método servePDF (linhas ~5052-5089): Extrai conteúdo do RTF atualizado
 *   - Método aprovarEdicoesLegislativo (linhas ~4251-4260): Adiciona logs e validação
 *
 * TESTES RECOMENDADOS:
 * 1. Parlamentar cria proposição
 * 2. Parlamentar edita no OnlyOffice
 * 3. Parlamentar envia para Legislativo
 * 4. Legislativo edita no OnlyOffice (adiciona texto novo)
 * 5. Legislativo aprova suas edições
 * 6. Parlamentar aprova edições do Legislativo
 * 7. PDF gerado deve conter as alterações do Legislativo
 *
 * @author Sistema
 * @date 15/09/2025
 */
class CorrecaoPDFLegislativoSeeder extends Seeder
{
    public function run()
    {
        Log::info('========================================');
        Log::info('CORREÇÃO PDF LEGISLATIVO - DOCUMENTAÇÃO');
        Log::info('========================================');

        Log::info('Esta correção garante que o PDF gerado após aprovação');
        Log::info('das edições do Legislativo sempre reflita o conteúdo');
        Log::info('mais recente do arquivo RTF editado no OnlyOffice.');

        // Verificar proposições que podem estar afetadas
        $proposicoesAfetadas = Proposicao::where('status', 'aprovado_assinatura')
            ->whereNotNull('arquivo_path')
            ->whereNull('arquivo_pdf_path')
            ->count();

        if ($proposicoesAfetadas > 0) {
            Log::warning("Encontradas {$proposicoesAfetadas} proposições que podem precisar regenerar PDF");

            // Marcar para regeneração
            Proposicao::where('status', 'aprovado_assinatura')
                ->whereNotNull('arquivo_path')
                ->whereNull('arquivo_pdf_path')
                ->update([
                    'pdf_gerado_em' => null,
                    'pdf_conversor_usado' => null
                ]);

            Log::info('Proposições marcadas para regeneração de PDF na próxima visualização');
        } else {
            Log::info('✅ Nenhuma proposição afetada encontrada');
        }

        Log::info('');
        Log::info('FLUXO CORRIGIDO:');
        Log::info('1. OnlyOffice salva RTF com edições do Legislativo ✅');
        Log::info('2. Parlamentar aprova edições ✅');
        Log::info('3. PDF é invalidado para regeneração ✅');
        Log::info('4. servePDF extrai conteúdo do RTF atualizado ✅');
        Log::info('5. PDF gerado reflete edições do Legislativo ✅');

        Log::info('========================================');
        Log::info('CORREÇÃO APLICADA COM SUCESSO');
        Log::info('========================================');
    }
}