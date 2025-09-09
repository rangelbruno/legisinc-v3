<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-08 20:37:24
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
                'hash_anterior' => '4af602a08be71b1229fc66949aab60a698cd1a5d7c6e0c999be1f388198f84a7',
                'hash_atual' => '2c94733853d53dc10c00cafefb841896d0944e28307a544281d1ef15584822e5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'b94c90878c295c42ebb50a31f455a274e645cd26709f55a03adf9ca36e7b9e88',
                'hash_atual' => '13724c2ab0986d1d922ae1dc0ab4dafd92f024953ff38a1cfb1ab927e0f109e4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33929,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '71353537afe84cb139e0184bde66f2e2c10c2d61bf90420117537f55ddf3b8b5',
                'hash_atual' => 'a83dada009b7e3a56d857ba66654836288b897452ce9afd8cd7a1967d2c18957',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '376bcf11798e624c10a049f62b130c3268fa1ba1c56f37627bbbca03869f8f2d',
                'hash_atual' => 'ddaa7088a1272707fe3f7804d52fc390d309413f94355fe5626f7d5825b09f5d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '4c69e0d3011028f41edf2ce5487d0f2c4f47df17c7cc55b6133169ee60eee6f0',
                'hash_atual' => 'bb39dd155bab6d0520818da8e6c3c9624fa6f4334492cf4b6c0485e1745040d7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '9d617212fbe3737c30ffc20f79b3acde8693a4d830d5e3d21e152faf2efe04a6',
                'hash_atual' => '2620174e42875ddb9ad2bd1bff51a14753138d8f04ce529ca1af0334f7577036',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '123c1058fa421f9e838f9dbcaa4bd7ccbbec8bb31a7ce136e7ef057885e5a938',
                'hash_atual' => 'c030421dec77cbf4398707abce15740b6449a0315c1ef56c5690e689e7f7c927',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '52ecb445413c4b771a212873591f8fa381fef6e7ced552eadd2a8016245b953e',
                'hash_atual' => '8f1b86a9e80b44a15ed148b35a00990c829e7070ef40fad606747812eddce3e0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '1bf5690c6d87610da61856a9b67539ded685f7e3cf146c007480061d2c400d18',
                'hash_atual' => '3534b8ae870ac54fcafbc8fbf681aba8ad8ba6cf0be1c24ac4a4e578eb60e02c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '1b53485236887fd5e40732d73a4165311ebfecc814fa7bce5fd0c991e252994c',
                'hash_atual' => 'eb8f079f66317710098c01eadf886bab5e223fbefaf9898925019ac280fc0c86',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '0e7eb09cd959365a2bd0991f188e865517319a7f52d02bb3f4c11e4782adfdf6',
                'hash_atual' => 'b859122f69b835b9d098826ea79bad338cd287f833ff0c824223197af4a6d99c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'e1b46d470937f717047987f85c966febb16b0ac77ef47d6abe23694bfb34f279',
                'hash_atual' => 'ca11385419e0448aba3beb17ff712462181f8db56387d5e55d62dd2eb77a0540',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'cd25bdfafc911fef7256b880efd934f6a41c67ef731aad2374874bfc49b0a46c',
                'hash_atual' => '05e194b7ace4828b4949d7ae83e2fbfe04bf7fb14d6a34fd5d25d08a87cf0f1e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '0d7167985d90cabd7d2bd7647ed33752a7b9d82a1b82f0009fe48d22bf7ce3ab',
                'hash_atual' => '682842cd4662c0d7afe779c28bb640f339b49a1a383850a9b262cc295673b91c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '3a537620f7e228e06fc0e2bb32884b8b39cc8801e7440876222f80e009f47a66',
                'hash_atual' => '56ffd0d466fee826ec5e1457f131bfd8b65dfe3c818174f8a5dd8e300919ff18',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '67216d0129b1cf61a85dc1742e886f0624edab93cf28f75dad0378f8f4704a76',
                'hash_atual' => 'b8e7f6eb19b5024f254b75183e251eb727c3d69c228a81989f0c0c2684c8c9e4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'a9a676a442aa6c2656170e6935c91a20c40ae8bd3e095b84478a2ea6ce3583b4',
                'hash_atual' => 'e1c0a760fcab995fbc81c9df472a68057fa434ae517730f8797404799be026be',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'f90376eb7360066f17c57d1e937f3edf1dedfeea82e047f946b93d6cee7f5c28',
                'hash_atual' => '5114c0182114fde51a734e6e2b777b8c416b4097ab59ba62a7dddbd4ce2f9edd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '9677f65de1d4f0252288565d48337472711408c073e70e8ef172b2ebf8f6aec8',
                'hash_atual' => '40d8f4eea17d2f4e332f323985454995c353c2e3b118fce59fee61450f79a4f8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '43fee6da29de27c999ed8c05084b51686bfa7b3730043b7c10ea3678cc8fbbea',
                'hash_atual' => '75d0790897164b1fce8ebc92d1fa3a7bf1cf022eabff17eee72cce94b9d00242',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '129b5ff93d159d03fce059a367f44c06578eb340536ac2c3cb1e9db7ca15dc07',
                'hash_atual' => '2f42b73dd52891473d49e7b0c1f0c7a79dccf06876d00358cc8ca48ce4698134',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'e2810679d325534a96e43db4b1ea48de6ecccb651ba189ff3d0c66633e326ae4',
                'hash_atual' => 'f9065b5c42d9fddd4c9c2e9a0c10a8a4228f3b061a0ea75868edb5b2071c5785',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '66de5f36a6ac511296a69b9163896aec9d6e997c1dbbd4fcc9e59d4721ad9441',
                'hash_atual' => 'a33c4d02f639213680c35a2ff2fe195328625d542fe7eb6260cd5fd9f5a1a5a0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'a7a83e9c45850e237017f72964a63e4b9c484c41fe05bc84eb43527f8fe8b824',
                'hash_atual' => 'd6349f318112730e20ab7234e8a72c61519824c8258642f1dcba25969cdda34b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '95129d9467a1dcad7f512c357ba1f9eb0cff9aa435ee91eba77b0f0fcb30f2f2',
                'hash_atual' => 'de5e7e6b9f6675aae1b462af6d4c1f43346ab3d59cac6571d66f50a0bd595a5e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '4c5956983be4718db16d8114c65a15c7001d9683e314595cc7f52d866cd13234',
                'hash_atual' => '8d1d948eb3c4b380e7f2365cf410ee853a81c16a3da2fd8795765e1d2a8737c3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'd369375e1bd95a289b90357046580533099475e52558e0a72490200f09bd7b9f',
                'hash_atual' => '64ab53a87043e7380d8d4c6598aa5e967be5941eb166c5c2439666d2241b8fe3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'f7b792c9e21ef88e802e064c1bfe3da3e0bbcf6e62794999dc49ea22c0e4da9b',
                'hash_atual' => '19a0a971a69e32187a9686343c27459c66a160caa6ccc89a6a417a3d741340e1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '7e28b99ad0ded2a6bf0df9babeb71b5ff6dc86685308591f4f87c7a218a2f152',
                'hash_atual' => '56792b51f2481f6939abdab3e83cdf6bca009f11f3206fdd3249a241cc2a5ec4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'd1b81401a9a83aad1f76fe6058e20d7f5a464a7c5f3db97029d26e5b1c0fe271',
                'hash_atual' => '29bf7a11e9ed15bcc98f40475f79dc4b8609614a4341db7ca37ce62e29c6a645',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'dd9448a34d524445291a4581c57f89ae472b62edffd9375977105e3a58558e9f',
                'hash_atual' => '7304c1b634171d2b7c1b517a6eed1237902ad8bc087fb1440170b164e9328020',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '4369f680a6d38054bfc1a14bdc21c692b2fc23b7809c07d1ceb9ac53b0efddc7',
                'hash_atual' => 'c589aabf433574af8c28b403291145c8e0b4a4d77e38ca1e8bbbf0849998eee9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '64cc83536425c767e7301c2e58c1a8e0a59cc075454f0618496edc090cd9aff2',
                'hash_atual' => '16049290b003fab12bb979b78f98179a4cfeaf153ff50aaa42b662505942a56f',
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