<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-05 03:38:00
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
                'hash_anterior' => 'cb0ecdc76c5afe0111474949054c4fd76e4574ed9f65b13bac0e635b5e5fffca',
                'hash_atual' => '784a120e951849432f1758a2b8587bb49fdf71413017fc72ca182e9adab53cc8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 176443,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'bdf782c10c5ed7f70e98cee180d951cb7c0ab7f5a68ec4406fb827de01b9eb42',
                'hash_atual' => '4a6f2d08a16153cca5a73207e1e00fdd437899a089464504753f37c27ac90867',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 25353,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'f996272bc6dc0dfcea333f72d1b16a39fd2ac63dd5378f2d8dc9ae861597ba03',
                'hash_atual' => '157fa67be49fdbb4ab8737f23d2ca30df034668ff9b0fb88261497be7f52b5a1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'bf01cfe18e17a5047af939d3b32591c5ba64fbf4ede8215eb72804abec11c2af',
                'hash_atual' => 'c7ba5b09868a0443a9e5bf1d63b799eb642e8edb29cd795e7327875021c8db5f',
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