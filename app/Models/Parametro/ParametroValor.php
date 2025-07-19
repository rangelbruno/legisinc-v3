<?php

namespace App\Models\Parametro;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParametroValor extends Model
{
    use HasFactory;

    protected $table = 'parametros_valores';

    protected $fillable = [
        'campo_id',
        'valor',
        'tipo_valor',
        'user_id',
        'valido_ate',
    ];

    protected $casts = [
        'campo_id' => 'integer',
        'user_id' => 'integer',
        'valido_ate' => 'datetime',
    ];

    // Relacionamentos
    public function campo(): BelongsTo
    {
        return $this->belongsTo(ParametroCampo::class, 'campo_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeValidos($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('valido_ate')
              ->orWhere('valido_ate', '>', now());
        });
    }

    public function scopeExpirados($query)
    {
        return $query->where('valido_ate', '<', now());
    }

    public function scopePorCampo($query, int $campoId)
    {
        return $query->where('campo_id', $campoId);
    }

    public function scopePorUsuario($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecentes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getValorFormatadoAttribute(): mixed
    {
        if (is_null($this->valor)) {
            return null;
        }

        return $this->formatarValor($this->valor);
    }

    public function getValorDisplayAttribute(): string
    {
        $valor = $this->valor_formatado;
        
        if (is_null($valor)) {
            return '-';
        }

        switch ($this->tipo_valor) {
            case 'boolean':
                return $valor ? 'Sim' : 'Não';
                
            case 'date':
                return $valor instanceof \DateTime ? $valor->format('d/m/Y') : $valor;
                
            case 'datetime':
                return $valor instanceof \DateTime ? $valor->format('d/m/Y H:i:s') : $valor;
                
            case 'array':
                return is_array($valor) ? implode(', ', $valor) : $valor;
                
            case 'json':
                return is_array($valor) ? json_encode($valor, JSON_PRETTY_PRINT) : $valor;
                
            default:
                return (string) $valor;
        }
    }

    // Métodos
    public function formatarValor(mixed $valor): mixed
    {
        if (is_null($valor)) {
            return null;
        }

        switch ($this->tipo_valor) {
            case 'integer':
                return (int) $valor;
                
            case 'decimal':
                return (float) $valor;
                
            case 'boolean':
                return (bool) $valor;
                
            case 'date':
                return $valor instanceof \DateTime ? $valor : new \DateTime($valor);
                
            case 'datetime':
                return $valor instanceof \DateTime ? $valor : new \DateTime($valor);
                
            case 'array':
                if (is_string($valor)) {
                    return explode(',', $valor);
                }
                return is_array($valor) ? $valor : [$valor];
                
            case 'json':
                return is_string($valor) ? json_decode($valor, true) : $valor;
                
            default:
                return (string) $valor;
        }
    }

    public function isValido(): bool
    {
        return is_null($this->valido_ate) || $this->valido_ate > now();
    }

    public function isExpirado(): bool
    {
        return !$this->isValido();
    }

    public function expire(): void
    {
        $this->valido_ate = now();
        $this->save();
    }

    public function getCacheKey(): string
    {
        return "parametro_valor:{$this->campo_id}";
    }

    public function defineValor(mixed $valor, string $tipo = 'string'): void
    {
        $this->valor = $valor;
        $this->tipo_valor = $tipo;
    }

    public function definePeriodoValidade(?\DateTime $validoAte = null): void
    {
        $this->valido_ate = $validoAte;
    }
}