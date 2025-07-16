<?php

namespace App\Http\Controllers\Projeto;

use App\Http\Controllers\Controller;
use App\Services\Projeto\ProjetoService;
use App\Services\Projeto\ProjetoWorkflowService;
use App\Services\Projeto\TramitacaoService;
use App\DTOs\Projeto\ProjetoDTO;
use App\DTOs\Projeto\ProjetoVersionDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProjetoController extends Controller
{
    protected ProjetoService $projetoService;
    protected ProjetoWorkflowService $workflowService;
    protected TramitacaoService $tramitacaoService;

    public function __construct(
        ProjetoService $projetoService,
        ProjetoWorkflowService $workflowService,
        TramitacaoService $tramitacaoService
    ) {
        $this->projetoService = $projetoService;
        $this->workflowService = $workflowService;
        $this->tramitacaoService = $tramitacaoService;
    }

    /**
     * Listar projetos
     */
    public function index(Request $request): View
    {
        try {
            $filtros = $request->only([
                'titulo', 'numero', 'ano', 'tipo', 'status', 'urgencia',
                'autor_id', 'relator_id', 'comissao_id', 'palavras_chave', 'urgentes'
            ]);

            $projetos = $this->projetoService->listar($filtros, $request->get('per_page', 15));
            $opcoes = $this->projetoService->obterOpcoes();
            $estatisticas = $this->projetoService->obterEstatisticas();

            return view('modules.projetos.index', compact('projetos', 'opcoes', 'estatisticas', 'filtros'));

        } catch (Exception $e) {
            return view('modules.projetos.index', [
                'projetos' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'opcoes' => [],
                'estatisticas' => [],
                'filtros' => [],
                'error' => 'Erro ao carregar projetos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Exibir formulário de criação
     */
    public function create(): View
    {
        try {
            $opcoes = $this->projetoService->obterOpcoes();
            
            // Debug: Log the options to see what's being passed
            \Log::info('Opções para criação de projeto', $opcoes);
            
            return view('modules.projetos.create', compact('opcoes'));
        } catch (Exception $e) {
            \Log::error('Erro ao carregar formulário de criação', ['erro' => $e->getMessage()]);
            
            // Fallback options in case of database issues
            $opcoes = [
                'tipos' => \App\Models\Projeto::TIPOS,
                'status' => \App\Models\Projeto::STATUS,
                'urgencias' => \App\Models\Projeto::URGENCIA,
                'autores' => collect(),
                'comissoes' => [],
            ];
            
            return view('modules.projetos.create', compact('opcoes'))
                ->with('error', 'Erro ao carregar dados do formulário. Alguns campos podem estar limitados.');
        }
    }

    /**
     * Armazenar novo projeto
     */
    public function store(Request $request): RedirectResponse
    {
        // Debug: Log incoming request data
        \Log::info('Request data para criação de projeto', $request->all());
        
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'ementa' => 'required|string',
            'tipo' => 'required|string|in:' . implode(',', array_keys(\App\Models\Projeto::TIPOS)),
            'numero' => 'nullable|string|max:20',
            'ano' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'relator_id' => 'nullable|exists:users,id',
            'comissao_id' => 'nullable|exists:comissoes,id',
            'urgencia' => 'required|string|in:' . implode(',', array_keys(\App\Models\Projeto::URGENCIA)),
            'resumo' => 'nullable|string',
            'palavras_chave' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'data_limite_tramitacao' => 'nullable|date|after:today',
            'conteudo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            \Log::error('Validação falhou na criação do projeto', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $dadosRequest = array_merge($request->all(), [
                'autor_id' => auth()->id(),
                'status' => 'rascunho',
                'ativo' => true,
            ]);
            
            \Log::info('Dados do request para criar projeto', ['dados' => $dadosRequest]);
            
            $dto = ProjetoDTO::fromArray($dadosRequest);

            $projeto = $this->projetoService->criar($dto);

            return redirect()->route('projetos.show', $projeto->id)
                ->with('success', 'Projeto criado com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar projeto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Exibir projeto
     */
    public function show(int $id): View
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);

            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            // $this->authorize('view', $projeto); // Comentado temporariamente

            return view('modules.projetos.show', compact('projeto'));

        } catch (Exception $e) {
            abort(500, 'Erro ao carregar projeto: ' . $e->getMessage());
        }
    }

    /**
     * Exibir formulário de edição
     */
    public function edit(int $id): View
    {
        try {
            \Log::info('Iniciando edição do projeto', ['id' => $id]);
            
            $projeto = $this->projetoService->obterPorId($id);
            \Log::info('Projeto obtido', ['projeto' => $projeto ? $projeto->toArray() : null]);

            if (!$projeto) {
                \Log::error('Projeto não encontrado', ['id' => $id]);
                abort(404, 'Projeto não encontrado');
            }

            // $this->authorize('update', $projeto); // Comentado temporariamente

            $opcoes = $this->projetoService->obterOpcoes();
            \Log::info('Opções obtidas', ['opcoes' => $opcoes]);

            return view('modules.projetos.edit', compact('projeto', 'opcoes'));

        } catch (Exception $e) {
            \Log::error('Erro ao carregar projeto para edição', ['erro' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            abort(500, 'Erro ao carregar projeto para edição: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar projeto
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $projeto = $this->projetoService->obterPorId($id);
        
        if (!$projeto) {
            abort(404, 'Projeto não encontrado');
        }

        // $this->authorize('update', $projeto); // Comentado temporariamente

        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'ementa' => 'required|string',
            'tipo' => 'required|string|in:' . implode(',', array_keys(\App\Models\Projeto::TIPOS)),
            'numero' => 'nullable|string|max:20',
            'ano' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'relator_id' => 'nullable|exists:users,id',
            'comissao_id' => 'nullable|exists:comissoes,id',
            'urgencia' => 'required|string|in:' . implode(',', array_keys(\App\Models\Projeto::URGENCIA)),
            'resumo' => 'nullable|string',
            'palavras_chave' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'data_limite_tramitacao' => 'nullable|date|after:today',
            'conteudo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $dto = ProjetoDTO::fromArray($request->all());
            $projeto = $this->projetoService->atualizar($id, $dto);

            return redirect()->route('projetos.show', $projeto->id)
                ->with('success', 'Projeto atualizado com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar projeto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Excluir projeto
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);
            
            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            // $this->authorize('delete', $projeto); // Comentado temporariamente

            $this->projetoService->excluir($id);

            return redirect()->route('projetos.index')
                ->with('success', 'Projeto excluído com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao excluir projeto: ' . $e->getMessage());
        }
    }

    /**
     * Protocolar projeto
     */
    public function protocolar(int $id): RedirectResponse
    {
        try {
            $projeto = $this->projetoService->protocolar($id);

            return redirect()->route('projetos.show', $projeto->id)
                ->with('success', 'Projeto protocolado com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao protocolar projeto: ' . $e->getMessage());
        }
    }

    /**
     * Encaminhar para comissão
     */
    public function encaminharComissao(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'comissao_id' => 'required|exists:comissoes,id',
            'relator_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $projeto = $this->projetoService->encaminharParaComissao(
                $id,
                $request->comissao_id,
                $request->relator_id
            );

            return redirect()->route('projetos.show', $projeto->id)
                ->with('success', 'Projeto encaminhado para comissão com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao encaminhar projeto: ' . $e->getMessage());
        }
    }

    /**
     * Buscar projetos (AJAX)
     */
    public function buscar(Request $request): JsonResponse
    {
        try {
            $termo = $request->get('termo', '');
            $limite = $request->get('limite', 10);

            $projetos = $this->projetoService->buscar($termo, $limite);

            return response()->json([
                'success' => true,
                'projetos' => $projetos->map(function ($projeto) {
                    return [
                        'id' => $projeto->id,
                        'titulo' => $projeto->titulo,
                        'numero_completo' => $projeto->numero_completo,
                        'tipo_formatado' => $projeto->tipo_formatado,
                        'status_formatado' => $projeto->status_formatado,
                        'autor' => $projeto->autor->name,
                        'url' => route('projetos.show', $projeto->id),
                    ];
                })
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar projetos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter estatísticas (AJAX)
     */
    public function estatisticas(): JsonResponse
    {
        try {
            $estatisticas = $this->projetoService->obterEstatisticas();

            return response()->json([
                'success' => true,
                'estatisticas' => $estatisticas
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Editor de conteúdo (nova aba)
     */
    public function editor(int $id): View
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);

            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            if (!$projeto->podeEditarConteudo()) {
                abort(403, 'Não é possível editar o conteúdo neste status');
            }

            return view('modules.projetos.editor', compact('projeto'));

        } catch (Exception $e) {
            abort(500, 'Erro ao carregar editor: ' . $e->getMessage());
        }
    }

    /**
     * Editor de conteúdo com Tiptap (nova versão)
     */
    public function editorTiptap(int $id): View
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);

            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            if (!$projeto->podeEditarConteudo()) {
                abort(403, 'Não é possível editar o conteúdo neste status');
            }

            return view('modules.projetos.editor-tiptap', compact('projeto'));

        } catch (Exception $e) {
            abort(500, 'Erro ao carregar editor: ' . $e->getMessage());
        }
    }

    /**
     * Salvar conteúdo do editor (AJAX)
     */
    public function salvarConteudo(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'conteudo' => 'required|string',
            'changelog' => 'nullable|string',
            'tipo_alteracao' => 'nullable|string|in:revisao,emenda,correcao,formatacao',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $projeto = $this->projetoService->obterPorId($id);

            if (!$projeto || !$projeto->podeEditarConteudo()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível editar o conteúdo'
                ], 403);
            }

            // Verificar se conteúdo mudou
            if ($request->conteudo === $projeto->conteudo) {
                return response()->json([
                    'success' => true,
                    'message' => 'Nenhuma alteração detectada',
                    'version' => $projeto->version_atual
                ]);
            }

            // Criar nova versão
            $versionDTO = ProjetoVersionDTO::fromArray([
                'projeto_id' => $id,
                'version_number' => $projeto->version_atual + 1,
                'conteudo' => $request->conteudo,
                'changelog' => $request->changelog ?: 'Atualização automática',
                'tipo_alteracao' => $request->tipo_alteracao ?: 'revisao',
                'author_id' => auth()->id(),
                'is_current' => true,
            ]);

            $version = $this->projetoService->criarVersao($id, $versionDTO);

            return response()->json([
                'success' => true,
                'message' => 'Conteúdo salvo com sucesso!',
                'version' => $version->version_number,
                'timestamp' => $version->created_at->format('d/m/Y H:i:s')
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar conteúdo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Histórico de versões
     */
    public function versoes(int $id): View
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);

            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            return view('modules.projetos.versoes', compact('projeto'));

        } catch (Exception $e) {
            abort(500, 'Erro ao carregar versões: ' . $e->getMessage());
        }
    }

    /**
     * Tramitação
     */
    public function tramitacao(int $id): View
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);

            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            return view('modules.projetos.tramitacao', compact('projeto'));

        } catch (Exception $e) {
            abort(500, 'Erro ao carregar tramitação: ' . $e->getMessage());
        }
    }

    /**
     * Anexos
     */
    public function anexos(int $id): View
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);

            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            return view('modules.projetos.anexos', compact('projeto'));

        } catch (Exception $e) {
            abort(500, 'Erro ao carregar anexos: ' . $e->getMessage());
        }
    }

    // ========== MÉTODOS DE WORKFLOW ==========

    /**
     * Enviar projeto para análise
     */
    public function enviarParaAnalise(Request $request, int $id): RedirectResponse
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);
            
            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            $this->authorize('projeto.edit_own', $projeto);

            $this->workflowService->enviarParaAnalise($projeto, $request->observacoes);

            return redirect()->route('projetos.show', $id)
                ->with('success', 'Projeto enviado para análise com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao enviar projeto: ' . $e->getMessage());
        }
    }

    /**
     * Iniciar análise do projeto
     */
    public function iniciarAnalise(Request $request, int $id): RedirectResponse
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);
            
            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            $this->authorize('projeto.analyze');

            $this->workflowService->iniciarAnalise($projeto, $request->observacoes);

            return redirect()->route('projetos.show', $id)
                ->with('success', 'Análise iniciada com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao iniciar análise: ' . $e->getMessage());
        }
    }

    /**
     * Aprovar projeto
     */
    public function aprovar(Request $request, int $id): RedirectResponse
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);
            
            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            $this->authorize('projeto.approve');

            $this->workflowService->aprovarProjeto($projeto, $request->observacoes);

            return redirect()->route('projetos.show', $id)
                ->with('success', 'Projeto aprovado com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao aprovar projeto: ' . $e->getMessage());
        }
    }

    /**
     * Rejeitar projeto
     */
    public function rejeitar(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'motivo' => 'required|string|min:10',
        ], [
            'motivo.required' => 'O motivo da rejeição é obrigatório',
            'motivo.min' => 'O motivo deve ter pelo menos 10 caracteres',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $projeto = $this->projetoService->obterPorId($id);
            
            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            $this->authorize('projeto.reject');

            $this->workflowService->rejeitarProjeto($projeto, $request->motivo);

            return redirect()->route('projetos.show', $id)
                ->with('success', 'Projeto rejeitado.');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao rejeitar projeto: ' . $e->getMessage());
        }
    }

    /**
     * Assinar projeto
     */
    public function assinar(Request $request, int $id): RedirectResponse
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);
            
            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            $this->authorize('projeto.sign', $projeto);

            $this->workflowService->assinarProjeto($projeto, $request->observacoes);

            return redirect()->route('projetos.show', $id)
                ->with('success', 'Projeto assinado com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao assinar projeto: ' . $e->getMessage());
        }
    }

    /**
     * Protocolar projeto
     */
    public function protocolarProjeto(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'numero_protocolo' => 'required|string|max:50',
        ], [
            'numero_protocolo.required' => 'O número do protocolo é obrigatório',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $projeto = $this->projetoService->obterPorId($id);
            
            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            $this->authorize('projeto.assign_number');

            $this->workflowService->protocolarProjeto(
                $projeto, 
                $request->numero_protocolo, 
                $request->observacoes
            );

            return redirect()->route('projetos.show', $id)
                ->with('success', 'Projeto protocolado com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao protocolar projeto: ' . $e->getMessage());
        }
    }

    /**
     * Incluir projeto em sessão
     */
    public function incluirEmSessao(Request $request, int $id): RedirectResponse
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);
            
            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            $this->authorize('projeto.include_session');

            $this->workflowService->incluirEmSessao($projeto, $request->observacoes);

            return redirect()->route('projetos.show', $id)
                ->with('success', 'Projeto incluído em sessão com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao incluir projeto em sessão: ' . $e->getMessage());
        }
    }

    /**
     * Votar projeto
     */
    public function votar(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'resultado_votacao' => 'required|in:aprovado,rejeitado,adiado',
        ], [
            'resultado_votacao.required' => 'O resultado da votação é obrigatório',
            'resultado_votacao.in' => 'Resultado inválido',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $projeto = $this->projetoService->obterPorId($id);
            
            if (!$projeto) {
                abort(404, 'Projeto não encontrado');
            }

            $this->authorize('tramitacao.manage');

            $this->workflowService->votarProjeto(
                $projeto, 
                $request->resultado_votacao, 
                $request->observacoes
            );

            return redirect()->route('projetos.show', $id)
                ->with('success', 'Votação registrada com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao registrar votação: ' . $e->getMessage());
        }
    }

    /**
     * Gerar número de protocolo automático (AJAX)
     */
    public function gerarNumeroProtocolo(): JsonResponse
    {
        try {
            $this->authorize('projeto.assign_number');

            $numeroProtocolo = $this->workflowService->gerarNumeroProtocolo();

            return response()->json([
                'success' => true,
                'numero_protocolo' => $numeroProtocolo
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar número de protocolo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter próximas ações do projeto (AJAX)
     */
    public function proximasAcoes(int $id): JsonResponse
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);
            
            if (!$projeto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Projeto não encontrado'
                ], 404);
            }

            $acoes = $this->workflowService->getProximasAcoes($projeto, auth()->user());

            return response()->json([
                'success' => true,
                'acoes' => $acoes
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter ações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter histórico de tramitação (AJAX)
     */
    public function historicoTramitacao(int $id): JsonResponse
    {
        try {
            $projeto = $this->projetoService->obterPorId($id);
            
            if (!$projeto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Projeto não encontrado'
                ], 404);
            }

            $historico = $this->workflowService->getHistoricoTramitacao($projeto);

            return response()->json([
                'success' => true,
                'historico' => $historico
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter histórico: ' . $e->getMessage()
            ], 500);
        }
    }
}