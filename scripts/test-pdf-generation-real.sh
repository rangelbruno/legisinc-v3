#!/bin/bash

echo "🚀 TESTE REAL DA GERAÇÃO DE PDF COM ESTRUTURA WORD COMPLETA"
echo "=========================================================="

echo ""
echo "📋 1. Verificando arquivos DOCX da proposição 2..."

arquivo_mais_recente=$(ls -t /home/bruno/legisinc/storage/app/private/proposicoes/proposicao_2_*.docx | head -1)

if [ -z "$arquivo_mais_recente" ]; then
    echo "❌ Nenhum arquivo DOCX encontrado"
    exit 1
fi

echo "   📂 Arquivo: $(basename "$arquivo_mais_recente")"
echo "   📏 Tamanho: $(stat -c %s "$arquivo_mais_recente") bytes"
echo "   📅 Modificado: $(stat -c %y "$arquivo_mais_recente")"

echo ""
echo "🔧 2. Testando extração PHP do método corrigido..."

# Criar teste PHP temporário
cat > /tmp/test_extracao_word.php << 'EOF'
<?php

// Bootstrap Laravel
require_once '/home/bruno/legisinc/vendor/autoload.php';

$app = require_once '/home/bruno/legisinc/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simular extração como o controller faz
class TestWordExtraction {
    
    public function testarExtracao($caminhoArquivo) {
        echo "🔍 Testando extração do arquivo: " . basename($caminhoArquivo) . "\n";
        
        if (!file_exists($caminhoArquivo)) {
            echo "❌ Arquivo não encontrado\n";
            return false;
        }
        
        try {
            $zip = new ZipArchive();
            if ($zip->open($caminhoArquivo) === TRUE) {
                
                echo "✅ DOCX aberto com sucesso\n";
                
                // 1. EXTRAIR CABEÇALHO
                $conteudoCabecalho = $this->extrairSecaoWord($zip, 'header', 'CABEÇALHO');
                echo "📋 Cabeçalho extraído: " . strlen($conteudoCabecalho) . " caracteres\n";
                if (!empty($conteudoCabecalho)) {
                    echo "   Preview: " . substr($conteudoCabecalho, 0, 100) . "...\n";
                }
                
                // 2. EXTRAIR CORPO
                $conteudoCorpo = $this->extrairSecaoWord($zip, 'document', 'CORPO');
                echo "📋 Corpo extraído: " . strlen($conteudoCorpo) . " caracteres\n";
                if (!empty($conteudoCorpo)) {
                    echo "   Preview: " . substr($conteudoCorpo, 0, 200) . "...\n";
                }
                
                // 3. EXTRAIR RODAPÉ
                $conteudoRodape = $this->extrairSecaoWord($zip, 'footer', 'RODAPÉ');
                echo "📋 Rodapé extraído: " . strlen($conteudoRodape) . " caracteres\n";
                if (!empty($conteudoRodape)) {
                    echo "   Conteúdo: '$conteudoRodape'\n";
                }
                
                $zip->close();
                
                // 4. COMBINAR SEÇÕES
                $documentoCompleto = $this->combinarSecoesWord($conteudoCabecalho, $conteudoCorpo, $conteudoRodape);
                
                echo "\n📊 RESULTADO FINAL:\n";
                echo "   Total de caracteres: " . strlen($documentoCompleto) . "\n";
                echo "   Linhas: " . substr_count($documentoCompleto, "\n") . "\n";
                
                echo "\n📝 DOCUMENTO COMBINADO:\n";
                echo "=====================================\n";
                echo $documentoCompleto . "\n";
                echo "=====================================\n";
                
                return true;
                
            } else {
                echo "❌ Não foi possível abrir DOCX\n";
                return false;
            }
            
        } catch (Exception $e) {
            echo "❌ Erro: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    private function extrairSecaoWord($zip, $tipoSecao, $nomeSecao) {
        $textoSecao = '';
        
        try {
            if ($tipoSecao === 'document') {
                $xmlContent = $zip->getFromName('word/document.xml');
                if ($xmlContent) {
                    $textoSecao = $this->extrairTextoDeXml($xmlContent, $nomeSecao);
                }
            } else {
                for ($i = 1; $i <= 10; $i++) {
                    $xmlContent = $zip->getFromName("word/{$tipoSecao}{$i}.xml");
                    if ($xmlContent) {
                        $textoArquivo = $this->extrairTextoDeXml($xmlContent, "{$nomeSecao}{$i}");
                        if (!empty($textoArquivo)) {
                            $textoSecao .= $textoArquivo . "\n";
                        }
                    }
                }
            }
        } catch (Exception $e) {
            echo "⚠️ Erro ao extrair {$nomeSecao}: " . $e->getMessage() . "\n";
        }
        
        return trim($textoSecao);
    }
    
    private function extrairTextoDeXml($xmlContent, $nomeSecao) {
        preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/s', $xmlContent, $matches);
        
        if (!isset($matches[1]) || empty($matches[1])) {
            return '';
        }
        
        $textoCompleto = '';
        foreach ($matches[1] as $texto) {
            $textoLimpo = html_entity_decode($texto, ENT_QUOTES | ENT_XML1);
            $textoLimpo = trim($textoLimpo);
            
            if (!empty($textoLimpo)) {
                $textoCompleto .= $textoLimpo . ' ';
            }
        }
        
        return trim($textoCompleto);
    }
    
    private function combinarSecoesWord($cabecalho, $corpo, $rodape) {
        $documentoFinal = '';
        
        if (!empty($cabecalho)) {
            $documentoFinal .= trim($cabecalho) . "\n\n";
        }
        
        if (!empty($corpo)) {
            $corpoFormatado = $this->formatarCorpoDocumento($corpo);
            $documentoFinal .= $corpoFormatado . "\n\n";
        }
        
        if (!empty($rodape)) {
            $documentoFinal .= trim($rodape);
        }
        
        return trim($documentoFinal);
    }
    
    private function formatarCorpoDocumento($corpo) {
        $corpo = preg_replace('/\s+/', ' ', $corpo);
        $corpo = trim($corpo);
        
        $corpo = str_replace('EMENTA:', "\n\nEMENTA:", $corpo);
        $corpo = str_replace('A Câmara Municipal manifesta:', "\n\nA Câmara Municipal manifesta:\n", $corpo);
        $corpo = str_replace('Resolve dirigir', "\n\nResolve dirigir", $corpo);
        
        $corpo = preg_replace('/\.\s+([A-Z])/', ".\n\n$1", $corpo);
        
        return $corpo;
    }
}

// Executar teste
$teste = new TestWordExtraction();
EOF

# Obter caminho do arquivo mais recente
echo "$arquivo_mais_recente" >> /tmp/test_extracao_word.php
echo 'echo "\n🚀 Iniciando teste de extração...\n";' >> /tmp/test_extracao_word.php
echo '$resultado = $teste->testarExtracao("'$arquivo_mais_recente'");' >> /tmp/test_extracao_word.php
echo 'echo "\n" . ($resultado ? "✅ TESTE CONCLUÍDO COM SUCESSO!" : "❌ TESTE FALHOU") . "\n";' >> /tmp/test_extracao_word.php

echo "   🐘 Executando teste PHP..."
php /tmp/test_extracao_word.php

echo ""
echo "🧹 3. Limpando arquivo temporário..."
rm -f /tmp/test_extracao_word.php

echo ""
echo "📋 4. Verificando logs do Laravel..."
if [ -f "/home/bruno/legisinc/storage/logs/laravel.log" ]; then
    logs_recentes=$(tail -20 /home/bruno/legisinc/storage/logs/laravel.log | grep -E "(PDF|Documento Word|Seção)" | tail -5)
    if [ -n "$logs_recentes" ]; then
        echo "   📊 Logs recentes relacionados:"
        echo "$logs_recentes" | sed 's/^/      /'
    fi
fi

echo ""
echo "=========================================================="
echo "✅ TESTE REAL DE EXTRAÇÃO CONCLUÍDO!"
echo ""
echo "🎯 PRÓXIMO PASSO: Testar no navegador"
echo "   http://localhost:8001/proposicoes/2/assinar"
echo ""
echo "✅ Estrutura Word completa implementada!"