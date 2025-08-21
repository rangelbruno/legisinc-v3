<?php

echo "🧪 TESTE SIMPLES DE EXTRAÇÃO DOCX\n";
echo "=================================\n\n";

// Arquivo mais recente
$arquivos = glob('/home/bruno/legisinc/storage/app/private/proposicoes/proposicao_2_*.docx');
if (empty($arquivos)) {
    echo "❌ Nenhum arquivo DOCX encontrado\n";
    exit;
}

usort($arquivos, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

$arquivoMaisRecente = $arquivos[0];
echo "📂 Arquivo: " . basename($arquivoMaisRecente) . "\n";
echo "📏 Tamanho: " . filesize($arquivoMaisRecente) . " bytes\n\n";

// Simular o método extrairConteudoDOCX
function extrairConteudoDOCXTeste($caminhoDocx) {
    try {
        $zip = new ZipArchive();
        if ($zip->open($caminhoDocx) !== TRUE) {
            return '';
        }
        
        $documentXml = $zip->getFromName('word/document.xml');
        $zip->close();
        
        if (empty($documentXml)) {
            return '';
        }
        
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadXML($documentXml);
        
        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        
        $paragrafos = $xpath->query('//w:p');
        $conteudoCompleto = '';
        
        echo "📊 Parágrafos encontrados no XML: " . $paragrafos->length . "\n\n";
        
        foreach ($paragrafos as $paragrafo) {
            $textoP = '';
            
            // MÉTODO HÍBRIDO: Primeiro extrair todo o texto
            $textos = $xpath->query('.//w:t', $paragrafo);
            foreach ($textos as $texto) {
                $textoP .= $texto->textContent;
            }
            
            // Se tem texto, processar
            if (!empty(trim($textoP))) {
                echo "   📝 Parágrafo: " . substr(trim($textoP), 0, 80) . "...\n";
                $conteudoCompleto .= '<p>' . trim($textoP) . '</p>' . "\n";
            }
        }
        
        return trim($conteudoCompleto);
        
    } catch (Exception $e) {
        echo "❌ Erro: " . $e->getMessage() . "\n";
        return '';
    }
}

echo "🔧 Extraindo conteúdo...\n";
$conteudo = extrairConteudoDOCXTeste($arquivoMaisRecente);

echo "\n📋 RESULTADO:\n";
echo "✅ Conteúdo extraído: " . strlen($conteudo) . " caracteres\n";
echo "📊 Parágrafos HTML: " . substr_count($conteudo, '<p') . "\n\n";

echo "📝 Primeiros 1000 chars:\n";
echo substr($conteudo, 0, 1000) . "\n\n";

// Verificar conteúdo específico
$marcadores = [
    'Revisado pelo Parlamentar',
    'Curiosidade para o dia 20 de agosto',
    'curso.dev',
    'NIC br anuncia',
    'Caraguatatuba, 20 de agosto de 2025'
];

echo "🔍 Verificando conteúdo específico:\n";
foreach ($marcadores as $marcador) {
    if (strpos($conteudo, $marcador) !== false) {
        echo "   ✅ '$marcador' - ENCONTRADO\n";
    } else {
        echo "   ❌ '$marcador' - NÃO ENCONTRADO\n";
    }
}

echo "\n================================\n";
echo "✅ Teste de extração concluído!\n";