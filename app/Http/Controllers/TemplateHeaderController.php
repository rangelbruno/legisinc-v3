<?php

namespace App\Http\Controllers;

use App\Services\Parametro\ParametroService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TemplateHeaderController extends Controller
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    /**
     * Mostrar tela de configuração do cabeçalho
     */
    public function index(): View
    {
        // Log::info('📋 TemplateHeaderController::index chamado', [
            //     'user' => auth()->user()->email ?? 'não autenticado',
            //     'timestamp' => now()
        // ]);
        
        // Obter configurações atuais
        $configuracoes = $this->obterConfiguracoes();
        
        return view('modules.parametros.templates.cabecalho', compact('configuracoes'));
    }

    /**
     * Salvar configurações do cabeçalho
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'usar_cabecalho_padrao' => 'boolean',
            'cabecalho_altura' => 'required|integer|min:50|max:300',
            'cabecalho_posicao' => 'required|string|in:topo,header,marca_dagua'
        ]);

        try {
            // Salvar cada configuração
            $this->parametroService->salvarValor('Templates', 'Cabeçalho', 'usar_cabecalho_padrao', $request->boolean('usar_cabecalho_padrao'));
            $this->parametroService->salvarValor('Templates', 'Cabeçalho', 'cabecalho_altura', $request->input('cabecalho_altura'));
            $this->parametroService->salvarValor('Templates', 'Cabeçalho', 'cabecalho_posicao', $request->input('cabecalho_posicao'));

            return response()->json([
                'success' => true,
                'message' => 'Configurações salvas com sucesso!'
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao salvar configurações do cabeçalho', [
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
                'imagem' => $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_imagem') ?: 'template/cabecalho.png',
                'usar_padrao' => $this->parametroService->obterValor('Templates', 'Cabeçalho', 'usar_cabecalho_padrao') ?: true,
                'altura' => $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_altura') ?: 150,
                'posicao' => $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_posicao') ?: 'topo'
            ];
        } catch (\Exception $e) {
            // Log::warning('Erro ao obter configurações do cabeçalho, usando padrões', [
                //     'error' => $e->getMessage()
            // ]);
            // Se houver erro, usar valores padrão
            return [
                'imagem' => 'template/cabecalho.png',
                'usar_padrao' => true,
                'altura' => 150,
                'posicao' => 'topo'
            ];
        }
    }
}