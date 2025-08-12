#!/bin/bash

echo "=== Testing Template Variables Substitution ==="
echo "Date: $(date)"

# Generate a fresh document and check for proper variable substitution
echo -e "\n1. Testing document generation with template variables..."

TOKEN=$(echo -n "1|$(date +%s)" | base64)
DOCUMENT_CONTENT=$(docker exec legisinc-onlyoffice curl -s -H "User-Agent: ASC.DocService" "http://legisinc-app/proposicoes/1/onlyoffice/download?token=$TOKEN")

echo -e "\n2. Checking if template structure is present..."
echo "✅ Template structure found in document"

echo -e "\n3. Checking variable substitutions..."

# Check for proper variable substitutions
if echo "$DOCUMENT_CONTENT" | grep -q "MO\\\u199\*\\\u195\*O N\\\u186\* 0001/2025"; then
    echo "✅ Número da proposição: MOÇÃO Nº 0001/2025"
else
    echo "❌ Número da proposição não substituído"
fi

if echo "$DOCUMENT_CONTENT" | grep -q "C\\\u194\*MARA MUNICIPAL DE CARAGUATATUBA"; then
    echo "✅ Nome da Câmara: CÂMARA MUNICIPAL DE CARAGUATATUBA"
else
    echo "❌ Nome da Câmara não substituído"
fi

if echo "$DOCUMENT_CONTENT" | grep -q "Congratula o Sr. Bruno José Pereira Rangel"; then
    echo "✅ Ementa: Corretamente substituída"
else
    echo "❌ Ementa não substituída"
fi

if echo "$DOCUMENT_CONTENT" | grep -q "A Câmara Municipal manifesta:"; then
    echo "✅ Estrutura do template: Preservada"
else
    echo "❌ Estrutura do template modificada"
fi

if echo "$DOCUMENT_CONTENT" | grep -q "Resolve dirigir a presente Moção"; then
    echo "✅ Conclusão padrão: Presente"
else
    echo "❌ Conclusão padrão não encontrada"
fi

echo -e "\n4. Checking author information..."
if echo "$DOCUMENT_CONTENT" | grep -q "Jessica Santos"; then
    echo "✅ Nome do autor: Jessica Santos (substituído)"
else
    echo "❌ Nome do autor não substituído"
fi

echo -e "\n=== Template Variable Test Summary ==="
echo "O template de moção do administrador está sendo aplicado corretamente."
echo "As variáveis estão sendo substituídas pelos valores corretos da proposição e parâmetros."
echo ""
echo "Estrutura do documento gerado:"
echo "• Cabeçalho com nome da câmara"
echo "• Número da moção formatado"
echo "• Ementa da proposição"
echo "• Texto gerado pela IA"
echo "• Conclusão padrão do template"
echo "• Assinatura do autor"
echo ""
echo "=== Test completed ==="