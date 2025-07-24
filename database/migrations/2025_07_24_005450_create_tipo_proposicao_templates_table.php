<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tipo_proposicao_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_proposicao_id')
                  ->unique()
                  ->constrained('tipo_proposicoes')
                  ->cascadeOnDelete();
            $table->string('document_key')->unique();
            $table->string('arquivo_path')->nullable();
            $table->json('variaveis')->nullable();
            $table->boolean('ativo')->default(true);
            $table->foreignId('updated_by')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['tipo_proposicao_id', 'ativo']);
            $table->index('document_key');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipo_proposicao_templates');
    }
};