# Sistema de Parâmetros - LegisInc

## 📋 Visão Geral

O Sistema de Parâmetros do LegisInc é uma funcionalidade robusta que permite configurar e gerenciar valores dinâmicos da aplicação sem necessidade de alterar código. O sistema oferece uma interface web intuitiva para administradores gerenciarem configurações globais do sistema.

## 🎯 Para que Serve

O sistema de parâmetros permite:

- **Configurações Dinâmicas**: Alterar comportamentos do sistema sem deployments
- **Personalização**: Adaptar o sistema às necessidades específicas da instituição
- **Flexibilidade**: Modificar valores em tempo real através da interface web
- **Auditoria**: Rastrear todas as alterações realizadas nos parâmetros
- **Organização**: Agrupar parâmetros por categoria para facilitar gestão

## 🔧 Funcionalidades

### ✅ **Tipos de Parâmetros Suportados:**
- **String**: Textos e mensagens
- **Integer**: Números inteiros
- **Boolean**: Valores verdadeiro/falso
- **Email**: Endereços de e-mail
- **URL**: Links e endereços web
- **JSON**: Dados estruturados
- **Array**: Listas de valores
- **Date**: Datas
- **Time**: Horários
- **DateTime**: Data e hora
- **Color**: Cores hexadecimais
- **File**: Caminhos de arquivos
- **Text**: Textos longos
- **Number**: Números decimais
- **Password**: Senhas (criptografadas)

### 📁 **Grupos de Parâmetros:**
- **Sistema**: Configurações gerais
- **Legislativo**: Configurações específicas do legislativo
- **Notificações**: Configurações de e-mail e alertas
- **Segurança**: Configurações de segurança
- **Interface**: Personalizações da interface
- **Performance**: Configurações de performance
- **Integração**: APIs e integrações externas
- **Backup**: Configurações de backup

## 🚀 Como Acessar

### 1. **Login no Sistema**
```
URL: http://localhost:8001/login
Email: admin@sistema.gov.br
Senha: 123456
```

### 2. **Navegação**
- **Via Menu**: Administração → Parâmetros
- **URL Direta**: `http://localhost:8001/admin/parametros`

## 📝 Como Criar um Parâmetro

### Passo 1: Acessar a Tela de Criação
1. Vá para **Administração** → **Parâmetros**
2. Clique no botão **"Novo Parâmetro"**

### Passo 2: Preencher Informações Básicas
- **Nome**: Nome descritivo do parâmetro
- **Código**: Identificador único (usado no código)
- **Descrição**: Descrição detalhada da funcionalidade
- **Texto de Ajuda**: Orientações para o usuário

### Passo 3: Configurar Parâmetro
- **Grupo**: Selecionar categoria apropriada
- **Tipo**: Escolher tipo de dados
- **Valor**: Definir valor inicial
- **Valor Padrão**: Valor usado quando parâmetro está vazio
- **Ordem**: Posição na listagem

### Passo 4: Definir Status
- **Obrigatório**: Se o parâmetro é obrigatório
- **Editável**: Se pode ser alterado pela interface
- **Visível**: Se aparece na interface
- **Ativo**: Se o parâmetro está ativo

## 💡 Exemplos Práticos

### Exemplo 1: Configurar Nome do Sistema
```php
// Parâmetro
Nome: Nome do Sistema
Código: sistema.nome
Tipo: String
Valor: "Câmara Municipal de São Paulo"
Grupo: Sistema

// Uso no código
echo nome_sistema(); // "Câmara Municipal de São Paulo"
echo parametro('sistema.nome'); // "Câmara Municipal de São Paulo"
```

### Exemplo 2: Configurar Limite de Sessões
```php
// Parâmetro
Nome: Limite de Sessões Simultâneas
Código: sistema.limite_sessoes
Tipo: Integer
Valor: 5
Grupo: Sistema

// Uso no código
$limite = parametro('sistema.limite_sessoes', 3);
if ($sessoesAtivas > $limite) {
    // Bloquear nova sessão
}
```

### Exemplo 3: Configurar E-mail do Administrador
```php
// Parâmetro
Nome: E-mail do Administrador
Código: sistema.admin_email
Tipo: Email
Valor: "admin@camara.sp.gov.br"
Grupo: Sistema

// Uso no código
Mail::to(admin_email())->send(new SystemAlert($message));
```

### Exemplo 4: Configurar Horário de Funcionamento
```php
// Parâmetro
Nome: Horário de Início
Código: sistema.horario_inicio
Tipo: Time
Valor: "08:00"
Grupo: Sistema

// Uso no código
$horarioInicio = parametro('sistema.horario_inicio', '08:00');
if (now()->format('H:i') < $horarioInicio) {
    // Sistema fora do horário
}
```

### Exemplo 5: Configurar Recursos Ativos
```php
// Parâmetro
Nome: Recursos Ativos
Código: sistema.recursos_ativos
Tipo: JSON
Valor: {"votacao": true, "tramitacao": false, "comissoes": true}
Grupo: Sistema

// Uso no código
$recursos = parametro('sistema.recursos_ativos', '{}');
$recursos = json_decode($recursos, true);
if ($recursos['votacao']) {
    // Mostrar módulo de votação
}
```

## 🛠️ Uso no Código

### Helpers Disponíveis

#### 1. **Função Global `parametro()`**
```php
// Buscar parâmetro com valor padrão
$valor = parametro('codigo_parametro', 'valor_padrao');

// Exemplos
$nomeSystem = parametro('sistema.nome', 'Sistema Legislativo');
$emailAdmin = parametro('sistema.admin_email', 'admin@sistema.gov.br');
```

#### 2. **Helpers Específicos**
```php
// Helpers pré-definidos
nome_sistema()                    // sistema.nome
admin_email()                     // sistema.admin_email
timezone_sistema()               // sistema.timezone
versao_sistema()                 // sistema.versao
```

#### 3. **Classe ParametroHelper**
```php
use App\Helpers\ParametroHelper;

// Buscar parâmetro
$valor = ParametroHelper::obter('codigo_parametro');

// Verificar se existe
$existe = ParametroHelper::existe('codigo_parametro');

// Buscar por grupo
$parametros = ParametroHelper::obterPorGrupo('sistema');
```

## 🎨 Interface da Tela

### Visualização em Grade
- **Cards visuais** para cada parâmetro
- **Filtros** por grupo e tipo
- **Busca** por nome ou código
- **Ações rápidas** (editar, duplicar, status)

### Visualização em Lista
- **Tabela detalhada** com todas as informações
- **Ordenação** por colunas
- **Exportação** para CSV/Excel
- **Ações em lote**

### Recursos da Interface
- **Responsiva**: Funciona em desktop e mobile
- **Temas**: Suporte a tema claro/escuro
- **Filtros avançados**: Múltiplos critérios
- **Exportação**: Backup de configurações

## 📊 Funcionalidades Avançadas

### 1. **Cache Inteligente**
```php
// O sistema automaticamente gerencia cache
$valor = parametro('sistema.nome'); // Primeira chamada: consulta DB
$valor = parametro('sistema.nome'); // Segunda chamada: retorna do cache
```

### 2. **Validação por Tipo**
- **E-mail**: Validação de formato
- **URL**: Verificação de URL válida
- **JSON**: Validação de sintaxe JSON
- **Color**: Verificação de cor hexadecimal
- **Integer**: Validação de número inteiro

### 3. **Auditoria**
- **Histórico completo** de alterações
- **Usuário** que fez a alteração
- **Data/hora** da modificação
- **Valores anterior e novo**
- **IP** e **User Agent**

### 4. **API REST**
```php
// Endpoints disponíveis
GET /api/parametros                    // Listar todos
GET /api/parametros/{codigo}           // Buscar por código
PUT /api/parametros/{codigo}           // Atualizar valor
GET /api/parametros/grupo/{grupo}      // Listar por grupo
```

## 🔒 Segurança

### Permissões Necessárias
- **parametros.view**: Visualizar parâmetros
- **parametros.create**: Criar parâmetros
- **parametros.edit**: Editar parâmetros
- **parametros.delete**: Excluir parâmetros

### Controle de Acesso
- Apenas **administradores** podem gerenciar parâmetros
- **Middleware** de autenticação e autorização
- **Logs** de auditoria para todas as ações

## 🔧 Administração

### Backup e Restore
```php
// Exportar configurações
GET /admin/parametros/exportar

// Importar configurações
POST /admin/parametros/importar
```

### Gerenciamento de Cache
```php
// Limpar cache de parâmetros
php artisan cache:clear

// Ou via interface administrativa
POST /admin/parametros/cache/clear
```

## 📈 Casos de Uso Reais

### 1. **Configuração de Sistema**
```php
// Definir informações básicas
parametro('sistema.nome', 'Câmara Municipal');
parametro('sistema.versao', '2.1.0');
parametro('sistema.timezone', 'America/Sao_Paulo');
```

### 2. **Configuração de E-mail**
```php
// Configurar SMTP
parametro('email.smtp_host', 'smtp.gmail.com');
parametro('email.smtp_port', '587');
parametro('email.from_name', 'Sistema Legislativo');
```

### 3. **Configuração de Limites**
```php
// Definir limites do sistema
parametro('sistema.max_upload_size', '10485760'); // 10MB
parametro('sistema.session_timeout', '120'); // 2 horas
parametro('sistema.max_tentativas_login', '5');
```

### 4. **Configuração de Interface**
```php
// Personalizar interface
parametro('interface.tema_padrao', 'dark');
parametro('interface.logo_url', '/assets/images/logo.png');
parametro('interface.mostrar_breadcrumb', 'true');
```

## 🎯 Benefícios

### Para Desenvolvedores
- **Código limpo**: Sem valores hardcoded
- **Flexibilidade**: Configurações dinâmicas
- **Manutenibilidade**: Fácil alteração de comportamentos

### Para Administradores
- **Autonomia**: Alterar configurações sem programador
- **Interface amigável**: Sem necessidade de conhecimento técnico
- **Controle total**: Gerenciar todos os aspectos do sistema

### Para a Instituição
- **Personalização**: Adaptar sistema às necessidades
- **Agilidade**: Mudanças imediatas sem deploy
- **Auditoria**: Rastreamento completo de alterações

## 🔄 Fluxo de Trabalho

### 1. **Criação de Parâmetro**
```
Identificar necessidade → Criar parâmetro → Configurar → Testar → Ativar
```

### 2. **Uso no Código**
```php
// Sempre usar helpers para acessar parâmetros
$valor = parametro('meu_parametro', 'valor_padrao');

// Evitar acesso direto ao banco
// ❌ Parametro::where('codigo', 'meu_parametro')->first()
// ✅ parametro('meu_parametro')
```

### 3. **Manutenção**
```
Monitorar uso → Atualizar valores → Verificar impacto → Documentar
```

## 📚 Referência Rápida

### Comandos Úteis
```bash
# Listar parâmetros
php artisan tinker
>>> parametro('sistema.nome')

# Limpar cache
php artisan cache:clear

# Executar seeders
php artisan db:seed --class=ParametroSeeder
```

### URLs Importantes
- **Listagem**: `/admin/parametros`
- **Criar**: `/admin/parametros/create`
- **Editar**: `/admin/parametros/{id}/edit`
- **API**: `/api/parametros`

---

## 🆘 Suporte

Para dúvidas ou problemas:
1. Verificar logs em `storage/logs/laravel.log`
2. Consultar documentação da API
3. Acessar interface de auditoria
4. Contatar equipe de desenvolvimento

---

*Sistema de Parâmetros LegisInc v1.0 - Desenvolvido para máxima flexibilidade e facilidade de uso*