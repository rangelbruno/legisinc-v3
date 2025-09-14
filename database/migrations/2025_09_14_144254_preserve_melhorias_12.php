<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-14 14:42:54
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
                'hash_anterior' => '6725784407bdf5c311a6a492e22f13db62449d5fcde1b51dcd824c6eac3c94fc',
                'hash_atual' => 'a77abfc859c31abe5e53ac9c3436d00411c52d66a1c29710cbd0b41aa21ef859',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194760,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '5a36a75939008e304d4ff14be5ed3a892600e472c1454cf01b343a940baf6391',
                'hash_atual' => '6afd6b4713315173c38dd828049aec0228df423aa6af9bfbc5e93dc6bfa5e9c9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'dd72235842a14ff5027430e0e59381a286bb3eaf1001b81a398c4671ca92a876',
                'hash_atual' => 'f88ac4ce582a4bd9a577a332d9aadd4581d79ba20384771cb3ac1ef383a5d9b6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '9af8ad9ce2994c0c1d730f567fdb97307475d08db4749c3f83ec97d234857e1b',
                'hash_atual' => '9c8fad2fa9db3c58cc7944bd8f6ec124b9924e428ebbff48871cfc88c953f13e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '0ae4b21a21c8bfaaea2761fc151186d6b6a7c0ba128dc27f6ee2c5963d355e70',
                'hash_atual' => 'd1c524ebeb952f38a41e2f99f369697d7960d3e2c6928179a218222da3193a1f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '6c63bc8a8e975f38b95b1335d3dff517f3b4034975cef969e0bd1cae2b0275d2',
                'hash_atual' => '7d0495527f508189c60096ae327555fc35d8c3d48bcf0dde692b48b99703509c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '244ffef6a59a1f638c33ac6665f4f9847ec5dcb4e878152beb5da4ee4acacdd8',
                'hash_atual' => '07877304c98ec08b6afa3ddcef7e63d2a13583f98c94a3c470fbad4873acbabb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '513059dfc7302a18b91e9d05e891b1fe6b6ff0186eb128e3749fb5c15149af0a',
                'hash_atual' => '11d681cd1108b11d2f5f96cfa5f34a8cf7094d10770ce1282bc3f21e3b253449',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '5debf01de75517c1776528d88857f95033bab8d6542834784a0faadb6e8c2b57',
                'hash_atual' => 'ae622cae9e99740a7a967c36ef63ae6fc32956eadfff3ea04c8eb503e87a31d7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '338dcefa1e2fe615647482cfa4aa09f5d97c7eca9c5a74f5961af75d862eed42',
                'hash_atual' => 'd67d3b5754b75c73976267a7bbf609c35a2bf25dad06f3800120e68226e3760e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'adc763bb3aed51321960e3583c6d4c846f4650a0910ad2316408e927d9e8fb62',
                'hash_atual' => '41b1b6c99d4448d80fa359ba790fea4a70fe9ce6851d2b2910647c36ee90b9e9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'f9c338ed1c18fbd851715f81e6e51076f2ae5acbfcb4854f0364e1ebfb84d52d',
                'hash_atual' => 'bd16bd868173bd5b6042728e445b2d23c0763dc2066139b856ac77bc1e05ca5d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'e3b68f9da10a15b382e5050f42fded9bf0f40553461ecb03d8d6d8a13466ac7a',
                'hash_atual' => '16f0694f5db67fb7434983ebe56f5ae8252583d4f8432c6b39bc22bf7e1f251d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'b2a63ce4e1b866b0b421d4c8123b38475a9e3041e5d3983ee62190a6d6a3eb5c',
                'hash_atual' => '18544b17a6cbc082d4bd0afb3a09f4126dc0b07724d144790a2abdd9d1b9bc22',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '330c2ad4ebf0796fb5bfc1451e2947dad94c7897b24f550a45da7d008d87345a',
                'hash_atual' => '11dc368b6757b6c9a5dee475c32de64e7383dc51600ecbb37dd314239d8c4f13',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'c2aa1d904e93dd6dd2f04b07c0f22b6dcdf423285c71246ae5b40ca35fbbf0e7',
                'hash_atual' => '225338af6f617a00a71cb4c3e87d4ba42cc21c76e176b1c419dbc6e2a5e63179',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'afe94a1067339c6b09a77a4ce39a0a2423e532a47bde2fa72871af868c9a95a5',
                'hash_atual' => '4a81d42d3a5fcfa89b5a05293df82ea07e092db216e2128d4fae9d07f562e4d9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'e03be2dc0061968e44939cd9e2d75edb16b7f5c196a607475b0ca266ef9fbb1a',
                'hash_atual' => '1fdcf319d6044bcaa029c07646dcf84c29e06a5a4ee1f3fae8691b6050f1f6fe',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '89e78a90df178ac221fefbec7be974b067cf85225456dcf931a9041cb377cad2',
                'hash_atual' => '2f4d7f64ed6a619d9f7bfb3ee245a048b7bbf7a23adea4bc49e594224975aee4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '62d8b01adb5e279e539a5ab99aa6d316aa67a5db4ba0e98bd74f9e146c0c2a65',
                'hash_atual' => 'a282e643a321e7c4bd3116ba3d7024a11854074c47731775a4aaa2c5dd068799',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '980b4b24c763f2123517d728d889612ed0bf47b3020d1e0215189fb4c0a04064',
                'hash_atual' => '26347bb3d2c9207c77b17f95c265018d37fa3dab0143dbaf5299157e9f39d05f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'a7b09831ff95c72995d6f990196906cbf381ee35b0a613e60e588365b7af2896',
                'hash_atual' => '3015b21ccfdcea8d87fe870b40bf2e77fdab4de7d92d8ad43c29bd9706cf272d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'd019548a9dc0122a3c21b20dd790b4a734156dba15c98f4609fc74d38278a585',
                'hash_atual' => '7d9b1f02b40675175503021ca845f348d2f910afe6f71160f7452de73f311736',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'd5fe0900f788adc9f8771577f09a694bdd8c1a86d7f49478f3ff1b0193a8beb8',
                'hash_atual' => 'c510144e5d580cdb0e73b5f56af5f6f022e7e0fca03a6a8bd5bc2945e1950a33',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'a8c31d3a7d7979badfae5c04826999a5184af7cc98846d6629461557168cb6d6',
                'hash_atual' => '420530e1553e763b29243e954f6910caf8aed35b199a23658e4da4591bc68152',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '112af07c1d0c72857d62e24fcb84cf1d53202ef63625cc3ae4d490f93778d1ed',
                'hash_atual' => '9849925995255673342208f1508e73b5af2220c344c88481ceb130def966c6dc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '4ed25803970a7f951fc4ef2e58b6c89a064ed05333998d6de892e865290c1c95',
                'hash_atual' => '8ea82d3e20a097b296435874d0b806b5207b2ad0ef289e379dfcb308cdb4c28c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '6222907fc41f741df58d38821351029b961c47e76cc098d0880107990ae82905',
                'hash_atual' => '37e079da025991ca24d714bd7b4fc64705b3384ee05939168804c9b7642458a0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'db1232df00f9f94eae4734102cf56de088b2aa1be2eef03a0254445f0f46ab83',
                'hash_atual' => '1d42d0f411646015141624ef4447c9d4fa267e0b6e9d9e18dd4a9d912d00f6b7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '56d3e1af5cb9b20048aedc0469f64558d27c032b2967ae72fb9ac5483c1715ae',
                'hash_atual' => 'e0e6ab40fe5ceff64018711c4a4b28b49b5429b3cc25e0d427da06a54dbcc9db',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '9db73d80cab221ad9fc67b6774134c2c01b04e26b75cd9614e95f34eddf708d1',
                'hash_atual' => '10973762d5cb52bb2d1e41acba14ec862d4b05a8baacb9e03d22119a8524c16f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'c83ca8b2b5388b2c61af351cd93683a3fd8019fde0a5db21b20809fa47ac6373',
                'hash_atual' => '37a66424ee7b9fa988de4a97f1a84efe857c0e2088550907c5ba5d3527b41e8b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '3b8febcbb4e1e91df0cd6e43a5661c9325064212211c0782dd456e9a52399dc2',
                'hash_atual' => '59ffa81b85db9c4745977faf23bb6952b6b3ed210ed3a688ce59fd4e062e3274',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 25889,
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