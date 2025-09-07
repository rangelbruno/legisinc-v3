<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-07 00:00:02
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
                'hash_anterior' => '5e6c3c717c136eb056e9f4759ad707fd6db07c6826208eae70c9aa0e7190d0b3',
                'hash_atual' => '293600003d8ff6c319f7d3002ce375f3ab43b1f0c2a4b4e26f80a6b031a6ecd3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '58c6a6a70446461b7d991d2f35589953201c6907cef4d7b651b57eab316255be',
                'hash_atual' => 'dbca528b7d51669ebdff514caa2c49162e1d6476013dc92fe1e18717104fa63d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '0fdfa143eecbca54285fdf7699a684eb722b1ede992d000cd280c49ddd400723',
                'hash_atual' => '0e6c73771a2f3810ff6ae5365fd16e2e58cb6404f5d823155629f7fb6c3699a1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '128c35e02a7d0c8bb4b817ed35392d3b9f25abdf95e4493c1d8b220d4aeeb903',
                'hash_atual' => '996e7a69ea4d88232669bcaccc622b801d5e20d18ec870d19a077e2e228dc24a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '00d5c80285f474a7be47b2c4782eee9a24a9493519f893d3d363f54c95507ce5',
                'hash_atual' => '79ddc8047eba09a6275448c9c2c136df10da1e999a0c8d8f5fb0e9b60e26b692',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'f93fe753194cd1658ae256a640f9780925bc30270f47239e0cfafd650d3262bb',
                'hash_atual' => 'ef10b5cb3a0fe9139c2f7d04a3f3d615c44c93615855349c5baa5918b1857162',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '789c0d6b3e9ebb5076b90d5aa66911d3bac16c6bdb157fc9f68c170074675990',
                'hash_atual' => '6f1390afb492c25984490dd988769a98f04f2615cdd01d1af67e1147a643f712',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '0e4376cfd977b0760651c90802680a668e28124cb0834ae00e19f1b96ccd2d15',
                'hash_atual' => '0f07d21e9711879132e45e348e3cc55ee4a5c5d3818ea964df246f7b4b1e6be9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '78d8757ba686924b939dbed1040d790e6532335c7514ac9a3ed8d3989627bf4f',
                'hash_atual' => '092c342122d2f7e971b54e94cbf1316604cc33e68ad4e4048fb2ce5ed038780b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '01a5606bad79ad7edcab6783c00a77a6d77e75346f6c637fac7486abff44d943',
                'hash_atual' => 'ff0470deb05421f517089cc99f3d5dfd1b8cf1385572f4481e42f4b0a3ae0f99',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'aae6e06034ccaace89a39a67a0b9d67f5b4a365f2df09a2c79d913c1e4bf1a28',
                'hash_atual' => '30c45b0c16b2f2abd4e45707c669a957344b79b8ca9c133173388be6bf96d490',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'fc6b1d017c2c1e29638cc0b3d08b760a6ab78db31c3f586420819cc4f5c1d380',
                'hash_atual' => '949c3a4c4961f6784044e16c9ef3d36de40b601009f89a226cb61cd2ad37d74f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '03c8f889c964531b7946f4c487362cf086d89c75a3bf4790e34ee96df7b906c2',
                'hash_atual' => 'a96ee670b3711feac62847296bd570d6038d2b40dd2af064ebc9855eb464bbce',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'e24c393a3426d1e76f91b10db0a5d6f1ce84a08d5c4ba9bb21d758ecbd0c9f20',
                'hash_atual' => '753081f3221e6556a10fbe6033ab0c8d00dcd9153efed74c8324231155dcec61',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'f39c5d2c9ea1eae82117ebc0918d729491996003b0cbe10813ec8ab5659ec2d0',
                'hash_atual' => '59a5bb31cead22722bf84e35efddf12f51b665f92b4e2d351d8559a09ffdf811',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '647d4784b62bb7847afa80026b7f1553ad814150fb0457c83e32734c0877e0b7',
                'hash_atual' => 'f216b20872664781a1a6149fd059f5eb0694a32a239aca5275eaced66c568c25',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'c2159cd51c8cc4b0bfe23c84a7101a6c832e7b3f1f665c52bdeb0c6a91feb858',
                'hash_atual' => 'bf05848883ee985c9af846bba478f709ead184405ca0921aed86a544c95d3cdc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'd829e8438e198de049265d12d54da2bf15b870b64fad8c0b9f80d28f93c90c77',
                'hash_atual' => 'e2840c4a0bdb26d959609334bb96ca6f15b04ead43c3ce22776aa10ec12c0d18',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '83fe6e239a81f90eb81176e2396c706416884ece6e1b8e79e170f013cde997c0',
                'hash_atual' => '84e3bc50cf48908a427b8a165b52228692ae7561104304875e6d30a795a5f430',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'b11cc660ee078e27f57fae555fa58a76c013347d9b4b7cbf5dddc219b33d2543',
                'hash_atual' => 'cd1931e2c47fe239f117c299f5a2ae84c11a9c57691510161358b21cf6e06048',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '4e38c59588ca70a6cccde655b3b39bb4b60933da3868d52ace590f05a0b76a18',
                'hash_atual' => 'e1940c7e6ac0a43cf264ee39450fda0afe009a7588e1b92c51452b7b00d05e46',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '23e6f2934b330fe97bfa25d78e7566cd5487727bb5800b8b143a2332e19f85db',
                'hash_atual' => '9cb934147f4ffdb184109caf531208daeb3043f0a22d0590426013200c96e52f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '333fbd7c6b2ff902a2b0a81b8ba747dc9be30dacdf4ad5c0935888990936aa43',
                'hash_atual' => 'cb6ca3de2c35c313ccc5abd6c63a50ddabf723eaea268e5414c16cad857c1290',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'f63be163a22e9c0b47b33df9e52638afe0165cd146334e4c1f3bd6d075e56805',
                'hash_atual' => 'de0c16aa77228a70d5fd543d370df6928e05953d563b56be61fab8304413ee57',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '13b5db886220f181a88556e10dda352404d3c200afccf8af7348cf6d563b84f9',
                'hash_atual' => '79753af551d4eb0f8aea5dbb87c5225f601693050b8635fe28293dd6581013cd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '4fea8358ac304a8187da429219e44b258f7b26057addda19ec6467c1401e8933',
                'hash_atual' => 'f27ae47e4bf4842e275eb597ab76aab9c42514a53a5aee84b382931dcb307ffa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '71c5abddad7ef0efa1e01d2fce22d06ea601a13f5afea04be825b22d2b496f55',
                'hash_atual' => 'e11121faec6031b35888f8f58dbb5a6d03b114172545f45d80853ab3aa7c9232',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'c1a4c5d285c67a31fbf754c26a7bd30c93ab1d3791bd80b0eead7278538678f2',
                'hash_atual' => 'b085bc91b9c7cb35353e66c3e6a9d62bcc1a911d7f270fff0382e219b7e878ea',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '587833b9bf8462d746873a2aa350339e8c60d9c79a0c59cce1bf281178b8b9a7',
                'hash_atual' => '8c5cbbb7c83099c5cb3768fea3c012b58db0671525959f10705276a97adbeaa7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '5b2b3847da1f162d8cf75c2c12995e6325dcb687f0da2d785d99df94e5a42c8f',
                'hash_atual' => '5a21f1e3b5d14161f6310144d12c8b867488de62cf9cc8c4e924d8c52145317f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '04afb2beba2018224b00d1f4cd2ab55abcc8a29e800b5cf8e114dfe5f167ac12',
                'hash_atual' => '470f733485fb06105c41bd0fe6e4fe72f98d968d025758295b8f42b942466aad',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '9782db006be71f9c3ec3204a9ff572276a05b6c52f1a7fb0725a41209b8bc7da',
                'hash_atual' => '4be98296532b14fd15b36e9f596fd4e8aa3a01d51699e8f7a0c1f3f1612ce468',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'ca0f4b2f16131c5388bf6b52ac2c0e8c02b834f06de9890d658fa601ca0350c7',
                'hash_atual' => '2e3a592c13ca5e370a35162647085ee64ce03e34ca9f4b4384adae40c690b2ea',
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