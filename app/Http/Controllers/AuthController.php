<?php

namespace App\Http\Controllers;

use App\Services\ApiClient\Providers\NodeApiClient;
use App\Services\ApiClient\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        private NodeApiClient $nodeApi
    ) {}

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

        try {
            $response = $this->nodeApi->login(
                $request->input('email'),
                $request->input('password')
            );

            if ($response->isSuccess()) {
                // Armazenar informações do usuário na sessão
                Session::put('api_authenticated', true);
                Session::put('api_user', $response->getData());
                
                return redirect()->route('dashboard')
                    ->with('success', 'Login realizado com sucesso!');
            }

            return back()->withErrors([
                'email' => 'Credenciais inválidas.'
            ])->withInput();

        } catch (ApiException $e) {
            return back()->withErrors([
                'email' => 'Erro ao conectar com a API: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Processar registro
     */
    public function register(Request $request)
    {
        // Validação com mensagens personalizadas
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string'
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'password_confirmation.required' => 'A confirmação de senha é obrigatória.'
        ]);

        try {
            $response = $this->nodeApi->register(
                $request->input('name'),
                $request->input('email'),
                $request->input('password')
            );

            if ($response->isSuccess()) {
                return redirect()->route('auth.register')
                    ->with('success', 'Usuário registrado com sucesso! Seus dados foram salvos no sistema.');
            }

            // Tratar diferentes tipos de erro da API
            $errorData = $response->getData();
            
            if (isset($errorData['error'])) {
                $errorMessage = $errorData['error'];
                
                // Verificar se é erro de usuário já existente
                if (str_contains($errorMessage, 'already exists') || str_contains($errorMessage, 'já existe')) {
                    return back()->withErrors([
                        'email' => 'Este email já está cadastrado no sistema.'
                    ])->withInput($request->except('password', 'password_confirmation'));
                }
                
                // Outros erros da API
                return back()->withErrors([
                    'email' => 'Erro ao registrar usuário: ' . $errorMessage
                ])->withInput($request->except('password', 'password_confirmation'));
            }

            return back()->withErrors([
                'email' => 'Falha ao registrar usuário. Tente novamente.'
            ])->withInput($request->except('password', 'password_confirmation'));

        } catch (ApiException $e) {
            Log::error('Erro na API durante registro', [
                'error' => $e->getMessage(),
                'context' => $e->getContext(),
                'email' => $request->input('email')
            ]);

            return back()
                ->with('error', 'Erro de conexão com a API. Tente novamente em alguns instantes.')
                ->withInput($request->except('password', 'password_confirmation'));
        } catch (\Exception $e) {
            Log::error('Erro inesperado durante registro', [
                'error' => $e->getMessage(),
                'email' => $request->input('email')
            ]);

            return back()
                ->with('error', 'Erro inesperado. Tente novamente.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->nodeApi->logout();
        
        Session::forget('api_authenticated');
        Session::forget('api_user');
        
        return redirect()->route('auth.login')
            ->with('success', 'Logout realizado com sucesso!');
    }
}