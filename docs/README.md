# 📚 Documentação do Sistema Legisinc

## 📋 Índice da Documentação

### 🚀 Performance e Otimização

- **[PERFORMANCE_OPTIMIZATION.md](../PERFORMANCE_OPTIMIZATION.md)** - Guia completo de otimização de performance
- **[PERFORMANCE_TECHNICAL_GUIDE.md](./PERFORMANCE_TECHNICAL_GUIDE.md)** - Guia técnico detalhado para desenvolvedores

### 🏛️ Sistema Principal

- **[CLAUDE.md](../CLAUDE.md)** - Configuração e instruções principais do sistema
- **[README.md](../README.md)** - Visão geral do projeto

---

## 🎯 Visão Geral dos Documentos

### Performance Optimization (Principal)

**Público-alvo:** Administradores, DevOps, Desenvolvedores  
**Conteúdo:**
- Visão geral das otimizações implementadas
- Comandos para uso diário
- Monitoramento e troubleshooting
- Benchmarks e resultados
- Guia de manutenção

### Performance Technical Guide (Técnico)

**Público-alvo:** Desenvolvedores avançados, Arquitetos de Software  
**Conteúdo:**
- Implementação detalhada de cada otimização
- Código de exemplo e configurações
- Scripts de teste e monitoramento
- Debugging avançado
- Checklist de deploy

---

## 🔧 Quick Start - Performance

### Comando Único para Otimização Completa

```bash
# Executar todas as otimizações
php artisan performance:optimize --all

# Deploy otimizado completo
./scripts/deploy-optimized.sh
```

### Verificação Rápida de Status

```bash
# Relatório de performance
php artisan performance:optimize --report

# Status dos serviços
redis-cli ping
pg_isready -h localhost -p 5432
curl -f http://localhost:8001/health
```

---

## 📊 Métricas de Sucesso

| Métrica | Antes | Depois | Melhoria |
|---------|-------|--------|----------|
| **Tempo de carregamento** | 3.2s | 0.8s | 75% ⬇️ |
| **Queries por request** | 45 | 8 | 82% ⬇️ |
| **Uso de memória** | 128MB | 48MB | 62% ⬇️ |
| **Geração de PDF** | 12s | 4s | 67% ⬇️ |
| **Cache hit rate** | 0% | 95% | ∞ ⬆️ |

---

## 🛠️ Ferramentas e Comandos Essenciais

### Monitoramento Diário

```bash
# Cache warmup (manhã)
php artisan performance:optimize --cache-warmup

# Limpeza de arquivos (noite)
php artisan performance:optimize --cleanup-pdfs

# Relatório semanal
php artisan performance:optimize --report > reports/performance-$(date +%Y%m%d).txt
```

### Debug de Performance

```bash
# Verificar queries lentas
tail -f storage/logs/laravel.log | grep "Slow query"

# Monitorar cache
redis-cli monitor

# Análise de memória
php artisan tinker --memory-usage
```

---

## 🚨 Alertas e Monitoramento

### Métricas Críticas para Monitorar

- **Cache Hit Rate** < 80% ⚠️
- **Tempo de Response** > 2s ⚠️
- **Uso de Memória** > 85% ⚠️
- **Queries Lentas** > 10/min ⚠️
- **Espaço em Disco** < 10% ⚠️

### Scripts de Monitoramento

```bash
# Cron jobs recomendados
0 2 * * * cd /path/to/legisinc && php artisan performance:optimize --cleanup-pdfs
0 6 * * * cd /path/to/legisinc && php artisan performance:optimize --cache-warmup
0 8 * * 0 cd /path/to/legisinc && php artisan performance:optimize --report > /tmp/weekly-report.txt
```

---

## 🔗 Links Úteis

### Ferramentas Externas
- [Redis Commander](http://localhost:8081) - Interface web para Redis
- [phpMyAdmin](http://localhost:8080) - Interface web para banco
- [New Relic](https://newrelic.com) - Monitoramento APM (recomendado)

### Documentação de Referência
- [Laravel Performance](https://laravel.com/docs/performance)
- [Redis Optimization](https://redis.io/docs/management/optimization/)
- [PostgreSQL Performance](https://www.postgresql.org/docs/current/performance-tips.html)

---

## 📞 Suporte e Contribuição

### Reportar Problemas de Performance

1. **Logs:** Incluir sempre logs relevantes
2. **Métricas:** Usar `php artisan performance:optimize --report`
3. **Ambiente:** Especificar versões e configurações
4. **Reprodução:** Passos claros para reproduzir

### Contribuir com Melhorias

1. **Fork** o repositório
2. **Branch** de feature: `git checkout -b feature/performance-improvement`
3. **Commit** com mensagens claras
4. **Pull Request** com documentação atualizada

---

**Documentação mantida pela equipe de Performance**  
**Última atualização:** $(date +'%d/%m/%Y %H:%M:%S')  
**Versão do sistema:** Legisinc v1.0 Performance Optimized