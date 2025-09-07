<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias15Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-07 13:56:24
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "159b011b3c9f76b358b3688c8f39ccb73f3a9b18c2d1bcc80bb0ff6f0e4aa605",
        "hash_atual": "f750ca29f25edf9935383ae8acc6aca6ed86ecdc62015c66c913d3764e8e778e",
        "tamanho": 183240,
        "modificado_em": "2025-09-07T01:48:36.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "0239de3a68c0abe573de593977570ff6acfc9e36721643cb7c0c8c6d3ebcb7bb",
        "hash_atual": "34cf403f870c876f61a6e9f75ce5d3e6f33cb5e5816a2330b20113c7cadb4884",
        "tamanho": 33855,
        "modificado_em": "2025-09-07T13:56:15.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "ef84977831cbac7e00192008ad6cfe9a8ebde91a55e11d986fa030603ee0c2b5",
        "hash_atual": "cfc80a4b6886f36d89965727d6a989a8f71161015e43e41a995b41841bd2a99c",
        "tamanho": 184884,
        "modificado_em": "2025-09-07T01:48:36.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "7433a4d9c02f2afac99cea3866b88907b38b2104f7a8bc317a46f04ba423583e",
        "hash_atual": "121ec46ebe335e9c33b470e76909c8a8a448a45fe7d2d67e43574bf993b7a534",
        "tamanho": 37954,
        "modificado_em": "2025-09-07T01:48:36.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "2249b27a88ca2d22f5ccc38860bd1ab5896fb7d8f63f379fb8e13461aeddbada",
        "hash_atual": "5207a7bae2060a74abc9c3b2bd9c70228f3ea0e85ceabbadf51d6315c083b322",
        "tamanho": 16468,
        "modificado_em": "2025-09-07T01:48:36.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "fb34fca0096004df995ad5a5abad0373c31f97dfe08053af93a633fb6b9cda24",
        "hash_atual": "bec674cb7767695ba05c5225faa4f6ce5a331f4ca3ea6bddbfea5e1fb3b5151d",
        "tamanho": 16728,
        "modificado_em": "2025-09-07T01:48:36.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "493a008c868abb07f3bbee78c355c7dac96a510e01e56802bf678847b6025f4d",
        "hash_atual": "a9ecdf805a953fc7ffb6c204af4f7395509cc152c80c3024318d48ac14058528",
        "tamanho": 11594,
        "modificado_em": "2025-09-07T01:48:36.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8841893f40444e0e2f92ff5c2bad7fbf103dc09716d85bc7598de5ae3178162b",
        "hash_atual": "0e9ec7ebc283b33841d9109ff0d42226864003562fae8e42f562296c9abfb5dd",
        "tamanho": 90333,
        "modificado_em": "2025-09-07T13:56:13.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bed47c30703d5aad5a40708a0d427690e9a10c574d3e4f957ea8e19ec76a61d3",
        "hash_atual": "252fc7265ded8e96e955f382e967f6ba472791b5f501c08e76c9e8cfb416ec1c",
        "tamanho": 49890,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "636170a26efbcc835bcd25ed8d90225813f15508a1ca219aba8e51b1f4b95a6e",
        "hash_atual": "54b06adac199feb387f07b0b1be82a90d77760ebcf92c6b8fc6354278f2e25a4",
        "tamanho": 64199,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2a56aa5e427a4b2d4bfe7f43db40ac2889f0e5dcd368921a29c25d4a3e0fd4e3",
        "hash_atual": "a90492c8ffd007165c336a4f492589c110ec14be29d7471795cc15c90f1193e5",
        "tamanho": 21668,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "dfbc95b3affa2cf05d633abf99510fafea189c3f088ca52a4d287e5e8e588e16",
        "hash_atual": "b1a5337a6894f5128762ea87a8e3f6e2948510914db3f3b2d28982d5ac2b8021",
        "tamanho": 39431,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "675d161ed3514d3dc071f65c79ba10da34ade21a7d254b6fd319d5bd79d48216",
        "hash_atual": "a615fcaf91ded97160034f09e7cadcfd5d5c0a067aa513c9bf4de3934641ccbc",
        "tamanho": 9714,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "96019608df6a69971151adadb8ae71e3a92f3fc6227a9aa171d53e5afd41df4f",
        "hash_atual": "78350b179f324442a2b0c686a5ca826195c8117b654b4dfe8d2d002aac87e511",
        "tamanho": 2116,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0d8b304d8d23e84e4d5e0d8b7864cc8298e92d1b41f0c44e64091be9a727aefd",
        "hash_atual": "4585960443a3a95975575dbef893d7fce1ec4cbbff3f46221da2c0d5ad32850c",
        "tamanho": 8438,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4e326dfd3fc1a60af739b47ee98bcc9d43a61afa3c0e7a10441ad0c83eac9957",
        "hash_atual": "a7f880d64217276a94f2edb950304f824ba32eacac07c97628f7e51ab2d22da9",
        "tamanho": 19647,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1104915a3b8805fc2fdeff6b5ff6e00e2ec6f26be377ad4df460adfde0db5b3d",
        "hash_atual": "57051d415d1d68dd6965efbc24111badcb278f7acc3350b2ebd1d3da7d804cd7",
        "tamanho": 18651,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "378316e3e1b68f489e9d4a5c95de3fcaf7695ac03f4b95aee007dd5f28ec431f",
        "hash_atual": "8fce159df9f47d297cb6e3eb46b2e22e178d78bf7a803e434b0bcea070369373",
        "tamanho": 44459,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8a70c0fb79b3a8052210e59258d2da4b2cb86e8cc2f773ef2554b0c6267ea983",
        "hash_atual": "592855d518379b814a308a1768c23640465c11674ff060a467322304a659167f",
        "tamanho": 1169,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fc3cbaeba2f38a7c994a4acc0071c01a550fce38555de2ae9e3e2629a10e6b44",
        "hash_atual": "57f571e92161adac99b88359fb30e70b64564bcf102eb3acefc255320cb9f6a0",
        "tamanho": 10124,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7b138edbad21319d4be48ed4009d5d8f117d8ce4954b7b2a2c6fb39efda759fa",
        "hash_atual": "e7e288e637a4206e47c3764e5714d33f28b62dc0046561711c54ce72d69b4788",
        "tamanho": 8297,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "932904c848168199f9f6e0ca29c7a394c12c1e0995e7299c3f89176604f9d1be",
        "hash_atual": "a13edb5c564b5f534d78dd6ebae7fbd28c758f0b5ed601d5c7188e3e867e9047",
        "tamanho": 8524,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2f85c4267499ff1c734f80132c61204f6a0adbaa86da657c35fdb69d889348c6",
        "hash_atual": "b8d1186312a05bd506ee6df88332f15f7a0ab2247668db3488ca6b780761ec49",
        "tamanho": 29449,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d8a194351781a98ad815c941e6356457f19c475ec7c7a56147960196e1b2b232",
        "hash_atual": "ea23370926d0f8083f55538fc73ed1c9d6c61367d7553261a1182efbd07c3db3",
        "tamanho": 10070,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "59ab367c2a2e88dd615a9424d01a71938b5fd1f2fd678e6fcdfa4566b3219246",
        "hash_atual": "6264f01c59c300efa8290c3286c4804888a28e1e348a6c858f3b3af87903ba09",
        "tamanho": 6219,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "db1a2f56954c5621c87067cb9d6c4465171ae45e106fb7003c8e91c1b0b43ba3",
        "hash_atual": "ab9d030e9eb5d93db34f6be31ba391f65128350af68940fac387cf70f07a58a8",
        "tamanho": 7208,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d9c1fa46312b07f1d3101fc33ef3f15bce12fb542c57ebafc5052318ee88ba5f",
        "hash_atual": "e833be71e1cba5673228340caadc0aa9a5dc2b3ac55d1a132e0cb8e0f755e7c1",
        "tamanho": 9296,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7fd2ce00da76c1ebc3611620680367ead486b5528810df6ce498d6ebb11d06e8",
        "hash_atual": "0152c126ffa9b28a5cc523e5716d8d0297bda530593d2c673a79ae4cdd62568a",
        "tamanho": 20506,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c12758d64d2fe6c7d1a706c5942db1612f71064bd7169292cffcb6ee190ecef4",
        "hash_atual": "3f8b0968f95d124a4ec6c43674ea8389edf39c2d2cb6a5e6c4d66f0a863ed422",
        "tamanho": 59888,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "adf8292c98689e461d91ea2ab4d1302abf65c27d37707304a731863d90cb89e4",
        "hash_atual": "db527d101d5ab8b006f9fc075cdaf53ad24e7c1dbb298f820702b700cbc668c5",
        "tamanho": 28604,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ee21c43889203ca6847b7f38bf6a1a48feaf5fca228fa96f303470e118fe7608",
        "hash_atual": "221afed1e78319c9eef1f558e07c68927ad6999e47ad3424e86a993a797a102a",
        "tamanho": 15343,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e9eb05eefb2e3edba70ce88991b0c74a038e5679c1c57b590cddbf8b142da242",
        "hash_atual": "85f2c43b8759c3f3c73ee18b1ab7b0d6135995e5922ac368643431f31637229f",
        "tamanho": 26051,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "08525c428c4f195a47fa204b7f9f5c140b5b248536efc0bb1d240562638bab29",
        "hash_atual": "a6aaf310897af0842e8585134c49976b20b211f2f272649dac7b357dd14651ce",
        "tamanho": 25889,
        "modificado_em": "2025-09-07T01:48:35.000000Z"
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
            
            Log::info('PreservarMelhorias15Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias15Seeder - Erro', ['error' => $e->getMessage()]);
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