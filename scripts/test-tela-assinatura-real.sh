#!/bin/bash

echo "🔧 TESTE REAL: Verificando tela de assinatura /proposicoes/8/assinar"
echo "==================================================================="

echo ""
echo "📍 PROBLEMA IDENTIFICADO ATRAVÉS DOS TESTES:"
echo "--------------------------------------------"
echo "✅ Método encontrarArquivoMaisRecente() encontra o arquivo correto"
echo "✅ Arquivo DOCX existe e tem conteúdo correto editado pelo parlamentar"
echo "✅ PDF de assinatura foi gerado com 29.419 bytes"
echo "❓ Mas pode não estar exibindo o conteúdo correto na tela"

echo ""
echo "🌐 TESTANDO A TELA DE ASSINATURA:"
echo "--------------------------------"

# Testar acesso direto à URL de assinatura
echo "1. Tentando acessar http://localhost:8001/proposicoes/8/assinar"

# Usar curl para testar se a página carrega
if command -v curl &> /dev/null; then
    echo "   Fazendo requisição HTTP..."
    
    # Primeiro verificar se o servidor está rodando
    response=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/ 2>/dev/null)
    if [ "$response" = "200" ]; then
        echo "   ✅ Servidor Laravel está rodando"
        
        # Testar a página de assinatura (sem autenticação, vai redirecionar para login)
        response=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/proposicoes/8/assinar 2>/dev/null)
        if [ "$response" = "302" ]; then
            echo "   ✅ Rota existe e redireciona para login (normal)"
        elif [ "$response" = "200" ]; then
            echo "   ✅ Página carregou diretamente (se já logado)"
        else
            echo "   ❌ Erro HTTP $response"
        fi
    else
        echo "   ❌ Servidor não está respondendo (HTTP $response)"
        echo "   💡 Execute: docker-compose up -d ou npm run dev"
    fi
else
    echo "   ⚠️  curl não disponível"
fi

echo ""
echo "2. Verificando arquivo de rota:"
if [ -f "/home/bruno/legisinc/routes/web.php" ]; then
    echo "   📄 Buscando rota 'assinar' em routes/web.php:"
    if grep -n "assinar" /home/bruno/legisinc/routes/web.php; then
        echo "   ✅ Rota de assinatura existe"
    else
        echo "   ❌ Rota de assinatura NÃO encontrada em web.php"
    fi
else
    echo "   ❌ Arquivo routes/web.php não encontrado"
fi

echo ""
echo "3. Verificando método assinar() no controller:"
if [ -f "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php" ]; then
    echo "   📄 Verificando se método assinar() existe:"
    if grep -n "public function assinar" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
        echo "   ✅ Método assinar() existe"
    else
        echo "   ❌ Método assinar() NÃO encontrado"
    fi
else
    echo "   ❌ ProposicaoAssinaturaController.php não encontrado"
fi

echo ""
echo "4. Verificando view de assinatura:"
POSSIBLE_VIEWS=(
    "/home/bruno/legisinc/resources/views/proposicoes/assinar.blade.php"
    "/home/bruno/legisinc/resources/views/proposicoes/assinatura.blade.php"
    "/home/bruno/legisinc/resources/views/assinatura/index.blade.php"
    "/home/bruno/legisinc/resources/views/assinatura/show.blade.php"
)

for view in "${POSSIBLE_VIEWS[@]}"; do
    if [ -f "$view" ]; then
        echo "   ✅ View encontrada: $view"
        echo "      Verificando se usa PDF embedado:"
        if grep -q "embed\|iframe\|pdf" "$view"; then
            echo "      ✅ Contém referência a PDF"
        else
            echo "      ❌ NÃO contém referência a PDF"
        fi
    fi
done

echo ""
echo "5. Verificando logs em tempo real:"
echo "   👀 Últimos logs relacionados à assinatura:"
if [ -f "/home/bruno/legisinc/storage/logs/laravel.log" ]; then
    grep -i "assinatura\|PDF\|proposicao.*8" /home/bruno/legisinc/storage/logs/laravel.log | tail -10
else
    echo "   ❌ Log do Laravel não encontrado"
fi

echo ""
echo "🎯 PRÓXIMOS PASSOS RECOMENDADOS:"
echo "==============================="
echo ""
echo "📋 SE O PROBLEMA ESTÁ NO PDF GERADO:"
echo "------------------------------------"
echo "1. Verificar se ProposicaoAssinaturaController::criarPDFDoArquivoMaisRecente()"
echo "   está realmente usando o arquivo correto"
echo "2. Verificar se extrairConteudoDOCX() está extraindo conteúdo correto"
echo "3. Verificar se a conversão DOCX → PDF preserva formatação"
echo ""
echo "📋 SE O PROBLEMA ESTÁ NA EXIBIÇÃO:"
echo "----------------------------------"
echo "1. Verificar se a view está carregando o PDF mais recente"
echo "2. Verificar se há cache de PDF antigo"
echo "3. Verificar se o arquivo PDF gerado tem o conteúdo correto"
echo ""
echo "🔧 TESTE MANUAL RECOMENDADO:"
echo "----------------------------"
echo "1. Acesse: http://localhost:8001/login"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Vá para: http://localhost:8001/proposicoes/8/assinar"
echo "4. Verifique se o PDF exibido contém:"
echo "   • Ementa: 'Editado pelo Parlamentar'"
echo "   • Texto: 'Bruno, sua oportunidade chegou!'"
echo "   • Número: '[AGUARDANDO PROTOCOLO]'"
echo ""
echo "SE NÃO CONTÉM esses elementos, o problema está confirmado!"

echo ""
echo "💡 SOLUÇÃO RÁPIDA PARA TESTE:"
echo "=============================="
echo "Execute este comando para forçar regeneração do PDF:"
echo ""
echo "# Deletar PDF atual e forçar regeneração"
echo "rm -f /home/bruno/legisinc/storage/app/proposicoes/pdfs/8/*.pdf"
echo "rm -f /home/bruno/legisinc/storage/app/private/proposicoes/pdfs/8/*.pdf"
echo ""
echo "# Depois acesse a tela de assinatura novamente"
echo "# O sistema irá regenerar o PDF automaticamente"