<?php

namespace App\DTOs\Parametro;

class CampoParametroDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $submodulo_id = null,
        public ?string $nome = null,
        public ?string $label = null,
        public ?string $tipo_campo = null,
        public ?string $descricao = null,
        public ?bool $obrigatorio = null,
        public ?string $valor_padrao = null,
        public ?array $opcoes = null,
        public ?array $validacao = null,
        public ?string $placeholder = null,
        public ?string $classe_css = null,
        public ?int $ordem = null,
        public ?bool $ativo = null,
        public ?SubmoduloParametroDTO $submodulo = null,
        public ?array $valores = null,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            submodulo_id: $data['submodulo_id'] ?? null,
            nome: $data['nome'] ?? null,
            label: $data['label'] ?? null,
            tipo_campo: $data['tipo_campo'] ?? null,
            descricao: $data['descricao'] ?? null,
            obrigatorio: $data['obrigatorio'] ?? null,
            valor_padrao: $data['valor_padrao'] ?? null,
            opcoes: $data['opcoes'] ?? null,
            validacao: $data['validacao'] ?? null,
            placeholder: $data['placeholder'] ?? null,
            classe_css: $data['classe_css'] ?? null,
            ordem: $data['ordem'] ?? null,
            ativo: $data['ativo'] ?? null,
            submodulo: isset($data['submodulo']) ? SubmoduloParametroDTO::fromArray($data['submodulo']) : null,
            valores: $data['valores'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'submodulo_id' => $this->submodulo_id,
            'nome' => $this->nome,
            'label' => $this->label,
            'tipo_campo' => $this->tipo_campo,
            'descricao' => $this->descricao,
            'obrigatorio' => $this->obrigatorio,
            'valor_padrao' => $this->valor_padrao,
            'opcoes' => $this->opcoes,
            'validacao' => $this->validacao,
            'placeholder' => $this->placeholder,
            'classe_css' => $this->classe_css,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo,
            'submodulo' => $this->submodulo?->toArray(),
            'valores' => $this->valores,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ], fn($value) => $value !== null);
    }

    public function toCreateArray(): array
    {
        return array_filter([
            'submodulo_id' => $this->submodulo_id,
            'nome' => $this->nome,
            'label' => $this->label,
            'tipo_campo' => $this->tipo_campo,
            'descricao' => $this->descricao,
            'obrigatorio' => $this->obrigatorio ?? false,
            'valor_padrao' => $this->valor_padrao,
            'opcoes' => $this->opcoes,
            'validacao' => $this->validacao,
            'placeholder' => $this->placeholder,
            'classe_css' => $this->classe_css,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo ?? true,
        ], fn($value) => $value !== null);
    }

    public function toUpdateArray(): array
    {
        return array_filter([
            'submodulo_id' => $this->submodulo_id,
            'nome' => $this->nome,
            'label' => $this->label,
            'tipo_campo' => $this->tipo_campo,
            'descricao' => $this->descricao,
            'obrigatorio' => $this->obrigatorio,
            'valor_padrao' => $this->valor_padrao,
            'opcoes' => $this->opcoes,
            'validacao' => $this->validacao,
            'placeholder' => $this->placeholder,
            'classe_css' => $this->classe_css,
            'ordem' => $this->ordem,
            'ativo' => $this->ativo,
        ], fn($value) => $value !== null);
    }

    public function getCaminhoCompleto(): string
    {
        $caminhoSubmodulo = $this->submodulo?->getCaminhoCompleto() ?? 'Submódulo';
        return "{$caminhoSubmodulo} > {$this->label}";
    }

    public function getValidationRules(): array
    {
        $rules = [];
        
        if ($this->obrigatorio) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        switch ($this->tipo_campo) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'datetime':
                $rules[] = 'date';
                break;
            case 'file':
                $rules[] = 'file';
                break;
        }

        if ($this->validacao) {
            $rules = array_merge($rules, $this->validacao);
        }

        return $rules;
    }

    public function isText(): bool
    {
        return $this->tipo_campo === 'text';
    }

    public function isEmail(): bool
    {
        return $this->tipo_campo === 'email';
    }

    public function isNumber(): bool
    {
        return $this->tipo_campo === 'number';
    }

    public function isTextarea(): bool
    {
        return $this->tipo_campo === 'textarea';
    }

    public function isSelect(): bool
    {
        return $this->tipo_campo === 'select';
    }

    public function isCheckbox(): bool
    {
        return $this->tipo_campo === 'checkbox';
    }

    public function isRadio(): bool
    {
        return $this->tipo_campo === 'radio';
    }

    public function isFile(): bool
    {
        return $this->tipo_campo === 'file';
    }

    public function isDate(): bool
    {
        return $this->tipo_campo === 'date';
    }

    public function isDatetime(): bool
    {
        return $this->tipo_campo === 'datetime';
    }

    public function hasOpcoes(): bool
    {
        return in_array($this->tipo_campo, ['select', 'radio', 'checkbox']) && !empty($this->opcoes);
    }
}