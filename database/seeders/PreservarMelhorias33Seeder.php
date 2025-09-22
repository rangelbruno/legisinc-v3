<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias33Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-22 18:07:14
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "eb7a4a29e82d6de5a3a2e7e0d2b730b7e81287b977810b03f1744715be4eada8",
        "hash_atual": "d535642332ae2407ce8be22e68264bf794f6a008b1cef4326fb0bc20d7e98da7",
        "tamanho": 199451,
        "modificado_em": "2025-09-22T17:15:10.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "979cbeb701e81c7967a4e63058462c5f4fcb4ceeaccce30609f14c48325a5f2b",
        "hash_atual": "9feb34a81e5b1e4c2b0d5a47799542e3e5a3994f93e45fb2e7d0d730915984f5",
        "tamanho": 38821,
        "modificado_em": "2025-09-22T16:53:54.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "dc610e152063d778a31b8484893bfe16b2bc1bb8845cf11d33a30a174f7d5ad7",
        "hash_atual": "14571e3791993c531ef0ceefda94fc1d48e89f335390118a87700b42a9720451",
        "tamanho": 190861,
        "modificado_em": "2025-09-22T16:53:54.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "20f71b68b67d7ad90999abf0b486a5e8b1611a43798bb63c04696f5c84a3433e",
        "hash_atual": "9902d7d13de14321fe99266b3cfaf7d9becfb622ff4b3e687f70ec7cee784163",
        "tamanho": 37954,
        "modificado_em": "2025-09-22T16:53:54.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "e24eee86ea22485ba01cb6f8d32fc77a18da421e2599efaf0bc82623246ac75e",
        "hash_atual": "9a7eed032b066b4804f0deaad4862fded4789331154fe22aadb8808ccc1ec0e3",
        "tamanho": 16468,
        "modificado_em": "2025-09-22T16:53:54.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "78a97a490f1e4de7e67ebce4f05f3274248a3769d9a1984d83a1604aceae7260",
        "hash_atual": "b2055b757fd5d12e65e1adecb470db91d13b9ddf382659a415637700ebcb9769",
        "tamanho": 19682,
        "modificado_em": "2025-09-22T16:53:54.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "bbcf80930ba4dc65eac89b5dde318f7f606e132f6187bf73577d1e04eed9d3da",
        "hash_atual": "964495238a4acb3c3e76746c99a5a6c32596369801157aae013ace670eee1ffd",
        "tamanho": 11654,
        "modificado_em": "2025-09-22T16:53:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "553945e1210c45123e790573da7783080ac31a696f12bab3f585a977d21fadc8",
        "hash_atual": "f089178016f28979dec262f7aceab637c74ac6721ca748fb4210eebb739eaeeb",
        "tamanho": 90333,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bc6756eea43fc204e4cb71c97d2d18e24045777e918eb6ce64e7230bbce4bd2b",
        "hash_atual": "ad73dd0a82a356d91748a83342753c63953c7254397d2707f198c17e3d65466e",
        "tamanho": 69556,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fc14f5162c9cbea822ffb0c712a286a08aa2c4ad87d2bae1c09c081e3da371b1",
        "hash_atual": "ef3cd979cc36da9571eea8d6668492dfdeca29861b51a93ee9e069d460bc2a97",
        "tamanho": 64199,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "296f37ffc2de920dfb279e5c941ea036834d624e26009bb59f662d886371d1a5",
        "hash_atual": "23faffa25d7face175659eedd604e364b5c0b04fe7b93a7e025c5e02dbc6985c",
        "tamanho": 21668,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3f50d67773b4b3ee67784d8866beb929d974089f19e0ab08f5ad7fc7ea82fc95",
        "hash_atual": "6af19c05bf6232136c748724fd3ca1718bb3fd5dbb7403edb4f016c88ada5c78",
        "tamanho": 39431,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "015940fb0a8b4025d9bb6f88f9ce23617b97a246880951393e8c66fbf6d0b373",
        "hash_atual": "09b2d3f1f22e53af83c19303ebaa4f0bfeba89d9a239d5715edae4d75ab15360",
        "tamanho": 9714,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6686a9a6bfb36575468e48ece3129d4f4ba7a030a8557cd14ef034831883a639",
        "hash_atual": "79966e8372d3545ea54a7251da20df82c0a81c91b67639a719fa6673151a3f0a",
        "tamanho": 2116,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3928fde4aa8626ad5d12840b90394bb4a106631a9bdaec9e32bad76688282719",
        "hash_atual": "ee3bcd223c7303de34fda0603410c6288f891ece7f1aa6c1e1a05c6d6136e996",
        "tamanho": 8438,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "109923291b20c0f8b9d0dbb6bb9014be92b3f298d370dd321c2911700c80daf0",
        "hash_atual": "2e1e72af1bdd958f566b92717f1a3399a968ec54f79fc8854c44f50addb20ea2",
        "tamanho": 19647,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e51dc758dd3a20fc90e254a2ffe51fa12b8b696c1e1c144d2f370606fac7dff6",
        "hash_atual": "e5b205a29075d04bbd9d4caedc86e6c463966f1858aa4755ed4398beb7c884b8",
        "tamanho": 18651,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9da58f35f6806f519036fae382d764290c31efbd09d8a3b013a53af790863dca",
        "hash_atual": "f9578e338296e88244766301ba32738098b82c248c18dafa6f4b2d2ab1a3825d",
        "tamanho": 44459,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "addaffd23cf71f1f71b3b822d9c14b46da133868e8547329bca97b868ee94d1a",
        "hash_atual": "86f4631a9a7c6a70073213149951e89b8be40dd416e639ff986f71c83f90d0c3",
        "tamanho": 1169,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c036b98bb5a8eafc5eae58594bdc1f26384b2f21fa26d6a2b5cdc9c257df8c43",
        "hash_atual": "3e422bb3830ea527f275b3447672c615ccc83f31bae33810940c6f89269ba924",
        "tamanho": 10124,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "71e350dcc24d21e1d6eb543ff8aa90e10498f0ce61fdc55e9b4097a42dca8cb4",
        "hash_atual": "c38ad83fd267030ffa3713a96087c6c629ee1646c105dd310802c24785d4f6bb",
        "tamanho": 8297,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ce84a78a8af22d41a3820ffdddfdd959fffc012cd7d74cc445df7da98159557b",
        "hash_atual": "c94e5297d811bf335008b5a29778b8d3bf3748504ce95ec2cb21dd64998c2f3a",
        "tamanho": 8524,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b3643269baea429665cbe7c2b12f306dee0edeee6639a36de7d538d1db5cd8e4",
        "hash_atual": "c7ccd792d34a2d5285567dac67d0dc8653aa6dc4bb59276b101188c442fb9e30",
        "tamanho": 29449,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e6f70c66ef97eb4cd045833b5a207f75266692f2ba244e6ee986f6b39478a8e3",
        "hash_atual": "237bcb6d7da08d4cbce48d81acdf6c5ce07e4d9ff4a482dd4ba0b57e6a914333",
        "tamanho": 10070,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "aacf3194060b1e254ca212f72467dda15a54fc76b3f175cef9d25fbb6a4330bc",
        "hash_atual": "e0fb621e69b1f677948b4aee4badbcd139cb5618cc321df894dc5b043037dc37",
        "tamanho": 6219,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f6380417759df953e2e6c6b6bfe79a2668271ed2d2edb486897f8a2269b25b62",
        "hash_atual": "77199d49c88c47bdd1bf54c97cdbc15a55321909e3c2d33325751e990b0b7aa9",
        "tamanho": 7208,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ad5e9113256d17fc075fedfc91d9f978764e14508a17d740d6639b45168645e8",
        "hash_atual": "c49502c8ddc9381c29b4632aacc4fdfd95d9ff1fbd6c0eb36045d2376397e8c3",
        "tamanho": 9296,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "528c231de2649fab50fce5978305ca9e3d3685a24c756cd5966d75fae64aa62e",
        "hash_atual": "02101435d83dfb3152f5b1ecda9f83ebe47d674323a99fde087f8fcc481b0d3e",
        "tamanho": 20506,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2af5f387c5fde775c93a5d0daf2d9bea85f4634257025c954af76d677e00da8d",
        "hash_atual": "f18569fd4732f6c773898249f740073c9be32541be8134945a99f27a06592463",
        "tamanho": 59888,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a7c93517e153e9700faf8cf139c3808066aa8b50b9351a7b479662f669a0713c",
        "hash_atual": "61f436208c8ec746e8c14a30553b04dd16533be682eb3692f3e157855e2a54a1",
        "tamanho": 28604,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2c95e97a80a68a37b242bfd7c193504326f44319b9b55519e485730feb2f6db1",
        "hash_atual": "553afb78d6a441ccff4de549ad45bcbfbf02161895b65fd0f889438e41d16777",
        "tamanho": 15343,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4fce5a40770f6b9a8cd8def0188ce6ccdfab65bd00a9bea34341a1385d4cb1b2",
        "hash_atual": "c2373defbb2d81f12c5419cde792f80d4a43b3022276c921bac9f21e31c58105",
        "tamanho": 26051,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "814fec7d64aaf7d0af90dd1fa1ef5082a7c06812d2eef755fb248a0a0b69dc1a",
        "hash_atual": "904d2cec6e5f5f8ce7e15db9076c5d3363378cda61bc7f3b2a9c2f3d7195e4b9",
        "tamanho": 25889,
        "modificado_em": "2025-09-22T16:53:53.000000Z"
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
            
            Log::info('PreservarMelhorias33Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias33Seeder - Erro', ['error' => $e->getMessage()]);
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