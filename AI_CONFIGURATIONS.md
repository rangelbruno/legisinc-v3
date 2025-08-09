# Sistema de Configurações de IA - Múltiplas APIs com Fallback

Este sistema permite configurar múltiplas APIs de IA (OpenAI, Anthropic, Google, Local) com fallback automático para garantir disponibilidade e controle de custos.

## ✨ Características Principais

### 🔄 Fallback Automático
- **Priorização**: Configure a ordem de tentativa das APIs
- **Recuperação**: Se uma API falhar ou atingir o limite, tenta a próxima automaticamente
- **Logging**: Registra todas as tentativas e falhas para monitoramento

### 💰 Controle de Custos
- **Limites Diários**: Defina limite de tokens por configuração
- **Estimativa de Custos**: Calcule custos por 1000 tokens
- **Contador Automático**: Reset diário automático dos contadores
- **Monitoramento**: Dashboard com estatísticas de uso

### 🔧 Provedores Suportados
- **OpenAI**: GPT-4, GPT-4-turbo, GPT-3.5-turbo
- **Anthropic**: Claude-3.5-sonnet, Claude-3-opus, Claude-3-haiku  
- **Google**: Gemini-1.5-pro, Gemini-1.5-flash
- **Local**: Ollama (sem custos)

### 🛡️ Segurança
- **Criptografia**: API keys são criptografadas no banco
- **Ocultação**: Chaves nunca são expostas na interface
- **Validação**: Teste de conexão antes de salvar

## 🚀 Como Usar

### 1. Migrar o Banco de Dados

```bash
php artisan migrate --path=database/migrations/2025_08_09_create_ai_configurations_table.php
```

### 2. Popular Dados de Exemplo

```bash
php artisan db:seed --class=AIConfigurationSeeder
```

### 3. Acessar Interface

Acesse `/admin/parametros/configurar-ia` que agora redireciona para `/admin/ai-configurations`

### 4. Configurar APIs

1. **Criar Nova Configuração**
   - Vá em "Nova Configuração"
   - Escolha o provedor (OpenAI, Anthropic, Google, Local)
   - Configure API key, modelo e parâmetros
   - Defina prioridade (1 = primeira tentativa)
   - Teste a conexão antes de salvar

2. **Configurar Limites** (opcional)
   - Defina limite diário de tokens
   - Configure custo por 1000 tokens
   - Sistema resetará contadores automaticamente

3. **Ativar Configurações**
   - Apenas configurações ativas são usadas
   - Configure múltiplas APIs ativas para redundância

## 📊 Funcionamento do Fallback

```
Requisição de Geração de Texto
       ↓
1. Buscar configurações ativas ordenadas por prioridade
       ↓
2. Para cada configuração:
   - Verificar se pode ser usada (ativa + tokens disponíveis)
   - Tentar gerar texto
   - Se sucesso: retornar resultado + contar tokens
   - Se falha: tentar próxima configuração
       ↓
3. Se todas falharem: retornar erro com detalhes
```

## 🔧 Estrutura do Código

### Arquivos Criados/Modificados

```
database/migrations/2025_08_09_create_ai_configurations_table.php
app/Models/AIConfiguration.php
app/Http/Controllers/Admin/AIConfigurationController.php
app/Services/AI/AITextGenerationService.php (modificado)
resources/views/admin/ai-configurations/
├── index.blade.php
├── create.blade.php
└── show.blade.php
database/seeders/AIConfigurationSeeder.php
```

### Rotas Adicionadas

```php
// Web Routes
/admin/ai-configurations/*

// API Routes  
/api/ai/test-connection
/api/ai/configurations
/api/ai/stats
```

## 📝 Exemplo de Configuração

```json
{
  "name": "OpenAI Principal",
  "provider": "openai",
  "model": "gpt-4o",
  "api_key": "sk-...", // Criptografado
  "max_tokens": 4000,
  "temperature": 0.7,
  "priority": 1,
  "daily_token_limit": 50000,
  "cost_per_1k_tokens": 0.01,
  "is_active": true
}
```

## 🔍 Monitoramento

### Dashboard Principal
- Total de configurações
- Configurações ativas/inativas
- Status de saúde (testadas recentemente)
- Uso diário de tokens

### Por Configuração
- Tokens usados vs limite
- Porcentagem de uso diário
- Último teste de conexão
- Histórico de erros

## 🚨 Solução de Problemas

### Configuração Não Funciona
1. Verifique se a API key está correta
2. Teste a conexão na interface
3. Verifique logs em `storage/logs/laravel.log`
4. Confirme se o modelo está disponível

### Fallback Não Ativou
1. Verifique se há outras configurações ativas
2. Confirme a ordem de prioridades
3. Verifique se as configurações podem ser usadas (limites)

### Tokens Esgotaram
1. Resetar contador manualmente na interface
2. Aumentar limite diário
3. Configurar API adicional como backup

## 🔄 Migração do Sistema Antigo

O sistema antigo (parâmetros simples) é automaticamente redirecionado para o novo sistema. Para migrar configurações existentes:

1. Extrair valores do sistema antigo
2. Criar novas configurações via interface
3. Testar funcionamento
4. Desativar sistema antigo

## 📈 Vantagens do Novo Sistema

### Antes (Sistema Simples)
- ✗ Uma única API
- ✗ Se falha, sistema para
- ✗ Sem controle de custos
- ✗ API key em texto plano
- ✗ Sem monitoramento

### Depois (Múltiplas APIs)
- ✅ Múltiplas APIs com fallback
- ✅ Sistema sempre funciona (se pelo menos uma API ativa)
- ✅ Controle granular de custos
- ✅ API keys criptografadas
- ✅ Dashboard completo de monitoramento
- ✅ Fácil teste e validação

## 🛠️ Próximos Passos

1. **Instalar dependências** (se necessário)
2. **Executar migration**
3. **Popular dados de exemplo**
4. **Configurar APIs reais**
5. **Testar geração de texto**
6. **Monitorar uso e custos**

---

**Observação**: Lembre-se de substituir as API keys de exemplo pelas chaves reais e configurar os limites apropriados para seu uso.