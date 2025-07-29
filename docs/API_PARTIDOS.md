# API de Partidos Políticos - LegisCorp

## 📋 Resumo da Implementação

Foi criada uma API completa para gerenciar dados de partidos políticos brasileiros, incluindo preenchimento automático de formulários e validações em tempo real.

## 🚀 Endpoints da API

### Base URL: `/api/partidos`

#### **1. Lista de Partidos Cadastrados**
```
GET /api/partidos/
```
- **Parâmetros**: 
  - `status` (opcional): ativo/inativo
  - `search` (opcional): termo de busca
  - `per_page` (opcional): paginação
  - `order_by` (opcional): campo de ordenação
  - `order_direction` (opcional): asc/desc

#### **2. Partidos Brasileiros (Base de Dados)**
```
GET /api/partidos/brasileiros
```
- Retorna lista completa dos principais partidos políticos do Brasil

#### **3. Buscar Partido por Sigla**
```
GET /api/partidos/buscar-sigla?sigla=PT
```
- Busca primeiro no banco local, depois na base de partidos brasileiros

#### **4. Dados Externos de Partidos**
```
GET /api/partidos/buscar-externos?sigla=PT
```
- Consulta dados externos (TSE, APIs externas)

#### **5. Validações**
```
GET /api/partidos/validar-sigla?sigla=PT&id=1
GET /api/partidos/validar-numero?numero=13&id=1
```

#### **6. Estatísticas**
```
GET /api/partidos/estatisticas
```

#### **7. Partido Específico**
```
GET /api/partidos/{id}
```

## 🔧 Funcionalidades Implementadas

### **1. Formulário Inteligente de Criação**
- ✅ **Preenchimento Automático**: Busca dados por sigla
- ✅ **Validação em Tempo Real**: Sigla e número únicos
- ✅ **Feedback Visual**: Indicadores de disponibilidade
- ✅ **Integração AJAX**: Sem recarregar página

### **2. Base de Dados de Partidos Brasileiros**
- ✅ **25+ Partidos**: Lista completa dos principais partidos
- ✅ **Dados Básicos**: Sigla, nome e número
- ✅ **Interface Visual**: Cards com botão "Criar Partido"
- ✅ **Preenchimento Automático**: Link direto para formulário

### **3. API Controller Completo**
- ✅ **Cache**: Sistema de cache para dados externos
- ✅ **Tratamento de Erros**: Responses padronizados
- ✅ **Validações**: Sigla e número únicos
- ✅ **Estatísticas**: Dados consolidados
- ✅ **Paginação**: Para listas grandes

## 📁 Arquivos Criados/Modificados

### **Novos Arquivos:**
- `app/Http/Controllers/Api/PartidoApiController.php`
- `resources/views/modules/partidos/brasileiros.blade.php`
- `API_PARTIDOS.md`

### **Arquivos Modificados:**
- `routes/api.php` - Rotas da API
- `routes/web.php` - Rota para view brasileiros
- `resources/views/modules/partidos/create.blade.php` - AJAX integration
- `resources/views/components/layouts/aside/aside.blade.php` - Menu link

## 🎯 Como Usar

### **1. Criar Novo Partido**
1. Acesse: **Partidos > Novo Partido**
2. Digite a sigla (ex: "PT")
3. Clique em **"Buscar Dados"**
4. Formulário será preenchido automaticamente
5. Complete os dados e salve

### **2. Ver Partidos Brasileiros**
1. Acesse: **Partidos > Partidos Brasileiros**
2. Clique em **"Carregar Lista"**
3. Escolha um partido e clique **"Criar Partido"**
4. Será redirecionado para formulário pré-preenchido

### **3. Validação Automática**
- Ao digitar sigla/número, validação acontece automaticamente
- Feedback visual indica se está disponível ou em uso

## 🔗 Exemplos de Uso da API

### **JavaScript - Buscar Dados de Partido**
```javascript
fetch('/api/partidos/buscar-sigla?sigla=PT')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Partido:', data.data);
      // Preencher formulário
    }
  });
```

### **JavaScript - Validar Sigla**
```javascript
fetch('/api/partidos/validar-sigla?sigla=NOVO&id=1')
  .then(response => response.json())
  .then(data => {
    if (data.exists) {
      console.log('Sigla já em uso');
    }
  });
```

## 💡 Benefícios

- **⚡ Agilidade**: Preenchimento automático de formulários
- **✅ Confiabilidade**: Validação em tempo real
- **📊 Consistência**: Base de dados padronizada
- **🔄 Flexibilidade**: API extensível para futuras integrações
- **👥 Usabilidade**: Interface intuitiva e responsiva

## 🔮 Futuras Expansões

- Integração com API do TSE
- Importação automática de parlamentares por partido
- Histórico de mudanças nos partidos
- Sincronização com bases externas
- Notificações de novos partidos registrados

---

**Status**: ✅ **Implementação Concluída**  
**Testes**: ✅ **Endpoints funcionais**  
**Documentação**: ✅ **Completa**