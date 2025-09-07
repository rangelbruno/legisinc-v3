<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-07 01:10:19
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
                'hash_anterior' => '3a419b5835d9846240c8d313e12f7b33842e900a6d0e74a71ecb24f074567222',
                'hash_atual' => 'f618d47175f48e86ae7afee9ce40694895eec85bcb0256bc27d81bbcc82749a8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '580fee26336f4c28c4498fc3561774c6c42a55143639b9df44d1494a075ea618',
                'hash_atual' => 'fa0693fc0963e14951b3f3b6d33d59d62e7fab40f70ffcb925c624df087f7ff8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '796906530b6f3a00b77ab35e3d8fecdd6f9b1900943d42aec3ed1a792cb4982d',
                'hash_atual' => 'edcf569984dc9b00fd4e1f9053e2f0952aba6600ffb00a2b3d62ae50b09503e6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'e6f2947356bc5dad283c2e81f50bf9872a1bfcb815011e9bf678a2e33533aed9',
                'hash_atual' => '3a6d22bb3e87c23c365b860fb0eb7c0ee47ae2b1cc6776095a72acc6c1292738',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'c87414909ada9b2c4e8060317eb9dee0c7f4b92306cedf62ffd9412e7ae3748f',
                'hash_atual' => 'cac332a3e1ce1db18d66f4edf987ebb98bd35f5af187874e2cdfb4e055173766',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'ddee2a28915ae0a786457a2e6252bcc25faaeabd0a3523b120f7b9cc6c8efa1b',
                'hash_atual' => '75256167a7f74ac0ab7c9bf92c3ce774e9faff2a2bdbd5605517f69c9bbda037',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'b41eaa4bf493d8de2c900994590d133ccfb287d60cf55c5941f936fe19844078',
                'hash_atual' => '922028c14e9344ef37709f9b641ca0a914ff0e7b1ec6f8218bb9d3239a6f2b73',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '60be9c88051b312614fc6e68ad6e275fd8be292cf5f4e9603bdc6324d87df035',
                'hash_atual' => 'cde29955bf5d1e3331e0eb8c4138bd19cadc218207b2fe8ab40d02b64689ca39',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '1d417b3bdbf75a3c0ffc223c3d56fef7d001a49add3e5a47d13f17157d27453d',
                'hash_atual' => '72e451e06e93b1d07f6ee41c7a3aadd550dad8076ef0eaa87cdec21ac5ccd2c2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'a1fbcc331480fc40375125543d3e2ff4f7ddcc3fa2efb5ac5366f14105a86f4b',
                'hash_atual' => '27656ca5ab3e5457f21e5f04ded9d1e13a5c4793ee895ddb098ec82612b6569c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'ccfcf74aed52bbcb0fe368761f40186c0172d47524454cd85fb0d3e8b5d0118d',
                'hash_atual' => '44f55495222ee7d5b7b45577562cbc7c037e8c3d6c476cdc61655bf72847b424',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '69d29053136a0a9298dc5ad0afb707db3f73dbb30b4021d6d721cc5853417364',
                'hash_atual' => '337508396c8207fff2754c10f971a6b742c9eb158f7e9aa402158849f3d3812d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '7c4a6120bfa313984ef306fb8c536f515d6f2732dad20d210c5d0cc3744de1ff',
                'hash_atual' => '8c8f0082af9443ede2c62c3690e2b0588b3684bf6f11bb256e17a864d66d30ac',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '920710e30d0ba762712a2b153a93ce5bd2aaa1dc1b394ff2cd17f155c6bb1cc1',
                'hash_atual' => 'a10838f5b36f9cc54ce325df4dd146f380e6b88aec9572a713111d0b66c2e73f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'd651e5ccdf92f6bd51eec2845bf22e4b89c355038f8725bb94c6bc258ad262f2',
                'hash_atual' => '605ee112e0335acbe5441bd6f5c06f312a2ff0c06772ccf4006820b652974e3c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '5a2512365be7407f6929be032967022e758c9c9542dbe5803891c419443a0fa5',
                'hash_atual' => '7845d10b8b58b57724dbad44df8a70dcb6db71d177057f14a562673629a4f545',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'cf016ad89d435a8c66b0a2bc41697e41854398852d16cf56c95dabc806b692d3',
                'hash_atual' => 'dd56cf4d8c5e5eced85a1b56e6ac252a611dd3391a7f593bc68c5c78c78d3277',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '2da2e5c668e1d4a4b7647d1c65cc99f7cf94712bbaeefc39929408c66ac4eec8',
                'hash_atual' => 'b80bbf285c62ef87af049db42337fae3e6c8d9ef31803e483539abb4ff60f19c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'b51b189bb5643c930017ab2833352f1e0e7276bb4f707228864e238d4b7eb9a6',
                'hash_atual' => 'c22fddaa98245afbff494fb97e3829d804f24c5a296f4a922d2cd21152f58c01',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'cf7f1f6833210ab8ce750830d6eff91722b27d6a6bae9357a01752248a6fe118',
                'hash_atual' => '14574f41f4d7e03cf19152e59597f2da1a7d7d54b5a0afd3114d7075f619343f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '70f56b5462082588b4e7aa3bc99989bfc0e37c404bc162db98a5fa5ccbb23700',
                'hash_atual' => '1c60fb7d8d82afcd53d46080e530e068da5df6f055674a7bde92e0f763cc12ee',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'e0c02d5fb603496a5792ef58064f68e4cecc0a00b9069129ba234d8d522f4287',
                'hash_atual' => 'e1c78f8571d5ea4b51c4be1105b0ef341641eb9ca8d24b01da55e2aa0bf983d0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '0cb950ae41a3b8caaa14a500783a71899ba6e0832c39cb94ff7156f354baa2c0',
                'hash_atual' => 'c86546872ecac5623ffbac5b81c046ba23df12cbe6c96f6604ce9392fc1eeb17',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '272a94ab1a92c3fde72d0fbf916a4fd7e6acf2aefdcfbbf166791e80a04c07a8',
                'hash_atual' => '2c11586d55cf400ccff3e818b39969198569423bef861e6048afccadd4f9451b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'adb5e16262d617f483af189273886d6eb3645effb2173ad469991ffabf92dcb8',
                'hash_atual' => '9f854fed843f16195091c338946fb026b8661a8098b2d43369e34ff77802fa6c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'd80b26e35748253f30476fc9877c22a37fcf668d2661ae6b2461649b4612ee8f',
                'hash_atual' => 'abf4f9e3c5c3dabe33938373b5e847a2e48523e4df01a603ff7d9c85905977b2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'f9d400b7f17df023710db6f701b2c1aff89049114bbcd82963cb3f13800f2d50',
                'hash_atual' => '80686d097f4f3bc711235d5eb841a30eaba1c76d469e3c9f31edc33c2b22dc7f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '2c3c70029d0eb55c42123d332a8a51fea6204376a01f142710fe250c32f84996',
                'hash_atual' => 'adff511727dd30a3a65ff4e58fba14619d8b159130442ea985bc2eb8e8b51619',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'cb7af9ab17ab5f382dc0083cdb491171680eb0e27429a8f2454156d922b9ff7b',
                'hash_atual' => 'd219ec5fc1720069f46e1250a4baff24f48914b978312467960783e91baf0f65',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'de46700311054a7dfeae8bca526c4d2828563b22ddd70e21bf1e1a392cca1e7b',
                'hash_atual' => 'ee4ccc612df6e1d9a564f231387d32aa2f24bea89ae5b2c1d968afaab8be0b94',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'd48f1c797710f249bd2da09f729947c2ab3d72c4ea79f7f875bf8622fc30958c',
                'hash_atual' => '20b1290f19736a7b76f1b2c3a1702c7312cb6f261b7750618f9b858c621f224b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '2ef6de416e2b6dc3cbe41290c0b13ce6b622d2e5e2c39e1b2b7fd6f58716492b',
                'hash_atual' => 'ad351ea7da78fb6b3673029484af569e763cd7417076d507241c6cc6366b33c7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'e4d1d29b5290de4029b88d8863c0906b8055a08ba6a415bc6782cd6afe6a9d6a',
                'hash_atual' => '37535d418a047f681dcf0cd3c2c9fed9c407e33d909e57c6371e1de701d17e16',
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