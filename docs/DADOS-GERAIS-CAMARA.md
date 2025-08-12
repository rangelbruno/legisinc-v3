# Documentação Técnica - Dados Gerais da Câmara

## Visão Geral

A tela **Dados Gerais da Câmara** (`/parametros-dados-gerais-camara`) é uma interface para configurar informações institucionais da Câmara Municipal. Este documento detalha como a funcionalidade opera, desde a interface até o salvamento no banco de dados.

## Estrutura do Sistema

### Arquitetura MVC + Service Layer

```
📁 Frontend (View)
└── resources/views/modules/parametros/dados-gerais-camara.blade.php

📁 Controller 
└── app/Http/Controllers/DadosGeraisCamaraController.php

📁 Service Layer
└── app/Services/Parametro/ParametroService.php

📁 Database
├── parametros_modulos
├── parametros_submodulos  
├── parametros_campos
└── parametros_valores
```

## Estrutura do Banco de Dados

### Tabelas Principais

#### parametros_modulos
```sql
id | nome        | descricao                    | icon    | ordem | ativo
6  | Dados Gerais| Dados gerais da câmara      | ki-bank | 1     | true
```

#### parametros_submodulos
```sql
id | modulo_id | nome          | descricao              | ordem | ativo
X  | 6         | Identificação | Dados de identificação | 1     | true
X  | 6         | Endereço      | Endereço completo     | 2     | true
X  | 6         | Contatos      | Informações contato   | 3     | true
X  | 6         | Funcionamento | Horários funcionamento| 4     | true
X  | 6         | Gestão        | Dados gestão atual    | 5     | true
```

#### parametros_campos
```sql
id | submodulo_id | nome                  | label                 | tipo_campo | obrigatorio | ordem
X  | X            | nome_camara          | Nome da Câmara        | text       | true        | 1
X  | X            | sigla_camara         | Sigla                 | text       | true        | 2
X  | X            | cnpj                 | CNPJ                  | text       | false       | 3
X  | X            | endereco             | Endereço              | text       | true        | 1
X  | X            | telefone             | Telefone Principal    | text       | true        | 1
...
```

#### parametros_valores
```sql
id | campo_id | valor                        | valido_ate | created_at          | updated_at
X  | X        | Câmara Municipal Caraguatatuba| NULL      | 2024-08-12 23:34:34 | 2024-08-12 23:34:34
X  | X        | CMC                          | NULL      | 2024-08-12 23:34:34 | 2024-08-12 23:34:34
X  | X        | 50.444.108/0001-41           | NULL      | 2024-08-12 23:34:34 | 2024-08-12 23:34:34
```

## Fluxo de Funcionamento

### 1. Carregamento da Página (GET)

#### Rota
```php
Route::get('/parametros-dados-gerais-camara', function () {
    Auth::loginUsingId(1); // Auto-login como admin
    return app(DadosGeraisCamaraController::class)->index();
});
```

#### Controller - Método `index()`
```php
public function index(): View
{
    // Obter configurações atuais
    $configuracoes = $this->obterConfiguracoes();
    
    return view('modules.parametros.dados-gerais-camara', compact('configuracoes'));
}
```

#### Estratégia de Cache Bypass
```php
private function obterConfiguracoes(): array
{
    try {
        // Estratégia agressiva: limpar caches principais
        \Cache::flush(); // Limpa todo o cache Redis
        
        // Limpar OpCache se disponível
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        
        // Buscar dados DIRETAMENTE do banco, bypassing o ParametroService
        $configuracoes = $this->obterConfiguracoesDiretas();
        
        return $configuracoes;
    } catch (\Exception $e) {
        // Se houver erro, usar valores padrão
        return [/* valores padrão */];
    }
}
```

#### Query Direta ao Banco
```php
private function obterConfiguracoesDiretas(): array
{
    // Query direta para buscar todos os valores de uma vez
    $valores = DB::table('parametros_valores as pv')
        ->join('parametros_campos as pc', 'pv.campo_id', '=', 'pc.id')
        ->join('parametros_submodulos as ps', 'pc.submodulo_id', '=', 'ps.id')
        ->join('parametros_modulos as pm', 'ps.modulo_id', '=', 'pm.id')
        ->where('pm.nome', 'Dados Gerais')
        ->whereNull('pv.valido_ate')
        ->select('pc.nome as campo', 'ps.nome as submodulo', 'pv.valor')
        ->get()
        ->keyBy('campo');

    // Mapear os valores ou usar defaults
    $configuracoes = [
        'nome_camara' => optional($valores->get('nome_camara'))->valor ?? 'Câmara Municipal',
        'sigla_camara' => optional($valores->get('sigla_camara'))->valor ?? 'CM',
        // ... outros campos
    ];

    return $configuracoes;
}
```

### 2. Interface de Usuário (Frontend)

#### Estrutura da View
```html
<!-- Abas de Navegação -->
<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a href="#kt_tab_identificacao">Identificação</a></li>
    <li class="nav-item"><a href="#kt_tab_endereco">Endereço</a></li>
    <li class="nav-item"><a href="#kt_tab_contatos">Contatos</a></li>
    <li class="nav-item"><a href="#kt_tab_funcionamento">Funcionamento</a></li>
    <li class="nav-item"><a href="#kt_tab_gestao">Gestão</a></li>
</ul>

<!-- Conteúdo das Abas -->
<div class="tab-content">
    <div class="tab-pane fade show active" id="kt_tab_identificacao">
        <input name="nome_camara" value="{{ $configuracoes['nome_camara'] }}" />
        <input name="sigla_camara" value="{{ $configuracoes['sigla_camara'] }}" />
        <input name="cnpj" value="{{ $configuracoes['cnpj'] }}" />
        <button type="button" onclick="salvarAba('identificacao')">Salvar</button>
    </div>
    <!-- ... outras abas -->
</div>
```

#### JavaScript - Salvamento por Aba
```javascript
function salvarAba(nomeAba) {
    const form = document.getElementById('dados-gerais-form');
    const formData = new FormData(form);
    formData.append('save_tab', nomeAba);

    fetch('/parametros-dados-gerais-camara', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: data.message
            });
        } else {
            Swal.fire({
                icon: 'error', 
                title: 'Erro!',
                text: data.message
            });
        }
    });
}
```

### 3. Salvamento no Banco (POST)

#### Controller - Método `store()`
```php
public function store(Request $request): JsonResponse
{
    $saveTab = $request->input('save_tab');
    
    // Define validation rules by tab
    $validationRules = $this->getValidationRulesByTab($saveTab);
    
    // Apply validation only for the current tab fields
    $request->validate($validationRules);

    try {
        // Save only the fields provided in the request based on the tab
        $this->saveTabFields($request, $saveTab);

        return response()->json([
            'success' => true,
            'message' => "Dados da aba \"$tabDisplayName\" salvos com sucesso!",
            'saved_tab' => $saveTab
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao salvar configurações: ' . $e->getMessage()
        ], 500);
    }
}
```

#### Salvamento por Aba
```php
private function saveTabFields(Request $request, string $tab): void
{
    $tabMapping = [
        'identificacao' => [
            'submodule' => 'Identificação',
            'fields' => ['nome_camara', 'sigla_camara', 'cnpj']
        ],
        'endereco' => [
            'submodule' => 'Endereço',
            'fields' => ['endereco', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'cep']
        ],
        // ... outros tabs
    ];

    $config = $tabMapping[$tab];
    $submoduleName = $config['submodule'];
    $fields = $config['fields'];

    foreach ($fields as $field) {
        if ($request->has($field)) {
            $valor = $request->input($field);
            $this->parametroService->salvarValor('Dados Gerais', $submoduleName, $field, $valor);
        }
    }
}
```

### 4. ParametroService - Salvamento Final

#### Método `salvarValor()`
```php
public function salvarValor(string $modulo, string $submodulo, string $campo, $valor): bool
{
    try {
        $parametroCampo = $this->obterCampo($modulo, $submodulo, $campo);
        
        if (!$parametroCampo) {
            throw new \InvalidArgumentException("Campo não encontrado: $modulo > $submodulo > $campo");
        }

        // Expirar valores antigos (CRÍTICO para evitar duplicatas)
        $parametroCampo->valores()->whereNull('valido_ate')->update(['valido_ate' => now()]);

        // Criar novo valor
        $parametroCampo->valores()->create([
            'valor' => $valor,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return true;
    } catch (\Exception $e) {
        Log::error("Erro ao salvar valor do parâmetro: " . $e->getMessage());
        return false;
    }
}
```

## Problemas Resolvidos e Soluções

### 1. Problema: Cache Agressivo
**Sintoma**: Dados salvavam no banco mas não apareciam na tela após reload
**Causa**: Múltiplas camadas de cache (Redis, OpCache, Laravel cache)
**Solução**: Cache bypass no método `obterConfiguracoes()`

```php
// ANTES: Usava ParametroService com cache
'nome_camara' => $this->parametroService->obterValor('Dados Gerais', 'Identificação', 'nome_camara') ?: 'Câmara Municipal',

// DEPOIS: Query direta com cache bypass
\Cache::flush();
$valores = DB::table('parametros_valores as pv')->/* query direta */;
'nome_camara' => optional($valores->get('nome_camara'))->valor ?? 'Câmara Municipal',
```

### 2. Problema: Valores Duplicados
**Sintoma**: Múltiplos registros na tabela `parametros_valores` para o mesmo campo
**Causa**: Valores antigos não estavam sendo expirados
**Solução**: Re-ativação da expiração no `ParametroService.php:232`

```php
// ANTES: Comentado
// $campo->valores()->validos()->update(['valido_ate' => now()]);

// DEPOIS: Ativo
$campo->valores()->whereNull('valido_ate')->update(['valido_ate' => now()]);
```

### 3. Problema: Erro 500 durante salvamento
**Sintoma**: Erro HTTP 500 ao tentar salvar dados
**Causa**: Problemas de log e permissões
**Solução**: Remoção de logs de debug e ajuste de permissões

## Estrutura de Dados por Aba

### Aba Identificação
- `nome_camara` (required) - Nome completo da Câmara
- `sigla_camara` (required) - Sigla oficial  
- `cnpj` (optional) - CNPJ da instituição

### Aba Endereço
- `endereco` (required) - Logradouro
- `numero` (optional) - Número
- `complemento` (optional) - Complemento
- `bairro` (required) - Bairro
- `cidade` (required) - Cidade
- `estado` (required) - Estado (UF)
- `cep` (required) - CEP

### Aba Contatos
- `telefone` (required) - Telefone principal
- `telefone_secundario` (optional) - Telefone secundário
- `email_institucional` (required) - E-mail principal
- `email_contato` (optional) - E-mail secundário
- `website` (optional) - Site institucional

### Aba Funcionamento
- `horario_funcionamento` (required) - Horário geral
- `horario_atendimento` (required) - Horário de atendimento

### Aba Gestão
- `presidente_nome` (required) - Nome do presidente
- `presidente_partido` (required) - Partido do presidente
- `legislatura_atual` (required) - Período da legislatura
- `numero_vereadores` (required) - Quantidade de vereadores

## Validação de Dados

### Regras por Campo
```php
'identificacao' => [
    'nome_camara' => 'required|string|max:255',
    'sigla_camara' => 'required|string|max:20',
    'cnpj' => 'nullable|string|max:20',
],
'endereco' => [
    'endereco' => 'required|string|max:255',
    'numero' => 'nullable|string|max:20',
    'bairro' => 'required|string|max:100',
    'cidade' => 'required|string|max:100',
    'estado' => 'required|string|max:3',
    'cep' => 'required|string|max:12',
],
// ... outras abas
```

### Máscaras JavaScript
```javascript
// CEP
$('#cep_input').mask('00000-000');

// CNPJ  
$('#cnpj_input').mask('00.000.000/0000-00');

// Telefone
$('#telefone_input').mask('(00) 0000-0000');
```

## Monitoramento e Debug

### Logs Importantes
```bash
# Logs do container
docker logs legisinc-app

# Logs Laravel
tail -f storage/logs/laravel.log

# Debug SQL
DB::enableQueryLog();
// ... operações
dd(DB::getQueryLog());
```

### Consultas Úteis
```sql
-- Verificar estrutura do módulo
SELECT 
    pm.nome as modulo,
    ps.nome as submodulo, 
    pc.nome as campo,
    pc.tipo_campo,
    pc.obrigatorio
FROM parametros_modulos pm
JOIN parametros_submodulos ps ON pm.id = ps.modulo_id  
JOIN parametros_campos pc ON ps.id = pc.submodulo_id
WHERE pm.nome = 'Dados Gerais'
ORDER BY ps.ordem, pc.ordem;

-- Verificar valores salvos
SELECT 
    pc.nome as campo,
    pv.valor,
    pv.created_at,
    pv.valido_ate
FROM parametros_valores pv
JOIN parametros_campos pc ON pv.campo_id = pc.id
JOIN parametros_submodulos ps ON pc.submodulo_id = ps.id
JOIN parametros_modulos pm ON ps.modulo_id = pm.id  
WHERE pm.nome = 'Dados Gerais'
AND pv.valido_ate IS NULL
ORDER BY pv.created_at DESC;

-- Verificar duplicatas
SELECT 
    pc.nome as campo,
    COUNT(*) as total
FROM parametros_valores pv
JOIN parametros_campos pc ON pv.campo_id = pc.id
JOIN parametros_submodulos ps ON pc.submodulo_id = ps.id
JOIN parametros_modulos pm ON ps.modulo_id = pm.id
WHERE pm.nome = 'Dados Gerais'
AND pv.valido_ate IS NULL
GROUP BY pc.nome
HAVING COUNT(*) > 1;
```

## Arquivos-Chave

### Backend
- `app/Http/Controllers/DadosGeraisCamaraController.php` - Controller principal
- `app/Services/Parametro/ParametroService.php` - Lógica de negócio
- `routes/web.php` - Definição das rotas

### Frontend  
- `resources/views/modules/parametros/dados-gerais-camara.blade.php` - Interface

### Database
- `database/seeders/DadosGeraisParametrosSeeder.php` - Estrutura
- `database/seeders/DadosGeraisValoresSeeder.php` - Valores padrão
- `database/seeders/DatabaseSeeder.php` - Orquestração

### Docker
- `docker-compose.yml` - Configuração dos containers
- `.env` - Variáveis de ambiente

## Manutenção e Troubleshooting

### Problemas Comuns

1. **Dados não aparecem após salvar**
   - Verificar se o cache bypass está ativo
   - Verificar conexão com banco de dados
   - Consultar tabela `parametros_valores` diretamente

2. **Erro 500 no salvamento**
   - Verificar logs do Laravel
   - Verificar permissões de escrita
   - Verificar estrutura do banco (seeders executados)

3. **Valores duplicados**
   - Verificar se expiração está ativa no ParametroService
   - Executar limpeza manual se necessário

4. **Interface quebrada**
   - Verificar JavaScript no console do browser
   - Verificar se jQuery e máscaras estão carregando
   - Verificar CSP e CORS

### Comandos de Diagnóstico
```bash
# Recriar banco completo
docker exec -it legisinc-app php artisan migrate:fresh --seed

# Apenas seeders
docker exec -it legisinc-app php artisan db:seed

# Limpar cache
docker exec -it legisinc-app php artisan cache:clear
docker exec -it legisinc-app php artisan config:clear
docker exec -it legisinc-app php artisan view:clear

# Verificar conectividade
docker exec -it legisinc-app php artisan tinker --execute="DB::connection()->getPdo();"
```

---

**Versão**: 1.0  
**Última Atualização**: 12/08/2024  
**Autor**: Sistema Legisinc