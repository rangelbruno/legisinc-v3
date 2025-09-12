<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 02:40:43
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
                'hash_anterior' => 'd5ace8c79cf230bb546a23bd94bf36de8ab9dc3928cdb90ce0fcd55007f90906',
                'hash_atual' => '493cc98554d686fc0e28c5e04bc1281f6ae8e9d30776fc141e373fd56cd0ad12',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'f27d5462793b6fb08fa7150252f00eb547927c03655a85a7f630f7a40ab77ade',
                'hash_atual' => '4307a706206fe88f360955ff163824fbd5a03b81a224aad9931172b727e35aa5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '3a6b163e67445fdafbc7713f4054c2c526efcc8db6bd4f09f89f5148672706bd',
                'hash_atual' => '884611a92cdee8dabbb9a11ef24b2ba6b82c31574ace479c71da047a23ed5178',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '7b5fb2cbb89deff8c57229cb6a26e871cdaa0ce1d345ee021a78dc89790d54e6',
                'hash_atual' => '096c903abf97bb46d137de7c9c2ca23008dd273f8f5fd28e13a80b056dc0e8fd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'b314cc524bb26d439ef678f7bdbfe1cec6ccd3c6425dd19c809fab6140d00d73',
                'hash_atual' => 'c254c04ca296590b037cbf518ee8df69422e5dab12ffcb3d2a913a8bc30c1c22',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '77f52e982e41f0cf325fe7e9488afb18c7eec254a3afeb9ca1b1daef1e68130e',
                'hash_atual' => '47488e84cbcbbf9c3c9ebd48159c64053865b57085fb8aa833eceb25bf3e9c47',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '6a756a53d73ab69e7609aca469deb4cc597ae30f49f039ad3c771dde64065020',
                'hash_atual' => '71caacd8900c1db71835dd41400c86c5fecb3cdf93ea98d4fa201775e4e0ee89',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '7e6c66646ced41be46d720ba51f90ffa27df66bf13d8e6921d19a26640a25a89',
                'hash_atual' => 'e5a721f8d43a4e200d1e3aa3fec3c37058b4ca24df250ddd4c1c8c42773c9497',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'afa37c962caaa5955e8f8c033d26d1589fe10f6036393721c411fa9df747024f',
                'hash_atual' => 'c2a84faba83d1c2385beea64ee02c59a39f46c970e99f8eafe0d44e52e37ada2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'fa753eb061014d6941257e7186caa5268c1c12f635ab35ca55c02b3b348ab6b3',
                'hash_atual' => 'f4f31564eb3b279d3b12c13ddc3202a1683d293967a96cc5aaf78f7493749bb9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '0d8183fc74a7a0fa606df165eabc489de8ba5b1222bf34ee9c8a841e1bd2151e',
                'hash_atual' => '39bea745ad1e922c31d9991e5595c1098298d7180146a291a19030897a6f316a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '3ab9a4aa16d4df99dd137e140115debdc8f03c90207a6afbaa067cddce14c5ed',
                'hash_atual' => '41cd76590b96fe912bd3c9b81fd7b9fa87d9b2c621ee98ef592b22fa33004d28',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'bdd0b14ffcd50040ad7c7a98ded9e8d84cf772f26c8fb4ebb737f1e42fd1423f',
                'hash_atual' => '9708d988ba6e769fcf27a652a32e0965793dbccb38ff2ad0bbbe12c058ccb892',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '3bba02e5d5c838e076941326cf35c73f2ef672cbc72d914a5f6566ea7bb259ac',
                'hash_atual' => 'c222af91ae50178a611bcedd4acda8b9431cb2df1d92f1cb4ec6ee5eb90468d0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'd0772af7830c656c741201b242f3766072857fb7575a6f570b7f4085d657afde',
                'hash_atual' => '8f131a3916dbb481b9bc3f7ef025109e37bc24182c1354d55c2e73ae3f0aeb7d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '3633ddaf68dd722dbfd3d556c7543071b265e61224438c2a7e2362c265d084dd',
                'hash_atual' => '849fd079879e9df6bcd7c73eb23a37c63118ec7d80a2224b631b7c6c44c6d05b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '454da1c250cf1dce310a3e624c684a9feeb351bafb12fd8322f0bb6177e8576a',
                'hash_atual' => '14cc212341ee34f415ef94934fa7d37502f92555666e65a26c6b763d207c18b3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'e1758998f6b21caa41eb5601d5444b6518a879123033b9fa8dab815459d01a69',
                'hash_atual' => '7a03f92cb49fca924713fbe3a96289d0d24242af8981da82f83c616d3d2c9bfd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'e3365c2cdcdc0d5027c23c2e34aa0135ad6ff040b7a6d85fc5426ab0284b75d6',
                'hash_atual' => '782a7660f2305af92056c33f85eabdd8133d3cfcf0d9b46b12669fb442b57810',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '8dae48bce622333c99589d88f9e73c0f99ea4d13f321108e30e2af937da50eca',
                'hash_atual' => 'd9e1f587f4da280bbc19bf98f7495d8a1a71ef2ae60137f162cb287b5fd3c474',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'b7ee2258d15fc2a09a4474ed95d1a67271e805ad54b1ba56a9b3c195432b0c12',
                'hash_atual' => '7c2bc8497203b67cd4451ae7590768295aa4e3c4b51f5ecdcd06a26d0d6c0338',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '954a6ad038b7968c7922914240d6101afbabd8b246506f30051e37126e112c87',
                'hash_atual' => 'a215383d11a57b66d04aadc576bcc81b56e2320ee618dd3e4b4a68ee40c91e23',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'bce4ebb6a844f7ad602e30f299c49fad70a21d1fe4c4474ec6586fd6f18b8ca5',
                'hash_atual' => '25deb7cec49d7bd1aef2d14cba79e7d46ae8d0c68f40e2e96ba3f14305ce93d7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'f8361823d178feba3afc53fa9d7d875ae8e36ef879181f8d650d392bbb911c6d',
                'hash_atual' => '192109cb7dfd6b5e89d0d43bde9f714c2f4c5739dae169e0f52f9f2a8443f1fe',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'b32c09e53308e751bf66108a9243e96c4eec4fdcf9345f7bf1c5e41fb7e46553',
                'hash_atual' => '703e67d2b4b9fa8f29eeeb510070a31cdeeaa142b4443168971e1db87d3138cc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'a83d4fa319a397d5e0db11805076261a645723997027829029a87fe41dbeff64',
                'hash_atual' => '9075c3ea2265704bf0983476bcb47ab87648c13f38051fb0d988ff3d6961ab5d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '43e93f2b869b984042675b7bfd844c90fd2bde0851d8bbeab81fef98851565a3',
                'hash_atual' => '6a59ce7c1e0ac3366f99c07214b3604fa5f2e12157319ae5554d48686d0ebcf9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '2b0e7519ec0d8b274b7902b2a477e93243caa76e14b9c85d84be61244b27588f',
                'hash_atual' => '75652550e3584cad6eb61f028ebefd13247a6d837ca8adf47b562f082ac7cc47',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '712367624148f5401650cb2b4e94225e793ca20377771b315ca26901fabff7b3',
                'hash_atual' => '847d3c3915f44fad6db6c225c4ab07ba32419fa64d8d51a3b25ef8b57f6a13d3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '2f2aedf57869526ecffd6cd7b444687540826455e6cc7536ef613e331d3c460a',
                'hash_atual' => '84d41585cebb1e8a1e700485989f03190f34c6c7e7db2eb41d0fa50759fa25ea',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '12481ec360c539e01f1d04c80f4508adc65dfac162778f6e29d26601e1b86d5c',
                'hash_atual' => '2c40d0548c90eec76cf227281e2dd60480995331c2cdc85e8df9e5a48b13b892',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '1421d3087e1a440b14ffaa0909250327942301db5dbdccd1de4cd8b5da64592e',
                'hash_atual' => '82debc26b62b33ac64717b98e8bfc675b5e5fdab0b477ecbcea1624793551c16',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'd1d1159822a61fde10d16bb08c2a287d5a835d032e349abaa90f5889317ec97a',
                'hash_atual' => 'c93aca0dab27da28388fcce67be9f73e686191db312ffec0c95eb8a88c4d095b',
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