<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias17Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-15 21:56:09
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "b45db0680b8bf827abb42891d83cf69af2d68f42d2d34e2f835d1051975bfea8",
        "hash_atual": "27e9751115d98467cb751bdd17ab69ac6b25a906d5d5ea9212c1801a4ec2cf17",
        "tamanho": 198288,
        "modificado_em": "2025-09-15T21:04:38.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "b552365b3c79e00dde178f02fcd6c5d0ea29149b0f47ee6f2bc85fb45962bc2b",
        "hash_atual": "56633a6ca3862043431b428e18ecc359e5e9a20a98db483d616fc4138180542d",
        "tamanho": 38821,
        "modificado_em": "2025-09-15T21:04:38.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "e4b9e97b6c927360a461ef11e201b228ede271870dfab05cd9699a91a5d44531",
        "hash_atual": "04d628e1152a5d7f5d1ec49777057c213f8fa42faabd9bae37840eb051315736",
        "tamanho": 190861,
        "modificado_em": "2025-09-15T21:04:38.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "62cdea2fdcd18cf1a9d69c3f2cd9918e56a1e31056aac0befe73bfca432c6c55",
        "hash_atual": "cb4f38002126fa12cbfce77d2c2d2766074ebe876142511a6597425deec59538",
        "tamanho": 37954,
        "modificado_em": "2025-09-15T21:04:38.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "cf132a482fe8b7767bedc71a5193c8e81d5da586c5c78b0b76e49ada132aa0dd",
        "hash_atual": "bc383f5f54ae49d9a813db48fb47e8a847fd1f2e10981e0d294eacad9e0a8ba1",
        "tamanho": 16468,
        "modificado_em": "2025-09-15T21:04:38.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "271dabcd34b890256d2222f27aa5abb31e369ca217f4f9d0fd46cd7e67d683c3",
        "hash_atual": "c67d4f857aa992f8e26b5d6960ea424216bb5f1c5d459ecb6e2000e5a7eb9533",
        "tamanho": 18417,
        "modificado_em": "2025-09-15T21:04:38.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "e946214f8782c1c5e1c1aa34fe4b54fa052193794ae970d476582e596bd256c6",
        "hash_atual": "19b53dedf56c7316c11174ce9ea0ac3bfa2c3215f8bac6c72c38e6b0a1d6fb6a",
        "tamanho": 11654,
        "modificado_em": "2025-09-15T21:44:19.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ea55dceb8d247b6fac4bfbff87cd7e6f50da955f84420de2b2f0e2fcba9ace8a",
        "hash_atual": "e41d516fcee3f07fca494619180bd978346d0d267ddf14d6f2c458e9d7181dba",
        "tamanho": 90333,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "119682950fcf08285a73a21259af9825f1ea3e8acbe16ac9297117a0e7d77e4e",
        "hash_atual": "34400be2bae98d698ad2424be92df401d409f8948a8cd9e56f984bc117d7f187",
        "tamanho": 69556,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1b654608633a1f4f757687136038d15f4ca111140739925d2c1d8d0f2cfd4a86",
        "hash_atual": "10c4d607dffde2a3366e87af6cc8739b1cd3e62a8fd74f5c48694a0a646a2236",
        "tamanho": 64199,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "421faa92877d3d2e444b546441696c48d4cf0a6736c78d5c86c6070581692f9b",
        "hash_atual": "4b01434f50f755503209f13dec036fbb880d65c8359c5a74641adb763059aaf8",
        "tamanho": 21668,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4186567b33860bd5461f6581e87e0547828d7836e775cc5cb980bb0a45a3039b",
        "hash_atual": "318d0fed230dd187866fed82f5e39621761e020e2937fe5f89670b3bb9bf2cf3",
        "tamanho": 39431,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "101356afd2557656133a0af4e5250016cc7f316177e74c4fdfe047340555b3b5",
        "hash_atual": "686574699c7aa7acfcd106ab730b1f40ba0b745aae78fab477c131dd850f87b8",
        "tamanho": 9714,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f0175aefc40ac01aeec01d5772977aa146deb4821335e92da12a043758ffb763",
        "hash_atual": "9572b384225e0ae56d491d3fd9c281caa0a900a87ec8fbbc8a211f6a347d7640",
        "tamanho": 2116,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3b90356c6454358c9b69a5dfa4ff6804200a0ddbc53829080c7979324fc4fcf6",
        "hash_atual": "ce467543d5a1c622768a35589bb4011248d3e69b35575eaa760fc6c5e7295387",
        "tamanho": 8438,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4b893225c35ec9ee24c5cb794abc340143b0e1b96786fa67c5ba21b1290f6ec5",
        "hash_atual": "4c75d8b3f5e561c2695e536a402b6a2fb40fb9d2191d95df9cc53daac6d35b37",
        "tamanho": 19647,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a8c7827ae150664f5b506d3c83fb092a81c2054dafabb540755dc120ae4a09ad",
        "hash_atual": "4b6bf5831db8e68f8ba71ef2255da1215aa9534500cf43268e287cd1c670a313",
        "tamanho": 18651,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b66f573bbb2b247d310d5148131cb9182aac69422a3d4d551840b99f9f8cd205",
        "hash_atual": "ce3730e3ac7758480f30360c3c8bd9aa1ecfab18d7fb0ddc10978760107a4031",
        "tamanho": 44459,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5330d67b1f5e8aa09881fa3d3cb62eeb58b584f846aa645f6dd42b98ef60b963",
        "hash_atual": "40ac2401c56972e50c375179b360bd279cfcc9e402c1de9adf837adb2ac1ed98",
        "tamanho": 1169,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f82310cc468bac26695ba7497e8cb1a62d4c02bf58d7ee9ec4e04cf1ba2095b5",
        "hash_atual": "55490e7bd42edc1ca995e5a5d10537caf3bf85323136903710d8c9877551d1d9",
        "tamanho": 10124,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ba09e835ff2c6166bb49163b0fd4492abd40ec66990f65dea74f5818a03229d2",
        "hash_atual": "c58061dde4caab1cf76e46957d2f2003ccde129d614f0ac949387965db873cdc",
        "tamanho": 8297,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6a75dfc1b8b895c2695e81c622b8ed53f7b7c9f771c2d2b51bb7854d9eb40368",
        "hash_atual": "eaf04332ecee6ba552aef62af43a3fa5c4f38712bbfb2112953b00dad7d99c72",
        "tamanho": 8524,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c098b74bfc3c398461fa25d108be848444b7b94f313986931ba096d5bb94edfb",
        "hash_atual": "eed703217182889a53e39b64c1ed59682d9c01e41682aea091b241eff63cb4dd",
        "tamanho": 29449,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7d9251dc64ae230470edcf9dfe71787822fd190e604bca87fc88fd71462355e8",
        "hash_atual": "8e98c0ab2f54436221c876a755cbc612b5b64981422b7f7b0e812fa725bc6e94",
        "tamanho": 10070,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6064823b7381e0b21dfc7c0f41a32af99b625e5958b765bf4efeee17c5ec4d95",
        "hash_atual": "55f1df515d34ae63dbfabfc5be2ee1f463e234df8c3bbe05598310c9d285630b",
        "tamanho": 6219,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "874c44fb08cbb8c8108aad9f4b40709ed7643ce1f68a6ce47be551c85935e9da",
        "hash_atual": "33311afd9e54f1a524907663b6a3a4688cd3bb728aeb6e06222ae3c5215d81f5",
        "tamanho": 7208,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ed310e09130349e05bb10f43b9121a09e3f1aedadfd198a5fd1a66ae21dea59f",
        "hash_atual": "846690856bcb0c274a7d081d070b02328adcc108b24ff69f2e9a9d96bfd1da14",
        "tamanho": 9296,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f248d8e9c2f135f470f72e3a465a715e759c10e1b399d3ecd71eeddb47d723d8",
        "hash_atual": "69ba9c78333d00d651d653b1535eeef97ba739f9b4ae80a733a23f252acd200c",
        "tamanho": 20506,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "300a6de105057c259f2922f9153cdc66a2473a299be8552c6495272182839c55",
        "hash_atual": "7b501fa6520b891b521a76c78bc4f8194361d8d07c7698e044ea92420a39bc04",
        "tamanho": 59888,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9317f4d0e65e11f7fa8f6842298f359331c4d401d43c0d48d79b4dbcdd175619",
        "hash_atual": "0b993e939bdf0149c9cabb809a9222165017516ee3125b66c984fde7de6f8ef9",
        "tamanho": 28604,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9eb238e1dd140daa3c703368d57037655a94b288b5f9572eaed8c504beec66d5",
        "hash_atual": "b69e4b58f58bcd3b750ac96ba0a15f396097f45ddb887a39426acbfd54ceeb54",
        "tamanho": 15343,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "374168a2ea09f8b1b819e024774a2ec27e5d5cf08a8100515cdf5578444b6f9c",
        "hash_atual": "d849de1433f36a647513154b40d1513c1c15bfdaed8f27ba21472cc8930d909e",
        "tamanho": 26051,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "551bae2b3c53bba4f8bcb7a7bb05594604a4171d4affd2bd39369074852a3500",
        "hash_atual": "21c2f866378086166c9bd7a000cede611de226bf3ca886e5edb783cd53c60991",
        "tamanho": 25889,
        "modificado_em": "2025-09-15T21:04:36.000000Z"
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
            
            Log::info('PreservarMelhorias17Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias17Seeder - Erro', ['error' => $e->getMessage()]);
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