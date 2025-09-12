<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 00:44:21
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
                'hash_anterior' => '00ce7d04f6db7ae9f9b00706de01d7d4eb3665922fbdf0b5c99d91e25e51e9ea',
                'hash_atual' => '17b84c60e394307831468e6193fdc68df86ade76149e40901c6fa1261ecd8322',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'b78b55311dda5f621d58f39cc34194958d53c81b2a06959183befdde43e0f4f3',
                'hash_atual' => '743c45b28b95fe1f2e28d84fb0f2be18ef48ccb141017ea3f8ff89d4e4fc519e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33929,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'd21303e229742ca98efc7bdb96cd702893a2530420e0cd31128ecf07591fe1bf',
                'hash_atual' => 'fabe7ee44e156e931f8a43174ce173c6032b132ca428085ee2dbefa58840164f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'a2c4ce1914de94783c95e5184b1900b3d33daef15f9ea74fe770590df9206d0c',
                'hash_atual' => 'c8306acf070f544504b55b24870bfb56b0b9d0003ebd992c4173c77c6a0d8dd6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '456f5eae4b50a13eb0328e9f8a31633f0619805f8694aa90cb1a823c00de441c',
                'hash_atual' => '0ad24c501d88e712282787b56d8606b0cd4705437b121ef514ac1e9a5551c553',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '09a79fb026bacea6217389ab88d1d0465604a4c6134169b0283bdc1f938b36b4',
                'hash_atual' => '8f63d76b5749813bb78ade84282412eae120e37fcd7e7a911acb4a66337e0dbe',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '9982117a439d6f39be168ea83c5de8a56f620dfc35ff9ecf09d18bda87f0ceb5',
                'hash_atual' => '8ded29185094dffc891358528a4313e63d838e3cadb84beec2ac53e5d2638b29',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '9e27b01582aa957c68f01df0d88fd64901c17243c0b28683dc2ff021e02d72c5',
                'hash_atual' => '4ebe999a68cc41c4ab60e91e14b885ade933d1c40c9e9afe2334d228869ec834',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'f1d739583f84a7e88eb6f96ad19397ea601ac5d0b529b8d92e6a6efa13bec522',
                'hash_atual' => '414aeaa9071f10aca277406ab1a6170a5632eb60a31fcd18ced30561410f5a0d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'd24077f36cb1821cf52cfc8f26f67fd8c33a0ffbc5a3823785da3ab7e0c6e763',
                'hash_atual' => '9a48cfe6b59904856c2666a8591da0f24f0214e74b72a0d4ee568dcbe969df8e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'da5a30132a17f0be4e7cf5ba2b31eb083b39f06f40fad27cc7d7da5e7d41ab3b',
                'hash_atual' => 'aa3ad4292172d8027dc07c51b2ccc3318d20d45bdc9fe325c37fba73adb9376f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'bdca26f0240809991518d4b88fcc5f838fb7ee7a08782ccd1d612aa8f8f94c9f',
                'hash_atual' => '3a4ad0471afd63b2fb87f54136783878c34840caf6bcaee13a7417d0050f9212',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '0a967d8db6e489cb812cefce5637e91c0ee81cd5ea4856d73d838d6c7b3de30c',
                'hash_atual' => 'f54508c50b5e0c7190bbdcf3738323ace49e2d57ba55dff5a0f8328a62c2689e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'fe73e797a781edceadd9e28174cb98657c0533478bce89a4ad1f9f984639fdfd',
                'hash_atual' => '36424bf1e3e5afbee8d641a531f92c77c575745672d9413ce4e8db940150e5a6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'ccecf8cb392ed240eb71e8dd96063e3b00db059a7e4d1053364e26bc178b6d09',
                'hash_atual' => 'eadde6baa50504ae3716f13aa902b832cf4c62dd49aa6233be96ac9ea4e8a162',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '99aa038296da8f2596ee42ea21bf94495c540b671e7c094fe953121ccf53e109',
                'hash_atual' => '61341e83297329751bbb41a5a3d7e517573163456b0da961f6dfb50f4e6bf127',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '96187c4e030c3f00370e8d9fa5cda7aef938aae24f461891d65b95764e095108',
                'hash_atual' => '8e93cf7d6deee051d748fb4e07572b5c992af13a1b0dad3d7c37d874f7d1d1dc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '76533bab3f06d323f00927dc01709488251aae80e14c203fa3e5bed8576c0e3d',
                'hash_atual' => '2f06ced38319e5e32566a73af8aab9616615881394e7ce5bb3d38afdf3d48c89',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'f4f9a836cc839d9ab523e95b487d59805c28de0035518ed4aa75f6281eda82b2',
                'hash_atual' => '64a321dd8e6e1472a34e0a092cd10071d97654deff5cf60fe5110ea61593138d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '56341b3b3633d2bbd5dd3245e4f2ac4e20d3ee182c2f5e804016482a7ae204d7',
                'hash_atual' => '806c4ebcc5577d90a278cb3dc14655ab2a619eb439f4c857454ca4e163202ef3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'b7880fb73e83c78053d9e57b8a9d7f4b3ac9eb98904eaa978a937a4501d57782',
                'hash_atual' => '4188b638dd1b687533f424ac290d5ecdac0b0cdd4e5bfcc0d19716c7ba6491f7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '10c37f48db1c4d156712f59516b8127fdbb5cbd057d6cf0c017c5be2dddc3173',
                'hash_atual' => 'f19cbf5cb292f3873648d708b1d6537e20124ca6bc04480e216956c0a1568715',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '665ba441781236e72a7101f80168eb33e3b3e2418a006f8030082bd0caa86f8e',
                'hash_atual' => '46063c7b32078e0685838aed2367ebad1dc79f7cd2e4170cd0aaa51de418948f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'c2155c239544475681276d0d5484262416794a6aea8ba61240c0cf21e7cbdbbb',
                'hash_atual' => '4445723a890b49453f856da7f588c7a191a43ee769b88c7ed4f3a9427dcbb8e5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '2ed2d21921b4a9af85493303d1d725a4daae1fedaf555f56adaa871c3b9db32d',
                'hash_atual' => '08a359e259512a725bb4878d6100e5398e9322b0c942c7fd57d7edcab56a428b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'ca2bf74736ac57e6fd01f6ced32d79bc83f853dc985c99dd8a507d6757634945',
                'hash_atual' => '582449c6c9a0d54b6d3c46c175049e858a0806550bb44adbb019285226997f5a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '830fc4878db0e297300e92c5586eaf87254769607f5db9a923c28ed4adde07cf',
                'hash_atual' => 'ea5369a6db86d364239fa85d4f0b1720d6d7341cd0e567411695a571d64eb4c0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'ec37f71e6c72eb14a1018b0a0a3f230a6fffa6e8e1a3e170fa128ffce008bbe6',
                'hash_atual' => 'c86dc8d297ad17ae28c5b7a8d19c9724bd99759660fb9e8e409414b8887456a9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '8555a35d948fa265fc00ddb61271ac410e46cf9420b60eb35dc6547603f59395',
                'hash_atual' => '8f120a2fea4e77e11def3bba40e2adb43f9e5a9d24045ee439f6829c83da9034',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '89ea461ed314a0f61575cfe0c32c4da4eb2f9096c764d6de0634ff50848812fa',
                'hash_atual' => '6d660c6e01c3a593c1e0042d8443264e3bceb472218d239238deba28ef3e033d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '6eac5ad4e243644714940c5cc0a6a8078e47f9637b4ef72a6568dcd163f6dfee',
                'hash_atual' => 'ce716418f77e2eb05ee3556f041d8c6d308ee595159247ff8264376c59561e47',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '0aba6410212ce186be0b40b8b6e051b09b6cc7c210e559024589f37e8b6d3276',
                'hash_atual' => '3b3b1c3b670b108132ed77641827379e9577f46aeeab5940011eab97af4dfe0a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '6cae5dbe4090edc6a6a0f0d664963824b123d103792ef74fc6c6d08b9a615ea0',
                'hash_atual' => '741e5ed6561073d8a68ab24dfc98029c1bb175b088bbb484d5f993e171906edb',
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