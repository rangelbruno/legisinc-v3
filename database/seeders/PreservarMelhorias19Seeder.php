<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias19Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-07 18:54:15
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "e397698bee0fed07d7a9a26420692895f68a0eebc982136ad1f3285185256615",
        "hash_atual": "9407a9cf05611a3003d16769af9dd4d21467bdbee1fe48ebb07c70e2c636374c",
        "tamanho": 183240,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "64c19f10d775d2fab7cdfea4f603f167a37ebca5e8aa8770e6a0311c197a65db",
        "hash_atual": "248bb7b50898be360ea079c89aed546b4a2f278c8d002770d48768b8a46fa775",
        "tamanho": 33855,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "dcc3d1f030803bea0489e61d3947f89696917066b0e192fb12ec1a096d9acf9e",
        "hash_atual": "7f2c5699c34ac2d51ca75dd278c8c8c229816bd1d31154c33cab38de16ddfea5",
        "tamanho": 184884,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "5b473e1b2d4018501a09377d66ac4d15d086b46a074131fda5644a88dc735cb4",
        "hash_atual": "09a522438b633398010b49b2a20a4ad18f0bdaafed1532b77e8c3334fd8ef02e",
        "tamanho": 37954,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "44b44518bf5777b4a5bbe7beddc568271540ae9290c4481869f6dbbf45174edf",
        "hash_atual": "051147b89dc08590eedf4a6330a454c902a72898cf11beff365ced3024b3fdf9",
        "tamanho": 16468,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "757885cac7b7faa5ac5d1b3232465d9cacb91e83923cb53b00427e14f4c4d1ab",
        "hash_atual": "4b02fb0a5421b30ef1e448dc6ab1487be6573c9a14f39df90ff8cc7b1520bcaf",
        "tamanho": 16728,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "628a742eac4c9157bf0a869f331ef8adc48d4e6c2d32ea34c58afe80a8d238a4",
        "hash_atual": "634eac763f609393a8421b22541b2347beacd2c4d9303d130c397d010e64aa2b",
        "tamanho": 11594,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "eb1ff529c99351c9f2544bd2441083fa2c6cd8ec58f76758a67098f0003103f2",
        "hash_atual": "b37bfca080c1c455b0b6fea280a09118f4b73e80a2d00b9105b95c800931fa43",
        "tamanho": 90333,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5938205a2c971f35a26b783d4638a86bf73d50cbbee4953f51dfe61569afcd5c",
        "hash_atual": "7d53d22a71a3e4cda0a1541c1998e9562c64fd72ccff9939a4bb164be8055020",
        "tamanho": 49890,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "79d06fb897eebd4c6a61dde61adc3894376b89d4c4e0c9fd980de30af47060a0",
        "hash_atual": "c15e503af859d389c8978ce8008a8138730d59c4822026e13dc1fa491917e44d",
        "tamanho": 64199,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c34a5ac17977932b63727e6b8ac85fd602dabe2d8c41d048c1b55f572fd457c6",
        "hash_atual": "bcda99f230c2e768f93baad2b7c789b47fe9f952c3c640d5366f61f9c010ea97",
        "tamanho": 21668,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "785744a970cc0eb0385fae34eed391e94e837817143ba5fa9e4d0c6a76142ac5",
        "hash_atual": "b8d8366b538d0a57287ddc0fbd97039abf804279d96134b8fdc3a082540e58f4",
        "tamanho": 39431,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "dd329f3818cab334092d0d0b12109cacb4adfd0d03fa1816336fc54aab7ecf87",
        "hash_atual": "80467e1f4155c4584d657bdbf440f46cb5cccf8e4d3e0467a717ebe4b8768aad",
        "tamanho": 9714,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "54ebe4581d2968fc0da08742a9daef46366f38d81b3db6318714c561b25e7ac1",
        "hash_atual": "fc48cc5ddd0ba31e297ace6e26c6d8be3fa5db4898c567a6d50d47660564b650",
        "tamanho": 2116,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "eeb04473da7aaff646d607aac2154eb7045e716d215c6aa23fbb680255345c51",
        "hash_atual": "4201c45d83dbe8a0a1233df8b672e7e13b2c68f1686ac8e809a7b7c2098a4469",
        "tamanho": 8438,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2ff8152a0c294cf165214150e62f84e01e73a427b193d9c1ea27d7f4b48cd6da",
        "hash_atual": "965a2d891915d4b9e9aea14950d59fc8105bf02ca115c36a9aad024cdaedca95",
        "tamanho": 19647,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c5ea433abd3118987bafa9d85a7fb502ad578bb01624490589d547feded1222f",
        "hash_atual": "ab640825a3526578bb33c77bc136676ba530a69ebb760cf28d0bf36346d8ad89",
        "tamanho": 18651,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "30e9042d7e691184627ff907c44e35d5b29878d84ae738d774a60899d0a8775a",
        "hash_atual": "d47c2d67235db3f53b66e80e9ffefb926d062977d2142865f19a1aa206cc2540",
        "tamanho": 44459,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "279f631cc3e1781a82ee439e998ffe1f1413e0630928670ed8a5447361d41972",
        "hash_atual": "d8334fe4ea21a294742bf95f794cf17c5f8cf598c421a46377c41ed08bcd8454",
        "tamanho": 1169,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "91f300a7635be81aa89c531ef016f213ef71ececd48d8a1a741b1536c18272fd",
        "hash_atual": "ab73e41912fadfd837a21a758c71fcf5fb8ea3e01639cfb7023e105126427c08",
        "tamanho": 10124,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b48912f906a3723833e9cc464730dca4eeb76d8ff166cc652a79d15c5abe095d",
        "hash_atual": "6d395e72336982a18cfbeb1a16db2ae69d5d218d91af925f5ca982a2ac51ef56",
        "tamanho": 8297,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6660d785850eed40eac23b1fb4ecea82052ba62aacd832005cbb3b1f41dcb11b",
        "hash_atual": "3166148df20dad24107311f2df9bbeb426e11f8687955ccadfbb408470ed1c69",
        "tamanho": 8524,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2d75d342c606e2569ed837dea37d820c2dddd90073ab110b84c6dcb63e3dbf45",
        "hash_atual": "2d5cbb0275a1815baa1bc525e2592bd7d9c1f6288c6632468d3c79f0241055c3",
        "tamanho": 29449,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bd261fb15244319dea5ece8e84cd9ced5041b9591fa11ac55c91d71a15d209bb",
        "hash_atual": "edf20af7f96ddbb79cf407d1f1e63c028761331c4f74da6c0704be9a074cec87",
        "tamanho": 10070,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3f58dceeac377d371c0591f8b01cfbd6f50e56ce51bcb08b0907639a83882608",
        "hash_atual": "97a7f39f26659e8796a89524538580201f377e4fa82bdec1791239048375fa2e",
        "tamanho": 6219,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ceaa9b8dba4a28299854f5c572d0fe71cd71d48ded96b68514349f56f68f3d5d",
        "hash_atual": "e3c2576b5b326acf34073adc7fdaddaf61278dee0fdfbd330fbfdcefc07b6646",
        "tamanho": 7208,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3da6880a0e35f3190c1fd473ed8c19f177ba965668818b2023200297f8be9f3c",
        "hash_atual": "fff3fd293a3018707909c058625acd5f8d72c5562cf01767e1daf7eb8fe9be60",
        "tamanho": 9296,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0d7f1042a0c7b66d1bf88ec4ce55bf65259b69d3017c03629d42744c64ca73cc",
        "hash_atual": "2af9d172c334a4456adfd4dcbc38cb78b421e2e9ac003cd997999683ff6b7c36",
        "tamanho": 20506,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c1888cd8da08d97e6f37ce695d7d97847b684261640d2d39877078a7300ff2b3",
        "hash_atual": "fe21da7c9694cfed6352db93aa5242fa59e663eb8cccfc49cac1a69b9bcc5906",
        "tamanho": 59888,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e828eeae71cdd341a3a1e20cd05157c29f282eac44fdbdf02f2bb36670af51ea",
        "hash_atual": "25c6b36410a1d0939e5ab7a41bc79823d40f1cfbc68013b2918f5716537806db",
        "tamanho": 28604,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3b4ac86ea848109d381ec84ed2685b67845d081dc046f01ec9ec7a940ee38fff",
        "hash_atual": "c5294170879e10d9b8f089cdd56c52aae1fa037312a50e3e98fb017a50163901",
        "tamanho": 15343,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "50a463c817dd599d923ac481ab9610285e5fd8919dc7602ef510c959f54c0fba",
        "hash_atual": "f87ce2975af6d967137ec1f80072f98fd860e14628a2223cc7a73ea0bee76dfb",
        "tamanho": 26051,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cb754f11588a9224162dfa7243634530ccad64a7e0fa4b4e3f9a4f821efa129a",
        "hash_atual": "cf7da18ebf1bb08084db5bc3c369fcd874b380e68d973dbea5a40bee48049a3c",
        "tamanho": 25889,
        "modificado_em": "2025-09-07T18:53:40.000000Z"
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
            
            Log::info('PreservarMelhorias19Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias19Seeder - Erro', ['error' => $e->getMessage()]);
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