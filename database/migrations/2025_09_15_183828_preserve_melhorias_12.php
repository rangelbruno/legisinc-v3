<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-15 18:38:28
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
                'hash_anterior' => '9316041a35306c96eed4090ded435e232e8bb33ca842f5ab3a3d33a0bc7eff6a',
                'hash_atual' => '3b2337722b9967f0adbb503e8bd4bb36c4be7ea3002b1efba23dd60430672eb5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194760,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '5daf4399be09dc02d7a885a3c5fbfac51c48676e529129e9d2c8db89bacf2436',
                'hash_atual' => 'cc99a8495ab884bb91fe13f42c34f8d02a1e462f006676ac66f8b2995abd7fa1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '506a706fdb2c0c127497aeaedaded38f95daeef24bf6580e37e714dcc309457d',
                'hash_atual' => '44ec9b60565dab6b14b430e2c5e434c84a97e1ec1cbd617543804fc396b6ed1b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '03d5c085fdd7df77f7fb4593326116a7d47c4fee7c456ee0ca4f4ea3e8a3c1f7',
                'hash_atual' => '0c373ea56613a25a83d480ae1ed230566989f8cd51b8f09d8a91bd57525d3b7f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'f29789bdba2cd8d829c84a68fe1682c75be2bbe0a93ea9556001537642789d7c',
                'hash_atual' => '89e852eff5c36e66c76d36989532e1801306fc80cad276877aecabf5b45be3fb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '0b9058b0582bc0b5e821c8188098cb6c2c496017fb62017364ef8be721c58ed5',
                'hash_atual' => '13b9ea4b42a97e1d6970d73bea3863f84d21f6867f83ab5fcccf8ad0a6abe559',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'a8882a7fcd2590d34c6efc6502b12295cc94612ddff2624dd0bc659c99e5889e',
                'hash_atual' => 'a242bd873016db99678910eb02e5dcd5079adef2854db213161b99b091678dc7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '157b0ecc1f8d3f813b9cf977083f10a297c051f981ec55f12fe38740c5de5a1f',
                'hash_atual' => '3fbaaeffbefa0f2ea661ad207e29f761352e62a78cd95ac20089117d359d86b8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '8b6a4e584a7a82caa73fb4ec82483afd6c4ae376ddda041e2a3ff3f7b2e4809f',
                'hash_atual' => 'aa5c0446af4548be712cdb91ae9b51de7e3b8468b1a6aab58405e712ca74ea8c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'fb70659bbe60288759428a999fc54b0796b1325dfd632ee51d1bab324a8b30e1',
                'hash_atual' => '6167fde52f8d1f112feb9a5c352051877b2dece240db9b227cb962db6ce9a89e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '9d748c9466e74a60510985dfefadd9920c77af28ca43031f59314507ce219914',
                'hash_atual' => '6ce44cd39f23b7e0682ae1930c8e20a00156a74b539ad3c8fa58f051d28832a4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '27ea087c656cf796658f4a1444fb59b25673223274ad8f1596b0515304123409',
                'hash_atual' => '457921870dbbca05d4d2b963220ad5a929101e64e3b7719e924a353aeada22b5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'b1319d7cdd172133ae5b96ff80fd16c2b51f49372400fd0ff14bff498091adc5',
                'hash_atual' => '01ca64a948b32964b51fc609760c5fe05abf25dd8a5c52e56c5306b90aeb12d7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '0138a975c71a72ae82b429493d62e4beb57aeded5f5b0c084971e92fa1560bda',
                'hash_atual' => 'ac3c092291ef5f3b5643423c9c35237d8514fe98b8da91281e4d0155b1802917',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'd599aad235eca48935abdeb6f3cffd25ce24d91024f7477102257f54884d3327',
                'hash_atual' => '45027e53dd806d01e7b5e398b261528d7b03ca8d2b8b338b28ed549bd6443394',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'ff92c2919c6aa1027c76accb47090121667394072d0debfc4e4d6c8688b0db8a',
                'hash_atual' => '01670b3200c1a093b91c4cddd575e3aef597590b52a0a9dba7c99a9082fe0a21',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '6681fa31af8e1c74bf284ffce8b18674820d37603719b0c474e291078c213afd',
                'hash_atual' => 'e66d4273f6a008d91e2d1c3c2d22b112a1830677410e0e379334b0ce915fe4be',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '07ec2fbf1af108b5acc3c875868889be79dd32f4a755d33ebb2a586fd7488ded',
                'hash_atual' => '88e9f280982f7933d1b6f40ea5cc5a11d4242a3b980256be3fdd836dae6a5edd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '9fb3abf06288b7e8aa9b7af5712d12248b22d9428da0a5876712b3190d81d82b',
                'hash_atual' => '838523382b0f440c4f08712062e6e641b00edc97a43e02f231743e499c60fe23',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '138804c3da15a96e96ecd92ddcc15de1023e390b3268b6a16a49cc6b38c488fb',
                'hash_atual' => 'cc1539aecbb1c20160b757fbb28cc12b74dd55e9d258f57b6b8a59d86dd0a52a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '7236315bb755a6986e9a36b6cd9230e2620880f754c95fdb6340c40d3864e56f',
                'hash_atual' => '04f6843b77cecbb25219de19b4169da42937c4951486fa375c8b8734bc35aec0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '08aa53306eae6a6e7c702fc3462f74b64626c1964c7361e258733bb1063c72e9',
                'hash_atual' => 'ef0fdfb6adc479bfa2920d0d4c1d1de0e45b7b3a20a9c11e56917cbc24ba0b47',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '12a99d73eb782d3f2de4658e73a8d567177302ddf1e1c95eaaed5fd7743e54f6',
                'hash_atual' => '930d9a47d580b4b036b41083378d7849184f9e0b0695980375e5742a7e065968',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '519ad2cd27fdece2dab829643b942101561c4b8508bf6e2fd4db645f7da5a81f',
                'hash_atual' => '06a1a0d5eceb53716919ab29b6db91e1381b2e3bb4231fe63dd3e4e439c99034',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '34c894348962ac6eb791a4cb81304bec0408409f42c78c17606e28e0dfac00f3',
                'hash_atual' => '014cafe0b4fc87f6d6b48918b9cadc61d002b93867e2a60327f8d4e27259de30',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'ed8696715ef8d0702e1614ce37174947e05a38b093b02e50e903384397849dde',
                'hash_atual' => '47c1ea1a3c545646bdffc39e622a1fdc66b62fb1af51b68c8d6775c4f54a5467',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '288d94b5c0986e44077d3802830c80bd6f0ab6fec0fd4cb96b410d5b983e2c74',
                'hash_atual' => '7453055c5562388c626992fde2ac008d8d0ae00170fddee6ae65ea0465db18ff',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '21f72c4222c0441d353870a74d7f20a9f2556dfbf9b8453f00a5b13bdfc7053e',
                'hash_atual' => 'f304480727c8c8709a40cb16bec4eff0436286fd0fc73b0334628eb7cf6ad2ef',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'c43af1711ef2499b9868e391386650ef26a5341b2187fa8a1cbb4f8f42d0b728',
                'hash_atual' => '4b9ca6087bad4aa0f0d2e7b6598867857424a3ccd76f954f4f891984b9b86d8f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'bbef4e9710a6fa97cc36ce19b3fa54653804ad05d5a9031a96cb52a100541b92',
                'hash_atual' => 'd7218c7ea91dbdb9141eb5266c13ea801165306c5dfdfe4539f5cfec306f03ab',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '604b1cc453c7bdc51b245c7716b62343146318ed1e0ff1f335a441b96b241d71',
                'hash_atual' => '85b32bbdd3e366137e27bdc2ce1b84ea283da98c7d25640a8a4f7a7f7f457285',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'eb087af885f44c386b28f6f817b403fedb55035b8f13a43bae0f3e90ef23043f',
                'hash_atual' => 'fe27131bcc9162b21e797f359e92eae17923eada1631f0f2479079803b862aae',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '520191908693600c87eec17dc4334e3314b20af37e017d0e927451c094156ef0',
                'hash_atual' => '7873853e9bcb57937316e0fcfbcbc2089f7cd82ab6e3755ad576274a94beb9ec',
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