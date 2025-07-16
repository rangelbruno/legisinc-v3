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
        Schema::create('permission_access_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('screen_route', 100)->index();
            $table->string('action', 20)->index();
            $table->enum('status', ['granted', 'denied'])->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            
            // Índices para performance
            $table->index(['user_id', 'status', 'created_at'], 'idx_user_status_date');
            $table->index(['screen_route', 'status', 'created_at'], 'idx_route_status_date');
            $table->index(['status', 'created_at'], 'idx_status_date');
            $table->index(['ip_address', 'created_at'], 'idx_ip_date');
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Particionamento comentado - pode ser adicionado posteriormente se necessário
        // DB::statement('ALTER TABLE permission_access_log PARTITION BY RANGE (UNIX_TIMESTAMP(created_at))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_access_log');
    }
};