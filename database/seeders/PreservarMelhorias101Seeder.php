<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias101Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-12 02:33:20
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "a47ffb1eb6fdba12ef41f7c5a1f895fb76ebdd218f3461fe7ab703c5f7952a5f",
        "hash_atual": "3f346e6dde6612aca99d772aab44b9bd92c66e3647beaab8443afac714d9b835",
        "tamanho": 194828,
        "modificado_em": "2025-09-12T02:23:31.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "a39d35fabe86c3bbd63e38bccc7d79aa15f397603e4c242ed885362ce87ded12",
        "hash_atual": "2424b01dd639359d85507a06f9c7568bdb5861fb3f2dda0a77e78b190c2d9ebc",
        "tamanho": 38821,
        "modificado_em": "2025-09-12T02:23:31.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "ace765f17d56841af7c16b8e488a5e9e888484e109d0e98111822e2fc174412e",
        "hash_atual": "f2c32d5fecb83f2432f9d6d7a7c9d391971111ddf25dfa24bf790fd079b56aac",
        "tamanho": 190861,
        "modificado_em": "2025-09-12T02:28:43.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "bbd82db6382415379de0bad3044b594b567f1f537d9a4d02740a0a41ddcc42b9",
        "hash_atual": "3f1210cf3aee0c760a0879403da824c6021029a927a5850caba542276cb0c55d",
        "tamanho": 37954,
        "modificado_em": "2025-09-12T02:23:31.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "7e5a55977840539df5808a5b6b317ada1a18de321add987633192c4e52ab9bc1",
        "hash_atual": "bc553406691bd6b2b0ff0089e1eaa9e3fe7815bdf968fbec8b4e542af9269349",
        "tamanho": 16468,
        "modificado_em": "2025-09-12T02:23:31.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "259d60deeda3138042d35963b64d8279f35d5810f20bfb1fe8bbfaa6dfe42558",
        "hash_atual": "0fbed0af14f2f6600620cc39a00d359b8dbf62964a6f6ef51d286f3a25800073",
        "tamanho": 18417,
        "modificado_em": "2025-09-12T02:23:31.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "6d5953dcec4adc9a737f447217ad4432b3f0bbe72433537b26e16b634983c1c0",
        "hash_atual": "fe3287f896ebc7b2d2d2e3626568c88faae33c4350c13cb40435de9b58300625",
        "tamanho": 11594,
        "modificado_em": "2025-09-12T02:23:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a9ce1d39245acf877eb6da3411cf2277fd49270d72041656f6568e859ceb00ae",
        "hash_atual": "77fa6a83885ad629462a154de740edb52452c1a6159c3cda9da0822704987ad7",
        "tamanho": 90333,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4bde96713a659abeb72afdbc13a9abad40601d8c36ef23acf8537a694287c8e5",
        "hash_atual": "03cc7a85f38b322617eed5087a36f2660da88b65586866fddc92aadc64ea0169",
        "tamanho": 69556,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d75719199a503ec5d2f4f9b1925f5ab3c8d6ce36553604f30bad13073652a4d5",
        "hash_atual": "b6cbe10a8fe2da59f69866f10dfd793ac0e7d1cb435804c9b469beb8dd5ac5e6",
        "tamanho": 64199,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c21a2ecb50c88a5956f4d84c63ae9994d543c8660d4139409d2c292eb4eb3a92",
        "hash_atual": "72d2e31c16bc2ae7a22d46f2ade1444d7db56bf32674b53e76e61be92564bbb3",
        "tamanho": 21668,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d2b6b774a3d378ff56f2cf509e8be0b98dcbf20e3a9692cdf63fe80844beb5c1",
        "hash_atual": "f8c58f3cc991df80b0c577da754909d05455a6af9cddb7ca094b5677444ab5bc",
        "tamanho": 39431,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5ef408883c085bfd0a8e7c4abada77bfcaa3e7bb4881d921112d73b66077cfd7",
        "hash_atual": "72ba00a0318d5c33b7f69114c8c3075a95299dbcc936e6c21a437c82194200b9",
        "tamanho": 9714,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "37ec806afbb5801039ca18440d3ecf89efb6e66f2e32a02b88f1864478d7b278",
        "hash_atual": "996b5f23f70aeeffd9c6270908ae1cbae3eb45688c7d0e05307e7906f6cf57c0",
        "tamanho": 2116,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b0d3e585c06734459d08c8188366c203c2c9e42f8d8c8d1a4fee0a93a3d0eafb",
        "hash_atual": "2919934565c5c1b52c70e4ce3878e1e03f79896ccf228d715615354466f874ae",
        "tamanho": 8438,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1975201d4b44e598bdf19080220617151876800312c5fa0001452679f3eac270",
        "hash_atual": "e9baff439f1ac3a325de3c06314b79d72c4e563f3b36fc2603c64add24b4ea12",
        "tamanho": 19647,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d2fc38011089bc1ac20af22dc060718f3973520f9c41125291f8d88bf2558271",
        "hash_atual": "28dda9dc5d13b3a35b52bf19d28d1c737d39dba5b24d013358acfaed0e739b83",
        "tamanho": 18651,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5060d8832c40165656aff182c8a551ff85c1c3599e98f6244342cb9015055df5",
        "hash_atual": "40d8a12e4f8ea72b3a894dea33ac1ec0e47aed225cf2a28a5583997f71cf2da0",
        "tamanho": 44459,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "08439b463c151779c364329e07b8dbcfecbb402d85d11a3f2a5d2f0a43ffa90e",
        "hash_atual": "9732dda67dc26a34b643a31ea2f3c7604ecad9828c01432df5849d63a4da8b06",
        "tamanho": 1169,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "270ac3d7598bcc654e8c833c0ef3bb7bc2f166bba399dc7f5d1018c13f55ece1",
        "hash_atual": "216eb40a71164840d678df67d94c7042a68417d7327000287afcb5b0169bbbdb",
        "tamanho": 10124,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5778197f3fa6cbb5fdf69801d33dfc9aca88f45c8861715b0ade57d38b61387c",
        "hash_atual": "8d52f532fb0478d0feb7a9021bf7b007123dc3143fb48ae83d3a5bea6557e527",
        "tamanho": 8297,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1838b309dd5769e9b523552f79248bd3e4186fe9204e3ac7c15f3388c571d78b",
        "hash_atual": "1f4e8fe9c0dc6ec47222accefab72285f0533c206ca97babfaf94b2fe01c2828",
        "tamanho": 8524,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7cc23079f97cb383b9587e7253f775c93b18f8809af1fd51116bb8312cb2e4d5",
        "hash_atual": "fb9f48125058ee344a895e37f649265d2544a7fdf816964e6197a31e2590947c",
        "tamanho": 29449,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8f3f41ab5d282474d48385da62acc240fdfbed948fbbb461788481a73d4a4acd",
        "hash_atual": "e9b4e636e76792ec65e4dcc7e75a05bc00625cc4ca242df147b72640ef58d399",
        "tamanho": 10070,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "755f5e089c887579b711858879ab4696ecbec8b6e20a50fe3910196b0742fbd6",
        "hash_atual": "4c77e3f367e7bb8c33a02cf4a1ed6a74556dfe1ad24fcca7596d6eb267a06601",
        "tamanho": 6219,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "15cb484a5b060b2689afa417911e67c55222b2fcef71f74812069a14529040f3",
        "hash_atual": "a15c22dd0c268fc32c6dde830ab4784b8bd657d83a635545df667bf244548908",
        "tamanho": 7208,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "92aecbe4d9735ff798bdae60a84bffc7310dcd709250eb2e9b7e932a433c22ca",
        "hash_atual": "8d100e65a0730db04a56ace81f6451494d6fc969824ca22db9ba62c00079cfa8",
        "tamanho": 9296,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "18a89ab826205552cfeb0efbda4e8711db4d40dfc7b4828c2090369ab65c6e79",
        "hash_atual": "fbf381de53df6d14325db0d412e86275f69d541d1985db430ffc548aaf1dc4e8",
        "tamanho": 20506,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ff3a06751f049aac564766ca56d6fb6c69f244b1a99f0ad18eff2003fc0e8978",
        "hash_atual": "2276fcca4bb5c00add1d673d94155ac6fd984044e7d9eda5b43aecc35e7b8ff8",
        "tamanho": 59888,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d49d8dbedda9defa7f082665b9edfdcc08d9be40825fea89421c600e4f51c3a6",
        "hash_atual": "f2a4991041f17d994012771cfa859aca419f6f4541cec2c3a7170418e5d721cf",
        "tamanho": 28604,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8327b9e88d0145baab81b1c50f86f3906c3954658edc68538b5399002a557322",
        "hash_atual": "f700638773e8da8186061d632e6ef066d52614a5582c8a47f939ec9c1281a54c",
        "tamanho": 15343,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8f8e1736e0aefbed1c226f10796f967e3509876445ccbee5df434c55bb2a18bd",
        "hash_atual": "e9495b92f894140e4074c5149b6e6a7dc846280c8dc0f90428de8cee7669e411",
        "tamanho": 26051,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6533c1a1df9c9d1015d49d041541e3b690adfe8d6ba11f14f8060dd831e8c757",
        "hash_atual": "02dc98b4e5abb7c4fbafea03f6fa0f02c224ef2802dc19edf54cebb1d6a30230",
        "tamanho": 25889,
        "modificado_em": "2025-09-12T02:23:30.000000Z"
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
            
            Log::info('PreservarMelhorias101Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias101Seeder - Erro', ['error' => $e->getMessage()]);
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