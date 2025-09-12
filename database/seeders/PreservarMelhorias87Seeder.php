<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias87Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-12 01:19:57
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "17b84c60e394307831468e6193fdc68df86ade76149e40901c6fa1261ecd8322",
        "hash_atual": "4cbeead9da82617e179459aa067b8f0fe80bb48b11e2d5cb54fff601cb5fbac2",
        "tamanho": 194828,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "743c45b28b95fe1f2e28d84fb0f2be18ef48ccb141017ea3f8ff89d4e4fc519e",
        "hash_atual": "da9df0e367a8d5bce199bd3181a6f4e32390b4221cb8277c92cd4f5e065cbeb9",
        "tamanho": 38821,
        "modificado_em": "2025-09-12T00:56:12.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "fabe7ee44e156e931f8a43174ce173c6032b132ca428085ee2dbefa58840164f",
        "hash_atual": "6a6cd5bab03f5a694ad096c1c1b4fe325bf0d4c9a0bc47963aab39180f8c06b9",
        "tamanho": 184884,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "c8306acf070f544504b55b24870bfb56b0b9d0003ebd992c4173c77c6a0d8dd6",
        "hash_atual": "6a823abbad90f90d36eef4d8b11cad92c309c9125d18636715732651ff8feb17",
        "tamanho": 37954,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "0ad24c501d88e712282787b56d8606b0cd4705437b121ef514ac1e9a5551c553",
        "hash_atual": "df98c6e024a42e6742398427806fa877c452f22524d2eb229e7401916bca4272",
        "tamanho": 16468,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "8f63d76b5749813bb78ade84282412eae120e37fcd7e7a911acb4a66337e0dbe",
        "hash_atual": "dcc969738e648ee865de12c8f88aae5e197d6e08ae17e9f3c4093cde6b649a04",
        "tamanho": 18417,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "8ded29185094dffc891358528a4313e63d838e3cadb84beec2ac53e5d2638b29",
        "hash_atual": "3941b07fa1d40502b7b2ab705cca9194871ce5e92144c6c29f74a9f29e07d413",
        "tamanho": 11594,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4ebe999a68cc41c4ab60e91e14b885ade933d1c40c9e9afe2334d228869ec834",
        "hash_atual": "639940bb5eb10c7dd7c166d05021667ac94cae6fd4b989709f9ae93a1e613d6b",
        "tamanho": 90333,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "414aeaa9071f10aca277406ab1a6170a5632eb60a31fcd18ced30561410f5a0d",
        "hash_atual": "fbedafebd2ce8757484618a473219fedc38bb3bdecce18a02d07c247c1a968bb",
        "tamanho": 69556,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9a48cfe6b59904856c2666a8591da0f24f0214e74b72a0d4ee568dcbe969df8e",
        "hash_atual": "4cd27c4b609634657c5003a3edc49c314c0a548a6aad868b15fd8d33fdf9d90c",
        "tamanho": 64199,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "aa3ad4292172d8027dc07c51b2ccc3318d20d45bdc9fe325c37fba73adb9376f",
        "hash_atual": "1f613a5141ba553a757e1f315a55caa714170c9300355c8e86ae0c38e6230b90",
        "tamanho": 21668,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3a4ad0471afd63b2fb87f54136783878c34840caf6bcaee13a7417d0050f9212",
        "hash_atual": "73f2133d8214c90851e52a6790a3b007053d2869ebfbf609856736a670cb1bce",
        "tamanho": 39431,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f54508c50b5e0c7190bbdcf3738323ace49e2d57ba55dff5a0f8328a62c2689e",
        "hash_atual": "80998b87dd8f4accc52f91b170b8dd6488981018278204b0d5f6e295abe5d377",
        "tamanho": 9714,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "36424bf1e3e5afbee8d641a531f92c77c575745672d9413ce4e8db940150e5a6",
        "hash_atual": "cf08c2e3ed6b6d7efaa723f174936bb2460215ff30e354e504c3f62249fd413d",
        "tamanho": 2116,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "eadde6baa50504ae3716f13aa902b832cf4c62dd49aa6233be96ac9ea4e8a162",
        "hash_atual": "fc523585caaadb8222fc3b32b4e0dd47a91b5465f6c8d61c4dd1cad1ade2f7ad",
        "tamanho": 8438,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "61341e83297329751bbb41a5a3d7e517573163456b0da961f6dfb50f4e6bf127",
        "hash_atual": "bc4c7436d723a1609e0a93761a35272745b80f2ec0725126019b43d235c299da",
        "tamanho": 19647,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8e93cf7d6deee051d748fb4e07572b5c992af13a1b0dad3d7c37d874f7d1d1dc",
        "hash_atual": "6351e9fbdecbf5742b2b49220f12d8b00a0add28957d36845613b2ea2427ce69",
        "tamanho": 18651,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2f06ced38319e5e32566a73af8aab9616615881394e7ce5bb3d38afdf3d48c89",
        "hash_atual": "364f24efcfb3b79071b51fbff7e48d5f7bc23e59970cf95e76caebd181c1c6c3",
        "tamanho": 44459,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "64a321dd8e6e1472a34e0a092cd10071d97654deff5cf60fe5110ea61593138d",
        "hash_atual": "f2a8cae112c674a3bbde6d19e6f562229395fa85dc56d961a0365d67942ef4a2",
        "tamanho": 1169,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "806c4ebcc5577d90a278cb3dc14655ab2a619eb439f4c857454ca4e163202ef3",
        "hash_atual": "9d9ec5fb9b0e90902bbb9c1899e8848ade11db4e06838b2f09850d4f7bce2b44",
        "tamanho": 10124,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4188b638dd1b687533f424ac290d5ecdac0b0cdd4e5bfcc0d19716c7ba6491f7",
        "hash_atual": "c6dbbbe79df1c067dbad42846464a5855e8aac4e84bde0fb8a0accb67546f2ac",
        "tamanho": 8297,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f19cbf5cb292f3873648d708b1d6537e20124ca6bc04480e216956c0a1568715",
        "hash_atual": "5af02029dbf992b29011ad13636891cdb9c259de7f6cd291c94689e1f05a2dca",
        "tamanho": 8524,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "46063c7b32078e0685838aed2367ebad1dc79f7cd2e4170cd0aaa51de418948f",
        "hash_atual": "15c7bf354d9616adadef5d9ef05ebdb290881777c2d40637114f5a7a1928bb79",
        "tamanho": 29449,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4445723a890b49453f856da7f588c7a191a43ee769b88c7ed4f3a9427dcbb8e5",
        "hash_atual": "6296b94d62ab795978ac71596c0f8ab5785074324f0ed4ca6ffe1e9817f99c69",
        "tamanho": 10070,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "08a359e259512a725bb4878d6100e5398e9322b0c942c7fd57d7edcab56a428b",
        "hash_atual": "5b53d9410474ef668bb9579034e90a2d05315c0708807c47b85dc2587f32ba0f",
        "tamanho": 6219,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "582449c6c9a0d54b6d3c46c175049e858a0806550bb44adbb019285226997f5a",
        "hash_atual": "bd0f83822f23c5a38e2d06f2abcb53a19d56c85aed58f1100a60564061544342",
        "tamanho": 7208,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ea5369a6db86d364239fa85d4f0b1720d6d7341cd0e567411695a571d64eb4c0",
        "hash_atual": "9300eab29cce46c11fc8f9620e8c8c30b8bc0cea07e109f04ed9475eeddb2f7b",
        "tamanho": 9296,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c86dc8d297ad17ae28c5b7a8d19c9724bd99759660fb9e8e409414b8887456a9",
        "hash_atual": "bcf86ece5e6c3a60191152344c57ae97bb72371ed1748b0be426a0167e5548a8",
        "tamanho": 20506,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8f120a2fea4e77e11def3bba40e2adb43f9e5a9d24045ee439f6829c83da9034",
        "hash_atual": "e017804d220f352c749b8bdc8456d5e7eec4edad0e42698c4a53178593533dd1",
        "tamanho": 59888,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6d660c6e01c3a593c1e0042d8443264e3bceb472218d239238deba28ef3e033d",
        "hash_atual": "672ae21f6102549ca668c554b102fb83fa29b742ba5ed2f1f0e123e3444a68f5",
        "tamanho": 28604,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ce716418f77e2eb05ee3556f041d8c6d308ee595159247ff8264376c59561e47",
        "hash_atual": "d82eac471400aa7b97f75c4ed3cde1475f12762e6e226b2cf43f914f0630b99d",
        "tamanho": 15343,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3b3b1c3b670b108132ed77641827379e9577f46aeeab5940011eab97af4dfe0a",
        "hash_atual": "83cec2eecec3d56e0c402bff8510dbb67168675a62ffa943d008cf4c39294ca1",
        "tamanho": 26051,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "741e5ed6561073d8a68ab24dfc98029c1bb175b088bbb484d5f993e171906edb",
        "hash_atual": "602c006e5b1080a740da16a5e4aa7f0934f041a2768d458a1158ca15f62da70d",
        "tamanho": 25889,
        "modificado_em": "2025-09-12T00:44:30.000000Z"
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
            
            Log::info('PreservarMelhorias87Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias87Seeder - Erro', ['error' => $e->getMessage()]);
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