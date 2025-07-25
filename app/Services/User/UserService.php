<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Exception;

class UserService
{
    /**
     * Listar usuários com paginação e filtros
     */
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with('roles')->orderBy('name');

        // Aplicar filtros
        if (!empty($filtros['nome'])) {
            $query->where('name', 'like', '%' . $filtros['nome'] . '%');
        }

        if (!empty($filtros['email'])) {
            $query->where('email', 'like', '%' . $filtros['email'] . '%');
        }

        if (!empty($filtros['perfil'])) {
            $query->whereHas('roles', function ($q) use ($filtros) {
                $q->where('name', $filtros['perfil']);
            });
        }

        if (isset($filtros['ativo'])) {
            $query->where('ativo', $filtros['ativo']);
        }

        if (!empty($filtros['documento'])) {
            $query->where('documento', 'like', '%' . $filtros['documento'] . '%');
        }

        if (!empty($filtros['partido'])) {
            $query->where('partido', 'like', '%' . $filtros['partido'] . '%');
        }

        return $query->paginate($perPage);
    }

    /**
     * Obter usuário por ID
     */
    public function obterPorId(int $id): ?User
    {
        return User::with('roles')->find($id);
    }

    /**
     * Criar novo usuário
     */
    public function criar(array $dados): User
    {
        try {
            DB::beginTransaction();

            // Preparar dados
            $dados['password'] = Hash::make($dados['password']);
            $dados['ativo'] = $dados['ativo'] ?? true;
            $dados['ultimo_acesso'] = now();

            // Criar usuário
            $usuario = User::create($dados);

            // Atribuir perfil
            if (!empty($dados['perfil'])) {
                // Verificar se o role existe, senão criar
                $role = Role::firstOrCreate([
                    'name' => $dados['perfil'],
                    'guard_name' => 'web'
                ]);
                
                $usuario->assignRole($dados['perfil']);
            }

            DB::commit();

            Log::info('Usuário criado com sucesso', [
                'usuario_id' => $usuario->id,
                'nome' => $usuario->name,
                'email' => $usuario->email
            ]);

            return $usuario;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar usuário', [
                'erro' => $e->getMessage(),
                'dados' => $dados
            ]);
            throw $e;
        }
    }

    /**
     * Atualizar usuário
     */
    public function atualizar(int $id, array $dados): User
    {
        try {
            DB::beginTransaction();

            $usuario = User::findOrFail($id);

            // Atualizar senha se fornecida
            if (!empty($dados['password'])) {
                $dados['password'] = Hash::make($dados['password']);
            } else {
                unset($dados['password']);
            }

            // Atualizar dados
            $usuario->update($dados);

            // Atualizar perfil se fornecido
            if (!empty($dados['perfil'])) {
                // Verificar se o role existe, senão criar
                $role = Role::firstOrCreate([
                    'name' => $dados['perfil'],
                    'guard_name' => 'web'
                ]);
                
                $usuario->syncRoles([$dados['perfil']]);
            }

            DB::commit();

            Log::info('Usuário atualizado com sucesso', [
                'usuario_id' => $usuario->id,
                'nome' => $usuario->name
            ]);

            return $usuario->fresh();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar usuário', [
                'usuario_id' => $id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Excluir usuário
     */
    public function excluir(int $id): bool
    {
        try {
            DB::beginTransaction();

            $usuario = User::findOrFail($id);

            // Verificar se não é o próprio usuário logado
            if (auth()->id() === $id) {
                throw new Exception('Não é possível excluir o próprio usuário');
            }

            // Remover roles antes de excluir
            $usuario->roles()->detach();

            // Excluir usuário
            $usuario->delete();

            DB::commit();

            Log::info('Usuário excluído com sucesso', [
                'usuario_id' => $id,
                'nome' => $usuario->name
            ]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir usuário', [
                'usuario_id' => $id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Ativar/desativar usuário
     */
    public function alterarStatus(int $id, bool $ativo): User
    {
        try {
            $usuario = User::findOrFail($id);

            // Verificar se não é o próprio usuário logado
            if (auth()->id() === $id) {
                throw new Exception('Não é possível alterar o status do próprio usuário');
            }

            $usuario->update(['ativo' => $ativo]);

            Log::info('Status do usuário alterado', [
                'usuario_id' => $id,
                'nome' => $usuario->name,
                'ativo' => $ativo
            ]);

            return $usuario->fresh();

        } catch (Exception $e) {
            Log::error('Erro ao alterar status do usuário', [
                'usuario_id' => $id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Resetar senha do usuário
     */
    public function resetarSenha(int $id): string
    {
        try {
            $usuario = User::findOrFail($id);

            // Gerar nova senha temporária
            $novaSenha = Str::random(8);
            $usuario->update(['password' => Hash::make($novaSenha)]);

            Log::info('Senha resetada com sucesso', [
                'usuario_id' => $id,
                'nome' => $usuario->name
            ]);

            return $novaSenha;

        } catch (Exception $e) {
            Log::error('Erro ao resetar senha', [
                'usuario_id' => $id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obter estatísticas de usuários
     */
    public function obterEstatisticas(): array
    {
        return [
            'total' => User::count(),
            'ativos' => User::where('ativo', true)->count(),
            'inativos' => User::where('ativo', false)->count(),
            'por_perfil' => DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->groupBy('roles.name')
                ->selectRaw('roles.name as perfil, count(*) as total')
                ->get(),
            'ultimo_acesso' => User::where('ultimo_acesso', '>=', now()->subDays(30))->count(),
            'parlamentares' => User::whereHas('roles', function ($q) {
                $q->whereIn('name', [User::PERFIL_PARLAMENTAR, User::PERFIL_RELATOR]);
            })->count(),
        ];
    }

    /**
     * Obter perfis disponíveis
     */
    public function obterPerfisDisponiveis(): array
    {
        return [
            User::PERFIL_ADMIN => 'Administrador',
            User::PERFIL_LEGISLATIVO => 'Servidor Legislativo',
            User::PERFIL_PARLAMENTAR => 'Parlamentar',
            User::PERFIL_RELATOR => 'Relator',
            User::PERFIL_ASSESSOR => 'Assessor',
            User::PERFIL_CIDADAO_VERIFICADO => 'Cidadão Verificado',
            User::PERFIL_PUBLICO => 'Público',
        ];
    }

    /**
     * Verificar se email já existe
     */
    public function emailExiste(string $email, int $ignorarId = null): bool
    {
        $query = User::where('email', $email);

        if ($ignorarId) {
            $query->where('id', '!=', $ignorarId);
        }

        return $query->exists();
    }

    /**
     * Verificar se documento já existe
     */
    public function documentoExiste(string $documento, int $ignorarId = null): bool
    {
        $query = User::where('documento', $documento);

        if ($ignorarId) {
            $query->where('id', '!=', $ignorarId);
        }

        return $query->exists();
    }

    /**
     * Buscar usuários por termo
     */
    public function buscar(string $termo, int $limite = 10): Collection
    {
        return User::where('name', 'like', '%' . $termo . '%')
            ->orWhere('email', 'like', '%' . $termo . '%')
            ->orWhere('documento', 'like', '%' . $termo . '%')
            ->orderBy('name')
            ->limit($limite)
            ->get();
    }

    /**
     * Obter usuários por perfil
     */
    public function obterPorPerfil(string $perfil): Collection
    {
        return User::whereHas('roles', function ($q) use ($perfil) {
            $q->where('name', $perfil);
        })->orderBy('name')->get();
    }

    /**
     * Atualizar último acesso
     */
    public function atualizarUltimoAcesso(int $id): void
    {
        User::where('id', $id)->update(['ultimo_acesso' => now()]);
    }

    /**
     * Obter usuários recentes
     */
    public function obterRecentes(int $limite = 10): Collection
    {
        return User::orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Obter usuários por último acesso
     */
    public function obterPorUltimoAcesso(int $dias = 30): Collection
    {
        return User::where('ultimo_acesso', '>=', now()->subDays($dias))
            ->orderBy('ultimo_acesso', 'desc')
            ->get();
    }
}