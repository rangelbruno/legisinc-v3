<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProposicaoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'por_status' => $this->getGroupedCounts('status'),
                'por_tipo' => $this->getGroupedCounts('tipo'),
                'por_ano' => $this->getGroupedCounts('ano'),
                'estatisticas' => [
                    'rascunhos' => $this->collection->where('status', 'RASCUNHO')->count(),
                    'em_revisao' => $this->collection->where('status', 'EM_REVISAO')->count(),
                    'revisadas' => $this->collection->where('status', 'REVISADO')->count(),
                    'aguardando_assinatura' => $this->collection->where('status', 'AGUARDANDO_ASSINATURA')->count(),
                    'assinadas' => $this->collection->where('status', 'ASSINADO')->count(),
                    'protocoladas' => $this->collection->where('status', 'PROTOCOLADO')->count(),
                    'com_template' => $this->collection->filter(fn ($p) => ! empty($p->template_id))->count(),
                    'com_parecer' => $this->collection->where('tem_parecer', true)->count(),
                ],
                'resumo_recente' => [
                    'criadas_hoje' => $this->collection->filter(fn ($p) => $p->created_at?->isToday())->count(),
                    'criadas_semana' => $this->collection->filter(fn ($p) => $p->created_at?->isCurrentWeek())->count(),
                    'modificadas_hoje' => $this->collection->filter(fn ($p) => $p->updated_at?->isToday())->count(),
                ],
            ],
            'links' => [
                'self' => $request->url(),
                'criar_nova' => route('proposicoes.create'),
            ],
            'filters_available' => [
                'status' => [
                    'RASCUNHO' => 'Rascunho',
                    'EM_REVISAO' => 'Em Revisão',
                    'REVISADO' => 'Revisado',
                    'AGUARDANDO_ASSINATURA' => 'Aguardando Assinatura',
                    'ASSINADO' => 'Assinado',
                    'PROTOCOLADO' => 'Protocolado',
                ],
                'tipos' => $this->getAvailableTypes(),
                'anos' => $this->getAvailableYears(),
            ],
        ];
    }

    /**
     * Obter contagem agrupada por campo
     */
    private function getGroupedCounts(string $field): array
    {
        return $this->collection
            ->groupBy($field)
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();
    }

    /**
     * Obter tipos disponíveis na coleção
     */
    private function getAvailableTypes(): array
    {
        $tipos = [
            'projeto_lei_ordinaria' => 'Projeto de Lei Ordinária',
            'projeto_lei_complementar' => 'Projeto de Lei Complementar',
            'indicacao' => 'Indicação',
            'projeto_decreto_legislativo' => 'Projeto de Decreto Legislativo',
            'projeto_resolucao' => 'Projeto de Resolução',
            'mocao' => 'Moção',
        ];

        return $this->collection
            ->pluck('tipo')
            ->unique()
            ->mapWithKeys(fn ($tipo) => [$tipo => $tipos[$tipo] ?? $tipo])
            ->toArray();
    }

    /**
     * Obter anos disponíveis na coleção
     */
    private function getAvailableYears(): array
    {
        return $this->collection
            ->pluck('ano')
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }
}
