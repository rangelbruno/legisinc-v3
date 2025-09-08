<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-07 23:32:17
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
                'hash_anterior' => '23928a3091ce94e71b9e2a6a1df610614ade4499602ae17b3e5c2a9771f239ba',
                'hash_atual' => '1c981965642d8dafd95707a1531ca0e5f9bd334724c622f6e262138472b94f8d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '81d4ac941449e4d5de4b7fad5281069b52441fadb8c3715c0802bdf36a7b0c18',
                'hash_atual' => '3b30c97e8e3b4af9cc15b61a1a11315e091db5b1e21f3caf8370540a1a35e253',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '28323d773254f93ea50c59a70ec43eb6766df763203de2a3c7bda230d81baaed',
                'hash_atual' => '03bffa968972b37808b644a8f07af5887dc2fe929d26e29e3f2ec36e25b46e00',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '45d945942e584a7a317eb25e8064b6db6ab611d9280a82c1b2e1b16c30a90b58',
                'hash_atual' => 'd58cae51842bfc90caf69ba493dff1e449590a729b4e2826625e68cb6a7725ad',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'b4f770a6558bc835e0c9aa5bb9b82dbca2192d3dbacea8c8693abeef9ff52c49',
                'hash_atual' => '0e7291d280f8b686cd8a72e2d13f0448d01e76c08c1fbe6c202f2e650e928f98',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'f4b360c3c66f3f93ee4af2ec35073ae8726555d1e5e82e9173db9300b9f43cf1',
                'hash_atual' => '69be3d75502bfd1df786e60a49ef055602360b82e83cebaedd952c4aa7928bb8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'd56bcbe2d0fb7021b72b0206da5bf5fa57cd180b3ad25c31c0e83841b3db36a3',
                'hash_atual' => '84c4c3392dd392a8813a5611fe619d4d089cc97e01d4b1712cbd3a09de346cda',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '8f5391b365de4302abffab7859df1ee3e67116c61ec69df62359b4fd94b113b8',
                'hash_atual' => 'b0cd11a657e890ff63348272fe0958747dff1b0101c99456f7b8f5575390e37d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'cdffcc1ffb6ab6981dd05d26f58e576c9a42a439710eaa4b1b464408e92d7d5c',
                'hash_atual' => '024d124dcd8b3ed9506e1d7560dadd06b5dc891f4cba8c8eec91ecf02cff681e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'c291e263226c34ef8b176186d4288d101129a64eef5415c99c2b8253af657e82',
                'hash_atual' => '69ee26321ee42dc74b9b96c9ab267f5aba39058f5af36a0f8eea2943d3a33fed',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '52b1a219f2ec582afa5e5364f1e966dbc9e21ace81a6de67f636139bda5b306f',
                'hash_atual' => 'dd0b83c9e7215425c5fd6bf7f8d82b4cfdb82829d5006ac3581460b75bb77d30',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '24cbc03f65c44d2c4d73b0e3ed5990c8da6c72485e8ac6d77ad666f794f1b07d',
                'hash_atual' => '426b06a5583a4db78955476e91d4cd35ca48be6537ac555eadcd43dfb6aa740c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'a86ac2507b5e9bbebdc256a7b8986b3fdb702ff058f37a466fc49b027fdc7d57',
                'hash_atual' => '7541a3995403862fc94376cac8dd4c39416ab9a65f4a28ff5db48f91d5e8d3d4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '6a4fb74e5240f0607bf7a69d994fe8e90c1c088cd04681c359ccb3526fa38370',
                'hash_atual' => 'dde11bf5c9bc8fac6ef667ec487bd9f9c17f8630d11b9537585a3b01298245f8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'fb71bdb26877f1fe8a2b64c3749fbe6434b8fc0124ba3f4499717de4cbd8a995',
                'hash_atual' => 'ecab9edf909253623d50fbaedb407df726a03060eb85b863e2066713c0e0e034',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'ff38e74268188125279c5f7ba30ae8898aa425e1f907d4a85d1b744d981191de',
                'hash_atual' => '2fd49b2a62749fa3e504a285e2f48788fabc9c0ecde01d93b476c8494b2fece9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '481960dad02d7b326f7dcc5bf5525b43b20506f190024d4ee26c6c335501b828',
                'hash_atual' => '2b853951668267f4efa465fd3af90e5bc854141d3fd131ab824b69ca404aab9f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'eb2146cf4038152cb08454d541d1e1ad0b133dd8a59221e1ee0694666627712a',
                'hash_atual' => '19306f7741d81a9b6f706b7c1a9ad9d3100ae6b19e26d8fde2ffad5abaaa3bdb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '83cc8b8391d4785fdd284e12354b45652f10f712fe7e8e591af4cc71d0a02895',
                'hash_atual' => 'ef59ecde8a63c95f27dbc27d33019fb193715f16b939878acef77cd6d6a4b69a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '56b462ba90ef640b935c86a612ac923f778845e3a5e201913a771e8203ced574',
                'hash_atual' => '8d185cc5f53685231b7c96b35556cc3464e69a2f0b43a01480ae6396cec5aee9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'b35acfa197d3b35fe23c4b5c118d79031d968649befe4cbc51f2c20944155706',
                'hash_atual' => '831db58eacf57102a178a49addcf8ea0e82e168aada7780ed5f527304be670e5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'd3881fe49440214051c538fb5f12e57e8a77da0d5bf625e9261b65a8ae92a0f6',
                'hash_atual' => 'bd401c9f6ef5ebfb968f3e30dcf33bbd132abb3093c42af95dc198a50adb6b53',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '8b718139b3b6dd88f6e252afd92f3a84d1f3f1c457c2362e12de7445edf4ff2e',
                'hash_atual' => '2273bca92059ddd4b6e354fcdf2f5eb2293914976d973ad05f3495fca01e7dc4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'ce0af52407baf89770af2e17574e8a68e16c509c91a68c5f4225929db0098c9c',
                'hash_atual' => 'fc3919cd9c63dcddf907e607c9027869c7127a76807195c9182e2db479d67e52',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '33c450d3abd26a6eea05d8bca082e5b8ec8258460ca4d2cf6ac21cbfad5057ce',
                'hash_atual' => '2df52f823f0c8fb36866e0595932cd9b33c222bdd1128d76b83194e749fbcce8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '6e33808657a196b322b1236d0e51ae4069c1d98e42bdbae9e338b8e2ce3e938a',
                'hash_atual' => 'd0d4657510b1596338e9912560e9cf804fb54cf999a277a23d1a22a0b07dc47e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '53db0eb7dd779bfe4e2cbe9d36f2de4a8cef2658bca17cfc235656b3d070474e',
                'hash_atual' => '5b7e848a91457148d67634ba4e16f6bebf7c4746fa386ca792ecbdbbdb569148',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'a45cbf8da5b0bb59ff316689f1771ca2989233b524548c73bfcf1d07c61d14b7',
                'hash_atual' => '5e8cf1c090353bcff47808e5c51f2f78e321a8d5b7a3c7a185be4a69fc9990ba',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '94bbdb3e6f2e26bb1a6f754f6558ed39c8a2f4279af951f8eb58b8871b6bcc34',
                'hash_atual' => 'f19282b3ac9e693e4b166663463875fc610dced21f86533929005feea3d8df4f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '5f8bafac73f021868adab2eac80546f42bd962ef4287ee0df8428d826329783e',
                'hash_atual' => '707ca54bac60207df5ee1adbc3ad962269776baf23ce3147039edc38bda2e38d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '67058dd4a1a6d928a0f529f2b15bca8f8a5654bf45efd0904d7e49132d4ae04d',
                'hash_atual' => '0efa15e1bd83fee23dd16054e4f8078621cca8222c742ce456445c216045c0e9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'c8984f0688577f02ef3164b53d76a678fffb17300bef21d4e97c8a8374e4a60b',
                'hash_atual' => 'ce34b7c460a9e1daf9024f8ca9b40f5d4138d00f728f2b19143b794fa46baa5b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '53141054b26bbe95b95e47c14bda845f31679d662391350ccd8be9d7ecbf99ab',
                'hash_atual' => '21f0f4d0ae692affbce2e13375867c75dcab0a9e8f477ef448d4cdc6d2730018',
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