<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias33Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-07 21:04:04
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "64b7b1a11d52901e72c47566079ff7e83db6a9c0c56544c81e0cd53ab7af1352",
        "hash_atual": "91b89c9c361a5d2adc95ea127060bff9e44128bd271b3b179579d539ab0b82da",
        "tamanho": 183240,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "8f23f2861a0b939b046451ed3a2b80d803ef9efa5366dcd4027c1bd922553623",
        "hash_atual": "fee4cea44aefeeb247cbe69e914f00c22a4b070abf06d2d9c600942ec792e7de",
        "tamanho": 33855,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "f96d06522f64bddf972c82abceab4bfc337ff4ddf68eb9143990d24bcf136bc1",
        "hash_atual": "a4b8d462a39fa9793711e5d0ae38d4f8f160cf48d564c73e085ac306162f657c",
        "tamanho": 184884,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "4825319f7cb1512668fa36e42a34514ec3a7778e9b64c1ca13565094cc2943ff",
        "hash_atual": "81b6c84727d7f56751e4c6dc83d0da181bc27731f6257fbb3a73524df68a0219",
        "tamanho": 37954,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "980cda7fb0e4d68c544b12f81f5fe77365a9a0d8d28ba5685efa8d09c44c9237",
        "hash_atual": "d75c41cb801f72005f4452d3e90de1bb829074c91439e7e391983c1471a03445",
        "tamanho": 16468,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "eac54fbda7916dfa6406022cb5a28fffdcf90eb9ea78a3a2603d782baf5d250a",
        "hash_atual": "44be26ba38376c121a143f08245beffc534fbc90d26304732907e5ef3da381b5",
        "tamanho": 16728,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "1708a3f75ba3e7049cce831c7d9096ab810e26e6f8ff0320662ca932e13b1278",
        "hash_atual": "53b08c31e103919791173bc1be733609e416c3c1b976387739442c02f82b01a8",
        "tamanho": 11594,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "362712fe84c5cad8bec6496cd90be2e8ed9ebb7908d58e09c4c49ddab83629e3",
        "hash_atual": "268964c76071a8212aedba4397f64939ca8181bac0e7df2a906c20e7eb1a5bd7",
        "tamanho": 90333,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "19d60c4fd3f3e04a839386ec7fefffb3350e1845fde42ca07957acf2ead11e59",
        "hash_atual": "08305c0e7dbabad084b7e207124c3ffdcae026a94f552a1a2e07329c2df7af6d",
        "tamanho": 49890,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bbcf4147fcb4f6916aa0097491b0b964c08feb6a14417657136f24c7d96cce73",
        "hash_atual": "4a68a39ef93a58b8943f58a19c1b76daf00dbd300b0fd87070d9d41b428e3192",
        "tamanho": 64199,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3531fd0b960ce52f732c201c656a7c716db120bd80fa1b39f791ce66f2367fbb",
        "hash_atual": "3bb60d5fec979acadcf9b69dd3fd34adf9ef46d1c3bb868518d61a2949c16368",
        "tamanho": 21668,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ea2e2b827e4d496e21c56f686580afe2ed135739fe30d20bac048c3bdd867ff0",
        "hash_atual": "a6cd44815560dbbd21c5d4a0c406d4e09b02c510e35712eb4c363338f46a044e",
        "tamanho": 39431,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2c1dd5a1e4b156d8c77e82e98916905cede01a2f38e720af0fdcf5b73b7e2f58",
        "hash_atual": "2e8790f59881fb91e9705f797fab83ac9440d429e1c986a41cb51b451262a850",
        "tamanho": 9714,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4f757a11295cb44ca7758d98b7bf99aa67a2c81d248f248fa09ac1db94c04448",
        "hash_atual": "1fb67d975eb1ed2620ef8c78a2dfa1cf4133d2f5c79b32a5c27d19561a8054f9",
        "tamanho": 2116,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e99569a8ea25b94fab9be6eb6793008db8a5da0d52c1edbce3074fbb242ca85c",
        "hash_atual": "c3e846de2788f8e27686906210fafc2a0fef5939de86bca15f5f613167fa5267",
        "tamanho": 8438,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "62b440b424596aa8169c3ff45708e65ed7175079bc36c7ca03ea2766311e12f6",
        "hash_atual": "8f05219c1e7a4871fb4c1ebea0c283bf11de6fd010342e42d538e19fae735216",
        "tamanho": 19647,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "476029a872b54bc4b72f28c6020c7eb09c391fc28379d334ef0d674969b7095b",
        "hash_atual": "a91655da9ebbe6575da8314bede25ded562584d96fdfa147ad787600179f4aba",
        "tamanho": 18651,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "88356578d4bb85cf0aab013a7ee1883afd0bb07b21f812bd16fa9d72ac735305",
        "hash_atual": "9295c08de3fa8b05a5a30a837226b7db8cf4deca5a5a95294b3ab1ad1aa26c37",
        "tamanho": 44459,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3685720e3464e8beb8030f16e26d247304b24d0d5040c6f9dd9562b3b550e912",
        "hash_atual": "90d79f183418b06fe71b96a3dec9f08f967c400ab27433d7ec63e8e1177400cc",
        "tamanho": 1169,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ffaea4b95016469ec59e89ea145bbdb3f550b48af4cb874160c5538d49e7012e",
        "hash_atual": "d9c23ffa121edfb31ff0bd568132a9686f2854299c05b010d85b1790e1982941",
        "tamanho": 10124,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b2edf288d85cbe9b5d9c3561a24c97ded9ccde9b70137ed87af17259bee42871",
        "hash_atual": "f6459e1b4f8d02e4fcbc4f5f06cbcfcead2945dedd017d8b53994242c2a3a915",
        "tamanho": 8297,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "235fb82ba34d93fc91d0d25369ab30fe5be0fba28382d5348b5d2da75cc13f97",
        "hash_atual": "77e28accb4bad196a2258eeb23c3cccff74282c3b4ebca36117ddc88ffa3ac18",
        "tamanho": 8524,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5a3d060d3fc1a1ac040707c8ea8d9b957dbca694f2c46a6d7c5b23e79348468c",
        "hash_atual": "dada87709b63826991d6205f82d0c9b18d27402fbdb8cb0a1124d131c83b5f52",
        "tamanho": 29449,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bdc9babc1035db99fc7e6b29b34d755e6edff8f368fd942fbad09491264ca69b",
        "hash_atual": "466ec16bcaab28d24fb797720a1b905a002720da2cf5392ced5ed644ec66cae3",
        "tamanho": 10070,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "017185e8885c52fae7d5a813488da074f490302b2b54d7fd0e14ce4ee37fe8a1",
        "hash_atual": "fff4150729a66b11bab11a3fe74938a5ce82e1e3f7a3c41e6d2f6ffe56d19b17",
        "tamanho": 6219,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5af6329affecac24a55849bacc1034ee6b57e0b16be7be559a26341f83e2d79a",
        "hash_atual": "1170f02e2e9a5af3ffa82afb1bdbf4a3b85e8685510a7a7bc6ff1aef867d835b",
        "tamanho": 7208,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0e5b2fe235b733bc069a37aaa2b137239e119e4752e766b1efee7bc3eeba371d",
        "hash_atual": "1e5cbc1a7d87292b82b5771e78bd90ce19f05e407d14c9bf6eb1a545e3b3dddf",
        "tamanho": 9296,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "52d1033511de009bacbe95ab272ad02f9022bb1715f19cd395dee52113e3e00a",
        "hash_atual": "afbe3819b1878323daabf8a44f2d503dade161b74dca4ad9a82ab6d5635ec4f0",
        "tamanho": 20506,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "54e3ef7bf57ce729c28f3c9d31a481c40d9183db8fea76f9ef4199ba75a77e1c",
        "hash_atual": "6f6d1904dc0e03f89312986caf91dc352ecefb7b37150adfe5c1402665d398f1",
        "tamanho": 59888,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f04ea4469a2c33d36a9feb7e04cb86ef62e68829446f1c66be99306a6eb4289b",
        "hash_atual": "516685e94c32235232671526adca20bd5a20efcd4bf7fc4f0cae46ee0c31375f",
        "tamanho": 28604,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "911823e211aa0df42b389b6eaf5a1c7acd1a048b5ae8516e8b285d9aa3b76cbe",
        "hash_atual": "efa365cd4b33c8c0150c3eb26595c7969ef094d4b43a141ff8680da2811d8f49",
        "tamanho": 15343,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d08b21d57eab04dc56e361801872f26f1706b5cc0bf8a2ded0720f2846b3adf1",
        "hash_atual": "ce6310eabc3d872e9f4c1642f572a28ba65d5feb835d719bb8cc248dca578382",
        "tamanho": 26051,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "59115c964f4a4b7bfa4f57afd0228179efc988aa3584c15e71477d1bdf4ac45d",
        "hash_atual": "6cd081750eff889d9782ec3fa63dc5c303b87eb0bd963ce5bc0926bbba314192",
        "tamanho": 25889,
        "modificado_em": "2025-09-07T21:03:06.000000Z"
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
            
            Log::info('PreservarMelhorias33Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias33Seeder - Erro', ['error' => $e->getMessage()]);
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