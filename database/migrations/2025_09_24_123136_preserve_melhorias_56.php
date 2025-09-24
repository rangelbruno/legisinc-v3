<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration para preservar melhorias
     * Gerado em: 2025-09-24 12:31:36
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
                'hash_anterior' => 'fbf48ab5bf0a79274685f8ff3e3ca7c49a67e15d9d823ffa64faf5415d35ed83',
                'hash_atual' => '41ae368cd06bbe788892d1e9508738067f99d453c1d50e03cfc2d3e2a3f57a6b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 199451,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Http/Controllers/ProposicaoProtocoloController.php',
                'hash_anterior' => 'c5cc57c487a474181a5124b6f5d24f312686b65ba0199696f035c440a1399113',
                'hash_atual' => '8ad3bad5387aabc51c048f7b4c3363e491a9ae415a7c2b66b157b4d29704780b',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 38821,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/OnlyOffice/OnlyOfficeService.php',
                'hash_anterior' => 'dcc5e755e7f4b01d1b46d5273967598c1a52516b178ba7be0901885aecfcdbc0',
                'hash_atual' => '7ae6244788b2e4315b8f4c531c2c6862308d862a8f26c5d609e5636098dd5ee6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 190861,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateProcessorService.php',
                'hash_anterior' => '0ebbad191784ba4c494dfede738ad277cda61bc2f382514bdf28ae485974b588',
                'hash_atual' => '9eadb56b71e6ce35a72b98047680e21b06e1f536f8c71d2387c00eecb622bc06',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 37954,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Services/Template/TemplateVariableService.php',
                'hash_anterior' => '016939cb8d231db89d8503252c15d13d284eb6300129d6e9e22c7d252f52f95d',
                'hash_atual' => '17202168ccec3269e05a2f4b3d4cfdb89f1668af1fbd144e22d3889b5cf3452f',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 16468,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'app/Models/Proposicao.php',
                'hash_anterior' => '435b05783c1b8dc944b4b74952faf230e09766f453dca867eeea5814f3f75b91',
                'hash_atual' => 'da66c4532b12a3f5e5dc80eb96da86045a7fa004f4b4d588f5cd7317f6083559',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19682,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'config/dompdf.php',
                'hash_anterior' => '43512cc10a7aafa2de259447830337cdbe4f878322690968ff6648077f0a3ffc',
                'hash_atual' => '58c2ee65d87ea9e59464fcbba13271703c1feacf6938ec6b618a7a36c05fb8e6',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 11654,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php',
                'hash_anterior' => 'cee44eeb0e87c9e9dae2fcffff868aa25b7603b781356414b6603663a257fbea',
                'hash_atual' => '85f67d345cd1cce09f4feba67b4054aa8d520833be4fb56c8f1eda8a274eb772',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 90333,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar-vue.blade.php',
                'hash_anterior' => 'fb9b1925063e17c11b2eb1ae77b43f1a61a136c956facbaa36109542d40d4492',
                'hash_atual' => '97279a0983a0f84d2b4cc2cfcc06c00632f2b793e99721d3e0de6de9bbc0f1d7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 69556,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/assinar.blade.php',
                'hash_anterior' => 'd27994b7d1e58f9ceffd0586518d0a3628464d2ce15edc298de4b13eba9f9c4d',
                'hash_atual' => 'dc359c72b2748e0a0b76774552a31ddf2bd5dcb77a7ac78f2f8107c5d45eaf87',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 64199,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/historico.blade.php',
                'hash_anterior' => '93864c8f678872ce4dcc39e9a457ac570931063afc5ebb0ff68a90c63d1180c3',
                'hash_atual' => 'd6aa82f87b810efa8cd65977f66d0cf72f559442937e2de77f94e19a93fedf20',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 21668,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/index.blade.php',
                'hash_anterior' => '144cf6fb12cac4e5104e51163da46c507fe314309e1758803b4a374527f81042',
                'hash_atual' => 'd36742d3b0253bf9380cebdf4793a243f080ed30d114ce3a4a157ac1d9b2fb80',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 39431,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php',
                'hash_anterior' => '5fc9af7d35529c3aac4f7d4b8591c23017757b4254ecd074cdfe70a05729b74f',
                'hash_atual' => 'b51acde22d4524806801a4142fa191f0f830c80ca14a265bd063bd40766f0fb3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9714,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/nao-encontrada.blade.php',
                'hash_anterior' => '7bb20ea63cb18f990c4f99124bb907926db20d753e71258f0f42ee310f8acb30',
                'hash_atual' => 'a7468eb430c73f32c6e5c3a7a58b774ff5642204d9a615cb3d231562c640ac6a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 2116,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/consulta/publica.blade.php',
                'hash_anterior' => 'e6c0bf2128518b17acc7b5f24e2d80425a448b98e3f2e49f46fd53f5214ce511',
                'hash_atual' => 'f42c897ed17d1f50cc94d8ba6b623a5686d6ff04c6d5c29ca3f23450b1259292',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8438,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php',
                'hash_anterior' => '0eeec5c2323552f2098aef40a433391dd5148a949859e4a912228ac901b6cbe7',
                'hash_atual' => '1a2154601bfd77dc8a0ba4796f4232d1f549455e608a34463f668406f099ada5',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 19647,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/editar.blade.php',
                'hash_anterior' => 'f8651e0d39c31e29939a662db4a94db6fcd57da7140730eed408f0b7aa4f19d1',
                'hash_atual' => '9cca88ead14b4a3543ca22d10937906f56e1d5f42c4ffd933dbe008c56ce0e75',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 18651,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/index.blade.php',
                'hash_anterior' => 'd47ab823e1dcb6b5427def02a8c496631033670c30bb8c75b1a8039a455bd7ce',
                'hash_atual' => 'a357499adb8c983370d9cc218b79c98c74366ff0be544bc3ec311532551a89ec',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 44459,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php',
                'hash_anterior' => 'f1b45e2f4a88eaa8e21e1fe9ef93314ad9238879bc23b9c4086996f93b41e021',
                'hash_atual' => '0e3baa800626da12f7c53e7e427f02aef597cea81f293a167abbecaabc5a291a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 1169,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-dados.blade.php',
                'hash_anterior' => 'e03de02f96b49f1555e0f4909fd7d42f7ecdf8e0396f281b5507a57435c163f9',
                'hash_atual' => 'cf465849144b7b3db0942414d38467f0f8dee58301d65a2883f7bf1be85a1f4a',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10124,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php',
                'hash_anterior' => 'aa029442bd19b9942f78c207b610694f81d805c625c8bdf6a3174ce48ec08d64',
                'hash_atual' => 'ab614b625376cd13180fd7efc29011d2af159de564e1408eeb81a1d5158c852c',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8297,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/relatorio.blade.php',
                'hash_anterior' => '62a481c47618c5270fb2003e4fa44dfddacb51538ed948732981b2791ab6ba40',
                'hash_atual' => '6f4672484fbb179c862e81903e408887993d437eb60c6a2886cbb281050233fc',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 8524,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/legislativo/revisar.blade.php',
                'hash_anterior' => 'ef1b14071b784efa20545a9e6bebd2b5f3df9c57fad22b97a6cc5408b5cf5a05',
                'hash_atual' => '319aa4530eabb668f1c64a3364d5664d3dc4b89cce6b8670f22be6d7f0ea2b38',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 29449,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php',
                'hash_anterior' => '01a090c84c8ed6fdd6771b8f3123f140f3ac6bbc696787395d881f586db58049',
                'hash_atual' => '78e4a1df8c7f3bc0b56f97baeca887a80390929e41bbc0e8373f3b3ea2d5c1af',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 10070,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php',
                'hash_anterior' => '5fe2f94825a31a41e9d904f7a1082a95242bd2669feb13a9bb5b0fe317e93b83',
                'hash_atual' => '6e844a489bd960038bda8a8ac9bf8225f94a5ac90e6da694dbda459de57abcc0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 6219,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template-optimized.blade.php',
                'hash_anterior' => '43e8e8557ddfe000ae0e099c888188c3728a55730a341ceff6db7cc3faef7137',
                'hash_atual' => 'c8e63103af3034aad42e89670d307fe2c5bf008817667e23debd06386da86ef3',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 7208,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/pdf/template.blade.php',
                'hash_anterior' => '8059986eba8da73b4e0921703497f5ad899b75e56ede0a53cd65d016d09c4f45',
                'hash_atual' => 'ea44febe76588c4c232e7c17dc6e41229a12a28d81e0ea1c2fe7cdc39ed75be7',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 9296,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-melhorado.blade.php',
                'hash_anterior' => '06225f024c71589b8de981500cda982dff9f7ed0c65f7d7480d2eca5b898e74a',
                'hash_atual' => '89eae9f31d782e269cd378f66724c71d0a73e2a3d416dc431c266a923a29439d',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 20506,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index-original.blade.php',
                'hash_anterior' => 'db8a9d59f7c471e9e6c456650fa38d46efa5c950d4b7c7be3ad52d552f90f624',
                'hash_atual' => 'c759051f29c98094c6469c41175276bd5dd1492791ec7040c1cc1ae2b31628b0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 59888,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/index.blade.php',
                'hash_anterior' => '8aae0944b10769cae6fc93c054407038e22468102793032649c77a217f2b0384',
                'hash_atual' => '7ddc45f481b1ffd55f071276604f61566e5fb63834455288ef45c31677ac3f64',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 28604,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar-simples.blade.php',
                'hash_anterior' => '988656725be720bdfea51096a35b4022d97dc7d818f2b24350a34ccfd08ade66',
                'hash_atual' => 'c6f78c4b407e650af3c9673e1524d6285108a68162f04d6cf12a8e9d5e1b4f21',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 15343,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolar.blade.php',
                'hash_anterior' => 'bcba508b11a74345ae05f038b0ad78c11f000ba999e7242f19c261f751253e81',
                'hash_atual' => '6adcfa58e32cfe87add37d48ac10c85478fc1f1e067bf3cbb60a852d0f052df0',
                'tipo' => 'modificado',
                'metadata' => json_encode(array (
  'tamanho' => 26051,
)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arquivo' => 'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php',
                'hash_anterior' => 'b4c199a77b68af11e2382f6988673eb79ad155440582ca6b0bbed2fb22ffc5e9',
                'hash_atual' => '87eaccce9840c04b2a9e4268cc1d6956f812d805a6c2cecb7049e34f9da88cc3',
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