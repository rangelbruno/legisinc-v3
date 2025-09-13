<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-13 03:31:48
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
                'hash_anterior' => 'c7e3aefa1a326d9beb8387dd426e3b704ccf7b6545bd5a1b0abd77909ccbb544',
                'hash_atual' => 'ba6158e3901a1ff8a2957fd44378b574c4874f790cc5c5004c5736cffdae9cfe',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194593,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '543e3876be7945354d040fef3beda572ed14c711e768993b5442863c97c43f3d',
                'hash_atual' => 'e9d813b53e1ca6d981d1d9f81525bc370b4bef46c351b747e01f45752abde7c8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'f62a0d2b46aaf1a9189e76d460a1eec7883ab1f1c26df48ac18d081fdc6bc1ff',
                'hash_atual' => 'bde8281672a5bfb2aa56ca80eb348771c8be740e56be66cac19a794eb73c088a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'e945e387c87eed0ea1978fda570b417258d6e3551a325af2c329df19257a7d4e',
                'hash_atual' => '959ac20cb229fea7c1e3b0a544e415fde35769662b525f7e1d7178cbfc643368',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'e5e1aff3df1afca8af8454bf20f11e9503851349bae07cfc64f24453096452bf',
                'hash_atual' => '8c8ddd5c917527317a0d5d0b91d6f92c0373bfc3d78fe2086c90b908b0903128',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '066237a88e2a5cdf96ea014bf7702d708de8a17bf4dee0c44454e361f9b117f5',
                'hash_atual' => 'db7533439093efe8dbe0e20fb5491f013b270b16bfe3ef95464c59b5504ee757',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '7aaa093fb992990739fea10112198201ede1d1d2e0ddd96fe5e5c44ce15c87c9',
                'hash_atual' => 'cd692472f53735408952a011fa47795996af3bc84d158a53dbb79f4d715d3adf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'aef4374af7924385904d6211f67ac68454412bcaf4bb613ee39354fbc3b8c0c6',
                'hash_atual' => '6408c36a1650e9487e4838c89a775b6634c9e8cee4fca4eff834d0bf5b9b9c7c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '0054eff5c64bc5e1a46adfff810bf3342a72d66dda5782af7771c63823b65c02',
                'hash_atual' => 'ed6b46b3ee609bc9a643a4c57c5e2e66ef973b1f044cb9e11cd51d75a409b8b3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'f943acffb833b3c2ca8e0e971582e767ca4026443e3d8904c6a640d9f3180e4a',
                'hash_atual' => 'ba4699f003b1b6f51d5b5ba00f94fedcdbe3ec280e1a0ed5f211f69202128497',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '5a7befe09e21b190ffe2c24ea8a913cc8ceab79867eabe9f84b7abea4ce482b4',
                'hash_atual' => 'faeb8eab7c635554c46832a9e1106acfd6805ed979d0f39737efc736e1212a00',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '05b9995c34f1580ca907d0cf6d4f8844707eb7a48b56a0d31354cfb69cb3d4dd',
                'hash_atual' => 'a188e2523f1845cfdeb897fefc154512543906e5480b6c3c04e7b86d7762b2aa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '6b9b5710a544f549ee980a2038d81a00311014a2e2d17e6c5dc4d73c1f617f1a',
                'hash_atual' => 'da71467c748b38c79af8d2f190656fc7d50f5fb3cd1ddb20e0ffc883ba78f797',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '19ded62d7fb17f6ce11904ed1a25cc349b063b6d315c3f1e6ee0e828576b9c5c',
                'hash_atual' => '8ca0d1085629db7f1f42f1170e34810dd31113622fc324fa9306db88251828b7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '941b7184248f37ad3c506f3ee2c13282ed7b113aacc6ac2d57c3a1154bb173c2',
                'hash_atual' => 'b0a5240c3365f8f6eceee44894951f0b18e18b242dda567a29dccabd966a00d6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'e4fe919693bc9321f76d147a7169cb4ef8425de6738625c4f450422ee38c8320',
                'hash_atual' => '4f314b204cc7b2dd821b18612ce1da1996f5ea5d6ad0ed415937b41092c9f3b0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'e13364d8f635e84ad676cd4ab869b5695a6a6b2c49ffe7215f6a502237cf97e8',
                'hash_atual' => '480b5612d5179e40e6475ab0a81d6b47311d8e2d5e1621ad7584bf080bdcf65a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '0da3085c50bf1e7b5e00f2495de1df7801ba57ed69c4d3572b740a48c96a9e57',
                'hash_atual' => '13c0974715e16623a021ca846c5f47bb7a7443dd93b882c57f8ca04944570d69',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '16eddb3497fdc8411e92062fc51a881a5a91e85b14f26848ffdfcc398d35387d',
                'hash_atual' => '931f3c245fe8a8b177624c5174ff6fe50878c90a0b1385f0f5ce35289fb6e1ac',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'd9913c74f0999f56852567ad54714024adddd48dae2a79516e92cbb6e4ef6c7b',
                'hash_atual' => 'f309b90ad4337c476536aad7ab172324257407627b25da2fe8f0453423540fa9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '870944671af12d3ca8ef3404ad3e70ffcfa51c0ed596c745ce2b52351e480d78',
                'hash_atual' => '55d450e492604a05adb33b912f38b43672157a08cc84377af352730a15d3b4ba',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '538f0289a8c80940973c5bd9015b924eeccb294dfd60a9050702e1feb5f86bb6',
                'hash_atual' => '2e1431c408ea3637b302c71226202c82192efcd80a0863a64563c82090667ee7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '55db4c540d8e2c059070e98c2e59f1cb44b47d8cf2ffdef13b17d8fc14262534',
                'hash_atual' => 'bb2e3b330f495139067138e9999e7ae48eade4c0ecdbc48a6485d34c90dc4804',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '0b6ce2e6ab1506576dfdb8d138a893f43d8f1f6ef53d4250fc79c48313c1e84d',
                'hash_atual' => 'b54cee491b437350ef094b7647d35b1d41e19873f9be5e85c94c4916a71edf05',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '82b685562a657dc4b49d56d586798675b13a04f40bc34e02ebd6154bf7987848',
                'hash_atual' => '37d5829ab6dc2ff9f6820831823d5e7dd4f7ba327e9c1310c587570197946ac6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '7d7a7d7813bba5461027fb8f2fd210dc210f191e7c4d3d73258fe16ec6a8054c',
                'hash_atual' => 'b762df921dcd8f42bda63c5f9658a37ff0e6adfa5f30652930878dd5789c35f5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'efcc4fc018a46393ab4048e0628528cb2fec7b225c6284fa28f12a5a0f69b814',
                'hash_atual' => '165587e796af29c05baab5f7bbc3d6cc0ef32a442b8ef2aa915eee352671ec9c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '7c2b7585f8cb8c5a0e501f9113dd4a34eb92f6e79f51b310d8eef491f0d7abf3',
                'hash_atual' => '87cb14b7cd8f5b29d163af0bf5819cf59ffc4daf08bab53fb1c9525e75e32197',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '7b551d7b2061912c4428d5c41e3608f8496d0ac30688438f0212fc62d97c7c15',
                'hash_atual' => '2a7b00e7b5089f952f8d61817972b191f72104fb4a82b69e70817b76faed8d71',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'e2a47be302a985ea9bf1ef20144f3d7b6aaa430e70d88931953b2202bb7345b9',
                'hash_atual' => '8cd4651047ee83a8dd319437cd456cf48b3157359f33d1c6e5a58ee0ee105b79',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '5500083692e7fc268fc9dfad2baf7ae255885e76ee902f3e783e1993f885565d',
                'hash_atual' => '14b40e3d603afd685c938ef18ee2bdda3437befb9e647f52831f4a17b96bafa2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '38cf395452c81409a096bee1ff533653e181de8a98ff5318897333f8f0a74398',
                'hash_atual' => '88c2e2058b1583299d5fc8b225923bea43cfe0ce910658edfb13b11969d55ab8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'e76417942779c71ab25e2ba5e7fc5c161dfd0e50a7f2ffd7495037c3f0a9e60a',
                'hash_atual' => 'd177acbc81ab804ccc0fda6c64da4950a535894df23c56877a772c6fa0e43918',
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