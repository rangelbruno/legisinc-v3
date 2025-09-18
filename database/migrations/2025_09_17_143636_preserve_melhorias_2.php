<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-17 14:36:36
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
                'hash_atual' => '440477206506fa19c34a55e52e6c9b48616be7dece0511517aac0140907a03a6',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 198288,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => null,
                'hash_atual' => 'f8b4725fd69184e12796dadefbec807bb10db786f322da48df7f0a2312f3c619',
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
                'hash_atual' => '8463f33f4ba1ce408adab5164509c2b86c7d535afb6fa0b4e9e054da489e3db8',
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
                'hash_atual' => '02e5f1d73e2dc3b6ae6183e9fea673da6f3c636ae2faa078c55f579cf4a02f60',
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
                'hash_atual' => '693a231dc1737fa2c8e942e592118ba6187cdbace530cb5c4afe76d3dd2ffb19',
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
                'hash_atual' => '9a11f9df35aaae4432d6e627b21d40cc49952dfe83508c49c2d611da60a4c828',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 18595,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => null,
                'hash_atual' => '215b4ec8f331787b0278463a34ad6c241722d0f74f5e9cce96f2abc3996fb094',
                'tipo' => 'novo',
                'metadata' => json_encode(array (
  'tamanho' => 11654,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => null,
                'hash_atual' => 'd63a9bb219d296988fab2bd9864f93f3a018b80a1f9cddc69c5876666fef34ce',
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
                'hash_atual' => '692000bcb383ee0c7af99fd6cc91558620e00332d1307c7acb5cad63bf55b78b',
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
                'hash_atual' => '8252ae993758cc6b891e73a6025a70c763552f66003e84835cb06f7aadfe1f09',
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
                'hash_atual' => 'a3cbe53925d72e690eb342dc1fd20024b438d1fd969b13b9a551a7d3f330c853',
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
                'hash_atual' => '767b2111d7e69d63536c5837de7f6b5e6f273d45c918737c0faf5235af7cc13c',
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
                'hash_atual' => '61e2661e702ed345eff1715a2c5b398b035562faf6a620972900cf6a4c8fe4f2',
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
                'hash_atual' => '17c202a1b39b4731e657c880fa710751406b164e224ee10b2f42547185680b35',
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
                'hash_atual' => '72512f500d91fdb58b639d16068f4c7dbc82149e00a4febb234d8d1e209d9d8c',
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
                'hash_atual' => '9ee91a7057aa14afe7c315df06a4543552efcee73cb5a1492659ad704c91f052',
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
                'hash_atual' => '869a193754e64ef1ca799b295e985095998bb59a7a4f616e78b8fd17dee3ef81',
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
                'hash_atual' => '5109f8610635405ae55323cf8e60e1316064c96c6f9f1ee9243c5cd008e7eec2',
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
                'hash_atual' => '5adaaae822bf9c0e97b2dfdf333b7ddecdc4e6494e04a54f1c5f450ed32728e3',
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
                'hash_atual' => '15eb27881edcb6fcb09c8f500bdb2c433eb5acd3bd0d9db022263abf878f83ce',
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
                'hash_atual' => '6bb6de955e3802a407dcd8efca096d2741d5c84db81f45d84f1f5a87cb22c276',
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
                'hash_atual' => '9eaaf83056f40d5d6006a9b4b35ce7135ce0d137c1fb47099ee69859795744f9',
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
                'hash_atual' => '1e89774c5f8607497839599aeeb1526b203142756bdca7319d094c01cb89ff81',
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
                'hash_atual' => 'f9d90c96427a36f07ca6fbcc62871ddbd238638125a4e38ad40a2142cbbeca8f',
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
                'hash_atual' => 'b7d731758f0183e88113164c9cd916a693b84ca370efa0ba96139e898033c545',
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
                'hash_atual' => '3cca100bcfc8c4f4ff18bfcd82edd8d87870478dba681aab4294b4de8822025d',
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
                'hash_atual' => 'a9ca5799ab219986f52149a144a7a065c25dacfde373024545f6bf251543f2f8',
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
                'hash_atual' => '3a99ecc94c3c27cf3002d309006a2f6f8042f1782a2429b414b60a940665364f',
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
                'hash_atual' => '51c9264e9d5383737c7d2e00b81d4357f8fd915e87bcb9fa753ecf80c886993c',
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
                'hash_atual' => '9e1d0eaedbdb353eb83f4d9e8cbc5125bd6367ea01f1e0dc5f3e70dfcde0053b',
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
                'hash_atual' => 'efb3d45b902381337ae281c64b756d4ea6be25737054dbd06c09032c9a5c6292',
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
                'hash_atual' => '070c577ca0123d3d4f678bf16c30750909f995a37f36ed06ea31695285997cb9',
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
                'hash_atual' => '286dacef7a144c1954e269c8af88a8efa9c71db4a38c04b75b5e83c5a5d6e480',
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