<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias31Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-22 16:53:42
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "64e24d85d762e6ddc8bcdf34f51f716f00fad16346dfeb0fb81f5cbbd248e825",
        "hash_atual": "eb7a4a29e82d6de5a3a2e7e0d2b730b7e81287b977810b03f1744715be4eada8",
        "tamanho": 198889,
        "modificado_em": "2025-09-22T16:13:21.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "aa99ff36771c5a083456ce8c3915b758513e8edefb36787f84f294375fa7f401",
        "hash_atual": "979cbeb701e81c7967a4e63058462c5f4fcb4ceeaccce30609f14c48325a5f2b",
        "tamanho": 38821,
        "modificado_em": "2025-09-22T16:13:21.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "617cfbf35bbc2dac8924072467070bf856de027e2589ae39573eeae40c322c30",
        "hash_atual": "dc610e152063d778a31b8484893bfe16b2bc1bb8845cf11d33a30a174f7d5ad7",
        "tamanho": 190861,
        "modificado_em": "2025-09-22T16:13:21.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "1fa5928612bc23a7b5de7648ad99ccceacb0a6d583029069fa67c37ab68158c7",
        "hash_atual": "20f71b68b67d7ad90999abf0b486a5e8b1611a43798bb63c04696f5c84a3433e",
        "tamanho": 37954,
        "modificado_em": "2025-09-22T16:13:21.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "0939fd69c3900b8bbd9445c9fa8e3244998db2275cc61743fb72e4ea094a8e94",
        "hash_atual": "e24eee86ea22485ba01cb6f8d32fc77a18da421e2599efaf0bc82623246ac75e",
        "tamanho": 16468,
        "modificado_em": "2025-09-22T16:13:21.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "ca6fbf0838b8b94fcb6673d351aa2ef83f2dc13b84d542738da71db34e971d08",
        "hash_atual": "78a97a490f1e4de7e67ebce4f05f3274248a3769d9a1984d83a1604aceae7260",
        "tamanho": 19682,
        "modificado_em": "2025-09-22T16:13:21.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "68614dbd8b9466fdd93742c428ed19fd5db5460972ff7cfd29ea178051222746",
        "hash_atual": "bbcf80930ba4dc65eac89b5dde318f7f606e132f6187bf73577d1e04eed9d3da",
        "tamanho": 11654,
        "modificado_em": "2025-09-22T16:13:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5a5dec37552617ed8a2a7c65df31dddebff26438b6ce3f37d2df854db19b8df1",
        "hash_atual": "553945e1210c45123e790573da7783080ac31a696f12bab3f585a977d21fadc8",
        "tamanho": 90333,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4f8713f58ed892ea6e9badd2bc9b04a687fad583ba000905ee5e135546322fab",
        "hash_atual": "bc6756eea43fc204e4cb71c97d2d18e24045777e918eb6ce64e7230bbce4bd2b",
        "tamanho": 69556,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "841b00e2c6adc1de2dcf161c827d3ca3210de99bf4b96be9de5921dc03207d49",
        "hash_atual": "fc14f5162c9cbea822ffb0c712a286a08aa2c4ad87d2bae1c09c081e3da371b1",
        "tamanho": 64199,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3c048a4e10b8254cd8e433ade68d0b8a20a25d9e0b4edf5a1dd68bd980722d12",
        "hash_atual": "296f37ffc2de920dfb279e5c941ea036834d624e26009bb59f662d886371d1a5",
        "tamanho": 21668,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "aa42d57dd4cdb1efdf2a53900ade06169c25dde3f2f1939e7922e0f4e2c39df9",
        "hash_atual": "3f50d67773b4b3ee67784d8866beb929d974089f19e0ab08f5ad7fc7ea82fc95",
        "tamanho": 39431,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2540cd4f7321276ea78f34ddf965ce83a7c14a1429939b59da0c76e291a6d0ca",
        "hash_atual": "015940fb0a8b4025d9bb6f88f9ce23617b97a246880951393e8c66fbf6d0b373",
        "tamanho": 9714,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7bc4461d8e2bd75fa6c05cb7865dffb72bd58ccd98cb421bdf97e4e4265a0c26",
        "hash_atual": "6686a9a6bfb36575468e48ece3129d4f4ba7a030a8557cd14ef034831883a639",
        "tamanho": 2116,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1508663f3e98c2b33b35bd9b53a1b430da2d619c70c0240df3dde8b8644f27a1",
        "hash_atual": "3928fde4aa8626ad5d12840b90394bb4a106631a9bdaec9e32bad76688282719",
        "tamanho": 8438,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0862e7dad1466cafeb2210d1e0cffee2aeb3834973c3b55e20af15b02d36afc8",
        "hash_atual": "109923291b20c0f8b9d0dbb6bb9014be92b3f298d370dd321c2911700c80daf0",
        "tamanho": 19647,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bb6996c7ac735134fd15abea806d7a2d1ea0494e9c631ab959d65cd375b68a17",
        "hash_atual": "e51dc758dd3a20fc90e254a2ffe51fa12b8b696c1e1c144d2f370606fac7dff6",
        "tamanho": 18651,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "49d01b30800ad8f74e079e080bc430d21cc2b3abe2ed7949511d97b9bb3637c5",
        "hash_atual": "9da58f35f6806f519036fae382d764290c31efbd09d8a3b013a53af790863dca",
        "tamanho": 44459,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1fb34ff82122dda6ca1116f9dbf326ed273f740056be82615c8ef87a083c5819",
        "hash_atual": "addaffd23cf71f1f71b3b822d9c14b46da133868e8547329bca97b868ee94d1a",
        "tamanho": 1169,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1a25cd059ca9879160991224d3d391316f72240e95e88a13990a03a9691b9117",
        "hash_atual": "c036b98bb5a8eafc5eae58594bdc1f26384b2f21fa26d6a2b5cdc9c257df8c43",
        "tamanho": 10124,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "160f8d6cced2ddf364048d708ffb26750e59e94e13e0a8c22eed62689526dd2b",
        "hash_atual": "71e350dcc24d21e1d6eb543ff8aa90e10498f0ce61fdc55e9b4097a42dca8cb4",
        "tamanho": 8297,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fc8cc3143e2355f78d018bb074f91517930332d93ae843a70fdd5e257ffe3262",
        "hash_atual": "ce84a78a8af22d41a3820ffdddfdd959fffc012cd7d74cc445df7da98159557b",
        "tamanho": 8524,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b4aeb3071035e1b1c17229bf0ca753665d8d584c6f1c38f638602fb657cfd5c6",
        "hash_atual": "b3643269baea429665cbe7c2b12f306dee0edeee6639a36de7d538d1db5cd8e4",
        "tamanho": 29449,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "debb29c3711152df19c3594c1f71ec2277b3d03d1883b65f50dda5dab7a14cfb",
        "hash_atual": "e6f70c66ef97eb4cd045833b5a207f75266692f2ba244e6ee986f6b39478a8e3",
        "tamanho": 10070,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4a01deee08f98005c8a809d207d98e458437b58a34079d4555e97695b1e823ca",
        "hash_atual": "aacf3194060b1e254ca212f72467dda15a54fc76b3f175cef9d25fbb6a4330bc",
        "tamanho": 6219,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1639abc98f8c680d72423fc0eb9447cd9d3a3286789cd6e3f4bc5bfecb0ca35a",
        "hash_atual": "f6380417759df953e2e6c6b6bfe79a2668271ed2d2edb486897f8a2269b25b62",
        "tamanho": 7208,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "68018c48ed2d2d862150c69a111801f367451893e4ba66c96f5501d0c902e41a",
        "hash_atual": "ad5e9113256d17fc075fedfc91d9f978764e14508a17d740d6639b45168645e8",
        "tamanho": 9296,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0e0f072fde211d750952d9792450844dcc6b6e995387ca597261d9dece69cd0d",
        "hash_atual": "528c231de2649fab50fce5978305ca9e3d3685a24c756cd5966d75fae64aa62e",
        "tamanho": 20506,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c60a650d03b6095dab59f7a192b62c68b8965866741fab3efae983ac7d654da2",
        "hash_atual": "2af5f387c5fde775c93a5d0daf2d9bea85f4634257025c954af76d677e00da8d",
        "tamanho": 59888,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a202284b3eb0e3e7dc6c67649d86e53626bcf17173eb71602dd210614532262e",
        "hash_atual": "a7c93517e153e9700faf8cf139c3808066aa8b50b9351a7b479662f669a0713c",
        "tamanho": 28604,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "99c63bd28d9178c3b477a8b1ee88ced68a2520f51026d018c26e2b48e6f6ea43",
        "hash_atual": "2c95e97a80a68a37b242bfd7c193504326f44319b9b55519e485730feb2f6db1",
        "tamanho": 15343,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "af1ba64b6c12d89d420182496ded2a263e7e37cb5ff309ccca93a095d3421f49",
        "hash_atual": "4fce5a40770f6b9a8cd8def0188ce6ccdfab65bd00a9bea34341a1385d4cb1b2",
        "tamanho": 26051,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b5149bc2f180683e1fbe1eaab49fcb6250c4ff492f4c8a41fd63c9a675493a05",
        "hash_atual": "814fec7d64aaf7d0af90dd1fa1ef5082a7c06812d2eef755fb248a0a0b69dc1a",
        "tamanho": 25889,
        "modificado_em": "2025-09-22T16:13:18.000000Z"
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
            
            Log::info('PreservarMelhorias31Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias31Seeder - Erro', ['error' => $e->getMessage()]);
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