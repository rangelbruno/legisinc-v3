<?php

namespace App\Services\Admin;

use App\Models\Parametro;
use App\Models\GrupoParametro;
use App\Models\TipoParametro;
use App\Models\HistoricoParametro;
use App\DTOs\Admin\ParametroDTO;
use App\Services\Admin\CacheParametroService;
use App\Services\Admin\ValidacaoParametroService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ParametroService
{
    public function __construct(
        private readonly CacheParametroService $cacheService,
        private readonly ValidacaoParametroService $validacaoService
    ) {
    }

    /**
     * Listar parâmetros com filtros
     */
    public function listarParametros(array $filtros = []): Collection
    {
        $query = Parametro::with(['grupoParametro', 'tipoParametro']);

        // Filtro por busca
        if (!empty($filtros['search'])) {
            $query->busca($filtros['search']);
        }

        // Filtro por grupo
        if (!empty($filtros['grupo_parametro_id'])) {
            $query->porGrupo($filtros['grupo_parametro_id']);
        }

        // Filtro por tipo
        if (!empty($filtros['tipo_parametro_id'])) {
            $query->porTipo($filtros['tipo_parametro_id']);
        }

        // Filtro por status
        if (isset($filtros['ativo'])) {
            $query->where('ativo', $filtros['ativo']);
        }

        // Ordenação
        $query->ordenados();

        // Paginação ou todos
        if (isset($filtros['paginar']) && $filtros['paginar']) {
            return $query->paginate($filtros['por_pagina'] ?? 20);
        }

        return $query->get();
    }

    /**
     * Obter parâmetro por ID
     */
    public function obterParametroPorId(int $id): ParametroDTO
    {
        $parametro = Parametro::with(['grupoParametro', 'tipoParametro'])
            ->findOrFail($id);

        return ParametroDTO::fromModel($parametro);
    }

    /**
     * Obter parâmetro por código
     */
    public function obterParametroPorCodigo(string $codigo): ParametroDTO
    {
        $parametro = Parametro::with(['grupoParametro', 'tipoParametro'])
            ->where('codigo', $codigo)
            ->firstOrFail();

        return ParametroDTO::fromModel($parametro);
    }

    /**
     * Criar novo parâmetro
     */
    public function criarParametro(array $dados): ParametroDTO
    {
        return DB::transaction(function () use ($dados) {
            // Validar dados
            $this->validarDadosParametro($dados);

            // Definir ordem se não especificada
            if (!isset($dados['ordem'])) {
                $dados['ordem'] = $this->obterProximaOrdem($dados['grupo_parametro_id']);
            }

            // Criar parâmetro
            $parametro = Parametro::create($dados);

            // Limpar cache
            $this->cacheService->limparCacheParametro($parametro->codigo);
            $this->cacheService->limparCacheGrupo($parametro->grupoParametro->codigo);

            return ParametroDTO::fromModel($parametro->fresh(['grupoParametro', 'tipoParametro']));
        });
    }

    /**
     * Atualizar parâmetro
     */
    public function atualizarParametro(int $id, array $dados): ParametroDTO
    {
        return DB::transaction(function () use ($id, $dados) {
            $parametro = Parametro::findOrFail($id);

            // Validar dados
            $this->validarDadosParametro($dados, $id);

            // Atualizar parâmetro
            $parametro->update($dados);

            // Limpar cache
            $this->cacheService->limparCacheParametro($parametro->codigo);
            $this->cacheService->limparCacheGrupo($parametro->grupoParametro->codigo);

            return ParametroDTO::fromModel($parametro->fresh(['grupoParametro', 'tipoParametro']));
        });
    }

    /**
     * Excluir parâmetro
     */
    public function excluirParametro(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $parametro = Parametro::findOrFail($id);

            // Limpar cache antes de excluir
            $this->cacheService->limparCacheParametro($parametro->codigo);
            $this->cacheService->limparCacheGrupo($parametro->grupoParametro->codigo);

            return $parametro->delete();
        });
    }

    /**
     * Obter parâmetros por grupo
     */
    public function obterParametrosPorGrupo(int $grupoId): Collection
    {
        return Parametro::with(['tipoParametro'])
            ->porGrupo($grupoId)
            ->ativos()
            ->visiveis()
            ->ordenados()
            ->get()
            ->map(fn($parametro) => ParametroDTO::fromModel($parametro));
    }

    /**
     * Atualizar múltiplos parâmetros
     */
    public function atualizarMultiplosParametros(array $parametros): array
    {
        $atualizados = [];
        $erros = [];

        DB::transaction(function () use ($parametros, &$atualizados, &$erros) {
            foreach ($parametros as $codigo => $valor) {
                try {
                    $parametro = Parametro::where('codigo', $codigo)
                        ->where('ativo', true)
                        ->where('editavel', true)
                        ->firstOrFail();

                    // Validar valor
                    $validacao = $this->validacaoService->validarValor(
                        $parametro->tipoParametro,
                        $valor,
                        $parametro->regras_validacao ?? []
                    );

                    if (!$validacao['valido']) {
                        $erros[$codigo] = $validacao['erros'];
                        continue;
                    }

                    // Atualizar valor
                    $parametro->update(['valor' => $valor]);

                    // Limpar cache
                    $this->cacheService->limparCacheParametro($parametro->codigo);

                    $atualizados[] = $codigo;

                } catch (\Exception $e) {
                    $erros[$codigo] = [$e->getMessage()];
                }
            }
        });

        return compact('atualizados', 'erros');
    }

    /**
     * Atualizar parâmetros por grupo
     */
    public function atualizarParametrosPorGrupo(int $grupoId, array $parametros): array
    {
        $atualizados = [];
        $erros = [];

        DB::transaction(function () use ($grupoId, $parametros, &$atualizados, &$erros) {
            foreach ($parametros as $codigo => $valor) {
                try {
                    $parametro = Parametro::where('codigo', $codigo)
                        ->where('grupo_parametro_id', $grupoId)
                        ->where('ativo', true)
                        ->where('editavel', true)
                        ->firstOrFail();

                    // Validar valor
                    $validacao = $this->validacaoService->validarValor(
                        $parametro->tipoParametro,
                        $valor,
                        $parametro->regras_validacao ?? []
                    );

                    if (!$validacao['valido']) {
                        $erros[$codigo] = $validacao['erros'];
                        continue;
                    }

                    // Atualizar valor
                    $parametro->update(['valor' => $valor]);

                    $atualizados[] = $codigo;

                } catch (\Exception $e) {
                    $erros[$codigo] = [$e->getMessage()];
                }
            }

            // Limpar cache do grupo
            if (!empty($atualizados)) {
                $grupo = GrupoParametro::find($grupoId);
                if ($grupo) {
                    $this->cacheService->limparCacheGrupo($grupo->codigo);
                }
            }
        });

        return compact('atualizados', 'erros');
    }

    /**
     * Atualizar valor de um parâmetro específico
     */
    public function atualizarValorParametro(int $id, string $valor): array
    {
        return DB::transaction(function () use ($id, $valor) {
            $parametro = Parametro::findOrFail($id);

            // Validar valor
            $validacao = $this->validacaoService->validarValor(
                $parametro->tipoParametro,
                $valor,
                $parametro->regras_validacao ?? []
            );

            if (!$validacao['valido']) {
                throw new \Exception('Valor inválido: ' . implode(', ', $validacao['erros']));
            }

            // Atualizar valor
            $parametro->update(['valor' => $valor]);

            // Limpar cache
            $this->cacheService->limparCacheParametro($parametro->codigo);

            return [
                'valor' => $parametro->valor,
                'valor_formatado' => $parametro->valor_formatado
            ];
        });
    }

    /**
     * Duplicar parâmetro
     */
    public function duplicarParametro(int $id): ParametroDTO
    {
        return DB::transaction(function () use ($id) {
            $parametroOriginal = Parametro::findOrFail($id);

            // Criar novo código único
            $novosCodigos = $this->gerarCodigoUnico($parametroOriginal->codigo);

            // Dados para duplicação
            $dados = $parametroOriginal->toArray();
            unset($dados['id'], $dados['created_at'], $dados['updated_at']);
            $dados['codigo'] = $novosCodigos;
            $dados['nome'] = $parametroOriginal->nome . ' (Cópia)';
            $dados['ordem'] = $this->obterProximaOrdem($parametroOriginal->grupo_parametro_id);

            // Criar parâmetro duplicado
            $parametro = Parametro::create($dados);

            return ParametroDTO::fromModel($parametro->fresh(['grupoParametro', 'tipoParametro']));
        });
    }

    /**
     * Resetar parâmetro para valor padrão
     */
    public function resetarParametroParaPadrao(int $id): ParametroDTO
    {
        return DB::transaction(function () use ($id) {
            $parametro = Parametro::findOrFail($id);

            $parametro->update(['valor' => $parametro->valor_padrao]);

            // Limpar cache
            $this->cacheService->limparCacheParametro($parametro->codigo);

            return ParametroDTO::fromModel($parametro->fresh(['grupoParametro', 'tipoParametro']));
        });
    }

    /**
     * Alterar status do parâmetro
     */
    public function alterarStatusParametro(int $id): ParametroDTO
    {
        return DB::transaction(function () use ($id) {
            $parametro = Parametro::findOrFail($id);

            $parametro->update(['ativo' => !$parametro->ativo]);

            // Limpar cache
            $this->cacheService->limparCacheParametro($parametro->codigo);

            return ParametroDTO::fromModel($parametro->fresh(['grupoParametro', 'tipoParametro']));
        });
    }

    /**
     * Reordenar parâmetros
     */
    public function reordenarParametros(array $parametros): void
    {
        DB::transaction(function () use ($parametros) {
            foreach ($parametros as $ordem => $parametroId) {
                Parametro::where('id', $parametroId)
                    ->update(['ordem' => $ordem + 1]);
            }
        });
    }

    /**
     * Obter histórico de um parâmetro
     */
    public function obterHistoricoParametro(int $id): Collection
    {
        return HistoricoParametro::with(['user'])
            ->porParametro($id)
            ->ordenados()
            ->get();
    }

    /**
     * Validar valor do parâmetro
     */
    public function validarValorParametro(int $tipoParametroId, string $valor, array $regrasAdicionais = []): array
    {
        $tipo = TipoParametro::findOrFail($tipoParametroId);

        return $this->validacaoService->validarValor($tipo, $valor, $regrasAdicionais);
    }

    /**
     * Obter estatísticas dos parâmetros
     */
    public function obterEstatisticas(): array
    {
        $total = Parametro::count();
        $ativos = Parametro::ativos()->count();
        $obrigatorios = Parametro::obrigatorios()->count();
        $editaveis = Parametro::editaveis()->count();
        $visiveis = Parametro::visiveis()->count();

        $porGrupo = Parametro::selectRaw('grupo_parametro_id, count(*) as total')
            ->with(['grupoParametro:id,nome,codigo'])
            ->groupBy('grupo_parametro_id')
            ->get()
            ->map(function ($item) {
                return [
                    'grupo' => $item->grupoParametro->nome,
                    'codigo' => $item->grupoParametro->codigo,
                    'total' => $item->total
                ];
            });

        $porTipo = Parametro::selectRaw('tipo_parametro_id, count(*) as total')
            ->with(['tipoParametro:id,nome,codigo'])
            ->groupBy('tipo_parametro_id')
            ->get()
            ->map(function ($item) {
                return [
                    'tipo' => $item->tipoParametro->nome,
                    'codigo' => $item->tipoParametro->codigo,
                    'total' => $item->total
                ];
            });

        $semValor = Parametro::whereNull('valor')
            ->orWhere('valor', '')
            ->count();

        return compact(
            'total',
            'ativos',
            'obrigatorios',
            'editaveis',
            'visiveis',
            'porGrupo',
            'porTipo',
            'semValor'
        );
    }

    /**
     * Exportar parâmetros para backup
     */
    public function exportarParametros(?int $grupoId = null): array
    {
        $query = Parametro::with(['grupoParametro', 'tipoParametro']);

        if ($grupoId) {
            $query->porGrupo($grupoId);
        }

        $parametros = $query->get();

        $backup = [
            'version' => '1.0',
            'exported_at' => now()->toISOString(),
            'exported_by' => Auth::id(),
            'total_parameters' => $parametros->count(),
            'grupo_id' => $grupoId,
            'parametros' => $parametros->map(function ($parametro) {
                return [
                    'nome' => $parametro->nome,
                    'codigo' => $parametro->codigo,
                    'descricao' => $parametro->descricao,
                    'grupo_codigo' => $parametro->grupoParametro->codigo,
                    'tipo_codigo' => $parametro->tipoParametro->codigo,
                    'valor' => $parametro->valor,
                    'valor_padrao' => $parametro->valor_padrao,
                    'configuracao' => $parametro->configuracao,
                    'regras_validacao' => $parametro->regras_validacao,
                    'obrigatorio' => $parametro->obrigatorio,
                    'editavel' => $parametro->editavel,
                    'visivel' => $parametro->visivel,
                    'ativo' => $parametro->ativo,
                    'ordem' => $parametro->ordem,
                    'help_text' => $parametro->help_text
                ];
            })
        ];

        return $backup;
    }

    /**
     * Importar parâmetros de backup
     */
    public function importarParametros(array $backupData): array
    {
        $importados = [];
        $erros = [];

        DB::transaction(function () use ($backupData, &$importados, &$erros) {
            foreach ($backupData['parametros'] as $dadosParametro) {
                try {
                    // Obter grupo e tipo
                    $grupo = GrupoParametro::where('codigo', $dadosParametro['grupo_codigo'])->first();
                    $tipo = TipoParametro::where('codigo', $dadosParametro['tipo_codigo'])->first();

                    if (!$grupo || !$tipo) {
                        $erros[] = "Grupo ou tipo não encontrado para: {$dadosParametro['codigo']}";
                        continue;
                    }

                    // Preparar dados
                    $dados = $dadosParametro;
                    unset($dados['grupo_codigo'], $dados['tipo_codigo']);
                    $dados['grupo_parametro_id'] = $grupo->id;
                    $dados['tipo_parametro_id'] = $tipo->id;

                    // Criar ou atualizar parâmetro
                    $parametro = Parametro::updateOrCreate(
                        ['codigo' => $dados['codigo']],
                        $dados
                    );

                    $importados[] = $parametro->codigo;

                } catch (\Exception $e) {
                    $erros[] = "Erro ao importar {$dadosParametro['codigo']}: {$e->getMessage()}";
                }
            }
        });

        // Limpar cache
        $this->cacheService->limparTodoCache();

        return compact('importados', 'erros');
    }

    /**
     * Validar dados do parâmetro
     */
    private function validarDadosParametro(array $dados, ?int $id = null): void
    {
        // Validar código único
        $query = Parametro::where('codigo', $dados['codigo']);
        if ($id) {
            $query->where('id', '!=', $id);
        }

        if ($query->exists()) {
            throw new \Exception('Código já existe: ' . $dados['codigo']);
        }

        // Validar grupo existe
        if (!GrupoParametro::where('id', $dados['grupo_parametro_id'])->exists()) {
            throw new \Exception('Grupo não encontrado');
        }

        // Validar tipo existe
        if (!TipoParametro::where('id', $dados['tipo_parametro_id'])->exists()) {
            throw new \Exception('Tipo não encontrado');
        }
    }

    /**
     * Obter próxima ordem para um grupo
     */
    private function obterProximaOrdem(int $grupoId): int
    {
        $ultimaOrdem = Parametro::where('grupo_parametro_id', $grupoId)
            ->max('ordem') ?? 0;

        return $ultimaOrdem + 1;
    }

    /**
     * Gerar código único baseado em outro código
     */
    private function gerarCodigoUnico(string $codigoBase): string
    {
        $contador = 1;
        $novoCodigo = $codigoBase . '_copia';

        while (Parametro::where('codigo', $novoCodigo)->exists()) {
            $contador++;
            $novoCodigo = $codigoBase . '_copia_' . $contador;
        }

        return $novoCodigo;
    }
}