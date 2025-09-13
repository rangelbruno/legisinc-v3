<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-13 14:13:10
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
                'hash_anterior' => 'bf548bfc7d2d62373eea82128569ce903fea3a64b1907a8b1ca429423d2f74ef',
                'hash_atual' => '18b11ca435d6a32cba939bf0b2c4e68cc8be13b1cba27ebcb23200ff184a350c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194593,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'cd9dc1bed23201737aa988f9612f8d41bf228512e508a06af7daaf8b9f36ae23',
                'hash_atual' => '1f8031abbd97bf82ca47e265209785dacbc5d1806205508642415215f058f1c2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '32f78c1f0b2c50f7ee4c190bd31b2ac170610da912d7c9e851c7c7f6611430ba',
                'hash_atual' => '7f992a543e55b0b486f230777ead70a177c97b5b1efb980721f704057b3a22d8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '4f8916309151b49008683109bdd9c7b97a91877a211b7fe87a387b686ff11f87',
                'hash_atual' => '3a838a867e3844082e412e80d0e84fe2c15aeabf04d66cbeeaea55d5b34f1e26',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '4ee6389461c42b175083e2f4611047c3d583344868253b369ab36d4ac272fb50',
                'hash_atual' => 'f7a30caca1d76be821631b14503059cee3af772d9e4b7edd5f12b8f6f6fbff4e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'ea7b639cb4e490f72f61a2938694d69926674a7c4caf59f4603f80c556e05fa9',
                'hash_atual' => 'e8e9b26e7d0d7e99b4e6aa5acdc8a76a7e901e5707cbc278eeda1da031eb9502',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'd81bdb9edef0416c78dcdbf7cd703da3f533c015e466b936ab8bfea7f64165f8',
                'hash_atual' => '35d8e0979b6f43377ffa07166e93030700fd8a5f02a64a42837bdcce3bf5faf3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '28a6cf98c8d1ab9cdefd862f1455ac02a00f7f4a6ec3e7b88680b0fc79b27e13',
                'hash_atual' => '09bf107f6d41ae20a43ab450c7f4427f6f1d3e6cf22be49f099f6b1774f72742',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '8cdd93adef60e942f1d52ac89d4973954740ba94c51cacf2dda35406505e8c29',
                'hash_atual' => 'b924c1655436eeb37f44546f956c926766356fdae4a671f9fad92b3d41b32271',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '85ff5f71ac5dfb931999b4427816898b862abc64f70bab860e9a6615d6e7f3c2',
                'hash_atual' => '3e68d6914ed03f9ca43298c217a02111b50166a2cae144d02c1455f9f1c6d9cf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'd4625d23b8934c9187febc20814884b6e8cee80155e48e3df33e091a16e18d7a',
                'hash_atual' => '765bb9e333fa7513b12829d489ed17a181e7789c10bdfa54127f037c4a7ea82f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '1c0d36021f915d22ab12f0f9188dde8afd39a4d1e3f046bc7c9b1fdbc25702e0',
                'hash_atual' => 'ae7bde1f0f904a1d62c1650038f014e933105f2ebe0564dba2eb846f630f7c34',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '10d2ecca456bae7a419f5168d5b3551b7694dbabbd3ce677a2c24bbb9116838c',
                'hash_atual' => '79ac528500afd962dedf68bcdc9055d6230c20304f0ffdf529f1757ea849f968',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'a3e6f40087961304e326ffdce46b40c214d2ce28e79c704bbeac2c1d33eb6a2d',
                'hash_atual' => '5ffd86e9471e81a3360d4149c32ac66dda332dfd6e106d0b5548b6452caf834c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'bd580349805c7fcc2d2d0a661ad29ad1091598b4f5a5d8a4e2b2bd462801affa',
                'hash_atual' => 'ad70a1ef56a1f15ebb43f3390fee733826fc7f2a76cb0e74be5ba2a60d0b60f4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '41fec6c393f2c5f5d9a98684bcf6774354ba26e86492d34a4cd6561b15a59f6d',
                'hash_atual' => '9f13a44c99c33243b20acb9d04d50e10a0b5710618c0feeda5bdbf69eccbd782',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '4bf983857a3d6efc497bc4623af018e49c261637d2db5b39e2eba133abb0332b',
                'hash_atual' => '5b623f1b3a28ffed59ae27ba7958a48a47781d939a12cd1a8e5a927b12d73d83',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '421fe0ab4b5b2c5cd027dc6da26ea7a04958762982a88482904a6cfb670ba782',
                'hash_atual' => '451de95727dc1c21948e0608fa04d34b931466302a9a960143258df5044aeede',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '298059a58c474a084da19cea6ce2e76be504946781ecc32abd3548cbe015a4e7',
                'hash_atual' => '65ba8b435fa797506ed14b35d0e07dabfbda3ebc4a98cba97a7a0c5da3620ae9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'a55f829ed17dc93f7f70995898191ed98d06419e480d104e5297556f2bb9c131',
                'hash_atual' => '247c09c14a126b1e4dc6858d8f029cd59f24eb328df29748eb41e0118484f22e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '5d8fa5a17b284cdd39048232dd8143e8af54dbb540bc0248800fad54938f84fe',
                'hash_atual' => 'a3f783e6707667334644d5259720b2f37bb34e74e52e47ebdf1ff23a789de3e9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '8f086a18e3a7119304b6b6ea00d92b84819796907e15870da03334da9e4e69a9',
                'hash_atual' => 'aa1ef4ce862fa8cbabf47ed96f31c16ccf639e89fa5f64e811216fa02926aff3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '1a5a97279835856abed10588bffc4c35acb5b150e3a23b1d684ccdc4ddd4c2ce',
                'hash_atual' => '4872ba1002d68b8735876f5762dbba4f5462d3adc95a2cd4f30018400a1df86a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '6de808bc576b1ed8e628c1c3a6ff9b1a8b034a400856ebcf4adfa87b62737ccf',
                'hash_atual' => '07d8a42a4c0b9cfe326fd0e0d7678b0d380042a2499f4d4a900c987a0f606fef',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'fee319c99cd1c85b7c35c37dc1d09b28cc5c44b7c4c0d0f9a01a5eaf86ed74f0',
                'hash_atual' => '190e233c9479a55547f15ead67948da32e9b05883dbdc80e17ea1733c43a2b28',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'ec1e95e44ebee590fdac4895bc11f585ebced911bbff29724529578623c62f0c',
                'hash_atual' => 'e3d607b6296b5b71a2392d3b222dfefa3b2b3d2a7cce887ddfabfe62ce51f8d0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'ab470a72727552b67ae802db534add5e2864c037df8d3b61ea333dd6a61473ee',
                'hash_atual' => '258c9dc22e6ba3bf49258b6bb6575b73ef6e163f2a09dbcc7ac45315f40d442b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'c386abeca6f3cca6f62fb2fcad95e169fde606a73e343dd344ca2d688e7b89a1',
                'hash_atual' => '4fbea50a5edefac8d2c7edd9bea5b5055228563e3a69f9ced671ffbc2e77a373',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'dec742aad9ea6ccffe1f520e126355e069ede0b4d8293e751d1ab55dcbbe30c7',
                'hash_atual' => '47a9019ac2f276b687ea019a546327dea2dbc3c0d71b949eb61b70a7589d9624',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '06fda24331dad252033fe994be427c2e65a44bd12a8cced7cd7f8552040500d4',
                'hash_atual' => '83dd362b386ec7906e27adf16ba1bbff1b2b43ebfbc239d3b6ea4d2d04f67738',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'd1a3d653073c59254bd23adf732a781ab67d350c3260e1cee9d22655d79cf19b',
                'hash_atual' => '424c55ed67253b6d19994d70e4e435a8782528868b41069ba7169bce596a7e2f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'abd3043cfae65cb3934fce0b34d8a4b2572f398e909262f74f6c412dafeda2d2',
                'hash_atual' => '60f4397f31d883b07afadfbd40a27f3106ef35743154f591913df1d4115af622',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '577deed1b14fafa9bc88d842edabe047f4992e369984e2ab120e80f6cb2b7d42',
                'hash_atual' => '1cad172224da9fd815800f841422676bb262b09278aa08adf6666f423e0c70d2',
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