<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Login realizado com sucesso!');
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
}