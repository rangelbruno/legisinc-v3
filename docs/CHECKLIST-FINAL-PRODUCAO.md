# ✅ Checklist Final: PDF Template Universal - Produção

## 🎯 Últimos 5% Críticos Antes de Deploy

### 🔧 1. Sistema & Container

#### Usuário e Permissões
```bash
# Verificar usuário do PHP-FPM
docker exec legisinc-app ps aux | grep php-fpm
# Deve rodar como www-data, não root

# Verificar permissões do storage
docker exec legisinc-app ls -la /var/www/storage/app/proposicoes/
# Deve ser drwxrwxr-x www-data www-data

# Corrigir se necessário
docker exec legisinc-app chown -R www-data:www-data /var/www/storage/app/proposicoes/
docker exec legisinc-app chmod -R 775 /var/www/storage/app/proposicoes/
```

#### Limites de Memória
```yaml
# docker-compose.yml
services:
  legisinc-app:
    deploy:
      resources:
        limits:
          memory: 2G
          cpus: '1.0'
        reservations:
          memory: 1G
          cpus: '0.5'
```

```dockerfile
# Dockerfile - Configurar PHP
RUN echo "memory_limit = 1024M" >> /usr/local/etc/php/conf.d/memory.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/timeout.ini
```

### 🎨 2. Fidelidade 1:1 - Testes Críticos

#### Script de Teste Documento Rico
```bash
#!/bin/bash
# scripts/test-documento-rico.sh

echo "🎨 Testando documento rico para fidelidade 1:1..."

# Criar documento complexo
docker exec legisinc-app php artisan tinker --execute="
    \$docx = new \\PhpOffice\\PhpWord\\PhpWord();
    \$section = \$docx->addSection();
    
    // Header com imagem
    \$header = \$section->addHeader();
    \$header->addImage('/var/www/public/template/cabecalho.png');
    
    // Título
    \$section->addText('PROPOSIÇÃO TESTE COMPLETO', ['bold' => true, 'size' => 16]);
    \$section->addTextBreak(2);
    
    // Tabela
    \$table = \$section->addTable();
    \$table->addRow();
    \$table->addCell(2000)->addText('Item');
    \$table->addCell(6000)->addText('Descrição');
    \$table->addRow();
    \$table->addCell(2000)->addText('1.');
    \$table->addCell(6000)->addText('Texto com formatação em negrito e itálico');
    
    // Lista numerada
    \$section->addTextBreak();
    for (\$i = 1; \$i <= 5; \$i++) {
        \$section->addListItem('Item da lista número ' . \$i, 0, null, 'decimal');
    }
    
    // Quebra de página
    \$section->addPageBreak();
    
    // Segunda página com rodapé
    \$footer = \$section->addFooter();
    \$footer->addText('Página {\PAGE} de {\NUMPAGES}', null, ['alignment' => 'center']);
    
    // Salvar
    \$objWriter = \\PhpOffice\\PhpWord\\IOFactory::createWriter(\$docx, 'Word2007');
    \$objWriter->save('/var/www/storage/app/test/documento_complexo.docx');
    
    echo 'Documento complexo criado\n';
"

# Converter e validar
docker exec legisinc-app php artisan tinker --execute="
    \$converter = app(App\\Services\\DocumentConversionService::class);
    \$result = \$converter->convertToPDF('test/documento_complexo.docx', 'test/documento_complexo.pdf', 'aprovado');
    
    if (\$result['success']) {
        \$size = Storage::size('test/documento_complexo.pdf');
        echo 'PDF gerado: ' . \$size . ' bytes\n';
        
        // Verificar se não é muito pequeno (sem conteúdo)
        if (\$size > 50000) {
            echo '✅ Tamanho OK - conteúdo preservado\n';
        } else {
            echo '❌ PDF muito pequeno - possível perda de conteúdo\n';
            exit(1);
        }
    } else {
        echo '❌ Falha: ' . \$result['error'] . '\n';
        exit(1);
    }
"

echo "✅ Documento rico testado com sucesso!"
```

#### Fontes Corporativas
```dockerfile
# Adicionar fontes custom ao Dockerfile
COPY fonts/ /usr/share/fonts/custom/
RUN fc-cache -f -v
```

### 🛡️ 3. Observabilidade - Dashboards & Alertas

#### Métricas Prometheus
```php
<?php
// app/Http/Controllers/MetricsController.php

class MetricsController extends Controller
{
    public function metrics()
    {
        $registry = app(\Prometheus\CollectorRegistry::class);
        
        // Taxa de sucesso por conversor
        $successRate = Proposicao::selectRaw('
            pdf_conversor_usado,
            COUNT(*) as total,
            COUNT(CASE WHEN arquivo_pdf_path IS NOT NULL THEN 1 END) as success
        ')
        ->whereNotNull('pdf_gerado_em')
        ->where('pdf_gerado_em', '>=', now()->subHours(24))
        ->groupBy('pdf_conversor_usado')
        ->get();
        
        $gauge = $registry->getOrRegisterGauge(
            'app', 'pdf_conversion_success_rate', 'PDF conversion success rate', ['converter']
        );
        
        foreach ($successRate as $stat) {
            $rate = $stat->total > 0 ? ($stat->success / $stat->total) : 0;
            $gauge->set($rate, [$stat->pdf_conversor_usado ?? 'unknown']);
        }
        
        // Duração média
        $avgDuration = DB::table('proposicoes')
            ->whereNotNull('pdf_gerado_em')
            ->where('pdf_gerado_em', '>=', now()->subHour())
            ->avg('pdf_duracao_ms') ?? 0;
            
        $durationGauge = $registry->getOrRegisterGauge(
            'app', 'pdf_conversion_duration_avg_ms', 'Average PDF conversion duration'
        );
        $durationGauge->set($avgDuration);
        
        return response($registry->getRenderer()->renderAll())
            ->header('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
    }
}
```

#### Alertas (AlertManager)
```yaml
# alerts/pdf-alerts.yml
groups:
  - name: pdf_conversion
    rules:
      - alert: PDFConversionFailureHigh
        expr: rate(proposicao_pdf_convert_fail_total[5m]) > 0.1
        for: 5m
        labels:
          severity: critical
        annotations:
          summary: "Alta taxa de falha na conversão de PDF"
          description: "{{ $value }} falhas por segundo nos últimos 5 minutos"

      - alert: PDFConversionLatencyHigh
        expr: histogram_quantile(0.95, rate(proposicao_pdf_convert_seconds_bucket[5m])) > 20
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "Latência alta na conversão de PDF"
          description: "P95 de {{ $value }} segundos nos últimos 5 minutos"

      - alert: OnlyOfficeCircuitBreakerOpen
        expr: increase(onlyoffice_circuit_breaker_open_total[1m]) > 0
        labels:
          severity: critical
        annotations:
          summary: "Circuit breaker do OnlyOffice aberto"
          description: "OnlyOffice indisponível - conversões usando fallback"
```

### 🚀 4. Nginx & Cache Otimizado

```nginx
# nginx/conf.d/pdf-cache.conf
location ~ ^/proposicoes/(\d+)/pdf$ {
    # Cache para PDFs com hash (imutáveis)
    location ~ \.(pdf)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header X-PDF-Cache "HIT";
    }
    
    # PDFs sem hash - sempre verificar
    expires 0;
    add_header Cache-Control "no-cache, no-store, must-revalidate";
    add_header Pragma "no-cache";
    
    # Passar para Laravel
    try_files $uri $uri/ /index.php?$query_string;
}

# Upload de documentos grandes
client_max_body_size 100M;
proxy_read_timeout 300s;
proxy_send_timeout 300s;
```

### 🔒 5. Segurança Hardened

#### JWT com Rotação
```php
<?php
// config/onlyoffice.php

return [
    'jwt_secret' => env('ONLYOFFICE_JWT_SECRET'),
    'jwt_secret_backup' => env('ONLYOFFICE_JWT_SECRET_BACKUP'), // Para rotação sem downtime
    'jwt_ttl' => env('ONLYOFFICE_JWT_TTL', 300), // 5 minutos
];
```

#### Invalidação de URLs Temporárias
```php
<?php
// app/Services/DocumentConversionService.php

private function invalidateOldTemporaryUrls(int $proposicaoId): void
{
    $pattern = "temp_url_proposicao_{$proposicaoId}_*";
    $keys = Cache::getRedis()->keys($pattern);
    
    if (!empty($keys)) {
        Cache::getRedis()->del($keys);
        Log::info('URLs temporárias invalidadas', [
            'proposicao_id' => $proposicaoId,
            'count' => count($keys)
        ]);
    }
}
```

### ⚡ 6. Queue Otimizada

```php
<?php
// config/queue.php

'connections' => [
    'pdf' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'pdf',
        'retry_after' => 600, // 10 minutos
        'block_for' => 5,
    ],
],
```

```bash
# Supervisor para worker dedicado
# /etc/supervisor/conf.d/laravel-pdf-worker.conf
[program:laravel-pdf-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/artisan queue:work pdf --tries=3 --timeout=300 --memory=512
directory=/var/www
autostart=true
autorestart=true
numprocs=2
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/storage/logs/pdf-worker.log
```

## ✅ Checklist de Deploy Final

### Pré-Deploy
- [ ] **Dockerfile atualizado** com LibreOffice + fontes + coreutils
- [ ] **Variáveis .env** configuradas (JWT secrets, URLs, timeouts)
- [ ] **Migration executada** (campos pdf_* nas proposições)
- [ ] **Nginx configurado** (proxy OnlyOffice + cache PDFs)
- [ ] **Supervisor configurado** (worker dedicado para PDF)

### Validação em Staging
- [ ] **Teste documento rico** - tabelas, imagens, múltiplas páginas
- [ ] **Teste governança** - DomPDF bloqueado para docs oficiais
- [ ] **Teste circuit breaker** - OnlyOffice down → fallback LibreOffice
- [ ] **Teste retry** - falha → job queue → sucesso
- [ ] **Teste permissões** - usuários corretos acessando PDFs

### Deploy em Produção
- [ ] **Rolling deploy** sem downtime
- [ ] **Health check** - OnlyOffice + LibreOffice funcionais
- [ ] **Smoke test** - 1 conversão de cada tipo (OnlyOffice/LibreOffice)
- [ ] **Métricas ativas** - Prometheus + AlertManager
- [ ] **Logs estruturados** - JSON no ELK/Loki

### Pós-Deploy
- [ ] **Monitorar alertas** primeiras 24h
- [ ] **Validar dashboards** - métricas aparecendo
- [ ] **Teste regressão** - proposições antigas ainda funcionam
- [ ] **Documentar runbook** para on-call

## 🚨 Sinais de Problemas

### ❌ Red Flags
- PDFs < 10KB para documentos > 1 página
- Taxa de sucesso < 95% em 1 hora  
- Circuit breaker abrindo > 1x por dia
- Jobs PDF acumulando na queue > 100

### ⚠️ Yellow Flags  
- Latência P95 > 15 segundos
- Mais de 3 retries por documento
- Uso de DomPDF > 1% do total
- Storage crescendo > 1GB/dia

---

**🎯 Meta**: 99.9% fidelidade 1:1 ao template universal  
**📊 SLA**: < 10s P95 para conversão, > 99% disponibilidade  
**🛡️ Governança**: Zero PDFs "capados" em produção