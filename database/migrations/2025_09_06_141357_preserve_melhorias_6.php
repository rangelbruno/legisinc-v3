<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-06 14:13:57
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
                'hash_anterior' => '77c6f7b7f7347bb56be53f4eff7f5f1f293bc1724d16f07e9b795dec4e446a0e',
                'hash_atual' => '5e6c3c717c136eb056e9f4759ad707fd6db07c6826208eae70c9aa0e7190d0b3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '051de1a83405b0a22114cdd8e69ff060ac11ee9465dcb3f0ef823aa104c87ab7',
                'hash_atual' => '58c6a6a70446461b7d991d2f35589953201c6907cef4d7b651b57eab316255be',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29133,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'b7ccd23410ea9d00dc85884e99427914aa58c42594a39761ea8cd53b603254d0',
                'hash_atual' => '0fdfa143eecbca54285fdf7699a684eb722b1ede992d000cd280c49ddd400723',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'e729e454241544b413c756957f8f6d5c5a052c11d80abbb3fe48b3908682bf70',
                'hash_atual' => '128c35e02a7d0c8bb4b817ed35392d3b9f25abdf95e4493c1d8b220d4aeeb903',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '3b8f56f808f3870eebceef87a2a8c0200e759aec5d04582555e0deebacd1f724',
                'hash_atual' => '00d5c80285f474a7be47b2c4782eee9a24a9493519f893d3d363f54c95507ce5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '49b747d7c6466ab493f3e5bdaac3149619ea7756c4a6436972205b69d7234987',
                'hash_atual' => 'f93fe753194cd1658ae256a640f9780925bc30270f47239e0cfafd650d3262bb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '0ded52de94bc2f6543b7366e3d6932ae475bef430572e7a070b46da8a4ddc8b7',
                'hash_atual' => '789c0d6b3e9ebb5076b90d5aa66911d3bac16c6bdb157fc9f68c170074675990',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '7be0758d6d8506746ae8b90a6f70c7f1a419b72988103ea3a1efecc1190db260',
                'hash_atual' => '0e4376cfd977b0760651c90802680a668e28124cb0834ae00e19f1b96ccd2d15',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '66682f3395fba255796f62904fd72f1e730b18c867a90f936f8be07b03c2bed1',
                'hash_atual' => '78d8757ba686924b939dbed1040d790e6532335c7514ac9a3ed8d3989627bf4f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '8dbebdfb107faaf5b2a6075ecf20120c346c83e419e57efcc6b22e8010b0fd1a',
                'hash_atual' => '01a5606bad79ad7edcab6783c00a77a6d77e75346f6c637fac7486abff44d943',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'be184627fa26154fbe4e25ba1a0ec5e8ea3442588b61c678503aa2d9b723ff9e',
                'hash_atual' => 'aae6e06034ccaace89a39a67a0b9d67f5b4a365f2df09a2c79d913c1e4bf1a28',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'fb514cd92511042485f3695cf4e81d383307f2b12cd6e67e5b5986fe2e7b8873',
                'hash_atual' => 'fc6b1d017c2c1e29638cc0b3d08b760a6ab78db31c3f586420819cc4f5c1d380',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '640d3ca8c25776dd54c28f27461d6ef02acccbae889979e6afd2deb80970d17c',
                'hash_atual' => '03c8f889c964531b7946f4c487362cf086d89c75a3bf4790e34ee96df7b906c2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '9ad24f6cd49af1562b1fbee5ce8f8827cd2c0714d10ed490ff2be398ba174045',
                'hash_atual' => 'e24c393a3426d1e76f91b10db0a5d6f1ce84a08d5c4ba9bb21d758ecbd0c9f20',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'b720f229c4652ef3183776c1af6363d4e34eaa907b3f0ea3f13bc3fc7846f178',
                'hash_atual' => 'f39c5d2c9ea1eae82117ebc0918d729491996003b0cbe10813ec8ab5659ec2d0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '41b479c9da5690ac80b9da086d5abbff0f3533f57520d213698f9f175ea9a3b9',
                'hash_atual' => '647d4784b62bb7847afa80026b7f1553ad814150fb0457c83e32734c0877e0b7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'c62028e16c0c2aeb65799081e8ee82e8b9eca36dbb796a56af01e98b714e2fc3',
                'hash_atual' => 'c2159cd51c8cc4b0bfe23c84a7101a6c832e7b3f1f665c52bdeb0c6a91feb858',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'f2512101f3ffbc2be10df442bfe81e9de1b5e739267ca422e79ab37a967e64b5',
                'hash_atual' => 'd829e8438e198de049265d12d54da2bf15b870b64fad8c0b9f80d28f93c90c77',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '7fd7dd3308295ea5a39cc63e98640b388a0c3b159d22bd5eca97fa40f44954c2',
                'hash_atual' => '83fe6e239a81f90eb81176e2396c706416884ece6e1b8e79e170f013cde997c0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'ac1c7cdb5504c8762c1413a3e89ce4e09da0ce4a08e819373b8bd72a43b5a401',
                'hash_atual' => 'b11cc660ee078e27f57fae555fa58a76c013347d9b4b7cbf5dddc219b33d2543',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'fad94f5d9240d6daed54dec1fd596aa2a332f083f1c6f14f224989338c9127f2',
                'hash_atual' => '4e38c59588ca70a6cccde655b3b39bb4b60933da3868d52ace590f05a0b76a18',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'a65bb6742e0a7013fc80a3d2f0ccbf48f565f3d7a63bed76d4b6437fca959657',
                'hash_atual' => '23e6f2934b330fe97bfa25d78e7566cd5487727bb5800b8b143a2332e19f85db',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '3f1162e7b6ffd5239c87245694e2eb1d35f9ea555f4f4611b25fcf55147fc431',
                'hash_atual' => '333fbd7c6b2ff902a2b0a81b8ba747dc9be30dacdf4ad5c0935888990936aa43',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'cd49e6793c8b3a50ebd7396ef4ade2e2b232f3db6ca9ae322ad3ade6b3131cc8',
                'hash_atual' => 'f63be163a22e9c0b47b33df9e52638afe0165cd146334e4c1f3bd6d075e56805',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'ac39482e34ad1a951c4c328e1000f1b27091ce1d2d36923ecad56521612a923a',
                'hash_atual' => '13b5db886220f181a88556e10dda352404d3c200afccf8af7348cf6d563b84f9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '6dc92dafd9b968ba65fed8867405895a48053e4648f758b695a300d41c60e727',
                'hash_atual' => '4fea8358ac304a8187da429219e44b258f7b26057addda19ec6467c1401e8933',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '68f04f34172ea8c41d0c6d1d880bca2e2389e447ba869c22d5ff4e2fecdfd6e5',
                'hash_atual' => '71c5abddad7ef0efa1e01d2fce22d06ea601a13f5afea04be825b22d2b496f55',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '06ce0b32f9b8f0145b5c3db9bb258796f1ce0dbcbf9148e25f1bb6bd5cc15c81',
                'hash_atual' => 'c1a4c5d285c67a31fbf754c26a7bd30c93ab1d3791bd80b0eead7278538678f2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '7bcdcc0dfee78377bdda9b32da1a6fce92e2e33c9ac7048a26de3ece5518132b',
                'hash_atual' => '587833b9bf8462d746873a2aa350339e8c60d9c79a0c59cce1bf281178b8b9a7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'ac7b3c80546a02ce4d9bcafb7d2028bd66c64a0c8de6e5cdc1b3c8c78ca0cf71',
                'hash_atual' => '5b2b3847da1f162d8cf75c2c12995e6325dcb687f0da2d785d99df94e5a42c8f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'c18601d967e691964828e905a36b6f09eef7d10fd7f76b81a2c920889d2e21df',
                'hash_atual' => '04afb2beba2018224b00d1f4cd2ab55abcc8a29e800b5cf8e114dfe5f167ac12',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '498aba8f6147a93a66c0193a56fc5ab2d00edcc77615e2791e7285241fed3280',
                'hash_atual' => '9782db006be71f9c3ec3204a9ff572276a05b6c52f1a7fb0725a41209b8bc7da',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '7a995b5bf4558e9afa47990eaf14b8c0e5cbd2eef7a1c89d06cdbd0321a205bf',
                'hash_atual' => 'ca0f4b2f16131c5388bf6b52ac2c0e8c02b834f06de9890d658fa601ca0350c7',
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