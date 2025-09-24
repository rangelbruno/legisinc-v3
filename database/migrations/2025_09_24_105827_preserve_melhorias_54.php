<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-24 10:58:27
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
                'hash_anterior' => 'ba272f8a92a8b053ed837eb889ef2725c1962d517a151aa00f5bd9940c084eb9',
                'hash_atual' => 'fbf48ab5bf0a79274685f8ff3e3ca7c49a67e15d9d823ffa64faf5415d35ed83',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 199451,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => '0fbd70c4d17a9ce0d480d7b1f45ff3b0c346329122a9f0cd26435cc16985a363',
                'hash_atual' => 'c5cc57c487a474181a5124b6f5d24f312686b65ba0199696f035c440a1399113',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => '5daa6d0706ef08db1ab8cee0a35b889e435c4c347a5cf70b3bf52568f13b4a7b',
                'hash_atual' => 'dcc5e755e7f4b01d1b46d5273967598c1a52516b178ba7be0901885aecfcdbc0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '9d8cbc3d8830a5fe2198c7885a5d00a7ce2889e20c3830db2c01f852e4a357c9',
                'hash_atual' => '0ebbad191784ba4c494dfede738ad277cda61bc2f382514bdf28ae485974b588',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '2fbb04bde0061b2519b6544ee5afe6189323a00167ff45e78cf02610b73e369d',
                'hash_atual' => '016939cb8d231db89d8503252c15d13d284eb6300129d6e9e22c7d252f52f95d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => 'ad85fa29e10b412d2819b3b842e48c232b9e46574012a38b368ed6ee2e9a4f60',
                'hash_atual' => '435b05783c1b8dc944b4b74952faf230e09766f453dca867eeea5814f3f75b91',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19682,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => 'e7ab32fed81a47b419eba4835617f105d2f69516a3d16fe0b7e2aea89b23c8a7',
                'hash_atual' => '43512cc10a7aafa2de259447830337cdbe4f878322690968ff6648077f0a3ffc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11654,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'ac7af63dd2740facee676aa81eecc42dbe9fef91ead3db25161402d25f99935f',
                'hash_atual' => 'cee44eeb0e87c9e9dae2fcffff868aa25b7603b781356414b6603663a257fbea',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => '61e2d002b32113003c63b075f1f760dbfac4f4901a327e938169bac04cc44c8d',
                'hash_atual' => 'fb9b1925063e17c11b2eb1ae77b43f1a61a136c956facbaa36109542d40d4492',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'e6b2412d7fe710d47fa2ba393236b5a09c0ba8b001fa3148b75302f28695d4a4',
                'hash_atual' => 'd27994b7d1e58f9ceffd0586518d0a3628464d2ce15edc298de4b13eba9f9c4d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '0a6eb666866bd268c0dd4d74512bc4fb385ab03115d6029610e501dd6be05de8',
                'hash_atual' => '93864c8f678872ce4dcc39e9a457ac570931063afc5ebb0ff68a90c63d1180c3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '61843b8592031ae1aaa9416ffd1be33e88a20fc9ea1370452a8db3dd33047d77',
                'hash_atual' => '144cf6fb12cac4e5104e51163da46c507fe314309e1758803b4a374527f81042',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '0085f591b05d961b74a16fc749f65c26aef36323e3c3c36f6f2bce5b96a2916b',
                'hash_atual' => '5fc9af7d35529c3aac4f7d4b8591c23017757b4254ecd074cdfe70a05729b74f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '20856e768fb0eb8adb39c34458305429385633edd144e904e932cacc271cec6e',
                'hash_atual' => '7bb20ea63cb18f990c4f99124bb907926db20d753e71258f0f42ee310f8acb30',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => '74c7c7e29f3b0b7873f8195bf6c4d5f4f682b050a86357e3aa5fd1bfaea3debc',
                'hash_atual' => 'e6c0bf2128518b17acc7b5f24e2d80425a448b98e3f2e49f46fd53f5214ce511',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => 'c016e470ad85527ba2d64ad968389ed64e5921b046f7ef5d5f13aaada8a73f97',
                'hash_atual' => '0eeec5c2323552f2098aef40a433391dd5148a949859e4a912228ac901b6cbe7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'a0bcf7db546beb39a30e633fac989d7b2c2b84e446bec4de1dd935b0eb2aec32',
                'hash_atual' => 'f8651e0d39c31e29939a662db4a94db6fcd57da7140730eed408f0b7aa4f19d1',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => '795bec101f8eb0f1721136f044ef484fc94cd503ea9a8afc3e3114a15008426b',
                'hash_atual' => 'd47ab823e1dcb6b5427def02a8c496631033670c30bb8c75b1a8039a455bd7ce',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => '648052fb98bc472bb97d44b267c1ce10eee8febdbe7c28e6a66e4f0dcad17d24',
                'hash_atual' => 'f1b45e2f4a88eaa8e21e1fe9ef93314ad9238879bc23b9c4086996f93b41e021',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => '343e0be5d1b775e77a3e716f4c86ccbe05292b40931bbdaabf66968efb11232a',
                'hash_atual' => 'e03de02f96b49f1555e0f4909fd7d42f7ecdf8e0396f281b5507a57435c163f9',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'b16d74ab0ee9d13065b48cedd1769b832c9b618e00e731df882c780fdf5f4434',
                'hash_atual' => 'aa029442bd19b9942f78c207b610694f81d805c625c8bdf6a3174ce48ec08d64',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '619eb7c5b67bd9895300f05e8da5883cfd36d47086d238d08748ee2e3e246429',
                'hash_atual' => '62a481c47618c5270fb2003e4fa44dfddacb51538ed948732981b2791ab6ba40',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => '47cca5ca99f94bf42c0e2b7f1e1eac592573b5c898da4fb8923e8077e84276cc',
                'hash_atual' => 'ef1b14071b784efa20545a9e6bebd2b5f3df9c57fad22b97a6cc5408b5cf5a05',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => 'a97290a5d475dbbda1c773edbb201169705b85ac2295eec28b96b444edef1f60',
                'hash_atual' => '01a090c84c8ed6fdd6771b8f3123f140f3ac6bbc696787395d881f586db58049',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '467ff9044cafddec2c941aacda7193f6bf63bf7e802b928a4450dd8760f69705',
                'hash_atual' => '5fe2f94825a31a41e9d904f7a1082a95242bd2669feb13a9bb5b0fe317e93b83',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '3107d4465deea2cb11a99ad2b853ef0da0a024bc67fab1b03eb5412cf6bf7293',
                'hash_atual' => '43e8e8557ddfe000ae0e099c888188c3728a55730a341ceff6db7cc3faef7137',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => 'c8949a1ff3e87e554f2fc6ad234819fab7802676d1401988b7f1f397f287c6a1',
                'hash_atual' => '8059986eba8da73b4e0921703497f5ad899b75e56ede0a53cd65d016d09c4f45',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => 'e27918c0bc6a11917b901b4aa33c1ea1a2072e261a34966896f3bf12cdf1954f',
                'hash_atual' => '06225f024c71589b8de981500cda982dff9f7ed0c65f7d7480d2eca5b898e74a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'b0847e9cf1a41e32b783c6d2ba27dcc415f42e30ee7d477d2cf3a087297ff2d7',
                'hash_atual' => 'db8a9d59f7c471e9e6c456650fa38d46efa5c950d4b7c7be3ad52d552f90f624',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '7ec2ee2a744c990388b8efcc6cedac4b701f92759936c4d18fb3df6a51449826',
                'hash_atual' => '8aae0944b10769cae6fc93c054407038e22468102793032649c77a217f2b0384',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => 'c5076b364840bee2d688a2a7502ec0739ad9458687b7fd6a930ff461e82bb376',
                'hash_atual' => '988656725be720bdfea51096a35b4022d97dc7d818f2b24350a34ccfd08ade66',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'cae58547f4346eee9bc3ab9becb64d887f409f59f3d2685c7a5d1a6af71894ef',
                'hash_atual' => 'bcba508b11a74345ae05f038b0ad78c11f000ba999e7242f19c261f751253e81',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => '31628c180d42e461a06687c5d3b87aaed5cc14e6bc1a92ccdefd0184a15faa0a',
                'hash_atual' => 'b4c199a77b68af11e2382f6988673eb79ad155440582ca6b0bbed2fb22ffc5e9',
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