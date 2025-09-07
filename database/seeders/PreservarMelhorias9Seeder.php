<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias9Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-07 00:32:54
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "293600003d8ff6c319f7d3002ce375f3ab43b1f0c2a4b4e26f80a6b031a6ecd3",
        "hash_atual": "3a419b5835d9846240c8d313e12f7b33842e900a6d0e74a71ecb24f074567222",
        "tamanho": 183240,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "dbca528b7d51669ebdff514caa2c49162e1d6476013dc92fe1e18717104fa63d",
        "hash_atual": "580fee26336f4c28c4498fc3561774c6c42a55143639b9df44d1494a075ea618",
        "tamanho": 33855,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "0e6c73771a2f3810ff6ae5365fd16e2e58cb6404f5d823155629f7fb6c3699a1",
        "hash_atual": "796906530b6f3a00b77ab35e3d8fecdd6f9b1900943d42aec3ed1a792cb4982d",
        "tamanho": 184884,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "996e7a69ea4d88232669bcaccc622b801d5e20d18ec870d19a077e2e228dc24a",
        "hash_atual": "e6f2947356bc5dad283c2e81f50bf9872a1bfcb815011e9bf678a2e33533aed9",
        "tamanho": 37954,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "79ddc8047eba09a6275448c9c2c136df10da1e999a0c8d8f5fb0e9b60e26b692",
        "hash_atual": "c87414909ada9b2c4e8060317eb9dee0c7f4b92306cedf62ffd9412e7ae3748f",
        "tamanho": 16468,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "ef10b5cb3a0fe9139c2f7d04a3f3d615c44c93615855349c5baa5918b1857162",
        "hash_atual": "ddee2a28915ae0a786457a2e6252bcc25faaeabd0a3523b120f7b9cc6c8efa1b",
        "tamanho": 16728,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "6f1390afb492c25984490dd988769a98f04f2615cdd01d1af67e1147a643f712",
        "hash_atual": "b41eaa4bf493d8de2c900994590d133ccfb287d60cf55c5941f936fe19844078",
        "tamanho": 11594,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0f07d21e9711879132e45e348e3cc55ee4a5c5d3818ea964df246f7b4b1e6be9",
        "hash_atual": "60be9c88051b312614fc6e68ad6e275fd8be292cf5f4e9603bdc6324d87df035",
        "tamanho": 90333,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "092c342122d2f7e971b54e94cbf1316604cc33e68ad4e4048fb2ce5ed038780b",
        "hash_atual": "1d417b3bdbf75a3c0ffc223c3d56fef7d001a49add3e5a47d13f17157d27453d",
        "tamanho": 49890,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ff0470deb05421f517089cc99f3d5dfd1b8cf1385572f4481e42f4b0a3ae0f99",
        "hash_atual": "a1fbcc331480fc40375125543d3e2ff4f7ddcc3fa2efb5ac5366f14105a86f4b",
        "tamanho": 64199,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "30c45b0c16b2f2abd4e45707c669a957344b79b8ca9c133173388be6bf96d490",
        "hash_atual": "ccfcf74aed52bbcb0fe368761f40186c0172d47524454cd85fb0d3e8b5d0118d",
        "tamanho": 21668,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "949c3a4c4961f6784044e16c9ef3d36de40b601009f89a226cb61cd2ad37d74f",
        "hash_atual": "69d29053136a0a9298dc5ad0afb707db3f73dbb30b4021d6d721cc5853417364",
        "tamanho": 39431,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a96ee670b3711feac62847296bd570d6038d2b40dd2af064ebc9855eb464bbce",
        "hash_atual": "7c4a6120bfa313984ef306fb8c536f515d6f2732dad20d210c5d0cc3744de1ff",
        "tamanho": 9714,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "753081f3221e6556a10fbe6033ab0c8d00dcd9153efed74c8324231155dcec61",
        "hash_atual": "920710e30d0ba762712a2b153a93ce5bd2aaa1dc1b394ff2cd17f155c6bb1cc1",
        "tamanho": 2116,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "59a5bb31cead22722bf84e35efddf12f51b665f92b4e2d351d8559a09ffdf811",
        "hash_atual": "d651e5ccdf92f6bd51eec2845bf22e4b89c355038f8725bb94c6bc258ad262f2",
        "tamanho": 8438,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f216b20872664781a1a6149fd059f5eb0694a32a239aca5275eaced66c568c25",
        "hash_atual": "5a2512365be7407f6929be032967022e758c9c9542dbe5803891c419443a0fa5",
        "tamanho": 19647,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bf05848883ee985c9af846bba478f709ead184405ca0921aed86a544c95d3cdc",
        "hash_atual": "cf016ad89d435a8c66b0a2bc41697e41854398852d16cf56c95dabc806b692d3",
        "tamanho": 18651,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e2840c4a0bdb26d959609334bb96ca6f15b04ead43c3ce22776aa10ec12c0d18",
        "hash_atual": "2da2e5c668e1d4a4b7647d1c65cc99f7cf94712bbaeefc39929408c66ac4eec8",
        "tamanho": 44459,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "84e3bc50cf48908a427b8a165b52228692ae7561104304875e6d30a795a5f430",
        "hash_atual": "b51b189bb5643c930017ab2833352f1e0e7276bb4f707228864e238d4b7eb9a6",
        "tamanho": 1169,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cd1931e2c47fe239f117c299f5a2ae84c11a9c57691510161358b21cf6e06048",
        "hash_atual": "cf7f1f6833210ab8ce750830d6eff91722b27d6a6bae9357a01752248a6fe118",
        "tamanho": 10124,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e1940c7e6ac0a43cf264ee39450fda0afe009a7588e1b92c51452b7b00d05e46",
        "hash_atual": "70f56b5462082588b4e7aa3bc99989bfc0e37c404bc162db98a5fa5ccbb23700",
        "tamanho": 8297,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9cb934147f4ffdb184109caf531208daeb3043f0a22d0590426013200c96e52f",
        "hash_atual": "e0c02d5fb603496a5792ef58064f68e4cecc0a00b9069129ba234d8d522f4287",
        "tamanho": 8524,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cb6ca3de2c35c313ccc5abd6c63a50ddabf723eaea268e5414c16cad857c1290",
        "hash_atual": "0cb950ae41a3b8caaa14a500783a71899ba6e0832c39cb94ff7156f354baa2c0",
        "tamanho": 29449,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "de0c16aa77228a70d5fd543d370df6928e05953d563b56be61fab8304413ee57",
        "hash_atual": "272a94ab1a92c3fde72d0fbf916a4fd7e6acf2aefdcfbbf166791e80a04c07a8",
        "tamanho": 10070,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "79753af551d4eb0f8aea5dbb87c5225f601693050b8635fe28293dd6581013cd",
        "hash_atual": "adb5e16262d617f483af189273886d6eb3645effb2173ad469991ffabf92dcb8",
        "tamanho": 6219,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f27ae47e4bf4842e275eb597ab76aab9c42514a53a5aee84b382931dcb307ffa",
        "hash_atual": "d80b26e35748253f30476fc9877c22a37fcf668d2661ae6b2461649b4612ee8f",
        "tamanho": 7208,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e11121faec6031b35888f8f58dbb5a6d03b114172545f45d80853ab3aa7c9232",
        "hash_atual": "f9d400b7f17df023710db6f701b2c1aff89049114bbcd82963cb3f13800f2d50",
        "tamanho": 9296,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b085bc91b9c7cb35353e66c3e6a9d62bcc1a911d7f270fff0382e219b7e878ea",
        "hash_atual": "2c3c70029d0eb55c42123d332a8a51fea6204376a01f142710fe250c32f84996",
        "tamanho": 20506,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8c5cbbb7c83099c5cb3768fea3c012b58db0671525959f10705276a97adbeaa7",
        "hash_atual": "cb7af9ab17ab5f382dc0083cdb491171680eb0e27429a8f2454156d922b9ff7b",
        "tamanho": 59888,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5a21f1e3b5d14161f6310144d12c8b867488de62cf9cc8c4e924d8c52145317f",
        "hash_atual": "de46700311054a7dfeae8bca526c4d2828563b22ddd70e21bf1e1a392cca1e7b",
        "tamanho": 28604,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "470f733485fb06105c41bd0fe6e4fe72f98d968d025758295b8f42b942466aad",
        "hash_atual": "d48f1c797710f249bd2da09f729947c2ab3d72c4ea79f7f875bf8622fc30958c",
        "tamanho": 15343,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4be98296532b14fd15b36e9f596fd4e8aa3a01d51699e8f7a0c1f3f1612ce468",
        "hash_atual": "2ef6de416e2b6dc3cbe41290c0b13ce6b622d2e5e2c39e1b2b7fd6f58716492b",
        "tamanho": 26051,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2e3a592c13ca5e370a35162647085ee64ce03e34ca9f4b4384adae40c690b2ea",
        "hash_atual": "e4d1d29b5290de4029b88d8863c0906b8055a08ba6a415bc6782cd6afe6a9d6a",
        "tamanho": 25889,
        "modificado_em": "2025-09-07T00:00:08.000000Z"
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
            
            Log::info('PreservarMelhorias9Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias9Seeder - Erro', ['error' => $e->getMessage()]);
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