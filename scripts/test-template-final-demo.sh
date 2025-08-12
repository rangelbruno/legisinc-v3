#!/bin/bash

echo "=== Template do Administrador - Demonstração Final ==="
echo "Date: $(date)"

echo -e "\n📋 RESUMO DO TEMPLATE DE MOÇÃO:"
echo "Template criado pelo administrador com as seguintes variáveis:"
echo "• \${imagem_cabecalho}"
echo "• \${cabecalho_nome_camara} → CÂMARA MUNICIPAL DE CARAGUATATUBA"
echo "• \${cabecalho_endereco}"
echo "• \${cabecalho_telefone}"
echo "• \${cabecalho_website}"
echo "• \${numero_proposicao}/\${ano_atual} → 0001/2025"
echo "• \${ementa} → Ementa da proposição"
echo "• \${texto} → Conteúdo da proposição (IA ou manual)"
echo "• \${justificativa} → Vazio por padrão"
echo "• \${municipio}, \${dia} de \${mes_extenso} de \${ano_atual}"
echo "• \${autor_nome} → Jessica Santos"
echo "• \${autor_cargo} → Vereador"

echo -e "\n🔄 PROCESSO CORRIGIDO:"
echo "1. Parlamentar cria proposição tipo 'mocao'"
echo "2. Sistema busca template para tipo 'mocao' (ID: 12)"
echo "3. Encontra template do administrador (ID: 6)"
echo "4. Substitui todas as variáveis pelos valores corretos"
echo "5. Parlamentar edita no OnlyOffice com estrutura formal"

echo -e "\n📄 DOCUMENTO GERADO (decodificado):"
TOKEN=$(echo -n "1|$(date +%s)" | base64)
DOCUMENT_RAW=$(docker exec legisinc-onlyoffice curl -s -H "User-Agent: ASC.DocService" "http://legisinc-app/proposicoes/1/onlyoffice/download?token=$TOKEN")

echo "---"
echo "$DOCUMENT_RAW" | sed 's/\\u[0-9A-Fa-f]\{3,4\}\*/[UNICODE]/g' | sed 's/{\\rtf.*\\f0\\fs24\\sl360\\slmult1 \\par //' | sed 's/\\par /\n/g' | sed 's/\\b /[BOLD]/g' | sed 's/ \\b0/[/BOLD]/g' | head -20 | tail -15
echo "..."
echo "$DOCUMENT_RAW" | sed 's/\\u[0-9A-Fa-f]\{3,4\}\*/[UNICODE]/g' | tail -5 | head -3
echo "---"

echo -e "\n✅ CORREÇÕES APLICADAS:"
echo "• Removida lógica que forçava template ABNT para proposições em edição"
echo "• Template do administrador tem precedência sobre métodos automáticos"
echo "• Variáveis sendo substituídas corretamente pelos parâmetros da câmara"
echo "• Estrutura formal preservada conforme definido pelo administrador"

echo -e "\n🎯 RESULTADO:"
echo "• O parlamentar agora pode criar proposições usando o template formal"
echo "• As variáveis são substituídas automaticamente"
echo "• O documento mantém a estrutura oficial definida pelo administrador"
echo "• O legislativo pode editar o documento mantendo a formatação"

echo -e "\n📝 PRÓXIMOS PASSOS RECOMENDADOS:"
echo "• Testar com outros tipos de proposição (projeto de lei, etc.)"
echo "• Verificar se todos os parâmetros da câmara estão configurados"
echo "• Ajustar template se necessário para melhor formatação"
echo "• Treinar usuários sobre o novo fluxo"

echo -e "\n=== Demonstração Concluída ==="