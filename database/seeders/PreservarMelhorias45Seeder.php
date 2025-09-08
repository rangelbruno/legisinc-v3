<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias45Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-07 23:53:39
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "5615a277b50301a115e7f57feb353ad2922f0649fc3bc7ad49bf0006acf9e307",
        "hash_atual": "32ccbdbb47f93f9297c82de2f8779c26b89c644815a06a09951a2e5e1f7c29a6",
        "tamanho": 183240,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "fbac509a161608805b46670ed1e068da5a243d2d2ecdad53460e48c3158e0e43",
        "hash_atual": "933d49414a9fbf6cf648cf1f931a3737e2746e3a18525efc84a5a200a98f00c4",
        "tamanho": 33855,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "1347fbd6b0c34b8c387d4c9bae90127a0fc0d4da80768148e88303b7f76cc431",
        "hash_atual": "50a701f0fd53a2870e353b1369ba7a65e695dc0eb665fd65dabf817bf00b453c",
        "tamanho": 184884,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "c5ffb7188dc38e68a2b2a3823242ea410beee556af7724195d53f297f893c2d8",
        "hash_atual": "c91b744a224619d4970c24278cdeb606021d04dcbea6be5c09bb07f0de399f95",
        "tamanho": 37954,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "bef7ac60b00ec25849ff12a39e472710c87bf435ccd8fa0b712a0c4a47fe1400",
        "hash_atual": "9bd57f024630470618f7c6ff47c9744dd997007a5a4dac3db7a5a80b2b03f07e",
        "tamanho": 16468,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "634c91ae1a7d67d7fa5b1499aaf0655c0609c3997567eecacb5f68b78d596678",
        "hash_atual": "9fe5fc84e6305b54e8d32e7652c372ad9eb6f57bbe1940b9fe86039fdd13ead5",
        "tamanho": 16728,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "dc1964050301b9440eb48b05ea81a6c132c8bde1f2d673c130062e80fe9be1a6",
        "hash_atual": "d15c285cc7b15d6e97ef4ee68b39e1f3d12beb06f853823bb9b3c164d73b02c8",
        "tamanho": 11594,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cd6fe9ca5846744b815181ddb0eabcd1d97088d41879a67fb27fe927cf895000",
        "hash_atual": "0a53266881f68c87bb2e59a68a50141e7ea897ef0df2958dfaed0d0642ef8b53",
        "tamanho": 90333,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c77beed77bfc7b36962ee3e29de6a3f4df79ffbaf3d37779e9cc0ea91bee94b0",
        "hash_atual": "ca9e9901b6414ab7b9a4e06836b3f7b4360ded1f50cbe0112844bab15947c10a",
        "tamanho": 49890,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c72238921aa20e6236df5f362d6d3858cc09c042f6865a4e6b00130fb5cd4aba",
        "hash_atual": "0c50853967af141436575304e81fc5fd6a9c36bcc550d97ac6b3859b698b6205",
        "tamanho": 64199,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "41c253bc53cb45664bf780641aacb1c2ee603857470951de32113239e4d1c090",
        "hash_atual": "f4918d5f4fd079a1bc5f717eb376d6bee710fd80ae58b90a29d732b23a097b15",
        "tamanho": 21668,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e85e90d135942b49c505dab2d4fda9ae4307626db595a302704f66ccb3f2827d",
        "hash_atual": "753e23cf3c37f5bed6abce6b0988f2efd824d1e2a6f65f64536456bc97013da5",
        "tamanho": 39431,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cf43b89ee44f911403693a7b66a9630eb78e7175810fb226565bcc7a12248bf1",
        "hash_atual": "e14a004d704018a6bc25990905aa5991c155807e0957028b43100d47a43e74a0",
        "tamanho": 9714,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b1915efe885aa53839ef8327d715c778f4cef687ad2dff9373bcf3375f31a851",
        "hash_atual": "8bdc171f945a84c8f7584beb750b28863f5806c3d143d8385d8242ddd8b9378c",
        "tamanho": 2116,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d05cce5b984ebc35e5a95d33bf0d699f9e205733d8e3a3dc501dbba62286de94",
        "hash_atual": "76433f04910026458af1089668994585784406d8797bafd667bdf376813e5174",
        "tamanho": 8438,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "730aed7a7e88f91148a3de33cf4f40380f37a15fc37e1a50848c35fb80a24a15",
        "hash_atual": "9b65ee346123e1ab4f6bb4bbebbdbe0fee56447edd5d4d8f3ea04e402a716811",
        "tamanho": 19647,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6cbbe3fb768dd79b814ad8d6463f50326f33c570ee4914d5326c1861fa3f05b1",
        "hash_atual": "c77dd01451436b698f3eb6e223edc858b8f88093cd490031a96c12b93f0b409b",
        "tamanho": 18651,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3eef0fddbe37cfea3d2a02d26def48de75177cef05cd0c60e748a22dfdad92ab",
        "hash_atual": "f53863d1d3964a8771635700782124b38433b5bf26e48ea426787c717e3daf27",
        "tamanho": 44459,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "401ce4fa06a51f9d6bfd486bac7ff5a16204f684e3182e5972d0cdd48b485111",
        "hash_atual": "5ebf7e87eb5bb0bfb0cb1966b59edab1acd0c96ce982aa3c81d4b6a47a26b57f",
        "tamanho": 1169,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bb30cf449ee18b2593bf1a6ee2b42df11a793545d1548cb784b975cd36fa6b03",
        "hash_atual": "a4db72a2190a687c048bb75f12f86dbed70b377a50ad0eef8233fda6d863f0b9",
        "tamanho": 10124,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8d1d9dfdc83e6a86518b09370c6c77a91b9e6bc5f10ef200bdc8677b822d67c5",
        "hash_atual": "7b9e5d6b1056593a72d6fadd5d7be4636277120b2320deb2cae5097783668537",
        "tamanho": 8297,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8facf767c352e98bf6bb816971a45ce17c39a9d038056ae1152db89c8111862e",
        "hash_atual": "2b3f3ec940e6c0b8d6568bfaa7786150c9f8980dccb75780c88f61935541f26a",
        "tamanho": 8524,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a6691d73bb55b5f9aae3949c7f6d34c663f42f041c9e8b1fcdef71e93abdb5b3",
        "hash_atual": "4cc20e0786791c4d6d4395091f96aa1563bfd6f8c5b2cc44d5c6bd5345d96abc",
        "tamanho": 29449,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a461c815a38959d6c64b8c178719ba834b97a0e90dea98cf42f71799ff5e3352",
        "hash_atual": "2fd0c9dee78721bc18083931239cee0503f90752ef70146129f8c650f11a0ad0",
        "tamanho": 10070,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "37db2c4ffc368da17540d1bafe74858b54b053f617ac13f67a9850cfe64406af",
        "hash_atual": "cc2788b54aaf79941f1034fe89ce5c4e26430e08e2e5b013e91391c15e9c1280",
        "tamanho": 6219,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6e94ee765d2ef2604373c94f90f9c207e904387582d4f04c0275bb59e6201e96",
        "hash_atual": "0ed91e22b2e06b565747b84faf6b7a0f8ef54185786cc5172dbb18f50a82a162",
        "tamanho": 7208,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b8fa17d64b0e9460782fea67c8840c7663c91c5306c362324e2755dd90b10678",
        "hash_atual": "5f6fca100eb77fb86ebb3351bed22e23ecef9d630ed42d916c70dc06995c618f",
        "tamanho": 9296,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "833f6c3f7e806285653ad4ab9876db18d34d84af136cf48304d2ec97a2218ad5",
        "hash_atual": "018ac986b745701b663740b15675530bd1980491aec57651ffe84a97a31cff3a",
        "tamanho": 20506,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fbed8749a21842493483bba063c95954711ab97aad962581efed6e6fcbb50e4d",
        "hash_atual": "ec1c96523824c47c4902ca1b913aa9f066a2ffc058a6da041b8169316baecd0c",
        "tamanho": 59888,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c299a2ce7fb8618c49bc3e7118269a0318377d5d3fe1db2b799e5634c2db6f2d",
        "hash_atual": "c6ddc4ab26d3caaa395b75fa6ee00cc4e7fc1c442f8886b35abc71dc364f57bc",
        "tamanho": 28604,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "eb2511a2e275161e5efa4fbb586bac5773a0f7be004f1ac97e6cd7f86281bf3e",
        "hash_atual": "a7d767080acb912ab0c6834c9dca116422f87cbf1ab4fe356a0b4de7e7a072f1",
        "tamanho": 15343,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0f8e1ff20c573ad3c632e8d1e92e4167d7d994593df1133948d7018709ae1b3b",
        "hash_atual": "56eb03934ef7c937044d3f43abce46ef9fa8d317b32fb2510b7eac5f1ee191af",
        "tamanho": 26051,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "014cc880f275dce96b1f8593de314867bf8ce4566174127f61f4d48e86ea060e",
        "hash_atual": "c2461fe5c901381690c093c665baf1eac34d3af9669f8286d3d1e489b09a9b44",
        "tamanho": 25889,
        "modificado_em": "2025-09-07T23:32:43.000000Z"
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
            
            Log::info('PreservarMelhorias45Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias45Seeder - Erro', ['error' => $e->getMessage()]);
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