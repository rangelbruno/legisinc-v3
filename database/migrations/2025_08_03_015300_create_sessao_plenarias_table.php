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
        Schema::create('sessao_plenarias', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim')->nullable();
            $table->enum('status', ['AGENDADA', 'EM_ANDAMENTO', 'FINALIZADA', 'CANCELADA'])->default('AGENDADA');
            $table->text('observacoes')->nullable();
            $table->unsignedBigInteger('criado_por');
            $table->timestamps();
            
            // Foreign key
            $table->foreign('criado_por')->references('id')->on('users');
            
            // Índices
            $table->index('data');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessao_plenarias');
    }
};
