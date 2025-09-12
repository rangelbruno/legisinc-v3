<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 02:33:20
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
                'hash_anterior' => 'a47ffb1eb6fdba12ef41f7c5a1f895fb76ebdd218f3461fe7ab703c5f7952a5f',
                'hash_atual' => '3f346e6dde6612aca99d772aab44b9bd92c66e3647beaab8443afac714d9b835',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'a39d35fabe86c3bbd63e38bccc7d79aa15f397603e4c242ed885362ce87ded12',
                'hash_atual' => '2424b01dd639359d85507a06f9c7568bdb5861fb3f2dda0a77e78b190c2d9ebc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'ace765f17d56841af7c16b8e488a5e9e888484e109d0e98111822e2fc174412e',
                'hash_atual' => 'f2c32d5fecb83f2432f9d6d7a7c9d391971111ddf25dfa24bf790fd079b56aac',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'bbd82db6382415379de0bad3044b594b567f1f537d9a4d02740a0a41ddcc42b9',
                'hash_atual' => '3f1210cf3aee0c760a0879403da824c6021029a927a5850caba542276cb0c55d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '7e5a55977840539df5808a5b6b317ada1a18de321add987633192c4e52ab9bc1',
                'hash_atual' => 'bc553406691bd6b2b0ff0089e1eaa9e3fe7815bdf968fbec8b4e542af9269349',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '259d60deeda3138042d35963b64d8279f35d5810f20bfb1fe8bbfaa6dfe42558',
                'hash_atual' => '0fbed0af14f2f6600620cc39a00d359b8dbf62964a6f6ef51d286f3a25800073',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '6d5953dcec4adc9a737f447217ad4432b3f0bbe72433537b26e16b634983c1c0',
                'hash_atual' => 'fe3287f896ebc7b2d2d2e3626568c88faae33c4350c13cb40435de9b58300625',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'a9ce1d39245acf877eb6da3411cf2277fd49270d72041656f6568e859ceb00ae',
                'hash_atual' => '77fa6a83885ad629462a154de740edb52452c1a6159c3cda9da0822704987ad7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '4bde96713a659abeb72afdbc13a9abad40601d8c36ef23acf8537a694287c8e5',
                'hash_atual' => '03cc7a85f38b322617eed5087a36f2660da88b65586866fddc92aadc64ea0169',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'd75719199a503ec5d2f4f9b1925f5ab3c8d6ce36553604f30bad13073652a4d5',
                'hash_atual' => 'b6cbe10a8fe2da59f69866f10dfd793ac0e7d1cb435804c9b469beb8dd5ac5e6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'c21a2ecb50c88a5956f4d84c63ae9994d543c8660d4139409d2c292eb4eb3a92',
                'hash_atual' => '72d2e31c16bc2ae7a22d46f2ade1444d7db56bf32674b53e76e61be92564bbb3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'd2b6b774a3d378ff56f2cf509e8be0b98dcbf20e3a9692cdf63fe80844beb5c1',
                'hash_atual' => 'f8c58f3cc991df80b0c577da754909d05455a6af9cddb7ca094b5677444ab5bc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '5ef408883c085bfd0a8e7c4abada77bfcaa3e7bb4881d921112d73b66077cfd7',
                'hash_atual' => '72ba00a0318d5c33b7f69114c8c3075a95299dbcc936e6c21a437c82194200b9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '37ec806afbb5801039ca18440d3ecf89efb6e66f2e32a02b88f1864478d7b278',
                'hash_atual' => '996b5f23f70aeeffd9c6270908ae1cbae3eb45688c7d0e05307e7906f6cf57c0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'b0d3e585c06734459d08c8188366c203c2c9e42f8d8c8d1a4fee0a93a3d0eafb',
                'hash_atual' => '2919934565c5c1b52c70e4ce3878e1e03f79896ccf228d715615354466f874ae',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '1975201d4b44e598bdf19080220617151876800312c5fa0001452679f3eac270',
                'hash_atual' => 'e9baff439f1ac3a325de3c06314b79d72c4e563f3b36fc2603c64add24b4ea12',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'd2fc38011089bc1ac20af22dc060718f3973520f9c41125291f8d88bf2558271',
                'hash_atual' => '28dda9dc5d13b3a35b52bf19d28d1c737d39dba5b24d013358acfaed0e739b83',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '5060d8832c40165656aff182c8a551ff85c1c3599e98f6244342cb9015055df5',
                'hash_atual' => '40d8a12e4f8ea72b3a894dea33ac1ec0e47aed225cf2a28a5583997f71cf2da0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '08439b463c151779c364329e07b8dbcfecbb402d85d11a3f2a5d2f0a43ffa90e',
                'hash_atual' => '9732dda67dc26a34b643a31ea2f3c7604ecad9828c01432df5849d63a4da8b06',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '270ac3d7598bcc654e8c833c0ef3bb7bc2f166bba399dc7f5d1018c13f55ece1',
                'hash_atual' => '216eb40a71164840d678df67d94c7042a68417d7327000287afcb5b0169bbbdb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '5778197f3fa6cbb5fdf69801d33dfc9aca88f45c8861715b0ade57d38b61387c',
                'hash_atual' => '8d52f532fb0478d0feb7a9021bf7b007123dc3143fb48ae83d3a5bea6557e527',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '1838b309dd5769e9b523552f79248bd3e4186fe9204e3ac7c15f3388c571d78b',
                'hash_atual' => '1f4e8fe9c0dc6ec47222accefab72285f0533c206ca97babfaf94b2fe01c2828',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '7cc23079f97cb383b9587e7253f775c93b18f8809af1fd51116bb8312cb2e4d5',
                'hash_atual' => 'fb9f48125058ee344a895e37f649265d2544a7fdf816964e6197a31e2590947c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '8f3f41ab5d282474d48385da62acc240fdfbed948fbbb461788481a73d4a4acd',
                'hash_atual' => 'e9b4e636e76792ec65e4dcc7e75a05bc00625cc4ca242df147b72640ef58d399',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '755f5e089c887579b711858879ab4696ecbec8b6e20a50fe3910196b0742fbd6',
                'hash_atual' => '4c77e3f367e7bb8c33a02cf4a1ed6a74556dfe1ad24fcca7596d6eb267a06601',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '15cb484a5b060b2689afa417911e67c55222b2fcef71f74812069a14529040f3',
                'hash_atual' => 'a15c22dd0c268fc32c6dde830ab4784b8bd657d83a635545df667bf244548908',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '92aecbe4d9735ff798bdae60a84bffc7310dcd709250eb2e9b7e932a433c22ca',
                'hash_atual' => '8d100e65a0730db04a56ace81f6451494d6fc969824ca22db9ba62c00079cfa8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '18a89ab826205552cfeb0efbda4e8711db4d40dfc7b4828c2090369ab65c6e79',
                'hash_atual' => 'fbf381de53df6d14325db0d412e86275f69d541d1985db430ffc548aaf1dc4e8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'ff3a06751f049aac564766ca56d6fb6c69f244b1a99f0ad18eff2003fc0e8978',
                'hash_atual' => '2276fcca4bb5c00add1d673d94155ac6fd984044e7d9eda5b43aecc35e7b8ff8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'd49d8dbedda9defa7f082665b9edfdcc08d9be40825fea89421c600e4f51c3a6',
                'hash_atual' => 'f2a4991041f17d994012771cfa859aca419f6f4541cec2c3a7170418e5d721cf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '8327b9e88d0145baab81b1c50f86f3906c3954658edc68538b5399002a557322',
                'hash_atual' => 'f700638773e8da8186061d632e6ef066d52614a5582c8a47f939ec9c1281a54c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '8f8e1736e0aefbed1c226f10796f967e3509876445ccbee5df434c55bb2a18bd',
                'hash_atual' => 'e9495b92f894140e4074c5149b6e6a7dc846280c8dc0f90428de8cee7669e411',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '6533c1a1df9c9d1015d49d041541e3b690adfe8d6ba11f14f8060dd831e8c757',
                'hash_atual' => '02dc98b4e5abb7c4fbafea03f6fa0f02c224ef2802dc19edf54cebb1d6a30230',
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