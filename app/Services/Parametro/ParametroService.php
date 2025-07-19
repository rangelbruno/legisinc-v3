<?php

namespace App\Services\Parametro;

use App\Models\Parametro\ParametroModulo;
use App\Models\Parametro\ParametroSubmodulo;
use App\Models\Parametro\ParametroCampo;
use App\Models\Parametro\ParametroValor;
use App\Services\Parametro\ValidacaoParametroService;
use App\Services\Parametro\CacheParametroService;
use App\Services\Parametro\AuditoriaParametroService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ParametroService
{
    protected ValidacaoParametroService $validacaoService;
    protected CacheParametroService $cacheService;
    protected AuditoriaParametroService $auditoriaService;
    protected int $cacheTtl = 3600; // 1 hora

    public function __construct(
        ValidacaoParametroService $validacaoService,
        CacheParametroService $cacheService,
        AuditoriaParametroService $auditoriaService
    ) {
        $this->validacaoService = $validacaoService;
        $this->cacheService = $cacheService;
        $this->auditoriaService = $auditoriaService;
    }

    /**
     * Obtém todos os módulos ativos
     */
    public function obterModulos(): Collection
    {
        return $this->cacheService->rememberModulos(function () {
            return ParametroModulo::ativos()->ordenados()->get();
        }, $this->cacheTtl);
    }

    /**
     * Obtém submódulos de um módulo específico
     */
    public function obterSubmodulos(int $moduloId): Collection
    {
        return $this->cacheService->rememberSubmodulos($moduloId, function () use ($moduloId) {
            return ParametroSubmodulo::porModulo($moduloId)
                ->ativos()
                ->ordenados()
                ->with(['modulo', 'campos'])
                ->get();
        }, $this->cacheTtl);
    }

    /**
     * Obtém campos de um submódulo específico
     */
    public function obterCampos(int $submoduloId): Collection
    {
        return $this->cacheService->rememberCampos($submoduloId, function () use ($submoduloId) {
            return ParametroCampo::porSubmodulo($submoduloId)
                ->ativos()
                ->ordenados()
                ->with(['submodulo.modulo', 'valores'])
                ->get();
        }, $this->cacheTtl);
    }

    /**
     * Obtém configurações de um módulo/submódulo específico
     */
    public function obterConfiguracoes(string $nomeModulo, string $nomeSubmodulo): array
    {
        return $this->cacheService->rememberConfiguracoes($nomeModulo, $nomeSubmodulo, function () use ($nomeModulo, $nomeSubmodulo) {
            $modulo = ParametroModulo::where('nome', $nomeModulo)->ativos()->first();
            
            if (!$modulo) {
                return [];
            }

            $submodulo = $modulo->submodulos()
                ->where('nome', $nomeSubmodulo)
                ->ativos()
                ->first();
                
            if (!$submodulo) {
                return [];
            }

            $campos = $this->obterCampos($submodulo->id);
            $configuracoes = [];

            foreach ($campos as $campo) {
                $configuracoes[$campo->nome] = [
                    'label' => $campo->label,
                    'tipo' => $campo->tipo_campo,
                    'valor' => $campo->valor_atual,
                    'obrigatorio' => $campo->obrigatorio,
                    'validacao' => $campo->getValidationRules(),
                    'opcoes' => $campo->opcoes_formatada,
                    'placeholder' => $campo->placeholder,
                    'classe_css' => $campo->classe_css,
                    'descricao' => $campo->descricao,
                ];
            }

            return $configuracoes;
        }, $this->cacheTtl);
    }

    /**
     * Valida um parâmetro específico
     */
    public function validar(string $nomeModulo, string $nomeSubmodulo, mixed $valor): bool
    {
        try {
            return $this->validacaoService->validar($nomeModulo, $nomeSubmodulo, $valor);
        } catch (\Exception $e) {
            Log::error("Erro na validação de parâmetro: {$e->getMessage()}", [
                'modulo' => $nomeModulo,
                'submodulo' => $nomeSubmodulo,
                'valor' => $valor
            ]);
            return false;
        }
    }

    /**
     * Salva valores de parâmetros
     */
    public function salvarValores(int $submoduloId, array $valores, int $userId = null): bool
    {
        try {
            $campos = $this->obterCampos($submoduloId);
            
            foreach ($campos as $campo) {
                if (!isset($valores[$campo->nome])) {
                    continue;
                }

                $valor = $valores[$campo->nome];

                // Validar o valor
                if (!$this->validarCampo($campo, $valor)) {
                    return false;
                }

                // Expirar valores antigos
                $valoresAntigos = $campo->valores()->validos()->get();
                foreach ($valoresAntigos as $valorAntigo) {
                    $this->auditoriaService->registrarExpiracaoValor($valorAntigo);
                }
                $campo->valores()->validos()->update(['valido_ate' => now()]);

                // Criar novo valor
                $novoValor = new ParametroValor();
                $novoValor->campo_id = $campo->id;
                $novoValor->defineValor($valor, $this->determinarTipoValor($valor));
                $novoValor->user_id = $userId;
                $novoValor->save();

                // Registrar auditoria do novo valor
                $this->auditoriaService->registrarCriacaoValor($novoValor);
            }

            // Limpar cache relacionado
            $this->invalidarCacheSubmodulo($submoduloId);

            return true;
        } catch (\Exception $e) {
            Log::error("Erro ao salvar valores de parâmetros: {$e->getMessage()}", [
                'submodulo_id' => $submoduloId,
                'valores' => $valores
            ]);
            return false;
        }
    }

    /**
     * Obtém valor de um parâmetro específico
     */
    public function obterValor(string $nomeModulo, string $nomeSubmodulo, string $nomeCampo): mixed
    {
        $configuracoes = $this->obterConfiguracoes($nomeModulo, $nomeSubmodulo);
        
        return $configuracoes[$nomeCampo]['valor'] ?? null;
    }

    /**
     * Cria um novo módulo
     */
    public function criarModulo(array $dados): ParametroModulo
    {
        $modulo = new ParametroModulo();
        $modulo->fill($dados);
        $modulo->ordem = $modulo->getProximaOrdem();
        $modulo->save();

        // Registrar auditoria
        $this->auditoriaService->registrarCriacaoModulo($modulo);

        $this->cacheService->invalidateModulos();

        return $modulo;
    }

    /**
     * Cria um novo submódulo
     */
    public function criarSubmodulo(array $dados): ParametroSubmodulo
    {
        $submodulo = new ParametroSubmodulo();
        $submodulo->fill($dados);
        $submodulo->ordem = $submodulo->getProximaOrdem();
        $submodulo->save();

        // Registrar auditoria
        $this->auditoriaService->registrarCriacaoSubmodulo($submodulo);

        $this->cacheService->invalidateSubmodulos($submodulo->modulo_id);

        return $submodulo;
    }

    /**
     * Cria um novo campo
     */
    public function criarCampo(array $dados): ParametroCampo
    {
        $campo = new ParametroCampo();
        $campo->fill($dados);
        $campo->ordem = $campo->getProximaOrdem();
        $campo->save();

        // Registrar auditoria
        $this->auditoriaService->registrarCriacaoCampo($campo);

        $this->cacheService->invalidateCampos($campo->submodulo_id);

        return $campo;
    }

    /**
     * Valida um campo específico
     */
    protected function validarCampo(ParametroCampo $campo, mixed $valor): bool
    {
        $rules = $campo->getValidationRules();
        
        if (empty($rules)) {
            return true;
        }

        try {
            $validator = \Validator::make([$campo->nome => $valor], [$campo->nome => $rules]);
            return !$validator->fails();
        } catch (\Exception $e) {
            Log::error("Erro na validação de campo: {$e->getMessage()}", [
                'campo' => $campo->nome,
                'valor' => $valor
            ]);
            return false;
        }
    }

    /**
     * Determina o tipo de valor baseado no conteúdo
     */
    protected function determinarTipoValor(mixed $valor): string
    {
        if (is_bool($valor)) {
            return 'boolean';
        }
        
        if (is_int($valor)) {
            return 'integer';
        }
        
        if (is_float($valor)) {
            return 'decimal';
        }
        
        if (is_array($valor)) {
            return 'json';
        }
        
        if (is_string($valor) && strtotime($valor) !== false) {
            return strlen($valor) > 10 ? 'datetime' : 'date';
        }
        
        return 'string';
    }

    /**
     * Invalida cache relacionado a um submódulo
     */
    protected function invalidarCacheSubmodulo(int $submoduloId): void
    {
        $submodulo = ParametroSubmodulo::find($submoduloId);
        
        if ($submodulo) {
            $this->cacheService->invalidateCampos($submoduloId);
            $this->cacheService->invalidateConfiguracoes($submodulo->modulo->nome, $submodulo->nome);
            $this->cacheService->invalidateSubmodulos($submodulo->modulo_id);
            $this->cacheService->invalidateValores($submodulo->modulo->nome, $submodulo->nome);
        }
    }

    /**
     * Exclui um módulo e todas suas dependências
     */
    public function excluirModulo(int $moduloId, bool $force = false): bool
    {
        try {
            $modulo = ParametroModulo::findOrFail($moduloId);
            
            // Verificar se possui submódulos (apenas se não for exclusão forçada)
            $totalSubmodulos = $modulo->submodulos()->count();
            if ($totalSubmodulos > 0 && !$force) {
                Log::warning("Tentativa de exclusão de módulo com submódulos", [
                    'modulo_id' => $moduloId,
                    'total_submodulos' => $totalSubmodulos
                ]);
                throw new \Exception("Não é possível excluir o módulo pois possui {$totalSubmodulos} submódulo(s) vinculado(s).");
            }

            // Log da exclusão antes de executar
            Log::info("Excluindo módulo de parâmetro", [
                'modulo_id' => $moduloId,
                'nome' => $modulo->nome,
                'user_id' => auth()->id(),
                'force' => $force,
                'total_submodulos' => $totalSubmodulos
            ]);

            // Se for exclusão forçada e há submódulos, excluir em cascata
            if ($force && $totalSubmodulos > 0) {
                Log::info("Executando exclusão forçada - removendo submódulos", [
                    'modulo_id' => $moduloId,
                    'total_submodulos' => $totalSubmodulos
                ]);
                
                // Excluir todos os submódulos e suas dependências
                foreach ($modulo->submodulos as $submodulo) {
                    // Excluir valores dos campos do submódulo
                    foreach ($submodulo->campos as $campo) {
                        $campo->valores()->delete();
                    }
                    // Excluir campos do submódulo
                    $submodulo->campos()->delete();
                    // Excluir o submódulo
                    $submodulo->delete();
                }
            }

            // Registrar auditoria antes da exclusão
            $this->auditoriaService->registrarExclusaoModulo($modulo);

            // Excluir o módulo
            $modulo->delete();

            // Limpar cache relacionado
            $this->cacheService->invalidateModulos();

            return true;

        } catch (\Exception $e) {
            Log::error("Erro ao excluir módulo de parâmetro", [
                'modulo_id' => $moduloId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            throw $e;
        }
    }

    /**
     * Exclui um submódulo e todas suas dependências
     */
    public function excluirSubmodulo(int $submoduloId): bool
    {
        try {
            $submodulo = ParametroSubmodulo::findOrFail($submoduloId);
            
            // Verificar se possui campos
            $totalCampos = $submodulo->campos()->count();
            if ($totalCampos > 0) {
                Log::warning("Tentativa de exclusão de submódulo com campos", [
                    'submodulo_id' => $submoduloId,
                    'total_campos' => $totalCampos
                ]);
                throw new \Exception("Não é possível excluir o submódulo pois possui {$totalCampos} campo(s) vinculado(s).");
            }

            // Log da exclusão
            Log::info("Excluindo submódulo de parâmetro", [
                'submodulo_id' => $submoduloId,
                'nome' => $submodulo->nome,
                'modulo_id' => $submodulo->modulo_id,
                'user_id' => auth()->id()
            ]);

            $moduloId = $submodulo->modulo_id;

            // Registrar auditoria antes da exclusão
            $this->auditoriaService->registrarExclusaoSubmodulo($submodulo);

            // Excluir o submódulo
            $submodulo->delete();

            // Limpar cache relacionado
            $this->cacheService->invalidateSubmodulos($moduloId);

            return true;

        } catch (\Exception $e) {
            Log::error("Erro ao excluir submódulo de parâmetro", [
                'submodulo_id' => $submoduloId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            throw $e;
        }
    }

    /**
     * Exclui um campo e todos seus valores
     */
    public function excluirCampo(int $campoId): bool
    {
        try {
            $campo = ParametroCampo::findOrFail($campoId);
            
            // Verificar se possui valores ativos
            $totalValores = $campo->valores()->validos()->count();
            
            // Log da exclusão
            Log::info("Excluindo campo de parâmetro", [
                'campo_id' => $campoId,
                'nome' => $campo->nome,
                'submodulo_id' => $campo->submodulo_id,
                'total_valores' => $totalValores,
                'user_id' => auth()->id()
            ]);

            $submoduloId = $campo->submodulo_id;

            // Primeiro, expirar todos os valores relacionados
            $valores = $campo->valores()->validos()->get();
            foreach ($valores as $valor) {
                $this->auditoriaService->registrarExpiracaoValor($valor);
            }
            $campo->valores()->update(['valido_ate' => now()]);

            // Registrar auditoria antes da exclusão
            $this->auditoriaService->registrarExclusaoCampo($campo);

            // Depois excluir o campo
            $campo->delete();

            // Limpar cache relacionado
            $this->cacheService->invalidateCampos($submoduloId);

            return true;

        } catch (\Exception $e) {
            Log::error("Erro ao excluir campo de parâmetro", [
                'campo_id' => $campoId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            throw $e;
        }
    }

    /**
     * Verifica se um módulo pode ser excluído
     */
    public function podeExcluirModulo(int $moduloId): array
    {
        $modulo = ParametroModulo::find($moduloId);
        
        if (!$modulo) {
            return ['pode' => false, 'motivo' => 'Módulo não encontrado'];
        }

        $totalSubmodulos = $modulo->submodulos()->count();
        
        if ($totalSubmodulos > 0) {
            return [
                'pode' => false, 
                'motivo' => "Possui {$totalSubmodulos} submódulo(s) vinculado(s)"
            ];
        }

        return ['pode' => true, 'motivo' => ''];
    }

    /**
     * Verifica se um submódulo pode ser excluído
     */
    public function podeExcluirSubmodulo(int $submoduloId): array
    {
        $submodulo = ParametroSubmodulo::find($submoduloId);
        
        if (!$submodulo) {
            return ['pode' => false, 'motivo' => 'Submódulo não encontrado'];
        }

        $totalCampos = $submodulo->campos()->count();
        
        if ($totalCampos > 0) {
            return [
                'pode' => false, 
                'motivo' => "Possui {$totalCampos} campo(s) vinculado(s)"
            ];
        }

        return ['pode' => true, 'motivo' => ''];
    }

    /**
     * Limpa todo o cache de parâmetros
     */
    public function limparTodoCache(): void
    {
        $this->cacheService->invalidateAll();
    }

    /**
     * Obtém estatísticas do cache
     */
    public function obterEstatisticasCache(): array
    {
        return $this->cacheService->getCacheStats();
    }

    /**
     * Aquece o cache
     */
    public function aquecerCache(): void
    {
        $this->cacheService->warmUpCache();
    }

    /**
     * Verifica saúde do cache
     */
    public function verificarSaudeCache(): array
    {
        return $this->cacheService->healthCheck();
    }

    /**
     * Obtém histórico de auditoria de uma entidade
     */
    public function obterHistoricoAuditoria(string $entidade, int $entidadeId, int $limite = 50): array
    {
        return $this->auditoriaService->obterHistorico($entidade, $entidadeId, $limite);
    }

    /**
     * Obtém relatório de atividades
     */
    public function obterRelatorioAtividades(\Carbon\Carbon $dataInicio, \Carbon\Carbon $dataFim): array
    {
        return $this->auditoriaService->obterRelatorioAtividades($dataInicio, $dataFim);
    }

    /**
     * Obtém estatísticas de uso do sistema
     */
    public function obterEstatisticasUso(): array
    {
        return $this->auditoriaService->obterEstatisticasUso();
    }

    /**
     * Limpa registros antigos de auditoria
     */
    public function limparAuditoriaAntica(int $diasParaManter = 365): int
    {
        return $this->auditoriaService->limparRegistrosAntigos($diasParaManter);
    }

    /**
     * Exporta dados de auditoria
     */
    public function exportarDadosAuditoria(\Carbon\Carbon $dataInicio, \Carbon\Carbon $dataFim, string $formato = 'json'): string
    {
        return $this->auditoriaService->exportarDados($dataInicio, $dataFim, $formato);
    }

    /**
     * Verifica integridade dos dados de auditoria
     */
    public function verificarIntegridadeAuditoria(): array
    {
        return $this->auditoriaService->verificarIntegridade();
    }
}