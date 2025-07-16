<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_permission_cache', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->string('permissions_hash', 64)->index();
            $table->json('cached_permissions');
            $table->timestamp('expires_at')->index();
            $table->timestamps();
            
            // Índices para performance
            $table->index(['expires_at', 'user_id'], 'idx_expires_user');
            $table->index(['permissions_hash', 'expires_at'], 'idx_hash_expires');
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Criar evento para limpeza automática de cache expirado (apenas MySQL)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                CREATE EVENT IF NOT EXISTS cleanup_expired_permission_cache
                ON SCHEDULE EVERY 1 HOUR
                DO
                DELETE FROM user_permission_cache WHERE expires_at < NOW()
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover evento (apenas MySQL)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("DROP EVENT IF EXISTS cleanup_expired_permission_cache");
        }
        
        Schema::dropIfExists('user_permission_cache');
    }
};