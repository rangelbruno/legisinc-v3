<?php

namespace App\Http\Controllers;

use App\Services\Parametro\ParametroService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TemplateWatermarkController extends Controller
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    /**
     * Mostrar tela de configuração da marca d'água
     */
    public function index(): View
    {
        // Log::info('🎨 TemplateWatermarkController::index chamado', [
            //     'user' => auth()->user()->email ?? 'não autenticado',
            //     'timestamp' => now()
        // ]);
        
        // Obter configurações atuais
        $configuracoes = $this->obterConfiguracoes();
        
        return view('modules.parametros.templates.marca-dagua', compact('configuracoes'));
    }

    /**
     * Salvar configurações da marca d'água
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'usar_marca_dagua' => 'boolean',
            'marca_dagua_tipo' => 'required|string|in:imagem,texto',
            'marca_dagua_texto' => 'nullable|string|max:255',
            'marca_dagua_opacidade' => 'required|integer|min:10|max:100',
            'marca_dagua_posicao' => 'required|string|in:centro,superior_direita,superior_esquerda,inferior_direita,inferior_esquerda',
            'marca_dagua_tamanho' => 'required|integer|min:50|max:300'
        ]);

        try {
            // Salvar cada configuração
            $this->parametroService->salvarValor('Templates', 'Marca D\'água', 'usar_marca_dagua', $request->boolean('usar_marca_dagua'));
            $this->parametroService->salvarValor('Templates', 'Marca D\'água', 'marca_dagua_tipo', $request->input('marca_dagua_tipo'));
            $this->parametroService->salvarValor('Templates', 'Marca D\'água', 'marca_dagua_texto', $request->input('marca_dagua_texto', ''));
            $this->parametroService->salvarValor('Templates', 'Marca D\'água', 'marca_dagua_opacidade', $request->input('marca_dagua_opacidade'));
            $this->parametroService->salvarValor('Templates', 'Marca D\'água', 'marca_dagua_posicao', $request->input('marca_dagua_posicao'));
            $this->parametroService->salvarValor('Templates', 'Marca D\'água', 'marca_dagua_tamanho', $request->input('marca_dagua_tamanho'));

            return response()->json([
                'success' => true,
                'message' => 'Configurações da marca d\'água salvas com sucesso!'
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao salvar configurações da marca d\'água', [
                //     'error' => $e->getMessage(),
                //     'user' => auth()->id()
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter configurações atuais
     */
    private function obterConfiguracoes(): array
    {
        try {
            return [
                'imagem' => $this->parametroService->obterValor('Templates', 'Marca D\'água', 'marca_dagua_imagem') ?: 'template/marca-dagua.png',
                'usar_marca_dagua' => $this->parametroService->obterValor('Templates', 'Marca D\'água', 'usar_marca_dagua') ?: false,
                'tipo' => $this->parametroService->obterValor('Templates', 'Marca D\'água', 'marca_dagua_tipo') ?: 'imagem',
                'texto' => $this->parametroService->obterValor('Templates', 'Marca D\'água', 'marca_dagua_texto') ?: 'CONFIDENCIAL',
                'opacidade' => $this->parametroService->obterValor('Templates', 'Marca D\'água', 'marca_dagua_opacidade') ?: 30,
                'posicao' => $this->parametroService->obterValor('Templates', 'Marca D\'água', 'marca_dagua_posicao') ?: 'centro',
                'tamanho' => $this->parametroService->obterValor('Templates', 'Marca D\'água', 'marca_dagua_tamanho') ?: 100
            ];
        } catch (\Exception $e) {
            // Log::warning('Erro ao obter configurações da marca d\'água, usando padrões', [
                //     'error' => $e->getMessage()
            // ]);
            // Se houver erro, usar valores padrão
            return [
                'imagem' => 'template/marca-dagua.png',
                'usar_marca_dagua' => false,
                'tipo' => 'imagem',
                'texto' => 'CONFIDENCIAL',
                'opacidade' => 30,
                'posicao' => 'centro',
                'tamanho' => 100
            ];
        }
    }
}