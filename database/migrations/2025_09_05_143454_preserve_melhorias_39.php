<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-05 14:34:54
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
                'hash_anterior' => '34ca0d99b4618646245fdb99cea15a7781ae198805e0a12448614f2cab2b88a3',
                'hash_atual' => '42199fb8fe966b9329825acb7f6b11d2e3aa90bddaf01c4bfeead660a76ba5c5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 176443,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '8a33011a7dd90b38e0bf8d9d0372324eb1e816dcd1e90616fa41c647217705c6',
                'hash_atual' => '11ea23fef1ecf99d7f2e3cf3370f8f083f3728db389eec0dae14c484ace628e7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29133,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'd9c1cd0e10f626c81f69a9888dafe32883e428a8888e0ab6c6b7cbdf9f141646',
                'hash_atual' => '2ac8c726d19f9e261db698a032a3d92e1009e9fbc5775a5cff6125a8e41e08a8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'fd83798629829f8e51dcb2f17045e2abe9fd376cc60ced3d1f3ab8962b4e0134',
                'hash_atual' => 'c85e59bf3a62c250cffc561fbe9a474ad68ee15b0da8feefe4a3a84de15e16e8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'adb865ada3816ff6fe4666b20110d3de5ce86ca930e289f44d8b7d2ffe7dbdcc',
                'hash_atual' => '0ef5da4c04383e3d2cdfcc8dd75e0ad40c683b19c39321135abe72b52ed125af',
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