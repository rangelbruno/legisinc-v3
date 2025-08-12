<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Services\Template\ABNTValidationService;
use App\Services\Template\TemplatePadraoABNTService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ABNTValidationController extends Controller
{
    protected ABNTValidationService $abntValidationService;
    protected TemplatePadraoABNTService $templatePadraoService;

    public function __construct(
        ABNTValidationService $abntValidationService,
        TemplatePadraoABNTService $templatePadraoService
    ) {
        $this->abntValidationService = $abntValidationService;
        $this->templatePadraoService = $templatePadraoService;
    }

    /**
     * Validar documento HTML conforme normas ABNT
     */
    public function validarDocumento(Request $request): JsonResponse
    {
        $request->validate([
            'conteudo_html' => 'required|string'
        ]);

        try {
            $validacao = $this->abntValidationService->validarDocumento($request->conteudo_html);
            
            // Gerar relatório em formato texto
            $relatorio = $this->abntValidationService->gerarRelatorio($validacao);
            
            return response()->json([
                'success' => true,
                'validacao' => $validacao,
                'relatorio' => $relatorio,
                'score' => $validacao['score_geral']['percentual'] ?? 0,
                'status' => $validacao['score_geral']['status'] ?? 'desconhecido'
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro na validação ABNT via API', [
                //     'error' => $e->getMessage(),
                //     'user_id' => auth()->id()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno na validação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aplicar correções automáticas em documento HTML
     */
    public function aplicarCorrecoes(Request $request): JsonResponse
    {
        $request->validate([
            'conteudo_html' => 'required|string'
        ]);

        try {
            $resultado = $this->abntValidationService->aplicarCorrecoesAutomaticas($request->conteudo_html);
            
            // Validar documento corrigido
            $validacao = $this->abntValidationService->validarDocumento($resultado['conteudo']);
            
            return response()->json([
                'success' => true,
                'conteudo_corrigido' => $resultado['conteudo'],
                'correcoes_aplicadas' => $resultado['correcoes'],
                'validacao_pos_correcao' => $validacao,
                'score_pos_correcao' => $validacao['score_geral']['percentual'] ?? 0
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao aplicar correções ABNT', [
                //     'error' => $e->getMessage(),
                //     'user_id' => auth()->id()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao aplicar correções: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter estatísticas do template padrão ABNT
     */
    public function obterEstatisticasTemplate(): JsonResponse
    {
        try {
            $estatisticas = $this->templatePadraoService->obterEstatisticas();
            
            return response()->json([
                'success' => true,
                'estatisticas' => $estatisticas
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao obter estatísticas do template ABNT', [
                //     'error' => $e->getMessage(),
                //     'user_id' => auth()->id()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gerar relatório detalhado de conformidade ABNT
     */
    public function gerarRelatorioDetalhado(Request $request): JsonResponse
    {
        $request->validate([
            'conteudo_html' => 'required|string',
            'formato' => 'nullable|string|in:json,markdown,html'
        ]);

        try {
            $formato = $request->get('formato', 'json');
            $validacao = $this->abntValidationService->validarDocumento($request->conteudo_html);
            
            $relatorio = match($formato) {
                'markdown' => $this->abntValidationService->gerarRelatorio($validacao),
                'html' => $this->gerarRelatorioHTML($validacao),
                default => $validacao
            };
            
            return response()->json([
                'success' => true,
                'formato' => $formato,
                'relatorio' => $relatorio,
                'score_geral' => $validacao['score_geral'] ?? []
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao gerar relatório ABNT', [
                //     'error' => $e->getMessage(),
                //     'user_id' => auth()->id()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar relatório: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibir página de validação ABNT
     */
    public function exibirPagina()
    {
        return view('admin.templates.validacao-abnt', [
            'title' => 'Validação ABNT',
            'estatisticas_template' => $this->templatePadraoService->obterEstatisticas()
        ]);
    }

    /**
     * Gerar relatório em formato HTML
     */
    protected function gerarRelatorioHTML(array $validacao): string
    {
        $html = '<div class="relatorio-abnt">';
        
        // Score geral
        if (isset($validacao['score_geral'])) {
            $score = $validacao['score_geral'];
            $statusClass = match($score['status']) {
                'excelente' => 'success',
                'bom' => 'info',
                'regular' => 'warning',
                default => 'danger'
            };
            
            $html .= "<div class=\"alert alert-{$statusClass}\">";
            $html .= "<h4>Score Geral: {$score['percentual']}% - " . ucfirst($score['status']) . "</h4>";
            $html .= "<p>{$score['mensagem']}</p>";
            $html .= "</div>";
        }

        // Detalhes por categoria
        foreach ($validacao as $categoria => $resultado) {
            if ($categoria === 'score_geral' || $categoria === 'erro') continue;
            
            if (is_array($resultado)) {
                $statusClass = $resultado['status'] === 'ok' ? 'success' : 'warning';
                
                $html .= "<div class=\"card mb-3\">";
                $html .= "<div class=\"card-header bg-{$statusClass}\">";
                $html .= "<h5>" . ucfirst(str_replace('_', ' ', $categoria)) . " - " . ucfirst($resultado['status']) . "</h5>";
                $html .= "</div>";
                $html .= "<div class=\"card-body\">";
                
                if (!empty($resultado['problemas'])) {
                    $html .= "<h6>Problemas encontrados:</h6><ul>";
                    foreach ($resultado['problemas'] as $problema) {
                        $html .= "<li>{$problema}</li>";
                    }
                    $html .= "</ul>";
                }
                
                if (!empty($resultado['sugestoes'])) {
                    $html .= "<h6>Sugestões:</h6><ul>";
                    foreach ($resultado['sugestoes'] as $sugestao) {
                        $html .= "<li>{$sugestao}</li>";
                    }
                    $html .= "</ul>";
                }
                
                $html .= "</div></div>";
            }
        }

        $html .= '</div>';
        return $html;
    }
}