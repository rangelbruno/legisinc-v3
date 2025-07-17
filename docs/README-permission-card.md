# 🔐 Permission Card Component

Um componente Blade reutilizável para gerenciar permissões de usuários em aplicações Laravel.

## 📋 Índice

- [Instalação](#instalação)
- [Uso Básico](#uso-básico)
- [Parâmetros](#parâmetros)
- [Exemplos](#exemplos)
- [Customização](#customização)
- [JavaScript](#javascript)
- [Troubleshooting](#troubleshooting)

## 🚀 Instalação

O componente já está disponível em `resources/views/components/permission-card.blade.php`. Não é necessária instalação adicional.

## 💡 Uso Básico

```blade
<x-permission-card 
    :module="[
        'value' => 'usuarios',
        'label' => 'Usuários',
        'color' => 'primary',
        'iconClass' => 'ki-duotone ki-profile-circle',
        'routes' => [
            'users.index' => 'Listar Usuários',
            'users.create' => 'Criar Usuário',
            'users.edit' => 'Editar Usuário'
        ]
    ]"
/>
```

## 📝 Parâmetros

### Obrigatórios

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `module` | array | Dados do módulo contendo informações sobre permissões |

### Opcionais

| Parâmetro | Tipo | Padrão | Descrição |
|-----------|------|---------|-----------|
| `show-actions` | boolean | `true` | Exibe botões de ação (Criar, Editar, Excluir) |
| `readonly` | boolean | `false` | Desabilita edição (somente visualização) |
| `size` | string | `default` | Tamanho do card (`small`, `default`, `large`) |
| `theme` | string | `light` | Tema do card (`light`, `dark`) |

### Estrutura do Array Module

```php
[
    'value' => 'nome_do_modulo',        // Identificador único
    'label' => 'Nome Exibido',          // Nome para exibição
    'color' => 'primary',               // Cor do tema (primary, success, warning, etc.)
    'iconClass' => 'ki-duotone ki-icon', // Classe do ícone
    'routes' => [                       // Array de rotas/permissões
        'route.name' => 'Nome da Permissão',
        'another.route' => 'Outra Permissão'
    ]
]
```

## 🎨 Exemplos de Uso

### 1. Card Básico

```blade
<x-permission-card 
    :module="[
        'value' => 'dashboard',
        'label' => 'Dashboard',
        'color' => 'success',
        'iconClass' => 'ki-duotone ki-element-11',
        'routes' => [
            'dashboard' => 'Painel Principal',
            'dashboard.stats' => 'Estatísticas'
        ]
    ]"
/>
```

### 2. Card Somente Leitura

```blade
<x-permission-card 
    :module="$moduleData"
    :readonly="true"
/>
```

### 3. Card Sem Botões de Ação

```blade
<x-permission-card 
    :module="$moduleData"
    :show-actions="false"
/>
```

### 4. Card com Tamanho Personalizado

```blade
<x-permission-card 
    :module="$moduleData"
    size="large"
    theme="dark"
/>
```

### 5. Card com Conteúdo Personalizado

```blade
<x-permission-card :module="$moduleData">
    <div class="alert alert-info">
        <strong>Informação:</strong> Este módulo requer permissões especiais.
    </div>
</x-permission-card>
```

## 🎯 Diferentes Tamanhos

### Small
- Altura mínima: 250px
- Ícone: 40px x 40px
- Ideal para dashboards compactos

### Default
- Altura padrão do conteúdo
- Ícone: 50px x 50px
- Uso geral

### Large
- Altura mínima: 450px
- Ícone: 60px x 60px
- Para telas com mais informações

## 🌗 Temas Disponíveis

### Light (Padrão)
- Fundo branco
- Bordas cinza claro
- Texto escuro

### Dark
- Fundo escuro (#2a2a2a)
- Bordas escuras
- Texto claro

## 🔧 Customização CSS

### Classes Personalizadas

```css
/* Personalizar card pequeno */
.permission-card-sm {
    min-height: 200px;
}

/* Personalizar tema escuro */
.permission-card-dark {
    background: #1a1a1a;
}

/* Personalizar estado readonly */
.permission-card[data-readonly="true"] {
    opacity: 0.8;
}
```

### Cores Disponíveis

- `primary` (azul)
- `success` (verde)
- `warning` (amarelo)
- `danger` (vermelho)
- `info` (azul claro)
- `secondary` (cinza)

## 📱 Responsividade

O componente é totalmente responsivo:

- **Mobile** (< 768px): 1 card por linha
- **Tablet** (768px - 1199px): 2 cards por linha
- **Desktop** (1200px - 1599px): 3 cards por linha
- **Large Desktop** (> 1600px): 4 cards por linha

## 🖥️ JavaScript

### Event Listeners

```javascript
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('permission-switch')) {
        const route = e.target.dataset.route;
        const module = e.target.closest('.permission-card').dataset.module;
        
        console.log(`Permissão alterada - Módulo: ${module}, Rota: ${route}`);
        
        // Sua lógica personalizada aqui
    }
});
```

### Métodos Úteis

```javascript
// Obter todas as permissões ativas
function getActivePermissions() {
    const permissions = [];
    document.querySelectorAll('.permission-switch:checked').forEach(switch => {
        permissions.push({
            module: switch.closest('.permission-card').dataset.module,
            route: switch.dataset.route,
            active: true
        });
    });
    return permissions;
}

// Desabilitar todas as permissões de um módulo
function disableModule(moduleValue) {
    const card = document.querySelector(`[data-module="${moduleValue}"]`);
    if (card) {
        card.querySelectorAll('.permission-switch').forEach(switch => {
            switch.checked = false;
            switch.dispatchEvent(new Event('change'));
        });
    }
}
```

## 🔍 Troubleshooting

### Problemas Comuns

#### 1. **Card não aparece**
```blade
<!-- ❌ Incorreto -->
<x-permission-card />

<!-- ✅ Correto -->
<x-permission-card :module="$moduleData" />
```

#### 2. **Ícone não exibe**
Verifique se a classe do ícone está correta:
```php
'iconClass' => 'ki-duotone ki-profile-circle' // ✅ Correto
'iconClass' => 'profile-circle'              // ❌ Incorreto
```

#### 3. **Cores não funcionam**
Use cores do Bootstrap:
```php
'color' => 'primary'  // ✅ Correto
'color' => 'blue'     // ❌ Incorreto
```

#### 4. **JavaScript não funciona**
Certifique-se de que o Bootstrap está carregado:
```html
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```

### Logs de Debug

```javascript
// Ativar logs detalhados
window.DEBUG_PERMISSION_CARD = true;

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('permission-switch') && window.DEBUG_PERMISSION_CARD) {
        console.log('Permission Card Debug:', {
            route: e.target.dataset.route,
            action: e.target.dataset.action,
            checked: e.target.checked,
            module: e.target.closest('.permission-card').dataset.module
        });
    }
});
```

## 🤝 Contribuindo

### Estrutura de Arquivos

```
resources/views/components/
├── permission-card.blade.php          # Componente principal
└── ...

resources/views/admin/
├── screen-permissions/
│   └── index.blade.php                # Exemplo de uso
└── ...

resources/views/examples/
└── permission-card-examples.blade.php # Página de exemplos
```

### Adicionando Novos Recursos

1. Edite o componente em `resources/views/components/permission-card.blade.php`
2. Adicione exemplos em `resources/views/examples/permission-card-examples.blade.php`
3. Atualize esta documentação
4. Teste em diferentes tamanhos de tela

## 📄 Licença

Este componente faz parte do projeto Laravel e segue a mesma licença.

## 🆘 Suporte

Para dúvidas ou problemas:

1. Verifique os exemplos em `/examples/permission-card-examples`
2. Consulte o troubleshooting acima
3. Verifique os logs do console do navegador
4. Abra uma issue no repositório do projeto

---

**Versão:** 1.0.0  
**Última atualização:** 2024  
**Compatibilidade:** Laravel 10+, PHP 8.2+ 