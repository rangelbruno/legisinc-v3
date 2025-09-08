<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-08 00:26:02
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
                'hash_anterior' => '42feacb312f8864db843a7cab49d1ba1ac89cd25af6587cb4ad6e97d03ade5a3',
                'hash_atual' => '23173fd08b27ae0a1642cb3fe0e0841f48324ccef8d13386ca7e1859d1890b1d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'fdfa60b4aac8f0ccbbe0088695d66422aa7736bd3720dd8e60543d27299c45d8',
                'hash_atual' => 'ee6c1d25c1bf7bb46984affbae4782e53ac2ab5949256540e3cd2bba4363e3d9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '9b426e6501f1b6a8af4f724e883103e8010c058f1fe149fa8a6b52fff571f235',
                'hash_atual' => 'ccd3049d7080abcc33d9371b102078f82bffdaaad060e7dc0f0de3f01a7ac9db',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '55e165193cb3ba9e8fbd3df5cd052b1946f4c1da7389b9b5b89aa2a1624a6490',
                'hash_atual' => 'f9bf53bd020c0aba0a835dac6e36e35479615d2370a2c0ac0ddc8183e0f92841',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '64445aeb8cc38819882f76ac7b9a96bd63b6faabfd7cdc12177d586ce1bfc65a',
                'hash_atual' => 'ed561877e66c16f0dc2a65a4e1ab99e4c81e214e580880e1fcd0b55e6400601a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '274a476eb4795ea25cc6cdd14007a8f4ddb3699c9a28c7e3d730e8ebc217c1db',
                'hash_atual' => 'fe2d9e486556a6e75dff187a17134d6dcb13f6bad2c1c47d65eaeee9b3f6517e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'ef41cb76fec28b356a2323d707ab9ac504c25677a47a40e46978ff994f022b2a',
                'hash_atual' => '29114ccc19043caaf59511b03cecf24f6724c00600c269ac2f9fb80e9e99e3fa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'a18def146c6181488e664d75f326480f95a5e9b424b5aeabf54b765513e9dc53',
                'hash_atual' => '4dd27756222cfa939e731a20bd364a1a1b8d78e7504e15ebab4790b041e66f92',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'd7248a4e52ac2e7dd591e0618b3a68fd0c0e4949eb95357f4a55f0aef0978d59',
                'hash_atual' => '4293b913c15cc6837039fc8bb0a5425e1b690768254d01123184428967a42aee',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '2293f7764f69b094612953b10eee53f5c6ce8564f39f26635bf38cb87db2bfb1',
                'hash_atual' => 'a008815ce3a73ffbf875999f8ea0c76d2d52ea40d2c4d7ccdf6e311d881f7bf9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '1e25940764fef80581c5bbaaf387d7124f201b67d19ab2418a14d48b123e1f83',
                'hash_atual' => '3e4f3528f5aab947b65427c6e06ec37a62bcb6065ab87eb9a3a1917bd6d19aa0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'bea81995013f48b5157e2fc592bc6749de2208b64b2664e1880fc75d4e6a142b',
                'hash_atual' => 'bb1a91b354b7ba44d08b71514c8aaa3abe8cf4d5432bc7b23393da3921e5eb46',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '0c3b590542f2b336cb31bccf799826634e26e00e8a612f3f7fd0959f1f96c9bb',
                'hash_atual' => '1523db328673ceaf2011139557896a196f1da3e5d7a640edcdd055651e663038',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '4c27eee4627af2ae8f1d2765d4727d15a58eebf3d9817b651abd87aaff90cdeb',
                'hash_atual' => '45acecaa986102d43e189d4661c73fa3aa326a2d8c755bcacd87e0a2761826fc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '3f122f5b058e65333050d15e4fd911ce1c8b8e7597f9b24ae7645b9c7c47438d',
                'hash_atual' => 'c426f231ba17662210bb247b6ed064f1b81ca4e4c195da1faaf6661af9d40edc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '60e57602dfffc098029c035099ed64d04a81c53e5a09f6be558ca59e5326a6ae',
                'hash_atual' => '6526d68568857c5c34f37efb3125cdc09a15011dbdcfb0a8d1730464ba36bc55',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'a860e581f1606a03f208ef46170b81309478d1a8170e9fc185d35fed418a620c',
                'hash_atual' => '1a41bd533ba2107c005ad6100856ed919db4c9c3a16416b2321e41f0cee7cb25',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'e7b7c94f8d577d812e6f6e4ec97d959a81a3d22dcb69c26d528c480768490206',
                'hash_atual' => '6881be8e7d865762465b3be9a6b2f6a6612b0adca5eec85a541dd8e07445f52a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'c9418cb107fb6a40854948a0347d11ea3620d122a8db33566dbfe45484758d98',
                'hash_atual' => 'd72d892eec8333ef8e31e6b8066fc1a053053596c25ae032a2b25ddcc39b196e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'b9cbdeb4e8a2480663025238999a7d465771a84674ac49b05c938192c8650ca4',
                'hash_atual' => '568257ca7d40f740c0d10cf607f1cd3c91693108e3b6094330d951f8ad0fbd90',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'f551279b9a4503411f68d72b703c5944d00e8c96f6e2694a95c6f59bc645bf15',
                'hash_atual' => 'a29fa2b44d8df6c408319958858582a82199dcaf9e62eaa79b3d443227a2b960',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '713960782c5718cd210f78b6325df2782415ebef02bb3a0e78cd1bcb6ba86805',
                'hash_atual' => 'b73ecbbf96a65453fd96051101a185fb7dc28d4dd8690a029daa584f0c4d1877',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'b4e4d84c703c82b8f894ffa194346a5bb3fe1b13bb4875fd39302d695404a1cb',
                'hash_atual' => 'fe385180f985bcf5c1d84fbd184e25a91e35ff0a5403f24d53e9882e234a298a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'ec65c8e7fcbb3e7f3164e6fe3948a8d44eb96f3c6f2a408f3e58e79bd7514f7d',
                'hash_atual' => '47d8ba2d9e856ccce39530608c9f2cfe9d2d00bccb61c51460d4f9252293df5a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '1ec51e007243d9b0bd484c22fd30aaf0223ca218ee5952e2fdea6d775aaa26ee',
                'hash_atual' => 'b70be5a27e91cfc36a878d0dfef4b38aaf888e741bf6ca1b46e7fb5b70e9d91d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'ddacbf412b3f373b8ce378614749b07a3d6bf0d955e477d9044a34d34e9f27af',
                'hash_atual' => '4eda1ce44ef749a2f8dc27eb7f98bacb046c6101fab61ae7fe22e48aab03af64',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '0652d0c137d84810cbd9ce2adce7f4011a38d23750439c00fc9a544c953cac93',
                'hash_atual' => '4491682266a9997cb950c8cd6504f7650178faccab38821ba500a1c8bcdf6acb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'd2398d8b9996b10c8bc1a316f56a5732e7c729a5f9fa5992555dcdf6fbfe8956',
                'hash_atual' => '5ebeff407bfa0969e09b63826de87ebec2a4249e9e724c19c36c6ce4a603882a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'ff77298d36b8231726662d5a1142485219b76fb91b7a0d9b063c82813c73519f',
                'hash_atual' => '4b3e75013e1df5b7444e7c022c08afa789500d8234e30ec709f99bd7ca73499c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '001e20cfb90d2fcfc7e6ce9969670afc2274852be8fd5d23f03ed7982aa27c7b',
                'hash_atual' => 'f61910e534000b99171560096cab979906c129e979fe5f39548a257e72b4a248',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'cfbd91a2359167152b57affe2ab15cf92a98eb26646d3b79d0acd3b78938498e',
                'hash_atual' => '3dea67f5dc5ff3a3a8c4e244e28119dd3c3b9ccb638f7bc21dc5358f8b99c8c7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '92bf5975fc444a22c5ae9c29850dee8b478abf324b72fe7a25b297fa0f38a628',
                'hash_atual' => '92433bfe3363b5b957aff70b666bccd43bea6a4d7a94aa2973456f6172516596',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '93283a8bb8f07081e067eb5b32a6b05903e811d0ccc54e5fcfbee2ed18637de2',
                'hash_atual' => 'fd83b8bc286dfb32890637dc22d3238e88aa53d334c3e417313c410c25ebf3f6',
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