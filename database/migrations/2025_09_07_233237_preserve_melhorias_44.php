<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-07 23:32:37
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
                'hash_anterior' => '1c981965642d8dafd95707a1531ca0e5f9bd334724c622f6e262138472b94f8d',
                'hash_atual' => '5615a277b50301a115e7f57feb353ad2922f0649fc3bc7ad49bf0006acf9e307',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '3b30c97e8e3b4af9cc15b61a1a11315e091db5b1e21f3caf8370540a1a35e253',
                'hash_atual' => 'fbac509a161608805b46670ed1e068da5a243d2d2ecdad53460e48c3158e0e43',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '03bffa968972b37808b644a8f07af5887dc2fe929d26e29e3f2ec36e25b46e00',
                'hash_atual' => '1347fbd6b0c34b8c387d4c9bae90127a0fc0d4da80768148e88303b7f76cc431',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'd58cae51842bfc90caf69ba493dff1e449590a729b4e2826625e68cb6a7725ad',
                'hash_atual' => 'c5ffb7188dc38e68a2b2a3823242ea410beee556af7724195d53f297f893c2d8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '0e7291d280f8b686cd8a72e2d13f0448d01e76c08c1fbe6c202f2e650e928f98',
                'hash_atual' => 'bef7ac60b00ec25849ff12a39e472710c87bf435ccd8fa0b712a0c4a47fe1400',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '69be3d75502bfd1df786e60a49ef055602360b82e83cebaedd952c4aa7928bb8',
                'hash_atual' => '634c91ae1a7d67d7fa5b1499aaf0655c0609c3997567eecacb5f68b78d596678',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '84c4c3392dd392a8813a5611fe619d4d089cc97e01d4b1712cbd3a09de346cda',
                'hash_atual' => 'dc1964050301b9440eb48b05ea81a6c132c8bde1f2d673c130062e80fe9be1a6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'b0cd11a657e890ff63348272fe0958747dff1b0101c99456f7b8f5575390e37d',
                'hash_atual' => 'cd6fe9ca5846744b815181ddb0eabcd1d97088d41879a67fb27fe927cf895000',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '024d124dcd8b3ed9506e1d7560dadd06b5dc891f4cba8c8eec91ecf02cff681e',
                'hash_atual' => 'c77beed77bfc7b36962ee3e29de6a3f4df79ffbaf3d37779e9cc0ea91bee94b0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '69ee26321ee42dc74b9b96c9ab267f5aba39058f5af36a0f8eea2943d3a33fed',
                'hash_atual' => 'c72238921aa20e6236df5f362d6d3858cc09c042f6865a4e6b00130fb5cd4aba',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'dd0b83c9e7215425c5fd6bf7f8d82b4cfdb82829d5006ac3581460b75bb77d30',
                'hash_atual' => '41c253bc53cb45664bf780641aacb1c2ee603857470951de32113239e4d1c090',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '426b06a5583a4db78955476e91d4cd35ca48be6537ac555eadcd43dfb6aa740c',
                'hash_atual' => 'e85e90d135942b49c505dab2d4fda9ae4307626db595a302704f66ccb3f2827d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '7541a3995403862fc94376cac8dd4c39416ab9a65f4a28ff5db48f91d5e8d3d4',
                'hash_atual' => 'cf43b89ee44f911403693a7b66a9630eb78e7175810fb226565bcc7a12248bf1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'dde11bf5c9bc8fac6ef667ec487bd9f9c17f8630d11b9537585a3b01298245f8',
                'hash_atual' => 'b1915efe885aa53839ef8327d715c778f4cef687ad2dff9373bcf3375f31a851',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'ecab9edf909253623d50fbaedb407df726a03060eb85b863e2066713c0e0e034',
                'hash_atual' => 'd05cce5b984ebc35e5a95d33bf0d699f9e205733d8e3a3dc501dbba62286de94',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '2fd49b2a62749fa3e504a285e2f48788fabc9c0ecde01d93b476c8494b2fece9',
                'hash_atual' => '730aed7a7e88f91148a3de33cf4f40380f37a15fc37e1a50848c35fb80a24a15',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '2b853951668267f4efa465fd3af90e5bc854141d3fd131ab824b69ca404aab9f',
                'hash_atual' => '6cbbe3fb768dd79b814ad8d6463f50326f33c570ee4914d5326c1861fa3f05b1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '19306f7741d81a9b6f706b7c1a9ad9d3100ae6b19e26d8fde2ffad5abaaa3bdb',
                'hash_atual' => '3eef0fddbe37cfea3d2a02d26def48de75177cef05cd0c60e748a22dfdad92ab',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'ef59ecde8a63c95f27dbc27d33019fb193715f16b939878acef77cd6d6a4b69a',
                'hash_atual' => '401ce4fa06a51f9d6bfd486bac7ff5a16204f684e3182e5972d0cdd48b485111',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '8d185cc5f53685231b7c96b35556cc3464e69a2f0b43a01480ae6396cec5aee9',
                'hash_atual' => 'bb30cf449ee18b2593bf1a6ee2b42df11a793545d1548cb784b975cd36fa6b03',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '831db58eacf57102a178a49addcf8ea0e82e168aada7780ed5f527304be670e5',
                'hash_atual' => '8d1d9dfdc83e6a86518b09370c6c77a91b9e6bc5f10ef200bdc8677b822d67c5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'bd401c9f6ef5ebfb968f3e30dcf33bbd132abb3093c42af95dc198a50adb6b53',
                'hash_atual' => '8facf767c352e98bf6bb816971a45ce17c39a9d038056ae1152db89c8111862e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '2273bca92059ddd4b6e354fcdf2f5eb2293914976d973ad05f3495fca01e7dc4',
                'hash_atual' => 'a6691d73bb55b5f9aae3949c7f6d34c663f42f041c9e8b1fcdef71e93abdb5b3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'fc3919cd9c63dcddf907e607c9027869c7127a76807195c9182e2db479d67e52',
                'hash_atual' => 'a461c815a38959d6c64b8c178719ba834b97a0e90dea98cf42f71799ff5e3352',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '2df52f823f0c8fb36866e0595932cd9b33c222bdd1128d76b83194e749fbcce8',
                'hash_atual' => '37db2c4ffc368da17540d1bafe74858b54b053f617ac13f67a9850cfe64406af',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'd0d4657510b1596338e9912560e9cf804fb54cf999a277a23d1a22a0b07dc47e',
                'hash_atual' => '6e94ee765d2ef2604373c94f90f9c207e904387582d4f04c0275bb59e6201e96',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '5b7e848a91457148d67634ba4e16f6bebf7c4746fa386ca792ecbdbbdb569148',
                'hash_atual' => 'b8fa17d64b0e9460782fea67c8840c7663c91c5306c362324e2755dd90b10678',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '5e8cf1c090353bcff47808e5c51f2f78e321a8d5b7a3c7a185be4a69fc9990ba',
                'hash_atual' => '833f6c3f7e806285653ad4ab9876db18d34d84af136cf48304d2ec97a2218ad5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'f19282b3ac9e693e4b166663463875fc610dced21f86533929005feea3d8df4f',
                'hash_atual' => 'fbed8749a21842493483bba063c95954711ab97aad962581efed6e6fcbb50e4d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '707ca54bac60207df5ee1adbc3ad962269776baf23ce3147039edc38bda2e38d',
                'hash_atual' => 'c299a2ce7fb8618c49bc3e7118269a0318377d5d3fe1db2b799e5634c2db6f2d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '0efa15e1bd83fee23dd16054e4f8078621cca8222c742ce456445c216045c0e9',
                'hash_atual' => 'eb2511a2e275161e5efa4fbb586bac5773a0f7be004f1ac97e6cd7f86281bf3e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'ce34b7c460a9e1daf9024f8ca9b40f5d4138d00f728f2b19143b794fa46baa5b',
                'hash_atual' => '0f8e1ff20c573ad3c632e8d1e92e4167d7d994593df1133948d7018709ae1b3b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '21f0f4d0ae692affbce2e13375867c75dcab0a9e8f477ef448d4cdc6d2730018',
                'hash_atual' => '014cc880f275dce96b1f8593de314867bf8ce4566174127f61f4d48e86ea060e',
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