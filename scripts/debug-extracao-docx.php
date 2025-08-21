<?php

echo "🔍 DEBUG: EXTRAÇÃO DE CONTEÚDO DOCX\n";
echo "==================================\n\n";

// Arquivo mais recente da proposição 2
$arquivos = glob('/home/bruno/legisinc/storage/app/private/proposicoes/proposicao_2_*.docx');
if (empty($arquivos)) {
    echo "❌ Nenhum arquivo encontrado\n";
    exit;
}

// Ordenar por data de modificação
usort($arquivos, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

$arquivoMaisRecente = $arquivos[0];
echo "📄 Arquivo: " . basename($arquivoMaisRecente) . "\n";
echo "📅 Modificado: " . date('Y-m-d H:i:s', filemtime($arquivoMaisRecente)) . "\n";
echo "📏 Tamanho: " . filesize($arquivoMaisRecente) . " bytes\n\n";

// Tentar extração
echo "🔧 Tentando extração...\n";

try {
    $zip = new ZipArchive();
    if ($zip->open($arquivoMaisRecente) !== TRUE) {
        echo "❌ Erro ao abrir ZIP\n";
        exit;
    }
    
    // Extrair document.xml
    $documentXml = $zip->getFromName('word/document.xml');
    $zip->close();
    
    if (empty($documentXml)) {
        echo "❌ document.xml vazio\n";
        exit;
    }
    
    echo "✅ XML extraído: " . strlen($documentXml) . " caracteres\n\n";
    
    // Carregar XML
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadXML($documentXml);
    
    $xpath = new DOMXPath($doc);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
    
    // Extrair texto simples primeiro
    echo "📝 MÉTODO 1: Extração simples\n";
    $paragrafos = $xpath->query('//w:p');
    echo "   Parágrafos encontrados: " . $paragrafos->length . "\n";
    
    $conteudoSimples = '';
    foreach ($paragrafos as $paragrafo) {
        $textos = $xpath->query('.//w:t', $paragrafo);
        $textoP = '';
        foreach ($textos as $texto) {
            $textoP .= $texto->textContent;
        }
        if (!empty(trim($textoP))) {
            $conteudoSimples .= trim($textoP) . "\n";
        }
    }
    
    echo "   Conteúdo extraído: " . strlen($conteudoSimples) . " caracteres\n";
    echo "   Primeiras 200 chars: " . substr($conteudoSimples, 0, 200) . "...\n\n";
    
    // Extrair com formatação
    echo "📝 MÉTODO 2: Extração com formatação\n";
    $conteudoFormatado = '';
    foreach ($paragrafos as $paragrafo) {
        $textoP = '';
        $elementosTexto = $xpath->query('.//w:r', $paragrafo);
        
        foreach ($elementosTexto as $run) {
            // Verificar formatação
            $rPr = $xpath->query('.//w:rPr', $run)->item(0);
            $isBold = $rPr && $xpath->query('.//w:b', $rPr)->length > 0;
            $isItalic = $rPr && $xpath->query('.//w:i', $rPr)->length > 0;
            
            // Extrair texto
            $textos = $xpath->query('.//w:t', $run);
            foreach ($textos as $texto) {
                $textoAtual = $texto->textContent;
                
                // Aplicar formatação HTML
                if ($isBold) $textoAtual = '<strong>' . $textoAtual . '</strong>';
                if ($isItalic) $textoAtual = '<em>' . $textoAtual . '</em>';
                
                $textoP .= $textoAtual;
            }
        }
        
        if (!empty(trim(strip_tags($textoP)))) {
            $conteudoFormatado .= '<p>' . trim($textoP) . '</p>' . "\n";
        }
    }
    
    echo "   Conteúdo formatado: " . strlen($conteudoFormatado) . " caracteres\n";
    echo "   Primeiras 300 chars: " . substr($conteudoFormatado, 0, 300) . "...\n\n";
    
    // Verificar conteúdo específico
    echo "🔍 VERIFICAÇÃO DE CONTEÚDO ESPECÍFICO:\n";
    $marcadores = [
        'Revisado pelo Parlamentar',
        'Curiosidade para o dia 20 de agosto',
        'curso.dev',
        'NIC br anuncia',
        'Caraguatatuba, 20 de agosto de 2025'
    ];
    
    foreach ($marcadores as $marcador) {
        if (strpos($conteudoSimples, $marcador) !== false) {
            echo "   ✅ '$marcador' - ENCONTRADO\n";
        } else {
            echo "   ❌ '$marcador' - NÃO ENCONTRADO\n";
        }
    }
    
    echo "\n✅ Debug de extração concluído!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}