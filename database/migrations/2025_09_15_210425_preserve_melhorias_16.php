<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-15 21:04:25
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
                'hash_anterior' => '493f2b137adbbddc7745bf39ba3142d0cb651e14a39378ac5a1353ffe7c460bc',
                'hash_atual' => 'b45db0680b8bf827abb42891d83cf69af2d68f42d2d34e2f835d1051975bfea8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 198288,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'edfb94853d2edbb3d1065c88598e645655b6b15cd493919fbbb5e2fdc3802cf6',
                'hash_atual' => 'b552365b3c79e00dde178f02fcd6c5d0ea29149b0f47ee6f2bc85fb45962bc2b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'cd02bd692f078dc0d97a51709d5ce3cbffda7ccb19c35d9ee6bca8f7029958ec',
                'hash_atual' => 'e4b9e97b6c927360a461ef11e201b228ede271870dfab05cd9699a91a5d44531',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'dd974a8578d821d83704d5f742ebf3377bee62bf61d54f5e23ecd58844fb0e32',
                'hash_atual' => '62cdea2fdcd18cf1a9d69c3f2cd9918e56a1e31056aac0befe73bfca432c6c55',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '8bd0fe52f6ba6ab6b657738bb7ad91948d874bf98780815fceee00952dfdba1c',
                'hash_atual' => 'cf132a482fe8b7767bedc71a5193c8e81d5da586c5c78b0b76e49ada132aa0dd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '16bf41b907948bad61bdb1cda60c7518ba0ec0777b184a7a6f43d3a9081707c1',
                'hash_atual' => '271dabcd34b890256d2222f27aa5abb31e369ca217f4f9d0fd46cd7e67d683c3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'd543b1933cf8f581f49b85013e886222e9233a7d0d6acc50d5086d27bf5d736f',
                'hash_atual' => 'e946214f8782c1c5e1c1aa34fe4b54fa052193794ae970d476582e596bd256c6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '1c869c347c150c98bce90e20aa3b1275643b17273e04f3ab7a1c7718b4b5b31c',
                'hash_atual' => 'ea55dceb8d247b6fac4bfbff87cd7e6f50da955f84420de2b2f0e2fcba9ace8a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '79c7a68ad9c331d7fb3c936228efc945537e88ee156002377707b726d9560a67',
                'hash_atual' => '119682950fcf08285a73a21259af9825f1ea3e8acbe16ac9297117a0e7d77e4e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'dbe3e38715635854be2f51b6609185cc417b28b7f952b15844d7f1f612dfe215',
                'hash_atual' => '1b654608633a1f4f757687136038d15f4ca111140739925d2c1d8d0f2cfd4a86',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '17968c3ba313e9fbf73ccac3fcb0babc977a67d8e5cad8ec8b86b6a51b384337',
                'hash_atual' => '421faa92877d3d2e444b546441696c48d4cf0a6736c78d5c86c6070581692f9b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'e19f6e3f20e5a49c2afa99f62f4f0b4c3ca4d559f4bafcdd67492038135d61f3',
                'hash_atual' => '4186567b33860bd5461f6581e87e0547828d7836e775cc5cb980bb0a45a3039b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'b1477e8997fcfd54515f70276356fb186f976530318d55436e9e4691dd040b08',
                'hash_atual' => '101356afd2557656133a0af4e5250016cc7f316177e74c4fdfe047340555b3b5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '6f752256af9839b00dc949b41096e0fa654669712b813c540e1418091ded83b1',
                'hash_atual' => 'f0175aefc40ac01aeec01d5772977aa146deb4821335e92da12a043758ffb763',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'ad56683729bc2bea5d6ebbe39d27778aff936c1202ccb615cbe2c3edd5eed39c',
                'hash_atual' => '3b90356c6454358c9b69a5dfa4ff6804200a0ddbc53829080c7979324fc4fcf6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '028404aa6885c0231beea053f27218aae225aa56ebbce886ce47d93866c4606f',
                'hash_atual' => '4b893225c35ec9ee24c5cb794abc340143b0e1b96786fa67c5ba21b1290f6ec5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'eef3fa0e29b348d055a4471caa47398d469d8c0e0ce5df69a69ff6afbe68aa1e',
                'hash_atual' => 'a8c7827ae150664f5b506d3c83fb092a81c2054dafabb540755dc120ae4a09ad',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'f0e389ccfd3eab7a1e7964815e6efc589e7d752136099059be95a11730fd0b5c',
                'hash_atual' => 'b66f573bbb2b247d310d5148131cb9182aac69422a3d4d551840b99f9f8cd205',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '3f4080dbb6c2ebc997b54e9b55d187aa71da5c6ebb6b39a6865385372ed38d9a',
                'hash_atual' => '5330d67b1f5e8aa09881fa3d3cb62eeb58b584f846aa645f6dd42b98ef60b963',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '35815803091318e7ac20981bcbebb2994de4cb396656036fcd96fc9b3597a6ab',
                'hash_atual' => 'f82310cc468bac26695ba7497e8cb1a62d4c02bf58d7ee9ec4e04cf1ba2095b5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '2907e9699808192245dd3e940e240b644e2ec2efee5c38aaf2445c27e769d6f1',
                'hash_atual' => 'ba09e835ff2c6166bb49163b0fd4492abd40ec66990f65dea74f5818a03229d2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '0cc59cdf6a63367b8e15e741a73e19d89882b30c5a98c2a1a29a643c6455e641',
                'hash_atual' => '6a75dfc1b8b895c2695e81c622b8ed53f7b7c9f771c2d2b51bb7854d9eb40368',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '8dc19cfd8bacc8509ce2b4b61c3884886110bc4fdd0740db05819a238be333f8',
                'hash_atual' => 'c098b74bfc3c398461fa25d108be848444b7b94f313986931ba096d5bb94edfb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'fa6d334f4862468d838c27279c1193751c44457e834e41b10a7b8dca5363a2fa',
                'hash_atual' => '7d9251dc64ae230470edcf9dfe71787822fd190e604bca87fc88fd71462355e8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '187f850c191c81ec0340a749ee782b5f841d808687387a3880d040ec8e39a92a',
                'hash_atual' => '6064823b7381e0b21dfc7c0f41a32af99b625e5958b765bf4efeee17c5ec4d95',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '3512bef5d48a62eea4918ba5a700202263564247c83d48a7c180eb657606a399',
                'hash_atual' => '874c44fb08cbb8c8108aad9f4b40709ed7643ce1f68a6ce47be551c85935e9da',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '57c5170f2cc646b17a224576d738024af0f9e26ff73c78d4a12217ecf0f2e2f1',
                'hash_atual' => 'ed310e09130349e05bb10f43b9121a09e3f1aedadfd198a5fd1a66ae21dea59f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '0f10e41f332e51e45fa0f2ea8a11f64a9b046bcb63847db4c7a6512c18732637',
                'hash_atual' => 'f248d8e9c2f135f470f72e3a465a715e759c10e1b399d3ecd71eeddb47d723d8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'ddaecfc9c70eeafd41bdec2cb97330384a31f42a6c097eb3d42121be6c5049e7',
                'hash_atual' => '300a6de105057c259f2922f9153cdc66a2473a299be8552c6495272182839c55',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '17e5a2a23f7e6c3f57a757f83d603eae51420b59cd154b37d84ec88362abfd47',
                'hash_atual' => '9317f4d0e65e11f7fa8f6842298f359331c4d401d43c0d48d79b4dbcdd175619',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'fe3c7792c7e9ad931b049fecfc2b4b5a4b01aaa85b9395647c216f8a9cf2cec6',
                'hash_atual' => '9eb238e1dd140daa3c703368d57037655a94b288b5f9572eaed8c504beec66d5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '56991ee4b2b6ec48091326a3f88bd32d44d57bd3f030dbe147ebc4448900139f',
                'hash_atual' => '374168a2ea09f8b1b819e024774a2ec27e5d5cf08a8100515cdf5578444b6f9c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '4b46bbfec1e83e3b5982ef58cb8eb33f44ed4b3f5fde25585c70c7a7d851d366',
                'hash_atual' => '551bae2b3c53bba4f8bcb7a7bb05594604a4171d4affd2bd39369074852a3500',
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