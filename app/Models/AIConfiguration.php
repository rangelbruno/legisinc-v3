<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class AIConfiguration extends Model
{
    use HasFactory;
    
    protected $table = 'ai_configurations';

    protected $fillable = [
        'name',
        'provider',
        'api_key',
        'model',
        'base_url',
        'max_tokens',
        'temperature',
        'custom_prompt',
        'priority',
        'is_active',
        'daily_token_limit',
        'cost_per_1k_tokens',
        'additional_parameters',
    ];

    protected $casts = [
        'temperature' => 'decimal:2',
        'cost_per_1k_tokens' => 'decimal:6',
        'is_active' => 'boolean',
        'last_test_success' => 'boolean',
        'additional_parameters' => 'array',
        'last_tested_at' => 'datetime',
        'last_reset_date' => 'date',
        'daily_tokens_used' => 'integer',
        'daily_token_limit' => 'integer',
        'max_tokens' => 'integer',
        'priority' => 'integer',
    ];

    protected $hidden = [
        'api_key', // Sempre ocultar a API key
    ];

    /**
     * Accessor/Mutator para criptografar/descriptografar a API key
     */
    protected function apiKey(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Crypt::decryptString($value) : null,
            set: fn ($value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeWithTokensAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('daily_token_limit')
              ->orWhereRaw('daily_tokens_used < daily_token_limit')
              ->orWhere('last_reset_date', '<', now()->toDateString());
        });
    }

    /**
     * Métodos auxiliares
     */

    /**
     * Verifica se a configuração pode ser usada (ativa e com tokens disponíveis)
     */
    public function canBeUsed(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Reset contador diário se necessário
        $this->resetDailyCounterIfNeeded();

        // Verifica limite diário
        if ($this->daily_token_limit && $this->daily_tokens_used >= $this->daily_token_limit) {
            return false;
        }

        return true;
    }

    /**
     * Reseta contador diário se necessário
     */
    public function resetDailyCounterIfNeeded(): void
    {
        if ($this->last_reset_date !== now()->toDateString()) {
            $this->update([
                'daily_tokens_used' => 0,
                'last_reset_date' => now()->toDateString(),
            ]);
        }
    }

    /**
     * Adiciona tokens usados ao contador
     */
    public function addTokensUsed(int $tokens): void
    {
        $this->resetDailyCounterIfNeeded();
        $this->increment('daily_tokens_used', $tokens);
    }

    /**
     * Calcula tokens restantes para hoje
     */
    public function getRemainingTokensAttribute(): ?int
    {
        if (!$this->daily_token_limit) {
            return null; // Sem limite
        }

        $this->resetDailyCounterIfNeeded();
        return max(0, $this->daily_token_limit - $this->daily_tokens_used);
    }

    /**
     * Retorna porcentagem de uso diário
     */
    public function getDailyUsagePercentageAttribute(): ?float
    {
        if (!$this->daily_token_limit) {
            return null;
        }

        $this->resetDailyCounterIfNeeded();
        return ($this->daily_tokens_used / $this->daily_token_limit) * 100;
    }

    /**
     * Verifica se o último teste foi bem-sucedido e recente
     */
    public function isHealthy(): bool
    {
        if (!$this->last_tested_at) {
            return false;
        }

        // Considera saudável se testado nas últimas 24 horas com sucesso
        return $this->last_test_success && 
               $this->last_tested_at->greaterThan(now()->subHours(24));
    }

    /**
     * Atualiza resultado do teste
     */
    public function updateTestResult(bool $success, ?string $error = null): void
    {
        $this->update([
            'last_tested_at' => now(),
            'last_test_success' => $success,
            'last_test_error' => $error,
        ]);
    }

    /**
     * Retorna configuração como array para uso na API
     */
    public function toApiConfig(): array
    {
        return [
            'provider' => $this->provider,
            'api_key' => $this->api_key, // Será descriptografada pelo accessor
            'model' => $this->model,
            'base_url' => $this->base_url,
            'max_tokens' => $this->max_tokens,
            'temperature' => (float) $this->temperature,
            'custom_prompt' => $this->custom_prompt,
            'additional_parameters' => $this->additional_parameters ?: [],
        ];
    }

    /**
     * Providers disponíveis
     */
    public static function getAvailableProviders(): array
    {
        return [
            'openai' => [
                'name' => 'OpenAI',
                'models' => ['gpt-4o', 'gpt-4o-mini', 'gpt-4-turbo', 'gpt-3.5-turbo'],
                'requires_api_key' => true,
                'default_base_url' => 'https://api.openai.com/v1',
                'cost_per_1k_tokens' => 0.01, // Estimativa
            ],
            'anthropic' => [
                'name' => 'Anthropic (Claude)',
                'models' => ['claude-3.5-sonnet', 'claude-3-opus', 'claude-3-haiku'],
                'requires_api_key' => true,
                'default_base_url' => 'https://api.anthropic.com/v1',
                'cost_per_1k_tokens' => 0.015,
            ],
            'google' => [
                'name' => 'Google (Gemini)',
                'models' => ['gemini-1.5-pro', 'gemini-1.5-flash', 'gemini-pro'],
                'requires_api_key' => true,
                'default_base_url' => 'https://generativelanguage.googleapis.com/v1beta',
                'cost_per_1k_tokens' => 0.0075,
            ],
            'local' => [
                'name' => 'Local (Ollama)',
                'models' => ['llama3.1', 'codellama', 'mistral', 'custom'],
                'requires_api_key' => false,
                'default_base_url' => 'http://localhost:11434',
                'cost_per_1k_tokens' => 0.0, // Gratuito
            ],
        ];
    }
}