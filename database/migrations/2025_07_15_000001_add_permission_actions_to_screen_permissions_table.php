<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('screen_permissions', function (Blueprint $table) {
            // Verificar se as colunas já existem antes de adicionar
            if (!Schema::hasColumn('screen_permissions', 'can_create')) {
                $table->boolean('can_create')->default(false)->after('can_access');
            }
            if (!Schema::hasColumn('screen_permissions', 'can_edit')) {
                $table->boolean('can_edit')->default(false)->after('can_create');
            }
            if (!Schema::hasColumn('screen_permissions', 'can_delete')) {
                $table->boolean('can_delete')->default(false)->after('can_edit');
            }
        });

        // Adicionar índices separadamente para evitar conflitos
        try {
            Schema::table('screen_permissions', function (Blueprint $table) {
                $table->index(['role_name', 'screen_module'], 'idx_role_module');
            });
        } catch (\Exception $e) {
            // Índice já existe
        }

        try {
            Schema::table('screen_permissions', function (Blueprint $table) {
                $table->index(['screen_route', 'can_access'], 'idx_route_access');
            });
        } catch (\Exception $e) {
            // Índice já existe
        }

        try {
            Schema::table('screen_permissions', function (Blueprint $table) {
                $table->index([
                    'screen_module', 'can_access', 'can_create', 'can_edit', 'can_delete'
                ], 'idx_module_permissions');
            });
        } catch (\Exception $e) {
            // Índice já existe
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('screen_permissions', function (Blueprint $table) {
            // Remover índices se existirem
            try {
                $table->dropIndex('idx_role_module');
            } catch (\Exception $e) {
                // Índice não existe
            }
            
            try {
                $table->dropIndex('idx_route_access');
            } catch (\Exception $e) {
                // Índice não existe
            }
            
            try {
                $table->dropIndex('idx_module_permissions');
            } catch (\Exception $e) {
                // Índice não existe
            }
            
            // Remover colunas se existirem
            if (Schema::hasColumn('screen_permissions', 'can_create')) {
                $table->dropColumn('can_create');
            }
            if (Schema::hasColumn('screen_permissions', 'can_edit')) {
                $table->dropColumn('can_edit');
            }
            if (Schema::hasColumn('screen_permissions', 'can_delete')) {
                $table->dropColumn('can_delete');
            }
        });
    }
};