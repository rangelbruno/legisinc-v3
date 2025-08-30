<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CorrecaoAssinaturePDFSeeder extends Seeder
{
    /**
     * Corrigir problema de visualização de assinatura e protocolo no PDF assinado
     */
    public function run(): void
    {
        echo "🔧 Aplicando correções para exibição de assinatura e protocolo no PDF...\n";

        // 1. Corrigir método gerarHTMLParaPDF no ProposicaoAssinaturaController
        $this->corrigirMetodoGerarHTMLParaPDF();

        // 2. Instalar biblioteca QR Code se não existir
        $this->instalarBibliotecaQRCode();

        // 3. Configurar parâmetros de assinatura se não existem
        $this->configurarParametrosAssinatura();

        // 4. Limpar cache para garantir que mudanças sejam aplicadas
        $this->limparCache();

        echo "✅ Correções aplicadas com sucesso!\n";
        echo "📋 Próximos passos:\n";
        echo "   - Testar geração de PDF em /proposicoes/3/assinar\n";
        echo "   - Verificar se assinatura e protocolo aparecem corretamente\n";
        echo "   - PDF deve mostrar dados completos do Parlamentar e protocolo\n";
    }

    /**
     * Corrigir método de geração de HTML para PDF
     */
    private function corrigirMetodoGerarHTMLParaPDF(): void
    {
        $controllerPath = app_path('Http/Controllers/ProposicaoAssinaturaController.php');

        if (! file_exists($controllerPath)) {
            echo "❌ Arquivo ProposicaoAssinaturaController.php não encontrado!\n";

            return;
        }

        $content = file_get_contents($controllerPath);

        // Verificar se já tem a correção
        if (strpos($content, '// CORREÇÃO: Melhorar renderização de assinatura no PDF') !== false) {
            echo "✅ Método gerarHTMLParaPDF já corrigido.\n";

            return;
        }

        // Encontrar o método gerarHTMLParaPDF e aplicar correções
        $search = 'private function gerarHTMLParaPDF(Proposicao $proposicao, string $conteudo): string
    {
        // Gerar cabeçalho com dados da câmara e número da proposição';

        $replace = 'private function gerarHTMLParaPDF(Proposicao $proposicao, string $conteudo): string
    {
        // CORREÇÃO: Melhorar renderização de assinatura no PDF
        // Gerar cabeçalho com dados da câmara e número da proposição';

        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);

            // Também corrigir a parte do CSS para melhorar visualização
            $cssSearch = '.assinatura-digital { 
                    border: 1px solid #28a745; 
                    padding: 10px; 
                    margin: 20px 0; 
                    background-color: #f8f9fa;
                    font-family: Arial, sans-serif;
                    font-size: 10pt;
                }';

            $cssReplace = '.assinatura-digital { 
                    border: 2px solid #28a745; 
                    padding: 15px; 
                    margin: 20px 0; 
                    background-color: #f0f8f0;
                    font-family: Arial, sans-serif;
                    font-size: 11pt;
                    page-break-inside: avoid;
                    position: relative;
                    width: 100%;
                    box-sizing: border-box;
                }';

            $content = str_replace($cssSearch, $cssReplace, $content);

            // Corrigir também para não usar position fixed que não funciona em PDF
            $positionFixedSearch = 'style="position: fixed; right: 20px; top: 50%; transform: translateY(-50%); width: 200px;';
            $positionFixedReplace = 'style="width: 100%; margin: 20px 0; text-align: center;';

            $content = str_replace($positionFixedSearch, $positionFixedReplace, $content);

            file_put_contents($controllerPath, $content);
            echo "✅ Método gerarHTMLParaPDF corrigido para melhor visualização em PDF.\n";
        } else {
            echo "⚠️ Método gerarHTMLParaPDF não encontrado na forma esperada.\n";
        }
    }

    /**
     * Instalar biblioteca QR Code se não existir
     */
    private function instalarBibliotecaQRCode(): void
    {
        // Verificar se a biblioteca já existe
        if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            echo "✅ Biblioteca QR Code já instalada.\n";

            return;
        }

        echo "📦 Instalando biblioteca simple-qrcode...\n";

        // Executar composer require para instalar
        $composerJson = base_path('composer.json');
        if (file_exists($composerJson)) {
            $json = json_decode(file_get_contents($composerJson), true);

            if (! isset($json['require']['simplesoftwareio/simple-qrcode'])) {
                $json['require']['simplesoftwareio/simple-qrcode'] = '^4.2';
                file_put_contents($composerJson, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

                echo "✅ Adicionado simple-qrcode ao composer.json.\n";
                echo "📝 Execute 'composer install' para instalar a biblioteca.\n";
            }
        }
    }

    /**
     * Configurar parâmetros de assinatura se não existem
     */
    private function configurarParametrosAssinatura(): void
    {
        // Verificar se módulo Templates existe
        $moduloTemplates = DB::table('parametros_modulos')
            ->where('nome', 'Templates')
            ->first();

        if (! $moduloTemplates) {
            echo "❌ Módulo Templates não encontrado!\n";

            return;
        }

        // Verificar se submódulo Assinatura e QR Code existe
        $submoduloAssinatura = DB::table('parametros_submodulos')
            ->where('modulo_id', $moduloTemplates->id)
            ->where('nome', 'Assinatura e QR Code')
            ->first();

        if (! $submoduloAssinatura) {
            // Criar submódulo
            $submoduloId = DB::table('parametros_submodulos')->insertGetId([
                'modulo_id' => $moduloTemplates->id,
                'nome' => 'Assinatura e QR Code',
                'descricao' => 'Configurações de assinatura digital e QR Code',
                'ordem' => 3,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "✅ Submódulo 'Assinatura e QR Code' criado.\n";
        } else {
            $submoduloId = $submoduloAssinatura->id;
        }

        // Criar campos necessários
        $campos = [
            [
                'nome' => 'assinatura_texto',
                'label' => 'Texto da Assinatura Digital',
                'tipo' => 'textarea',
                'valor_padrao' => "Documento assinado digitalmente por:\n{autor_nome}\n{autor_cargo}\nEm {data_assinatura}",
                'ordem' => 1,
            ],
            [
                'nome' => 'assinatura_apenas_protocolo',
                'label' => 'Exibir assinatura apenas após protocolo',
                'tipo' => 'boolean',
                'valor_padrao' => '1',
                'ordem' => 2,
            ],
            [
                'nome' => 'qrcode_texto',
                'label' => 'Texto do QR Code',
                'tipo' => 'textarea',
                'valor_padrao' => "Consulte este documento online:\nProtocolo: {numero_protocolo}",
                'ordem' => 3,
            ],
            [
                'nome' => 'qrcode_apenas_protocolo',
                'label' => 'Exibir QR Code apenas após protocolo',
                'tipo' => 'boolean',
                'valor_padrao' => '1',
                'ordem' => 4,
            ],
            [
                'nome' => 'qrcode_url_formato',
                'label' => 'Formato da URL do QR Code',
                'tipo' => 'text',
                'valor_padrao' => '{base_url}/proposicoes/consulta/{numero_protocolo}',
                'ordem' => 5,
            ],
            [
                'nome' => 'qrcode_tamanho',
                'label' => 'Tamanho do QR Code (pixels)',
                'tipo' => 'number',
                'valor_padrao' => '100',
                'ordem' => 6,
            ],
        ];

        foreach ($campos as $campo) {
            $campoExistente = DB::table('parametros_campos')
                ->where('submodulo_id', $submoduloId)
                ->where('nome', $campo['nome'])
                ->first();

            if (! $campoExistente) {
                $campoId = DB::table('parametros_campos')->insertGetId([
                    'submodulo_id' => $submoduloId,
                    'nome' => $campo['nome'],
                    'label' => $campo['label'],
                    'tipo' => $campo['tipo'],
                    'valor_padrao' => $campo['valor_padrao'],
                    'obrigatorio' => false,
                    'ordem' => $campo['ordem'],
                    'ativo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Criar valor padrão do parâmetro
                DB::table('parametros')->insert([
                    'campo_id' => $campoId,
                    'valor' => $campo['valor_padrao'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                echo "✅ Campo '{$campo['nome']}' criado com valor padrão.\n";
            }
        }
    }

    /**
     * Limpar cache
     */
    private function limparCache(): void
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            echo "✅ Cache limpo com sucesso.\n";
        } catch (\Exception $e) {
            echo '⚠️ Erro ao limpar cache: '.$e->getMessage()."\n";
        }
    }
}
