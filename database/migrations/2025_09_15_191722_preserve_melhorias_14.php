<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-15 19:17:22
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
                'hash_anterior' => '3b2337722b9967f0adbb503e8bd4bb36c4be7ea3002b1efba23dd60430672eb5',
                'hash_atual' => '493f2b137adbbddc7745bf39ba3142d0cb651e14a39378ac5a1353ffe7c460bc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194760,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'cc99a8495ab884bb91fe13f42c34f8d02a1e462f006676ac66f8b2995abd7fa1',
                'hash_atual' => 'edfb94853d2edbb3d1065c88598e645655b6b15cd493919fbbb5e2fdc3802cf6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '44ec9b60565dab6b14b430e2c5e434c84a97e1ec1cbd617543804fc396b6ed1b',
                'hash_atual' => 'cd02bd692f078dc0d97a51709d5ce3cbffda7ccb19c35d9ee6bca8f7029958ec',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '0c373ea56613a25a83d480ae1ed230566989f8cd51b8f09d8a91bd57525d3b7f',
                'hash_atual' => 'dd974a8578d821d83704d5f742ebf3377bee62bf61d54f5e23ecd58844fb0e32',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '89e852eff5c36e66c76d36989532e1801306fc80cad276877aecabf5b45be3fb',
                'hash_atual' => '8bd0fe52f6ba6ab6b657738bb7ad91948d874bf98780815fceee00952dfdba1c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '13b9ea4b42a97e1d6970d73bea3863f84d21f6867f83ab5fcccf8ad0a6abe559',
                'hash_atual' => '16bf41b907948bad61bdb1cda60c7518ba0ec0777b184a7a6f43d3a9081707c1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'a242bd873016db99678910eb02e5dcd5079adef2854db213161b99b091678dc7',
                'hash_atual' => 'd543b1933cf8f581f49b85013e886222e9233a7d0d6acc50d5086d27bf5d736f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '3fbaaeffbefa0f2ea661ad207e29f761352e62a78cd95ac20089117d359d86b8',
                'hash_atual' => '1c869c347c150c98bce90e20aa3b1275643b17273e04f3ab7a1c7718b4b5b31c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'aa5c0446af4548be712cdb91ae9b51de7e3b8468b1a6aab58405e712ca74ea8c',
                'hash_atual' => '79c7a68ad9c331d7fb3c936228efc945537e88ee156002377707b726d9560a67',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '6167fde52f8d1f112feb9a5c352051877b2dece240db9b227cb962db6ce9a89e',
                'hash_atual' => 'dbe3e38715635854be2f51b6609185cc417b28b7f952b15844d7f1f612dfe215',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '6ce44cd39f23b7e0682ae1930c8e20a00156a74b539ad3c8fa58f051d28832a4',
                'hash_atual' => '17968c3ba313e9fbf73ccac3fcb0babc977a67d8e5cad8ec8b86b6a51b384337',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '457921870dbbca05d4d2b963220ad5a929101e64e3b7719e924a353aeada22b5',
                'hash_atual' => 'e19f6e3f20e5a49c2afa99f62f4f0b4c3ca4d559f4bafcdd67492038135d61f3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '01ca64a948b32964b51fc609760c5fe05abf25dd8a5c52e56c5306b90aeb12d7',
                'hash_atual' => 'b1477e8997fcfd54515f70276356fb186f976530318d55436e9e4691dd040b08',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'ac3c092291ef5f3b5643423c9c35237d8514fe98b8da91281e4d0155b1802917',
                'hash_atual' => '6f752256af9839b00dc949b41096e0fa654669712b813c540e1418091ded83b1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '45027e53dd806d01e7b5e398b261528d7b03ca8d2b8b338b28ed549bd6443394',
                'hash_atual' => 'ad56683729bc2bea5d6ebbe39d27778aff936c1202ccb615cbe2c3edd5eed39c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '01670b3200c1a093b91c4cddd575e3aef597590b52a0a9dba7c99a9082fe0a21',
                'hash_atual' => '028404aa6885c0231beea053f27218aae225aa56ebbce886ce47d93866c4606f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'e66d4273f6a008d91e2d1c3c2d22b112a1830677410e0e379334b0ce915fe4be',
                'hash_atual' => 'eef3fa0e29b348d055a4471caa47398d469d8c0e0ce5df69a69ff6afbe68aa1e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '88e9f280982f7933d1b6f40ea5cc5a11d4242a3b980256be3fdd836dae6a5edd',
                'hash_atual' => 'f0e389ccfd3eab7a1e7964815e6efc589e7d752136099059be95a11730fd0b5c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '838523382b0f440c4f08712062e6e641b00edc97a43e02f231743e499c60fe23',
                'hash_atual' => '3f4080dbb6c2ebc997b54e9b55d187aa71da5c6ebb6b39a6865385372ed38d9a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'cc1539aecbb1c20160b757fbb28cc12b74dd55e9d258f57b6b8a59d86dd0a52a',
                'hash_atual' => '35815803091318e7ac20981bcbebb2994de4cb396656036fcd96fc9b3597a6ab',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '04f6843b77cecbb25219de19b4169da42937c4951486fa375c8b8734bc35aec0',
                'hash_atual' => '2907e9699808192245dd3e940e240b644e2ec2efee5c38aaf2445c27e769d6f1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'ef0fdfb6adc479bfa2920d0d4c1d1de0e45b7b3a20a9c11e56917cbc24ba0b47',
                'hash_atual' => '0cc59cdf6a63367b8e15e741a73e19d89882b30c5a98c2a1a29a643c6455e641',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '930d9a47d580b4b036b41083378d7849184f9e0b0695980375e5742a7e065968',
                'hash_atual' => '8dc19cfd8bacc8509ce2b4b61c3884886110bc4fdd0740db05819a238be333f8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '06a1a0d5eceb53716919ab29b6db91e1381b2e3bb4231fe63dd3e4e439c99034',
                'hash_atual' => 'fa6d334f4862468d838c27279c1193751c44457e834e41b10a7b8dca5363a2fa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '014cafe0b4fc87f6d6b48918b9cadc61d002b93867e2a60327f8d4e27259de30',
                'hash_atual' => '187f850c191c81ec0340a749ee782b5f841d808687387a3880d040ec8e39a92a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '47c1ea1a3c545646bdffc39e622a1fdc66b62fb1af51b68c8d6775c4f54a5467',
                'hash_atual' => '3512bef5d48a62eea4918ba5a700202263564247c83d48a7c180eb657606a399',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '7453055c5562388c626992fde2ac008d8d0ae00170fddee6ae65ea0465db18ff',
                'hash_atual' => '57c5170f2cc646b17a224576d738024af0f9e26ff73c78d4a12217ecf0f2e2f1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'f304480727c8c8709a40cb16bec4eff0436286fd0fc73b0334628eb7cf6ad2ef',
                'hash_atual' => '0f10e41f332e51e45fa0f2ea8a11f64a9b046bcb63847db4c7a6512c18732637',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '4b9ca6087bad4aa0f0d2e7b6598867857424a3ccd76f954f4f891984b9b86d8f',
                'hash_atual' => 'ddaecfc9c70eeafd41bdec2cb97330384a31f42a6c097eb3d42121be6c5049e7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'd7218c7ea91dbdb9141eb5266c13ea801165306c5dfdfe4539f5cfec306f03ab',
                'hash_atual' => '17e5a2a23f7e6c3f57a757f83d603eae51420b59cd154b37d84ec88362abfd47',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '85b32bbdd3e366137e27bdc2ce1b84ea283da98c7d25640a8a4f7a7f7f457285',
                'hash_atual' => 'fe3c7792c7e9ad931b049fecfc2b4b5a4b01aaa85b9395647c216f8a9cf2cec6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'fe27131bcc9162b21e797f359e92eae17923eada1631f0f2479079803b862aae',
                'hash_atual' => '56991ee4b2b6ec48091326a3f88bd32d44d57bd3f030dbe147ebc4448900139f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '7873853e9bcb57937316e0fcfbcbc2089f7cd82ab6e3755ad576274a94beb9ec',
                'hash_atual' => '4b46bbfec1e83e3b5982ef58cb8eb33f44ed4b3f5fde25585c70c7a7d851d366',
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