<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-05 12:08:21
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
                'hash_anterior' => '302788f5bf6309b439b144b8e6fa509e1c901b9aa968fbe0a4f39fccf4bd5d41',
                'hash_atual' => 'b9c6ed409dc49d340a4bc56ab1a76b94c6ad60e8028b5a20c9c83aff5cf2a325',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 176443,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '6de0663d3d3a39aa05a6157f8b3ba657943d84883408d9acf6803576c6fbfcdd',
                'hash_atual' => 'c762786700e03d430a6161041b2ed61eef4e2e2da555b6371ab5f05720af1a9c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29133,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '08fbb27d6a289b20c5497703617f0d288e2cd8d0479189eff71fb96ddeae1eaf',
                'hash_atual' => 'a8d94bd2ffc61ccad245718fe60f233acf7b473edcfde0c71ad5247ae170311c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '90485eb42cf9f97555d9cb7038b0795c2a5e362713749d4c5b766a002a31eb07',
                'hash_atual' => '613d7d489c47e648b38f952f8ea6e555787e08a80332e99457d802bbdc9a0977',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '8f73ebc4cb14f20e67e550f4e0f32b5cb29d7c4f7b1985af0be99476c3547985',
                'hash_atual' => '7bc265b7c3c37f518e7f66bd17c1df1a66b9b1d8dcb6c487573c2df8ecb384c0',
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