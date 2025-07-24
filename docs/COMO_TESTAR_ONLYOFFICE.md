# Como Testar a Integração OnlyOffice

Este guia explica como executar testes e diagnósticos da integração OnlyOffice no sistema Legisinc.

## Visão Geral dos Scripts

O sistema inclui dois scripts principais para teste e monitoramento:

### 1. Script de Diagnóstico (`diagnose-onlyoffice.sh`)
- **Propósito**: Diagnóstico completo da integração
- **Quando usar**: Ao detectar problemas ou após mudanças na configuração
- **Duração**: ~10 segundos

### 2. Script de Monitoramento (`monitor-onlyoffice.sh`)
- **Propósito**: Verificação contínua da saúde do sistema
- **Quando usar**: Para monitoramento regular e preventivo
- **Duração**: ~5 segundos

## Pré-requisitos

### Containers necessários rodando:
```bash
docker ps | grep -E "(legisinc-app|legisinc-onlyoffice)"
```

**Deve mostrar:**
```
legisinc-onlyoffice    onlyoffice/documentserver:8.0
legisinc-app          legisinc-app
```

### Rede Docker configurada:
```bash
docker network ls | grep legisinc-network
```

## Executando o Diagnóstico

### Uso Básico
```bash
# No diretório raiz do projeto
./scripts/diagnose-onlyoffice.sh
```

### Com parâmetros específicos
```bash
# Testar proposição e template específicos
./scripts/diagnose-onlyoffice.sh [proposicao_id] [template_id]

# Exemplo:
./scripts/diagnose-onlyoffice.sh 4169 11
```

### Exemplo de Output Esperado

```
🔍 Diagnóstico OnlyOffice Integration
=======================================
Proposição ID: 4169
Template ID: 11
Arquivo: proposicao_4169_template_11.docx

1. Verificando containers...
✅ Container Laravel (legisinc-app) rodando
✅ Container OnlyOffice (legisinc-onlyoffice) rodando

2. Verificando rede Docker...
✅ Rede legisinc-network existe
ℹ️  Laravel IP: 172.24.0.2
ℹ️  OnlyOffice IP: 172.24.0.3

3. Verificando acessibilidade do OnlyOffice...
✅ OnlyOffice acessível via localhost:8080

4. Verificando conectividade entre containers...
✅ OnlyOffice consegue acessar Laravel

5. Verificando acesso ao arquivo...
✅ Arquivo acessível (HTTP 200)

6. Verificando callback...
✅ Callback funcionando (HTTP 200)

7. Logs recentes do Laravel...
[logs do OnlyOffice]

8. Logs recentes do OnlyOffice...
ℹ️  Nenhum erro recente no OnlyOffice

9. Verificando arquivos no storage...
ℹ️  Arquivos da proposição 4169 no storage: 4

10. Resumo da configuração atual...
ℹ️  URLs esperados na configuração JavaScript:
   Document URL: http://172.24.0.2:80/onlyoffice/file/proposicao/4169/proposicao_4169_template_11.docx
   Callback URL: http://172.24.0.2:80/api/onlyoffice/callback/proposicao/4169

🎯 Diagnóstico concluído!
```

## Executando o Monitoramento

### Verificação única
```bash
./scripts/monitor-onlyoffice.sh
```

### Monitoramento contínuo
```bash
# Atualiza a cada 30 segundos
watch -n 30 ./scripts/monitor-onlyoffice.sh

# Parar com Ctrl+C
```

### Exemplo de Output de Monitoramento

```
🔄 OnlyOffice Health Monitor - 2025-07-24 15:30:00
==============================================
✅ Containers rodando
✅ OnlyOffice web interface
✅ Conectividade entre containers
✅ Callback endpoint
✅ Storage de proposições
✅ Sem erros recentes no OnlyOffice

📊 Health Score: 6/6 (100%)
🎉 Sistema OnlyOffice funcionando perfeitamente!

📈 Estatísticas:
   - Logs OnlyOffice no Laravel: 15
   - Arquivos de proposições: 8
   - OnlyOffice iniciado em: 2025-07-24T14:35:05.030779158Z

💡 Recomendações:
Sistema funcionando normalmente!

📋 Log completo salvo em: /tmp/onlyoffice-monitor.log
```

## Interpretando os Resultados

### Status Icons
- ✅ **Verde**: Funcionando corretamente
- ⚠️ **Amarelo**: Aviso, mas não crítico
- ❌ **Vermelho**: Erro que precisa ser corrigido

### Health Score
- **100%**: Sistema perfeito
- **80-99%**: Funcionando com pequenos problemas
- **50-79%**: Problemas moderados
- **< 50%**: Problemas sérios, precisa intervenção

### Códigos HTTP Importantes
- **200**: Sucesso
- **404**: Arquivo não encontrado
- **419**: Erro de CSRF (precisa configurar exceção)
- **500**: Erro interno do servidor

## Cenários de Teste

### 1. Teste Após Instalação Inicial
```bash
# 1. Verificar se containers estão rodando
docker ps

# 2. Executar diagnóstico completo
./scripts/diagnose-onlyoffice.sh

# 3. Se tudo OK, testar no browser
# Acessar: http://localhost:8001/proposicoes/ID/preparar-edicao/TEMPLATE
```

### 2. Teste Após Mudanças de Configuração
```bash
# 1. Limpar cache do Laravel
docker exec legisinc-app php artisan config:clear
docker exec legisinc-app php artisan route:clear

# 2. Reiniciar containers
docker restart legisinc-onlyoffice legisinc-app

# 3. Aguardar containers iniciarem (30s)
sleep 30

# 4. Executar diagnóstico
./scripts/diagnose-onlyoffice.sh
```

### 3. Teste de Conectividade de Rede
```bash
# Verificar IPs dos containers
docker inspect legisinc-app | grep IPAddress
docker inspect legisinc-onlyoffice | grep IPAddress

# Testar conectividade manualmente
docker exec legisinc-onlyoffice curl -I http://172.24.0.2:80
docker exec legisinc-app curl -I http://172.24.0.3:80
```

### 4. Teste de Callback
```bash
# Testar callback manualmente
docker exec legisinc-onlyoffice curl -X POST \
  -H "Content-Type: application/json" \
  -d '{"status":0}' \
  http://172.24.0.2:80/api/onlyoffice/callback/proposicao/TEST

# Deve retornar: {"error":0}
```

## Solução de Problemas Comuns

### Problema: Containers não encontrados
```bash
# Verificar se containers existem
docker ps -a | grep legisinc

# Se não existirem, iniciar com docker-compose
docker-compose up -d
```

### Problema: Rede não encontrada
```bash
# Recriar rede
docker network create legisinc-network
docker network connect legisinc-network legisinc-app
docker network connect legisinc-network legisinc-onlyoffice
```

### Problema: OnlyOffice não acessível
```bash
# Verificar se porta está ocupada
netstat -tlnp | grep :8080

# Verificar logs do container
docker logs legisinc-onlyoffice
```

### Problema: Callback retorna 419
```bash
# Verificar configuração CSRF
grep -r "onlyoffice" app/Http/Middleware/VerifyCsrfToken.php

# Deve conter: 'onlyoffice/*'
```

## Testes Manuais no Browser

### 1. Testar Interface Web do OnlyOffice
```
URL: http://localhost:8080
Esperado: Página de boas-vindas do OnlyOffice
```

### 2. Testar Fluxo Completo de Edição
```
1. Acessar: http://localhost:8001/proposicoes
2. Criar nova proposição
3. Clicar "Editar" → "Preparar Edição"
4. Selecionar template
5. Clicar "Abrir OnlyOffice"
6. Verificar se editor carrega em nova aba
7. Fazer alterações no documento
8. Verificar se salvamento automático funciona
```

### 3. Verificar Logs em Tempo Real
```bash
# Terminal 1: Logs do Laravel
docker exec legisinc-app tail -f storage/logs/laravel.log | grep -i onlyoffice

# Terminal 2: Logs do OnlyOffice
docker logs -f legisinc-onlyoffice

# Terminal 3: Executar ações no browser
```

## Automatização para CI/CD

### Script de Teste para Pipeline
```bash
#!/bin/bash
# test-onlyoffice-integration.sh

set -e

echo "🧪 Testando integração OnlyOffice..."

# 1. Verificar pré-requisitos
./scripts/diagnose-onlyoffice.sh > /tmp/diagnose.log 2>&1

# 2. Verificar health score
HEALTH_SCORE=$(./scripts/monitor-onlyoffice.sh | grep "Health Score" | cut -d'(' -f2 | cut -d'%' -f1)

if [ "$HEALTH_SCORE" -ge 80 ]; then
    echo "✅ Integração OnlyOffice OK ($HEALTH_SCORE%)"
    exit 0
else
    echo "❌ Integração OnlyOffice com problemas ($HEALTH_SCORE%)"
    cat /tmp/diagnose.log
    exit 1
fi
```

## Logs e Debugging

### Localizações dos Logs
```bash
# Laravel (aplicação)
docker exec legisinc-app tail -f storage/logs/laravel.log

# OnlyOffice (container)
docker logs -f legisinc-onlyoffice

# Nginx (se aplicável)
docker exec legisinc-app tail -f /var/log/nginx/error.log
```

### Filtros Úteis
```bash
# Apenas logs OnlyOffice no Laravel
docker exec legisinc-app grep -i onlyoffice storage/logs/laravel.log

# Apenas erros no OnlyOffice
docker logs legisinc-onlyoffice 2>&1 | grep -i error

# Callbacks recentes
docker exec legisinc-app grep "callback recebido" storage/logs/laravel.log | tail -10
```

## Métricas de Performance

### Tempos Esperados
- **Carregamento do editor**: < 5 segundos
- **Salvamento automático**: < 2 segundos
- **Resposta do callback**: < 1 segundo

### Teste de Performance
```bash
# Tempo de resposta do OnlyOffice
time curl -s http://localhost:8080/welcome/ > /dev/null

# Tempo de resposta do arquivo
time docker exec legisinc-onlyoffice curl -s \
  http://172.24.0.2:80/onlyoffice/file/proposicao/4169/proposicao_4169_template_11.docx > /dev/null
```

## Troubleshooting Avançado

Para problemas complexos, consulte:
- `docs/TROUBLESHOOTING_ONLYOFFICE.md` - Guia detalhado de problemas
- `docs/ONLYOFFICE_INTEGRATION.md` - Documentação técnica completa

### Comandos de Reset Completo
```bash
# 1. Parar tudo
docker stop legisinc-onlyoffice legisinc-app

# 2. Remover containers
docker rm legisinc-onlyoffice

# 3. Recriar OnlyOffice
docker run -d --name legisinc-onlyoffice --network legisinc-network \
  -p 8080:80 -e JWT_ENABLED=false -e ALLOW_PRIVATE_IP_ADDRESS=true \
  onlyoffice/documentserver:8.0

# 4. Reiniciar Laravel
docker start legisinc-app

# 5. Aguardar e testar
sleep 60 && ./scripts/diagnose-onlyoffice.sh
```

---

**📝 Resumo dos Comandos Principais:**

```bash
# Diagnóstico rápido
./scripts/diagnose-onlyoffice.sh

# Monitoramento contínuo
watch -n 30 ./scripts/monitor-onlyoffice.sh

# Teste de conectividade manual
docker exec legisinc-onlyoffice curl -I http://172.24.0.2:80

# Logs em tempo real
docker exec legisinc-app tail -f storage/logs/laravel.log | grep -i onlyoffice
```

**Para suporte adicional**, consulte a documentação técnica em `docs/ONLYOFFICE_INTEGRATION.md`.