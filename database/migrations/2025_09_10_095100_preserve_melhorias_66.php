<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-10 09:51:00
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
                'hash_anterior' => 'de2a463fa1a9f9a277d3b4006538cddacb6fa47224b4f53b09d0d607f9787159',
                'hash_atual' => 'bb46fc9237c502b6bee401845f41cf352198a2f16d006c1aa4a0b083cc514e32',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '46b990deac66437de1a1daf84525aa0f59b927e979b81ddab74aa1f389add051',
                'hash_atual' => 'd67ece8a6ec2a09f909b939c2c5a3a2bfac6aaa54f1f239ba26e77b700e7b679',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33929,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '6d72d39e13000c1a1f97de009cb3fade625f86eee71a6cd8876ebae0cb4c20db',
                'hash_atual' => 'c2fbcf8b9e31e7353e8e87c03911b09218aa7eae4189794ae3d16b88d046163a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '742796e864ea27a2b5ef43408fcb2f9a378be2db29cda4b44bf28aef6ebcae94',
                'hash_atual' => '308e5dcd5219f991f0c464c48ccb7de4a9e1cc417da677b52a1fece9f47a523f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'cfaef99d4f17c0889ab34e8f4dc659032347b37c004fa9a3221135a79b9c9e34',
                'hash_atual' => '1795f0969424c57bd3c6da57ae7bebf8fa5542628bb3474cbd94cc5b72bdecaa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '90a7c09e18e729e05d4ebafac3aa4c1ef4c2c9917737d19316615b328e30174c',
                'hash_atual' => 'e1af6e18d60fefbd9041e8e3148b7366dc49a0f197a05b4bf1220c6fa98936eb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '7e9f732cfa6426dd1bb5e2db59094c6d7adf6795de7eb9938ad5310a82bcedfa',
                'hash_atual' => 'c267121cc60791ddfb77622c6db12ae0878dabcf772709bfea02bedee99b11e3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '162e112caa8ca500eeb723028bf26e2ca5647933b39ca7ff9b5651be6ec3a121',
                'hash_atual' => 'd6029c9755bc37fc32ef7e50db94d3abcf71259b16e469169168cc093630fbfa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '78fc1f7f5653d0a95d27368056b64a93f9648ab58e68f8b0411ede017d77a3ca',
                'hash_atual' => '19a867ce95dc15293b8a0fe2ed75fedd3be7d5a4101630747c4359532063812a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '2713894ff0fbf17da68b8cd6ffde9d3a179cffeee3c78dbeb58f14b143430361',
                'hash_atual' => '0eedd5f88b36d7adca40a5a7b10375c8193f4156785b0bbd676f53ad74db0ff4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '8f6fa807f72e7a8c7eb982d873af24a93a4fb2c28b5fb700a7ef95e6b3ba48d3',
                'hash_atual' => '6b385ac66d37f7a311385532c2334bae96fa5a93017947af5d8e172645d93aa1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '1fd6d94642cdf018cc0531438623154eddda97bbea5d29327cf5239891e62dfb',
                'hash_atual' => 'ce4a7ea287100419d5d72ab0b52617f9152798583720ac2dcd6d207dd1b5c7c4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'acc1c6f9f81e1282754945affc6a5fe1001216e4797ccefdb60c0406ea49f267',
                'hash_atual' => '080e32f39fd0ecd4da9adccdfdaa3153ddf2a2a181942141657766e506079088',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '60c3d273f1416aee978a5c7aa12c24b9f48a88d3196f51494c20b0a7b5809d83',
                'hash_atual' => 'e4433cee16453064d13718050aa975cfbbd23f0e2888a6f086e24f17dfdf3d62',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '69eaf39814c12492570d60442146b3a0e5863aea510919c53bb4d94f7641e95f',
                'hash_atual' => 'aed9e96582da60f8b1aecebe4eaa34b5be2eb1fa94d5cd7fd487fee31b99938c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '79ec74c8db1bd311a6bab4f1cdc4609817da7eb70a09df2cf92bb29ca024fc8d',
                'hash_atual' => '813f19f909c05330f111d13e705c1aae146cd8b21062fe27d3c803287b7cc854',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'eaee2ded7a28051441e95319b54340c70b16e2a3cefb35572e4f694928e67c82',
                'hash_atual' => '3dce4eeebe0309d860921f04e4c3f5812c15d37fa6e00b41f4475c4d3601e649',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '6f169f103e0a9c80cbb9e46ce7614394e8cdefadb8a2e11b0c9a2ebf049c59c4',
                'hash_atual' => '85999606569995f1aa2b8949a489ae58bac9eba0f3b5a145f43f1ad8b6918bee',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '40c9cf7a4808679a3c7ec38dd79ef000db87af4f541d7bb88d75b477a362f3cd',
                'hash_atual' => '6c417e78b7425772ddc15945a951a7b4d434e16b145791a7d24f95342c08fdb2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'a8b0b32949f86c22c8f2290b292208c64f6117b464f3ed7214c96afd9ce745e3',
                'hash_atual' => '5dcea9db612e814327491e601bf02bfe69adb6b449fe6ac47429a2fe5ff5d2ac',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'a5c13348d777fe038ed804a2f4eb2c24cf194237179f63181e1c628e1c404857',
                'hash_atual' => '8e61e144237a3fe330d4701bb75a07a89a615d20a4d9dc002ef2b5c1d28273b1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'b9da76b41cf8c7cf853239c21c13a5b4a0e376275c37ca4a53e5455ca52dd04a',
                'hash_atual' => '443234d6c7eed3f790e6aaf319b4bc5da98ea191b9a1c87a7e6ca3e64cd31f0e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'c49c73d874feeefce07e74803b5427b0ffb1ced2f66d13a62d970e151335f8d1',
                'hash_atual' => '8edf21088c1f3be307e2a862a81f5c4052bb48d6fc8a4f6b18fae24d2b7d82c6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'd616783e87238c8867ca293ca0f4c7ebbda886c1c61c76fb4b9081bec2a63d3e',
                'hash_atual' => '8a4cb1351110a6ed2098256779d1bfec87fe5a2b0d05a7ab6e00bf66c189d04b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '370c20f2e4716e58b6468f8c66080db4b6e9fc617cf9d92e36c7062b53bc8418',
                'hash_atual' => '7e29daa590f3f96f3421919e88aeba10cbbe0889327ef3d831d31b4ce6637bfc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '00ad8a8ebd811872af8b93e43432edc628d924555b4500999a18cfd56f2ffc76',
                'hash_atual' => 'eb7028f688db5b84cc87f8e927a073e5be1f6202a7c1b1958602b8ba6347d750',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '20d483346410b4c005f770ede9f56daab475706cb7d9d81c02bf165b5b703b36',
                'hash_atual' => '196b3abe39a89f439f0d8dff97f20d60ed4d15b1b5e915b495c87febcce7f6c2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '858b429cfbca537c57e1ab7dc18e6e473a3ef266a191743581613c78eec46ac8',
                'hash_atual' => '9ec508427314f4164ba913b50beb8b05f436eb728ebe5d9f4a80cd4988ee5d6d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'eb81186fbf895f6c53790a01879e02feef583bff0bef4e09940d5f715a6cfd73',
                'hash_atual' => '9933d0eb80706962daa4a1e58fac7b893a69fb143db97a8b380b704dabde50cb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '4d4246f0d253b835ff8302350c4a73d5c30bfd0ee6299ca1141c06b4ec650adb',
                'hash_atual' => '1335b59e3516e41fc00a044255dc8064d635782d0f5d619cfb876b6d9146e69b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '89cf153f3e470ade8f24ca545f09132fb415970a2027aca7fe1824e35b0cbca6',
                'hash_atual' => '97003ec0a1335e43a40cb1bb674790cf7a578202abac139a32eba1203889ed22',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '7d7ddb2c89296373f45bb5e460335d201cebb20d51e20683c42595b4ca280194',
                'hash_atual' => '6da4381d8a4e986e625480827ec4e2b28dd288408eb18935c690da491d50881c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '314b38a75eae2c4b4520b33ad4f0b7e8fdf467b0bf8855dc71fe5b2792ce9b75',
                'hash_atual' => '904801e3feee1e3f98ee026b12157486b64129296d37da1c450565f50261661f',
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