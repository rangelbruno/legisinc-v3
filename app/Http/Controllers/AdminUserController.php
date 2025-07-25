<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Exception;

class AdminUserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = User::with(['roles'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === '1');
        }

        if ($request->filled('busca')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->busca . '%')
                  ->orWhere('email', 'like', '%' . $request->busca . '%')
                  ->orWhere('documento', 'like', '%' . $request->busca . '%');
            });
        }

        $usuarios = $query->paginate(15);
        $roles = Role::all();
        $perfis = [
            User::PERFIL_ADMIN => 'Administrador',
            User::PERFIL_LEGISLATIVO => 'Servidor Legislativo',
            User::PERFIL_PARLAMENTAR => 'Parlamentar',
            User::PERFIL_RELATOR => 'Relator',
            User::PERFIL_PROTOCOLO => 'Protocolo',
            User::PERFIL_ASSESSOR => 'Assessor',
            User::PERFIL_CIDADAO_VERIFICADO => 'Cidadão Verificado',
            User::PERFIL_PUBLICO => 'Público',
        ];

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'perfis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = Role::all();
        $perfis = [
            User::PERFIL_ADMIN => 'Administrador',
            User::PERFIL_LEGISLATIVO => 'Servidor Legislativo',
            User::PERFIL_PARLAMENTAR => 'Parlamentar',
            User::PERFIL_RELATOR => 'Relator',
            User::PERFIL_PROTOCOLO => 'Protocolo',
            User::PERFIL_ASSESSOR => 'Assessor',
            User::PERFIL_CIDADAO_VERIFICADO => 'Cidadão Verificado',
            User::PERFIL_PUBLICO => 'Público',
        ];

        return view('admin.usuarios.create', compact('roles', 'perfis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'documento' => ['nullable', 'string', 'max:20'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date'],
            'profissao' => ['nullable', 'string', 'max:100'],
            'cargo_atual' => ['nullable', 'string', 'max:100'],
            'partido' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'ativo' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $userData = $request->only([
                'name', 'email', 'documento', 'telefone', 
                'data_nascimento', 'profissao', 'cargo_atual', 'partido'
            ]);
            
            $userData['password'] = Hash::make($request->password);
            $userData['ativo'] = $request->boolean('ativo', true);

            $user = User::create($userData);
            $user->assignRole($request->role);

            return redirect()
                ->route('admin.usuarios.index')
                ->with('success', 'Usuário criado com sucesso!');

        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao criar usuário: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $usuario): View
    {
        $usuario->load(['roles']);
        return view('admin.usuarios.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $usuario): View
    {
        $usuario->load(['roles']);
        $roles = Role::all();
        $perfis = [
            User::PERFIL_ADMIN => 'Administrador',
            User::PERFIL_LEGISLATIVO => 'Servidor Legislativo',
            User::PERFIL_PARLAMENTAR => 'Parlamentar',
            User::PERFIL_RELATOR => 'Relator',
            User::PERFIL_PROTOCOLO => 'Protocolo',
            User::PERFIL_ASSESSOR => 'Assessor',
            User::PERFIL_CIDADAO_VERIFICADO => 'Cidadão Verificado',
            User::PERFIL_PUBLICO => 'Público',
        ];

        return view('admin.usuarios.edit', compact('usuario', 'roles', 'perfis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $usuario): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'documento' => ['nullable', 'string', 'max:20'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date'],
            'profissao' => ['nullable', 'string', 'max:100'],
            'cargo_atual' => ['nullable', 'string', 'max:100'],
            'partido' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'ativo' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $userData = $request->only([
                'name', 'email', 'documento', 'telefone', 
                'data_nascimento', 'profissao', 'cargo_atual', 'partido'
            ]);
            
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            
            $userData['ativo'] = $request->boolean('ativo', true);

            $usuario->update($userData);
            $usuario->syncRoles([$request->role]);

            return redirect()
                ->route('admin.usuarios.index')
                ->with('success', 'Usuário atualizado com sucesso!');

        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao atualizar usuário: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $usuario): JsonResponse
    {
        try {
            // Impedir que o usuário delete a si mesmo
            if (auth()->id() === $usuario->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode excluir sua própria conta.'
                ], 400);
            }

            $usuario->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuário excluído com sucesso!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleAtivo(User $usuario): JsonResponse
    {
        try {
            // Impedir que o usuário desative a si mesmo
            if (auth()->id() === $usuario->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode desativar sua própria conta.'
                ], 400);
            }

            $usuario->update(['ativo' => !$usuario->ativo]);

            return response()->json([
                'success' => true,
                'message' => $usuario->ativo ? 'Usuário ativado com sucesso!' : 'Usuário desativado com sucesso!',
                'ativo' => $usuario->ativo
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status do usuário: ' . $e->getMessage()
            ], 500);
        }
    }
}