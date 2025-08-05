# 📋 Guia: Menu Personalizado por Perfil de Usuário

## 📚 Introdução

Este guia documenta o processo completo para criar menus personalizados baseados no perfil do usuário no sistema Legisinc, incluindo troubleshooting de problemas comuns de cache e sincronização.

## 🎯 Caso de Uso: Perfil EXPEDIENTE

Este exemplo mostra como foi implementado o menu específico para usuários com perfil "EXPEDIENTE".

---

## 🔧 Passo 1: Configurar Permissões no Banco de Dados

### 1.1 Criar Command para Configurar Permissões

Crie um arquivo `app/Console/Commands/ConfigurePerfilPermissions.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;

class ConfigureExpedientePermissions extends Command
{
    protected $signature = 'permissions:configure-expediente';
    protected $description = 'Configura as permissões corretas para o perfil EXPEDIENTE';

    public function handle()
    {
        $this->info('Configurando permissões para o perfil EXPEDIENTE...');

        $permissions = [
            // Dashboard - permitido
            ['route' => 'dashboard', 'name' => 'Dashboard', 'module' => 'dashboard', 'access' => true],
            
            // Módulos específicos do perfil
            ['route' => 'expediente.index', 'name' => 'Painel do Expediente', 'module' => 'expediente', 'access' => true],
            ['route' => 'expediente.show', 'name' => 'Visualizar Proposição no Expediente', 'module' => 'expediente', 'access' => true],
            ['route' => 'expediente.classificar', 'name' => 'Classificar Momento da Sessão', 'module' => 'expediente', 'access' => true],
            ['route' => 'expediente.aguardando-pauta', 'name' => 'Proposições Aguardando Pauta', 'module' => 'expediente', 'access' => true],
            ['route' => 'expediente.relatorio', 'name' => 'Relatório do Expediente', 'module' => 'expediente', 'access' => true],
            
            // Proposições - acesso específico
            ['route' => 'proposicoes.show', 'name' => 'Visualizar Proposição', 'module' => 'proposicoes', 'access' => true],
            ['route' => 'proposicoes.legislativo.index', 'name' => 'Proposições Protocoladas', 'module' => 'proposicoes', 'access' => true],
            
            // Módulos negados
            ['route' => 'proposicoes.criar', 'name' => 'Nova Proposição', 'module' => 'proposicoes', 'access' => false],
            ['route' => 'proposicoes.minhas-proposicoes', 'name' => 'Minhas Proposições', 'module' => 'proposicoes', 'access' => false],
        ];

        // Remover permissões existentes
        ScreenPermission::where('role_name', 'EXPEDIENTE')->delete();

        // Aplicar novas permissões
        foreach ($permissions as $permission) {
            ScreenPermission::setScreenAccess(
                'EXPEDIENTE',
                $permission['route'],
                $permission['name'],
                $permission['module'],
                $permission['access']
            );
        }

        $this->info('✅ Permissões configuradas com sucesso!');
        return 0;
    }
}
```

### 1.2 Executar o Command

```bash
php artisan permissions:configure-expediente
```

---

## 🎨 Passo 2: Modificar o Template do Menu

### 2.1 Localizar o Arquivo Correto

**⚠️ ATENÇÃO:** Identifique o arquivo correto do menu:
- ❌ Errado: `/resources/views/components/layouts/aside.blade.php`
- ✅ Correto: `/resources/views/components/layouts/aside/aside.blade.php`

### 2.2 Adicionar Submenu no Template

Edite `/resources/views/components/layouts/aside/aside.blade.php`:

```blade
<!--begin:Menu item - Proposições-->
@if(\App\Models\ScreenPermission::userCanAccessModule('proposicoes'))
<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('proposicoes.*') || request()->routeIs('expediente.*') ? 'here show' : '' }}">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="ki-duotone ki-file-up fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </span>
        <span class="menu-title">Proposições</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion {{ request()->routeIs('proposicoes.*') || request()->routeIs('expediente.*') ? 'show' : '' }}">
        
        {{-- SUBMENU EXPEDIENTE --}}
        @if(\App\Models\ScreenPermission::userCanAccessModule('expediente') || \App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
        <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('expediente.*') ? 'here show' : '' }}">
            <span class="menu-link">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">📋 EXPEDIENTE</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion {{ request()->routeIs('expediente.*') ? 'show' : '' }}">
                @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.index'))
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('expediente.index') ? 'active' : '' }}" href="{{ route('expediente.index') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">📋 Painel do Expediente</span>
                    </a>
                </div>
                @endif
                @if(\App\Models\ScreenPermission::userCanAccessRoute('proposicoes.legislativo.index'))
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('proposicoes.legislativo.index') ? 'active' : '' }}" href="{{ route('proposicoes.legislativo.index') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Proposições Protocoladas</span>
                    </a>
                </div>
                @endif
                @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.aguardando-pauta'))
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('expediente.aguardando-pauta') ? 'active' : '' }}" href="{{ route('expediente.aguardando-pauta') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Aguardando Pauta</span>
                    </a>
                </div>
                @endif
                @if(\App\Models\ScreenPermission::userCanAccessRoute('expediente.relatorio'))
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('expediente.relatorio') ? 'active' : '' }}" href="{{ route('expediente.relatorio') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Relatório</span>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif
        {{-- END SUBMENU EXPEDIENTE --}}
        
        {{-- Outros itens do menu... --}}
    </div>
</div>
@endif
```

---

## 🧪 Passo 3: Testes e Validação

### 3.1 Criar Páginas de Debug

Crie `/resources/views/test-permissions-live.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Test Permissions Live</title>
    <meta http-equiv="refresh" content="5">
</head>
<body>
    <h1>Live Permissions Test</h1>
    
    @if(Auth::check())
        <h2>User: {{ Auth::user()->name }}</h2>
        <h2>Module Permissions:</h2>
        <p>expediente: {{ \App\Models\ScreenPermission::userCanAccessModule('expediente') ? '✅ TRUE' : '❌ FALSE' }}</p>
        <p>proposicoes: {{ \App\Models\ScreenPermission::userCanAccessModule('proposicoes') ? '✅ TRUE' : '❌ FALSE' }}</p>
        
        <h2>Route Permissions:</h2>
        <p>expediente.index: {{ \App\Models\ScreenPermission::userCanAccessRoute('expediente.index') ? '✅ TRUE' : '❌ FALSE' }}</p>
    @endif
</body>
</html>
```

### 3.2 Adicionar Rota de Debug

Em `/routes/web.php`:

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/test-permissions-live', function() {
        return view('test-permissions-live');
    })->name('test-permissions-live');
});
```

---

## 🛠️ Troubleshooting: Problemas Comuns

### ❌ Problema 1: Menu não aparece

**Sintomas:** Menu vazio, sem submenu visível

**Soluções:**
1. Verificar arquivo correto: `/aside/aside.blade.php`, não `/aside.blade.php`
2. Limpar cache de views: `php artisan view:clear`
3. Verificar permissões no banco: acessar `/test-permissions-live`

### ❌ Problema 2: Template não atualiza

**Sintomas:** Modificações não aparecem no navegador

**Soluções:**
1. Limpar todos os caches:
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

2. Reiniciar container Docker:
```bash
docker restart legisinc-app
```

3. Forçar atualização no navegador:
```
Ctrl+F5 (Windows/Linux)
Cmd+Shift+R (Mac)
```

### ❌ Problema 3: Permissões não funcionam

**Sintomas:** Debug mostra FALSE para permissões

**Soluções:**
1. Executar command novamente: `php artisan permissions:configure-expediente`
2. Verificar se usuário tem role correta
3. Fazer logout/login para renovar sessão

### ❌ Problema 4: Menu não fica ativo

**Sintomas:** Menu não destaca página atual

**Verificar classes CSS:**
- Menu principal: `{{ request()->routeIs('expediente.*') ? 'here show' : '' }}`
- Submenu: `{{ request()->routeIs('expediente.*') ? 'show' : '' }}`
- Links: `{{ request()->routeIs('expediente.index') ? 'active' : '' }}`

---

## 📋 Template para Novos Perfis

### Passo 1: Criar Command de Permissões

```php
<?php
class Configure[PERFIL]Permissions extends Command
{
    protected $signature = 'permissions:configure-[perfil]';
    protected $description = 'Configura permissões para [PERFIL]';

    public function handle()
    {
        $permissions = [
            // Definir permissões aqui
        ];

        ScreenPermission::where('role_name', '[PERFIL]')->delete();
        
        foreach ($permissions as $permission) {
            ScreenPermission::setScreenAccess(
                '[PERFIL]',
                $permission['route'],
                $permission['name'],
                $permission['module'],
                $permission['access']
            );
        }
    }
}
```

### Passo 2: Adicionar Submenu

```blade
@if(\App\Models\ScreenPermission::userCanAccessModule('[modulo]'))
<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('[prefixo].*') ? 'here show' : '' }}">
    <span class="menu-link">
        <span class="menu-bullet">
            <span class="bullet bullet-dot"></span>
        </span>
        <span class="menu-title">[NOME DO SUBMENU]</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion {{ request()->routeIs('[prefixo].*') ? 'show' : '' }}">
        <!-- Itens do submenu aqui -->
    </div>
</div>
@endif
```

---

## ✅ Checklist Final

- [ ] Command de permissões criado e executado
- [ ] Submenu adicionado no arquivo correto (`/aside/aside.blade.php`)
- [ ] Classes CSS de menu ativo configuradas
- [ ] Cache limpo (`php artisan view:clear`)
- [ ] Permissões testadas em `/test-permissions-live`
- [ ] Menu funciona para logout/login
- [ ] Navegação entre páginas mantém menu ativo

---

## 📞 Suporte

Se encontrar problemas não cobertos neste guia:

1. Verificar logs: `storage/logs/laravel.log`
2. Testar permissões: acessar `/test-permissions-live`
3. Verificar estrutura HTML com F12 → Elements
4. Procurar por comentários `{{-- DEBUG --}}` no código fonte

---

**Autor:** Sistema Legisinc  
**Data:** Agosto 2025  
**Versão:** 1.0