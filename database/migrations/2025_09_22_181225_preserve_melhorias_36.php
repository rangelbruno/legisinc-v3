<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-22 18:12:25
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
                'hash_anterior' => 'd535642332ae2407ce8be22e68264bf794f6a008b1cef4326fb0bc20d7e98da7',
                'hash_atual' => '64a9daf487b5d0439077d21d17539406800ad548d96b82667b878bfd0c070b3b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 199451,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '9feb34a81e5b1e4c2b0d5a47799542e3e5a3994f93e45fb2e7d0d730915984f5',
                'hash_atual' => 'f2b1a83d11201e62c6d86a35df7381e53c30351efbb2c6d054c5b6a05ee67ede',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '14571e3791993c531ef0ceefda94fc1d48e89f335390118a87700b42a9720451',
                'hash_atual' => '8dbd5f4b71e4f930fcc7b640c9e7efc776da410459f52371b562a3299e748fee',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '9902d7d13de14321fe99266b3cfaf7d9becfb622ff4b3e687f70ec7cee784163',
                'hash_atual' => '0b4447f62fa48af9e024d5aa71bbc7a248b0992a7a2c9fce104c4b71de6c3280',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '9a7eed032b066b4804f0deaad4862fded4789331154fe22aadb8808ccc1ec0e3',
                'hash_atual' => '17978a599fa464ac8afd2d3702804b671c55bf07b257f2c8e65a9de08132fd7d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'b2055b757fd5d12e65e1adecb470db91d13b9ddf382659a415637700ebcb9769',
                'hash_atual' => 'ee8ddc6f0ff44ed7abb820e1e4662cf07c58e0ec44b545bf66ae12bf77ed27bb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19682,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '964495238a4acb3c3e76746c99a5a6c32596369801157aae013ace670eee1ffd',
                'hash_atual' => '9552166fd786503c86673487defe946ee53d13505c2914d3519a728b56ce6c47',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11654,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'f089178016f28979dec262f7aceab637c74ac6721ca748fb4210eebb739eaeeb',
                'hash_atual' => '815fbbb4c207569ea73ec052bd8f1ec483650e18ee78b720ca9895eb3b144c9a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'ad73dd0a82a356d91748a83342753c63953c7254397d2707f198c17e3d65466e',
                'hash_atual' => '8dcbf44ebdf0fc2b675a4d19c2701a848e8281f754226798287078d8420095dd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'ef3cd979cc36da9571eea8d6668492dfdeca29861b51a93ee9e069d460bc2a97',
                'hash_atual' => 'e7674e5cfd19880e3100c6c710711e60ebeb6384b39a3ccc1f5c627729fecb78',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '23faffa25d7face175659eedd604e364b5c0b04fe7b93a7e025c5e02dbc6985c',
                'hash_atual' => '1f36fb66cdf0849a37d838c97ca1401889680cbb361514deca9c3da86670d7a2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '6af19c05bf6232136c748724fd3ca1718bb3fd5dbb7403edb4f016c88ada5c78',
                'hash_atual' => 'c849086b7dda2d48cce5062adc818b61330913eb81a81f3aace74a317588f6b4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '09b2d3f1f22e53af83c19303ebaa4f0bfeba89d9a239d5715edae4d75ab15360',
                'hash_atual' => 'e6cd7d2230d761e932942e6a5cda800815a1c38590ee2ffbbd7982b671d52146',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '79966e8372d3545ea54a7251da20df82c0a81c91b67639a719fa6673151a3f0a',
                'hash_atual' => 'd2f01b040258e589e8f4aa481749c9fee2d05d8d449062b824dd41b8f2b8bb01',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'ee3bcd223c7303de34fda0603410c6288f891ece7f1aa6c1e1a05c6d6136e996',
                'hash_atual' => '5b20c80f82714acb51b02aae9af51635eacf50bae1a0984b35d277f4104f9010',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '2e1e72af1bdd958f566b92717f1a3399a968ec54f79fc8854c44f50addb20ea2',
                'hash_atual' => 'e5d1f40543d66065de8beda5fb56ce9c29c52dd948b8dabc059e54e27f7b7ca5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'e5b205a29075d04bbd9d4caedc86e6c463966f1858aa4755ed4398beb7c884b8',
                'hash_atual' => '04478cb464fd7daf5ce9a9ba6371c7d5a55780c76e1baefc6bb913939023d61d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'f9578e338296e88244766301ba32738098b82c248c18dafa6f4b2d2ab1a3825d',
                'hash_atual' => '89653b5d3662ba8702a09a3b62f38547e22399d4c96171005424e3c6fa63a3f2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '86f4631a9a7c6a70073213149951e89b8be40dd416e639ff986f71c83f90d0c3',
                'hash_atual' => '59750b6be903fcaee6a41c78ceab0e1f18eb193bb11403b51267fb284919deae',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '3e422bb3830ea527f275b3447672c615ccc83f31bae33810940c6f89269ba924',
                'hash_atual' => '1b85579524372705bfce21c6600d67dd40ef51bb7def52feacd58e93ff68f989',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'c38ad83fd267030ffa3713a96087c6c629ee1646c105dd310802c24785d4f6bb',
                'hash_atual' => '931dba60992842eb3693ab2d8bcc52625b481771fd2824b0b6c4fd976f256463',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'c94e5297d811bf335008b5a29778b8d3bf3748504ce95ec2cb21dd64998c2f3a',
                'hash_atual' => 'd9dcf681e0d99370f00b3dd6116850a08e345e925869f4c09d54d1f6304da919',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'c7ccd792d34a2d5285567dac67d0dc8653aa6dc4bb59276b101188c442fb9e30',
                'hash_atual' => '277a51d6c0588e97df3f9f5d7e53f28de8b7fc03c92145950763086ce17f186b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '237bcb6d7da08d4cbce48d81acdf6c5ce07e4d9ff4a482dd4ba0b57e6a914333',
                'hash_atual' => '96e519a9d2d318bae6e6d0ad167181142b2ec71591010709c192b0d0d7b6f2a7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'e0fb621e69b1f677948b4aee4badbcd139cb5618cc321df894dc5b043037dc37',
                'hash_atual' => '19f432d8a0c488f6c7455d388c2c45033a99843a6434d63d18245bb8e6517ce7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '77199d49c88c47bdd1bf54c97cdbc15a55321909e3c2d33325751e990b0b7aa9',
                'hash_atual' => '942c4a440aee620b9f7d79603d150c3462ce2953bccbd190ca119c3b84e23fcb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'c49502c8ddc9381c29b4632aacc4fdfd95d9ff1fbd6c0eb36045d2376397e8c3',
                'hash_atual' => 'e5dc4db19eca540a0e20029b610116f57c7c87d83eb71de87f657107711367ad',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '02101435d83dfb3152f5b1ecda9f83ebe47d674323a99fde087f8fcc481b0d3e',
                'hash_atual' => '0fbf915ab25cf677219acef0eb6f18a632966f8719c8c31859aad084b174dc90',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'f18569fd4732f6c773898249f740073c9be32541be8134945a99f27a06592463',
                'hash_atual' => '4362b81eac86d61edd81dc827bb2cb4c84b632faccbcdbac5fdd5234753cc32a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '61f436208c8ec746e8c14a30553b04dd16533be682eb3692f3e157855e2a54a1',
                'hash_atual' => 'bcff218b5bbf8348d4cf5816aa2f7528731fd0c06564d4e66bba0134b9d7efaa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '553afb78d6a441ccff4de549ad45bcbfbf02161895b65fd0f889438e41d16777',
                'hash_atual' => '1847a50388beb1c890cbec92d84348da8ed3e403cbeea6250a1ec8145c289ff7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'c2373defbb2d81f12c5419cde792f80d4a43b3022276c921bac9f21e31c58105',
                'hash_atual' => 'c073239cb99b9d9fb4c366ae4aa23a12e40ac9b9e73e42e72476daf42c345fc3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '904d2cec6e5f5f8ce7e15db9076c5d3363378cda61bc7f3b2a9c2f3d7195e4b9',
                'hash_atual' => 'dad8ad22e587199ccada4d1d5cf152994ae6ee798adaa6a9e97fa3714f47ac4e',
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