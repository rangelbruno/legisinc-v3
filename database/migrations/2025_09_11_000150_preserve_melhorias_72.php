<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-11 00:01:50
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
                'hash_anterior' => '63357742c7e57478c94b5def8adfcfa84c1bd8f0189a802f4236f1421ba0749d',
                'hash_atual' => '0e2014a188707d94feb1ec2399cba04013db8a9284252d4551c5bb8ce5bbb6a5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 185055,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '738124bc4e91e8e70dcecf4e363d6a193c8edcf64ab4b0829bdecf40643c295d',
                'hash_atual' => 'd7d015b10e563bec1ca471bdc226716288f6c973d620b39368e572ba0a58f68c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33929,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '18e68e0608f0d36a3f88ffe657412100a0cf426aa069a5c1ed5cbd215a497bc7',
                'hash_atual' => '4d3539b3b611059c7fa041142e99138ab9d2117590fda8df2f2cb1563b0dd7dd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '60dc77226b039947632af35b3c53ed97fb6ec1256c4babf834afd96e203f3312',
                'hash_atual' => 'f79aca9ca366bc7553f3d9bd4863a0b2eff49169af13e65f97df2f4e1f6d8592',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '47b701c1a640a83cad6a9828a5a5dff2b7a5ef5eddf9b7e28c5aa5972e7e1be8',
                'hash_atual' => 'df3ede6e8421ccb143e3344012b07cc157261cae5fa295ea4f8b6492ea95bdc6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '4012500f0f361fbba56ffb861340df3760c61598084c74430c696314a13a620a',
                'hash_atual' => 'cd0cd2d6765d5de92a9fab59196652e5823935bf3ddde1f89a5e015f0e9b098b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '17634d4d6d3ff5dc05210a7ba4f24fca9bbc37bbc72bf72902c2737fe30e3d1e',
                'hash_atual' => '59c882f4793d752d827706342dbb72ad3db77b18f8152e35558c584651d6da30',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '2db486828f8472cebac90630bafd0b635155e64cabeb7c7138b5870e3a2cce1b',
                'hash_atual' => 'bd094d3eca908057c85549833008bf9da9f0c188c82f17c418bcb03eb9d1dd30',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '5f47617b3df4b630d7831941f57940d33432b1b1bac57ea941c49115cdb9de46',
                'hash_atual' => '64bb8588630449ae6a478a26e8609c418aca6bb936f82492ef234d4bf4dcf41b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '462e989a956de15c52cc90a25ece0734164f622fdf0078fe88ab618ab99a0443',
                'hash_atual' => '4f54d3d211498b2288be657d7c0d48f0c46b63b2fb86a1b303901135a66e7ecd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '2cd0228900956673b4eb6ca7b68349507b196695a1d669c03fd665fd96258969',
                'hash_atual' => 'a89d570ea898950aa9a88a2b5090393d139b53f35a25b6ede62e429e79d5d17e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '9074012d59291adb9e3a4e44541bb136884a0b5f4a568cdd370b5923d8a0d7a8',
                'hash_atual' => '5e709e3c9000f0345000a7cc3f6639b905b92cba33dfacd0d7c843325d5c0429',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '4090dae0ac07abd0eb947866991f0f2428460462d53c5fdffe6e51ceac33fe7f',
                'hash_atual' => '9ddbce7cee92d3d46d23f803926d589a34e8293a4b1287dab04be3c0f5b35589',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '6a33028f53f78a6b0e4a63868bd9f1538ca7c9bce135c51188ff9c7ab0b16a85',
                'hash_atual' => 'a77bf7264ccdaad2fd4fc64007bcc09a6bf4df021575c90d48ce4db057d618f7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'b7a08f57d8366b21d3a2d8676cc2a205ad658075abe5a7fcfae48548b575b462',
                'hash_atual' => '775157bd63e88de8a1fdb6fbb3d359cf0d956e30b73d3dc8816cdae7925cbd24',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '5f70a6d78d0386ecf4f72ec9003a3552e5aaf308244ec081600dc0a67c54fd1c',
                'hash_atual' => 'b5d131fc02c969ade49883890d71653091d7cf00aaaf0c90246b0b86eee0d6a0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '52c14f25fecca4ec59c75dcd8d8accfaf3bbbf946e8287ef753e88084c83af81',
                'hash_atual' => '03defe8d76faff0b38af5e1f8ac233c576fc9a2e1def559e59abd8118b104d6f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '532eb7ba9cceb0a9f8c08aa796df801b3b952897f4df06f67b287d0457f66b53',
                'hash_atual' => 'b0dfd256c26f2d1d6e78fef6f1776baf1325a2edf269ef6f5c1be0fb20e1089a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'd314f9a2b230f24757deac4a0c83982528f2a3be80e92535294ecab33394c816',
                'hash_atual' => 'de2ba709927a018b91d25a5317bb1202cf66bf631f251b4ada30bff36cd49315',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '11eb6c174be1b8a83f5531c3484479ffcbf8f30fad5c7a2703c313ffb369aa79',
                'hash_atual' => '8b43ddec96c3d67db03ec013dffa3ce3a37021e074adce8bdc02dfffd6eebd5c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '52ecc82ae61c81e54b4def974b1732fec59b25ea6ec7dc7a7df6942d9be3dc50',
                'hash_atual' => '3added813224560c402afb85ec8923449199770d0ddd4690c856a237e0453f0c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '30b2dc844d7f23e5f13f6873bfbf92d5118b7f671e9b58a5b523cc5e823b4399',
                'hash_atual' => '6c89709969818be005b1769b97f58ad2ffb27807de7e6162837948cb7e6cd19f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'a84062f4b1cfb42bced7508fd1a5f9cba69980a58ba477e86b312fc09d010a89',
                'hash_atual' => '30c8646bff9815a6e30d7e311b663e47d9da5d89701e1b97bcc296ac4e38815c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '2020675750dd78d64d00592f1708ccf7492a048a92a79a0e6036674c5e11b1f3',
                'hash_atual' => '210293aa46a52846cde0518d3561b85f4abf49bbfc90a1bfa3cae616f8fb0df6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'f001dad4108bdcbe6a5b0e54c2cf4f1c707919f255e7e85e88518444c2ae93cb',
                'hash_atual' => '74645cedf823a49adab5681ed099e8fd305254813f90ecbf70b0dc3c7dffd8d0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '6c83bbaee4fcce164a2a5633b484f617a66ce5aeea8b2cf2e5f2efb0913df4d6',
                'hash_atual' => 'c429b31ebc680ae1e25aa9c5afe0e90a88a29ef6da2c1784dc2b4728eccc79ac',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '4cb1cecba6a9f90859443a08436ce01972c6590be6994e83b1c1f4f1318e7e46',
                'hash_atual' => '20384c0623b988cc5d15b686277e4537a9df9ec61b8f030dde934adfa8724f97',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'd1996ca8c28f00a7f9e63d68d2aabe65f428e9b3eaba6fa857fe7e09f7b23cb6',
                'hash_atual' => 'c401d7dc17050b39a322e2d126a8e00ae9ff84e4a37821f7df9488efec97b746',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'c2719c73f41604770bd5f290f5f38414eea3b094cb7fbc816aa71507f3f2ff60',
                'hash_atual' => 'e3e30b92c8b221a2004837bd17cecd01eabd7369d6882cf7d418c42574d71fbd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'f78f947b4ac2cf8b373bde70001e518e55b74d0f2d004f65da8a24014acfbd32',
                'hash_atual' => '1cebfc7ec59d4b747a478e643746c10f7dbe7fd7f1463d0fc5832b3ae32f23f6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'd0803a5991a4a3c9e7d2e10996ca595fcdf098425dd101734ebcce5ea2e895bd',
                'hash_atual' => '1aa4f86636eb01dcbd91e72add6683c72d784ed901b91ef34daf3dd7551596b9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '75026a974000d60004d81124c929e888d55de264d20cf8bb279a6133dd3c20f2',
                'hash_atual' => '68a0182fd1b9387484bdefc98737f131fae8a8d2328350fdf61c98a30949a99f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '79d0edf6d19b5be7bae8c13994be6bbc390a53c31abcf74b50c952dec52ab74d',
                'hash_atual' => '047e06edcc9ac88cc3cdd974611cd8cc4ff91a73fbc14a0378998e81ca0638ad',
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