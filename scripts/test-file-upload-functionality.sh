#!/bin/bash

echo "🔧 TESTANDO FUNCIONALIDADE DE CONTADOR DE ARQUIVOS"
echo "=================================================="

echo ""
echo "📋 VERIFICAÇÕES IMPLEMENTADAS:"
echo ""

echo "1. ✅ CSS corrigido:"
echo "   - Removido 'display: none !important' que impedia exibição"
echo "   - Agora usa 'display: none' normal"

echo ""
echo "2. ✅ JavaScript corrigido:"
echo "   - Removido '!important' das funções de show/hide"
echo "   - Adicionados console.log para debug"
echo "   - Lógica de atualização preservada"

echo ""
echo "3. ✅ Debug habilitado:"
echo "   - Log quando arquivo é adicionado"
echo "   - Log quando arquivo é removido"
echo "   - Log do cálculo de tamanho total"
echo "   - Log da visibilidade do contador"

echo ""
echo "🎯 COMO TESTAR AGORA:"
echo ""

echo "1. 🌐 Acesse: http://localhost:8001/proposicoes/create"
echo "2. 👤 Faça login: jessica@sistema.gov.br / 123456"
echo "3. 📝 Preencha tipo e ementa da proposição"
echo "4. 📎 Vá até a seção 'Anexos da Proposição'"
echo "5. 🛠️ Abra Developer Tools (F12) → Console"
echo "6. 📤 Adicione um arquivo via drag-and-drop"
echo "7. 👀 Observe os logs no console e o contador na tela"

echo ""
echo "📊 O QUE VOCÊ DEVE VER:"
echo ""

echo "LOGS NO CONSOLE:"
echo "• 'File added to selectedFiles: arquivo.pdf 123456'"
echo "• 'updateFilesCounter called, selectedFiles.length: 1'"
echo "• 'Total bytes: 123456 Formatted: 0.12 MB'"
echo "• 'Counter should be visible now'"

echo ""
echo "NA TELA:"
echo "┌─────────────────────────────────────────────────────────┐"
echo "│ Máximo 5 arquivos de 10MB cada                         │"
echo "│                                                         │"
echo "│ 📄 1 arquivo(s)    💾 Total: 0.12 MB                   │"
echo "└─────────────────────────────────────────────────────────┘"

echo ""
echo "🚨 SE NÃO FUNCIONAR:"
echo "• Verifique os logs no console"
echo "• Confirme que não há erros JavaScript"
echo "• Teste com diferentes tipos de arquivo"
echo "• Verifique se IDs dos elementos existem na página"

echo ""
echo "🔍 ELEMENTOS HTML VERIFICADOS:"

# Verificar se os IDs existem no HTML
if grep -q "id=\"files-counter\"" /home/bruno/legisinc/resources/views/proposicoes/create.blade.php; then
    echo "✅ ID 'files-counter' existe"
else
    echo "❌ ID 'files-counter' NÃO existe"
fi

if grep -q "id=\"files-count\"" /home/bruno/legisinc/resources/views/proposicoes/create.blade.php; then
    echo "✅ ID 'files-count' existe"
else
    echo "❌ ID 'files-count' NÃO existe"
fi

if grep -q "id=\"total-size\"" /home/bruno/legisinc/resources/views/proposicoes/create.blade.php; then
    echo "✅ ID 'total-size' existe"
else
    echo "❌ ID 'total-size' NÃO existe"
fi

echo ""
echo "📝 CORREÇÕES APLICADAS:"
echo "✅ CSS: Removido !important que bloqueava exibição"
echo "✅ JS: Corrigida lógica de show/hide do contador"
echo "✅ Debug: Adicionados logs para troubleshooting"
echo "✅ Lógica: Mantida integridade dos eventos DropzoneJS"

echo ""
echo "🎉 PRONTO PARA TESTE COM DEBUG HABILITADO!"