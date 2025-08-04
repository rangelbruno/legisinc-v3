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
        Schema::create('parecer_juridicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proposicao_id');
            $table->unsignedBigInteger('assessor_id');
            $table->enum('tipo_parecer', ['FAVORAVEL', 'CONTRARIO', 'COM_EMENDAS']);
            $table->text('fundamentacao');
            $table->text('conclusao');
            $table->text('emendas')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('data_emissao')->useCurrent();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('proposicao_id')->references('id')->on('proposicoes');
            $table->foreign('assessor_id')->references('id')->on('users');
            
            // Índices
            $table->index('proposicao_id');
            $table->index('assessor_id');
            $table->index('tipo_parecer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parecer_juridicos');
    }
};
