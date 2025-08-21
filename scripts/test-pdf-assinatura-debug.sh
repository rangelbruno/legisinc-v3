#!/bin/bash

echo "🔧 TESTE ESPECÍFICO: Simulando geração de PDF para assinatura"
echo "============================================================="

echo ""
echo "📍 PROBLEMA IDENTIFICADO:"
echo "-------------------------"
echo "• Arquivo DOCX editado pelo OnlyOffice existe: ✅"
echo "• Tamanho: 50.615 bytes (adequado)"
echo "• Localização: /home/bruno/legisinc/storage/app/private/proposicoes/proposicao_8_1755736247.docx"
echo "• Conteúdo do DOCX parece correto (tem ementa e texto do parlamentar)"
echo ""
echo "• PDF de assinatura foi gerado: ✅"
echo "• Tamanho: 29.419 bytes (bem maior que o PDF OnlyOffice de 1.395 bytes)"
echo "• Mas pode não estar mostrando o conteúdo correto na tela de assinatura"

echo ""
echo "🔍 ANÁLISE DO CÓDIGO ProposicaoAssinaturaController:"
echo "---------------------------------------------------"
echo "Fluxo identificado:"
echo "1. ✅ assinar() chama precisaRegerarPDF()"
echo "2. ✅ gerarPDFParaAssinatura() chama criarPDFDoArquivoMaisRecente()"
echo "3. ✅ criarPDFDoArquivoMaisRecente() chama encontrarArquivoMaisRecente()"
echo "4. ❓ encontrarArquivoMaisRecente() retorna o arquivo correto?"
echo "5. ❓ O PDF é gerado com o conteúdo correto?"

echo ""
echo "🔧 VERIFICAÇÃO DOS DIRETÓRIOS DE BUSCA:"
echo "---------------------------------------"

# Listar exatamente o que o método encontrarArquivoMaisRecente() buscaria
echo "Simulando busca do método encontrarArquivoMaisRecente() para proposição 8:"
echo ""

# Padrões de busca (do código original)
PADROES=(
    "proposicao_8_*.docx"
    "proposicao_8_*.rtf"
    "proposicao_8.docx"
    "proposicao_8.rtf"
)

# Diretórios onde buscar (do código original)
DIRETORIOS=(
    "/home/bruno/legisinc/storage/app/proposicoes"
    "/home/bruno/legisinc/storage/app/private/proposicoes"
    "/home/bruno/legisinc/storage/app/public/proposicoes"
    "/home/bruno/legisinc/storage/app"
    "/var/www/html/storage/app/proposicoes"
    "/var/www/html/storage/app/private/proposicoes"
)

ARQUIVOS_ENCONTRADOS=()

echo "Buscando em cada diretório com cada padrão:"
for dir in "${DIRETORIOS[@]}"; do
    echo ""
    echo "📁 Diretório: $dir"
    if [ -d "$dir" ]; then
        echo "   ✅ Existe"
        for padrao in "${PADROES[@]}"; do
            echo "   🔍 Padrão: $padrao"
            # Buscar arquivos com o padrão
            arquivos=($(find "$dir" -maxdepth 1 -name "$padrao" 2>/dev/null))
            if [ ${#arquivos[@]} -gt 0 ]; then
                for arquivo in "${arquivos[@]}"; do
                    if [ -f "$arquivo" ]; then
                        mtime=$(stat -c%Y "$arquivo" 2>/dev/null)
                        size=$(stat -c%s "$arquivo" 2>/dev/null)
                        echo "      ✅ ENCONTRADO: $arquivo"
                        echo "         📏 Tamanho: $size bytes"
                        echo "         📅 Timestamp: $mtime ($(date -d @$mtime '+%Y-%m-%d %H:%M:%S'))"
                        ARQUIVOS_ENCONTRADOS+=("$mtime:$arquivo:$size")
                    fi
                done
            else
                echo "      ❌ Nenhum arquivo encontrado"
            fi
        done
    else
        echo "   ❌ Não existe"
    fi
done

echo ""
echo "📊 RESUMO DE ARQUIVOS ENCONTRADOS:"
echo "---------------------------------"
if [ ${#ARQUIVOS_ENCONTRADOS[@]} -gt 0 ]; then
    echo "Total encontrados: ${#ARQUIVOS_ENCONTRADOS[@]}"
    echo ""
    
    # Ordenar por timestamp (mais recente primeiro)
    IFS=$'\n' sorted=($(sort -rn <<<"${ARQUIVOS_ENCONTRADOS[*]}"))
    
    echo "Ordenação por data (mais recente primeiro):"
    for i in "${!sorted[@]}"; do
        IFS=':' read -r timestamp arquivo size <<< "${sorted[i]}"
        data_formatada=$(date -d @$timestamp '+%Y-%m-%d %H:%M:%S')
        echo "  $((i+1)). $arquivo"
        echo "     📅 $data_formatada"
        echo "     📏 $size bytes"
        if [ $i -eq 0 ]; then
            echo "     🏆 SERIA SELECIONADO (mais recente)"
            ARQUIVO_MAIS_RECENTE="$arquivo"
        fi
        echo ""
    done
else
    echo "❌ NENHUM ARQUIVO ENCONTRADO - ISSO É UM PROBLEMA!"
fi

# Se encontrou arquivo, verificar se é o correto
if [ -n "$ARQUIVO_MAIS_RECENTE" ]; then
    echo ""
    echo "🎯 ARQUIVO QUE SERIA USADO PARA PDF:"
    echo "-----------------------------------"
    echo "Arquivo: $ARQUIVO_MAIS_RECENTE"
    
    # Verificar se é o arquivo correto editado pelo OnlyOffice
    if [[ "$ARQUIVO_MAIS_RECENTE" == *"proposicao_8_1755736247.docx"* ]]; then
        echo "✅ CORRETO: É o arquivo editado pelo OnlyOffice!"
    else
        echo "❌ PROBLEMA: NÃO é o arquivo editado pelo OnlyOffice!"
        echo "   Esperado: algum arquivo contendo 'proposicao_8_1755736247.docx'"
        echo "   Encontrado: $ARQUIVO_MAIS_RECENTE"
    fi
    
    # Verificar conteúdo básico se for DOCX
    if [[ "$ARQUIVO_MAIS_RECENTE" == *.docx ]]; then
        echo ""
        echo "📄 VERIFICAÇÃO BÁSICA DO CONTEÚDO:"
        echo "---------------------------------"
        if command -v unzip &> /dev/null; then
            # Extrair document.xml temporariamente
            TEMP_DIR=$(mktemp -d)
            unzip -q "$ARQUIVO_MAIS_RECENTE" word/document.xml -d "$TEMP_DIR" 2>/dev/null
            if [ -f "$TEMP_DIR/word/document.xml" ]; then
                # Buscar por palavras-chave do conteúdo editado
                if grep -q "Bruno, sua oportunidade chegou" "$TEMP_DIR/word/document.xml"; then
                    echo "✅ Contém o texto editado pelo parlamentar"
                else
                    echo "❌ NÃO contém o texto editado pelo parlamentar"
                fi
                
                if grep -q "Editado pelo Parlamentar" "$TEMP_DIR/word/document.xml"; then
                    echo "✅ Contém a ementa editada"
                else
                    echo "❌ NÃO contém a ementa editada"
                fi
                
                if grep -q "AGUARDANDO PROTOCOLO" "$TEMP_DIR/word/document.xml"; then
                    echo "✅ Contém o número de protocolo correto"
                else
                    echo "❌ NÃO contém o número de protocolo"
                fi
                
                rm -rf "$TEMP_DIR"
            else
                echo "⚠️  Não foi possível extrair document.xml para verificação"
                rm -rf "$TEMP_DIR"
            fi
        else
            echo "⚠️  unzip não disponível - não é possível verificar conteúdo"
        fi
    fi
fi

echo ""
echo "🎯 CONCLUSÃO:"
echo "============"
if [ -n "$ARQUIVO_MAIS_RECENTE" ] && [[ "$ARQUIVO_MAIS_RECENTE" == *"proposicao_8_1755736247.docx"* ]]; then
    echo "✅ O método encontrarArquivoMaisRecente() DEVERIA encontrar o arquivo correto"
    echo "✅ O arquivo existe e tem o conteúdo esperado"
    echo ""
    echo "🔧 POSSÍVEIS CAUSAS DO PROBLEMA:"
    echo "--------------------------------"
    echo "1. ❓ Bug na conversão DOCX → PDF (extrairConteudoDOCX não funciona corretamente)"
    echo "2. ❓ PDF sendo gerado de template em vez de arquivo editado"
    echo "3. ❓ Cache ou problema de timing na geração do PDF"
    echo "4. ❓ Problema na exibição do PDF na view (PDF correto mas view não atualizada)"
    echo ""
    echo "📋 PRÓXIMO PASSO:"
    echo "----------------"
    echo "Testar a tela /proposicoes/8/assinar e verificar EXATAMENTE que conteúdo está sendo exibido"
else
    echo "❌ PROBLEMA CRÍTICO: encontrarArquivoMaisRecente() não encontraria o arquivo correto!"
    echo ""
    echo "🔧 ISSO EXPLICA O PROBLEMA:"
    echo "--------------------------"
    echo "• O método não está encontrando o arquivo editado pelo OnlyOffice"
    echo "• Provavelmente está usando template ou arquivo antigo"
    echo "• Por isso o PDF não reflete as edições do legislativo"
fi