<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-10 15:26:43
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
                'hash_anterior' => 'bb46fc9237c502b6bee401845f41cf352198a2f16d006c1aa4a0b083cc514e32',
                'hash_atual' => 'fa15e95bbb6b4d2799cce7ddabf8b257e633e6c5804eb052befa3d659f81fdcf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'd67ece8a6ec2a09f909b939c2c5a3a2bfac6aaa54f1f239ba26e77b700e7b679',
                'hash_atual' => 'a492ef46fd116e8a6f97fa0b3063d8cf7b3c2698fa6e4ee7e9467082a3991d69',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33929,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'c2fbcf8b9e31e7353e8e87c03911b09218aa7eae4189794ae3d16b88d046163a',
                'hash_atual' => 'e1f90d1911f73a91c5f0079460e10d9f877ff5d1c12e756288e1f5dd0cc10e4d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '308e5dcd5219f991f0c464c48ccb7de4a9e1cc417da677b52a1fece9f47a523f',
                'hash_atual' => '1ef117f4d8bc3ca472afda1f4a3029112ba5d35c51f4fd2f166155a5656b7468',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '1795f0969424c57bd3c6da57ae7bebf8fa5542628bb3474cbd94cc5b72bdecaa',
                'hash_atual' => '39d9d5c94752ed79a759f4e8719ef0a5b2ff67de13ec67e9e8620a2ab11301bb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'e1af6e18d60fefbd9041e8e3148b7366dc49a0f197a05b4bf1220c6fa98936eb',
                'hash_atual' => 'e38e1df0c0af99cb673b79efaad7564cc8b1513fcb255438f53012b384bd9014',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'c267121cc60791ddfb77622c6db12ae0878dabcf772709bfea02bedee99b11e3',
                'hash_atual' => 'a642f3f8998f721983e1e866e2ac60d09e961b3c57f0114fa940f34fd3172556',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'd6029c9755bc37fc32ef7e50db94d3abcf71259b16e469169168cc093630fbfa',
                'hash_atual' => '13d436776548d9d21993e0f64061936c605737b192c0f2f934a2a36c1e052554',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '19a867ce95dc15293b8a0fe2ed75fedd3be7d5a4101630747c4359532063812a',
                'hash_atual' => '53703915f0d40a797a746bb413ca73c6c05e9ff979a4405de668ecb5e31f96b7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '0eedd5f88b36d7adca40a5a7b10375c8193f4156785b0bbd676f53ad74db0ff4',
                'hash_atual' => '381687fba964a20c0a384993345ef47a6cd1f440d41b44b07f58bffabdf1b844',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '6b385ac66d37f7a311385532c2334bae96fa5a93017947af5d8e172645d93aa1',
                'hash_atual' => '7424386d3aca400413ca15e23cf0eda4c47cb504aff8d5267134ae91c76e3b42',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'ce4a7ea287100419d5d72ab0b52617f9152798583720ac2dcd6d207dd1b5c7c4',
                'hash_atual' => '83a0cd230851f83cc57fd5d9b3363e449f049f0c15a2da8c86f3c77986b43ae0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '080e32f39fd0ecd4da9adccdfdaa3153ddf2a2a181942141657766e506079088',
                'hash_atual' => 'd3ed8dc7399c8630fb223169269327c2f23412cca465d7e4ae866fddeeea8471',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'e4433cee16453064d13718050aa975cfbbd23f0e2888a6f086e24f17dfdf3d62',
                'hash_atual' => 'fd2e166f7740602c983f1df831aeda246152803091ea13de53df8e72ae06bd86',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'aed9e96582da60f8b1aecebe4eaa34b5be2eb1fa94d5cd7fd487fee31b99938c',
                'hash_atual' => '06e70119064d935e2728acfb0449a1f6cbc3f327019b7036cee77dbd01969e68',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '813f19f909c05330f111d13e705c1aae146cd8b21062fe27d3c803287b7cc854',
                'hash_atual' => '070dd44c9eae6192022724c0d78bb7a759f89d40b4ab2f566d0853de14f125ab',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '3dce4eeebe0309d860921f04e4c3f5812c15d37fa6e00b41f4475c4d3601e649',
                'hash_atual' => '19b8c90d702c3f3df736938ecef014220888a4e370f4bbc9f83b99cd9e474b09',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '85999606569995f1aa2b8949a489ae58bac9eba0f3b5a145f43f1ad8b6918bee',
                'hash_atual' => '38f07d92461382e77f7f7872c793a6c572458f4e45456604c9a4fc23f5ed6090',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '6c417e78b7425772ddc15945a951a7b4d434e16b145791a7d24f95342c08fdb2',
                'hash_atual' => '03a07db38356a364c6f8e83f40b3779a87a2e75cf497c8e2b69f3b49d0803994',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '5dcea9db612e814327491e601bf02bfe69adb6b449fe6ac47429a2fe5ff5d2ac',
                'hash_atual' => '4cb888f4f3cd5da45eb7ae9240ae78f8e8d709fde44d3a31973018562c7aa5e8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '8e61e144237a3fe330d4701bb75a07a89a615d20a4d9dc002ef2b5c1d28273b1',
                'hash_atual' => '4178a71fc4e20086f6d4ec7b8a2d466ee27534702a050343a9b02d573534e85d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '443234d6c7eed3f790e6aaf319b4bc5da98ea191b9a1c87a7e6ca3e64cd31f0e',
                'hash_atual' => '7a7f9aaf47ebd86754560d673055f9e9338bdb1712c7e8be0da7a282c51a44f3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '8edf21088c1f3be307e2a862a81f5c4052bb48d6fc8a4f6b18fae24d2b7d82c6',
                'hash_atual' => '5ed1c4567e6c3b5f224ddb99e5660e9aee624e97234517d89208f08016bc7048',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '8a4cb1351110a6ed2098256779d1bfec87fe5a2b0d05a7ab6e00bf66c189d04b',
                'hash_atual' => '7d0aac7ca4c1548944475d0a417c064e561ff43b875e7b62a3813873d5775b10',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '7e29daa590f3f96f3421919e88aeba10cbbe0889327ef3d831d31b4ce6637bfc',
                'hash_atual' => 'ab7babcfdcd4438ea2ed16696f125694b6b7eedd68ff91e23e04bd80f061c28e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'eb7028f688db5b84cc87f8e927a073e5be1f6202a7c1b1958602b8ba6347d750',
                'hash_atual' => '6eb134dec3917e206e215ef8146b2124ad91c31d7e2710dabd42c5265aa0ff67',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '196b3abe39a89f439f0d8dff97f20d60ed4d15b1b5e915b495c87febcce7f6c2',
                'hash_atual' => '783ba63f28fe72a8a8a64c005b5344af794b3b637c19b0001f788cffca60937e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '9ec508427314f4164ba913b50beb8b05f436eb728ebe5d9f4a80cd4988ee5d6d',
                'hash_atual' => '7650d0a3d58b3df5bec40f12c0830372586485fd1dea7e29213064418e962547',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '9933d0eb80706962daa4a1e58fac7b893a69fb143db97a8b380b704dabde50cb',
                'hash_atual' => 'a28790e2a50c8869ed01f9dce0848b5ebb261cb0eacbfd382d030be00f562f16',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '1335b59e3516e41fc00a044255dc8064d635782d0f5d619cfb876b6d9146e69b',
                'hash_atual' => 'adc60afbcc7d27ad47fa5004937b60371c7daffef16276f20e66dcda8cff44e3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '97003ec0a1335e43a40cb1bb674790cf7a578202abac139a32eba1203889ed22',
                'hash_atual' => 'cb86d2082049a19f97c5c6cfa798fdd4bfe9291a8721826449477dfa3360b3a6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '6da4381d8a4e986e625480827ec4e2b28dd288408eb18935c690da491d50881c',
                'hash_atual' => 'c5fc59891f1105c4505ca273ebb2da27f6be4cc9f2f8b5c4f30da4fea12683ea',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '904801e3feee1e3f98ee026b12157486b64129296d37da1c450565f50261661f',
                'hash_atual' => '3aa2b205d62619fdfe50f8938831bd31479e76d1fe6c168923b80f28aadd4ef2',
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