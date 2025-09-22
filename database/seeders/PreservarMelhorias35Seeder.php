<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias35Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-22 18:12:25
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "d535642332ae2407ce8be22e68264bf794f6a008b1cef4326fb0bc20d7e98da7",
        "hash_atual": "64a9daf487b5d0439077d21d17539406800ad548d96b82667b878bfd0c070b3b",
        "tamanho": 199451,
        "modificado_em": "2025-09-22T18:07:29.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "9feb34a81e5b1e4c2b0d5a47799542e3e5a3994f93e45fb2e7d0d730915984f5",
        "hash_atual": "f2b1a83d11201e62c6d86a35df7381e53c30351efbb2c6d054c5b6a05ee67ede",
        "tamanho": 38821,
        "modificado_em": "2025-09-22T18:07:29.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "14571e3791993c531ef0ceefda94fc1d48e89f335390118a87700b42a9720451",
        "hash_atual": "8dbd5f4b71e4f930fcc7b640c9e7efc776da410459f52371b562a3299e748fee",
        "tamanho": 190861,
        "modificado_em": "2025-09-22T18:07:29.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "9902d7d13de14321fe99266b3cfaf7d9becfb622ff4b3e687f70ec7cee784163",
        "hash_atual": "0b4447f62fa48af9e024d5aa71bbc7a248b0992a7a2c9fce104c4b71de6c3280",
        "tamanho": 37954,
        "modificado_em": "2025-09-22T18:07:29.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "9a7eed032b066b4804f0deaad4862fded4789331154fe22aadb8808ccc1ec0e3",
        "hash_atual": "17978a599fa464ac8afd2d3702804b671c55bf07b257f2c8e65a9de08132fd7d",
        "tamanho": 16468,
        "modificado_em": "2025-09-22T18:07:29.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "b2055b757fd5d12e65e1adecb470db91d13b9ddf382659a415637700ebcb9769",
        "hash_atual": "ee8ddc6f0ff44ed7abb820e1e4662cf07c58e0ec44b545bf66ae12bf77ed27bb",
        "tamanho": 19682,
        "modificado_em": "2025-09-22T18:07:29.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "964495238a4acb3c3e76746c99a5a6c32596369801157aae013ace670eee1ffd",
        "hash_atual": "9552166fd786503c86673487defe946ee53d13505c2914d3519a728b56ce6c47",
        "tamanho": 11654,
        "modificado_em": "2025-09-22T18:07:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f089178016f28979dec262f7aceab637c74ac6721ca748fb4210eebb739eaeeb",
        "hash_atual": "815fbbb4c207569ea73ec052bd8f1ec483650e18ee78b720ca9895eb3b144c9a",
        "tamanho": 90333,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ad73dd0a82a356d91748a83342753c63953c7254397d2707f198c17e3d65466e",
        "hash_atual": "8dcbf44ebdf0fc2b675a4d19c2701a848e8281f754226798287078d8420095dd",
        "tamanho": 69556,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ef3cd979cc36da9571eea8d6668492dfdeca29861b51a93ee9e069d460bc2a97",
        "hash_atual": "e7674e5cfd19880e3100c6c710711e60ebeb6384b39a3ccc1f5c627729fecb78",
        "tamanho": 64199,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "23faffa25d7face175659eedd604e364b5c0b04fe7b93a7e025c5e02dbc6985c",
        "hash_atual": "1f36fb66cdf0849a37d838c97ca1401889680cbb361514deca9c3da86670d7a2",
        "tamanho": 21668,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6af19c05bf6232136c748724fd3ca1718bb3fd5dbb7403edb4f016c88ada5c78",
        "hash_atual": "c849086b7dda2d48cce5062adc818b61330913eb81a81f3aace74a317588f6b4",
        "tamanho": 39431,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "09b2d3f1f22e53af83c19303ebaa4f0bfeba89d9a239d5715edae4d75ab15360",
        "hash_atual": "e6cd7d2230d761e932942e6a5cda800815a1c38590ee2ffbbd7982b671d52146",
        "tamanho": 9714,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "79966e8372d3545ea54a7251da20df82c0a81c91b67639a719fa6673151a3f0a",
        "hash_atual": "d2f01b040258e589e8f4aa481749c9fee2d05d8d449062b824dd41b8f2b8bb01",
        "tamanho": 2116,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ee3bcd223c7303de34fda0603410c6288f891ece7f1aa6c1e1a05c6d6136e996",
        "hash_atual": "5b20c80f82714acb51b02aae9af51635eacf50bae1a0984b35d277f4104f9010",
        "tamanho": 8438,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2e1e72af1bdd958f566b92717f1a3399a968ec54f79fc8854c44f50addb20ea2",
        "hash_atual": "e5d1f40543d66065de8beda5fb56ce9c29c52dd948b8dabc059e54e27f7b7ca5",
        "tamanho": 19647,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e5b205a29075d04bbd9d4caedc86e6c463966f1858aa4755ed4398beb7c884b8",
        "hash_atual": "04478cb464fd7daf5ce9a9ba6371c7d5a55780c76e1baefc6bb913939023d61d",
        "tamanho": 18651,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f9578e338296e88244766301ba32738098b82c248c18dafa6f4b2d2ab1a3825d",
        "hash_atual": "89653b5d3662ba8702a09a3b62f38547e22399d4c96171005424e3c6fa63a3f2",
        "tamanho": 44459,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "86f4631a9a7c6a70073213149951e89b8be40dd416e639ff986f71c83f90d0c3",
        "hash_atual": "59750b6be903fcaee6a41c78ceab0e1f18eb193bb11403b51267fb284919deae",
        "tamanho": 1169,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3e422bb3830ea527f275b3447672c615ccc83f31bae33810940c6f89269ba924",
        "hash_atual": "1b85579524372705bfce21c6600d67dd40ef51bb7def52feacd58e93ff68f989",
        "tamanho": 10124,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c38ad83fd267030ffa3713a96087c6c629ee1646c105dd310802c24785d4f6bb",
        "hash_atual": "931dba60992842eb3693ab2d8bcc52625b481771fd2824b0b6c4fd976f256463",
        "tamanho": 8297,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c94e5297d811bf335008b5a29778b8d3bf3748504ce95ec2cb21dd64998c2f3a",
        "hash_atual": "d9dcf681e0d99370f00b3dd6116850a08e345e925869f4c09d54d1f6304da919",
        "tamanho": 8524,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c7ccd792d34a2d5285567dac67d0dc8653aa6dc4bb59276b101188c442fb9e30",
        "hash_atual": "277a51d6c0588e97df3f9f5d7e53f28de8b7fc03c92145950763086ce17f186b",
        "tamanho": 29449,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "237bcb6d7da08d4cbce48d81acdf6c5ce07e4d9ff4a482dd4ba0b57e6a914333",
        "hash_atual": "96e519a9d2d318bae6e6d0ad167181142b2ec71591010709c192b0d0d7b6f2a7",
        "tamanho": 10070,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e0fb621e69b1f677948b4aee4badbcd139cb5618cc321df894dc5b043037dc37",
        "hash_atual": "19f432d8a0c488f6c7455d388c2c45033a99843a6434d63d18245bb8e6517ce7",
        "tamanho": 6219,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "77199d49c88c47bdd1bf54c97cdbc15a55321909e3c2d33325751e990b0b7aa9",
        "hash_atual": "942c4a440aee620b9f7d79603d150c3462ce2953bccbd190ca119c3b84e23fcb",
        "tamanho": 7208,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c49502c8ddc9381c29b4632aacc4fdfd95d9ff1fbd6c0eb36045d2376397e8c3",
        "hash_atual": "e5dc4db19eca540a0e20029b610116f57c7c87d83eb71de87f657107711367ad",
        "tamanho": 9296,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "02101435d83dfb3152f5b1ecda9f83ebe47d674323a99fde087f8fcc481b0d3e",
        "hash_atual": "0fbf915ab25cf677219acef0eb6f18a632966f8719c8c31859aad084b174dc90",
        "tamanho": 20506,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f18569fd4732f6c773898249f740073c9be32541be8134945a99f27a06592463",
        "hash_atual": "4362b81eac86d61edd81dc827bb2cb4c84b632faccbcdbac5fdd5234753cc32a",
        "tamanho": 59888,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "61f436208c8ec746e8c14a30553b04dd16533be682eb3692f3e157855e2a54a1",
        "hash_atual": "bcff218b5bbf8348d4cf5816aa2f7528731fd0c06564d4e66bba0134b9d7efaa",
        "tamanho": 28604,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "553afb78d6a441ccff4de549ad45bcbfbf02161895b65fd0f889438e41d16777",
        "hash_atual": "1847a50388beb1c890cbec92d84348da8ed3e403cbeea6250a1ec8145c289ff7",
        "tamanho": 15343,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c2373defbb2d81f12c5419cde792f80d4a43b3022276c921bac9f21e31c58105",
        "hash_atual": "c073239cb99b9d9fb4c366ae4aa23a12e40ac9b9e73e42e72476daf42c345fc3",
        "tamanho": 26051,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "904d2cec6e5f5f8ce7e15db9076c5d3363378cda61bc7f3b2a9c2f3d7195e4b9",
        "hash_atual": "dad8ad22e587199ccada4d1d5cf152994ae6ee798adaa6a9e97fa3714f47ac4e",
        "tamanho": 25889,
        "modificado_em": "2025-09-22T18:07:26.000000Z"
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
            
            Log::info('PreservarMelhorias35Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias35Seeder - Erro', ['error' => $e->getMessage()]);
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