<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PreservarMelhorias57Seeder extends Seeder
{
    /**
     * Preservar melhorias detectadas automaticamente
     * Gerado em: 2025-09-08 20:37:24
     * 
     * Alterações detectadas:
     * [
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoAssinaturaController.php",
        "tipo": "modificado",
        "hash_anterior": "4af602a08be71b1229fc66949aab60a698cd1a5d7c6e0c999be1f388198f84a7",
        "hash_atual": "2c94733853d53dc10c00cafefb841896d0944e28307a544281d1ef15584822e5",
        "tamanho": 183240,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "app\/Http\/Controllers\/ProposicaoProtocoloController.php",
        "tipo": "modificado",
        "hash_anterior": "b94c90878c295c42ebb50a31f455a274e645cd26709f55a03adf9ca36e7b9e88",
        "hash_atual": "13724c2ab0986d1d922ae1dc0ab4dafd92f024953ff38a1cfb1ab927e0f109e4",
        "tamanho": 33929,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "app\/Services\/OnlyOffice\/OnlyOfficeService.php",
        "tipo": "modificado",
        "hash_anterior": "71353537afe84cb139e0184bde66f2e2c10c2d61bf90420117537f55ddf3b8b5",
        "hash_atual": "a83dada009b7e3a56d857ba66654836288b897452ce9afd8cd7a1967d2c18957",
        "tamanho": 184884,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateProcessorService.php",
        "tipo": "modificado",
        "hash_anterior": "376bcf11798e624c10a049f62b130c3268fa1ba1c56f37627bbbca03869f8f2d",
        "hash_atual": "ddaa7088a1272707fe3f7804d52fc390d309413f94355fe5626f7d5825b09f5d",
        "tamanho": 37954,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "app\/Services\/Template\/TemplateVariableService.php",
        "tipo": "modificado",
        "hash_anterior": "4c69e0d3011028f41edf2ce5487d0f2c4f47df17c7cc55b6133169ee60eee6f0",
        "hash_atual": "bb39dd155bab6d0520818da8e6c3c9624fa6f4334492cf4b6c0485e1745040d7",
        "tamanho": 16468,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "app\/Models\/Proposicao.php",
        "tipo": "modificado",
        "hash_anterior": "9d617212fbe3737c30ffc20f79b3acde8693a4d830d5e3d21e152faf2efe04a6",
        "hash_atual": "2620174e42875ddb9ad2bd1bff51a14753138d8f04ce529ca1af0334f7577036",
        "tamanho": 16728,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "config\/dompdf.php",
        "tipo": "modificado",
        "hash_anterior": "123c1058fa421f9e838f9dbcaa4bd7ccbbec8bb31a7ce136e7ef057885e5a938",
        "hash_atual": "c030421dec77cbf4398707abce15740b6449a0315c1ef56c5690e689e7f7c927",
        "tamanho": 11594,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-pdf-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "52ecb445413c4b771a212873591f8fa381fef6e7ced552eadd2a8016245b953e",
        "hash_atual": "8f1b86a9e80b44a15ed148b35a00990c829e7070ef40fad606747812eddce3e0",
        "tamanho": 90333,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar-vue.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1bf5690c6d87610da61856a9b67539ded685f7e3cf146c007480061d2c400d18",
        "hash_atual": "3534b8ae870ac54fcafbc8fbf681aba8ad8ba6cf0be1c24ac4a4e578eb60e02c",
        "tamanho": 49890,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/assinar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "1b53485236887fd5e40732d73a4165311ebfecc814fa7bce5fd0c991e252994c",
        "hash_atual": "eb8f079f66317710098c01eadf886bab5e223fbefaf9898925019ac280fc0c86",
        "tamanho": 64199,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/historico.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0e7eb09cd959365a2bd0991f188e865517319a7f52d02bb3f4c11e4782adfdf6",
        "hash_atual": "b859122f69b835b9d098826ea79bad338cd287f833ff0c824223197af4a6d99c",
        "tamanho": 21668,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e1b46d470937f717047987f85c966febb16b0ac77ef47d6abe23694bfb34f279",
        "hash_atual": "ca11385419e0448aba3beb17ff712462181f8db56387d5e55d62dd2eb77a0540",
        "tamanho": 39431,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/assinatura\/visualizar-pdf-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "cd25bdfafc911fef7256b880efd934f6a41c67ef731aad2374874bfc49b0a46c",
        "hash_atual": "05e194b7ace4828b4949d7ae83e2fbfe04bf7fb14d6a34fd5d25d08a87cf0f1e",
        "tamanho": 9714,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/nao-encontrada.blade.php",
        "tipo": "modificado",
        "hash_anterior": "0d7167985d90cabd7d2bd7647ed33752a7b9d82a1b82f0009fe48d22bf7ce3ab",
        "hash_atual": "682842cd4662c0d7afe779c28bb640f339b49a1a383850a9b262cc295673b91c",
        "tamanho": 2116,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/consulta\/publica.blade.php",
        "tipo": "modificado",
        "hash_anterior": "3a537620f7e228e06fc0e2bb32884b8b39cc8801e7440876222f80e009f47a66",
        "hash_atual": "56ffd0d466fee826ec5e1457f131bfd8b65dfe3c818174f8a5dd8e300919ff18",
        "tamanho": 8438,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/aguardando-protocolo.blade.php",
        "tipo": "modificado",
        "hash_anterior": "67216d0129b1cf61a85dc1742e886f0624edab93cf28f75dad0378f8f4704a76",
        "hash_atual": "b8e7f6eb19b5024f254b75183e251eb727c3d69c228a81989f0c0c2684c8c9e4",
        "tamanho": 19647,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/editar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a9a676a442aa6c2656170e6935c91a20c40ae8bd3e095b84478a2ea6ce3583b4",
        "hash_atual": "e1c0a760fcab995fbc81c9df472a68057fa434ae517730f8797404799be026be",
        "tamanho": 18651,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f90376eb7360066f17c57d1e937f3edf1dedfeea82e047f946b93d6cee7f5c28",
        "hash_atual": "5114c0182114fde51a734e6e2b777b8c416b4097ab59ba62a7dddbd4ce2f9edd",
        "tamanho": 44459,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "9677f65de1d4f0252288565d48337472711408c073e70e8ef172b2ebf8f6aec8",
        "hash_atual": "40d8f4eea17d2f4e332f323985454995c353c2e3b118fce59fee61450f79a4f8",
        "tamanho": 1169,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-dados.blade.php",
        "tipo": "modificado",
        "hash_anterior": "43fee6da29de27c999ed8c05084b51686bfa7b3730043b7c10ea3678cc8fbbea",
        "hash_atual": "75d0790897164b1fce8ebc92d1fa3a7bf1cf022eabff17eee72cce94b9d00242",
        "tamanho": 10124,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio-pdf.blade.php",
        "tipo": "modificado",
        "hash_anterior": "129b5ff93d159d03fce059a367f44c06578eb340536ac2c3cb1e9db7ca15dc07",
        "hash_atual": "2f42b73dd52891473d49e7b0c1f0c7a79dccf06876d00358cc8ca48ce4698134",
        "tamanho": 8297,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/relatorio.blade.php",
        "tipo": "modificado",
        "hash_anterior": "e2810679d325534a96e43db4b1ea48de6ecccb651ba189ff3d0c66633e326ae4",
        "hash_atual": "f9065b5c42d9fddd4c9c2e9a0c10a8a4228f3b061a0ea75868edb5b2071c5785",
        "tamanho": 8524,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/legislativo\/revisar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "66de5f36a6ac511296a69b9163896aec9d6e997c1dbbd4fcc9e59d4721ad9441",
        "hash_atual": "a33c4d02f639213680c35a2ff2fe195328625d542fe7eb6260cd5fd9f5a1a5a0",
        "tamanho": 29449,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/parlamentar\/onlyoffice-editor.blade.php",
        "tipo": "modificado",
        "hash_anterior": "a7a83e9c45850e237017f72964a63e4b9c484c41fe05bc84eb43527f8fe8b824",
        "hash_atual": "d6349f318112730e20ab7234e8a72c61519824c8258642f1dcba25969cdda34b",
        "tamanho": 10070,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/protocolo-otimizado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "95129d9467a1dcad7f512c357ba1f9eb0cff9aa435ee91eba77b0f0fcb30f2f2",
        "hash_atual": "de5e7e6b9f6675aae1b462af6d4c1f43346ab3d59cac6571d66f50a0bd595a5e",
        "tamanho": 6219,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template-optimized.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4c5956983be4718db16d8114c65a15c7001d9683e314595cc7f52d866cd13234",
        "hash_atual": "8d1d948eb3c4b380e7f2365cf410ee853a81c16a3da2fd8795765e1d2a8737c3",
        "tamanho": 7208,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/pdf\/template.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d369375e1bd95a289b90357046580533099475e52558e0a72490200f09bd7b9f",
        "hash_atual": "64ab53a87043e7380d8d4c6598aa5e967be5941eb166c5c2439666d2241b8fe3",
        "tamanho": 9296,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-melhorado.blade.php",
        "tipo": "modificado",
        "hash_anterior": "f7b792c9e21ef88e802e064c1bfe3da3e0bbcf6e62794999dc49ea22c0e4da9b",
        "hash_atual": "19a0a971a69e32187a9686343c27459c66a160caa6ccc89a6a417a3d741340e1",
        "tamanho": 20506,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index-original.blade.php",
        "tipo": "modificado",
        "hash_anterior": "7e28b99ad0ded2a6bf0df9babeb71b5ff6dc86685308591f4f87c7a218a2f152",
        "hash_atual": "56792b51f2481f6939abdab3e83cdf6bca009f11f3206fdd3249a241cc2a5ec4",
        "tamanho": 59888,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/index.blade.php",
        "tipo": "modificado",
        "hash_anterior": "d1b81401a9a83aad1f76fe6058e20d7f5a464a7c5f3db97029d26e5b1c0fe271",
        "hash_atual": "29bf7a11e9ed15bcc98f40475f79dc4b8609614a4341db7ca37ce62e29c6a645",
        "tamanho": 28604,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar-simples.blade.php",
        "tipo": "modificado",
        "hash_anterior": "dd9448a34d524445291a4581c57f89ae472b62edffd9375977105e3a58558e9f",
        "hash_atual": "7304c1b634171d2b7c1b517a6eed1237902ad8bc087fb1440170b164e9328020",
        "tamanho": 15343,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolar.blade.php",
        "tipo": "modificado",
        "hash_anterior": "4369f680a6d38054bfc1a14bdc21c692b2fc23b7809c07d1ceb9ac53b0efddc7",
        "hash_atual": "c589aabf433574af8c28b403291145c8e0b4a4d77e38ca1e8bbbf0849998eee9",
        "tamanho": 26051,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
    },
    {
        "arquivo": "resources\/views\/proposicoes\/protocolo\/protocolos-hoje.blade.php",
        "tipo": "modificado",
        "hash_anterior": "64cc83536425c767e7301c2e58c1a8e0a59cc075454f0618496edc090cd9aff2",
        "hash_atual": "16049290b003fab12bb979b78f98179a4cfeaf153ff50aaa42b662505942a56f",
        "tamanho": 25889,
        "modificado_em": "2025-09-08T20:30:20.000000Z"
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
            
            Log::info('PreservarMelhorias57Seeder - Melhorias preservadas', [
                'arquivos_preservados' => count($this->arquivosPreservados()),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erro ao preservar melhorias: ' . $e->getMessage());
            Log::error('PreservarMelhorias57Seeder - Erro', ['error' => $e->getMessage()]);
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