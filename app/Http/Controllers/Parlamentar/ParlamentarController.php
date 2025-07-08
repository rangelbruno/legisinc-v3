<?php

namespace App\Http\Controllers\Parlamentar;

use App\Http\Controllers\Controller;
use App\Services\Parlamentar\ParlamentarService;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
                return $this->parlamentarService->formatForDisplay($parlamentar);
            });
            
            return view('modules.parlamentares.index', [
                'parlamentares' => $parlamentaresFormatados,
                'estatisticas' => $estatisticas,
                'filtros' => $filters,
                'title' => 'Parlamentares'
            ]);
            
        } catch (ApiException $e) {
            return view('modules.parlamentares.index', [
                'parlamentares' => collect([]),
                'estatisticas' => [],
                'filtros' => [],
                'error' => $e->getMessage(),
                'title' => 'Parlamentares'
            ]);
        }
    }
    
    /**
     * Exibir detalhes de um parlamentar
     */
    public function show(int $id): View
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
            
        } catch (ApiException $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Parlamentar não encontrado: ' . $e->getMessage());
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
        try {
            $data = $request->only([
                'nome', 'partido', 'cargo', 'telefone', 'email',
                'data_nascimento', 'profissao', 'escolaridade', 'comissoes'
            ]);
            
            // Validar dados
            $errors = $this->parlamentarService->validateData($data);
            if (!empty($errors)) {
                return redirect()->back()
                    ->withErrors($errors)
                    ->withInput();
            }
            
            // Processar comissões (se vier como string)
            if (isset($data['comissoes']) && is_string($data['comissoes'])) {
                $data['comissoes'] = array_filter(explode(',', $data['comissoes']));
            }
            
            $parlamentar = $this->parlamentarService->create($data);
            
            return redirect()->route('parlamentares.show', $parlamentar['id'])
                ->with('success', 'Parlamentar criado com sucesso!');
                
        } catch (ApiException $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar parlamentar: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Exibir formulário de edição
     */
    public function edit(int $id): View
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
            
        } catch (ApiException $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Parlamentar não encontrado: ' . $e->getMessage());
        }
    }
    
    /**
     * Atualizar parlamentar
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $data = $request->only([
                'nome', 'partido', 'cargo', 'telefone', 'email',
                'data_nascimento', 'profissao', 'escolaridade', 'status', 'comissoes'
            ]);
            
            // Processar comissões (se vier como string)
            if (isset($data['comissoes']) && is_string($data['comissoes'])) {
                $data['comissoes'] = array_filter(explode(',', $data['comissoes']));
            }
            
            $parlamentar = $this->parlamentarService->update($id, $data);
            
            return redirect()->route('parlamentares.show', $id)
                ->with('success', 'Parlamentar atualizado com sucesso!');
                
        } catch (ApiException $e) {
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
            $this->parlamentarService->delete($id);
            
            return redirect()->route('parlamentares.index')
                ->with('success', 'Parlamentar deletado com sucesso!');
                
        } catch (ApiException $e) {
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
                return $this->parlamentarService->formatForDisplay($parlamentar);
            });
            
            return view('modules.parlamentares.por-partido', [
                'parlamentares' => $parlamentaresFormatados,
                'partido' => strtoupper($partido),
                'title' => 'Parlamentares do ' . strtoupper($partido)
            ]);
            
        } catch (ApiException $e) {
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
            
        } catch (ApiException $e) {
            return view('modules.parlamentares.mesa-diretora', [
                'mesaDiretora' => collect([]),
                'error' => $e->getMessage(),
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
                return $this->parlamentarService->formatForDisplay($parlamentar);
            });
            
            return view('modules.parlamentares.search', [
                'parlamentares' => $parlamentaresFormatados,
                'termo' => $termo,
                'total' => $parlamentares->count(),
                'title' => 'Busca por: ' . $termo
            ]);
            
        } catch (ApiException $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro na busca: ' . $e->getMessage());
        }
    }
    
    /**
     * Obter opções de partidos
     */
    private function getPartidosOptions(): array
    {
        return [
            'PT' => 'Partido dos Trabalhadores',
            'PSDB' => 'Partido da Social Democracia Brasileira',
            'MDB' => 'Movimento Democrático Brasileiro',
            'PSL' => 'Partido Social Liberal',
            'PDT' => 'Partido Democrático Trabalhista',
            'PP' => 'Progressistas',
            'PSOL' => 'Partido Socialismo e Liberdade',
            'DEM' => 'Democratas',
            'PL' => 'Partido Liberal',
            'PCdoB' => 'Partido Comunista do Brasil'
        ];
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
}