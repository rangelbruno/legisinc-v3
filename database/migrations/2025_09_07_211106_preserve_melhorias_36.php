<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-07 21:11:06
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
                'hash_anterior' => '91b89c9c361a5d2adc95ea127060bff9e44128bd271b3b179579d539ab0b82da',
                'hash_atual' => 'ce6c6235ec17470e13bbf26570c19ee466a582e81f707ebfcd1252d7569212ac',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'fee4cea44aefeeb247cbe69e914f00c22a4b070abf06d2d9c600942ec792e7de',
                'hash_atual' => '3751e3b241229c68ad6d7cac59460970b17786975ae1dd83b5c4664b1251521b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'a4b8d462a39fa9793711e5d0ae38d4f8f160cf48d564c73e085ac306162f657c',
                'hash_atual' => 'aa3d64b2e74eb876657d48548cfbf39ce86e7663f864fd481200770e7840ec34',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '81b6c84727d7f56751e4c6dc83d0da181bc27731f6257fbb3a73524df68a0219',
                'hash_atual' => '50c279b48de2bc86213cd4e84c3bd6707538262db8c024a934e08dfcc8f2df73',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'd75c41cb801f72005f4452d3e90de1bb829074c91439e7e391983c1471a03445',
                'hash_atual' => '1984e9ea0a1117294c0c71996a2c74256ea6ad2a068403b9f9ad5f72f60a0ec6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '44be26ba38376c121a143f08245beffc534fbc90d26304732907e5ef3da381b5',
                'hash_atual' => 'a4304518e2370ec98156c980b780abeff7d6bcb33ebd464651ef83f0d3d67974',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '53b08c31e103919791173bc1be733609e416c3c1b976387739442c02f82b01a8',
                'hash_atual' => '928c439e92c628ccb38477e50cae4cf452b174babbfa7ff7791f68fe6c0849e2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '268964c76071a8212aedba4397f64939ca8181bac0e7df2a906c20e7eb1a5bd7',
                'hash_atual' => 'c5f013824be9e5fe4821ecd8ccf14494a2dbe6113a2bf775d82f3180c7e020da',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '08305c0e7dbabad084b7e207124c3ffdcae026a94f552a1a2e07329c2df7af6d',
                'hash_atual' => '7da2c4354076fdc6c0d4b17d6ef0aa91882db6a586f5f1b84c554a63b409de25',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '4a68a39ef93a58b8943f58a19c1b76daf00dbd300b0fd87070d9d41b428e3192',
                'hash_atual' => 'fddad7b10e01baea59064f4e3c3a4d12faf49f4d7d4c0bf99a1c018615a3a5e1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '3bb60d5fec979acadcf9b69dd3fd34adf9ef46d1c3bb868518d61a2949c16368',
                'hash_atual' => 'f7393ca47f581f9b2f0af570c5d75253996d5bcfe3fbec606b2d6330dcd44381',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'a6cd44815560dbbd21c5d4a0c406d4e09b02c510e35712eb4c363338f46a044e',
                'hash_atual' => '06d177755414d4188068ad6dcd099c764c08189bb08c34b0e78c6b6377623ad5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '2e8790f59881fb91e9705f797fab83ac9440d429e1c986a41cb51b451262a850',
                'hash_atual' => '37e6cbd4489e94e505b75afc6f5ed52386ef97d73458cb07d511511c13f991f6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '1fb67d975eb1ed2620ef8c78a2dfa1cf4133d2f5c79b32a5c27d19561a8054f9',
                'hash_atual' => 'ad9c332dea4f0e4b43b698c4b8d4792f86cb7baea45fbbb52a169af7a65d9626',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'c3e846de2788f8e27686906210fafc2a0fef5939de86bca15f5f613167fa5267',
                'hash_atual' => 'a03a50d9dbd06578545ba33d08794b8e44fa2bb47291dc1c825fe0fbd927807c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '8f05219c1e7a4871fb4c1ebea0c283bf11de6fd010342e42d538e19fae735216',
                'hash_atual' => 'bcce1a5748338e154b5352ede294875217561a242a6b6692deb5cdc1dcbb8885',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'a91655da9ebbe6575da8314bede25ded562584d96fdfa147ad787600179f4aba',
                'hash_atual' => '02803aff5b3757e0a4fea74ad57e06231715bc91222e824c41c5c59813dfe3c9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '9295c08de3fa8b05a5a30a837226b7db8cf4deca5a5a95294b3ab1ad1aa26c37',
                'hash_atual' => '4e4cc0c47a8ef60f6b007d3abcd2410a29d1d0950ba36ec6d91c4e70bd446f04',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '90d79f183418b06fe71b96a3dec9f08f967c400ab27433d7ec63e8e1177400cc',
                'hash_atual' => '5bc45a8893d21dd80cb5650eeee84ca7850cef31f1d64ed77a4f817c6631c753',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'd9c23ffa121edfb31ff0bd568132a9686f2854299c05b010d85b1790e1982941',
                'hash_atual' => 'a9720a423e450d706a74b44c42d648bd2455f21197a2e5f79f18158ccccd407c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'f6459e1b4f8d02e4fcbc4f5f06cbcfcead2945dedd017d8b53994242c2a3a915',
                'hash_atual' => 'a6fc637b0eb3ada47f94acf861b36fff35b8db9ce2165d5c420fbc6bd81dd93e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '77e28accb4bad196a2258eeb23c3cccff74282c3b4ebca36117ddc88ffa3ac18',
                'hash_atual' => '816506c499a3ce16be1bdbf8a2cd22af33bf6b30b6ee2ed6f18eeddf4bf99301',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'dada87709b63826991d6205f82d0c9b18d27402fbdb8cb0a1124d131c83b5f52',
                'hash_atual' => 'fb7eed18fa8b5d6a62a89b0e7e2fcd1f86ecad7e586ca0feb38b0df4c08ea521',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '466ec16bcaab28d24fb797720a1b905a002720da2cf5392ced5ed644ec66cae3',
                'hash_atual' => '41efbf68cc2be03557515022bddb215c3c1931e93b6a75ec132618099ca07b3b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'fff4150729a66b11bab11a3fe74938a5ce82e1e3f7a3c41e6d2f6ffe56d19b17',
                'hash_atual' => '72264b917fba6ce57e8f6f047dd2e3f06a235c01d921dab606f3fd66bdcfb67f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '1170f02e2e9a5af3ffa82afb1bdbf4a3b85e8685510a7a7bc6ff1aef867d835b',
                'hash_atual' => '1a10cf776c7398f3956d4c0a5aa11655acb0d00e89bfd03e1d6b47259a096a6d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '1e5cbc1a7d87292b82b5771e78bd90ce19f05e407d14c9bf6eb1a545e3b3dddf',
                'hash_atual' => 'c04a8819701026d411153074ecf17b5820d810c92eb7ce552147d2bcfc299548',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'afbe3819b1878323daabf8a44f2d503dade161b74dca4ad9a82ab6d5635ec4f0',
                'hash_atual' => 'a8bf4b63b65003989f0a4cff0316c43bbc99971883ed4043584a8ec8ee1c30d0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '6f6d1904dc0e03f89312986caf91dc352ecefb7b37150adfe5c1402665d398f1',
                'hash_atual' => '4ea6819e21e74f0e9f242d86d45a0d03bd4b365ec5e0d210f05ab6166643e08e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '516685e94c32235232671526adca20bd5a20efcd4bf7fc4f0cae46ee0c31375f',
                'hash_atual' => '86ff45c67aa7acfef7acc997b6d151858b7390aba4572126d91e7fb5a008c61b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'efa365cd4b33c8c0150c3eb26595c7969ef094d4b43a141ff8680da2811d8f49',
                'hash_atual' => '0b428f980dc6d67ef251c7fbf157dc06d72d47b08f12eecf7beb6c83a9883c8c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'ce6310eabc3d872e9f4c1642f572a28ba65d5feb835d719bb8cc248dca578382',
                'hash_atual' => '46b33c6e5c68c8ea5e37fda893ce5e451224e5998ae4d5bc2630b0292ce955df',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '6cd081750eff889d9782ec3fa63dc5c303b87eb0bd963ce5bc0926bbba314192',
                'hash_atual' => '7767f05861035a17b831d435497ce0a0d1e5895189afa3e55a45c9841d657a37',
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