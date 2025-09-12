<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 02:43:37
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
                'hash_anterior' => '875309c5cd4a2269f70c5b3913f4474c83972552a8782c2a3d7d6963e4c9a2b9',
                'hash_atual' => '897b1fbe978d60adcbe2531954f84fe9f5dbcf9a31ea95a40b7af45a171d465d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'c07e2f75a63a36a01418cfc2bb2bc79bed9837d1372bcfe9dc65d55b70ecdec5',
                'hash_atual' => '359b52ee36a858c6885738f6c325d0491731bb81a9d5cda22ddc392200f71333',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '5a95149b4f28fe21480b4e9bb9e4a71736cf2c12d40fbd9b64dd6e3b324e01c6',
                'hash_atual' => '3425b766e7b8cceb6fd2cbc8065d9266cc97dca54b3cfee28ee5872e3bd21070',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'ecf4900f7790f8375b993cb389118fe760109aaaca0c84c89cd69d44574362eb',
                'hash_atual' => '3e1ec88394c26ebfeb17851b9cadc1f260b6fcc23d45659eba59e21d688dfcc0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'faabca90acaf788b6e8a2468fbb6fd97ce53ec5566a30e999599f74e13d9ca6f',
                'hash_atual' => '16596b32227e50a3edc781873bb304d85b835bfcdce3a399df0dfeec1b1f4805',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '08bf43cf722200c3d211a888d7938d7d70a9d5ec504ea88a203155da5402765d',
                'hash_atual' => '990f12d11cebede2e2c12a63daa3c21c9f3ff0291d4d727678c9c4df0cfa638a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '03a9d256d741462a640600fc35d2703c5d9fb12ec7a7c7d1ef37377e7418fc5c',
                'hash_atual' => '56b2ea10d7bd4b0624e5e2c812ee8685e0542a4b6b34239837e493aa1cfdfe80',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'b6540d6ea9f05b4f0524813810b34b5046f3f4f0bad9cb5e71981a854f8517cc',
                'hash_atual' => '04e0970d95810aa1e6fe8acd58408ab505e1ef2e298dd236fb7766d1820e2f5b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '696897e7807ee2ed4b41cfd281665947414202b26743646f737c6173728b1710',
                'hash_atual' => 'd41710efdb10b4e87d08b609e04068f39d002a9d6ac50534b3ee87c8c5869f74',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'cc2e68146a2d03b8dc6eddbb3cf575c025a1554c471ad739e09b23ca395c3cd9',
                'hash_atual' => '5b82643aee574b21885103e3b14630b1cc70c9ccea8dae201f39dabc094a58c0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '2d07e37657463ec1766b8694298869d449f7292ccc3ec9ae603a033db2ebcb24',
                'hash_atual' => '353d65f8ff2f933408fa3bf5d7bcbe14ae073eebc290b681d82d0bf87843bd66',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '52a6a44b460f66233247daec712c221964a02e318a05665ce7cac82db2cac5b4',
                'hash_atual' => '2120fe3b70e61bface923b8ad121ded67810d43ac275fc25063155adf3f26fab',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '16ec8d5916f284e653a6dc8ae8aa7af430d5225ef46a41a25b00e650b28ee071',
                'hash_atual' => 'e8a77a4e94f30f24037e5528390f4c9269d8763ffa30f65f08d5af5db5383f6e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '5ff7c568ae486aecc0e5cee4adce671f87ea5cebded30b92965fa58d7542a90e',
                'hash_atual' => 'e101a78e93c6f5a958e70b8fb26f6ccf28311c53a4eb26c9edbad6477690052c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '8b8fb66ba8e1cc628bf10f6215e797070bfa654708aa7e3751eac4806c601413',
                'hash_atual' => 'e0c4389e166db81c2e9f4ed05d1de656d57706e6cd5a4da1ff68ddd69abbc27d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '71eb9eefed632c962bd94ace9c4009304ea7c659cb4b3371dbde13c9dad24dd3',
                'hash_atual' => 'be8cb29d7edb21386644d086be3a7f0fd7bc03e1e957df144196a419c4e95690',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '7d2ce56b1fef9d3be42e3a18d93641c9089bcf701a35e2429f306449aa4a45ff',
                'hash_atual' => '5ab64a75e6dfe982b3f47357f3ef2ece0e008663651e75a47b560542a302c00b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'b1d645d5bce9407be315922d58a8800154aa1191cdb84d62b5715ab3bebfd473',
                'hash_atual' => 'bdd4defaa6158f6f92265cebdb4c0073cb99d3a42ab3fe3a711d2a7ae33c05ca',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '34ec10f0a7bf8e1bf23841b48184a96771b9dbddbb002227e8337134e03586ef',
                'hash_atual' => 'a0c1072c0567f20e47f7f4845b686aae239df1b280f2d2597617cb6b2fdbce0a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '99808bba2be7720257194bc7f5750d2c5d1e24fa87a71b7ca2c733520a491065',
                'hash_atual' => '2fa8101e103da1342a6fbafb727367b32162916c0ded62f3fecc3dcce9a71148',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '588a99515b4cee74fdd409b1bfc9333f4274050e4898cb705d6c58b90ca9dd96',
                'hash_atual' => 'ad364d0bd851ca700bad5faa7911fefb6986e08cb64e707bc75dcb7f560a2833',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '5a02f39101a0797bccb7092f0371958971a12b1e2de19d071383d715eaabca32',
                'hash_atual' => '2e650e85eae93042a69655a6c40b0fa19517a388f7568f8b6d791b485b201f56',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '0cc6212f5ab2bc797e894ffcad1e476c3173f653337235497d7ca6a34c865406',
                'hash_atual' => 'ae38ea0fc438e9cd64258082d3f1d5147de1dfe29f24c6f16e3a3025cb1ea2c1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'a12b977cbb446397146ee27c958b4f3490de5b79565bba7076793f6b78ecf81c',
                'hash_atual' => '66882ff4c50aa1cce2e217ea6a290097b9b853da3fe0b25e4d35157357cf5734',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '641e051cfc14e78db63d795351fbcaf12918e5900dde0e5ffcb3a2a5452c3267',
                'hash_atual' => 'aaeeb40f4a6f6ac32253ab4e7d93d5785df72282899e161e1c926d60a4b7cf19',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '9505182c91fb47c76b211525395a3d4ab53ed19a09991b73ebd1aee36b59e875',
                'hash_atual' => 'f49cfd554e81538f0c3e0f991c86042b4f4699a55cee3bbe0f79ff93a2271ac0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '76c260adf6222090335ed549a4d9fe19233b0751b69a78d401d5e3b69b9e4a3f',
                'hash_atual' => '8f499fb10566e8e778c27cfc565acd9cb3412bba2fd83c403742170a39f82ade',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '4b5e6ae442a914f29b4460bea8b9e85f68dec350d9de45ccfcd380414ae67d08',
                'hash_atual' => '55eaa1b9b03788e0cbade94f5e78dd4e4ff6fd6a1225f952aec8016882f4272d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '6c629aed9b98d2dd0a6dc6483710861c292c1c3a5d7cb8716dc6487edd97e7eb',
                'hash_atual' => 'e3b4809bda1e45eaa7b0417e687c5ee8151012ec1b92bef1073e6b5b6cccc80a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '02b1b0d53e1694339ed7ada611165b2e14853da8d15b4ccc6fd5af3ab9da891a',
                'hash_atual' => 'b1873965f97e294a1ddda23f559a7a96a7a085251faeadd7214381e818c55aa9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '38043e3f06c83a5c39effe8f90583f9d907f13a8578aa2d70224aaed4958de88',
                'hash_atual' => '6a493ffc827f221f0c16be1ec20e56ba5ed13b31b81678d902816b4b0a6e0ad9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '42d671ed451418e3909ff3e62a7a3ba2ea272313ec38d27f575930cbb4cd7062',
                'hash_atual' => '40747db5ffdcbf4545596d087e6919f767b68a923596ea4c22fbdbdd20265ff1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '6c5806e7cf7e71364b536a3d3739c35d7aa521086b9d364e3898e0ab63831144',
                'hash_atual' => 'b7087fd890eb49b42ea66864fa906371bc13415bef27f783f62d40aa38e56741',
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