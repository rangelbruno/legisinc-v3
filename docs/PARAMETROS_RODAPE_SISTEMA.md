# Sistema de Parâmetros do Rodapé - Documentação Completa

## 📋 Visão Geral

O sistema de parâmetros do rodapé permite configurar e gerenciar o texto, imagem e propriedades do rodapé que será utilizado nos documentos do sistema Legisinc. O sistema suporta três tipos de rodapé: texto, imagem ou misto (texto + imagem).

## 🚀 Acesso ao Sistema

### URL Principal
```
GET/POST /parametros-templates-rodape
```

### Rotas Configuradas
- **GET** `/parametros-templates-rodape` - Exibe a tela de configuração do rodapé
- **POST** `/parametros-templates-rodape` - Salva as configurações do rodapé
- **POST** `/upload/rodape` - Upload da imagem do rodapé

## 🏗️ Arquitetura do Sistema

### Controllers Envolvidos

#### 1. TemplateFooterController
**Localização**: `/app/Http/Controllers/TemplateFooterController.php`

**Métodos principais**:
- `index()`: Exibe a tela de configuração do rodapé
- `store(Request $request)`: Salva as configurações do rodapé

**Parâmetros configuráveis**:
- `usar_rodape` (boolean): Ativa/desativa o rodapé automático
- `rodape_tipo` (string): Tipo do rodapé (texto, imagem, misto)
- `rodape_texto` (string): Texto do rodapé (máximo 500 caracteres)
- `rodape_posicao` (string): Posição (rodape, final, todas_paginas)
- `rodape_alinhamento` (string): Alinhamento (esquerda, centro, direita)
- `rodape_numeracao` (boolean): Incluir numeração de páginas

#### 2. ImageUploadController
**Localização**: `/app/Http/Controllers/ImageUploadController.php`

**Método específico do rodapé**:
- `uploadRodape(Request $request)`: Gerencia o upload da imagem do rodapé

**Validações de upload**:
- Formatos aceitos: JPEG, JPG, PNG
- Tamanho máximo: 2MB
- Nome do arquivo: `rodape.[extensão]`
- Recomendação: Imagem com fundo transparente

## 💾 Estrutura do Banco de Dados

### Organização Hierárquica
```
Módulo: "Templates" (ID: 6)
└── Submódulo: "Rodapé"
    ├── Campo: "usar_rodape" → boolean (true)
    ├── Campo: "rodape_tipo" → string ("texto", "imagem", "misto")
    ├── Campo: "rodape_texto" → text (max 500 chars)
    ├── Campo: "rodape_imagem" → string (caminho da imagem)
    ├── Campo: "rodape_posicao" → string ("rodape", "final", "todas_paginas")
    ├── Campo: "rodape_alinhamento" → string ("esquerda", "centro", "direita")
    └── Campo: "rodape_numeracao" → boolean (true)
```

### Tabelas Utilizadas

#### parametros_modulos
```sql
- id: 6 (Templates)
- nome: "Templates"
- descricao: "Configurações de templates do sistema"
- ativo: true
```

#### parametros_submodulos
```sql
- id: [auto]
- modulo_id: 6
- nome: "Rodapé"
- descricao: "Configurações do rodapé dos documentos"
- ativo: true
```

#### parametros_campos
Campos específicos do rodapé:
```sql
- nome: "usar_rodape" | tipo: "boolean" | obrigatorio: false
- nome: "rodape_tipo" | tipo: "select" | opcoes: ["texto","imagem","misto"]
- nome: "rodape_texto" | tipo: "textarea" | max_length: 500
- nome: "rodape_imagem" | tipo: "image" | 
- nome: "rodape_posicao" | tipo: "select" | opcoes: ["rodape","final","todas_paginas"]
- nome: "rodape_alinhamento" | tipo: "radio" | opcoes: ["esquerda","centro","direita"]
- nome: "rodape_numeracao" | tipo: "boolean"
```

#### parametros_valores
```sql
- campo_id: [FK para parametros_campos]
- valor: [valor do parâmetro]
- tipo_valor: "string", "boolean", etc.
- valido_ate: null (valor atual)
```

## 📁 Armazenamento de Imagens

### Localização Física
- **Diretório**: `/public/template/`
- **Arquivo padrão**: `rodape.png`
- **Permissões**: 755 (criado automaticamente se não existir)

### Processo de Upload
1. **Validação**: Arquivo deve ser imagem (JPEG/JPG/PNG) com máximo 2MB
2. **Nomenclatura**: Arquivo renomeado para `rodape.[extensão]`
3. **Armazenamento**: Salvo em `/public/template/`
4. **Banco de dados**: Caminho relativo salvo como `template/rodape.[extensão]`
5. **Parâmetro**: Valor atualizado em `Templates > Rodapé > rodape_imagem`
6. **Cache**: Cache de parâmetros invalidado automaticamente

## 🔧 Serviços Utilizados

### ParametroService
**Localização**: `/app/Services/Parametro/ParametroService.php`

**Métodos para rodapé**:
```php
// Obter configurações específicas
$this->parametroService->obterValor('Templates', 'Rodapé', 'usar_rodape');
$this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_tipo');
$this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_texto');
$this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_imagem');
$this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_posicao');
$this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_alinhamento');
$this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_numeracao');

// Salvar configuração específica
$this->parametroService->salvarValor('Templates', 'Rodapé', 'rodape_texto', $valor);
```

### TemplateVariableService
**Localização**: `/app/Services/Template/TemplateVariableService.php`

**Variáveis disponíveis para templates**:
```php
$variables['rodape_texto'] = $this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_texto') ?: 'Documento oficial da Câmara Municipal';
$variables['rodape_numeracao'] = $this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_numeracao') ?: '1';
```

## 🎨 Interface do Usuário

### Tela de Configuração
**View**: `/resources/views/modules/parametros/templates/rodape.blade.php`

**Componentes principais**:

#### 1. Configurações Gerais
- **Switch "Usar Rodapé"**: Ativa/desativa rodapé automático
- **Select "Tipo de Rodapé"**: Escolha entre texto, imagem ou misto

#### 2. Configuração de Texto
- **Textarea**: Texto do rodapé (máximo 500 caracteres)
- **Visibilidade**: Mostrado apenas quando tipo é "texto" ou "misto"

#### 3. Configuração de Imagem
- **Upload de imagem**: Preview com botões alterar/cancelar/remover
- **Validação**: Frontend valida formato e tamanho
- **Visibilidade**: Mostrado apenas quando tipo é "imagem" ou "misto"

#### 4. Posicionamento
- **Select "Posição"**: 
  - `rodape`: Rodapé da página
  - `final`: Final do documento
  - `todas_paginas`: Todas as páginas

#### 5. Formatação
- **Radio "Alinhamento"**: Esquerda, centro, direita
- **Switch "Numeração"**: Incluir numeração de páginas

#### 6. Preview em Tempo Real
- **Visualização**: Mostra como ficará o rodapé
- **Informações**: Exibe configurações atuais
- **Atualização**: Preview atualiza conforme alterações

### JavaScript Frontend
**Funcionalidades**:
- **Toggle dinâmico**: Mostra/oculta campos conforme tipo selecionado
- **Upload AJAX**: Upload de imagem com feedback visual
- **Preview em tempo real**: Atualização automática da visualização
- **Validações**: Formato de arquivo, tamanho, campos obrigatórios
- **Feedback**: SweetAlert2 para confirmações e erros

## 🔄 Fluxo de Funcionamento

### 1. Configuração Initial (Seeder)
```php
// Valores padrão criados pelo sistema
'usar_rodape' => true
'rodape_tipo' => 'texto'
'rodape_texto' => 'Este documento foi gerado automaticamente pelo Sistema Legislativo.'
'rodape_imagem' => 'template/rodape.png'
'rodape_posicao' => 'rodape'
'rodape_alinhamento' => 'centro'
'rodape_numeracao' => true
```

### 2. Alteração de Configurações
1. **Interface**: Usuário acessa `/parametros-templates-rodape`
2. **Carregamento**: `TemplateFooterController::index()` obtém configurações atuais
3. **Exibição**: View renderiza formulário com valores existentes
4. **Preview**: JavaScript atualiza preview conforme configurações

### 3. Upload de Nova Imagem
1. **Seleção**: Usuário escolhe arquivo via interface
2. **Validação**: JavaScript valida formato (PNG/JPG/JPEG) e tamanho (max 2MB)
3. **Upload**: AJAX POST para `/upload/rodape`
4. **Processamento**: `ImageUploadController::uploadRodape()`:
   - Move arquivo para `/public/template/rodape.[ext]`
   - Salva caminho no BD via `ParametroService::salvarValor()`
   - Invalida cache de parâmetros
   - Retorna URL para atualizar preview
5. **Feedback**: Preview atualizado e mensagem de sucesso

### 4. Salvamento de Configurações
1. **Submissão**: Formulário enviado via AJAX POST
2. **Validação**: `TemplateFooterController::store()` valida dados:
   - `usar_rodape`: boolean
   - `rodape_tipo`: valores permitidos (texto/imagem/misto)
   - `rodape_texto`: máximo 500 caracteres
   - `rodape_posicao`: valores permitidos
   - `rodape_alinhamento`: valores permitidos
   - `rodape_numeracao`: boolean
3. **Salvamento**: Cada configuração salva individualmente via `ParametroService`
4. **Cache**: Invalidação automática do cache
5. **Resposta**: JSON de sucesso/erro

### 5. Utilização nos Templates
1. **Carregamento**: `TemplateVariableService::getTemplateVariables()` obtém valores
2. **Disponibilização**: Variáveis disponíveis para substituição:
   - `${rodape_texto}`: Texto configurado
   - `${rodape_numeracao}`: Status da numeração
3. **Processamento**: Templates RTF/DOCX substituem variáveis
4. **Renderização**: OnlyOffice exibe documento com rodapé aplicado

## 📝 Parâmetros Configuráveis

| Parâmetro | Tipo | Valores Possíveis | Padrão | Descrição |
|-----------|------|------------------|---------|-----------|
| `usar_rodape` | boolean | true, false | `true` | Ativa/desativa rodapé automático |
| `rodape_tipo` | string | texto, imagem, misto | `texto` | Tipo de conteúdo do rodapé |
| `rodape_texto` | string | max 500 chars | `Este documento foi gerado...` | Texto do rodapé |
| `rodape_imagem` | string | caminho relativo | `template/rodape.png` | Caminho da imagem |
| `rodape_posicao` | string | rodape, final, todas_paginas | `rodape` | Posicionamento no documento |
| `rodape_alinhamento` | string | esquerda, centro, direita | `centro` | Alinhamento do conteúdo |
| `rodape_numeracao` | boolean | true, false | `true` | Incluir numeração de páginas |

## 🔒 Permissões e Segurança

### Autenticação
- Sistema possui auto-login para usuário admin (bruno@sistema.gov.br)
- Middleware de autenticação aplicado nas rotas

### Validações
- **Upload**: Apenas imagens JPEG/JPG/PNG, máximo 2MB
- **Texto**: Máximo 500 caracteres
- **Enums**: Valores de posição e alinhamento validados
- **Tipos**: Validação de tipos boolean e string

### Cache e Performance
- Cache automático das configurações (TTL: 1 hora)
- Invalidação automática após alterações
- Otimização para reduzir consultas ao banco
- Preview JavaScript não afeta performance do servidor

## 🚀 Como Usar em Novos Processos

### 1. Para Obter Configurações do Rodapé
```php
use App\Services\Parametro\ParametroService;

$parametroService = app(ParametroService::class);

// Verificar se rodapé está ativo
$usarRodape = $parametroService->obterValor('Templates', 'Rodapé', 'usar_rodape');

// Obter tipo de rodapé
$tipoRodape = $parametroService->obterValor('Templates', 'Rodapé', 'rodape_tipo');

// Obter texto do rodapé
$textoRodape = $parametroService->obterValor('Templates', 'Rodapé', 'rodape_texto');

// Obter imagem do rodapé
$imagemRodape = $parametroService->obterValor('Templates', 'Rodapé', 'rodape_imagem');
$urlImagem = asset($imagemRodape); // URL completa

// Obter todas as configurações de uma vez
$configuracoes = $parametroService->obterConfiguracoes('Templates', 'Rodapé');
```

### 2. Para Usar Variáveis nos Templates
```php
use App\Services\Template\TemplateVariableService;

$templateService = app(TemplateVariableService::class);
$variables = $templateService->getTemplateVariables();

// Variáveis disponíveis:
$rodapeTexto = $variables['rodape_texto']; // "Documento oficial da Câmara Municipal"
$rodapeNumeracao = $variables['rodape_numeracao']; // "1" ou "0"

// Em templates RTF/DOCX usar:
// ${rodape_texto} - substituído pelo texto configurado
// ${rodape_numeracao} - substituído pelo status da numeração
```

### 3. Para Aplicar Rodapé Condicionalmente
```php
// Verificar se deve aplicar rodapé
if ($parametroService->obterValor('Templates', 'Rodapé', 'usar_rodape')) {
    $tipo = $parametroService->obterValor('Templates', 'Rodapé', 'rodape_tipo');
    
    switch($tipo) {
        case 'texto':
            $texto = $parametroService->obterValor('Templates', 'Rodapé', 'rodape_texto');
            // Aplicar apenas texto
            break;
            
        case 'imagem':
            $imagem = $parametroService->obterValor('Templates', 'Rodapé', 'rodape_imagem');
            // Aplicar apenas imagem
            break;
            
        case 'misto':
            $texto = $parametroService->obterValor('Templates', 'Rodapé', 'rodape_texto');
            $imagem = $parametroService->obterValor('Templates', 'Rodapé', 'rodape_imagem');
            // Aplicar texto + imagem
            break;
    }
    
    // Configurações de posicionamento
    $posicao = $parametroService->obterValor('Templates', 'Rodapé', 'rodape_posicao');
    $alinhamento = $parametroService->obterValor('Templates', 'Rodapé', 'rodape_alinhamento');
    $numeracao = $parametroService->obterValor('Templates', 'Rodapé', 'rodape_numeracao');
}
```

### 4. Para Criar Novo Upload de Imagem Similar
```php
// Método no ImageUploadController
public function uploadNovaImagemRodape(Request $request): JsonResponse
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,jpg,png|max:2048'
    ]);

    try {
        $file = $request->file('image');
        $fileName = 'nova-rodape.' . $file->getClientOriginalExtension();
        
        // Salvar em public/template
        $destinationPath = public_path('template');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        
        $file->move($destinationPath, $fileName);
        $relativePath = 'template/' . $fileName;
        
        // Salvar nos parâmetros (criar novo campo se necessário)
        $this->parametroService->salvarValor('Templates', 'Rodapé', 'nova_imagem_rodape', $relativePath);
        
        return response()->json([
            'success' => true,
            'path' => $relativePath,
            'url' => asset($relativePath),
            'message' => 'Nova imagem do rodapé enviada com sucesso!'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao enviar imagem: ' . $e->getMessage()
        ], 500);
    }
}
```

### 5. Para Criar Nova View de Configuração
```php
// Controller
public function novaConfiguracaoRodape(): View
{
    $configuracoes = $this->obterConfiguracoes();
    $moduloId = ParametroModulo::where('nome', 'Templates')->first()->id ?? 6;
    
    return view('modules.parametros.templates.nova-rodape', compact('configuracoes', 'moduloId'));
}

// Obter configurações personalizadas
private function obterConfiguracoes(): array
{
    return [
        'nova_config' => $this->parametroService->obterValor('Templates', 'Rodapé', 'nova_config') ?: 'valor_padrao',
        // ... outras configurações
    ];
}
```

## 🛠️ Comandos Úteis

### Verificar Configurações Atuais
```bash
# Via tinker
php artisan tinker
>>> $service = app(\App\Services\Parametro\ParametroService::class);
>>> $service->obterConfiguracoes('Templates', 'Rodapé');
```

### Limpar Cache
```bash
# Cache é invalidado automaticamente, mas pode ser forçado
php artisan cache:clear
```

### Resetar Configurações
```bash
# Executar seeder específico
php artisan db:seed --class=ParametrosTemplatesSeeder
```

### Verificar Arquivos de Imagem
```bash
ls -la public/template/rodape*
```

## 🐛 Troubleshooting

### Problemas Comuns

1. **Rodapé não aparece nos documentos**
   - Verificar se `usar_rodape` está `true`
   - Confirmar tipo configurado (texto/imagem/misto)
   - Validar se variáveis `${rodape_texto}` estão nos templates
   - Verificar logs de processamento de templates

2. **Upload de imagem falha**
   - Verificar permissões da pasta `/public/template/`
   - Confirmar formato (PNG/JPG/JPEG) e tamanho (max 2MB)
   - Verificar se rota `/upload/rodape` está funcionando
   - Consultar logs do Laravel

3. **Configurações não salvam**
   - Verificar CSRF token na requisição
   - Confirmar estrutura do BD (Templates > Rodapé)
   - Validar dados enviados no formulário
   - Verificar logs de erro do `ParametroService`

4. **Preview não atualiza**
   - Verificar JavaScript no console do navegador
   - Confirmar se SweetAlert2 está carregado
   - Testar eventos de mudança nos campos
   - Verificar se arquivo da view está correto

5. **Numeração não funciona**
   - Confirmar se `rodape_numeracao` está `true`
   - Verificar se template suporta numeração
   - Testar em diferentes posições de rodapé

### Logs Relevantes
- Upload de imagens: `storage/logs/laravel.log`
- Erros de parâmetros: Buscar por "ParametroService"
- JavaScript: Console do navegador
- OnlyOffice: Logs específicos do OnlyOffice

## 📚 Arquivos Relacionados

### Controllers
- `/app/Http/Controllers/TemplateFooterController.php`
- `/app/Http/Controllers/ImageUploadController.php` (método `uploadRodape`)

### Services  
- `/app/Services/Parametro/ParametroService.php`
- `/app/Services/Template/TemplateVariableService.php`

### Views
- `/resources/views/modules/parametros/templates/rodape.blade.php`

### Migrations
- `/database/migrations/2025_07_18_000001_create_parametros_modulos_table.php`
- `/database/migrations/2025_07_18_000002_create_parametros_submodulos_table.php`
- `/database/migrations/2025_07_18_000003_create_parametros_campos_table.php`
- `/database/migrations/2025_07_18_000004_create_parametros_valores_table.php`

### Seeders
- `/database/seeders/ParametrosTemplatesSeeder.php`

### Rotas
- `/routes/web.php` (linhas 639-664, 908)

### JavaScript
- Incluído inline na view `rodape.blade.php` (linhas 404-626)

## 📊 Estrutura de Arquivos

```
public/
└── template/
    ├── cabecalho.png
    ├── marca-dagua.png
    └── rodape.png          ← Imagem do rodapé

app/Http/Controllers/
├── TemplateFooterController.php    ← Controller principal
└── ImageUploadController.php       ← Upload de imagens

app/Services/
├── Parametro/
│   └── ParametroService.php       ← Gerenciamento de parâmetros
└── Template/
    └── TemplateVariableService.php ← Variáveis para templates

resources/views/modules/parametros/templates/
└── rodape.blade.php               ← Interface do usuário

database/
├── migrations/                    ← Estrutura do BD
└── seeders/                      ← Dados iniciais
```

---

**Documentação criada em:** Agosto 2025  
**Versão do sistema:** Legisinc v1.0  
**Última atualização:** 13/08/2025