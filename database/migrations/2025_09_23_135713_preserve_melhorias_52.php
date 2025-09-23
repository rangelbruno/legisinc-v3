<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-23 13:57:13
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
                'hash_anterior' => '6157d540544968acd7c055cf0874034d361919662bd75681a646f4b77c3a3c22',
                'hash_atual' => 'ba272f8a92a8b053ed837eb889ef2725c1962d517a151aa00f5bd9940c084eb9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 199451,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '73632e2970793f506b12f2b8aa84a7bbad30769da93a06ee042d1b7f881baadd',
                'hash_atual' => '0fbd70c4d17a9ce0d480d7b1f45ff3b0c346329122a9f0cd26435cc16985a363',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '333dbf2b3e456e93de7b66617605e2d65eeece0b24be0b7183159f9f4d591e53',
                'hash_atual' => '5daa6d0706ef08db1ab8cee0a35b889e435c4c347a5cf70b3bf52568f13b4a7b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'aecd71d164501331ce21c8e8ce221694f258ebb8e3d1046f3294287a726b52da',
                'hash_atual' => '9d8cbc3d8830a5fe2198c7885a5d00a7ce2889e20c3830db2c01f852e4a357c9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'a5ce0521ee0a8f867539a48daeb2ec64c0ab726fc7ed0378340da8c708dc84e4',
                'hash_atual' => '2fbb04bde0061b2519b6544ee5afe6189323a00167ff45e78cf02610b73e369d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '270e5d8591a5407bb4cdd9ccf27250e9cf417da7da627e0b2e1dd2f1ff94e90a',
                'hash_atual' => 'ad85fa29e10b412d2819b3b842e48c232b9e46574012a38b368ed6ee2e9a4f60',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19682,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '75bd87de5398a113e1dfe43473d0a5caeeb9e80b5b8bfb2e82dabc2e6a911e9f',
                'hash_atual' => 'e7ab32fed81a47b419eba4835617f105d2f69516a3d16fe0b7e2aea89b23c8a7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11654,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '8125d114d7524959f04f63f9f2636e93c6e358c61850c20fa037190d7dce4cd5',
                'hash_atual' => 'ac7af63dd2740facee676aa81eecc42dbe9fef91ead3db25161402d25f99935f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '86de860ea00c3ecd4db22d89a65550fabfe6f09ada684466e88f223c237e517a',
                'hash_atual' => '61e2d002b32113003c63b075f1f760dbfac4f4901a327e938169bac04cc44c8d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '2fc3babcf6a4b6ad60b9339f53bc19878ebaf4f564151dfffbd370799fabee77',
                'hash_atual' => 'e6b2412d7fe710d47fa2ba393236b5a09c0ba8b001fa3148b75302f28695d4a4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '95d4bdbf8b019f8c8821a9b4ad3934411155f183ba62f4d3aea3ff177c09811a',
                'hash_atual' => '0a6eb666866bd268c0dd4d74512bc4fb385ab03115d6029610e501dd6be05de8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '567da8efbd389403cbcd82390d9d1e6675807bd974e939a7573dde808f09c6f7',
                'hash_atual' => '61843b8592031ae1aaa9416ffd1be33e88a20fc9ea1370452a8db3dd33047d77',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'eb38b183c966ed217594d2b6b8d192da86e2cc165c5499bbf2d27eac08e0e371',
                'hash_atual' => '0085f591b05d961b74a16fc749f65c26aef36323e3c3c36f6f2bce5b96a2916b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'b5d7d355b2e72b5fc81a6b2aec25588c4082088fe5df1ce21f5e86f25633c867',
                'hash_atual' => '20856e768fb0eb8adb39c34458305429385633edd144e904e932cacc271cec6e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '8d4b95ebd98dd5504c445b28f9aeb1b3dfb8c8c0fc735b9a785abd8c4012d91d',
                'hash_atual' => '74c7c7e29f3b0b7873f8195bf6c4d5f4f682b050a86357e3aa5fd1bfaea3debc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '8088cad024eec0722a644e697fb80da6fb5534dfa0ddec6af0cf600f8e684c81',
                'hash_atual' => 'c016e470ad85527ba2d64ad968389ed64e5921b046f7ef5d5f13aaada8a73f97',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '6b92e376502b9d27b9607de1ae42d37b04eb88d3a974385ac381c20eae7f0e74',
                'hash_atual' => 'a0bcf7db546beb39a30e633fac989d7b2c2b84e446bec4de1dd935b0eb2aec32',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'edcc0a55ed863eb7234e67a28371e09f40b2179f9a4ef7f4bd7509829ba682ba',
                'hash_atual' => '795bec101f8eb0f1721136f044ef484fc94cd503ea9a8afc3e3114a15008426b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'c43f473f238008b6b9b87a6b84905da97a8d6c641f783a2ddfc7431db92fcde0',
                'hash_atual' => '648052fb98bc472bb97d44b267c1ce10eee8febdbe7c28e6a66e4f0dcad17d24',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'e7ddfe9ff8eb89ed480f677a70e05b6b9b08ac245423e82619d7e609510af704',
                'hash_atual' => '343e0be5d1b775e77a3e716f4c86ccbe05292b40931bbdaabf66968efb11232a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '322100dbde46c5e581b5709a6c63997566a2843b8eb9e4dadc2b66a41afc1592',
                'hash_atual' => 'b16d74ab0ee9d13065b48cedd1769b832c9b618e00e731df882c780fdf5f4434',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '007ae079c67560fc420cf0010c4fc55fd3f4a3d3e5a73207efedaff8187458a5',
                'hash_atual' => '619eb7c5b67bd9895300f05e8da5883cfd36d47086d238d08748ee2e3e246429',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'c849328c7e3ce694a6c3626fbb0406992cdcb01183ead6f0ffb32a6df563c339',
                'hash_atual' => '47cca5ca99f94bf42c0e2b7f1e1eac592573b5c898da4fb8923e8077e84276cc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'b84fd073b63f6d5b4511b908fb18dcd8b17a0518fe1e20c132b899974a3ea086',
                'hash_atual' => 'a97290a5d475dbbda1c773edbb201169705b85ac2295eec28b96b444edef1f60',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '01c849349bbc92a01df7aad8dc35c2400e60a3bacbea3c45e7e37d1040e19604',
                'hash_atual' => '467ff9044cafddec2c941aacda7193f6bf63bf7e802b928a4450dd8760f69705',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '5099417df2dfed39956041bea1e19f9a00d4ae9373d29d25680de9126b3f9a15',
                'hash_atual' => '3107d4465deea2cb11a99ad2b853ef0da0a024bc67fab1b03eb5412cf6bf7293',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'baeb702b2112da5540415c76c9f63579c17f5b8953f8fdca05b4310957672cf5',
                'hash_atual' => 'c8949a1ff3e87e554f2fc6ad234819fab7802676d1401988b7f1f397f287c6a1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '248472da974b5f44d7a79bb25b33665f3ad456e456c38b656b24ccd6933ed115',
                'hash_atual' => 'e27918c0bc6a11917b901b4aa33c1ea1a2072e261a34966896f3bf12cdf1954f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '2f0786ca3704e1967ef3a283561f630918e13ee2ae0484b409013514af6d656a',
                'hash_atual' => 'b0847e9cf1a41e32b783c6d2ba27dcc415f42e30ee7d477d2cf3a087297ff2d7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '32d5322202bbee434dac73a109813f99fc2dfdc453d02d77dd93650c6d1ca9ac',
                'hash_atual' => '7ec2ee2a744c990388b8efcc6cedac4b701f92759936c4d18fb3df6a51449826',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '891faa29250df75c8d7150634ce5d2cd02e9dbd107cfd71ac28a3c7d44efd507',
                'hash_atual' => 'c5076b364840bee2d688a2a7502ec0739ad9458687b7fd6a930ff461e82bb376',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '9a1cb70341c5a8722386a56fddd0a429ba6974a806b7d50c32704113618573cc',
                'hash_atual' => 'cae58547f4346eee9bc3ab9becb64d887f409f59f3d2685c7a5d1a6af71894ef',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '310b26268a3121596f088d8dc6c28be631ecacbee70dffdbc84bf58d8a8491fa',
                'hash_atual' => '31628c180d42e461a06687c5d3b87aaed5cc14e6bc1a92ccdefd0184a15faa0a',
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