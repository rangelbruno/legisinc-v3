#!/bin/bash

# Script de Deploy Otimizado para Legisinc
# =========================================

set -e # Exit on any error

echo "🚀 Iniciando deploy otimizado do Legisinc..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para logging
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] ⚠️  $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ❌ $1${NC}"
    exit 1
}

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    error "Arquivo artisan não encontrado. Execute este script na raiz do projeto."
fi

log "1. Entrando em modo de manutenção..."
php artisan down --render="errors::503" --secret="deploy-$(date +%s)"

# Função para limpar em caso de erro
cleanup() {
    log "Saindo do modo de manutenção..."
    php artisan up
}
trap cleanup EXIT

log "2. Atualizando dependências do Composer..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

log "3. Otimizando autoloader..."
composer dump-autoload --optimize --classmap-authoritative

log "4. Limpando caches antigos..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

log "5. Executando migrações..."
php artisan migrate --force

log "6. Criando caches otimizados..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

log "7. Otimizando aplicação..."
php artisan optimize

log "8. Aquecendo cache do sistema..."
php artisan performance:optimize --cache-warmup

log "9. Limpando arquivos temporários..."
php artisan performance:optimize --cleanup-pdfs

log "10. Configurando permissões..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

log "11. Otimizando banco de dados..."
php artisan performance:optimize --optimize-db

log "12. Gerando relatório de performance..."
php artisan performance:optimize --report

log "13. Verificando saúde do sistema..."

# Verificar serviços críticos
if ! php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1; then
    error "Falha na conexão com banco de dados"
fi

if ! php artisan tinker --execute="Cache::store('redis')->ping();" > /dev/null 2>&1; then
    warn "Redis não está disponível, cache em arquivo será usado"
fi

# Verificar OnlyOffice
if ! curl -s -f http://localhost:8080/healthcheck > /dev/null; then
    warn "OnlyOffice DocumentServer não está respondendo"
fi

log "14. Testando rotas principais..."
timeout 10s php artisan route:list --name=login > /dev/null || warn "Algumas rotas podem não estar funcionando"

log "15. Configurações finais..."

# Configurar logs
if [ ! -d "storage/logs" ]; then
    mkdir -p storage/logs
    chown www-data:www-data storage/logs
fi

# Rotacionar logs antigos
find storage/logs -name "*.log" -mtime +7 -delete

# Configurar opcache se disponível
if php -m | grep -q "Zend OPcache"; then
    log "OPcache detectado, reiniciando..."
    service php8.2-fpm reload || service php-fpm reload || true
fi

# Reiniciar workers de queue se existirem
if pgrep -f "queue:work" > /dev/null; then
    log "Reiniciando workers de queue..."
    php artisan queue:restart
fi

log "16. Saindo do modo de manutenção..."
php artisan up

# Remover trap
trap - EXIT

log "✅ Deploy concluído com sucesso!"

# Métricas finais
echo ""
echo "📊 MÉTRICAS DO DEPLOY:"
echo "====================="
echo "📁 Tamanho do storage: $(du -sh storage | cut -f1)"
echo "🗄️  Proposições no banco: $(php artisan tinker --execute="echo App\\Models\\Proposicao::count();")"
echo "🏛️  Templates ativos: $(php artisan tinker --execute="echo App\\Models\\TipoProposicaoTemplate::where('ativo', true)->count();")"
echo "👥 Usuários no sistema: $(php artisan tinker --execute="echo App\\Models\\User::count();")"

# Verificação final de performance
echo ""
echo "🔍 VERIFICAÇÃO DE PERFORMANCE:"
echo "=============================="

# Tempo de resposta da aplicação
START_TIME=$(date +%s%N)
curl -s -o /dev/null http://localhost:8001/login
END_TIME=$(date +%s%N)
RESPONSE_TIME=$(( (END_TIME - START_TIME) / 1000000 ))
echo "⚡ Tempo de resposta do login: ${RESPONSE_TIME}ms"

# Status dos caches
echo "💾 Status do cache de config: $([ -f bootstrap/cache/config.php ] && echo '✅ Ativo' || echo '❌ Inativo')"
echo "🛣️  Status do cache de rotas: $([ -f bootstrap/cache/routes-v7.php ] && echo '✅ Ativo' || echo '❌ Inativo')"
echo "👁️  Status do cache de views: $([ -d storage/framework/views ] && [ "$(ls -A storage/framework/views)" ] && echo '✅ Ativo' || echo '❌ Inativo')"

echo ""
echo "🎉 Sistema pronto para uso em modo otimizado!"
echo "🌐 Acesse: http://localhost:8001"
echo ""

# Log do deploy
echo "$(date): Deploy otimizado concluído" >> storage/logs/deploy.log