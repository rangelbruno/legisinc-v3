# ⚙️ Exemplos de Configuração para Performance

## 📋 Configurações de Ambiente

### .env Otimizado para Produção

```bash
# Aplicação
APP_NAME="Sistema Legisinc"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://legisinc.exemplo.gov.br

# Cache (Redis recomendado)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0

# Banco de Dados (PostgreSQL recomendado)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=legisinc
DB_USERNAME=legisinc_user
DB_PASSWORD=strong_password_here

# Performance Settings
TELESCOPE_ENABLED=false
DEBUGBAR_ENABLED=false
LOG_CHANNEL=daily
LOG_LEVEL=warning

# PDF Optimization
PDF_TIMEOUT=60
LIBREOFFICE_TIMEOUT=30
PDF_COMPRESSION_LEVEL=screen

# OnlyOffice
ONLYOFFICE_URL=http://localhost:8080
ONLYOFFICE_JWT_SECRET=your-jwt-secret
ONLYOFFICE_JWT_ENABLED=false

# Performance Monitoring
PERFORMANCE_MONITORING=true
SLOW_QUERY_THRESHOLD=1000
MEMORY_LIMIT_WARNING=200
```

### .env Otimizado para Desenvolvimento

```bash
# Aplicação
APP_NAME="Legisinc Dev"
APP_ENV=local
APP_KEY=base64:your-dev-key-here
APP_DEBUG=true
APP_URL=http://localhost:8001

# Cache (File para desenvolvimento)
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

# Banco de Dados
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=legisinc_dev
DB_USERNAME=postgres
DB_PASSWORD=123456

# Debug Tools
TELESCOPE_ENABLED=true
DEBUGBAR_ENABLED=true
LOG_CHANNEL=stack
LOG_LEVEL=debug

# Performance Debugging
DB_LOG_QUERIES=true
PERFORMANCE_MONITORING=true
SLOW_QUERY_THRESHOLD=100
```

---

## 🐘 Configuração do PostgreSQL

### postgresql.conf Otimizado

```ini
# postgresql.conf

# Connection Settings
max_connections = 100
shared_buffers = 256MB
effective_cache_size = 1GB
work_mem = 4MB
maintenance_work_mem = 64MB

# Checkpoint Settings
wal_buffers = 16MB
checkpoint_completion_target = 0.9
checkpoint_timeout = 10min
max_wal_size = 1GB
min_wal_size = 80MB

# Query Optimization
random_page_cost = 1.1
effective_io_concurrency = 200

# Logging (para desenvolvimento)
log_statement = 'all'
log_duration = on
log_min_duration_statement = 1000  # Log queries > 1s

# Performance Monitoring
shared_preload_libraries = 'pg_stat_statements'
pg_stat_statements.track = all
pg_stat_statements.max = 10000
```

### pg_hba.conf

```ini
# pg_hba.conf
local   all             postgres                                peer
local   all             all                                     md5
host    all             all             127.0.0.1/32            md5
host    all             all             ::1/128                 md5
```

---

## 🔴 Configuração do Redis

### redis.conf Otimizado

```ini
# redis.conf

# Network
bind 127.0.0.1
port 6379
timeout 0
tcp-keepalive 300

# Memory Management
maxmemory 512mb
maxmemory-policy allkeys-lru
maxmemory-samples 5

# Persistence
save 900 1
save 300 10
save 60 10000
stop-writes-on-bgsave-error yes
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb

# Append Only File
appendonly yes
appendfilename "appendonly.aof"
appendfsync everysec
no-appendfsync-on-rewrite no
auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb

# Slow Log
slowlog-log-slower-than 10000
slowlog-max-len 128

# Client Output Buffer
client-output-buffer-limit normal 0 0 0
client-output-buffer-limit replica 256mb 64mb 60
client-output-buffer-limit pubsub 32mb 8mb 60
```

---

## 🌐 Configuração do Nginx

### nginx.conf Principal

```nginx
user www-data;
worker_processes auto;
pid /run/nginx.pid;
include /etc/nginx/modules-enabled/*.conf;

events {
    worker_connections 1024;
    use epoll;
    multi_accept on;
}

http {
    # Basic Settings
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    server_tokens off;

    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # SSL Settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;

    # Logging Settings
    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for" '
                    '$request_time $upstream_response_time';

    access_log /var/log/nginx/access.log main;
    error_log /var/log/nginx/error.log;

    # Gzip Settings
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/javascript
        application/xml
        application/json
        application/rss+xml
        application/atom+xml
        image/svg+xml;

    # Rate Limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
    limit_req_zone $binary_remote_addr zone=login:10m rate=1r/s;

    # Include server blocks
    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;
}
```

### Site Específico do Legisinc

```nginx
# /etc/nginx/sites-available/legisinc
server {
    listen 80;
    listen [::]:80;
    server_name legisinc.exemplo.gov.br;
    root /var/www/html/legisinc/public;
    index index.php index.html;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Static Assets with Long Cache
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary Accept-Encoding;
        
        # Compressão específica para assets
        gzip_static on;
    }

    # PDF Cache
    location ~* \.pdf$ {
        expires 1h;
        add_header Cache-Control "public";
        
        # Headers para download
        add_header Content-Disposition 'inline';
        add_header X-Content-Type-Options nosniff;
    }

    # API Rate Limiting
    location /api/ {
        limit_req zone=api burst=20 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Login Rate Limiting
    location /login {
        limit_req zone=login burst=5 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    # OnlyOffice Proxy
    location /onlyoffice/ {
        proxy_pass http://localhost:8080/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # Timeouts para documentos grandes
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
    }

    # PHP Processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Buffers otimizados
        fastcgi_buffer_size 32k;
        fastcgi_buffers 16 32k;
        fastcgi_busy_buffers_size 64k;
        
        # Timeouts
        fastcgi_connect_timeout 60s;
        fastcgi_send_timeout 180s;
        fastcgi_read_timeout 180s;
        
        # Cache para assets PHP (opcache)
        fastcgi_cache_valid 200 301 302 1h;
        fastcgi_cache_valid 404 1m;
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ /(vendor|storage|bootstrap|database|tests)/ {
        deny all;
    }

    # Main location
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Error pages
    error_page 404 /index.php;
    error_page 500 502 503 504 /50x.html;
    
    location = /50x.html {
        root /var/www/html;
    }
}
```

---

## 🐘 Configuração do PHP-FPM

### Pool Principal

```ini
; /etc/php/8.2/fpm/pool.d/legisinc.conf
[legisinc]
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

; Pool Configuration
pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 1000

; Process Management
pm.process_idle_timeout = 60s
pm.max_requests = 1000

; Performance Settings
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 120
php_admin_value[max_input_time] = 60
php_admin_value[max_input_vars] = 3000
php_admin_value[post_max_size] = 100M
php_admin_value[upload_max_filesize] = 100M

; OPcache Optimization
php_admin_value[opcache.enable] = 1
php_admin_value[opcache.memory_consumption] = 256
php_admin_value[opcache.interned_strings_buffer] = 16
php_admin_value[opcache.max_accelerated_files] = 20000
php_admin_value[opcache.validate_timestamps] = 0
php_admin_value[opcache.save_comments] = 0
php_admin_value[opcache.fast_shutdown] = 1

; Error Logging
php_admin_value[log_errors] = on
php_admin_value[error_log] = /var/log/php/legisinc-error.log
```

### php.ini Otimizado

```ini
; php.ini optimizations

[PHP]
; Core Settings
max_execution_time = 120
max_input_time = 60
memory_limit = 256M
post_max_size = 100M
upload_max_filesize = 100M
max_file_uploads = 20

; Error Reporting (Production)
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php/errors.log

; Session Optimization
session.save_handler = redis
session.save_path = "tcp://127.0.0.1:6379"
session.gc_maxlifetime = 7200
session.cookie_lifetime = 0
session.cookie_secure = 1
session.cookie_httponly = 1

; OPcache
[opcache]
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=0
opcache.fast_shutdown=1
opcache.enable_file_override=1

; Realpath Cache
realpath_cache_size = 4096K
realpath_cache_ttl = 600

; Security
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
```

---

## 🗄️ Scripts de Inicialização

### Systemd Service para Cache Warmup

```ini
# /etc/systemd/system/legisinc-cache-warmup.service
[Unit]
Description=Legisinc Cache Warmup
After=redis.service postgresql.service

[Service]
Type=oneshot
User=www-data
WorkingDirectory=/var/www/html/legisinc
ExecStart=/usr/bin/php artisan performance:optimize --cache-warmup
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
```

### Timer para Limpeza Automática

```ini
# /etc/systemd/system/legisinc-cleanup.timer
[Unit]
Description=Legisinc Daily Cleanup
Requires=legisinc-cleanup.service

[Timer]
OnCalendar=daily
Persistent=true

[Install]
WantedBy=timers.target

# /etc/systemd/system/legisinc-cleanup.service
[Unit]
Description=Legisinc Cleanup Service

[Service]
Type=oneshot
User=www-data
WorkingDirectory=/var/www/html/legisinc
ExecStart=/usr/bin/php artisan performance:optimize --cleanup-pdfs
StandardOutput=journal
StandardError=journal
```

### Script de Backup com Performance

```bash
#!/bin/bash
# /usr/local/bin/legisinc-backup.sh

set -e

BACKUP_DIR="/backup/legisinc"
DATE=$(date +%Y%m%d_%H%M%S)
APP_DIR="/var/www/html/legisinc"

echo "🚀 Iniciando backup otimizado do Legisinc..."

# Criar diretório de backup
mkdir -p "$BACKUP_DIR/$DATE"

# Backup do banco com compressão
echo "📄 Backup do banco de dados..."
pg_dump -h localhost -U legisinc_user legisinc | gzip > "$BACKUP_DIR/$DATE/database.sql.gz"

# Backup de arquivos importantes (excluindo cache)
echo "📁 Backup de arquivos..."
tar -czf "$BACKUP_DIR/$DATE/files.tar.gz" \
    --exclude="$APP_DIR/storage/framework/cache/*" \
    --exclude="$APP_DIR/storage/framework/sessions/*" \
    --exclude="$APP_DIR/storage/framework/views/*" \
    --exclude="$APP_DIR/storage/logs/*" \
    --exclude="$APP_DIR/node_modules" \
    --exclude="$APP_DIR/vendor" \
    "$APP_DIR/storage" \
    "$APP_DIR/.env" \
    "$APP_DIR/public"

# Backup de configurações do sistema
echo "⚙️ Backup de configurações..."
tar -czf "$BACKUP_DIR/$DATE/configs.tar.gz" \
    /etc/nginx/sites-available/legisinc \
    /etc/php/8.2/fpm/pool.d/legisinc.conf \
    /etc/redis/redis.conf \
    /etc/postgresql/*/main/postgresql.conf

# Limpeza de backups antigos (manter 7 dias)
find "$BACKUP_DIR" -type d -mtime +7 -exec rm -rf {} +

echo "✅ Backup concluído: $BACKUP_DIR/$DATE"
```

---

## 📊 Configuração de Monitoramento

### Script de Health Check

```bash
#!/bin/bash
# /usr/local/bin/legisinc-health.sh

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo "🏥 Health Check do Sistema Legisinc"
echo "=================================="

# Verificar serviços básicos
echo -n "🔴 Redis: "
if redis-cli ping > /dev/null 2>&1; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}ERRO${NC}"
fi

echo -n "🐘 PostgreSQL: "
if pg_isready -h localhost -p 5432 > /dev/null 2>&1; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}ERRO${NC}"
fi

echo -n "🌐 Nginx: "
if systemctl is-active --quiet nginx; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}ERRO${NC}"
fi

echo -n "🐘 PHP-FPM: "
if systemctl is-active --quiet php8.2-fpm; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}ERRO${NC}"
fi

# Verificar aplicação
echo -n "🚀 Aplicação: "
if curl -s -f http://localhost:8001/health > /dev/null; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}ERRO${NC}"
fi

# Métricas de performance
echo ""
echo "📊 Métricas de Performance:"
echo "=========================="

# Cache hit rate
CACHE_HIT=$(php -r "
try {
    \$redis = new Redis();
    \$redis->connect('127.0.0.1', 6379);
    \$info = \$redis->info('stats');
    \$hits = \$info['keyspace_hits'] ?? 0;
    \$misses = \$info['keyspace_misses'] ?? 0;
    \$total = \$hits + \$misses;
    echo \$total > 0 ? round((\$hits / \$total) * 100, 2) : 0;
} catch (Exception \$e) {
    echo 'N/A';
}
")
echo "💾 Cache Hit Rate: ${CACHE_HIT}%"

# Uso de memória
MEMORY_USAGE=$(free | grep Mem | awk '{printf("%.1f", $3/$2 * 100.0)}')
echo "🧠 Uso de Memória: ${MEMORY_USAGE}%"

# Espaço em disco
DISK_USAGE=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
echo "💽 Uso de Disco: ${DISK_USAGE}%"

# Connections PostgreSQL
PG_CONNECTIONS=$(psql -h localhost -U legisinc_user -d legisinc -t -c "SELECT count(*) FROM pg_stat_activity;" 2>/dev/null || echo "N/A")
echo "🔗 Conexões PostgreSQL: ${PG_CONNECTIONS}"

echo ""
echo "✅ Health Check concluído!"
```

---

**Arquivo de configuração atualizado em:** $(date +'%d/%m/%Y %H:%M:%S')  
**Versão:** 1.0 Configuration Examples  
**Compatível com:** Ubuntu 20.04+, PHP 8.2+, PostgreSQL 15+, Redis 6+