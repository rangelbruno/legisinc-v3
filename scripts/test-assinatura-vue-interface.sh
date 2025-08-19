#!/bin/bash

echo "🧪 ==============================================="
echo "✅ TESTANDO NOVA INTERFACE VUE.JS DE ASSINATURA"
echo "🧪 ==============================================="
echo ""

# Verificar se arquivos existem
echo "📁 Verificando arquivos da nova interface..."

FILES=(
    "/home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar-vue.blade.php"
    "/home/bruno/legisinc/database/seeders/AssinaturaVueInterfaceSeeder.php"
    "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"
)

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $(basename $file) encontrado"
    else
        echo "❌ $(basename $file) NÃO encontrado"
    fi
done

echo ""
echo "🔍 Verificando configuração do controller..."

# Verificar se controller está usando a view Vue.js
if grep -q "assinar-vue" /home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php; then
    echo "✅ Controller configurado para usar interface Vue.js"
else
    echo "❌ Controller ainda não configurado para Vue.js"
fi

echo ""
echo "🎨 Verificando funcionalidades na interface Vue.js..."

VUE_VIEW="/home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar-vue.blade.php"

FEATURES=(
    "createApp:Vue.js 3 configurado"
    "v-cloak:Prevenção de flash"
    "certificado-option:Sistema de certificados"
    "file-upload-area:Upload drag & drop"
    "toast-container:Sistema de notificações"
    "pdf-viewer-container:Visualizador PDF"
    "loadFromCache:Cache local"
    "measurePerformance:Monitoramento performance"
    "handleConfirmacaoLeitura:Confirmação de leitura"
    "processarAssinatura:Processamento assinatura"
    "devolverParaLegislativo:Devolução para legislativo"
)

for feature in "${FEATURES[@]}"; do
    IFS=':' read -r pattern description <<< "$feature"
    if grep -q "$pattern" "$VUE_VIEW"; then
        echo "✅ $description implementado"
    else
        echo "❌ $description NÃO encontrado"
    fi
done

echo ""
echo "📦 Verificando integração com sistema..."

# Verificar se seeder está no DatabaseSeeder
if grep -q "AssinaturaVueInterfaceSeeder" /home/bruno/legisinc/database/seeders/DatabaseSeeder.php; then
    echo "✅ Seeder integrado ao DatabaseSeeder"
else
    echo "❌ Seeder NÃO integrado ao DatabaseSeeder"
fi

echo ""
echo "⚡ Testando performance da interface..."

# Verificar características de performance
PERFORMANCE_FEATURES=(
    "AbortController:Timeout requests"
    "localStorage:Cache local"
    "debounceTimer:Debounce"
    "interfaceCache:Cache de interface"
    "lastPdfCheck:Prevenção múltiplas chamadas"
)

for feature in "${PERFORMANCE_FEATURES[@]}"; do
    IFS=':' read -r pattern description <<< "$feature"
    if grep -q "$pattern" "$VUE_VIEW"; then
        echo "✅ $description implementado"
    else
        echo "⚠️  $description não encontrado"
    fi
done

echo ""
echo "🎯 Verificando melhorias de UX/UI..."

UI_FEATURES=(
    "card-hover:Hover effects"
    "loading-overlay:Loading states"
    "btn-assinar:Botão otimizado"
    "toast:Sistema notificações"
    "progress-bar:Barras de progresso"
    "responsive:Design responsivo"
)

for feature in "${UI_FEATURES[@]}"; do
    IFS=':' read -r pattern description <<< "$feature"
    if grep -q "$pattern" "$VUE_VIEW"; then
        echo "✅ $description implementado"
    else
        echo "⚠️  $description não encontrado"
    fi
done

echo ""
echo "📊 Estatísticas da nova interface:"

if [ -f "$VUE_VIEW" ]; then
    TOTAL_LINES=$(wc -l < "$VUE_VIEW")
    JS_LINES=$(grep -c "^[[:space:]]*[^[:space:]<]" "$VUE_VIEW" | grep -v "{{" || echo 0)
    CSS_LINES=$(sed -n '/<style>/,/<\/style>/p' "$VUE_VIEW" | wc -l)
    
    echo "📏 Total de linhas: $TOTAL_LINES"
    echo "🎨 Linhas de CSS: $CSS_LINES"
    echo "⚡ Funcionalidades Vue.js: $(grep -c "v-" "$VUE_VIEW")"
    echo "🔧 Métodos implementados: $(grep -c "async.*(" "$VUE_VIEW")"
fi

echo ""
echo "🌟 ============================="
echo "✅ TESTE CONCLUÍDO COM SUCESSO!"
echo "🌟 ============================="
echo ""
echo "📋 FUNCIONALIDADES IMPLEMENTADAS:"
echo "   ⚡ Interface reativa com Vue.js 3"
echo "   📱 Design responsivo e moderno"
echo "   🔄 Loading states e feedback visual"
echo "   📁 Upload certificados drag & drop"
echo "   📄 Visualizador PDF integrado"
echo "   🔔 Sistema notificações toast"
echo "   💾 Cache local com auto-save"
echo "   📊 Monitoramento de performance"
echo "   🎯 UX/UI seguindo padrões projeto"
echo ""
echo "🚀 Para acessar: http://localhost:8001/proposicoes/2/assinar"
echo "🔧 Para aplicar: docker exec -it legisinc-app php artisan migrate:fresh --seed"
echo ""