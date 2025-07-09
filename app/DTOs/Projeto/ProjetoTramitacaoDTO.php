<?php

namespace App\DTOs\Projeto;

class ProjetoTramitacaoDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $projetoId = null,
        public ?string $etapa = null,
        public ?string $acao = null,
        public ?int $responsavelId = null,
        public ?int $comissaoId = null,
        public ?string $orgaoDestino = null,
        public ?string $observacoes = null,
        public ?string $despacho = null,
        public ?\DateTime $prazo = null,
        public ?\DateTime $dataInicio = null,
        public ?\DateTime $dataFim = null,
        public ?int $diasTramitacao = null,
        public ?string $status = null,
        public ?bool $urgente = null,
        public ?int $ordem = null,
        public ?array $dadosComplementares = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            projetoId: $data['projeto_id'] ?? null,
            etapa: $data['etapa'] ?? null,
            acao: $data['acao'] ?? null,
            responsavelId: $data['responsavel_id'] ?? null,
            comissaoId: $data['comissao_id'] ?? null,
            orgaoDestino: $data['orgao_destino'] ?? null,
            observacoes: $data['observacoes'] ?? null,
            despacho: $data['despacho'] ?? null,
            prazo: isset($data['prazo']) ? new \DateTime($data['prazo']) : null,
            dataInicio: isset($data['data_inicio']) ? new \DateTime($data['data_inicio']) : null,
            dataFim: isset($data['data_fim']) ? new \DateTime($data['data_fim']) : null,
            diasTramitacao: $data['dias_tramitacao'] ?? null,
            status: $data['status'] ?? 'pendente',
            urgente: $data['urgente'] ?? false,
            ordem: $data['ordem'] ?? null,
            dadosComplementares: $data['dados_complementares'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'projeto_id' => $this->projetoId,
            'etapa' => $this->etapa,
            'acao' => $this->acao,
            'responsavel_id' => $this->responsavelId,
            'comissao_id' => $this->comissaoId,
            'orgao_destino' => $this->orgaoDestino,
            'observacoes' => $this->observacoes,
            'despacho' => $this->despacho,
            'prazo' => $this->prazo?->format('Y-m-d'),
            'data_inicio' => $this->dataInicio?->format('Y-m-d H:i:s'),
            'data_fim' => $this->dataFim?->format('Y-m-d H:i:s'),
            'dias_tramitacao' => $this->diasTramitacao,
            'status' => $this->status,
            'urgente' => $this->urgente,
            'ordem' => $this->ordem,
            'dados_complementares' => $this->dadosComplementares,
        ];
    }

    public function toCreateArray(): array
    {
        $array = $this->toArray();
        unset($array['id']);
        return array_filter($array, fn($value) => $value !== null);
    }

    public function isValid(): bool
    {
        return !empty($this->projetoId) && 
               !empty($this->etapa) && 
               !empty($this->acao);
    }

    public function getValidationErrors(): array
    {
        $errors = [];

        if (empty($this->projetoId)) {
            $errors['projeto_id'] = 'Projeto é obrigatório';
        }

        if (empty($this->etapa)) {
            $errors['etapa'] = 'Etapa é obrigatória';
        }

        if (empty($this->acao)) {
            $errors['acao'] = 'Ação é obrigatória';
        }

        if ($this->prazo && $this->prazo < new \DateTime()) {
            $errors['prazo'] = 'Prazo não pode ser no passado';
        }

        if ($this->dataFim && $this->dataInicio && $this->dataFim < $this->dataInicio) {
            $errors['data_fim'] = 'Data fim não pode ser anterior à data início';
        }

        return $errors;
    }

    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }

    public function isEmAndamento(): bool
    {
        return $this->status === 'em_andamento';
    }

    public function isConcluido(): bool
    {
        return $this->status === 'concluido';
    }

    public function isCancelado(): bool
    {
        return $this->status === 'cancelado';
    }

    public function temPrazo(): bool
    {
        return $this->prazo !== null;
    }

    public function estaDentroDosPrazos(): bool
    {
        if (!$this->temPrazo()) {
            return true;
        }

        $dataReferencia = $this->dataFim ?? new \DateTime();
        return $dataReferencia <= $this->prazo;
    }

    public function getDiasRestantes(): ?int
    {
        if (!$this->temPrazo() || $this->isConcluido()) {
            return null;
        }

        $hoje = new \DateTime();
        return $hoje->diff($this->prazo)->days * ($hoje < $this->prazo ? 1 : -1);
    }

    public function getDuracaoEmDias(): ?int
    {
        if (!$this->dataInicio) {
            return null;
        }

        $dataFim = $this->dataFim ?? new \DateTime();
        return $this->dataInicio->diff($dataFim)->days;
    }

    public function withDefaults(): self
    {
        return new self(
            id: $this->id,
            projetoId: $this->projetoId,
            etapa: $this->etapa,
            acao: $this->acao,
            responsavelId: $this->responsavelId,
            comissaoId: $this->comissaoId,
            orgaoDestino: $this->orgaoDestino,
            observacoes: $this->observacoes,
            despacho: $this->despacho,
            prazo: $this->prazo,
            dataInicio: $this->dataInicio ?? new \DateTime(),
            dataFim: $this->dataFim,
            diasTramitacao: $this->diasTramitacao,
            status: $this->status ?? 'pendente',
            urgente: $this->urgente ?? false,
            ordem: $this->ordem,
            dadosComplementares: $this->dadosComplementares ?? [],
        );
    }

    // Factory methods para etapas específicas
    public static function criarProtocolo(int $projetoId, int $responsavelId): self
    {
        return new self(
            projetoId: $projetoId,
            etapa: 'protocolo',
            acao: 'criado',
            responsavelId: $responsavelId,
            status: 'em_andamento',
            dataInicio: new \DateTime(),
        );
    }

    public static function criarDistribuicao(int $projetoId, int $comissaoId, int $responsavelId): self
    {
        return new self(
            projetoId: $projetoId,
            etapa: 'distribuicao',
            acao: 'enviado',
            comissaoId: $comissaoId,
            responsavelId: $responsavelId,
            status: 'pendente',
        );
    }

    public static function criarRelatoria(int $projetoId, int $relatorId, int $comissaoId): self
    {
        return new self(
            projetoId: $projetoId,
            etapa: 'relatoria',
            acao: 'recebido',
            responsavelId: $relatorId,
            comissaoId: $comissaoId,
            status: 'em_andamento',
            dataInicio: new \DateTime(),
        );
    }
}