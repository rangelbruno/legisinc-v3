# 🔧 Guia Técnico de Performance - Legisinc

## 🏗️ Arquitetura de Performance

### Stack Tecnológico Otimizado

```yaml
Application Layer:
  - PHP 8.2+ (OPcache, JIT)
  - Laravel 11.x (Optimized)
  - Nginx (Gzip, Caching)

Cache Layer:
  - Redis (Primary Cache)
  - File Cache (Fallback)
  - OPcache (PHP Bytecode)

Database Layer:
  - PostgreSQL 15+ (Optimized)
  - Connection Pooling
  - Query Cache

Storage Layer:
  - SSD Storage
  - Compressed PDFs
  - Optimized File Structure
```

---

## 📊 Implementação Detalhada

### 1. Cache Service - Estratégia de Cache Distribuído

#### Cache Keys Strategy

```php
// Padrão de nomenclatura
"entity_type_{id}_{hash}"

// Exemplos
"proposicao_full_123_a1b2c3"
"templates_tipo_5_x9y8z7"
"stats_usuario_42_cached"
```

#### TTL Hierarchy

```php
const TTL_HIERARCHY = [
    'static_data'     => 86400,  // 24h - Dados que raramente mudam
    'user_data'       => 3600,   // 1h  - Dados específicos do usuário
    'dynamic_data'    => 900,    // 15m - Dados que mudam frequentemente
    'volatile_data'   => 300,    // 5m  - Dados muito voláteis
];
```

#### Cache Warming Strategy

```php
public function warmupCriticalPaths(): void
{
    // 1. Pré-carregar tipos de proposição mais usados
    $topTypes = DB::table('proposicoes')
        ->select('tipo', DB::raw('COUNT(*) as count'))
        ->groupBy('tipo')
        ->orderByDesc('count')
        ->limit(5)
        ->get();

    foreach ($topTypes as $type) {
        $this->warmupTemplatesForType($type->tipo);
    }

    // 2. Pré-carregar usuários ativos
    $activeUsers = User::whereNotNull('last_login_at')
        ->where('last_login_at', '>', now()->subDays(7))
        ->limit(20)
        ->get();

    foreach ($activeUsers as $user) {
        $this->warmupUserData($user->id);
    }
}
```

### 2. Query Optimization - Advanced Techniques

#### Smart Eager Loading

```php
// Context-aware eager loading
public function getProposicoesWithContext(string $context): Collection
{
    $baseQuery = Proposicao::query();

    switch ($context) {
        case 'dashboard':
            return $baseQuery->with([
                'autor:id,name',
                'tipoProposicao:id,tipo'
            ])->select(['id', 'tipo', 'ementa', 'status', 'autor_id'])->get();

        case 'full_view':
            return $baseQuery->with([
                'autor:id,name,email,cargo_atual',
                'revisor:id,name,email',
                'tipoProposicao:id,tipo,nome,codigo',
                'template:id,nome,arquivo_path',
                'tramitacoes' => function($q) {
                    $q->latest()->limit(10);
                }
            ])->get();

        case 'export':
            return $baseQuery->with('autor:id,name')->select([
                'id', 'tipo', 'ementa', 'status', 'created_at', 'autor_id'
            ])->get();
    }
}
```

#### Database Connection Optimization

```php
// config/database.php
'pgsql' => [
    'driver' => 'pgsql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'search_path' => 'public',
    'sslmode' => 'prefer',
    'options' => [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ],
    // Connection pooling
    'pool' => [
        'min_connections' => 5,
        'max_connections' => 20,
        'connect_timeout' => 60,
        'wait_timeout' => 60,
        'heartbeat' => -1,
        'max_idle_time' => 60,
    ],
],
```

#### Index Optimization

```sql
-- Índices recomendados para performance
CREATE INDEX CONCURRENTLY idx_proposicoes_author_status 
ON proposicoes(autor_id, status) 
WHERE status IN ('rascunho', 'enviado_legislativo', 'aprovado_assinatura');

CREATE INDEX CONCURRENTLY idx_proposicoes_status_created 
ON proposicoes(status, created_at DESC) 
WHERE status != 'excluido';

CREATE INDEX CONCURRENTLY idx_proposicoes_tipo_status 
ON proposicoes(tipo, status) 
WHERE status IN ('protocolado', 'aprovado');

-- Índice para busca textual
CREATE INDEX CONCURRENTLY idx_proposicoes_search 
ON proposicoes USING gin(to_tsvector('portuguese', ementa || ' ' || COALESCE(conteudo, '')));
```

### 3. PDF Optimization - Advanced Processing

#### Multi-format PDF Pipeline

```php
class AdvancedPDFProcessor
{
    public function generateOptimizedPDF(Proposicao $proposicao): string
    {
        // 1. Tentar conversão nativa (mais rápida)
        if ($path = $this->tryNativeConversion($proposicao)) {
            return $path;
        }

        // 2. Tentar LibreOffice (melhor qualidade)
        if ($path = $this->tryLibreOfficeConversion($proposicao)) {
            return $path;
        }

        // 3. Fallback para DomPDF (sempre funciona)
        return $this->generateDomPDF($proposicao);
    }

    private function tryNativeConversion(Proposicao $proposicao): ?string
    {
        if (!$proposicao->arquivo_path) return null;

        $inputPath = $this->findSourceFile($proposicao->arquivo_path);
        if (!$inputPath || !str_ends_with($inputPath, '.docx')) return null;

        // Usar conversão direta se disponível
        if ($this->isUnoconvAvailable()) {
            return $this->convertWithUnoconv($inputPath, $proposicao);
        }

        return null;
    }

    private function convertWithUnoconv(string $input, Proposicao $proposicao): string
    {
        $outputDir = storage_path("app/proposicoes/pdfs/{$proposicao->id}");
        $outputFile = "proposicao_{$proposicao->id}_optimized.pdf";
        
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $command = sprintf(
            'timeout 30s unoconv -f pdf -o %s %s 2>/dev/null',
            escapeshellarg($outputDir . '/' . $outputFile),
            escapeshellarg($input)
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($outputDir . '/' . $outputFile)) {
            $this->optimizePDFSize($outputDir . '/' . $outputFile);
            return "proposicoes/pdfs/{$proposicao->id}/{$outputFile}";
        }

        throw new \Exception('Falha na conversão com unoconv');
    }
}
```

#### PDF Compression Strategies

```php
private function optimizePDFSize(string $pdfPath): void
{
    $strategies = [
        'ghostscript_screen'   => $this->getGhostscriptCommand('screen'),    // Menor tamanho
        'ghostscript_ebook'    => $this->getGhostscriptCommand('ebook'),     // Balanceado  
        'ghostscript_printer'  => $this->getGhostscriptCommand('printer'),   // Alta qualidade
    ];

    $originalSize = filesize($pdfPath);
    $bestStrategy = null;
    $bestSize = $originalSize;

    foreach ($strategies as $name => $command) {
        $tempPath = $pdfPath . '.temp_' . $name;
        $fullCommand = sprintf($command, $tempPath, $pdfPath);
        
        exec($fullCommand, $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($tempPath)) {
            $newSize = filesize($tempPath);
            
            // Usar se for significativamente menor mas não muito pequeno
            if ($newSize < $bestSize && $newSize > ($originalSize * 0.1)) {
                $bestStrategy = $tempPath;
                $bestSize = $newSize;
            }
            
            if ($tempPath !== $bestStrategy) {
                unlink($tempPath);
            }
        }
    }

    if ($bestStrategy && $bestSize < $originalSize) {
        rename($bestStrategy, $pdfPath);
        
        Log::info('PDF comprimido', [
            'original_size' => $originalSize,
            'compressed_size' => $bestSize,
            'reduction' => round((($originalSize - $bestSize) / $originalSize) * 100, 2) . '%'
        ]);
    }
}
```

### 4. Memory Management

#### Memory-Efficient Collections

```php
class MemoryEfficientProposicaoService
{
    public function processLargeBatch(int $batchSize = 100): void
    {
        // Usar chunks para processar grandes volumes
        Proposicao::chunk($batchSize, function ($proposicoes) {
            foreach ($proposicoes as $proposicao) {
                $this->processProposicao($proposicao);
                
                // Limpar relacionamentos para liberar memória
                $proposicao->unsetRelations();
            }
            
            // Forçar coleta de lixo a cada batch
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        });
    }

    public function exportLargeDataset(): Generator
    {
        // Usar generator para não carregar tudo na memória
        $query = Proposicao::with('autor:id,name')->orderBy('id');
        
        foreach ($query->cursor() as $proposicao) {
            yield [
                'id' => $proposicao->id,
                'tipo' => $proposicao->tipo,
                'ementa' => $proposicao->ementa,
                'autor' => $proposicao->autor->name,
                'created_at' => $proposicao->created_at->format('Y-m-d'),
            ];
        }
    }
}
```

#### Memory Monitoring

```php
class MemoryProfiler
{
    private array $checkpoints = [];

    public function checkpoint(string $label): void
    {
        $this->checkpoints[$label] = [
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'time' => microtime(true),
        ];
    }

    public function report(): array
    {
        $report = [];
        $previous = null;

        foreach ($this->checkpoints as $label => $data) {
            $report[$label] = [
                'memory_mb' => round($data['memory_usage'] / 1024 / 1024, 2),
                'peak_mb' => round($data['peak_memory'] / 1024 / 1024, 2),
            ];

            if ($previous) {
                $report[$label]['diff_mb'] = round(
                    ($data['memory_usage'] - $previous['memory_usage']) / 1024 / 1024, 2
                );
                $report[$label]['time_diff'] = round(
                    ($data['time'] - $previous['time']) * 1000, 2
                ) . 'ms';
            }

            $previous = $data;
        }

        return $report;
    }
}
```

---

## 🚀 Advanced Optimizations

### 1. Redis Cluster Configuration

```yaml
# redis-cluster.conf
cluster-enabled yes
cluster-config-file nodes.conf
cluster-node-timeout 5000
appendonly yes
save 900 1
save 300 10
save 60 10000

# Memory optimization
maxmemory 2gb
maxmemory-policy allkeys-lru
tcp-keepalive 60
```

### 2. Nginx Optimization

```nginx
# nginx.conf para performance
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php;

    # Compressão
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/javascript
        application/xml
        application/json;

    # Cache de assets estáticos
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary Accept-Encoding;
    }

    # Cache de PDFs
    location ~* \.pdf$ {
        expires 1h;
        add_header Cache-Control "public";
    }

    # PHP Processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        
        # Buffers otimizados
        fastcgi_buffer_size 32k;
        fastcgi_buffers 16 32k;
        fastcgi_busy_buffers_size 64k;
        fastcgi_temp_file_write_size 64k;
        
        # Timeouts
        fastcgi_connect_timeout 60s;
        fastcgi_send_timeout 60s;
        fastcgi_read_timeout 60s;
    }
}
```

### 3. PHP-FPM Tuning

```ini
; php-fpm.conf
[legisinc]
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

; Pool sizing (ajustar baseado no servidor)
pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 1000

; Memory limits
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 120
php_admin_value[max_input_time] = 60

; OPcache otimizado
php_admin_value[opcache.enable] = 1
php_admin_value[opcache.memory_consumption] = 256
php_admin_value[opcache.interned_strings_buffer] = 16
php_admin_value[opcache.max_accelerated_files] = 20000
php_admin_value[opcache.validate_timestamps] = 0
php_admin_value[opcache.save_comments] = 0
php_admin_value[opcache.fast_shutdown] = 1
```

---

## 📈 Performance Testing

### Load Testing Scripts

```bash
#!/bin/bash
# load-test.sh

echo "🚀 Iniciando testes de carga..."

# Teste 1: Página inicial
echo "Testando página inicial..."
ab -n 1000 -c 50 http://localhost:8001/ > results/homepage.txt

# Teste 2: Listagem de proposições
echo "Testando listagem de proposições..."
ab -n 500 -c 25 -H "Cookie: laravel_session=test_session" \
   http://localhost:8001/proposicoes > results/proposicoes-list.txt

# Teste 3: Visualização de proposição
echo "Testando visualização de proposição..."
ab -n 200 -c 10 -H "Cookie: laravel_session=test_session" \
   http://localhost:8001/proposicoes/1 > results/proposicao-view.txt

# Teste 4: Geração de PDF
echo "Testando geração de PDF..."
ab -n 50 -c 5 -H "Cookie: laravel_session=test_session" \
   http://localhost:8001/proposicoes/1/pdf > results/pdf-generation.txt

echo "✅ Testes concluídos. Resultados em ./results/"
```

### Memory Leak Detection

```php
class MemoryLeakDetector
{
    private array $snapshots = [];

    public function takeSnapshot(string $label): void
    {
        $this->snapshots[$label] = [
            'memory' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'objects' => count(get_declared_classes()),
            'time' => microtime(true),
        ];
    }

    public function detectLeaks(): array
    {
        $leaks = [];
        $snapshots = array_values($this->snapshots);

        for ($i = 1; $i < count($snapshots); $i++) {
            $previous = $snapshots[$i - 1];
            $current = $snapshots[$i];

            $memoryIncrease = $current['memory'] - $previous['memory'];
            $objectIncrease = $current['objects'] - $previous['objects'];

            // Detectar crescimento anormal de memória
            if ($memoryIncrease > 50 * 1024 * 1024) { // 50MB
                $leaks[] = [
                    'type' => 'memory_spike',
                    'increase' => round($memoryIncrease / 1024 / 1024, 2) . 'MB',
                    'between' => array_keys($this->snapshots)[$i - 1] . ' -> ' . array_keys($this->snapshots)[$i],
                ];
            }

            // Detectar crescimento de objetos
            if ($objectIncrease > 100) {
                $leaks[] = [
                    'type' => 'object_leak',
                    'increase' => $objectIncrease . ' objects',
                    'between' => array_keys($this->snapshots)[$i - 1] . ' -> ' . array_keys($this->snapshots)[$i],
                ];
            }
        }

        return $leaks;
    }
}
```

---

## 🔧 Debugging Tools

### Query Analyzer

```php
class QueryAnalyzer
{
    public function analyzeSlowQueries(): array
    {
        $slowQueries = collect(DB::getQueryLog())
            ->where('time', '>', 100) // > 100ms
            ->map(function ($query) {
                return [
                    'sql' => $query['query'],
                    'time' => $query['time'],
                    'bindings' => $query['bindings'],
                    'analysis' => $this->analyzeQuery($query['query']),
                ];
            });

        return $slowQueries->toArray();
    }

    private function analyzeQuery(string $sql): array
    {
        $issues = [];

        // Detectar queries sem WHERE
        if (stripos($sql, 'SELECT') === 0 && stripos($sql, 'WHERE') === false) {
            $issues[] = 'Missing WHERE clause - Full table scan possible';
        }

        // Detectar falta de LIMIT
        if (stripos($sql, 'SELECT') === 0 && stripos($sql, 'LIMIT') === false) {
            $issues[] = 'Missing LIMIT clause - Large result set possible';
        }

        // Detectar N+1
        if (stripos($sql, 'SELECT') === 0 && substr_count($sql, 'WHERE') > 1) {
            $issues[] = 'Possible N+1 query - Consider eager loading';
        }

        // Detectar subconsultas desnecessárias
        if (substr_count($sql, 'SELECT') > 1) {
            $issues[] = 'Subquery detected - Consider JOIN instead';
        }

        return $issues;
    }
}
```

### Performance Dashboard

```php
class PerformanceDashboard
{
    public function getMetrics(): array
    {
        return [
            'cache' => $this->getCacheMetrics(),
            'database' => $this->getDatabaseMetrics(),
            'memory' => $this->getMemoryMetrics(),
            'requests' => $this->getRequestMetrics(),
        ];
    }

    private function getCacheMetrics(): array
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info();

            return [
                'hit_rate' => $this->calculateHitRate($info),
                'memory_usage' => $info['used_memory_human'] ?? 'N/A',
                'keys_count' => $redis->dbsize(),
                'expired_keys' => $info['expired_keys'] ?? 0,
            ];
        } catch (\Exception $e) {
            return ['error' => 'Redis unavailable'];
        }
    }

    private function getDatabaseMetrics(): array
    {
        return [
            'active_connections' => DB::select('SELECT count(*) as count FROM pg_stat_activity')[0]->count,
            'slow_queries' => $this->getSlowQueryCount(),
            'table_sizes' => $this->getTableSizes(),
            'index_usage' => $this->getIndexUsage(),
        ];
    }

    private function getMemoryMetrics(): array
    {
        return [
            'current_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'peak_usage' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
            'limit' => ini_get('memory_limit'),
            'opcache_usage' => $this->getOpcacheUsage(),
        ];
    }
}
```

---

## 📝 Checklist de Deploy

### Pre-Deploy Checklist

```bash
#!/bin/bash
# pre-deploy-check.sh

echo "🔍 Verificações pré-deploy..."

# 1. Verificar dependências
echo "Verificando dependências..."
php artisan package:discover --ansi
composer validate --strict

# 2. Executar testes
echo "Executando testes..."
php artisan test --coverage --min=80

# 3. Analisar performance
echo "Analisando queries..."
php artisan performance:analyze --report

# 4. Verificar configurações
echo "Verificando configurações..."
if grep -q "APP_DEBUG=true" .env; then
    echo "❌ DEBUG habilitado em produção!"
    exit 1
fi

# 5. Verificar cache
echo "Verificando cache..."
php artisan config:check
php artisan route:check

echo "✅ Pré-deploy OK!"
```

### Post-Deploy Verification

```bash
#!/bin/bash
# post-deploy-verify.sh

echo "🔍 Verificações pós-deploy..."

# 1. Smoke tests
echo "Executando smoke tests..."
curl -f http://localhost:8001/health || exit 1
curl -f http://localhost:8001/login || exit 1

# 2. Verificar serviços
echo "Verificando serviços..."
redis-cli ping || echo "⚠️ Redis não disponível"
pg_isready -h localhost -p 5432 || exit 1

# 3. Verificar performance
echo "Verificando performance..."
php artisan performance:optimize --report

# 4. Verificar logs
echo "Verificando logs de erro..."
if grep -q "ERROR" storage/logs/laravel.log; then
    echo "⚠️ Erros encontrados nos logs"
    tail -20 storage/logs/laravel.log
fi

echo "✅ Deploy verificado!"
```

---

**Documentação técnica atualizada em:** $(date +'%d/%m/%Y %H:%M:%S')  
**Versão:** 1.0 Technical Guide  
**Autor:** Performance Optimization Team