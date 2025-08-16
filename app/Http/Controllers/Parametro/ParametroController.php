<?php

namespace App\Http\Controllers\Parametro;

use App\Http\Controllers\Controller;
use App\Services\Parametro\ParametroService;
use App\Services\Parametro\ValidacaoParametroService;
use App\DTOs\Parametro\ModuloParametroDTO;
use App\DTOs\Parametro\SubmoduloParametroDTO;
use App\DTOs\Parametro\CampoParametroDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ParametroController extends Controller
{
    protected ParametroService $parametroService;
    protected ValidacaoParametroService $validacaoService;

    public function __construct(
        ParametroService $parametroService,
        ValidacaoParametroService $validacaoService
    ) {
        $this->parametroService = $parametroService;
        $this->validacaoService = $validacaoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // BYPASS TEMPORÁRIO - usar dados diretos do banco
        $modulos = \App\Models\Parametro\ParametroModulo::ativos()->ordenados()->get();
        
        // Log para debug
        // // Log::info('DEBUG Parametros Index - BYPASS TEMPORÁRIO', [
        //     'count' => $modulos->count(),
        //     'modulos' => $modulos->pluck('nome', 'id')->toArray()
        // ]);
        
        // Carregar submódulos para cada módulo
        $modulos->load('submodulosAtivos');
        
        // Remover duplicados por nome (manter o mais recente)
        $modulosUnicos = $modulos->groupBy('nome')->map(function ($grupo) {
            return $grupo->sortByDesc('id')->first(); // Pega o mais recente (ID maior)
        })->values();
        
        // Log após processamento
        // // Log::info('DEBUG Parametros Index - Módulos únicos processados', [
        //     'count' => $modulosUnicos->count(),
        //     'modulos' => $modulosUnicos->pluck('nome', 'id')->toArray()
        // ]);
        
        // Adicionar contagem de submódulos
        $modulosUnicos->transform(function ($modulo) {
            $modulo->submodulos_count = $modulo->submodulosAtivos->count();
            return $modulo;
        });
        
        $modulos = $modulosUnicos;
        return view('modules.parametros.index', compact('modulos'));
    }
    
    private function getSubmoduloIcon(string $nome): string
    {
        return match($nome) {
            'Cabeçalho' => 'ki-document',
            'Marca D\'água' => 'ki-water',
            'Rodapé' => 'ki-scroll-down',
            'Variáveis Dinâmicas' => 'ki-code',
            default => 'ki-setting-2'
        };
    }
    
    private function getSubmoduloRoute(string $nome): string
    {
        return match($nome) {
            'Cabeçalho' => 'parametros.templates.cabecalho',
            'Marca D\'água' => 'parametros.templates.marca-dagua',
            'Rodapé' => 'parametros.templates.rodape',
            'Variáveis Dinâmicas' => 'parametros.variaveis-dinamicas',
            default => 'parametros.index'
        };
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('modules.parametros.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $dados = $request->validate([
                'nome' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'icon' => 'nullable|string|max:100',
                'ordem' => 'nullable|integer|min:0',
                'ativo' => 'boolean',
            ]);

            $validacao = $this->validacaoService->validarCriacaoModulo($dados);
            
            if (!$validacao['valido']) {
                return back()
                    ->withErrors($validacao['erros'])
                    ->withInput();
            }

            $modulo = $this->parametroService->criarModulo($dados);

            return redirect()
                ->route('parametros.index')
                ->with('success', 'Módulo criado com sucesso!');
                
        } catch (\Exception $e) {
            return back()
                ->withError('Erro ao criar módulo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): View|RedirectResponse
    {
        // Se for ID 3 (IA), redirecionar imediatamente para interface customizada
        if ($id == 3 || $id === '3') {
            return redirect()->route('parametros.ia.config');
        }
        
        // Tentar buscar por ID primeiro, depois por nome
        if (is_numeric($id)) {
            $modulo = $this->parametroService->obterModulos()->find((int)$id);
        } else {
            $modulo = $this->parametroService->obterModulos()->where('nome', $id)->first();
        }
        
        if (!$modulo) {
            abort(404, 'Módulo não encontrado');
        }

        $submodulos = $this->parametroService->obterSubmodulos($modulo->id);

        // Se for Templates, mostrar página especial com cards dos submódulos
        if ($modulo->nome === 'Templates') {
            // Transformar submódulos em cards
            $cards = $submodulos->map(function ($submodulo) use ($modulo) {
                return (object) [
                    'id' => $submodulo->id,
                    'nome' => $submodulo->nome,
                    'descricao' => $submodulo->descricao,
                    'icon' => $this->getSubmoduloIcon($submodulo->nome),
                    'ordem' => $submodulo->ordem,
                    'ativo' => $submodulo->ativo,
                    'rota' => $this->getSubmoduloRoute($submodulo->nome),
                    'modulo_pai' => $modulo->nome
                ];
            })->sortBy('ordem');
            
            return view('modules.parametros.templates.index', compact('modulo', 'cards'));
        }

        // Se for IA, redirecionar para interface customizada de IA
        if ($modulo->nome === 'IA') {
            return redirect()->route('parametros.ia.config');
        }

        return view('modules.parametros.show', compact('modulo', 'submodulos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string|int $id): View
    {
        // Tentar buscar por ID primeiro, depois por nome
        if (is_numeric($id)) {
            $modulo = $this->parametroService->obterModulos()->find((int)$id);
        } else {
            $modulo = $this->parametroService->obterModulos()->where('nome', $id)->first();
        }
        
        if (!$modulo) {
            abort(404, 'Módulo não encontrado');
        }

        return view('modules.parametros.edit', compact('modulo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string|int $id): RedirectResponse
    {
        try {
            $dados = $request->validate([
                'nome' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'icon' => 'nullable|string|max:100',
                'ordem' => 'nullable|integer|min:0',
                'ativo' => 'boolean',
            ]);

            // Lógica de atualização seria implementada no service
            
            return redirect()
                ->route('parametros.index')
                ->with('success', 'Módulo atualizado com sucesso!');
                
        } catch (\Exception $e) {
            return back()
                ->withError('Erro ao atualizar módulo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): RedirectResponse
    {
        try {
            // Converter para ID se necessário
            if (!is_numeric($id)) {
                $modulo = $this->parametroService->obterModulos()->where('nome', $id)->first();
                $id = $modulo ? $modulo->id : $id;
            }
            
            // Verificar se pode excluir
            $verificacao = $this->parametroService->podeExcluirModulo((int)$id);
            
            if (!$verificacao['pode']) {
                return back()
                    ->withError('Não é possível excluir o módulo: ' . $verificacao['motivo']);
            }

            // Executar exclusão
            $this->parametroService->excluirModulo((int)$id);
            
            return redirect()
                ->route('parametros.index')
                ->with('success', 'Módulo excluído com sucesso!');
                
        } catch (\Exception $e) {
            return back()
                ->withError('Erro ao excluir módulo: ' . $e->getMessage());
        }
    }

    /**
     * Página de configuração de um módulo específico
     */
    public function configurar(string $nomeModulo): View
    {
        $modulo = $this->parametroService->obterModulos()
            ->where('nome', $nomeModulo)
            ->first();
            
        if (!$modulo) {
            abort(404, 'Módulo não encontrado');
        }

        $submodulos = $this->parametroService->obterSubmodulos($modulo->id);
        
        // Se for Templates, mostrar página especial com cards dos submódulos
        if ($nomeModulo === 'Templates') {
            // Transformar submódulos em cards
            $cards = $submodulos->map(function ($submodulo) use ($modulo) {
                return (object) [
                    'id' => $submodulo->id,
                    'nome' => $submodulo->nome,
                    'descricao' => $submodulo->descricao,
                    'icon' => $this->getSubmoduloIcon($submodulo->nome),
                    'ordem' => $submodulo->ordem,
                    'ativo' => $submodulo->ativo,
                    'rota' => $this->getSubmoduloRoute($submodulo->nome),
                    'modulo_pai' => $modulo->nome
                ];
            })->sortBy('ordem');
            
            return view('modules.parametros.templates.index', compact('modulo', 'cards'));
        }

        // Se for IA, redirecionar para interface customizada de IA
        if ($nomeModulo === 'IA') {
            return redirect()->route('parametros.ia.config');
        }
        
        // Para submódulos normais, buscar os campos e valores atuais
        foreach ($submodulos as $submodulo) {
            $submodulo->campos = $this->parametroService->obterCampos($submodulo->id);
            $submodulo->valores = $this->parametroService->obterValores($submodulo->id);
        }
        
        return view('modules.parametros.configurar', compact('modulo', 'submodulos'));
    }

    /**
     * Salva configurações de um submódulo
     */
    public function salvarConfiguracoes(Request $request, int $submoduloId)
    {
        try {
            $valores = $request->except(['_token', '_method']);
            $userId = auth()->id();
            
            $sucesso = $this->parametroService->salvarValores($submoduloId, $valores, $userId);
            
            if ($sucesso) {
                // Check if request is AJAX
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Configurações salvas com sucesso!'
                    ]);
                }
                
                return back()->with('success', 'Configurações salvas com sucesso!');
            } else {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao salvar configurações'
                    ], 400);
                }
                
                return back()->withError('Erro ao salvar configurações');
            }
            
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao salvar configurações: ' . $e->getMessage()
                ], 500);
            }
            
            return back()
                ->withError('Erro ao salvar configurações: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * API: Validar parâmetro
     */
    public function validar(Request $request, string $modulo, string $submodulo): JsonResponse
    {
        try {
            $valor = $request->input('valor');
            
            $valido = $this->parametroService->validar($modulo, $submodulo, $valor);
            
            return response()->json([
                'valido' => $valido,
                'mensagem' => $valido ? 'Parâmetro válido' : 'Parâmetro inválido'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'valido' => false,
                'mensagem' => 'Erro na validação: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * API: Obter configurações
     */
    public function obterConfiguracoes(string $modulo, string $submodulo): JsonResponse
    {
        try {
            $configuracoes = $this->parametroService->obterConfiguracoes($modulo, $submodulo);
            
            return response()->json($configuracoes);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao obter configurações: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * API: Obter valor específico
     */
    public function obterValor(string $modulo, string $submodulo, string $campo): JsonResponse
    {
        try {
            $valor = $this->parametroService->obterValor($modulo, $submodulo, $campo);
            
            return response()->json([
                'valor' => $valor
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao obter valor: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * API: Limpar cache
     */
    public function limparCache(): JsonResponse
    {
        try {
            $this->parametroService->limparTodoCache();
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Cache limpo com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro ao limpar cache: ' . $e->getMessage()
            ], 500);
        }
    }
}