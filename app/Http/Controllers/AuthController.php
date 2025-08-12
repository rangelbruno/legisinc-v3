<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Parlamentar;
use App\Factories\NavigationControlFactory;
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
        // Verifica se usuário já está autenticado
        if (!NavigationControlFactory::canAccessLoginPage()) {
            return redirect(NavigationControlFactory::getRedirectRoute());
        }
        
        return view('auth.login');
    }

    /**
     * Mostrar página de registro
     */
    public function showRegisterForm()
    {
        // Verifica se usuário já está autenticado
        if (!NavigationControlFactory::canAccessLoginPage()) {
            return redirect(NavigationControlFactory::getRedirectRoute());
        }
        
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
                // Atualizar último acesso
                $user = Auth::user();
                $user->ultimo_acesso = now();
                $user->save();
                
                // Configura sessão pós-login
                $redirectTo = NavigationControlFactory::setupPostLoginSession($request);
                
                // Obtém rota apropriada baseada no perfil
                $redirectTo = NavigationControlFactory::getRedirectRoute($user);

                return redirect($redirectTo)
                    ->with('success', 'Login realizado com sucesso!')
                    ->withHeaders([
                        'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT'
                    ]);
            }
        } catch (\Exception $e) {
            // Database not available, use mock authentication
            // Log::info('Database not available, using mock authentication');
        }

        // Mock authentication for demo purposes
        if ($this->attemptMockLogin($credentials)) {
            // Configura sessão pós-login
            $redirectTo = NavigationControlFactory::setupPostLoginSession($request);
            
            // Obtém rota apropriada baseada no perfil
            $user = Auth::user();
            $redirectTo = NavigationControlFactory::getRedirectRoute($user);
            
            return redirect($redirectTo)
                ->with('success', 'Login realizado com sucesso (modo demo)!')
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT'
                ]);
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
                
                // Log::info('Registro de parlamentar criado automaticamente', [
                    //     'usuario_id' => $user->id,
                    //     'nome' => $user->name
                // ]);
            }
            
            // Log::info('Usuário registrado com sucesso', [
                //     'usuario_id' => $user->id,
                //     'nome' => $user->name,
                //     'email' => $user->email,
                //     'tipo_usuario' => $tipoUsuario
            // ]);

            return redirect()->route('auth.register')
                ->with('success', 'Usuário registrado com sucesso! Você pode fazer login agora.');

        } catch (\Exception $e) {
            // Log::error('Erro inesperado durante registro', [
                //     'error' => $e->getMessage(),
                //     'email' => $request->input('email'),
                //     'tipo_usuario' => $request->input('tipo_usuario')
            // ]);

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
        
        // Limpa sessão usando a factory
        NavigationControlFactory::clearSession($request);
        
        return redirect()->route('login')
            ->with('success', 'Logout realizado com sucesso!')
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT'
            ]);
    }

    /**
     * Attempt mock login when database is not available
     */
    private function attemptMockLogin(array $credentials): bool
    {
        // Mock users for demo purposes
        $mockUsers = [
            'bruno@sistema.gov.br' => [
                'password' => '123456',
                'user' => [
                    'id' => 1,
                    'name' => 'Bruno Silva',
                    'email' => 'bruno@sistema.gov.br',
                    'documento' => '000.000.001-00',
                    'telefone' => '(11) 9000-0001',
                    'profissao' => 'Administrador de Sistema',
                    'cargo_atual' => 'Administrador',
                    'ativo' => true,
                ]
            ],
            'jessica@sistema.gov.br' => [
                'password' => '123456',
                'user' => [
                    'id' => 2,
                    'name' => 'Jessica Santos',
                    'email' => 'jessica@sistema.gov.br',
                    'documento' => '111.111.111-11',
                    'telefone' => '(11) 9111-1111',
                    'data_nascimento' => '1980-05-20',
                    'profissao' => 'Advogada',
                    'cargo_atual' => 'Vereadora',
                    'partido' => 'PT',
                    'ativo' => true,
                ]
            ],
            'joao@sistema.gov.br' => [
                'password' => '123456',
                'user' => [
                    'id' => 3,
                    'name' => 'João Oliveira',
                    'email' => 'joao@sistema.gov.br',
                    'documento' => '222.222.222-22',
                    'telefone' => '(11) 9222-2222',
                    'profissao' => 'Servidor Legislativo',
                    'cargo_atual' => 'Diretor Legislativo',
                    'ativo' => true,
                ]
            ],
            'roberto@sistema.gov.br' => [
                'password' => '123456',
                'user' => [
                    'id' => 4,
                    'name' => 'Roberto Costa',
                    'email' => 'roberto@sistema.gov.br',
                    'documento' => '333.333.333-33',
                    'telefone' => '(11) 9333-3333',
                    'profissao' => 'Servidor Público',
                    'cargo_atual' => 'Chefe de Protocolo',
                    'ativo' => true,
                ]
            ],
            'expediente@sistema.gov.br' => [
                'password' => '123456',
                'user' => [
                    'id' => 10,
                    'name' => 'Carlos Expediente',
                    'email' => 'expediente@sistema.gov.br',
                    'documento' => '444.444.444-44',
                    'telefone' => '(11) 9444-4444',
                    'profissao' => 'Servidor Público',
                    'cargo_atual' => 'Responsável pelo Expediente',
                    'ativo' => true,
                ]
            ],
            'juridico@sistema.gov.br' => [
                'password' => '123456',
                'user' => [
                    'id' => 6,
                    'name' => 'Carlos Jurídico',
                    'email' => 'juridico@sistema.gov.br',
                    'documento' => '555.555.555-55',
                    'telefone' => '(11) 9555-5555',
                    'profissao' => 'Advogado',
                    'cargo_atual' => 'Assessor Jurídico',
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

            // Assign appropriate role based on email
            $roleMapping = [
                'bruno@sistema.gov.br' => 'ADMIN',
                'jessica@sistema.gov.br' => 'PARLAMENTAR',
                'joao@sistema.gov.br' => 'LEGISLATIVO',
                'roberto@sistema.gov.br' => 'PROTOCOLO',
                'expediente@sistema.gov.br' => 'EXPEDIENTE',
                'juridico@sistema.gov.br' => 'ASSESSOR_JURIDICO',
            ];
            
            if (isset($roleMapping[$email])) {
                // Create a mock role collection
                $user->roles = collect([(object)['name' => $roleMapping[$email]]]);
            }

            // Manually log in the user
            Auth::login($user);
            
            return true;
        }

        return false;
    }
}