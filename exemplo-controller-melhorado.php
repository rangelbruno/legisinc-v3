<?php

namespace App\Http\Controllers\Parameters;

use App\Http\Controllers\Controller;
use App\Services\ParameterService;
use App\Http\Requests\Parameters\StoreTipoSessaoRequest;
use App\Http\Requests\Parameters\UpdateTipoSessaoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Controller para gerenciamento de Tipos de Sessão
 * 
 * Este controller demonstra a implementação dos novos padrões
 * propostos para o sistema de parametrização SGVP.
 * 
 * @package App\Http\Controllers\Parameters
 * @version 2.0.0
 * @author Equipe SGVP
 * @since 2024-01-15
 */
class TipoSessaoController extends Controller
{
    protected ParameterService $parameterService;
    protected string $cachePrefix = 'tipo_sessao';
    protected int $cacheDuration = 3600; // 1 hora
    protected string $endpoint = 'tipoSessao';
    protected string $routePrefix = 'parametro.tipo';
    protected string $viewPrefix = 'parametrizacao.tipo';

    public function __construct(ParameterService $parameterService)
    {
        $this->middleware('auth.token');
        $this->parameterService = $parameterService;
    }

    /**
     * Lista todos os tipos de sessão
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            // Tenta buscar dados do cache primeiro
            $data = Cache::remember(
                "{$this->cachePrefix}.index",
                $this->cacheDuration,
                fn() => $this->parameterService->getAll($this->endpoint)
            );

            return view("{$this->viewPrefix}.index", [
                'title' => 'Tipos de Sessão',
                'data' => $data,
                'meta' => $this->generateMeta(),
                'breadcrumbs' => $this->getBreadcrumbs(),
                'endpoint' => $this->endpoint
            ]);

        } catch (Exception $e) {
            return $this->handleError($e, 'Erro ao carregar tipos de sessão.');
        }
    }

    /**
     * Exibe formulário de criação
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("{$this->viewPrefix}.create", [
            'title' => 'Criar Tipo de Sessão',
            'breadcrumbs' => $this->getBreadcrumbs('create'),
            'formData' => $this->getFormData()
        ]);
    }

    /**
     * Armazena novo tipo de sessão
     * 
     * @param StoreTipoSessaoRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTipoSessaoRequest $request)
    {
        try {
            $data = $this->prepareDataForStorage($request->validated());
            
            $result = $this->parameterService->create($this->endpoint, $data);
            
            // Limpa o cache após criação
            $this->clearCache();
            
            // Log da operação
            Log::info("Tipo de Sessão criado", [
                'user_id' => auth()->id() ?? 'system',
                'data' => $data,
                'result' => $result
            ]);
            
            return redirect()
                ->route("{$this->routePrefix}.index")
                ->with('success', 'Tipo de Sessão criado com sucesso.');

        } catch (Exception $e) {
            return $this->handleError($e, 'Erro ao criar Tipo de Sessão.');
        }
    }

    /**
     * Exibe formulário de edição
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request)
    {
        try {
            $id = $request->query('nrSequence') ?? $request->query('id');
            
            if (!$id) {
                return redirect()
                    ->route("{$this->routePrefix}.index")
                    ->withErrors('ID do registro é obrigatório.');
            }

            $record = $this->parameterService->findById($this->endpoint, $id);

            if (!$record) {
                return redirect()
                    ->route("{$this->routePrefix}.index")
                    ->withErrors('Tipo de Sessão não encontrado.');
            }

            return view("{$this->viewPrefix}.edit", [
                'title' => 'Editar Tipo de Sessão',
                'record' => $record,
                'breadcrumbs' => $this->getBreadcrumbs('edit', $record),
                'formData' => $this->getFormData($record)
            ]);

        } catch (Exception $e) {
            return $this->handleError($e, 'Erro ao carregar dados para edição.');
        }
    }

    /**
     * Atualiza tipo de sessão existente
     * 
     * @param UpdateTipoSessaoRequest $request
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateTipoSessaoRequest $request, $id)
    {
        try {
            $data = $this->prepareDataForUpdate($request->validated());
            
            $result = $this->parameterService->update($this->endpoint, $id, $data);
            
            // Limpa o cache após atualização
            $this->clearCache();
            
            // Log da operação
            Log::info("Tipo de Sessão atualizado", [
                'user_id' => auth()->id() ?? 'system',
                'record_id' => $id,
                'data' => $data,
                'result' => $result
            ]);
            
            return redirect()
                ->route("{$this->routePrefix}.index")
                ->with('success', 'Tipo de Sessão atualizado com sucesso.');

        } catch (Exception $e) {
            return $this->handleError($e, 'Erro ao atualizar Tipo de Sessão.');
        }
    }

    /**
     * Remove tipo de sessão
     * 
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Verifica se o registro pode ser excluído
            if (!$this->canDelete($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este tipo de sessão não pode ser excluído pois está em uso.'
                ], 422);
            }

            $this->parameterService->delete($this->endpoint, $id);
            
            // Limpa o cache após exclusão
            $this->clearCache();
            
            // Log da operação
            Log::info("Tipo de Sessão excluído", [
                'user_id' => auth()->id() ?? 'system',
                'record_id' => $id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Tipo de Sessão excluído com sucesso.'
            ]);

        } catch (Exception $e) {
            Log::error("Erro ao excluir Tipo de Sessão: " . $e->getMessage(), [
                'record_id' => $id,
                'error' => $e->getTrace()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir Tipo de Sessão.'
            ], 500);
        }
    }

    /**
     * Prepara dados para criação
     * 
     * @param array $validatedData
     * @return array
     */
    protected function prepareDataForStorage(array $validatedData): array
    {
        return [
            'tipoSessao' => $validatedData['dto']['tipoSessao'] ?? $validatedData['tipoSessao'],
            'ativo' => $validatedData['dto']['ativo'] ?? $validatedData['ativo'] ?? true,
        ];
    }

    /**
     * Prepara dados para atualização
     * 
     * @param array $validatedData
     * @return array
     */
    protected function prepareDataForUpdate(array $validatedData): array
    {
        return $this->prepareDataForStorage($validatedData);
    }

    /**
     * Gera meta tags para SEO
     * 
     * @return array
     */
    protected function generateMeta(): array
    {
        return [
            'description' => 'Gerenciamento de tipos de sessão do sistema SGVP',
            'og:title' => 'Tipos de Sessão - SGVP',
            'og:description' => 'Configure e gerencie os tipos de sessão utilizados no sistema',
            'robots' => 'noindex, nofollow' // Admin area
        ];
    }

    /**
     * Gera breadcrumbs para navegação
     * 
     * @param string|null $action
     * @param array|null $record
     * @return array
     */
    protected function getBreadcrumbs(?string $action = null, ?array $record = null): array
    {
        $breadcrumbs = [
            ['title' => 'Home', 'url' => route('admin.home')],
            ['title' => 'Parametrização', 'url' => route('parametro')],
            ['title' => 'Tipos de Sessão', 'url' => route("{$this->routePrefix}.index")]
        ];

        if ($action === 'create') {
            $breadcrumbs[] = ['title' => 'Criar', 'url' => null];
        } elseif ($action === 'edit' && $record) {
            $breadcrumbs[] = ['title' => 'Editar: ' . ($record['tipoSessao'] ?? 'N/A'), 'url' => null];
        }

        return $breadcrumbs;
    }

    /**
     * Obtém dados auxiliares para formulários
     * 
     * @param array|null $record
     * @return array
     */
    protected function getFormData(?array $record = null): array
    {
        return [
            'statusOptions' => [
                1 => 'Ativo',
                0 => 'Inativo'
            ],
            'isEditing' => $record !== null,
            'record' => $record
        ];
    }

    /**
     * Verifica se um registro pode ser excluído
     * 
     * @param mixed $id
     * @return bool
     */
    protected function canDelete($id): bool
    {
        try {
            // Implementar verificação de dependências
            // Por exemplo: verificar se existem sessões usando este tipo
            
            // Por enquanto, permite todas as exclusões
            return true;
            
        } catch (Exception $e) {
            Log::error("Erro ao verificar se pode excluir: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Limpa o cache relacionado a tipos de sessão
     * 
     * @return void
     */
    protected function clearCache(): void
    {
        Cache::forget("{$this->cachePrefix}.index");
        Cache::tags([$this->cachePrefix])->flush();
    }

    /**
     * Manipula erros de forma consistente
     * 
     * @param Exception $e
     * @param string $userMessage
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleError(Exception $e, string $userMessage)
    {
        Log::error($userMessage . ': ' . $e->getMessage(), [
            'exception' => $e,
            'user_id' => auth()->id() ?? 'system',
            'route' => request()->route()?->getName(),
            'url' => request()->fullUrl()
        ]);

        return back()
            ->withInput()
            ->withErrors($userMessage);
    }

    /**
     * Obtém token de autenticação
     * 
     * @return string|null
     */
    protected function getToken(): ?string
    {
        return $this->parameterService->getAuthToken();
    }
} 