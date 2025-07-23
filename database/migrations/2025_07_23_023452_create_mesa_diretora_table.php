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
        Schema::create('mesa_diretora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parlamentar_id')->constrained('parlamentars')->onDelete('cascade');
            $table->string('cargo_mesa', 100);
            $table->date('mandato_inicio');
            $table->date('mandato_fim');
            $table->enum('status', ['ativo', 'finalizado'])->default('ativo');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->index(['parlamentar_id']);
            $table->index(['status']);
            $table->index(['mandato_inicio', 'mandato_fim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesa_diretora');
    }
};
