<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias73Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-25 10:28:30
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "eb888666a3d1497953de02437db280b01bd657df93f1248e87afb7b41b7c47c3",
        "hash_atual": "c35554aaee7aad1e311a9190ded28fe75b0faaab1a89e70ac6ed209e7da02332",
        "tamanho": 200514,
        "modificado_em": "2025-09-25T09:46:19.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "b18bae31014094823e1fae11a680c82d33f04582d63cb025bfcd2f15dc6669a2",
        "hash_atual": "e5ef1e0478fd1e51b1c71f9c319bbd71dff0f0c3838f5446d41b11afec6b6f2c",
        "tamanho": 39773,
        "modificado_em": "2025-09-25T09:46:19.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "8429478de02706869cbc108d191d22ca2f1ad58bde301436619c93151b378284",
        "hash_atual": "50f2169c87d153c00c34cd2d5844a9a63c846518037492a5247aa7dd8dc6a4f5",
        "tamanho": 190861,
        "modificado_em": "2025-09-25T09:46:19.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "46a2ab2fbadf65a9104f002cf5982666919bf31d8ad62e76ce1fe14aaced13c9",
        "hash_atual": "f8e8614b5b14b3a80a76b8e7f7ebb5823422a1a344168283d0ccac30107aaeb3",
        "tamanho": 37954,
        "modificado_em": "2025-09-25T09:46:19.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "7e0713e1fe84e1206fbc16afed9e60bfa0319de48c4c4de36321ad039ca6d6e8",
        "hash_atual": "8bdb46d1244efb28fb0a9056e1461827df97e8df5715e4dba929ec19151d486e",
        "tamanho": 16468,
        "modificado_em": "2025-09-25T09:46:19.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "aff1ae023cd9b783c2adfb8f5c86c280c92f06e40b55196d4e4054eca741b35b",
        "hash_atual": "ce5cc70ad550061c6144f4cbcf63c9bc3b574b082b5daad6fa015bbf946134c5",
        "tamanho": 19782,
        "modificado_em": "2025-09-25T09:46:19.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "85a2a3c795469dab228d6f2d0f51d6ea7a66db5f26c57f1baad8682676bc8272",
        "hash_atual": "a9fa22cee2b25c58f3c720d13eabeed66015536521c322b5c037d70cb1578354",
        "tamanho": 11654,
        "modificado_em": "2025-09-25T09:46:19.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "72cc6ecfba3c58e85a77ee0d04bbc4f9d9c2fe61d75dc7a2a7ae9ef7b17fefc1",
        "hash_atual": "035949bddaf8c3b617c46bf8fa573cb291384a81f03be245b44ffb06c6292cb1",
        "tamanho": 90333,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d90eddef35d0513960cdf6ecf1d495ee502597bbd4f29ecb749da1455928d707",
        "hash_atual": "09b9d56ed1e6ac053ce7f67d171e765361839d3f76a9217e3a3f8e4e4fb867e9",
        "tamanho": 71172,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f0c27befd39fcadeb7373a2cb89022a0b7ac412fbc26c9b34bb18f3a32e66216",
        "hash_atual": "8e1425c2cef5c39fe05304de21303e2219b7b6d79544bdc4b3d42c8605f54d45",
        "tamanho": 64199,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bb9ac02bd98c2c2f67a91fcda8258aee1416d24baccd665e7da8ab0efb69a756",
        "hash_atual": "f94b35ab2e013e43cba43f3fbea32a29c25f197d97020ddc770468c1817e1e39",
        "tamanho": 21668,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "36eb2edcf9bc3110fed116e621fedd52eeeef6015c4e15a8faebc7c6f68de0bb",
        "hash_atual": "0c67afed85ff60a0adb4a8c33fe6294f10e1f3a70b269305a13ff9723bb7a39d",
        "tamanho": 39431,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ce83c3ad580882054eff8db7f03d61028872e572d5495a658a965cb2ab2e8490",
        "hash_atual": "260cba3a04d2cf06ed05bc1d4c4b2b16a1feebab37439fc010deebeb8c7c6811",
        "tamanho": 9714,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d76fe49fda24353d210e9b0a28cc185d32d5155f38552e7a608a2862826f15ba",
        "hash_atual": "dc18e2cff959b543ccbf8129de66dc78b3809746c89e68eb4bb0d27b1d4dd144",
        "tamanho": 2116,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d0ea87b581cf356d90e73b2fb2d16cdb36e8f4cf2ff574055763434a2e9ba9c6",
        "hash_atual": "df5f81a8b97a17c62a32ac31f35e4e31a4f4be65824c72b643079fa765256d5d",
        "tamanho": 8438,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c473ff200d1a72690b299e176e9ed4cf3af6fb14f23e5bc3eebe581dafc62b58",
        "hash_atual": "3b62039e5079e6e597711c0f6f591476af52fbe203e855ea575b8476c29c68b0",
        "tamanho": 19647,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ae5b97aa7e9d3ff63203f74ed19fb6417f4715b6e87f09a29946d89e7b65fb3a",
        "hash_atual": "ec44d01b96140e3c2625980f272cc27219add2eb27d86e7dc34e96d2791732b3",
        "tamanho": 18651,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2fdf54b3ee6bdd635a357d3e42d5a6702605814e19a20c58c64a3c48d2da5e8e",
        "hash_atual": "ea68ea51992ae719ea0f627d90b82a5eb67926121eb63e4101c826cff19ad917",
        "tamanho": 44459,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1b0b68f6ded333d5bb3ee0bc685517c750ec290b96ffd4192dbe2b24d446bc13",
        "hash_atual": "d757bb72b9d46191b3c7deca18c13c39aebcaca7a9f8dec22bf20cef87550bd8",
        "tamanho": 1169,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d2e02e4ac034b40a92a40bb21ac04a1ce2029d78276797cd4a4d5c0d74886fc4",
        "hash_atual": "46b4c901beba05fb269e6774193f852b9863ba853071cddb8eb8893128bf19a9",
        "tamanho": 10124,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "beaed2dfb80db1e191887f867c155363e923b6401b822de4c121d7bd478f8ace",
        "hash_atual": "0e4e7d58eb212a8ac74e7b275160ed65fbdee99081a6368d8885c4492a619834",
        "tamanho": 8297,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "009f2ca076332bd79cfd2d5181e1edd7cbcd74e38ed0795b12389990fd366a35",
        "hash_atual": "6c984b204ea084e6060485777e989318c609f9510ca4d1470cf1f96d5e3dbb7c",
        "tamanho": 8524,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "af0ec094d0d45fbf0a9dedcc14a1285d5a27965c5285f361155b5416ba29f7a9",
        "hash_atual": "986f0adb278230ef7c0f917e87841b1cbccf670902741fc12cad1c12be5c8d29",
        "tamanho": 29449,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "758bf30aef68ec9e47ec06d9e2e2835e99ed3ceff0f5d84f7154840b956449a2",
        "hash_atual": "a5ea450d680054b9317642ac6a02af751e9d4850f00ad33760e7f79c58d239ca",
        "tamanho": 10070,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "60d63659c56cec183b364451d44cac685fe17a42db43fc51d5563e43be4eb09b",
        "hash_atual": "c5ee05c92355a5a1f887b19326159b47823cdb6a9f170f409d34ccdf669e822e",
        "tamanho": 6219,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a081c2beb2a2600657bd1f248cfd40b02016afc17d067fb5056d256e8992a5d0",
        "hash_atual": "f2570c612846e290072feb638a57eab13f85bb3bc4ae2247356b5d36abe7b7dc",
        "tamanho": 7208,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "513691a440390ad530c08b93740b34ffdb00ddd5da63186ff0ac6ec6b164ab54",
        "hash_atual": "0d1704192f14bef839c939658f6a916a0724463a00b6db85c8bf61ccacddc45c",
        "tamanho": 9296,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f74fa31a7570be1e299ebb8f40560601f48ab4a2bddd7c6b8476c48bd350bdc2",
        "hash_atual": "310c6f937fcc2c93a73ceb04d229f07a6d58d4fba430b0711ef90fbd255baed1",
        "tamanho": 20506,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bb1ec1ddf61b9259307dc5eddca5034b86c9abf0fb3fc645cec7f719ff5bf3db",
        "hash_atual": "a64bf950ed6c7bfde3ca2517a757b90bbfda9549df8dc68c869182b1eb76bbb6",
        "tamanho": 59888,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3652e089d8c9a9b81314b3cfa6a2bfca93e9dd06abaadcfde1124e695761224f",
        "hash_atual": "12112e2cfb71e5ac17b5f01719bb663e279e62fccc30b10dfe33e760b2ecf36e",
        "tamanho": 28604,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "91dfe965357570e90136c6e2c8356b25e302b9747f4111e44eef5b3e742a185b",
        "hash_atual": "959206755653b4e20f2af19e6985a9ae5c2da25bbdd39976f3f98bcc04446da7",
        "tamanho": 15343,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "116bdf7dbca325827d2890c04c4cd07889ef1ed82c57d3cc337f65e569386765",
        "hash_atual": "bbee09231e9d980dd66b9c28ef7971924d45f3eb78b3836802f0b5dad40c5b22",
        "tamanho": 26051,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ae1f3a5fc5af0c3b620e9236e1f8bc38253e583def86ca03fdd2fbc4faae03ed",
        "hash_atual": "62bb34beaa2b1a5b55e277451666ed9c40467c7f34021a345f90c043dd03ba80",
        "tamanho": 25889,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/verificacao\/assinatura.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b719273d91a52069ab030773c3ed519d4d8ee1fc56adb7dfa6d7b49c53e94847",
        "hash_atual": "5b1c9d5210c4f5a2030dedeca57a8ebe6ffff7e10a1998a789013d35dd2b5d3f",
        "tamanho": 14979,
        "modificado_em": "2025-09-25T09:46:16.000000Z"
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
            
            Log::info('PreservarMelhorias73Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias73Seeder - Erro', ['error' => $e->getMessage()]);
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
            'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_protocolo_protocolos-hoje.blade.php',
            'resources/views/proposicoes/verificacao/assinatura.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_verificacao_assinatura.blade.php'
        ];
    }
}