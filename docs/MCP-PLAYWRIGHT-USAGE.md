# 🤖 Como Usar Playwright com MCP no Claude Code

## 📖 Visão Geral

O **MCP (Model Context Protocol) Playwright** permite que o Claude Code execute testes automatizados E2E diretamente através de comandos naturais, integrando perfeitamente com o sistema Legisinc.

## 🔧 Configuração Inicial

### 1. MCP Já Configurado
```bash
# MCP foi adicionado com:
claude mcp add playwright npx @playwright/mcp@latest
```

### 2. Verificar Configuração
```bash
# Ver MCPs disponíveis
/mcps

# Verificar Playwright MCP
/mcp status playwright
```

## 🎯 Comandos Básicos via MCP

### Executar Testes Completos
```
Executar todos os testes E2E do sistema Legisinc
```

### Testes Específicos por Módulo
```
Executar apenas os testes de autenticação

Executar testes do módulo de proposições

Executar testes de integração OnlyOffice

Executar testes de performance
```

### Testes com Interface Visual
```
Executar testes em modo visual para debug

Abrir interface UI do Playwright para análise

Executar teste específico com browser visível
```

### Debug e Troubleshooting
```
Executar testes em modo debug passo-a-passo

Mostrar último relatório de testes

Verificar se sistema está pronto para testes
```

## 🧪 Exemplos Práticos de Uso

### Cenário 1: Validação Após Deploy
```
User: "Preciso validar se o sistema está funcionando após o deploy"

Claude: Vou executar a suíte completa de testes E2E:
- Testes de autenticação (6 perfis)
- Fluxo de proposições completo  
- Integração OnlyOffice
- Métricas de performance

[Executa via MCP Playwright e mostra resultados]
```

### Cenário 2: Debug de Problema Específico
```
User: "O editor OnlyOffice não está funcionando, pode investigar?"

Claude: Vou executar testes específicos do OnlyOffice em modo debug:
- Carregamento do editor
- Aplicação de templates
- Sistema de callbacks
- Document keys únicos

[Executa testes OnlyOffice com --headed e analisa falhas]
```

### Cenário 3: Validação de Performance
```
User: "O sistema está lento, pode medir a performance?"

Claude: Executando testes de performance com métricas detalhadas:
- Tempo de carregamento de páginas
- Latência da API
- Consumo de memória
- Estabilidade com múltiplas sessões

[Executa testes de performance e analisa métricas]
```

### Cenário 4: Teste de Fluxo Específico
```
User: "Teste o fluxo completo de uma proposição do parlamentar até o protocolo"

Claude: Executando teste do fluxo legislativo completo:
1. Login como Parlamentar
2. Criação de proposição
3. Edição no OnlyOffice
4. Revisão pelo Legislativo
5. Protocolação
6. Verificação final

[Executa fluxo-completo.spec.js com relatório detalhado]
```

## 🔍 Comandos Avançados via MCP

### Análise de Falhas
```
Analisar falhas do último teste e sugerir correções

Mostrar screenshots das falhas encontradas

Extrair logs de erro dos testes falhados
```

### Comparação de Performance
```
Comparar performance atual com execução anterior

Identificar regressões de performance

Medir impacto de mudanças recentes
```

### Validação Específica
```
Testar apenas login de um perfil específico

Validar se template de moção está funcionando

Verificar se sistema de assinatura está operacional
```

## 📊 Interpretação de Resultados via MCP

### Relatórios Automáticos
O Claude pode:
- ✅ Interpretar resultados dos testes
- 📊 Analisar métricas de performance  
- 🔍 Identificar padrões de falha
- 💡 Sugerir correções automáticas
- 📈 Comparar com execuções anteriores

### Exemplo de Análise
```
User: "Analise os resultados do último teste"

Claude: Análise dos testes E2E executados:

✅ SUCESSOS (18/20 testes):
- Autenticação: 100% (6/6)
- Proposições: 90% (4/5) 
- Performance: Dentro dos limites

❌ FALHAS (2/20 testes):
- OnlyOffice: Template não aplicado
- Assinatura: PDF não gerado

🔧 CORREÇÕES SUGERIDAS:
1. Verificar variáveis do template
2. Validar configuração LibreOffice
3. Reiniciar container OnlyOffice

📊 PERFORMANCE:
- Dashboard: 1.2s (✅ < 3s)
- Lista: 3.8s (✅ < 5s)  
- Editor: 28s (✅ < 30s)
```

## 🎭 Comandos MCP Específicos

### Playwright Test Runner
```javascript
// Via MCP, Claude pode executar:
await playwright.test('tests/e2e/auth/login.spec.js')
await playwright.test('tests/e2e/proposicoes/', { headed: true })
await playwright.test({ grep: 'OnlyOffice' })
```

### Browser Automation
```javascript
// Inspeção direta via MCP:
await playwright.goto('http://localhost:8001')
await playwright.screenshot('current-state.png')
await playwright.fill('input[name="email"]', 'test@test.com')
```

### Configuration
```javascript
// Ajustar configuração via MCP:
await playwright.setConfig({ 
  timeout: 60000,
  retries: 2,
  reporter: 'html'
})
```

## 🚀 Workflows Inteligentes

### Auto-Diagnóstico
```
User: "O sistema não está funcionando bem"

Claude executa automaticamente:
1. Teste de conectividade básica
2. Validação de autenticação
3. Teste de funcionalidades críticas
4. Análise de performance
5. Relatório com diagnóstico
```

### Monitoramento Contínuo
```
User: "Configure monitoramento automático"

Claude pode configurar:
- Execução de testes a cada deploy
- Alertas para falhas críticas
- Relatórios de performance periódicos
- Validação de regressões
```

### Validação de Features
```
User: "Teste a nova funcionalidade X"

Claude pode:
1. Criar teste específico para feature X
2. Executar teste isolado
3. Validar integração com sistema existente
4. Medir impacto na performance
5. Documentar comportamento
```

## 🔧 Comandos de Manutenção

### Atualização de Testes
```
Atualizar testes após mudanças no sistema

Adicionar validação para nova funcionalidade

Ajustar timeouts para ambiente de produção
```

### Limpeza e Reset
```
Limpar dados de teste antigos

Resetar estado do banco para testes

Limpar cache de browsers do Playwright
```

### Configuração de Ambiente
```
Configurar testes para ambiente de staging

Ajustar URLs para diferentes ambientes

Configurar credenciais de teste
```

## 📋 Comandos Rápidos de Referência

| Comando Natural | Ação MCP |
|----------------|----------|
| "Execute todos os testes" | `npm test` |
| "Teste apenas autenticação" | `npm run test:auth` |
| "Debug visual OnlyOffice" | `npm run test:onlyoffice --headed` |
| "Abrir interface de testes" | `npm run test:ui` |
| "Ver último relatório" | `npm run test:report` |
| "Teste de performance" | `npm run test:performance` |
| "Debug passo-a-passo" | `npm run test:debug` |

## 🎯 Benefícios do MCP Integration

### Para Desenvolvedores
- 🤖 **Automação Inteligente**: Claude executa e analisa testes automaticamente
- 🔍 **Debug Assistido**: Análise de falhas com sugestões de correção
- 📊 **Relatórios Inteligentes**: Interpretação automática de métricas
- ⚡ **Execução Rápida**: Comandos naturais em vez de sintaxe complexa

### Para QA/Testes
- ✅ **Validação Completa**: Todos os fluxos testados automaticamente
- 🎭 **Cenários Realistas**: Testes simulam usuários reais
- 📈 **Métricas Detalhadas**: Performance e estabilidade medidas
- 🔄 **Regressão Automática**: Detecção de problemas introduzidos

### Para DevOps/Deploy
- 🚀 **Validação de Deploy**: Testes automáticos pós-deploy
- 🏗️ **CI/CD Integration**: Execução em pipelines automatizados
- 📊 **Monitoramento**: Saúde do sistema em tempo real
- 🔧 **Troubleshooting**: Diagnóstico rápido de problemas

## 💡 Dicas de Uso Eficiente

### 1. Execução Estratégica
```
# Início do dia
"Execute testes de smoke para validar sistema"

# Após mudanças
"Teste funcionalidades afetadas pela alteração X"

# Antes de deploy
"Execute suíte completa e valide performance"
```

### 2. Debug Eficiente
```
# Para problemas específicos
"Debug apenas o fluxo de assinatura em modo visual"

# Para análise detalhada
"Execute com traces habilitados e analise falhas"
```

### 3. Monitoramento Proativo
```
# Validação periódica
"Execute testes críticos e me informe se houver problemas"

# Análise de tendências
"Compare performance da última semana"
```

## ⚠️ Considerações Importantes

### Limitações
- MCP requer sistema rodando em localhost:8001
- Browsers precisam estar instalados (`npm run test:install`)
- Alguns testes podem ser sensíveis ao timing

### Melhores Práticas
- Execute `migrate:fresh --seed` antes de testar
- Use modo headed para debug visual
- Analise relatórios HTML para detalhes completos
- Configure timeouts adequados para seu ambiente

---

**🎊 Com MCP Playwright, você tem o poder de testar o sistema Legisinc através de comandos naturais, com Claude analisando e interpretando os resultados automaticamente!**

Execute: `"Teste o sistema completo e me dê um relatório"` para começar! 🚀