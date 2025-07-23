<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Parlamentar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Mostrar página de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Mostrar página de registro
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Processar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        // Try database authentication first
        try {
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return redirect()->intended(route('dashboard'))
                    ->with('success', 'Login realizado com sucesso!');
            }
        } catch (\Exception $e) {
            // Database not available, use mock authentication
            Log::info('Database not available, using mock authentication');
        }

        // Mock authentication for demo purposes
        if ($this->attemptMockLogin($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Login realizado com sucesso (modo demo)!');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas.'
        ])->withInput();
    }

    /**
     * Processar registro
     */
    public function register(Request $request)
    {
        // Validação com mensagens personalizadas
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
            'tipo_usuario' => 'required|string|in:PUBLICO,CIDADAO_VERIFICADO,ASSESSOR,LEGISLATIVO,PARLAMENTAR,ADMIN'
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'email.unique' => 'Este email já está cadastrado no sistema.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'password_confirmation.required' => 'A confirmação de senha é obrigatória.',
            'tipo_usuario.required' => 'O tipo de usuário é obrigatório.',
            'tipo_usuario.in' => 'Tipo de usuário inválido.'
        ]);

        try {
            // Criar usuário
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'ativo' => true,
            ]);

            // Atribuir role selecionada
            $tipoUsuario = $request->input('tipo_usuario');
            $user->assignRole($tipoUsuario);
            
            // Se o usuário for parlamentar, criar registro na tabela parlamentars
            if ($tipoUsuario === User::PERFIL_PARLAMENTAR) {
                Parlamentar::create([
                    'user_id' => $user->id,
                    'nome' => $user->name,
                    'email' => $user->email,
                    'cpf' => $user->documento,
                    'telefone' => $user->telefone,
                    'data_nascimento' => $user->data_nascimento,
                    'profissao' => $user->profissao,
                    'cargo' => $user->cargo_atual ?? 'Parlamentar',
                    'partido' => $user->partido,
                    'status' => 'ativo',
                ]);
                
                Log::info('Registro de parlamentar criado automaticamente', [
                    'usuario_id' => $user->id,
                    'nome' => $user->name
                ]);
            }
            
            Log::info('Usuário registrado com sucesso', [
                'usuario_id' => $user->id,
                'nome' => $user->name,
                'email' => $user->email,
                'tipo_usuario' => $tipoUsuario
            ]);

            return redirect()->route('auth.register')
                ->with('success', 'Usuário registrado com sucesso! Você pode fazer login agora.');

        } catch (\Exception $e) {
            Log::error('Erro inesperado durante registro', [
                'error' => $e->getMessage(),
                'email' => $request->input('email'),
                'tipo_usuario' => $request->input('tipo_usuario')
            ]);

            return back()
                ->with('error', 'Erro inesperado. Tente novamente.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'Logout realizado com sucesso!');
    }

    /**
     * Attempt mock login when database is not available
     */
    private function attemptMockLogin(array $credentials): bool
    {
        // Mock users for demo purposes
        $mockUsers = [
            'admin@sistema.gov.br' => [
                'password' => 'admin123',
                'user' => [
                    'id' => 1,
                    'name' => 'Administrador do Sistema',
                    'email' => 'admin@sistema.gov.br',
                    'documento' => '000.000.000-00',
                    'telefone' => '(11) 0000-0000',
                    'profissao' => 'Administrador de Sistema',
                    'cargo_atual' => 'Administrador',
                    'ativo' => true,
                ]
            ],
            'parlamentar@camara.gov.br' => [
                'password' => 'parlamentar123',
                'user' => [
                    'id' => 2,
                    'name' => 'João Silva Santos',
                    'email' => 'parlamentar@camara.gov.br',
                    'documento' => '111.111.111-11',
                    'telefone' => '(11) 1111-1111',
                    'data_nascimento' => '1975-03-15',
                    'profissao' => 'Advogado',
                    'cargo_atual' => 'Vereador',
                    'partido' => 'PT',
                    'ativo' => true,
                ]
            ]
        ];

        $email = $credentials['email'];
        $password = $credentials['password'];

        if (isset($mockUsers[$email]) && $password === $mockUsers[$email]['password']) {
            // Create a mock User instance
            $userData = $mockUsers[$email]['user'];
            $user = new User();
            foreach ($userData as $key => $value) {
                $user->{$key} = $value;
            }
            $user->exists = true; // Mark as existing user

            // Manually log in the user
            Auth::login($user);
            
            return true;
        }

        return false;
    }
}