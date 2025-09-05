<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-05 14:15:33
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
                'hash_anterior' => '56c3d0efa6fe08870825a7b172c3e324292521390b47e73e1dd072d2a7bba921',
                'hash_atual' => 'a9381f4966b0b05104e3ecd07184e4552c979f5c6724745dc029baa89c3091a8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 176443,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '6161b074dba12bfe7a75f4009ce0075d7df36eef4318d7bf775bfc9ad4f6d8b7',
                'hash_atual' => 'c9e90af9134e2821e4babfc7cd4dc45c6c054f68e2768ff1a59df8fe94e6bd56',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29133,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '05c2316cc771862c860eb71151ba174ca6191558cd46f44eeff5d2021746c1ad',
                'hash_atual' => '2d6a26e85edd88c453beb7ec204266aff0772b494001321ddc7859ce53718c69',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'a0981bb3307c7117eb31864629daba616086d79a397a734c69d87566be385924',
                'hash_atual' => '3d9e0fe64b1fb17dce23efc92137d43ff68984f693504be94bb44394573f749c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '2595b1806581edd70110fe11a696275d49fa4330fb2d6fa6081182450d571bc2',
                'hash_atual' => 'fb1c49da170d6f4db023a91f26bb327e691565ffff665fdc2ef63cfa8db3087a',
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