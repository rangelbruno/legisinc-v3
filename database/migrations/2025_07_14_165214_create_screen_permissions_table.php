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
        Schema::create('screen_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role_name'); // Nome do perfil (ADMIN, PARLAMENTAR, etc)
            $table->string('screen_route'); // Rota da tela (parlamentares.index, projetos.create, etc)
            $table->string('screen_name'); // Nome amigável da tela
            $table->string('screen_module'); // Módulo da tela (parlamentares, projetos, usuarios, etc)
            $table->boolean('can_access')->default(false); // Se pode acessar a tela
            $table->timestamps();
            
            // Índices
            $table->index(['role_name', 'screen_route']);
            $table->unique(['role_name', 'screen_route']); // Um perfil só pode ter uma configuração por tela
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screen_permissions');
    }
};
