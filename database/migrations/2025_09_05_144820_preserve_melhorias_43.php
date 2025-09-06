<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-05 14:48:20
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
                'hash_anterior' => 'bafab60ec820b0ffb532eb078211b186c96844ddd9a1cdd2039b74b9a616e890',
                'hash_atual' => '904339e08fcf32ef33fe360e176e3aaac0d5c983ec886d90f700958f97297d8b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 176443,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '42c31994279db40265fc0c5bf1c7a0c617724de02aa62866f8ede05288d1f523',
                'hash_atual' => '18a157fe26eeb1bab0f49f1c1f5a6ad7b26b36d0ec4824e4bf5e2066e1933ddf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29133,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '1d574e85cd7df49781e65dd3de22b257b13d61858ba5cfcb21a49e8f5eb3ed23',
                'hash_atual' => 'b78714a721e31179f197ce2b9c1eb417287ffacdfcf5bf662433bb725813f03b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '6720e0780afdfb5589c5099fb479b714c6a027ba4b208b62b6a7782a1e2f87e9',
                'hash_atual' => '4f6318a24b61b8ea9da026bce5f57242ccdbb77beee80881f7d6d8d4b34b77f5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'eb6eb2d7365245b1bd0d27c795d46cd966980d55a313e14eec9e5288add1d6f8',
                'hash_atual' => 'a7b68aea2690636a9ea04e4d3e39b6d22f77de4476ef2b27b24116e4a2d69d26',
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