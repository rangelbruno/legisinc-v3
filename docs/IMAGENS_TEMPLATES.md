# Sistema de Imagens em Templates de Proposições

## Visão Geral

O sistema foi configurado para usar uma imagem padrão de cabeçalho em todos os templates de proposições, garantindo que as imagens sejam sempre salvas na pasta `public` para evitar problemas de acesso quando os documentos são importados por outros usuários.

## Imagem Padrão do Cabeçalho

### Localização
- **Arquivo**: `public/template/cabecalho.png`
- **Variável no template**: `${imagem_cabecalho}`
- **URL gerada**: Utiliza a função `asset()` do Laravel para gerar URL absoluta

### Como Funciona

1. **Variável do Sistema**: Foi adicionada a variável `imagem_cabecalho` às variáveis do sistema no `TemplateProcessorService.php`

2. **Substituição Automática**: Quando um template é processado, a variável `${imagem_cabecalho}` é automaticamente substituída pela URL completa da imagem

3. **Template Inicial**: Novos templates criados já incluem referência à imagem do cabeçalho

## Upload de Imagens

### Serviço de Upload

Foi criado o `ImageUploadService` com métodos específicos para:

- **Upload de imagem para templates**: `uploadTemplateImage()`
  - Salva em: `public/template/images/`
  
- **Upload de imagem para proposições**: `uploadProposicaoImage()`
  - Salva em: `public/proposicoes/{id}/`

- **Upload múltiplo**: `uploadMultiple()`
  - Permite enviar várias imagens de uma vez

### Controlador de Upload

O `ImageUploadController` fornece endpoints para:

```php
// Upload de imagem para template
POST /images/upload/template

// Upload de imagem para proposição específica
POST /images/upload/proposicao/{proposicao_id}

// Upload múltiplo
POST /images/upload/multiple
```

### Validações

- Formato: Apenas arquivos de imagem (jpg, png, gif, etc.)
- Tamanho máximo: 10MB por arquivo
- Nomes únicos: Usa UUID para evitar conflitos

## Integração com OnlyOffice

### Template Inicial

Quando um novo template é criado, o sistema:

1. Cria um documento RTF com referência à imagem do cabeçalho
2. Inclui a variável `${imagem_cabecalho}` no documento
3. O OnlyOffice permite edição visual mantendo a variável

### Processamento

Quando uma proposição é criada usando o template:

1. O `TemplateProcessorService` substitui `${imagem_cabecalho}` pela URL real
2. A imagem é exibida corretamente no documento final
3. Como a imagem está em `public/`, ela permanece acessível

## Estrutura de Pastas

```
public/
├── template/
│   ├── cabecalho.png         # Imagem padrão do cabeçalho
│   └── images/               # Outras imagens de templates
├── proposicoes/
│   ├── 1/                    # Imagens da proposição ID 1
│   ├── 2/                    # Imagens da proposição ID 2
│   └── ...
└── uploads/                  # Uploads genéricos
```

## Benefícios

1. **Consistência**: Todos os documentos têm o mesmo cabeçalho padrão
2. **Portabilidade**: Imagens em `public/` são sempre acessíveis
3. **Organização**: Estrutura clara de pastas por tipo
4. **Performance**: Servidas diretamente pelo servidor web
5. **Segurança**: Validações previnem uploads maliciosos

## Uso no Frontend

### Upload de Imagem

```javascript
// Exemplo de upload via AJAX
const formData = new FormData();
formData.append('image', fileInput.files[0]);

fetch('/images/upload/template', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Imagem enviada:', data.url);
        // Usar data.url no editor
    }
});
```

### Exibição no Template

```html
<!-- No editor de templates -->
<img src="${imagem_cabecalho}" alt="Cabeçalho" />

<!-- Após processamento -->
<img src="http://seu-dominio.com/template/cabecalho.png" alt="Cabeçalho" />
```

## Manutenção

### Atualizar Imagem do Cabeçalho

1. Substituir o arquivo `public/template/cabecalho.png`
2. Limpar cache se necessário: `php artisan cache:clear`
3. Novos documentos usarão automaticamente a nova imagem

### Limpeza de Imagens Antigas

```bash
# Remover imagens de proposições deletadas
php artisan images:cleanup

# Listar imagens órfãs
php artisan images:orphans
```

## Considerações de Segurança

1. **Validação de Tipo**: Apenas imagens são aceitas
2. **Limite de Tamanho**: Máximo 10MB por arquivo
3. **Nomes Únicos**: UUID previne sobrescrita acidental
4. **Permissões**: Apenas usuários autenticados podem fazer upload
5. **Sanitização**: Nomes de arquivo são sanitizados

## Troubleshooting

### Imagem não aparece no documento

1. Verificar se o arquivo existe em `public/template/cabecalho.png`
2. Confirmar permissões de leitura no arquivo
3. Testar URL diretamente: `http://seu-dominio.com/template/cabecalho.png`
4. Verificar logs para erros de processamento

### Upload falha

1. Verificar limite de upload do PHP (`upload_max_filesize`)
2. Confirmar permissões de escrita em `public/`
3. Verificar espaço em disco disponível
4. Revisar logs de erro do Laravel