<?php

namespace App\Http\Controllers\MesaDiretora;

use App\Http\Controllers\Controller;
use App\Services\MesaDiretora\MesaDiretoraService;
use App\Models\MesaDiretora;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class MesaDiretoraController extends Controller
{
    protected MesaDiretoraService $mesaDiretoraService;
    
    public function __construct(MesaDiretoraService $mesaDiretoraService)
    {
        $this->mesaDiretoraService = $mesaDiretoraService;
    }
    
    /**
     * Exibir lista de membros da mesa diretora
     */
    public function index(Request $request): View
    {
        try {
            $filters = [];
            
            // Aplicar filtros da requisição
            if ($request->has('status') && $request->status) {
                $filters['status'] = $request->status;
            }
            
            if ($request->has('cargo') && $request->cargo) {
                $filters['cargo'] = $request->cargo;
            }
            
            if ($request->has('mandato') && $request->mandato) {
                $filters['mandato'] = $request->mandato;
            }
            
            $membros = $this->mesaDiretoraService->getAll($filters);
            $estatisticas = $this->mesaDiretoraService->getEstatisticas();
            $cargosDisponiveis = $this->mesaDiretoraService->getCargosDisponiveis();
            
            // Formatar membros para exibição
            $membrosFormatados = $membros->map(function ($membro) {
                return $this->mesaDiretoraService->formatForDisplay($membro->toArray());
            });
            
            return view('modules.mesa-diretora.index', [
                'membros' => $membrosFormatados,
                'estatisticas' => $estatisticas,
                'filtros' => $filters,
                'cargos_disponiveis' => $cargosDisponiveis,
                'title' => 'Mesa Diretora'
            ]);
            
        } catch (\Exception $e) {
            return view('modules.mesa-diretora.index', [
                'membros' => collect([]),
                'estatisticas' => [],
                'filtros' => [],
                'cargos_disponiveis' => [],
                'error' => 'Erro ao carregar mesa diretora: ' . $e->getMessage(),
                'title' => 'Mesa Diretora'
            ]);
        }
    }

    /**
     * Exibir composição atual da mesa diretora
     */
    public function composicaoAtual(): View
    {
        try {
            $composicaoAtual = $this->mesaDiretoraService->getComposicaoAtual();
            $estatisticas = $this->mesaDiretoraService->getEstatisticas();
            
            // Formatar composição para exibição
            $composicaoFormatada = $composicaoAtual->map(function ($membro) {
                return $this->mesaDiretoraService->formatForDisplay($membro->toArray());
            });
            
            return view('modules.mesa-diretora.atual', [
                'composicao' => $composicaoFormatada,
                'estatisticas' => $estatisticas,
                'title' => 'Composição Atual - Mesa Diretora'
            ]);
            
        } catch (\Exception $e) {
            return view('modules.mesa-diretora.atual', [
                'composicao' => collect([]),
                'estatisticas' => [],
                'error' => 'Erro ao carregar composição atual: ' . $e->getMessage(),
                'title' => 'Composição Atual - Mesa Diretora'
            ]);
        }
    }

    /**
     * Exibir histórico de mandatos
     */
    public function historico(): View
    {
        try {
            $historico = $this->mesaDiretoraService->getHistoricoMandatos();
            
            return view('modules.mesa-diretora.historico', [
                'historico' => $historico,
                'title' => 'Histórico - Mesa Diretora'
            ]);
            
        } catch (\Exception $e) {
            return view('modules.mesa-diretora.historico', [
                'historico' => collect([]),
                'error' => 'Erro ao carregar histórico: ' . $e->getMessage(),
                'title' => 'Histórico - Mesa Diretora'
            ]);
        }
    }
    
    /**
     * Exibir formulário de criação
     */
    public function create(): View
    {
        try {
            $parlamentares = $this->mesaDiretoraService->getParlamentaresElegiveis();
            $cargos = $this->mesaDiretoraService->getCargosDisponiveis();
            
            return view('modules.mesa-diretora.create', [
                'parlamentares' => $parlamentares,
                'cargos' => $cargos,
                'title' => 'Novo Membro - Mesa Diretora'
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('mesa-diretora.index')
                ->with('error', 'Erro ao carregar formulário: ' . $e->getMessage());
        }
    }
    
    /**
     * Armazenar novo membro
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validar dados de entrada
            $validatedData = $request->validate([
                'parlamentar_id' => 'required|exists:parlamentars,id',
                'cargo_mesa' => 'required|string|max:100',
                'mandato_inicio' => 'required|date_format:d/m/Y',
                'mandato_fim' => 'required|date_format:d/m/Y|after:mandato_inicio',
                'observacoes' => 'nullable|string',
            ], [
                'parlamentar_id.required' => 'Parlamentar é obrigatório',
                'parlamentar_id.exists' => 'Parlamentar não encontrado',
                'cargo_mesa.required' => 'Cargo é obrigatório',
                'mandato_inicio.required' => 'Data de início do mandato é obrigatória',
                'mandato_inicio.date_format' => 'Data de início deve estar no formato DD/MM/AAAA',
                'mandato_fim.required' => 'Data de fim do mandato é obrigatória',
                'mandato_fim.date_format' => 'Data de fim deve estar no formato DD/MM/AAAA',
                'mandato_fim.after' => 'Data de fim deve ser posterior à data de início',
            ]);

            // Converter datas do formato brasileiro para o formato do banco
            $validatedData['mandato_inicio'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validatedData['mandato_inicio'])->format('Y-m-d');
            $validatedData['mandato_fim'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validatedData['mandato_fim'])->format('Y-m-d');
            $validatedData['status'] = 'ativo';
            
            $membro = $this->mesaDiretoraService->create($validatedData);
            
            return redirect()->route('mesa-diretora.index')
                ->with('success', 'Membro da mesa diretora criado com sucesso!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar membro: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Exibir detalhes de um membro
     */
    public function show(int $id): View|RedirectResponse
    {
        try {
            $membro = $this->mesaDiretoraService->getById($id);
            
            return view('modules.mesa-diretora.show', [
                'membro' => $membro,
                'title' => 'Membro - ' . $membro['parlamentar_nome']
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('mesa-diretora.index')
                ->with('error', 'Membro da mesa diretora não encontrado.');
        } catch (\Exception $e) {
            return redirect()->route('mesa-diretora.index')
                ->with('error', 'Erro ao carregar membro: ' . $e->getMessage());
        }
    }
    
    /**
     * Exibir formulário de edição
     */
    public function edit(int $id): View|RedirectResponse
    {
        try {
            $membro = $this->mesaDiretoraService->getById($id);
            $parlamentares = $this->mesaDiretoraService->getParlamentaresElegiveis();
            $cargos = $this->mesaDiretoraService->getCargosDisponiveis();
            
            return view('modules.mesa-diretora.edit', [
                'membro' => $membro,
                'parlamentares' => $parlamentares,
                'cargos' => $cargos,
                'title' => 'Editar Membro - ' . $membro['parlamentar_nome']
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('mesa-diretora.index')
                ->with('error', 'Membro da mesa diretora não encontrado.');
        } catch (\Exception $e) {
            return redirect()->route('mesa-diretora.index')
                ->with('error', 'Erro ao carregar formulário: ' . $e->getMessage());
        }
    }
    
    /**
     * Atualizar membro
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            // Validar dados de entrada
            $validatedData = $request->validate([
                'parlamentar_id' => 'required|exists:parlamentars,id',
                'cargo_mesa' => 'required|string|max:100',
                'mandato_inicio' => 'required|date_format:d/m/Y',
                'mandato_fim' => 'required|date_format:d/m/Y|after:mandato_inicio',
                'status' => 'required|in:ativo,finalizado',
                'observacoes' => 'nullable|string',
            ], [
                'parlamentar_id.required' => 'Parlamentar é obrigatório',
                'parlamentar_id.exists' => 'Parlamentar não encontrado',
                'cargo_mesa.required' => 'Cargo é obrigatório',
                'mandato_inicio.required' => 'Data de início do mandato é obrigatória',
                'mandato_inicio.date_format' => 'Data de início deve estar no formato DD/MM/AAAA',
                'mandato_fim.required' => 'Data de fim do mandato é obrigatória',
                'mandato_fim.date_format' => 'Data de fim deve estar no formato DD/MM/AAAA',
                'mandato_fim.after' => 'Data de fim deve ser posterior à data de início',
                'status.required' => 'Status é obrigatório',
                'status.in' => 'Status deve ser ativo ou finalizado',
            ]);
            
            // Converter datas do formato brasileiro para o formato do banco
            $validatedData['mandato_inicio'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validatedData['mandato_inicio'])->format('Y-m-d');
            $validatedData['mandato_fim'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validatedData['mandato_fim'])->format('Y-m-d');
            
            $membro = $this->mesaDiretoraService->update($id, $validatedData);
            
            return redirect()->route('mesa-diretora.index')
                ->with('success', 'Membro da mesa diretora atualizado com sucesso!');
                
        } catch (ModelNotFoundException $e) {
            return redirect()->route('mesa-diretora.index')
                ->with('error', 'Membro da mesa diretora não encontrado.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar membro: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Deletar membro
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->mesaDiretoraService->delete($id);
            
            return redirect()->route('mesa-diretora.index')
                ->with('success', 'Membro da mesa diretora removido com sucesso!');
                
        } catch (ModelNotFoundException $e) {
            return redirect()->route('mesa-diretora.index')
                ->with('error', 'Membro da mesa diretora não encontrado.');
        } catch (\Exception $e) {
            return redirect()->route('mesa-diretora.index')
                ->with('error', 'Erro ao remover membro: ' . $e->getMessage());
        }
    }

    /**
     * Finalizar mandato de um membro
     */
    public function finalizarMandato(int $id): RedirectResponse
    {
        try {
            $this->mesaDiretoraService->finalizarMandato($id);
            
            return redirect()->route('mesa-diretora.index')
                ->with('success', 'Mandato finalizado com sucesso!');
                
        } catch (ModelNotFoundException $e) {
            return redirect()->route('mesa-diretora.index')
                ->with('error', 'Membro da mesa diretora não encontrado.');
        } catch (\Exception $e) {
            return redirect()->route('mesa-diretora.index')
                ->with('error', 'Erro ao finalizar mandato: ' . $e->getMessage());
        }
    }

    /**
     * Buscar membros via AJAX
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $termo = $request->get('termo', '');
            
            if (strlen($termo) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Termo de busca deve ter pelo menos 2 caracteres'
                ]);
            }
            
            $membros = $this->mesaDiretoraService->search($termo);
            
            $membrosFormatados = $membros->map(function ($membro) {
                return $this->mesaDiretoraService->formatForDisplay($membro->toArray());
            });
            
            return response()->json([
                'success' => true,
                'data' => $membrosFormatados,
                'total' => $membrosFormatados->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na busca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter estatísticas via AJAX
     */
    public function estatisticas(): JsonResponse
    {
        try {
            $estatisticas = $this->mesaDiretoraService->getEstatisticas();
            
            return response()->json([
                'success' => true,
                'data' => $estatisticas
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}
