<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias29Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-22 16:13:07
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "a6c27388a97c0cd87dee0cd51717a23eae233fc57cfd39f60a0ea60ea8bb6c59",
        "hash_atual": "64e24d85d762e6ddc8bcdf34f51f716f00fad16346dfeb0fb81f5cbbd248e825",
        "tamanho": 198889,
        "modificado_em": "2025-09-22T15:31:20.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "dfb460b21c36eb6c97edc2178a53f98691478148d64b7efeba2a7b6ff02abed9",
        "hash_atual": "aa99ff36771c5a083456ce8c3915b758513e8edefb36787f84f294375fa7f401",
        "tamanho": 38821,
        "modificado_em": "2025-09-22T14:25:14.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "fda4c6b092b0a0112b8cec3816a592742d633d05beca4ffa7c5f93059f5d9ab2",
        "hash_atual": "617cfbf35bbc2dac8924072467070bf856de027e2589ae39573eeae40c322c30",
        "tamanho": 190861,
        "modificado_em": "2025-09-22T14:25:14.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "cb958cbaa20da031e98eb12de425127c4d391029c466ee8248b4676bd7fb95f2",
        "hash_atual": "1fa5928612bc23a7b5de7648ad99ccceacb0a6d583029069fa67c37ab68158c7",
        "tamanho": 37954,
        "modificado_em": "2025-09-22T14:25:14.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "dfd2fb4248b71961869c788e55acbe17965d546f26d70a67215f3ab48d8a03b4",
        "hash_atual": "0939fd69c3900b8bbd9445c9fa8e3244998db2275cc61743fb72e4ea094a8e94",
        "tamanho": 16468,
        "modificado_em": "2025-09-22T14:25:14.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "3b0d8f5a425973a98b28c74958faf4336639a5a9d71dd06d7b902935bb176d36",
        "hash_atual": "ca6fbf0838b8b94fcb6673d351aa2ef83f2dc13b84d542738da71db34e971d08",
        "tamanho": 19682,
        "modificado_em": "2025-09-22T15:30:33.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "5d40ade9a29b0e3f37ffcd78907199963e241bb834cd30f25728b8154a820ce3",
        "hash_atual": "68614dbd8b9466fdd93742c428ed19fd5db5460972ff7cfd29ea178051222746",
        "tamanho": 11654,
        "modificado_em": "2025-09-22T14:25:14.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d209c83ff8a6c1d1a07fe369d7f5df4c8a8197cbf82f083a3b867bc51c302f19",
        "hash_atual": "5a5dec37552617ed8a2a7c65df31dddebff26438b6ce3f37d2df854db19b8df1",
        "tamanho": 90333,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8ec9a46ff3fa8034cc47f3c35a972c72f6e4e6b0822d795a32c6d856daea14fd",
        "hash_atual": "4f8713f58ed892ea6e9badd2bc9b04a687fad583ba000905ee5e135546322fab",
        "tamanho": 69556,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "25633abcaf0a19010f42ddb8605c1add358fb84846f8ae3829f89ae1e2d2f452",
        "hash_atual": "841b00e2c6adc1de2dcf161c827d3ca3210de99bf4b96be9de5921dc03207d49",
        "tamanho": 64199,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7d1b106c962ae98cc830ccf8faafa53b2332897f618517d208b792b011ff5992",
        "hash_atual": "3c048a4e10b8254cd8e433ade68d0b8a20a25d9e0b4edf5a1dd68bd980722d12",
        "tamanho": 21668,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d00e11f6cfe311143704df3fb763e026bc5fce257047cd68f11d6610cdf85b96",
        "hash_atual": "aa42d57dd4cdb1efdf2a53900ade06169c25dde3f2f1939e7922e0f4e2c39df9",
        "tamanho": 39431,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e005cabdd4da811c944ba2f75cdda2fb8362f425651425037a998da82e67300a",
        "hash_atual": "2540cd4f7321276ea78f34ddf965ce83a7c14a1429939b59da0c76e291a6d0ca",
        "tamanho": 9714,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "dff6b3147d8b0ce0b33781512a503d1de9affdcef751b40ebba023b60e3a4363",
        "hash_atual": "7bc4461d8e2bd75fa6c05cb7865dffb72bd58ccd98cb421bdf97e4e4265a0c26",
        "tamanho": 2116,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a01bedc0a6e99c7e25f1b19ad3dfdaa1782b4a4c1e6ceb4f23f39ddbfa9c8e78",
        "hash_atual": "1508663f3e98c2b33b35bd9b53a1b430da2d619c70c0240df3dde8b8644f27a1",
        "tamanho": 8438,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "007c6f417932e92a5d8729c5f1cf7a377df3f1ce22c48e03783ce3b641f203e3",
        "hash_atual": "0862e7dad1466cafeb2210d1e0cffee2aeb3834973c3b55e20af15b02d36afc8",
        "tamanho": 19647,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a630603053c7d8725c721126a4587aaf20bf4f295c0820abf04bae58359014ce",
        "hash_atual": "bb6996c7ac735134fd15abea806d7a2d1ea0494e9c631ab959d65cd375b68a17",
        "tamanho": 18651,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1912476641a5f36119025b11db01658555465539f9434b9d9c9891f1ffbed11d",
        "hash_atual": "49d01b30800ad8f74e079e080bc430d21cc2b3abe2ed7949511d97b9bb3637c5",
        "tamanho": 44459,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0daef18da4601bd33bf7dca24b68ac3229a97ab565021408584a03fde70e073b",
        "hash_atual": "1fb34ff82122dda6ca1116f9dbf326ed273f740056be82615c8ef87a083c5819",
        "tamanho": 1169,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "64aa7be2b73f1cf93d7df585323a654abdff04a9d89a2021b678b8132b853602",
        "hash_atual": "1a25cd059ca9879160991224d3d391316f72240e95e88a13990a03a9691b9117",
        "tamanho": 10124,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d87ec8216a0006f2d9f9625c30affb85fd04a21910abeaf4d05958e843775465",
        "hash_atual": "160f8d6cced2ddf364048d708ffb26750e59e94e13e0a8c22eed62689526dd2b",
        "tamanho": 8297,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "af19d1ce2d1873d43a438b243ad3e0b102fa925b01285356f6b41721d1a2a21b",
        "hash_atual": "fc8cc3143e2355f78d018bb074f91517930332d93ae843a70fdd5e257ffe3262",
        "tamanho": 8524,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "432f5554ce9bf2ecc9eeed84416bb50083999889c705690ead34218dda20fdc5",
        "hash_atual": "b4aeb3071035e1b1c17229bf0ca753665d8d584c6f1c38f638602fb657cfd5c6",
        "tamanho": 29449,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d56afa5ad8bbb8f647b8ae3753e2d67558a82cbb41bd532482fcbdad9798f60f",
        "hash_atual": "debb29c3711152df19c3594c1f71ec2277b3d03d1883b65f50dda5dab7a14cfb",
        "tamanho": 10070,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5917004e04c1c65aa649e605412f1973f56f578fc0feff9bfcc796d798c77eb6",
        "hash_atual": "4a01deee08f98005c8a809d207d98e458437b58a34079d4555e97695b1e823ca",
        "tamanho": 6219,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cac0de65071458e1100e9801cc1458dbd3c93992b5040adc209e9bc4bdfcbe5b",
        "hash_atual": "1639abc98f8c680d72423fc0eb9447cd9d3a3286789cd6e3f4bc5bfecb0ca35a",
        "tamanho": 7208,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "272f6cf8b936b38d8632277b7d5a4dd2e9cfe7a3e64ce6b923016958ae40003e",
        "hash_atual": "68018c48ed2d2d862150c69a111801f367451893e4ba66c96f5501d0c902e41a",
        "tamanho": 9296,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9ad63f0e5d5de3e105dbf111ea0a60553a197761f6b4786a3ce1e2372b00f0f9",
        "hash_atual": "0e0f072fde211d750952d9792450844dcc6b6e995387ca597261d9dece69cd0d",
        "tamanho": 20506,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "349f7eac71110fc0f2f12c64daaaf311661862a418706e7e7396b1fc0434743c",
        "hash_atual": "c60a650d03b6095dab59f7a192b62c68b8965866741fab3efae983ac7d654da2",
        "tamanho": 59888,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "edeaf18fcff8d2c13e5fe4c050801ec36e98f1d8aca803fecfb94bf04a6aa5e3",
        "hash_atual": "a202284b3eb0e3e7dc6c67649d86e53626bcf17173eb71602dd210614532262e",
        "tamanho": 28604,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f4982501dab4a5dd5104543a6c25475973182b70d863d68453d98d1ccef2731d",
        "hash_atual": "99c63bd28d9178c3b477a8b1ee88ced68a2520f51026d018c26e2b48e6f6ea43",
        "tamanho": 15343,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "86886e4647ba1761b290f12676257183f146c75bf3c4e7bd3d1d6986458bb735",
        "hash_atual": "af1ba64b6c12d89d420182496ded2a263e7e37cb5ff309ccca93a095d3421f49",
        "tamanho": 26051,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b1da7bd417c3022d489767467bcb117e826c0f24b7f91b1fb460354491180958",
        "hash_atual": "b5149bc2f180683e1fbe1eaab49fcb6250c4ff492f4c8a41fd63c9a675493a05",
        "tamanho": 25889,
        "modificado_em": "2025-09-22T14:25:12.000000Z"
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
            
            Log::info('PreservarMelhorias29Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias29Seeder - Erro', ['error' => $e->getMessage()]);
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