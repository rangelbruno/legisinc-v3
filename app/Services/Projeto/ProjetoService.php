<?php

namespace App\Services\Projeto;

use App\Models\Projeto;
use App\Models\ProjetoVersion;
use App\Models\ProjetoAnexo;
use App\Models\ProjetoTramitacao;
use App\Models\User;
use App\DTOs\Projeto\ProjetoDTO;
use App\DTOs\Projeto\ProjetoVersionDTO;
use App\DTOs\Projeto\ProjetoAnexoDTO;
use App\DTOs\Projeto\ProjetoTramitacaoDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProjetoService
{
    /**
     * Listar projetos com filtros e paginação
     */
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Projeto::with(['autor', 'relator', 'versionAtual'])
            ->ativos()
            ->visiveisPorUsuario(auth()->user())
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if (!empty($filtros['titulo'])) {
            $query->where('titulo', 'like', '%' . $filtros['titulo'] . '%');
        }

        if (!empty($filtros['numero'])) {
            $query->where('numero', 'like', '%' . $filtros['numero'] . '%');
        }

        if (!empty($filtros['ano'])) {
            $query->where('ano', $filtros['ano']);
        }

        if (!empty($filtros['tipo'])) {
            $query->porTipo($filtros['tipo']);
        }

        if (!empty($filtros['status'])) {
            $query->porStatus($filtros['status']);
        }

        if (!empty($filtros['urgencia'])) {
            $query->where('urgencia', $filtros['urgencia']);
        }

        if (!empty($filtros['autor_id'])) {
            $query->porAutor($filtros['autor_id']);
        }

        if (!empty($filtros['relator_id'])) {
            $query->where('relator_id', $filtros['relator_id']);
        }

        if (!empty($filtros['comissao_id'])) {
            $query->porComissao($filtros['comissao_id']);
        }

        if (!empty($filtros['palavras_chave'])) {
            $query->where(function ($q) use ($filtros) {
                $q->where('titulo', 'like', '%' . $filtros['palavras_chave'] . '%')
                  ->orWhere('ementa', 'like', '%' . $filtros['palavras_chave'] . '%')
                  ->orWhere('palavras_chave', 'like', '%' . $filtros['palavras_chave'] . '%');
            });
        }

        if (isset($filtros['urgentes']) && $filtros['urgentes']) {
            $query->urgentes();
        }

        return $query->paginate($perPage);
    }

    /**
     * Obter projeto por ID
     */
    public function obterPorId(int $id): ?Projeto
    {
        return Projeto::with([
            'autor', 
            'relator', 
            'versions.author',
            'versionAtual',
            'anexos.uploadedBy',
            'tramitacao.responsavel',
            'tramitacaoAtual'
        ])->visiveisPorUsuario(auth()->user())
         ->find($id);
    }

    /**
     * Criar novo projeto
     */
    public function criar(ProjetoDTO $dto): Projeto
    {
        try {
            DB::beginTransaction();

            // Validar DTO (desabilitado temporariamente para debug)
            // if (!$dto->isValid()) {
            //     $errors = $dto->getValidationErrors();
            //     Log::error('Dados inválidos no DTO', [
            //         'errors' => $errors,
            //         'dto_data' => $dto->toArray()
            //     ]);
            //     throw new Exception('Dados inválidos: ' . implode(', ', $errors));
            // }

            // Gerar número automático se não fornecido (desabilitado temporariamente)
            // if (!$dto->numero) {
            //     $dto = ProjetoDTO::fromArray(array_merge($dto->toArray(), [
            //         'numero' => $this->gerarProximoNumero($dto->ano ?? date('Y'), $dto->tipo)
            //     ]));
            // }

            // Criar projeto
            $dadosParaCriar = $dto->withDefaults()->toCreateArray();
            Log::info('Dados para criar projeto', ['dados' => $dadosParaCriar]);
            $projeto = Projeto::create($dadosParaCriar);

            // Criar primeira versão se houver conteúdo (comentado temporariamente)
            // if ($dto->conteudo) {
            //     $this->criarVersao($projeto->id, ProjetoVersionDTO::fromArray([
            //         'projeto_id' => $projeto->id,
            //         'version_number' => 1,
            //         'conteudo' => $dto->conteudo,
            //         'changelog' => 'Versão inicial',
            //         'tipo_alteracao' => 'criacao',
            //         'author_id' => $dto->autorId,
            //         'is_current' => true,
            //     ]));
            // }

            // Criar tramitação inicial (comentado temporariamente)
            // $this->adicionarTramitacao($projeto->id, ProjetoTramitacaoDTO::criarProtocolo(
            //     $projeto->id, 
            //     $dto->autorId
            // ));

            DB::commit();

            Log::info('Projeto criado com sucesso', [
                'projeto_id' => $projeto->id,
                'titulo' => $projeto->titulo,
                'autor_id' => $projeto->autor_id
            ]);

            return $projeto->fresh();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar projeto', [
                'erro' => $e->getMessage(),
                'dto' => $dto->toArray()
            ]);
            throw $e;
        }
    }

    /**
     * Atualizar projeto
     */
    public function atualizar(int $id, ProjetoDTO $dto): Projeto
    {
        try {
            DB::beginTransaction();

            $projeto = Projeto::findOrFail($id);

            // Verificar permissões
            if (!$this->podeEditar($projeto)) {
                throw new Exception('Sem permissão para editar este projeto');
            }

            // Validar DTO
            if (!$dto->isValid()) {
                throw new Exception('Dados inválidos: ' . implode(', ', $dto->getValidationErrors()));
            }

            // Verificar se conteúdo mudou para criar nova versão
            $conteudoMudou = $dto->conteudo && $dto->conteudo !== $projeto->conteudo;

            // Atualizar projeto
            $projeto->update($dto->toUpdateArray());

            // Criar nova versão se conteúdo mudou
            if ($conteudoMudou) {
                $this->criarVersao($id, ProjetoVersionDTO::fromArray([
                    'projeto_id' => $id,
                    'version_number' => $projeto->version_atual + 1,
                    'conteudo' => $dto->conteudo,
                    'changelog' => 'Atualização do conteúdo',
                    'tipo_alteracao' => 'revisao',
                    'author_id' => auth()->id(),
                    'is_current' => true,
                ]));
            }

            DB::commit();

            Log::info('Projeto atualizado com sucesso', [
                'projeto_id' => $id,
                'conteudo_mudou' => $conteudoMudou
            ]);

            return $projeto->fresh();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar projeto', [
                'projeto_id' => $id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Excluir projeto (soft delete)
     */
    public function excluir(int $id): bool
    {
        try {
            $projeto = Projeto::findOrFail($id);

            // Verificar permissões
            if (!$this->podeExcluir($projeto)) {
                throw new Exception('Sem permissão para excluir este projeto');
            }

            // Verificar se pode ser excluído (apenas rascunhos)
            if (!$projeto->isRascunho()) {
                throw new Exception('Apenas projetos em rascunho podem ser excluídos');
            }

            $projeto->update(['ativo' => false]);

            Log::info('Projeto excluído com sucesso', ['projeto_id' => $id]);

            return true;

        } catch (Exception $e) {
            Log::error('Erro ao excluir projeto', [
                'projeto_id' => $id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Protocolar projeto
     */
    public function protocolar(int $id): Projeto
    {
        try {
            DB::beginTransaction();

            $projeto = Projeto::findOrFail($id);

            // Verificar se pode ser protocolado
            if ($projeto->status !== 'rascunho') {
                throw new Exception('Apenas projetos em rascunho podem ser protocolados');
            }

            if (!$projeto->hasContent()) {
                throw new Exception('Projeto deve ter conteúdo para ser protocolado');
            }

            // Atualizar status
            $projeto->update([
                'status' => 'protocolado',
                'data_protocolo' => now(),
            ]);

            // Atualizar tramitação
            $tramitacaoAtual = $projeto->tramitacaoAtual;
            if ($tramitacaoAtual) {
                $tramitacaoAtual->concluir('Projeto protocolado com sucesso');
            }

            DB::commit();

            Log::info('Projeto protocolado com sucesso', ['projeto_id' => $id]);

            return $projeto->fresh();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao protocolar projeto', [
                'projeto_id' => $id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Criar nova versão
     */
    public function criarVersao(int $projetoId, ProjetoVersionDTO $dto): ProjetoVersion
    {
        try {
            $projeto = Projeto::findOrFail($projetoId);

            if (!$projeto->podeEditarConteudo()) {
                throw new Exception('Não é possível criar nova versão neste status');
            }

            // Desativar versão atual
            ProjetoVersion::where('projeto_id', $projetoId)
                ->update(['is_current' => false]);

            // Criar nova versão
            $version = ProjetoVersion::create($dto->withDefaults()->toCreateArray());

            // Atualizar projeto
            $projeto->update([
                'version_atual' => $version->version_number,
                'conteudo' => $version->conteudo,
            ]);

            Log::info('Nova versão criada', [
                'projeto_id' => $projetoId,
                'version_number' => $version->version_number
            ]);

            return $version;

        } catch (Exception $e) {
            Log::error('Erro ao criar versão', [
                'projeto_id' => $projetoId,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Adicionar anexo
     */
    public function adicionarAnexo(int $projetoId, ProjetoAnexoDTO $dto): ProjetoAnexo
    {
        try {
            $projeto = Projeto::findOrFail($projetoId);

            if (!$projeto->podeAnexarArquivos()) {
                throw new Exception('Não é possível anexar arquivos neste status');
            }

            $anexo = ProjetoAnexo::create($dto->withDefaults()->toCreateArray());

            Log::info('Anexo adicionado com sucesso', [
                'projeto_id' => $projetoId,
                'anexo_id' => $anexo->id,
                'nome_arquivo' => $anexo->nome_original
            ]);

            return $anexo;

        } catch (Exception $e) {
            Log::error('Erro ao adicionar anexo', [
                'projeto_id' => $projetoId,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Adicionar tramitação
     */
    public function adicionarTramitacao(int $projetoId, ProjetoTramitacaoDTO $dto): ProjetoTramitacao
    {
        try {
            $tramitacao = ProjetoTramitacao::create($dto->withDefaults()->toCreateArray());

            Log::info('Tramitação adicionada', [
                'projeto_id' => $projetoId,
                'etapa' => $tramitacao->etapa,
                'acao' => $tramitacao->acao
            ]);

            return $tramitacao;

        } catch (Exception $e) {
            Log::error('Erro ao adicionar tramitação', [
                'projeto_id' => $projetoId,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Encaminhar para comissão
     */
    public function encaminharParaComissao(int $id, int $comissaoId, int $relatorId = null): Projeto
    {
        try {
            DB::beginTransaction();

            $projeto = Projeto::findOrFail($id);
            // TODO: Validar comissão via API externa
            // $comissao = Comissao::findOrFail($comissaoId);

            // Atualizar projeto
            $projeto->update([
                'status' => 'na_comissao',
                'comissao_id' => $comissaoId,
                'relator_id' => $relatorId,
            ]);

            // Adicionar tramitação
            $this->adicionarTramitacao($id, ProjetoTramitacaoDTO::criarDistribuicao(
                $id, 
                $comissaoId, 
                auth()->id()
            ));

            // Se tem relator, criar tramitação de relatoria
            if ($relatorId) {
                $this->adicionarTramitacao($id, ProjetoTramitacaoDTO::criarRelatoria(
                    $id, 
                    $relatorId, 
                    $comissaoId
                ));
            }

            DB::commit();

            Log::info('Projeto encaminhado para comissão', [
                'projeto_id' => $id,
                'comissao_id' => $comissaoId,
                'relator_id' => $relatorId
            ]);

            return $projeto->fresh();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao encaminhar para comissão', [
                'projeto_id' => $id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obter estatísticas
     */
    public function obterEstatisticas(): array
    {
        $baseQuery = Projeto::ativos()->visiveisPorUsuario(auth()->user());
        
        return [
            'total' => $baseQuery->count(),
            'por_status' => $baseQuery
                ->groupBy('status')
                ->selectRaw('status, count(*) as total')
                ->pluck('total', 'status')
                ->toArray(),
            'por_tipo' => $baseQuery
                ->groupBy('tipo')
                ->selectRaw('tipo, count(*) as total')
                ->pluck('total', 'tipo')
                ->toArray(),
            'urgentes' => $baseQuery->urgentes()->count(),
            'em_tramitacao' => $baseQuery
                ->whereIn('status', ['protocolado', 'em_tramitacao', 'na_comissao'])
                ->count(),
            'este_ano' => $baseQuery
                ->where('ano', date('Y'))
                ->count(),
        ];
    }

    /**
     * Gerar próximo número
     */
    private function gerarProximoNumero(int $ano, string $tipo): string
    {
        $ultimoNumero = Projeto::where('ano', $ano)
            ->where('tipo', $tipo)
            ->max('numero');

        $proximoNumero = $ultimoNumero ? ((int) $ultimoNumero) + 1 : 1;

        return str_pad($proximoNumero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Verificações de permissão
     */
    private function podeEditar(Projeto $projeto): bool
    {
        $user = auth()->user();
        
        return $user->id === $projeto->autor_id ||
               $user->id === $projeto->relator_id ||
               $user->isAdmin();
    }

    private function podeExcluir(Projeto $projeto): bool
    {
        $user = auth()->user();
        
        return ($user->id === $projeto->autor_id && $projeto->status === 'rascunho') ||
               $user->isAdmin();
    }

    /**
     * Obter opções para formulários
     */
    public function obterOpcoes(): array
    {
        try {
            $autores = User::orderBy('name')->get();
        } catch (Exception $e) {
            Log::error('Erro ao buscar autores', ['erro' => $e->getMessage()]);
            $autores = collect(); // Return empty collection on error
        }

        return [
            'tipos' => Projeto::TIPOS,
            'status' => Projeto::STATUS,
            'urgencias' => Projeto::URGENCIA,
            'autores' => $autores,
            'comissoes' => [], // TODO: Buscar comissões via API externa
        ];
    }

    /**
     * Buscar projetos
     */
    public function buscar(string $termo, int $limite = 10): Collection
    {
        return Projeto::ativos()
            ->visiveisPorUsuario(auth()->user())
            ->where(function ($q) use ($termo) {
                $q->where('titulo', 'like', '%' . $termo . '%')
                  ->orWhere('numero', 'like', '%' . $termo . '%')
                  ->orWhere('ementa', 'like', '%' . $termo . '%');
            })
            ->with(['autor'])
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();
    }
}