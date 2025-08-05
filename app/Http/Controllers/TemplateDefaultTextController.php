<?php

namespace App\Http\Controllers;

use App\Services\Parametro\ParametroService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TemplateDefaultTextController extends Controller
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    /**
     * Mostrar tela de configuração do texto padrão
     */
    public function index(): View
    {
        \Log::info('📝 TemplateDefaultTextController::index chamado', [
            'user' => auth()->user()->email ?? 'não autenticado',
            'timestamp' => now()
        ]);
        
        // Obter configurações atuais
        $configuracoes = $this->obterConfiguracoes();
        
        return view('modules.parametros.templates.texto-padrao', compact('configuracoes'));
    }

    /**
     * Salvar configurações do texto padrão
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'usar_texto_padrao' => 'boolean',
            'texto_introducao' => 'nullable|string|max:1000',
            'texto_justificativa' => 'nullable|string|max:2000',
            'texto_conclusao' => 'nullable|string|max:1000',
            'assinatura_cargo' => 'nullable|string|max:255',
            'assinatura_nome' => 'nullable|string|max:255',
            'assinatura_departamento' => 'nullable|string|max:255',
        ]);

        try {
            // Salvar cada configuração
            $this->parametroService->salvarValor('Templates', 'Texto Padrão', 'usar_texto_padrao', $request->boolean('usar_texto_padrao'));
            $this->parametroService->salvarValor('Templates', 'Texto Padrão', 'texto_introducao', $request->input('texto_introducao', ''));
            $this->parametroService->salvarValor('Templates', 'Texto Padrão', 'texto_justificativa', $request->input('texto_justificativa', ''));
            $this->parametroService->salvarValor('Templates', 'Texto Padrão', 'texto_conclusao', $request->input('texto_conclusao', ''));
            $this->parametroService->salvarValor('Templates', 'Texto Padrão', 'assinatura_cargo', $request->input('assinatura_cargo', ''));
            $this->parametroService->salvarValor('Templates', 'Texto Padrão', 'assinatura_nome', $request->input('assinatura_nome', ''));
            $this->parametroService->salvarValor('Templates', 'Texto Padrão', 'assinatura_departamento', $request->input('assinatura_departamento', ''));

            return response()->json([
                'success' => true,
                'message' => 'Configurações do texto padrão salvas com sucesso!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao salvar configurações do texto padrão', [
                'error' => $e->getMessage(),
                'user' => auth()->id()
            ]);

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
                'usar_texto_padrao' => $this->parametroService->obterValor('Templates', 'Texto Padrão', 'usar_texto_padrao') ?: false,
                'texto_introducao' => $this->parametroService->obterValor('Templates', 'Texto Padrão', 'texto_introducao') ?: 'Este documento apresenta proposta de lei que visa...',
                'texto_justificativa' => $this->parametroService->obterValor('Templates', 'Texto Padrão', 'texto_justificativa') ?: 'A presente proposição justifica-se pela necessidade de...',
                'texto_conclusao' => $this->parametroService->obterValor('Templates', 'Texto Padrão', 'texto_conclusao') ?: 'Diante do exposto, submetemos esta proposição à apreciação dos nobres pares desta Casa.',
                'assinatura_cargo' => $this->parametroService->obterValor('Templates', 'Texto Padrão', 'assinatura_cargo') ?: 'Vereador(a)',
                'assinatura_nome' => $this->parametroService->obterValor('Templates', 'Texto Padrão', 'assinatura_nome') ?: '',
                'assinatura_departamento' => $this->parametroService->obterValor('Templates', 'Texto Padrão', 'assinatura_departamento') ?: 'Câmara Municipal'
            ];
        } catch (\Exception $e) {
            \Log::warning('Erro ao obter configurações do texto padrão, usando padrões', [
                'error' => $e->getMessage()
            ]);
            // Se houver erro, usar valores padrão
            return [
                'usar_texto_padrao' => false,
                'texto_introducao' => 'Este documento apresenta proposta de lei que visa...',
                'texto_justificativa' => 'A presente proposição justifica-se pela necessidade de...',
                'texto_conclusao' => 'Diante do exposto, submetemos esta proposição à apreciação dos nobres pares desta Casa.',
                'assinatura_cargo' => 'Vereador(a)',
                'assinatura_nome' => '',
                'assinatura_departamento' => 'Câmara Municipal'
            ];
        }
    }
}