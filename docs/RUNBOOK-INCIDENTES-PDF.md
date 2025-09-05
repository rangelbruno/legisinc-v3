# 🚨 Runbook: Incidentes PDF Template Universal

## 🎯 Para On-Call: Resolução Rápida de Problemas

### 📞 Severidades

| Nível | Critério | SLA Resposta |
|-------|----------|--------------|
| **P0 - Crítico** | PDFs oficiais não geram | 15 min |
| **P1 - Alto** | Taxa falha > 20% por 15 min | 1 hora |
| **P2 - Médio** | Latência > 30s P95 por 30 min | 4 horas |
| **P3 - Baixo** | Alertas informativos | 24 horas |

---

## 🔥 P0: PDFs Oficiais Não Geram

### Sintomas
- Usuário Legislativo não consegue ver PDF após aprovação
- Logs: "Todos os conversores falharam" para status "aprovado"
- Alert: `PDFOfficialDocumentFailure`

### Diagnóstico Rápido (3 min)
```bash
# 1. Verificar OnlyOffice
curl -f http://legisinc-onlyoffice/healthcheck
# Se falhar → OnlyOffice down

# 2. Verificar LibreOffice  
docker exec legisinc-app soffice --version
# Se falhar → container sem LibreOffice

# 3. Verificar storage
docker exec legisinc-app ls -la /var/www/storage/app/proposicoes/
# Se "Permission denied" → problema de permissões

# 4. Verificar últimas falhas
docker exec legisinc-app tail -50 /var/www/storage/logs/laravel.log | grep "DocumentConversion failed"
```

### Ações por Cenário

#### 🔧 OnlyOffice Down
```bash
# Restart OnlyOffice
docker restart legisinc-onlyoffice

# Aguardar 30s e testar
sleep 30
curl -f http://legisinc-onlyoffice/

# Se ainda falhar: usar apenas LibreOffice temporariamente
docker exec legisinc-app php artisan config:cache
# Alterar PDF_CONVERTER_PRIORITY=libreoffice,dompdf no .env
```

#### 🔧 LibreOffice Missing
```bash
# Instalar LibreOffice emergencial
docker exec legisinc-app apk add --no-cache libreoffice libreoffice-writer
docker exec legisinc-app soffice --version
```

#### 🔧 Permissões Storage
```bash
# Corrigir permissões
docker exec legisinc-app chown -R www-data:www-data /var/www/storage/app/proposicoes/
docker exec legisinc-app chmod -R 775 /var/www/storage/app/proposicoes/
```

#### 🔧 Arquivo Fonte Missing
```bash
# Identificar proposições afetadas
docker exec legisinc-app php artisan tinker --execute="
    \$proposicoes = App\\Models\\Proposicao::whereIn('status', ['aprovado', 'protocolado'])
        ->whereNull('arquivo_pdf_path')
        ->where('updated_at', '>', now()->subHours(24))
        ->get(['id', 'arquivo_path']);
    
    foreach (\$proposicoes as \$p) {
        if (empty(\$p->arquivo_path) || !Storage::exists(\$p->arquivo_path)) {
            echo \"Proposição {\$p->id}: arquivo fonte missing\\n\";
        }
    }
"

# Escalar para dev team se arquivo fonte realmente perdido
```

---

## ⚡ P1: Alta Taxa de Falha

### Sintomas  
- Alert: `PDFConversionFailureHigh`
- Dashboard: Taxa sucesso < 80%
- Múltiplos usuários reportando PDFs não abrem

### Diagnóstico (5 min)
```bash
# 1. Ver distribuição de erros
docker exec legisinc-app php artisan tinker --execute="
    \$errors = App\\Models\\Proposicao::whereNotNull('pdf_erro_geracao')
        ->where('pdf_tentativa_em', '>', now()->subHour())
        ->pluck('pdf_erro_geracao')
        ->countBy()
        ->sortDesc();
    print_r(\$errors->toArray());
"

# 2. Verificar circuit breaker
docker exec legisinc-app tail -20 /var/www/storage/logs/laravel.log | grep "circuit breaker"

# 3. Verificar queue
docker exec legisinc-app php artisan queue:monitor pdf
```

### Ações por Erro Comum

#### 🔧 "Connection timeout"
```bash
# Aumentar timeout temporariamente
# No .env: ONLYOFFICE_TIMEOUT=120

# Restart workers
docker exec legisinc-app php artisan queue:restart
supervisorctl restart laravel-pdf-worker:*
```

#### 🔧 "File too large"
```bash
# Ver arquivos grandes
docker exec legisinc-app php artisan tinker --execute="
    \$large = App\\Models\\Proposicao::whereNotNull('arquivo_path')
        ->get()
        ->map(function(\$p) {
            \$size = Storage::exists(\$p->arquivo_path) ? Storage::size(\$p->arquivo_path) : 0;
            return ['id' => \$p->id, 'size' => \$size];
        })
        ->where('size', '>', 50000000)
        ->sortByDesc('size');
    print_r(\$large->toArray());
"

# Processar arquivos grandes via LibreOffice apenas
```

#### 🔧 "Invalid JWT token"
```bash
# Verificar configuração JWT
docker exec legisinc-app php artisan tinker --execute="
    echo 'JWT Secret: ' . (env('ONLYOFFICE_JWT_SECRET') ? 'Configurado' : 'MISSING') . \"\\n\";
    echo 'Server URL: ' . env('ONLYOFFICE_DOCUMENT_SERVER_URL') . \"\\n\";
"

# Gerar novo JWT secret se necessário
# Ou desabilitar JWT temporariamente (só para troubleshoot!)
```

---

## 📊 P2: Alta Latência

### Sintomas
- Alert: `PDFConversionLatencyHigh`  
- Dashboard: P95 > 30 segundos
- Usuários reclamam de lentidão

### Diagnóstico (5 min)
```bash
# 1. Ver conversões mais lentas
docker exec legisinc-app php artisan tinker --execute="
    \$slow = App\\Models\\Proposicao::whereNotNull('pdf_duracao_ms')
        ->where('pdf_gerado_em', '>', now()->subHours(2))
        ->orderBy('pdf_duracao_ms', 'desc')
        ->take(10)
        ->get(['id', 'pdf_duracao_ms', 'pdf_conversor_usado', 'pdf_tamanho']);
    print_r(\$slow->toArray());
"

# 2. Verificar carga sistema
docker stats legisinc-onlyoffice legisinc-app --no-stream

# 3. Verificar queue backlog
docker exec legisinc-app php artisan horizon:status
```

### Ações por Causa

#### 🔧 OnlyOffice Sobrecarregado
```bash
# Aumentar workers LibreOffice temporariamente
# Alterar prioridade: PDF_CONVERTER_PRIORITY=libreoffice,onlyoffice

# Ou scale OnlyOffice se possível
docker-compose scale legisinc-onlyoffice=2
```

#### 🔧 Queue Backlog
```bash
# Aumentar workers temporariamente
supervisorctl start laravel-pdf-worker:*
# (configure mais workers no supervisor)

# Processar queue urgente
docker exec legisinc-app php artisan queue:work pdf --timeout=60 --tries=2 --memory=256
```

#### 🔧 Disco Lento
```bash
# Verificar I/O
iostat -x 1 5

# Limpar PDFs antigos se storage cheio
docker exec legisinc-app php artisan tinker --execute="
    \$old = Storage::files('proposicoes/pdfs');
    \$count = 0;
    foreach (\$old as \$file) {
        if (Storage::lastModified(\$file) < now()->subDays(90)->timestamp) {
            Storage::delete(\$file);
            \$count++;
        }
    }
    echo \"Removidos {\$count} PDFs antigos\\n\";
"
```

---

## 🛠️ Ferramentas de Debug

### Teste Manual de Conversão
```bash
# Testar conversão específica
docker exec legisinc-app php artisan tinker --execute="
    \$result = app(App\\Services\\DocumentConversionService::class)
        ->convertToPDF('proposicoes/proposicao_123.rtf', 'debug/test.pdf', 'aprovado');
    print_r(\$result);
"
```

### Monitor em Tempo Real
```bash
# Logs PDF em tempo real
docker exec legisinc-app tail -f /var/www/storage/logs/laravel.log | grep -E "(PDF|DocumentConversion)"

# Queue em tempo real  
watch -n 2 'docker exec legisinc-app php artisan queue:monitor pdf'

# Métricas OnlyOffice
curl -s http://legisinc-onlyoffice/healthcheck | jq .
```

### Reset Circuit Breaker
```bash
# Forçar reset do circuit breaker
docker exec legisinc-app php artisan tinker --execute="
    Cache::forget('circuit_breaker_onlyoffice_failures');
    Cache::forget('circuit_breaker_onlyoffice_last_failure');
    echo 'Circuit breaker resetado\\n';
"
```

### Reprocessar PDFs Falhados
```bash
# Reprocessar últimas 24h
docker exec legisinc-app php artisan tinker --execute="
    \$failed = App\\Models\\Proposicao::whereNotNull('pdf_erro_geracao')
        ->where('pdf_tentativa_em', '>', now()->subDay())
        ->whereIn('status', ['aprovado', 'protocolado'])
        ->get();
    
    foreach (\$failed as \$proposicao) {
        \\App\\Jobs\\GerarPDFProposicaoJob::dispatch(\$proposicao);
        echo \"Job disparado para proposição {\$proposicao->id}\\n\";
    }
    
    echo \"Total: {\$failed->count()} jobs disparados\\n\";
"
```

---

## 📱 Escalation Path

### Dev Team
**Quando escalar:**
- Arquivo fonte corrompido/perdido
- Bug no código de conversão
- Novo tipo de documento não suportado

### Infra Team  
**Quando escalar:**
- OnlyOffice container não sobe
- Performance degradada persistente
- Storage/network issues

### Product Team
**Quando escalar:**
- Mudança no template universal
- Novos requisitos de formatação
- Compliance/regulamentação

---

## 💡 Quick Wins

### Resolver 80% dos Casos
1. **Restart OnlyOffice** (resolve timeout temporário)
2. **Corrigir permissões storage** (resolve "access denied")
3. **Limpar queue** (resolve backlog)
4. **Increase timeout** (resolve falhas esporádicas)

### Preventivo Semanal
- Limpar PDFs > 90 dias
- Verificar crescimento do storage
- Review logs de erros patterns
- Testar documento rico completo

---

**🔗 Links Úteis:**
- Dashboard: http://grafana/dashboards/pdf-metrics
- Logs: http://kibana/app/logs/pdf-conversion  
- Queue: http://horizon/dashboard/pdf
- OnlyOffice: http://legisinc-onlyoffice:8080/welcome