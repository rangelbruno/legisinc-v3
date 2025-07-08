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
        Schema::table('users', function (Blueprint $table) {
            // Campos específicos do sistema parlamentar
            $table->string('documento')->nullable()->after('email'); // CPF ou documento
            $table->string('telefone')->nullable()->after('documento');
            $table->date('data_nascimento')->nullable()->after('telefone');
            $table->string('profissao')->nullable()->after('data_nascimento');
            $table->string('cargo_atual')->nullable()->after('profissao'); // Cargo no sistema
            $table->string('partido')->nullable()->after('cargo_atual'); // Apenas para parlamentares
            $table->json('preferencias')->nullable()->after('partido'); // Configurações do usuário
            $table->boolean('ativo')->default(true)->after('preferencias');
            $table->timestamp('ultimo_acesso')->nullable()->after('ativo');
            $table->string('avatar')->nullable()->after('ultimo_acesso'); // URL do avatar
            
            // Índices para performance
            $table->index('documento');
            $table->index('partido');
            $table->index('ativo');
            $table->index('ultimo_acesso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['documento']);
            $table->dropIndex(['partido']);
            $table->dropIndex(['ativo']);
            $table->dropIndex(['ultimo_acesso']);
            
            $table->dropColumn([
                'documento',
                'telefone',
                'data_nascimento',
                'profissao',
                'cargo_atual',
                'partido',
                'preferencias',
                'ativo',
                'ultimo_acesso',
                'avatar'
            ]);
        });
    }
};