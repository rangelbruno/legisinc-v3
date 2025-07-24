# OnlyOffice Integration - Legisinc

Documentação completa da integração OnlyOffice Document Server no sistema Legisinc.

## 📋 Índice da Documentação

### 🚀 Início Rápido
- **[COMO_TESTAR_ONLYOFFICE.md](COMO_TESTAR_ONLYOFFICE.md)** - Guia prático para executar testes e diagnósticos

### 📖 Documentação Técnica
- **[ONLYOFFICE_INTEGRATION.md](ONLYOFFICE_INTEGRATION.md)** - Arquitetura, configuração e implementação detalhada

### 🔧 Solução de Problemas
- **[TROUBLESHOOTING_ONLYOFFICE.md](TROUBLESHOOTING_ONLYOFFICE.md)** - Guia completo de diagnóstico e correção de erros

## 🛠️ Scripts Disponíveis

### Diagnóstico Automatizado
```bash
./scripts/diagnose-onlyoffice.sh [proposicao_id] [template_id]
```
- Verifica containers, rede, conectividade e endpoints
- Testa acesso a arquivos específicos
- Valida configuração atual

### Monitoramento Contínuo
```bash
./scripts/monitor-onlyoffice.sh
```
- Health check completo do sistema
- Métricas de performance
- Recomendações automáticas

## ⚡ Comandos Essenciais

### Verificação Rápida
```bash
# Status geral
./scripts/diagnose-onlyoffice.sh

# Monitoramento em tempo real
watch -n 30 ./scripts/monitor-onlyoffice.sh

# Logs do sistema
docker exec legisinc-app tail -f storage/logs/laravel.log | grep -i onlyoffice
```

### Teste Manual
```bash
# OnlyOffice acessível
curl http://localhost:8080/welcome/

# Conectividade entre containers
docker exec legisinc-onlyoffice curl -I http://172.24.0.2:80

# Callback funcionando
curl -X POST -H "Content-Type: application/json" -d '{"status":0}' \
  http://localhost:8001/api/onlyoffice/callback/proposicao/test
```

## 🎯 Fluxo de Teste Recomendado

### 1. **Após Instalação**
```bash
./scripts/diagnose-onlyoffice.sh
```

### 2. **Após Mudanças**
```bash
docker restart legisinc-onlyoffice legisinc-app
sleep 30
./scripts/diagnose-onlyoffice.sh
```

### 3. **Monitoramento Regular**
```bash
watch -n 30 ./scripts/monitor-onlyoffice.sh
```

## 🚨 Resolução de Problemas Frequentes

| Erro | Solução Rápida | Documentação |
|------|----------------|--------------|
| Editor não carrega | Verificar `localhost:8080` | [TROUBLESHOOTING](TROUBLESHOOTING_ONLYOFFICE.md#editor-não-carrega) |
| "Erro ao baixar arquivo" | Testar conectividade de rede | [TROUBLESHOOTING](TROUBLESHOOTING_ONLYOFFICE.md#erro--4-erro-ao-baixar-arquivo) |
| "Documento não pode ser gravado" | Verificar callback endpoint | [TROUBLESHOOTING](TROUBLESHOOTING_ONLYOFFICE.md#erro-419-page-expired-no-callback) |
| Token JWT inválido | Desabilitar JWT no container | [TROUBLESHOOTING](TROUBLESHOOTING_ONLYOFFICE.md#erro--20-token-de-segurança-não-formado-corretamente) |

## 📊 Métricas de Saúde

### Health Score Esperado
- **100%**: Sistema perfeito ✅
- **80-99%**: Pequenos problemas ⚠️
- **< 80%**: Requer atenção ❌

### Tempos de Resposta
- Carregamento do editor: < 5s
- Salvamento automático: < 2s
- Resposta de callback: < 1s

## 🏗️ Arquitetura Resumida

```
Browser ←→ Laravel App (172.24.0.2) ←→ OnlyOffice (172.24.0.3)
           │
           ↓
        PostgreSQL Database
```

### Componentes Principais
- **Laravel Container**: Serve arquivos e processa callbacks
- **OnlyOffice Container**: Editor de documentos
- **Rede Docker**: `legisinc-network` para comunicação
- **Storage**: Arquivos DOCX em `storage/app/public/proposicoes/`

## 🔄 Status Atual

Com base nos testes mais recentes:

✅ **Funcionando**: Containers, rede, conectividade, callbacks  
✅ **Editor**: Carregamento e interface otimizada  
✅ **Salvamento**: Auto-save e callbacks implementados  
✅ **Diagnóstico**: Scripts de teste automatizados  
✅ **Documentação**: Guias completos disponíveis  

## 📞 Suporte

Para problemas não cobertos nesta documentação:

1. **Execute diagnóstico**: `./scripts/diagnose-onlyoffice.sh`
2. **Consulte troubleshooting**: [TROUBLESHOOTING_ONLYOFFICE.md](TROUBLESHOOTING_ONLYOFFICE.md)
3. **Verifique logs**: `docker logs legisinc-onlyoffice` e `storage/logs/laravel.log`
4. **Documentação técnica**: [ONLYOFFICE_INTEGRATION.md](ONLYOFFICE_INTEGRATION.md)

---

**Versão da Documentação**: 1.0  
**Última Atualização**: 24/07/2025  
**OnlyOffice Version**: 8.0  
**Laravel Version**: 10.x