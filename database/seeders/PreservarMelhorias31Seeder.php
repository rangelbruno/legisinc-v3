<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias31Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-07 21:02:59
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "5b8a45666dd1dd1d9be91520f5fd8e2d829e34c0c2ff75a67f023fc0b0be1d14",
        "hash_atual": "64b7b1a11d52901e72c47566079ff7e83db6a9c0c56544c81e0cd53ab7af1352",
        "tamanho": 183240,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "280c8a064459ec717ca1f409c0a4b0bc3e2d39d87e88a956e778748432785c47",
        "hash_atual": "8f23f2861a0b939b046451ed3a2b80d803ef9efa5366dcd4027c1bd922553623",
        "tamanho": 33855,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "0db1b0e51c8f8dc57644c159c96dca2c7e2a850a3429499fc6213dbaa5b115fc",
        "hash_atual": "f96d06522f64bddf972c82abceab4bfc337ff4ddf68eb9143990d24bcf136bc1",
        "tamanho": 184884,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "b3bb8d4b69a8ee5c0bd7366ef303c3724844e1f25a76f4a08184b4120fb07883",
        "hash_atual": "4825319f7cb1512668fa36e42a34514ec3a7778e9b64c1ca13565094cc2943ff",
        "tamanho": 37954,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "e2476a801109b6e37ffe5d0e89ff2f8c9d2bc6545cc697e7760827fee4944778",
        "hash_atual": "980cda7fb0e4d68c544b12f81f5fe77365a9a0d8d28ba5685efa8d09c44c9237",
        "tamanho": 16468,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "6d415a88f30fed4c113b5473bd5df4b451397c2d2f778c9f797736cea8cb27ae",
        "hash_atual": "eac54fbda7916dfa6406022cb5a28fffdcf90eb9ea78a3a2603d782baf5d250a",
        "tamanho": 16728,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "51322a7fedbc38bfdce2b26eb6dfea1ea44d07ce1462feb35e60b349aa4be9a3",
        "hash_atual": "1708a3f75ba3e7049cce831c7d9096ab810e26e6f8ff0320662ca932e13b1278",
        "tamanho": 11594,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9abb376a3b4437fa0d712aedf4f79a3537307d5102051edc7f4282c7fec33afe",
        "hash_atual": "362712fe84c5cad8bec6496cd90be2e8ed9ebb7908d58e09c4c49ddab83629e3",
        "tamanho": 90333,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "baa63941a98c860435e808680c52c6b2d28947df588f6176a1393b53bd778e92",
        "hash_atual": "19d60c4fd3f3e04a839386ec7fefffb3350e1845fde42ca07957acf2ead11e59",
        "tamanho": 49890,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "049c4a67b48e4e54aab09b5bc455ccf0c9078e00b6d292e80e10c0d01b79d3ea",
        "hash_atual": "bbcf4147fcb4f6916aa0097491b0b964c08feb6a14417657136f24c7d96cce73",
        "tamanho": 64199,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "983ee95be66571e0fa9442d26d615ba6ec70614328de702557b418d4be255c6b",
        "hash_atual": "3531fd0b960ce52f732c201c656a7c716db120bd80fa1b39f791ce66f2367fbb",
        "tamanho": 21668,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f9ae5ea38beadfece3b291286df6797da1e262ee45c7a271da36ed79a99b0809",
        "hash_atual": "ea2e2b827e4d496e21c56f686580afe2ed135739fe30d20bac048c3bdd867ff0",
        "tamanho": 39431,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fda72a6c7655a916d8c89b979e1dc4299e7f7ba68662078b17b828ddf6c63a22",
        "hash_atual": "2c1dd5a1e4b156d8c77e82e98916905cede01a2f38e720af0fdcf5b73b7e2f58",
        "tamanho": 9714,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "521270e9f5de5bf6e31d1760777242649e63d4b4b3ca9ca5fa7ee5634e7ccee6",
        "hash_atual": "4f757a11295cb44ca7758d98b7bf99aa67a2c81d248f248fa09ac1db94c04448",
        "tamanho": 2116,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "211d72053731e6dd8fc4d69e058adad27017321bdfb15bc39b408bb55ee21125",
        "hash_atual": "e99569a8ea25b94fab9be6eb6793008db8a5da0d52c1edbce3074fbb242ca85c",
        "tamanho": 8438,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2b9e45417bf8e0b123b70a7514c5f5d9d690479fcb734bf04ac04a4d3f5df116",
        "hash_atual": "62b440b424596aa8169c3ff45708e65ed7175079bc36c7ca03ea2766311e12f6",
        "tamanho": 19647,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f40eccbd29e640d7a83d884c6411f79f590fe633de2b366c986dd784d0dcaeec",
        "hash_atual": "476029a872b54bc4b72f28c6020c7eb09c391fc28379d334ef0d674969b7095b",
        "tamanho": 18651,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ee6ee37a93f234c18f1997d7d16c2ab96f97c046d80c435c7d9df1bbfe362e6a",
        "hash_atual": "88356578d4bb85cf0aab013a7ee1883afd0bb07b21f812bd16fa9d72ac735305",
        "tamanho": 44459,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9a33c56a526686cc99a98b02c990f144b97cc4f61dc8a2af5a3424b6a9212cbb",
        "hash_atual": "3685720e3464e8beb8030f16e26d247304b24d0d5040c6f9dd9562b3b550e912",
        "tamanho": 1169,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cb7eba641d8cdbf1db174319e242616b8f2ee7536d00a159ffe2cb85b77a56ba",
        "hash_atual": "ffaea4b95016469ec59e89ea145bbdb3f550b48af4cb874160c5538d49e7012e",
        "tamanho": 10124,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "295efdfaa3a82b812e37c71079e93c91d75fc48afba75a5ef68b537eac2e0f81",
        "hash_atual": "b2edf288d85cbe9b5d9c3561a24c97ded9ccde9b70137ed87af17259bee42871",
        "tamanho": 8297,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "80366daeb22ef2a49c1d3f13ebd564d3c1503a0388a0c65a6e1931ee00ef1718",
        "hash_atual": "235fb82ba34d93fc91d0d25369ab30fe5be0fba28382d5348b5d2da75cc13f97",
        "tamanho": 8524,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fdf5ee78ca17c5f4a8a07e66c95567bf544921f40308e218c64c617e8ac4e5d3",
        "hash_atual": "5a3d060d3fc1a1ac040707c8ea8d9b957dbca694f2c46a6d7c5b23e79348468c",
        "tamanho": 29449,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "00529ae0d301030d827b6eb3b3f63096bf74a01f7906d1c0fdbf8912f91fa032",
        "hash_atual": "bdc9babc1035db99fc7e6b29b34d755e6edff8f368fd942fbad09491264ca69b",
        "tamanho": 10070,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "87efae821ac209b9e36064283f2ce17d7c7b5c22992b48b484589e2a0f8a211c",
        "hash_atual": "017185e8885c52fae7d5a813488da074f490302b2b54d7fd0e14ce4ee37fe8a1",
        "tamanho": 6219,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7afd04de6dca231f7ea0db8938603a34ae4c8995bb1088b09a0a97821caf5e35",
        "hash_atual": "5af6329affecac24a55849bacc1034ee6b57e0b16be7be559a26341f83e2d79a",
        "tamanho": 7208,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "76cc9e6875da5131eaa094ae9367d8ee201baa880ea67e069f24196416b89302",
        "hash_atual": "0e5b2fe235b733bc069a37aaa2b137239e119e4752e766b1efee7bc3eeba371d",
        "tamanho": 9296,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "db0bdc43e335258834ded920e14ac25277b0d4c4d203961a6348e9cb7850b8ac",
        "hash_atual": "52d1033511de009bacbe95ab272ad02f9022bb1715f19cd395dee52113e3e00a",
        "tamanho": 20506,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fa3f2de8d57615266399fc09a1c70a803e463338d063018692a45b92182fc5af",
        "hash_atual": "54e3ef7bf57ce729c28f3c9d31a481c40d9183db8fea76f9ef4199ba75a77e1c",
        "tamanho": 59888,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6d69004f671c7ede18c0f11cd40238455092ab3518914a779aa9ae8262ca2f63",
        "hash_atual": "f04ea4469a2c33d36a9feb7e04cb86ef62e68829446f1c66be99306a6eb4289b",
        "tamanho": 28604,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "679bb5cba9c7fa194384b1bb4d2a031593e10b8b3e3fadb1441f8d9557745886",
        "hash_atual": "911823e211aa0df42b389b6eaf5a1c7acd1a048b5ae8516e8b285d9aa3b76cbe",
        "tamanho": 15343,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "140c477264e448f31b14fd5d9b53cccc1fb56693dd99b3263fa04481f89a6542",
        "hash_atual": "d08b21d57eab04dc56e361801872f26f1706b5cc0bf8a2ded0720f2846b3adf1",
        "tamanho": 26051,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a5d73f2865f4973ccc4dfa15b72d09638d46db88a3555df9e89b4b52566bc6a7",
        "hash_atual": "59115c964f4a4b7bfa4f57afd0228179efc988aa3584c15e71477d1bdf4ac45d",
        "tamanho": 25889,
        "modificado_em": "2025-09-07T20:42:39.000000Z"
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