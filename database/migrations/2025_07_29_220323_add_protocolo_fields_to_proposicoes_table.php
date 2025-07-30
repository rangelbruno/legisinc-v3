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
        Schema::table('proposicoes', function (Blueprint $table) {
            // Campos para protocolo
            $table->string('numero_protocolo')->nullable()->after('status');
            $table->timestamp('data_protocolo')->nullable()->after('numero_protocolo');
            $table->unsignedBigInteger('funcionario_protocolo_id')->nullable()->after('data_protocolo');
            $table->json('comissoes_destino')->nullable()->after('funcionario_protocolo_id');
            $table->text('observacoes_protocolo')->nullable()->after('comissoes_destino');
            $table->json('verificacoes_realizadas')->nullable()->after('observacoes_protocolo');
            
            // Foreign key para funcionario do protocolo
            $table->foreign('funcionario_protocolo_id')->references('id')->on('users');
            
            // Índice para consultas por número de protocolo
            $table->index('numero_protocolo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropForeign(['funcionario_protocolo_id']);
            $table->dropIndex(['numero_protocolo']);
            $table->dropColumn([
                'numero_protocolo',
                'data_protocolo', 
                'funcionario_protocolo_id',
                'comissoes_destino',
                'observacoes_protocolo',
                'verificacoes_realizadas'
            ]);
        });
    }
};