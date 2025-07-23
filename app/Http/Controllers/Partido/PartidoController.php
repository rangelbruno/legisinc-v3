<?php

namespace App\Http\Controllers\Partido;

use App\Http\Controllers\Controller;
use App\Models\Partido;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PartidoController extends Controller
{
    /**
     * Exibir lista de partidos
     */
    public function index(Request $request): View
    {
        try {
            $query = Partido::query();
            
            // Aplicar filtros da requisição
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('busca') && $request->busca) {
                $query->buscar($request->busca);
            }
            
            $partidos = $query->orderBy('nome')->paginate(15);
            
            return view('modules.partidos.index', [
                'partidos' => $partidos,
                'filtros' => $request->only(['status', 'busca']),
                'title' => 'Partidos Políticos'
            ]);
            
        } catch (\Exception $e) {
            return view('modules.partidos.index', [
                'partidos' => collect([]),
                'filtros' => [],
                'error' => 'Erro ao carregar partidos: ' . $e->getMessage(),
                'title' => 'Partidos Políticos'
            ]);
        }
    }
    
    /**
     * Exibir detalhes de um partido
     */
    public function show(int $id): View|RedirectResponse
    {
        try {
            $partido = Partido::with('parlamentares')->findOrFail($id);
            
            return view('modules.partidos.show', [
                'partido' => $partido,
                'parlamentares' => $partido->parlamentares,
                'title' => 'Partido - ' . $partido->nome
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('partidos.index')
                ->with('error', 'Partido não encontrado.');
        } catch (\Exception $e) {
            return redirect()->route('partidos.index')
                ->with('error', 'Erro ao carregar partido: ' . $e->getMessage());
        }
    }
    
    /**
     * Exibir formulário de criação
     */
    public function create(): View
    {
        return view('modules.partidos.create', [
            'title' => 'Novo Partido',
            'statusOptions' => $this->getStatusOptions()
        ]);
    }
    
    /**
     * Armazenar novo partido
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'sigla' => 'required|string|max:10|unique:partidos,sigla',
            'nome' => 'required|string|max:255',
            'numero' => 'required|string|max:3|unique:partidos,numero',
            'presidente' => 'nullable|string|max:255',
            'fundacao' => 'nullable|date|before:today',
            'site' => 'nullable|url|max:255',
            'status' => 'required|in:ativo,inativo',
        ]);

        try {
            // Converter sigla para maiúsculas
            $validatedData['sigla'] = strtoupper($validatedData['sigla']);
            
            $partido = Partido::create($validatedData);
            
            return redirect()->route('partidos.show', $partido->id)
                ->with('success', 'Partido criado com sucesso!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar partido: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Exibir formulário de edição
     */
    public function edit(int $id): View|RedirectResponse
    {
        try {
            $partido = Partido::findOrFail($id);
            
            return view('modules.partidos.edit', [
                'partido' => $partido,
                'title' => 'Editar Partido - ' . $partido->nome,
                'statusOptions' => $this->getStatusOptions()
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('partidos.index')
                ->with('error', 'Partido não encontrado.');
        } catch (\Exception $e) {
            return redirect()->route('partidos.index')
                ->with('error', 'Erro ao carregar partido: ' . $e->getMessage());
        }
    }
    
    /**
     * Atualizar partido
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validatedData = $request->validate([
            'sigla' => 'required|string|max:10|unique:partidos,sigla,' . $id,
            'nome' => 'required|string|max:255',
            'numero' => 'required|string|max:3|unique:partidos,numero,' . $id,
            'presidente' => 'nullable|string|max:255',
            'fundacao' => 'nullable|date|before:today',
            'site' => 'nullable|url|max:255',
            'status' => 'required|in:ativo,inativo',
        ]);

        try {
            $partido = Partido::findOrFail($id);
            
            // Converter sigla para maiúsculas
            $validatedData['sigla'] = strtoupper($validatedData['sigla']);
            
            $partido->update($validatedData);
            
            return redirect()->route('partidos.show', $id)
                ->with('success', 'Partido atualizado com sucesso!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->route('partidos.index')
                ->with('error', 'Partido não encontrado.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar partido: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Deletar partido
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $partido = Partido::findOrFail($id);
            
            // Verificar se há parlamentares vinculados
            if ($partido->parlamentares()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Não é possível deletar este partido pois há parlamentares vinculados a ele.');
            }
            
            $nomePartido = $partido->nome;
            $partido->delete();
            
            return redirect()->route('partidos.index')
                ->with('success', "Partido {$nomePartido} deletado com sucesso!");
                
        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Partido não encontrado.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao deletar partido: ' . $e->getMessage());
        }
    }
    
    /**
     * Buscar partidos
     */
    public function search(Request $request): View
    {
        $termo = $request->get('q', '');
        
        try {
            if (empty($termo)) {
                return redirect()->route('partidos.index');
            }
            
            $partidos = Partido::buscar($termo)->orderBy('nome')->paginate(15);
            
            return view('modules.partidos.search', [
                'partidos' => $partidos,
                'termo' => $termo,
                'total' => $partidos->total(),
                'title' => 'Busca por: ' . $termo
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('partidos.index')
                ->with('error', 'Erro na busca: ' . $e->getMessage());
        }
    }
    
    /**
     * Obter opções de status
     */
    private function getStatusOptions(): array
    {
        return [
            'ativo' => 'Ativo',
            'inativo' => 'Inativo'
        ];
    }

    /**
     * Exportar partidos para CSV
     */
    public function exportCsv(): \Symfony\Component\HttpFoundation\Response
    {
        try {
            $partidos = Partido::with('parlamentares')->orderBy('nome')->get();
            
            $csvData = [];
            $csvData[] = ['Sigla', 'Nome', 'Número', 'Presidente', 'Status', 'Fundação', 'Site', 'Total Parlamentares'];
            
            foreach ($partidos as $partido) {
                $csvData[] = [
                    $partido->sigla,
                    $partido->nome,
                    $partido->numero,
                    $partido->presidente ?? '',
                    $partido->status,
                    $partido->fundacao_formatada,
                    $partido->site ?? '',
                    $partido->total_parlamentares
                ];
            }
            
            $filename = 'partidos_' . date('Y-m-d_H-i-s') . '.csv';
            
            $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() use ($csvData) {
                $handle = fopen('php://output', 'w');
                foreach ($csvData as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
            });
            
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
            return $response;
            
        } catch (\Exception $e) {
            return redirect()->route('partidos.index')
                ->with('error', 'Erro ao exportar dados: ' . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas dos partidos
     */
    public function estatisticas(): View|RedirectResponse
    {
        try {
            $partidos = Partido::with('parlamentares')->get();
            
            $estatisticas = [
                'total_partidos' => $partidos->count(),
                'partidos_ativos' => $partidos->where('status', 'ativo')->count(),
                'partidos_inativos' => $partidos->where('status', 'inativo')->count(),
                'total_parlamentares' => $partidos->sum(fn($p) => $p->parlamentares->count()),
                'partido_mais_parlamentares' => $partidos->sortByDesc(fn($p) => $p->parlamentares->count())->first(),
                'partidos_sem_parlamentares' => $partidos->filter(fn($p) => $p->parlamentares->count() === 0)->count(),
                'por_decada_fundacao' => $partidos->filter(fn($p) => $p->fundacao)
                    ->groupBy(fn($p) => floor($p->fundacao->year / 10) * 10)
                    ->map->count()
                    ->sortKeys()
                    ->toArray(),
            ];
            
            return view('modules.partidos.estatisticas', [
                'estatisticas' => $estatisticas,
                'title' => 'Estatísticas dos Partidos'
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('partidos.index')
                ->with('error', 'Erro ao carregar estatísticas: ' . $e->getMessage());
        }
    }
}