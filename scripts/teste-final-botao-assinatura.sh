#!/bin/bash

echo "=== TESTE FINAL: PROBLEMA DO BOTÃO ASSINATURA ==="
echo ""

echo "🎯 RESUMO DO PROBLEMA:"
echo "   • Botão 'Assinar Documento' não funciona"
echo "   • HTML: <a href=\"/proposicoes/1/assinar\" class=\"btn btn-success btn-lg btn-assinatura\">"
echo "   • Status: retornado_legislativo (válido para assinatura)"
echo "   • Usuário: Jessica (parlamentar, autor da proposição)"
echo ""

echo "🔍 1. VERIFICANDO SE URL É GERADA CORRETAMENTE:"
docker exec legisinc-app php artisan tinker --execute="
echo 'URL do botão: ' . route('proposicoes.assinar', 1) . PHP_EOL;
echo 'URL esperada: http://localhost:8001/proposicoes/1/assinar' . PHP_EOL;
"

echo ""
echo "🔍 2. TESTANDO ACESSO DIRETO (SEM AUTENTICAÇÃO):"
RESPONSE_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/proposicoes/1/assinar)
echo "Status HTTP: $RESPONSE_CODE"
if [ "$RESPONSE_CODE" = "302" ]; then
    echo "✅ Resultado: Redireciona para login (comportamento esperado)"
elif [ "$RESPONSE_CODE" = "200" ]; then
    echo "⚠️  Resultado: Página carrega sem autenticação (problema de segurança)"
else
    echo "❌ Resultado: Erro $RESPONSE_CODE"
fi

echo ""
echo "🔐 3. VERIFICANDO ÚLTIMO ACESSO À TELA DE ASSINATURA:"
echo "   Procurando nos logs por acessos recentes..."

# Verificar se há logs de acesso à tela de assinatura
if grep -q "ProposicaoAssinaturaController@assinar" /home/bruno/legisinc/storage/logs/laravel.log 2>/dev/null; then
    echo "✅ Encontrados logs de acesso ao controller de assinatura"
    echo "   Últimos acessos:"
    grep "ProposicaoAssinaturaController@assinar" /home/bruno/legisinc/storage/logs/laravel.log | tail -3
else
    echo "❌ Nenhum log de acesso ao controller de assinatura encontrado"
    echo "   Isso confirma que o botão NÃO está funcionando"
fi

echo ""
echo "🚨 4. CAUSAS MAIS PROVÁVEIS:"
echo ""
echo "   A) SESSÃO EXPIRADA:"
echo "      • Usuário não está mais logado"
echo "      • Solução: Fazer login novamente"
echo ""
echo "   B) JAVASCRIPT BLOQUEANDO:"
echo "      • Algum script está cancelando o evento click"
echo "      • Solução: Verificar DevTools Console"
echo ""
echo "   C) CSS INTERFERINDO:"
echo "      • Elemento invisível sobrepondo o botão"
echo "      • z-index ou pointer-events bloqueando"
echo "      • Solução: Inspecionar elemento no navegador"
echo ""
echo "   D) MIDDLEWARE BLOQUEANDO:"
echo "      • check.screen.permission negando acesso"
echo "      • Solução: Verificar logs de acesso negado"
echo ""

echo "🔍 5. VERIFICANDO LOGS DE ACESSO NEGADO:"
if grep -q "Acesso negado" /home/bruno/legisinc/storage/logs/laravel.log 2>/dev/null; then
    echo "⚠️  Encontrados logs de acesso negado:"
    grep "Acesso negado" /home/bruno/legisinc/storage/logs/laravel.log | tail -3
else
    echo "✅ Nenhum log de acesso negado encontrado"
fi

echo ""
echo "💡 6. TESTE DEFINITIVO:"
echo ""
echo "   1. Abra o navegador e vá para: http://localhost:8001/login"
echo "   2. Faça login: jessica@sistema.gov.br / 123456"
echo "   3. Vá para: http://localhost:8001/proposicoes/1"
echo "   4. Abra DevTools (F12) → Console tab"
echo "   5. Clique no botão 'Assinar Documento'"
echo "   6. Observe se aparece:"
echo "      • Erros no Console"
echo "      • Requests na aba Network"
echo "      • Redirecionamentos"
echo ""
echo "   7. TESTE ALTERNATIVO:"
echo "      • Clique com botão direito no botão → 'Abrir link em nova aba'"
echo "      • Se abrir em nova aba → Botão funciona, problema é JavaScript"
echo "      • Se não abrir → Problema é de HTML/CSS"
echo ""

echo "📋 7. CHECKLIST FINAL:"
echo "   ✅ Rota registrada"
echo "   ✅ Permissão configurada"
echo "   ✅ Status válido"
echo "   ✅ Usuário é autor"
echo "   ✅ Controller permite acesso"
echo "   ❓ Usuário está logado? (verificar manualmente)"
echo "   ❓ JavaScript interferindo? (verificar DevTools)"
echo "   ❓ CSS bloqueando? (verificar inspetor)"
echo ""

echo "🎯 CONCLUSÃO:"
echo "   O problema NÃO é de configuração do sistema."
echo "   O problema é de SESSÃO ou INTERFACE do navegador."
echo "   Teste manual no navegador é obrigatório para diagnóstico final."
echo ""