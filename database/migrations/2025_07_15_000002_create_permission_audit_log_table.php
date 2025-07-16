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
        Schema::create('permission_audit_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->enum('action', ['grant', 'revoke', 'modify', 'reset'])->index();
            $table->string('permission_type', 50)->index();
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            
            // Índices para performance e consultas
            $table->index(['user_id', 'action'], 'idx_user_action');
            $table->index(['admin_user_id', 'created_at'], 'idx_admin_audit');
            $table->index(['ip_address', 'created_at'], 'idx_ip_tracking');
            $table->index(['permission_type', 'created_at'], 'idx_permission_type');
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('admin_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_audit_log');
    }
};