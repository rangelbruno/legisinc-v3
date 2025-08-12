<?php

namespace App\Factories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class NavigationControlFactory
{
    /**
     * Previne navegação para página de login quando autenticado
     */
    public static function preventBackToLogin(Request $request)
    {
        // Adiciona headers para controlar cache do navegador
        return response()
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
    
    /**
     * Configura sessão após login bem-sucedido
     */
    public static function setupPostLoginSession(Request $request)
    {
        // Regenera ID da sessão por segurança
        $request->session()->regenerate();
        
        // Marca que o usuário está autenticado
        Session::put('authenticated', true);
        Session::put('auth_time', now());
        Session::put('last_activity', now());
        
        // Armazena URL pretendida ou define dashboard como padrão
        $intendedUrl = Session::pull('url.intended', route('dashboard'));
        
        return $intendedUrl;
    }
    
    /**
     * Limpa sessão no logout
     */
    public static function clearSession(Request $request)
    {
        // Remove flags de autenticação
        Session::forget('authenticated');
        Session::forget('auth_time');
        Session::forget('last_activity');
        
        // Invalida sessão
        $request->session()->invalidate();
        
        // Regenera token CSRF
        $request->session()->regenerateToken();
    }
    
    /**
     * Verifica se usuário pode acessar página de login
     */
    public static function canAccessLoginPage()
    {
        // Se está autenticado, não pode acessar login
        if (Auth::check() || Session::get('authenticated')) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Obtém rota de redirecionamento baseada no perfil do usuário
     */
    public static function getRedirectRoute($user = null)
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return route('login');
        }
        
        // Mapeia perfis para rotas específicas - verifica se rota existe antes de usar
        $roleRoutes = [
            'ADMIN' => 'dashboard',
            'PARLAMENTAR' => 'proposicoes.index',
            'LEGISLATIVO' => 'proposicoes.legislativo.index',
            'PROTOCOLO' => 'proposicoes.index',
            'EXPEDIENTE' => 'expediente.index',
            'ASSESSOR_JURIDICO' => 'parecer-juridico.index',
            'PUBLICO' => 'dashboard',
            'CIDADAO_VERIFICADO' => 'dashboard',
        ];
        
        // Obtém primeiro role do usuário
        $userRole = $user->roles->first();
        
        if ($userRole && isset($roleRoutes[$userRole->name])) {
            $routeName = $roleRoutes[$userRole->name];
            
            // Verifica se a rota existe antes de tentar usá-la
            if (\Route::has($routeName)) {
                return route($routeName);
            }
        }
        
        // Fallback para dashboard se existe, senão vai para home
        if (\Route::has('dashboard')) {
            return route('dashboard');
        }
        
        return '/';
    }
    
    /**
     * Valida tempo de inatividade da sessão
     */
    public static function validateSessionActivity(Request $request, $maxInactiveMinutes = 30)
    {
        $lastActivity = Session::get('last_activity');
        
        if ($lastActivity) {
            $inactiveMinutes = now()->diffInMinutes($lastActivity);
            
            if ($inactiveMinutes > $maxInactiveMinutes) {
                // Sessão expirou por inatividade
                self::clearSession($request);
                Auth::logout();
                return false;
            }
        }
        
        // Atualiza última atividade
        Session::put('last_activity', now());
        return true;
    }
    
    /**
     * Adiciona JavaScript para prevenir navegação com botão voltar
     */
    public static function getPreventBackScript()
    {
        return <<<'SCRIPT'
        <script>
            (function() {
                // Previne navegação com botão voltar após login
                if (window.history && window.history.pushState) {
                    window.history.pushState('forward', null, window.location.href);
                    window.onpopstate = function() {
                        window.history.pushState('forward', null, window.location.href);
                    };
                }
                
                // Desabilita cache do navegador
                window.onpageshow = function(event) {
                    if (event.persisted) {
                        window.location.reload();
                    }
                };
            })();
        </script>
        SCRIPT;
    }
}