<?php

namespace App\DTOs\Parlamentar;

class ParlamentarDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $partido,
        public readonly string $status,
        public readonly string $cargo,
        public readonly string $telefone,
        public readonly string $email,
        public readonly string $dataNascimento,
        public readonly string $profissao,
        public readonly string $escolaridade,
        public readonly array $comissoes,
        public readonly array $mandatos,
        public readonly string $createdAt,
        public readonly string $updatedAt
    ) {}
    
    /**
     * Criar DTO a partir de array de dados
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            nome: $data['nome'] ?? '',
            partido: $data['partido'] ?? '',
            status: $data['status'] ?? 'ativo',
            cargo: $data['cargo'] ?? '',
            telefone: $data['telefone'] ?? '',
            email: $data['email'] ?? '',
            dataNascimento: $data['data_nascimento'] ?? '',
            profissao: $data['profissao'] ?? '',
            escolaridade: $data['escolaridade'] ?? '',
            comissoes: $data['comissoes'] ?? [],
            mandatos: $data['mandatos'] ?? [],
            createdAt: $data['created_at'] ?? '',
            updatedAt: $data['updated_at'] ?? ''
        );
    }
    
    /**
     * Converter DTO para array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'partido' => $this->partido,
            'status' => $this->status,
            'cargo' => $this->cargo,
            'telefone' => $this->telefone,
            'email' => $this->email,
            'data_nascimento' => $this->dataNascimento,
            'profissao' => $this->profissao,
            'escolaridade' => $this->escolaridade,
            'comissoes' => $this->comissoes,
            'mandatos' => $this->mandatos,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
    
    /**
     * Obter nome formatado
     */
    public function getNomeCompleto(): string
    {
        return $this->nome;
    }
    
    /**
     * Obter partido em maiúsculas
     */
    public function getPartidoFormatado(): string
    {
        return strtoupper($this->partido);
    }
    
    /**
     * Verificar se está ativo
     */
    public function isAtivo(): bool
    {
        return $this->status === 'ativo';
    }
    
    /**
     * Obter número de comissões
     */
    public function getTotalComissoes(): int
    {
        return count($this->comissoes);
    }
    
    /**
     * Obter mandato atual
     */
    public function getMandatoAtual(): ?array
    {
        foreach ($this->mandatos as $mandato) {
            if (($mandato['status'] ?? '') === 'atual') {
                return $mandato;
            }
        }
        return null;
    }
    
    /**
     * Verificar se é da mesa diretora
     */
    public function isMesaDiretora(): bool
    {
        return in_array('Mesa Diretora', $this->comissoes);
    }
    
    /**
     * Obter dados para formulário
     */
    public function toFormArray(): array
    {
        return [
            'nome' => $this->nome,
            'partido' => $this->partido,
            'cargo' => $this->cargo,
            'telefone' => $this->telefone,
            'email' => $this->email,
            'data_nascimento' => $this->dataNascimento,
            'profissao' => $this->profissao,
            'escolaridade' => $this->escolaridade,
            'status' => $this->status,
            'comissoes' => $this->comissoes
        ];
    }
}