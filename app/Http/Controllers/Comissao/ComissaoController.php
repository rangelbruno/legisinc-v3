<?php

namespace App\Http\Controllers\Comissao;

use App\Http\Controllers\Controller;
use App\Services\Comissao\ComissaoService;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComissaoController extends Controller
{
    protected ComissaoService $comissaoService;
    
    public function __construct(ComissaoService $comissaoService)
    {
        $this->comissaoService = $comissaoService;
    }
    
    /**
     * Exibir lista de comissões
     */
    public function index(Request $request): View
    {
        try {
            $filters = [];
            
            // Aplicar filtros da requisição
            if ($request->has('tipo') && $request->tipo) {
                $filters['tipo'] = $request->tipo;
            }
            
            if ($request->has('status') && $request->status) {
                $filters['status'] = $request->status;
            }
            
            $comissoes = $this->comissaoService->getAll($filters);
            $estatisticas = $this->comissaoService->getEstatisticas();
            
            // Formatir comissões para exibição
            $comissoesFormatadas = $comissoes->map(function ($comissao) {
                return $this->comissaoService->formatForDisplay($comissao);
            });
            
            return view('modules.comissoes.index', [
                'comissoes' => $comissoesFormatadas,
                'estatisticas' => $estatisticas,
                'filtros' => $filters,
                'title' => 'Comissões'
            ]);
            
        } catch (ApiException $e) {
            return view('modules.comissoes.index', [
                'comissoes' => collect([]),
                'estatisticas' => [],
                'filtros' => [],
                'error' => $e->getMessage(),
                'title' => 'Comissões'
            ]);
        }
    }
    
    /**
     * Exibir detalhes de uma comissão
     */
    public function show(int $id): View
    {
        try {
            $comissao = $this->comissaoService->getById($id);
            $membros = $this->comissaoService->getMembros($id);
            $reunioes = $this->comissaoService->getReunioes($id);
            
            $comissaoFormatada = $this->comissaoService->formatForDisplay($comissao);
            
            return view('modules.comissoes.show', [
                'comissao' => $comissaoFormatada,
                'membros' => $membros['membros'],
                'reunioes' => $reunioes['reunioes'],
                'meta_membros' => $membros['meta'],
                'meta_reunioes' => $reunioes['meta'],
                'title' => 'Comissão - ' . $comissao['nome']
            ]);
            
        } catch (ApiException $e) {
            return redirect()->route('comissoes.index')
                ->with('error', 'Comissão não encontrada: ' . $e->getMessage());
        }
    }
    
    /**
     * Exibir formulário de criação
     */
    public function create(): View
    {
        return view('modules.comissoes.create', [
            'title' => 'Nova Comissão',
            'tipos' => $this->getTiposOptions(),
            'statusOptions' => $this->getStatusOptions(),
            'parlamentares' => $this->getParlamentaresOptions()
        ]);
    }
    
    /**
     * Armazenar nova comissão
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $request->only([
                'nome', 'descricao', 'tipo', 'status', 'presidente_id',
                'vice_presidente_id', 'relator_id', 'membros', 'finalidade'
            ]);
            
            // Validar dados
            $errors = $this->comissaoService->validateData($data);
            if (!empty($errors)) {
                return redirect()->back()
                    ->withErrors($errors)
                    ->withInput();
            }
            
            // Processar membros (se vier como string)
            if (isset($data['membros']) && is_string($data['membros'])) {
                $data['membros'] = array_filter(explode(',', $data['membros']));
            }
            
            $comissao = $this->comissaoService->create($data);
            
            return redirect()->route('comissoes.show', $comissao['id'])
                ->with('success', 'Comissão criada com sucesso!');
                
        } catch (ApiException $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar comissão: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Exibir formulário de edição
     */
    public function edit(int $id): View
    {
        try {
            $comissao = $this->comissaoService->getById($id);
            
            return view('modules.comissoes.edit', [
                'comissao' => $comissao,
                'title' => 'Editar Comissão - ' . $comissao['nome'],
                'tipos' => $this->getTiposOptions(),
                'statusOptions' => $this->getStatusOptions(),
                'parlamentares' => $this->getParlamentaresOptions()
            ]);
            
        } catch (ApiException $e) {
            return redirect()->route('comissoes.index')
                ->with('error', 'Comissão não encontrada: ' . $e->getMessage());
        }
    }
    
    /**
     * Atualizar comissão
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $data = $request->only([
                'nome', 'descricao', 'tipo', 'status', 'presidente_id',
                'vice_presidente_id', 'relator_id', 'membros', 'finalidade'
            ]);
            
            // Processar membros (se vier como string)
            if (isset($data['membros']) && is_string($data['membros'])) {
                $data['membros'] = array_filter(explode(',', $data['membros']));
            }
            
            $comissao = $this->comissaoService->update($id, $data);
            
            return redirect()->route('comissoes.show', $id)
                ->with('success', 'Comissão atualizada com sucesso!');
                
        } catch (ApiException $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar comissão: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Deletar comissão
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->comissaoService->delete($id);
            
            return redirect()->route('comissoes.index')
                ->with('success', 'Comissão deletada com sucesso!');
                
        } catch (ApiException $e) {
            return redirect()->back()
                ->with('error', 'Erro ao deletar comissão: ' . $e->getMessage());
        }
    }
    
    /**
     * Exibir comissões por tipo
     */
    public function porTipo(string $tipo): View
    {
        try {
            $comissoes = $this->comissaoService->getByTipo($tipo);
            
            $comissoesFormatadas = $comissoes->map(function ($comissao) {
                return $this->comissaoService->formatForDisplay($comissao);
            });
            
            $tipoFormatado = match($tipo) {
                'permanente' => 'Permanentes',
                'temporaria' => 'Temporárias',
                'especial' => 'Especiais',
                'cpi' => 'CPIs',
                default => ucfirst($tipo)
            };

            return view('modules.comissoes.por-tipo', [
                'comissoes' => $comissoesFormatadas,
                'tipo' => $tipo,
                'tipoFormatado' => $tipoFormatado,
                'title' => 'Comissões ' . $tipoFormatado
            ]);
            
        } catch (ApiException $e) {
            return redirect()->route('comissoes.index')
                ->with('error', 'Erro ao buscar comissões: ' . $e->getMessage());
        }
    }
    
    /**
     * Buscar comissões
     */
    public function search(Request $request): View
    {
        $termo = $request->get('q', '');
        
        try {
            if (empty($termo)) {
                return redirect()->route('comissoes.index');
            }
            
            $comissoes = $this->comissaoService->search($termo);
            
            $comissoesFormatadas = $comissoes->map(function ($comissao) {
                return $this->comissaoService->formatForDisplay($comissao);
            });
            
            return view('modules.comissoes.search', [
                'comissoes' => $comissoesFormatadas,
                'termo' => $termo,
                'total' => $comissoes->count(),
                'title' => 'Busca por: ' . $termo
            ]);
            
        } catch (ApiException $e) {
            return redirect()->route('comissoes.index')
                ->with('error', 'Erro na busca: ' . $e->getMessage());
        }
    }
    
    /**
     * Obter opções de tipos
     */
    private function getTiposOptions(): array
    {
        return [
            'permanente' => 'Permanente',
            'temporaria' => 'Temporária',
            'especial' => 'Especial',
            'cpi' => 'CPI - Comissão Parlamentar de Inquérito',
            'mista' => 'Mista'
        ];
    }
    
    /**
     * Obter opções de status
     */
    private function getStatusOptions(): array
    {
        return [
            'ativa' => 'Ativa',
            'inativa' => 'Inativa',
            'suspensa' => 'Suspensa',
            'encerrada' => 'Encerrada'
        ];
    }
    
    /**
     * Obter opções de parlamentares
     */
    private function getParlamentaresOptions(): array
    {
        try {
            // Buscar parlamentares ativos através do serviço
            $parlamentarService = app('App\Services\Parlamentar\ParlamentarService');
            $parlamentares = $parlamentarService->getAll(['status' => 'ativo']);
            
            $options = [];
            foreach ($parlamentares as $parlamentar) {
                $options[$parlamentar['id']] = $parlamentar['nome'] . ' (' . $parlamentar['partido'] . ')';
            }
            
            return $options;
        } catch (ApiException $e) {
            return [];
        }
    }
}