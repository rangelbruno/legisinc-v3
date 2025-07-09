<?php

namespace App\DTOs\User;

use Carbon\Carbon;

class UserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $documento,
        public readonly ?string $telefone,
        public readonly ?Carbon $dataNascimento,
        public readonly ?string $profissao,
        public readonly ?string $cargoAtual,
        public readonly ?string $partido,
        public readonly array $preferencias,
        public readonly bool $ativo,
        public readonly ?Carbon $ultimoAcesso,
        public readonly ?string $avatar,
        public readonly array $perfis,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt
    ) {}

    /**
     * Criar DTO a partir de um modelo User
     */
    public static function fromModel($user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            documento: $user->documento,
            telefone: $user->telefone,
            dataNascimento: $user->data_nascimento,
            profissao: $user->profissao,
            cargoAtual: $user->cargo_atual,
            partido: $user->partido,
            preferencias: $user->preferencias ?? [],
            ativo: $user->ativo,
            ultimoAcesso: $user->ultimo_acesso,
            avatar: $user->avatar,
            perfis: $user->getRoleNames()->toArray(),
            createdAt: $user->created_at,
            updatedAt: $user->updated_at
        );
    }

    /**
     * Converter para array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'documento' => $this->documento,
            'telefone' => $this->telefone,
            'data_nascimento' => $this->dataNascimento?->format('Y-m-d'),
            'profissao' => $this->profissao,
            'cargo_atual' => $this->cargoAtual,
            'partido' => $this->partido,
            'preferencias' => $this->preferencias,
            'ativo' => $this->ativo,
            'ultimo_acesso' => $this->ultimoAcesso?->format('Y-m-d H:i:s'),
            'avatar' => $this->avatar,
            'perfis' => $this->perfis,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Obter perfil principal
     */
    public function getPerfilPrincipal(): ?string
    {
        return $this->perfis[0] ?? null;
    }

    /**
     * Verificar se tem perfil
     */
    public function temPerfil(string $perfil): bool
    {
        return in_array($perfil, $this->perfis);
    }

    /**
     * Verificar se está ativo
     */
    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    /**
     * Obter iniciais do nome
     */
    public function getIniciais(): string
    {
        $nomes = explode(' ', $this->name);
        $iniciais = '';
        foreach (array_slice($nomes, 0, 2) as $nome) {
            $iniciais .= strtoupper(substr($nome, 0, 1));
        }
        return $iniciais;
    }

    /**
     * Obter nome formatado
     */
    public function getNomeFormatado(): string
    {
        return $this->name;
    }

    /**
     * Obter idade
     */
    public function getIdade(): ?int
    {
        if (!$this->dataNascimento) {
            return null;
        }

        return $this->dataNascimento->diffInYears(now());
    }

    /**
     * Obter status formatado
     */
    public function getStatusFormatado(): string
    {
        return $this->ativo ? 'Ativo' : 'Inativo';
    }

    /**
     * Obter cor do status
     */
    public function getCorStatus(): string
    {
        return $this->ativo ? 'success' : 'danger';
    }

    /**
     * Obter último acesso formatado
     */
    public function getUltimoAcessoFormatado(): string
    {
        if (!$this->ultimoAcesso) {
            return 'Nunca';
        }

        return $this->ultimoAcesso->diffForHumans();
    }

    /**
     * Verificar se é parlamentar
     */
    public function isParlamentar(): bool
    {
        return $this->temPerfil('PARLAMENTAR') || $this->temPerfil('RELATOR');
    }

    /**
     * Verificar se é admin
     */
    public function isAdmin(): bool
    {
        return $this->temPerfil('ADMIN');
    }

    /**
     * Verificar se é assessor
     */
    public function isAssessor(): bool
    {
        return $this->temPerfil('ASSESSOR');
    }

    /**
     * Obter documento formatado
     */
    public function getDocumentoFormatado(): string
    {
        if (!$this->documento) {
            return '';
        }

        // Formato CPF: 000.000.000-00
        if (strlen($this->documento) === 11) {
            return substr($this->documento, 0, 3) . '.' . 
                   substr($this->documento, 3, 3) . '.' . 
                   substr($this->documento, 6, 3) . '-' . 
                   substr($this->documento, 9, 2);
        }

        return $this->documento;
    }

    /**
     * Obter telefone formatado
     */
    public function getTelefoneFormatado(): string
    {
        if (!$this->telefone) {
            return '';
        }

        // Remove caracteres não numéricos
        $numeros = preg_replace('/\D/', '', $this->telefone);

        // Formato celular: (00) 00000-0000
        if (strlen($numeros) === 11) {
            return '(' . substr($numeros, 0, 2) . ') ' . 
                   substr($numeros, 2, 5) . '-' . 
                   substr($numeros, 7, 4);
        }

        // Formato fixo: (00) 0000-0000
        if (strlen($numeros) === 10) {
            return '(' . substr($numeros, 0, 2) . ') ' . 
                   substr($numeros, 2, 4) . '-' . 
                   substr($numeros, 6, 4);
        }

        return $this->telefone;
    }

    /**
     * Obter data de nascimento formatada
     */
    public function getDataNascimentoFormatada(): string
    {
        if (!$this->dataNascimento) {
            return '';
        }

        return $this->dataNascimento->format('d/m/Y');
    }

    /**
     * Obter avatar ou iniciais
     */
    public function getAvatarOuIniciais(): string
    {
        return $this->avatar ?: $this->getIniciais();
    }

    /**
     * Converter para JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}