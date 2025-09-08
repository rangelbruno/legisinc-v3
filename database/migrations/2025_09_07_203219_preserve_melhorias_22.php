<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-07 20:32:19
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
                'hash_anterior' => '9407a9cf05611a3003d16769af9dd4d21467bdbee1fe48ebb07c70e2c636374c',
                'hash_atual' => 'b6c06049d6e5bdacfd6ae07d8de9bdcdc7e65ae8ff6ea710842bb808858db244',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '248bb7b50898be360ea079c89aed546b4a2f278c8d002770d48768b8a46fa775',
                'hash_atual' => '241563747256c7f6139e5c591d2e1e60d463b6fdedd33ad9335fc3a728274c07',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '7f2c5699c34ac2d51ca75dd278c8c8c229816bd1d31154c33cab38de16ddfea5',
                'hash_atual' => '50a14501541d15e32a9a49888468ea7894d20ce877801832b099a31bb06fdc9d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '09a522438b633398010b49b2a20a4ad18f0bdaafed1532b77e8c3334fd8ef02e',
                'hash_atual' => 'fa4fb631ae2205065ee379585a64aa39a9b72e319e6cabbec0aba8dc35f68767',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '051147b89dc08590eedf4a6330a454c902a72898cf11beff365ced3024b3fdf9',
                'hash_atual' => '918d979c30b2d1328063228075446b2cf8ab4885362c2f8c6756f0f204351f72',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '4b02fb0a5421b30ef1e448dc6ab1487be6573c9a14f39df90ff8cc7b1520bcaf',
                'hash_atual' => 'ca5747b7b727a0de87e1425942efa639b13efb0dcf0715f1c0699a092ef003a8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '634eac763f609393a8421b22541b2347beacd2c4d9303d130c397d010e64aa2b',
                'hash_atual' => 'de237923fc78c15a18a494286a122ca3dd10e7e6e92c899ff924590dbcc0ef89',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'b37bfca080c1c455b0b6fea280a09118f4b73e80a2d00b9105b95c800931fa43',
                'hash_atual' => 'ca559a12c5d5544bb770c16f25eabf536ac4d0d1a1b7235a0af495c7d0e0a5da',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '7d53d22a71a3e4cda0a1541c1998e9562c64fd72ccff9939a4bb164be8055020',
                'hash_atual' => '5c5ded4df02527925a111bf3396a9965172851163dc212f277d241bda021df0c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'c15e503af859d389c8978ce8008a8138730d59c4822026e13dc1fa491917e44d',
                'hash_atual' => 'a127930659fe7ac26cf1d06d494b83d806dd38389a5dcffa15fb455d182180c4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'bcda99f230c2e768f93baad2b7c789b47fe9f952c3c640d5366f61f9c010ea97',
                'hash_atual' => '924383d7c345083a5767573b6497c4174d88561a266c3ad4d84893be8e114847',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'b8d8366b538d0a57287ddc0fbd97039abf804279d96134b8fdc3a082540e58f4',
                'hash_atual' => '2499c3a804b6584b34fc8fcfeff993d1f0fe5ba58257fa32fbe376717f1c3bdd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '80467e1f4155c4584d657bdbf440f46cb5cccf8e4d3e0467a717ebe4b8768aad',
                'hash_atual' => 'bd79aabe6842d8ab1d456e0433399fa8e2e57b27d376ec0346259486cf0f7b1d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'fc48cc5ddd0ba31e297ace6e26c6d8be3fa5db4898c567a6d50d47660564b650',
                'hash_atual' => '2e32dfe1b80635f4888706baf2306cd36fc6947142c1ff1783cab866e354ce19',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '4201c45d83dbe8a0a1233df8b672e7e13b2c68f1686ac8e809a7b7c2098a4469',
                'hash_atual' => '64e6e4866d0d077f9c10aa0dbe1d6fbc0d816b4e27b8101dc83f47198a927637',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '965a2d891915d4b9e9aea14950d59fc8105bf02ca115c36a9aad024cdaedca95',
                'hash_atual' => 'db8bc8b10d651712034b7c45f2246c7f032f40d973be994cc12d30fd6e973079',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'ab640825a3526578bb33c77bc136676ba530a69ebb760cf28d0bf36346d8ad89',
                'hash_atual' => '13071e507272a82d59590c9378808a4b658bf333cc6ef64425fb61f39ea87369',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'd47c2d67235db3f53b66e80e9ffefb926d062977d2142865f19a1aa206cc2540',
                'hash_atual' => '485b9322c626655733c4d9dd6021cff6d3c4cd22733d90fffc23b21c838ec2dd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'd8334fe4ea21a294742bf95f794cf17c5f8cf598c421a46377c41ed08bcd8454',
                'hash_atual' => '954706c1538266c3a59f09c3d06de5c599b1803781019a8d848718623b33c900',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'ab73e41912fadfd837a21a758c71fcf5fb8ea3e01639cfb7023e105126427c08',
                'hash_atual' => '2a3576686ad8c38f10dbc3e889bce7a539f6b5284d42635a2ca68e17e6b48d43',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '6d395e72336982a18cfbeb1a16db2ae69d5d218d91af925f5ca982a2ac51ef56',
                'hash_atual' => 'eaa83dee66d55d35fb5109e21408353bd636d4728aefef4c2e0eee27e81a4c28',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '3166148df20dad24107311f2df9bbeb426e11f8687955ccadfbb408470ed1c69',
                'hash_atual' => '33b13aad6771e84ac010fb328fc00ee366805ce0df8e5e7a6686d79a674b1701',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '2d5cbb0275a1815baa1bc525e2592bd7d9c1f6288c6632468d3c79f0241055c3',
                'hash_atual' => '7fc2f375fad298350eb8742168b12226248519a092f1e258d3d0cad5f8e2299a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'edf20af7f96ddbb79cf407d1f1e63c028761331c4f74da6c0704be9a074cec87',
                'hash_atual' => '8ea4f98ade3ed64b861e61b5a08d47e62e3c2f23556e6001ffd9d1e7ddbbd1c7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '97a7f39f26659e8796a89524538580201f377e4fa82bdec1791239048375fa2e',
                'hash_atual' => '4e082ecc45ecfa00b17845a499ed620e93202ca542f81b414a5312135f69642b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'e3c2576b5b326acf34073adc7fdaddaf61278dee0fdfbd330fbfdcefc07b6646',
                'hash_atual' => 'c5f61ffaa1eb037e282fc9e14fc57770663bbf8635b9b710d592cdcbf47c2184',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'fff3fd293a3018707909c058625acd5f8d72c5562cf01767e1daf7eb8fe9be60',
                'hash_atual' => 'ce2b2f3b45e4d16984daae5c6d9038d2c7fea2bf33093b52e6c6998b1e41962b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '2af9d172c334a4456adfd4dcbc38cb78b421e2e9ac003cd997999683ff6b7c36',
                'hash_atual' => 'c012f4f1f048d12110ebfcc8f1859530cc7357e0756eb4df98376b42bbb47af0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'fe21da7c9694cfed6352db93aa5242fa59e663eb8cccfc49cac1a69b9bcc5906',
                'hash_atual' => '69b3159ee403d080f0d4f6f7409568ee2ceb742422afae84bee618ab7eabcf07',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '25c6b36410a1d0939e5ab7a41bc79823d40f1cfbc68013b2918f5716537806db',
                'hash_atual' => 'b3dda00b97509c0fa416dddb582d95a028ffb2c590a271d0eed0afc6fab9337e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'c5294170879e10d9b8f089cdd56c52aae1fa037312a50e3e98fb017a50163901',
                'hash_atual' => '5f83ec40f03a154f926031232253e36ed1fb2f636a369698d27c8a59f32b2ca2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'f87ce2975af6d967137ec1f80072f98fd860e14628a2223cc7a73ea0bee76dfb',
                'hash_atual' => '3251f6ea8f9988b39d54ba88243d51c060927487474cfacb5794142f7a49aaef',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'cf7da18ebf1bb08084db5bc3c369fcd874b380e68d973dbea5a40bee48049a3c',
                'hash_atual' => '41db08c7e0f7182da2fe868c23b1bd64ed906148d7218eed20acabd6ad3d7820',
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