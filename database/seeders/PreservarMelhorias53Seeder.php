<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias53Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-24 10:58:27
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "ba272f8a92a8b053ed837eb889ef2725c1962d517a151aa00f5bd9940c084eb9",
        "hash_atual": "fbf48ab5bf0a79274685f8ff3e3ca7c49a67e15d9d823ffa64faf5415d35ed83",
        "tamanho": 199451,
        "modificado_em": "2025-09-23T13:57:27.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "0fbd70c4d17a9ce0d480d7b1f45ff3b0c346329122a9f0cd26435cc16985a363",
        "hash_atual": "c5cc57c487a474181a5124b6f5d24f312686b65ba0199696f035c440a1399113",
        "tamanho": 38821,
        "modificado_em": "2025-09-23T13:57:27.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "5daa6d0706ef08db1ab8cee0a35b889e435c4c347a5cf70b3bf52568f13b4a7b",
        "hash_atual": "dcc5e755e7f4b01d1b46d5273967598c1a52516b178ba7be0901885aecfcdbc0",
        "tamanho": 190861,
        "modificado_em": "2025-09-23T13:57:27.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "9d8cbc3d8830a5fe2198c7885a5d00a7ce2889e20c3830db2c01f852e4a357c9",
        "hash_atual": "0ebbad191784ba4c494dfede738ad277cda61bc2f382514bdf28ae485974b588",
        "tamanho": 37954,
        "modificado_em": "2025-09-23T13:57:27.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "2fbb04bde0061b2519b6544ee5afe6189323a00167ff45e78cf02610b73e369d",
        "hash_atual": "016939cb8d231db89d8503252c15d13d284eb6300129d6e9e22c7d252f52f95d",
        "tamanho": 16468,
        "modificado_em": "2025-09-23T13:57:27.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "ad85fa29e10b412d2819b3b842e48c232b9e46574012a38b368ed6ee2e9a4f60",
        "hash_atual": "435b05783c1b8dc944b4b74952faf230e09766f453dca867eeea5814f3f75b91",
        "tamanho": 19682,
        "modificado_em": "2025-09-23T13:57:27.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "e7ab32fed81a47b419eba4835617f105d2f69516a3d16fe0b7e2aea89b23c8a7",
        "hash_atual": "43512cc10a7aafa2de259447830337cdbe4f878322690968ff6648077f0a3ffc",
        "tamanho": 11654,
        "modificado_em": "2025-09-23T13:57:27.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ac7af63dd2740facee676aa81eecc42dbe9fef91ead3db25161402d25f99935f",
        "hash_atual": "cee44eeb0e87c9e9dae2fcffff868aa25b7603b781356414b6603663a257fbea",
        "tamanho": 90333,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "61e2d002b32113003c63b075f1f760dbfac4f4901a327e938169bac04cc44c8d",
        "hash_atual": "fb9b1925063e17c11b2eb1ae77b43f1a61a136c956facbaa36109542d40d4492",
        "tamanho": 69556,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e6b2412d7fe710d47fa2ba393236b5a09c0ba8b001fa3148b75302f28695d4a4",
        "hash_atual": "d27994b7d1e58f9ceffd0586518d0a3628464d2ce15edc298de4b13eba9f9c4d",
        "tamanho": 64199,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0a6eb666866bd268c0dd4d74512bc4fb385ab03115d6029610e501dd6be05de8",
        "hash_atual": "93864c8f678872ce4dcc39e9a457ac570931063afc5ebb0ff68a90c63d1180c3",
        "tamanho": 21668,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "61843b8592031ae1aaa9416ffd1be33e88a20fc9ea1370452a8db3dd33047d77",
        "hash_atual": "144cf6fb12cac4e5104e51163da46c507fe314309e1758803b4a374527f81042",
        "tamanho": 39431,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0085f591b05d961b74a16fc749f65c26aef36323e3c3c36f6f2bce5b96a2916b",
        "hash_atual": "5fc9af7d35529c3aac4f7d4b8591c23017757b4254ecd074cdfe70a05729b74f",
        "tamanho": 9714,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "20856e768fb0eb8adb39c34458305429385633edd144e904e932cacc271cec6e",
        "hash_atual": "7bb20ea63cb18f990c4f99124bb907926db20d753e71258f0f42ee310f8acb30",
        "tamanho": 2116,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "74c7c7e29f3b0b7873f8195bf6c4d5f4f682b050a86357e3aa5fd1bfaea3debc",
        "hash_atual": "e6c0bf2128518b17acc7b5f24e2d80425a448b98e3f2e49f46fd53f5214ce511",
        "tamanho": 8438,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c016e470ad85527ba2d64ad968389ed64e5921b046f7ef5d5f13aaada8a73f97",
        "hash_atual": "0eeec5c2323552f2098aef40a433391dd5148a949859e4a912228ac901b6cbe7",
        "tamanho": 19647,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a0bcf7db546beb39a30e633fac989d7b2c2b84e446bec4de1dd935b0eb2aec32",
        "hash_atual": "f8651e0d39c31e29939a662db4a94db6fcd57da7140730eed408f0b7aa4f19d1",
        "tamanho": 18651,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "795bec101f8eb0f1721136f044ef484fc94cd503ea9a8afc3e3114a15008426b",
        "hash_atual": "d47ab823e1dcb6b5427def02a8c496631033670c30bb8c75b1a8039a455bd7ce",
        "tamanho": 44459,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "648052fb98bc472bb97d44b267c1ce10eee8febdbe7c28e6a66e4f0dcad17d24",
        "hash_atual": "f1b45e2f4a88eaa8e21e1fe9ef93314ad9238879bc23b9c4086996f93b41e021",
        "tamanho": 1169,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "343e0be5d1b775e77a3e716f4c86ccbe05292b40931bbdaabf66968efb11232a",
        "hash_atual": "e03de02f96b49f1555e0f4909fd7d42f7ecdf8e0396f281b5507a57435c163f9",
        "tamanho": 10124,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b16d74ab0ee9d13065b48cedd1769b832c9b618e00e731df882c780fdf5f4434",
        "hash_atual": "aa029442bd19b9942f78c207b610694f81d805c625c8bdf6a3174ce48ec08d64",
        "tamanho": 8297,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "619eb7c5b67bd9895300f05e8da5883cfd36d47086d238d08748ee2e3e246429",
        "hash_atual": "62a481c47618c5270fb2003e4fa44dfddacb51538ed948732981b2791ab6ba40",
        "tamanho": 8524,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "47cca5ca99f94bf42c0e2b7f1e1eac592573b5c898da4fb8923e8077e84276cc",
        "hash_atual": "ef1b14071b784efa20545a9e6bebd2b5f3df9c57fad22b97a6cc5408b5cf5a05",
        "tamanho": 29449,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a97290a5d475dbbda1c773edbb201169705b85ac2295eec28b96b444edef1f60",
        "hash_atual": "01a090c84c8ed6fdd6771b8f3123f140f3ac6bbc696787395d881f586db58049",
        "tamanho": 10070,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "467ff9044cafddec2c941aacda7193f6bf63bf7e802b928a4450dd8760f69705",
        "hash_atual": "5fe2f94825a31a41e9d904f7a1082a95242bd2669feb13a9bb5b0fe317e93b83",
        "tamanho": 6219,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3107d4465deea2cb11a99ad2b853ef0da0a024bc67fab1b03eb5412cf6bf7293",
        "hash_atual": "43e8e8557ddfe000ae0e099c888188c3728a55730a341ceff6db7cc3faef7137",
        "tamanho": 7208,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c8949a1ff3e87e554f2fc6ad234819fab7802676d1401988b7f1f397f287c6a1",
        "hash_atual": "8059986eba8da73b4e0921703497f5ad899b75e56ede0a53cd65d016d09c4f45",
        "tamanho": 9296,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e27918c0bc6a11917b901b4aa33c1ea1a2072e261a34966896f3bf12cdf1954f",
        "hash_atual": "06225f024c71589b8de981500cda982dff9f7ed0c65f7d7480d2eca5b898e74a",
        "tamanho": 20506,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b0847e9cf1a41e32b783c6d2ba27dcc415f42e30ee7d477d2cf3a087297ff2d7",
        "hash_atual": "db8a9d59f7c471e9e6c456650fa38d46efa5c950d4b7c7be3ad52d552f90f624",
        "tamanho": 59888,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7ec2ee2a744c990388b8efcc6cedac4b701f92759936c4d18fb3df6a51449826",
        "hash_atual": "8aae0944b10769cae6fc93c054407038e22468102793032649c77a217f2b0384",
        "tamanho": 28604,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c5076b364840bee2d688a2a7502ec0739ad9458687b7fd6a930ff461e82bb376",
        "hash_atual": "988656725be720bdfea51096a35b4022d97dc7d818f2b24350a34ccfd08ade66",
        "tamanho": 15343,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cae58547f4346eee9bc3ab9becb64d887f409f59f3d2685c7a5d1a6af71894ef",
        "hash_atual": "bcba508b11a74345ae05f038b0ad78c11f000ba999e7242f19c261f751253e81",
        "tamanho": 26051,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "31628c180d42e461a06687c5d3b87aaed5cc14e6bc1a92ccdefd0184a15faa0a",
        "hash_atual": "b4c199a77b68af11e2382f6988673eb79ad155440582ca6b0bbed2fb22ffc5e9",
        "tamanho": 25889,
        "modificado_em": "2025-09-23T13:57:24.000000Z"
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
            
            Log::info('PreservarMelhorias53Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias53Seeder - Erro', ['error' => $e->getMessage()]);
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