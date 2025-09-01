<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTemplateUniversalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $template = $this->route('template');

        return $this->user()->hasRole('ADMIN') ||
               $this->user()->can('update', $template);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|nullable|string|max:1000',
            'conteudo' => 'sometimes|required|string|max:2000000', // 2MB RTF max
            'document_key' => 'sometimes|nullable|string|max:100',
            'status' => 'sometimes|in:ativo,inativo,em_edicao',

            // Validação específica de RTF
            'conteudo_rtf_valido' => [
                function ($attribute, $value, $fail) {
                    if ($this->has('conteudo') && ! empty($this->input('conteudo'))) {
                        $conteudo = $this->input('conteudo');

                        // Verificar se é RTF válido
                        if (! str_starts_with($conteudo, '{\\rtf1')) {
                            $fail('O conteúdo deve ser um arquivo RTF válido.');
                        }

                        // Verificar se termina corretamente
                        if (! str_ends_with(trim($conteudo), '}')) {
                            $fail('O conteúdo RTF deve terminar com chave de fechamento.');
                        }
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do template é obrigatório.',
            'nome.max' => 'O nome não pode exceder 255 caracteres.',
            'descricao.max' => 'A descrição não pode exceder 1000 caracteres.',
            'conteudo.required' => 'O conteúdo do template é obrigatório.',
            'conteudo.max' => 'O conteúdo não pode exceder 2MB.',
            'document_key.max' => 'A chave do documento não pode exceder 100 caracteres.',
            'status.in' => 'Status deve ser: ativo, inativo ou em_edicao.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'updated_by' => $this->user()->id,
        ]);
    }

    /**
     * Get the validated data with additional processing.
     */
    public function validatedWithProcessing(): array
    {
        $validated = $this->validated();

        // Processar conteúdo RTF se necessário
        if (isset($validated['conteudo'])) {
            // Limpar conteúdo RTF de possíveis caracteres problemáticos
            $validated['conteudo'] = $this->sanitizeRtfContent($validated['conteudo']);
        }

        return $validated;
    }

    /**
     * Sanitizar conteúdo RTF.
     */
    private function sanitizeRtfContent(string $content): string
    {
        // Remove caracteres de controle problemáticos mas preserva RTF
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);

        // Normalizar quebras de linha RTF
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        return $content;
    }
}
