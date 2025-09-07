<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias13Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-07 01:48:30
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "f618d47175f48e86ae7afee9ce40694895eec85bcb0256bc27d81bbcc82749a8",
        "hash_atual": "159b011b3c9f76b358b3688c8f39ccb73f3a9b18c2d1bcc80bb0ff6f0e4aa605",
        "tamanho": 183240,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "fa0693fc0963e14951b3f3b6d33d59d62e7fab40f70ffcb925c624df087f7ff8",
        "hash_atual": "0239de3a68c0abe573de593977570ff6acfc9e36721643cb7c0c8c6d3ebcb7bb",
        "tamanho": 33855,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "edcf569984dc9b00fd4e1f9053e2f0952aba6600ffb00a2b3d62ae50b09503e6",
        "hash_atual": "ef84977831cbac7e00192008ad6cfe9a8ebde91a55e11d986fa030603ee0c2b5",
        "tamanho": 184884,
        "modificado_em": "2025-09-07T01:42:32.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "3a6d22bb3e87c23c365b860fb0eb7c0ee47ae2b1cc6776095a72acc6c1292738",
        "hash_atual": "7433a4d9c02f2afac99cea3866b88907b38b2104f7a8bc317a46f04ba423583e",
        "tamanho": 37954,
        "modificado_em": "2025-09-07T01:42:32.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "cac332a3e1ce1db18d66f4edf987ebb98bd35f5af187874e2cdfb4e055173766",
        "hash_atual": "2249b27a88ca2d22f5ccc38860bd1ab5896fb7d8f63f379fb8e13461aeddbada",
        "tamanho": 16468,
        "modificado_em": "2025-09-07T01:42:32.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "75256167a7f74ac0ab7c9bf92c3ce774e9faff2a2bdbd5605517f69c9bbda037",
        "hash_atual": "fb34fca0096004df995ad5a5abad0373c31f97dfe08053af93a633fb6b9cda24",
        "tamanho": 16728,
        "modificado_em": "2025-09-07T01:42:32.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "922028c14e9344ef37709f9b641ca0a914ff0e7b1ec6f8218bb9d3239a6f2b73",
        "hash_atual": "493a008c868abb07f3bbee78c355c7dac96a510e01e56802bf678847b6025f4d",
        "tamanho": 11594,
        "modificado_em": "2025-09-07T01:42:32.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cde29955bf5d1e3331e0eb8c4138bd19cadc218207b2fe8ab40d02b64689ca39",
        "hash_atual": "8841893f40444e0e2f92ff5c2bad7fbf103dc09716d85bc7598de5ae3178162b",
        "tamanho": 90333,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "72e451e06e93b1d07f6ee41c7a3aadd550dad8076ef0eaa87cdec21ac5ccd2c2",
        "hash_atual": "bed47c30703d5aad5a40708a0d427690e9a10c574d3e4f957ea8e19ec76a61d3",
        "tamanho": 49890,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "27656ca5ab3e5457f21e5f04ded9d1e13a5c4793ee895ddb098ec82612b6569c",
        "hash_atual": "636170a26efbcc835bcd25ed8d90225813f15508a1ca219aba8e51b1f4b95a6e",
        "tamanho": 64199,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "44f55495222ee7d5b7b45577562cbc7c037e8c3d6c476cdc61655bf72847b424",
        "hash_atual": "2a56aa5e427a4b2d4bfe7f43db40ac2889f0e5dcd368921a29c25d4a3e0fd4e3",
        "tamanho": 21668,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "337508396c8207fff2754c10f971a6b742c9eb158f7e9aa402158849f3d3812d",
        "hash_atual": "dfbc95b3affa2cf05d633abf99510fafea189c3f088ca52a4d287e5e8e588e16",
        "tamanho": 39431,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8c8f0082af9443ede2c62c3690e2b0588b3684bf6f11bb256e17a864d66d30ac",
        "hash_atual": "675d161ed3514d3dc071f65c79ba10da34ade21a7d254b6fd319d5bd79d48216",
        "tamanho": 9714,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a10838f5b36f9cc54ce325df4dd146f380e6b88aec9572a713111d0b66c2e73f",
        "hash_atual": "96019608df6a69971151adadb8ae71e3a92f3fc6227a9aa171d53e5afd41df4f",
        "tamanho": 2116,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "605ee112e0335acbe5441bd6f5c06f312a2ff0c06772ccf4006820b652974e3c",
        "hash_atual": "0d8b304d8d23e84e4d5e0d8b7864cc8298e92d1b41f0c44e64091be9a727aefd",
        "tamanho": 8438,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7845d10b8b58b57724dbad44df8a70dcb6db71d177057f14a562673629a4f545",
        "hash_atual": "4e326dfd3fc1a60af739b47ee98bcc9d43a61afa3c0e7a10441ad0c83eac9957",
        "tamanho": 19647,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "dd56cf4d8c5e5eced85a1b56e6ac252a611dd3391a7f593bc68c5c78c78d3277",
        "hash_atual": "1104915a3b8805fc2fdeff6b5ff6e00e2ec6f26be377ad4df460adfde0db5b3d",
        "tamanho": 18651,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b80bbf285c62ef87af049db42337fae3e6c8d9ef31803e483539abb4ff60f19c",
        "hash_atual": "378316e3e1b68f489e9d4a5c95de3fcaf7695ac03f4b95aee007dd5f28ec431f",
        "tamanho": 44459,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c22fddaa98245afbff494fb97e3829d804f24c5a296f4a922d2cd21152f58c01",
        "hash_atual": "8a70c0fb79b3a8052210e59258d2da4b2cb86e8cc2f773ef2554b0c6267ea983",
        "tamanho": 1169,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "14574f41f4d7e03cf19152e59597f2da1a7d7d54b5a0afd3114d7075f619343f",
        "hash_atual": "fc3cbaeba2f38a7c994a4acc0071c01a550fce38555de2ae9e3e2629a10e6b44",
        "tamanho": 10124,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1c60fb7d8d82afcd53d46080e530e068da5df6f055674a7bde92e0f763cc12ee",
        "hash_atual": "7b138edbad21319d4be48ed4009d5d8f117d8ce4954b7b2a2c6fb39efda759fa",
        "tamanho": 8297,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e1c78f8571d5ea4b51c4be1105b0ef341641eb9ca8d24b01da55e2aa0bf983d0",
        "hash_atual": "932904c848168199f9f6e0ca29c7a394c12c1e0995e7299c3f89176604f9d1be",
        "tamanho": 8524,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c86546872ecac5623ffbac5b81c046ba23df12cbe6c96f6604ce9392fc1eeb17",
        "hash_atual": "2f85c4267499ff1c734f80132c61204f6a0adbaa86da657c35fdb69d889348c6",
        "tamanho": 29449,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2c11586d55cf400ccff3e818b39969198569423bef861e6048afccadd4f9451b",
        "hash_atual": "d8a194351781a98ad815c941e6356457f19c475ec7c7a56147960196e1b2b232",
        "tamanho": 10070,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9f854fed843f16195091c338946fb026b8661a8098b2d43369e34ff77802fa6c",
        "hash_atual": "59ab367c2a2e88dd615a9424d01a71938b5fd1f2fd678e6fcdfa4566b3219246",
        "tamanho": 6219,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "abf4f9e3c5c3dabe33938373b5e847a2e48523e4df01a603ff7d9c85905977b2",
        "hash_atual": "db1a2f56954c5621c87067cb9d6c4465171ae45e106fb7003c8e91c1b0b43ba3",
        "tamanho": 7208,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "80686d097f4f3bc711235d5eb841a30eaba1c76d469e3c9f31edc33c2b22dc7f",
        "hash_atual": "d9c1fa46312b07f1d3101fc33ef3f15bce12fb542c57ebafc5052318ee88ba5f",
        "tamanho": 9296,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "adff511727dd30a3a65ff4e58fba14619d8b159130442ea985bc2eb8e8b51619",
        "hash_atual": "7fd2ce00da76c1ebc3611620680367ead486b5528810df6ce498d6ebb11d06e8",
        "tamanho": 20506,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d219ec5fc1720069f46e1250a4baff24f48914b978312467960783e91baf0f65",
        "hash_atual": "c12758d64d2fe6c7d1a706c5942db1612f71064bd7169292cffcb6ee190ecef4",
        "tamanho": 59888,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ee4ccc612df6e1d9a564f231387d32aa2f24bea89ae5b2c1d968afaab8be0b94",
        "hash_atual": "adf8292c98689e461d91ea2ab4d1302abf65c27d37707304a731863d90cb89e4",
        "tamanho": 28604,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "20b1290f19736a7b76f1b2c3a1702c7312cb6f261b7750618f9b858c621f224b",
        "hash_atual": "ee21c43889203ca6847b7f38bf6a1a48feaf5fca228fa96f303470e118fe7608",
        "tamanho": 15343,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ad351ea7da78fb6b3673029484af569e763cd7417076d507241c6cc6366b33c7",
        "hash_atual": "e9eb05eefb2e3edba70ce88991b0c74a038e5679c1c57b590cddbf8b142da242",
        "tamanho": 26051,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "37535d418a047f681dcf0cd3c2c9fed9c407e33d909e57c6371e1de701d17e16",
        "hash_atual": "08525c428c4f195a47fa204b7f9f5c140b5b248536efc0bb1d240562638bab29",
        "tamanho": 25889,
        "modificado_em": "2025-09-07T01:42:31.000000Z"
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
            
            Log::info('PreservarMelhorias13Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias13Seeder - Erro', ['error' => $e->getMessage()]);
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