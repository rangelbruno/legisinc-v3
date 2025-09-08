<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias43Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-07 23:32:37
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "1c981965642d8dafd95707a1531ca0e5f9bd334724c622f6e262138472b94f8d",
        "hash_atual": "5615a277b50301a115e7f57feb353ad2922f0649fc3bc7ad49bf0006acf9e307",
        "tamanho": 183240,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "3b30c97e8e3b4af9cc15b61a1a11315e091db5b1e21f3caf8370540a1a35e253",
        "hash_atual": "fbac509a161608805b46670ed1e068da5a243d2d2ecdad53460e48c3158e0e43",
        "tamanho": 33855,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "03bffa968972b37808b644a8f07af5887dc2fe929d26e29e3f2ec36e25b46e00",
        "hash_atual": "1347fbd6b0c34b8c387d4c9bae90127a0fc0d4da80768148e88303b7f76cc431",
        "tamanho": 184884,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "d58cae51842bfc90caf69ba493dff1e449590a729b4e2826625e68cb6a7725ad",
        "hash_atual": "c5ffb7188dc38e68a2b2a3823242ea410beee556af7724195d53f297f893c2d8",
        "tamanho": 37954,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "0e7291d280f8b686cd8a72e2d13f0448d01e76c08c1fbe6c202f2e650e928f98",
        "hash_atual": "bef7ac60b00ec25849ff12a39e472710c87bf435ccd8fa0b712a0c4a47fe1400",
        "tamanho": 16468,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "69be3d75502bfd1df786e60a49ef055602360b82e83cebaedd952c4aa7928bb8",
        "hash_atual": "634c91ae1a7d67d7fa5b1499aaf0655c0609c3997567eecacb5f68b78d596678",
        "tamanho": 16728,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "84c4c3392dd392a8813a5611fe619d4d089cc97e01d4b1712cbd3a09de346cda",
        "hash_atual": "dc1964050301b9440eb48b05ea81a6c132c8bde1f2d673c130062e80fe9be1a6",
        "tamanho": 11594,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b0cd11a657e890ff63348272fe0958747dff1b0101c99456f7b8f5575390e37d",
        "hash_atual": "cd6fe9ca5846744b815181ddb0eabcd1d97088d41879a67fb27fe927cf895000",
        "tamanho": 90333,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "024d124dcd8b3ed9506e1d7560dadd06b5dc891f4cba8c8eec91ecf02cff681e",
        "hash_atual": "c77beed77bfc7b36962ee3e29de6a3f4df79ffbaf3d37779e9cc0ea91bee94b0",
        "tamanho": 49890,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "69ee26321ee42dc74b9b96c9ab267f5aba39058f5af36a0f8eea2943d3a33fed",
        "hash_atual": "c72238921aa20e6236df5f362d6d3858cc09c042f6865a4e6b00130fb5cd4aba",
        "tamanho": 64199,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "dd0b83c9e7215425c5fd6bf7f8d82b4cfdb82829d5006ac3581460b75bb77d30",
        "hash_atual": "41c253bc53cb45664bf780641aacb1c2ee603857470951de32113239e4d1c090",
        "tamanho": 21668,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "426b06a5583a4db78955476e91d4cd35ca48be6537ac555eadcd43dfb6aa740c",
        "hash_atual": "e85e90d135942b49c505dab2d4fda9ae4307626db595a302704f66ccb3f2827d",
        "tamanho": 39431,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7541a3995403862fc94376cac8dd4c39416ab9a65f4a28ff5db48f91d5e8d3d4",
        "hash_atual": "cf43b89ee44f911403693a7b66a9630eb78e7175810fb226565bcc7a12248bf1",
        "tamanho": 9714,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "dde11bf5c9bc8fac6ef667ec487bd9f9c17f8630d11b9537585a3b01298245f8",
        "hash_atual": "b1915efe885aa53839ef8327d715c778f4cef687ad2dff9373bcf3375f31a851",
        "tamanho": 2116,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ecab9edf909253623d50fbaedb407df726a03060eb85b863e2066713c0e0e034",
        "hash_atual": "d05cce5b984ebc35e5a95d33bf0d699f9e205733d8e3a3dc501dbba62286de94",
        "tamanho": 8438,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2fd49b2a62749fa3e504a285e2f48788fabc9c0ecde01d93b476c8494b2fece9",
        "hash_atual": "730aed7a7e88f91148a3de33cf4f40380f37a15fc37e1a50848c35fb80a24a15",
        "tamanho": 19647,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2b853951668267f4efa465fd3af90e5bc854141d3fd131ab824b69ca404aab9f",
        "hash_atual": "6cbbe3fb768dd79b814ad8d6463f50326f33c570ee4914d5326c1861fa3f05b1",
        "tamanho": 18651,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "19306f7741d81a9b6f706b7c1a9ad9d3100ae6b19e26d8fde2ffad5abaaa3bdb",
        "hash_atual": "3eef0fddbe37cfea3d2a02d26def48de75177cef05cd0c60e748a22dfdad92ab",
        "tamanho": 44459,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ef59ecde8a63c95f27dbc27d33019fb193715f16b939878acef77cd6d6a4b69a",
        "hash_atual": "401ce4fa06a51f9d6bfd486bac7ff5a16204f684e3182e5972d0cdd48b485111",
        "tamanho": 1169,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8d185cc5f53685231b7c96b35556cc3464e69a2f0b43a01480ae6396cec5aee9",
        "hash_atual": "bb30cf449ee18b2593bf1a6ee2b42df11a793545d1548cb784b975cd36fa6b03",
        "tamanho": 10124,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "831db58eacf57102a178a49addcf8ea0e82e168aada7780ed5f527304be670e5",
        "hash_atual": "8d1d9dfdc83e6a86518b09370c6c77a91b9e6bc5f10ef200bdc8677b822d67c5",
        "tamanho": 8297,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bd401c9f6ef5ebfb968f3e30dcf33bbd132abb3093c42af95dc198a50adb6b53",
        "hash_atual": "8facf767c352e98bf6bb816971a45ce17c39a9d038056ae1152db89c8111862e",
        "tamanho": 8524,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2273bca92059ddd4b6e354fcdf2f5eb2293914976d973ad05f3495fca01e7dc4",
        "hash_atual": "a6691d73bb55b5f9aae3949c7f6d34c663f42f041c9e8b1fcdef71e93abdb5b3",
        "tamanho": 29449,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fc3919cd9c63dcddf907e607c9027869c7127a76807195c9182e2db479d67e52",
        "hash_atual": "a461c815a38959d6c64b8c178719ba834b97a0e90dea98cf42f71799ff5e3352",
        "tamanho": 10070,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2df52f823f0c8fb36866e0595932cd9b33c222bdd1128d76b83194e749fbcce8",
        "hash_atual": "37db2c4ffc368da17540d1bafe74858b54b053f617ac13f67a9850cfe64406af",
        "tamanho": 6219,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d0d4657510b1596338e9912560e9cf804fb54cf999a277a23d1a22a0b07dc47e",
        "hash_atual": "6e94ee765d2ef2604373c94f90f9c207e904387582d4f04c0275bb59e6201e96",
        "tamanho": 7208,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5b7e848a91457148d67634ba4e16f6bebf7c4746fa386ca792ecbdbbdb569148",
        "hash_atual": "b8fa17d64b0e9460782fea67c8840c7663c91c5306c362324e2755dd90b10678",
        "tamanho": 9296,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5e8cf1c090353bcff47808e5c51f2f78e321a8d5b7a3c7a185be4a69fc9990ba",
        "hash_atual": "833f6c3f7e806285653ad4ab9876db18d34d84af136cf48304d2ec97a2218ad5",
        "tamanho": 20506,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f19282b3ac9e693e4b166663463875fc610dced21f86533929005feea3d8df4f",
        "hash_atual": "fbed8749a21842493483bba063c95954711ab97aad962581efed6e6fcbb50e4d",
        "tamanho": 59888,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "707ca54bac60207df5ee1adbc3ad962269776baf23ce3147039edc38bda2e38d",
        "hash_atual": "c299a2ce7fb8618c49bc3e7118269a0318377d5d3fe1db2b799e5634c2db6f2d",
        "tamanho": 28604,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0efa15e1bd83fee23dd16054e4f8078621cca8222c742ce456445c216045c0e9",
        "hash_atual": "eb2511a2e275161e5efa4fbb586bac5773a0f7be004f1ac97e6cd7f86281bf3e",
        "tamanho": 15343,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ce34b7c460a9e1daf9024f8ca9b40f5d4138d00f728f2b19143b794fa46baa5b",
        "hash_atual": "0f8e1ff20c573ad3c632e8d1e92e4167d7d994593df1133948d7018709ae1b3b",
        "tamanho": 26051,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "21f0f4d0ae692affbce2e13375867c75dcab0a9e8f477ef448d4cdc6d2730018",
        "hash_atual": "014cc880f275dce96b1f8593de314867bf8ce4566174127f61f4d48e86ea060e",
        "tamanho": 25889,
        "modificado_em": "2025-09-07T23:32:23.000000Z"
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
            
            Log::info('PreservarMelhorias43Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias43Seeder - Erro', ['error' => $e->getMessage()]);
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