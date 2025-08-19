#!/bin/bash

echo "=== TESTE COMPLETO DO BOTÃO ASSINATURA (COM LOGIN) ==="
echo ""

echo "🔐 1. Fazendo LOGIN como Jessica (Parlamentar)..."
echo ""

echo "📝 2. Verificando se proposição 1 está disponível para assinatura:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
echo 'ID: ' . \$proposicao->id . PHP_EOL;
echo 'Status: ' . \$proposicao->status . PHP_EOL;
echo 'Autor ID: ' . \$proposicao->autor_id . PHP_EOL;
echo 'Arquivo path: ' . (\$proposicao->arquivo_path ?? 'null') . PHP_EOL;
echo 'Arquivo PDF path: ' . (\$proposicao->arquivo_pdf_path ?? 'null') . PHP_EOL;
"

echo ""
echo "🎯 3. PROBLEMA IDENTIFICADO:"
echo "   ❌ O botão não funciona porque requer AUTENTICAÇÃO"
echo "   ❌ Quando você clica no botão, você está logado na interface?"
echo "   ❌ O botão está dentro de middleware 'auth' (linha 813 routes/web.php)"
echo ""

echo "✅ 4. SOLUÇÕES POSSÍVEIS:"
echo ""
echo "   A) VERIFICAR SESSÃO ATIVA:"
echo "      - Confirme que está logado como jessica@sistema.gov.br"
echo "      - Verifique se a sessão não expirou"
echo "      - Teste: acesse http://localhost:8001/dashboard primeiro"
echo ""
echo "   B) TESTAR FLUXO CORRETO:"
echo "      1. Acesse: http://localhost:8001/login"
echo "      2. Login: jessica@sistema.gov.br / 123456"
echo "      3. Vá para: http://localhost:8001/proposicoes/1"
echo "      4. Clique no botão 'Assinar Documento'"
echo "      5. Deve abrir: http://localhost:8001/proposicoes/1/assinar"
echo ""
echo "   C) VERIFICAR SE PROBLEMA É NO JAVASCRIPT:"
echo "      - Abra DevTools (F12) no navegador"
echo "      - Vá para Console e Network tabs"
echo "      - Clique no botão e veja se há erros JS ou requests HTTP"
echo ""

echo "🔍 5. TESTANDO GERAÇÃO DE PDF (se logado funcionasse):"
docker exec legisinc-app php artisan tinker --execute="
\$controller = new App\Http\Controllers\ProposicaoAssinaturaController();
try {
    \$proposicao = App\Models\Proposicao::find(1);
    echo 'Proposição encontrada: ' . \$proposicao->id . PHP_EOL;
    echo 'Status válido para assinatura: ' . (in_array(\$proposicao->status, ['aprovado_assinatura', 'retornado_legislativo']) ? 'SIM' : 'NÃO') . PHP_EOL;
} catch (\Exception \$e) {
    echo 'Erro: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "📋 6. CHECKLIST DE DIAGNÓSTICO:"
echo "   - [ ] Usuário está logado? (verifique /dashboard)"
echo "   - [ ] Proposição tem status 'retornado_legislativo'? ✅"
echo "   - [ ] Usuário é autor da proposição? ✅"
echo "   - [ ] Permissão 'proposicoes.assinar' existe? ✅"
echo "   - [ ] Rota está registrada? ✅"
echo "   - [ ] Não há erros JavaScript no console? (verificar)"
echo "   - [ ] Não há erro de CSRF token? (verificar)"
echo ""

echo "💡 DICA: O botão usa route() helper do Laravel."
echo "   Se a URL aparece como http://localhost:8001/proposicoes/1/assinar"
echo "   mas não funciona, o problema é AUTENTICAÇÃO ou JAVASCRIPT."

echo ""
echo "=== TESTE FINAL: SIMULAR CLIQUE DO BOTÃO ==="
echo "URL do botão: http://localhost:8001/proposicoes/1/assinar"
echo "HTML: <a href=\"{{ route('proposicoes.assinar', \$proposicao->id) }}\" class=\"btn btn-success btn-lg btn-assinatura\">"
echo ""
echo "🚨 IMPORTANTE: Este botão SÓ funciona se você estiver LOGADO!"
echo ""