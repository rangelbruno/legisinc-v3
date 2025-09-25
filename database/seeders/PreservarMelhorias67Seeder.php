<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias67Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-24 21:21:39
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "9e51283dc73d8c87d6805e56916c341458a070fd7f8858b5a24a40f75ecbfa3b",
        "hash_atual": "58080873b4f108be0554401212d508311b34acee3fdc458332e44ecfef583b6a",
        "tamanho": 199451,
        "modificado_em": "2025-09-24T20:57:24.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "27776ae90902d4f04d03e3206aa559513a8c6b8acef311a158a5bf6176fd7467",
        "hash_atual": "250ef58e3925d761e150c4472a02490393a36b02fe057906581793789a875808",
        "tamanho": 38821,
        "modificado_em": "2025-09-24T20:57:24.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "71e32d836c87aa98403806fffea71c78c4a7a130595b502eed6e613cd887a961",
        "hash_atual": "d40fc0c5e123d421c2529d989d51e10515974583c99e0f99b1e4714107176c81",
        "tamanho": 190861,
        "modificado_em": "2025-09-24T20:57:24.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "c65a65b4e752aff5bbe9bbea7370ed7481aaf651d9bd8a9c3e1671e6da5cac16",
        "hash_atual": "6edea2d1d6944340644b9b7a21ab7a7bb8b4c0510b136d86a5e65380fc00994b",
        "tamanho": 37954,
        "modificado_em": "2025-09-24T20:57:24.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "484561a4de3ff6c69dfa1d0e268d31137c64b582026d2a565892d55be631de21",
        "hash_atual": "e674f8ed1208775f5ca8bd0601208450afdc729ec26eb7d5f9648725d0537279",
        "tamanho": 16468,
        "modificado_em": "2025-09-24T20:57:24.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "8f9840284013151e19a1ab05a416ad5103d15be735abbc838b3878a52fd2cb3e",
        "hash_atual": "bcc6c29309785a486c6a03c8ca4884bb34c5aaa0e7a403ea021352dcc70ce9ef",
        "tamanho": 19682,
        "modificado_em": "2025-09-24T20:57:24.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "573299f6cb4e7fc76bb02f10907983bb1617710862c953bbe45dd8536a51adde",
        "hash_atual": "100185e9edbb23284bf9a5af0402c2bce600e648d3df4eff939d5fae9a669b27",
        "tamanho": 11654,
        "modificado_em": "2025-09-24T20:57:24.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "81350284f8614dab63abb6467f3a98c2fe838a50bd5cfdb4672e30e7f7c6eb70",
        "hash_atual": "dea1f43d86dc4f308baf53dbdf9d4835d605d8ad1f4037e0c59e960c5916ecdf",
        "tamanho": 90333,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7cca1a38502f448bf50613492ea83738a881fe99866f348812c87e7cd701aa76",
        "hash_atual": "5d59ec6e0d4f82097e197f321e08aeccb6e27dadc89b271d587ab05c35ea17fe",
        "tamanho": 71172,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "70702987ecd60c4911034f8f84e7edc7609dbbb714b0f6cfb3737eb8d6210dc6",
        "hash_atual": "52188ea2a68a56f1cb03b0ca31c1921c6a7727a5a8452312c2ecea09dd3d4ff2",
        "tamanho": 64199,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8200ac2131438d608e0c003f389b802e296423b7a2de26a3ba75037a5df93130",
        "hash_atual": "69c8c36df0feaa4e6db846fa4e162e9c7702930d043239df7067a74fd9c2ccba",
        "tamanho": 21668,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "69d4e32d5787452a32ef0631757c6c271b56f8850d5e81afa06a10d1db29cc8e",
        "hash_atual": "9dad54e3810d67aad91f7dfb8891c83f16d1f026b57d8feaad3389ab2399425b",
        "tamanho": 39431,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5634d3ca02a85d14a17ab077a8d2d2ac34e37bc3781b3e1ba713cd603eb8de2e",
        "hash_atual": "9b03854cfb0518e4959a3a7998053ca8c825cc24edcc580a956e53cd29f458b8",
        "tamanho": 9714,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "23ef78ff396aa1ccb408c53d772763b37180ebc3a1bae1176ae732d7facc9222",
        "hash_atual": "a479b49876f41107bae526a2892d73f86bb08abc144f80dfc8152b960dd4c7ce",
        "tamanho": 2116,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b07c266ffda730410eeda42b17018e8504fd235e46f3c67f8a4e2365ca9836bb",
        "hash_atual": "c075b25a7f49a59435170013119b97c3315dfd4a360fe7cf9fcc78ffd7191057",
        "tamanho": 8438,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "58116778451f21af22686abf050baf18b23f5d6528409f214e15ba64aaac8b50",
        "hash_atual": "4cab3a97b41ab9aab1c299f41433f5798b4c0fa01bc45f04659000513b5f36ef",
        "tamanho": 19647,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "594923a2644cf45ec3891febe6eb718d0d816452259e6985717929f4666073ef",
        "hash_atual": "d815b3f5370178f9495c0406cd8735beecf2e588e353f375bd7ec9894c97a405",
        "tamanho": 18651,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a168cc429d434ae4cdb6fd22bdeb60116db429baf97e917595babd7af02ed5f5",
        "hash_atual": "0af34222506803e9e57ffe87b26de94405f153c64a49fc1cf006fec257494c76",
        "tamanho": 44459,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7dd967f1e85c75c5d0569b4584c7f31f85cdc8dafba8d2f29a2511860d10a7c5",
        "hash_atual": "5b01381936302cc3dd891f4744724d018fa13422d1419ca1ad01f972966d509d",
        "tamanho": 1169,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4293245b2619555b15da355aa483a276c63e1e261267f6daeac45a29fe873fa8",
        "hash_atual": "abe8b8e32d4555d8d16a658d5004b11aa81e09506a2facb80abb40bc4dcb11a5",
        "tamanho": 10124,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "6b0c12da565bb5a38b1d881078154abccb8e7d8c8242f2a64301e8f70ee8937d",
        "hash_atual": "8268682ef1279415f9fdf41eccc7f08719ddb1b6926e12eeb792d4c04c126655",
        "tamanho": 8297,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "42a12d493bfa9b9fec010f4891a50a50c42f1e1cb7fb4d76fe7aea7e9c4f2875",
        "hash_atual": "66c4318fcb78de6ac7961ea71aae810982d51a48d2de91ed84c7334899fc2dfb",
        "tamanho": 8524,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8c302a4308e5f917f1659df3fc39afe2cbd2f9d2eb75cae96de04cc905f21784",
        "hash_atual": "140f77a07405d2d692af6ba484c1593cb527610b0ba03922c44d7eff178931b7",
        "tamanho": 29449,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1308722d1e85d879bc384dfd8b472d1c70d57134e61d920bd128f9a1e7494ce8",
        "hash_atual": "f08a6cc87f1b7783ac30d125f4863ad5af4999f7b268ccb3134eca2fee835045",
        "tamanho": 10070,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f2f74829ef2889a68f520706ad77d1f18fad82f111f0730650931cb61d2efc8d",
        "hash_atual": "f8e1e78ba47a2af3553e91a5e1f8d91d3ba593f233925f5c92614c1e70cd0fa7",
        "tamanho": 6219,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fb0d42ff9c703c8abc5ecc3168ab65441620b9a2367136b052e4290296d9a4bb",
        "hash_atual": "4a32c6048ca9ed72370a95c1af664d58cad97163aea072dabff516026e6186fe",
        "tamanho": 7208,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e518e487b70b15e0a30831cfdd82a5490bf3b63d628ed59b4865b01c5e25e794",
        "hash_atual": "a366f29935463b4c728527ea6252d1d8ae9f5135d331ab37296a2ba8248240b6",
        "tamanho": 9296,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "88094713e2803d9a5d2223c38051c3c59955dc91ff854efbfd9184e937623be8",
        "hash_atual": "39630ed4094d897769260797d36ba612ecd88f94d39d84783cc556b363df7b6b",
        "tamanho": 20506,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "fb8cb9df81b9845c62e8f26be3edaeb3a2409d65c4833f7d73d750e02436ffea",
        "hash_atual": "96cb3428baae17ec71303ea0c67b14579cc57af4320505fa74f4f9e8fc4ccd9c",
        "tamanho": 59888,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3522c08dc21799058e34e27ff719227fb6eae4ff85805c829ce0ebd6a868149b",
        "hash_atual": "a73d4b7a1f625590423ef77ab9f622e9b829c03f4864a57c7ff5b71665908530",
        "tamanho": 28604,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "dad11c349f0f1158f5355c111c7293732079dd24a58cfb84114a671692ff8218",
        "hash_atual": "38014a36694d55bf55cdad4d6a8625614569f3e50855222700389fff1a2d981f",
        "tamanho": 15343,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1482a3842bb98fb7998d1240c9e06bcd18f7646b5981d7396ffc4277f9203c1c",
        "hash_atual": "d38d669a2c3af8bf45486e979657b63120cb29d10f843b4089345fa2314ec3b1",
        "tamanho": 26051,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "adde7629254451b41c8c6fc7b98cacc9760a2fddfca20411ddd7feff827d7462",
        "hash_atual": "bd91ff7d4a6c12507766e8ae4ac773e7da875f1dd75214d8bb3b90d845ba5ae5",
        "tamanho": 25889,
        "modificado_em": "2025-09-24T20:57:21.000000Z"
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
            
            Log::info('PreservarMelhorias67Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias67Seeder - Erro', ['error' => $e->getMessage()]);
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