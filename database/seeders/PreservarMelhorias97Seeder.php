<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias97Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-12 02:16:12
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "9b46deea7f8825732f1725ea4f96da944899466fc7cc15f452b1e3affd7b5064",
        "hash_atual": "eabe37e2cf5946904ff65c3a7566e03493a4a51d912a5c85abd8aa46eb737d36",
        "tamanho": 194828,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "a32e53276a874de4faffc5e2bc0ebe6bdc6aa0a116b3c1c6048cc28e02bf7460",
        "hash_atual": "ee09aaf43b332e7f9858e9e13a00be982f6bc88f50dacaae4d927798af2e36dc",
        "tamanho": 38821,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "7ff6e01c9ec248a9d347159d0cafa5bd2d974b0082bed68c7f7f373a0b2c1572",
        "hash_atual": "1237ba40d499428754fad005106e04a3f9c10074a40eae53eb522defa5a459ff",
        "tamanho": 188969,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "fce6c1b3d3b6d081452467ca4cf9280082624b3abd24dd37ff7856550b6a0a5e",
        "hash_atual": "10e74d9b8c7fab3b4967e8f88cb37336f8ce8527e70a0bec27988080e777a6d7",
        "tamanho": 37954,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "14fb5815fbf2326403c32d2b4b2163c902d3c5c9cc7d692788c28c065a1d0d4c",
        "hash_atual": "3cf668c444a87e294dec26e7944c94b3bdde3aa86fb3f3a80ea1d3e6fd2f8ba0",
        "tamanho": 16468,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "b368353943a958ded060f8424173ece2314661149f2c293e2a2f3300f7fc540d",
        "hash_atual": "b38efefe3140fb4a9f04c2f7e116152e26042c0053061e9b82242ac57c874527",
        "tamanho": 18417,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "1c564b509bb4e4a93d1ae0a607c321a1e9bab20929a9a7f92b9b157029446fd4",
        "hash_atual": "28809833b375eea5a74c7774e2881072fee1608bd61fb6bcb21a79fd093c7c48",
        "tamanho": 11594,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b232109cc7ff43f83452e3fd8bd51b6c0beddc48dd799c8590e23c78d9156b6d",
        "hash_atual": "aa49885d2e68d3b9696ac148ff9cec84b816b83e7a9615b1302345bfa2bee4a5",
        "tamanho": 90333,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5ae68d9cb9f18cd89009d316eaba58c9e6a088d015bd78fb1a8e80a88e908d16",
        "hash_atual": "d245f9d712963acb298995277c65337d16d6113a3cde0e1f2b178320713ad205",
        "tamanho": 69556,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "13253d6dc2d30af46f2f700c7c2bc0be84dc66cdd7b15f7eb0f2bfea87eeb4c6",
        "hash_atual": "2f4ea29f8c28941cf91cfbc99c754d6faedf3933dc56327936a7dfb90cc61fb6",
        "tamanho": 64199,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3f954dc20026ec75850b017209fcc0794d46e975bd1f7c2bb6473605432c3efd",
        "hash_atual": "f9fc496ccb8af4c50363c23fd8ed4b0939a39b7badcd6eb34471481e5ab8043d",
        "tamanho": 21668,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9d33c0b154ba2a1ede0721f80780e0feeb1517f4f400a3cff5a93a2f150c9266",
        "hash_atual": "f15ec2891ad7aa65d64cad206ef7241c76c058e63af230ba668548aa0bf3ad84",
        "tamanho": 39431,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e2b79e5c2e710607c8653012b5ccd686cd17a25d00fdf4c3f86ff5e12ce65649",
        "hash_atual": "7074bcb067a0afc2d2336ca921d6fbf029f1c5f26a5638385c4399b091627ece",
        "tamanho": 9714,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "78043c538a135dff6f88a645de21c46d867c5149b21daaaa3c9afa9956feb633",
        "hash_atual": "2cd07155894b64b3843509a2701db8f9ae88af9581340e524e29b5c73364f498",
        "tamanho": 2116,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e5158b53035cbb43349a1f28f72003ff53635ad5c02d2c728288f76fd103cf72",
        "hash_atual": "450a42238be2e8620199263e4fdfb895a61135481c8a419bf29cc9c9b98ae026",
        "tamanho": 8438,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e95928fa3fd3196d3266ab7b0a2a4c07a7784866f8474ee0ace2279c257942f2",
        "hash_atual": "f5060106b24635cae9cb2843ed74c90d17f90d87940c1eda1144b03c064fe373",
        "tamanho": 19647,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "91fb47f92324a105e6451f469783fa4e265be6537ad3fcb8b18399575418533a",
        "hash_atual": "1e65d089068f0c571a7a1672d40442d33223944d8e682a81c1a66887595bfebe",
        "tamanho": 18651,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8436e007a134ced980fe3e41a71322e7b9a76113671c2fc58058286ff05304a3",
        "hash_atual": "994f45fc168e3e85f29ba2342e228bf120e02a4ecd9f04c20f111b355de33842",
        "tamanho": 44459,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cf29043f31840549e8e71a6cd6117c74219d16f3c56f59f574ee9c189189e7f0",
        "hash_atual": "7827d745e7aa56155379f37f10d6664e313af21a93d5352b644d5be0cae0a237",
        "tamanho": 1169,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ec174342d3fe3a8dd090deeccd1eb992d9ce555cee9a60ff17c99b5b72164929",
        "hash_atual": "5212954b8e4b9325a93941d97584b0bafe47b521cdb85239e82f9a0d0fb00eaa",
        "tamanho": 10124,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1b8d1893c721726ba2778a7835d2f774f75e85d03659953f49ce18a60ef64b1f",
        "hash_atual": "896648894e675a4c67a646a4ddd05de7672173322a77b5d7111f7401efb44622",
        "tamanho": 8297,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7121c33a8a8a0f87f9db8b57103211a997c5e634f9796090bb07cd90ca322ee1",
        "hash_atual": "76204a4c94d90bb3564600aa84353003ddf8adb3967883cb0f904a94619d70ce",
        "tamanho": 8524,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "99abc9af182d0da6ec9a0b11decf898852206355f1fd133eea42309816705411",
        "hash_atual": "5c0a8f4e8ff6f32d8ca2cd8b1887d8d4ac734a81b09d5954772fccc2b23edeb7",
        "tamanho": 29449,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6a860101e960558504b5cc30d496ce1d0793541c5a79aa102e22ef92b28b2ac7",
        "hash_atual": "d2d30fdbc9014057964ec09b803bd764731101ebc5d02fe9cc63fda3f9b093d7",
        "tamanho": 10070,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3028125753cd7fc9c457a75115952f4d3a4c59ece5b6c5826bb1d848e1841f3f",
        "hash_atual": "0afba3222a548efbd6c9300e4fc4cb95c24924bf0d7b38602830c491a386a47d",
        "tamanho": 6219,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2cadb82ab3c927f8564f596f9b1b548190b960f6a01e4556d6aa9b427602440e",
        "hash_atual": "43058266e119279c5b0bd329eac3a5d351230657cf3195da71546828b7588a78",
        "tamanho": 7208,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8cece85c199d9e04218194a26a1f63ab29c1ec02e9121435438891ebfb1eaace",
        "hash_atual": "f6f09eff1717266b70ad6dbd5dbac3757844d73b1a42458e50142ad62d3002c6",
        "tamanho": 9296,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f2d2bb277386616fe778bef029f3597b1d66c829cd464617dbf3f8dfde4917a2",
        "hash_atual": "d07727021088c4a1943a2fdc77c06265c503b1665c4651574dbf71050bd48c2f",
        "tamanho": 20506,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b2dac6eea9915297c45b6524b1eb0a346aec704e6b3795ec615e93cdf8dd918a",
        "hash_atual": "52e81a5ae91283185370609f3ed70aeb87f202137cf5c8979c95d3dd7f32ae27",
        "tamanho": 59888,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "87e6c7f7ea22f87f2ee49e4bf07a6730d78e11ef08feaa91d452becf1b97f17b",
        "hash_atual": "9393e870f4ba7866216034cb831cbd1e7a9d3fc75bab5790691d86099565405c",
        "tamanho": 28604,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0f582358efcdea1ced2867d51fe9e5ae07c09cbb3366ddbabd1cee7201fb8495",
        "hash_atual": "2ab85e5021124b22fe3ae80e0f4b2057606c7f08bb35ff54bb273ad790158b41",
        "tamanho": 15343,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4a146ffaf5d7edabe32d747ee4af0656ae151582d9cec89438b644c4d8b4cc60",
        "hash_atual": "69f5e1e7d5de2a7dd0b659d4b594d5c58a872de3a5e170262f8228a830755386",
        "tamanho": 26051,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "275f843dd7752ed5b644b033e849ecdcf06d9d63e673695d418cdb8bbe7fc6f1",
        "hash_atual": "08dd4c50fd4dc0906180b79fb686f1e471ea4cb17aa0b217444f48fbc83434fb",
        "tamanho": 25889,
        "modificado_em": "2025-09-12T02:08:52.000000Z"
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
            
            Log::info('PreservarMelhorias97Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias97Seeder - Erro', ['error' => $e->getMessage()]);
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