<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 01:37:45
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
                'hash_anterior' => '01679204168eb027ab05cb911a4629be72c3aa344cdfde7143cba49c9b9d3308',
                'hash_atual' => 'b3edb7735532af6ced96b880a14830948d412137a3dea06d8501969d6b4563e5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'aeea01be18ed64776541fc9f26ca53cdb9b2a3462c0c6d3fa90034495d0d192d',
                'hash_atual' => '8a98b0d3fea0ff223579b7e690a9f1b91a08aa3ae339ca55cdbfc62249109bfc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '215188b5ef2c9a2dc0b00ac41f124ac230a84e318a090e644005667203ec915a',
                'hash_atual' => '9cdccc2ec5b97c49d64d70b02b1029fea6c2e8aeb60e93f8e2041a20e0a34fbf',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 188969,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '2346a6ff1c95aaa4b73a84b78bd77873d28f8d51e1b250b70987e4c408745136',
                'hash_atual' => '54c4ab5596bf117d37fdd3a56ca836417b9e3f20dc1cb9c90aa885879ce1f28e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '4ea1289263608965a1e5531471bb442ad34c35aab258342db421b59980ad32a5',
                'hash_atual' => '260ca590c75cbc9b4693cc274554f1d1b80f58e423eb74eedf327a596cae7f34',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '832622e7d03ddce5f6901794ae315457376d5610530c4e32b2a9707000406539',
                'hash_atual' => '565b6dfdf01836a3a2182f7c0f6a62bb24024918de1c5dc2f081aa261d4419ab',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'a8c131b3bb2f731e73938b417b014ba8bfe014601b8419bfc850297bf1e9be4d',
                'hash_atual' => '6f52fe981f56616e059605221046652b081aabe2b9107b649d3710789ca59cc0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '4b8f4aa7f60551a1a035367bd419b7c41dfa48f0ce4f0969073ce0de44dd881d',
                'hash_atual' => 'c028c40ac8c888a1481520736232e0e3bca3d6ff6f982c261dcc0b97e72219b6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '45dd6f5b2deb05af0d747008b6247994a4f92a0675586ac10532e07d6d5f5db6',
                'hash_atual' => '4cd61fb3eaa42bd2be46a31491aa7820dbaa6ed73e08f546b12ec1d86f0036cd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '8ab13380df69f218cf276e0a9f5769573d7c5032486eb147808ce8215b64e27d',
                'hash_atual' => 'f8d955312b73b669fe5234c9f6b199e2c4be49fbfd9b737305bae7e962bd4f75',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => 'e8750135ef2e22d6bfc2f39f08a1370f720d6a889c08145fc825c06e573909b8',
                'hash_atual' => 'd3162bbba8163b1efeac9c6635bfa66385c79d3dd423612cce8ce060a47241fe',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '3034bdea8aa3da799823f64008bd9f537317c1201832b9b9efeef421464cd505',
                'hash_atual' => '9b2c4a639669f3b006356a049d9874f5af8761d8a7a57f3e042900413106bbd7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '196f4f80a1f381dc4f3529c4d704e23747c72ebfe214e18b0779d4cbd3fde217',
                'hash_atual' => 'ec918deb54b95e495aa37582bc7a40e97d55ebb0c390afa5c4406c5f91f0e610',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '90ffffcd7fe4d0d0ed14a68aa2c5553234e9c124649727fca2ccc7fa034cbb8a',
                'hash_atual' => 'd3d70b28b3a81df5c197613665cb6f09141750e766d515ce9b0f55b596dea6d4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '8d65fd3fe0b5edf57a88075082ab57322b3364b6c60d92ce7be6894621312ca6',
                'hash_atual' => 'd75a6ad9a7094b47218603d4bd86c0a540adb66c745677f2e913735eeb4bb630',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '2671eee73f01c3cb50cc2dd259792a824e7bbaddf22966e6011b1ce8548d2e3c',
                'hash_atual' => 'aee6b5dcce238781e720d01a259b99f9ec4fbdad3f46ff2ab5d5ca50e40b0595',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '25a61da9d4b141fdef54089cde7029d67ff6002acf9f2b64679ada8c20820d9f',
                'hash_atual' => 'c37c2c674369cbfb3196c4b28aae2df5ae16c258243ba9f94625580da1d585f4',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'e44a59483fc3fc3da8496f9a1cf7ff556e4fe71f04396936ecab09e28503d248',
                'hash_atual' => '5d75ab9bd5e430a661fd102e8a487c920c04dca3e477bc7ac8b5801c954e55bb',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '2b1e9dad122d8f46afea8b66d92dae7ec14104acfd098214531420c7c5bf2ebd',
                'hash_atual' => 'ae6ccbbae73a611df9256a9942c09d2742bd6ad859f6e0ff2cdab6fb7256826c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'cc7ee479967af8b75394ae7071c368a9e6f7b46094cd81e438a04f47b8a9671a',
                'hash_atual' => '56fef83df6dc5f643ee260d6042a742ab55272b643d0c0bef99b24d4818a553e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => '64fa4c47f56a96676c70af171f2c7e40f816d1b8163198ea7c3129807f8b7dec',
                'hash_atual' => '08bee99fc4ced6bed6657e65e05d28482d5f190ac6773b7a9dbd92e725417652',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => 'cdf3ffe9af12bf997aa748186cd175d37789b2222096580ebab3241e5a2d2a5d',
                'hash_atual' => 'a09e9cd399db6cea7dea6d0a4ec6e5085ecfc3ef417002e753a520e542f0faf9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'f638fcf2516f788e8da17bf64f44235652a4603db68f94fd6271b02cbde60bb2',
                'hash_atual' => '6946f95f8116bc34a5f95f735e1b5dbce323a3c15537bb0700dfc0c41d0c9fe3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '455b7ccf6f8eaaa7e5afd54138f356f33c91738614b3bc8fb4c9368104150c7c',
                'hash_atual' => 'b7599376102f0a3a4d07227ef4735c95e5faf9f8c4b3aafd00e6f70971de1b80',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '603a4303856e513583fe6e60a507e73d708a8eb2f2e9662110f81b7b4914113e',
                'hash_atual' => 'faff0a2e02c48233b7c8261395f4931623767ae123f5b2c376c84e7f1c748ad6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'b0075871ed2aba6e5afc66dc935656ff3cbb39a9c9ff2d1a6ca0d3bfd49b110c',
                'hash_atual' => '8ddac80fb073431edbe198ea36aa8c44f1881e03b899885682ebdb7fb3e7faa8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '4a522ba96cbf9486012d87bb4afb0175bba2fc20e9bb522942581fa100fb3a50',
                'hash_atual' => '97ca6a4f5d7b8696d617e15cb8b6004b99b26c45eec62eb112a6cd5154ef1c78',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'f510dd544fb5216161e204fd285bc9af8fb36005f4d10225cc8cf193653ceebc',
                'hash_atual' => 'aa66438ce8d2a35fbdde8bc119e24567749f8f0fb5383c70370c47607e6f2f49',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => '12548fd45c4d6256e4e5784020cfdba283f0affe207eddd7bbfd1f321691722c',
                'hash_atual' => '3030022a1b47b32ea36b3936c414792457a90bb41a27ee9095cb60c9d2760095',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => 'a17739151f07e89502f68f89afea2f5636d7b76251313f307c304fd345c3944a',
                'hash_atual' => '86ab2f329cf66c7a86830ee85134ae4d67166ea229cd2035eb6badded290c57b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '3aecb39173f9f70a931812735f6dec65053d83ed5367268d54e8450d9def2f68',
                'hash_atual' => '58211046b2a933c45090bfbd8771e899f3294620f331e7d4eb0cee6f3957578f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '6a7b5c15860dce8cfea6074fe7374458a21660bc841171efbd96623fc5228cca',
                'hash_atual' => '620d36375a300a0bf68480b298c4c2bfa12c2ed7c2bfcfe8cd8a068bd4667f8a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '5688b068c2611be4cc8186fc8d0d9d54451119c0a76dfd901796c5831bc9d5d7',
                'hash_atual' => 'd838bfad0a01a870b69276d4b103a4b0b69ff3cfbd6e57ae0d5105f5b4a8264e',
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