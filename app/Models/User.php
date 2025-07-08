<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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
        ];
    }
    
    /**
     * Constantes dos perfis do sistema parlamentar
     */
    public const PERFIL_ADMIN = 'ADMIN';
    public const PERFIL_LEGISLATIVO = 'LEGISLATIVO';
    public const PERFIL_PARLAMENTAR = 'PARLAMENTAR';
    public const PERFIL_RELATOR = 'RELATOR';
    public const PERFIL_ASSESSOR = 'ASSESSOR';
    public const PERFIL_CIDADAO_VERIFICADO = 'CIDADAO_VERIFICADO';
    public const PERFIL_PUBLICO = 'PUBLICO';
    
    /**
     * Hierarquia de perfis (maior número = mais privilégios)
     */
    public const HIERARQUIA_PERFIS = [
        self::PERFIL_PUBLICO => 1,
        self::PERFIL_CIDADAO_VERIFICADO => 2,
        self::PERFIL_ASSESSOR => 3,
        self::PERFIL_RELATOR => 4,
        self::PERFIL_PARLAMENTAR => 5,
        self::PERFIL_LEGISLATIVO => 6,
        self::PERFIL_ADMIN => 7,
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
     * Check if user has a specific role
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        $userRoles = \DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->where('model_has_roles.model_id', $this->id)
            ->pluck('roles.name');
            
        return $userRoles->intersect($roles)->isNotEmpty();
    }
    
    /**
     * Check if user has a specific permission
     */
    public function hasPermissionTo(string $permission): bool
    {
        $userRoles = \DB::table('model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->where('model_id', $this->id)
            ->pluck('role_id');

        if ($userRoles->isEmpty()) {
            return false;
        }

        return \DB::table('role_has_permissions')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->whereIn('role_has_permissions.role_id', $userRoles)
            ->where('permissions.name', $permission)
            ->exists();
    }
    
    /**
     * Get user's role names
     */
    public function getRoleNames()
    {
        return \DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->where('model_has_roles.model_id', $this->id)
            ->pluck('roles.name');
    }
    
    /**
     * Obter perfil formatado para exibição
     */
    public function getPerfilFormatado(): string
    {
        $perfil = $this->getRoleNames()->first();
        
        return match($perfil) {
            self::PERFIL_ADMIN => 'Administrador',
            self::PERFIL_LEGISLATIVO => 'Servidor Legislativo',
            self::PERFIL_PARLAMENTAR => 'Parlamentar',
            self::PERFIL_RELATOR => 'Relator',
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
        
        return match($perfil) {
            self::PERFIL_ADMIN => 'danger',
            self::PERFIL_LEGISLATIVO => 'primary',
            self::PERFIL_PARLAMENTAR => 'success',
            self::PERFIL_RELATOR => 'warning',
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
        if ($value) {
            return $value;
        }
        
        // Retornar iniciais se não tiver avatar
        $nomes = explode(' ', $this->name);
        $iniciais = '';
        foreach (array_slice($nomes, 0, 2) as $nome) {
            $iniciais .= strtoupper(substr($nome, 0, 1));
        }
        
        return $iniciais;
    }
    
    /**
     * Scope para usuários ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
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
}
