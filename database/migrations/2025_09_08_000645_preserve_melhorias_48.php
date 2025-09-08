<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-08 00:06:45
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
                'hash_anterior' => '32ccbdbb47f93f9297c82de2f8779c26b89c644815a06a09951a2e5e1f7c29a6',
                'hash_atual' => '229ced2f98e540bf7531a3b1370ee65b1b89c198b7fb692f5b55428074bc398e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '933d49414a9fbf6cf648cf1f931a3737e2746e3a18525efc84a5a200a98f00c4',
                'hash_atual' => '8e761482765ea810e1ac1f9a30aabbab1c687f39f5e0f0db3835393b030f662a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '50a701f0fd53a2870e353b1369ba7a65e695dc0eb665fd65dabf817bf00b453c',
                'hash_atual' => '158a5470d27235a51d41edcd1c3928d3c6c276bb793b46fdb591674d506827a8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'c91b744a224619d4970c24278cdeb606021d04dcbea6be5c09bb07f0de399f95',
                'hash_atual' => '05e1511cdc9eb3e47fa3c708511d9d27acb1aca60f5b1c0b703fff95c5b76610',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '9bd57f024630470618f7c6ff47c9744dd997007a5a4dac3db7a5a80b2b03f07e',
                'hash_atual' => 'e5b2ad544a3408c8f8c1006919ac583ee44fec73c24e3a4203b60e540c122aba',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '9fe5fc84e6305b54e8d32e7652c372ad9eb6f57bbe1940b9fe86039fdd13ead5',
                'hash_atual' => 'f887913cd4972016f7fc8f970561484a8a79bc58a50d4f611e636b1fc81b5ecb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'd15c285cc7b15d6e97ef4ee68b39e1f3d12beb06f853823bb9b3c164d73b02c8',
                'hash_atual' => 'a6a91cdc877018db626863d3353d7899cc951b61719e22d66c6b56311d05ff96',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '0a53266881f68c87bb2e59a68a50141e7ea897ef0df2958dfaed0d0642ef8b53',
                'hash_atual' => 'a081ce66772d5b0c9edce34fafb973b71c234598f95e20c849c727875b17c5e6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'ca9e9901b6414ab7b9a4e06836b3f7b4360ded1f50cbe0112844bab15947c10a',
                'hash_atual' => '657179bb558d8593f70682295d996f3fb4265be7ad0088ddcb17612aed37ae76',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '0c50853967af141436575304e81fc5fd6a9c36bcc550d97ac6b3859b698b6205',
                'hash_atual' => 'c855460f7c3e4d865eb23fcabd91e52d04cdf64a901ee0f19ec18f99194385b5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'f4918d5f4fd079a1bc5f717eb376d6bee710fd80ae58b90a29d732b23a097b15',
                'hash_atual' => 'dbf185ba706d466e16bd3408001614ca8a9650d7e1052e3df4b4a059f439db9c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '753e23cf3c37f5bed6abce6b0988f2efd824d1e2a6f65f64536456bc97013da5',
                'hash_atual' => '1fe3c64e625e7426fad6ddaa0adae1c175c8c3f967446c95c40e78bcbe141574',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'e14a004d704018a6bc25990905aa5991c155807e0957028b43100d47a43e74a0',
                'hash_atual' => '6c5d16d9966a1f6d2af8e7d5aa1bc41fa629f8af0292876ef4f9c502ae776abd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '8bdc171f945a84c8f7584beb750b28863f5806c3d143d8385d8242ddd8b9378c',
                'hash_atual' => '587b9b52495dd9981bfe8e3bc494a9f834e71ac8b422df2a5ba5f5a4f441202f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '76433f04910026458af1089668994585784406d8797bafd667bdf376813e5174',
                'hash_atual' => '39de0743497a049589fb023fa13d77fbf3afb2cdd59cffaf2c7166593baf8429',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '9b65ee346123e1ab4f6bb4bbebbdbe0fee56447edd5d4d8f3ea04e402a716811',
                'hash_atual' => 'bb03dfa676cfc09977a793db17d758a429ba27d9f3142357926090fc45430b3e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'c77dd01451436b698f3eb6e223edc858b8f88093cd490031a96c12b93f0b409b',
                'hash_atual' => '1d934fd530ad19ff499ac264a34c6c7230e9bbdbf228bb706ecb328e3cce4165',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'f53863d1d3964a8771635700782124b38433b5bf26e48ea426787c717e3daf27',
                'hash_atual' => '75a1fafa51caff00ae71ceb3967b7a7a27810408dadcbe4d02c88f6a60e5fa6f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '5ebf7e87eb5bb0bfb0cb1966b59edab1acd0c96ce982aa3c81d4b6a47a26b57f',
                'hash_atual' => '8fd74bf1d26839d60c7ab3c1853eb8cc413c7107faa73c3dea381d6e69bec1b8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'a4db72a2190a687c048bb75f12f86dbed70b377a50ad0eef8233fda6d863f0b9',
                'hash_atual' => '954d5c1fd7c4fe98a5fd6b14f0b0e25eaed2a4e3bb63eb906957135ef8fc94c2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '7b9e5d6b1056593a72d6fadd5d7be4636277120b2320deb2cae5097783668537',
                'hash_atual' => 'dc146ac5fb1c735178563805936ec9bc5c49d6e96c291eb38865ae73d0f61b86',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '2b3f3ec940e6c0b8d6568bfaa7786150c9f8980dccb75780c88f61935541f26a',
                'hash_atual' => '96819c3c12d406752352f70bf96038d067c335adc4ac51e3dad0b276ce16c159',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '4cc20e0786791c4d6d4395091f96aa1563bfd6f8c5b2cc44d5c6bd5345d96abc',
                'hash_atual' => 'ea53d1f97507a80f37817a13bd050bcb98860311d120df4d69202cf3b8efcab3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '2fd0c9dee78721bc18083931239cee0503f90752ef70146129f8c650f11a0ad0',
                'hash_atual' => '6488e33f4d5b9f9309d1dc9b111014e56b7a3bacec47ce108f4921e7df6233d2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'cc2788b54aaf79941f1034fe89ce5c4e26430e08e2e5b013e91391c15e9c1280',
                'hash_atual' => 'd5b1f6e5bc546ead2ee6c35b6b7f9997be984b666498a1884e9f1042f72f88d2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '0ed91e22b2e06b565747b84faf6b7a0f8ef54185786cc5172dbb18f50a82a162',
                'hash_atual' => '13350aefbe564f2ebd0f06bc5796a5194a09eb257db41e447df018110946aaa8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '5f6fca100eb77fb86ebb3351bed22e23ecef9d630ed42d916c70dc06995c618f',
                'hash_atual' => 'cdee91e7875b369e10d7009895998f20e93af4b054b98d6ef30cbf144ee8e648',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '018ac986b745701b663740b15675530bd1980491aec57651ffe84a97a31cff3a',
                'hash_atual' => '3419ed511ecb916bb060cbf6e18c03c936e7c66090fdcaa146196f3ed57790e0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'ec1c96523824c47c4902ca1b913aa9f066a2ffc058a6da041b8169316baecd0c',
                'hash_atual' => '65ea6c30d64bd38d5dce677f07526dcdfc1fbbc1aa783e49ab37b47395417c44',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'c6ddc4ab26d3caaa395b75fa6ee00cc4e7fc1c442f8886b35abc71dc364f57bc',
                'hash_atual' => '6d7cdaba5b405c0b131e5d3f66fbc6751f3108a47c3b3eb84c69b0aba6566cf1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'a7d767080acb912ab0c6834c9dca116422f87cbf1ab4fe356a0b4de7e7a072f1',
                'hash_atual' => '8318fffd6f72b4811ca17b3cb9320c9a01b049262d57d78629fcd0e10e22bf20',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '56eb03934ef7c937044d3f43abce46ef9fa8d317b32fb2510b7eac5f1ee191af',
                'hash_atual' => '3641effa1813916a857633f1c786d9375e26b7303cf8fb7dd87ae543f4b9d54e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'c2461fe5c901381690c093c665baf1eac34d3af9669f8286d3d1e489b09a9b44',
                'hash_atual' => '53878e9a8a007a11818b6b45d4b51ded56e5f3def329862b6c9b789ac3ea8c1f',
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