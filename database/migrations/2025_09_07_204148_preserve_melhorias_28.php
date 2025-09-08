<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-07 20:41:48
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
                'hash_anterior' => 'cd158bc4285f42b30e9978be644c6c0cbb07a6de70680e1396b03da6e5fbb6c3',
                'hash_atual' => 'e04acaca63521f47cac102229053a00d03fa41a7b731fc051c49d80cd2d33942',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '9b4fb3fbad995a2efea991e9e51dbd0efcb218cf2daf576c5606fd67acfd775c',
                'hash_atual' => 'dc3d660d27920e6485f2ec33d0bac7156d4010dbb30bd11f70a9b5591292ceb9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'b2aced3a8b3e332084c52bf697058a958773fee8a96eda7d6e8208755a818775',
                'hash_atual' => '2325ff9155aac3f50585c8818b7bb705017ebb50a48379288a4172ecaee4977c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '868a72922baef8562013eb43cceda79cc928cb35f277d5cae7f0696a837f1ad2',
                'hash_atual' => '72e117f1a84bb0ce3e345b363a86cd21c1399fb00ae20079b8d133606a0735d5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '009035723c63c0c621e2d977d178159433e9c8b9af760a4da463504f9f4f35ff',
                'hash_atual' => '646b72658d3a661d8353842836cdf47b1cff64224af4c7cd3d2e1c0526247299',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'eed00931e856a21066e2bbf4ad335b20f9eab33c36df44b81f3a07420bbe320a',
                'hash_atual' => '257ae8243b00192707b106f976f93cd8f34d46594a53df225d30e422208746a4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'ce6a864f3e97ad48195fd53b20d4faf64fffce4035731e3f8e5b3410bd39c376',
                'hash_atual' => '6d52ff91c71dceb00bf3b897eb0dc7c646f8ce986ffc476f42cee6a226051301',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '1ca6eec9d58e1ae3b1b1eee24d1d7d689c37fdbf3467c0ee4623d806f8387264',
                'hash_atual' => 'd446afd2eadbed800d41ea9e6b7ab42ea08ff25b135f6c9a28ee9668f3037ed8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '799b58242d05cfb1b71f8d0e20248202329d8a64dc390fe4cae274a509848d52',
                'hash_atual' => '27d23941b83d525ed859f0e5bc0c4c31dd1152ec2641316977915ceb9924145b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'd7ce11caaf454a9621c61faefb81bac9329e9f967ed2bf546dc1c4242cb694a7',
                'hash_atual' => 'ec63fb61d44b84ca89b89b6fcf2c0cf7ca8a4adc14c15a8c4f3eef619b66298d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '31fc34d56f2b51515c657210bb480784eb3cde2c3c0b5d22a854fa62e185a7bb',
                'hash_atual' => '5e8648b9bf87563775d1172ba755df26c767568953b6d80f81f99959e1b05cd2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '1f4f100fae09a2800f6ae703ed1a22722480a0a93391fb0b3f355195d0453af9',
                'hash_atual' => '0bbe2d5854ef2f8bb1db77a42471385900b48413c3911565ea3c1bcf555398d3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '0f6f1a1b18f1327c01086d7c43fa65278eb3a473457a70b2d55419c417271077',
                'hash_atual' => '90d7f6682d4c0e84070ffd71d96b8eae12dd3801b87e4e801a3d57ef6544b8bf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'feaecf3fc6364f6ead1f9597e36dff0cd6de67778b862e5e3840d035e4b6bb33',
                'hash_atual' => '95f5224d8fcdf915b34bf985040b829969e153394365bd7c20f8ae48abd93527',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'e6f56935171dffd3105b8516ceed098ff228165fb951cac00e1b51b608aaca7c',
                'hash_atual' => 'f8037918965a0c8dab2d95f16097ca7220a19b88701b0805931313a4797db02a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '1d557f90a4f1b63095bbe94dba480e145c9dce4ff4ce836b9958fc75d886aa3c',
                'hash_atual' => '4b0537bae9736e11098e35c2389081a712984c7f65b8b368d96d7cb51d6fdf8b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '08cd474888ec65b67b809435720651cb89dfa3f517e11cf03cacc8ced621a9fa',
                'hash_atual' => 'cf41881273099be51b4688c66539dddaab6673d3a2d1938fce5c230c9743a64f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '10a2656e7cf1b927a4388aaa241eb390c7c79d4a00b7f9443aceebdca96d6ce5',
                'hash_atual' => 'd359c410b00f72e8c4d3ac47637c9d7f08685ee83aeb40420ad09f6d0610843f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '1652097b0ab3f0c08e4c5f946507fb3fef7d9780f61413b9c7aeec5dbebefa6a',
                'hash_atual' => 'db4b4c90b82ac361345cb0a85eda99548b05ec7fcc3eadbe7351232cea31720e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '1455e77d26b61263615a1079c2a17a1edcb7822a93289f6cf455e02b402a9d0e',
                'hash_atual' => 'fe341a1cef872dd394be5341af055bc181a89db9b0ed717f870515967e495867',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '879c72c54558df75a39a342fd56c494488a6a0a7f7f5e306b03b94e772a86ad7',
                'hash_atual' => '264436879d6779451646168eff29abd94d43b3be9e4c9fb2cd91841163f49c70',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '8b1ba095fff8719b8f54a1e13ea688d9d224b1119c622212a3c7b21237adbe88',
                'hash_atual' => '4d159afa5af21dac0ee16114b1971bb5bd8b2a4b6da6cfa0bb11fc7fb1a2ecf8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'e1483d2d2df425ff3f7df4beb870d8168ebad4792008f3c3ff8fb4ea93872097',
                'hash_atual' => '1cb4ec5e45530bb8c6aa5e103ecd557e500a186cf69f8d7bc564dd27f04c77ed',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '17956324b59ee0180b8fd09300c44fc1527639b91ab5bb743b1e029f9ec2c10f',
                'hash_atual' => '23cd3bd13140ba02f8d81c18737b0f2900a5d2e0dfeb1588fb77fbd96dad3b2b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '48b270af8c20dea96f65a17ad479497c5cd979f155af86f1ae587ebe72d4e363',
                'hash_atual' => 'b7b60111494df0d77f35009f091a0430c12501a2126ccc920252851812450999',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'bd03a4e468b2f12c22bca412a57592533919a54ab64b61d525504941f4141096',
                'hash_atual' => 'e9e771c073d0cfaedc65d3a977a5c0d62e8a8d00a9e85a27e565232df97b72a4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'e93192eb4c22e7ad491e0e34c4746cd7b7c6d6c2848e3625c27c4bc1b1712b8f',
                'hash_atual' => '3784564e5927cc85fc6b4c1ac570b29971ecd0e413130b513e39680dec0746f0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '81ac913a6056f246f70b88e0d4330ba2aa7bf6810b5eb267b367c3983b5873ba',
                'hash_atual' => '81120eb1790599032fde8d580543d20017618dd100102558bacff311a8afa77d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '5f3fe1f0ab84585e9cf946366668077be44a34108926f2b1a73cb61a215c5f30',
                'hash_atual' => '653474b17ef2614588ebef81f806ffa9c94c4845ad5c4edd3eaeed4546c44177',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'a7c10c41a2c9fd060354e040e08b4ea5412699ab150b15a4fd0e30380e664ae5',
                'hash_atual' => 'f23e14d247ab36a7d2d9f3cec4d27ca74bc23517c8ec9583e104002ea0b09c6f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'e8bad376fd6f25095ac0db08e1f43b9d427210c9736f230236b2fc792b7088bd',
                'hash_atual' => '3d63165ac916708fe335e14f9377bbb83e4bb231e40a5d21e8822be681fc7a1b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '303d2ba96ab5379972eb1e0309f9646a2e24cef4220ab3888c443ed973166ef5',
                'hash_atual' => '680db829b9bdeefaae087168feb56993c32ba4df2d816637cb621ec6a8d683d7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '51d6e45d250314c0f27ea29125bbe744100fd0280f656dfdb0f15b7cf23c7034',
                'hash_atual' => 'f72e2d6f64d813dbee3b6b88a970b8ad94d6f9c6e919e5b72b7b33f0b41f2b65',
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