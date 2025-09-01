<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateUniversalResource extends JsonResource
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
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'document_key' => $this->document_key,
            'status' => $this->status ?? 'ativo',

            // Conteúdo condicional (apenas para admins)
            'conteudo' => $this->when(
                $request->user()?->hasRole('ADMIN'),
                $this->conteudo
            ),

            // Estatísticas
            'tamanho_conteudo' => strlen($this->conteudo ?? ''),
            'tem_conteudo' => ! empty($this->conteudo),

            // Relacionamentos condicionais
            'updated_by' => $this->whenLoaded('updatedBy'),

            // Campos de data
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Permissões calculadas
            'pode_editar' => $this->when($request->user(), function () use ($request) {
                return $request->user()->hasRole(['ADMIN']) ||
                       $request->user()->can('update', $this->resource);
            }),

            'pode_visualizar' => $this->when($request->user(), function () use ($request) {
                return $request->user()->hasRole(['ADMIN', 'LEGISLATIVO']) ||
                       $request->user()->can('view', $this->resource);
            }),

            // URLs de ação
            'urls' => [
                'editor' => route('admin.templates.universal.editor', $this->id),
                'download' => route('api.templates.universal.download', $this->id),
                'self' => $request->url(),
            ],
        ];
    }
}
