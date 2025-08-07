# Componente OnlyOffice Editor

## Visão Geral

O componente `onlyoffice-editor` é um componente Blade reutilizável que encapsula toda a funcionalidade do editor OnlyOffice. Este componente foi criado para unificar e simplificar o uso do editor em diferentes contextos do sistema.

## Localização dos Arquivos

- **Componente Principal**: `/resources/views/components/onlyoffice-editor.blade.php`
- **Painel de Variáveis**: `/resources/views/components/onlyoffice-variables.blade.php`
- **Implementações**:
  - Admin: `/resources/views/admin/templates/editor-new.blade.php`
  - Parlamentar: `/resources/views/proposicoes/editar-onlyoffice-new.blade.php`
  - Legislativo: `/resources/views/proposicoes/legislativo/onlyoffice-editor-new.blade.php`

## Uso do Componente

### Sintaxe Básica

```blade
<x-onlyoffice-editor
    :document-key="$documentKey"
    :document-url="$documentUrl"
    :document-title="$documentTitle"
    document-type="rtf"
    :callback-url="$callbackUrl"
    mode="edit"
    user-type="parlamentar"
    :save-route="$saveRoute"
    :back-route="$backRoute"
    :show-variables-panel="true"
    :show-toolbar="true"
/>
```

### Parâmetros do Componente

| Parâmetro | Tipo | Obrigatório | Descrição |
|-----------|------|-------------|-----------|
| `document-key` | string | Sim | Chave única do documento |
| `document-url` | string | Sim | URL para carregar o documento |
| `document-title` | string | Não | Título do documento (padrão: "Documento") |
| `document-type` | string | Não | Tipo do documento: rtf, docx, doc (padrão: "rtf") |
| `callback-url` | string | Sim | URL de callback para salvar alterações |
| `mode` | string | Não | Modo do editor: edit ou view (padrão: "edit") |
| `user-type` | string | Não | Tipo de usuário: admin, parlamentar, legislativo |
| `save-route` | string | Não | Rota para salvar o documento |
| `back-route` | string | Não | Rota para voltar |
| `height` | string | Não | Altura do editor (padrão: "calc(100vh - 70px)") |
| `show-variables-panel` | boolean | Não | Mostrar painel de variáveis (padrão: false) |
| `show-toolbar` | boolean | Não | Mostrar barra de ferramentas (padrão: true) |
| `custom-actions` | array | Não | Ações customizadas para a toolbar |
| `proposicao-id` | integer | Não | ID da proposição |
| `template-id` | integer | Não | ID do template |

## Exemplos de Uso

### 1. Administrador - Editor de Templates

```blade
<x-onlyoffice-editor
    :document-key="$template->document_key"
    :document-url="route('api.onlyoffice.document', ['type' => 'template', 'id' => $template->id])"
    :document-title="'Template: ' . $tipo->nome"
    document-type="rtf"
    :callback-url="route('api.onlyoffice.callback', $template->document_key)"
    mode="edit"
    user-type="admin"
    :save-route="route('templates.salvar', $tipo)"
    :back-route="route('templates.index')"
    :show-variables-panel="true"
    :show-toolbar="true"
    :template-id="$template->id"
/>
```

### 2. Parlamentar - Criação de Proposição

```blade
<x-onlyoffice-editor
    :document-key="$documentKey"
    :document-url="$documentUrl"
    :document-title="'Proposição ' . $proposicao->id"
    document-type="rtf"
    :callback-url="$callbackUrl"
    mode="edit"
    user-type="parlamentar"
    :save-route="route('api.onlyoffice.force-save', ['type' => 'proposicao', 'id' => $proposicao->id])"
    :back-route="route('proposicoes.minhas-proposicoes')"
    :show-variables-panel="false"
    :show-toolbar="true"
    :proposicao-id="$proposicao->id"
/>
```

### 3. Legislativo - Edição de Proposição

```blade
<x-onlyoffice-editor
    :document-key="$config['document']['key']"
    :document-url="$config['document']['url']"
    :document-title="$titulo"
    document-type="word"
    :callback-url="$config['editorConfig']['callbackUrl']"
    mode="edit"
    user-type="legislativo"
    :save-route="route('api.onlyoffice.save', ['type' => 'proposicao', 'id' => $proposicao->id])"
    :back-route="route('proposicoes.show', $proposicao)"
    :show-variables-panel="false"
    :show-toolbar="true"
    :proposicao-id="$proposicao->id"
    :custom-actions="[
        [
            'label' => 'Converter para PDF',
            'onclick' => 'converterParaPDF()',
            'class' => 'btn-warning btn-sm',
            'icon' => 'ki-duotone ki-file-pdf fs-2'
        ],
        [
            'label' => 'Aprovar',
            'onclick' => 'aprovarProposicao()',
            'class' => 'btn-success btn-sm',
            'icon' => 'ki-duotone ki-check-circle fs-2'
        ]
    ]"
/>
```

## Ações Customizadas

Você pode adicionar botões customizados na toolbar passando um array de ações:

```php
$customActions = [
    [
        'label' => 'Minha Ação',
        'onclick' => 'minhaFuncaoJS()',
        'class' => 'btn-primary btn-sm',
        'icon' => 'ki-duotone ki-star fs-2'
    ]
];
```

## JavaScript API

O componente expõe um objeto global `onlyofficeEditor` com os seguintes métodos:

```javascript
// Forçar salvamento
onlyofficeEditor.forceSave();

// Mostrar toast
onlyofficeEditor.showToast('Mensagem', 'success', 3000);

// Atualizar status
onlyofficeEditor.updateStatusBadge('saved'); // saved, modified, saving, error

// Fechar editor
onlyofficeEditor.fecharEditor('/minha-rota');

// Toggle painel de variáveis
onlyofficeEditor.togglePanel();

// Inserir variável
onlyofficeEditor.inserirVariavel('${nome_variavel}');
```

## Eventos

O componente gerencia automaticamente os seguintes eventos do OnlyOffice:

- `onDocumentReady`: Quando o documento está pronto
- `onDocumentStateChange`: Quando o documento é modificado
- `onError`: Quando ocorre um erro
- `onRequestSave`: Quando o salvamento é requisitado

## Personalização

### Adicionar Scripts Customizados

```blade
<x-onlyoffice-editor ...>
    @push('scripts')
    <script>
        // Seu código JavaScript aqui
        function minhaFuncao() {
            console.log('Função customizada');
        }
    </script>
    @endpush
</x-onlyoffice-editor>
```

### Estilos Customizados

O componente usa Bootstrap 5 e os estilos do tema Metronic. Você pode sobrescrever estilos usando CSS inline ou classes customizadas.

## Migração das Implementações Antigas

Para migrar das implementações antigas para o novo componente:

1. **Identifique os parâmetros necessários** da implementação antiga
2. **Substitua a view antiga** pela chamada do componente
3. **Passe os parâmetros** conforme documentado acima
4. **Teste a funcionalidade** em cada contexto

### Exemplo de Migração

**Antes:**
```blade
@extends('layouts.app')
@section('content')
<div id="onlyoffice-editor"></div>
<script>
    // Centenas de linhas de código JavaScript
</script>
@endsection
```

**Depois:**
```blade
<x-onlyoffice-editor
    :document-key="$documentKey"
    :document-url="$documentUrl"
    :callback-url="$callbackUrl"
    user-type="admin"
/>
```

## Benefícios da Componentização

1. **Reutilização de Código**: Um único componente para todos os contextos
2. **Manutenção Simplificada**: Mudanças em um único lugar
3. **Padronização**: Interface consistente em todo o sistema
4. **Performance**: Menos código duplicado, carregamento mais rápido
5. **Testabilidade**: Mais fácil de testar um componente isolado
6. **Flexibilidade**: Parâmetros permitem customização sem duplicação

## Troubleshooting

### Erro: "OnlyOffice API não carregada"
- Verifique se o servidor OnlyOffice está rodando
- Confirme a URL do servidor no arquivo `.env`

### Erro: "Documento não pode ser salvo"
- Verifique a URL de callback
- Confirme as permissões do usuário
- Verifique os logs do Laravel

### Editor não carrega
- Verifique o console do navegador para erros JavaScript
- Confirme que todos os parâmetros obrigatórios foram passados
- Verifique a conectividade com o servidor OnlyOffice

## Suporte

Para suporte ou dúvidas sobre o componente, consulte a equipe de desenvolvimento ou abra uma issue no repositório do projeto.