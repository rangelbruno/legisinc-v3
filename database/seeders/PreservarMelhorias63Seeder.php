<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias63Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-10 09:29:30
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "80ca49ba7c2126e638f28bbaee3e74c2821584f9c4f4af275d045dbdf8d4b5e5",
        "hash_atual": "de2a463fa1a9f9a277d3b4006538cddacb6fa47224b4f53b09d0d607f9787159",
        "tamanho": 183240,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "d620e36f0ba34c2a2a2578945442c89e350cdd8958a31bd88f41724cd91c7c5e",
        "hash_atual": "46b990deac66437de1a1daf84525aa0f59b927e979b81ddab74aa1f389add051",
        "tamanho": 33929,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "bc1ede98c89641ddb25cff0a1d6576efa7d75b986bddd22a523eadaa3cee3dc9",
        "hash_atual": "6d72d39e13000c1a1f97de009cb3fade625f86eee71a6cd8876ebae0cb4c20db",
        "tamanho": 184884,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "d9f37f66e441fcdfcba80b6f578762eda31993f8db9565a50ca4cd86d07c4585",
        "hash_atual": "742796e864ea27a2b5ef43408fcb2f9a378be2db29cda4b44bf28aef6ebcae94",
        "tamanho": 37954,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "afb0d336a5dfcf184dee6da8e565e54bccd53343631e78ab663a3d7a628caaad",
        "hash_atual": "cfaef99d4f17c0889ab34e8f4dc659032347b37c004fa9a3221135a79b9c9e34",
        "tamanho": 16468,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "eed2b2406887c8c88c1ef962db9567ec9486b75e4f79f75ce1de477e271aa1c3",
        "hash_atual": "90a7c09e18e729e05d4ebafac3aa4c1ef4c2c9917737d19316615b328e30174c",
        "tamanho": 18417,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "6b4a01e3472e62c80a83b70df28f2e2690a547ee59bf3b558884240b5cb98b85",
        "hash_atual": "7e9f732cfa6426dd1bb5e2db59094c6d7adf6795de7eb9938ad5310a82bcedfa",
        "tamanho": 11594,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3c1264e645dc8b1ca1f93448965fc1e1eb8beb22f4e168ff6244a341afb0c3a6",
        "hash_atual": "162e112caa8ca500eeb723028bf26e2ca5647933b39ca7ff9b5651be6ec3a121",
        "tamanho": 90333,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "322ee9b10c989674932e89e75e2f1cec702a1296f5949067440995b6e98e83bd",
        "hash_atual": "78fc1f7f5653d0a95d27368056b64a93f9648ab58e68f8b0411ede017d77a3ca",
        "tamanho": 49890,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6f5ea4812c4151f67c2abf1e123b06cd78d79a9a33cdf16575caa6c41dbb5422",
        "hash_atual": "2713894ff0fbf17da68b8cd6ffde9d3a179cffeee3c78dbeb58f14b143430361",
        "tamanho": 64199,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0db2a7efdf5083d0851b9ed18f89f578a4463e178d613eef7493f316caa5ef31",
        "hash_atual": "8f6fa807f72e7a8c7eb982d873af24a93a4fb2c28b5fb700a7ef95e6b3ba48d3",
        "tamanho": 21668,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "70666e0f3ce2ca57e78fa61bfa6d3659730868cc90ed4107c889c17c6492f9fc",
        "hash_atual": "1fd6d94642cdf018cc0531438623154eddda97bbea5d29327cf5239891e62dfb",
        "tamanho": 39431,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9dbaf10d811db70540ec1f7833a0ff40c847d0bc5f285c651b9de550882243a5",
        "hash_atual": "acc1c6f9f81e1282754945affc6a5fe1001216e4797ccefdb60c0406ea49f267",
        "tamanho": 9714,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "73a59f6418b94979c6deb080f4b658fdde9f12426f8b73724b05cd676db076b1",
        "hash_atual": "60c3d273f1416aee978a5c7aa12c24b9f48a88d3196f51494c20b0a7b5809d83",
        "tamanho": 2116,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "77075dd4adaa983149f3a713c931e665d25253549a540f2cf37dca156d76a4e8",
        "hash_atual": "69eaf39814c12492570d60442146b3a0e5863aea510919c53bb4d94f7641e95f",
        "tamanho": 8438,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "babaa12dacd1a151175618a4981effd10df31eb9dceaaa43d4c9b5c573d41c1c",
        "hash_atual": "79ec74c8db1bd311a6bab4f1cdc4609817da7eb70a09df2cf92bb29ca024fc8d",
        "tamanho": 19647,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ffab44e7fcd02b5aa59fb69ef041444120c0bd6c6c21e221d7b91fbdccf69d65",
        "hash_atual": "eaee2ded7a28051441e95319b54340c70b16e2a3cefb35572e4f694928e67c82",
        "tamanho": 18651,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a9a4900e437e43bd77038eb23992045fe1cede5251db534ea3b8d8cb3e280e83",
        "hash_atual": "6f169f103e0a9c80cbb9e46ce7614394e8cdefadb8a2e11b0c9a2ebf049c59c4",
        "tamanho": 44459,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "67ce3fe69278c3366e0131dd1b5d815112a7d15cded49c2185043cde6239e33f",
        "hash_atual": "40c9cf7a4808679a3c7ec38dd79ef000db87af4f541d7bb88d75b477a362f3cd",
        "tamanho": 1169,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "dfd3eb2d34deb538db2ab4e535401eddc3a10ef1a2926ff869ac83336fd9e256",
        "hash_atual": "a8b0b32949f86c22c8f2290b292208c64f6117b464f3ed7214c96afd9ce745e3",
        "tamanho": 10124,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f05e830e90de15aaa6f8fe10ef1015d3a11acf38645e46a9415d1bc232808306",
        "hash_atual": "a5c13348d777fe038ed804a2f4eb2c24cf194237179f63181e1c628e1c404857",
        "tamanho": 8297,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8b0f7a46be749532eeb55eae71b96d3e2a7ced6939554c15639779880eff6a77",
        "hash_atual": "b9da76b41cf8c7cf853239c21c13a5b4a0e376275c37ca4a53e5455ca52dd04a",
        "tamanho": 8524,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "094289aa5f988e6a8acc5319ac794ef41a7e2c588a6394949a0dbcfc00d46a06",
        "hash_atual": "c49c73d874feeefce07e74803b5427b0ffb1ced2f66d13a62d970e151335f8d1",
        "tamanho": 29449,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a773b8c245cc78e8c24ef10471e4bc2579cb1286bdcde349fc9287d1f446d482",
        "hash_atual": "d616783e87238c8867ca293ca0f4c7ebbda886c1c61c76fb4b9081bec2a63d3e",
        "tamanho": 10070,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5566291ab5af5617f004bc8941673d59f271ff8e83776aaaaa7ead6f19dfefbf",
        "hash_atual": "370c20f2e4716e58b6468f8c66080db4b6e9fc617cf9d92e36c7062b53bc8418",
        "tamanho": 6219,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f11f4f04a5b2feb0d8b657b78027539def66cdea380e5a0b35a308b91b057df9",
        "hash_atual": "00ad8a8ebd811872af8b93e43432edc628d924555b4500999a18cfd56f2ffc76",
        "tamanho": 7208,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "39274e9e18a6ab523e4b0e801b95ce463f8fde4f6045c76a4803aa971038244e",
        "hash_atual": "20d483346410b4c005f770ede9f56daab475706cb7d9d81c02bf165b5b703b36",
        "tamanho": 9296,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "793e82b3801357ef88ba12726773c49dbf41ab663d4f5dd38176b48ae4cf3f8b",
        "hash_atual": "858b429cfbca537c57e1ab7dc18e6e473a3ef266a191743581613c78eec46ac8",
        "tamanho": 20506,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a4cf0163e0e6ee4763ec13bdccddccc18a3c87fbbe42ed70285a094c47bfe8d5",
        "hash_atual": "eb81186fbf895f6c53790a01879e02feef583bff0bef4e09940d5f715a6cfd73",
        "tamanho": 59888,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "df1394b5ae3fb254b8d2215d96bd3e78d3813d1665312ed0edf932f25d6735dd",
        "hash_atual": "4d4246f0d253b835ff8302350c4a73d5c30bfd0ee6299ca1141c06b4ec650adb",
        "tamanho": 28604,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "22fbd66fabb994f451748f338d91a1c6f537e06331ee6b7ebee86cf0e50e3247",
        "hash_atual": "89cf153f3e470ade8f24ca545f09132fb415970a2027aca7fe1824e35b0cbca6",
        "tamanho": 15343,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f178bbb89cc6ef34b20df7193a807028af9a80f09b8b1b815d27a88d87dcb74b",
        "hash_atual": "7d7ddb2c89296373f45bb5e460335d201cebb20d51e20683c42595b4ca280194",
        "tamanho": 26051,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f3d769bd9c8b1737f1f41bb50f8724be5bec81d456d6dd2d9074248814191118",
        "hash_atual": "314b38a75eae2c4b4520b33ad4f0b7e8fdf467b0bf8855dc71fe5b2792ce9b75",
        "tamanho": 25889,
        "modificado_em": "2025-09-09T09:59:41.000000Z"
    }
]
     */
    public function run(): void
    {
        $this->command->info('🛡️ Preservando melhorias detectadas automaticamente...');
        
        try {
            $this->preservarArquivos();
            $this->validarPreservacao();
            
            $this->command->info('✅ Melhorias preservadas com sucesso!');
            
            Log::info('PreservarMelhorias63Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias63Seeder - Erro', ['error' => $e->getMessage()]);
        }
    }

    private function preservarArquivos(): void
    {
        $arquivos = $this->arquivosPreservados();
        
        foreach ($arquivos as $arquivo => $backupPath) {
            if (File::exists(base_path($arquivo))) {
                // Fazer backup do arquivo atual
                $currentBackup = $backupPath . '.current.' . time();
                File::copy(base_path($arquivo), $currentBackup);
                
                // Restaurar versão melhorada se o backup existir
                if (File::exists($backupPath)) {
                    File::copy($backupPath, base_path($arquivo));
                    $this->command->line("  ✓ Restaurado: {$arquivo}");
                }
            }
        }
    }

    private function validarPreservacao(): void
    {
        $arquivos = $this->arquivosPreservados();
        $sucessos = 0;
        
        foreach ($arquivos as $arquivo => $backupPath) {
            if (File::exists(base_path($arquivo))) {
                $sucessos++;
            }
        }
        
        $total = count($arquivos);
        $this->command->info("📊 Validação: {$sucessos}/{$total} arquivos preservados");
    }

    private function arquivosPreservados(): array
    {
        return [
            'app/Http/Controllers/ProposicaoAssinaturaController.php' => '/var/www/html/storage/app/melhorias-backup/app_Http_Controllers_ProposicaoAssinaturaController.php',
            'app/Http/Controllers/ProposicaoProtocoloController.php' => '/var/www/html/storage/app/melhorias-backup/app_Http_Controllers_ProposicaoProtocoloController.php',
            'app/Services/OnlyOffice/OnlyOfficeService.php' => '/var/www/html/storage/app/melhorias-backup/app_Services_OnlyOffice_OnlyOfficeService.php',
            'app/Services/Template/TemplateProcessorService.php' => '/var/www/html/storage/app/melhorias-backup/app_Services_Template_TemplateProcessorService.php',
            'app/Services/Template/TemplateVariableService.php' => '/var/www/html/storage/app/melhorias-backup/app_Services_Template_TemplateVariableService.php',
            'app/Models/Proposicao.php' => '/var/www/html/storage/app/melhorias-backup/app_Models_Proposicao.php',
            'config/dompdf.php' => '/var/www/html/storage/app/melhorias-backup/config_dompdf.php',
            'resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_assinatura_assinar-pdf-vue.blade.php',
            'resources/views/proposicoes/assinatura/assinar-vue.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_assinatura_assinar-vue.blade.php',
            'resources/views/proposicoes/assinatura/assinar.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_assinatura_assinar.blade.php',
            'resources/views/proposicoes/assinatura/historico.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_assinatura_historico.blade.php',
            'resources/views/proposicoes/assinatura/index.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_assinatura_index.blade.php',
            'resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_assinatura_visualizar-pdf-otimizado.blade.php',
            'resources/views/proposicoes/consulta/nao-encontrada.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_consulta_nao-encontrada.blade.php',
            'resources/views/proposicoes/consulta/publica.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_consulta_publica.blade.php',
            'resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_legislativo_aguardando-protocolo.blade.php',
            'resources/views/proposicoes/legislativo/editar.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_legislativo_editar.blade.php',
            'resources/views/proposicoes/legislativo/index.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_legislativo_index.blade.php',
            'resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_legislativo_onlyoffice-editor.blade.php',
            'resources/views/proposicoes/legislativo/relatorio-dados.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_legislativo_relatorio-dados.blade.php',
            'resources/views/proposicoes/legislativo/relatorio-pdf.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_legislativo_relatorio-pdf.blade.php',
            'resources/views/proposicoes/legislativo/relatorio.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_legislativo_relatorio.blade.php',
            'resources/views/proposicoes/legislativo/revisar.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_legislativo_revisar.blade.php',
            'resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_parlamentar_onlyoffice-editor.blade.php',
            'resources/views/proposicoes/pdf/protocolo-otimizado.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_pdf_protocolo-otimizado.blade.php',
            'resources/views/proposicoes/pdf/template-optimized.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_pdf_template-optimized.blade.php',
            'resources/views/proposicoes/pdf/template.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_pdf_template.blade.php',
            'resources/views/proposicoes/protocolo/index-melhorado.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_protocolo_index-melhorado.blade.php',
            'resources/views/proposicoes/protocolo/index-original.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_protocolo_index-original.blade.php',
            'resources/views/proposicoes/protocolo/index.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_protocolo_index.blade.php',
            'resources/views/proposicoes/protocolo/protocolar-simples.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_protocolo_protocolar-simples.blade.php',
            'resources/views/proposicoes/protocolo/protocolar.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_protocolo_protocolar.blade.php',
            'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_protocolo_protocolos-hoje.blade.php'
        ];
    }
}