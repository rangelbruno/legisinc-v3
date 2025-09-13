<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-13 03:27:51
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
                'hash_anterior' => 'bacf486d71362cf777a3a00bedc6d455cb389da48c73aac89066147f2744ef46',
                'hash_atual' => 'c7e3aefa1a326d9beb8387dd426e3b704ccf7b6545bd5a1b0abd77909ccbb544',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194593,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '7f85110f5ad4da8c12fb09358898464e5b04cfa5f321ab8fe5d07a4549adfa47',
                'hash_atual' => '543e3876be7945354d040fef3beda572ed14c711e768993b5442863c97c43f3d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '99d4217830901e37253d454b4c22a4bbe4259f516b3333efc222e28575f9d4fc',
                'hash_atual' => 'f62a0d2b46aaf1a9189e76d460a1eec7883ab1f1c26df48ac18d081fdc6bc1ff',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '806ae9453e51e8bd89890e8fe47774c77438335a7600a41949aa7fecf0a1e4b5',
                'hash_atual' => 'e945e387c87eed0ea1978fda570b417258d6e3551a325af2c329df19257a7d4e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'fa1e004da6a327bc10d495ac322d8e522ce86a89be4ab127854185e8bf36cc12',
                'hash_atual' => 'e5e1aff3df1afca8af8454bf20f11e9503851349bae07cfc64f24453096452bf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '4db8c19eb9f6d01c71f118ec315434bfdbe1252c5dbae3d1a1434c5db1cc4179',
                'hash_atual' => '066237a88e2a5cdf96ea014bf7702d708de8a17bf4dee0c44454e361f9b117f5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'ca423d38b206557c448d0c888f9777535e370fce0056a9f7f12d9104038ab09f',
                'hash_atual' => '7aaa093fb992990739fea10112198201ede1d1d2e0ddd96fe5e5c44ce15c87c9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '6cd269f833341781e900d40deeb8129019f56723bb85daed93ecdb60ad628926',
                'hash_atual' => 'aef4374af7924385904d6211f67ac68454412bcaf4bb613ee39354fbc3b8c0c6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'c3dac9b1cc4fe6ee754865e6cb00aa16c100f366294fcfafb2b386e21e916949',
                'hash_atual' => '0054eff5c64bc5e1a46adfff810bf3342a72d66dda5782af7771c63823b65c02',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '0ef76cd649a6bbf38548d86cc18d406be5d13745a8332f02d2348df8b842678f',
                'hash_atual' => 'f943acffb833b3c2ca8e0e971582e767ca4026443e3d8904c6a640d9f3180e4a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '62f42836f44967e4f2bf6c7ea9f6408dc0a103dad83b7346a2566c57194f648b',
                'hash_atual' => '5a7befe09e21b190ffe2c24ea8a913cc8ceab79867eabe9f84b7abea4ce482b4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '20b6917671eb7726007b7a77c1d0a085c3c0c1aab8d755094d71cbf384512b41',
                'hash_atual' => '05b9995c34f1580ca907d0cf6d4f8844707eb7a48b56a0d31354cfb69cb3d4dd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'd18dccb98ec76e08bbe9a8b6724604fc86946545de525d0a1d56af23be1b874c',
                'hash_atual' => '6b9b5710a544f549ee980a2038d81a00311014a2e2d17e6c5dc4d73c1f617f1a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '4a7ba519ea18431f13c9332a0350bed441eb23655f21c974085f4c23a96b969b',
                'hash_atual' => '19ded62d7fb17f6ce11904ed1a25cc349b063b6d315c3f1e6ee0e828576b9c5c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '6b4c8d1cee8cb7a6d3607d0896e40a4774aca917d65883a63f487fd31520d26c',
                'hash_atual' => '941b7184248f37ad3c506f3ee2c13282ed7b113aacc6ac2d57c3a1154bb173c2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '542e6e712cd67c6d888dd540593e7b539689767eb90b2a9fe1a7011912ee4498',
                'hash_atual' => 'e4fe919693bc9321f76d147a7169cb4ef8425de6738625c4f450422ee38c8320',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '35342f26f949b59b375a0bc4d266f69cfe50ead992fe768cb706f4afeb648baa',
                'hash_atual' => 'e13364d8f635e84ad676cd4ab869b5695a6a6b2c49ffe7215f6a502237cf97e8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '59a63e6c6fe7d42c4d89ed81b197cd3b6dd6f9b69409449a8f0e5efd046f9d48',
                'hash_atual' => '0da3085c50bf1e7b5e00f2495de1df7801ba57ed69c4d3572b740a48c96a9e57',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '222ac4af59236633b65a10fb6fe02bcde7b5557770dca92291ef91b43bf7c2f7',
                'hash_atual' => '16eddb3497fdc8411e92062fc51a881a5a91e85b14f26848ffdfcc398d35387d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'be460e312ab49da02c4144c998bbe083303e5832ccdc3d07fd97c6f830aee2fd',
                'hash_atual' => 'd9913c74f0999f56852567ad54714024adddd48dae2a79516e92cbb6e4ef6c7b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '08e3e0d9239589fd0154cce3c049169d259a5155d58fcdd3d6da5162fef8d9d9',
                'hash_atual' => '870944671af12d3ca8ef3404ad3e70ffcfa51c0ed596c745ce2b52351e480d78',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'f04abd38044b8b48928db8526dc20baa953892d06379fc3da984c361ba7b04e6',
                'hash_atual' => '538f0289a8c80940973c5bd9015b924eeccb294dfd60a9050702e1feb5f86bb6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'e948e2de37dcf4d2f4eeb9228fe6555566eae1375ed505ea1b951d34a5fb73f9',
                'hash_atual' => '55db4c540d8e2c059070e98c2e59f1cb44b47d8cf2ffdef13b17d8fc14262534',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'd88d8612a133c73daf536c12370b5a470ce0507b2403dd247aae5fd71a88abff',
                'hash_atual' => '0b6ce2e6ab1506576dfdb8d138a893f43d8f1f6ef53d4250fc79c48313c1e84d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '23a1cd6c46c624bb29f8ae99dd894593be1633bebcbda7db8f7a253aa12c29f6',
                'hash_atual' => '82b685562a657dc4b49d56d586798675b13a04f40bc34e02ebd6154bf7987848',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '25c4a9c578b885bcf7357acf1579d7f595d1d889069703505d3dda8d64baf194',
                'hash_atual' => '7d7a7d7813bba5461027fb8f2fd210dc210f191e7c4d3d73258fe16ec6a8054c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '795842a79f6baa09793a87f284d7c13df8690c10fdf675e94bcf8dc524e85015',
                'hash_atual' => 'efcc4fc018a46393ab4048e0628528cb2fec7b225c6284fa28f12a5a0f69b814',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'e49c07353e28c9ceedf0de1f19c7a0c27731c1d4dab3588208d3cb7e2945d994',
                'hash_atual' => '7c2b7585f8cb8c5a0e501f9113dd4a34eb92f6e79f51b310d8eef491f0d7abf3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '59e61c1a92a1d0351938daf3cb9826025018dc8e0ed3bcf35146d1ba00a15c49',
                'hash_atual' => '7b551d7b2061912c4428d5c41e3608f8496d0ac30688438f0212fc62d97c7c15',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'a89a9b41a4a9ec6b9a0ffbb7005d294c111f11da6bdc4b365a0e0161a7d75b7e',
                'hash_atual' => 'e2a47be302a985ea9bf1ef20144f3d7b6aaa430e70d88931953b2202bb7345b9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'c77728bc4d280201e0e6f16c798d061063e0f5bc704e9cf7cd2f439bdd85aa48',
                'hash_atual' => '5500083692e7fc268fc9dfad2baf7ae255885e76ee902f3e783e1993f885565d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '9e0e36333067adc33def6fd67ea2751ef4ef8a589a2898c011c7b19d50716f3e',
                'hash_atual' => '38cf395452c81409a096bee1ff533653e181de8a98ff5318897333f8f0a74398',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '4c70bba1e88f16bbe6455b3e18cb73f2dbd22c7641b6c7302cc031c0d0be756e',
                'hash_atual' => 'e76417942779c71ab25e2ba5e7fc5c161dfd0e50a7f2ffd7495037c3f0a9e60a',
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