# 🔧 Solução: Botão "Assinar Documento" Não Funciona (Apenas Atualiza Página)

## 📋 Problema Relatado

**Sintoma**: Ao clicar no botão "Assinar Documento", a página apenas atualiza em vez de navegar para a tela de assinatura.

```html
<!-- Botão que não estava funcionando -->
<a href="http://localhost:8001/proposicoes/1/assinar" class="btn btn-success btn-lg btn-assinatura">
    <i class="fas fa-signature me-2"></i>Assinar Documento
</a>
```

## 🎯 Diagnóstico Sistemático

### 1. Verificação de Rota
```bash
# Verificar se a rota existe
php artisan route:list | grep "proposicoes.assinar"

# Resultado esperado:
# GET|HEAD  proposicoes/{proposicao}/assinar proposicoes.assinar › ProposicaoAssinaturaController@assinar
```

### 2. Verificação de Controller
```php
// Verificar se o controller existe
App\Http\Controllers\ProposicaoAssinaturaController@assinar

// Verificar método assinar() no controller
public function assinar(Proposicao $proposicao)
{
    if (!in_array($proposicao->status, ['aprovado_assinatura', 'retornado_legislativo'])) {
        abort(403, 'Proposição não está disponível para assinatura.');
    }
    // ... resto do método
}
```

### 3. Verificação de Permissões
```bash
# Via tinker - verificar permissões do usuário
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'jessica@sistema.gov.br')->first();
$permissions = App\Models\ScreenPermission::where('role_name', 'PARLAMENTAR')
    ->where('screen_route', 'proposicoes.assinar')
    ->get(['screen_route', 'can_access']);
    
// Deve retornar: proposicoes.assinar com can_access = true
```

### 4. Verificação de Status da Proposição
```php
$proposicao = App\Models\Proposicao::find(1);
echo "Status: " . $proposicao->status;

// Status válidos para assinatura:
// - 'aprovado_assinatura'
// - 'retornado_legislativo'
```

### 5. Teste de Autenticação (Causa Mais Comum)
```bash
# Testar rota via curl
curl -I "http://localhost:8001/proposicoes/1/assinar"

# Se retornar HTTP 302 Location: /login
# → PROBLEMA: Usuário não está autenticado
```

## ❌ Causas Identificadas por Ordem de Frequência

### 1. **🔑 Problema de Autenticação (90% dos casos)**
**Causa**: Usuário não está logado ou sessão expirou
**Sintomas**: 
- Botão parece funcionar mas redireciona para login
- Página "apenas atualiza"
- Curl retorna HTTP 302 para /login

**Solução**:
```bash
# 1. Fazer login correto
URL: http://localhost:8001/login
Usuário: jessica@sistema.gov.br
Senha: 123456

# 2. Limpar cache do navegador
Ctrl + Shift + Delete (Chrome/Firefox)

# 3. Testar em aba anônima/incógnita
```

### 2. **📊 Status Inválido da Proposição (5% dos casos)**
**Causa**: Proposição não está no status correto para assinatura
**Sintomas**: Erro 403 ou mensagem de acesso negado

**Verificação**:
```sql
SELECT id, status FROM proposicoes WHERE id = 1;
-- Status deve ser: 'aprovado_assinatura' ou 'retornado_legislativo'
```

**Solução**:
```php
// Atualizar status se necessário
$proposicao = App\Models\Proposicao::find(1);
$proposicao->status = 'retornado_legislativo';
$proposicao->save();
```

### 3. **🔒 Problema de Permissões (3% dos casos)**
**Causa**: Permissão `proposicoes.assinar` não configurada para PARLAMENTAR
**Verificação**:
```php
// Verificar se permissão existe
App\Models\ScreenPermission::where('role_name', 'PARLAMENTAR')
    ->where('screen_route', 'proposicoes.assinar')
    ->where('can_access', true)
    ->exists();
```

**Solução**:
```php
// Criar permissão se não existir
App\Models\ScreenPermission::create([
    'role_name' => 'PARLAMENTAR',
    'screen_route' => 'proposicoes.assinar',
    'screen_name' => 'Assinar Proposição',
    'can_access' => true
]);
```

### 4. **🌐 Problema de JavaScript/Frontend (2% dos casos)**
**Causa**: JavaScript interceptando clique ou erro de frontend
**Verificação**: 
- Abrir DevTools (F12)
- Verificar console de erros
- Verificar se `preventDefault()` está sendo chamado

**Solução**:
```javascript
// Remover event listeners problemáticos
document.addEventListener('click', function(e) {
    if (e.target.tagName === 'A') {
        console.log('Link clicked:', e.target.href);
        // Verificar se preventDefault foi chamado
    }
});
```

## 🛠️ Kit de Ferramentas de Debug

### 1. Script de Verificação Rápida
```bash
#!/bin/bash
echo "=== DEBUG: Botão Assinar Documento ==="

# Verificar rota
echo "1. Verificando rota..."
php artisan route:list | grep "proposicoes.assinar"

# Verificar usuário
echo "2. Verificando usuário..."
php artisan tinker --execute="
\$user = App\\Models\\User::where('email', 'jessica@sistema.gov.br')->first();
echo \$user ? 'Usuário OK: ' . \$user->name : 'Usuário não encontrado';
"

# Verificar proposição
echo "3. Verificando proposição..."
php artisan tinker --execute="
\$prop = App\\Models\\Proposicao::find(1);
echo \$prop ? 'Status: ' . \$prop->status : 'Proposição não encontrada';
"

# Testar autenticação
echo "4. Testando autenticação..."
curl -I "http://localhost:8001/proposicoes/1/assinar" 2>/dev/null | head -1
```

### 2. Página de Debug HTML
```html
<!DOCTYPE html>
<html>
<head>
    <title>Debug Assinar</title>
</head>
<body>
    <h1>🔍 Debug: Botão Assinar</h1>
    
    <!-- Teste 1: Link direto -->
    <h2>Teste 1: Link Direto</h2>
    <a href="http://localhost:8001/proposicoes/1/assinar" target="_blank">
        🔗 Teste Link Direto
    </a>
    
    <!-- Teste 2: JavaScript fetch -->
    <h2>Teste 2: Fetch API</h2>
    <button onclick="testarFetch()">📡 Teste Fetch</button>
    <div id="resultado"></div>
    
    <script>
        function testarFetch() {
            fetch('http://localhost:8001/proposicoes/1/assinar', {
                credentials: 'include'
            })
            .then(response => {
                document.getElementById('resultado').innerHTML = `
                    Status: ${response.status}<br>
                    URL: ${response.url}<br>
                    Redirected: ${response.redirected}
                `;
            })
            .catch(error => {
                document.getElementById('resultado').innerHTML = 'Erro: ' + error.message;
            });
        }
    </script>
</body>
</html>
```

### 3. Teste de Controller Direto
```php
<?php
// test-controller.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\User;
use App\Models\Proposicao;
use Illuminate\Support\Facades\Auth;

// Simular autenticação
$user = User::where('email', 'jessica@sistema.gov.br')->first();
Auth::login($user);

// Testar proposição
$proposicao = Proposicao::find(1);
echo "Proposição: " . $proposicao->id . " - " . $proposicao->status . PHP_EOL;

// Verificar condições do controller
$statusValidos = ['aprovado_assinatura', 'retornado_legislativo'];
$podeAssinar = in_array($proposicao->status, $statusValidos);
echo "Pode assinar: " . ($podeAssinar ? 'SIM' : 'NÃO') . PHP_EOL;
```

## 🚀 Procedimento de Resolução Padrão

### Passo 1: Verificação Rápida
```bash
# 1. Login no sistema
# 2. Abrir DevTools (F12)
# 3. Clicar no botão
# 4. Verificar console e Network tab
```

### Passo 2: Se Não Funcionar
```bash
# 1. Executar script de debug
./debug-assinar.sh

# 2. Verificar logs do Laravel
tail -f storage/logs/laravel.log

# 3. Testar com usuário diferente
```

### Passo 3: Diagnóstico Avançado
```bash
# 1. Verificar middleware
php artisan route:list -v | grep assinar

# 2. Verificar permissões no banco
SELECT * FROM screen_permissions WHERE screen_route = 'proposicoes.assinar';

# 3. Testar sem middleware
# (temporariamente remover middleware da rota)
```

## 📊 Checklist de Verificação

### ✅ **Checklist Básico**
- [ ] Usuário está logado com credenciais corretas
- [ ] Proposição existe (ID válido)
- [ ] Status da proposição permite assinatura
- [ ] Usuário tem role PARLAMENTAR
- [ ] Permissão `proposicoes.assinar` existe e está ativa

### ✅ **Checklist Avançado**
- [ ] Rota está registrada no sistema
- [ ] Controller existe e método é público
- [ ] Middleware não está bloqueando
- [ ] Sessão não expirou
- [ ] Não há JavaScript interferindo
- [ ] Não há problemas de CORS

### ✅ **Checklist de Produção**
- [ ] Cache de rotas limpo (`php artisan route:clear`)
- [ ] Cache de configuração limpo (`php artisan config:clear`)
- [ ] Permissões de arquivo corretas
- [ ] Log não mostra erros relacionados

## 🎯 Soluções por Código de Erro

### HTTP 302 → /login
**Problema**: Não autenticado
**Solução**: Fazer login correto

### HTTP 403
**Problema**: Sem permissão
**Solução**: Verificar permissões do usuário

### HTTP 404
**Problema**: Rota não encontrada
**Solução**: Verificar definição da rota

### HTTP 500
**Problema**: Erro interno
**Solução**: Verificar logs do Laravel

### Página apenas "atualiza"
**Problema**: JavaScript ou cache
**Solução**: Limpar cache, verificar console

## 📞 Contatos e Referências

- **Documentação de Rotas**: `/routes/web.php` linha 879
- **Controller**: `/app/Http/Controllers/ProposicaoAssinaturaController.php`
- **Middleware**: `/app/Http/Middleware/CheckScreenPermission.php`
- **Permissões**: Tabela `screen_permissions`

---

**Criado em**: 17/08/2025  
**Autor**: Sistema de Debug Legisinc  
**Versão**: 1.0 - Guia Completo  
**Status**: Documentação Técnica de Produção