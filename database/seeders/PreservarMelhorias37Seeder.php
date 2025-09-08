<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias37Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-07 21:28:20
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "ce6c6235ec17470e13bbf26570c19ee466a582e81f707ebfcd1252d7569212ac",
        "hash_atual": "b616978841d0bf08a9f344a33bb07b4ed231ef7a835e8581b54e0c94fddd3eb6",
        "tamanho": 183240,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "3751e3b241229c68ad6d7cac59460970b17786975ae1dd83b5c4664b1251521b",
        "hash_atual": "864d2e60acffa711eb2c275f2f50e0bc0bdb1d23a3eafd63d87a574e9fef9498",
        "tamanho": 33855,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "aa3d64b2e74eb876657d48548cfbf39ce86e7663f864fd481200770e7840ec34",
        "hash_atual": "00130f9407533af8975e214fd331abfe1aca16861bb0ce9357c1f8408255ea3b",
        "tamanho": 184884,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "50c279b48de2bc86213cd4e84c3bd6707538262db8c024a934e08dfcc8f2df73",
        "hash_atual": "1fa11d06887fc1f65ad119ad3e12176af5e49b6b0f03314703f48b6050ec9d03",
        "tamanho": 37954,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "1984e9ea0a1117294c0c71996a2c74256ea6ad2a068403b9f9ad5f72f60a0ec6",
        "hash_atual": "1dd83b25b6431d592610dc6d7ce4f6987b24c18eaf5ba98c59704371a582744b",
        "tamanho": 16468,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "a4304518e2370ec98156c980b780abeff7d6bcb33ebd464651ef83f0d3d67974",
        "hash_atual": "281f3428ea80696d66f1b19f81326d7cee390535308377477610288ccfe7f3e7",
        "tamanho": 16728,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "928c439e92c628ccb38477e50cae4cf452b174babbfa7ff7791f68fe6c0849e2",
        "hash_atual": "be204b59f565811cbfa1ec90764f80c0a084439aaf64dbd7287fb1720d6348e1",
        "tamanho": 11594,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c5f013824be9e5fe4821ecd8ccf14494a2dbe6113a2bf775d82f3180c7e020da",
        "hash_atual": "c61471e95abb1a8ea37b0c307f661d3f274b4feca39c737cd51953688b7accb3",
        "tamanho": 90333,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7da2c4354076fdc6c0d4b17d6ef0aa91882db6a586f5f1b84c554a63b409de25",
        "hash_atual": "282aca815320a709a5dc69d6a9e072f5d95cf9d2a9edc38c7e6e64b144e06852",
        "tamanho": 49890,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fddad7b10e01baea59064f4e3c3a4d12faf49f4d7d4c0bf99a1c018615a3a5e1",
        "hash_atual": "29d4331a9c9a1b997640a23d7cbf441d9e62754076b9fd0c46c26facd31cda1a",
        "tamanho": 64199,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f7393ca47f581f9b2f0af570c5d75253996d5bcfe3fbec606b2d6330dcd44381",
        "hash_atual": "896ef43f11fc336954370e55340417bb86ea8aabbf38df4f2e32c0dc48aab4c4",
        "tamanho": 21668,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "06d177755414d4188068ad6dcd099c764c08189bb08c34b0e78c6b6377623ad5",
        "hash_atual": "6cdca083957af4f8eca63296d67fc6370b3534d28172c656b15a03bb687de53a",
        "tamanho": 39431,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "37e6cbd4489e94e505b75afc6f5ed52386ef97d73458cb07d511511c13f991f6",
        "hash_atual": "03e19adf125944ed5db744c82b6f03da9dc2471139512c13adefd45be0856f1b",
        "tamanho": 9714,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ad9c332dea4f0e4b43b698c4b8d4792f86cb7baea45fbbb52a169af7a65d9626",
        "hash_atual": "b333959cff3d251fd19b8f5f206860549f415399cc568e810525f47e3fc79562",
        "tamanho": 2116,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a03a50d9dbd06578545ba33d08794b8e44fa2bb47291dc1c825fe0fbd927807c",
        "hash_atual": "154e4138d14857fde3d65b415323eae7e2def7c70fa2dbc6649d854ecdea3278",
        "tamanho": 8438,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bcce1a5748338e154b5352ede294875217561a242a6b6692deb5cdc1dcbb8885",
        "hash_atual": "2f1eb8dcb1aa2357ac31dfb20e5640c005c6450f6ac96fcacd9d328d8cdd8196",
        "tamanho": 19647,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "02803aff5b3757e0a4fea74ad57e06231715bc91222e824c41c5c59813dfe3c9",
        "hash_atual": "1efc2848cdaca88106347a7007b7cfa611e9bb2bb27acfe875fb5b7078a8f2c8",
        "tamanho": 18651,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4e4cc0c47a8ef60f6b007d3abcd2410a29d1d0950ba36ec6d91c4e70bd446f04",
        "hash_atual": "c1176717da8480b79a602d507ebd37170d3bac694e97d5dd3aa1133a0571f00d",
        "tamanho": 44459,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5bc45a8893d21dd80cb5650eeee84ca7850cef31f1d64ed77a4f817c6631c753",
        "hash_atual": "0e72c4061f63bd8f2e333744e63314478ccc9a661aaf7e1fe727740dd239b779",
        "tamanho": 1169,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a9720a423e450d706a74b44c42d648bd2455f21197a2e5f79f18158ccccd407c",
        "hash_atual": "976c009d7503412bb42946ee31661662fa18b0bec87f408e192049b27bef51d8",
        "tamanho": 10124,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a6fc637b0eb3ada47f94acf861b36fff35b8db9ce2165d5c420fbc6bd81dd93e",
        "hash_atual": "e68ed302eef72991dd587e8b8e380732e94d1bbaf2e655792165e70c841058d0",
        "tamanho": 8297,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "816506c499a3ce16be1bdbf8a2cd22af33bf6b30b6ee2ed6f18eeddf4bf99301",
        "hash_atual": "10606fd81d9b8d163871605f8ffd8e5bdf1f855a0db57e021894a3bd097d6c02",
        "tamanho": 8524,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fb7eed18fa8b5d6a62a89b0e7e2fcd1f86ecad7e586ca0feb38b0df4c08ea521",
        "hash_atual": "a1672b43e9002f72f0040ca96ca4403f0d054931de65c60c2e3e747bdfa9a619",
        "tamanho": 29449,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "41efbf68cc2be03557515022bddb215c3c1931e93b6a75ec132618099ca07b3b",
        "hash_atual": "bb0d048d3a442f83b4103b7f9aafdaaa0d539aaba8224493d2a58461635d8b3d",
        "tamanho": 10070,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "72264b917fba6ce57e8f6f047dd2e3f06a235c01d921dab606f3fd66bdcfb67f",
        "hash_atual": "696b45ee80eec4497f923c76d8ba03f26c51c984180f35c6ec8d25ab404eeac4",
        "tamanho": 6219,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1a10cf776c7398f3956d4c0a5aa11655acb0d00e89bfd03e1d6b47259a096a6d",
        "hash_atual": "abce5e10e49d7872a2d675cc8590c98ed08e5689c6d87b8809c0adbeff264911",
        "tamanho": 7208,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c04a8819701026d411153074ecf17b5820d810c92eb7ce552147d2bcfc299548",
        "hash_atual": "58f402690e53381f95591009eee289f8a95b2b5c9e58d503351484802940549c",
        "tamanho": 9296,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a8bf4b63b65003989f0a4cff0316c43bbc99971883ed4043584a8ec8ee1c30d0",
        "hash_atual": "eb0e3ba1ccb5403c77063db55a9b5df60e010c55f745ac6414ebef9c49caa87f",
        "tamanho": 20506,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4ea6819e21e74f0e9f242d86d45a0d03bd4b365ec5e0d210f05ab6166643e08e",
        "hash_atual": "fa9d7e973785feda9b47b3972509b924e8a1729ecc2bb2051112d3e6a24af2d3",
        "tamanho": 59888,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "86ff45c67aa7acfef7acc997b6d151858b7390aba4572126d91e7fb5a008c61b",
        "hash_atual": "f53448b2b02d36ec6985eca0f2fd959821d9ecea0d8953971d1a7abc798ddc14",
        "tamanho": 28604,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0b428f980dc6d67ef251c7fbf157dc06d72d47b08f12eecf7beb6c83a9883c8c",
        "hash_atual": "b40b95aada1c1c2bbfa898bd044d0d964d4296009da82b5214150c7242bb882b",
        "tamanho": 15343,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "46b33c6e5c68c8ea5e37fda893ce5e451224e5998ae4d5bc2630b0292ce955df",
        "hash_atual": "8952bec1bddec7d9a4a1fa1d437984c1736a84e4173ae92a2d0a5e818971d250",
        "tamanho": 26051,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7767f05861035a17b831d435497ce0a0d1e5895189afa3e55a45c9841d657a37",
        "hash_atual": "50efd84f1cdee0ad0867ba3d275b7216615353274e46717280764932c8a0bbf3",
        "tamanho": 25889,
        "modificado_em": "2025-09-07T21:11:12.000000Z"
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
            
            Log::info('PreservarMelhorias37Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias37Seeder - Erro', ['error' => $e->getMessage()]);
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