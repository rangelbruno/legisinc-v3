<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-07 18:51:04
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
                'hash_anterior' => 'f750ca29f25edf9935383ae8acc6aca6ed86ecdc62015c66c913d3764e8e778e',
                'hash_atual' => 'e397698bee0fed07d7a9a26420692895f68a0eebc982136ad1f3285185256615',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '34cf403f870c876f61a6e9f75ce5d3e6f33cb5e5816a2330b20113c7cadb4884',
                'hash_atual' => '64c19f10d775d2fab7cdfea4f603f167a37ebca5e8aa8770e6a0311c197a65db',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33855,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'cfc80a4b6886f36d89965727d6a989a8f71161015e43e41a995b41841bd2a99c',
                'hash_atual' => 'dcc3d1f030803bea0489e61d3947f89696917066b0e192fb12ec1a096d9acf9e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '121ec46ebe335e9c33b470e76909c8a8a448a45fe7d2d67e43574bf993b7a534',
                'hash_atual' => '5b473e1b2d4018501a09377d66ac4d15d086b46a074131fda5644a88dc735cb4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '5207a7bae2060a74abc9c3b2bd9c70228f3ea0e85ceabbadf51d6315c083b322',
                'hash_atual' => '44b44518bf5777b4a5bbe7beddc568271540ae9290c4481869f6dbbf45174edf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'bec674cb7767695ba05c5225faa4f6ce5a331f4ca3ea6bddbfea5e1fb3b5151d',
                'hash_atual' => '757885cac7b7faa5ac5d1b3232465d9cacb91e83923cb53b00427e14f4c4d1ab',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16728,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'a9ecdf805a953fc7ffb6c204af4f7395509cc152c80c3024318d48ac14058528',
                'hash_atual' => '628a742eac4c9157bf0a869f331ef8adc48d4e6c2d32ea34c58afe80a8d238a4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '0e9ec7ebc283b33841d9109ff0d42226864003562fae8e42f562296c9abfb5dd',
                'hash_atual' => 'eb1ff529c99351c9f2544bd2441083fa2c6cd8ec58f76758a67098f0003103f2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '252fc7265ded8e96e955f382e967f6ba472791b5f501c08e76c9e8cfb416ec1c',
                'hash_atual' => '5938205a2c971f35a26b783d4638a86bf73d50cbbee4953f51dfe61569afcd5c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '54b06adac199feb387f07b0b1be82a90d77760ebcf92c6b8fc6354278f2e25a4',
                'hash_atual' => '79d06fb897eebd4c6a61dde61adc3894376b89d4c4e0c9fd980de30af47060a0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'a90492c8ffd007165c336a4f492589c110ec14be29d7471795cc15c90f1193e5',
                'hash_atual' => 'c34a5ac17977932b63727e6b8ac85fd602dabe2d8c41d048c1b55f572fd457c6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'b1a5337a6894f5128762ea87a8e3f6e2948510914db3f3b2d28982d5ac2b8021',
                'hash_atual' => '785744a970cc0eb0385fae34eed391e94e837817143ba5fa9e4d0c6a76142ac5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'a615fcaf91ded97160034f09e7cadcfd5d5c0a067aa513c9bf4de3934641ccbc',
                'hash_atual' => 'dd329f3818cab334092d0d0b12109cacb4adfd0d03fa1816336fc54aab7ecf87',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '78350b179f324442a2b0c686a5ca826195c8117b654b4dfe8d2d002aac87e511',
                'hash_atual' => '54ebe4581d2968fc0da08742a9daef46366f38d81b3db6318714c561b25e7ac1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '4585960443a3a95975575dbef893d7fce1ec4cbbff3f46221da2c0d5ad32850c',
                'hash_atual' => 'eeb04473da7aaff646d607aac2154eb7045e716d215c6aa23fbb680255345c51',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'a7f880d64217276a94f2edb950304f824ba32eacac07c97628f7e51ab2d22da9',
                'hash_atual' => '2ff8152a0c294cf165214150e62f84e01e73a427b193d9c1ea27d7f4b48cd6da',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '57051d415d1d68dd6965efbc24111badcb278f7acc3350b2ebd1d3da7d804cd7',
                'hash_atual' => 'c5ea433abd3118987bafa9d85a7fb502ad578bb01624490589d547feded1222f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '8fce159df9f47d297cb6e3eb46b2e22e178d78bf7a803e434b0bcea070369373',
                'hash_atual' => '30e9042d7e691184627ff907c44e35d5b29878d84ae738d774a60899d0a8775a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '592855d518379b814a308a1768c23640465c11674ff060a467322304a659167f',
                'hash_atual' => '279f631cc3e1781a82ee439e998ffe1f1413e0630928670ed8a5447361d41972',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '57f571e92161adac99b88359fb30e70b64564bcf102eb3acefc255320cb9f6a0',
                'hash_atual' => '91f300a7635be81aa89c531ef016f213ef71ececd48d8a1a741b1536c18272fd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'e7e288e637a4206e47c3764e5714d33f28b62dc0046561711c54ce72d69b4788',
                'hash_atual' => 'b48912f906a3723833e9cc464730dca4eeb76d8ff166cc652a79d15c5abe095d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'a13edb5c564b5f534d78dd6ebae7fbd28c758f0b5ed601d5c7188e3e867e9047',
                'hash_atual' => '6660d785850eed40eac23b1fb4ecea82052ba62aacd832005cbb3b1f41dcb11b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'b8d1186312a05bd506ee6df88332f15f7a0ab2247668db3488ca6b780761ec49',
                'hash_atual' => '2d75d342c606e2569ed837dea37d820c2dddd90073ab110b84c6dcb63e3dbf45',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'ea23370926d0f8083f55538fc73ed1c9d6c61367d7553261a1182efbd07c3db3',
                'hash_atual' => 'bd261fb15244319dea5ece8e84cd9ced5041b9591fa11ac55c91d71a15d209bb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '6264f01c59c300efa8290c3286c4804888a28e1e348a6c858f3b3af87903ba09',
                'hash_atual' => '3f58dceeac377d371c0591f8b01cfbd6f50e56ce51bcb08b0907639a83882608',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'ab9d030e9eb5d93db34f6be31ba391f65128350af68940fac387cf70f07a58a8',
                'hash_atual' => 'ceaa9b8dba4a28299854f5c572d0fe71cd71d48ded96b68514349f56f68f3d5d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'e833be71e1cba5673228340caadc0aa9a5dc2b3ac55d1a132e0cb8e0f755e7c1',
                'hash_atual' => '3da6880a0e35f3190c1fd473ed8c19f177ba965668818b2023200297f8be9f3c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '0152c126ffa9b28a5cc523e5716d8d0297bda530593d2c673a79ae4cdd62568a',
                'hash_atual' => '0d7f1042a0c7b66d1bf88ec4ce55bf65259b69d3017c03629d42744c64ca73cc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '3f8b0968f95d124a4ec6c43674ea8389edf39c2d2cb6a5e6c4d66f0a863ed422',
                'hash_atual' => 'c1888cd8da08d97e6f37ce695d7d97847b684261640d2d39877078a7300ff2b3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'db527d101d5ab8b006f9fc075cdaf53ad24e7c1dbb298f820702b700cbc668c5',
                'hash_atual' => 'e828eeae71cdd341a3a1e20cd05157c29f282eac44fdbdf02f2bb36670af51ea',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '221afed1e78319c9eef1f558e07c68927ad6999e47ad3424e86a993a797a102a',
                'hash_atual' => '3b4ac86ea848109d381ec84ed2685b67845d081dc046f01ec9ec7a940ee38fff',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '85f2c43b8759c3f3c73ee18b1ab7b0d6135995e5922ac368643431f31637229f',
                'hash_atual' => '50a463c817dd599d923ac481ab9610285e5fd8919dc7602ef510c959f54c0fba',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'a6aaf310897af0842e8585134c49976b20b211f2f272649dac7b357dd14651ce',
                'hash_atual' => 'cb754f11588a9224162dfa7243634530ccad64a7e0fa4b4e3f9a4f821efa129a',
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