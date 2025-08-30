<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProposicaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $proposicao = $this->route('proposicao');

        if (! $proposicao) {
            return false;
        }

        return $this->user()?->can('update', $proposicao);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ementa' => 'sometimes|required|string|max:1000',
            'conteudo' => 'sometimes|nullable|string|max:100000',
            'observacoes_edicao' => 'nullable|string|max:2000',
            'status' => 'sometimes|in:RASCUNHO,EM_REVISAO,REVISADO,AGUARDANDO_ASSINATURA',
            'template_id' => 'sometimes|nullable|exists:tipo_proposicao_templates,id',
            'anexos' => 'sometimes|nullable|array|max:5',
            'anexos.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
            'variaveis_template' => 'sometimes|nullable|array',
            // Campos permitidos apenas para Legislativo
            'revisor_id' => 'sometimes|nullable|exists:users,id',
            'revisado_em' => 'sometimes|nullable|date',
            // Campos permitidos apenas para Protocolo
            'numero_protocolo' => 'sometimes|nullable|string|max:50',
            'data_protocolo' => 'sometimes|nullable|date',
            'funcionario_protocolo_id' => 'sometimes|nullable|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ementa.required' => 'A ementa é obrigatória.',
            'ementa.max' => 'A ementa não pode exceder 1000 caracteres.',
            'conteudo.max' => 'O conteúdo não pode exceder 100.000 caracteres.',
            'observacoes_edicao.max' => 'Observações não podem exceder 2000 caracteres.',
            'status.in' => 'Status inválido para esta operação.',
            'template_id.exists' => 'Template selecionado não é válido.',
            'anexos.max' => 'Máximo de 5 anexos permitidos.',
            'anexos.*.mimes' => 'Anexo deve ser: PDF, DOC, DOCX, JPG, JPEG ou PNG.',
            'anexos.*.max' => 'Cada anexo deve ter no máximo 10MB.',
            'variaveis_template.array' => 'Variáveis do template devem ser um array válido.',
            'revisor_id.exists' => 'Revisor selecionado não é válido.',
            'funcionario_protocolo_id.exists' => 'Funcionário do protocolo selecionado não é válido.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'ultima_modificacao' => now(),
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'ementa' => 'ementa',
            'conteudo' => 'conteúdo',
            'observacoes_edicao' => 'observações de edição',
            'status' => 'status',
            'template_id' => 'template',
            'anexos' => 'anexos',
            'variaveis_template' => 'variáveis do template',
            'revisor_id' => 'revisor',
            'funcionario_protocolo_id' => 'funcionário do protocolo',
        ];
    }
}
