<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 02:48:45
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
                'hash_anterior' => 'c398e8bf4d67ddcf8d838e26e1c4e9949b3cc32edaa72f41bff25429fc21c371',
                'hash_atual' => 'bbefb3ab7506348fcc70dab2726374e2b8eb7f347d7a21e3262d0707ff7fc18f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '6307e2e2ec6aceae15f0aa0bdd17a6c82dde4a85b781e74e8e2e47f6f44c3ea9',
                'hash_atual' => 'b653d67ef4996fe1f886e39a192f7438553bc3169531d1442e3d4210a56b41cc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '968498db056591c318fc99768f398279bdced58d6c0e5ab49222c5fc4f10b202',
                'hash_atual' => '25ad75be4d7e7e018976aec85ecdce0092995892b588a6107ec51ee34fae750a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '1c8a4d9380281c10628e623144a0821dc03ed8d6118ca1bed1bf6b8d630cbb73',
                'hash_atual' => '8c2930dd214a7947db5e44fe45e16b637a302deeee3b78bf38fb9480bff07147',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '3feeeb578df547217783168054a72b141c221686bd7294e6700409d10b5c5398',
                'hash_atual' => 'd03f4774bdd60e669dbf76990445de8a036013c0b270502c1884d1f9cad5e026',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '13bc829dda6efe686c22c0ee82025d4df0e550d51cb84093d6862c6082293843',
                'hash_atual' => '854a3d592911dab4348b49b0d1ff6a8bf9fd73008d1653b9efcfc220b463542e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'efeaf2a32f39913a54d03406337d6bb3915948b4821cc134d432576ac90b8be3',
                'hash_atual' => '223b721ac48750df6d51e528739758d5dd8e2212dbd061a45d96df45e4d57d7f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '1aaf83e4e36e251b3e8de31087eec5cd8f36c8880bb750a709aae81db1e6ee08',
                'hash_atual' => '872e750acaaae79a0765dedccc059b8a380677081147621b4383b2e83974c212',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'b34b7e5cd9d22874a74397574339d37e80732468547daa163a8b6b2e20b35fd6',
                'hash_atual' => '2d55cacef2d763ce836aa4b1db8291fd25ce9e58a36abe423d839c3a3434fae1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '65d8e4122d664d50aafa02526899068d37dd280c50c84a159a52ae0856590b95',
                'hash_atual' => '225419ef873e550ef427cb5985070fe86eac6d21c9c6e0e68d5f157c5848d4e7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'f03802cea6c4062b952b090e8917ca0651c4fa9918f88bef31e94cd678329462',
                'hash_atual' => '37ad991d26fdfd85c139ad5c4fce4610d0037cfeca77b8bbd9a3c47edc637611',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '519d06bc25966f761d2028bf5dd9ee5db328f11d62723e93c5534e5545778e56',
                'hash_atual' => '141aca33ca240d6f6b897a9d2f28d4228be7bad757a16fed81cab8a3ab9f7b9b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '2125c96f3ebc2340a5be60405dbce9f426505209b3a76b1ac09fa480c70bde56',
                'hash_atual' => '5bdf023fb15db9ff815aec8030b39efe8ccb0fe4a08f7cecb50516ea7b913d27',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '20a90521a5a2b5e57a52d01fe9995bf88a37b76fc24c31788f7e545c9d3865cb',
                'hash_atual' => '809d1ea39f15bb61918b9a3fc283938c1cdf09c3fbfa9a69c2407614027b790c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '0a3e284f7efa974cc106e5e610d949ce680fb8d65ebdac9c699b5d7a51e5d95c',
                'hash_atual' => '1c650929238951ab01fbe3ecf91df61450a5e420c37c76edb7d7450b3156183c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'c0608fd00951fce80442f8af879523f7679ed3f2101b31cfa61e9b2330c0b178',
                'hash_atual' => 'e264c794751d135ce502f569c4c313cca5ce7a9a0e4e6f6fe9d26394d34b46a3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'e691f92fafbe4541887dd21c070879faf81c1058dc8ba591e623edf6b1b27306',
                'hash_atual' => 'd9ee4ad1f9568ea90db868896db4a28a250017e047b7f60675f767b7735a28e1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '1a3dfff04ec559242bfa43e85ac9fda5cf670174d4b1b8ee24c11789013b751d',
                'hash_atual' => '6ce4b2dd4045e99ef2636b77c53bf898d374fb87d2534b2019a19557713e4136',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'eaaa66f6dec4950d141b3501ebfd035db74b588b3c7c9f12beb7bfc5bc1da7d4',
                'hash_atual' => '1255f9c401fa951bc14014604d658bf858356467ad5e37b0ed3d50bd9c312fc0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '2298da118a662170404ac12f945cff91d1d4cf552aa0b8313922e7ff8bb7907c',
                'hash_atual' => '71bb72dbb5c2796bf42893fa4c56e5120a90619b6cb1100a2dba60926ac7f50e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '79a3d285aeb9a2b00ab6ba436d982df7019d96490c526e7df437ebeb0bb8cf94',
                'hash_atual' => 'd122b47fc61a08526fd2fefc7b07347109b5be18957187fa67638ea5b6e05809',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '80da03067c28752d4aaa075111e312840fbe1589c3b0a836195a6429dbc84ed5',
                'hash_atual' => 'ffe3558a58b765acaee7b3038b8115e88fa9b0ff1df7a160f2810377c7bdd52e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'd05ad01cbfa2b8171d06ac634a72e216121aec77dc7ba8b5dd864984fe9e3447',
                'hash_atual' => 'f0fea2993dabaf19daa15b5333d56f1ae502f356ae6af104a99bf110294478f3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '4088c5dbf649c14e54981f5831db56f309f4669f76e05c158c90a8dccacadc7a',
                'hash_atual' => 'f45617f5b4bae6e050095c2429d58d21e318d2a0e202b5210217d5e6fa8a1c83',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'c02e9e358f0977ee298403b96dd6410d488560a0c1372260eb7f59aea51e9e43',
                'hash_atual' => '97d1149334fab638b9794abef2191c63e4f50c67f56fe5f426f32a91e6134680',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '5cacac69dd4ddbd216c44fbd6407084f0baa5851d2781c014139ede8b8f296eb',
                'hash_atual' => '905614850c0e8cb30cd8ac6e7d890bc63cf2fe14d97c2a8948db06eaede3118d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '2d4c4cedb78810daa503a23cff551306a323c382d7517426f3403e5cdc17c10f',
                'hash_atual' => '38d3e3f5a9cfe6dd54204d5aaa1ab57ebbe8db7ec705579503b1d62f7f241f57',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '479f0885def3a4970856c2bb1d483f2d4d01a306826ec5e3bb941c36a6a42179',
                'hash_atual' => '6607f33c0b4e0ad60528a774bd8e97169061d8db95e9d0886c92160eac7de219',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '36aea420807941b9d8b61b92c594ef0d6369139dc9389f61a1da07ef0bb1425e',
                'hash_atual' => 'e043ce26805b1e14f6a495bbf7b18c6965723fc2834576c2535fbde9d3b7f262',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'fd38644dea6c9adb1bd17d92e68c881f57db23cdc5a1bbf045cb57427ee15bbd',
                'hash_atual' => 'bac781931024364acfd3fb4d06388cb6b69d058080cc9b018c3bd5e80d70d1c9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '044bb1e00cbf19f1b0bd39759467a1c95078f1fc02828c336c118f4fa4062b18',
                'hash_atual' => '44641995611909d586b71089868748a6cac6825b0763c9047d48f053dae9bdbc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'f5185f4e6c2f126cb8f1acda230c323c93ac7fa4d433e13f7afd1551ee5b905f',
                'hash_atual' => '455ddb747874d8f537083f3bb24ab0f5c6a2bcf37366819ed057e3d147801d43',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'acea1797b1d266cd43bd25c069b6e1999370e1e1395110ba2df66ba4d0492545',
                'hash_atual' => 'eb9abca69a439daf45840e08d7c262329311dc42936cbdeadb5b003af53c40c4',
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