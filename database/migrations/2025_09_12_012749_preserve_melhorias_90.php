<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-12 01:27:49
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
                'hash_anterior' => '4cbeead9da82617e179459aa067b8f0fe80bb48b11e2d5cb54fff601cb5fbac2',
                'hash_atual' => '01679204168eb027ab05cb911a4629be72c3aa344cdfde7143cba49c9b9d3308',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 194828,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'da9df0e367a8d5bce199bd3181a6f4e32390b4221cb8277c92cd4f5e065cbeb9',
                'hash_atual' => 'aeea01be18ed64776541fc9f26ca53cdb9b2a3462c0c6d3fa90034495d0d192d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '6a6cd5bab03f5a694ad096c1c1b4fe325bf0d4c9a0bc47963aab39180f8c06b9',
                'hash_atual' => '215188b5ef2c9a2dc0b00ac41f124ac230a84e318a090e644005667203ec915a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 184884,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '6a823abbad90f90d36eef4d8b11cad92c309c9125d18636715732651ff8feb17',
                'hash_atual' => '2346a6ff1c95aaa4b73a84b78bd77873d28f8d51e1b250b70987e4c408745136',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => 'df98c6e024a42e6742398427806fa877c452f22524d2eb229e7401916bca4272',
                'hash_atual' => '4ea1289263608965a1e5531471bb442ad34c35aab258342db421b59980ad32a5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'dcc969738e648ee865de12c8f88aae5e197d6e08ae17e9f3c4093cde6b649a04',
                'hash_atual' => '832622e7d03ddce5f6901794ae315457376d5610530c4e32b2a9707000406539',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18417,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '3941b07fa1d40502b7b2ab705cca9194871ce5e92144c6c29f74a9f29e07d413',
                'hash_atual' => 'a8c131b3bb2f731e73938b417b014ba8bfe014601b8419bfc850297bf1e9be4d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11594,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => '639940bb5eb10c7dd7c166d05021667ac94cae6fd4b989709f9ae93a1e613d6b',
                'hash_atual' => '4b8f4aa7f60551a1a035367bd419b7c41dfa48f0ce4f0969073ce0de44dd881d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'fbedafebd2ce8757484618a473219fedc38bb3bdecce18a02d07c247c1a968bb',
                'hash_atual' => '45dd6f5b2deb05af0d747008b6247994a4f92a0675586ac10532e07d6d5f5db6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => '4cd27c4b609634657c5003a3edc49c314c0a548a6aad868b15fd8d33fdf9d90c',
                'hash_atual' => '8ab13380df69f218cf276e0a9f5769573d7c5032486eb147808ce8215b64e27d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '1f613a5141ba553a757e1f315a55caa714170c9300355c8e86ae0c38e6230b90',
                'hash_atual' => 'e8750135ef2e22d6bfc2f39f08a1370f720d6a889c08145fc825c06e573909b8',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '73f2133d8214c90851e52a6790a3b007053d2869ebfbf609856736a670cb1bce',
                'hash_atual' => '3034bdea8aa3da799823f64008bd9f537317c1201832b9b9efeef421464cd505',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '80998b87dd8f4accc52f91b170b8dd6488981018278204b0d5f6e295abe5d377',
                'hash_atual' => '196f4f80a1f381dc4f3529c4d704e23747c72ebfe214e18b0779d4cbd3fde217',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => 'cf08c2e3ed6b6d7efaa723f174936bb2460215ff30e354e504c3f62249fd413d',
                'hash_atual' => '90ffffcd7fe4d0d0ed14a68aa2c5553234e9c124649727fca2ccc7fa034cbb8a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'fc523585caaadb8222fc3b32b4e0dd47a91b5465f6c8d61c4dd1cad1ade2f7ad',
                'hash_atual' => '8d65fd3fe0b5edf57a88075082ab57322b3364b6c60d92ce7be6894621312ca6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'bc4c7436d723a1609e0a93761a35272745b80f2ec0725126019b43d235c299da',
                'hash_atual' => '2671eee73f01c3cb50cc2dd259792a824e7bbaddf22966e6011b1ce8548d2e3c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => '6351e9fbdecbf5742b2b49220f12d8b00a0add28957d36845613b2ea2427ce69',
                'hash_atual' => '25a61da9d4b141fdef54089cde7029d67ff6002acf9f2b64679ada8c20820d9f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '364f24efcfb3b79071b51fbff7e48d5f7bc23e59970cf95e76caebd181c1c6c3',
                'hash_atual' => 'e44a59483fc3fc3da8496f9a1cf7ff556e4fe71f04396936ecab09e28503d248',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'f2a8cae112c674a3bbde6d19e6f562229395fa85dc56d961a0365d67942ef4a2',
                'hash_atual' => '2b1e9dad122d8f46afea8b66d92dae7ec14104acfd098214531420c7c5bf2ebd',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '9d9ec5fb9b0e90902bbb9c1899e8848ade11db4e06838b2f09850d4f7bce2b44',
                'hash_atual' => 'cc7ee479967af8b75394ae7071c368a9e6f7b46094cd81e438a04f47b8a9671a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'c6dbbbe79df1c067dbad42846464a5855e8aac4e84bde0fb8a0accb67546f2ac',
                'hash_atual' => '64fa4c47f56a96676c70af171f2c7e40f816d1b8163198ea7c3129807f8b7dec',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '5af02029dbf992b29011ad13636891cdb9c259de7f6cd291c94689e1f05a2dca',
                'hash_atual' => 'cdf3ffe9af12bf997aa748186cd175d37789b2222096580ebab3241e5a2d2a5d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '15c7bf354d9616adadef5d9ef05ebdb290881777c2d40637114f5a7a1928bb79',
                'hash_atual' => 'f638fcf2516f788e8da17bf64f44235652a4603db68f94fd6271b02cbde60bb2',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '6296b94d62ab795978ac71596c0f8ab5785074324f0ed4ca6ffe1e9817f99c69',
                'hash_atual' => '455b7ccf6f8eaaa7e5afd54138f356f33c91738614b3bc8fb4c9368104150c7c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '5b53d9410474ef668bb9579034e90a2d05315c0708807c47b85dc2587f32ba0f',
                'hash_atual' => '603a4303856e513583fe6e60a507e73d708a8eb2f2e9662110f81b7b4914113e',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => 'bd0f83822f23c5a38e2d06f2abcb53a19d56c85aed58f1100a60564061544342',
                'hash_atual' => 'b0075871ed2aba6e5afc66dc935656ff3cbb39a9c9ff2d1a6ca0d3bfd49b110c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '9300eab29cce46c11fc8f9620e8c8c30b8bc0cea07e109f04ed9475eeddb2f7b',
                'hash_atual' => '4a522ba96cbf9486012d87bb4afb0175bba2fc20e9bb522942581fa100fb3a50',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'bcf86ece5e6c3a60191152344c57ae97bb72371ed1748b0be426a0167e5548a8',
                'hash_atual' => 'f510dd544fb5216161e204fd285bc9af8fb36005f4d10225cc8cf193653ceebc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'e017804d220f352c749b8bdc8456d5e7eec4edad0e42698c4a53178593533dd1',
                'hash_atual' => '12548fd45c4d6256e4e5784020cfdba283f0affe207eddd7bbfd1f321691722c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '672ae21f6102549ca668c554b102fb83fa29b742ba5ed2f1f0e123e3444a68f5',
                'hash_atual' => 'a17739151f07e89502f68f89afea2f5636d7b76251313f307c304fd345c3944a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'd82eac471400aa7b97f75c4ed3cde1475f12762e6e226b2cf43f914f0630b99d',
                'hash_atual' => '3aecb39173f9f70a931812735f6dec65053d83ed5367268d54e8450d9def2f68',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => '83cec2eecec3d56e0c402bff8510dbb67168675a62ffa943d008cf4c39294ca1',
                'hash_atual' => '6a7b5c15860dce8cfea6074fe7374458a21660bc841171efbd96623fc5228cca',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '602c006e5b1080a740da16a5e4aa7f0934f041a2768d458a1158ca15f62da70d',
                'hash_atual' => '5688b068c2611be4cc8186fc8d0d9d54451119c0a76dfd901796c5831bc9d5d7',
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