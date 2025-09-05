<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-05 13:56:46
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
                'hash_anterior' => 'ccf956924395586ba26e5709dce07537465b218030391049b9c040ac774c87e2',
                'hash_atual' => '56c3d0efa6fe08870825a7b172c3e324292521390b47e73e1dd072d2a7bba921',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 176443,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '7f8b674068dbb9a39a5e483d66546b108b77a28a3ce5abf0212ea0ac8bed7c2d',
                'hash_atual' => '6161b074dba12bfe7a75f4009ce0075d7df36eef4318d7bf775bfc9ad4f6d8b7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29133,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '3cecadcafb77c0db997bed3e8dea76bddfe7146f3675b345b3f675a0a48e6787',
                'hash_atual' => '05c2316cc771862c860eb71151ba174ca6191558cd46f44eeff5d2021746c1ad',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '7526e898e6fd9ec581e22259ad05a3fedf505fb24bdfc48f79f20b6529a898fd',
                'hash_atual' => 'a0981bb3307c7117eb31864629daba616086d79a397a734c69d87566be385924',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'ac84dee10d9fa78ec8bcd6ed1edc1f81f2cee976b4e86ee4690a4fb56f09317b',
                'hash_atual' => '2595b1806581edd70110fe11a696275d49fa4330fb2d6fa6081182450d571bc2',
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