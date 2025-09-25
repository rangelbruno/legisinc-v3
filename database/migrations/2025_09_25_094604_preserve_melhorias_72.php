<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-25 09:46:04
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
                'hash_anterior' => '535bade96f0113270985010cd8c4f48e80d61d9084b68c35c42218072aec4a98',
                'hash_atual' => 'eb888666a3d1497953de02437db280b01bd657df93f1248e87afb7b41b7c47c3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 200514,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '5b352bea46ed0d79c2e26b9cf14f14acd49e488f9f9a2c5a46dc104a166d1e79',
                'hash_atual' => 'b18bae31014094823e1fae11a680c82d33f04582d63cb025bfcd2f15dc6669a2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39773,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'abf470dc15c7338ef5de78fcd0a7e55ca91c885ab23abbfc2877a6e232d99f72',
                'hash_atual' => '8429478de02706869cbc108d191d22ca2f1ad58bde301436619c93151b378284',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '15b60e678d2c5770060c43e782b6f2dd5e18032d230546b7cd79207125075b1b',
                'hash_atual' => '46a2ab2fbadf65a9104f002cf5982666919bf31d8ad62e76ce1fe14aaced13c9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'a75ef3be13cdad1044d6640bfc1284987be565d58f661341d5729fc15ba1094b',
                'hash_atual' => '7e0713e1fe84e1206fbc16afed9e60bfa0319de48c4c4de36321ad039ca6d6e8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'ecedb919cb95cd013e7cc45ba5459e3ff72953642758940ca7c0740ee9b7b0db',
                'hash_atual' => 'aff1ae023cd9b783c2adfb8f5c86c280c92f06e40b55196d4e4054eca741b35b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19782,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '2420daae23b51bafb420dfa84145ce0b78d87ecbfc5132ccea83a10494c0a983',
                'hash_atual' => '85a2a3c795469dab228d6f2d0f51d6ea7a66db5f26c57f1baad8682676bc8272',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11654,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'd2e5be6aaf906d422767a289b668fa63670825a2870e55319eb69bd72581646c',
                'hash_atual' => '72cc6ecfba3c58e85a77ee0d04bbc4f9d9c2fe61d75dc7a2a7ae9ef7b17fefc1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '4a326d162141c4b3ec4079236d140fdae4fd0a3e32de439daaf6242b4f1bf108',
                'hash_atual' => 'd90eddef35d0513960cdf6ecf1d495ee502597bbd4f29ecb749da1455928d707',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 71172,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'd238463a1e10d82e95f9f0e9e3c609b478dc0571d04448ec2ef273c47509803f',
                'hash_atual' => 'f0c27befd39fcadeb7373a2cb89022a0b7ac412fbc26c9b34bb18f3a32e66216',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'ea5451bd6b79a38f50ea39d89ffdbeed5db26864b7da9d8cebb591bb95599547',
                'hash_atual' => 'bb9ac02bd98c2c2f67a91fcda8258aee1416d24baccd665e7da8ab0efb69a756',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'f5965ef0f9e1eeb6800ad414946c75aaa3fef1cb85b554b3848e23561c41cc6e',
                'hash_atual' => '36eb2edcf9bc3110fed116e621fedd52eeeef6015c4e15a8faebc7c6f68de0bb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '93a54d035049cb945e61568d901b48f39d2988eac5df9def26dac2f5543ace7f',
                'hash_atual' => 'ce83c3ad580882054eff8db7f03d61028872e572d5495a658a965cb2ab2e8490',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '0654b028cd7f6f14b481ca1582b88db4f9b63846890a9f09f50368b98c266a67',
                'hash_atual' => 'd76fe49fda24353d210e9b0a28cc185d32d5155f38552e7a608a2862826f15ba',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '53cc3379a64ee3a7994d02ded25ef3bafeebe610b36f0c58147e56b3c67f3d41',
                'hash_atual' => 'd0ea87b581cf356d90e73b2fb2d16cdb36e8f4cf2ff574055763434a2e9ba9c6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '9c5724d0432a1826ff16b9e8ce7f913b5bee6764affe33fc8e6d25255bf11cbe',
                'hash_atual' => 'c473ff200d1a72690b299e176e9ed4cf3af6fb14f23e5bc3eebe581dafc62b58',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'ae5ee3d7ebbe423f34284a2d02fc3d972e4b82f7876fe3c3223e06592b27eaf4',
                'hash_atual' => 'ae5b97aa7e9d3ff63203f74ed19fb6417f4715b6e87f09a29946d89e7b65fb3a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '9a4ce2e16aa812a50d6612e5d43ac064f425906a6e6076c16af8e3475f570d7b',
                'hash_atual' => '2fdf54b3ee6bdd635a357d3e42d5a6702605814e19a20c58c64a3c48d2da5e8e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'fed63157309a949d983200fc7041f7e4572d91284212303926879569774bed8f',
                'hash_atual' => '1b0b68f6ded333d5bb3ee0bc685517c750ec290b96ffd4192dbe2b24d446bc13',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'f7961da97de2e03595f7f80dca898dbc0a002b5d6d77498bb9befcc9cfd4e671',
                'hash_atual' => 'd2e02e4ac034b40a92a40bb21ac04a1ce2029d78276797cd4a4d5c0d74886fc4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'ec998aecb1d9e4fef4d6756f0b5e428729d5ed92f01e2ed46077b5aa633210c9',
                'hash_atual' => 'beaed2dfb80db1e191887f867c155363e923b6401b822de4c121d7bd478f8ace',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'cf3f36f22078fd4edc12c9ee8fad0cb1dbf97ff5bb7207a65e8f2ca28b172a39',
                'hash_atual' => '009f2ca076332bd79cfd2d5181e1edd7cbcd74e38ed0795b12389990fd366a35',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '5456e367941eebfffe9c23e0f4232ff5d450416f51e5874330f521c188ce3c72',
                'hash_atual' => 'af0ec094d0d45fbf0a9dedcc14a1285d5a27965c5285f361155b5416ba29f7a9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'a8ed41b5e3f152d9216d0bbcbdf6742cf14cb37b0ed642864e5f4ee6f7f89939',
                'hash_atual' => '758bf30aef68ec9e47ec06d9e2e2835e99ed3ceff0f5d84f7154840b956449a2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '044cfef915f306f7ae81f7e939515c252785b80802a848c85dfb19400067ae73',
                'hash_atual' => '60d63659c56cec183b364451d44cac685fe17a42db43fc51d5563e43be4eb09b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'cf3f9edb10692b5afbc768d07609c3bf9389c09032058fbcc1955faf67a48357',
                'hash_atual' => 'a081c2beb2a2600657bd1f248cfd40b02016afc17d067fb5056d256e8992a5d0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '2aca7a8d8c7ac68b7a4f2e7f950b8512f69d8031b402d4885aa554df565b5e62',
                'hash_atual' => '513691a440390ad530c08b93740b34ffdb00ddd5da63186ff0ac6ec6b164ab54',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '8dbcb42e638822da7004cefd9f8ff701c5af5572f3ddb3626f60a9392f23df7b',
                'hash_atual' => 'f74fa31a7570be1e299ebb8f40560601f48ab4a2bddd7c6b8476c48bd350bdc2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '9cb364a56564208ae56b1d68c9f8fb18ea92f76d3c4f1e2c8e76d065ad507d90',
                'hash_atual' => 'bb1ec1ddf61b9259307dc5eddca5034b86c9abf0fb3fc645cec7f719ff5bf3db',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '947400c3a1689bd837204b1740874f6e687caef45c9282a2fbe6e05998d0f8a3',
                'hash_atual' => '3652e089d8c9a9b81314b3cfa6a2bfca93e9dd06abaadcfde1124e695761224f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '6ef3e81caa260a09e79e02081cf26cca3d32257f3598313d45b3a719686fdbcb',
                'hash_atual' => '91dfe965357570e90136c6e2c8356b25e302b9747f4111e44eef5b3e742a185b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '0033538ef6068f7d707893ae251fce2c51a50f298199de007e97de566c21637e',
                'hash_atual' => '116bdf7dbca325827d2890c04c4cd07889ef1ed82c57d3cc337f65e569386765',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '562e99f6e016afe83edf20cd9b93d0751e6ff2c94f0fa80a9e4123a3d6af2f55',
                'hash_atual' => 'ae1f3a5fc5af0c3b620e9236e1f8bc38253e583def86ca03fdd2fbc4faae03ed',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 25889,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/verificacao/assinatura.blade.php',
                'hash_anterior' => 'd5ab675d1566d04cd342da32321e1a1f9db265f22a0448de93edc8f8915cfbeb',
                'hash_atual' => 'b719273d91a52069ab030773c3ed519d4d8ee1fc56adb7dfa6d7b49c53e94847',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 14979,
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