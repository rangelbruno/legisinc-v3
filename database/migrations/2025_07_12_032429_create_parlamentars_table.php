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
        Schema::create('parlamentars', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('partido', 50);
            $table->string('cargo', 100);
            $table->enum('status', ['ativo', 'licenciado', 'inativo'])->default('ativo');
            $table->string('email')->unique();
            $table->string('telefone', 20);
            $table->date('data_nascimento');
            $table->string('profissao', 100)->nullable();
            $table->string('escolaridade', 100)->nullable();
            $table->json('comissoes')->nullable();
            $table->json('mandatos')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'partido']);
            $table->index('nome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parlamentars');
    }
};
