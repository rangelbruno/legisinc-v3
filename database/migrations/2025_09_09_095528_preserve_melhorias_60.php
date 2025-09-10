<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-09 09:55:28
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
                'hash_anterior' => '2c94733853d53dc10c00cafefb841896d0944e28307a544281d1ef15584822e5',
                'hash_atual' => 'aa9e225a5caf5b3c26bc094eaa2a5d9113131bf57ece12e2eb6d3eb1b3289b19',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '13724c2ab0986d1d922ae1dc0ab4dafd92f024953ff38a1cfb1ab927e0f109e4',
                'hash_atual' => '1bfaeb1a0d3e441d400bdf36f3c41023c7efadac0b2d0ec586aa8d422cc1a97e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33929,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'a83dada009b7e3a56d857ba66654836288b897452ce9afd8cd7a1967d2c18957',
                'hash_atual' => '31d35cc7c68e579d18847586cd0aa5f29bedb5f329db6fa03fdd287f7cfe0499',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => 'ddaa7088a1272707fe3f7804d52fc390d309413f94355fe5626f7d5825b09f5d',
                'hash_atual' => '4b7761c1dee628a09274dd4cb4f8d1147f3247125209801c4af673e495214907',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'bb39dd155bab6d0520818da8e6c3c9624fa6f4334492cf4b6c0485e1745040d7',
                'hash_atual' => '2763f17ad05083994403898192bbabb90b5a9350ea4aeb61cdd7dd93c9ac673d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '2620174e42875ddb9ad2bd1bff51a14753138d8f04ce529ca1af0334f7577036',
                'hash_atual' => 'bf07dca4687f8008fc8671f3dec5a6775139a350262f6044ae9730aea209c1f5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'c030421dec77cbf4398707abce15740b6449a0315c1ef56c5690e689e7f7c927',
                'hash_atual' => '1a02296811878ed85dcb551bf44d6a2dc1e07cac4a3539402f54c2e99fcc32aa',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '8f1b86a9e80b44a15ed148b35a00990c829e7070ef40fad606747812eddce3e0',
                'hash_atual' => '3ea32f5f2a9d356d7511797d3427df7dbc27e5ed1898843b5df7a589eeb87ad8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '3534b8ae870ac54fcafbc8fbf681aba8ad8ba6cf0be1c24ac4a4e578eb60e02c',
                'hash_atual' => '1a3c430e2158184d9f6074eb5b4bf25810bbbc0e784d6064de376ed0a7a45185',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'eb8f079f66317710098c01eadf886bab5e223fbefaf9898925019ac280fc0c86',
                'hash_atual' => '13e5ba5a0f9099d4394609e0b8040de4935cf755f7e1c91c50bba31205a61336',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'b859122f69b835b9d098826ea79bad338cd287f833ff0c824223197af4a6d99c',
                'hash_atual' => 'c01f077f8601e737250f30fffcec2d486b1de0b3f92c52ee628d786f8c71ba34',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'ca11385419e0448aba3beb17ff712462181f8db56387d5e55d62dd2eb77a0540',
                'hash_atual' => 'f5b7a1eeddb25ae2df9cebd5229342eeb0309d0e00da91380dc65733f7b3c8ef',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '05e194b7ace4828b4949d7ae83e2fbfe04bf7fb14d6a34fd5d25d08a87cf0f1e',
                'hash_atual' => 'd02bf9ff3fc755ac3dc7d1b84e6fe5765a88b641b5e93c91a62601a7a4e553a2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '682842cd4662c0d7afe779c28bb640f339b49a1a383850a9b262cc295673b91c',
                'hash_atual' => '2a5eb8aeb0ef8a09dfb5653a8990bc20f93641c0f335df92f51d1d022103e25f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '56ffd0d466fee826ec5e1457f131bfd8b65dfe3c818174f8a5dd8e300919ff18',
                'hash_atual' => 'bc8a95501a9dc2e02793cfb8f38f708c08773ddc316918c7beb54bbdc6aeaf14',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'b8e7f6eb19b5024f254b75183e251eb727c3d69c228a81989f0c0c2684c8c9e4',
                'hash_atual' => '0ce9e3ea16677c5e12c86a4b5361f04b17afae8ff6002421cef5a33fbb93febe',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'e1c0a760fcab995fbc81c9df472a68057fa434ae517730f8797404799be026be',
                'hash_atual' => 'a2157fbef9987d6705c7d4c18a63e3d6a7a918ea105441dfdd229c1c4d0ab9a4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '5114c0182114fde51a734e6e2b777b8c416b4097ab59ba62a7dddbd4ce2f9edd',
                'hash_atual' => 'bc0cca2bfb58458a60d15eb2e3c7fe577eff6a0b89975dfde2a3a897c2e954d1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '40d8f4eea17d2f4e332f323985454995c353c2e3b118fce59fee61450f79a4f8',
                'hash_atual' => '1bc3bb12f0f06205c99c629c594ad0b6146a95ec887b9f95bbb8c8c6116b410b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '75d0790897164b1fce8ebc92d1fa3a7bf1cf022eabff17eee72cce94b9d00242',
                'hash_atual' => '1d576f0f75e0a4ca3a48d54dd0f87a5efbbc46c449bdaa3b437f0a3a00241238',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '2f42b73dd52891473d49e7b0c1f0c7a79dccf06876d00358cc8ca48ce4698134',
                'hash_atual' => '1889cb1a2f63c15f8496d057e1e3185a8b6281cd3566ea3a2e231c440e09ff80',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'f9065b5c42d9fddd4c9c2e9a0c10a8a4228f3b061a0ea75868edb5b2071c5785',
                'hash_atual' => '4236405b640c1e5f822c6aa8d1caf0d3c12aedb260504acefbb09a8494d1004f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'a33c4d02f639213680c35a2ff2fe195328625d542fe7eb6260cd5fd9f5a1a5a0',
                'hash_atual' => 'f3484e0ecfff196eceb14c0d9dca73a2c6cc6bd1dd39d0bbd2021082b7034129',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'd6349f318112730e20ab7234e8a72c61519824c8258642f1dcba25969cdda34b',
                'hash_atual' => '91ad146fcca823cbe977dcaadd6a739fd35991ad66b97ff07d083ff78b72dd94',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'de5e7e6b9f6675aae1b462af6d4c1f43346ab3d59cac6571d66f50a0bd595a5e',
                'hash_atual' => 'a9268a912448f0f2acf4f230d967b561399c0d2ded1801e35620f9cb46302f04',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '8d1d948eb3c4b380e7f2365cf410ee853a81c16a3da2fd8795765e1d2a8737c3',
                'hash_atual' => '7d3d0c61a3fdd7396110b595aa8b4a2a95e71d97b077a7304bd66de24cb91d62',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '64ab53a87043e7380d8d4c6598aa5e967be5941eb166c5c2439666d2241b8fe3',
                'hash_atual' => '6959df05da670f71895af53e285a42dc421b59692776fe65dd5f5d1be612321b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '19a0a971a69e32187a9686343c27459c66a160caa6ccc89a6a417a3d741340e1',
                'hash_atual' => '6f18548dfa6f5e53824bdaef764448b1e21ca106def5979a897eed55e90e14e0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '56792b51f2481f6939abdab3e83cdf6bca009f11f3206fdd3249a241cc2a5ec4',
                'hash_atual' => 'edcfb093e018536fd41111335ad69908e80326c4c9d05f7082df60f758d6cef0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '29bf7a11e9ed15bcc98f40475f79dc4b8609614a4341db7ca37ce62e29c6a645',
                'hash_atual' => 'b15c72768f6514a08c0e15b324238719c8d32f2be7838e8659299f154803fa85',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '7304c1b634171d2b7c1b517a6eed1237902ad8bc087fb1440170b164e9328020',
                'hash_atual' => 'f621bc5a196bff098b5d836217ca95b71cd1d423fdf84a8d1b77aa0138c934af',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'c589aabf433574af8c28b403291145c8e0b4a4d77e38ca1e8bbbf0849998eee9',
                'hash_atual' => 'a4aa553330e75bbf8ec629e30ae466545794e54594cbb7baf011e5a961c919ff',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '16049290b003fab12bb979b78f98179a4cfeaf153ff50aaa42b662505942a56f',
                'hash_atual' => '62878020ef21d43019a092b9a209fcbefe57b51f02506d63849f194acb33605c',
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