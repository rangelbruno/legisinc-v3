<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-13 12:26:13
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
                'hash_anterior' => '365250a78e328f63258525d8e248186bfdcee58e4f60c2cef963c82286eebf32',
                'hash_atual' => 'f3bbae443156c378860e23858d5e5e46d56cd27b3fe009a305b15a53c24a2b8b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194593,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'a329e83c12d0037584809e6b23ddef3d3a6c6353a88470c6d2d74955f0448ef1',
                'hash_atual' => '3b10c5e506de2e67ff601b65502d47b59a752caafc6c18783085086fe93f5f03',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '17f8950ff913bb4fc19b9646b4092d71101d8a05e851aaa29a3529a8b722cdaf',
                'hash_atual' => 'ba0f4d748f0bd0467bc1a646875fb210f08e8cae59b6bc9aa42e3c3899168861',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '9c4b804775f10263fe6cceb87fafe13dbe14c6001dc3f8bece582e52332a3b54',
                'hash_atual' => 'b95612b45f5abc5e9310f416dcb6a9adc1b8023ca8ab2206243a0ab8aaf89996',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '2a223d48f21793e12b5aba9249d188670a9218ea961d0eb4f5c074cb9e9ac9ac',
                'hash_atual' => '88723a510e187582b58cde4032d01f1a90dfd3a07f4cf93e50258d020fcaa26b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'bed62efa7f9d58515b2aa1494a5d05dc426df1556d0143b8e85a1111d326e8b7',
                'hash_atual' => 'dab461d4f2f258250e132f4377fec31eaf93e80fa2dbf67db5cfed08856e529a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'a57831fd6c46a7df1820927ba79366800657777329758a58eb677100603f5e42',
                'hash_atual' => '6eaf1b4c8cab21102e9dfbed04e9e21a5e99932ec757a1e7136fe9e70b73c9fa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'fa85c89995036c093dd9af1c931850268be28c4929352cc723dea7f96939374e',
                'hash_atual' => 'b33f09a90c1b48f072d4452ea2239d276c0bd0abfac695224e8a23d0bd315a01',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '5d2e9160c57c590ca297eabdde7e3cdd7fc88b0bbd6a1df6e7338f13242a32f2',
                'hash_atual' => 'e1b01df668cfe0e9449ed19bf26fc321ad349f2f09ed9cb834dac001fa94d780',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'da4bf992f9a97a2c3e1a3efec0f0e9545c7cdbcab33a8227475ffdc59ebfd033',
                'hash_atual' => 'd75cfbc0474255fa0ad8ef20a3ddccf155e43e0caa9cfa3dedcf0eee39b5f63d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'e3bef3e95e730189b8add356b31bcd40e717e6a82520a13ddb8f58ccac2fad65',
                'hash_atual' => 'ef8367a4206b90b37d553818d4d314e13262ccbc0cb7e5a49387e5298c2f9728',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '9cf8e5cc7a4fb1958ed147d299b63cfc665e235900c35795ec77b2e88a77e167',
                'hash_atual' => '672f6899709a12b80bcad22347dfcfdf39889dd4bc8f84a4c48ea288c2794c11',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '8dabf63c6df253e731afbad1d6597b028ab9c259e40ec273d48e92623e21a352',
                'hash_atual' => '5751e2cfead09f5ee8145eb393d2522937915235c2772a80bafecaed4d377862',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'ccb3ba0fd23cd28a2935f5be66b7b789c44e67d489d132c9884a8d467781a063',
                'hash_atual' => '8bd833503558c658c51a44a23f52e4bb5d57557afc1b731208a373ee65c31178',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '4817f3ca51afaeaee50a53d058d5715dd91faa36ac518de330589cb3cef3887c',
                'hash_atual' => 'a38e3442fb2dcee2009d38e3aef948872f724d377dd64fd8e103ba4779fbcd58',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'ba5421945117d7788b834687b4ce4be002c7322974ef775d2bf191d3f41fd74e',
                'hash_atual' => '5436f94fa916993973f231005af4df03e952aad13fbbef62c473f6e53487c96a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'd6e14ab11398c52f07a20c986ce0ee5bda63618e196e23595c1670fae3c3e5f1',
                'hash_atual' => 'dcdd2e00c00aada98212f78ba8ff79c263942d94bae8056477bd891b4aaaacbd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '8926f2438895480ebbfc0953b485cf83c76d93b7dfea24b96a5ddc3c3d20b7d4',
                'hash_atual' => 'c2b3f3ab0a85cf4e3ea678d9671040197f9bae4dbbdfe289e6e906d148be6b6d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '94efea01298576ae5c2f5482022efb61fde01842c7438283bd5345c100ad25bc',
                'hash_atual' => '7041b19afe9acdd63b1e47d5480a9de4aee11abef71824e471792a42810ca932',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '2facc7a94d90c3bb6792ecb22b7148b70db6dc74b3509b649070e52c33954e4d',
                'hash_atual' => '96bb4532abe5d3c3caea81147a7e858d5f3573ab0b20e66c59b88d90882dc734',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'cf64af0dec3a9c96a49e50571282eb0b12d7921873f406465e6fdd59ef3347e4',
                'hash_atual' => '31cef4a4c452c88a3154ebd25f065a08646c74bb7fe1dce905fdb5f3f1d123f2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'c02620c9771cf98e86fc4d695c0adcf0e5bf2240640a8bf4cfbbd336b05a3a54',
                'hash_atual' => '7779e3195f8d69321894ffd9da23ed055af88f334227c1becf4990213b5dc89f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '5f3ce0183d34a7b342c08913067e5f6618e60d15f0c33f2413e36253e5052f9c',
                'hash_atual' => '8dd606b6b0e95a17beae3cafa7019c1d25992940f4db0e2d93d098a9c9275bd8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '82f0d6fa14549d29f99f0abd422ee64fe6cc56614f49d263e1e11fd9df0b9737',
                'hash_atual' => '5bea17e93893686b29e6671de4ca3a078c1f580a6ddb1420baedcb6446012457',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'e5c4cc4027692d9c28e2326f25ea0634201c488f02559287509b3385bca8cb54',
                'hash_atual' => '4f1884c5488555165c2987a24530121f914728c7308adbd621522129aff4af45',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'accb741985d12df93074fa0bc8cae0c105ae45261dee7594eaa08a88da8be90b',
                'hash_atual' => '42962883884fc3f085fb626dd53d46a2feedb7255b1f62ebde20afc609b4416b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '01a7a5676ae2e5b36a5fb26a7f08f7bffd645a5ba70fc537a91cd5f8ea9b45c9',
                'hash_atual' => '259b3aaddda2271e76d7e47a1ce49cc192e8fa7130e1f2942edb632acafcbe06',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'e6ea4b5e41d8020ab727b49ddd5014735177cb000fb582bdb30bb6db5d3de83f',
                'hash_atual' => '09d57390c61c204cbf4bc3d1ad5f295a0390b531a6a807f97ff409bb8a219f82',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'a245bc99d13bd505ed89654de1d213f632cdba0057d7c91b6edfc64a1a9cbb11',
                'hash_atual' => '63c6ab100dd9fb472b1fd94604b92b3b30573fac24be7070f17065f326ebff7d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '6df08759acfe88f5410f3a8427e0b6b5a7700280ded1fd63619eee232ba6b2b6',
                'hash_atual' => 'cdd13fe5b386e0e625e12b5c1fb3112524f3ed293d7af13444b54bf422788be3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '7a8142a88dafea467e79abfec6bebe9df818686b8f8a4b0c047918976b045f6d',
                'hash_atual' => 'bc2092267e5f9e9b4b7731913afe742e927f49209fa48abb93d1cfc4be13c23d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '4a4c2334e72e6be543d610ffaca24c630b047fc71df918869f6ca425b4d429f4',
                'hash_atual' => 'c857f923e5920fc270c169b70b42ff60f0f536ba52533af3478f48dab913367b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '6a3a60e69fce99637cb3c8a69c2492d4e6fff8eb97349ad449ed092021b6bd38',
                'hash_atual' => '7281de9f7fc8addc1e013e8a5954b0a452cf89f65534fca0803c5267c2e18d5b',
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