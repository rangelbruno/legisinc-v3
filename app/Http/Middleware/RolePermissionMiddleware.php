<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RolePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permission = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $this->getUserRole($user);
        
        // Se não conseguiu identificar o role, negar acesso
        if (!$userRole) {
            abort(403, 'Usuário sem role definido.');
        }

        // Verificar permissão específica se fornecida
        if ($permission) {
            if (!$this->hasPermission($userRole, $permission, $request)) {
                abort(403, "Acesso negado. Permissão '$permission' não encontrada para o role '$userRole'.");
            }
        }

        // Adicionar dados do usuário ao request para uso nos controllers
        $request->merge([
            'user_role' => $userRole,
            'user_permissions' => $this->getUserPermissions($userRole)
        ]);

        return $next($request);
    }

    /**
     * Obter role do usuário
     */
    private function getUserRole($user): ?string
    {
        try {
            // Buscar role do usuário na tabela model_has_roles
            $role = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $user->id)
                ->where('model_has_roles.model_type', get_class($user))
                ->value('roles.name');

            return $role;
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar role do usuário', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Verificar se o usuário tem permissão específica
     */
    private function hasPermission(string $role, string $permission, Request $request): bool
    {
        // Definir regras de permissão por role
        $rolePermissions = $this->getRolePermissions();

        // ADMIN tem acesso total
        if ($role === 'ADMIN') {
            return true;
        }

        // Verificar permissões específicas do role
        if (!isset($rolePermissions[$role])) {
            return false;
        }

        $permissions = $rolePermissions[$role];

        // Verificar permissão direta
        if (in_array($permission, $permissions)) {
            return true;
        }

        // Verificar permissões contextuais (baseadas na rota e parâmetros)
        return $this->hasContextualPermission($role, $permission, $request);
    }

    /**
     * Verificar permissões contextuais baseadas na proposição e usuário
     */
    private function hasContextualPermission(string $role, string $permission, Request $request): bool
    {
        // Para permissões de proposições, verificar se é o autor
        if (str_contains($permission, 'proposicoes.') && $request->route('proposicao')) {
            $proposicao = $request->route('proposicao');
            $user = Auth::user();

            // Se não conseguiu carregar a proposição, negar acesso
            if (!$proposicao) {
                return false;
            }

            // PARLAMENTAR pode acessar suas próprias proposições
            if ($role === 'PARLAMENTAR' && $proposicao->autor_id == $user->id) {
                $allowedActions = ['view', 'edit', 'assinar', 'corrigir'];
                $action = explode('.', $permission)[1] ?? '';
                return in_array($action, $allowedActions);
            }

            // LEGISLATIVO pode acessar proposições para análise
            if ($role === 'LEGISLATIVO') {
                $legislativeActions = ['view', 'review', 'approve', 'reject'];
                $action = explode('.', $permission)[1] ?? '';
                return in_array($action, $legislativeActions);
            }

            // PROTOCOLO pode protocolar proposições
            if ($role === 'PROTOCOLO') {
                $protocolActions = ['view', 'protocolar', 'numerar'];
                $action = explode('.', $permission)[1] ?? '';
                return in_array($action, $protocolActions);
            }
        }

        return false;
    }

    /**
     * Obter todas as permissões do usuário
     */
    private function getUserPermissions(string $role): array
    {
        $rolePermissions = $this->getRolePermissions();
        return $rolePermissions[$role] ?? [];
    }

    /**
     * Definir permissões por role
     */
    private function getRolePermissions(): array
    {
        return [
            'ADMIN' => [
                '*', // Acesso total
            ],
            
            'PARLAMENTAR' => [
                // Dashboard e navegação
                'dashboard.view',
                'home.view',
                
                // Proposições próprias
                'proposicoes.create',
                'proposicoes.view.own',
                'proposicoes.edit.own',
                'proposicoes.delete.own',
                'proposicoes.assinar.own',
                'proposicoes.corrigir.own',
                
                // OnlyOffice para edição
                'onlyoffice.editor.own',
                'onlyoffice.callback',
                
                // API para interface Vue
                'api.proposicoes.view.own',
                'api.proposicoes.update.own',
                
                // Perfil próprio
                'profile.view',
                'profile.edit',
            ],

            'LEGISLATIVO' => [
                // Dashboard e navegação
                'dashboard.view',
                'home.view',
                
                // Proposições para análise
                'proposicoes.view.all',
                'proposicoes.review',
                'proposicoes.approve',
                'proposicoes.reject',
                'proposicoes.return',
                
                // OnlyOffice para revisão
                'onlyoffice.editor.review',
                'onlyoffice.callback',
                
                // API para interface Vue
                'api.proposicoes.view.all',
                'api.proposicoes.update.status',
                
                // Relatórios legislativos
                'relatorios.legislativo',
                
                // Perfil próprio
                'profile.view',
                'profile.edit',
            ],

            'PROTOCOLO' => [
                // Dashboard e navegação
                'dashboard.view',
                'home.view',
                
                // Proposições para protocolo
                'proposicoes.view.protocol',
                'proposicoes.protocolar',
                'proposicoes.numerar',
                
                // Gestão de protocolos
                'protocolos.create',
                'protocolos.view',
                'protocolos.edit',
                
                // API
                'api.proposicoes.view.protocol',
                'api.proposicoes.protocolar',
                
                // Relatórios de protocolo
                'relatorios.protocolo',
                
                // Perfil próprio
                'profile.view',
                'profile.edit',
            ],

            'EXPEDIENTE' => [
                // Dashboard básico
                'dashboard.view',
                'home.view',
                
                // Proposições para expediente
                'proposicoes.view.expediente',
                'proposicoes.encaminhar',
                
                // Documentos
                'documentos.view',
                'documentos.create',
                
                // Perfil próprio
                'profile.view',
                'profile.edit',
            ],

            'ASSESSOR_JURIDICO' => [
                // Dashboard e navegação
                'dashboard.view',
                'home.view',
                
                // Proposições para análise jurídica
                'proposicoes.view.juridico',
                'proposicoes.analise.juridica',
                'proposicoes.parecer.juridico',
                
                // OnlyOffice para análise
                'onlyoffice.editor.juridico',
                
                // Relatórios jurídicos
                'relatorios.juridico',
                
                // Perfil próprio
                'profile.view',
                'profile.edit',
            ],

            'ASSESSOR' => [
                // Dashboard básico
                'dashboard.view',
                'home.view',
                
                // Proposições para assessoria
                'proposicoes.view.assessor',
                'proposicoes.assessoria',
                
                // Documentos
                'documentos.view',
                
                // Perfil próprio
                'profile.view',
                'profile.edit',
            ],

            'RELATOR' => [
                // Dashboard e navegação
                'dashboard.view',
                'home.view',
                
                // Proposições para relatoria
                'proposicoes.view.relator',
                'proposicoes.relatar',
                'proposicoes.parecer.relator',
                
                // OnlyOffice para relatoria
                'onlyoffice.editor.relator',
                
                // Perfil próprio
                'profile.view',
                'profile.edit',
            ],

            'CIDADAO_VERIFICADO' => [
                // Acesso público limitado
                'proposicoes.view.public',
                'documentos.view.public',
                'pesquisa.public',
            ],

            'PUBLICO' => [
                // Acesso muito limitado
                'home.view',
                'pesquisa.basic',
            ],
        ];
    }

    /**
     * Verificar se proposição pertence ao usuário (para PARLAMENTAR)
     */
    public static function isOwner($proposicao, $user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user || !$proposicao) {
            return false;
        }

        return $proposicao->autor_id == $user->id;
    }

    /**
     * Verificar se usuário pode assinar proposição
     */
    public static function canSign($proposicao, $user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user || !$proposicao) {
            return false;
        }

        // Apenas PARLAMENTAR pode assinar suas próprias proposições
        $userRole = (new self())->getUserRole($user);
        
        if ($userRole !== 'PARLAMENTAR') {
            return false;
        }

        // Deve ser o autor da proposição
        if ($proposicao->autor_id != $user->id) {
            return false;
        }

        // Status deve permitir assinatura
        $allowedStatuses = ['aprovado', 'aprovado_assinatura', 'retornado_legislativo'];
        return in_array($proposicao->status, $allowedStatuses);
    }

    /**
     * Verificar se usuário pode editar proposição no OnlyOffice
     */
    public static function canEditOnlyOffice($proposicao, $user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user || !$proposicao) {
            return false;
        }

        $userRole = (new self())->getUserRole($user);

        // ADMIN sempre pode
        if ($userRole === 'ADMIN') {
            return true;
        }

        // PARLAMENTAR pode editar suas próprias proposições em status editável
        if ($userRole === 'PARLAMENTAR' && $proposicao->autor_id == $user->id) {
            $editableStatuses = ['rascunho', 'em_elaboracao', 'devolvido_correcao'];
            return in_array($proposicao->status, $editableStatuses);
        }

        // LEGISLATIVO pode revisar proposições enviadas
        if ($userRole === 'LEGISLATIVO') {
            $reviewableStatuses = ['enviado_legislativo', 'em_analise'];
            return in_array($proposicao->status, $reviewableStatuses);
        }

        return false;
    }
}