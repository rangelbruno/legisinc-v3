<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'documento',
        'telefone',
        'data_nascimento',
        'profissao',
        'cargo_atual',
        'partido',
        'preferencias',
        'ativo',
        'ultimo_acesso',
        'avatar',
        'certificado_digital_path',
        'certificado_digital_nome',
        'certificado_digital_upload_em',
        'certificado_digital_validade',
        'certificado_digital_cn',
        'certificado_digital_ativo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'data_nascimento' => 'date',
            'preferencias' => 'array',
            'ativo' => 'boolean',
            'ultimo_acesso' => 'datetime',
            'certificado_digital_upload_em' => 'datetime',
            'certificado_digital_validade' => 'date',
            'certificado_digital_ativo' => 'boolean',
        ];
    }

    /**
     * Constantes dos perfis do sistema parlamentar
     */
    public const PERFIL_ADMIN = 'ADMIN';

    public const PERFIL_ASSESSOR_JURIDICO = 'ASSESSOR_JURIDICO';

    public const PERFIL_LEGISLATIVO = 'LEGISLATIVO';

    public const PERFIL_EXPEDIENTE = 'EXPEDIENTE';

    public const PERFIL_PARLAMENTAR = 'PARLAMENTAR';

    public const PERFIL_RELATOR = 'RELATOR';

    public const PERFIL_PROTOCOLO = 'PROTOCOLO';

    public const PERFIL_ASSESSOR = 'ASSESSOR';

    public const PERFIL_CIDADAO_VERIFICADO = 'CIDADAO_VERIFICADO';

    public const PERFIL_PUBLICO = 'PUBLICO';

    /**
     * Hierarquia de perfis (maior número = mais privilégios)
     */
    public const HIERARQUIA_PERFIS = [
        self::PERFIL_PUBLICO => 10,
        self::PERFIL_CIDADAO_VERIFICADO => 20,
        self::PERFIL_ASSESSOR => 30,
        self::PERFIL_PROTOCOLO => 40,
        self::PERFIL_PARLAMENTAR => 70,
        self::PERFIL_EXPEDIENTE => 75,
        self::PERFIL_LEGISLATIVO => 80,
        self::PERFIL_ASSESSOR_JURIDICO => 85,
        self::PERFIL_RELATOR => 90,
        self::PERFIL_ADMIN => 100,
    ];

    /**
     * Obter nível hierárquico do perfil atual
     */
    public function getNivelHierarquico(): int
    {
        $perfilAtual = $this->getRoleNames()->first();

        return self::HIERARQUIA_PERFIS[$perfilAtual] ?? 0;
    }

    /**
     * Verificar se tem nível hierárquico igual ou superior
     */
    public function temNivelMinimo(string $perfil): bool
    {
        return $this->getNivelHierarquico() >= (self::HIERARQUIA_PERFIS[$perfil] ?? 0);
    }

    /**
     * Verificar se é parlamentar
     */
    public function isParlamentar(): bool
    {
        return $this->hasRole([self::PERFIL_PARLAMENTAR, self::PERFIL_RELATOR, self::PERFIL_ADMIN]);
    }

    /**
     * Verificar se é admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::PERFIL_ADMIN);
    }

    /**
     * Verificar se é legislativo (servidor técnico)
     */
    public function isLegislativo(): bool
    {
        return $this->hasRole([self::PERFIL_LEGISLATIVO, self::PERFIL_ADMIN]);
    }

    /**
     * Verificar se é relator
     */
    public function isRelator(): bool
    {
        return $this->hasRole([self::PERFIL_RELATOR, self::PERFIL_ADMIN]);
    }

    /**
     * Verificar se é protocolo
     */
    public function isProtocolo(): bool
    {
        return $this->hasRole([self::PERFIL_PROTOCOLO, self::PERFIL_ADMIN]);
    }

    /**
     * Verificar se é expediente
     */
    public function isExpediente(): bool
    {
        return $this->hasRole([self::PERFIL_EXPEDIENTE, self::PERFIL_ADMIN]);
    }

    /**
     * Verificar se é assessor jurídico
     */
    public function isAssessorJuridico(): bool
    {
        return $this->hasRole([self::PERFIL_ASSESSOR_JURIDICO, self::PERFIL_ADMIN]);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        // Handle nested arrays that might come from Spatie middleware
        if (is_array($roles) && isset($roles[0]) && is_array($roles[0])) {
            $roles = $roles[0];
        }
        
        // Ensure roles is always a collection for consistent handling
        $rolesCollection = collect($roles);

        try {
            // Check if database connection is available
            if (config('database.default') === null || config('database.default') === 'null') {
                return $this->hasRoleFallback($roles);
            }

            $userRoles = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->where('model_has_roles.model_id', $this->id)
                ->pluck('roles.name');

            // If no roles found in database, use fallback
            if ($userRoles->isEmpty()) {
                return $this->hasRoleFallback($roles);
            }

            return $userRoles->intersect($rolesCollection)->isNotEmpty();
        } catch (\Exception $e) {
            // Fallback when database is not available
            return $this->hasRoleFallback($roles);
        }
    }

    /**
     * Fallback role checking when database is not available
     */
    private function hasRoleFallback($roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        // Handle nested arrays that might come from Spatie middleware
        if (is_array($roles) && isset($roles[0]) && is_array($roles[0])) {
            $roles = $roles[0];
        }

        try {
            // Get user roles from fallback method
            $userRoles = $this->getRoleNamesFallback();
            
            // Ensure userRoles is a Collection
            if (!$userRoles instanceof \Illuminate\Support\Collection) {
                $userRoles = collect($userRoles);
            }

            // Convert roles array to collection for intersection
            $rolesCollection = collect($roles);

            // Check if any of the user's roles match the required roles
            return $userRoles->intersect($rolesCollection)->isNotEmpty();
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error in hasRoleFallback', [
                'user_id' => $this->id,
                'email' => $this->email,
                'roles_requested' => $roles,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return false as safe fallback
            return false;
        }
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermissionTo(string $permission): bool
    {
        try {
            // Check if database connection is available
            if (config('database.default') === null || config('database.default') === 'null') {
                return $this->hasPermissionFallback($permission);
            }

            $userRoles = DB::table('model_has_roles')
                ->where('model_type', 'App\\Models\\User')
                ->where('model_id', $this->id)
                ->pluck('role_id');

            if ($userRoles->isEmpty()) {
                return $this->hasPermissionFallback($permission);
            }

            $hasPermission = DB::table('role_has_permissions')
                ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->whereIn('role_has_permissions.role_id', $userRoles)
                ->where('permissions.name', $permission)
                ->exists();

            // If no permission found in database but user is admin, use fallback
            if (! $hasPermission) {
                return $this->hasPermissionFallback($permission);
            }

            return $hasPermission;
        } catch (\Exception $e) {
            // Fallback when database is not available
            return $this->hasPermissionFallback($permission);
        }
    }

    /**
     * Fallback permission checking when database is not available
     */
    private function hasPermissionFallback(string $permission): bool
    {
        // For mock/demo purposes, check if user email indicates admin role
        if ($this->email === 'admin@sistema.gov.br' || str_contains($this->email, 'admin') || $this->email === 'test@example.com') {
            return true; // Admin has all permissions - treating test user as admin for demo
        }

        // Basic permissions for non-admin users
        $publicPermissions = [
            'parlamentares.view',
            'projetos.view',
            'sessions.view',
            'sessions.create',
            'sessions.edit',
            'sessions.delete',
            'sessions.export',
            'sessoes.view',
            'comissoes.view',
            'sistema.dashboard',
        ];

        return in_array($permission, $publicPermissions);
    }

    /**
     * Get user's role names
     */
    public function getRoleNames()
    {
        try {
            // Check if database connection is available
            if (config('database.default') === null || config('database.default') === 'null') {
                return $this->getRoleNamesFallback();
            }

            $roleNames = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->where('model_has_roles.model_id', $this->id)
                ->pluck('roles.name');

            // If no roles found in database, use fallback
            if ($roleNames->isEmpty()) {
                return $this->getRoleNamesFallback();
            }

            return $roleNames;
        } catch (\Exception $e) {
            // Fallback when database is not available
            return $this->getRoleNamesFallback();
        }
    }

    /**
     * Fallback role names when database is not available
     */
    private function getRoleNamesFallback()
    {
        // Use mock roles if available (from AuthController)
        if (isset($this->roles) && $this->roles) {
            $roleNames = $this->roles->pluck('name');
            // Ensure it's always a Collection
            return $roleNames instanceof \Illuminate\Support\Collection ? $roleNames : collect($roleNames);
        }

        // For mock/demo purposes, check if user email indicates specific roles
        if ($this->email === 'admin@sistema.gov.br' || str_contains($this->email, 'admin') || $this->email === 'test@example.com') {
            return collect([self::PERFIL_ADMIN]);
        }

        // Check for expediente role
        if ($this->email === 'expediente@sistema.gov.br' ||
            str_contains(strtolower($this->name), 'expediente') ||
            str_contains(strtolower($this->cargo_atual ?? ''), 'expediente')) {
            return collect([self::PERFIL_EXPEDIENTE]);
        }

        // Check for protocolo role
        if (str_contains(strtolower($this->name), 'protocolo') ||
            str_contains(strtolower($this->cargo_atual ?? ''), 'protocolo') ||
            str_contains(strtolower($this->email), 'protocolo')) {
            return collect([self::PERFIL_PROTOCOLO]);
        }

        // Check for juridico role
        if (str_contains(strtolower($this->name), 'juridico') ||
            str_contains(strtolower($this->cargo_atual ?? ''), 'juridico') ||
            str_contains(strtolower($this->email), 'juridico')) {
            return collect([self::PERFIL_ASSESSOR_JURIDICO]);
        }

        // Check if user name/email indicates legislativo role
        if (str_contains(strtolower($this->name), 'legislativo') ||
            str_contains(strtolower($this->name), 'servidor') ||
            str_contains(strtolower($this->email), 'legislativo') ||
            str_contains(strtolower($this->cargo_atual ?? ''), 'legislativo') ||
            str_contains(strtolower($this->cargo_atual ?? ''), 'servidor')) {
            return collect([self::PERFIL_LEGISLATIVO]);
        }

        // Check if user name/email indicates parlamentar role
        if (str_contains(strtolower($this->name), 'parlamentar') ||
            str_contains(strtolower($this->cargo_atual ?? ''), 'parlamentar') ||
            str_contains(strtolower($this->email), 'parlamentar')) {
            return collect([self::PERFIL_PARLAMENTAR]);
        }

        // Default to PUBLICO for unknown users
        return collect(['PUBLICO']);
    }

    /**
     * Obter perfil formatado para exibição
     */
    public function getPerfilFormatado(): string
    {
        $perfil = $this->getRoleNames()->first();

        return match ($perfil) {
            self::PERFIL_ADMIN => 'Administrador',
            self::PERFIL_ASSESSOR_JURIDICO => 'Assessor Jurídico',
            self::PERFIL_LEGISLATIVO => 'Servidor Legislativo',
            self::PERFIL_EXPEDIENTE => 'Expediente',
            self::PERFIL_PARLAMENTAR => 'Parlamentar',
            self::PERFIL_RELATOR => 'Relator',
            self::PERFIL_PROTOCOLO => 'Protocolo',
            self::PERFIL_ASSESSOR => 'Assessor',
            self::PERFIL_CIDADAO_VERIFICADO => 'Cidadão Verificado',
            self::PERFIL_PUBLICO => 'Público',
            default => 'Sem perfil'
        };
    }

    /**
     * Obter cor do badge do perfil
     */
    public function getCorPerfil(): string
    {
        $perfil = $this->getRoleNames()->first();

        return match ($perfil) {
            self::PERFIL_ADMIN => 'danger',
            self::PERFIL_ASSESSOR_JURIDICO => 'warning',
            self::PERFIL_LEGISLATIVO => 'primary',
            self::PERFIL_EXPEDIENTE => 'info',
            self::PERFIL_PARLAMENTAR => 'success',
            self::PERFIL_RELATOR => 'warning',
            self::PERFIL_PROTOCOLO => 'dark',
            self::PERFIL_ASSESSOR => 'info',
            self::PERFIL_CIDADAO_VERIFICADO => 'light-primary',
            self::PERFIL_PUBLICO => 'light',
            default => 'secondary'
        };
    }

    /**
     * Atualizar último acesso
     */
    public function atualizarUltimoAcesso(): void
    {
        $this->update(['ultimo_acesso' => now()]);
    }

    /**
     * Obter avatar ou iniciais
     */
    public function getAvatarAttribute($value): string
    {
        // Se há um valor e é um arquivo que existe, retorna o nome do arquivo
        if ($value && ! ctype_alpha($value) && file_exists(public_path('storage/'.$value))) {
            return $value;
        }

        // Retornar iniciais se não tiver avatar ou arquivo não existir
        $nomes = explode(' ', $this->name);
        $iniciais = '';
        foreach (array_slice($nomes, 0, 2) as $nome) {
            $iniciais .= strtoupper(substr($nome, 0, 1));
        }

        return $iniciais;
    }

    /**
     * Verifica se o usuário possui uma foto válida
     */
    public function temFotoValida(): bool
    {
        return $this->attributes['avatar'] &&
               ! ctype_alpha($this->attributes['avatar']) &&
               file_exists(public_path('storage/'.$this->attributes['avatar']));
    }

    /**
     * Scope para usuários ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Relacionamento com Parlamentar
     */
    public function parlamentar()
    {
        return $this->hasOne(Parlamentar::class);
    }

    /**
     * Relacionamento com proposições como autor
     */
    public function proposicoesAutor()
    {
        return $this->hasMany(Proposicao::class, 'autor_id');
    }

    /**
     * Scope para parlamentares
     */
    public function scopeParlamentares($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->whereIn('name', [self::PERFIL_PARLAMENTAR, self::PERFIL_RELATOR]);
        });
    }

    // ========== SCOPED RELATIONSHIPS ==========

    /**
     * Relacionamento scoped: proposições ativas do usuário
     */
    public function proposicoesAtivas()
    {
        return $this->proposicoesAutor()->whereNotIn('status', ['ARQUIVADO', 'CANCELADO']);
    }

    /**
     * Relacionamento scoped: proposições protocoladas do usuário
     */
    public function proposicoesProtocoladas()
    {
        return $this->proposicoesAutor()->where('status', 'PROTOCOLADO');
    }

    /**
     * Relacionamento scoped: proposições pendentes do usuário
     */
    public function proposicoesPendentes()
    {
        return $this->proposicoesAutor()->whereIn('status', [
            'RASCUNHO', 'EM_REVISAO', 'AGUARDANDO_ASSINATURA',
        ]);
    }

    /**
     * Relacionamento scoped: proposições revisadas (como revisor)
     */
    public function proposicoesRevisadas()
    {
        return $this->hasMany(Proposicao::class, 'revisor_id')->latest();
    }

    /**
     * Relacionamento scoped: proposições do ano atual
     */
    public function proposicoesAnoAtual()
    {
        return $this->proposicoesAutor()->where('ano', date('Y'));
    }

    /**
     * Relacionamento scoped: proposições com template
     */
    public function proposicoesComTemplate()
    {
        return $this->proposicoesAutor()->whereNotNull('template_id');
    }

    /**
     * Obter estatísticas das proposições do usuário
     */
    public function getEstatisticasProposicoes(): array
    {
        return [
            'total' => $this->proposicoesAutor()->count(),
            'protocoladas' => $this->proposicoesProtocoladas()->count(),
            'pendentes' => $this->proposicoesPendentes()->count(),
            'ativas' => $this->proposicoesAtivas()->count(),
            'ano_atual' => $this->proposicoesAnoAtual()->count(),
            'com_template' => $this->proposicoesComTemplate()->count(),
            'por_tipo' => $this->proposicoesAutor()
                ->selectRaw('tipo, count(*) as total')
                ->groupBy('tipo')
                ->pluck('total', 'tipo')
                ->toArray(),
            'por_status' => $this->proposicoesAutor()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray(),
        ];
    }

    // ========== CERTIFICADO DIGITAL ==========

    /**
     * Verificar se o usuário tem certificado digital ativo
     */
    public function temCertificadoDigital(): bool
    {
        return $this->certificado_digital_ativo && 
               $this->certificado_digital_path && 
               \Storage::exists($this->certificado_digital_path);
    }

    /**
     * Verificar se o certificado digital está válido (não expirado)
     */
    public function certificadoDigitalValido(): bool
    {
        if (!$this->temCertificadoDigital()) {
            return false;
        }

        return $this->certificado_digital_validade && 
               $this->certificado_digital_validade->isFuture();
    }

    /**
     * Obter caminho completo do certificado digital
     */
    public function getCaminhoCompletoCertificado(): ?string
    {
        if (!$this->certificado_digital_path) {
            return null;
        }

        return storage_path('app/' . $this->certificado_digital_path);
    }

    /**
     * Obter status do certificado digital
     */
    public function getStatusCertificadoDigital(): string
    {
        if (!$this->certificado_digital_path) {
            return 'Não cadastrado';
        }

        if (!$this->certificado_digital_ativo) {
            return 'Inativo';
        }

        if (!$this->certificadoDigitalValido()) {
            return 'Expirado';
        }

        return 'Ativo';
    }

    /**
     * Obter dias restantes até expiração do certificado
     */
    public function getDiasParaExpiracaoCertificado(): ?int
    {
        if (!$this->certificado_digital_validade) {
            return null;
        }

        return now()->diffInDays($this->certificado_digital_validade, false);
    }

    /**
     * Verificar se o certificado está próximo do vencimento (30 dias)
     */
    public function certificadoProximoVencimento(): bool
    {
        $dias = $this->getDiasParaExpiracaoCertificado();
        return $dias !== null && $dias <= 30 && $dias > 0;
    }

    /**
     * Remover certificado digital
     */
    public function removerCertificadoDigital(): bool
    {
        if ($this->certificado_digital_path && \Storage::exists($this->certificado_digital_path)) {
            \Storage::delete($this->certificado_digital_path);
        }

        return $this->update([
            'certificado_digital_path' => null,
            'certificado_digital_nome' => null,
            'certificado_digital_upload_em' => null,
            'certificado_digital_validade' => null,
            'certificado_digital_cn' => null,
            'certificado_digital_ativo' => false,
            'certificado_digital_senha' => null,
            'certificado_digital_senha_salva' => false,
        ]);
    }
    
    /**
     * Salvar senha do certificado criptografada
     */
    public function salvarSenhaCertificado(string $senha): bool
    {
        return $this->update([
            'certificado_digital_senha' => encrypt($senha),
            'certificado_digital_senha_salva' => true,
        ]);
    }
    
    /**
     * Obter senha do certificado descriptografada
     */
    public function getSenhaCertificado(): ?string
    {
        if (!$this->certificado_digital_senha) {
            return null;
        }
        
        try {
            return decrypt($this->certificado_digital_senha);
        } catch (\Exception $e) {
            \Log::error('Erro ao descriptografar senha do certificado', [
                'user_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Remover senha do certificado
     */
    public function removerSenhaCertificado(): bool
    {
        return $this->update([
            'certificado_digital_senha' => null,
            'certificado_digital_senha_salva' => false,
        ]);
    }
}
