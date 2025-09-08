<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias53Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-08 06:35:34
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "23173fd08b27ae0a1642cb3fe0e0841f48324ccef8d13386ca7e1859d1890b1d",
        "hash_atual": "7ec40fb45cb03a0aff72ed6ec97d015d56d6ca51e4be264d6ae7c7c3adaa945f",
        "tamanho": 183240,
        "modificado_em": "2025-09-08T00:26:09.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "ee6c1d25c1bf7bb46984affbae4782e53ac2ab5949256540e3cd2bba4363e3d9",
        "hash_atual": "88fbe79dbbf88a81f7a421c749a46913caa42b8efb74fa141e61327b65680535",
        "tamanho": 33855,
        "modificado_em": "2025-09-08T05:14:55.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "ccd3049d7080abcc33d9371b102078f82bffdaaad060e7dc0f0de3f01a7ac9db",
        "hash_atual": "c26b8cee593dff3c3868445a152ce31aa123f4c8881cce4c4dbb73db17bf9cfd",
        "tamanho": 184884,
        "modificado_em": "2025-09-08T00:26:09.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "f9bf53bd020c0aba0a835dac6e36e35479615d2370a2c0ac0ddc8183e0f92841",
        "hash_atual": "caef0c91d4de68a4996acb22d9da057d10639d99d9299bf0cad3f1d10a9a5a38",
        "tamanho": 37954,
        "modificado_em": "2025-09-08T00:26:09.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "ed561877e66c16f0dc2a65a4e1ab99e4c81e214e580880e1fcd0b55e6400601a",
        "hash_atual": "fddfda600e22ddd5ca9758f4a6b8981d9ac63f31d51c3969a888b63678ae21c9",
        "tamanho": 16468,
        "modificado_em": "2025-09-08T00:26:09.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "fe2d9e486556a6e75dff187a17134d6dcb13f6bad2c1c47d65eaeee9b3f6517e",
        "hash_atual": "97e932f8f0209d66e57d3983e359bc0e7266aaaae5c161e631c053bc27aa0e9e",
        "tamanho": 16728,
        "modificado_em": "2025-09-08T00:26:09.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "29114ccc19043caaf59511b03cecf24f6724c00600c269ac2f9fb80e9e99e3fa",
        "hash_atual": "4dd1018ab7a2373074b3836a94fe944ed4f86181f21511faff12b33601c1645d",
        "tamanho": 11594,
        "modificado_em": "2025-09-08T00:26:09.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4dd27756222cfa939e731a20bd364a1a1b8d78e7504e15ebab4790b041e66f92",
        "hash_atual": "773411e32af24115d092947d34db717317569d355804d8a2f09fbe860cebb732",
        "tamanho": 90333,
        "modificado_em": "2025-09-08T05:14:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4293b913c15cc6837039fc8bb0a5425e1b690768254d01123184428967a42aee",
        "hash_atual": "2f5724a3fd24b60d21bb7feb4f820508a57e8ce73d624f8cce07a11218998da1",
        "tamanho": 49890,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a008815ce3a73ffbf875999f8ea0c76d2d52ea40d2c4d7ccdf6e311d881f7bf9",
        "hash_atual": "42cc5a815a3b028859035338a5f015f4fb42893fb81dee66ad90afc3861426e1",
        "tamanho": 64199,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3e4f3528f5aab947b65427c6e06ec37a62bcb6065ab87eb9a3a1917bd6d19aa0",
        "hash_atual": "70ee31fa552f016739cb36d702740b4d529d95e6d921ad68d6fd6ce43421dc2b",
        "tamanho": 21668,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bb1a91b354b7ba44d08b71514c8aaa3abe8cf4d5432bc7b23393da3921e5eb46",
        "hash_atual": "aa0ead1d101ce14cc4f049017b3d6431092b460958b8efccf7c10a18e0571198",
        "tamanho": 39431,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1523db328673ceaf2011139557896a196f1da3e5d7a640edcdd055651e663038",
        "hash_atual": "6b5a456f37ec7bddc13ac60f0209862a383f92be3187a0353619f3c2d97287ac",
        "tamanho": 9714,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "45acecaa986102d43e189d4661c73fa3aa326a2d8c755bcacd87e0a2761826fc",
        "hash_atual": "544a9b7bf426368340ac2619914ebe589afdd53cf8db30ee67daa0e0e247bbfe",
        "tamanho": 2116,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c426f231ba17662210bb247b6ed064f1b81ca4e4c195da1faaf6661af9d40edc",
        "hash_atual": "aabcee7ea9490e94c834820d47ea5fc991f74872eb2fcbaea8698d2ce04b006c",
        "tamanho": 8438,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6526d68568857c5c34f37efb3125cdc09a15011dbdcfb0a8d1730464ba36bc55",
        "hash_atual": "90bfa91c52c4fad47802704123dd9d1fecb8042a47b0a845ab774ba282cfcc50",
        "tamanho": 19647,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1a41bd533ba2107c005ad6100856ed919db4c9c3a16416b2321e41f0cee7cb25",
        "hash_atual": "7111b750d5747800aa85df7a3aeb229fc74da34d4bb280b18f8423c5a87090a9",
        "tamanho": 18651,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6881be8e7d865762465b3be9a6b2f6a6612b0adca5eec85a541dd8e07445f52a",
        "hash_atual": "a9409285bdb1050e1c5400d205d4519c60cd4e021f5ce4ca0cdcb1c1dae0df5b",
        "tamanho": 44459,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d72d892eec8333ef8e31e6b8066fc1a053053596c25ae032a2b25ddcc39b196e",
        "hash_atual": "ae4edfe5c2319a427996cdff2f84203acbd07fa08cfde9772ec6cf5ce6d82d91",
        "tamanho": 1169,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "568257ca7d40f740c0d10cf607f1cd3c91693108e3b6094330d951f8ad0fbd90",
        "hash_atual": "7c82deb231a97e6c150c40a30cf35b0c13406d0311dd7c53d9bec551655fde4f",
        "tamanho": 10124,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a29fa2b44d8df6c408319958858582a82199dcaf9e62eaa79b3d443227a2b960",
        "hash_atual": "b65cf154608c473c4daadf2c7773c263ca9f5a31cad57b81ddef753381870eaa",
        "tamanho": 8297,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b73ecbbf96a65453fd96051101a185fb7dc28d4dd8690a029daa584f0c4d1877",
        "hash_atual": "2528578d0f3cd89d471c2c25d8492bdae2a64aed77b21a8ca58df7ca4067b467",
        "tamanho": 8524,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fe385180f985bcf5c1d84fbd184e25a91e35ff0a5403f24d53e9882e234a298a",
        "hash_atual": "80c5788e3735b48b989b960127f3335cd937ea4398ab599a271364938e7ca84d",
        "tamanho": 29449,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "47d8ba2d9e856ccce39530608c9f2cfe9d2d00bccb61c51460d4f9252293df5a",
        "hash_atual": "84ef3c0e0cfd55454d878f65b4bf185dd836e560fb4e85a97cae05405fe190ee",
        "tamanho": 10070,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b70be5a27e91cfc36a878d0dfef4b38aaf888e741bf6ca1b46e7fb5b70e9d91d",
        "hash_atual": "b26443f583fad592cc61699cfdfc7021ac9c2e1881fb744f9b1d2c26912bb2a3",
        "tamanho": 6219,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4eda1ce44ef749a2f8dc27eb7f98bacb046c6101fab61ae7fe22e48aab03af64",
        "hash_atual": "2ed3ac7db06268979f8df0b3aa76eaadce9c3fe33be39b0580160674f4a69fb2",
        "tamanho": 7208,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4491682266a9997cb950c8cd6504f7650178faccab38821ba500a1c8bcdf6acb",
        "hash_atual": "b54f6f1eb874dca4869a170715c4adff5993b81c1ecefb64ec452a1e114ab3aa",
        "tamanho": 9296,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5ebeff407bfa0969e09b63826de87ebec2a4249e9e724c19c36c6ce4a603882a",
        "hash_atual": "e9c7a9a6072d8399cb8a6fd494e0fbfc4b4e3c3f90ef480edced03e7eb937d87",
        "tamanho": 20506,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4b3e75013e1df5b7444e7c022c08afa789500d8234e30ec709f99bd7ca73499c",
        "hash_atual": "c85abeb187a9c0ede93ad22be93ebcaaf22c7ad941cfc4b7db2a0219e14a4ed0",
        "tamanho": 59888,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f61910e534000b99171560096cab979906c129e979fe5f39548a257e72b4a248",
        "hash_atual": "ac6ff3a9f16b647fd926c33cf4589c9a5751dfedfee7a2991427ee5edb744683",
        "tamanho": 28604,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3dea67f5dc5ff3a3a8c4e244e28119dd3c3b9ccb638f7bc21dc5358f8b99c8c7",
        "hash_atual": "1f2b9f70444a550bc903ffc4c33cb467b67b2611c5158725ad8a24a2c70da5d2",
        "tamanho": 15343,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "92433bfe3363b5b957aff70b666bccd43bea6a4d7a94aa2973456f6172516596",
        "hash_atual": "8e004d5f4013aa2939bf44fe69c75301bc098c9d2a7f1470b9104464195184ad",
        "tamanho": 26051,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fd83b8bc286dfb32890637dc22d3238e88aa53d334c3e417313c410c25ebf3f6",
        "hash_atual": "c42bdc92c8ef76ccc20ebd7b7e98fdf9d7e005518b7f92e6dcdbc68bea85009c",
        "tamanho": 25889,
        "modificado_em": "2025-09-08T00:26:08.000000Z"
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
            
            Log::info('PreservarMelhorias53Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias53Seeder - Erro', ['error' => $e->getMessage()]);
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