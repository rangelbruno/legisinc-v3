<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias99Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-12 02:17:33
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "eabe37e2cf5946904ff65c3a7566e03493a4a51d912a5c85abd8aa46eb737d36",
        "hash_atual": "a47ffb1eb6fdba12ef41f7c5a1f895fb76ebdd218f3461fe7ab703c5f7952a5f",
        "tamanho": 194828,
        "modificado_em": "2025-09-12T02:16:22.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "ee09aaf43b332e7f9858e9e13a00be982f6bc88f50dacaae4d927798af2e36dc",
        "hash_atual": "a39d35fabe86c3bbd63e38bccc7d79aa15f397603e4c242ed885362ce87ded12",
        "tamanho": 38821,
        "modificado_em": "2025-09-12T02:16:22.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "1237ba40d499428754fad005106e04a3f9c10074a40eae53eb522defa5a459ff",
        "hash_atual": "ace765f17d56841af7c16b8e488a5e9e888484e109d0e98111822e2fc174412e",
        "tamanho": 188969,
        "modificado_em": "2025-09-12T02:16:22.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "10e74d9b8c7fab3b4967e8f88cb37336f8ce8527e70a0bec27988080e777a6d7",
        "hash_atual": "bbd82db6382415379de0bad3044b594b567f1f537d9a4d02740a0a41ddcc42b9",
        "tamanho": 37954,
        "modificado_em": "2025-09-12T02:16:22.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "3cf668c444a87e294dec26e7944c94b3bdde3aa86fb3f3a80ea1d3e6fd2f8ba0",
        "hash_atual": "7e5a55977840539df5808a5b6b317ada1a18de321add987633192c4e52ab9bc1",
        "tamanho": 16468,
        "modificado_em": "2025-09-12T02:16:22.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "b38efefe3140fb4a9f04c2f7e116152e26042c0053061e9b82242ac57c874527",
        "hash_atual": "259d60deeda3138042d35963b64d8279f35d5810f20bfb1fe8bbfaa6dfe42558",
        "tamanho": 18417,
        "modificado_em": "2025-09-12T02:16:22.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "28809833b375eea5a74c7774e2881072fee1608bd61fb6bcb21a79fd093c7c48",
        "hash_atual": "6d5953dcec4adc9a737f447217ad4432b3f0bbe72433537b26e16b634983c1c0",
        "tamanho": 11594,
        "modificado_em": "2025-09-12T02:16:22.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "aa49885d2e68d3b9696ac148ff9cec84b816b83e7a9615b1302345bfa2bee4a5",
        "hash_atual": "a9ce1d39245acf877eb6da3411cf2277fd49270d72041656f6568e859ceb00ae",
        "tamanho": 90333,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d245f9d712963acb298995277c65337d16d6113a3cde0e1f2b178320713ad205",
        "hash_atual": "4bde96713a659abeb72afdbc13a9abad40601d8c36ef23acf8537a694287c8e5",
        "tamanho": 69556,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2f4ea29f8c28941cf91cfbc99c754d6faedf3933dc56327936a7dfb90cc61fb6",
        "hash_atual": "d75719199a503ec5d2f4f9b1925f5ab3c8d6ce36553604f30bad13073652a4d5",
        "tamanho": 64199,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f9fc496ccb8af4c50363c23fd8ed4b0939a39b7badcd6eb34471481e5ab8043d",
        "hash_atual": "c21a2ecb50c88a5956f4d84c63ae9994d543c8660d4139409d2c292eb4eb3a92",
        "tamanho": 21668,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f15ec2891ad7aa65d64cad206ef7241c76c058e63af230ba668548aa0bf3ad84",
        "hash_atual": "d2b6b774a3d378ff56f2cf509e8be0b98dcbf20e3a9692cdf63fe80844beb5c1",
        "tamanho": 39431,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7074bcb067a0afc2d2336ca921d6fbf029f1c5f26a5638385c4399b091627ece",
        "hash_atual": "5ef408883c085bfd0a8e7c4abada77bfcaa3e7bb4881d921112d73b66077cfd7",
        "tamanho": 9714,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2cd07155894b64b3843509a2701db8f9ae88af9581340e524e29b5c73364f498",
        "hash_atual": "37ec806afbb5801039ca18440d3ecf89efb6e66f2e32a02b88f1864478d7b278",
        "tamanho": 2116,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "450a42238be2e8620199263e4fdfb895a61135481c8a419bf29cc9c9b98ae026",
        "hash_atual": "b0d3e585c06734459d08c8188366c203c2c9e42f8d8c8d1a4fee0a93a3d0eafb",
        "tamanho": 8438,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f5060106b24635cae9cb2843ed74c90d17f90d87940c1eda1144b03c064fe373",
        "hash_atual": "1975201d4b44e598bdf19080220617151876800312c5fa0001452679f3eac270",
        "tamanho": 19647,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1e65d089068f0c571a7a1672d40442d33223944d8e682a81c1a66887595bfebe",
        "hash_atual": "d2fc38011089bc1ac20af22dc060718f3973520f9c41125291f8d88bf2558271",
        "tamanho": 18651,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "994f45fc168e3e85f29ba2342e228bf120e02a4ecd9f04c20f111b355de33842",
        "hash_atual": "5060d8832c40165656aff182c8a551ff85c1c3599e98f6244342cb9015055df5",
        "tamanho": 44459,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7827d745e7aa56155379f37f10d6664e313af21a93d5352b644d5be0cae0a237",
        "hash_atual": "08439b463c151779c364329e07b8dbcfecbb402d85d11a3f2a5d2f0a43ffa90e",
        "tamanho": 1169,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5212954b8e4b9325a93941d97584b0bafe47b521cdb85239e82f9a0d0fb00eaa",
        "hash_atual": "270ac3d7598bcc654e8c833c0ef3bb7bc2f166bba399dc7f5d1018c13f55ece1",
        "tamanho": 10124,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "896648894e675a4c67a646a4ddd05de7672173322a77b5d7111f7401efb44622",
        "hash_atual": "5778197f3fa6cbb5fdf69801d33dfc9aca88f45c8861715b0ade57d38b61387c",
        "tamanho": 8297,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "76204a4c94d90bb3564600aa84353003ddf8adb3967883cb0f904a94619d70ce",
        "hash_atual": "1838b309dd5769e9b523552f79248bd3e4186fe9204e3ac7c15f3388c571d78b",
        "tamanho": 8524,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5c0a8f4e8ff6f32d8ca2cd8b1887d8d4ac734a81b09d5954772fccc2b23edeb7",
        "hash_atual": "7cc23079f97cb383b9587e7253f775c93b18f8809af1fd51116bb8312cb2e4d5",
        "tamanho": 29449,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d2d30fdbc9014057964ec09b803bd764731101ebc5d02fe9cc63fda3f9b093d7",
        "hash_atual": "8f3f41ab5d282474d48385da62acc240fdfbed948fbbb461788481a73d4a4acd",
        "tamanho": 10070,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0afba3222a548efbd6c9300e4fc4cb95c24924bf0d7b38602830c491a386a47d",
        "hash_atual": "755f5e089c887579b711858879ab4696ecbec8b6e20a50fe3910196b0742fbd6",
        "tamanho": 6219,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "43058266e119279c5b0bd329eac3a5d351230657cf3195da71546828b7588a78",
        "hash_atual": "15cb484a5b060b2689afa417911e67c55222b2fcef71f74812069a14529040f3",
        "tamanho": 7208,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f6f09eff1717266b70ad6dbd5dbac3757844d73b1a42458e50142ad62d3002c6",
        "hash_atual": "92aecbe4d9735ff798bdae60a84bffc7310dcd709250eb2e9b7e932a433c22ca",
        "tamanho": 9296,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d07727021088c4a1943a2fdc77c06265c503b1665c4651574dbf71050bd48c2f",
        "hash_atual": "18a89ab826205552cfeb0efbda4e8711db4d40dfc7b4828c2090369ab65c6e79",
        "tamanho": 20506,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "52e81a5ae91283185370609f3ed70aeb87f202137cf5c8979c95d3dd7f32ae27",
        "hash_atual": "ff3a06751f049aac564766ca56d6fb6c69f244b1a99f0ad18eff2003fc0e8978",
        "tamanho": 59888,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9393e870f4ba7866216034cb831cbd1e7a9d3fc75bab5790691d86099565405c",
        "hash_atual": "d49d8dbedda9defa7f082665b9edfdcc08d9be40825fea89421c600e4f51c3a6",
        "tamanho": 28604,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2ab85e5021124b22fe3ae80e0f4b2057606c7f08bb35ff54bb273ad790158b41",
        "hash_atual": "8327b9e88d0145baab81b1c50f86f3906c3954658edc68538b5399002a557322",
        "tamanho": 15343,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "69f5e1e7d5de2a7dd0b659d4b594d5c58a872de3a5e170262f8228a830755386",
        "hash_atual": "8f8e1736e0aefbed1c226f10796f967e3509876445ccbee5df434c55bb2a18bd",
        "tamanho": 26051,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "08dd4c50fd4dc0906180b79fb686f1e471ea4cb17aa0b217444f48fbc83434fb",
        "hash_atual": "6533c1a1df9c9d1015d49d041541e3b690adfe8d6ba11f14f8060dd831e8c757",
        "tamanho": 25889,
        "modificado_em": "2025-09-12T02:16:21.000000Z"
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
            
            Log::info('PreservarMelhorias99Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias99Seeder - Erro', ['error' => $e->getMessage()]);
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