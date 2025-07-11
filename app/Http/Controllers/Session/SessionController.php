<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Services\Session\SessionService;
use App\Services\Parlamentar\ParlamentarService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Exception;

class SessionController extends Controller
{
    private SessionService $sessionService;
    private ParlamentarService $parlamentarService;

    public function __construct(SessionService $sessionService, ParlamentarService $parlamentarService)
    {
        $this->sessionService = $sessionService;
        $this->parlamentarService = $parlamentarService;
    }

    /**
     * Lista de sessões
     */
    public function index(Request $request): View
    {
        try {
            $filtros = $request->only(['tipo_id', 'ano', 'status', 'com_votacao']);
            $sessions = $this->sessionService->listar($filtros);
            
            return view('admin.sessions.index', [
                'sessions' => $sessions['data'] ?? [],
                'filtros' => $filtros,
                'tipos_sessao' => $this->sessionService->obterTiposSessao(),
                'success' => session('success'),
                'error' => session('error')
            ]);

        } catch (Exception $e) {
            return view('admin.sessions.index', [
                'sessions' => [],
                'filtros' => [],
                'tipos_sessao' => $this->sessionService->obterTiposSessao(),
                'error' => 'Erro ao carregar sessões: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Formulário de criação
     */
    public function create(): View
    {
        return view('admin.sessions.create', [
            'tipos_sessao' => $this->sessionService->obterTiposSessao(),
        ]);
    }

    /**
     * Salvar nova sessão
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'numero' => 'required|integer|min:1',
                'ano' => 'required|integer|min:2020|max:2030',
                'data' => 'required|date|after_or_equal:today',
                'hora' => 'required|string',
                'tipo_id' => 'required|integer|in:8,9,10',
                'observacoes' => 'nullable|string|max:1000'
            ]);

            $data = $request->only(['numero', 'ano', 'data', 'hora', 'tipo_id', 'observacoes']);
            $session = $this->sessionService->criar($data);

            return redirect()->route('admin.sessions.show', $session['id'])
                ->with('success', 'Sessão criada com sucesso!');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar sessão: ' . $e->getMessage());
        }
    }

    /**
     * Detalhes da sessão
     */
    public function show(Request $request, int $id): View
    {
        try {
            $session = $this->sessionService->obterPorId($id);
            
            if (!$session) {
                abort(404, 'Sessão não encontrada');
            }

            $matters = $this->sessionService->obterMaterias($id);
            $exports = $this->sessionService->obterHistoricoExportacoes($id);

            return view('admin.sessions.show', [
                'session' => $session,
                'matters' => $matters['data'] ?? [],
                'exports' => $exports['data'] ?? [],
                'tipos_materia' => $this->sessionService->obterTiposMateria(),
                'fases_tramitacao' => $this->sessionService->obterFasesTramitacao(),
                'regimes_tramitacao' => $this->sessionService->obterRegimesTramitacao(),
                'tipos_quorum' => $this->sessionService->obterTiposQuorum(),
                'parlamentares' => $this->obterParlamentares(),
                'success' => session('success'),
                'error' => session('error')
            ]);

        } catch (Exception $e) {
            return redirect()->route('admin.sessions.index')
                ->with('error', 'Erro ao carregar sessão: ' . $e->getMessage());
        }
    }

    /**
     * Formulário de edição
     */
    public function edit(int $id): View
    {
        try {
            $session = $this->sessionService->obterPorId($id);
            
            if (!$session) {
                abort(404, 'Sessão não encontrada');
            }

            return view('admin.sessions.edit', [
                'session' => $session,
                'tipos_sessao' => $this->sessionService->obterTiposSessao(),
            ]);

        } catch (Exception $e) {
            return redirect()->route('admin.sessions.index')
                ->with('error', 'Erro ao carregar sessão: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar sessão
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $request->validate([
                'numero' => 'required|integer|min:1',
                'ano' => 'required|integer|min:2020|max:2030',
                'data' => 'required|date',
                'hora' => 'required|string',
                'tipo_id' => 'required|integer|in:8,9,10',
                'status' => 'required|string|in:preparacao,agendada,exportada,concluida',
                'observacoes' => 'nullable|string|max:1000',
                'hora_inicial' => 'nullable|string',
                'hora_final' => 'nullable|string'
            ]);

            $data = $request->only([
                'numero', 'ano', 'data', 'hora', 'tipo_id', 'status', 
                'observacoes', 'hora_inicial', 'hora_final'
            ]);
            
            $this->sessionService->atualizar($id, $data);

            return redirect()->route('admin.sessions.show', $id)
                ->with('success', 'Sessão atualizada com sucesso!');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar sessão: ' . $e->getMessage());
        }
    }

    /**
     * Excluir sessão
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->sessionService->excluir($id);

            return redirect()->route('admin.sessions.index')
                ->with('success', 'Sessão excluída com sucesso!');

        } catch (Exception $e) {
            return back()
                ->with('error', 'Erro ao excluir sessão: ' . $e->getMessage());
        }
    }

    /**
     * Adicionar matéria à sessão
     */
    public function addMatter(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'tipo_id' => 'required|integer|in:109,135,138,140,141',
                'numero' => 'required|string|max:20',
                'ano' => 'required|integer|min:2020|max:2030',
                'descricao' => 'required|string|max:500',
                'assunto' => 'required|string|max:200',
                'autor_id' => 'required|integer',
                'fase_id' => 'required|integer|in:13,14,15,16,17',
                'regime_id' => 'nullable|integer|in:6,7,8',
                'quorum_id' => 'nullable|integer|in:28,29,30',
                'data' => 'nullable|date'
            ]);

            $data = $request->only([
                'tipo_id', 'numero', 'ano', 'descricao', 'assunto', 
                'autor_id', 'fase_id', 'regime_id', 'quorum_id', 'data'
            ]);

            $matter = $this->sessionService->adicionarMateria($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Matéria adicionada com sucesso!',
                'data' => $matter
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar matéria: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Atualizar matéria na sessão
     */
    public function updateMatter(Request $request, int $sessionId, int $matterId): JsonResponse
    {
        try {
            $request->validate([
                'tipo_id' => 'sometimes|integer|in:109,135,138,140,141',
                'numero' => 'sometimes|string|max:20',
                'ano' => 'sometimes|integer|min:2020|max:2030',
                'descricao' => 'sometimes|string|max:500',
                'assunto' => 'sometimes|string|max:200',
                'autor_id' => 'sometimes|integer',
                'fase_id' => 'sometimes|integer|in:13,14,15,16,17',
                'regime_id' => 'nullable|integer|in:6,7,8',
                'quorum_id' => 'nullable|integer|in:28,29,30',
                'data' => 'nullable|date'
            ]);

            $data = $request->only([
                'tipo_id', 'numero', 'ano', 'descricao', 'assunto', 
                'autor_id', 'fase_id', 'regime_id', 'quorum_id', 'data'
            ]);

            $matter = $this->sessionService->atualizarMateria($sessionId, $matterId, $data);

            return response()->json([
                'success' => true,
                'message' => 'Matéria atualizada com sucesso!',
                'data' => $matter
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar matéria: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remover matéria da sessão
     */
    public function removeMatter(int $sessionId, int $matterId): JsonResponse
    {
        try {
            $this->sessionService->removerMateria($sessionId, $matterId);

            return response()->json([
                'success' => true,
                'message' => 'Matéria removida com sucesso!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover matéria: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Gerar XML da sessão
     */
    public function generateXml(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'document_type' => 'required|string|in:expediente,ordem_do_dia'
            ]);

            $documentType = $request->input('document_type');
            $xml = $this->sessionService->gerarXml($id, $documentType);

            return response()->json([
                'success' => true,
                'message' => 'XML gerado com sucesso!',
                'data' => $xml
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar XML: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Exportar XML da sessão
     */
    public function exportXml(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'xml' => 'required|string',
                'document_type' => 'required|string|in:expediente,ordem_do_dia'
            ]);

            $xmlData = $request->only(['xml', 'document_type']);
            $export = $this->sessionService->exportarXml($id, $xmlData);

            return response()->json([
                'success' => true,
                'message' => 'XML exportado com sucesso!',
                'data' => $export
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao exportar XML: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Preview do XML antes da exportação
     */
    public function previewXml(Request $request, int $id): View
    {
        try {
            $request->validate([
                'document_type' => 'required|string|in:expediente,ordem_do_dia'
            ]);

            $documentType = $request->input('document_type');
            $xmlData = $this->sessionService->gerarXml($id, $documentType);
            $session = $this->sessionService->obterPorId($id);

            return view('admin.sessions.preview-xml', [
                'session' => $session,
                'xml' => $xmlData['data']['xml'] ?? '',
                'document_type' => $documentType,
                'matter_count' => $xmlData['data']['matter_count'] ?? 0
            ]);

        } catch (Exception $e) {
            return redirect()->route('admin.sessions.show', $id)
                ->with('error', 'Erro ao gerar preview: ' . $e->getMessage());
        }
    }

    /**
     * Obter parlamentares para select
     */
    private function obterParlamentares(): array
    {
        try {
            $parlamentares = $this->parlamentarService->getAll();
            return $parlamentares->toArray();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * API endpoint para buscar parlamentares (AJAX)
     */
    public function searchParlamentares(Request $request): JsonResponse
    {
        try {
            $parlamentares = $this->obterParlamentares();
            
            $termo = $request->input('q', '');
            if ($termo) {
                $parlamentares = array_filter($parlamentares, function($p) use ($termo) {
                    return stripos($p['nome'], $termo) !== false;
                });
            }

            return response()->json([
                'success' => true,
                'data' => array_values($parlamentares)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar parlamentares: ' . $e->getMessage()
            ], 400);
        }
    }
}