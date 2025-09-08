<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias29Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-07 20:42:33
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "e04acaca63521f47cac102229053a00d03fa41a7b731fc051c49d80cd2d33942",
        "hash_atual": "5b8a45666dd1dd1d9be91520f5fd8e2d829e34c0c2ff75a67f023fc0b0be1d14",
        "tamanho": 183240,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "dc3d660d27920e6485f2ec33d0bac7156d4010dbb30bd11f70a9b5591292ceb9",
        "hash_atual": "280c8a064459ec717ca1f409c0a4b0bc3e2d39d87e88a956e778748432785c47",
        "tamanho": 33855,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "2325ff9155aac3f50585c8818b7bb705017ebb50a48379288a4172ecaee4977c",
        "hash_atual": "0db1b0e51c8f8dc57644c159c96dca2c7e2a850a3429499fc6213dbaa5b115fc",
        "tamanho": 184884,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "72e117f1a84bb0ce3e345b363a86cd21c1399fb00ae20079b8d133606a0735d5",
        "hash_atual": "b3bb8d4b69a8ee5c0bd7366ef303c3724844e1f25a76f4a08184b4120fb07883",
        "tamanho": 37954,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "646b72658d3a661d8353842836cdf47b1cff64224af4c7cd3d2e1c0526247299",
        "hash_atual": "e2476a801109b6e37ffe5d0e89ff2f8c9d2bc6545cc697e7760827fee4944778",
        "tamanho": 16468,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "257ae8243b00192707b106f976f93cd8f34d46594a53df225d30e422208746a4",
        "hash_atual": "6d415a88f30fed4c113b5473bd5df4b451397c2d2f778c9f797736cea8cb27ae",
        "tamanho": 16728,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "6d52ff91c71dceb00bf3b897eb0dc7c646f8ce986ffc476f42cee6a226051301",
        "hash_atual": "51322a7fedbc38bfdce2b26eb6dfea1ea44d07ce1462feb35e60b349aa4be9a3",
        "tamanho": 11594,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d446afd2eadbed800d41ea9e6b7ab42ea08ff25b135f6c9a28ee9668f3037ed8",
        "hash_atual": "9abb376a3b4437fa0d712aedf4f79a3537307d5102051edc7f4282c7fec33afe",
        "tamanho": 90333,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "27d23941b83d525ed859f0e5bc0c4c31dd1152ec2641316977915ceb9924145b",
        "hash_atual": "baa63941a98c860435e808680c52c6b2d28947df588f6176a1393b53bd778e92",
        "tamanho": 49890,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ec63fb61d44b84ca89b89b6fcf2c0cf7ca8a4adc14c15a8c4f3eef619b66298d",
        "hash_atual": "049c4a67b48e4e54aab09b5bc455ccf0c9078e00b6d292e80e10c0d01b79d3ea",
        "tamanho": 64199,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5e8648b9bf87563775d1172ba755df26c767568953b6d80f81f99959e1b05cd2",
        "hash_atual": "983ee95be66571e0fa9442d26d615ba6ec70614328de702557b418d4be255c6b",
        "tamanho": 21668,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0bbe2d5854ef2f8bb1db77a42471385900b48413c3911565ea3c1bcf555398d3",
        "hash_atual": "f9ae5ea38beadfece3b291286df6797da1e262ee45c7a271da36ed79a99b0809",
        "tamanho": 39431,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "90d7f6682d4c0e84070ffd71d96b8eae12dd3801b87e4e801a3d57ef6544b8bf",
        "hash_atual": "fda72a6c7655a916d8c89b979e1dc4299e7f7ba68662078b17b828ddf6c63a22",
        "tamanho": 9714,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "95f5224d8fcdf915b34bf985040b829969e153394365bd7c20f8ae48abd93527",
        "hash_atual": "521270e9f5de5bf6e31d1760777242649e63d4b4b3ca9ca5fa7ee5634e7ccee6",
        "tamanho": 2116,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f8037918965a0c8dab2d95f16097ca7220a19b88701b0805931313a4797db02a",
        "hash_atual": "211d72053731e6dd8fc4d69e058adad27017321bdfb15bc39b408bb55ee21125",
        "tamanho": 8438,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4b0537bae9736e11098e35c2389081a712984c7f65b8b368d96d7cb51d6fdf8b",
        "hash_atual": "2b9e45417bf8e0b123b70a7514c5f5d9d690479fcb734bf04ac04a4d3f5df116",
        "tamanho": 19647,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cf41881273099be51b4688c66539dddaab6673d3a2d1938fce5c230c9743a64f",
        "hash_atual": "f40eccbd29e640d7a83d884c6411f79f590fe633de2b366c986dd784d0dcaeec",
        "tamanho": 18651,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d359c410b00f72e8c4d3ac47637c9d7f08685ee83aeb40420ad09f6d0610843f",
        "hash_atual": "ee6ee37a93f234c18f1997d7d16c2ab96f97c046d80c435c7d9df1bbfe362e6a",
        "tamanho": 44459,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "db4b4c90b82ac361345cb0a85eda99548b05ec7fcc3eadbe7351232cea31720e",
        "hash_atual": "9a33c56a526686cc99a98b02c990f144b97cc4f61dc8a2af5a3424b6a9212cbb",
        "tamanho": 1169,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fe341a1cef872dd394be5341af055bc181a89db9b0ed717f870515967e495867",
        "hash_atual": "cb7eba641d8cdbf1db174319e242616b8f2ee7536d00a159ffe2cb85b77a56ba",
        "tamanho": 10124,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "264436879d6779451646168eff29abd94d43b3be9e4c9fb2cd91841163f49c70",
        "hash_atual": "295efdfaa3a82b812e37c71079e93c91d75fc48afba75a5ef68b537eac2e0f81",
        "tamanho": 8297,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4d159afa5af21dac0ee16114b1971bb5bd8b2a4b6da6cfa0bb11fc7fb1a2ecf8",
        "hash_atual": "80366daeb22ef2a49c1d3f13ebd564d3c1503a0388a0c65a6e1931ee00ef1718",
        "tamanho": 8524,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1cb4ec5e45530bb8c6aa5e103ecd557e500a186cf69f8d7bc564dd27f04c77ed",
        "hash_atual": "fdf5ee78ca17c5f4a8a07e66c95567bf544921f40308e218c64c617e8ac4e5d3",
        "tamanho": 29449,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "23cd3bd13140ba02f8d81c18737b0f2900a5d2e0dfeb1588fb77fbd96dad3b2b",
        "hash_atual": "00529ae0d301030d827b6eb3b3f63096bf74a01f7906d1c0fdbf8912f91fa032",
        "tamanho": 10070,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b7b60111494df0d77f35009f091a0430c12501a2126ccc920252851812450999",
        "hash_atual": "87efae821ac209b9e36064283f2ce17d7c7b5c22992b48b484589e2a0f8a211c",
        "tamanho": 6219,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e9e771c073d0cfaedc65d3a977a5c0d62e8a8d00a9e85a27e565232df97b72a4",
        "hash_atual": "7afd04de6dca231f7ea0db8938603a34ae4c8995bb1088b09a0a97821caf5e35",
        "tamanho": 7208,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3784564e5927cc85fc6b4c1ac570b29971ecd0e413130b513e39680dec0746f0",
        "hash_atual": "76cc9e6875da5131eaa094ae9367d8ee201baa880ea67e069f24196416b89302",
        "tamanho": 9296,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "81120eb1790599032fde8d580543d20017618dd100102558bacff311a8afa77d",
        "hash_atual": "db0bdc43e335258834ded920e14ac25277b0d4c4d203961a6348e9cb7850b8ac",
        "tamanho": 20506,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "653474b17ef2614588ebef81f806ffa9c94c4845ad5c4edd3eaeed4546c44177",
        "hash_atual": "fa3f2de8d57615266399fc09a1c70a803e463338d063018692a45b92182fc5af",
        "tamanho": 59888,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f23e14d247ab36a7d2d9f3cec4d27ca74bc23517c8ec9583e104002ea0b09c6f",
        "hash_atual": "6d69004f671c7ede18c0f11cd40238455092ab3518914a779aa9ae8262ca2f63",
        "tamanho": 28604,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3d63165ac916708fe335e14f9377bbb83e4bb231e40a5d21e8822be681fc7a1b",
        "hash_atual": "679bb5cba9c7fa194384b1bb4d2a031593e10b8b3e3fadb1441f8d9557745886",
        "tamanho": 15343,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "680db829b9bdeefaae087168feb56993c32ba4df2d816637cb621ec6a8d683d7",
        "hash_atual": "140c477264e448f31b14fd5d9b53cccc1fb56693dd99b3263fa04481f89a6542",
        "tamanho": 26051,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f72e2d6f64d813dbee3b6b88a970b8ad94d6f9c6e919e5b72b7b33f0b41f2b65",
        "hash_atual": "a5d73f2865f4973ccc4dfa15b72d09638d46db88a3555df9e89b4b52566bc6a7",
        "tamanho": 25889,
        "modificado_em": "2025-09-07T20:41:54.000000Z"
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
            
            Log::info('PreservarMelhorias29Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias29Seeder - Erro', ['error' => $e->getMessage()]);
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