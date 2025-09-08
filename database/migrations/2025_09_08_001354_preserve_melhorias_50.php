<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-08 00:13:54
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
                'hash_anterior' => '229ced2f98e540bf7531a3b1370ee65b1b89c198b7fb692f5b55428074bc398e',
                'hash_atual' => '42feacb312f8864db843a7cab49d1ba1ac89cd25af6587cb4ad6e97d03ade5a3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '8e761482765ea810e1ac1f9a30aabbab1c687f39f5e0f0db3835393b030f662a',
                'hash_atual' => 'fdfa60b4aac8f0ccbbe0088695d66422aa7736bd3720dd8e60543d27299c45d8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '158a5470d27235a51d41edcd1c3928d3c6c276bb793b46fdb591674d506827a8',
                'hash_atual' => '9b426e6501f1b6a8af4f724e883103e8010c058f1fe149fa8a6b52fff571f235',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '05e1511cdc9eb3e47fa3c708511d9d27acb1aca60f5b1c0b703fff95c5b76610',
                'hash_atual' => '55e165193cb3ba9e8fbd3df5cd052b1946f4c1da7389b9b5b89aa2a1624a6490',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'e5b2ad544a3408c8f8c1006919ac583ee44fec73c24e3a4203b60e540c122aba',
                'hash_atual' => '64445aeb8cc38819882f76ac7b9a96bd63b6faabfd7cdc12177d586ce1bfc65a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'f887913cd4972016f7fc8f970561484a8a79bc58a50d4f611e636b1fc81b5ecb',
                'hash_atual' => '274a476eb4795ea25cc6cdd14007a8f4ddb3699c9a28c7e3d730e8ebc217c1db',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'a6a91cdc877018db626863d3353d7899cc951b61719e22d66c6b56311d05ff96',
                'hash_atual' => 'ef41cb76fec28b356a2323d707ab9ac504c25677a47a40e46978ff994f022b2a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'a081ce66772d5b0c9edce34fafb973b71c234598f95e20c849c727875b17c5e6',
                'hash_atual' => 'a18def146c6181488e664d75f326480f95a5e9b424b5aeabf54b765513e9dc53',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '657179bb558d8593f70682295d996f3fb4265be7ad0088ddcb17612aed37ae76',
                'hash_atual' => 'd7248a4e52ac2e7dd591e0618b3a68fd0c0e4949eb95357f4a55f0aef0978d59',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'c855460f7c3e4d865eb23fcabd91e52d04cdf64a901ee0f19ec18f99194385b5',
                'hash_atual' => '2293f7764f69b094612953b10eee53f5c6ce8564f39f26635bf38cb87db2bfb1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'dbf185ba706d466e16bd3408001614ca8a9650d7e1052e3df4b4a059f439db9c',
                'hash_atual' => '1e25940764fef80581c5bbaaf387d7124f201b67d19ab2418a14d48b123e1f83',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '1fe3c64e625e7426fad6ddaa0adae1c175c8c3f967446c95c40e78bcbe141574',
                'hash_atual' => 'bea81995013f48b5157e2fc592bc6749de2208b64b2664e1880fc75d4e6a142b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '6c5d16d9966a1f6d2af8e7d5aa1bc41fa629f8af0292876ef4f9c502ae776abd',
                'hash_atual' => '0c3b590542f2b336cb31bccf799826634e26e00e8a612f3f7fd0959f1f96c9bb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '587b9b52495dd9981bfe8e3bc494a9f834e71ac8b422df2a5ba5f5a4f441202f',
                'hash_atual' => '4c27eee4627af2ae8f1d2765d4727d15a58eebf3d9817b651abd87aaff90cdeb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '39de0743497a049589fb023fa13d77fbf3afb2cdd59cffaf2c7166593baf8429',
                'hash_atual' => '3f122f5b058e65333050d15e4fd911ce1c8b8e7597f9b24ae7645b9c7c47438d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'bb03dfa676cfc09977a793db17d758a429ba27d9f3142357926090fc45430b3e',
                'hash_atual' => '60e57602dfffc098029c035099ed64d04a81c53e5a09f6be558ca59e5326a6ae',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '1d934fd530ad19ff499ac264a34c6c7230e9bbdbf228bb706ecb328e3cce4165',
                'hash_atual' => 'a860e581f1606a03f208ef46170b81309478d1a8170e9fc185d35fed418a620c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '75a1fafa51caff00ae71ceb3967b7a7a27810408dadcbe4d02c88f6a60e5fa6f',
                'hash_atual' => 'e7b7c94f8d577d812e6f6e4ec97d959a81a3d22dcb69c26d528c480768490206',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '8fd74bf1d26839d60c7ab3c1853eb8cc413c7107faa73c3dea381d6e69bec1b8',
                'hash_atual' => 'c9418cb107fb6a40854948a0347d11ea3620d122a8db33566dbfe45484758d98',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '954d5c1fd7c4fe98a5fd6b14f0b0e25eaed2a4e3bb63eb906957135ef8fc94c2',
                'hash_atual' => 'b9cbdeb4e8a2480663025238999a7d465771a84674ac49b05c938192c8650ca4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'dc146ac5fb1c735178563805936ec9bc5c49d6e96c291eb38865ae73d0f61b86',
                'hash_atual' => 'f551279b9a4503411f68d72b703c5944d00e8c96f6e2694a95c6f59bc645bf15',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '96819c3c12d406752352f70bf96038d067c335adc4ac51e3dad0b276ce16c159',
                'hash_atual' => '713960782c5718cd210f78b6325df2782415ebef02bb3a0e78cd1bcb6ba86805',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'ea53d1f97507a80f37817a13bd050bcb98860311d120df4d69202cf3b8efcab3',
                'hash_atual' => 'b4e4d84c703c82b8f894ffa194346a5bb3fe1b13bb4875fd39302d695404a1cb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '6488e33f4d5b9f9309d1dc9b111014e56b7a3bacec47ce108f4921e7df6233d2',
                'hash_atual' => 'ec65c8e7fcbb3e7f3164e6fe3948a8d44eb96f3c6f2a408f3e58e79bd7514f7d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'd5b1f6e5bc546ead2ee6c35b6b7f9997be984b666498a1884e9f1042f72f88d2',
                'hash_atual' => '1ec51e007243d9b0bd484c22fd30aaf0223ca218ee5952e2fdea6d775aaa26ee',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '13350aefbe564f2ebd0f06bc5796a5194a09eb257db41e447df018110946aaa8',
                'hash_atual' => 'ddacbf412b3f373b8ce378614749b07a3d6bf0d955e477d9044a34d34e9f27af',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'cdee91e7875b369e10d7009895998f20e93af4b054b98d6ef30cbf144ee8e648',
                'hash_atual' => '0652d0c137d84810cbd9ce2adce7f4011a38d23750439c00fc9a544c953cac93',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '3419ed511ecb916bb060cbf6e18c03c936e7c66090fdcaa146196f3ed57790e0',
                'hash_atual' => 'd2398d8b9996b10c8bc1a316f56a5732e7c729a5f9fa5992555dcdf6fbfe8956',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '65ea6c30d64bd38d5dce677f07526dcdfc1fbbc1aa783e49ab37b47395417c44',
                'hash_atual' => 'ff77298d36b8231726662d5a1142485219b76fb91b7a0d9b063c82813c73519f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '6d7cdaba5b405c0b131e5d3f66fbc6751f3108a47c3b3eb84c69b0aba6566cf1',
                'hash_atual' => '001e20cfb90d2fcfc7e6ce9969670afc2274852be8fd5d23f03ed7982aa27c7b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '8318fffd6f72b4811ca17b3cb9320c9a01b049262d57d78629fcd0e10e22bf20',
                'hash_atual' => 'cfbd91a2359167152b57affe2ab15cf92a98eb26646d3b79d0acd3b78938498e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '3641effa1813916a857633f1c786d9375e26b7303cf8fb7dd87ae543f4b9d54e',
                'hash_atual' => '92bf5975fc444a22c5ae9c29850dee8b478abf324b72fe7a25b297fa0f38a628',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '53878e9a8a007a11818b6b45d4b51ded56e5f3def329862b6c9b789ac3ea8c1f',
                'hash_atual' => '93283a8bb8f07081e067eb5b32a6b05903e811d0ccc54e5fcfbee2ed18637de2',
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