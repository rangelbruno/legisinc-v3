<?php

namespace App\Http\Middleware;

use App\Models\ScreenPermission;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckScreenPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin sempre tem acesso a tudo
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Obter a rota atual
        $currentRoute = $request->route()->getName();
        
        // Obter o perfil do usuário
        $userRole = $user->getRoleNames()->first();
        
        if (!$userRole) {
            return redirect()->route('dashboard')->with('error', 'Usuário sem perfil definido.');
        }

        // Verificar se o usuário tem permissão para acessar esta tela
        $hasAccess = $this->checkAccess($userRole, $currentRoute);
        
        if (!$hasAccess) {
            // Se for uma requisição AJAX, retornar erro JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Acesso negado',
                    'message' => 'Você não tem permissão para acessar esta funcionalidade.'
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }

    /**
     * Verificar se o usuário tem acesso à rota
     */
    private function checkAccess(string $userRole, string $route): bool
    {
        // Primeiro verificar no banco de dados
        $permission = ScreenPermission::where('role_name', $userRole)
            ->where('screen_route', $route)
            ->first();
            
        if ($permission) {
            return $permission->can_access;
        }
        
        // Se não encontrou no banco, usar regras padrão
        return $this->getDefaultAccess($userRole, $route);
    }

    /**
     * Obter acesso padrão baseado no perfil quando não há configuração específica
     */
    private function getDefaultAccess(string $userRole, string $route): bool
    {
        // Admin sempre tem acesso
        if ($userRole === User::PERFIL_ADMIN) {
            return true;
        }

        // Mapear módulos por rota
        $moduleMap = [
            'dashboard' => 'dashboard',
            'parlamentares' => 'parlamentares',
            'comissoes' => 'comissoes', 
            'projetos' => 'projetos',
            'sessoes' => 'sessoes',
            'admin.sessions' => 'sessoes',
            'usuarios' => 'usuarios',
            'admin.usuarios' => 'usuarios',
            'modelos' => 'modelos',
            'admin.screen-permissions' => 'admin'
        ];

        $module = null;
        foreach ($moduleMap as $routePrefix => $moduleKey) {
            if (str_starts_with($route, $routePrefix)) {
                $module = $moduleKey;
                break;
            }
        }

        if (!$module) {
            return false; // Se não conseguir identificar o módulo, negar acesso
        }

        // Aplicar regras por perfil
        switch ($userRole) {
            case User::PERFIL_LEGISLATIVO:
                // Servidor legislativo tem acesso quase total exceto administração
                return !in_array($module, ['usuarios', 'admin']);
                
            case User::PERFIL_PARLAMENTAR:
            case User::PERFIL_RELATOR:
                // Parlamentares têm acesso limitado
                return !in_array($module, ['usuarios', 'modelos', 'admin']) && 
                       (!str_contains($route, '.delete') || str_contains($route, 'projetos'));
                
            case User::PERFIL_PROTOCOLO:
                // Protocolo tem acesso principalmente a projetos e sessões
                return in_array($module, ['dashboard', 'projetos', 'sessoes', 'parlamentares']);
                
            case User::PERFIL_ASSESSOR:
                // Assessor tem acesso limitado principalmente a consultas
                return in_array($module, ['dashboard', 'parlamentares', 'projetos', 'comissoes']) &&
                       (!str_contains($route, '.create') && !str_contains($route, '.edit') && !str_contains($route, '.delete'));
                
            case User::PERFIL_CIDADAO_VERIFICADO:
            case User::PERFIL_PUBLICO:
                // Acesso apenas a visualizações públicas
                return in_array($module, ['dashboard', 'parlamentares', 'comissoes']) &&
                       str_contains($route, '.index');
                
            default:
                return false;
        }
    }
}
