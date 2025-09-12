<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 02:41:48
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
                'hash_anterior' => '493cc98554d686fc0e28c5e04bc1281f6ae8e9d30776fc141e373fd56cd0ad12',
                'hash_atual' => '875309c5cd4a2269f70c5b3913f4474c83972552a8782c2a3d7d6963e4c9a2b9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '4307a706206fe88f360955ff163824fbd5a03b81a224aad9931172b727e35aa5',
                'hash_atual' => 'c07e2f75a63a36a01418cfc2bb2bc79bed9837d1372bcfe9dc65d55b70ecdec5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '884611a92cdee8dabbb9a11ef24b2ba6b82c31574ace479c71da047a23ed5178',
                'hash_atual' => '5a95149b4f28fe21480b4e9bb9e4a71736cf2c12d40fbd9b64dd6e3b324e01c6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '096c903abf97bb46d137de7c9c2ca23008dd273f8f5fd28e13a80b056dc0e8fd',
                'hash_atual' => 'ecf4900f7790f8375b993cb389118fe760109aaaca0c84c89cd69d44574362eb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'c254c04ca296590b037cbf518ee8df69422e5dab12ffcb3d2a913a8bc30c1c22',
                'hash_atual' => 'faabca90acaf788b6e8a2468fbb6fd97ce53ec5566a30e999599f74e13d9ca6f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '47488e84cbcbbf9c3c9ebd48159c64053865b57085fb8aa833eceb25bf3e9c47',
                'hash_atual' => '08bf43cf722200c3d211a888d7938d7d70a9d5ec504ea88a203155da5402765d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '71caacd8900c1db71835dd41400c86c5fecb3cdf93ea98d4fa201775e4e0ee89',
                'hash_atual' => '03a9d256d741462a640600fc35d2703c5d9fb12ec7a7c7d1ef37377e7418fc5c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'e5a721f8d43a4e200d1e3aa3fec3c37058b4ca24df250ddd4c1c8c42773c9497',
                'hash_atual' => 'b6540d6ea9f05b4f0524813810b34b5046f3f4f0bad9cb5e71981a854f8517cc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'c2a84faba83d1c2385beea64ee02c59a39f46c970e99f8eafe0d44e52e37ada2',
                'hash_atual' => '696897e7807ee2ed4b41cfd281665947414202b26743646f737c6173728b1710',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'f4f31564eb3b279d3b12c13ddc3202a1683d293967a96cc5aaf78f7493749bb9',
                'hash_atual' => 'cc2e68146a2d03b8dc6eddbb3cf575c025a1554c471ad739e09b23ca395c3cd9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '39bea745ad1e922c31d9991e5595c1098298d7180146a291a19030897a6f316a',
                'hash_atual' => '2d07e37657463ec1766b8694298869d449f7292ccc3ec9ae603a033db2ebcb24',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '41cd76590b96fe912bd3c9b81fd7b9fa87d9b2c621ee98ef592b22fa33004d28',
                'hash_atual' => '52a6a44b460f66233247daec712c221964a02e318a05665ce7cac82db2cac5b4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '9708d988ba6e769fcf27a652a32e0965793dbccb38ff2ad0bbbe12c058ccb892',
                'hash_atual' => '16ec8d5916f284e653a6dc8ae8aa7af430d5225ef46a41a25b00e650b28ee071',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'c222af91ae50178a611bcedd4acda8b9431cb2df1d92f1cb4ec6ee5eb90468d0',
                'hash_atual' => '5ff7c568ae486aecc0e5cee4adce671f87ea5cebded30b92965fa58d7542a90e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '8f131a3916dbb481b9bc3f7ef025109e37bc24182c1354d55c2e73ae3f0aeb7d',
                'hash_atual' => '8b8fb66ba8e1cc628bf10f6215e797070bfa654708aa7e3751eac4806c601413',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '849fd079879e9df6bcd7c73eb23a37c63118ec7d80a2224b631b7c6c44c6d05b',
                'hash_atual' => '71eb9eefed632c962bd94ace9c4009304ea7c659cb4b3371dbde13c9dad24dd3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '14cc212341ee34f415ef94934fa7d37502f92555666e65a26c6b763d207c18b3',
                'hash_atual' => '7d2ce56b1fef9d3be42e3a18d93641c9089bcf701a35e2429f306449aa4a45ff',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '7a03f92cb49fca924713fbe3a96289d0d24242af8981da82f83c616d3d2c9bfd',
                'hash_atual' => 'b1d645d5bce9407be315922d58a8800154aa1191cdb84d62b5715ab3bebfd473',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '782a7660f2305af92056c33f85eabdd8133d3cfcf0d9b46b12669fb442b57810',
                'hash_atual' => '34ec10f0a7bf8e1bf23841b48184a96771b9dbddbb002227e8337134e03586ef',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'd9e1f587f4da280bbc19bf98f7495d8a1a71ef2ae60137f162cb287b5fd3c474',
                'hash_atual' => '99808bba2be7720257194bc7f5750d2c5d1e24fa87a71b7ca2c733520a491065',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '7c2bc8497203b67cd4451ae7590768295aa4e3c4b51f5ecdcd06a26d0d6c0338',
                'hash_atual' => '588a99515b4cee74fdd409b1bfc9333f4274050e4898cb705d6c58b90ca9dd96',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'a215383d11a57b66d04aadc576bcc81b56e2320ee618dd3e4b4a68ee40c91e23',
                'hash_atual' => '5a02f39101a0797bccb7092f0371958971a12b1e2de19d071383d715eaabca32',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '25deb7cec49d7bd1aef2d14cba79e7d46ae8d0c68f40e2e96ba3f14305ce93d7',
                'hash_atual' => '0cc6212f5ab2bc797e894ffcad1e476c3173f653337235497d7ca6a34c865406',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '192109cb7dfd6b5e89d0d43bde9f714c2f4c5739dae169e0f52f9f2a8443f1fe',
                'hash_atual' => 'a12b977cbb446397146ee27c958b4f3490de5b79565bba7076793f6b78ecf81c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '703e67d2b4b9fa8f29eeeb510070a31cdeeaa142b4443168971e1db87d3138cc',
                'hash_atual' => '641e051cfc14e78db63d795351fbcaf12918e5900dde0e5ffcb3a2a5452c3267',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '9075c3ea2265704bf0983476bcb47ab87648c13f38051fb0d988ff3d6961ab5d',
                'hash_atual' => '9505182c91fb47c76b211525395a3d4ab53ed19a09991b73ebd1aee36b59e875',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '6a59ce7c1e0ac3366f99c07214b3604fa5f2e12157319ae5554d48686d0ebcf9',
                'hash_atual' => '76c260adf6222090335ed549a4d9fe19233b0751b69a78d401d5e3b69b9e4a3f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '75652550e3584cad6eb61f028ebefd13247a6d837ca8adf47b562f082ac7cc47',
                'hash_atual' => '4b5e6ae442a914f29b4460bea8b9e85f68dec350d9de45ccfcd380414ae67d08',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '847d3c3915f44fad6db6c225c4ab07ba32419fa64d8d51a3b25ef8b57f6a13d3',
                'hash_atual' => '6c629aed9b98d2dd0a6dc6483710861c292c1c3a5d7cb8716dc6487edd97e7eb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '84d41585cebb1e8a1e700485989f03190f34c6c7e7db2eb41d0fa50759fa25ea',
                'hash_atual' => '02b1b0d53e1694339ed7ada611165b2e14853da8d15b4ccc6fd5af3ab9da891a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '2c40d0548c90eec76cf227281e2dd60480995331c2cdc85e8df9e5a48b13b892',
                'hash_atual' => '38043e3f06c83a5c39effe8f90583f9d907f13a8578aa2d70224aaed4958de88',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '82debc26b62b33ac64717b98e8bfc675b5e5fdab0b477ecbcea1624793551c16',
                'hash_atual' => '42d671ed451418e3909ff3e62a7a3ba2ea272313ec38d27f575930cbb4cd7062',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'c93aca0dab27da28388fcce67be9f73e686191db312ffec0c95eb8a88c4d095b',
                'hash_atual' => '6c5806e7cf7e71364b536a3d3739c35d7aa521086b9d364e3898e0ab63831144',
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