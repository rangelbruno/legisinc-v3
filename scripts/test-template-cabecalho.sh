#!/bin/bash

echo "🧪 Testando Sistema de Cabeçalho de Templates"
echo "=============================================="

# Verificar se o arquivo padrão existe
echo "1. Verificando arquivo padrão..."
if [ -f "/home/bruno/legisinc/public/template/cabecalho.png" ]; then
    echo "✅ Arquivo padrão existe: public/template/cabecalho.png"
else
    echo "❌ Arquivo padrão não encontrado!"
fi

# Verificar estrutura do banco
echo ""
echo "2. Verificando estrutura do banco..."
docker exec legisinc-postgres psql -U postgres -d legisinc -c "
SELECT 
    m.nome as modulo,
    s.nome as submodulo,
    c.label as campo,
    c.tipo_campo
FROM parametros_modulos m 
LEFT JOIN parametros_submodulos s ON m.id = s.modulo_id 
LEFT JOIN parametros_campos c ON s.id = c.submodulo_id 
WHERE m.nome = 'Templates'
ORDER BY c.ordem;
" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ Estrutura do banco verificada"
else
    echo "❌ Erro ao verificar banco"
fi

# Verificar se as rotas foram criadas
echo ""
echo "3. Verificando rotas..."
docker exec legisinc-app php artisan route:list | grep -E "(templates|cabecalho)" > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ Rotas configuradas:"
    docker exec legisinc-app php artisan route:list | grep -E "(templates|cabecalho)" | awk '{print "   " $2 " " $3}'
else
    echo "❌ Rotas não encontradas"
fi

# Verificar se o serviço está funcionando
echo ""
echo "4. Testando TemplateProcessorService..."
docker exec legisinc-app php -r "
try {
    \$service = app(App\Services\Template\TemplateProcessorService::class);
    \$config = \$service->deveAplicarCabecalho();
    echo '✅ TemplateProcessorService funcionando: ' . (\$config ? 'Cabeçalho ativo' : 'Cabeçalho inativo') . PHP_EOL;
} catch (Exception \$e) {
    echo '❌ Erro no TemplateProcessorService: ' . \$e->getMessage() . PHP_EOL;
}
" 2>/dev/null

# Verificar permissões da pasta template
echo ""
echo "5. Verificando permissões..."
if [ -d "/home/bruno/legisinc/public/template" ]; then
    permissions=$(ls -ld /home/bruno/legisinc/public/template | awk '{print $1}')
    echo "✅ Pasta template existe com permissões: $permissions"
else
    echo "❌ Pasta template não existe"
fi

# Verificar se storage está linkado
echo ""
echo "6. Verificando storage link..."
if [ -L "/home/bruno/legisinc/public/storage" ]; then
    echo "✅ Storage link configurado"
else
    echo "⚠️  Storage link não encontrado, executando..."
    docker exec legisinc-app php artisan storage:link
fi

echo ""
echo "🎯 Teste concluído!"
echo ""
echo "Para testar manualmente:"
echo "1. Acesse: http://localhost:8001/admin/parametros"
echo "2. Clique em 'Configurar' no módulo Templates"
echo "3. Faça upload de uma nova imagem"
echo "4. Salve as configurações"
echo ""
echo "📋 URLs importantes:"
echo "   - Parâmetros: /admin/parametros"
echo "   - Templates: /admin/parametros/templates/cabecalho"
echo "   - Upload: /images/upload/cabecalho"