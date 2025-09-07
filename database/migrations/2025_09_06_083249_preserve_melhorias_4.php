<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-06 08:32:49
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
                'hash_anterior' => '2f178eb37ad5128c7038ca73531154a8faa2c847b1a6b3911d274119872251e1',
                'hash_atual' => '77c6f7b7f7347bb56be53f4eff7f5f1f293bc1724d16f07e9b795dec4e446a0e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 176443,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '982d5c60c55544b5a594581d710029efd5f703736fd34906699bd1589c17bd4a',
                'hash_atual' => '051de1a83405b0a22114cdd8e69ff060ac11ee9465dcb3f0ef823aa104c87ab7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29133,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'f9540205324e82090585c233756dc7dec1e033747513c7afe1b0ec2f149bbe9a',
                'hash_atual' => 'b7ccd23410ea9d00dc85884e99427914aa58c42594a39761ea8cd53b603254d0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '46bd7fd09cce9ee71e13b796ae3935de1106294a0fd12a844c47e905170c56b2',
                'hash_atual' => 'e729e454241544b413c756957f8f6d5c5a052c11d80abbb3fe48b3908682bf70',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'eddb0b65afaa58a258f7a91f95f41d9ab7a6ff2e15c3d48f4262805b87bda9e1',
                'hash_atual' => '3b8f56f808f3870eebceef87a2a8c0200e759aec5d04582555e0deebacd1f724',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '7e0970e0479c53c9d21b17ca100c543c1cc20c3231dd69a409b3d98ff8b82287',
                'hash_atual' => '49b747d7c6466ab493f3e5bdaac3149619ea7756c4a6436972205b69d7234987',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'a7ac55e881b1f7c13ee0a6e7b1ccbd05b41d0481e83b83ede97dfd03017c95d2',
                'hash_atual' => '0ded52de94bc2f6543b7366e3d6932ae475bef430572e7a070b46da8a4ddc8b7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '3fad5ff5dc81737260199be6754211888c28d0b50cbdb56539eac247fedbd5e1',
                'hash_atual' => '7be0758d6d8506746ae8b90a6f70c7f1a419b72988103ea3a1efecc1190db260',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '639ebe31093df07101494e8660883d1cebcf14d9545299975b7c9e70ece5f09d',
                'hash_atual' => '66682f3395fba255796f62904fd72f1e730b18c867a90f936f8be07b03c2bed1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'a924fe6f089a70492a9c04a625d1cdf80cce44f45164c614753d022c575b73a3',
                'hash_atual' => '8dbebdfb107faaf5b2a6075ecf20120c346c83e419e57efcc6b22e8010b0fd1a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'dade6fa707fa7aa1c11ed6716223998d97170904dcfa94d3f32fac7d9b9d8942',
                'hash_atual' => 'be184627fa26154fbe4e25ba1a0ec5e8ea3442588b61c678503aa2d9b723ff9e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'ccea401406354ff593d1483a1b1217e5409cb5d0ee3a9e053e84938551db9997',
                'hash_atual' => 'fb514cd92511042485f3695cf4e81d383307f2b12cd6e67e5b5986fe2e7b8873',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '112170caa1dca22ceae8c25790ada948a31eab307c4039fab99f9ab6fabb597a',
                'hash_atual' => '640d3ca8c25776dd54c28f27461d6ef02acccbae889979e6afd2deb80970d17c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '92144d58d2a6e9c26667bd28be7eadf8ad3b175249ce66396d2d63f4f6d9753c',
                'hash_atual' => '9ad24f6cd49af1562b1fbee5ce8f8827cd2c0714d10ed490ff2be398ba174045',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'dfae1bc2861973a31ad227c90b8220c2d1e85dfffd82ac589dbefb3464891190',
                'hash_atual' => 'b720f229c4652ef3183776c1af6363d4e34eaa907b3f0ea3f13bc3fc7846f178',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'bcb8c4f8b0987082618763e0e7a52150e318e613d2043ff092ac8e6cb1f4c207',
                'hash_atual' => '41b479c9da5690ac80b9da086d5abbff0f3533f57520d213698f9f175ea9a3b9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'eb68ab913a78cb8030d8d78504ef10c777f5e06dbd4ad64d530750e10c9a9639',
                'hash_atual' => 'c62028e16c0c2aeb65799081e8ee82e8b9eca36dbb796a56af01e98b714e2fc3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '1740e8a7ef81f86ff775f549a902ff8076ca21583ed8db852f4741765e6c3ea2',
                'hash_atual' => 'f2512101f3ffbc2be10df442bfe81e9de1b5e739267ca422e79ab37a967e64b5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'db1a2934143fd302c15c4bcfd4eae711be5707e8cfbccfb33d32662abf577cd9',
                'hash_atual' => '7fd7dd3308295ea5a39cc63e98640b388a0c3b159d22bd5eca97fa40f44954c2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '55d7311f0b741b2265c7b1882a74b649172ae97af3d2b091dcea728b932b0901',
                'hash_atual' => 'ac1c7cdb5504c8762c1413a3e89ce4e09da0ce4a08e819373b8bd72a43b5a401',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '9129be2f980716587d4a424534045a5b67554cc18413afc1e7028b34059d10ba',
                'hash_atual' => 'fad94f5d9240d6daed54dec1fd596aa2a332f083f1c6f14f224989338c9127f2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '1d285315eca838b58a82230597666721f13e0ba8c47d1adc7ac2ce04541d55a1',
                'hash_atual' => 'a65bb6742e0a7013fc80a3d2f0ccbf48f565f3d7a63bed76d4b6437fca959657',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'fa48d051a4239abac862e6bf6bbe1a3d567e9114c616ce275e8e40a31ad0d3ad',
                'hash_atual' => '3f1162e7b6ffd5239c87245694e2eb1d35f9ea555f4f4611b25fcf55147fc431',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '4ae3bb384ac551f66ee41bb6a31d0104c9a5ebdab35f4a0c933ddaac82474823',
                'hash_atual' => 'cd49e6793c8b3a50ebd7396ef4ade2e2b232f3db6ca9ae322ad3ade6b3131cc8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '26e0917ac7fff0d9c535c1d81bb4521733e3e2ec2c5e26d402dfb4927224de83',
                'hash_atual' => 'ac39482e34ad1a951c4c328e1000f1b27091ce1d2d36923ecad56521612a923a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'e1ba6f6f0d2670fecc169b85fcb268c7763909b18e4fc679871678073e58e025',
                'hash_atual' => '6dc92dafd9b968ba65fed8867405895a48053e4648f758b695a300d41c60e727',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '8b19cbe9bbc6d4d555daace024ea0412afcfafc0be86402083294729cb8af2ce',
                'hash_atual' => '68f04f34172ea8c41d0c6d1d880bca2e2389e447ba869c22d5ff4e2fecdfd6e5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '1e7005b2b086f848b4aee2735454c2c544852ac0e443973d294f528ce9b9c28b',
                'hash_atual' => '06ce0b32f9b8f0145b5c3db9bb258796f1ce0dbcbf9148e25f1bb6bd5cc15c81',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '55f8c66966118527e96fa11293268e6b47bf603a20b4ed45ef2b7a7535c80dbe',
                'hash_atual' => '7bcdcc0dfee78377bdda9b32da1a6fce92e2e33c9ac7048a26de3ece5518132b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '922d12fc7d4bfb16d55ba8586d84599860ca7cede7a640c3df57deec7d9b636b',
                'hash_atual' => 'ac7b3c80546a02ce4d9bcafb7d2028bd66c64a0c8de6e5cdc1b3c8c78ca0cf71',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'ca3b670015a8343922595e105db3907961b296a74deb6678e23273be69fa93c6',
                'hash_atual' => 'c18601d967e691964828e905a36b6f09eef7d10fd7f76b81a2c920889d2e21df',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '067bb9f2f146836ef7a3d1f39bc927b566de39ac5bfdc40218ac45ece166ef06',
                'hash_atual' => '498aba8f6147a93a66c0193a56fc5ab2d00edcc77615e2791e7285241fed3280',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '65aa0bbc714ba027e9bfbc9d420489152d7a895fbad712f9dab6ad1c09d45b16',
                'hash_atual' => '7a995b5bf4558e9afa47990eaf14b8c0e5cbd2eef7a1c89d06cdbd0321a205bf',
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