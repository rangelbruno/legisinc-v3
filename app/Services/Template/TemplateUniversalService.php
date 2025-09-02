<?php

namespace App\Services\Template;

use App\Models\Proposicao;
use App\Models\TemplateUniversal;
use App\Models\TipoProposicao;
use Illuminate\Support\Facades\Log;

class TemplateUniversalService
{
    public function __construct(
        private TemplateProcessorService $templateProcessor
    ) {}

    /**
     * Obter template universal padrão ou criar se não existir
     */
    public function getTemplateUniversal(): TemplateUniversal
    {
        return TemplateUniversal::getOrCreateDefault();
    }

    /**
     * Aplicar template universal a uma proposição
     */
    public function aplicarTemplateParaProposicao(Proposicao $proposicao): string
    {
        $template = $this->getTemplateUniversal();

        if (! $template || empty($template->conteudo)) {
            $errorMessage = 'Template universal não encontrado ou sem conteúdo';
            
            Log::error($errorMessage, [
                'proposicao_id' => $proposicao->id,
                'template_exists' => $template !== null,
                'template_has_content' => $template ? !empty($template->conteudo) : false,
                'template_id' => $template->id ?? null,
                'template_content_length' => $template ? strlen($template->conteudo) : 0,
            ]);

            // Em vez de template básico, lançar exception para mostrar erro real
            throw new \Exception($errorMessage . ' (Proposição ID: ' . $proposicao->id . ')');
        }

        // Obter tipo da proposição com fallback
        $tipoProposicao = $proposicao->tipoProposicao;
        if (!$tipoProposicao && $proposicao->tipo) {
            $tipoProposicao = \App\Models\TipoProposicao::where('nome', $proposicao->tipo)->first();
        }
        
        if (!$tipoProposicao) {
            throw new \Exception('Tipo de proposição não encontrado para proposição ID: ' . $proposicao->id);
        }

        // Preparar dados da proposição
        $dadosProposicao = $this->prepararDadosProposicao($proposicao, $tipoProposicao);

        // Usar TemplateProcessorService para processar variáveis corretamente (RTF + acentuação + imagem)
        $conteudoProcessado = $this->templateProcessor->processarVariaveisRTF($template->conteudo, $dadosProposicao);

        Log::info('Template universal aplicado com sucesso', [
            'proposicao_id' => $proposicao->id,
            'tipo' => $tipoProposicao->nome,
            'template_id' => $template->id,
        ]);

        return $conteudoProcessado;
    }

    /**
     * Preparar dados da proposição para substituição de variáveis
     */
    private function prepararDadosProposicao(Proposicao $proposicao, ?\App\Models\TipoProposicao $tipoProposicao = null): array
    {
        $tipoProposicao = $tipoProposicao ?: $proposicao->tipoProposicao;
        
        // Obter variáveis do sistema (incluindo municipio e rodape_texto)
        $templateVariableService = app(\App\Services\Template\TemplateVariableService::class);
        $variaveisGlobais = $templateVariableService->getTemplateVariables();
        
        $dados = [
            // Dados básicos da proposição
            'numero_proposicao' => $proposicao->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]',
            'tipo_proposicao' => $tipoProposicao ? strtoupper($tipoProposicao->nome) : strtoupper($proposicao->tipo ?: '[TIPO]'),
            'codigo_tipo' => $tipoProposicao ? $tipoProposicao->codigo : ($proposicao->tipo ?: 'unknown'),
            'ementa' => $proposicao->ementa ?: '[EMENTA A DEFINIR]',
            'texto' => $proposicao->conteudo ?: '[CONTEÚDO A DEFINIR]',
            'justificativa' => $proposicao->justificativa ?: '',

            // Dados do autor
            'autor_nome' => $proposicao->autor->name ?? '[AUTOR]',
            'autor_cargo' => 'Vereador',
            'autor_partido' => '[PARTIDO]', // Pode ser expandido futuramente

            // Dados de data
            'data_atual' => now()->format('d/m/Y'),
            'data_criacao' => $proposicao->created_at->format('d/m/Y'),
            'dia' => now()->format('d'),
            'mes' => now()->format('m'),
            'ano_atual' => now()->format('Y'),
            'mes_extenso' => $this->getMesExtenso(now()->format('n')),

            // Status e protocolo
            'status' => $proposicao->status,
            'protocolo' => $proposicao->numero_protocolo ?: '',
            'data_protocolo' => $proposicao->data_protocolo ? $proposicao->data_protocolo->format('d/m/Y') : '',

            // Dados específicos do tipo
            'categoria_tipo' => $tipoProposicao ? $this->getCategoriaTemplate($tipoProposicao) : 'GERAL',
            'preambulo_dinamico' => $tipoProposicao ? $this->getPreambuloDinamico($tipoProposicao) : 'A Câmara Municipal DECRETA:',
            'clausula_vigencia' => $tipoProposicao ? $this->getClausulaVigencia($tipoProposicao) : 'Esta Lei entra em vigor na data de sua publicação.',
        ];
        
        // Combinar dados da proposição com variáveis globais do sistema
        // Dados da proposição têm precedência sobre as globais
        $todasVariaveis = array_merge($variaveisGlobais, $dados);
        
        // Garantir que a imagem do cabeçalho seja processada corretamente
        if (!isset($todasVariaveis['imagem_cabecalho']) || empty($todasVariaveis['imagem_cabecalho'])) {
            $todasVariaveis['imagem_cabecalho'] = 'template/cabecalho.png';
        }
        
        return $todasVariaveis;
    }

    /**
     * Substituir variáveis de forma simples (sem conversão RTF)
     */
    private function substituirVariaveisSimples(string $conteudo, array $variaveis): string
    {
        // Ordenar variáveis por tamanho (maior primeiro) para evitar substituições parciais
        uksort($variaveis, function($a, $b) {
            return strlen($b) - strlen($a);
        });
        
        foreach ($variaveis as $variavel => $valor) {
            // Converter valor para string se necessário
            $valorString = is_string($valor) ? $valor : (string) $valor;
            
            // Formatos de variáveis a substituir
            $formatos = [
                '${' . $variavel . '}',  // Formato ${variavel}
                '$' . $variavel,         // Formato $variavel
            ];
            
            foreach ($formatos as $formato) {
                $conteudo = str_replace($formato, $valorString, $conteudo);
            }
        }
        
        return $conteudo;
    }

    /**
     * Obter mês por extenso
     */
    private function getMesExtenso(int $mes): string
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro',
        ];

        return $meses[$mes] ?? 'janeiro';
    }

    /**
     * Obter categoria do template baseada no tipo de proposição
     */
    private function getCategoriaTemplate(TipoProposicao $tipo): string
    {
        $codigo = strtolower($tipo->codigo);

        if (str_contains($codigo, 'projeto_lei')) {
            return 'PROJETO_LEGISLATIVO';
        }

        if (in_array($codigo, ['requerimento', 'indicacao', 'mocao'])) {
            return 'PROPOSICAO_PARLAMENTAR';
        }

        if (str_contains($codigo, 'emenda')) {
            return 'EMENDA';
        }

        return 'GERAL';
    }

    /**
     * Obter preâmbulo dinâmico baseado no tipo
     */
    private function getPreambuloDinamico(TipoProposicao $tipo): string
    {
        $codigo = strtolower($tipo->codigo);

        return match (true) {
            str_contains($codigo, 'projeto_lei_ordinaria') => 'A CÂMARA MUNICIPAL DECRETA:',
            str_contains($codigo, 'projeto_lei_complementar') => 'A CÂMARA MUNICIPAL DECRETA:',
            str_contains($codigo, 'projeto_resolucao') => 'A CÂMARA MUNICIPAL RESOLVE:',
            str_contains($codigo, 'projeto_decreto_legislativo') => 'A CÂMARA MUNICIPAL DECRETA:',
            $codigo === 'requerimento' => 'Requeiro, nos termos regimentais:',
            $codigo === 'indicacao' => 'Indico ao Senhor Prefeito Municipal:',
            $codigo === 'mocao' => 'A Câmara Municipal manifesta:',
            str_contains($codigo, 'emenda') => 'Emenda ao Projeto:',
            str_contains($codigo, 'parecer') => 'RELATÓRIO:',
            default => 'Considerando:'
        };
    }

    /**
     * Obter cláusula de vigência apropriada
     */
    private function getClausulaVigencia(TipoProposicao $tipo): string
    {
        $codigo = strtolower($tipo->codigo);

        return match (true) {
            str_contains($codigo, 'projeto_lei_ordinaria') => 'Esta lei entra em vigor na data de sua publicação.',
            str_contains($codigo, 'projeto_lei_complementar') => 'Esta lei complementar entra em vigor na data de sua publicação.',
            str_contains($codigo, 'projeto_resolucao') => 'Esta resolução entra em vigor na data de sua publicação.',
            str_contains($codigo, 'projeto_decreto_legislativo') => 'Este decreto legislativo entra em vigor na data de sua publicação.',
            default => ''
        };
    }

    /**
     * Criar template básico como fallback
     */
    private function criarTemplateBasico(Proposicao $proposicao): string
    {
        $dados = $this->prepararDadosProposicao($proposicao);

        return <<<RTF
{\rtf1\ansi\ansicpg65001\deff0 {\fonttbl {\f0 Arial;}}
\f0\fs24\sl360\slmult1 

\qc\b\fs26 {$dados['tipo_proposicao']} Nº {$dados['numero_proposicao']}\b0\fs24\par
\ql\par

\b EMENTA:\b0 {$dados['ementa']}\par
\par

{$dados['preambulo_dinamico']}\par
\par

{$dados['texto']}\par
\par

{$dados['clausula_vigencia']}\par
\par

{$dados['data_atual']}\par
\par

{$dados['autor_nome']}\par
{$dados['autor_cargo']}\par

}
RTF;
    }

    /**
     * Verificar se deve usar template universal
     */
    public function deveUsarTemplateUniversal(TipoProposicao $tipo): bool
    {
        $template = TemplateUniversal::getDefault();

        // Se não há template universal padrão ativo, usar sistema antigo
        if (! $template || ! $template->ativo) {
            return false;
        }

        // Verificar se o tipo específico tem um template customizado mais recente
        $templateEspecifico = $tipo->template;
        if ($templateEspecifico &&
            $templateEspecifico->ativo &&
            $templateEspecifico->updated_at > $template->updated_at) {

            Log::info('Usando template específico em vez do universal', [
                'tipo_id' => $tipo->id,
                'template_especifico' => $templateEspecifico->updated_at,
                'template_universal' => $template->updated_at,
            ]);

            return false;
        }

        return true;
    }

    /**
     * Migrar template específico para estrutura universal
     */
    public function migrarTemplateEspecifico(TipoProposicao $tipo): bool
    {
        $templateEspecifico = $tipo->template;
        if (! $templateEspecifico) {
            return false;
        }

        $templateUniversal = $this->getTemplateUniversal();

        // Se o template específico tem conteúdo mais recente, considerá-lo
        if ($templateEspecifico->conteudo &&
            $templateEspecifico->updated_at > $templateUniversal->updated_at) {

            Log::info('Template específico mais recente encontrado para migração', [
                'tipo_id' => $tipo->id,
                'template_id' => $templateEspecifico->id,
            ]);

            // Aqui você pode implementar lógica para mesclar ou atualizar o template universal
            // com elementos específicos do tipo
        }

        return true;
    }

    /**
     * Obter estatísticas do uso do template universal
     */
    public function getEstatisticas(): array
    {
        $template = TemplateUniversal::getDefault();
        if (! $template) {
            return [
                'template_universal_ativo' => false,
                'total_tipos_suportados' => 0,
                'ultima_atualizacao' => null,
            ];
        }

        $totalTipos = TipoProposicao::where('ativo', true)->count();
        $tiposComTemplateEspecifico = TipoProposicao::where('ativo', true)
            ->whereHas('template', function ($query) {
                $query->where('ativo', true);
            })->count();

        return [
            'template_universal_ativo' => $template->ativo,
            'template_universal_id' => $template->id,
            'total_tipos_proposicao' => $totalTipos,
            'tipos_com_template_especifico' => $tiposComTemplateEspecifico,
            'tipos_usando_universal' => $totalTipos - $tiposComTemplateEspecifico,
            'cobertura_universal' => $totalTipos > 0 ? round((($totalTipos - $tiposComTemplateEspecifico) / $totalTipos) * 100, 1) : 0,
            'ultima_atualizacao' => $template->updated_at,
            'atualizado_por' => $template->updatedBy->name ?? 'Sistema',
        ];
    }
}
