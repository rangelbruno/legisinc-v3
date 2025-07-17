<?php

namespace App\DTOs\Admin;

use App\Models\Parametro;

class ParametroDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $codigo,
        public readonly ?string $descricao,
        public readonly int $grupoParametroId,
        public readonly string $grupoParametroNome,
        public readonly string $grupoParametroCodigo,
        public readonly int $tipoParametroId,
        public readonly string $tipoParametroNome,
        public readonly string $tipoParametroCodigo,
        public readonly ?string $valor,
        public readonly ?string $valorPadrao,
        public readonly mixed $valorFormatado,
        public readonly string $valorDisplay,
        public readonly ?array $configuracao,
        public readonly ?array $regrasValidacao,
        public readonly bool $obrigatorio,
        public readonly bool $editavel,
        public readonly bool $visivel,
        public readonly bool $ativo,
        public readonly int $ordem,
        public readonly ?string $helpText,
        public readonly string $createdAt,
        public readonly string $updatedAt
    ) {
    }

    public static function fromModel(Parametro $parametro): self
    {
        return new self(
            id: $parametro->id,
            nome: $parametro->nome,
            codigo: $parametro->codigo,
            descricao: $parametro->descricao,
            grupoParametroId: $parametro->grupo_parametro_id,
            grupoParametroNome: $parametro->grupoParametro->nome,
            grupoParametroCodigo: $parametro->grupoParametro->codigo,
            tipoParametroId: $parametro->tipo_parametro_id,
            tipoParametroNome: $parametro->tipoParametro->nome,
            tipoParametroCodigo: $parametro->tipoParametro->codigo,
            valor: $parametro->valor,
            valorPadrao: $parametro->valor_padrao,
            valorFormatado: $parametro->valor_formatado,
            valorDisplay: $parametro->valor_display,
            configuracao: $parametro->configuracao,
            regrasValidacao: $parametro->regras_validacao,
            obrigatorio: $parametro->obrigatorio,
            editavel: $parametro->editavel,
            visivel: $parametro->visivel,
            ativo: $parametro->ativo,
            ordem: $parametro->ordem,
            helpText: $parametro->help_text,
            createdAt: $parametro->created_at->format('d/m/Y H:i:s'),
            updatedAt: $parametro->updated_at->format('d/m/Y H:i:s')
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            nome: $data['nome'] ?? '',
            codigo: $data['codigo'] ?? '',
            descricao: $data['descricao'] ?? null,
            grupoParametroId: $data['grupo_parametro_id'] ?? 0,
            grupoParametroNome: $data['grupo_parametro_nome'] ?? '',
            grupoParametroCodigo: $data['grupo_parametro_codigo'] ?? '',
            tipoParametroId: $data['tipo_parametro_id'] ?? 0,
            tipoParametroNome: $data['tipo_parametro_nome'] ?? '',
            tipoParametroCodigo: $data['tipo_parametro_codigo'] ?? '',
            valor: $data['valor'] ?? null,
            valorPadrao: $data['valor_padrao'] ?? null,
            valorFormatado: $data['valor_formatado'] ?? null,
            valorDisplay: $data['valor_display'] ?? '',
            configuracao: $data['configuracao'] ?? null,
            regrasValidacao: $data['regras_validacao'] ?? null,
            obrigatorio: $data['obrigatorio'] ?? false,
            editavel: $data['editavel'] ?? true,
            visivel: $data['visivel'] ?? true,
            ativo: $data['ativo'] ?? true,
            ordem: $data['ordem'] ?? 0,
            helpText: $data['help_text'] ?? null,
            createdAt: $data['created_at'] ?? '',
            updatedAt: $data['updated_at'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'codigo' => $this->codigo,
            'descricao' => $this->descricao,
            'grupo_parametro_id' => $this->grupoParametroId,
            'grupo_parametro_nome' => $this->grupoParametroNome,
            'grupo_parametro_codigo' => $this->grupoParametroCodigo,
            'tipo_parametro_id' => $this->tipoParametroId,
            'tipo_parametro_nome' => $this->tipoParametroNome,
            'tipo_parametro_codigo' => $this->tipoParametroCodigo,
            'valor' => $this->valor,
            'valor_padrao' => $this->valorPadrao,
            'valor_formatado' => $this->valorFormatado,
            'valor_display' => $this->valorDisplay,
            'configuracao' => $this->configuracao,
            'regras_validacao' => $this->regrasValidacao,
            'obrigatorio' => $this->obrigatorio,
            'editavel' => $this->editavel,
            'visivel' => $this->visivel,
            'ativo' => $this->ativo,
            'ordem' => $this->ordem,
            'help_text' => $this->helpText,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }

    public function toFormArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'codigo' => $this->codigo,
            'descricao' => $this->descricao,
            'grupo_parametro_id' => $this->grupoParametroId,
            'tipo_parametro_id' => $this->tipoParametroId,
            'valor' => $this->valor,
            'valor_padrao' => $this->valorPadrao,
            'configuracao' => $this->configuracao,
            'regras_validacao' => $this->regrasValidacao,
            'obrigatorio' => $this->obrigatorio,
            'editavel' => $this->editavel,
            'visivel' => $this->visivel,
            'ativo' => $this->ativo,
            'ordem' => $this->ordem,
            'help_text' => $this->helpText
        ];
    }

    public function toCreateArray(): array
    {
        $data = $this->toFormArray();
        unset($data['id']);
        return $data;
    }

    public function toUpdateArray(): array
    {
        $data = $this->toFormArray();
        unset($data['id']);
        return $data;
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'codigo' => $this->codigo,
            'descricao' => $this->descricao,
            'grupo' => [
                'id' => $this->grupoParametroId,
                'nome' => $this->grupoParametroNome,
                'codigo' => $this->grupoParametroCodigo
            ],
            'tipo' => [
                'id' => $this->tipoParametroId,
                'nome' => $this->tipoParametroNome,
                'codigo' => $this->tipoParametroCodigo
            ],
            'valor' => $this->valor,
            'valor_formatado' => $this->valorFormatado,
            'valor_display' => $this->valorDisplay,
            'obrigatorio' => $this->obrigatorio,
            'editavel' => $this->editavel,
            'visivel' => $this->visivel,
            'ativo' => $this->ativo,
            'ordem' => $this->ordem,
            'help_text' => $this->helpText
        ];
    }

    public function toListArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'codigo' => $this->codigo,
            'grupo' => $this->grupoParametroNome,
            'tipo' => $this->tipoParametroNome,
            'valor_display' => $this->valorDisplay,
            'obrigatorio' => $this->obrigatorio,
            'editavel' => $this->editavel,
            'visivel' => $this->visivel,
            'ativo' => $this->ativo,
            'ordem' => $this->ordem
        ];
    }

    public function toCardArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'codigo' => $this->codigo,
            'descricao' => $this->descricao,
            'grupo' => [
                'nome' => $this->grupoParametroNome,
                'codigo' => $this->grupoParametroCodigo
            ],
            'tipo' => [
                'nome' => $this->tipoParametroNome,
                'codigo' => $this->tipoParametroCodigo
            ],
            'valor_display' => $this->valorDisplay,
            'status' => [
                'obrigatorio' => $this->obrigatorio,
                'editavel' => $this->editavel,
                'visivel' => $this->visivel,
                'ativo' => $this->ativo
            ],
            'ordem' => $this->ordem,
            'help_text' => $this->helpText
        ];
    }

    public function getStatusBadges(): array
    {
        $badges = [];

        if ($this->obrigatorio) {
            $badges[] = [
                'label' => 'Obrigatório',
                'class' => 'badge-danger'
            ];
        }

        if (!$this->editavel) {
            $badges[] = [
                'label' => 'Somente Leitura',
                'class' => 'badge-warning'
            ];
        }

        if (!$this->visivel) {
            $badges[] = [
                'label' => 'Oculto',
                'class' => 'badge-secondary'
            ];
        }

        if (!$this->ativo) {
            $badges[] = [
                'label' => 'Inativo',
                'class' => 'badge-dark'
            ];
        }

        return $badges;
    }

    public function getStatusIcon(): string
    {
        if (!$this->ativo) {
            return 'ki-cross text-danger';
        }

        if ($this->obrigatorio) {
            return 'ki-star text-warning';
        }

        if (!$this->editavel) {
            return 'ki-lock text-secondary';
        }

        if (!$this->visivel) {
            return 'ki-eye-slash text-muted';
        }

        return 'ki-check text-success';
    }

    public function getStatusColor(): string
    {
        if (!$this->ativo) {
            return 'danger';
        }

        if ($this->obrigatorio) {
            return 'warning';
        }

        if (!$this->editavel) {
            return 'secondary';
        }

        if (!$this->visivel) {
            return 'muted';
        }

        return 'success';
    }

    public function hasValor(): bool
    {
        return !is_null($this->valor) && $this->valor !== '';
    }

    public function isEditavel(): bool
    {
        return $this->editavel && $this->ativo;
    }

    public function isVisivel(): bool
    {
        return $this->visivel && $this->ativo;
    }

    public function getValorOuPadrao(): mixed
    {
        return $this->hasValor() ? $this->valorFormatado : $this->valorPadrao;
    }
}