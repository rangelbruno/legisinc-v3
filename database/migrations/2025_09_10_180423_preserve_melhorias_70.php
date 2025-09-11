<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-10 18:04:23
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
                'hash_anterior' => 'fa15e95bbb6b4d2799cce7ddabf8b257e633e6c5804eb052befa3d659f81fdcf',
                'hash_atual' => '63357742c7e57478c94b5def8adfcfa84c1bd8f0189a802f4236f1421ba0749d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 185055,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'a492ef46fd116e8a6f97fa0b3063d8cf7b3c2698fa6e4ee7e9467082a3991d69',
                'hash_atual' => '738124bc4e91e8e70dcecf4e363d6a193c8edcf64ab4b0829bdecf40643c295d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33929,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'e1f90d1911f73a91c5f0079460e10d9f877ff5d1c12e756288e1f5dd0cc10e4d',
                'hash_atual' => '18e68e0608f0d36a3f88ffe657412100a0cf426aa069a5c1ed5cbd215a497bc7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '1ef117f4d8bc3ca472afda1f4a3029112ba5d35c51f4fd2f166155a5656b7468',
                'hash_atual' => '60dc77226b039947632af35b3c53ed97fb6ec1256c4babf834afd96e203f3312',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '39d9d5c94752ed79a759f4e8719ef0a5b2ff67de13ec67e9e8620a2ab11301bb',
                'hash_atual' => '47b701c1a640a83cad6a9828a5a5dff2b7a5ef5eddf9b7e28c5aa5972e7e1be8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'e38e1df0c0af99cb673b79efaad7564cc8b1513fcb255438f53012b384bd9014',
                'hash_atual' => '4012500f0f361fbba56ffb861340df3760c61598084c74430c696314a13a620a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'a642f3f8998f721983e1e866e2ac60d09e961b3c57f0114fa940f34fd3172556',
                'hash_atual' => '17634d4d6d3ff5dc05210a7ba4f24fca9bbc37bbc72bf72902c2737fe30e3d1e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '13d436776548d9d21993e0f64061936c605737b192c0f2f934a2a36c1e052554',
                'hash_atual' => '2db486828f8472cebac90630bafd0b635155e64cabeb7c7138b5870e3a2cce1b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '53703915f0d40a797a746bb413ca73c6c05e9ff979a4405de668ecb5e31f96b7',
                'hash_atual' => '5f47617b3df4b630d7831941f57940d33432b1b1bac57ea941c49115cdb9de46',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '381687fba964a20c0a384993345ef47a6cd1f440d41b44b07f58bffabdf1b844',
                'hash_atual' => '462e989a956de15c52cc90a25ece0734164f622fdf0078fe88ab618ab99a0443',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '7424386d3aca400413ca15e23cf0eda4c47cb504aff8d5267134ae91c76e3b42',
                'hash_atual' => '2cd0228900956673b4eb6ca7b68349507b196695a1d669c03fd665fd96258969',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '83a0cd230851f83cc57fd5d9b3363e449f049f0c15a2da8c86f3c77986b43ae0',
                'hash_atual' => '9074012d59291adb9e3a4e44541bb136884a0b5f4a568cdd370b5923d8a0d7a8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'd3ed8dc7399c8630fb223169269327c2f23412cca465d7e4ae866fddeeea8471',
                'hash_atual' => '4090dae0ac07abd0eb947866991f0f2428460462d53c5fdffe6e51ceac33fe7f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'fd2e166f7740602c983f1df831aeda246152803091ea13de53df8e72ae06bd86',
                'hash_atual' => '6a33028f53f78a6b0e4a63868bd9f1538ca7c9bce135c51188ff9c7ab0b16a85',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '06e70119064d935e2728acfb0449a1f6cbc3f327019b7036cee77dbd01969e68',
                'hash_atual' => 'b7a08f57d8366b21d3a2d8676cc2a205ad658075abe5a7fcfae48548b575b462',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '070dd44c9eae6192022724c0d78bb7a759f89d40b4ab2f566d0853de14f125ab',
                'hash_atual' => '5f70a6d78d0386ecf4f72ec9003a3552e5aaf308244ec081600dc0a67c54fd1c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '19b8c90d702c3f3df736938ecef014220888a4e370f4bbc9f83b99cd9e474b09',
                'hash_atual' => '52c14f25fecca4ec59c75dcd8d8accfaf3bbbf946e8287ef753e88084c83af81',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '38f07d92461382e77f7f7872c793a6c572458f4e45456604c9a4fc23f5ed6090',
                'hash_atual' => '532eb7ba9cceb0a9f8c08aa796df801b3b952897f4df06f67b287d0457f66b53',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '03a07db38356a364c6f8e83f40b3779a87a2e75cf497c8e2b69f3b49d0803994',
                'hash_atual' => 'd314f9a2b230f24757deac4a0c83982528f2a3be80e92535294ecab33394c816',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '4cb888f4f3cd5da45eb7ae9240ae78f8e8d709fde44d3a31973018562c7aa5e8',
                'hash_atual' => '11eb6c174be1b8a83f5531c3484479ffcbf8f30fad5c7a2703c313ffb369aa79',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '4178a71fc4e20086f6d4ec7b8a2d466ee27534702a050343a9b02d573534e85d',
                'hash_atual' => '52ecc82ae61c81e54b4def974b1732fec59b25ea6ec7dc7a7df6942d9be3dc50',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '7a7f9aaf47ebd86754560d673055f9e9338bdb1712c7e8be0da7a282c51a44f3',
                'hash_atual' => '30b2dc844d7f23e5f13f6873bfbf92d5118b7f671e9b58a5b523cc5e823b4399',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '5ed1c4567e6c3b5f224ddb99e5660e9aee624e97234517d89208f08016bc7048',
                'hash_atual' => 'a84062f4b1cfb42bced7508fd1a5f9cba69980a58ba477e86b312fc09d010a89',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '7d0aac7ca4c1548944475d0a417c064e561ff43b875e7b62a3813873d5775b10',
                'hash_atual' => '2020675750dd78d64d00592f1708ccf7492a048a92a79a0e6036674c5e11b1f3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'ab7babcfdcd4438ea2ed16696f125694b6b7eedd68ff91e23e04bd80f061c28e',
                'hash_atual' => 'f001dad4108bdcbe6a5b0e54c2cf4f1c707919f255e7e85e88518444c2ae93cb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '6eb134dec3917e206e215ef8146b2124ad91c31d7e2710dabd42c5265aa0ff67',
                'hash_atual' => '6c83bbaee4fcce164a2a5633b484f617a66ce5aeea8b2cf2e5f2efb0913df4d6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '783ba63f28fe72a8a8a64c005b5344af794b3b637c19b0001f788cffca60937e',
                'hash_atual' => '4cb1cecba6a9f90859443a08436ce01972c6590be6994e83b1c1f4f1318e7e46',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '7650d0a3d58b3df5bec40f12c0830372586485fd1dea7e29213064418e962547',
                'hash_atual' => 'd1996ca8c28f00a7f9e63d68d2aabe65f428e9b3eaba6fa857fe7e09f7b23cb6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'a28790e2a50c8869ed01f9dce0848b5ebb261cb0eacbfd382d030be00f562f16',
                'hash_atual' => 'c2719c73f41604770bd5f290f5f38414eea3b094cb7fbc816aa71507f3f2ff60',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'adc60afbcc7d27ad47fa5004937b60371c7daffef16276f20e66dcda8cff44e3',
                'hash_atual' => 'f78f947b4ac2cf8b373bde70001e518e55b74d0f2d004f65da8a24014acfbd32',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'cb86d2082049a19f97c5c6cfa798fdd4bfe9291a8721826449477dfa3360b3a6',
                'hash_atual' => 'd0803a5991a4a3c9e7d2e10996ca595fcdf098425dd101734ebcce5ea2e895bd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'c5fc59891f1105c4505ca273ebb2da27f6be4cc9f2f8b5c4f30da4fea12683ea',
                'hash_atual' => '75026a974000d60004d81124c929e888d55de264d20cf8bb279a6133dd3c20f2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '3aa2b205d62619fdfe50f8938831bd31479e76d1fe6c168923b80f28aadd4ef2',
                'hash_atual' => '79d0edf6d19b5be7bae8c13994be6bbc390a53c31abcf74b50c952dec52ab74d',
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