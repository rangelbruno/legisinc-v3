#!/bin/bash

echo "🔍 Teste da Correção do Erro 404 ao Visualizar PDF"
echo "==================================================="

echo ""
echo "1. Verificando PDFs existentes para proposição 3:"
ls -la /home/bruno/legisinc/storage/app/private/proposicoes/pdfs/3/*.pdf 2>/dev/null | head -3

echo ""
echo "2. Verificando campo arquivo_pdf_path no banco:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, status, arquivo_pdf_path FROM proposicoes WHERE id = 3;"

echo ""
echo "3. Verificando PDF assinado (prioridade):"
ls -la /home/bruno/legisinc/storage/app/private/proposicoes/pdfs/3/*assinado*.pdf 2>/dev/null

echo ""
echo "✅ CORREÇÃO IMPLEMENTADA:"
echo "   - Método servePDF() agora usa encontrarPDFMaisRecente()"
echo "   - Busca PDFs fisicamente quando arquivo_pdf_path está vazio"
echo "   - Prioriza PDFs assinados (*_assinado_*.pdf)"
echo "   - Retorna o PDF mais recente se não houver assinado"

echo ""
echo "📋 FLUXO DA SOLUÇÃO:"
echo "   1. Verifica arquivo_pdf_path no banco"
echo "   2. Se vazio, busca em: storage/app/private/proposicoes/pdfs/{id}/"
echo "   3. Prioriza PDFs assinados"
echo "   4. Retorna o mais recente se disponível"
echo "   5. Serve o arquivo encontrado"

echo ""
echo "🧪 TESTE NO NAVEGADOR:"
echo "   1. Acesse: http://localhost:8001/proposicoes/3"
echo "   2. Clique no botão 'Visualizar PDF'"
echo "   3. PDF deve abrir corretamente (sem erro 404)"

echo ""
echo "✅ Status esperado: PDF abre normalmente para proposições protocoladas!"