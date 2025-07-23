<?php

namespace App\Http\Controllers\Parlamentar;

use App\Http\Controllers\Controller;
use App\Services\Parlamentar\ParlamentarService;
use App\Models\Parlamentar;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ParlamentarController extends Controller
{
    protected ParlamentarService $parlamentarService;
    
    public function __construct(ParlamentarService $parlamentarService)
    {
        $this->parlamentarService = $parlamentarService;
    }
    
    /**
     * Exibir lista de parlamentares
     */
    public function index(Request $request): View
    {
        try {
            $filters = [];
            
            // Aplicar filtros da requisição
            if ($request->has('partido') && $request->partido) {
                $filters['partido'] = $request->partido;
            }
            
            if ($request->has('status') && $request->status) {
                $filters['status'] = $request->status;
            }
            
            $parlamentares = $this->parlamentarService->getAll($filters);
            $estatisticas = $this->parlamentarService->getEstatisticas();
            
            // Formatir parlamentares para exibição
            $parlamentaresFormatados = $parlamentares->map(function ($parlamentar) {
                return $this->parlamentarService->formatForDisplay($parlamentar->toArray());
            });
            
            return view('modules.parlamentares.index', [
                'parlamentares' => $parlamentaresFormatados,
                'estatisticas' => $estatisticas,
                'filtros' => $filters,
                'title' => 'Parlamentares'
            ]);
            
        } catch (\Exception $e) {
            return view('modules.parlamentares.index', [
                'parlamentares' => collect([]),
                'estatisticas' => [],
                'filtros' => [],
                'error' => 'Erro ao carregar parlamentares: ' . $e->getMessage(),
                'title' => 'Parlamentares'
            ]);
        }
    }
    
    /**
     * Exibir detalhes de um parlamentar
     */
    public function show(int $id): View|RedirectResponse
    {
        try {
            $parlamentar = $this->parlamentarService->getById($id);
            $comissoes = $this->parlamentarService->getComissoes($id);
            
            $parlamentarFormatado = $this->parlamentarService->formatForDisplay($parlamentar);
            
            return view('modules.parlamentares.show', [
                'parlamentar' => $parlamentarFormatado,
                'comissoes' => $comissoes['comissoes'],
                'meta_comissoes' => $comissoes['meta'],
                'title' => 'Parlamentar - ' . $parlamentar['nome']
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Parlamentar não encontrado.');
        } catch (\Exception $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro ao carregar parlamentar: ' . $e->getMessage());
        }
    }
    
    /**
     * Exibir formulário de criação
     */
    public function create(): View
    {
        return view('modules.parlamentares.create', [
            'title' => 'Novo Parlamentar',
            'partidos' => $this->getPartidosOptions(),
            'cargos' => $this->getCargosOptions(),
            'statusOptions' => $this->getStatusOptions(),
            'escolaridadeOptions' => $this->getEscolaridadeOptions()
        ]);
    }
    
    /**
     * Armazenar novo parlamentar
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'nome_politico' => 'nullable|string|max:255',
            'partido' => 'required|string|max:50',
            'cargo' => 'required|string|max:100',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:parlamentars,email',
            'cpf' => 'nullable|string|max:14|unique:parlamentars,cpf',
            'data_nascimento' => 'nullable|date|before:today',
            'profissao' => 'nullable|string|max:100',
            'escolaridade' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'comissoes' => 'nullable|string',
        ]);

        // Definir status padrão para novos parlamentares
        $validatedData['status'] = 'ativo';

        // Debug: log dados validados
        \Log::info('Dados validados para criação:', $validatedData);

        try {
            // Processar upload da foto
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $nomeArquivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $foto->getClientOriginalName());
                $caminhoFoto = $foto->storeAs('public/parlamentares/fotos', $nomeArquivo);
                $validatedData['foto'] = $nomeArquivo;
            }
            
            // Processar comissões (se vier como string)
            if (isset($validatedData['comissoes']) && is_string($validatedData['comissoes'])) {
                $validatedData['comissoes'] = array_filter(explode(',', $validatedData['comissoes']));
            }
            
            // Remover campos vazios para evitar problemas
            $validatedData = array_filter($validatedData, function($value) {
                return $value !== null && $value !== '';
            });
            
            // Re-adicionar status pois pode ter sido removido
            $validatedData['status'] = 'ativo';
            
            \Log::info('Dados finais para criação:', $validatedData);
            
            $parlamentar = $this->parlamentarService->create($validatedData);
            
            return redirect()->route('parlamentares.show', $parlamentar['id'])
                ->with('success', 'Parlamentar criado com sucesso!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar parlamentar: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Exibir formulário de edição
     */
    public function edit(int $id): View|RedirectResponse
    {
        try {
            $parlamentar = $this->parlamentarService->getById($id);
            
            return view('modules.parlamentares.edit', [
                'parlamentar' => $parlamentar,
                'title' => 'Editar Parlamentar - ' . $parlamentar['nome'],
                'partidos' => $this->getPartidosOptions(),
                'cargos' => $this->getCargosOptions(),
                'statusOptions' => $this->getStatusOptions(),
                'escolaridadeOptions' => $this->getEscolaridadeOptions()
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Parlamentar não encontrado.');
        } catch (\Exception $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro ao carregar parlamentar: ' . $e->getMessage());
        }
    }
    
    /**
     * Atualizar parlamentar
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'nome_politico' => 'nullable|string|max:255',
            'partido' => 'required|string|max:50',
            'cargo' => 'required|string|max:100',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:parlamentars,email,' . $id,
            'cpf' => 'nullable|string|max:14|unique:parlamentars,cpf,' . $id,
            'data_nascimento' => 'nullable|date|before:today',
            'profissao' => 'nullable|string|max:100',
            'escolaridade' => 'nullable|string|max:100',
            'status' => 'required|in:ativo,licenciado,inativo',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'comissoes' => 'nullable|string',
        ]);


        // Debug: log dados validados
        \Log::info('Dados validados para atualização:', $validatedData);

        try {
            // Processar upload da foto
            if ($request->hasFile('foto')) {
                // Buscar parlamentar atual para deletar foto antiga
                $parlamentarAtual = $this->parlamentarService->getById($id);
                if (!empty($parlamentarAtual['foto']) && Storage::exists('public/parlamentares/fotos/' . $parlamentarAtual['foto'])) {
                    Storage::delete('public/parlamentares/fotos/' . $parlamentarAtual['foto']);
                }
                
                // Upload da nova foto
                $foto = $request->file('foto');
                $nomeArquivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $foto->getClientOriginalName());
                $caminhoFoto = $foto->storeAs('public/parlamentares/fotos', $nomeArquivo);
                $validatedData['foto'] = $nomeArquivo;
            }
            
            // Processar comissões (se vier como string)
            if (isset($validatedData['comissoes']) && is_string($validatedData['comissoes'])) {
                $validatedData['comissoes'] = array_filter(explode(',', $validatedData['comissoes']));
            }
            
            // Para atualização, manter campos vazios pois podem ser para limpar dados
            \Log::info('Dados finais para atualização:', $validatedData);
            
            $parlamentar = $this->parlamentarService->update($id, $validatedData);
            
            return redirect()->route('parlamentares.show', $id)
                ->with('success', 'Parlamentar atualizado com sucesso!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar parlamentar: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Deletar parlamentar
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Verificar se o parlamentar existe antes de deletar
            $parlamentar = $this->parlamentarService->getById($id);
            
            $this->parlamentarService->delete($id);
            
            return redirect()->route('parlamentares.index')
                ->with('success', "Parlamentar {$parlamentar['nome']} deletado com sucesso!");
                
        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Parlamentar não encontrado.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao deletar parlamentar: ' . $e->getMessage());
        }
    }
    
    /**
     * Exibir parlamentares por partido
     */
    public function porPartido(string $partido): View
    {
        try {
            $parlamentares = $this->parlamentarService->getByPartido($partido);
            
            $parlamentaresFormatados = $parlamentares->map(function ($parlamentar) {
                return $this->parlamentarService->formatForDisplay($parlamentar->toArray());
            });
            
            return view('modules.parlamentares.por-partido', [
                'parlamentares' => $parlamentaresFormatados,
                'partido' => strtoupper($partido),
                'title' => 'Parlamentares do ' . strtoupper($partido)
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro ao buscar parlamentares: ' . $e->getMessage());
        }
    }
    
    /**
     * Exibir mesa diretora
     */
    public function mesaDiretora(): View
    {
        try {
            $mesaDiretora = $this->parlamentarService->getMesaDiretora();
            
            return view('modules.parlamentares.mesa-diretora', [
                'mesaDiretora' => $mesaDiretora,
                'title' => 'Mesa Diretora'
            ]);
            
        } catch (\Exception $e) {
            return view('modules.parlamentares.mesa-diretora', [
                'mesaDiretora' => collect([]),
                'error' => 'Erro ao carregar mesa diretora: ' . $e->getMessage(),
                'title' => 'Mesa Diretora'
            ]);
        }
    }
    
    /**
     * Buscar parlamentares
     */
    public function search(Request $request): View
    {
        $termo = $request->get('q', '');
        
        try {
            if (empty($termo)) {
                return redirect()->route('parlamentares.index');
            }
            
            $parlamentares = $this->parlamentarService->search($termo);
            
            $parlamentaresFormatados = $parlamentares->map(function ($parlamentar) {
                return $this->parlamentarService->formatForDisplay($parlamentar->toArray());
            });
            
            return view('modules.parlamentares.search', [
                'parlamentares' => $parlamentaresFormatados,
                'termo' => $termo,
                'total' => $parlamentares->count(),
                'title' => 'Busca por: ' . $termo
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro na busca: ' . $e->getMessage());
        }
    }
    
    /**
     * Obter opções de partidos
     */
    private function getPartidosOptions(): array
    {
        return \App\Models\Partido::ativos()
            ->orderBy('sigla')
            ->pluck('nome', 'sigla')
            ->toArray();
    }
    
    /**
     * Obter opções de cargos
     */
    private function getCargosOptions(): array
    {
        return [
            'Vereador' => 'Vereador',
            'Vereadora' => 'Vereadora',
            'Presidente da Câmara' => 'Presidente da Câmara',
            'Vice-Presidente' => 'Vice-Presidente',
            '1º Secretário' => '1º Secretário',
            '2º Secretário' => '2º Secretário'
        ];
    }
    
    /**
     * Obter opções de status
     */
    private function getStatusOptions(): array
    {
        return [
            'ativo' => 'Ativo',
            'licenciado' => 'Licenciado',
            'inativo' => 'Inativo'
        ];
    }
    
    /**
     * Obter opções de escolaridade
     */
    private function getEscolaridadeOptions(): array
    {
        return [
            'Ensino Fundamental' => 'Ensino Fundamental',
            'Ensino Médio' => 'Ensino Médio',
            'Superior Incompleto' => 'Superior Incompleto',
            'Superior Completo' => 'Superior Completo',
            'Pós-Graduação' => 'Pós-Graduação',
            'Mestrado' => 'Mestrado',
            'Doutorado' => 'Doutorado'
        ];
    }

    /**
     * Exportar parlamentares para CSV
     */
    public function exportCsv(): \Symfony\Component\HttpFoundation\Response
    {
        try {
            $parlamentares = $this->parlamentarService->getAll();
            
            $csvData = [];
            $csvData[] = ['Nome', 'Partido', 'Cargo', 'Status', 'Email', 'Telefone', 'Data Nascimento', 'Profissão', 'Escolaridade'];
            
            foreach ($parlamentares as $parlamentar) {
                $csvData[] = [
                    $parlamentar['nome'],
                    $parlamentar['partido'],
                    $parlamentar['cargo'],
                    $parlamentar['status'],
                    $parlamentar['email'],
                    $parlamentar['telefone'],
                    $parlamentar['data_nascimento'],
                    $parlamentar['profissao'] ?? '',
                    $parlamentar['escolaridade'] ?? ''
                ];
            }
            
            $filename = 'parlamentares_' . date('Y-m-d_H-i-s') . '.csv';
            
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
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro ao exportar dados: ' . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas avançadas
     */
    public function estatisticas(): View|RedirectResponse
    {
        try {
            $estatisticas = $this->parlamentarService->getEstatisticas();
            $parlamentares = $this->parlamentarService->getAll();
            
            // Estatísticas por idade
            $idades = $parlamentares->map(function ($parlamentar) {
                return \Carbon\Carbon::parse($parlamentar['data_nascimento'])->age;
            });
            
            $estatisticasAvancadas = [
                'idade_media' => round($idades->avg(), 1),
                'idade_min' => $idades->min(),
                'idade_max' => $idades->max(),
                'por_faixa_etaria' => [
                    '20-30' => $idades->filter(fn($idade) => $idade >= 20 && $idade < 30)->count(),
                    '30-40' => $idades->filter(fn($idade) => $idade >= 30 && $idade < 40)->count(),
                    '40-50' => $idades->filter(fn($idade) => $idade >= 40 && $idade < 50)->count(),
                    '50-60' => $idades->filter(fn($idade) => $idade >= 50 && $idade < 60)->count(),
                    '60+' => $idades->filter(fn($idade) => $idade >= 60)->count(),
                ],
                'por_genero' => [
                    'masculino' => $parlamentares->filter(fn($p) => !str_ends_with($p['cargo'], 'a'))->count(),
                    'feminino' => $parlamentares->filter(fn($p) => str_ends_with($p['cargo'], 'a'))->count(),
                ],
                'por_escolaridade' => $parlamentares->groupBy('escolaridade')->map->count()->toArray(),
                'tempo_mandato' => $parlamentares->map(function ($parlamentar) {
                    $mandatos = $parlamentar['mandatos'] ?? [];
                    return count($mandatos);
                })->sum(),
            ];
            
            return view('modules.parlamentares.estatisticas', [
                'estatisticas' => array_merge($estatisticas, $estatisticasAvancadas),
                'title' => 'Estatísticas dos Parlamentares'
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro ao carregar estatísticas: ' . $e->getMessage());
        }
    }

}