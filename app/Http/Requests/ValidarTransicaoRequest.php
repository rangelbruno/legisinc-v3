<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\WorkflowTransicao;
use App\Models\DocumentoWorkflowStatus;
use App\Rules\CondicaoTransicaoValida;

class ValidarTransicaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'documento_type' => 'required|string',
            'documento_id' => 'required|integer',
            'transicao_id' => [
                'required',
                'integer',
                'exists:workflow_transicoes,id',
                function ($attribute, $value, $fail) {
                    $this->validarTransicaoDisponivel($value, $fail);
                }
            ],
            'dados_adicionais' => 'sometimes|array',
            'dados_adicionais.condicoes' => ['sometimes', 'array', new CondicaoTransicaoValida()],
            'observacoes' => 'sometimes|string|max:1000'
        ];
    }

    protected function validarTransicaoDisponivel($transicaoId, $fail): void
    {
        $transicao = WorkflowTransicao::find($transicaoId);
        
        if (!$transicao) {
            $fail('Transição não encontrada.');
            return;
        }

        // Verificar se existe um documento com workflow ativo
        $statusAtual = DocumentoWorkflowStatus::where('documento_type', $this->input('documento_type'))
            ->where('documento_id', $this->input('documento_id'))
            ->where('ativo', true)
            ->first();

        if (!$statusAtual) {
            $fail('Documento não possui workflow ativo.');
            return;
        }

        // Verificar se a transição é válida para o estado atual
        if ($statusAtual->workflow_etapa_id !== $transicao->etapa_origem_id) {
            $fail('Transição não é válida para o estado atual do documento.');
            return;
        }

        // Verificar se os workflows coincidem
        if ($statusAtual->workflow_id !== $transicao->workflow_id) {
            $fail('Transição não pertence ao workflow ativo do documento.');
            return;
        }
    }

    public function messages(): array
    {
        return [
            'documento_type.required' => 'Tipo do documento é obrigatório.',
            'documento_id.required' => 'ID do documento é obrigatório.',
            'documento_id.integer' => 'ID do documento deve ser um número inteiro.',
            'transicao_id.required' => 'ID da transição é obrigatório.',
            'transicao_id.integer' => 'ID da transição deve ser um número inteiro.',
            'transicao_id.exists' => 'Transição não encontrada.',
            'dados_adicionais.array' => 'Dados adicionais devem ser um array.',
            'observacoes.string' => 'Observações devem ser texto.',
            'observacoes.max' => 'Observações não podem exceder 1000 caracteres.'
        ];
    }
}