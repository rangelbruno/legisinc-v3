<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-11 00:50:16
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
                'hash_anterior' => '651604656ee6af4dfaea48b0e0f9746ee3ca33cfa0e4dc5bfc71c6a89689f42f',
                'hash_atual' => '359cc3a9b176de11d2ddea4f37010b0fb36ca756625f9fc584c7734c7e96339e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 185055,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '8d444df39e7acdb5078b8b3cfb902fb0b4b50eacf6c2d66b95ba3d57ef52897a',
                'hash_atual' => 'ba86b3eb945d9e11e2fd94f60aaa0c9c9c6597406f8b8cc664475b3d5881d6c5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33929,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '73f433080672290eda95397bbbfb7afea4172cb8e2f36ffc67df3f57d7533c93',
                'hash_atual' => '92462809fe4e1ae9d8643ca12dd92cfab654b29e937e049441900bdc1259a415',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '588acad86ef4bf1d3b1ee791ea06642ee0e96f1b4b70a7b64f62d6dfac0eba2b',
                'hash_atual' => '8de4dd28bd3dcf1f0806cd718c4562235331df4bde350d43e45df2a63aaf7157',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '48a2cff40323609d95ab3f0b3f8c47c039435bb0008115cc8db2e05b03186fe0',
                'hash_atual' => '7cf5802d9a8fcb286625eda3b29ec77d64c9822721cc596398aeede31db8d1b3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '903508db16d2643e3032dbb9782e5dae791b78279835d02433b2b64000b79420',
                'hash_atual' => '5ae37d64f1dbd2b29854eb245d0fff3d39a06363fb8f3adb60aa76434da1ce1c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'dc8c4004c59c464b1f9ceddf950d48a271ba6d90b316c984598c6ec2a20f2055',
                'hash_atual' => '8e8d565a4a897472d9cf6233849e6366af81c661aec945acef1c64e5747404f7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '93d5e65b9fc5620fdf5823bc723c64074b6cb87f43758783627a1ca6bebb51cf',
                'hash_atual' => '07faf7386368e22e9646373194d62b2cc239ec3de977b91dc1e2302c43e7803b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '15a11cf817ed77df761925b964bc23dd64690638d22a171065ae9189fc80713a',
                'hash_atual' => 'a435ebf8f34817c591847af3f80fabbeac34e0d023d0cfd103f2162fe9a64dcd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '95ba80f81b933a51b568d0447f632aaa739ce3c28b341735902a490417067b50',
                'hash_atual' => 'c3bd095ea0d5ad55f17af124fbb79d8a7258c61ae34cf4f15e6be3c055ae2800',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'be87beb829c5b1b397b285a8c701658c522137da554daa3612524be80993f6da',
                'hash_atual' => '6e4a119473212e34283f5f9ef5cc2f6e14737674408dc51e1c18a780b87be7b5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '56164cbbaf654b0d226d7203c226c41ec6d0325db78b7a5fe75d93338bcace8f',
                'hash_atual' => '825d829026a446991f70c7f57141e4960b63303007a3e7cca15b1c70120ae4a7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '0007c9799a64a508e3edcfd31241d30863253460c1186783fed93894bd9c9073',
                'hash_atual' => '40e1f075c39e96d95d499a2cf39ea814b5573527a7b2995242df79c32254ad62',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '63f8377789be48195507c307137929fa3b166d5ce2b055d125babe2cadb97e3a',
                'hash_atual' => 'fff551c48866820f2ea612ce271422946e1bc633b33a4196900d5f02dfe0fb53',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '3ed27204e1123996534de6307aafae5e190918a962b9696529d3e28a2adb2428',
                'hash_atual' => '36670a99f424978a7493b7d001dbe91f94257da9360766685d11efd076266b6f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'd7993559a85a152bb37206556d3ff4823048c5e5a4265e8af76632cd28af0a14',
                'hash_atual' => 'e7404306aa4481eaefc758d629ff73fda15405bf6c9971cdac2f2e6630dfbfe0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'd774b0664a8231c52144b75881338eccfc9d3434b7be7a7f0b4acfcdd5c72878',
                'hash_atual' => '95acd470f0935778940baf2fea195d4cb14e08f2f9604540188f769e577c8992',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'cc1dc4fb80538c1861fdf84fde8cd5078b105023005bf2ac59502499c1dfda80',
                'hash_atual' => 'e07e901d2fed1431e824e86ea89743161a0532b9c390104aa9d4c6af24ae3e89',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '8e48622aa76d327baa615fcddb7cd5639be020798e04544efdef6560adbbd490',
                'hash_atual' => '2a782641937be6e416590679711b52e06acfbfb5013816a452e47c396e11cf30',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'f627de0e9bc689eedf13168fb800fc5c28cdc1673e62b6051413f5ae539feb0b',
                'hash_atual' => '0e689f4afd3f8c80d2f7f6ddbb6a6c4150ec00d668c672142e75182e3188b59d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '3b60b1045605c9a5ff8399955b5425fee9f162970dfbe32f18710fa08c518c10',
                'hash_atual' => '6974520b287f790a28aa49f694adb8a17072a50bf85a57f22c02bd594558af96',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '432ee9b136a0d6286926daa805d7b4f4451c4fdc7a076ced8fedfae0fca76cfe',
                'hash_atual' => '0bc089b1094dbb331e3fe023325ea995dd3b475b0933dc141697ebd57ef31b77',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '117e3df945f45577e57bac642a07e27e1d05b62104097bb9295b78d7f43de9da',
                'hash_atual' => 'fa5128c2aefc26519589a18c2853184c17910b109fb61c6d1bd252fdafc76b54',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '04d3e899a6c9a15d3eb1bcd8166eedcc6e28d44405330e8ef3bbc8713ba6e53b',
                'hash_atual' => 'aadced91db8290a16c1258eab9bc6cb3f109e1626e0a4d86945e9f26ea7220ad',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'e3018da4053bf2e6414738e84265a74800b363ccda640623533b5ba986ff8854',
                'hash_atual' => '88617c586cb2409c68c84a45fe0c174635305c8a1f7e16dd323a438c506d6da2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '095d97572c1f336ae122e47b45a01bbae7724ecc7584f7454c12e8553332cc77',
                'hash_atual' => 'e3b41df0a575f4c52f17618d63dbb2f51122fe4a2a51215cf88c34a4637803e2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'a2a121de090f5b382731a5ed9ad39bbdffe166706d41f4dd7bdcec8e223f2fff',
                'hash_atual' => 'd9aab296193612c8c08040c1ed0f7c3eeb8ea7e330833632a1da11f543388911',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '8840b7bea5e0f65f0792b28d0775cc6b73075b504d56edbdb50b8a9442e4bae4',
                'hash_atual' => '26d05d5c0967da58f86db0b8b0279ea5fddb4d4646f618d03c08738b6a9dad04',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '62482814316c88b1ecca791f5982a57aa93373d29f442fd22f77319958bbe690',
                'hash_atual' => 'cadc1eb0fa35c4f063e0ecf6458ce8388c99174340782eadd6aa9d3112d81e0d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'd658ef10c644662514512e966b3af8926561ea8e5e0f32e9d28aea925f960e35',
                'hash_atual' => '0a88b734f97943d09ea8aabad3dd99ba2039e770d2106c4826ad7f8ab661516f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'be64052b463f2c9c510dab1279c7f1c02d0fdce3a4507be6ba4af7dc5e1ead54',
                'hash_atual' => 'e81ecdd9881459ed50ea96c23eec3535c77d2399f8fd0dcaa41c3c7bd2232976',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'f238be0865755b5c010934d07def000771adacc3e701c46c97310e48e3a94ab2',
                'hash_atual' => '5635bfd75c1eab56314ac803037ea12d376389f43b010b571bf22facf51a85aa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'a549a1ce69682c3ab39de7e070ce5a4498cc1fcaf49208fe22f7185b71aa1e60',
                'hash_atual' => '89382712d0d26d6b880639198fc1e4f0ecc0d89dc164b823146a89324b83b34e',
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