<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-09 09:59:35
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
                'hash_anterior' => 'aa9e225a5caf5b3c26bc094eaa2a5d9113131bf57ece12e2eb6d3eb1b3289b19',
                'hash_atual' => '80ca49ba7c2126e638f28bbaee3e74c2821584f9c4f4af275d045dbdf8d4b5e5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 183240,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '1bfaeb1a0d3e441d400bdf36f3c41023c7efadac0b2d0ec586aa8d422cc1a97e',
                'hash_atual' => 'd620e36f0ba34c2a2a2578945442c89e350cdd8958a31bd88f41724cd91c7c5e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 33929,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '31d35cc7c68e579d18847586cd0aa5f29bedb5f329db6fa03fdd287f7cfe0499',
                'hash_atual' => 'bc1ede98c89641ddb25cff0a1d6576efa7d75b986bddd22a523eadaa3cee3dc9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '4b7761c1dee628a09274dd4cb4f8d1147f3247125209801c4af673e495214907',
                'hash_atual' => 'd9f37f66e441fcdfcba80b6f578762eda31993f8db9565a50ca4cd86d07c4585',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '2763f17ad05083994403898192bbabb90b5a9350ea4aeb61cdd7dd93c9ac673d',
                'hash_atual' => 'afb0d336a5dfcf184dee6da8e565e54bccd53343631e78ab663a3d7a628caaad',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'bf07dca4687f8008fc8671f3dec5a6775139a350262f6044ae9730aea209c1f5',
                'hash_atual' => 'eed2b2406887c8c88c1ef962db9567ec9486b75e4f79f75ce1de477e271aa1c3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '1a02296811878ed85dcb551bf44d6a2dc1e07cac4a3539402f54c2e99fcc32aa',
                'hash_atual' => '6b4a01e3472e62c80a83b70df28f2e2690a547ee59bf3b558884240b5cb98b85',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '3ea32f5f2a9d356d7511797d3427df7dbc27e5ed1898843b5df7a589eeb87ad8',
                'hash_atual' => '3c1264e645dc8b1ca1f93448965fc1e1eb8beb22f4e168ff6244a341afb0c3a6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '1a3c430e2158184d9f6074eb5b4bf25810bbbc0e784d6064de376ed0a7a45185',
                'hash_atual' => '322ee9b10c989674932e89e75e2f1cec702a1296f5949067440995b6e98e83bd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 49890,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '13e5ba5a0f9099d4394609e0b8040de4935cf755f7e1c91c50bba31205a61336',
                'hash_atual' => '6f5ea4812c4151f67c2abf1e123b06cd78d79a9a33cdf16575caa6c41dbb5422',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'c01f077f8601e737250f30fffcec2d486b1de0b3f92c52ee628d786f8c71ba34',
                'hash_atual' => '0db2a7efdf5083d0851b9ed18f89f578a4463e178d613eef7493f316caa5ef31',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => 'f5b7a1eeddb25ae2df9cebd5229342eeb0309d0e00da91380dc65733f7b3c8ef',
                'hash_atual' => '70666e0f3ce2ca57e78fa61bfa6d3659730868cc90ed4107c889c17c6492f9fc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => 'd02bf9ff3fc755ac3dc7d1b84e6fe5765a88b641b5e93c91a62601a7a4e553a2',
                'hash_atual' => '9dbaf10d811db70540ec1f7833a0ff40c847d0bc5f285c651b9de550882243a5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '2a5eb8aeb0ef8a09dfb5653a8990bc20f93641c0f335df92f51d1d022103e25f',
                'hash_atual' => '73a59f6418b94979c6deb080f4b658fdde9f12426f8b73724b05cd676db076b1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'bc8a95501a9dc2e02793cfb8f38f708c08773ddc316918c7beb54bbdc6aeaf14',
                'hash_atual' => '77075dd4adaa983149f3a713c931e665d25253549a540f2cf37dca156d76a4e8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '0ce9e3ea16677c5e12c86a4b5361f04b17afae8ff6002421cef5a33fbb93febe',
                'hash_atual' => 'babaa12dacd1a151175618a4981effd10df31eb9dceaaa43d4c9b5c573d41c1c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'a2157fbef9987d6705c7d4c18a63e3d6a7a918ea105441dfdd229c1c4d0ab9a4',
                'hash_atual' => 'ffab44e7fcd02b5aa59fb69ef041444120c0bd6c6c21e221d7b91fbdccf69d65',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'bc0cca2bfb58458a60d15eb2e3c7fe577eff6a0b89975dfde2a3a897c2e954d1',
                'hash_atual' => 'a9a4900e437e43bd77038eb23992045fe1cede5251db534ea3b8d8cb3e280e83',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '1bc3bb12f0f06205c99c629c594ad0b6146a95ec887b9f95bbb8c8c6116b410b',
                'hash_atual' => '67ce3fe69278c3366e0131dd1b5d815112a7d15cded49c2185043cde6239e33f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '1d576f0f75e0a4ca3a48d54dd0f87a5efbbc46c449bdaa3b437f0a3a00241238',
                'hash_atual' => 'dfd3eb2d34deb538db2ab4e535401eddc3a10ef1a2926ff869ac83336fd9e256',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '1889cb1a2f63c15f8496d057e1e3185a8b6281cd3566ea3a2e231c440e09ff80',
                'hash_atual' => 'f05e830e90de15aaa6f8fe10ef1015d3a11acf38645e46a9415d1bc232808306',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '4236405b640c1e5f822c6aa8d1caf0d3c12aedb260504acefbb09a8494d1004f',
                'hash_atual' => '8b0f7a46be749532eeb55eae71b96d3e2a7ced6939554c15639779880eff6a77',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'f3484e0ecfff196eceb14c0d9dca73a2c6cc6bd1dd39d0bbd2021082b7034129',
                'hash_atual' => '094289aa5f988e6a8acc5319ac794ef41a7e2c588a6394949a0dbcfc00d46a06',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '91ad146fcca823cbe977dcaadd6a739fd35991ad66b97ff07d083ff78b72dd94',
                'hash_atual' => 'a773b8c245cc78e8c24ef10471e4bc2579cb1286bdcde349fc9287d1f446d482',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => 'a9268a912448f0f2acf4f230d967b561399c0d2ded1801e35620f9cb46302f04',
                'hash_atual' => '5566291ab5af5617f004bc8941673d59f271ff8e83776aaaaa7ead6f19dfefbf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '7d3d0c61a3fdd7396110b595aa8b4a2a95e71d97b077a7304bd66de24cb91d62',
                'hash_atual' => 'f11f4f04a5b2feb0d8b657b78027539def66cdea380e5a0b35a308b91b057df9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '6959df05da670f71895af53e285a42dc421b59692776fe65dd5f5d1be612321b',
                'hash_atual' => '39274e9e18a6ab523e4b0e801b95ce463f8fde4f6045c76a4803aa971038244e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '6f18548dfa6f5e53824bdaef764448b1e21ca106def5979a897eed55e90e14e0',
                'hash_atual' => '793e82b3801357ef88ba12726773c49dbf41ab663d4f5dd38176b48ae4cf3f8b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'edcfb093e018536fd41111335ad69908e80326c4c9d05f7082df60f758d6cef0',
                'hash_atual' => 'a4cf0163e0e6ee4763ec13bdccddccc18a3c87fbbe42ed70285a094c47bfe8d5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'b15c72768f6514a08c0e15b324238719c8d32f2be7838e8659299f154803fa85',
                'hash_atual' => 'df1394b5ae3fb254b8d2215d96bd3e78d3813d1665312ed0edf932f25d6735dd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'f621bc5a196bff098b5d836217ca95b71cd1d423fdf84a8d1b77aa0138c934af',
                'hash_atual' => '22fbd66fabb994f451748f338d91a1c6f537e06331ee6b7ebee86cf0e50e3247',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'a4aa553330e75bbf8ec629e30ae466545794e54594cbb7baf011e5a961c919ff',
                'hash_atual' => 'f178bbb89cc6ef34b20df7193a807028af9a80f09b8b1b815d27a88d87dcb74b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '62878020ef21d43019a092b9a209fcbefe57b51f02506d63849f194acb33605c',
                'hash_atual' => 'f3d769bd9c8b1737f1f41bb50f8724be5bec81d456d6dd2d9074248814191118',
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