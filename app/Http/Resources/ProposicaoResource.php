<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProposicaoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'tipo_formatado' => $this->tipo_formatado,
            'ementa' => $this->ementa,
            'conteudo' => $this->when($this->canViewContent($request), $this->conteudo),
            'status' => $this->status,
            'status_formatado' => $this->status_formatado,
            'status_color' => $this->getStatusColor(),
            'numero' => $this->when($this->numero, $this->numero),
            'numero_protocolo' => $this->numero_protocolo,
            'numero_sequencial' => $this->numero_sequencial,
            'ano' => $this->ano,

            // Relacionamentos condicionais
            'autor' => new UserResource($this->whenLoaded('autor')),
            'revisor' => new UserResource($this->whenLoaded('revisor')),
            'funcionario_protocolo' => new UserResource($this->whenLoaded('funcionarioProtocolo')),
            'template' => $this->whenLoaded('template'),
            'tipo_proposicao' => $this->whenLoaded('tipoProposicao'),
            'parecer_juridico' => $this->whenLoaded('parecerJuridico'),

            // Contadores condicionais
            'logs_tramitacao_count' => $this->whenCounted('logstramitacao'),
            'itens_pauta_count' => $this->whenCounted('itensPauta'),

            // Campos de data
            'data_protocolo' => $this->data_protocolo,
            'data_assinatura' => $this->data_assinatura,
            'enviado_revisao_em' => $this->enviado_revisao_em,
            'revisado_em' => $this->revisado_em,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'ultima_modificacao' => $this->ultima_modificacao,

            // Campos calculados/validações
            'pode_editar' => $this->when($request->user(), fn () => $this->podeSerEditada()),
            'pode_excluir' => $this->when($request->user(), fn () => $this->podeSerExcluida()),
            'pode_assinar' => $this->when($request->user(), fn () => $this->podeSerAssinada()),
            'pode_enviar_revisao' => $this->when($request->user(), fn () => $this->podeSerEnviadaParaRevisao()),
            'foi_assinada' => $this->foiAssinada(),
            'foi_protocolada' => $this->foiProtocolada(),
            'tem_parecer' => $this->temParecer(),
            'esta_em_pauta' => $this->estaEmPauta(),
            'usa_template' => $this->usa_template,

            // Arquivos
            'arquivo_path' => $this->arquivo_path,
            'arquivo_pdf_path' => $this->arquivo_pdf_path,
            'pdf_path' => $this->pdf_path,
            'pdf_assinado_path' => $this->pdf_assinado_path,

            // Campos específicos do sistema
            'momento_sessao' => $this->momento_sessao,
            'momento_sessao_formatado' => $this->getMomentoSessaoFormatado(),
            'cor_momento_sessao' => $this->getCorMomentoSessao(),
            'anexos' => $this->anexos,
            'total_anexos' => $this->total_anexos,
            'comissoes_destino' => $this->comissoes_destino,
            'observacoes_edicao' => $this->observacoes_edicao,
            'observacoes_protocolo' => $this->observacoes_protocolo,
            'variaveis_template' => $this->when($this->usa_template, $this->variaveis_template),
        ];
    }

    /**
     * Verificar se pode visualizar conteúdo completo
     */
    private function canViewContent(Request $request): bool
    {
        $user = $request->user();

        if (! $user) {
            return false;
        }

        // Admin vê tudo
        if ($user->isAdmin()) {
            return true;
        }

        // Autor sempre pode ver
        if ($this->autor_id === $user->id) {
            return true;
        }

        // Legislativo pode ver proposições em revisão/revisadas
        if ($user->isLegislativo() && in_array($this->status, ['EM_REVISAO', 'REVISADO', 'AGUARDANDO_ASSINATURA'])) {
            return true;
        }

        // Protocolo pode ver proposições protocoladas
        if ($user->isProtocolo() && $this->foi_protocolada) {
            return true;
        }

        return false;
    }
}
