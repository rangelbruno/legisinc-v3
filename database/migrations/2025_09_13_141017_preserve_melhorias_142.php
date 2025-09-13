<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-13 14:10:17
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
                'hash_anterior' => 'f3bbae443156c378860e23858d5e5e46d56cd27b3fe009a305b15a53c24a2b8b',
                'hash_atual' => 'bf548bfc7d2d62373eea82128569ce903fea3a64b1907a8b1ca429423d2f74ef',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194593,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '3b10c5e506de2e67ff601b65502d47b59a752caafc6c18783085086fe93f5f03',
                'hash_atual' => 'cd9dc1bed23201737aa988f9612f8d41bf228512e508a06af7daaf8b9f36ae23',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'ba0f4d748f0bd0467bc1a646875fb210f08e8cae59b6bc9aa42e3c3899168861',
                'hash_atual' => '32f78c1f0b2c50f7ee4c190bd31b2ac170610da912d7c9e851c7c7f6611430ba',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'b95612b45f5abc5e9310f416dcb6a9adc1b8023ca8ab2206243a0ab8aaf89996',
                'hash_atual' => '4f8916309151b49008683109bdd9c7b97a91877a211b7fe87a387b686ff11f87',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '88723a510e187582b58cde4032d01f1a90dfd3a07f4cf93e50258d020fcaa26b',
                'hash_atual' => '4ee6389461c42b175083e2f4611047c3d583344868253b369ab36d4ac272fb50',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'dab461d4f2f258250e132f4377fec31eaf93e80fa2dbf67db5cfed08856e529a',
                'hash_atual' => 'ea7b639cb4e490f72f61a2938694d69926674a7c4caf59f4603f80c556e05fa9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '6eaf1b4c8cab21102e9dfbed04e9e21a5e99932ec757a1e7136fe9e70b73c9fa',
                'hash_atual' => 'd81bdb9edef0416c78dcdbf7cd703da3f533c015e466b936ab8bfea7f64165f8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'b33f09a90c1b48f072d4452ea2239d276c0bd0abfac695224e8a23d0bd315a01',
                'hash_atual' => '28a6cf98c8d1ab9cdefd862f1455ac02a00f7f4a6ec3e7b88680b0fc79b27e13',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'e1b01df668cfe0e9449ed19bf26fc321ad349f2f09ed9cb834dac001fa94d780',
                'hash_atual' => '8cdd93adef60e942f1d52ac89d4973954740ba94c51cacf2dda35406505e8c29',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'd75cfbc0474255fa0ad8ef20a3ddccf155e43e0caa9cfa3dedcf0eee39b5f63d',
                'hash_atual' => '85ff5f71ac5dfb931999b4427816898b862abc64f70bab860e9a6615d6e7f3c2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'ef8367a4206b90b37d553818d4d314e13262ccbc0cb7e5a49387e5298c2f9728',
                'hash_atual' => 'd4625d23b8934c9187febc20814884b6e8cee80155e48e3df33e091a16e18d7a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '672f6899709a12b80bcad22347dfcfdf39889dd4bc8f84a4c48ea288c2794c11',
                'hash_atual' => '1c0d36021f915d22ab12f0f9188dde8afd39a4d1e3f046bc7c9b1fdbc25702e0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '5751e2cfead09f5ee8145eb393d2522937915235c2772a80bafecaed4d377862',
                'hash_atual' => '10d2ecca456bae7a419f5168d5b3551b7694dbabbd3ce677a2c24bbb9116838c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '8bd833503558c658c51a44a23f52e4bb5d57557afc1b731208a373ee65c31178',
                'hash_atual' => 'a3e6f40087961304e326ffdce46b40c214d2ce28e79c704bbeac2c1d33eb6a2d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'a38e3442fb2dcee2009d38e3aef948872f724d377dd64fd8e103ba4779fbcd58',
                'hash_atual' => 'bd580349805c7fcc2d2d0a661ad29ad1091598b4f5a5d8a4e2b2bd462801affa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '5436f94fa916993973f231005af4df03e952aad13fbbef62c473f6e53487c96a',
                'hash_atual' => '41fec6c393f2c5f5d9a98684bcf6774354ba26e86492d34a4cd6561b15a59f6d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'dcdd2e00c00aada98212f78ba8ff79c263942d94bae8056477bd891b4aaaacbd',
                'hash_atual' => '4bf983857a3d6efc497bc4623af018e49c261637d2db5b39e2eba133abb0332b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'c2b3f3ab0a85cf4e3ea678d9671040197f9bae4dbbdfe289e6e906d148be6b6d',
                'hash_atual' => '421fe0ab4b5b2c5cd027dc6da26ea7a04958762982a88482904a6cfb670ba782',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '7041b19afe9acdd63b1e47d5480a9de4aee11abef71824e471792a42810ca932',
                'hash_atual' => '298059a58c474a084da19cea6ce2e76be504946781ecc32abd3548cbe015a4e7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '96bb4532abe5d3c3caea81147a7e858d5f3573ab0b20e66c59b88d90882dc734',
                'hash_atual' => 'a55f829ed17dc93f7f70995898191ed98d06419e480d104e5297556f2bb9c131',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '31cef4a4c452c88a3154ebd25f065a08646c74bb7fe1dce905fdb5f3f1d123f2',
                'hash_atual' => '5d8fa5a17b284cdd39048232dd8143e8af54dbb540bc0248800fad54938f84fe',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '7779e3195f8d69321894ffd9da23ed055af88f334227c1becf4990213b5dc89f',
                'hash_atual' => '8f086a18e3a7119304b6b6ea00d92b84819796907e15870da03334da9e4e69a9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '8dd606b6b0e95a17beae3cafa7019c1d25992940f4db0e2d93d098a9c9275bd8',
                'hash_atual' => '1a5a97279835856abed10588bffc4c35acb5b150e3a23b1d684ccdc4ddd4c2ce',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '5bea17e93893686b29e6671de4ca3a078c1f580a6ddb1420baedcb6446012457',
                'hash_atual' => '6de808bc576b1ed8e628c1c3a6ff9b1a8b034a400856ebcf4adfa87b62737ccf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '4f1884c5488555165c2987a24530121f914728c7308adbd621522129aff4af45',
                'hash_atual' => 'fee319c99cd1c85b7c35c37dc1d09b28cc5c44b7c4c0d0f9a01a5eaf86ed74f0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '42962883884fc3f085fb626dd53d46a2feedb7255b1f62ebde20afc609b4416b',
                'hash_atual' => 'ec1e95e44ebee590fdac4895bc11f585ebced911bbff29724529578623c62f0c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '259b3aaddda2271e76d7e47a1ce49cc192e8fa7130e1f2942edb632acafcbe06',
                'hash_atual' => 'ab470a72727552b67ae802db534add5e2864c037df8d3b61ea333dd6a61473ee',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '09d57390c61c204cbf4bc3d1ad5f295a0390b531a6a807f97ff409bb8a219f82',
                'hash_atual' => 'c386abeca6f3cca6f62fb2fcad95e169fde606a73e343dd344ca2d688e7b89a1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '63c6ab100dd9fb472b1fd94604b92b3b30573fac24be7070f17065f326ebff7d',
                'hash_atual' => 'dec742aad9ea6ccffe1f520e126355e069ede0b4d8293e751d1ab55dcbbe30c7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'cdd13fe5b386e0e625e12b5c1fb3112524f3ed293d7af13444b54bf422788be3',
                'hash_atual' => '06fda24331dad252033fe994be427c2e65a44bd12a8cced7cd7f8552040500d4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'bc2092267e5f9e9b4b7731913afe742e927f49209fa48abb93d1cfc4be13c23d',
                'hash_atual' => 'd1a3d653073c59254bd23adf732a781ab67d350c3260e1cee9d22655d79cf19b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'c857f923e5920fc270c169b70b42ff60f0f536ba52533af3478f48dab913367b',
                'hash_atual' => 'abd3043cfae65cb3934fce0b34d8a4b2572f398e909262f74f6c412dafeda2d2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '7281de9f7fc8addc1e013e8a5954b0a452cf89f65534fca0803c5267c2e18d5b',
                'hash_atual' => '577deed1b14fafa9bc88d842edabe047f4992e369984e2ab120e80f6cb2b7d42',
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