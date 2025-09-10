<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias59Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-09 09:55:28
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "2c94733853d53dc10c00cafefb841896d0944e28307a544281d1ef15584822e5",
        "hash_atual": "aa9e225a5caf5b3c26bc094eaa2a5d9113131bf57ece12e2eb6d3eb1b3289b19",
        "tamanho": 183240,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "13724c2ab0986d1d922ae1dc0ab4dafd92f024953ff38a1cfb1ab927e0f109e4",
        "hash_atual": "1bfaeb1a0d3e441d400bdf36f3c41023c7efadac0b2d0ec586aa8d422cc1a97e",
        "tamanho": 33929,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "a83dada009b7e3a56d857ba66654836288b897452ce9afd8cd7a1967d2c18957",
        "hash_atual": "31d35cc7c68e579d18847586cd0aa5f29bedb5f329db6fa03fdd287f7cfe0499",
        "tamanho": 184884,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "ddaa7088a1272707fe3f7804d52fc390d309413f94355fe5626f7d5825b09f5d",
        "hash_atual": "4b7761c1dee628a09274dd4cb4f8d1147f3247125209801c4af673e495214907",
        "tamanho": 37954,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "bb39dd155bab6d0520818da8e6c3c9624fa6f4334492cf4b6c0485e1745040d7",
        "hash_atual": "2763f17ad05083994403898192bbabb90b5a9350ea4aeb61cdd7dd93c9ac673d",
        "tamanho": 16468,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "2620174e42875ddb9ad2bd1bff51a14753138d8f04ce529ca1af0334f7577036",
        "hash_atual": "bf07dca4687f8008fc8671f3dec5a6775139a350262f6044ae9730aea209c1f5",
        "tamanho": 18417,
        "modificado_em": "2025-09-09T09:46:00.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "c030421dec77cbf4398707abce15740b6449a0315c1ef56c5690e689e7f7c927",
        "hash_atual": "1a02296811878ed85dcb551bf44d6a2dc1e07cac4a3539402f54c2e99fcc32aa",
        "tamanho": 11594,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8f1b86a9e80b44a15ed148b35a00990c829e7070ef40fad606747812eddce3e0",
        "hash_atual": "3ea32f5f2a9d356d7511797d3427df7dbc27e5ed1898843b5df7a589eeb87ad8",
        "tamanho": 90333,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3534b8ae870ac54fcafbc8fbf681aba8ad8ba6cf0be1c24ac4a4e578eb60e02c",
        "hash_atual": "1a3c430e2158184d9f6074eb5b4bf25810bbbc0e784d6064de376ed0a7a45185",
        "tamanho": 49890,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "eb8f079f66317710098c01eadf886bab5e223fbefaf9898925019ac280fc0c86",
        "hash_atual": "13e5ba5a0f9099d4394609e0b8040de4935cf755f7e1c91c50bba31205a61336",
        "tamanho": 64199,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b859122f69b835b9d098826ea79bad338cd287f833ff0c824223197af4a6d99c",
        "hash_atual": "c01f077f8601e737250f30fffcec2d486b1de0b3f92c52ee628d786f8c71ba34",
        "tamanho": 21668,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "ca11385419e0448aba3beb17ff712462181f8db56387d5e55d62dd2eb77a0540",
        "hash_atual": "f5b7a1eeddb25ae2df9cebd5229342eeb0309d0e00da91380dc65733f7b3c8ef",
        "tamanho": 39431,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "05e194b7ace4828b4949d7ae83e2fbfe04bf7fb14d6a34fd5d25d08a87cf0f1e",
        "hash_atual": "d02bf9ff3fc755ac3dc7d1b84e6fe5765a88b641b5e93c91a62601a7a4e553a2",
        "tamanho": 9714,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "682842cd4662c0d7afe779c28bb640f339b49a1a383850a9b262cc295673b91c",
        "hash_atual": "2a5eb8aeb0ef8a09dfb5653a8990bc20f93641c0f335df92f51d1d022103e25f",
        "tamanho": 2116,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "56ffd0d466fee826ec5e1457f131bfd8b65dfe3c818174f8a5dd8e300919ff18",
        "hash_atual": "bc8a95501a9dc2e02793cfb8f38f708c08773ddc316918c7beb54bbdc6aeaf14",
        "tamanho": 8438,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "b8e7f6eb19b5024f254b75183e251eb727c3d69c228a81989f0c0c2684c8c9e4",
        "hash_atual": "0ce9e3ea16677c5e12c86a4b5361f04b17afae8ff6002421cef5a33fbb93febe",
        "tamanho": 19647,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e1c0a760fcab995fbc81c9df472a68057fa434ae517730f8797404799be026be",
        "hash_atual": "a2157fbef9987d6705c7d4c18a63e3d6a7a918ea105441dfdd229c1c4d0ab9a4",
        "tamanho": 18651,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "5114c0182114fde51a734e6e2b777b8c416b4097ab59ba62a7dddbd4ce2f9edd",
        "hash_atual": "bc0cca2bfb58458a60d15eb2e3c7fe577eff6a0b89975dfde2a3a897c2e954d1",
        "tamanho": 44459,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "40d8f4eea17d2f4e332f323985454995c353c2e3b118fce59fee61450f79a4f8",
        "hash_atual": "1bc3bb12f0f06205c99c629c594ad0b6146a95ec887b9f95bbb8c8c6116b410b",
        "tamanho": 1169,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "75d0790897164b1fce8ebc92d1fa3a7bf1cf022eabff17eee72cce94b9d00242",
        "hash_atual": "1d576f0f75e0a4ca3a48d54dd0f87a5efbbc46c449bdaa3b437f0a3a00241238",
        "tamanho": 10124,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "2f42b73dd52891473d49e7b0c1f0c7a79dccf06876d00358cc8ca48ce4698134",
        "hash_atual": "1889cb1a2f63c15f8496d057e1e3185a8b6281cd3566ea3a2e231c440e09ff80",
        "tamanho": 8297,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f9065b5c42d9fddd4c9c2e9a0c10a8a4228f3b061a0ea75868edb5b2071c5785",
        "hash_atual": "4236405b640c1e5f822c6aa8d1caf0d3c12aedb260504acefbb09a8494d1004f",
        "tamanho": 8524,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a33c4d02f639213680c35a2ff2fe195328625d542fe7eb6260cd5fd9f5a1a5a0",
        "hash_atual": "f3484e0ecfff196eceb14c0d9dca73a2c6cc6bd1dd39d0bbd2021082b7034129",
        "tamanho": 29449,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d6349f318112730e20ab7234e8a72c61519824c8258642f1dcba25969cdda34b",
        "hash_atual": "91ad146fcca823cbe977dcaadd6a739fd35991ad66b97ff07d083ff78b72dd94",
        "tamanho": 10070,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "de5e7e6b9f6675aae1b462af6d4c1f43346ab3d59cac6571d66f50a0bd595a5e",
        "hash_atual": "a9268a912448f0f2acf4f230d967b561399c0d2ded1801e35620f9cb46302f04",
        "tamanho": 6219,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "8d1d948eb3c4b380e7f2365cf410ee853a81c16a3da2fd8795765e1d2a8737c3",
        "hash_atual": "7d3d0c61a3fdd7396110b595aa8b4a2a95e71d97b077a7304bd66de24cb91d62",
        "tamanho": 7208,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "64ab53a87043e7380d8d4c6598aa5e967be5941eb166c5c2439666d2241b8fe3",
        "hash_atual": "6959df05da670f71895af53e285a42dc421b59692776fe65dd5f5d1be612321b",
        "tamanho": 9296,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "19a0a971a69e32187a9686343c27459c66a160caa6ccc89a6a417a3d741340e1",
        "hash_atual": "6f18548dfa6f5e53824bdaef764448b1e21ca106def5979a897eed55e90e14e0",
        "tamanho": 20506,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "56792b51f2481f6939abdab3e83cdf6bca009f11f3206fdd3249a241cc2a5ec4",
        "hash_atual": "edcfb093e018536fd41111335ad69908e80326c4c9d05f7082df60f758d6cef0",
        "tamanho": 59888,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "29bf7a11e9ed15bcc98f40475f79dc4b8609614a4341db7ca37ce62e29c6a645",
        "hash_atual": "b15c72768f6514a08c0e15b324238719c8d32f2be7838e8659299f154803fa85",
        "tamanho": 28604,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7304c1b634171d2b7c1b517a6eed1237902ad8bc087fb1440170b164e9328020",
        "hash_atual": "f621bc5a196bff098b5d836217ca95b71cd1d423fdf84a8d1b77aa0138c934af",
        "tamanho": 15343,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "c589aabf433574af8c28b403291145c8e0b4a4d77e38ca1e8bbbf0849998eee9",
        "hash_atual": "a4aa553330e75bbf8ec629e30ae466545794e54594cbb7baf011e5a961c919ff",
        "tamanho": 26051,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "16049290b003fab12bb979b78f98179a4cfeaf153ff50aaa42b662505942a56f",
        "hash_atual": "62878020ef21d43019a092b9a209fcbefe57b51f02506d63849f194acb33605c",
        "tamanho": 25889,
        "modificado_em": "2025-09-08T20:37:30.000000Z"
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
            
            Log::info('PreservarMelhorias59Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias59Seeder - Erro', ['error' => $e->getMessage()]);
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