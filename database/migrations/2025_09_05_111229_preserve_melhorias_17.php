<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-05 11:12:29
     */
    public function up(): void
    {
        // Criar tabela para rastrear melhorias se não existir (PostgreSQL compatível)
        if (!Schema::hasTable('melhorias_tracking')) {
            Schema::create('melhorias_tracking', function (Blueprint $table) {
                $table->id();
                $table->string('arquivo', 500)->index();
                $table->text('hash_anterior')->nullable();
                $table->text('hash_atual');
                $table->string('tipo', 20)->default('modificado');
                $table->json('metadata')->nullable();
                $table->boolean('preservado')->default(false);
                $table->timestamps();
                
                $table->index('preservado');
                $table->index('created_at');
            });
        }

        // Registrar alterações detectadas
        $alteracoes = [            [
                'arquivo' => 'app/Http/Controllers/ProposicaoAssinaturaController.php',
                'hash_anterior' => '6850f99cdc5f4e6b1c7b64d292236da939d7df0ec27a25f4b4bea4d02ec9b6c8',
                'hash_atual' => '302788f5bf6309b439b144b8e6fa509e1c901b9aa968fbe0a4f39fccf4bd5d41',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 176443,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'd264e1021f63f2690bd21dd80f6228187ff4e1dd59972c6395c5957d2cd5a83b',
                'hash_atual' => '6de0663d3d3a39aa05a6157f8b3ba657943d84883408d9acf6803576c6fbfcdd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29023,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'ed066449dbdb49de5202007b7f9972a52579e0333620b9a96d7aea9b6e6e8f02',
                'hash_atual' => '08fbb27d6a289b20c5497703617f0d288e2cd8d0479189eff71fb96ddeae1eaf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '8aaba764e9769bf92859dad8309b15f512a08e9bbbfa9cfb43223b1afbe85025',
                'hash_atual' => '8f73ebc4cb14f20e67e550f4e0f32b5cb29d7c4f7b1985af0be99476c3547985',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ]        ];

        foreach ($alteracoes as $alteracao) {
            DB::table('melhorias_tracking')->updateOrInsert(
                ['arquivo' => $alteracao['arquivo']],
                $alteracao
            );
        }
    }

    public function down(): void
    {
        // Remover registros desta migration
        DB::table('melhorias_tracking')
          ->where('created_at', '>=', now()->subMinute())
          ->delete();
    }
};