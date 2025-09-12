<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 02:37:30
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
                'hash_anterior' => '3f346e6dde6612aca99d772aab44b9bd92c66e3647beaab8443afac714d9b835',
                'hash_atual' => '387bc487bfffb144c6b1390d9fb700620d879fc9ff051e1d48ff7bf9a9fa6c19',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '2424b01dd639359d85507a06f9c7568bdb5861fb3f2dda0a77e78b190c2d9ebc',
                'hash_atual' => '59a0c4f8b74de5dbb926d7c42a695178655f4ab537ec99702368cefd664f3fd8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'f2c32d5fecb83f2432f9d6d7a7c9d391971111ddf25dfa24bf790fd079b56aac',
                'hash_atual' => '7623dcc531dca3b695d109b283151813eb073abbffe5d8b71e079cf8c029e01d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '3f1210cf3aee0c760a0879403da824c6021029a927a5850caba542276cb0c55d',
                'hash_atual' => 'b270ac0b2115844da7d26f1e851e33ba387fc7cba594bff98479e1cd0d748109',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'bc553406691bd6b2b0ff0089e1eaa9e3fe7815bdf968fbec8b4e542af9269349',
                'hash_atual' => '6f79c42416abe96b26cc9d9387019abe425038a2fc24b9e49171e5ab31e327b0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '0fbed0af14f2f6600620cc39a00d359b8dbf62964a6f6ef51d286f3a25800073',
                'hash_atual' => '6bf07a01b4cc85391eb99404af6a9b2d9ba82fcbbd16ca389e09157b13bf7e92',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'fe3287f896ebc7b2d2d2e3626568c88faae33c4350c13cb40435de9b58300625',
                'hash_atual' => 'ad344ee2e1cd351c2a345ff78592e682f3e952d877e8377fb6f88688bf57c3a5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '77fa6a83885ad629462a154de740edb52452c1a6159c3cda9da0822704987ad7',
                'hash_atual' => 'af9dbabaecd5a82d7a8c2b1e79f3fa5c7b3e3ac5db39c04eeddc70563ea2653d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '03cc7a85f38b322617eed5087a36f2660da88b65586866fddc92aadc64ea0169',
                'hash_atual' => 'bfb366a92466516834d9a059f5f3637ac4966d7ec7a4f10498fa138b722a42d9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'b6cbe10a8fe2da59f69866f10dfd793ac0e7d1cb435804c9b469beb8dd5ac5e6',
                'hash_atual' => '36d38541ebb9ec4a333bbe9ef577a80bc807ca17604d13a93d44d699b799ffe7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '72d2e31c16bc2ae7a22d46f2ade1444d7db56bf32674b53e76e61be92564bbb3',
                'hash_atual' => '9988f8636a1e9fd92fdca4afc4301590429503769bc989b5077a68611d0cf247',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'f8c58f3cc991df80b0c577da754909d05455a6af9cddb7ca094b5677444ab5bc',
                'hash_atual' => '04db724f89b2ac64619a2a63c2891a4b76f81567ad35505ce3d66a5e1042865f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '72ba00a0318d5c33b7f69114c8c3075a95299dbcc936e6c21a437c82194200b9',
                'hash_atual' => 'db308f4345fa8b32a4a2adf766681ee5745fe3c6415b4c17714ca26407eae614',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '996b5f23f70aeeffd9c6270908ae1cbae3eb45688c7d0e05307e7906f6cf57c0',
                'hash_atual' => '3a805a9f0df0b82e6e3b9b07beb7dc4a44e6afcba665765e523d67df194843de',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '2919934565c5c1b52c70e4ce3878e1e03f79896ccf228d715615354466f874ae',
                'hash_atual' => 'a4ff023ba7d3eb0b95fe8c4a69ef274d4f21784fb17e8c8f628e86ef83aad424',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'e9baff439f1ac3a325de3c06314b79d72c4e563f3b36fc2603c64add24b4ea12',
                'hash_atual' => '73d383a5d328f35be04e50c77a58f055848a3b89e724c1c5a918b21a66a7f780',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '28dda9dc5d13b3a35b52bf19d28d1c737d39dba5b24d013358acfaed0e739b83',
                'hash_atual' => 'a0a53e349365e251431b551396392d811d35619d788577827da52db18db6103f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '40d8a12e4f8ea72b3a894dea33ac1ec0e47aed225cf2a28a5583997f71cf2da0',
                'hash_atual' => '0c55523a6612b72cf50da09a4759e8385800dfab875b1b8a53f3724746a38f84',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '9732dda67dc26a34b643a31ea2f3c7604ecad9828c01432df5849d63a4da8b06',
                'hash_atual' => '4a1a5bee462d290da36245c78b9e1cdce97494eaba1814082bc38dd730dadc37',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '216eb40a71164840d678df67d94c7042a68417d7327000287afcb5b0169bbbdb',
                'hash_atual' => '1a0ee54a865692995f226f2cb494fee713b3589f9b2fa7266e510f685d6fd635',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '8d52f532fb0478d0feb7a9021bf7b007123dc3143fb48ae83d3a5bea6557e527',
                'hash_atual' => '02e375403eaa86c34c5bfb7690b8cfddcbe3cd8b9b9d8961cbf077fe7ca31795',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '1f4e8fe9c0dc6ec47222accefab72285f0533c206ca97babfaf94b2fe01c2828',
                'hash_atual' => '791de1e56740c7af3a9d5298262c0c54db613f71a6b837fed5819bb23eb84a55',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'fb9f48125058ee344a895e37f649265d2544a7fdf816964e6197a31e2590947c',
                'hash_atual' => 'a546d24bfcfde899ea5f51cb6147b1996f45248dc3fc9de2bd95f984ef6db4ad',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'e9b4e636e76792ec65e4dcc7e75a05bc00625cc4ca242df147b72640ef58d399',
                'hash_atual' => 'fc937c3ca10c0bea21f87b4e84544c67be21ed1420f6a245817bbc8fbf582910',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '4c77e3f367e7bb8c33a02cf4a1ed6a74556dfe1ad24fcca7596d6eb267a06601',
                'hash_atual' => '5eb655d8a049b659940624cd89fee99b51886cbbba8f05f1abc16c4ab384b2d4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'a15c22dd0c268fc32c6dde830ab4784b8bd657d83a635545df667bf244548908',
                'hash_atual' => 'd965a33d1aa4c29244db4dc90924d1fac0c2caa141cad6708d03463161fbaa68',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '8d100e65a0730db04a56ace81f6451494d6fc969824ca22db9ba62c00079cfa8',
                'hash_atual' => '68358cf3630af0eba191b23247f899cf753702f3ed5a4c524e64354e6d37a6be',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'fbf381de53df6d14325db0d412e86275f69d541d1985db430ffc548aaf1dc4e8',
                'hash_atual' => '6baf3aaab6b6381d52d42460da02c99bbfdd6ff3530236cdcec310ee1d1cab6c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '2276fcca4bb5c00add1d673d94155ac6fd984044e7d9eda5b43aecc35e7b8ff8',
                'hash_atual' => 'fd727f0a0cf99f17e9dd0096958e18ee6e262b92a91fc1e87c7dd183ba63dede',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'f2a4991041f17d994012771cfa859aca419f6f4541cec2c3a7170418e5d721cf',
                'hash_atual' => '71084ca7e2d0a23bf0c6830f1373c7537e5159abb9566a2bc6566deea08e11a3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'f700638773e8da8186061d632e6ef066d52614a5582c8a47f939ec9c1281a54c',
                'hash_atual' => 'b97a8b220795781773282300c34a2b587b743b6cf7d017fda834c410b1e738b2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'e9495b92f894140e4074c5149b6e6a7dc846280c8dc0f90428de8cee7669e411',
                'hash_atual' => 'da401fa4f63769868eb0d0c78837e4d9a6b71e17d1d3f390b1aede5598fc462b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '02dc98b4e5abb7c4fbafea03f6fa0f02c224ef2802dc19edf54cebb1d6a30230',
                'hash_atual' => '01687d7eafc695a09a241dd1dd223679b9c3b3f6c402ddc67b946ad3a8d33d6e',
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