<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-05 03:42:29
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
                'hash_anterior' => '784a120e951849432f1758a2b8587bb49fdf71413017fc72ca182e9adab53cc8',
                'hash_atual' => '6850f99cdc5f4e6b1c7b64d292236da939d7df0ec27a25f4b4bea4d02ec9b6c8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 176443,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '4a6f2d08a16153cca5a73207e1e00fdd437899a089464504753f37c27ac90867',
                'hash_atual' => 'd264e1021f63f2690bd21dd80f6228187ff4e1dd59972c6395c5957d2cd5a83b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 25353,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '157fa67be49fdbb4ab8737f23d2ca30df034668ff9b0fb88261497be7f52b5a1',
                'hash_atual' => 'ed066449dbdb49de5202007b7f9972a52579e0333620b9a96d7aea9b6e6e8f02',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'c7ba5b09868a0443a9e5bf1d63b799eb642e8edb29cd795e7327875021c8db5f',
                'hash_atual' => '8aaba764e9769bf92859dad8309b15f512a08e9bbbfa9cfb43223b1afbe85025',
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