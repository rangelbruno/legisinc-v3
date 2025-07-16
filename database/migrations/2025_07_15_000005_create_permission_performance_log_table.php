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
        Schema::create('permission_performance_log', function (Blueprint $table) {
            $table->id();
            $table->string('operation', 50)->index();
            $table->decimal('response_time_ms', 8, 3);
            $table->integer('cache_hits')->default(0);
            $table->integer('cache_misses')->default(0);
            $table->integer('database_queries')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            
            // Índices para análise de performance
            $table->index(['operation', 'created_at'], 'idx_operation_date');
            $table->index(['response_time_ms', 'created_at'], 'idx_response_time');
            $table->index(['created_at'], 'idx_created_date');
        });

        // Trigger para limpeza automática após 30 dias (apenas MySQL)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                CREATE EVENT IF NOT EXISTS cleanup_old_performance_log
                ON SCHEDULE EVERY 1 DAY
                DO
                DELETE FROM permission_performance_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("DROP EVENT IF EXISTS cleanup_old_performance_log");
        }
        Schema::dropIfExists('permission_performance_log');
    }
};