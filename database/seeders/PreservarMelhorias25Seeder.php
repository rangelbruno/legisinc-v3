<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias25Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-22 13:58:53
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "89e961dc250c134d923584cda38a519a567e19fbfdc091d6c6b33fd0020ac418",
        "hash_atual": "0cf5d857f37dcdead1a401d01b28bd5f9630b39d09f5e947a117586d38b2c738",
        "tamanho": 198288,
        "modificado_em": "2025-09-22T01:53:31.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "b9e3266bbdb3515f17f6a1400eddac01291af5d6745b493fcdc125bf23cfb641",
        "hash_atual": "dc8142851add636ab6078908451eafc6fdd7cc6fee9501869c68991226d3ee7d",
        "tamanho": 38821,
        "modificado_em": "2025-09-22T01:53:31.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "0e74df67e13c21b372b985775aed128f54671aa693bdac06634fb85704c185e4",
        "hash_atual": "1456f3a7c3a168c8ece6668480cef3bdd848bb4cae06ff8513ca95a0d53fecb0",
        "tamanho": 190861,
        "modificado_em": "2025-09-22T13:34:30.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "06359f910b1a92b88e732b3cbfdb06fa280d5c99e443c5019654f5afcd1c3aa8",
        "hash_atual": "9eddf6bb9fe6186c86d5bb57d15e85cb7507e6e793788f9f039834811aa3d872",
        "tamanho": 37954,
        "modificado_em": "2025-09-22T01:53:31.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "3f5beaf137dfe5030e51bc1a007e1cebfcf9c05fca0358282e4dccb9b10830c2",
        "hash_atual": "97da9d6e68802d5a29154f0973b788046c4f50ee4119cc1cc97a08e61d4bddb3",
        "tamanho": 16468,
        "modificado_em": "2025-09-22T01:53:31.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "f1a04c5b0b3cdbe02846be08f6e16503164c42db41a0f070bae0a3d502475a9e",
        "hash_atual": "715c57f9718c644b3842af2367106d46e2039a22e746b709ddc5d89f17f8ecf7",
        "tamanho": 18936,
        "modificado_em": "2025-09-22T13:48:23.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "66321d666a5a49972e7f1938ee82e24a0e0eda8acae609b22015d702ddb6f5ba",
        "hash_atual": "5298417a063ae6b7bf2bf927ba7fbd93e5003eb7321ee8827fc880eb5b54d3b4",
        "tamanho": 11654,
        "modificado_em": "2025-09-22T01:53:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c6aee625e309cfe11359154955805258521d4326ef717363922205d782b87690",
        "hash_atual": "e00b609099959db87ade84a89b9bafcaec86b73a7dfeeaca02fc5a7bec34cd05",
        "tamanho": 90333,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0267b9a088bb3801164dd3339b6c156bc2b30dbdfffead5f3bf64b6213d5639d",
        "hash_atual": "db5e5ff3f7a0cbdee9de1a247e13f42dc9e793bad38f4b31975071a822277a30",
        "tamanho": 69556,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "eea3d2614a86a9016acf64ff2a6dff98b69af474fc2a0bb8a6517b60ea85febb",
        "hash_atual": "7d073e230914695a2c7033f35cdecc1ddfdc1fb6dad430d2ef0e10bdaa85bd96",
        "tamanho": 64199,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "18f7872ca8a4882f8baa41edf400981f61cde588a06a8f8b06ceaef2f6b5b978",
        "hash_atual": "8d4e4c4870a2bf609846a0493108230d9161a432207669b6adf7ee7873e973a3",
        "tamanho": 21668,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "eab941be22c6844a8c8ad9a8a94ba4935ac9aab0e1514ac3d277d4aae5eccde1",
        "hash_atual": "f7382035c4e343461039740b14f23294706e5f3e7f37e609e38e1d571a7a0a28",
        "tamanho": 39431,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7272fc2af2741fa16a945e40d597ab54d6628520376effdb1cea21b6a9ec73f9",
        "hash_atual": "84adb19ff02ca768096ac68c4a8522e925d3fd1798a10b12ea06c122bf722a2b",
        "tamanho": 9714,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ba66e08f3af6801bdf01512fd787bdf5fba1cd88eae262fe8948012fb6cec595",
        "hash_atual": "5029ca659570d1b60afb1510111db13a52da7e263335c16b783c9ea7858ffe37",
        "tamanho": 2116,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "612d5523af9a631961cfa97484d23998d541861d2fe69aecec17620803696004",
        "hash_atual": "448be119530d8f597b5050f8e76bbda56190613d8a3c7d06ef4c50d439c42b6f",
        "tamanho": 8438,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c9c8caf8f1b353804760e652ffd26810c30a29bf1254870d0641aa062c5f751e",
        "hash_atual": "2193b12fc9bce14aeda4ba2c209e69c25231d9226ccbdc399a84b497fb76f52b",
        "tamanho": 19647,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e2562161719d18ebea6e5def46341955b7eb83b98ac5cb92035701913bef91fa",
        "hash_atual": "aa72f2f66285b586589e05bca7c736c41c800f9cbfa93288a5c5106b46fa7bbc",
        "tamanho": 18651,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "aa52fb820de7749575257685e7b70e6f9bcc6b6a5a777aef489fbe0d81bef8fa",
        "hash_atual": "846ace8fee3adccf295d9d3a94bf1b61aa1affcc90289458276a430d8654baf3",
        "tamanho": 44459,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9a4360748a2c8fb3924787b22961cc5e7bd6921ee5d9566449e191340466bf1e",
        "hash_atual": "72dcd6d01d6ee7971248d9c519bf27640a9191ae8fdea18129999c9521fefa9d",
        "tamanho": 1169,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f2215ea7e5d8ca076e0d8cfbff55bd9fa21400da45bb8a85cebe5b9701237087",
        "hash_atual": "81ec0d7a542779a227d082e53b99cc30c5ca6e2f29211c8f227b5f2bb77747cb",
        "tamanho": 10124,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "40975990c4a82b2de12ff7f40476ff7e44ef894741ca879bd7f077415e8b0020",
        "hash_atual": "51a3520424d82520dbf11da3b0aa54410d4de7fc9271129a34f8b3529c05f85e",
        "tamanho": 8297,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7f20817238f6c9a37a9bb4addc9173b6831af8e2aab77ec8adbf32cb52bfdd2d",
        "hash_atual": "ad46636238623557f41037e0d8dc3bc682c13e30be9ec226519997e9eceaee74",
        "tamanho": 8524,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1f55239ebd94c6cce1d96877e8912c0446d3f5426ec951a2e235238a69a31b8d",
        "hash_atual": "eb30947b5ff55dc2d41fc68cfae3d98ad2d0c36127346338730b500f9ea3c9dd",
        "tamanho": 29449,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4d9dd01b317e3dc53309f5f71de6a45f779c39a1e81be650c69cfb3dd2bb4ac0",
        "hash_atual": "0f9f9d83c99904c6dc4fde9787b2c05b09d3ac563fad9a88dddf263eb1879806",
        "tamanho": 10070,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8191888e1104a264d3e5a647a6ae1713c509146716f5ecc6964a9a0d430b2827",
        "hash_atual": "14a1cdebb526c067f85abffd9fea47c0cfe8fcc002b11e6d464ed07adc4677c2",
        "tamanho": 6219,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "714efa6291fb2cc5f9e030fd1d891a7a53b420bd05fc8019614e86cbcfb720b4",
        "hash_atual": "743892902d5a73a58b3fa52cc4e06580eef409ca45b8d1385bd6afeae899ddcb",
        "tamanho": 7208,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "02d19c76ed7a987bc3290ec09d271f0e586b97aab5f6546bec705237bd14f4e0",
        "hash_atual": "521f5448781cfeda038ea3c4de1b04bbfc9b18fde6e4c2598c5081521eb90137",
        "tamanho": 9296,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0b0868c812d361217c9f53ed07c17f37cd043eedd56c70dc5297d6f43d3d3442",
        "hash_atual": "686595f8ae4fd891b236ba99a6686c85119dd8e7682ad67f2da19ec6b9d19027",
        "tamanho": 20506,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "68cd70a9c9882b9e4508e26cf57e61e94407e42381edf9d23491e2f0efc8a1a1",
        "hash_atual": "79bd7480a650b192e28a13d6ab6e2c2c10857ee33e3e5de86abbe0d387766901",
        "tamanho": 59888,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b368a6b01b96d3673d16c308becf6a3ebe2eb3e14db91be958c77add10464390",
        "hash_atual": "a7526010d01d35e63741974d5a9a0ac686f6e85d2c937eb827ccbeab5ad1c810",
        "tamanho": 28604,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c9cc9d2dad54d7f7bcd707a800bf88bf330eb88ec86ea406938902683c6b6088",
        "hash_atual": "c9fc15a844a94b44f228067e8bac111de2509f01f64bb765b5343431b22247a7",
        "tamanho": 15343,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "42515eb255a6ff836dfb156744b547e9dc2ca04976467ed3148c4514136d4e30",
        "hash_atual": "0044ad4e9ed8ae90f1a0dced8808d64b96a126e4342f4f7a7fc12bebff6577ce",
        "tamanho": 26051,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3c2228d4decfdcbca33b60bb654f96e3204c4cfce81f0e73e96b65f5445bb435",
        "hash_atual": "51c1e389b04314935170b8ddf9bd68a9a0e2ffcf08785e7c8f416cfc1779e1b8",
        "tamanho": 25889,
        "modificado_em": "2025-09-22T01:53:29.000000Z"
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
            
            Log::info('PreservarMelhorias25Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias25Seeder - Erro', ['error' => $e->getMessage()]);
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