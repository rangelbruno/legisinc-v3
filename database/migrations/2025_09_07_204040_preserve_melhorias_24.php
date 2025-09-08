<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-07 20:40:40
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
                'hash_anterior' => 'b6c06049d6e5bdacfd6ae07d8de9bdcdc7e65ae8ff6ea710842bb808858db244',
                'hash_atual' => 'a8bb005254ec377e5b2fd5a6938b6b848d373d2fbb348b748335fa06966645b3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '241563747256c7f6139e5c591d2e1e60d463b6fdedd33ad9335fc3a728274c07',
                'hash_atual' => '8e0182e01c8cd9d14cfb4333f08a3bc12e1148ad52db1a8f99e3b1a8e9ce1f39',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '50a14501541d15e32a9a49888468ea7894d20ce877801832b099a31bb06fdc9d',
                'hash_atual' => 'd0744310ea22081624648cbf090a33a569465decc5ea44d575c5d41877e8debe',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'fa4fb631ae2205065ee379585a64aa39a9b72e319e6cabbec0aba8dc35f68767',
                'hash_atual' => '38dba4a4aeb2c00dae1ab0415868a301844734979a9ed16b524c00d6c3b52f5c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '918d979c30b2d1328063228075446b2cf8ab4885362c2f8c6756f0f204351f72',
                'hash_atual' => '8bc8a3d805110f6a5971baeed549370aa226f8d53bf2b6dc3925e9d0806d166f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'ca5747b7b727a0de87e1425942efa639b13efb0dcf0715f1c0699a092ef003a8',
                'hash_atual' => '2087ff370bc614c41846fab29c6d33bba3745ae50bc1b6fbb6ee4968d9d90a78',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'de237923fc78c15a18a494286a122ca3dd10e7e6e92c899ff924590dbcc0ef89',
                'hash_atual' => '7af2f5067f81c6c8c27c71610b4e3604fb1121e1f0b91bec1512fc920ec1a160',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'ca559a12c5d5544bb770c16f25eabf536ac4d0d1a1b7235a0af495c7d0e0a5da',
                'hash_atual' => '79d710d474345279602036ee6eaae8cc326169214e3424f186a81f69d40f0ec2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '5c5ded4df02527925a111bf3396a9965172851163dc212f277d241bda021df0c',
                'hash_atual' => '689e0efd4dff06a2fa6d91af568f0ff5e9ae95aaabcd4eafd3b48b9f72cc2070',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'a127930659fe7ac26cf1d06d494b83d806dd38389a5dcffa15fb455d182180c4',
                'hash_atual' => 'e49c52be3483bbd41ecb80ce301dfcc4ddd85c0aba3a25cac56585d921d855b1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '924383d7c345083a5767573b6497c4174d88561a266c3ad4d84893be8e114847',
                'hash_atual' => '736cf6828d28ce31ab36cd8d5850d228026b2282b24074a22316bc5ab9a2ad61',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '2499c3a804b6584b34fc8fcfeff993d1f0fe5ba58257fa32fbe376717f1c3bdd',
                'hash_atual' => '85e2d861bc2deb97b0fc3307dd78043257fa36fafb450208a20d98d3f18d60cc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'bd79aabe6842d8ab1d456e0433399fa8e2e57b27d376ec0346259486cf0f7b1d',
                'hash_atual' => '87f1068d9a95c576949ee4e5215c68a6d607a06bee94ac49c032464ffdac3e91',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '2e32dfe1b80635f4888706baf2306cd36fc6947142c1ff1783cab866e354ce19',
                'hash_atual' => '3d359cde3d6d8f6c41c15d823468eb0e7875aed8d84f5f06d82720bd09708123',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '64e6e4866d0d077f9c10aa0dbe1d6fbc0d816b4e27b8101dc83f47198a927637',
                'hash_atual' => '60c5b53b33fd3daf25e720fd5ffed730635e928d2c84bddb5382cfd0e33a6eed',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'db8bc8b10d651712034b7c45f2246c7f032f40d973be994cc12d30fd6e973079',
                'hash_atual' => 'a5672b87f9bebf406719849dc8d4763a3e5b7a6ff8f33916960e14dc89ab41df',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '13071e507272a82d59590c9378808a4b658bf333cc6ef64425fb61f39ea87369',
                'hash_atual' => '1481496926b0805cc503df841c924cfe64f5eac88768484a81b42eaec162a475',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '485b9322c626655733c4d9dd6021cff6d3c4cd22733d90fffc23b21c838ec2dd',
                'hash_atual' => 'c2245008c8b1b020008e82f98f8c82aa5a04762c01b415ad4427f7d40c2ca3f2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '954706c1538266c3a59f09c3d06de5c599b1803781019a8d848718623b33c900',
                'hash_atual' => '13bc5142048a2b0b2b6eb9dbe3212f3f8dc7a728f28903ec7878cfb2003799dd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '2a3576686ad8c38f10dbc3e889bce7a539f6b5284d42635a2ca68e17e6b48d43',
                'hash_atual' => '07034845d3ca5053cd17eec0ce62cf16dce509784893a3b8da85a3abef0388d7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'eaa83dee66d55d35fb5109e21408353bd636d4728aefef4c2e0eee27e81a4c28',
                'hash_atual' => 'fa6ba5b07ecd8a1b1c672e7e779aff42a620b40d4104fa946686174877ec3901',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '33b13aad6771e84ac010fb328fc00ee366805ce0df8e5e7a6686d79a674b1701',
                'hash_atual' => '38a12da968b7d91ac9aeab065d7c3703a24d1c36c599ad98c4fbeeb0322436d7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '7fc2f375fad298350eb8742168b12226248519a092f1e258d3d0cad5f8e2299a',
                'hash_atual' => '86b81cc09afbfc6c003ac0e4928e48504f1f449406ea116e502e7ac018a54483',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '8ea4f98ade3ed64b861e61b5a08d47e62e3c2f23556e6001ffd9d1e7ddbbd1c7',
                'hash_atual' => '32ab0a00bc1936482b7fd2f9dc5ddcf5cd2e14b69cbc8757d9def8de31569e4c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '4e082ecc45ecfa00b17845a499ed620e93202ca542f81b414a5312135f69642b',
                'hash_atual' => 'e2912b74d8714cc830e694f74166be93003d9a9fd66c972723f2af4abf910fb2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'c5f61ffaa1eb037e282fc9e14fc57770663bbf8635b9b710d592cdcbf47c2184',
                'hash_atual' => 'fe95a8db8267d9de836e14c680c4700d45700e184d9076e75d98be7539ad70b7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'ce2b2f3b45e4d16984daae5c6d9038d2c7fea2bf33093b52e6c6998b1e41962b',
                'hash_atual' => 'b28370961a47ccc36275b2ab5b5ab72e8581e0631ff8a6a4a18a3e3d5d2e497f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'c012f4f1f048d12110ebfcc8f1859530cc7357e0756eb4df98376b42bbb47af0',
                'hash_atual' => 'ae252db45abdf2a2794580f784463f14cf7c8dc43402f8a65426870e67472e96',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '69b3159ee403d080f0d4f6f7409568ee2ceb742422afae84bee618ab7eabcf07',
                'hash_atual' => '1bbe785c6b788132cc7e4b22bbc35f064f73acea55ef5976f08ab5421bf68e15',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'b3dda00b97509c0fa416dddb582d95a028ffb2c590a271d0eed0afc6fab9337e',
                'hash_atual' => 'f65badc4c5f2c4a82c1921eaa96f469800ddc693e64db19573574965dade8004',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '5f83ec40f03a154f926031232253e36ed1fb2f636a369698d27c8a59f32b2ca2',
                'hash_atual' => '3185d76f412afa922f462a2d2d615b648acbd739567808b7dde40f186487e2e5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '3251f6ea8f9988b39d54ba88243d51c060927487474cfacb5794142f7a49aaef',
                'hash_atual' => '4bbda47097b899a2b8b4c7fe1b9ec7e3a8f0368fe1fa11a0b489d15485b3e217',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '41db08c7e0f7182da2fe868c23b1bd64ed906148d7218eed20acabd6ad3d7820',
                'hash_atual' => '9a05d24accf4cde92f9d97b4b09c52c8c0de966d0f2de896de7313d7fbbdde16',
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