<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Services\Template\TemplateProcessorService;
use App\Models\TipoProposicaoTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TemplateValidationController extends Controller
{
    private TemplateProcessorService $templateProcessor;

    public function __construct(TemplateProcessorService $templateProcessor)
    {
        $this->templateProcessor = $templateProcessor;
    }

    /**
     * Validar conteúdo de template
     */
    public function validarConteudo(Request $request): JsonResponse
    {
        $request->validate([
            'conteudo' => 'required|string'
        ]);

        $resultado = $this->templateProcessor->validarTemplate($request->conteudo);

        return response()->json([
            'success' => true,
            'dados' => $resultado
        ]);
    }

    /**
     * Gerar preview do template
     */
    public function gerarPreview($templateId): JsonResponse
    {
        try {
            $template = TipoProposicaoTemplate::findOrFail($templateId);
            $preview = $this->templateProcessor->gerarPreview($template);

            return response()->json([
                'success' => true,
                'preview' => $preview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar variáveis disponíveis
     */
    public function variaveisDisponiveis(): JsonResponse
    {
        $variaveis = $this->templateProcessor->getVariaveisDisponiveis();

        return response()->json([
            'success' => true,
            'variaveis' => $variaveis
        ]);
    }

    /**
     * Validar template específico
     */
    public function validarTemplate($templateId): JsonResponse
    {
        try {
            $template = TipoProposicaoTemplate::findOrFail($templateId);
            
            // Obter conteúdo do template
            $conteudo = '';
            if ($template->arquivo_path && \Storage::disk('public')->exists($template->arquivo_path)) {
                $conteudo = \Storage::disk('public')->get($template->arquivo_path);
            }

            if (empty($conteudo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template não possui conteúdo válido'
                ], 400);
            }

            $resultado = $this->templateProcessor->validarTemplate($conteudo);

            return response()->json([
                'success' => true,
                'template' => [
                    'id' => $template->id,
                    'tipo_proposicao' => $template->tipoProposicao->nome ?? 'N/A',
                    'ativo' => $template->ativo
                ],
                'validacao' => $resultado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Testar processamento de template
     */
    public function testarProcessamento(Request $request): JsonResponse
    {
        $request->validate([
            'template_id' => 'required|exists:tipo_proposicao_templates,id',
            'variaveis' => 'array'
        ]);

        try {
            $template = TipoProposicaoTemplate::findOrFail($request->template_id);
            $variaveis = $request->variaveis ?? [];

            // Criar proposição mock para teste
            $proposicaoMock = new \App\Models\Proposicao([
                'id' => 999,
                'tipo' => 'teste',
                'ementa' => 'Ementa de teste',
                'conteudo' => 'Conteúdo de teste',
                'ano' => date('Y'),
                'created_at' => now()
            ]);

            $conteudoProcessado = $this->templateProcessor->processarTemplate(
                $template,
                $proposicaoMock,
                $variaveis
            );

            return response()->json([
                'success' => true,
                'conteudo_processado' => $conteudoProcessado,
                'variaveis_utilizadas' => $variaveis
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extrair variáveis de um conteúdo
     */
    public function extrairVariaveis(Request $request): JsonResponse
    {
        $request->validate([
            'conteudo' => 'required|string'
        ]);

        $variaveis = $this->templateProcessor->extrairVariaveis($request->conteudo);

        return response()->json([
            'success' => true,
            'variaveis_encontradas' => $variaveis,
            'total' => count($variaveis)
        ]);
    }
}