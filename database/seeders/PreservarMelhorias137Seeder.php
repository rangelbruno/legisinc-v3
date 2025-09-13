<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias137Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-13 12:22:20
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "e0418b5855bb859c5f867515afaa3a4bc2121974b5959156527a57d3f55f86da",
        "hash_atual": "365250a78e328f63258525d8e248186bfdcee58e4f60c2cef963c82286eebf32",
        "tamanho": 194593,
        "modificado_em": "2025-09-13T03:40:59.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "a3d3dc61eb2087b52b0ff7bc9f65c6e507311b3e3071c18305d8928eee234552",
        "hash_atual": "a329e83c12d0037584809e6b23ddef3d3a6c6353a88470c6d2d74955f0448ef1",
        "tamanho": 38821,
        "modificado_em": "2025-09-13T03:40:59.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "f8878621316457f348ba5b4aab82fb6add0113b9c0efac1ba0d29ed5ffda3374",
        "hash_atual": "17f8950ff913bb4fc19b9646b4092d71101d8a05e851aaa29a3529a8b722cdaf",
        "tamanho": 190861,
        "modificado_em": "2025-09-13T03:40:59.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "514db06bf2ce4337c5d8734e5f7b70b5bae655ec560678b7c7e7fb212d3c97f2",
        "hash_atual": "9c4b804775f10263fe6cceb87fafe13dbe14c6001dc3f8bece582e52332a3b54",
        "tamanho": 37954,
        "modificado_em": "2025-09-13T03:40:59.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "41cd49cebd1b6cbc774ce0447f0590450d55d05bdee233734033b7b348200c79",
        "hash_atual": "2a223d48f21793e12b5aba9249d188670a9218ea961d0eb4f5c074cb9e9ac9ac",
        "tamanho": 16468,
        "modificado_em": "2025-09-13T03:40:59.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "fba86d959d5e16429d6bebd7a2329240ba1e860766f3144d51c9f8fb75c92b5f",
        "hash_atual": "bed62efa7f9d58515b2aa1494a5d05dc426df1556d0143b8e85a1111d326e8b7",
        "tamanho": 18417,
        "modificado_em": "2025-09-13T03:40:59.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "d9c46dfa4e5b0470e236fe5d7aec17fbbf5084eec54d49db34b95119bd22f626",
        "hash_atual": "a57831fd6c46a7df1820927ba79366800657777329758a58eb677100603f5e42",
        "tamanho": 11594,
        "modificado_em": "2025-09-13T03:40:59.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1c9afd9c2a656c2f472c5e3b035030e35194345d06c87eb9fb53457a4b061a85",
        "hash_atual": "fa85c89995036c093dd9af1c931850268be28c4929352cc723dea7f96939374e",
        "tamanho": 90333,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7df360f4b12659d689367a301740e806e376bcf0fa959c6f65fa22485126e091",
        "hash_atual": "5d2e9160c57c590ca297eabdde7e3cdd7fc88b0bbd6a1df6e7338f13242a32f2",
        "tamanho": 69556,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "22f221c078d5e8c2d1f18db803738c0985fe17fccf3904c567f95b141e255541",
        "hash_atual": "da4bf992f9a97a2c3e1a3efec0f0e9545c7cdbcab33a8227475ffdc59ebfd033",
        "tamanho": 64199,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "016a0d4a5d91c20f1d43ea5222d618312edda87307d513298605ebb7341f7018",
        "hash_atual": "e3bef3e95e730189b8add356b31bcd40e717e6a82520a13ddb8f58ccac2fad65",
        "tamanho": 21668,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7588c6549bef7a06b3ad5d22491841ddfe7a6711bfab871f8e974e54d443e71e",
        "hash_atual": "9cf8e5cc7a4fb1958ed147d299b63cfc665e235900c35795ec77b2e88a77e167",
        "tamanho": 39431,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "68e5396bfac5c1e065e0077048e13247c800b62a8794485debb675aa7680d123",
        "hash_atual": "8dabf63c6df253e731afbad1d6597b028ab9c259e40ec273d48e92623e21a352",
        "tamanho": 9714,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b61ec8c2f58be6043b67107258c874ffc662324117acae522a3073437cefbffe",
        "hash_atual": "ccb3ba0fd23cd28a2935f5be66b7b789c44e67d489d132c9884a8d467781a063",
        "tamanho": 2116,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d6eb532100d986b5a72c8652e78f9edde25ea4b7ca5a34e09260bee0458a9b9e",
        "hash_atual": "4817f3ca51afaeaee50a53d058d5715dd91faa36ac518de330589cb3cef3887c",
        "tamanho": 8438,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6fbd5ae44546fe227e24abd48452e9960e7ede8b2b591680dd9ac428b4ef1814",
        "hash_atual": "ba5421945117d7788b834687b4ce4be002c7322974ef775d2bf191d3f41fd74e",
        "tamanho": 19647,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f1e63660321d0889f61bd824ae674f68e4f6b55adb339a917dc46301d32ae178",
        "hash_atual": "d6e14ab11398c52f07a20c986ce0ee5bda63618e196e23595c1670fae3c3e5f1",
        "tamanho": 18651,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3cebf883b931c8b1e8e19bbda1489ca7a4925eea6c686f6957171da6bcbc7e9b",
        "hash_atual": "8926f2438895480ebbfc0953b485cf83c76d93b7dfea24b96a5ddc3c3d20b7d4",
        "tamanho": 44459,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "24f23686f44d87309324f3ad33211ac00b80857e84289aa3aadf9369b4777463",
        "hash_atual": "94efea01298576ae5c2f5482022efb61fde01842c7438283bd5345c100ad25bc",
        "tamanho": 1169,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ea6da50d5c4582e9402f2d29c41abf01a7fab1bc9c6faa46e7d44312d1ab45cf",
        "hash_atual": "2facc7a94d90c3bb6792ecb22b7148b70db6dc74b3509b649070e52c33954e4d",
        "tamanho": 10124,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1845b080cc7d16c9b671eca7a2b4872c4cedba25ce9d042934d5185949942ee3",
        "hash_atual": "cf64af0dec3a9c96a49e50571282eb0b12d7921873f406465e6fdd59ef3347e4",
        "tamanho": 8297,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9b4ed47c9c61656e4e868ffc57e7829624ca25e5a9563d8054edaaedf9f7233d",
        "hash_atual": "c02620c9771cf98e86fc4d695c0adcf0e5bf2240640a8bf4cfbbd336b05a3a54",
        "tamanho": 8524,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "351cc9dc9e204e8dea128ab583e69c5dcac6f0ea21db5dda348b987726633551",
        "hash_atual": "5f3ce0183d34a7b342c08913067e5f6618e60d15f0c33f2413e36253e5052f9c",
        "tamanho": 29449,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e5a1665d76ac277b8aaefa8ec2845e1b0946fd0487d7efa31db4899d19ac6de4",
        "hash_atual": "82f0d6fa14549d29f99f0abd422ee64fe6cc56614f49d263e1e11fd9df0b9737",
        "tamanho": 10070,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6251d84b5d20df6fec2575b4c726ac6fc549ae3a6bb4a0496ed10c42bd560f89",
        "hash_atual": "e5c4cc4027692d9c28e2326f25ea0634201c488f02559287509b3385bca8cb54",
        "tamanho": 6219,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "59e717cf55d63188ae3d92b92518b532eb8171e040cf33ae49601ddd55b4f6e3",
        "hash_atual": "accb741985d12df93074fa0bc8cae0c105ae45261dee7594eaa08a88da8be90b",
        "tamanho": 7208,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9e4e93172fc035bdf27e5946cdcfdd68d3529ecb75e2527214fdcadb8963e110",
        "hash_atual": "01a7a5676ae2e5b36a5fb26a7f08f7bffd645a5ba70fc537a91cd5f8ea9b45c9",
        "tamanho": 9296,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9b745a1ca23d3b913918106cf5544da815446a026bc2cdcf586d1672e3c87c72",
        "hash_atual": "e6ea4b5e41d8020ab727b49ddd5014735177cb000fb582bdb30bb6db5d3de83f",
        "tamanho": 20506,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "49702d8d1e90e6137b4c2df082be3cc77d4bbaefcaadfada5cbc177cc2d9ba91",
        "hash_atual": "a245bc99d13bd505ed89654de1d213f632cdba0057d7c91b6edfc64a1a9cbb11",
        "tamanho": 59888,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d224f9c17309631b3903168d2f4827aa39fdd51d41d515cf8cce068329171208",
        "hash_atual": "6df08759acfe88f5410f3a8427e0b6b5a7700280ded1fd63619eee232ba6b2b6",
        "tamanho": 28604,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "59094d04444d50b438ec47206b137eb71281d4dbfffa5d60e741abafb0b8c140",
        "hash_atual": "7a8142a88dafea467e79abfec6bebe9df818686b8f8a4b0c047918976b045f6d",
        "tamanho": 15343,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e095d0f6de4a255ac367ea685a54a42c4253f28d00c255d60df7b81bb5e84761",
        "hash_atual": "4a4c2334e72e6be543d610ffaca24c630b047fc71df918869f6ca425b4d429f4",
        "tamanho": 26051,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a84ef0e6b3cb883de058ed0894d578841997cf3ed4cab59321164064ff2349b5",
        "hash_atual": "6a3a60e69fce99637cb3c8a69c2492d4e6fff8eb97349ad449ed092021b6bd38",
        "tamanho": 25889,
        "modificado_em": "2025-09-13T03:40:53.000000Z"
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
            
            Log::info('PreservarMelhorias137Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias137Seeder - Erro', ['error' => $e->getMessage()]);
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