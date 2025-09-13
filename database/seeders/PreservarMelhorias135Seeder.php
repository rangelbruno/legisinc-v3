<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias135Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-13 03:40:37
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "8b3c7d53ba79eb6c9374dede73cfae2d380bc5ee6c3c438735e35892426c7c9d",
        "hash_atual": "e0418b5855bb859c5f867515afaa3a4bc2121974b5959156527a57d3f55f86da",
        "tamanho": 194593,
        "modificado_em": "2025-09-13T03:39:29.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "0db2593bd7f04ac4224dca3167a5e0c49798f7b60770c68799bc81b355938a7a",
        "hash_atual": "a3d3dc61eb2087b52b0ff7bc9f65c6e507311b3e3071c18305d8928eee234552",
        "tamanho": 38821,
        "modificado_em": "2025-09-13T03:39:29.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "24018ad93488e2ec8bc31237934728908cdc132ecd17e8832cadeaeb20d81656",
        "hash_atual": "f8878621316457f348ba5b4aab82fb6add0113b9c0efac1ba0d29ed5ffda3374",
        "tamanho": 190861,
        "modificado_em": "2025-09-13T03:39:29.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "079703791b3a20aef217914e27d0f66f4c044ea33eaae5b8d509112f58567487",
        "hash_atual": "514db06bf2ce4337c5d8734e5f7b70b5bae655ec560678b7c7e7fb212d3c97f2",
        "tamanho": 37954,
        "modificado_em": "2025-09-13T03:39:29.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "cdfb63cf9c114099379c9e9424991e8e57584d1d7b7dd8720e71c3eae3f262b9",
        "hash_atual": "41cd49cebd1b6cbc774ce0447f0590450d55d05bdee233734033b7b348200c79",
        "tamanho": 16468,
        "modificado_em": "2025-09-13T03:39:29.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "a66e966ebab0ccfa0c431a650cd8f9490112ea841182f8400f3ccb8749f0d1d9",
        "hash_atual": "fba86d959d5e16429d6bebd7a2329240ba1e860766f3144d51c9f8fb75c92b5f",
        "tamanho": 18417,
        "modificado_em": "2025-09-13T03:39:29.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "dd7a5881d79a8d33ba7df1f84fe9c32c3277bd46a4ac26977da9b67f7210a7f8",
        "hash_atual": "d9c46dfa4e5b0470e236fe5d7aec17fbbf5084eec54d49db34b95119bd22f626",
        "tamanho": 11594,
        "modificado_em": "2025-09-13T03:39:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5ea53e05982424d5b24e080e0ade0e235d9732f73ffe2cbe69d4cdee4f5cd194",
        "hash_atual": "1c9afd9c2a656c2f472c5e3b035030e35194345d06c87eb9fb53457a4b061a85",
        "tamanho": 90333,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7043688db27203f08293be9e45d58a9807ce47255678df053c63ea7a7f1cc5e5",
        "hash_atual": "7df360f4b12659d689367a301740e806e376bcf0fa959c6f65fa22485126e091",
        "tamanho": 69556,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f701fa9ad469c4a0b8ab058ca6bf734037c5bcbbb326fb966d4474aa2c1330a7",
        "hash_atual": "22f221c078d5e8c2d1f18db803738c0985fe17fccf3904c567f95b141e255541",
        "tamanho": 64199,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "625db02f11566d02c60ad7d3d50a5aebda1dc74c4f0c6dcd8da2e1e94007abcc",
        "hash_atual": "016a0d4a5d91c20f1d43ea5222d618312edda87307d513298605ebb7341f7018",
        "tamanho": 21668,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "621a5e64ee78d814d78d803a0726cf29fa655622820ce5d7671518e24dab87d8",
        "hash_atual": "7588c6549bef7a06b3ad5d22491841ddfe7a6711bfab871f8e974e54d443e71e",
        "tamanho": 39431,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ae0da9f313eb1e752642de16c2e8a1315a5a1cdf7b531ceb94eeb86d94a096f6",
        "hash_atual": "68e5396bfac5c1e065e0077048e13247c800b62a8794485debb675aa7680d123",
        "tamanho": 9714,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c07c73c1cdd02b0c6543bf13faae13663d203f59b851426f61601370b24cfc1c",
        "hash_atual": "b61ec8c2f58be6043b67107258c874ffc662324117acae522a3073437cefbffe",
        "tamanho": 2116,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c21a07a826650549c9a8dc2ea40569f03d9e3252867332c611bd2b23e22f756f",
        "hash_atual": "d6eb532100d986b5a72c8652e78f9edde25ea4b7ca5a34e09260bee0458a9b9e",
        "tamanho": 8438,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "998025079218bd628aaa9170caee187c8810ebf33b83767e95cf048609044853",
        "hash_atual": "6fbd5ae44546fe227e24abd48452e9960e7ede8b2b591680dd9ac428b4ef1814",
        "tamanho": 19647,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "727099317c62f52bf347c73f5440df486b2fc1a4ec671037da2c487fdfe45803",
        "hash_atual": "f1e63660321d0889f61bd824ae674f68e4f6b55adb339a917dc46301d32ae178",
        "tamanho": 18651,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "458ee2912434b98a99010fc557f4f22753a5e14e3bebf5b14ee0b67b7c693704",
        "hash_atual": "3cebf883b931c8b1e8e19bbda1489ca7a4925eea6c686f6957171da6bcbc7e9b",
        "tamanho": 44459,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d763da6e3c9431d5ddd9e2b13f4c782a06fe9e1b53089ad999f815e61c01bd6c",
        "hash_atual": "24f23686f44d87309324f3ad33211ac00b80857e84289aa3aadf9369b4777463",
        "tamanho": 1169,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1087511e666cf1cacc88c30031b2156fd435bb22d5e753f016ce7c2e63e8866e",
        "hash_atual": "ea6da50d5c4582e9402f2d29c41abf01a7fab1bc9c6faa46e7d44312d1ab45cf",
        "tamanho": 10124,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2157d50916d56c1fd430451aea0e19525a71485c7f6a78a9ccc5c6c2ec993017",
        "hash_atual": "1845b080cc7d16c9b671eca7a2b4872c4cedba25ce9d042934d5185949942ee3",
        "tamanho": 8297,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7eb97660c74b2319fdfb47fca235306ceff7ef9115b8953395b39aa5e9415159",
        "hash_atual": "9b4ed47c9c61656e4e868ffc57e7829624ca25e5a9563d8054edaaedf9f7233d",
        "tamanho": 8524,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7c08f6f18e13ce2ee9eff1adbff1d3b7daed92cefa3754937efca35e5f575cdd",
        "hash_atual": "351cc9dc9e204e8dea128ab583e69c5dcac6f0ea21db5dda348b987726633551",
        "tamanho": 29449,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0cd4b22a590df625b419da75987b4f9b3ace1b1c9454b50c92bf6531499d876c",
        "hash_atual": "e5a1665d76ac277b8aaefa8ec2845e1b0946fd0487d7efa31db4899d19ac6de4",
        "tamanho": 10070,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "db2296f50e023aada21926bf173435a2fbdac98aab5b2f183d67382ae64cb110",
        "hash_atual": "6251d84b5d20df6fec2575b4c726ac6fc549ae3a6bb4a0496ed10c42bd560f89",
        "tamanho": 6219,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "67eebcbc14c2351341bcb77abb66010ee4e0b88bc9833312f9b3c03fa0d2bb91",
        "hash_atual": "59e717cf55d63188ae3d92b92518b532eb8171e040cf33ae49601ddd55b4f6e3",
        "tamanho": 7208,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5f1b3570149245dd9496de1745723851edc2c2f45c8de6f3acabfcedbf55353b",
        "hash_atual": "9e4e93172fc035bdf27e5946cdcfdd68d3529ecb75e2527214fdcadb8963e110",
        "tamanho": 9296,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "74f751014c6ada745825a93126593ee311a7b9817b0ea85b57d40960a06b7509",
        "hash_atual": "9b745a1ca23d3b913918106cf5544da815446a026bc2cdcf586d1672e3c87c72",
        "tamanho": 20506,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "85e3982905dae55945c55d23e170d79d4eb7090e7e2cbadeb87f2359ad19e9ad",
        "hash_atual": "49702d8d1e90e6137b4c2df082be3cc77d4bbaefcaadfada5cbc177cc2d9ba91",
        "tamanho": 59888,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0db5f23fb9fd51e63412a3dd2f9b2dbf2afee6122437fbc9a85dc85e21d11fc7",
        "hash_atual": "d224f9c17309631b3903168d2f4827aa39fdd51d41d515cf8cce068329171208",
        "tamanho": 28604,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9d630743e5f60abb6de6d759d24e5968f99f2da6dc3a8d24cc8438679a688372",
        "hash_atual": "59094d04444d50b438ec47206b137eb71281d4dbfffa5d60e741abafb0b8c140",
        "tamanho": 15343,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4946984a1d866f64d63d5b1e78bfb2548c7baf90e5f05729274596e60854480e",
        "hash_atual": "e095d0f6de4a255ac367ea685a54a42c4253f28d00c255d60df7b81bb5e84761",
        "tamanho": 26051,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d760eab4144d06187d32cb5f5587ecb059c01a03927f615ad4385fa8fe9658ed",
        "hash_atual": "a84ef0e6b3cb883de058ed0894d578841997cf3ed4cab59321164064ff2349b5",
        "tamanho": 25889,
        "modificado_em": "2025-09-13T03:39:24.000000Z"
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
            
            Log::info('PreservarMelhorias135Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias135Seeder - Erro', ['error' => $e->getMessage()]);
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