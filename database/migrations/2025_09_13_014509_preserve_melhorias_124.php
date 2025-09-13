<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-13 01:45:09
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
                'hash_anterior' => 'f76914c312b7df4fa5a17c2f23fac4560e9d7d9f6143c9f9f5cc70410ec6fc27',
                'hash_atual' => 'd3268f8eef9248627d58639b7579e5879303e4fd92b1db90bcb8987610626bce',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194593,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '73e299bc9438440f980b6917c446b846c73ab2a0fb07459cdbf1920b91a0f488',
                'hash_atual' => '91e19cac2d4d10f03670bf3a39cd590d3b60f4dac8eb3675bd44e1d05a613f36',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '02d0b774dab17252be9f383ae4889886bb2045b1c102d5648ade816006edd630',
                'hash_atual' => '43ebf4a47851a41f20cf60e5a7bcc5ca4912ed6f006c6951f4c09940cc722be8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '0ac3de925f3817272ba4136ae98a45d3d8d278c8c169d21c8dd1b9ec94195deb',
                'hash_atual' => '07bacc89688e2382d6eaaccec6689b94d37ca418d20d29e989e07470e4bbaca8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '904e530beb3575e7168a46b7c36f50368eea4e3f33ab6bb235e234aa2b6c3a00',
                'hash_atual' => '5951540ff6ea15e082148272cde5b687336ca33fef96b31efbf7fcfd134f9b86',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '3fcb30018fe6dcc7f38915765a362f058f59bcba25bb76f50ea6758d9c31ea9f',
                'hash_atual' => 'd6f035fb53593012e9d8773b53171b3e49a579b15d9e8836a3b7e028cc7cac84',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'cadff55fe47afead510e4e453c8e4f029e1bb2673bf216ed6c8ab133e569075a',
                'hash_atual' => 'c0f4326fb0d2afbdd3d6f97a3e3aa97d0ba03d7f42706851b90e7084aeb59bb6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '66cd075c90b11e1e2f6ae2842ad01f4a0316033ace25192b240c251dd35aab18',
                'hash_atual' => 'a342e4bc8cb4170fe1d97e6028a2aeb92c5ce456df73c586d4eac321743400a3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '22fd9832a651bc95a33e9bc4d58713aa50ba6251d47bb75e21e17bf6d020b372',
                'hash_atual' => 'b08fdaa3fc1d1456cede94aa902178cd89d2b0d37af684a8ce6b2f1e61284b73',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '62beaa924fb7d514f5f30e72e011df9fce0f339086b0d517825cd690850bf064',
                'hash_atual' => '0e7d29c5a5667a8d7b4d86d045fa3fcc027d8b544cd69c0e469799c7cd619f93',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '21a67b19dc624e9f3d15efb30df31acaed136fa8f4ae49f542169009577ad4f6',
                'hash_atual' => 'fde7613a71110cb3eb068f7a2daec2a2bac48d284bc45995345833e0275f8ad3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '3c15136b13392195e42cbaa4705d1e5ab108a6d83799f4e72a665d5b01e337c1',
                'hash_atual' => '182f1558625252dba6e77535a26d0afa13b4ec8fbb9437df4fdca96099b20cde',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'f4de3a92d646bb5b7eca359585831366e949b3a455a019e3bd3eaa08f980d9cf',
                'hash_atual' => 'b3191de2e183a7b72217aec46d6cae48c4317f0ba3406f5fc1428ff518e926c1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '2d9c1b93643a4a474369d7a44e41c04022962867eb8a29f3c0352e334b910fe1',
                'hash_atual' => '26560d9564f7ab0f9931fe439e723925f971cffbcfc6d2a1f05ac91ca4127158',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'c7fcc7122860cb6c2e2070da11bdc36b97256715f579ab3f14b86069d3740f96',
                'hash_atual' => '2e98095d7caf309751178b0576b883a703d36617f720a4a17cbcd85b5b6d0f36',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '5b2f0c681f04277a44c6ea1b7bd6dd51bc4b7fca7c6671bfefcac0e495b169b5',
                'hash_atual' => '8ecb7dd61b226dfdc79d749bf6f0f4eaa4277b90d14c1d6612987fea7a303faa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'bb9aa20b4a3ffdab8028127cd9e4160dd2d914726c84a8bc35a4e1b8a6f21d8a',
                'hash_atual' => '53497a17de584d7dcc613f8a79839be9458ab3c080cf48d9924db1ab2e81729d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '18bf73b1ca1f720ff0235ace36d03c7e2b1256d5ddccea5bc604ab92d1ba2543',
                'hash_atual' => 'f70d57f5502b4bc2d0edfc8fa19bc756441d5d52330a645acd505356733d0c54',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'd0a8c08d150442865af3d60ca99f9c8340287756a64b451b23629603a5efc3b1',
                'hash_atual' => 'e658de53e42a2af440fc82068c5634ed620768fed48b1ca52a610d77f74bba1f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '3b8d23624cca3ae07988bc3372b1e645ce947c6736034dca7e28035f3755ead8',
                'hash_atual' => '6b3b83aeb5be94af4980f79247098801384f1d78661fca52011fc9352c190037',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '1638e66abff41275fac9aa1872a7374a68a4edd6f1b56fe5c8d15591410558ab',
                'hash_atual' => 'a9e99c4a58b1196e3bbbdb6f37e7231b45778c22c415fd435a31f3bb4fb1dac5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '132e88cd2229c5a251c8aebd5981e68be62209a80bde7e00ca21775da5649434',
                'hash_atual' => 'da18c45fb5294d9fcb202b6cab4553593f7ef7ab47cd49f7ac514a656d2cec5d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'a09db8b5f79637e04c9592fffc333040cfab7e760d5698875aee7b4a8f98d45c',
                'hash_atual' => '3c79f5cd1ce663e4053facef569efece7d4a6f90b8f02a14a77b556af2f7d5b9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'f3f687b39e675cc196d681fce331fef83f64b18ee45203f914966f6c6bb125c3',
                'hash_atual' => 'a907120bc9f4a1eed23fc0f7073959c7204434dcc096c61f34fa9b0ceac0c1c3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '1979ac192b9d7ea43822f987a325fa5ba96dd5d5dca200c81df61dcd96192d05',
                'hash_atual' => '9b8e71660326f705e386476c3f75802c7cc7d149d4e2d74ff3ca62be78c98e86',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '2af8bad8d5987e39d3806876c32266af6e4b30365a3b2e175eb056e369f6625f',
                'hash_atual' => '19c1bfc8fc2b041e54729ab5a63e9aefee6ef262791ceee3d0a70cadec66b39e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '7e776dab4f9f9dfd1286536777225d0114d5f73bd56a58711cd3b004497c1afa',
                'hash_atual' => '2e8a30a24b2f8b7eee9b44a9eab52ecf0572a10bd8d66149a6ab678e410caf5c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'b57708ee9bf515251284cf6e1a8302383328963e01943e36045c858437eb93c0',
                'hash_atual' => '11ea716945213fb7c7b46a9f185c28530b34bd651c129f53a1af91ee7c555ae8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '47d00e1796e2fe66d4fe5f7e65fa773907b6a2e712065497fc91f455047935c8',
                'hash_atual' => '8813f52a748707a07aad79baa16c59677f87ef9e9505c876333e0b3f7bde1a04',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '99ef34bd5146164e2546ebb33ed9286dfdbda091ce44a6117e6fea4dda6b0482',
                'hash_atual' => '1b2486f8422975cfa3e2fe75dd5c87de3aa9b7e11fd53c7209893cc406f7531c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'd7a002f09f2706f8570865b6ff6674b53a7198e52f3e4d55e7b53bc2e856bd45',
                'hash_atual' => '3c4c650899be0aabd7e593f019294caf7b58aaf541eb6fe9e52123dcc3cdae63',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'd19e8b119cca2a303c5e310988552c7f1f89973c9de9d4aed67faa85ddee896b',
                'hash_atual' => '6c9bea47f1644a66efdf32ab5534720fbb4cb6ec3df790a81ae0d81c2914d180',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'e66e177f9e078fb8342ff0311065bc78f1b2effac6600ef784212f3d240b5abe',
                'hash_atual' => 'ee801843d5ce43502b2e3c53f49b3f363902e476eb70fc7a048bdc7b643b3297',
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