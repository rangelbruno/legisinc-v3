<?php

namespace App\Http\Controllers;

use App\Services\Parametro\ParametroService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TemplateFooterController extends Controller
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    /**
     * Mostrar tela de configuração do rodapé
     */
    public function index(): View
    {
        // Log::info('🦶 TemplateFooterController::index chamado', [
            //     'user' => auth()->user()->email ?? 'não autenticado',
            //     'timestamp' => now()
        // ]);
        
        // Obter configurações atuais
        $configuracoes = $this->obterConfiguracoes();
        
        return view('modules.parametros.templates.rodape', compact('configuracoes'));
    }

    /**
     * Salvar configurações do rodapé
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'usar_rodape' => 'boolean',
            'rodape_tipo' => 'required|string|in:texto,imagem,misto',
            'rodape_texto' => 'nullable|string|max:500',
            'rodape_posicao' => 'required|string|in:rodape,final,todas_paginas',
            'rodape_alinhamento' => 'required|string|in:esquerda,centro,direita',
            'rodape_numeracao' => 'boolean'
        ]);

        try {
            // Salvar cada configuração
            $this->parametroService->salvarValor('Templates', 'Rodapé', 'usar_rodape', $request->boolean('usar_rodape'));
            $this->parametroService->salvarValor('Templates', 'Rodapé', 'rodape_tipo', $request->input('rodape_tipo'));
            $this->parametroService->salvarValor('Templates', 'Rodapé', 'rodape_texto', $request->input('rodape_texto'));
            $this->parametroService->salvarValor('Templates', 'Rodapé', 'rodape_posicao', $request->input('rodape_posicao'));
            $this->parametroService->salvarValor('Templates', 'Rodapé', 'rodape_alinhamento', $request->input('rodape_alinhamento'));
            $this->parametroService->salvarValor('Templates', 'Rodapé', 'rodape_numeracao', $request->boolean('rodape_numeracao'));

            return response()->json([
                'success' => true,
                'message' => 'Configurações do rodapé salvas com sucesso!'
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao salvar configurações do rodapé', [
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
                'usar_rodape' => $this->parametroService->obterValor('Templates', 'Rodapé', 'usar_rodape') ?: true,
                'tipo' => $this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_tipo') ?: 'texto',
                'texto' => $this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_texto') ?: 'Este documento foi gerado automaticamente pelo Sistema Legislativo.',
                'imagem' => $this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_imagem') ?: 'template/rodape.png',
                'posicao' => $this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_posicao') ?: 'rodape',
                'alinhamento' => $this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_alinhamento') ?: 'centro',
                'numeracao' => $this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_numeracao') ?: true
            ];
        } catch (\Exception $e) {
            // Log::warning('Erro ao obter configurações do rodapé, usando padrões', [
                //     'error' => $e->getMessage()
            // ]);
            // Se houver erro, usar valores padrão
            return [
                'usar_rodape' => true,
                'tipo' => 'texto',
                'texto' => 'Este documento foi gerado automaticamente pelo Sistema Legislativo.',
                'imagem' => 'template/rodape.png',
                'posicao' => 'rodape',
                'alinhamento' => 'centro',
                'numeracao' => true
            ];
        }
    }
}