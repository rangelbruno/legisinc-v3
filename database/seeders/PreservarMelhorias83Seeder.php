<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias83Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-11 21:47:41
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "e2a82e215d34d928a6a9ba9d572cfeeba6e586c470af88f1a86c3e64820dcb3c",
        "hash_atual": "00ce7d04f6db7ae9f9b00706de01d7d4eb3665922fbdf0b5c99d91e25e51e9ea",
        "tamanho": 185055,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "02672a583c93ff46ff94fa0c4a9d97d6a206e1114d6729d0359375042ee5a482",
        "hash_atual": "b78b55311dda5f621d58f39cc34194958d53c81b2a06959183befdde43e0f4f3",
        "tamanho": 33929,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "53828de38930df399ad8ea9ff2f4ac062d0a06f640eb6c5c56b76bbb5141ab0b",
        "hash_atual": "d21303e229742ca98efc7bdb96cd702893a2530420e0cd31128ecf07591fe1bf",
        "tamanho": 184884,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "bef8c97355f65ff6967981daed0da112db8be84d10e587446ce1728f46138a0a",
        "hash_atual": "a2c4ce1914de94783c95e5184b1900b3d33daef15f9ea74fe770590df9206d0c",
        "tamanho": 37954,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "d61d18228a311471fa94c0bf18325cc33e33889d5276c2d481b95a4b80fc712b",
        "hash_atual": "456f5eae4b50a13eb0328e9f8a31633f0619805f8694aa90cb1a823c00de441c",
        "tamanho": 16468,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "39b230b22c22fef530f018aa19e5dfb434240ae50fd7b8794fcdc7b98793ae51",
        "hash_atual": "09a79fb026bacea6217389ab88d1d0465604a4c6134169b0283bdc1f938b36b4",
        "tamanho": 18417,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "a8c19b758983b0d35c22bf88ec48a8447d269e3ded7203d71037f4135f82242d",
        "hash_atual": "9982117a439d6f39be168ea83c5de8a56f620dfc35ff9ecf09d18bda87f0ceb5",
        "tamanho": 11594,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "47df120509a20da7fd99f84bf818f74423bb1d7e32d8cc89da619befd3f69402",
        "hash_atual": "9e27b01582aa957c68f01df0d88fd64901c17243c0b28683dc2ff021e02d72c5",
        "tamanho": 90333,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3d259deb55ea72de1078a0abc6bfaa61962ef9c185c148245b26131bb52d28d2",
        "hash_atual": "f1d739583f84a7e88eb6f96ad19397ea601ac5d0b529b8d92e6a6efa13bec522",
        "tamanho": 69556,
        "modificado_em": "2025-09-11T21:45:15.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7f1490554511dfa90c6f69907447da188ea54c8080dca82cbe42b79e956f9fbf",
        "hash_atual": "d24077f36cb1821cf52cfc8f26f67fd8c33a0ffbc5a3823785da3ab7e0c6e763",
        "tamanho": 64199,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "612d196c054f1117e56a337be51a15fcb4bcaadd615a93478679ddfc0a24eda9",
        "hash_atual": "da5a30132a17f0be4e7cf5ba2b31eb083b39f06f40fad27cc7d7da5e7d41ab3b",
        "tamanho": 21668,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "252e73295de5e357f029ae2c4cbd7eb5317d3a59e6549faa14646a9fcc08adee",
        "hash_atual": "bdca26f0240809991518d4b88fcc5f838fb7ee7a08782ccd1d612aa8f8f94c9f",
        "tamanho": 39431,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9fc98b6b6fd516415e01f5a7ddbba02ed96dbeead96853d45b0fcb3f0d7c9479",
        "hash_atual": "0a967d8db6e489cb812cefce5637e91c0ee81cd5ea4856d73d838d6c7b3de30c",
        "tamanho": 9714,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ad2e4d70eef2d6bb9a7a79af84f825e4bbadccc6ad7d7a8234499da7624149c8",
        "hash_atual": "fe73e797a781edceadd9e28174cb98657c0533478bce89a4ad1f9f984639fdfd",
        "tamanho": 2116,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d04066e81b9de823b5b2d03146295cc5dd90550acecd83f8d6f2d789432e48fa",
        "hash_atual": "ccecf8cb392ed240eb71e8dd96063e3b00db059a7e4d1053364e26bc178b6d09",
        "tamanho": 8438,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1f343230456b575797f161516566ab238c335f8bb459a6a21662f510a4ecb5d3",
        "hash_atual": "99aa038296da8f2596ee42ea21bf94495c540b671e7c094fe953121ccf53e109",
        "tamanho": 19647,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8646bc0ef99bb029c7b6fa2fc80db7d03826a620674a2c4d8d62e4053f5aa807",
        "hash_atual": "96187c4e030c3f00370e8d9fa5cda7aef938aae24f461891d65b95764e095108",
        "tamanho": 18651,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6f3b57af2f83e5eedb8ede5dfa8e0c2dcfe5d4ae2f14a907c2db466b9d1fab7f",
        "hash_atual": "76533bab3f06d323f00927dc01709488251aae80e14c203fa3e5bed8576c0e3d",
        "tamanho": 44459,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3a5f18c771b84d15032d8b466b88c6e473b34cf425b86bb3049907a9368db063",
        "hash_atual": "f4f9a836cc839d9ab523e95b487d59805c28de0035518ed4aa75f6281eda82b2",
        "tamanho": 1169,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e73fe4667df62378314e27d80466e4cb760ec3d15c3344521d584195afb97653",
        "hash_atual": "56341b3b3633d2bbd5dd3245e4f2ac4e20d3ee182c2f5e804016482a7ae204d7",
        "tamanho": 10124,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ef12dc9d8e96b0cb42e1545338d9ecf4e66b871f63d839226e0ace6d8f3b9f7b",
        "hash_atual": "b7880fb73e83c78053d9e57b8a9d7f4b3ac9eb98904eaa978a937a4501d57782",
        "tamanho": 8297,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "430ecef0395ba83adb184a5cd02bff2c614e9e63d11ea04832ba5c79689de8be",
        "hash_atual": "10c37f48db1c4d156712f59516b8127fdbb5cbd057d6cf0c017c5be2dddc3173",
        "tamanho": 8524,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0cfbaa3275e1a77acd5840893a02abd69e9d2449ce3cd9fee4064d27c35b9771",
        "hash_atual": "665ba441781236e72a7101f80168eb33e3b3e2418a006f8030082bd0caa86f8e",
        "tamanho": 29449,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "26a77134db6c4de2683120d270aab7d33bfd8d5e8c270ed133c5d03d1665ca0b",
        "hash_atual": "c2155c239544475681276d0d5484262416794a6aea8ba61240c0cf21e7cbdbbb",
        "tamanho": 10070,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6241aac1c23da3e2fc80ca2adfe472363baaffcf9244b2cc711c2864a4ed4273",
        "hash_atual": "2ed2d21921b4a9af85493303d1d725a4daae1fedaf555f56adaa871c3b9db32d",
        "tamanho": 6219,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bd95bb74b99810da8e5820649d3837e5f460a9f0bbe06b07938565fe2ecad49f",
        "hash_atual": "ca2bf74736ac57e6fd01f6ced32d79bc83f853dc985c99dd8a507d6757634945",
        "tamanho": 7208,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7c49b3de9709fe67253e3f3a60207d0bb6b326c903f1b69fab668570821e6c5f",
        "hash_atual": "830fc4878db0e297300e92c5586eaf87254769607f5db9a923c28ed4adde07cf",
        "tamanho": 9296,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e490896e3178098144f32952b4ca67f8e938e39e3b85f60ad688ee0ee737a556",
        "hash_atual": "ec37f71e6c72eb14a1018b0a0a3f230a6fffa6e8e1a3e170fa128ffce008bbe6",
        "tamanho": 20506,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "212900d03a451a318992838777fb257f6abbef2512d962d0babb146d479f7b85",
        "hash_atual": "8555a35d948fa265fc00ddb61271ac410e46cf9420b60eb35dc6547603f59395",
        "tamanho": 59888,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "276fefd1d8e876974d1b4b5f433c291927277ce9d75666ed9e203aa0bd700560",
        "hash_atual": "89ea461ed314a0f61575cfe0c32c4da4eb2f9096c764d6de0634ff50848812fa",
        "tamanho": 28604,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3be92eb2eff9a15b2c120c63f28646bbbb67f19d36a293dd74d831503a29685e",
        "hash_atual": "6eac5ad4e243644714940c5cc0a6a8078e47f9637b4ef72a6568dcd163f6dfee",
        "tamanho": 15343,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "df261d38d720b47258c0cf0bb2ca663b8d74e14bb014a6c278532d7f4b8c9313",
        "hash_atual": "0aba6410212ce186be0b40b8b6e051b09b6cc7c210e559024589f37e8b6d3276",
        "tamanho": 26051,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "28ef08d42fbac7cd7504869e10e161ac8930fd616a83e06c60afbfe59054d35c",
        "hash_atual": "6cae5dbe4090edc6a6a0f0d664963824b123d103792ef74fc6c6d08b9a615ea0",
        "tamanho": 25889,
        "modificado_em": "2025-09-11T19:48:27.000000Z"
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
            
            Log::info('PreservarMelhorias83Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias83Seeder - Erro', ['error' => $e->getMessage()]);
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