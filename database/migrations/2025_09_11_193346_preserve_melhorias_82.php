<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-11 19:33:46
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
                'hash_anterior' => '5eb790fb37d4aa4203ecc1d7dc48e668aa152e55d0d3518ac24af0d7e5312235',
                'hash_atual' => 'e2a82e215d34d928a6a9ba9d572cfeeba6e586c470af88f1a86c3e64820dcb3c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 185055,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '749645b256cd608ee3278f1b0940efad48b24d9432c372a735668afe19d116ea',
                'hash_atual' => '02672a583c93ff46ff94fa0c4a9d97d6a206e1114d6729d0359375042ee5a482',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33929,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '93176d2e5bfd0dc345c6d892c7925799eb195a13a69990d0ba59fb18e435bf70',
                'hash_atual' => '53828de38930df399ad8ea9ff2f4ac062d0a06f640eb6c5c56b76bbb5141ab0b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '52c85abadc1da5cec8665dfb533c3aa07b387649c1ce869d5a86d306d2521a3d',
                'hash_atual' => 'bef8c97355f65ff6967981daed0da112db8be84d10e587446ce1728f46138a0a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '03527dd0956cd2acd1701216ebd4fd93b319b579dd3623ec1d70d8f23dc3219b',
                'hash_atual' => 'd61d18228a311471fa94c0bf18325cc33e33889d5276c2d481b95a4b80fc712b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '11e6caf7e8f856a281ee7af3ad8e404bbfbe43de3438c9599e9fdc4771afae22',
                'hash_atual' => '39b230b22c22fef530f018aa19e5dfb434240ae50fd7b8794fcdc7b98793ae51',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '5626217d40509b12b2fd54d6dc6fc14a72124b3ea4b9b214b3d5eea4cd62608a',
                'hash_atual' => 'a8c19b758983b0d35c22bf88ec48a8447d269e3ded7203d71037f4135f82242d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '6e8f1e76643b061ee30a9b2c1a7c2b5aa0c0e1ed2dfa8bedc776a96a902dd300',
                'hash_atual' => '47df120509a20da7fd99f84bf818f74423bb1d7e32d8cc89da619befd3f69402',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '091f25c7de733dfbac342c10fcbbc8d2e708979a939b03be0b403ccd8259d2d0',
                'hash_atual' => '3d259deb55ea72de1078a0abc6bfaa61962ef9c185c148245b26131bb52d28d2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69442,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '4460ff455651832237ef9e58be36a223a38c6074b9602d92c19dec43d3ecce66',
                'hash_atual' => '7f1490554511dfa90c6f69907447da188ea54c8080dca82cbe42b79e956f9fbf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '8153ba4881cfc8fb3420b579e8b3962622c32937bc0daa4d38d31eb851d323b3',
                'hash_atual' => '612d196c054f1117e56a337be51a15fcb4bcaadd615a93478679ddfc0a24eda9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '2743506bae4228e294f6240895410076d02a9108159d0290a4914d45aaba47d4',
                'hash_atual' => '252e73295de5e357f029ae2c4cbd7eb5317d3a59e6549faa14646a9fcc08adee',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '19e75adb30631e200455157231a40cd080ebcc99b3b371b6597517c8e192893d',
                'hash_atual' => '9fc98b6b6fd516415e01f5a7ddbba02ed96dbeead96853d45b0fcb3f0d7c9479',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '5faced7b4a3f0f02bf8c07f30747a616003626fa1ffb3de20181bbef67c19164',
                'hash_atual' => 'ad2e4d70eef2d6bb9a7a79af84f825e4bbadccc6ad7d7a8234499da7624149c8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'e8ebec4faa21315bb1a9b57a1df970860ce82160d34ab71975db30b7e272102c',
                'hash_atual' => 'd04066e81b9de823b5b2d03146295cc5dd90550acecd83f8d6f2d789432e48fa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'c76c8b02ade332cdb51a24083b347d69d1b774fae3b61f693642bce0b4bfc39d',
                'hash_atual' => '1f343230456b575797f161516566ab238c335f8bb459a6a21662f510a4ecb5d3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '9f0799b0dcebf7ea6ddb259ca29bc7591eca71ab093991f2a0e354143d38c775',
                'hash_atual' => '8646bc0ef99bb029c7b6fa2fc80db7d03826a620674a2c4d8d62e4053f5aa807',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '899998d893bb9c5e6bfa337350e115233c16c060bb3606e9fac11e3297cb08f0',
                'hash_atual' => '6f3b57af2f83e5eedb8ede5dfa8e0c2dcfe5d4ae2f14a907c2db466b9d1fab7f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'c659a5b3a2ba923d4ff8b47ff4f8fb73995829ab82b60a6b65a3a3cd0dd6864e',
                'hash_atual' => '3a5f18c771b84d15032d8b466b88c6e473b34cf425b86bb3049907a9368db063',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '9468d67c569b72674adc7de0bed208e7d03aeb9c41297f28948289a2fefa3014',
                'hash_atual' => 'e73fe4667df62378314e27d80466e4cb760ec3d15c3344521d584195afb97653',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '191f7cac08b92bf988dc3a41867d9eea72324b0f1d3c27fe40fb0fe66bd458be',
                'hash_atual' => 'ef12dc9d8e96b0cb42e1545338d9ecf4e66b871f63d839226e0ace6d8f3b9f7b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'f0647a068e1a664fe52ff259248118d18f8c9f7566bb7fe5f9f23533624ddb40',
                'hash_atual' => '430ecef0395ba83adb184a5cd02bff2c614e9e63d11ea04832ba5c79689de8be',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'c13db53ed5619918f5e5617c771a5aca5024745762f53bd4f6862586f33c28b8',
                'hash_atual' => '0cfbaa3275e1a77acd5840893a02abd69e9d2449ce3cd9fee4064d27c35b9771',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '4e250148f35cf97d555b72a40ac7631d79907d1f09bae77ca927cfb3036a130d',
                'hash_atual' => '26a77134db6c4de2683120d270aab7d33bfd8d5e8c270ed133c5d03d1665ca0b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '895c133ebcfffdd310a334560107dafd831217b1dd81e548310acb253dc6fbcd',
                'hash_atual' => '6241aac1c23da3e2fc80ca2adfe472363baaffcf9244b2cc711c2864a4ed4273',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '37aa318576bcc2a68aa21c5dca923a8ed1f3d80ad8d3c2e297135419890e67ba',
                'hash_atual' => 'bd95bb74b99810da8e5820649d3837e5f460a9f0bbe06b07938565fe2ecad49f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '831f953db774abff456cbde36e4689ea300b7fc3105a1976e218be8a086f0876',
                'hash_atual' => '7c49b3de9709fe67253e3f3a60207d0bb6b326c903f1b69fab668570821e6c5f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '09d6ded7cf641bc345f168952eb1584aac1f4a8fa5055b4b6e7685a5bb0c0a0a',
                'hash_atual' => 'e490896e3178098144f32952b4ca67f8e938e39e3b85f60ad688ee0ee737a556',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'afa7a153047d3ac78f0edd782b79bc2bb117f0989824898137503712d1fbca71',
                'hash_atual' => '212900d03a451a318992838777fb257f6abbef2512d962d0babb146d479f7b85',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'ff6db9a9d056923f400b9eb860c8d9f82c88c5b176c96764b20098a4b62f73c4',
                'hash_atual' => '276fefd1d8e876974d1b4b5f433c291927277ce9d75666ed9e203aa0bd700560',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '2bbb3d72c1c72e4a75c65aeb6e2be77e36fa24336f45d81e66eab63f86410307',
                'hash_atual' => '3be92eb2eff9a15b2c120c63f28646bbbb67f19d36a293dd74d831503a29685e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '7a2ead24b4a7edf3c3d7dc6ae30067d9bf052d826ff9ca98c0851ff5c474eccb',
                'hash_atual' => 'df261d38d720b47258c0cf0bb2ca663b8d74e14bb014a6c278532d7f4b8c9313',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'dc62c88104a4ec48e1751c7f2cc292c26deba386eedf9f3f95cc7512e82587ac',
                'hash_atual' => '28ef08d42fbac7cd7504869e10e161ac8930fd616a83e06c60afbfe59054d35c',
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