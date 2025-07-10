<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Exibir lista de usuários
     */
    public function index(Request $request)
    {
        // Preparar filtros
        $filtros = [
            'nome' => $request->get('nome'),
            'email' => $request->get('email'),
            'perfil' => $request->get('perfil'),
            'ativo' => $request->get('ativo'),
            'documento' => $request->get('documento'),
            'partido' => $request->get('partido'),
        ];

        // Buscar usuários com filtros
        $usuarios = $this->userService->listar($filtros, 15);

        // Obter perfis disponíveis
        $perfis = $this->userService->obterPerfisDisponiveis();

        // Obter estatísticas
        $estatisticas = $this->userService->obterEstatisticas();

        return view('modules.usuarios.index', compact('usuarios', 'perfis', 'estatisticas', 'filtros'));
    }

    /**
     * Exibir formulário de criação
     */
    public function create(): View
    {
        $perfis = $this->userService->obterPerfisDisponiveis();
        return view('modules.usuarios.create', compact('perfis'));
    }

    /**
     * Armazenar novo usuário
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'documento' => 'nullable|string|max:20|unique:users,documento',
            'telefone' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'profissao' => 'nullable|string|max:255',
            'cargo_atual' => 'nullable|string|max:255',
            'partido' => 'nullable|string|max:50',
            'perfil' => 'required|string|in:' . implode(',', array_keys($this->userService->obterPerfisDisponiveis())),
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->userService->criar($request->all());

            return redirect()->route('usuarios.index')
                ->with('success', 'Usuário criado com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar usuário: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Exibir detalhes do usuário
     */
    public function show(int $id): View
    {
        $usuario = $this->userService->obterPorId($id);

        if (!$usuario) {
            abort(404);
        }

        return view('modules.usuarios.show', compact('usuario'));
    }

    /**
     * Exibir formulário de edição
     */
    public function edit(int $id): View
    {
        $usuario = $this->userService->obterPorId($id);

        if (!$usuario) {
            abort(404);
        }

        $perfis = $this->userService->obterPerfisDisponiveis();
        
        return view('modules.usuarios.edit', compact('usuario', 'perfis'));
    }

    /**
     * Atualizar usuário
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'documento' => 'nullable|string|max:20|unique:users,documento,' . $id,
            'telefone' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'profissao' => 'nullable|string|max:255',
            'cargo_atual' => 'nullable|string|max:255',
            'partido' => 'nullable|string|max:50',
            'perfil' => 'required|string|in:' . implode(',', array_keys($this->userService->obterPerfisDisponiveis())),
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->userService->atualizar($id, $request->all());

            return redirect()->route('usuarios.index')
                ->with('success', 'Usuário atualizado com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar usuário: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Excluir usuário
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->userService->excluir($id);

            return redirect()->route('usuarios.index')
                ->with('success', 'Usuário excluído com sucesso!');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao excluir usuário: ' . $e->getMessage());
        }
    }

    /**
     * Alterar status do usuário
     */
    public function alterarStatus(Request $request, int $id): JsonResponse
    {
        try {
            $usuario = $this->userService->alterarStatus($id, $request->boolean('ativo'));

            return response()->json([
                'success' => true,
                'message' => 'Status alterado com sucesso!',
                'usuario' => $usuario
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Resetar senha do usuário
     */
    public function resetarSenha(int $id): JsonResponse
    {
        try {
            $novaSenha = $this->userService->resetarSenha($id);

            return response()->json([
                'success' => true,
                'message' => 'Senha resetada com sucesso!',
                'nova_senha' => $novaSenha
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao resetar senha: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Buscar usuários (para autocomplete)
     */
    public function buscar(Request $request): JsonResponse
    {
        $termo = $request->get('termo', '');
        $limite = $request->get('limite', 10);

        $usuarios = $this->userService->buscar($termo, $limite);

        return response()->json([
            'success' => true,
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Obter estatísticas dos usuários
     */
    public function estatisticas(): JsonResponse
    {
        $estatisticas = $this->userService->obterEstatisticas();

        return response()->json([
            'success' => true,
            'estatisticas' => $estatisticas
        ]);
    }

    /**
     * Obter usuários por perfil
     */
    public function porPerfil(string $perfil): JsonResponse
    {
        try {
            $usuarios = $this->userService->obterPorPerfil($perfil);

            return response()->json([
                'success' => true,
                'usuarios' => $usuarios
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter usuários: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Validar email único
     */
    public function validarEmail(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $ignorarId = $request->get('ignorar_id');

        $existe = $this->userService->emailExiste($email, $ignorarId);

        return response()->json([
            'disponivel' => !$existe
        ]);
    }

    /**
     * Validar documento único
     */
    public function validarDocumento(Request $request): JsonResponse
    {
        $documento = $request->get('documento');
        $ignorarId = $request->get('ignorar_id');

        $existe = $this->userService->documentoExiste($documento, $ignorarId);

        return response()->json([
            'disponivel' => !$existe
        ]);
    }

    /**
     * Exportar usuários
     */
    public function exportar(Request $request): JsonResponse
    {
        // TODO: Implementar exportação quando necessário
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidade de exportação não implementada ainda.'
        ]);
    }

    /**
     * Importar usuários
     */
    public function importar(Request $request): JsonResponse
    {
        // TODO: Implementar importação quando necessário
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidade de importação não implementada ainda.'
        ]);
    }
}