<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias39Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-07 23:32:01
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "b616978841d0bf08a9f344a33bb07b4ed231ef7a835e8581b54e0c94fddd3eb6",
        "hash_atual": "23928a3091ce94e71b9e2a6a1df610614ade4499602ae17b3e5c2a9771f239ba",
        "tamanho": 183240,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "864d2e60acffa711eb2c275f2f50e0bc0bdb1d23a3eafd63d87a574e9fef9498",
        "hash_atual": "81d4ac941449e4d5de4b7fad5281069b52441fadb8c3715c0802bdf36a7b0c18",
        "tamanho": 33855,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "00130f9407533af8975e214fd331abfe1aca16861bb0ce9357c1f8408255ea3b",
        "hash_atual": "28323d773254f93ea50c59a70ec43eb6766df763203de2a3c7bda230d81baaed",
        "tamanho": 184884,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "1fa11d06887fc1f65ad119ad3e12176af5e49b6b0f03314703f48b6050ec9d03",
        "hash_atual": "45d945942e584a7a317eb25e8064b6db6ab611d9280a82c1b2e1b16c30a90b58",
        "tamanho": 37954,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "1dd83b25b6431d592610dc6d7ce4f6987b24c18eaf5ba98c59704371a582744b",
        "hash_atual": "b4f770a6558bc835e0c9aa5bb9b82dbca2192d3dbacea8c8693abeef9ff52c49",
        "tamanho": 16468,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "281f3428ea80696d66f1b19f81326d7cee390535308377477610288ccfe7f3e7",
        "hash_atual": "f4b360c3c66f3f93ee4af2ec35073ae8726555d1e5e82e9173db9300b9f43cf1",
        "tamanho": 16728,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "be204b59f565811cbfa1ec90764f80c0a084439aaf64dbd7287fb1720d6348e1",
        "hash_atual": "d56bcbe2d0fb7021b72b0206da5bf5fa57cd180b3ad25c31c0e83841b3db36a3",
        "tamanho": 11594,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c61471e95abb1a8ea37b0c307f661d3f274b4feca39c737cd51953688b7accb3",
        "hash_atual": "8f5391b365de4302abffab7859df1ee3e67116c61ec69df62359b4fd94b113b8",
        "tamanho": 90333,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "282aca815320a709a5dc69d6a9e072f5d95cf9d2a9edc38c7e6e64b144e06852",
        "hash_atual": "cdffcc1ffb6ab6981dd05d26f58e576c9a42a439710eaa4b1b464408e92d7d5c",
        "tamanho": 49890,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "29d4331a9c9a1b997640a23d7cbf441d9e62754076b9fd0c46c26facd31cda1a",
        "hash_atual": "c291e263226c34ef8b176186d4288d101129a64eef5415c99c2b8253af657e82",
        "tamanho": 64199,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "896ef43f11fc336954370e55340417bb86ea8aabbf38df4f2e32c0dc48aab4c4",
        "hash_atual": "52b1a219f2ec582afa5e5364f1e966dbc9e21ace81a6de67f636139bda5b306f",
        "tamanho": 21668,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6cdca083957af4f8eca63296d67fc6370b3534d28172c656b15a03bb687de53a",
        "hash_atual": "24cbc03f65c44d2c4d73b0e3ed5990c8da6c72485e8ac6d77ad666f794f1b07d",
        "tamanho": 39431,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "03e19adf125944ed5db744c82b6f03da9dc2471139512c13adefd45be0856f1b",
        "hash_atual": "a86ac2507b5e9bbebdc256a7b8986b3fdb702ff058f37a466fc49b027fdc7d57",
        "tamanho": 9714,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b333959cff3d251fd19b8f5f206860549f415399cc568e810525f47e3fc79562",
        "hash_atual": "6a4fb74e5240f0607bf7a69d994fe8e90c1c088cd04681c359ccb3526fa38370",
        "tamanho": 2116,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "154e4138d14857fde3d65b415323eae7e2def7c70fa2dbc6649d854ecdea3278",
        "hash_atual": "fb71bdb26877f1fe8a2b64c3749fbe6434b8fc0124ba3f4499717de4cbd8a995",
        "tamanho": 8438,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2f1eb8dcb1aa2357ac31dfb20e5640c005c6450f6ac96fcacd9d328d8cdd8196",
        "hash_atual": "ff38e74268188125279c5f7ba30ae8898aa425e1f907d4a85d1b744d981191de",
        "tamanho": 19647,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1efc2848cdaca88106347a7007b7cfa611e9bb2bb27acfe875fb5b7078a8f2c8",
        "hash_atual": "481960dad02d7b326f7dcc5bf5525b43b20506f190024d4ee26c6c335501b828",
        "tamanho": 18651,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c1176717da8480b79a602d507ebd37170d3bac694e97d5dd3aa1133a0571f00d",
        "hash_atual": "eb2146cf4038152cb08454d541d1e1ad0b133dd8a59221e1ee0694666627712a",
        "tamanho": 44459,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0e72c4061f63bd8f2e333744e63314478ccc9a661aaf7e1fe727740dd239b779",
        "hash_atual": "83cc8b8391d4785fdd284e12354b45652f10f712fe7e8e591af4cc71d0a02895",
        "tamanho": 1169,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "976c009d7503412bb42946ee31661662fa18b0bec87f408e192049b27bef51d8",
        "hash_atual": "56b462ba90ef640b935c86a612ac923f778845e3a5e201913a771e8203ced574",
        "tamanho": 10124,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e68ed302eef72991dd587e8b8e380732e94d1bbaf2e655792165e70c841058d0",
        "hash_atual": "b35acfa197d3b35fe23c4b5c118d79031d968649befe4cbc51f2c20944155706",
        "tamanho": 8297,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "10606fd81d9b8d163871605f8ffd8e5bdf1f855a0db57e021894a3bd097d6c02",
        "hash_atual": "d3881fe49440214051c538fb5f12e57e8a77da0d5bf625e9261b65a8ae92a0f6",
        "tamanho": 8524,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a1672b43e9002f72f0040ca96ca4403f0d054931de65c60c2e3e747bdfa9a619",
        "hash_atual": "8b718139b3b6dd88f6e252afd92f3a84d1f3f1c457c2362e12de7445edf4ff2e",
        "tamanho": 29449,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bb0d048d3a442f83b4103b7f9aafdaaa0d539aaba8224493d2a58461635d8b3d",
        "hash_atual": "ce0af52407baf89770af2e17574e8a68e16c509c91a68c5f4225929db0098c9c",
        "tamanho": 10070,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "696b45ee80eec4497f923c76d8ba03f26c51c984180f35c6ec8d25ab404eeac4",
        "hash_atual": "33c450d3abd26a6eea05d8bca082e5b8ec8258460ca4d2cf6ac21cbfad5057ce",
        "tamanho": 6219,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "abce5e10e49d7872a2d675cc8590c98ed08e5689c6d87b8809c0adbeff264911",
        "hash_atual": "6e33808657a196b322b1236d0e51ae4069c1d98e42bdbae9e338b8e2ce3e938a",
        "tamanho": 7208,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "58f402690e53381f95591009eee289f8a95b2b5c9e58d503351484802940549c",
        "hash_atual": "53db0eb7dd779bfe4e2cbe9d36f2de4a8cef2658bca17cfc235656b3d070474e",
        "tamanho": 9296,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "eb0e3ba1ccb5403c77063db55a9b5df60e010c55f745ac6414ebef9c49caa87f",
        "hash_atual": "a45cbf8da5b0bb59ff316689f1771ca2989233b524548c73bfcf1d07c61d14b7",
        "tamanho": 20506,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fa9d7e973785feda9b47b3972509b924e8a1729ecc2bb2051112d3e6a24af2d3",
        "hash_atual": "94bbdb3e6f2e26bb1a6f754f6558ed39c8a2f4279af951f8eb58b8871b6bcc34",
        "tamanho": 59888,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f53448b2b02d36ec6985eca0f2fd959821d9ecea0d8953971d1a7abc798ddc14",
        "hash_atual": "5f8bafac73f021868adab2eac80546f42bd962ef4287ee0df8428d826329783e",
        "tamanho": 28604,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b40b95aada1c1c2bbfa898bd044d0d964d4296009da82b5214150c7242bb882b",
        "hash_atual": "67058dd4a1a6d928a0f529f2b15bca8f8a5654bf45efd0904d7e49132d4ae04d",
        "tamanho": 15343,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8952bec1bddec7d9a4a1fa1d437984c1736a84e4173ae92a2d0a5e818971d250",
        "hash_atual": "c8984f0688577f02ef3164b53d76a678fffb17300bef21d4e97c8a8374e4a60b",
        "tamanho": 26051,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "50efd84f1cdee0ad0867ba3d275b7216615353274e46717280764932c8a0bbf3",
        "hash_atual": "53141054b26bbe95b95e47c14bda845f31679d662391350ccd8be9d7ecbf99ab",
        "tamanho": 25889,
        "modificado_em": "2025-09-07T21:28:27.000000Z"
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
            
            Log::info('PreservarMelhorias39Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias39Seeder - Erro', ['error' => $e->getMessage()]);
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