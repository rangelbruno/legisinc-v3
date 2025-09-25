<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias69Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-25 02:35:25
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "58080873b4f108be0554401212d508311b34acee3fdc458332e44ecfef583b6a",
        "hash_atual": "535bade96f0113270985010cd8c4f48e80d61d9084b68c35c42218072aec4a98",
        "tamanho": 199451,
        "modificado_em": "2025-09-24T21:21:54.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "250ef58e3925d761e150c4472a02490393a36b02fe057906581793789a875808",
        "hash_atual": "5b352bea46ed0d79c2e26b9cf14f14acd49e488f9f9a2c5a46dc104a166d1e79",
        "tamanho": 38821,
        "modificado_em": "2025-09-24T21:21:54.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "d40fc0c5e123d421c2529d989d51e10515974583c99e0f99b1e4714107176c81",
        "hash_atual": "abf470dc15c7338ef5de78fcd0a7e55ca91c885ab23abbfc2877a6e232d99f72",
        "tamanho": 190861,
        "modificado_em": "2025-09-24T21:21:54.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "6edea2d1d6944340644b9b7a21ab7a7bb8b4c0510b136d86a5e65380fc00994b",
        "hash_atual": "15b60e678d2c5770060c43e782b6f2dd5e18032d230546b7cd79207125075b1b",
        "tamanho": 37954,
        "modificado_em": "2025-09-24T21:21:54.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "e674f8ed1208775f5ca8bd0601208450afdc729ec26eb7d5f9648725d0537279",
        "hash_atual": "a75ef3be13cdad1044d6640bfc1284987be565d58f661341d5729fc15ba1094b",
        "tamanho": 16468,
        "modificado_em": "2025-09-24T21:21:54.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "bcc6c29309785a486c6a03c8ca4884bb34c5aaa0e7a403ea021352dcc70ce9ef",
        "hash_atual": "ecedb919cb95cd013e7cc45ba5459e3ff72953642758940ca7c0740ee9b7b0db",
        "tamanho": 19682,
        "modificado_em": "2025-09-24T21:21:54.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "100185e9edbb23284bf9a5af0402c2bce600e648d3df4eff939d5fae9a669b27",
        "hash_atual": "2420daae23b51bafb420dfa84145ce0b78d87ecbfc5132ccea83a10494c0a983",
        "tamanho": 11654,
        "modificado_em": "2025-09-24T21:21:54.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "dea1f43d86dc4f308baf53dbdf9d4835d605d8ad1f4037e0c59e960c5916ecdf",
        "hash_atual": "d2e5be6aaf906d422767a289b668fa63670825a2870e55319eb69bd72581646c",
        "tamanho": 90333,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5d59ec6e0d4f82097e197f321e08aeccb6e27dadc89b271d587ab05c35ea17fe",
        "hash_atual": "4a326d162141c4b3ec4079236d140fdae4fd0a3e32de439daaf6242b4f1bf108",
        "tamanho": 71172,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "52188ea2a68a56f1cb03b0ca31c1921c6a7727a5a8452312c2ecea09dd3d4ff2",
        "hash_atual": "d238463a1e10d82e95f9f0e9e3c609b478dc0571d04448ec2ef273c47509803f",
        "tamanho": 64199,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "69c8c36df0feaa4e6db846fa4e162e9c7702930d043239df7067a74fd9c2ccba",
        "hash_atual": "ea5451bd6b79a38f50ea39d89ffdbeed5db26864b7da9d8cebb591bb95599547",
        "tamanho": 21668,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9dad54e3810d67aad91f7dfb8891c83f16d1f026b57d8feaad3389ab2399425b",
        "hash_atual": "f5965ef0f9e1eeb6800ad414946c75aaa3fef1cb85b554b3848e23561c41cc6e",
        "tamanho": 39431,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9b03854cfb0518e4959a3a7998053ca8c825cc24edcc580a956e53cd29f458b8",
        "hash_atual": "93a54d035049cb945e61568d901b48f39d2988eac5df9def26dac2f5543ace7f",
        "tamanho": 9714,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a479b49876f41107bae526a2892d73f86bb08abc144f80dfc8152b960dd4c7ce",
        "hash_atual": "0654b028cd7f6f14b481ca1582b88db4f9b63846890a9f09f50368b98c266a67",
        "tamanho": 2116,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c075b25a7f49a59435170013119b97c3315dfd4a360fe7cf9fcc78ffd7191057",
        "hash_atual": "53cc3379a64ee3a7994d02ded25ef3bafeebe610b36f0c58147e56b3c67f3d41",
        "tamanho": 8438,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4cab3a97b41ab9aab1c299f41433f5798b4c0fa01bc45f04659000513b5f36ef",
        "hash_atual": "9c5724d0432a1826ff16b9e8ce7f913b5bee6764affe33fc8e6d25255bf11cbe",
        "tamanho": 19647,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d815b3f5370178f9495c0406cd8735beecf2e588e353f375bd7ec9894c97a405",
        "hash_atual": "ae5ee3d7ebbe423f34284a2d02fc3d972e4b82f7876fe3c3223e06592b27eaf4",
        "tamanho": 18651,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0af34222506803e9e57ffe87b26de94405f153c64a49fc1cf006fec257494c76",
        "hash_atual": "9a4ce2e16aa812a50d6612e5d43ac064f425906a6e6076c16af8e3475f570d7b",
        "tamanho": 44459,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5b01381936302cc3dd891f4744724d018fa13422d1419ca1ad01f972966d509d",
        "hash_atual": "fed63157309a949d983200fc7041f7e4572d91284212303926879569774bed8f",
        "tamanho": 1169,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "abe8b8e32d4555d8d16a658d5004b11aa81e09506a2facb80abb40bc4dcb11a5",
        "hash_atual": "f7961da97de2e03595f7f80dca898dbc0a002b5d6d77498bb9befcc9cfd4e671",
        "tamanho": 10124,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8268682ef1279415f9fdf41eccc7f08719ddb1b6926e12eeb792d4c04c126655",
        "hash_atual": "ec998aecb1d9e4fef4d6756f0b5e428729d5ed92f01e2ed46077b5aa633210c9",
        "tamanho": 8297,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "66c4318fcb78de6ac7961ea71aae810982d51a48d2de91ed84c7334899fc2dfb",
        "hash_atual": "cf3f36f22078fd4edc12c9ee8fad0cb1dbf97ff5bb7207a65e8f2ca28b172a39",
        "tamanho": 8524,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "140f77a07405d2d692af6ba484c1593cb527610b0ba03922c44d7eff178931b7",
        "hash_atual": "5456e367941eebfffe9c23e0f4232ff5d450416f51e5874330f521c188ce3c72",
        "tamanho": 29449,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f08a6cc87f1b7783ac30d125f4863ad5af4999f7b268ccb3134eca2fee835045",
        "hash_atual": "a8ed41b5e3f152d9216d0bbcbdf6742cf14cb37b0ed642864e5f4ee6f7f89939",
        "tamanho": 10070,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f8e1e78ba47a2af3553e91a5e1f8d91d3ba593f233925f5c92614c1e70cd0fa7",
        "hash_atual": "044cfef915f306f7ae81f7e939515c252785b80802a848c85dfb19400067ae73",
        "tamanho": 6219,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4a32c6048ca9ed72370a95c1af664d58cad97163aea072dabff516026e6186fe",
        "hash_atual": "cf3f9edb10692b5afbc768d07609c3bf9389c09032058fbcc1955faf67a48357",
        "tamanho": 7208,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a366f29935463b4c728527ea6252d1d8ae9f5135d331ab37296a2ba8248240b6",
        "hash_atual": "2aca7a8d8c7ac68b7a4f2e7f950b8512f69d8031b402d4885aa554df565b5e62",
        "tamanho": 9296,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "39630ed4094d897769260797d36ba612ecd88f94d39d84783cc556b363df7b6b",
        "hash_atual": "8dbcb42e638822da7004cefd9f8ff701c5af5572f3ddb3626f60a9392f23df7b",
        "tamanho": 20506,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "96cb3428baae17ec71303ea0c67b14579cc57af4320505fa74f4f9e8fc4ccd9c",
        "hash_atual": "9cb364a56564208ae56b1d68c9f8fb18ea92f76d3c4f1e2c8e76d065ad507d90",
        "tamanho": 59888,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a73d4b7a1f625590423ef77ab9f622e9b829c03f4864a57c7ff5b71665908530",
        "hash_atual": "947400c3a1689bd837204b1740874f6e687caef45c9282a2fbe6e05998d0f8a3",
        "tamanho": 28604,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "38014a36694d55bf55cdad4d6a8625614569f3e50855222700389fff1a2d981f",
        "hash_atual": "6ef3e81caa260a09e79e02081cf26cca3d32257f3598313d45b3a719686fdbcb",
        "tamanho": 15343,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d38d669a2c3af8bf45486e979657b63120cb29d10f843b4089345fa2314ec3b1",
        "hash_atual": "0033538ef6068f7d707893ae251fce2c51a50f298199de007e97de566c21637e",
        "tamanho": 26051,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "bd91ff7d4a6c12507766e8ae4ac773e7da875f1dd75214d8bb3b90d845ba5ae5",
        "hash_atual": "562e99f6e016afe83edf20cd9b93d0751e6ff2c94f0fa80a9e4123a3d6af2f55",
        "tamanho": 25889,
        "modificado_em": "2025-09-24T21:21:51.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/verificacao\/assinatura.blade.php",
        "tipo": "novo",
        "hash_anterior": null,
        "hash_atual": "d5ab675d1566d04cd342da32321e1a1f9db265f22a0448de93edc8f8915cfbeb",
        "tamanho": 14979,
        "modificado_em": "2025-09-25T00:26:45.000000Z"
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
            
            Log::info('PreservarMelhorias69Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias69Seeder - Erro', ['error' => $e->getMessage()]);
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
            'resources/views/proposicoes/protocolo/protocolos-hoje.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_protocolo_protocolos-hoje.blade.php',
            'resources/views/proposicoes/verificacao/assinatura.blade.php' => '/var/www/html/storage/app/melhorias-backup/resources_views_proposicoes_verificacao_assinatura.blade.php'
        ];
    }
}