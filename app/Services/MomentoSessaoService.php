<?php

namespace App\Services;

use App\Models\Proposicao;
use App\Models\TipoProposicao;

class MomentoSessaoService
{
    /**
     * Mapeamento de tipos de proposição para momento da sessão
     * Baseado nas regras regimentais típicas do legislativo brasileiro
     */
    const MAPEAMENTO_TIPOS = [
        // EXPEDIENTE - Fase informativa e matérias simples
        'indicacao' => 'EXPEDIENTE',
        'requerimento' => 'EXPEDIENTE',
        'requerimento_simples' => 'EXPEDIENTE',
        'mocao' => 'EXPEDIENTE',
        'mocao_aplauso' => 'EXPEDIENTE',
        'mocao_louvor' => 'EXPEDIENTE',
        'mocao_repudio' => 'EXPEDIENTE',
        'mocao_congratulacao' => 'EXPEDIENTE',
        'comunicacao' => 'EXPEDIENTE',
        'correspondencia' => 'EXPEDIENTE',
        'voto_pesar' => 'EXPEDIENTE',
        'voto_aplauso' => 'EXPEDIENTE',
        'apresentacao_projeto' => 'EXPEDIENTE', // Apenas apresentação, sem debate
        
        // ORDEM DO DIA - Fase deliberativa
        'projeto_lei_ordinaria' => 'ORDEM_DO_DIA',
        'projeto_lei_complementar' => 'ORDEM_DO_DIA',
        'projeto_decreto_legislativo' => 'ORDEM_DO_DIA',
        'projeto_resolucao' => 'ORDEM_DO_DIA',
        'proposta_emenda_constitucional' => 'ORDEM_DO_DIA',
        'emenda_lei_organica' => 'ORDEM_DO_DIA',
        'veto' => 'ORDEM_DO_DIA',
        'parecer_comissao' => 'ORDEM_DO_DIA', // Para votação
        'emenda_plenario' => 'ORDEM_DO_DIA',
        'redacao_final' => 'ORDEM_DO_DIA',
        'requerimento_urgencia' => 'ORDEM_DO_DIA', // Requerimentos especiais
        'requerimento_regime_especial' => 'ORDEM_DO_DIA',
    ];

    /**
     * Regras específicas para determinação do momento
     */
    const REGRAS_ESPECIAIS = [
        // Requerimentos que dependem de parecer vão para Ordem do Dia
        'requerimento_com_parecer' => 'ORDEM_DO_DIA',
        'requerimento_quorum_qualificado' => 'ORDEM_DO_DIA',
        
        // Matérias em segunda discussão sempre vão para Ordem do Dia
        'segunda_discussao' => 'ORDEM_DO_DIA',
        
        // Matérias com urgência podem ir direto para Ordem do Dia
        'com_urgencia' => 'ORDEM_DO_DIA',
    ];

    /**
     * Determinar momento da sessão baseado no tipo da proposição
     */
    public static function determinarMomento(string $tipoProposicao, array $configuracoes = []): string
    {
        // Verificar mapeamento direto
        if (isset(self::MAPEAMENTO_TIPOS[$tipoProposicao])) {
            return self::MAPEAMENTO_TIPOS[$tipoProposicao];
        }

        // Aplicar regras especiais
        if (isset($configuracoes['tem_parecer']) && $configuracoes['tem_parecer']) {
            return 'ORDEM_DO_DIA';
        }

        if (isset($configuracoes['segunda_discussao']) && $configuracoes['segunda_discussao']) {
            return 'ORDEM_DO_DIA';
        }

        if (isset($configuracoes['urgencia']) && $configuracoes['urgencia']) {
            return 'ORDEM_DO_DIA';
        }

        // Lógica baseada em padrões comuns
        if (str_contains($tipoProposicao, 'projeto_')) {
            return 'ORDEM_DO_DIA';
        }

        if (str_contains($tipoProposicao, 'requerimento') && 
            !str_contains($tipoProposicao, 'simples')) {
            return 'ORDEM_DO_DIA';
        }

        if (in_array($tipoProposicao, ['mocao', 'indicacao', 'comunicacao'])) {
            return 'EXPEDIENTE';
        }

        // Padrão: se não conseguir determinar, marca como não classificado
        return 'NAO_CLASSIFICADO';
    }

    /**
     * Classificar proposição automaticamente
     */
    public static function classificarProposicao(Proposicao $proposicao): void
    {
        $configuracoes = [
            'tem_parecer' => $proposicao->tem_parecer,
            'urgencia' => false, // Pode ser expandido futuramente
        ];

        $momento = self::determinarMomento($proposicao->tipo, $configuracoes);
        
        $proposicao->update([
            'momento_sessao' => $momento
        ]);
    }

    /**
     * Reclassificar todas as proposições protocoladas
     */
    public static function reclassificarProposicoes(): int
    {
        $proposicoes = Proposicao::where('status', 'protocolado')
            ->whereIn('momento_sessao', ['NAO_CLASSIFICADO', null])
            ->get();

        $classificadas = 0;
        foreach ($proposicoes as $proposicao) {
            self::classificarProposicao($proposicao);
            $classificadas++;
        }

        return $classificadas;
    }

    /**
     * Obter proposições por momento da sessão
     */
    public static function obterProposicoesPorMomento(string $momento): \Illuminate\Database\Eloquent\Collection
    {
        return Proposicao::where('momento_sessao', $momento)
            ->where('status', 'protocolado')
            ->with(['autor', 'tipoProposicao'])
            ->orderBy('data_protocolo', 'asc')
            ->get();
    }

    /**
     * Obter estatísticas de momento da sessão
     */
    public static function obterEstatisticas(): array
    {
        $total = Proposicao::where('status', 'protocolado')->count();
        
        $expediente = Proposicao::where('status', 'protocolado')
            ->where('momento_sessao', 'EXPEDIENTE')
            ->count();
            
        $ordemDia = Proposicao::where('status', 'protocolado')
            ->where('momento_sessao', 'ORDEM_DO_DIA')
            ->count();
            
        $naoClassificado = Proposicao::where('status', 'protocolado')
            ->whereIn('momento_sessao', ['NAO_CLASSIFICADO', null])
            ->count();

        return [
            'total' => $total,
            'expediente' => $expediente,
            'ordem_dia' => $ordemDia,
            'nao_classificado' => $naoClassificado,
            'percentual_expediente' => $total > 0 ? round(($expediente / $total) * 100, 1) : 0,
            'percentual_ordem_dia' => $total > 0 ? round(($ordemDia / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Validar se proposição pode ser enviada para votação
     */
    public static function podeEnviarParaVotacao(Proposicao $proposicao): array
    {
        $erros = [];

        // Verificar se está protocolada
        if ($proposicao->status !== 'protocolado') {
            $erros[] = 'Proposição deve estar protocolada';
        }

        // Verificar se tem momento definido
        if (empty($proposicao->momento_sessao) || $proposicao->momento_sessao === 'NAO_CLASSIFICADO') {
            $erros[] = 'Momento da sessão deve estar definido';
        }

        // Verificar regras específicas para Ordem do Dia
        if ($proposicao->momento_sessao === 'ORDEM_DO_DIA') {
            // Projetos devem ter parecer (em muitos casos)
            if (str_contains($proposicao->tipo, 'projeto_') && !$proposicao->tem_parecer) {
                $erros[] = 'Projetos na Ordem do Dia geralmente precisam de parecer';
            }
        }

        return [
            'pode_enviar' => empty($erros),
            'erros' => $erros,
            'momento' => $proposicao->momento_sessao,
            'descricao_momento' => self::getDescricaoMomento($proposicao->momento_sessao)
        ];
    }

    /**
     * Obter descrição do momento da sessão
     */
    public static function getDescricaoMomento(string $momento): string
    {
        return match($momento) {
            'EXPEDIENTE' => 'Fase informativa - Leitura e votação de matérias simples',
            'ORDEM_DO_DIA' => 'Fase deliberativa - Debate e votação de projetos',
            'NAO_CLASSIFICADO' => 'Momento não definido - Necessita classificação',
            default => 'Momento não reconhecido'
        };
    }

    /**
     * Obter regras do momento da sessão
     */
    public static function getRegrasMomento(string $momento): array
    {
        if ($momento === 'EXPEDIENTE') {
            return [
                'pode_votar' => true,
                'permite_debate' => false,
                'tipos_permitidos' => [
                    'Requerimentos simples',
                    'Indicações',
                    'Moções',
                    'Comunicações',
                    'Correspondências',
                    'Apresentação de projetos (sem debate)'
                ],
                'observacoes' => 'Deliberações só quando não dependem de parecer nem interferem na Ordem do Dia'
            ];
        }
        
        if ($momento === 'ORDEM_DO_DIA') {
            return [
                'pode_votar' => true,
                'permite_debate' => true,
                'tipos_permitidos' => [
                    'Projetos de Lei',
                    'Projetos de Decreto Legislativo',
                    'Resoluções',
                    'Vetos',
                    'Pareceres de comissão',
                    'Emendas de plenário',
                    'Requerimentos com quórum qualificado'
                ],
                'observacoes' => 'Só entram matérias com pareceres prontos e prazos cumpridos'
            ];
        }
        
        return [
            'pode_votar' => false,
            'permite_debate' => false,
            'tipos_permitidos' => [],
            'observacoes' => 'Momento não definido'
        ];
    }
}