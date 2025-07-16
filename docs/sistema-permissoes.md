# Sistema de Permissões - Documentação Completa

## 📋 Índice
- [Visão Geral](#visão-geral)
- [Interface da Tela](#interface-da-tela)
- [Funcionalidades](#funcionalidades)
- [Perfis de Usuário](#perfis-de-usuário)
- [Componentes da Tela](#componentes-da-tela)
- [Fluxo de Uso](#fluxo-de-uso)
- [Comandos do Sistema](#comandos-do-sistema)
- [Arquitetura Técnica](#arquitetura-técnica)

---

## 🎯 Visão Geral

O Sistema de Permissões é uma ferramenta avançada que permite ao administrador controlar **quais telas cada tipo de usuário pode acessar** no sistema. É baseado no princípio de **permissões explícitas**: apenas as telas marcadas como liberadas aparecerão no menu lateral para aquele perfil de usuário.

### Características Principais:
- ✅ **Controle Granular**: Define acesso por tela específica
- ✅ **Dashboard Universal**: Sempre acessível para todos os usuários
- ✅ **Interface Intuitiva**: Cards visuais para cada módulo do sistema
- ✅ **Segurança**: Apenas permissões explícitas são válidas
- ✅ **Automação**: Inicialização automática do sistema

---

## 🖥️ Interface da Tela

### **Localização**
**Administração > Configurações > Permissões**  
*Rota: `/admin/screen-permissions`*

### **Acesso**
Apenas usuários com perfil **ADMIN** podem acessar esta tela.

---

## 📊 Componentes da Tela

### **1. Estatísticas do Sistema**
Quatro cards informativos na parte superior:

#### **Cache Hit Ratio**
- **Função**: Mostra eficiência do cache de permissões
- **Formato**: `89.5%` (hits/total)
- **Detalhes**: Exibe quantos acessos foram servidos pelo cache

#### **Total Permissões**
- **Função**: Quantidade total de permissões configuradas no sistema
- **Formato**: Número absoluto (ex: `56`)
- **Detalhes**: Inclui todas as combinações role + tela

#### **Permissões Ativas**
- **Função**: Quantas permissões estão habilitadas (can_access = true)
- **Formato**: Número absoluto (ex: `23`)
- **Detalhes**: Telas efetivamente liberadas para acesso

#### **Cobertura**
- **Função**: Percentual de perfis que têm permissões configuradas
- **Formato**: Porcentagem (ex: `85%`)
- **Detalhes**: Indica se todos os perfis foram configurados

### **2. Seletor de Perfil**
- **Localização**: Dropdown no canto superior direito
- **Função**: Escolher qual tipo de usuário configurar
- **Opções**: ADMIN, LEGISLATIVO, PARLAMENTAR, RELATOR, PROTOCOLO, ASSESSOR, CIDADAO_VERIFICADO, PUBLICO

### **3. Botões de Ação**

#### **Salvar Alterações**
- **Função**: Salva as permissões marcadas para o perfil selecionado
- **Estado**: Desabilitado até que um perfil seja selecionado
- **Comportamento**: Atualiza banco de dados e limpa cache

#### **Restaurar Padrão**
- **Função**: Volta às permissões padrão do perfil
- **Estado**: Desabilitado até que um perfil seja selecionado
- **Comportamento**: Reseta para configuração inicial

#### **Inicializar Sistema**
- **Função**: Configura automaticamente todo o sistema de permissões
- **Estado**: Sempre disponível
- **Comportamento**: Cria estrutura completa de permissões

### **4. Cards de Módulos**
Cada módulo do sistema é representado por um card visual:

#### **Estrutura do Card**
```
┌─────────────────────────────────────┐
│ 🏠 Dashboard                   100% │
│ 1/1 permissões              ativo  │
│ ████████████████████████████████   │
│                                     │
│ ☑️ Painel Principal                │
│     dashboard.index                 │
│                                     │
│ Status: Ativo                      │
└─────────────────────────────────────┘
```

#### **Elementos do Card**
- **Ícone + Nome**: Identifica o módulo
- **Progresso**: Barra visual (1/1 = 100%)
- **Status**: Completo/Parcial/Desabilitado
- **Checkboxes**: Uma para cada tela do módulo
- **Rota**: Nome técnico da rota

### **5. Informações do Perfil Selecionado**
Quando um perfil é escolhido, aparece um card informativo:
- **Nome do Perfil**: Ex: "Parlamentar"
- **Descrição**: Explicação do que o perfil faz
- **Nível**: Hierarquia numérica do perfil

---

## 🔧 Funcionalidades

### **1. Inicializar Sistema**
**O que faz:**
- Cria **todas** as permissões possíveis para **todos** os perfis
- Habilita **Dashboard** para todos automaticamente
- Configura **ADMIN** com acesso total
- Deixa outros perfis com **apenas Dashboard** habilitado

**Quando usar:**
- ✅ Primeira configuração do sistema
- ✅ Estatísticas mostrando "0 permissões"
- ✅ Quero resetar tudo e começar do zero

**Resultado:**
```
ADMIN:       ✅ Todas as telas
PARLAMENTAR: ✅ Dashboard apenas
LEGISLATIVO: ✅ Dashboard apenas
RELATOR:     ✅ Dashboard apenas
... (outros perfis)
```

### **2. Configurar Permissões por Perfil**
**Como funciona:**
1. Seleciona um perfil no dropdown
2. Marca/desmarca as telas desejadas
3. Clica "Salvar Alterações"
4. Sistema atualiza permissões e cache

**Regras:**
- ✅ **Dashboard**: Sempre marcado, não pode ser desmarcado
- ✅ **Admin**: Todas as telas marcadas, não podem ser desmarcadas
- ✅ **Outros**: Livremente configuráveis

### **3. Visualização em Tempo Real**
**Cards Dinâmicos:**
- **Progresso**: Atualiza conforme marca/desmarca telas
- **Status**: Muda de "Desabilitado" → "Parcial" → "Completo"
- **Contadores**: Mostra X/Y permissões ativas

**Cores dos Status:**
- 🔴 **Desabilitado**: Nenhuma tela marcada
- 🟡 **Parcial**: Algumas telas marcadas
- 🟢 **Completo**: Todas as telas marcadas

---

## 👥 Perfis de Usuário

### **Hierarquia de Perfis**
```
ADMIN (100)           ← Acesso total
├── LEGISLATIVO (80)   ← Staff legislativo
├── PARLAMENTAR (70)   ← Membros do parlamento
├── RELATOR (65)       ← Relatores de projetos
├── PROTOCOLO (50)     ← Oficiais de protocolo
├── ASSESSOR (40)      ← Assessores
├── CIDADAO_VERIFICADO (20) ← Cidadãos verificados
└── PUBLICO (10)       ← Acesso público
```

### **Comportamento por Perfil**

#### **ADMIN**
- **Acesso**: Todas as telas sempre
- **Interface**: Todos os switches marcados e desabilitados
- **Botões**: "Salvar" e "Restaurar" desabilitados
- **Mensagem**: "Todas as telas estão habilitadas por padrão"

#### **Outros Perfis**
- **Acesso**: Apenas telas explicitamente liberadas + Dashboard
- **Interface**: Switches editáveis (exceto Dashboard)
- **Botões**: "Salvar" e "Restaurar" habilitados
- **Mensagem**: Instruções de configuração

---

## 📋 Fluxo de Uso

### **Cenário 1: Primeira Configuração**
```
1. Admin acessa tela → Vê "Sistema Não Inicializado"
2. Clica "Inicializar Sistema" → Sistema cria estrutura básica
3. Página recarrega → Estatísticas mostram dados reais
4. Seleciona perfil → Configura permissões específicas
5. Salva alterações → Usuários veem apenas telas liberadas
```

### **Cenário 2: Configuração de Novo Perfil**
```
1. Seleciona perfil no dropdown → Dashboard já aparece marcado
2. Marca telas desejadas → Progresso atualiza em tempo real
3. Clica "Salvar" → Permissões são persistidas
4. Usuários deste perfil → Veem apenas telas selecionadas
```

### **Cenário 3: Ajuste de Permissões Existentes**
```
1. Seleciona perfil → Permissões atuais são carregadas
2. Modifica marcações → Sistema detecta mudanças
3. Salva alterações → Cache é limpo automaticamente
4. Efeito imediato → Usuários veem mudanças na próxima navegação
```

---

## 🛠️ Comandos do Sistema

### **Artisan Commands**

#### **Inicializar Permissões**
```bash
php artisan permissions:initialize
```
**Função**: Cria estrutura completa de permissões  
**Resultado**: Dashboard para todos + Admin com tudo + outros vazios

#### **Limpar Permissões Padrão**
```bash
php artisan permissions:clear-defaults
```
**Função**: Remove permissões antigas, força reconfiguração  
**Resultado**: Apenas Admin mantém permissões

### **Rotas da API**

#### **Carregar Permissões**
```
GET /admin/screen-permissions/role/{role}
```
**Função**: Obtém permissões de um perfil específico

#### **Salvar Permissões**
```
POST /admin/screen-permissions
```
**Função**: Atualiza permissões de um perfil

#### **Inicializar via Web**
```
POST /admin/screen-permissions/initialize
```
**Função**: Executa inicialização via interface web

---

## 🏗️ Arquitetura Técnica

### **Modelos de Dados**

#### **ScreenPermission**
```php
// Tabela: screen_permissions
- role_name: string         // ADMIN, PARLAMENTAR, etc.
- screen_route: string      // dashboard.index, parlamentares.index
- screen_name: string       // "Painel Principal", "Lista Parlamentares"
- screen_module: string     // dashboard, parlamentares, etc.
- can_access: boolean       // true/false
```

#### **SystemModule (Enum)**
```php
// Define módulos disponíveis
DASHBOARD → ['dashboard.index' => 'Painel Principal']
PARLAMENTARES → ['parlamentares.index' => 'Lista', ...]
COMISSOES → ['comissoes.index' => 'Lista', ...]
// ... outros módulos
```

#### **UserRole (Enum)**
```php
// Define perfis de usuário
ADMIN(100), LEGISLATIVO(80), PARLAMENTAR(70), 
RELATOR(65), PROTOCOLO(50), ASSESSOR(40),
CIDADAO_VERIFICADO(20), PUBLICO(10)
```

### **Serviços**

#### **PermissionManagementService**
- Gerencia matriz de permissões
- Calcula estatísticas
- Sincroniza com rotas
- Import/Export de permissões

#### **PermissionCacheService**
- Cache de permissões por usuário
- Estatísticas de hit/miss
- Limpeza automática
- Pré-carregamento

### **Middleware**

#### **CheckScreenPermission**
- Intercepta todas as rotas protegidas
- Verifica permissões via cache
- Registra tentativas de acesso negado
- Redireciona ou bloqueia acesso

### **Lógica de Verificação**
```php
// Ordem de prioridade
1. Admin → Sempre TRUE
2. Permissões configuradas → Usar valor salvo
3. Dashboard → Sempre TRUE para qualquer usuário
4. Outras rotas → FALSE (acesso negado)
```

---

## 🔒 Segurança e Comportamento

### **Princípios de Segurança**
- **Negação por Padrão**: Sem permissão explícita = sem acesso
- **Dashboard Universal**: Garantia de acesso mínimo
- **Admin Irrestrito**: Sempre tem acesso total
- **Cache Seguro**: Limpeza automática após mudanças

### **Comportamento no Menu Lateral**
- **Com Permissão**: Item aparece no aside
- **Sem Permissão**: Item não renderiza
- **Dashboard**: Sempre visível para todos
- **Módulos**: Só aparecem se têm pelo menos 1 rota liberada

### **Fallbacks de Segurança**
- **Banco Indisponível**: Admin mantém acesso, outros só Dashboard
- **Cache Vazio**: Consulta banco diretamente
- **Permissão Não Encontrada**: Aplica regra padrão
- **Erro no Sistema**: Nega acesso por segurança

---

## 📝 Mensagens e Notificações

### **Mensagens Informativas**
- ✅ **Sucesso**: "Permissões salvas com sucesso! As telas selecionadas agora aparecerão no menu lateral dos usuários deste perfil."
- ⚠️ **Dashboard**: "O Dashboard não pode ser removido. Todos os usuários devem ter acesso ao Dashboard."
- ℹ️ **Admin**: "Perfil Administrador: Todas as telas estão habilitadas por padrão e não podem ser desabilitadas."
- 🔄 **Inicialização**: "Sistema de permissões inicializado com sucesso! Dashboard habilitado para todos os perfis."

### **Estados da Interface**
- **Carregando**: Spinner durante operações
- **Vazio**: Instruções para selecionar perfil
- **Configurado**: Cards com permissões carregadas
- **Não Inicializado**: Aviso para usar "Inicializar Sistema"

---

## 🎯 Casos de Uso Práticos

### **Caso 1: Restringir Acesso de Assessores**
```
Objetivo: Assessores só podem ver Dashboard e Parlamentares
Ação:
1. Seleciona "ASSESSOR"
2. Marca apenas "parlamentares.index"
3. Salva
Resultado: Assessores veem só Dashboard + Lista de Parlamentares
```

### **Caso 2: Liberar Projetos para Protocolo**
```
Objetivo: Protocolo precisa gerenciar projetos
Ação:
1. Seleciona "PROTOCOLO"
2. Marca "projetos.index" e "projetos.create"
3. Salva
Resultado: Protocolo pode listar e criar projetos
```

### **Caso 3: Configuração Completa para Legislativo**
```
Objetivo: Staff legislativo precisa de acesso amplo
Ação:
1. Seleciona "LEGISLATIVO"
2. Marca todas as telas necessárias
3. Salva
Resultado: Legislativo vê menu completo conforme marcado
```

---

Esta documentação cobre todos os aspectos do Sistema de Permissões. Para dúvidas técnicas, consulte o código-fonte nos arquivos mencionados na seção de Arquitetura Técnica.