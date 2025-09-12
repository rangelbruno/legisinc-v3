<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 01:45:11
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
                'hash_anterior' => 'b3edb7735532af6ced96b880a14830948d412137a3dea06d8501969d6b4563e5',
                'hash_atual' => '2d87842959a3db7606d3b6b6405c6394ae3659d2854c62c46a25b6947951f604',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '8a98b0d3fea0ff223579b7e690a9f1b91a08aa3ae339ca55cdbfc62249109bfc',
                'hash_atual' => '90541c2dba1cc2515761531e2dd6048cab505f736e01013090d9f3295567fec8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '9cdccc2ec5b97c49d64d70b02b1029fea6c2e8aeb60e93f8e2041a20e0a34fbf',
                'hash_atual' => '68cfd536ed9d6d81520200db90ffb56f767738de56f22d53293c71608197f209',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 188969,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '54c4ab5596bf117d37fdd3a56ca836417b9e3f20dc1cb9c90aa885879ce1f28e',
                'hash_atual' => 'dacb08241994e41ba15e160a4f0f7dbf9721b0ad90d67ae72d937edf14c142bb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '260ca590c75cbc9b4693cc274554f1d1b80f58e423eb74eedf327a596cae7f34',
                'hash_atual' => '955f861f2f74cf8a26a2b0a4073822bf141931876c62b377bf8fd00c91caefa6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '565b6dfdf01836a3a2182f7c0f6a62bb24024918de1c5dc2f081aa261d4419ab',
                'hash_atual' => '1a2623aad6acb71120973b7ea62d2011159d25e2789b448434317120218fda56',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '6f52fe981f56616e059605221046652b081aabe2b9107b649d3710789ca59cc0',
                'hash_atual' => 'e41fd7851f1bfb1e2127bf717fa5d2fd05fadc4ef55209eaa2a7b0bdfcc114ab',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'c028c40ac8c888a1481520736232e0e3bca3d6ff6f982c261dcc0b97e72219b6',
                'hash_atual' => '1af0ce16ee0197bef2f35533b82480fb417de30a7b279e05835a26c5a5a3d84e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '4cd61fb3eaa42bd2be46a31491aa7820dbaa6ed73e08f546b12ec1d86f0036cd',
                'hash_atual' => '9b21e89be4d84bead938a96e9e846e7098e871b569ade57920dc3cc32fc894da',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'f8d955312b73b669fe5234c9f6b199e2c4be49fbfd9b737305bae7e962bd4f75',
                'hash_atual' => 'f2a6987e762a19bd4728ffac25bd0b4cf700b483a3795f8207e2e83bb0d8bf17',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'd3162bbba8163b1efeac9c6635bfa66385c79d3dd423612cce8ce060a47241fe',
                'hash_atual' => '6536e28c8ee67f99acf795ad43504028c19c972584b01f56a27bfb790461a50c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '9b2c4a639669f3b006356a049d9874f5af8761d8a7a57f3e042900413106bbd7',
                'hash_atual' => '83021cde72d9487ee874634936e1271f28c2bb8a712cd1389f0930df0abbc338',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'ec918deb54b95e495aa37582bc7a40e97d55ebb0c390afa5c4406c5f91f0e610',
                'hash_atual' => '966e555cf6aa63ed932e80826f33f2945497a47ce7f0e064117c01adc5f89aeb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'd3d70b28b3a81df5c197613665cb6f09141750e766d515ce9b0f55b596dea6d4',
                'hash_atual' => '91852d61da45fc3b82f393550b38a27d94878eb3cdcecef479a79989a95d3e91',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'd75a6ad9a7094b47218603d4bd86c0a540adb66c745677f2e913735eeb4bb630',
                'hash_atual' => 'd529ab34cc2d9f930ae1dbc6302875281815ac56489cb7d5c45fe3c5978f1dad',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'aee6b5dcce238781e720d01a259b99f9ec4fbdad3f46ff2ab5d5ca50e40b0595',
                'hash_atual' => 'bf712758b14f50c25269d760ab0c839cc64eeaf878cf966b1c0e05ef5b256392',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'c37c2c674369cbfb3196c4b28aae2df5ae16c258243ba9f94625580da1d585f4',
                'hash_atual' => '5d14a5068d93fb31c6813d98a8de71af181cd2c14275252ee90fdbfd7c46a644',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '5d75ab9bd5e430a661fd102e8a487c920c04dca3e477bc7ac8b5801c954e55bb',
                'hash_atual' => '9453e4a05d4ba714925b3f9cdcd9cbef010e9c45bca697a09018fb4ffd22ca1a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'ae6ccbbae73a611df9256a9942c09d2742bd6ad859f6e0ff2cdab6fb7256826c',
                'hash_atual' => '83ff7e101b811cf44ebc79cd4f0cda1354f05e689489540ec0016be00be34674',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '56fef83df6dc5f643ee260d6042a742ab55272b643d0c0bef99b24d4818a553e',
                'hash_atual' => 'fc92539ca7d7278eb24d1c3585c9c65fe737d1566872980549b0fa517c01bc83',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '08bee99fc4ced6bed6657e65e05d28482d5f190ac6773b7a9dbd92e725417652',
                'hash_atual' => 'a643e48e220c221a38a2cc1d02c008a7058003ad1c2f9da62ea8258c0f595777',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'a09e9cd399db6cea7dea6d0a4ec6e5085ecfc3ef417002e753a520e542f0faf9',
                'hash_atual' => '28265a823ce4faddfd8165b6d302359fa9425af0f670379eee6446bd21d14980',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '6946f95f8116bc34a5f95f735e1b5dbce323a3c15537bb0700dfc0c41d0c9fe3',
                'hash_atual' => '5c0dea12862e80ffa3ccc341976641ecf715e08206146fd66309bceda78beb49',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'b7599376102f0a3a4d07227ef4735c95e5faf9f8c4b3aafd00e6f70971de1b80',
                'hash_atual' => '139b702cb88afdd548dc55640ddfe336332bc79f21076711578b8ca0342fe096',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'faff0a2e02c48233b7c8261395f4931623767ae123f5b2c376c84e7f1c748ad6',
                'hash_atual' => 'dec25ba7e8752a801e86b0ce4329f063bac5697a15c99a6343c549abf4b23982',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '8ddac80fb073431edbe198ea36aa8c44f1881e03b899885682ebdb7fb3e7faa8',
                'hash_atual' => '2da504c1db32907f701609a405dd73e56ab8ed6b8107cfaa62f4ffce5fd674c8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '97ca6a4f5d7b8696d617e15cb8b6004b99b26c45eec62eb112a6cd5154ef1c78',
                'hash_atual' => '0bcb43784143eb3b3c29e2e2c0aeb54ee8b77f2dd1a1ab232083c3472c143600',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'aa66438ce8d2a35fbdde8bc119e24567749f8f0fb5383c70370c47607e6f2f49',
                'hash_atual' => 'b17e86cdf73fe6b521efd401c1ae9dd2b3235c28fa64f8fffb8c06ddc5f6447a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '3030022a1b47b32ea36b3936c414792457a90bb41a27ee9095cb60c9d2760095',
                'hash_atual' => 'aa1d45de1bd12413b52762ae9caa88b671475d81ab0614094621b68e6fc3e3d1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '86ab2f329cf66c7a86830ee85134ae4d67166ea229cd2035eb6badded290c57b',
                'hash_atual' => '26258728a92c6769472ee9519605096509eac3321b2cabd1d8577a770ed416ab',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '58211046b2a933c45090bfbd8771e899f3294620f331e7d4eb0cee6f3957578f',
                'hash_atual' => '763d84ac55a79ceaa6becd1408d399e09d2e03f2bdb4d8b2bc27b185823b72a5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '620d36375a300a0bf68480b298c4c2bfa12c2ed7c2bfcfe8cd8a068bd4667f8a',
                'hash_atual' => 'e73c89e2454230009b671ed851911bc2266b1a4509ce38c9905736d2465d21f1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'd838bfad0a01a870b69276d4b103a4b0b69ff3cfbd6e57ae0d5105f5b4a8264e',
                'hash_atual' => 'da1ca94970b32bba2293cebd0c50d72c06dff56b25d315218650e8a41e52e788',
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