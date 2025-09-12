<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 02:45:33
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
                'hash_anterior' => '897b1fbe978d60adcbe2531954f84fe9f5dbcf9a31ea95a40b7af45a171d465d',
                'hash_atual' => '3946f362d1094c90e99b7a6d171fa36e63b7b27ce416387aefd683586a9be48b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '359b52ee36a858c6885738f6c325d0491731bb81a9d5cda22ddc392200f71333',
                'hash_atual' => '72b0334372c06296d25194e28393a6cb8df88e5bd7d8060bac0977c9960d94c6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '3425b766e7b8cceb6fd2cbc8065d9266cc97dca54b3cfee28ee5872e3bd21070',
                'hash_atual' => '41274f7cc269065e17b8792e94e6acc11dec1ddc623f074fc3ab3f5397e112a6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '3e1ec88394c26ebfeb17851b9cadc1f260b6fcc23d45659eba59e21d688dfcc0',
                'hash_atual' => '7da021a4fcd4396359f645116e88e494ccc532661c44ccedc0f11801de7f2803',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '16596b32227e50a3edc781873bb304d85b835bfcdce3a399df0dfeec1b1f4805',
                'hash_atual' => '5f8d06ac08377b9f6dbbfdb4e5bacf437bdc994aa05e1156782965b1cab955b9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '990f12d11cebede2e2c12a63daa3c21c9f3ff0291d4d727678c9c4df0cfa638a',
                'hash_atual' => 'ddf1a527839e4ad308cc803b139464ae0270635a2b306bc469d9aa6f93e4e124',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '56b2ea10d7bd4b0624e5e2c812ee8685e0542a4b6b34239837e493aa1cfdfe80',
                'hash_atual' => '5815728f64b0070772e5a2fd0914712fa0c1e9a2a253d85909a3717329e02aa5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '04e0970d95810aa1e6fe8acd58408ab505e1ef2e298dd236fb7766d1820e2f5b',
                'hash_atual' => '0e7a6715ac22e15f41d7dba4c60ee82db5dc0ccd00b3689b89f908aed6f11a0c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'd41710efdb10b4e87d08b609e04068f39d002a9d6ac50534b3ee87c8c5869f74',
                'hash_atual' => '22e2208a33756205218fa8fd63d54ad0585050fa1e8440bdec7da9a185c5461c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '5b82643aee574b21885103e3b14630b1cc70c9ccea8dae201f39dabc094a58c0',
                'hash_atual' => '549ffbc995010439b4778621efe2b5719dee4b8334054cb2868372265caacc04',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '353d65f8ff2f933408fa3bf5d7bcbe14ae073eebc290b681d82d0bf87843bd66',
                'hash_atual' => '941c32a5dc743ee85a935ec10909932d63aadd3d0b447c786fde082a476af62e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '2120fe3b70e61bface923b8ad121ded67810d43ac275fc25063155adf3f26fab',
                'hash_atual' => '533de234903b10442a55531ca462efa562db29f01ec41d8fe6e1ae0756c3cc66',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'e8a77a4e94f30f24037e5528390f4c9269d8763ffa30f65f08d5af5db5383f6e',
                'hash_atual' => 'ca3327b5a7d62a89746638571c3e11eb7ffb3bfd5d0b6eedb9432201e204741e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'e101a78e93c6f5a958e70b8fb26f6ccf28311c53a4eb26c9edbad6477690052c',
                'hash_atual' => 'f3bf6405d3a3eca11a9b3efbfd3c43d465574eb6bc18fcd623b9f0bed66745c1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'e0c4389e166db81c2e9f4ed05d1de656d57706e6cd5a4da1ff68ddd69abbc27d',
                'hash_atual' => 'ded539ebb3a87a8d61b6687a52412ef596dac8e88d8867160fc6b00dda5c7482',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'be8cb29d7edb21386644d086be3a7f0fd7bc03e1e957df144196a419c4e95690',
                'hash_atual' => '90c8cb56ade34f8f3b0ff807167c6506d5f65f61392320b6792faee8fdafc2dd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '5ab64a75e6dfe982b3f47357f3ef2ece0e008663651e75a47b560542a302c00b',
                'hash_atual' => '5d5bc5c2be3967f7d1105c8cb4f72c3b4cd2bad0790f8243a769a322faa2c441',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'bdd4defaa6158f6f92265cebdb4c0073cb99d3a42ab3fe3a711d2a7ae33c05ca',
                'hash_atual' => '671dc45da4cdb62f04b8a9bb9a51165e010cd558c83ffeb76a07081dfcf7553b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'a0c1072c0567f20e47f7f4845b686aae239df1b280f2d2597617cb6b2fdbce0a',
                'hash_atual' => 'dba17d5964eb434dfdd99cee81f8c9a0dc889fbc2c54ee7d98b2a370ce8c788c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '2fa8101e103da1342a6fbafb727367b32162916c0ded62f3fecc3dcce9a71148',
                'hash_atual' => '4909b4d4ee8caeb2be74132c7fb326eb4cb578641869105a8e12d1e0642c6f68',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'ad364d0bd851ca700bad5faa7911fefb6986e08cb64e707bc75dcb7f560a2833',
                'hash_atual' => '3eeb9ddc55fa7d261c7b1d687e14d856e160e1ea1434a09c5120142b3db9d765',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '2e650e85eae93042a69655a6c40b0fa19517a388f7568f8b6d791b485b201f56',
                'hash_atual' => '1c7e2312007c7effb35a256fa2c6d20a0cdc109b9f87c12528fad8a394741213',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'ae38ea0fc438e9cd64258082d3f1d5147de1dfe29f24c6f16e3a3025cb1ea2c1',
                'hash_atual' => '3cb46c255a95674ff6da9f2dcc9788f555b3607a078e2f4096a60d440f6e1d0b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '66882ff4c50aa1cce2e217ea6a290097b9b853da3fe0b25e4d35157357cf5734',
                'hash_atual' => '8db442de77d8d7af9a35dff1a3c9f649123c30c0f1a179ed23340937c96d879b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'aaeeb40f4a6f6ac32253ab4e7d93d5785df72282899e161e1c926d60a4b7cf19',
                'hash_atual' => '53db61f628fc198fa2572a60ed27e954f893d085df7b0ddcee57be6b7eb63f2c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'f49cfd554e81538f0c3e0f991c86042b4f4699a55cee3bbe0f79ff93a2271ac0',
                'hash_atual' => '3f691a09f619af211be6c5362d32af7dfd876ff6448b58294aba6877e148b5f8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '8f499fb10566e8e778c27cfc565acd9cb3412bba2fd83c403742170a39f82ade',
                'hash_atual' => 'f4b4bd6ff2e938c77d257e3eb1a1e4737bd336afe73d4b167349e3bc7f67d43d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '55eaa1b9b03788e0cbade94f5e78dd4e4ff6fd6a1225f952aec8016882f4272d',
                'hash_atual' => '9e53fd5b99a774c878f4fc3ad44e5653f617519d71278a425bd97264be45ebfe',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'e3b4809bda1e45eaa7b0417e687c5ee8151012ec1b92bef1073e6b5b6cccc80a',
                'hash_atual' => 'c72237dac05e99236a09c4b7082a410465e044456306da1f508ec82e3ccd2f2e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'b1873965f97e294a1ddda23f559a7a96a7a085251faeadd7214381e818c55aa9',
                'hash_atual' => 'be49a74f114ff75d8f2510ea6b058cf8178021d5b930d84fc11053752f198d7b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '6a493ffc827f221f0c16be1ec20e56ba5ed13b31b81678d902816b4b0a6e0ad9',
                'hash_atual' => 'e02d1ee89a5ac276948493ed62f8a1cd9ceb9bbcf5bc336ad535f8182239ff9a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '40747db5ffdcbf4545596d087e6919f767b68a923596ea4c22fbdbdd20265ff1',
                'hash_atual' => 'd625dd6970e286f11b400a7720f97487edcb39be6a0c77fe3f3b86265dd3332f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'b7087fd890eb49b42ea66864fa906371bc13415bef27f783f62d40aa38e56741',
                'hash_atual' => '90a2291f551ece9653e0547a5c820f3f768539d878bca3e73586310f62ebbf7e',
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