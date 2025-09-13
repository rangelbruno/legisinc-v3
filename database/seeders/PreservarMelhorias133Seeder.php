<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias133Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-13 03:39:09
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "ba6158e3901a1ff8a2957fd44378b574c4874f790cc5c5004c5736cffdae9cfe",
        "hash_atual": "8b3c7d53ba79eb6c9374dede73cfae2d380bc5ee6c3c438735e35892426c7c9d",
        "tamanho": 194593,
        "modificado_em": "2025-09-13T03:32:07.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "e9d813b53e1ca6d981d1d9f81525bc370b4bef46c351b747e01f45752abde7c8",
        "hash_atual": "0db2593bd7f04ac4224dca3167a5e0c49798f7b60770c68799bc81b355938a7a",
        "tamanho": 38821,
        "modificado_em": "2025-09-13T03:32:07.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "bde8281672a5bfb2aa56ca80eb348771c8be740e56be66cac19a794eb73c088a",
        "hash_atual": "24018ad93488e2ec8bc31237934728908cdc132ecd17e8832cadeaeb20d81656",
        "tamanho": 190861,
        "modificado_em": "2025-09-13T03:32:07.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "959ac20cb229fea7c1e3b0a544e415fde35769662b525f7e1d7178cbfc643368",
        "hash_atual": "079703791b3a20aef217914e27d0f66f4c044ea33eaae5b8d509112f58567487",
        "tamanho": 37954,
        "modificado_em": "2025-09-13T03:32:07.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "8c8ddd5c917527317a0d5d0b91d6f92c0373bfc3d78fe2086c90b908b0903128",
        "hash_atual": "cdfb63cf9c114099379c9e9424991e8e57584d1d7b7dd8720e71c3eae3f262b9",
        "tamanho": 16468,
        "modificado_em": "2025-09-13T03:32:07.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "db7533439093efe8dbe0e20fb5491f013b270b16bfe3ef95464c59b5504ee757",
        "hash_atual": "a66e966ebab0ccfa0c431a650cd8f9490112ea841182f8400f3ccb8749f0d1d9",
        "tamanho": 18417,
        "modificado_em": "2025-09-13T03:32:07.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "cd692472f53735408952a011fa47795996af3bc84d158a53dbb79f4d715d3adf",
        "hash_atual": "dd7a5881d79a8d33ba7df1f84fe9c32c3277bd46a4ac26977da9b67f7210a7f8",
        "tamanho": 11594,
        "modificado_em": "2025-09-13T03:32:07.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6408c36a1650e9487e4838c89a775b6634c9e8cee4fca4eff834d0bf5b9b9c7c",
        "hash_atual": "5ea53e05982424d5b24e080e0ade0e235d9732f73ffe2cbe69d4cdee4f5cd194",
        "tamanho": 90333,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ed6b46b3ee609bc9a643a4c57c5e2e66ef973b1f044cb9e11cd51d75a409b8b3",
        "hash_atual": "7043688db27203f08293be9e45d58a9807ce47255678df053c63ea7a7f1cc5e5",
        "tamanho": 69556,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ba4699f003b1b6f51d5b5ba00f94fedcdbe3ec280e1a0ed5f211f69202128497",
        "hash_atual": "f701fa9ad469c4a0b8ab058ca6bf734037c5bcbbb326fb966d4474aa2c1330a7",
        "tamanho": 64199,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "faeb8eab7c635554c46832a9e1106acfd6805ed979d0f39737efc736e1212a00",
        "hash_atual": "625db02f11566d02c60ad7d3d50a5aebda1dc74c4f0c6dcd8da2e1e94007abcc",
        "tamanho": 21668,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a188e2523f1845cfdeb897fefc154512543906e5480b6c3c04e7b86d7762b2aa",
        "hash_atual": "621a5e64ee78d814d78d803a0726cf29fa655622820ce5d7671518e24dab87d8",
        "tamanho": 39431,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "da71467c748b38c79af8d2f190656fc7d50f5fb3cd1ddb20e0ffc883ba78f797",
        "hash_atual": "ae0da9f313eb1e752642de16c2e8a1315a5a1cdf7b531ceb94eeb86d94a096f6",
        "tamanho": 9714,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8ca0d1085629db7f1f42f1170e34810dd31113622fc324fa9306db88251828b7",
        "hash_atual": "c07c73c1cdd02b0c6543bf13faae13663d203f59b851426f61601370b24cfc1c",
        "tamanho": 2116,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b0a5240c3365f8f6eceee44894951f0b18e18b242dda567a29dccabd966a00d6",
        "hash_atual": "c21a07a826650549c9a8dc2ea40569f03d9e3252867332c611bd2b23e22f756f",
        "tamanho": 8438,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4f314b204cc7b2dd821b18612ce1da1996f5ea5d6ad0ed415937b41092c9f3b0",
        "hash_atual": "998025079218bd628aaa9170caee187c8810ebf33b83767e95cf048609044853",
        "tamanho": 19647,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "480b5612d5179e40e6475ab0a81d6b47311d8e2d5e1621ad7584bf080bdcf65a",
        "hash_atual": "727099317c62f52bf347c73f5440df486b2fc1a4ec671037da2c487fdfe45803",
        "tamanho": 18651,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "13c0974715e16623a021ca846c5f47bb7a7443dd93b882c57f8ca04944570d69",
        "hash_atual": "458ee2912434b98a99010fc557f4f22753a5e14e3bebf5b14ee0b67b7c693704",
        "tamanho": 44459,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "931f3c245fe8a8b177624c5174ff6fe50878c90a0b1385f0f5ce35289fb6e1ac",
        "hash_atual": "d763da6e3c9431d5ddd9e2b13f4c782a06fe9e1b53089ad999f815e61c01bd6c",
        "tamanho": 1169,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f309b90ad4337c476536aad7ab172324257407627b25da2fe8f0453423540fa9",
        "hash_atual": "1087511e666cf1cacc88c30031b2156fd435bb22d5e753f016ce7c2e63e8866e",
        "tamanho": 10124,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "55d450e492604a05adb33b912f38b43672157a08cc84377af352730a15d3b4ba",
        "hash_atual": "2157d50916d56c1fd430451aea0e19525a71485c7f6a78a9ccc5c6c2ec993017",
        "tamanho": 8297,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2e1431c408ea3637b302c71226202c82192efcd80a0863a64563c82090667ee7",
        "hash_atual": "7eb97660c74b2319fdfb47fca235306ceff7ef9115b8953395b39aa5e9415159",
        "tamanho": 8524,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bb2e3b330f495139067138e9999e7ae48eade4c0ecdbc48a6485d34c90dc4804",
        "hash_atual": "7c08f6f18e13ce2ee9eff1adbff1d3b7daed92cefa3754937efca35e5f575cdd",
        "tamanho": 29449,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b54cee491b437350ef094b7647d35b1d41e19873f9be5e85c94c4916a71edf05",
        "hash_atual": "0cd4b22a590df625b419da75987b4f9b3ace1b1c9454b50c92bf6531499d876c",
        "tamanho": 10070,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "37d5829ab6dc2ff9f6820831823d5e7dd4f7ba327e9c1310c587570197946ac6",
        "hash_atual": "db2296f50e023aada21926bf173435a2fbdac98aab5b2f183d67382ae64cb110",
        "tamanho": 6219,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b762df921dcd8f42bda63c5f9658a37ff0e6adfa5f30652930878dd5789c35f5",
        "hash_atual": "67eebcbc14c2351341bcb77abb66010ee4e0b88bc9833312f9b3c03fa0d2bb91",
        "tamanho": 7208,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "165587e796af29c05baab5f7bbc3d6cc0ef32a442b8ef2aa915eee352671ec9c",
        "hash_atual": "5f1b3570149245dd9496de1745723851edc2c2f45c8de6f3acabfcedbf55353b",
        "tamanho": 9296,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "87cb14b7cd8f5b29d163af0bf5819cf59ffc4daf08bab53fb1c9525e75e32197",
        "hash_atual": "74f751014c6ada745825a93126593ee311a7b9817b0ea85b57d40960a06b7509",
        "tamanho": 20506,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2a7b00e7b5089f952f8d61817972b191f72104fb4a82b69e70817b76faed8d71",
        "hash_atual": "85e3982905dae55945c55d23e170d79d4eb7090e7e2cbadeb87f2359ad19e9ad",
        "tamanho": 59888,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8cd4651047ee83a8dd319437cd456cf48b3157359f33d1c6e5a58ee0ee105b79",
        "hash_atual": "0db5f23fb9fd51e63412a3dd2f9b2dbf2afee6122437fbc9a85dc85e21d11fc7",
        "tamanho": 28604,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "14b40e3d603afd685c938ef18ee2bdda3437befb9e647f52831f4a17b96bafa2",
        "hash_atual": "9d630743e5f60abb6de6d759d24e5968f99f2da6dc3a8d24cc8438679a688372",
        "tamanho": 15343,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "88c2e2058b1583299d5fc8b225923bea43cfe0ce910658edfb13b11969d55ab8",
        "hash_atual": "4946984a1d866f64d63d5b1e78bfb2548c7baf90e5f05729274596e60854480e",
        "tamanho": 26051,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d177acbc81ab804ccc0fda6c64da4950a535894df23c56877a772c6fa0e43918",
        "hash_atual": "d760eab4144d06187d32cb5f5587ecb059c01a03927f615ad4385fa8fe9658ed",
        "tamanho": 25889,
        "modificado_em": "2025-09-13T03:32:02.000000Z"
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
            
            Log::info('PreservarMelhorias133Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias133Seeder - Erro', ['error' => $e->getMessage()]);
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