<?php

namespace App\DTOs\Projeto;

class ProjetoDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $titulo = null,
        public ?string $numero = null,
        public ?int $ano = null,
        public ?string $tipo = null,
        public ?int $autorId = null,
        public ?int $relatorId = null,
        public ?int $comissaoId = null,
        public ?string $status = null,
        public ?string $urgencia = null,
        public ?string $resumo = null,
        public ?string $ementa = null,
        public ?string $conteudo = null,
        public ?int $versionAtual = null,
        public ?string $palavrasChave = null,
        public ?string $observacoes = null,
        public ?\DateTime $dataProtocolo = null,
        public ?\DateTime $dataLimiteTramitacao = null,
        public ?bool $ativo = null,
        public ?array $metadados = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            titulo: $data['titulo'] ?? null,
            numero: $data['numero'] ?? null,
            ano: $data['ano'] ?? null,
            tipo: $data['tipo'] ?? null,
            autorId: $data['autor_id'] ?? null,
            relatorId: $data['relator_id'] ?? null,
            comissaoId: $data['comissao_id'] ?? null,
            status: $data['status'] ?? 'rascunho',
            urgencia: $data['urgencia'] ?? 'normal',
            resumo: $data['resumo'] ?? null,
            ementa: $data['ementa'] ?? null,
            conteudo: $data['conteudo'] ?? null,
            versionAtual: $data['version_atual'] ?? 1,
            palavrasChave: $data['palavras_chave'] ?? null,
            observacoes: $data['observacoes'] ?? null,
            dataProtocolo: isset($data['data_protocolo']) ? new \DateTime($data['data_protocolo']) : null,
            dataLimiteTramitacao: isset($data['data_limite_tramitacao']) ? new \DateTime($data['data_limite_tramitacao']) : null,
            ativo: $data['ativo'] ?? true,
            metadados: $data['metadados'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'numero' => $this->numero,
            'ano' => $this->ano,
            'tipo' => $this->tipo,
            'autor_id' => $this->autorId,
            'relator_id' => $this->relatorId,
            'comissao_id' => $this->comissaoId,
            'status' => $this->status,
            'urgencia' => $this->urgencia,
            'resumo' => $this->resumo,
            'ementa' => $this->ementa,
            'conteudo' => $this->conteudo,
            'version_atual' => $this->versionAtual,
            'palavras_chave' => $this->palavrasChave,
            'observacoes' => $this->observacoes,
            'data_protocolo' => $this->dataProtocolo?->format('Y-m-d'),
            'data_limite_tramitacao' => $this->dataLimiteTramitacao?->format('Y-m-d'),
            'ativo' => $this->ativo,
            'metadados' => $this->metadados,
        ];
    }

    public function toCreateArray(): array
    {
        $array = $this->toArray();
        unset($array['id']); // Remove ID para criação
        return array_filter($array, fn($value) => $value !== null);
    }

    public function toUpdateArray(): array
    {
        $array = $this->toArray();
        unset($array['id']); // Remove ID para atualização
        return array_filter($array, fn($value) => $value !== null);
    }

    // Métodos de validação
    public function isValid(): bool
    {
        return !empty($this->titulo) && 
               !empty($this->ementa) && 
               !empty($this->tipo) && 
               !empty($this->autorId);
    }

    public function getValidationErrors(): array
    {
        $errors = [];

        if (empty($this->titulo)) {
            $errors['titulo'] = 'Título é obrigatório';
        }

        if (empty($this->ementa)) {
            $errors['ementa'] = 'Ementa é obrigatória';
        }

        if (empty($this->tipo)) {
            $errors['tipo'] = 'Tipo é obrigatório';
        }

        if (empty($this->autorId)) {
            $errors['autor_id'] = 'Autor é obrigatório';
        }

        if ($this->numero && !is_numeric($this->numero)) {
            $errors['numero'] = 'Número deve ser numérico';
        }

        if ($this->ano && ($this->ano < 1900 || $this->ano > date('Y') + 10)) {
            $errors['ano'] = 'Ano inválido';
        }

        return $errors;
    }

    // Métodos utilitários
    public function generateNumero(): string
    {
        if (!$this->numero || !$this->ano) {
            throw new \InvalidArgumentException('Número e ano são necessários para gerar número completo');
        }

        return sprintf('%s/%d', str_pad($this->numero, 4, '0', STR_PAD_LEFT), $this->ano);
    }

    public function isRascunho(): bool
    {
        return $this->status === 'rascunho';
    }

    public function isProtocolado(): bool
    {
        return $this->status === 'protocolado';
    }

    public function isUrgente(): bool
    {
        return in_array($this->urgencia, ['urgente', 'urgentissima']);
    }

    public function hasContent(): bool
    {
        return !empty($this->conteudo);
    }

    public function withDefaults(): self
    {
        return new self(
            id: $this->id,
            titulo: $this->titulo,
            numero: $this->numero,
            ano: $this->ano ?? (int) date('Y'),
            tipo: $this->tipo ?? 'projeto_lei_ordinaria',
            autorId: $this->autorId ?? auth()->id(),
            relatorId: $this->relatorId,
            comissaoId: $this->comissaoId,
            status: $this->status ?? 'rascunho',
            urgencia: $this->urgencia ?? 'normal',
            resumo: $this->resumo,
            ementa: $this->ementa,
            conteudo: $this->conteudo,
            versionAtual: $this->versionAtual ?? 1,
            palavrasChave: $this->palavrasChave,
            observacoes: $this->observacoes,
            dataProtocolo: $this->dataProtocolo,
            dataLimiteTramitacao: $this->dataLimiteTramitacao,
            ativo: $this->ativo ?? true,
            metadados: $this->metadados ?? [],
        );
    }
}