<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-14 14:17:03
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
                'hash_anterior' => '659e00937c49c4034818e98f8b911ff08cccc3cf8919b68a95b2348aa2fb9a68',
                'hash_atual' => '6725784407bdf5c311a6a492e22f13db62449d5fcde1b51dcd824c6eac3c94fc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194760,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'e983e731fe0f8d4917b19fb1e35d204e2aca17eb4f22b8a7df1b4f6b72b621d1',
                'hash_atual' => '5a36a75939008e304d4ff14be5ed3a892600e472c1454cf01b343a940baf6391',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'da891a2e9072fabbee507b5343439465c477f8db994d7e472c3a0d5261d95795',
                'hash_atual' => 'dd72235842a14ff5027430e0e59381a286bb3eaf1001b81a398c4671ca92a876',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '8cea6a0ed80e7e6ba6983089dd088bbc0b39b434a7ea504a51bc778f1c968227',
                'hash_atual' => '9af8ad9ce2994c0c1d730f567fdb97307475d08db4749c3f83ec97d234857e1b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '93d5a870c5c3703ab49a480ab05a3bec3795d21f7785af5f10943c29611e2968',
                'hash_atual' => '0ae4b21a21c8bfaaea2761fc151186d6b6a7c0ba128dc27f6ee2c5963d355e70',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '1166a6e13665ec0092db964955b18cdd50420b9a5ba8ca40983643bf21272e08',
                'hash_atual' => '6c63bc8a8e975f38b95b1335d3dff517f3b4034975cef969e0bd1cae2b0275d2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'ea9b2f18c5701c43cb675d9efe1cdca0e56b9534db5609725438c6f0911076d3',
                'hash_atual' => '244ffef6a59a1f638c33ac6665f4f9847ec5dcb4e878152beb5da4ee4acacdd8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '938ce21f31880720c999c4ecd46436595f74ff064721e78805c34f20c2fd9ac7',
                'hash_atual' => '513059dfc7302a18b91e9d05e891b1fe6b6ff0186eb128e3749fb5c15149af0a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '609955e2aa08f75f55a3179d981aab4b36b0b36353b72b940bd48ed0e4fd9fb9',
                'hash_atual' => '5debf01de75517c1776528d88857f95033bab8d6542834784a0faadb6e8c2b57',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '02a4e504a556c8d2005a39e278bf8943447396cf2d1c5e2df49adbbf5ad872cc',
                'hash_atual' => '338dcefa1e2fe615647482cfa4aa09f5d97c7eca9c5a74f5961af75d862eed42',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'c1f21aa5b807144c506d837573c1bca3c0c57b46ce4bd1eddb9bd045b43ea14c',
                'hash_atual' => 'adc763bb3aed51321960e3583c6d4c846f4650a0910ad2316408e927d9e8fb62',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'c51638a2ca70f19346ae06c7db725d1364bb4ae757d8b58b8feb1c38e68d06c2',
                'hash_atual' => 'f9c338ed1c18fbd851715f81e6e51076f2ae5acbfcb4854f0364e1ebfb84d52d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'a3e0a9f61c22a7a2e4aff32717411c7217041210136fce257fb7b5115b581871',
                'hash_atual' => 'e3b68f9da10a15b382e5050f42fded9bf0f40553461ecb03d8d6d8a13466ac7a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '60129681b162d30543e8b2c6e9d08ad29982d1bef32dd84d8b565b31e6ad81b5',
                'hash_atual' => 'b2a63ce4e1b866b0b421d4c8123b38475a9e3041e5d3983ee62190a6d6a3eb5c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '8bcf80fdc4e7aea2ab222c878404e94de165d531113c608104857d0d4f310c53',
                'hash_atual' => '330c2ad4ebf0796fb5bfc1451e2947dad94c7897b24f550a45da7d008d87345a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'b3343e9155e3f236659b749526a41de4f5aa47bc13b31d3b55a8dad6b637f8dc',
                'hash_atual' => 'c2aa1d904e93dd6dd2f04b07c0f22b6dcdf423285c71246ae5b40ca35fbbf0e7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'ff4e3b8a2d7bbd11c6d81899c1f1f4e356c66c325145222e3785f18cc75fc7d4',
                'hash_atual' => 'afe94a1067339c6b09a77a4ce39a0a2423e532a47bde2fa72871af868c9a95a5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '2aa37418521143a0a2d085fd3344197339df7e2a01ce0c04dfe34361e7ece0d1',
                'hash_atual' => 'e03be2dc0061968e44939cd9e2d75edb16b7f5c196a607475b0ca266ef9fbb1a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'd4c209d5c6541ff77ce068a7712490c6b3288b6a0edf67b51485704964d8831f',
                'hash_atual' => '89e78a90df178ac221fefbec7be974b067cf85225456dcf931a9041cb377cad2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'c57152610ff376b3c9044716278ce294711e44310affae9391485b36f8fb02f7',
                'hash_atual' => '62d8b01adb5e279e539a5ab99aa6d316aa67a5db4ba0e98bd74f9e146c0c2a65',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '0114b43b6b870809605cb44828a1e31b1ce0970d86bb411fdfb30bea515360e9',
                'hash_atual' => '980b4b24c763f2123517d728d889612ed0bf47b3020d1e0215189fb4c0a04064',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'caa8a8815d4a4cc3c1ce5ce94b6b7bd789d3d608d47ea5bf6c5421fa48f846f7',
                'hash_atual' => 'a7b09831ff95c72995d6f990196906cbf381ee35b0a613e60e588365b7af2896',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '117b1de9a6e4acffa63e49bcd32ebb5e3bb4067d5d71b1430b0c63a607cf23d0',
                'hash_atual' => 'd019548a9dc0122a3c21b20dd790b4a734156dba15c98f4609fc74d38278a585',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'adeff86ef385cc718c78eedb8740dba0783a01fe4f404f40d03d2c468add4eca',
                'hash_atual' => 'd5fe0900f788adc9f8771577f09a694bdd8c1a86d7f49478f3ff1b0193a8beb8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'ea5784533311f26d6b6273baf7a4a7fdbc6cb1550f0f3158ebc1db0e2d9de531',
                'hash_atual' => 'a8c31d3a7d7979badfae5c04826999a5184af7cc98846d6629461557168cb6d6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '003876b8d21b58bb39df539a70739e5c5eab50fdc65c7acc4046b3f72b9002d6',
                'hash_atual' => '112af07c1d0c72857d62e24fcb84cf1d53202ef63625cc3ae4d490f93778d1ed',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '4454664372039a0ce137dc6c4aa655e43d66d852a7b31ad42ca4eebe2e5a60b2',
                'hash_atual' => '4ed25803970a7f951fc4ef2e58b6c89a064ed05333998d6de892e865290c1c95',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '10130659ba283ce9d9fb338a72e9ff004e33ee5db97dcecc232a6733cb64e86f',
                'hash_atual' => '6222907fc41f741df58d38821351029b961c47e76cc098d0880107990ae82905',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '625e939afe2acbd3725e8c4a03966bc7e16b26e6bc3b72b64a781a15a2c87627',
                'hash_atual' => 'db1232df00f9f94eae4734102cf56de088b2aa1be2eef03a0254445f0f46ab83',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '1aef01cbc317a6ea26b4f672bffbfb816cec3b3b94589e1e9117702b83defe16',
                'hash_atual' => '56d3e1af5cb9b20048aedc0469f64558d27c032b2967ae72fb9ac5483c1715ae',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '0e6d6661160481b9c66e62988ea5bbe1ecbd6d15e0d826a74d47ae68e6060da8',
                'hash_atual' => '9db73d80cab221ad9fc67b6774134c2c01b04e26b75cd9614e95f34eddf708d1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'ec62af7d75ef00074093e1f34f5696cba7b5ff85c64dd37707af65500a6eeba1',
                'hash_atual' => 'c83ca8b2b5388b2c61af351cd93683a3fd8019fde0a5db21b20809fa47ac6373',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'dc1106d3cb28cd74f1f0e371817fc9ddab6681c25a1b9bfc836622488785b0d7',
                'hash_atual' => '3b8febcbb4e1e91df0cd6e43a5661c9325064212211c0782dd456e9a52399dc2',
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