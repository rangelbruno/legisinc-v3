<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GeneratedModule extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'table_name',
        'fields_config',
        'relationships',
        'business_logic',
        'has_crud',
        'has_permissions',
        'icon',
        'color',
        'active',
        'menu_config',
        'created_by',
        'generated_at',
        'generated_files',
        'status',
        'generation_log',
    ];

    protected $casts = [
        'fields_config' => 'array',
        'relationships' => 'array',
        'menu_config' => 'array',
        'generated_files' => 'array',
        'has_crud' => 'boolean',
        'has_permissions' => 'boolean',
        'active' => 'boolean',
        'generated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function generateSlug(): void
    {
        $this->slug = Str::slug($this->name);
    }

    public function generateTableName(): void
    {
        $this->table_name = Str::snake(Str::plural($this->name));
    }

    public function isGenerated(): bool
    {
        return $this->status === 'generated';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function hasError(): bool
    {
        return $this->status === 'error';
    }

    public function markAsGenerated(array $generatedFiles): void
    {
        $this->update([
            'status' => 'generated',
            'generated_at' => now(),
            'generated_files' => $generatedFiles,
        ]);
    }

    public function markAsError(string $errorLog): void
    {
        $this->update([
            'status' => 'error',
            'generation_log' => $errorLog,
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($module) {
            if (empty($module->slug)) {
                $module->generateSlug();
            }
            if (empty($module->table_name)) {
                $module->generateTableName();
            }
        });

        static::updating(function ($module) {
            if ($module->isDirty('name')) {
                $module->generateSlug();
                $module->generateTableName();
            }
        });
    }
}
