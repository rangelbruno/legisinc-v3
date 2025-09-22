<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias27Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-22 14:25:01
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "0cf5d857f37dcdead1a401d01b28bd5f9630b39d09f5e947a117586d38b2c738",
        "hash_atual": "a6c27388a97c0cd87dee0cd51717a23eae233fc57cfd39f60a0ea60ea8bb6c59",
        "tamanho": 198288,
        "modificado_em": "2025-09-22T13:59:05.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "dc8142851add636ab6078908451eafc6fdd7cc6fee9501869c68991226d3ee7d",
        "hash_atual": "dfb460b21c36eb6c97edc2178a53f98691478148d64b7efeba2a7b6ff02abed9",
        "tamanho": 38821,
        "modificado_em": "2025-09-22T13:59:05.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "1456f3a7c3a168c8ece6668480cef3bdd848bb4cae06ff8513ca95a0d53fecb0",
        "hash_atual": "fda4c6b092b0a0112b8cec3816a592742d633d05beca4ffa7c5f93059f5d9ab2",
        "tamanho": 190861,
        "modificado_em": "2025-09-22T13:59:05.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "9eddf6bb9fe6186c86d5bb57d15e85cb7507e6e793788f9f039834811aa3d872",
        "hash_atual": "cb958cbaa20da031e98eb12de425127c4d391029c466ee8248b4676bd7fb95f2",
        "tamanho": 37954,
        "modificado_em": "2025-09-22T13:59:05.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "97da9d6e68802d5a29154f0973b788046c4f50ee4119cc1cc97a08e61d4bddb3",
        "hash_atual": "dfd2fb4248b71961869c788e55acbe17965d546f26d70a67215f3ab48d8a03b4",
        "tamanho": 16468,
        "modificado_em": "2025-09-22T13:59:05.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "715c57f9718c644b3842af2367106d46e2039a22e746b709ddc5d89f17f8ecf7",
        "hash_atual": "3b0d8f5a425973a98b28c74958faf4336639a5a9d71dd06d7b902935bb176d36",
        "tamanho": 18936,
        "modificado_em": "2025-09-22T13:59:05.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "5298417a063ae6b7bf2bf927ba7fbd93e5003eb7321ee8827fc880eb5b54d3b4",
        "hash_atual": "5d40ade9a29b0e3f37ffcd78907199963e241bb834cd30f25728b8154a820ce3",
        "tamanho": 11654,
        "modificado_em": "2025-09-22T13:59:05.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e00b609099959db87ade84a89b9bafcaec86b73a7dfeeaca02fc5a7bec34cd05",
        "hash_atual": "d209c83ff8a6c1d1a07fe369d7f5df4c8a8197cbf82f083a3b867bc51c302f19",
        "tamanho": 90333,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "db5e5ff3f7a0cbdee9de1a247e13f42dc9e793bad38f4b31975071a822277a30",
        "hash_atual": "8ec9a46ff3fa8034cc47f3c35a972c72f6e4e6b0822d795a32c6d856daea14fd",
        "tamanho": 69556,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7d073e230914695a2c7033f35cdecc1ddfdc1fb6dad430d2ef0e10bdaa85bd96",
        "hash_atual": "25633abcaf0a19010f42ddb8605c1add358fb84846f8ae3829f89ae1e2d2f452",
        "tamanho": 64199,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8d4e4c4870a2bf609846a0493108230d9161a432207669b6adf7ee7873e973a3",
        "hash_atual": "7d1b106c962ae98cc830ccf8faafa53b2332897f618517d208b792b011ff5992",
        "tamanho": 21668,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f7382035c4e343461039740b14f23294706e5f3e7f37e609e38e1d571a7a0a28",
        "hash_atual": "d00e11f6cfe311143704df3fb763e026bc5fce257047cd68f11d6610cdf85b96",
        "tamanho": 39431,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "84adb19ff02ca768096ac68c4a8522e925d3fd1798a10b12ea06c122bf722a2b",
        "hash_atual": "e005cabdd4da811c944ba2f75cdda2fb8362f425651425037a998da82e67300a",
        "tamanho": 9714,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5029ca659570d1b60afb1510111db13a52da7e263335c16b783c9ea7858ffe37",
        "hash_atual": "dff6b3147d8b0ce0b33781512a503d1de9affdcef751b40ebba023b60e3a4363",
        "tamanho": 2116,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "448be119530d8f597b5050f8e76bbda56190613d8a3c7d06ef4c50d439c42b6f",
        "hash_atual": "a01bedc0a6e99c7e25f1b19ad3dfdaa1782b4a4c1e6ceb4f23f39ddbfa9c8e78",
        "tamanho": 8438,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2193b12fc9bce14aeda4ba2c209e69c25231d9226ccbdc399a84b497fb76f52b",
        "hash_atual": "007c6f417932e92a5d8729c5f1cf7a377df3f1ce22c48e03783ce3b641f203e3",
        "tamanho": 19647,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "aa72f2f66285b586589e05bca7c736c41c800f9cbfa93288a5c5106b46fa7bbc",
        "hash_atual": "a630603053c7d8725c721126a4587aaf20bf4f295c0820abf04bae58359014ce",
        "tamanho": 18651,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "846ace8fee3adccf295d9d3a94bf1b61aa1affcc90289458276a430d8654baf3",
        "hash_atual": "1912476641a5f36119025b11db01658555465539f9434b9d9c9891f1ffbed11d",
        "tamanho": 44459,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "72dcd6d01d6ee7971248d9c519bf27640a9191ae8fdea18129999c9521fefa9d",
        "hash_atual": "0daef18da4601bd33bf7dca24b68ac3229a97ab565021408584a03fde70e073b",
        "tamanho": 1169,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "81ec0d7a542779a227d082e53b99cc30c5ca6e2f29211c8f227b5f2bb77747cb",
        "hash_atual": "64aa7be2b73f1cf93d7df585323a654abdff04a9d89a2021b678b8132b853602",
        "tamanho": 10124,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "51a3520424d82520dbf11da3b0aa54410d4de7fc9271129a34f8b3529c05f85e",
        "hash_atual": "d87ec8216a0006f2d9f9625c30affb85fd04a21910abeaf4d05958e843775465",
        "tamanho": 8297,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ad46636238623557f41037e0d8dc3bc682c13e30be9ec226519997e9eceaee74",
        "hash_atual": "af19d1ce2d1873d43a438b243ad3e0b102fa925b01285356f6b41721d1a2a21b",
        "tamanho": 8524,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "eb30947b5ff55dc2d41fc68cfae3d98ad2d0c36127346338730b500f9ea3c9dd",
        "hash_atual": "432f5554ce9bf2ecc9eeed84416bb50083999889c705690ead34218dda20fdc5",
        "tamanho": 29449,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0f9f9d83c99904c6dc4fde9787b2c05b09d3ac563fad9a88dddf263eb1879806",
        "hash_atual": "d56afa5ad8bbb8f647b8ae3753e2d67558a82cbb41bd532482fcbdad9798f60f",
        "tamanho": 10070,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "14a1cdebb526c067f85abffd9fea47c0cfe8fcc002b11e6d464ed07adc4677c2",
        "hash_atual": "5917004e04c1c65aa649e605412f1973f56f578fc0feff9bfcc796d798c77eb6",
        "tamanho": 6219,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "743892902d5a73a58b3fa52cc4e06580eef409ca45b8d1385bd6afeae899ddcb",
        "hash_atual": "cac0de65071458e1100e9801cc1458dbd3c93992b5040adc209e9bc4bdfcbe5b",
        "tamanho": 7208,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "521f5448781cfeda038ea3c4de1b04bbfc9b18fde6e4c2598c5081521eb90137",
        "hash_atual": "272f6cf8b936b38d8632277b7d5a4dd2e9cfe7a3e64ce6b923016958ae40003e",
        "tamanho": 9296,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "686595f8ae4fd891b236ba99a6686c85119dd8e7682ad67f2da19ec6b9d19027",
        "hash_atual": "9ad63f0e5d5de3e105dbf111ea0a60553a197761f6b4786a3ce1e2372b00f0f9",
        "tamanho": 20506,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "79bd7480a650b192e28a13d6ab6e2c2c10857ee33e3e5de86abbe0d387766901",
        "hash_atual": "349f7eac71110fc0f2f12c64daaaf311661862a418706e7e7396b1fc0434743c",
        "tamanho": 59888,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a7526010d01d35e63741974d5a9a0ac686f6e85d2c937eb827ccbeab5ad1c810",
        "hash_atual": "edeaf18fcff8d2c13e5fe4c050801ec36e98f1d8aca803fecfb94bf04a6aa5e3",
        "tamanho": 28604,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c9fc15a844a94b44f228067e8bac111de2509f01f64bb765b5343431b22247a7",
        "hash_atual": "f4982501dab4a5dd5104543a6c25475973182b70d863d68453d98d1ccef2731d",
        "tamanho": 15343,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0044ad4e9ed8ae90f1a0dced8808d64b96a126e4342f4f7a7fc12bebff6577ce",
        "hash_atual": "86886e4647ba1761b290f12676257183f146c75bf3c4e7bd3d1d6986458bb735",
        "tamanho": 26051,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "51c1e389b04314935170b8ddf9bd68a9a0e2ffcf08785e7c8f416cfc1779e1b8",
        "hash_atual": "b1da7bd417c3022d489767467bcb117e826c0f24b7f91b1fb460354491180958",
        "tamanho": 25889,
        "modificado_em": "2025-09-22T13:59:02.000000Z"
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
            
            Log::info('PreservarMelhorias27Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias27Seeder - Erro', ['error' => $e->getMessage()]);
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