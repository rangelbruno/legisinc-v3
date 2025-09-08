<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-08 06:43:39
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
                'hash_anterior' => '7ec40fb45cb03a0aff72ed6ec97d015d56d6ca51e4be264d6ae7c7c3adaa945f',
                'hash_atual' => '4af602a08be71b1229fc66949aab60a698cd1a5d7c6e0c999be1f388198f84a7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '88fbe79dbbf88a81f7a421c749a46913caa42b8efb74fa141e61327b65680535',
                'hash_atual' => 'b94c90878c295c42ebb50a31f455a274e645cd26709f55a03adf9ca36e7b9e88',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'c26b8cee593dff3c3868445a152ce31aa123f4c8881cce4c4dbb73db17bf9cfd',
                'hash_atual' => '71353537afe84cb139e0184bde66f2e2c10c2d61bf90420117537f55ddf3b8b5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'caef0c91d4de68a4996acb22d9da057d10639d99d9299bf0cad3f1d10a9a5a38',
                'hash_atual' => '376bcf11798e624c10a049f62b130c3268fa1ba1c56f37627bbbca03869f8f2d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'fddfda600e22ddd5ca9758f4a6b8981d9ac63f31d51c3969a888b63678ae21c9',
                'hash_atual' => '4c69e0d3011028f41edf2ce5487d0f2c4f47df17c7cc55b6133169ee60eee6f0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '97e932f8f0209d66e57d3983e359bc0e7266aaaae5c161e631c053bc27aa0e9e',
                'hash_atual' => '9d617212fbe3737c30ffc20f79b3acde8693a4d830d5e3d21e152faf2efe04a6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '4dd1018ab7a2373074b3836a94fe944ed4f86181f21511faff12b33601c1645d',
                'hash_atual' => '123c1058fa421f9e838f9dbcaa4bd7ccbbec8bb31a7ce136e7ef057885e5a938',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '773411e32af24115d092947d34db717317569d355804d8a2f09fbe860cebb732',
                'hash_atual' => '52ecb445413c4b771a212873591f8fa381fef6e7ced552eadd2a8016245b953e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '2f5724a3fd24b60d21bb7feb4f820508a57e8ce73d624f8cce07a11218998da1',
                'hash_atual' => '1bf5690c6d87610da61856a9b67539ded685f7e3cf146c007480061d2c400d18',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '42cc5a815a3b028859035338a5f015f4fb42893fb81dee66ad90afc3861426e1',
                'hash_atual' => '1b53485236887fd5e40732d73a4165311ebfecc814fa7bce5fd0c991e252994c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '70ee31fa552f016739cb36d702740b4d529d95e6d921ad68d6fd6ce43421dc2b',
                'hash_atual' => '0e7eb09cd959365a2bd0991f188e865517319a7f52d02bb3f4c11e4782adfdf6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'aa0ead1d101ce14cc4f049017b3d6431092b460958b8efccf7c10a18e0571198',
                'hash_atual' => 'e1b46d470937f717047987f85c966febb16b0ac77ef47d6abe23694bfb34f279',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '6b5a456f37ec7bddc13ac60f0209862a383f92be3187a0353619f3c2d97287ac',
                'hash_atual' => 'cd25bdfafc911fef7256b880efd934f6a41c67ef731aad2374874bfc49b0a46c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '544a9b7bf426368340ac2619914ebe589afdd53cf8db30ee67daa0e0e247bbfe',
                'hash_atual' => '0d7167985d90cabd7d2bd7647ed33752a7b9d82a1b82f0009fe48d22bf7ce3ab',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'aabcee7ea9490e94c834820d47ea5fc991f74872eb2fcbaea8698d2ce04b006c',
                'hash_atual' => '3a537620f7e228e06fc0e2bb32884b8b39cc8801e7440876222f80e009f47a66',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '90bfa91c52c4fad47802704123dd9d1fecb8042a47b0a845ab774ba282cfcc50',
                'hash_atual' => '67216d0129b1cf61a85dc1742e886f0624edab93cf28f75dad0378f8f4704a76',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '7111b750d5747800aa85df7a3aeb229fc74da34d4bb280b18f8423c5a87090a9',
                'hash_atual' => 'a9a676a442aa6c2656170e6935c91a20c40ae8bd3e095b84478a2ea6ce3583b4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'a9409285bdb1050e1c5400d205d4519c60cd4e021f5ce4ca0cdcb1c1dae0df5b',
                'hash_atual' => 'f90376eb7360066f17c57d1e937f3edf1dedfeea82e047f946b93d6cee7f5c28',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'ae4edfe5c2319a427996cdff2f84203acbd07fa08cfde9772ec6cf5ce6d82d91',
                'hash_atual' => '9677f65de1d4f0252288565d48337472711408c073e70e8ef172b2ebf8f6aec8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '7c82deb231a97e6c150c40a30cf35b0c13406d0311dd7c53d9bec551655fde4f',
                'hash_atual' => '43fee6da29de27c999ed8c05084b51686bfa7b3730043b7c10ea3678cc8fbbea',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'b65cf154608c473c4daadf2c7773c263ca9f5a31cad57b81ddef753381870eaa',
                'hash_atual' => '129b5ff93d159d03fce059a367f44c06578eb340536ac2c3cb1e9db7ca15dc07',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '2528578d0f3cd89d471c2c25d8492bdae2a64aed77b21a8ca58df7ca4067b467',
                'hash_atual' => 'e2810679d325534a96e43db4b1ea48de6ecccb651ba189ff3d0c66633e326ae4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '80c5788e3735b48b989b960127f3335cd937ea4398ab599a271364938e7ca84d',
                'hash_atual' => '66de5f36a6ac511296a69b9163896aec9d6e997c1dbbd4fcc9e59d4721ad9441',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '84ef3c0e0cfd55454d878f65b4bf185dd836e560fb4e85a97cae05405fe190ee',
                'hash_atual' => 'a7a83e9c45850e237017f72964a63e4b9c484c41fe05bc84eb43527f8fe8b824',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'b26443f583fad592cc61699cfdfc7021ac9c2e1881fb744f9b1d2c26912bb2a3',
                'hash_atual' => '95129d9467a1dcad7f512c357ba1f9eb0cff9aa435ee91eba77b0f0fcb30f2f2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '2ed3ac7db06268979f8df0b3aa76eaadce9c3fe33be39b0580160674f4a69fb2',
                'hash_atual' => '4c5956983be4718db16d8114c65a15c7001d9683e314595cc7f52d866cd13234',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'b54f6f1eb874dca4869a170715c4adff5993b81c1ecefb64ec452a1e114ab3aa',
                'hash_atual' => 'd369375e1bd95a289b90357046580533099475e52558e0a72490200f09bd7b9f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'e9c7a9a6072d8399cb8a6fd494e0fbfc4b4e3c3f90ef480edced03e7eb937d87',
                'hash_atual' => 'f7b792c9e21ef88e802e064c1bfe3da3e0bbcf6e62794999dc49ea22c0e4da9b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'c85abeb187a9c0ede93ad22be93ebcaaf22c7ad941cfc4b7db2a0219e14a4ed0',
                'hash_atual' => '7e28b99ad0ded2a6bf0df9babeb71b5ff6dc86685308591f4f87c7a218a2f152',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'ac6ff3a9f16b647fd926c33cf4589c9a5751dfedfee7a2991427ee5edb744683',
                'hash_atual' => 'd1b81401a9a83aad1f76fe6058e20d7f5a464a7c5f3db97029d26e5b1c0fe271',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '1f2b9f70444a550bc903ffc4c33cb467b67b2611c5158725ad8a24a2c70da5d2',
                'hash_atual' => 'dd9448a34d524445291a4581c57f89ae472b62edffd9375977105e3a58558e9f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '8e004d5f4013aa2939bf44fe69c75301bc098c9d2a7f1470b9104464195184ad',
                'hash_atual' => '4369f680a6d38054bfc1a14bdc21c692b2fc23b7809c07d1ceb9ac53b0efddc7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'c42bdc92c8ef76ccc20ebd7b7e98fdf9d7e005518b7f92e6dcdbc68bea85009c',
                'hash_atual' => '64cc83536425c767e7301c2e58c1a8e0a59cc075454f0618496edc090cd9aff2',
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