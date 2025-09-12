<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias85Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-12 00:44:21
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "00ce7d04f6db7ae9f9b00706de01d7d4eb3665922fbdf0b5c99d91e25e51e9ea",
        "hash_atual": "17b84c60e394307831468e6193fdc68df86ade76149e40901c6fa1261ecd8322",
        "tamanho": 194828,
        "modificado_em": "2025-09-11T21:55:11.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "b78b55311dda5f621d58f39cc34194958d53c81b2a06959183befdde43e0f4f3",
        "hash_atual": "743c45b28b95fe1f2e28d84fb0f2be18ef48ccb141017ea3f8ff89d4e4fc519e",
        "tamanho": 33929,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "d21303e229742ca98efc7bdb96cd702893a2530420e0cd31128ecf07591fe1bf",
        "hash_atual": "fabe7ee44e156e931f8a43174ce173c6032b132ca428085ee2dbefa58840164f",
        "tamanho": 184884,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "a2c4ce1914de94783c95e5184b1900b3d33daef15f9ea74fe770590df9206d0c",
        "hash_atual": "c8306acf070f544504b55b24870bfb56b0b9d0003ebd992c4173c77c6a0d8dd6",
        "tamanho": 37954,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "456f5eae4b50a13eb0328e9f8a31633f0619805f8694aa90cb1a823c00de441c",
        "hash_atual": "0ad24c501d88e712282787b56d8606b0cd4705437b121ef514ac1e9a5551c553",
        "tamanho": 16468,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "09a79fb026bacea6217389ab88d1d0465604a4c6134169b0283bdc1f938b36b4",
        "hash_atual": "8f63d76b5749813bb78ade84282412eae120e37fcd7e7a911acb4a66337e0dbe",
        "tamanho": 18417,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "9982117a439d6f39be168ea83c5de8a56f620dfc35ff9ecf09d18bda87f0ceb5",
        "hash_atual": "8ded29185094dffc891358528a4313e63d838e3cadb84beec2ac53e5d2638b29",
        "tamanho": 11594,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9e27b01582aa957c68f01df0d88fd64901c17243c0b28683dc2ff021e02d72c5",
        "hash_atual": "4ebe999a68cc41c4ab60e91e14b885ade933d1c40c9e9afe2334d228869ec834",
        "tamanho": 90333,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f1d739583f84a7e88eb6f96ad19397ea601ac5d0b529b8d92e6a6efa13bec522",
        "hash_atual": "414aeaa9071f10aca277406ab1a6170a5632eb60a31fcd18ced30561410f5a0d",
        "tamanho": 69556,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d24077f36cb1821cf52cfc8f26f67fd8c33a0ffbc5a3823785da3ab7e0c6e763",
        "hash_atual": "9a48cfe6b59904856c2666a8591da0f24f0214e74b72a0d4ee568dcbe969df8e",
        "tamanho": 64199,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "da5a30132a17f0be4e7cf5ba2b31eb083b39f06f40fad27cc7d7da5e7d41ab3b",
        "hash_atual": "aa3ad4292172d8027dc07c51b2ccc3318d20d45bdc9fe325c37fba73adb9376f",
        "tamanho": 21668,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bdca26f0240809991518d4b88fcc5f838fb7ee7a08782ccd1d612aa8f8f94c9f",
        "hash_atual": "3a4ad0471afd63b2fb87f54136783878c34840caf6bcaee13a7417d0050f9212",
        "tamanho": 39431,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0a967d8db6e489cb812cefce5637e91c0ee81cd5ea4856d73d838d6c7b3de30c",
        "hash_atual": "f54508c50b5e0c7190bbdcf3738323ace49e2d57ba55dff5a0f8328a62c2689e",
        "tamanho": 9714,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fe73e797a781edceadd9e28174cb98657c0533478bce89a4ad1f9f984639fdfd",
        "hash_atual": "36424bf1e3e5afbee8d641a531f92c77c575745672d9413ce4e8db940150e5a6",
        "tamanho": 2116,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ccecf8cb392ed240eb71e8dd96063e3b00db059a7e4d1053364e26bc178b6d09",
        "hash_atual": "eadde6baa50504ae3716f13aa902b832cf4c62dd49aa6233be96ac9ea4e8a162",
        "tamanho": 8438,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "99aa038296da8f2596ee42ea21bf94495c540b671e7c094fe953121ccf53e109",
        "hash_atual": "61341e83297329751bbb41a5a3d7e517573163456b0da961f6dfb50f4e6bf127",
        "tamanho": 19647,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "96187c4e030c3f00370e8d9fa5cda7aef938aae24f461891d65b95764e095108",
        "hash_atual": "8e93cf7d6deee051d748fb4e07572b5c992af13a1b0dad3d7c37d874f7d1d1dc",
        "tamanho": 18651,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "76533bab3f06d323f00927dc01709488251aae80e14c203fa3e5bed8576c0e3d",
        "hash_atual": "2f06ced38319e5e32566a73af8aab9616615881394e7ce5bb3d38afdf3d48c89",
        "tamanho": 44459,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f4f9a836cc839d9ab523e95b487d59805c28de0035518ed4aa75f6281eda82b2",
        "hash_atual": "64a321dd8e6e1472a34e0a092cd10071d97654deff5cf60fe5110ea61593138d",
        "tamanho": 1169,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "56341b3b3633d2bbd5dd3245e4f2ac4e20d3ee182c2f5e804016482a7ae204d7",
        "hash_atual": "806c4ebcc5577d90a278cb3dc14655ab2a619eb439f4c857454ca4e163202ef3",
        "tamanho": 10124,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b7880fb73e83c78053d9e57b8a9d7f4b3ac9eb98904eaa978a937a4501d57782",
        "hash_atual": "4188b638dd1b687533f424ac290d5ecdac0b0cdd4e5bfcc0d19716c7ba6491f7",
        "tamanho": 8297,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "10c37f48db1c4d156712f59516b8127fdbb5cbd057d6cf0c017c5be2dddc3173",
        "hash_atual": "f19cbf5cb292f3873648d708b1d6537e20124ca6bc04480e216956c0a1568715",
        "tamanho": 8524,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "665ba441781236e72a7101f80168eb33e3b3e2418a006f8030082bd0caa86f8e",
        "hash_atual": "46063c7b32078e0685838aed2367ebad1dc79f7cd2e4170cd0aaa51de418948f",
        "tamanho": 29449,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c2155c239544475681276d0d5484262416794a6aea8ba61240c0cf21e7cbdbbb",
        "hash_atual": "4445723a890b49453f856da7f588c7a191a43ee769b88c7ed4f3a9427dcbb8e5",
        "tamanho": 10070,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2ed2d21921b4a9af85493303d1d725a4daae1fedaf555f56adaa871c3b9db32d",
        "hash_atual": "08a359e259512a725bb4878d6100e5398e9322b0c942c7fd57d7edcab56a428b",
        "tamanho": 6219,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ca2bf74736ac57e6fd01f6ced32d79bc83f853dc985c99dd8a507d6757634945",
        "hash_atual": "582449c6c9a0d54b6d3c46c175049e858a0806550bb44adbb019285226997f5a",
        "tamanho": 7208,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "830fc4878db0e297300e92c5586eaf87254769607f5db9a923c28ed4adde07cf",
        "hash_atual": "ea5369a6db86d364239fa85d4f0b1720d6d7341cd0e567411695a571d64eb4c0",
        "tamanho": 9296,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ec37f71e6c72eb14a1018b0a0a3f230a6fffa6e8e1a3e170fa128ffce008bbe6",
        "hash_atual": "c86dc8d297ad17ae28c5b7a8d19c9724bd99759660fb9e8e409414b8887456a9",
        "tamanho": 20506,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8555a35d948fa265fc00ddb61271ac410e46cf9420b60eb35dc6547603f59395",
        "hash_atual": "8f120a2fea4e77e11def3bba40e2adb43f9e5a9d24045ee439f6829c83da9034",
        "tamanho": 59888,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "89ea461ed314a0f61575cfe0c32c4da4eb2f9096c764d6de0634ff50848812fa",
        "hash_atual": "6d660c6e01c3a593c1e0042d8443264e3bceb472218d239238deba28ef3e033d",
        "tamanho": 28604,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6eac5ad4e243644714940c5cc0a6a8078e47f9637b4ef72a6568dcd163f6dfee",
        "hash_atual": "ce716418f77e2eb05ee3556f041d8c6d308ee595159247ff8264376c59561e47",
        "tamanho": 15343,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0aba6410212ce186be0b40b8b6e051b09b6cc7c210e559024589f37e8b6d3276",
        "hash_atual": "3b3b1c3b670b108132ed77641827379e9577f46aeeab5940011eab97af4dfe0a",
        "tamanho": 26051,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6cae5dbe4090edc6a6a0f0d664963824b123d103792ef74fc6c6d08b9a615ea0",
        "hash_atual": "741e5ed6561073d8a68ab24dfc98029c1bb175b088bbb484d5f993e171906edb",
        "tamanho": 25889,
        "modificado_em": "2025-09-11T21:47:51.000000Z"
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
            
            Log::info('PreservarMelhorias85Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias85Seeder - Erro', ['error' => $e->getMessage()]);
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