<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProposicaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isParlamentar() || $this->user()?->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tipo' => 'required|string|max:100',
            'ementa' => 'required|string|max:1000',
            'conteudo' => 'nullable|string|max:100000',
            'template_id' => 'nullable|exists:tipo_proposicao_templates,id',
            'anexos' => 'nullable|array|max:5',
            'anexos.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
            'observacoes_edicao' => 'nullable|string|max:2000',
            'variaveis_template' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tipo.required' => 'O tipo da proposição é obrigatório.',
            'tipo.max' => 'O tipo não pode exceder 100 caracteres.',
            'ementa.required' => 'A ementa é obrigatória.',
            'ementa.max' => 'A ementa não pode exceder 1000 caracteres.',
            'conteudo.max' => 'O conteúdo não pode exceder 100.000 caracteres.',
            'template_id.exists' => 'Template selecionado não é válido.',
            'anexos.max' => 'Máximo de 5 anexos permitidos.',
            'anexos.*.mimes' => 'Anexo deve ser: PDF, DOC, DOCX, JPG, JPEG ou PNG.',
            'anexos.*.max' => 'Cada anexo deve ter no máximo 10MB.',
            'observacoes_edicao.max' => 'Observações não podem exceder 2000 caracteres.',
            'variaveis_template.array' => 'Variáveis do template devem ser um array válido.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'autor_id' => $this->user()?->id,
            'ano' => now()->year,
            'status' => 'RASCUNHO',
            'ultima_modificacao' => now(),
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'tipo' => 'tipo de proposição',
            'ementa' => 'ementa',
            'conteudo' => 'conteúdo',
            'template_id' => 'template',
            'anexos' => 'anexos',
            'observacoes_edicao' => 'observações de edição',
            'variaveis_template' => 'variáveis do template',
        ];
    }
}
