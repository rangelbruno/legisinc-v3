<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias79Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-11 18:20:17
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "359cc3a9b176de11d2ddea4f37010b0fb36ca756625f9fc584c7734c7e96339e",
        "hash_atual": "5eb790fb37d4aa4203ecc1d7dc48e668aa152e55d0d3518ac24af0d7e5312235",
        "tamanho": 185055,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "ba86b3eb945d9e11e2fd94f60aaa0c9c9c6597406f8b8cc664475b3d5881d6c5",
        "hash_atual": "749645b256cd608ee3278f1b0940efad48b24d9432c372a735668afe19d116ea",
        "tamanho": 33929,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "92462809fe4e1ae9d8643ca12dd92cfab654b29e937e049441900bdc1259a415",
        "hash_atual": "93176d2e5bfd0dc345c6d892c7925799eb195a13a69990d0ba59fb18e435bf70",
        "tamanho": 184884,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "8de4dd28bd3dcf1f0806cd718c4562235331df4bde350d43e45df2a63aaf7157",
        "hash_atual": "52c85abadc1da5cec8665dfb533c3aa07b387649c1ce869d5a86d306d2521a3d",
        "tamanho": 37954,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "7cf5802d9a8fcb286625eda3b29ec77d64c9822721cc596398aeede31db8d1b3",
        "hash_atual": "03527dd0956cd2acd1701216ebd4fd93b319b579dd3623ec1d70d8f23dc3219b",
        "tamanho": 16468,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "5ae37d64f1dbd2b29854eb245d0fff3d39a06363fb8f3adb60aa76434da1ce1c",
        "hash_atual": "11e6caf7e8f856a281ee7af3ad8e404bbfbe43de3438c9599e9fdc4771afae22",
        "tamanho": 18417,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "8e8d565a4a897472d9cf6233849e6366af81c661aec945acef1c64e5747404f7",
        "hash_atual": "5626217d40509b12b2fd54d6dc6fc14a72124b3ea4b9b214b3d5eea4cd62608a",
        "tamanho": 11594,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "07faf7386368e22e9646373194d62b2cc239ec3de977b91dc1e2302c43e7803b",
        "hash_atual": "6e8f1e76643b061ee30a9b2c1a7c2b5aa0c0e1ed2dfa8bedc776a96a902dd300",
        "tamanho": 90333,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a435ebf8f34817c591847af3f80fabbeac34e0d023d0cfd103f2162fe9a64dcd",
        "hash_atual": "091f25c7de733dfbac342c10fcbbc8d2e708979a939b03be0b403ccd8259d2d0",
        "tamanho": 66812,
        "modificado_em": "2025-09-11T16:46:15.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c3bd095ea0d5ad55f17af124fbb79d8a7258c61ae34cf4f15e6be3c055ae2800",
        "hash_atual": "4460ff455651832237ef9e58be36a223a38c6074b9602d92c19dec43d3ecce66",
        "tamanho": 64199,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6e4a119473212e34283f5f9ef5cc2f6e14737674408dc51e1c18a780b87be7b5",
        "hash_atual": "8153ba4881cfc8fb3420b579e8b3962622c32937bc0daa4d38d31eb851d323b3",
        "tamanho": 21668,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "825d829026a446991f70c7f57141e4960b63303007a3e7cca15b1c70120ae4a7",
        "hash_atual": "2743506bae4228e294f6240895410076d02a9108159d0290a4914d45aaba47d4",
        "tamanho": 39431,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "40e1f075c39e96d95d499a2cf39ea814b5573527a7b2995242df79c32254ad62",
        "hash_atual": "19e75adb30631e200455157231a40cd080ebcc99b3b371b6597517c8e192893d",
        "tamanho": 9714,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fff551c48866820f2ea612ce271422946e1bc633b33a4196900d5f02dfe0fb53",
        "hash_atual": "5faced7b4a3f0f02bf8c07f30747a616003626fa1ffb3de20181bbef67c19164",
        "tamanho": 2116,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "36670a99f424978a7493b7d001dbe91f94257da9360766685d11efd076266b6f",
        "hash_atual": "e8ebec4faa21315bb1a9b57a1df970860ce82160d34ab71975db30b7e272102c",
        "tamanho": 8438,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e7404306aa4481eaefc758d629ff73fda15405bf6c9971cdac2f2e6630dfbfe0",
        "hash_atual": "c76c8b02ade332cdb51a24083b347d69d1b774fae3b61f693642bce0b4bfc39d",
        "tamanho": 19647,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "95acd470f0935778940baf2fea195d4cb14e08f2f9604540188f769e577c8992",
        "hash_atual": "9f0799b0dcebf7ea6ddb259ca29bc7591eca71ab093991f2a0e354143d38c775",
        "tamanho": 18651,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e07e901d2fed1431e824e86ea89743161a0532b9c390104aa9d4c6af24ae3e89",
        "hash_atual": "899998d893bb9c5e6bfa337350e115233c16c060bb3606e9fac11e3297cb08f0",
        "tamanho": 44459,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2a782641937be6e416590679711b52e06acfbfb5013816a452e47c396e11cf30",
        "hash_atual": "c659a5b3a2ba923d4ff8b47ff4f8fb73995829ab82b60a6b65a3a3cd0dd6864e",
        "tamanho": 1169,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0e689f4afd3f8c80d2f7f6ddbb6a6c4150ec00d668c672142e75182e3188b59d",
        "hash_atual": "9468d67c569b72674adc7de0bed208e7d03aeb9c41297f28948289a2fefa3014",
        "tamanho": 10124,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6974520b287f790a28aa49f694adb8a17072a50bf85a57f22c02bd594558af96",
        "hash_atual": "191f7cac08b92bf988dc3a41867d9eea72324b0f1d3c27fe40fb0fe66bd458be",
        "tamanho": 8297,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0bc089b1094dbb331e3fe023325ea995dd3b475b0933dc141697ebd57ef31b77",
        "hash_atual": "f0647a068e1a664fe52ff259248118d18f8c9f7566bb7fe5f9f23533624ddb40",
        "tamanho": 8524,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fa5128c2aefc26519589a18c2853184c17910b109fb61c6d1bd252fdafc76b54",
        "hash_atual": "c13db53ed5619918f5e5617c771a5aca5024745762f53bd4f6862586f33c28b8",
        "tamanho": 29449,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "aadced91db8290a16c1258eab9bc6cb3f109e1626e0a4d86945e9f26ea7220ad",
        "hash_atual": "4e250148f35cf97d555b72a40ac7631d79907d1f09bae77ca927cfb3036a130d",
        "tamanho": 10070,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "88617c586cb2409c68c84a45fe0c174635305c8a1f7e16dd323a438c506d6da2",
        "hash_atual": "895c133ebcfffdd310a334560107dafd831217b1dd81e548310acb253dc6fbcd",
        "tamanho": 6219,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e3b41df0a575f4c52f17618d63dbb2f51122fe4a2a51215cf88c34a4637803e2",
        "hash_atual": "37aa318576bcc2a68aa21c5dca923a8ed1f3d80ad8d3c2e297135419890e67ba",
        "tamanho": 7208,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d9aab296193612c8c08040c1ed0f7c3eeb8ea7e330833632a1da11f543388911",
        "hash_atual": "831f953db774abff456cbde36e4689ea300b7fc3105a1976e218be8a086f0876",
        "tamanho": 9296,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "26d05d5c0967da58f86db0b8b0279ea5fddb4d4646f618d03c08738b6a9dad04",
        "hash_atual": "09d6ded7cf641bc345f168952eb1584aac1f4a8fa5055b4b6e7685a5bb0c0a0a",
        "tamanho": 20506,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cadc1eb0fa35c4f063e0ecf6458ce8388c99174340782eadd6aa9d3112d81e0d",
        "hash_atual": "afa7a153047d3ac78f0edd782b79bc2bb117f0989824898137503712d1fbca71",
        "tamanho": 59888,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0a88b734f97943d09ea8aabad3dd99ba2039e770d2106c4826ad7f8ab661516f",
        "hash_atual": "ff6db9a9d056923f400b9eb860c8d9f82c88c5b176c96764b20098a4b62f73c4",
        "tamanho": 28604,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e81ecdd9881459ed50ea96c23eec3535c77d2399f8fd0dcaa41c3c7bd2232976",
        "hash_atual": "2bbb3d72c1c72e4a75c65aeb6e2be77e36fa24336f45d81e66eab63f86410307",
        "tamanho": 15343,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5635bfd75c1eab56314ac803037ea12d376389f43b010b571bf22facf51a85aa",
        "hash_atual": "7a2ead24b4a7edf3c3d7dc6ae30067d9bf052d826ff9ca98c0851ff5c474eccb",
        "tamanho": 26051,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "89382712d0d26d6b880639198fc1e4f0ecc0d89dc164b823146a89324b83b34e",
        "hash_atual": "dc62c88104a4ec48e1751c7f2cc292c26deba386eedf9f3f95cc7512e82587ac",
        "tamanho": 25889,
        "modificado_em": "2025-09-11T00:50:25.000000Z"
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
            
            Log::info('PreservarMelhorias79Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias79Seeder - Erro', ['error' => $e->getMessage()]);
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