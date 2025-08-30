<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->when($this->canViewEmail($request), $this->email),
            'documento' => $this->when($this->canViewSensitiveData($request), $this->documento),
            'telefone' => $this->when($this->canViewSensitiveData($request), $this->telefone),
            'data_nascimento' => $this->when($this->canViewSensitiveData($request), $this->data_nascimento),
            'profissao' => $this->profissao,
            'cargo_atual' => $this->cargo_atual,
            'partido' => $this->partido,

            // Perfil/Role information
            'roles' => $this->getRoleNames(),
            'perfil_formatado' => $this->getPerfilFormatado(),
            'cor_perfil' => $this->getCorPerfil(),
            'nivel_hierarquico' => $this->getNivelHierarquico(),

            // Status
            'ativo' => $this->ativo,
            'ultimo_acesso' => $this->ultimo_acesso,

            // Avatar
            'avatar' => $this->avatar,
            'tem_foto_valida' => $this->temFotoValida(),

            // Relacionamentos condicionais
            'parlamentar' => $this->whenLoaded('parlamentar'),
            'proposicoes_autor_count' => $this->whenCounted('proposicoesAutor'),

            // Campos de perfil específico
            'is_admin' => $this->isAdmin(),
            'is_parlamentar' => $this->isParlamentar(),
            'is_legislativo' => $this->isLegislativo(),
            'is_protocolo' => $this->isProtocolo(),
            'is_expediente' => $this->isExpediente(),
            'is_assessor_juridico' => $this->isAssessorJuridico(),

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Verificar se pode visualizar email
     */
    private function canViewEmail(Request $request): bool
    {
        $user = $request->user();

        if (! $user) {
            return false;
        }

        // Admin vê tudo
        if ($user->isAdmin()) {
            return true;
        }

        // O próprio usuário pode ver seu email
        if ($this->id === $user->id) {
            return true;
        }

        // Legislativo pode ver emails para contato oficial
        if ($user->isLegislativo()) {
            return true;
        }

        return false;
    }

    /**
     * Verificar se pode visualizar dados sensíveis
     */
    private function canViewSensitiveData(Request $request): bool
    {
        $user = $request->user();

        if (! $user) {
            return false;
        }

        // Admin vê tudo
        if ($user->isAdmin()) {
            return true;
        }

        // O próprio usuário pode ver seus dados
        if ($this->id === $user->id) {
            return true;
        }

        return false;
    }
}
