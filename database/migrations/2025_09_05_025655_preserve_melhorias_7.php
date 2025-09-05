<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-05 02:56:55
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
                'hash_anterior' => '379228d1291f4c3e9d7572b027757dca24d6caa75c3104849f263ff64d6d091c',
                'hash_atual' => 'f9a67a52629902bef0c415a03b4a7778498d8dabec58ebe6d4f017119112cfc2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 176443,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '4f0ba82cf0d25fb56030399c76a838041ba9607362fd3d801fee3473e4fc42e4',
                'hash_atual' => '3677f9787a8f76354e5fcd05df52f50418f5be1a33ed239e6afb131d1b080eed',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 25353,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'cb52794d9fbb17b12bed7631655d9ad0e45719eec6eeaa96ee24d240afe3f1e1',
                'hash_atual' => '1d68e378f2acabc7d8b70f1e455d66ced7bf3e9ea2585068793d0a5ef042fe1b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '7b519e119749cfead3076359b34521aeba8df90b30cb259841b9cc1b12e67d09',
                'hash_atual' => '4621575ea679eb3a1b91e6f1b0a55b5d126e73799c7e3b63d52d02cfd24b3ee5',
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