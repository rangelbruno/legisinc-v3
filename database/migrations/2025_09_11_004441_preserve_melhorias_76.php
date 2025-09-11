<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-11 00:44:41
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
                'hash_anterior' => '83b517499b81c82ab245db0a722a8750446e87418e8d7e10f5f69975308f19b9',
                'hash_atual' => '651604656ee6af4dfaea48b0e0f9746ee3ca33cfa0e4dc5bfc71c6a89689f42f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 185055,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '61b832694a53c1ca68b39ac4eb698ea981272be661ec37d101ffef5eb2d77850',
                'hash_atual' => '8d444df39e7acdb5078b8b3cfb902fb0b4b50eacf6c2d66b95ba3d57ef52897a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33929,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'a6a2b97adbe55006d0466313b8635e7dafe58cb336e061b37f29bd9e8d7c544f',
                'hash_atual' => '73f433080672290eda95397bbbfb7afea4172cb8e2f36ffc67df3f57d7533c93',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'fa5a2a5be7642756345e89907f8f2fbc9a65c98115e9c2d3723d66e0ee3d3238',
                'hash_atual' => '588acad86ef4bf1d3b1ee791ea06642ee0e96f1b4b70a7b64f62d6dfac0eba2b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '2c11ccad861508456d7adb6fc37c81b44dbf3c203d7507952722f0e746beadf3',
                'hash_atual' => '48a2cff40323609d95ab3f0b3f8c47c039435bb0008115cc8db2e05b03186fe0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '9e449680d56faf88e020947228a87bb28f22763d6f3958305b55156bdfa3b907',
                'hash_atual' => '903508db16d2643e3032dbb9782e5dae791b78279835d02433b2b64000b79420',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '2573f7cbacb1954101e22309dc2d8459bf642fe8982d1bbde8313f5821cc4090',
                'hash_atual' => 'dc8c4004c59c464b1f9ceddf950d48a271ba6d90b316c984598c6ec2a20f2055',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '37ef29116b27ba77090fd0d429441df9a044255f6510446a473e9e65383e5bee',
                'hash_atual' => '93d5e65b9fc5620fdf5823bc723c64074b6cb87f43758783627a1ca6bebb51cf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '4ab213f23103a1775b7cf6178b56a49519d121eb51814a95a88221681070687f',
                'hash_atual' => '15a11cf817ed77df761925b964bc23dd64690638d22a171065ae9189fc80713a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'a512731e9a2e990e9604bb31770b134c95c33d4d1a094fc65e8e14f4efc1d3b1',
                'hash_atual' => '95ba80f81b933a51b568d0447f632aaa739ce3c28b341735902a490417067b50',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '764bbfeb10d6168523a6351085635d25753e854e75c92c7d30d83cd820b6525b',
                'hash_atual' => 'be87beb829c5b1b397b285a8c701658c522137da554daa3612524be80993f6da',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '1e8f5f14ee6b2837d8cf01d93df177d71eed9800d7c1bd2c29ee000b6fb49177',
                'hash_atual' => '56164cbbaf654b0d226d7203c226c41ec6d0325db78b7a5fe75d93338bcace8f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '121f0d54d4480c7bb9f1b766f9042847f20648eebfaf13a20a256da3c008e8b3',
                'hash_atual' => '0007c9799a64a508e3edcfd31241d30863253460c1186783fed93894bd9c9073',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'ee1f1c3d78116c9885122a04402e216393ab54eef4ba87477c3ba43446a9952e',
                'hash_atual' => '63f8377789be48195507c307137929fa3b166d5ce2b055d125babe2cadb97e3a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'd1f6176808866cec1aef54f9083ce287e267eb8aac46a6ca27a3e3be002b07d3',
                'hash_atual' => '3ed27204e1123996534de6307aafae5e190918a962b9696529d3e28a2adb2428',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '1047c884208961dba47d4e4dbf90820db8daa8153e68da05f72fea826a88c318',
                'hash_atual' => 'd7993559a85a152bb37206556d3ff4823048c5e5a4265e8af76632cd28af0a14',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '06ded980865e588e1526acff202ad6a7337faabe26e9beb171ed2f0103138382',
                'hash_atual' => 'd774b0664a8231c52144b75881338eccfc9d3434b7be7a7f0b4acfcdd5c72878',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '30d9f9420ea52020db954b7c26b45217b050fbcd6e21d98758cc10f58b014748',
                'hash_atual' => 'cc1dc4fb80538c1861fdf84fde8cd5078b105023005bf2ac59502499c1dfda80',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '7c0a7211dfee1941fd7e50ff9f0ad267b6a3e8ec712eba9a33342dbcc8891194',
                'hash_atual' => '8e48622aa76d327baa615fcddb7cd5639be020798e04544efdef6560adbbd490',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '3dae2a5319f2c30a2b4c24866ae39c4e26602b3bbbba47ec8fa0c8514ff64616',
                'hash_atual' => 'f627de0e9bc689eedf13168fb800fc5c28cdc1673e62b6051413f5ae539feb0b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '8e397417ca7e8f6e861919c5efa831fbeb5e566a380261078a798b82e1fad743',
                'hash_atual' => '3b60b1045605c9a5ff8399955b5425fee9f162970dfbe32f18710fa08c518c10',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'c6044de9a3ea10b786a0fd91ce89a908aa830becc4202eb03a4e9f5331da12c7',
                'hash_atual' => '432ee9b136a0d6286926daa805d7b4f4451c4fdc7a076ced8fedfae0fca76cfe',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '89161cb709aea21467c5b07bcb1e0a00b356173d59b4a06b1bb7154ea8a43f3a',
                'hash_atual' => '117e3df945f45577e57bac642a07e27e1d05b62104097bb9295b78d7f43de9da',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '2975bd895a7ef464a68795879a4d84a5e0e85301d162fe660aeb8032c58ee6f4',
                'hash_atual' => '04d3e899a6c9a15d3eb1bcd8166eedcc6e28d44405330e8ef3bbc8713ba6e53b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '842116876863b6bc5c4113695dc4444b3f4bd876d5cb913f96fa68a0feb38788',
                'hash_atual' => 'e3018da4053bf2e6414738e84265a74800b363ccda640623533b5ba986ff8854',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '3898e5f1093b70b4c1c5a965dc51e21c6bc4f591af2a9dffb1fa25c40ba7ca22',
                'hash_atual' => '095d97572c1f336ae122e47b45a01bbae7724ecc7584f7454c12e8553332cc77',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '89b4ad67bd970f7517dae914f187f2e2ca8f8961e5d1add91b95b156148894bd',
                'hash_atual' => 'a2a121de090f5b382731a5ed9ad39bbdffe166706d41f4dd7bdcec8e223f2fff',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '35b1504b4de51fdffb762a48ee72a80ea6f8bb88d49c3e0ebb0effa751bc871e',
                'hash_atual' => '8840b7bea5e0f65f0792b28d0775cc6b73075b504d56edbdb50b8a9442e4bae4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'f9b26a67027ee8899d2fddd7521449274d644bdc29f1d9de4472502dfbaa0c8a',
                'hash_atual' => '62482814316c88b1ecca791f5982a57aa93373d29f442fd22f77319958bbe690',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '30d2a820da3bbdb4b24ad95683fef846bd2677d4cd94853d7384f2fa4893f7d0',
                'hash_atual' => 'd658ef10c644662514512e966b3af8926561ea8e5e0f32e9d28aea925f960e35',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'aa3365a801545c06d99c3e422abd345b2b94a4e3b173d1fc5fae7b11bbcc34cf',
                'hash_atual' => 'be64052b463f2c9c510dab1279c7f1c02d0fdce3a4507be6ba4af7dc5e1ead54',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '0ca55dad7f3f80ed7ae3f0214daa26d0d8a6508df2d1b6e28230b4cc0323a1b2',
                'hash_atual' => 'f238be0865755b5c010934d07def000771adacc3e701c46c97310e48e3a94ab2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '32b97c1345c9db05c48766f5112ae480e65315e53a26087e66b3384389c8f3d8',
                'hash_atual' => 'a549a1ce69682c3ab39de7e070ce5a4498cc1fcaf49208fe22f7185b71aa1e60',
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