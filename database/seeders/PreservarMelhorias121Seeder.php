<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias121Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-13 01:42:21
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "5cf2af698ee8a95a3bab40d96eced64cd23a96bf0aaa31063c48a163f01737c5",
        "hash_atual": "f76914c312b7df4fa5a17c2f23fac4560e9d7d9f6143c9f9f5cc70410ec6fc27",
        "tamanho": 194593,
        "modificado_em": "2025-09-12T20:51:48.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "88eef5ed038e8d7f89ba006ef92c2d2c518575513495cdc0ce39fb6fa0a3ab4c",
        "hash_atual": "73e299bc9438440f980b6917c446b846c73ab2a0fb07459cdbf1920b91a0f488",
        "tamanho": 38821,
        "modificado_em": "2025-09-12T14:38:53.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "5dc37eeb66b60891422458438c49a4f1368890ef6e1d280bfab08c6283523a87",
        "hash_atual": "02d0b774dab17252be9f383ae4889886bb2045b1c102d5648ade816006edd630",
        "tamanho": 190861,
        "modificado_em": "2025-09-12T14:38:53.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "7ff4f89062adfcc3bc61546cf2add4871449c151b6ccf66828d8c44bea6aaf4c",
        "hash_atual": "0ac3de925f3817272ba4136ae98a45d3d8d278c8c169d21c8dd1b9ec94195deb",
        "tamanho": 37954,
        "modificado_em": "2025-09-12T14:38:53.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "74f24015c97e231dc2f5ca73050c473e1c5f422ca912d33aa2e4dbf037b4ccbf",
        "hash_atual": "904e530beb3575e7168a46b7c36f50368eea4e3f33ab6bb235e234aa2b6c3a00",
        "tamanho": 16468,
        "modificado_em": "2025-09-12T14:38:53.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "6c27127d9d5d56cb75ff45d7afd819053cfcfc7aaa129eff598472b8c30c281d",
        "hash_atual": "3fcb30018fe6dcc7f38915765a362f058f59bcba25bb76f50ea6758d9c31ea9f",
        "tamanho": 18417,
        "modificado_em": "2025-09-12T14:38:53.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "25d48691621975b21b4123c2dbc73f00391425c6efe1b144806d151d0871c3cf",
        "hash_atual": "cadff55fe47afead510e4e453c8e4f029e1bb2673bf216ed6c8ab133e569075a",
        "tamanho": 11594,
        "modificado_em": "2025-09-12T14:38:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8f935525aae0634220989ec59dc86b490a01908573a0c91caa82330af44ed588",
        "hash_atual": "66cd075c90b11e1e2f6ae2842ad01f4a0316033ace25192b240c251dd35aab18",
        "tamanho": 90333,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "76ed4b6e1ff07712236f0bd410ce00be9e03395402395c36452d49097642b7b6",
        "hash_atual": "22fd9832a651bc95a33e9bc4d58713aa50ba6251d47bb75e21e17bf6d020b372",
        "tamanho": 69556,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "afc41a8d38c0fb64a1d249c7f7de15ff9fbb80ef71ba7b183bd31e9da5015972",
        "hash_atual": "62beaa924fb7d514f5f30e72e011df9fce0f339086b0d517825cd690850bf064",
        "tamanho": 64199,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d0662dc0981110bf760243a58597884e9c1e64cd968e3109e4fea30c9df4a5ef",
        "hash_atual": "21a67b19dc624e9f3d15efb30df31acaed136fa8f4ae49f542169009577ad4f6",
        "tamanho": 21668,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8cb7fb9a592cd511b63be99a91ffb20d93803d5c64959356f4abb7b82b0f568d",
        "hash_atual": "3c15136b13392195e42cbaa4705d1e5ab108a6d83799f4e72a665d5b01e337c1",
        "tamanho": 39431,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "954b45d7223f12e6d0baaf07c66a1cd44b5602e2117af406f2f360ee7d595cda",
        "hash_atual": "f4de3a92d646bb5b7eca359585831366e949b3a455a019e3bd3eaa08f980d9cf",
        "tamanho": 9714,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a7f3b19c8935d41bdcf7328d4c2395f88f059a99198cd359675ab269ef26ffd4",
        "hash_atual": "2d9c1b93643a4a474369d7a44e41c04022962867eb8a29f3c0352e334b910fe1",
        "tamanho": 2116,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f646506bbefe39d71902941da36d078f88183a1374c9920ba4f82f1e50d2ff08",
        "hash_atual": "c7fcc7122860cb6c2e2070da11bdc36b97256715f579ab3f14b86069d3740f96",
        "tamanho": 8438,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "738bf2f167a6bb7903f5378a3281dbc6f41f0ea415c71859f986eb304d90eefd",
        "hash_atual": "5b2f0c681f04277a44c6ea1b7bd6dd51bc4b7fca7c6671bfefcac0e495b169b5",
        "tamanho": 19647,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d91366b3581e78ef5fe2cc0e1e2b3f70fa402add572e387361d050d98c5d67a3",
        "hash_atual": "bb9aa20b4a3ffdab8028127cd9e4160dd2d914726c84a8bc35a4e1b8a6f21d8a",
        "tamanho": 18651,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d89da5d0bd6f90c8f1d8d5ed5ea7466707cb182e136fff7d747a88b097a61a65",
        "hash_atual": "18bf73b1ca1f720ff0235ace36d03c7e2b1256d5ddccea5bc604ab92d1ba2543",
        "tamanho": 44459,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "040b0cc91d0df6b2cc7aab35555739ca01c733b7c4a33dc4eccccecfe81d7fd1",
        "hash_atual": "d0a8c08d150442865af3d60ca99f9c8340287756a64b451b23629603a5efc3b1",
        "tamanho": 1169,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7c40b288da1f54436af7241e31eef3ad86e07150b9314dfcc888f25f9f29f6f6",
        "hash_atual": "3b8d23624cca3ae07988bc3372b1e645ce947c6736034dca7e28035f3755ead8",
        "tamanho": 10124,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "47161160b1a7a392b1833d2d2a7a70bec519fce2c154b5e1c5258464a09ddb20",
        "hash_atual": "1638e66abff41275fac9aa1872a7374a68a4edd6f1b56fe5c8d15591410558ab",
        "tamanho": 8297,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a5095be2e3d3a1471baecbe4089bf338f7ea0d61da37d29dfc292352ebe33817",
        "hash_atual": "132e88cd2229c5a251c8aebd5981e68be62209a80bde7e00ca21775da5649434",
        "tamanho": 8524,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f8c5b83c4d9b109d5228a26df3a4c4ea35d912e221ccad7c3733fccc461d7395",
        "hash_atual": "a09db8b5f79637e04c9592fffc333040cfab7e760d5698875aee7b4a8f98d45c",
        "tamanho": 29449,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d3fbc2037345dd2256f68d36068eb3d1525c2fc2daa1a926feda7e2232a1a4f6",
        "hash_atual": "f3f687b39e675cc196d681fce331fef83f64b18ee45203f914966f6c6bb125c3",
        "tamanho": 10070,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f422f7872f9f116e17ac3eaa56695d0325da5616344c4bfad2d0a6e0d9d3c254",
        "hash_atual": "1979ac192b9d7ea43822f987a325fa5ba96dd5d5dca200c81df61dcd96192d05",
        "tamanho": 6219,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d8f2542d0eb4dd575f3d699de70f7e8bb443817c958201c984bf777c1529d4b3",
        "hash_atual": "2af8bad8d5987e39d3806876c32266af6e4b30365a3b2e175eb056e369f6625f",
        "tamanho": 7208,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "23ceea5f17199d25d91e6bbe3e7fe3f47947a8014b7a52ec2764022cb04d9e0c",
        "hash_atual": "7e776dab4f9f9dfd1286536777225d0114d5f73bd56a58711cd3b004497c1afa",
        "tamanho": 9296,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c98c413e352688c96e22ee70821f3104e529849802c66e885a00f6f77f8c2d91",
        "hash_atual": "b57708ee9bf515251284cf6e1a8302383328963e01943e36045c858437eb93c0",
        "tamanho": 20506,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e5800d1ddba1e1e179fe552c4db91a429341571ffa422e1873eb94a9afcc1391",
        "hash_atual": "47d00e1796e2fe66d4fe5f7e65fa773907b6a2e712065497fc91f455047935c8",
        "tamanho": 59888,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cff5c8cfc121f84ed2c530d7395e922cbb1c3b4603c3e377d28624bc685bb8b0",
        "hash_atual": "99ef34bd5146164e2546ebb33ed9286dfdbda091ce44a6117e6fea4dda6b0482",
        "tamanho": 28604,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "96ae847f3fe2e1d402c5df4d931790ac00ccb1709f4709bbba9ee5a0e6990990",
        "hash_atual": "d7a002f09f2706f8570865b6ff6674b53a7198e52f3e4d55e7b53bc2e856bd45",
        "tamanho": 15343,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "52f6dbea49e8e4c79494edb6a4a0069df75556ab2d66a8dcf04c28fc84170d97",
        "hash_atual": "d19e8b119cca2a303c5e310988552c7f1f89973c9de9d4aed67faa85ddee896b",
        "tamanho": 26051,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "90a1f365248c7866d1a2a5dd6ecb4895a00bcbf49b920b039fe8667b17b84e47",
        "hash_atual": "e66e177f9e078fb8342ff0311065bc78f1b2effac6600ef784212f3d240b5abe",
        "tamanho": 25889,
        "modificado_em": "2025-09-12T14:38:50.000000Z"
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
            
            Log::info('PreservarMelhorias121Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias121Seeder - Erro', ['error' => $e->getMessage()]);
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