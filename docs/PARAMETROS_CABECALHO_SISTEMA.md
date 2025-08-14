# Sistema de Parâmetros do Cabeçalho - Documentação Completa

## 📋 Visão Geral

O sistema de parâmetros do cabeçalho permite configurar e gerenciar a imagem e propriedades do cabeçalho padrão que é utilizado nas proposições do sistema Legisinc.

## 🚀 Acesso ao Sistema

### URL Principal
```
GET/POST /parametros-templates-cabecalho
```

### Rotas Configuradas
- **GET** `/parametros-templates-cabecalho` - Exibe a tela de configuração
- **POST** `/parametros-templates-cabecalho` - Salva as configurações
- **POST** `/upload/cabecalho` - Upload da imagem do cabeçalho

## 🏗️ Arquitetura do Sistema

### Controllers Envolvidos

#### 1. TemplateHeaderController
**Localização**: `/app/Http/Controllers/TemplateHeaderController.php`

**Métodos principais**:
- `index()`: Exibe a tela de configuração do cabeçalho
- `store(Request $request)`: Salva as configurações do cabeçalho

**Parâmetros configuráveis**:
- `usar_cabecalho_padrao` (boolean): Ativa/desativa o cabeçalho padrão
- `cabecalho_altura` (integer): Altura do cabeçalho em pixels (50-300px)
- `cabecalho_posicao` (string): Posição do cabeçalho (topo, header, marca_dagua)

#### 2. ImageUploadController
**Localização**: `/app/Http/Controllers/ImageUploadController.php`

**Método principal**:
- `uploadCabecalhoTemplate(Request $request)`: Gerencia o upload da imagem do cabeçalho

**Validações de upload**:
- Formatos aceitos: JPEG, JPG, PNG
- Tamanho máximo: 2MB
- Nome do arquivo: `cabecalho.[extensão]`

## 💾 Estrutura do Banco de Dados

### Tabelas Principais

#### parametros_modulos
```sql
- id (bigint, PK)
- nome (varchar) - "Templates"
- descricao (text)
- icon (varchar)
- ordem (integer)
- ativo (boolean)
- timestamps
```

#### parametros_submodulos
```sql
- id (bigint, PK)
- modulo_id (bigint, FK)
- nome (varchar) - "Cabeçalho"
- descricao (text)
- ordem (integer)
- ativo (boolean)
- timestamps
```

#### parametros_campos
```sql
- id (bigint, PK)
- submodulo_id (bigint, FK)
- nome (varchar) - Nome do campo (ex: "cabecalho_imagem")
- label (varchar) - Label exibida na interface
- tipo_campo (varchar) - Tipo do campo (text, select, etc.)
- obrigatorio (boolean)
- ordem (integer)
- timestamps
```

#### parametros_valores
```sql
- id (bigint, PK)
- campo_id (bigint, FK)
- valor (text) - Valor armazenado
- tipo_valor (varchar) - Tipo do valor (string, json, boolean)
- user_id (bigint, FK, nullable)
- valido_ate (timestamp, nullable)
- timestamps
```

## 📁 Armazenamento de Imagens

### Localização Física
- **Diretório**: `/public/template/`
- **Arquivo padrão**: `cabecalho.png`
- **Permissões**: 755 (criado automaticamente se não existir)

### Processo de Upload
1. **Validação**: Arquivo deve ser imagem (JPEG/JPG/PNG) com máximo 2MB
2. **Nomenclatura**: Arquivo renomeado para `cabecalho.[extensão]`
3. **Armazenamento**: Salvo em `/public/template/`
4. **Banco de dados**: Caminho relativo salvo como `template/cabecalho.[extensão]`
5. **Cache**: Cache de parâmetros invalidado automaticamente

## 🔧 Serviços Utilizados

### ParametroService
**Localização**: `/app/Services/Parametro/ParametroService.php`

**Métodos principais**:
- `obterValor(string $modulo, string $submodulo, string $campo)`: Obtém valor específico
- `salvarValor(string $modulo, string $submodulo, string $campo, mixed $valor)`: Salva valor específico
- `obterConfiguracoes(string $modulo, string $submodulo)`: Obtém todas configurações

### TemplateVariableService
**Localização**: `/app/Services/Template/TemplateVariableService.php`

**Variável utilizada**:
```php
$variables['cabecalho_imagem'] = $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_imagem') ?: '';
```

## 🎨 Interface do Usuário

### Tela de Configuração
**View**: `/resources/views/modules/parametros/templates/cabecalho.blade.php`

**Componentes**:
1. **Upload de Imagem**: 
   - Preview da imagem atual
   - Botões para alterar, cancelar e remover
   - Validação frontend em JavaScript

2. **Configurações**:
   - Switch para ativar/desativar cabeçalho padrão
   - Campo numérico para altura (50-300px)
   - Select para posição (topo, header, marca d'água)

3. **Painel Informativo**:
   - Status atual da imagem
   - Dimensões recomendadas (800x200px)
   - Dicas de uso

### JavaScript Frontend
**Funcionalidades**:
- Upload de imagem via AJAX com preview em tempo real
- Validação de tipo e tamanho de arquivo
- Feedback visual com SweetAlert2
- Atualização automática do preview

## 🔄 Fluxo de Funcionamento

### 1. Configuração Initial
```bash
# Dados criados pelo seeder
Módulo: "Templates" (ID: 6)
├── Submódulo: "Cabeçalho" (ID: auto)
    ├── Campo: "cabecalho_imagem" → "template/cabecalho.png"
    ├── Campo: "usar_cabecalho_padrao" → true
    ├── Campo: "cabecalho_altura" → 150
    └── Campo: "cabecalho_posicao" → "topo"
```

### 2. Upload de Nova Imagem
1. Usuário seleciona arquivo na interface
2. JavaScript valida tipo e tamanho
3. AJAX POST para `/upload/cabecalho`
4. `ImageUploadController::uploadCabecalhoTemplate()` processa:
   - Move arquivo para `/public/template/cabecalho.[ext]`
   - Salva caminho no BD via `ParametroService`
   - Invalida cache
   - Retorna URL para atualizar preview

### 3. Salvamento de Configurações
1. Usuário altera configurações e clica "Salvar"
2. AJAX POST para `/parametros-templates-cabecalho`
3. `TemplateHeaderController::store()` processa:
   - Valida dados recebidos
   - Salva cada configuração via `ParametroService`
   - Retorna confirmação de sucesso

### 4. Utilização nos Templates
1. `TemplateVariableService::getTemplateVariables()` carrega:
   ```php
   $variables['cabecalho_imagem'] = 'template/cabecalho.png'
   ```
2. Templates RTF/DOCX substituem `${imagem_cabecalho}` pelo valor
3. OnlyOffice renderiza documento com imagem do cabeçalho

## 📝 Parâmetros Configuráveis

| Parâmetro | Tipo | Padrão | Descrição |
|-----------|------|--------|-----------|
| `cabecalho_imagem` | string | `template/cabecalho.png` | Caminho da imagem do cabeçalho |
| `usar_cabecalho_padrao` | boolean | `true` | Ativa/desativa cabeçalho automático |
| `cabecalho_altura` | integer | `150` | Altura em pixels (50-300px) |
| `cabecalho_posicao` | string | `topo` | Posição (topo/header/marca_dagua) |

## 🔒 Permissões e Segurança

### Autenticação
- Sistema possui auto-login para usuário admin (bruno@sistema.gov.br)
- Middleware de autenticação aplicado nas rotas

### Validações
- **Upload**: Apenas imagens JPEG/JPG/PNG, máximo 2MB
- **Altura**: Entre 50px e 300px
- **Posição**: Apenas valores predefinidos (topo, header, marca_dagua)

### Cache
- Cache automático das configurações (TTL: 1 hora)
- Invalidação automática após alterações
- Otimização para reduzir consultas ao banco

## 🚀 Como Usar em Novos Processos

### 1. Para Obter Imagem do Cabeçalho
```php
use App\Services\Template\TemplateVariableService;

$templateService = app(TemplateVariableService::class);
$variables = $templateService->getTemplateVariables();
$imagemCabecalho = $variables['cabecalho_imagem']; // "template/cabecalho.png"
$urlCompleta = asset($imagemCabecalho); // "http://localhost:8001/template/cabecalho.png"
```

### 2. Para Verificar se Cabeçalho Está Ativo
```php
use App\Services\Parametro\ParametroService;

$parametroService = app(ParametroService::class);
$usarCabecalho = $parametroService->obterValor('Templates', 'Cabeçalho', 'usar_cabecalho_padrao');

if ($usarCabecalho) {
    // Aplicar cabeçalho no documento
}
```

### 3. Para Obter Configurações Completas
```php
$configuracoes = $parametroService->obterConfiguracoes('Templates', 'Cabeçalho');
/*
Array retornado:
[
    'cabecalho_imagem' => ['valor' => 'template/cabecalho.png', 'tipo' => 'text', ...],
    'usar_cabecalho_padrao' => ['valor' => true, 'tipo' => 'boolean', ...],
    'cabecalho_altura' => ['valor' => 150, 'tipo' => 'number', ...],
    'cabecalho_posicao' => ['valor' => 'topo', 'tipo' => 'select', ...]
]
*/
```

### 4. Para Criar Novo Upload de Imagem
```php
// Adicionar rota
Route::post('/upload/nova-imagem', [ImageUploadController::class, 'uploadNovaImagem'])->name('upload.nova');

// Implementar método no ImageUploadController
public function uploadNovaImagem(Request $request): JsonResponse
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,jpg,png|max:2048'
    ]);

    try {
        $file = $request->file('image');
        $fileName = 'nova-imagem.' . $file->getClientOriginalExtension();
        
        $destinationPath = public_path('template');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        
        $file->move($destinationPath, $fileName);
        $relativePath = 'template/' . $fileName;
        
        // Salvar nos parâmetros
        $this->parametroService->salvarValor('Templates', 'NovoSubmodulo', 'nova_imagem', $relativePath);
        
        return response()->json([
            'success' => true,
            'path' => $relativePath,
            'url' => asset($relativePath),
            'message' => 'Nova imagem enviada com sucesso!'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao enviar nova imagem: ' . $e->getMessage()
        ], 500);
    }
}
```

## 🛠️ Comandos Úteis

### Limpar Cache de Parâmetros
```bash
# Via artisan (se implementado)
php artisan cache:clear-parametros

# Via banco de dados
# Cache é invalidado automaticamente, mas pode ser forçado via código
```

### Resetar Configurações Padrão
```bash
# Executar seeder específico
php artisan db:seed --class=ParametrosTemplatesSeeder
```

### Verificar Imagens Existentes
```bash
ls -la public/template/
```

## 🐛 Troubleshooting

### Problemas Comuns

1. **Imagem não aparece nos documentos**
   - Verificar se arquivo existe em `/public/template/`
   - Verificar se valor está salvo no banco de dados
   - Limpar cache de parâmetros

2. **Upload falha**
   - Verificar permissões da pasta `/public/template/`
   - Confirmar que arquivo atende validações (tipo, tamanho)
   - Verificar logs do Laravel

3. **Configurações não salvam**
   - Verificar CSRF token na requisição
   - Confirmar estrutura do banco (módulo/submódulo/campo)
   - Verificar logs de erro

### Logs Relevantes
- Upload de imagens: `storage/logs/laravel.log`
- Erros de parâmetros: Buscar por "ParametroService" nos logs
- JavaScript: Console do navegador

## 📚 Arquivos Relacionados

### Controllers
- `/app/Http/Controllers/TemplateHeaderController.php`
- `/app/Http/Controllers/ImageUploadController.php`

### Services  
- `/app/Services/Parametro/ParametroService.php`
- `/app/Services/Template/TemplateVariableService.php`

### Views
- `/resources/views/modules/parametros/templates/cabecalho.blade.php`

### Migrations
- `/database/migrations/2025_07_18_000001_create_parametros_modulos_table.php`
- `/database/migrations/2025_07_18_000002_create_parametros_submodulos_table.php`
- `/database/migrations/2025_07_18_000003_create_parametros_campos_table.php`
- `/database/migrations/2025_07_18_000004_create_parametros_valores_table.php`

### Seeders
- `/database/seeders/ParametrosTemplatesSeeder.php`

### Rotas
- `/routes/web.php` (linhas 554-580, 906)

---

**Documentação criada em:** Agosto 2025  
**Versão do sistema:** Legisinc v1.0  
**Última atualização:** 13/08/2025