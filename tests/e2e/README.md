# Testes E2E com Playwright - Sistema Legisinc

## 🚀 Configuração e Execução

### Instalação
```bash
# Instalar dependências
npm install

# Instalar browsers do Playwright
npm run test:install
```

### Executar Testes
```bash
# Todos os testes
npm test

# Modo visual (com browser aberto)
npm run test:headed

# Mode debug interativo
npm run test:debug

# Interface UI do Playwright
npm run test:ui

# Testes específicos
npm run test:auth           # Testes de autenticação
npm run test:proposicoes    # Testes de proposições  
npm run test:onlyoffice     # Testes do OnlyOffice

# Ver relatório
npm run test:report
```

## 📁 Estrutura de Testes

```
tests/e2e/
├── auth/                   # Testes de autenticação
│   └── login.spec.js
├── proposicoes/           # Testes do módulo proposições
│   └── fluxo-completo.spec.js
├── onlyoffice/           # Testes de integração OnlyOffice
│   └── editor-integration.spec.js
├── performance/          # Testes de performance
│   └── load-testing.spec.js
├── utils/               # Helpers e utilitários
│   ├── auth-helper.js
│   └── proposicao-helper.js
└── setup/              # Configuração global
    ├── global.setup.js
    └── global.teardown.js
```

## 🧪 Tipos de Testes

### 1. Autenticação (`auth/`)
- ✅ Login de todos os perfis de usuário
- ✅ Logout e expiração de sessão
- ✅ Validação de permissões
- ✅ Redirecionamentos de segurança

### 2. Fluxo de Proposições (`proposicoes/`)
- ✅ Criação de proposições
- ✅ Fluxo completo: Parlamentar → Legislativo → Protocolo
- ✅ Validação de status e permissões
- ✅ Workflow de assinatura digital

### 3. Integração OnlyOffice (`onlyoffice/`)
- ✅ Carregamento do editor
- ✅ Aplicação de templates
- ✅ Sistema de callbacks e salvamento
- ✅ Persistência entre sessões
- ✅ Múltiplos usuários simultâneos
- ✅ Gerenciamento de document keys

### 4. Performance (`performance/`)
- ✅ Tempo de carregamento de páginas
- ✅ Performance da API
- ✅ Consumo de memória
- ✅ Estabilidade com múltiplas abas
- ✅ Carregamento do editor OnlyOffice

## 🔧 Helpers Disponíveis

### AuthHelper
```javascript
import { AuthHelper } from '../utils/auth-helper.js';

// Login por perfil
await AuthHelper.login(page, 'PARLAMENTAR');
await AuthHelper.login(page, 'LEGISLATIVO');
await AuthHelper.login(page, 'PROTOCOLO');

// Logout
await AuthHelper.logout(page);

// Verificar autenticação
const isAuth = await AuthHelper.isAuthenticated(page);
```

### ProposicaoHelper
```javascript
import { ProposicaoHelper } from '../utils/proposicao-helper.js';

// Criar proposição
const proposicao = await ProposicaoHelper.criarProposicao(page, {
  tipo: 'Moção',
  ementa: 'Minha proposição de teste',
  texto: 'Conteúdo da proposição'
});

// Buscar e abrir proposição
await ProposicaoHelper.abrirProposicao(page, 'Minha proposição');

// Abrir editor OnlyOffice
await ProposicaoHelper.abrirEditor(page);

// Processo de assinatura
await ProposicaoHelper.assinarDocumento(page);
```

## 📊 Relatórios e Métricas

### Métricas de Performance
- ⏱️ Tempo de carregamento de páginas
- 📡 Latência de requests da API
- 💾 Consumo de memória JavaScript
- 🌐 Estabilidade com múltiplas sessões

### Relatórios Gerados
- `playwright-report/` - Relatório HTML detalhado
- `test-results.json` - Dados em JSON
- `test-results.xml` - Formato JUnit (CI/CD)

## 🎯 Cenários de Teste

### Fluxo Parlamentar Completo
1. Login como Parlamentar
2. Criar nova proposição (Moção)
3. Abrir no editor OnlyOffice
4. Verificar template aplicado
5. Simular edição e salvamento
6. Logout

### Fluxo Legislativo
1. Login como Legislativo
2. Acessar proposição criada por Parlamentar
3. Abrir editor para revisão
4. Verificar se pode editar
5. Simular alterações
6. Salvar revisão

### Fluxo Protocolo
1. Login como Protocolo
2. Acessar proposições pendentes
3. Verificar opções de protocolação
4. Simular atribuição de número
5. Verificar mudança de status

### Testes de Integração
- ✅ OnlyOffice carrega corretamente
- ✅ Templates são aplicados
- ✅ Callbacks funcionam
- ✅ Document keys únicos
- ✅ Salvamento persistente

## 🔍 Debug e Troubleshooting

### Modo Debug
```bash
# Debug interativo
npm run test:debug

# Executar com browser visível
npm run test:headed

# Interface UI para análise
npm run test:ui
```

### Logs Detalhados
Os testes incluem logs extensivos:
- 🔐 Operações de autenticação
- 📝 Criação e edição de proposições
- 📡 Interceptação de requests
- ⏱️ Métricas de performance
- 🎯 Verificações de status

### Capturas e Vídeos
- 📸 Screenshots automáticos em falhas
- 🎥 Gravação de vídeo em falhas
- 📊 Traces detalhados para debug

## 🚨 Configurações Importantes

### Pré-requisitos
- Sistema rodando em `localhost:8001`
- Database resetado com `migrate:fresh --seed`
- OnlyOffice configurado e funcionando
- Usuários de teste criados

### Timeouts
- Ações: 30 segundos
- Testes: 60 segundos
- Editor OnlyOffice: 30 segundos
- Aguardar elementos: 10 segundos

### Browsers Testados
- ✅ Chromium (Desktop)
- ✅ Firefox (Desktop)
- ✅ Safari/WebKit (Desktop)
- ✅ Chrome Mobile
- ✅ Safari Mobile

## 📈 Métricas de Sucesso

### Performance
- Dashboard < 3s
- Lista proposições < 5s
- Criação proposição < 10s
- Editor OnlyOffice < 30s
- API requests < 2s médio

### Estabilidade
- Taxa de sucesso > 95%
- Sem vazamentos de memória
- Suporte múltiplas abas
- Recuperação de falhas de rede

### Funcionalidade
- Todos os perfis fazem login
- Fluxo completo funciona
- OnlyOffice integra corretamente
- Assinatura digital funciona
- Permissões respeitadas

---

**Status**: ✅ Configuração completa e funcional  
**Última atualização**: 18/08/2025  
**Compatibilidade**: Playwright 1.54.2+