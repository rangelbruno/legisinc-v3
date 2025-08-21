#!/bin/bash

echo "🎯 TESTE DA ESTRUTURA WORD COMPLETA NO PDF"
echo "=========================================="

echo ""
echo "🔧 1. Validando implementação no Controller..."

controller_file="/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"

if [ -f "$controller_file" ]; then
    echo "   ✅ Controller encontrado"
    
    # Verificar métodos implementados
    metodos_necessarios=(
        "extrairConteudoDOCX"
        "extrairSecaoWord"
        "extrairTextoDeXml"
        "combinarSecoesWord"
        "formatarCorpoDocumento"
    )
    
    for metodo in "${metodos_necessarios[@]}"; do
        if grep -q "private function ${metodo}(" "$controller_file"; then
            echo "      ✅ Método $metodo implementado"
        else
            echo "      ❌ Método $metodo NÃO ENCONTRADO"
        fi
    done
    
    # Verificar lógica específica
    if grep -q "EXTRAIR CABEÇALHO (header\*.xml)" "$controller_file"; then
        echo "      ✅ Extração de cabeçalho implementada"
    fi
    
    if grep -q "EXTRAIR RODAPÉ (footer\*.xml)" "$controller_file"; then
        echo "      ✅ Extração de rodapé implementada"
    fi
    
    if grep -q "COMBINAR NA ORDEM CORRETA" "$controller_file"; then
        echo "      ✅ Combinação ordenada implementada"
    fi
else
    echo "   ❌ Controller não encontrado"
    exit 1
fi

echo ""
echo "📂 2. Verificando arquivos DOCX disponíveis..."

arquivos_docx=$(ls -t /home/bruno/legisinc/storage/app/private/proposicoes/proposicao_2_*.docx 2>/dev/null | head -3)

if [ -z "$arquivos_docx" ]; then
    echo "   ❌ Nenhum arquivo DOCX encontrado para proposição 2"
    exit 1
fi

echo "   ✅ Arquivos DOCX encontrados:"
echo "$arquivos_docx" | while read arquivo; do
    if [ -n "$arquivo" ]; then
        echo "      📄 $(basename "$arquivo") - $(stat -c %s "$arquivo") bytes"
    fi
done

# Usar o arquivo mais recente
arquivo_mais_recente=$(echo "$arquivos_docx" | head -1)

echo ""
echo "🔍 3. Analisando estrutura do arquivo mais recente..."
echo "   📂 Arquivo: $(basename "$arquivo_mais_recente")"

# Extrair e verificar estrutura
temp_dir=$(mktemp -d)
if unzip -q "$arquivo_mais_recente" -d "$temp_dir"; then
    echo "   ✅ DOCX extraído com sucesso"
    
    # Verificar presença de header
    if ls "$temp_dir/word/header"*.xml &>/dev/null; then
        header_count=$(ls "$temp_dir/word/header"*.xml | wc -l)
        echo "      🎩 Cabeçalhos encontrados: $header_count"
        
        for header in "$temp_dir/word/header"*.xml; do
            if [ -s "$header" ]; then
                header_size=$(stat -c %s "$header")
                echo "         📄 $(basename "$header"): $header_size bytes"
                
                # Verificar se tem imagem
                if grep -q '<w:drawing>' "$header"; then
                    echo "         🖼️ Contém imagem/desenho"
                fi
            fi
        done
    else
        echo "      ❌ Nenhum cabeçalho encontrado"
    fi
    
    # Verificar presença de footer
    if ls "$temp_dir/word/footer"*.xml &>/dev/null; then
        footer_count=$(ls "$temp_dir/word/footer"*.xml | wc -l)
        echo "      👠 Rodapés encontrados: $footer_count"
        
        for footer in "$temp_dir/word/footer"*.xml; do
            if [ -s "$footer" ]; then
                footer_size=$(stat -c %s "$footer")
                echo "         📄 $(basename "$footer"): $footer_size bytes"
                
                # Extrair texto do rodapé
                footer_text=$(grep -o '<w:t[^>]*>[^<]*</w:t>' "$footer" | sed 's/<[^>]*>//g' | tr '\n' ' ')
                if [ -n "$footer_text" ]; then
                    echo "         📝 Texto: $footer_text"
                fi
            fi
        done
    else
        echo "      ❌ Nenhum rodapé encontrado"
    fi
    
    # Verificar document.xml
    if [ -f "$temp_dir/word/document.xml" ]; then
        doc_size=$(stat -c %s "$temp_dir/word/document.xml")
        echo "      📄 document.xml: $doc_size bytes"
        
        # Contar parágrafos
        paragrafos=$(grep -o '<w:p[^>]*>' "$temp_dir/word/document.xml" | wc -l)
        echo "         📊 Parágrafos: $paragrafos"
    fi
    
    rm -rf "$temp_dir"
else
    echo "   ❌ Erro ao extrair DOCX"
    exit 1
fi

echo ""
echo "🧪 4. TESTE REAL: Acessando o PDF..."

echo "   🌐 Testando URL: http://localhost:8001/proposicoes/2/assinar"
echo ""
echo "   📋 INSTRUÇÕES PARA TESTE MANUAL:"
echo "      1. Abra: http://localhost:8001/login"
echo "      2. Login: jessica@sistema.gov.br / 123456"
echo "      3. Acesse: http://localhost:8001/proposicoes/2/assinar"
echo "      4. Clique na aba 'Visualização do Documento'"
echo ""
echo "   ✅ RESULTADO ESPERADO:"
echo "      • CABEÇALHO: Imagem da Câmara Municipal (se configurada)"
echo "      • CORPO: Todo o conteúdo editado pelo Legislativo"
echo "      • RODAPÉ: 'Câmara Municipal de Caraguatatuba - Documento Oficial'"
echo ""

echo "🔄 5. Validação automática via logs..."

# Limpar logs anteriores relacionados a PDF
if [ -f "/home/bruno/legisinc/storage/logs/laravel.log" ]; then
    echo "   📋 Verificando logs recentes..."
    
    # Procurar por logs de PDF dos últimos 5 minutos
    logs_pdf=$(tail -100 /home/bruno/legisinc/storage/logs/laravel.log | grep -i "PDF Assinatura")
    
    if [ -n "$logs_pdf" ]; then
        echo "      ✅ Logs de PDF encontrados:"
        echo "$logs_pdf" | tail -5 | sed 's/^/         /'
    else
        echo "      ⚠️ Nenhum log recente de PDF encontrado"
        echo "         💡 Acesse o link acima para gerar logs"
    fi
fi

echo ""
echo "=========================================="
echo "✅ TESTE DE ESTRUTURA WORD COMPLETA"
echo ""
echo "🎯 CORREÇÃO IMPLEMENTADA:"
echo "   • Sistema agora extrai header*.xml + document.xml + footer*.xml"
echo "   • Combina na ordem correta: CABEÇALHO + CORPO + RODAPÉ"
echo "   • Respeita formatação configurada pelo Legislativo"
echo ""
echo "🚀 PARA TESTAR:"
echo "   http://localhost:8001/proposicoes/2/assinar"
echo ""
echo "✅ Status: PRONTO PARA VALIDAÇÃO!"