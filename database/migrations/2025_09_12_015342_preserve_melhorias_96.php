<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 01:53:42
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
                'hash_anterior' => '2d87842959a3db7606d3b6b6405c6394ae3659d2854c62c46a25b6947951f604',
                'hash_atual' => '9b46deea7f8825732f1725ea4f96da944899466fc7cc15f452b1e3affd7b5064',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '90541c2dba1cc2515761531e2dd6048cab505f736e01013090d9f3295567fec8',
                'hash_atual' => 'a32e53276a874de4faffc5e2bc0ebe6bdc6aa0a116b3c1c6048cc28e02bf7460',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '68cfd536ed9d6d81520200db90ffb56f767738de56f22d53293c71608197f209',
                'hash_atual' => '7ff6e01c9ec248a9d347159d0cafa5bd2d974b0082bed68c7f7f373a0b2c1572',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 188969,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'dacb08241994e41ba15e160a4f0f7dbf9721b0ad90d67ae72d937edf14c142bb',
                'hash_atual' => 'fce6c1b3d3b6d081452467ca4cf9280082624b3abd24dd37ff7856550b6a0a5e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '955f861f2f74cf8a26a2b0a4073822bf141931876c62b377bf8fd00c91caefa6',
                'hash_atual' => '14fb5815fbf2326403c32d2b4b2163c902d3c5c9cc7d692788c28c065a1d0d4c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '1a2623aad6acb71120973b7ea62d2011159d25e2789b448434317120218fda56',
                'hash_atual' => 'b368353943a958ded060f8424173ece2314661149f2c293e2a2f3300f7fc540d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'e41fd7851f1bfb1e2127bf717fa5d2fd05fadc4ef55209eaa2a7b0bdfcc114ab',
                'hash_atual' => '1c564b509bb4e4a93d1ae0a607c321a1e9bab20929a9a7f92b9b157029446fd4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '1af0ce16ee0197bef2f35533b82480fb417de30a7b279e05835a26c5a5a3d84e',
                'hash_atual' => 'b232109cc7ff43f83452e3fd8bd51b6c0beddc48dd799c8590e23c78d9156b6d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '9b21e89be4d84bead938a96e9e846e7098e871b569ade57920dc3cc32fc894da',
                'hash_atual' => '5ae68d9cb9f18cd89009d316eaba58c9e6a088d015bd78fb1a8e80a88e908d16',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'f2a6987e762a19bd4728ffac25bd0b4cf700b483a3795f8207e2e83bb0d8bf17',
                'hash_atual' => '13253d6dc2d30af46f2f700c7c2bc0be84dc66cdd7b15f7eb0f2bfea87eeb4c6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '6536e28c8ee67f99acf795ad43504028c19c972584b01f56a27bfb790461a50c',
                'hash_atual' => '3f954dc20026ec75850b017209fcc0794d46e975bd1f7c2bb6473605432c3efd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '83021cde72d9487ee874634936e1271f28c2bb8a712cd1389f0930df0abbc338',
                'hash_atual' => '9d33c0b154ba2a1ede0721f80780e0feeb1517f4f400a3cff5a93a2f150c9266',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '966e555cf6aa63ed932e80826f33f2945497a47ce7f0e064117c01adc5f89aeb',
                'hash_atual' => 'e2b79e5c2e710607c8653012b5ccd686cd17a25d00fdf4c3f86ff5e12ce65649',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '91852d61da45fc3b82f393550b38a27d94878eb3cdcecef479a79989a95d3e91',
                'hash_atual' => '78043c538a135dff6f88a645de21c46d867c5149b21daaaa3c9afa9956feb633',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'd529ab34cc2d9f930ae1dbc6302875281815ac56489cb7d5c45fe3c5978f1dad',
                'hash_atual' => 'e5158b53035cbb43349a1f28f72003ff53635ad5c02d2c728288f76fd103cf72',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'bf712758b14f50c25269d760ab0c839cc64eeaf878cf966b1c0e05ef5b256392',
                'hash_atual' => 'e95928fa3fd3196d3266ab7b0a2a4c07a7784866f8474ee0ace2279c257942f2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '5d14a5068d93fb31c6813d98a8de71af181cd2c14275252ee90fdbfd7c46a644',
                'hash_atual' => '91fb47f92324a105e6451f469783fa4e265be6537ad3fcb8b18399575418533a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '9453e4a05d4ba714925b3f9cdcd9cbef010e9c45bca697a09018fb4ffd22ca1a',
                'hash_atual' => '8436e007a134ced980fe3e41a71322e7b9a76113671c2fc58058286ff05304a3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '83ff7e101b811cf44ebc79cd4f0cda1354f05e689489540ec0016be00be34674',
                'hash_atual' => 'cf29043f31840549e8e71a6cd6117c74219d16f3c56f59f574ee9c189189e7f0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'fc92539ca7d7278eb24d1c3585c9c65fe737d1566872980549b0fa517c01bc83',
                'hash_atual' => 'ec174342d3fe3a8dd090deeccd1eb992d9ce555cee9a60ff17c99b5b72164929',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'a643e48e220c221a38a2cc1d02c008a7058003ad1c2f9da62ea8258c0f595777',
                'hash_atual' => '1b8d1893c721726ba2778a7835d2f774f75e85d03659953f49ce18a60ef64b1f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '28265a823ce4faddfd8165b6d302359fa9425af0f670379eee6446bd21d14980',
                'hash_atual' => '7121c33a8a8a0f87f9db8b57103211a997c5e634f9796090bb07cd90ca322ee1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '5c0dea12862e80ffa3ccc341976641ecf715e08206146fd66309bceda78beb49',
                'hash_atual' => '99abc9af182d0da6ec9a0b11decf898852206355f1fd133eea42309816705411',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '139b702cb88afdd548dc55640ddfe336332bc79f21076711578b8ca0342fe096',
                'hash_atual' => '6a860101e960558504b5cc30d496ce1d0793541c5a79aa102e22ef92b28b2ac7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'dec25ba7e8752a801e86b0ce4329f063bac5697a15c99a6343c549abf4b23982',
                'hash_atual' => '3028125753cd7fc9c457a75115952f4d3a4c59ece5b6c5826bb1d848e1841f3f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '2da504c1db32907f701609a405dd73e56ab8ed6b8107cfaa62f4ffce5fd674c8',
                'hash_atual' => '2cadb82ab3c927f8564f596f9b1b548190b960f6a01e4556d6aa9b427602440e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '0bcb43784143eb3b3c29e2e2c0aeb54ee8b77f2dd1a1ab232083c3472c143600',
                'hash_atual' => '8cece85c199d9e04218194a26a1f63ab29c1ec02e9121435438891ebfb1eaace',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'b17e86cdf73fe6b521efd401c1ae9dd2b3235c28fa64f8fffb8c06ddc5f6447a',
                'hash_atual' => 'f2d2bb277386616fe778bef029f3597b1d66c829cd464617dbf3f8dfde4917a2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'aa1d45de1bd12413b52762ae9caa88b671475d81ab0614094621b68e6fc3e3d1',
                'hash_atual' => 'b2dac6eea9915297c45b6524b1eb0a346aec704e6b3795ec615e93cdf8dd918a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '26258728a92c6769472ee9519605096509eac3321b2cabd1d8577a770ed416ab',
                'hash_atual' => '87e6c7f7ea22f87f2ee49e4bf07a6730d78e11ef08feaa91d452becf1b97f17b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '763d84ac55a79ceaa6becd1408d399e09d2e03f2bdb4d8b2bc27b185823b72a5',
                'hash_atual' => '0f582358efcdea1ced2867d51fe9e5ae07c09cbb3366ddbabd1cee7201fb8495',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'e73c89e2454230009b671ed851911bc2266b1a4509ce38c9905736d2465d21f1',
                'hash_atual' => '4a146ffaf5d7edabe32d747ee4af0656ae151582d9cec89438b644c4d8b4cc60',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'da1ca94970b32bba2293cebd0c50d72c06dff56b25d315218650e8a41e52e788',
                'hash_atual' => '275f843dd7752ed5b644b033e849ecdcf06d9d63e673695d418cdb8bbe7fc6f1',
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