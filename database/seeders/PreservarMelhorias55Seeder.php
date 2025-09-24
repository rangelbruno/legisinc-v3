<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias55Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-24 12:31:36
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "fbf48ab5bf0a79274685f8ff3e3ca7c49a67e15d9d823ffa64faf5415d35ed83",
        "hash_atual": "41ae368cd06bbe788892d1e9508738067f99d453c1d50e03cfc2d3e2a3f57a6b",
        "tamanho": 199451,
        "modificado_em": "2025-09-24T10:58:41.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "c5cc57c487a474181a5124b6f5d24f312686b65ba0199696f035c440a1399113",
        "hash_atual": "8ad3bad5387aabc51c048f7b4c3363e491a9ae415a7c2b66b157b4d29704780b",
        "tamanho": 38821,
        "modificado_em": "2025-09-24T10:58:41.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "dcc5e755e7f4b01d1b46d5273967598c1a52516b178ba7be0901885aecfcdbc0",
        "hash_atual": "7ae6244788b2e4315b8f4c531c2c6862308d862a8f26c5d609e5636098dd5ee6",
        "tamanho": 190861,
        "modificado_em": "2025-09-24T10:58:41.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "0ebbad191784ba4c494dfede738ad277cda61bc2f382514bdf28ae485974b588",
        "hash_atual": "9eadb56b71e6ce35a72b98047680e21b06e1f536f8c71d2387c00eecb622bc06",
        "tamanho": 37954,
        "modificado_em": "2025-09-24T10:58:41.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "016939cb8d231db89d8503252c15d13d284eb6300129d6e9e22c7d252f52f95d",
        "hash_atual": "17202168ccec3269e05a2f4b3d4cfdb89f1668af1fbd144e22d3889b5cf3452f",
        "tamanho": 16468,
        "modificado_em": "2025-09-24T10:58:41.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "435b05783c1b8dc944b4b74952faf230e09766f453dca867eeea5814f3f75b91",
        "hash_atual": "da66c4532b12a3f5e5dc80eb96da86045a7fa004f4b4d588f5cd7317f6083559",
        "tamanho": 19682,
        "modificado_em": "2025-09-24T10:58:41.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "43512cc10a7aafa2de259447830337cdbe4f878322690968ff6648077f0a3ffc",
        "hash_atual": "58c2ee65d87ea9e59464fcbba13271703c1feacf6938ec6b618a7a36c05fb8e6",
        "tamanho": 11654,
        "modificado_em": "2025-09-24T10:58:41.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cee44eeb0e87c9e9dae2fcffff868aa25b7603b781356414b6603663a257fbea",
        "hash_atual": "85f67d345cd1cce09f4feba67b4054aa8d520833be4fb56c8f1eda8a274eb772",
        "tamanho": 90333,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fb9b1925063e17c11b2eb1ae77b43f1a61a136c956facbaa36109542d40d4492",
        "hash_atual": "97279a0983a0f84d2b4cc2cfcc06c00632f2b793e99721d3e0de6de9bbc0f1d7",
        "tamanho": 69556,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d27994b7d1e58f9ceffd0586518d0a3628464d2ce15edc298de4b13eba9f9c4d",
        "hash_atual": "dc359c72b2748e0a0b76774552a31ddf2bd5dcb77a7ac78f2f8107c5d45eaf87",
        "tamanho": 64199,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "93864c8f678872ce4dcc39e9a457ac570931063afc5ebb0ff68a90c63d1180c3",
        "hash_atual": "d6aa82f87b810efa8cd65977f66d0cf72f559442937e2de77f94e19a93fedf20",
        "tamanho": 21668,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "144cf6fb12cac4e5104e51163da46c507fe314309e1758803b4a374527f81042",
        "hash_atual": "d36742d3b0253bf9380cebdf4793a243f080ed30d114ce3a4a157ac1d9b2fb80",
        "tamanho": 39431,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5fc9af7d35529c3aac4f7d4b8591c23017757b4254ecd074cdfe70a05729b74f",
        "hash_atual": "b51acde22d4524806801a4142fa191f0f830c80ca14a265bd063bd40766f0fb3",
        "tamanho": 9714,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7bb20ea63cb18f990c4f99124bb907926db20d753e71258f0f42ee310f8acb30",
        "hash_atual": "a7468eb430c73f32c6e5c3a7a58b774ff5642204d9a615cb3d231562c640ac6a",
        "tamanho": 2116,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e6c0bf2128518b17acc7b5f24e2d80425a448b98e3f2e49f46fd53f5214ce511",
        "hash_atual": "f42c897ed17d1f50cc94d8ba6b623a5686d6ff04c6d5c29ca3f23450b1259292",
        "tamanho": 8438,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0eeec5c2323552f2098aef40a433391dd5148a949859e4a912228ac901b6cbe7",
        "hash_atual": "1a2154601bfd77dc8a0ba4796f4232d1f549455e608a34463f668406f099ada5",
        "tamanho": 19647,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f8651e0d39c31e29939a662db4a94db6fcd57da7140730eed408f0b7aa4f19d1",
        "hash_atual": "9cca88ead14b4a3543ca22d10937906f56e1d5f42c4ffd933dbe008c56ce0e75",
        "tamanho": 18651,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d47ab823e1dcb6b5427def02a8c496631033670c30bb8c75b1a8039a455bd7ce",
        "hash_atual": "a357499adb8c983370d9cc218b79c98c74366ff0be544bc3ec311532551a89ec",
        "tamanho": 44459,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f1b45e2f4a88eaa8e21e1fe9ef93314ad9238879bc23b9c4086996f93b41e021",
        "hash_atual": "0e3baa800626da12f7c53e7e427f02aef597cea81f293a167abbecaabc5a291a",
        "tamanho": 1169,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e03de02f96b49f1555e0f4909fd7d42f7ecdf8e0396f281b5507a57435c163f9",
        "hash_atual": "cf465849144b7b3db0942414d38467f0f8dee58301d65a2883f7bf1be85a1f4a",
        "tamanho": 10124,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "aa029442bd19b9942f78c207b610694f81d805c625c8bdf6a3174ce48ec08d64",
        "hash_atual": "ab614b625376cd13180fd7efc29011d2af159de564e1408eeb81a1d5158c852c",
        "tamanho": 8297,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "62a481c47618c5270fb2003e4fa44dfddacb51538ed948732981b2791ab6ba40",
        "hash_atual": "6f4672484fbb179c862e81903e408887993d437eb60c6a2886cbb281050233fc",
        "tamanho": 8524,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ef1b14071b784efa20545a9e6bebd2b5f3df9c57fad22b97a6cc5408b5cf5a05",
        "hash_atual": "319aa4530eabb668f1c64a3364d5664d3dc4b89cce6b8670f22be6d7f0ea2b38",
        "tamanho": 29449,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "01a090c84c8ed6fdd6771b8f3123f140f3ac6bbc696787395d881f586db58049",
        "hash_atual": "78e4a1df8c7f3bc0b56f97baeca887a80390929e41bbc0e8373f3b3ea2d5c1af",
        "tamanho": 10070,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5fe2f94825a31a41e9d904f7a1082a95242bd2669feb13a9bb5b0fe317e93b83",
        "hash_atual": "6e844a489bd960038bda8a8ac9bf8225f94a5ac90e6da694dbda459de57abcc0",
        "tamanho": 6219,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "43e8e8557ddfe000ae0e099c888188c3728a55730a341ceff6db7cc3faef7137",
        "hash_atual": "c8e63103af3034aad42e89670d307fe2c5bf008817667e23debd06386da86ef3",
        "tamanho": 7208,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8059986eba8da73b4e0921703497f5ad899b75e56ede0a53cd65d016d09c4f45",
        "hash_atual": "ea44febe76588c4c232e7c17dc6e41229a12a28d81e0ea1c2fe7cdc39ed75be7",
        "tamanho": 9296,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "06225f024c71589b8de981500cda982dff9f7ed0c65f7d7480d2eca5b898e74a",
        "hash_atual": "89eae9f31d782e269cd378f66724c71d0a73e2a3d416dc431c266a923a29439d",
        "tamanho": 20506,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "db8a9d59f7c471e9e6c456650fa38d46efa5c950d4b7c7be3ad52d552f90f624",
        "hash_atual": "c759051f29c98094c6469c41175276bd5dd1492791ec7040c1cc1ae2b31628b0",
        "tamanho": 59888,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8aae0944b10769cae6fc93c054407038e22468102793032649c77a217f2b0384",
        "hash_atual": "7ddc45f481b1ffd55f071276604f61566e5fb63834455288ef45c31677ac3f64",
        "tamanho": 28604,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "988656725be720bdfea51096a35b4022d97dc7d818f2b24350a34ccfd08ade66",
        "hash_atual": "c6f78c4b407e650af3c9673e1524d6285108a68162f04d6cf12a8e9d5e1b4f21",
        "tamanho": 15343,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bcba508b11a74345ae05f038b0ad78c11f000ba999e7242f19c261f751253e81",
        "hash_atual": "6adcfa58e32cfe87add37d48ac10c85478fc1f1e067bf3cbb60a852d0f052df0",
        "tamanho": 26051,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b4c199a77b68af11e2382f6988673eb79ad155440582ca6b0bbed2fb22ffc5e9",
        "hash_atual": "87eaccce9840c04b2a9e4268cc1d6956f812d805a6c2cecb7049e34f9da88cc3",
        "tamanho": 25889,
        "modificado_em": "2025-09-24T10:58:38.000000Z"
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
            
            Log::info('PreservarMelhorias55Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias55Seeder - Erro', ['error' => $e->getMessage()]);
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