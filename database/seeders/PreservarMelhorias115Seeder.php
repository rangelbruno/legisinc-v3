<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias115Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-12 02:47:04
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "3946f362d1094c90e99b7a6d171fa36e63b7b27ce416387aefd683586a9be48b",
        "hash_atual": "c398e8bf4d67ddcf8d838e26e1c4e9949b3cc32edaa72f41bff25429fc21c371",
        "tamanho": 194828,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "72b0334372c06296d25194e28393a6cb8df88e5bd7d8060bac0977c9960d94c6",
        "hash_atual": "6307e2e2ec6aceae15f0aa0bdd17a6c82dde4a85b781e74e8e2e47f6f44c3ea9",
        "tamanho": 38821,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "41274f7cc269065e17b8792e94e6acc11dec1ddc623f074fc3ab3f5397e112a6",
        "hash_atual": "968498db056591c318fc99768f398279bdced58d6c0e5ab49222c5fc4f10b202",
        "tamanho": 190861,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "7da021a4fcd4396359f645116e88e494ccc532661c44ccedc0f11801de7f2803",
        "hash_atual": "1c8a4d9380281c10628e623144a0821dc03ed8d6118ca1bed1bf6b8d630cbb73",
        "tamanho": 37954,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "5f8d06ac08377b9f6dbbfdb4e5bacf437bdc994aa05e1156782965b1cab955b9",
        "hash_atual": "3feeeb578df547217783168054a72b141c221686bd7294e6700409d10b5c5398",
        "tamanho": 16468,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "ddf1a527839e4ad308cc803b139464ae0270635a2b306bc469d9aa6f93e4e124",
        "hash_atual": "13bc829dda6efe686c22c0ee82025d4df0e550d51cb84093d6862c6082293843",
        "tamanho": 18417,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "5815728f64b0070772e5a2fd0914712fa0c1e9a2a253d85909a3717329e02aa5",
        "hash_atual": "efeaf2a32f39913a54d03406337d6bb3915948b4821cc134d432576ac90b8be3",
        "tamanho": 11594,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0e7a6715ac22e15f41d7dba4c60ee82db5dc0ccd00b3689b89f908aed6f11a0c",
        "hash_atual": "1aaf83e4e36e251b3e8de31087eec5cd8f36c8880bb750a709aae81db1e6ee08",
        "tamanho": 90333,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "22e2208a33756205218fa8fd63d54ad0585050fa1e8440bdec7da9a185c5461c",
        "hash_atual": "b34b7e5cd9d22874a74397574339d37e80732468547daa163a8b6b2e20b35fd6",
        "tamanho": 69556,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "549ffbc995010439b4778621efe2b5719dee4b8334054cb2868372265caacc04",
        "hash_atual": "65d8e4122d664d50aafa02526899068d37dd280c50c84a159a52ae0856590b95",
        "tamanho": 64199,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "941c32a5dc743ee85a935ec10909932d63aadd3d0b447c786fde082a476af62e",
        "hash_atual": "f03802cea6c4062b952b090e8917ca0651c4fa9918f88bef31e94cd678329462",
        "tamanho": 21668,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "533de234903b10442a55531ca462efa562db29f01ec41d8fe6e1ae0756c3cc66",
        "hash_atual": "519d06bc25966f761d2028bf5dd9ee5db328f11d62723e93c5534e5545778e56",
        "tamanho": 39431,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ca3327b5a7d62a89746638571c3e11eb7ffb3bfd5d0b6eedb9432201e204741e",
        "hash_atual": "2125c96f3ebc2340a5be60405dbce9f426505209b3a76b1ac09fa480c70bde56",
        "tamanho": 9714,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f3bf6405d3a3eca11a9b3efbfd3c43d465574eb6bc18fcd623b9f0bed66745c1",
        "hash_atual": "20a90521a5a2b5e57a52d01fe9995bf88a37b76fc24c31788f7e545c9d3865cb",
        "tamanho": 2116,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ded539ebb3a87a8d61b6687a52412ef596dac8e88d8867160fc6b00dda5c7482",
        "hash_atual": "0a3e284f7efa974cc106e5e610d949ce680fb8d65ebdac9c699b5d7a51e5d95c",
        "tamanho": 8438,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "90c8cb56ade34f8f3b0ff807167c6506d5f65f61392320b6792faee8fdafc2dd",
        "hash_atual": "c0608fd00951fce80442f8af879523f7679ed3f2101b31cfa61e9b2330c0b178",
        "tamanho": 19647,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5d5bc5c2be3967f7d1105c8cb4f72c3b4cd2bad0790f8243a769a322faa2c441",
        "hash_atual": "e691f92fafbe4541887dd21c070879faf81c1058dc8ba591e623edf6b1b27306",
        "tamanho": 18651,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "671dc45da4cdb62f04b8a9bb9a51165e010cd558c83ffeb76a07081dfcf7553b",
        "hash_atual": "1a3dfff04ec559242bfa43e85ac9fda5cf670174d4b1b8ee24c11789013b751d",
        "tamanho": 44459,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "dba17d5964eb434dfdd99cee81f8c9a0dc889fbc2c54ee7d98b2a370ce8c788c",
        "hash_atual": "eaaa66f6dec4950d141b3501ebfd035db74b588b3c7c9f12beb7bfc5bc1da7d4",
        "tamanho": 1169,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4909b4d4ee8caeb2be74132c7fb326eb4cb578641869105a8e12d1e0642c6f68",
        "hash_atual": "2298da118a662170404ac12f945cff91d1d4cf552aa0b8313922e7ff8bb7907c",
        "tamanho": 10124,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3eeb9ddc55fa7d261c7b1d687e14d856e160e1ea1434a09c5120142b3db9d765",
        "hash_atual": "79a3d285aeb9a2b00ab6ba436d982df7019d96490c526e7df437ebeb0bb8cf94",
        "tamanho": 8297,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1c7e2312007c7effb35a256fa2c6d20a0cdc109b9f87c12528fad8a394741213",
        "hash_atual": "80da03067c28752d4aaa075111e312840fbe1589c3b0a836195a6429dbc84ed5",
        "tamanho": 8524,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3cb46c255a95674ff6da9f2dcc9788f555b3607a078e2f4096a60d440f6e1d0b",
        "hash_atual": "d05ad01cbfa2b8171d06ac634a72e216121aec77dc7ba8b5dd864984fe9e3447",
        "tamanho": 29449,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8db442de77d8d7af9a35dff1a3c9f649123c30c0f1a179ed23340937c96d879b",
        "hash_atual": "4088c5dbf649c14e54981f5831db56f309f4669f76e05c158c90a8dccacadc7a",
        "tamanho": 10070,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "53db61f628fc198fa2572a60ed27e954f893d085df7b0ddcee57be6b7eb63f2c",
        "hash_atual": "c02e9e358f0977ee298403b96dd6410d488560a0c1372260eb7f59aea51e9e43",
        "tamanho": 6219,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3f691a09f619af211be6c5362d32af7dfd876ff6448b58294aba6877e148b5f8",
        "hash_atual": "5cacac69dd4ddbd216c44fbd6407084f0baa5851d2781c014139ede8b8f296eb",
        "tamanho": 7208,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f4b4bd6ff2e938c77d257e3eb1a1e4737bd336afe73d4b167349e3bc7f67d43d",
        "hash_atual": "2d4c4cedb78810daa503a23cff551306a323c382d7517426f3403e5cdc17c10f",
        "tamanho": 9296,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9e53fd5b99a774c878f4fc3ad44e5653f617519d71278a425bd97264be45ebfe",
        "hash_atual": "479f0885def3a4970856c2bb1d483f2d4d01a306826ec5e3bb941c36a6a42179",
        "tamanho": 20506,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c72237dac05e99236a09c4b7082a410465e044456306da1f508ec82e3ccd2f2e",
        "hash_atual": "36aea420807941b9d8b61b92c594ef0d6369139dc9389f61a1da07ef0bb1425e",
        "tamanho": 59888,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "be49a74f114ff75d8f2510ea6b058cf8178021d5b930d84fc11053752f198d7b",
        "hash_atual": "fd38644dea6c9adb1bd17d92e68c881f57db23cdc5a1bbf045cb57427ee15bbd",
        "tamanho": 28604,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e02d1ee89a5ac276948493ed62f8a1cd9ceb9bbcf5bc336ad535f8182239ff9a",
        "hash_atual": "044bb1e00cbf19f1b0bd39759467a1c95078f1fc02828c336c118f4fa4062b18",
        "tamanho": 15343,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d625dd6970e286f11b400a7720f97487edcb39be6a0c77fe3f3b86265dd3332f",
        "hash_atual": "f5185f4e6c2f126cb8f1acda230c323c93ac7fa4d433e13f7afd1551ee5b905f",
        "tamanho": 26051,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "90a2291f551ece9653e0547a5c820f3f768539d878bca3e73586310f62ebbf7e",
        "hash_atual": "acea1797b1d266cd43bd25c069b6e1999370e1e1395110ba2df66ba4d0492545",
        "tamanho": 25889,
        "modificado_em": "2025-09-12T02:45:45.000000Z"
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
            
            Log::info('PreservarMelhorias115Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias115Seeder - Erro', ['error' => $e->getMessage()]);
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