<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-07 20:41:02
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
                'hash_anterior' => 'a8bb005254ec377e5b2fd5a6938b6b848d373d2fbb348b748335fa06966645b3',
                'hash_atual' => 'cd158bc4285f42b30e9978be644c6c0cbb07a6de70680e1396b03da6e5fbb6c3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '8e0182e01c8cd9d14cfb4333f08a3bc12e1148ad52db1a8f99e3b1a8e9ce1f39',
                'hash_atual' => '9b4fb3fbad995a2efea991e9e51dbd0efcb218cf2daf576c5606fd67acfd775c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'd0744310ea22081624648cbf090a33a569465decc5ea44d575c5d41877e8debe',
                'hash_atual' => 'b2aced3a8b3e332084c52bf697058a958773fee8a96eda7d6e8208755a818775',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '38dba4a4aeb2c00dae1ab0415868a301844734979a9ed16b524c00d6c3b52f5c',
                'hash_atual' => '868a72922baef8562013eb43cceda79cc928cb35f277d5cae7f0696a837f1ad2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '8bc8a3d805110f6a5971baeed549370aa226f8d53bf2b6dc3925e9d0806d166f',
                'hash_atual' => '009035723c63c0c621e2d977d178159433e9c8b9af760a4da463504f9f4f35ff',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '2087ff370bc614c41846fab29c6d33bba3745ae50bc1b6fbb6ee4968d9d90a78',
                'hash_atual' => 'eed00931e856a21066e2bbf4ad335b20f9eab33c36df44b81f3a07420bbe320a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '7af2f5067f81c6c8c27c71610b4e3604fb1121e1f0b91bec1512fc920ec1a160',
                'hash_atual' => 'ce6a864f3e97ad48195fd53b20d4faf64fffce4035731e3f8e5b3410bd39c376',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '79d710d474345279602036ee6eaae8cc326169214e3424f186a81f69d40f0ec2',
                'hash_atual' => '1ca6eec9d58e1ae3b1b1eee24d1d7d689c37fdbf3467c0ee4623d806f8387264',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '689e0efd4dff06a2fa6d91af568f0ff5e9ae95aaabcd4eafd3b48b9f72cc2070',
                'hash_atual' => '799b58242d05cfb1b71f8d0e20248202329d8a64dc390fe4cae274a509848d52',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'e49c52be3483bbd41ecb80ce301dfcc4ddd85c0aba3a25cac56585d921d855b1',
                'hash_atual' => 'd7ce11caaf454a9621c61faefb81bac9329e9f967ed2bf546dc1c4242cb694a7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '736cf6828d28ce31ab36cd8d5850d228026b2282b24074a22316bc5ab9a2ad61',
                'hash_atual' => '31fc34d56f2b51515c657210bb480784eb3cde2c3c0b5d22a854fa62e185a7bb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '85e2d861bc2deb97b0fc3307dd78043257fa36fafb450208a20d98d3f18d60cc',
                'hash_atual' => '1f4f100fae09a2800f6ae703ed1a22722480a0a93391fb0b3f355195d0453af9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '87f1068d9a95c576949ee4e5215c68a6d607a06bee94ac49c032464ffdac3e91',
                'hash_atual' => '0f6f1a1b18f1327c01086d7c43fa65278eb3a473457a70b2d55419c417271077',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '3d359cde3d6d8f6c41c15d823468eb0e7875aed8d84f5f06d82720bd09708123',
                'hash_atual' => 'feaecf3fc6364f6ead1f9597e36dff0cd6de67778b862e5e3840d035e4b6bb33',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '60c5b53b33fd3daf25e720fd5ffed730635e928d2c84bddb5382cfd0e33a6eed',
                'hash_atual' => 'e6f56935171dffd3105b8516ceed098ff228165fb951cac00e1b51b608aaca7c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'a5672b87f9bebf406719849dc8d4763a3e5b7a6ff8f33916960e14dc89ab41df',
                'hash_atual' => '1d557f90a4f1b63095bbe94dba480e145c9dce4ff4ce836b9958fc75d886aa3c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '1481496926b0805cc503df841c924cfe64f5eac88768484a81b42eaec162a475',
                'hash_atual' => '08cd474888ec65b67b809435720651cb89dfa3f517e11cf03cacc8ced621a9fa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'c2245008c8b1b020008e82f98f8c82aa5a04762c01b415ad4427f7d40c2ca3f2',
                'hash_atual' => '10a2656e7cf1b927a4388aaa241eb390c7c79d4a00b7f9443aceebdca96d6ce5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '13bc5142048a2b0b2b6eb9dbe3212f3f8dc7a728f28903ec7878cfb2003799dd',
                'hash_atual' => '1652097b0ab3f0c08e4c5f946507fb3fef7d9780f61413b9c7aeec5dbebefa6a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '07034845d3ca5053cd17eec0ce62cf16dce509784893a3b8da85a3abef0388d7',
                'hash_atual' => '1455e77d26b61263615a1079c2a17a1edcb7822a93289f6cf455e02b402a9d0e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'fa6ba5b07ecd8a1b1c672e7e779aff42a620b40d4104fa946686174877ec3901',
                'hash_atual' => '879c72c54558df75a39a342fd56c494488a6a0a7f7f5e306b03b94e772a86ad7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '38a12da968b7d91ac9aeab065d7c3703a24d1c36c599ad98c4fbeeb0322436d7',
                'hash_atual' => '8b1ba095fff8719b8f54a1e13ea688d9d224b1119c622212a3c7b21237adbe88',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '86b81cc09afbfc6c003ac0e4928e48504f1f449406ea116e502e7ac018a54483',
                'hash_atual' => 'e1483d2d2df425ff3f7df4beb870d8168ebad4792008f3c3ff8fb4ea93872097',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '32ab0a00bc1936482b7fd2f9dc5ddcf5cd2e14b69cbc8757d9def8de31569e4c',
                'hash_atual' => '17956324b59ee0180b8fd09300c44fc1527639b91ab5bb743b1e029f9ec2c10f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'e2912b74d8714cc830e694f74166be93003d9a9fd66c972723f2af4abf910fb2',
                'hash_atual' => '48b270af8c20dea96f65a17ad479497c5cd979f155af86f1ae587ebe72d4e363',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'fe95a8db8267d9de836e14c680c4700d45700e184d9076e75d98be7539ad70b7',
                'hash_atual' => 'bd03a4e468b2f12c22bca412a57592533919a54ab64b61d525504941f4141096',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'b28370961a47ccc36275b2ab5b5ab72e8581e0631ff8a6a4a18a3e3d5d2e497f',
                'hash_atual' => 'e93192eb4c22e7ad491e0e34c4746cd7b7c6d6c2848e3625c27c4bc1b1712b8f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'ae252db45abdf2a2794580f784463f14cf7c8dc43402f8a65426870e67472e96',
                'hash_atual' => '81ac913a6056f246f70b88e0d4330ba2aa7bf6810b5eb267b367c3983b5873ba',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '1bbe785c6b788132cc7e4b22bbc35f064f73acea55ef5976f08ab5421bf68e15',
                'hash_atual' => '5f3fe1f0ab84585e9cf946366668077be44a34108926f2b1a73cb61a215c5f30',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'f65badc4c5f2c4a82c1921eaa96f469800ddc693e64db19573574965dade8004',
                'hash_atual' => 'a7c10c41a2c9fd060354e040e08b4ea5412699ab150b15a4fd0e30380e664ae5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '3185d76f412afa922f462a2d2d615b648acbd739567808b7dde40f186487e2e5',
                'hash_atual' => 'e8bad376fd6f25095ac0db08e1f43b9d427210c9736f230236b2fc792b7088bd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '4bbda47097b899a2b8b4c7fe1b9ec7e3a8f0368fe1fa11a0b489d15485b3e217',
                'hash_atual' => '303d2ba96ab5379972eb1e0309f9646a2e24cef4220ab3888c443ed973166ef5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '9a05d24accf4cde92f9d97b4b09c52c8c0de966d0f2de896de7313d7fbbdde16',
                'hash_atual' => '51d6e45d250314c0f27ea29125bbe744100fd0280f656dfdb0f15b7cf23c7034',
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