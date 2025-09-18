<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-15 11:06:15
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
                'hash_anterior' => null,
                'hash_atual' => '9e5ce3214fc53e47627561c3b7ed58047fa9e29984b838c5873ad95bfce1bae5',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 194760,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => null,
                'hash_atual' => 'f1a98b330232cc2bdfc38166c241b79f1a615146b0d62448c923b213839a5574',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => null,
                'hash_atual' => '4b7b1809d6659609e0ab61973a37a82111f52ec4dec72ceaa833be780cdae583',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => null,
                'hash_atual' => 'a5af38a23afb38544872ce2ecb626bc8b7e8de182f893e45dbbff978befae498',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => null,
                'hash_atual' => 'db6c14678449723be0e1e2b1419eb82107dc594838479a7167acbe49081e9c20',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => null,
                'hash_atual' => '7f71db6d9aded101c161cf729200da9aa66156284a98403ee24fd047bb4afb57',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => null,
                'hash_atual' => 'ccf05dea4acbac99e4a5c9508187a9ad39a48c586861c8210e11077bc2bf8577',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '1617f6fc96e71633431f2bcfb3d9977919ef4141978fe8c9629bbe1d6af91870',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '2366885cca0c4a6393f7c240e5bd5af0d565b35625dfccebcd114a1dd5499aa8',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => null,
                'hash_atual' => 'd7cfe9cb42689f53a966c5a221f67b2ef3bc8bb496955a4d4c7429edb93634ea',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => null,
                'hash_atual' => 'f414e686c69dd360fda2517bab1ea043b846389205176b7c2bfd6a0e388bdecc',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '6e44c0dcd51ac5318baf462b1ebbd58016ae471d2f20f06129afad971e417ef5',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => null,
                'hash_atual' => 'a44e441176f054584129bc66fc647cd2bf80afbf113a8ce2c6ad322f2f9da89f',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '98c79614e89b23a2c0882ce54252afa497603f9850b3941f0c25c5dcf550468c',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '5dcbb9b0e07dd3b0f12fd34352a6a1b004d86d634e00b8044d90d2a4b6742d49',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '2dc2c647196e01967bc33482fc0346b9b57517333eab03c69775df479c823441',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '9c30105cd07edbcdbec9955c6e4fdfb5864b105958c9deaa4c92b2be02cc9285',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '37617fb43c5e9be91e65d759efaeee7c35511943bd9af7015f00e01ffa9db7dc',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '15b7a8497f86936cafae0b1e8cfb27983fe74fdcbfc24cbfe2cf5de866def4d3',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '971580e1b634114e1539c196afece0aa9dc882a17fa19d5409a691191cbec4d0',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '6c453d1d12b959ad2591a18839593e4443b36e9533ed99e46083117d1f5f91cf',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '4e541a8f1ba8baaabbd32b746a478ee7f2f949965df0db82838545e8b0dd840d',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '836c502701abc0d82a4827d07276143da0f31f133d2520b0b07fcd85358a5c2c',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '6125b2e6722eb97d2d347095e5869c7fdbe8c1df83d0322292fa7b7c356667df',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '747f528d5400fffdd0e7477108b2af042ff6f420b58c0b406dbb5d9511ef20f6',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '83747cecfa9106b05c7c9a8fa70961a0d8ceaddd468d5da9d8bdce04e8a73b3c',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '2cf5fedd18cabfb7a7e811547a6f38efb95b5e18be06180be0ef1e55d9fa339f',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => null,
                'hash_atual' => 'e93d73d20a36ff8f03a2c7de1049136fe7d0e087669d2c3584e2fa12021500db',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => null,
                'hash_atual' => 'cd9e1c1b2a58acf729799045d4c75ada8047da6bff1adea17519aecdd6eebc7c',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => null,
                'hash_atual' => 'a27c9164144bcdd1053f2e0ca1244c33be68326b0a5765c863034287762261d2',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => null,
                'hash_atual' => 'e5f19c87080649b1220ef770646ba0f0a38411400b681a95c257f666c727fa19',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => null,
                'hash_atual' => 'a7bca45e4ec28cd619cb2d6da0679413a61319e508f874a33c5bd2eeccfd7cc4',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => null,
                'hash_atual' => '13758c1a3445b6ddcaa3f154e3ac8322ed64578e98f9fcf0df14d460d8851b7c',
                'tipo' => 'novo',
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