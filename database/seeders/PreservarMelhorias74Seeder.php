<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias74Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-11 00:05:30
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "0e2014a188707d94feb1ec2399cba04013db8a9284252d4551c5bb8ce5bbb6a5",
        "hash_atual": "83b517499b81c82ab245db0a722a8750446e87418e8d7e10f5f69975308f19b9",
        "tamanho": 185055,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "d7d015b10e563bec1ca471bdc226716288f6c973d620b39368e572ba0a58f68c",
        "hash_atual": "61b832694a53c1ca68b39ac4eb698ea981272be661ec37d101ffef5eb2d77850",
        "tamanho": 33929,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "4d3539b3b611059c7fa041142e99138ab9d2117590fda8df2f2cb1563b0dd7dd",
        "hash_atual": "a6a2b97adbe55006d0466313b8635e7dafe58cb336e061b37f29bd9e8d7c544f",
        "tamanho": 184884,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "f79aca9ca366bc7553f3d9bd4863a0b2eff49169af13e65f97df2f4e1f6d8592",
        "hash_atual": "fa5a2a5be7642756345e89907f8f2fbc9a65c98115e9c2d3723d66e0ee3d3238",
        "tamanho": 37954,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "df3ede6e8421ccb143e3344012b07cc157261cae5fa295ea4f8b6492ea95bdc6",
        "hash_atual": "2c11ccad861508456d7adb6fc37c81b44dbf3c203d7507952722f0e746beadf3",
        "tamanho": 16468,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "cd0cd2d6765d5de92a9fab59196652e5823935bf3ddde1f89a5e015f0e9b098b",
        "hash_atual": "9e449680d56faf88e020947228a87bb28f22763d6f3958305b55156bdfa3b907",
        "tamanho": 18417,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "59c882f4793d752d827706342dbb72ad3db77b18f8152e35558c584651d6da30",
        "hash_atual": "2573f7cbacb1954101e22309dc2d8459bf642fe8982d1bbde8313f5821cc4090",
        "tamanho": 11594,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bd094d3eca908057c85549833008bf9da9f0c188c82f17c418bcb03eb9d1dd30",
        "hash_atual": "37ef29116b27ba77090fd0d429441df9a044255f6510446a473e9e65383e5bee",
        "tamanho": 90333,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "64bb8588630449ae6a478a26e8609c418aca6bb936f82492ef234d4bf4dcf41b",
        "hash_atual": "4ab213f23103a1775b7cf6178b56a49519d121eb51814a95a88221681070687f",
        "tamanho": 49890,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4f54d3d211498b2288be657d7c0d48f0c46b63b2fb86a1b303901135a66e7ecd",
        "hash_atual": "a512731e9a2e990e9604bb31770b134c95c33d4d1a094fc65e8e14f4efc1d3b1",
        "tamanho": 64199,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a89d570ea898950aa9a88a2b5090393d139b53f35a25b6ede62e429e79d5d17e",
        "hash_atual": "764bbfeb10d6168523a6351085635d25753e854e75c92c7d30d83cd820b6525b",
        "tamanho": 21668,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5e709e3c9000f0345000a7cc3f6639b905b92cba33dfacd0d7c843325d5c0429",
        "hash_atual": "1e8f5f14ee6b2837d8cf01d93df177d71eed9800d7c1bd2c29ee000b6fb49177",
        "tamanho": 39431,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9ddbce7cee92d3d46d23f803926d589a34e8293a4b1287dab04be3c0f5b35589",
        "hash_atual": "121f0d54d4480c7bb9f1b766f9042847f20648eebfaf13a20a256da3c008e8b3",
        "tamanho": 9714,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a77bf7264ccdaad2fd4fc64007bcc09a6bf4df021575c90d48ce4db057d618f7",
        "hash_atual": "ee1f1c3d78116c9885122a04402e216393ab54eef4ba87477c3ba43446a9952e",
        "tamanho": 2116,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "775157bd63e88de8a1fdb6fbb3d359cf0d956e30b73d3dc8816cdae7925cbd24",
        "hash_atual": "d1f6176808866cec1aef54f9083ce287e267eb8aac46a6ca27a3e3be002b07d3",
        "tamanho": 8438,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b5d131fc02c969ade49883890d71653091d7cf00aaaf0c90246b0b86eee0d6a0",
        "hash_atual": "1047c884208961dba47d4e4dbf90820db8daa8153e68da05f72fea826a88c318",
        "tamanho": 19647,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "03defe8d76faff0b38af5e1f8ac233c576fc9a2e1def559e59abd8118b104d6f",
        "hash_atual": "06ded980865e588e1526acff202ad6a7337faabe26e9beb171ed2f0103138382",
        "tamanho": 18651,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b0dfd256c26f2d1d6e78fef6f1776baf1325a2edf269ef6f5c1be0fb20e1089a",
        "hash_atual": "30d9f9420ea52020db954b7c26b45217b050fbcd6e21d98758cc10f58b014748",
        "tamanho": 44459,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "de2ba709927a018b91d25a5317bb1202cf66bf631f251b4ada30bff36cd49315",
        "hash_atual": "7c0a7211dfee1941fd7e50ff9f0ad267b6a3e8ec712eba9a33342dbcc8891194",
        "tamanho": 1169,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8b43ddec96c3d67db03ec013dffa3ce3a37021e074adce8bdc02dfffd6eebd5c",
        "hash_atual": "3dae2a5319f2c30a2b4c24866ae39c4e26602b3bbbba47ec8fa0c8514ff64616",
        "tamanho": 10124,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3added813224560c402afb85ec8923449199770d0ddd4690c856a237e0453f0c",
        "hash_atual": "8e397417ca7e8f6e861919c5efa831fbeb5e566a380261078a798b82e1fad743",
        "tamanho": 8297,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6c89709969818be005b1769b97f58ad2ffb27807de7e6162837948cb7e6cd19f",
        "hash_atual": "c6044de9a3ea10b786a0fd91ce89a908aa830becc4202eb03a4e9f5331da12c7",
        "tamanho": 8524,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "30c8646bff9815a6e30d7e311b663e47d9da5d89701e1b97bcc296ac4e38815c",
        "hash_atual": "89161cb709aea21467c5b07bcb1e0a00b356173d59b4a06b1bb7154ea8a43f3a",
        "tamanho": 29449,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "210293aa46a52846cde0518d3561b85f4abf49bbfc90a1bfa3cae616f8fb0df6",
        "hash_atual": "2975bd895a7ef464a68795879a4d84a5e0e85301d162fe660aeb8032c58ee6f4",
        "tamanho": 10070,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "74645cedf823a49adab5681ed099e8fd305254813f90ecbf70b0dc3c7dffd8d0",
        "hash_atual": "842116876863b6bc5c4113695dc4444b3f4bd876d5cb913f96fa68a0feb38788",
        "tamanho": 6219,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c429b31ebc680ae1e25aa9c5afe0e90a88a29ef6da2c1784dc2b4728eccc79ac",
        "hash_atual": "3898e5f1093b70b4c1c5a965dc51e21c6bc4f591af2a9dffb1fa25c40ba7ca22",
        "tamanho": 7208,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "20384c0623b988cc5d15b686277e4537a9df9ec61b8f030dde934adfa8724f97",
        "hash_atual": "89b4ad67bd970f7517dae914f187f2e2ca8f8961e5d1add91b95b156148894bd",
        "tamanho": 9296,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c401d7dc17050b39a322e2d126a8e00ae9ff84e4a37821f7df9488efec97b746",
        "hash_atual": "35b1504b4de51fdffb762a48ee72a80ea6f8bb88d49c3e0ebb0effa751bc871e",
        "tamanho": 20506,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e3e30b92c8b221a2004837bd17cecd01eabd7369d6882cf7d418c42574d71fbd",
        "hash_atual": "f9b26a67027ee8899d2fddd7521449274d644bdc29f1d9de4472502dfbaa0c8a",
        "tamanho": 59888,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1cebfc7ec59d4b747a478e643746c10f7dbe7fd7f1463d0fc5832b3ae32f23f6",
        "hash_atual": "30d2a820da3bbdb4b24ad95683fef846bd2677d4cd94853d7384f2fa4893f7d0",
        "tamanho": 28604,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1aa4f86636eb01dcbd91e72add6683c72d784ed901b91ef34daf3dd7551596b9",
        "hash_atual": "aa3365a801545c06d99c3e422abd345b2b94a4e3b173d1fc5fae7b11bbcc34cf",
        "tamanho": 15343,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "68a0182fd1b9387484bdefc98737f131fae8a8d2328350fdf61c98a30949a99f",
        "hash_atual": "0ca55dad7f3f80ed7ae3f0214daa26d0d8a6508df2d1b6e28230b4cc0323a1b2",
        "tamanho": 26051,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "047e06edcc9ac88cc3cdd974611cd8cc4ff91a73fbc14a0378998e81ca0638ad",
        "hash_atual": "32b97c1345c9db05c48766f5112ae480e65315e53a26087e66b3384389c8f3d8",
        "tamanho": 25889,
        "modificado_em": "2025-09-11T00:01:59.000000Z"
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
            
            Log::info('PreservarMelhorias74Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias74Seeder - Erro', ['error' => $e->getMessage()]);
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