<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias125Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-13 01:50:29
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "d3268f8eef9248627d58639b7579e5879303e4fd92b1db90bcb8987610626bce",
        "hash_atual": "b2b97eb6ece2a976e53b7be1a82b046887c9898f729cc9b15813d887f1ba9e1e",
        "tamanho": 194593,
        "modificado_em": "2025-09-13T01:45:27.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "91e19cac2d4d10f03670bf3a39cd590d3b60f4dac8eb3675bd44e1d05a613f36",
        "hash_atual": "43175732a3a4c7dc3328db94a68533e04ce97bc6d777bf78de5c645254a4c20a",
        "tamanho": 38821,
        "modificado_em": "2025-09-13T01:45:27.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "43ebf4a47851a41f20cf60e5a7bcc5ca4912ed6f006c6951f4c09940cc722be8",
        "hash_atual": "f7e2219ba97dd7f68705849316534dbd7528b2c3cc0357d6b7bacf103017b83d",
        "tamanho": 190861,
        "modificado_em": "2025-09-13T01:45:27.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "07bacc89688e2382d6eaaccec6689b94d37ca418d20d29e989e07470e4bbaca8",
        "hash_atual": "ac8d9103849b2bad254975da44cdc7633d3a28c8f02675fe9278fec6e755a96c",
        "tamanho": 37954,
        "modificado_em": "2025-09-13T01:45:27.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "5951540ff6ea15e082148272cde5b687336ca33fef96b31efbf7fcfd134f9b86",
        "hash_atual": "400a475f814cf1043fea49526909d7d314229398cbf5cedfc70d2cd2875f1779",
        "tamanho": 16468,
        "modificado_em": "2025-09-13T01:45:27.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "d6f035fb53593012e9d8773b53171b3e49a579b15d9e8836a3b7e028cc7cac84",
        "hash_atual": "6a2be495737a4831501fd099058b41e008ded91c0e0ee06191880d6d4166c69c",
        "tamanho": 18417,
        "modificado_em": "2025-09-13T01:45:27.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "c0f4326fb0d2afbdd3d6f97a3e3aa97d0ba03d7f42706851b90e7084aeb59bb6",
        "hash_atual": "a8d48760b710c5b3882bb8dc4cf4cd1e201ce5f4c315c4b8d68ff3804fea323c",
        "tamanho": 11594,
        "modificado_em": "2025-09-13T01:45:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a342e4bc8cb4170fe1d97e6028a2aeb92c5ce456df73c586d4eac321743400a3",
        "hash_atual": "45dc50ab8ae1af3b3f729c1de4488e1b5649ca3eed8a646c617bc1ab1fd6553f",
        "tamanho": 90333,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b08fdaa3fc1d1456cede94aa902178cd89d2b0d37af684a8ce6b2f1e61284b73",
        "hash_atual": "3aa766df78a050f7244d065e686e4962e781745281b6100e396d70d46ad23057",
        "tamanho": 69556,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0e7d29c5a5667a8d7b4d86d045fa3fcc027d8b544cd69c0e469799c7cd619f93",
        "hash_atual": "135e0d3d973e40023a9c28b2d1114a3f4679ca714881fb122dea7afa4ca49ace",
        "tamanho": 64199,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fde7613a71110cb3eb068f7a2daec2a2bac48d284bc45995345833e0275f8ad3",
        "hash_atual": "6e6136f645128c94102c3f7586ceaaea1ec09fbafcc73f49d37cf9e64e161799",
        "tamanho": 21668,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "182f1558625252dba6e77535a26d0afa13b4ec8fbb9437df4fdca96099b20cde",
        "hash_atual": "112e824fdeb774701d2cbf082efee9d8ed79cee6ef38d5a01738263a340da51e",
        "tamanho": 39431,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b3191de2e183a7b72217aec46d6cae48c4317f0ba3406f5fc1428ff518e926c1",
        "hash_atual": "f5f39a9d997b27e667a053bda6c53900c2eb05a78a0b958d701a06b069f4521a",
        "tamanho": 9714,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "26560d9564f7ab0f9931fe439e723925f971cffbcfc6d2a1f05ac91ca4127158",
        "hash_atual": "bbcb5b7caea32395cc8df7df619df16347194a94af8915deca11152f1bb4241c",
        "tamanho": 2116,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2e98095d7caf309751178b0576b883a703d36617f720a4a17cbcd85b5b6d0f36",
        "hash_atual": "8564042c75a208f32e3ee606fb828ad350d00db1497fa83317195f51f60c139b",
        "tamanho": 8438,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8ecb7dd61b226dfdc79d749bf6f0f4eaa4277b90d14c1d6612987fea7a303faa",
        "hash_atual": "7be35f40bca945e9dc81cecb59c9c5ff12af123c8082b4989231e9027179ffb7",
        "tamanho": 19647,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "53497a17de584d7dcc613f8a79839be9458ab3c080cf48d9924db1ab2e81729d",
        "hash_atual": "625c7b3a6f7e0cd8c2bd38583985049f787da4891f6b7dff81aa1f120849f1c5",
        "tamanho": 18651,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f70d57f5502b4bc2d0edfc8fa19bc756441d5d52330a645acd505356733d0c54",
        "hash_atual": "b5148db3ff44926c9cd1a1bf2a15d3a2d426039ac976334dcf7f93ffbee1c1ed",
        "tamanho": 44459,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e658de53e42a2af440fc82068c5634ed620768fed48b1ca52a610d77f74bba1f",
        "hash_atual": "27d402d1b0e71c8edfa3273c14eb2f89c04fb2c7114bffb8aa3f12a823b9ca29",
        "tamanho": 1169,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6b3b83aeb5be94af4980f79247098801384f1d78661fca52011fc9352c190037",
        "hash_atual": "00660d1db402fbddc2e1027c0687e12aa1071be01d5fdb019123ad66038c66bf",
        "tamanho": 10124,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a9e99c4a58b1196e3bbbdb6f37e7231b45778c22c415fd435a31f3bb4fb1dac5",
        "hash_atual": "df4b6adbbb9d09c7d02328f284d9964f5e8daee65b9fc4429609bcb5ad6cb3f4",
        "tamanho": 8297,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "da18c45fb5294d9fcb202b6cab4553593f7ef7ab47cd49f7ac514a656d2cec5d",
        "hash_atual": "ddcc1a118244480c52c1483ac5f962ae14cfc311e7c926458d3377467b97c536",
        "tamanho": 8524,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3c79f5cd1ce663e4053facef569efece7d4a6f90b8f02a14a77b556af2f7d5b9",
        "hash_atual": "d11346717adb7ce37b7bf3788e2d9bc04b0435a1abd3df05d3969a9d1524eeac",
        "tamanho": 29449,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a907120bc9f4a1eed23fc0f7073959c7204434dcc096c61f34fa9b0ceac0c1c3",
        "hash_atual": "b076d974a73c912ff2280d94db51434e351b729ba4a3039ac76cac386db09687",
        "tamanho": 10070,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9b8e71660326f705e386476c3f75802c7cc7d149d4e2d74ff3ca62be78c98e86",
        "hash_atual": "49617472c663079e8e0669a53734149051b8528a62211e1cd5e4881af001cab5",
        "tamanho": 6219,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "19c1bfc8fc2b041e54729ab5a63e9aefee6ef262791ceee3d0a70cadec66b39e",
        "hash_atual": "57557246a1008deed41175448e7ef095e0f02ef357488cfdbb53176c3ae0a757",
        "tamanho": 7208,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2e8a30a24b2f8b7eee9b44a9eab52ecf0572a10bd8d66149a6ab678e410caf5c",
        "hash_atual": "9bc825a61042bdd1b7c2112753c54ae16c9416b8170df2066a9c3e50eff10b5d",
        "tamanho": 9296,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "11ea716945213fb7c7b46a9f185c28530b34bd651c129f53a1af91ee7c555ae8",
        "hash_atual": "88d7aa6496d564de17272af9c9f246a20434cb9616ffd1899eec1a42186c471d",
        "tamanho": 20506,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8813f52a748707a07aad79baa16c59677f87ef9e9505c876333e0b3f7bde1a04",
        "hash_atual": "1e36f24a741a64134227a5e900a89c6bfa55ac2a7dc6ab5718e10fd3994abbc9",
        "tamanho": 59888,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1b2486f8422975cfa3e2fe75dd5c87de3aa9b7e11fd53c7209893cc406f7531c",
        "hash_atual": "940d7e603c88eb8c1c85c16ef0175648728713d2c68a0cfe1fac17719e41c02e",
        "tamanho": 28604,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3c4c650899be0aabd7e593f019294caf7b58aaf541eb6fe9e52123dcc3cdae63",
        "hash_atual": "e9e4a37afe43aeed040faeb0255e5d9c85683140c4a2ef7f072d17d754bd78fa",
        "tamanho": 15343,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6c9bea47f1644a66efdf32ab5534720fbb4cb6ec3df790a81ae0d81c2914d180",
        "hash_atual": "7fe67c86bd3ca3835c73600d1bb02cdbd9b6b24aff1f4df107fbd54e9c869a7b",
        "tamanho": 26051,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ee801843d5ce43502b2e3c53f49b3f363902e476eb70fc7a048bdc7b643b3297",
        "hash_atual": "ff554892a7874d01f01f0d89894c3be79714bd79820de17b14077c828636b9d0",
        "tamanho": 25889,
        "modificado_em": "2025-09-13T01:45:22.000000Z"
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
            
            Log::info('PreservarMelhorias125Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias125Seeder - Erro', ['error' => $e->getMessage()]);
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