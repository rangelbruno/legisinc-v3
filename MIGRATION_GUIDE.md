# Guia de Migração - Sistema de Parâmetros Modulares

## Sobre a Migração

O sistema antigo de parâmetros foi **substituído** pelo novo **Sistema de Parâmetros Modulares**, que oferece:

- ✅ **Estrutura Hierárquica**: Módulos → Submódulos → Campos → Valores
- ✅ **Interface Moderna**: Nova interface com tema Metronic
- ✅ **API Completa**: Endpoints REST para integração
- ✅ **Validação Centralizada**: Sistema de validação robusto
- ✅ **Cache Otimizado**: Performance melhorada
- ✅ **Comandos Artisan**: Ferramentas de linha de comando

## Passos para Migração

### 1. Executar Migrations
```bash
php artisan migrate
```

### 2. Executar Seeders (Parâmetros Iniciais)
```bash
# Todos os módulos
php artisan parametros:seed --all

# Ou módulo específico
php artisan parametros:seed --modulo=dados_camara
```

### 3. Migrar Dados Existentes (se necessário)
```bash
php artisan parametros:migrate-existing
```

### 4. Validar Integridade
```bash
php artisan parametros:validate-all
```

## Mudanças nas Rotas

### Rotas Web (Antigas → Novas)
```php
// ANTIGAS (redirecionam automaticamente)
/admin/parametros/               → /admin/parametros/
/admin/parametros/create         → /admin/parametros/create
/admin/parametros/{id}           → /admin/parametros/{id}

// NOVAS (sistema atual)
/admin/parametros/               # Lista de módulos
/admin/parametros/create         # Criar módulo
/admin/parametros/{id}           # Visualizar módulo
/admin/parametros/configurar/{nome} # Configurar módulo
```

### Rotas API (Antigas → Novas)
```php
// ANTIGAS (depreciadas)
/api/parametros/                 → Retorna 410 (Gone)

// NOVAS (sistema atual)
/api/parametros-modular/modulos/              # CRUD módulos
/api/parametros-modular/submodulos/           # CRUD submódulos
/api/parametros-modular/campos/               # CRUD campos
/api/parametros-modular/configuracoes/{m}/{s} # Obter configurações
```

## Uso no Código

### Antes (Sistema Antigo)
```php
// Não usar mais
$parametro = Parametro::where('codigo', 'nome_camara')->first();
$valor = $parametro->valor;
```

### Depois (Sistema Novo)
```php
// Usar o service
$parametroService = app(\App\Services\Parametro\ParametroService::class);

// Obter valor específico
$valor = $parametroService->obterValor('Dados da Câmara', 'Formulário Institucional', 'nome_camara');

// Obter todas as configurações
$configs = $parametroService->obterConfiguracoes('Dados da Câmara', 'Formulário Institucional');

// Validar valor
$valido = $parametroService->validar('Dados da Câmara', 'Formulário Institucional', $valor);
```

## Comandos Artisan Disponíveis

```bash
# Criar módulo/submódulo
php artisan parametros:create "Dados da Câmara" "Formulário Institucional"

# Migrar dados existentes
php artisan parametros:migrate-existing

# Limpar cache
php artisan parametros:cache-clear

# Validar integridade
php artisan parametros:validate-all

# Seed parâmetros iniciais
php artisan parametros:seed --all
```

## Estrutura dos Módulos Padrão

### 1. Dados da Câmara
- **Submódulo**: Formulário Institucional
- **Campos**: Nome, Endereço, Tipo Integração, Qtd Vereadores, etc.

### 2. Configurações da Sessão
- **Submódulo**: Controles de Sessão
- **Campos**: Checkboxes para veto, expediente, abstenção, etc.

### 3. Tipo de Sessão
- **Submódulo**: Cadastro de Tipos
- **Campos**: Nome, Descrição, Status

### 4. Momento da Sessão
- **Submódulo**: Cadastro de Momentos
- **Campos**: Nome, Descrição, Ordem, Status

### 5. Tipo de Votação
- **Submódulo**: Cadastro de Tipos de Votação
- **Campos**: Nome, Descrição, Regras, Status

## Integração com Middleware

```php
// Usar o middleware de validação
Route::post('/data', function (Request $request) {
    // Dados já validados pelo middleware
})->middleware('validacao.parametros:dados_camara.formulario_institucional.nome_camara');
```

## Resolução de Problemas

### Erro "Módulo não encontrado"
```bash
# Verificar se os seeders foram executados
php artisan parametros:seed --all
```

### Cache não atualizado
```bash
# Limpar cache
php artisan parametros:cache-clear
```

### Problemas de integridade
```bash
# Validar e tentar corrigir
php artisan parametros:validate-all --fix
```

## Suporte

Para dúvidas sobre a migração:
1. Verifique os logs em `storage/logs/laravel.log`
2. Execute `php artisan parametros:validate-all` para diagnóstico
3. Consulte a documentação técnica em `docs/parametros.md`

## Cronograma de Depreciação

- **Imediato**: Sistema antigo redireciona para o novo
- **30 dias**: Avisos de depreciação nas views antigas
- **60 dias**: Remoção completa do sistema antigo (planejado)

---

**Importante**: O sistema antigo continuará funcionando por redirecionamento, mas é recomendado migrar o código para usar o novo sistema o quanto antes.