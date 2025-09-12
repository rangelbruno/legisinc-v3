<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 02:39:04
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
                'hash_anterior' => '387bc487bfffb144c6b1390d9fb700620d879fc9ff051e1d48ff7bf9a9fa6c19',
                'hash_atual' => 'd5ace8c79cf230bb546a23bd94bf36de8ab9dc3928cdb90ce0fcd55007f90906',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '59a0c4f8b74de5dbb926d7c42a695178655f4ab537ec99702368cefd664f3fd8',
                'hash_atual' => 'f27d5462793b6fb08fa7150252f00eb547927c03655a85a7f630f7a40ab77ade',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '7623dcc531dca3b695d109b283151813eb073abbffe5d8b71e079cf8c029e01d',
                'hash_atual' => '3a6b163e67445fdafbc7713f4054c2c526efcc8db6bd4f09f89f5148672706bd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'b270ac0b2115844da7d26f1e851e33ba387fc7cba594bff98479e1cd0d748109',
                'hash_atual' => '7b5fb2cbb89deff8c57229cb6a26e871cdaa0ce1d345ee021a78dc89790d54e6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '6f79c42416abe96b26cc9d9387019abe425038a2fc24b9e49171e5ab31e327b0',
                'hash_atual' => 'b314cc524bb26d439ef678f7bdbfe1cec6ccd3c6425dd19c809fab6140d00d73',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '6bf07a01b4cc85391eb99404af6a9b2d9ba82fcbbd16ca389e09157b13bf7e92',
                'hash_atual' => '77f52e982e41f0cf325fe7e9488afb18c7eec254a3afeb9ca1b1daef1e68130e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'ad344ee2e1cd351c2a345ff78592e682f3e952d877e8377fb6f88688bf57c3a5',
                'hash_atual' => '6a756a53d73ab69e7609aca469deb4cc597ae30f49f039ad3c771dde64065020',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'af9dbabaecd5a82d7a8c2b1e79f3fa5c7b3e3ac5db39c04eeddc70563ea2653d',
                'hash_atual' => '7e6c66646ced41be46d720ba51f90ffa27df66bf13d8e6921d19a26640a25a89',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'bfb366a92466516834d9a059f5f3637ac4966d7ec7a4f10498fa138b722a42d9',
                'hash_atual' => 'afa37c962caaa5955e8f8c033d26d1589fe10f6036393721c411fa9df747024f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '36d38541ebb9ec4a333bbe9ef577a80bc807ca17604d13a93d44d699b799ffe7',
                'hash_atual' => 'fa753eb061014d6941257e7186caa5268c1c12f635ab35ca55c02b3b348ab6b3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '9988f8636a1e9fd92fdca4afc4301590429503769bc989b5077a68611d0cf247',
                'hash_atual' => '0d8183fc74a7a0fa606df165eabc489de8ba5b1222bf34ee9c8a841e1bd2151e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '04db724f89b2ac64619a2a63c2891a4b76f81567ad35505ce3d66a5e1042865f',
                'hash_atual' => '3ab9a4aa16d4df99dd137e140115debdc8f03c90207a6afbaa067cddce14c5ed',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'db308f4345fa8b32a4a2adf766681ee5745fe3c6415b4c17714ca26407eae614',
                'hash_atual' => 'bdd0b14ffcd50040ad7c7a98ded9e8d84cf772f26c8fb4ebb737f1e42fd1423f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '3a805a9f0df0b82e6e3b9b07beb7dc4a44e6afcba665765e523d67df194843de',
                'hash_atual' => '3bba02e5d5c838e076941326cf35c73f2ef672cbc72d914a5f6566ea7bb259ac',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'a4ff023ba7d3eb0b95fe8c4a69ef274d4f21784fb17e8c8f628e86ef83aad424',
                'hash_atual' => 'd0772af7830c656c741201b242f3766072857fb7575a6f570b7f4085d657afde',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '73d383a5d328f35be04e50c77a58f055848a3b89e724c1c5a918b21a66a7f780',
                'hash_atual' => '3633ddaf68dd722dbfd3d556c7543071b265e61224438c2a7e2362c265d084dd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'a0a53e349365e251431b551396392d811d35619d788577827da52db18db6103f',
                'hash_atual' => '454da1c250cf1dce310a3e624c684a9feeb351bafb12fd8322f0bb6177e8576a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '0c55523a6612b72cf50da09a4759e8385800dfab875b1b8a53f3724746a38f84',
                'hash_atual' => 'e1758998f6b21caa41eb5601d5444b6518a879123033b9fa8dab815459d01a69',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '4a1a5bee462d290da36245c78b9e1cdce97494eaba1814082bc38dd730dadc37',
                'hash_atual' => 'e3365c2cdcdc0d5027c23c2e34aa0135ad6ff040b7a6d85fc5426ab0284b75d6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '1a0ee54a865692995f226f2cb494fee713b3589f9b2fa7266e510f685d6fd635',
                'hash_atual' => '8dae48bce622333c99589d88f9e73c0f99ea4d13f321108e30e2af937da50eca',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '02e375403eaa86c34c5bfb7690b8cfddcbe3cd8b9b9d8961cbf077fe7ca31795',
                'hash_atual' => 'b7ee2258d15fc2a09a4474ed95d1a67271e805ad54b1ba56a9b3c195432b0c12',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '791de1e56740c7af3a9d5298262c0c54db613f71a6b837fed5819bb23eb84a55',
                'hash_atual' => '954a6ad038b7968c7922914240d6101afbabd8b246506f30051e37126e112c87',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'a546d24bfcfde899ea5f51cb6147b1996f45248dc3fc9de2bd95f984ef6db4ad',
                'hash_atual' => 'bce4ebb6a844f7ad602e30f299c49fad70a21d1fe4c4474ec6586fd6f18b8ca5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'fc937c3ca10c0bea21f87b4e84544c67be21ed1420f6a245817bbc8fbf582910',
                'hash_atual' => 'f8361823d178feba3afc53fa9d7d875ae8e36ef879181f8d650d392bbb911c6d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '5eb655d8a049b659940624cd89fee99b51886cbbba8f05f1abc16c4ab384b2d4',
                'hash_atual' => 'b32c09e53308e751bf66108a9243e96c4eec4fdcf9345f7bf1c5e41fb7e46553',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'd965a33d1aa4c29244db4dc90924d1fac0c2caa141cad6708d03463161fbaa68',
                'hash_atual' => 'a83d4fa319a397d5e0db11805076261a645723997027829029a87fe41dbeff64',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '68358cf3630af0eba191b23247f899cf753702f3ed5a4c524e64354e6d37a6be',
                'hash_atual' => '43e93f2b869b984042675b7bfd844c90fd2bde0851d8bbeab81fef98851565a3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '6baf3aaab6b6381d52d42460da02c99bbfdd6ff3530236cdcec310ee1d1cab6c',
                'hash_atual' => '2b0e7519ec0d8b274b7902b2a477e93243caa76e14b9c85d84be61244b27588f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'fd727f0a0cf99f17e9dd0096958e18ee6e262b92a91fc1e87c7dd183ba63dede',
                'hash_atual' => '712367624148f5401650cb2b4e94225e793ca20377771b315ca26901fabff7b3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '71084ca7e2d0a23bf0c6830f1373c7537e5159abb9566a2bc6566deea08e11a3',
                'hash_atual' => '2f2aedf57869526ecffd6cd7b444687540826455e6cc7536ef613e331d3c460a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'b97a8b220795781773282300c34a2b587b743b6cf7d017fda834c410b1e738b2',
                'hash_atual' => '12481ec360c539e01f1d04c80f4508adc65dfac162778f6e29d26601e1b86d5c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'da401fa4f63769868eb0d0c78837e4d9a6b71e17d1d3f390b1aede5598fc462b',
                'hash_atual' => '1421d3087e1a440b14ffaa0909250327942301db5dbdccd1de4cd8b5da64592e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '01687d7eafc695a09a241dd1dd223679b9c3b3f6c402ddc67b946ad3a8d33d6e',
                'hash_atual' => 'd1d1159822a61fde10d16bb08c2a287d5a835d032e349abaa90f5889317ec97a',
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