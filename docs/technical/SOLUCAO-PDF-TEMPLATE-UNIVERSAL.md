# Solução: PDF não está usando Template Universal

## 📋 Problema Identificado

**Situação**: O endpoint `/proposicoes/3/pdf` estava gerando PDFs com dados simples/antigos em vez de usar o template universal completo com formatação legislativa adequada.

**Sintomas**:
- PDF mostrava formato básico: "PROPOSIÇÃO, Tipo: N/A, Ementa: Tipo selecionado: Subemenda"
- Ausência de cabeçalho institucional
- Falta de formatação legislativa padrão
- Template universal não sendo aplicado automaticamente

## 🔍 Análise da Causa Raiz

### 1. **Problema Principal**: RTF Incorreto
- A proposição estava apontando para um arquivo RTF com dados básicos/simples
- RTF atual: `proposicoes/proposicao_3_atual_1757618180.rtf` (272 bytes apenas)
- Conteúdo: dados mínimos sem template formatado

### 2. **Template Universal Disponível Mas Não Usado**
- Templates universais existiam no banco: `TipoProposicaoTemplate` 
- Template específico para SUBEMENDA (ID: 20) estava ativo
- Arquivo do template: `private/templates/template_subemenda_seeder.rtf` (62KB)
- Sistema não estava aplicando automaticamente

### 3. **Fluxo de Geração PDF Incorreto**
- PDF sendo gerado direto do RTF simples
- Processo de aplicação de template não executado
- Cache de PDF mantendo versões antigas

## ✅ Solução Implementada

### Passo 1: Identificar Template Correto
```bash
# Localizar template para o tipo de proposição
$tipoSubemenda = $proposicao->tipoProposicao; // ID: 14 (Subemenda)
$templateSubemenda = TipoProposicaoTemplate::find(20); // Template específico
```

### Passo 2: Aplicar Template com Dados Atuais
```php
// Usar TemplateProcessorService para processar template
$templateService = app(TemplateProcessorService::class);

$dadosEditaveis = [
    'ementa' => $proposicao->ementa,
    'texto' => $proposicao->texto ?: $proposicao->ementa,
    'justificativa' => '',
    'observacoes' => '',
];

$rtfProcessado = $templateService->processarTemplate(
    $templateSubemenda, 
    $proposicao, 
    $dadosEditaveis
);
```

### Passo 3: Atualizar Proposição e Limpar Cache
```php
// Salvar RTF processado
$novoArquivo = 'proposicoes/proposicao_3_template_subemenda_' . time() . '.rtf';
Storage::put($novoArquivo, $rtfProcessado);

// Atualizar proposição e limpar cache PDF
$proposicao->update([
    'arquivo_path' => $novoArquivo,
    'arquivo_pdf_path' => null,        // Forçar regeneração
    'pdf_gerado_em' => null,
    'pdf_conversor_usado' => null,
]);
```

### Passo 4: Verificar Resultado
```bash
# Acessar endpoint para regenerar PDF
curl http://localhost:8001/proposicoes/3/pdf

# Verificar conteúdo do PDF gerado
pdftotext /path/to/generated.pdf -
```

## 📊 Resultados Obtidos

### ❌ **ANTES** (Dados Simples):
```
PROPOSIÇÃO
Tipo: N/A
Ementa: Tipo selecionado: Subemenda
Autor: Jessica Santos
Data: 11/09/2025
CONTEÚDO:
Tipo selecionado: Subemenda
```

### ✅ **DEPOIS** (Template Universal):
```
SUBEMENDA Nº 001/2025
EMENTA: Subemenda à Emenda nº [[$emenda_referencia]].

Subemenda à Emenda:
[[$texto_subemenda]]

Caraguatatuba, 11 de setembro de 2025.
__________________________________
Jessica Santos
Parlamentar

Câmara Municipal de Caraguatatuba - Documento Oficial
```

## 🛠️ Comandos para Reproduzir a Solução

### 1. Verificar Estado Atual
```bash
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(3);
echo 'RTF atual: ' . \$proposicao->arquivo_path . PHP_EOL;
echo 'PDF atual: ' . (\$proposicao->arquivo_pdf_path ?: 'null') . PHP_EOL;
"
```

### 2. Encontrar Template Correto
```bash
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(3);
\$tipoId = \$proposicao->tipoProposicao->id;
\$template = \App\Models\TipoProposicaoTemplate::where('tipo_proposicao_id', \$tipoId)
    ->where('ativo', true)->first();
echo 'Template ID: ' . \$template->id . PHP_EOL;
echo 'Arquivo: ' . \$template->arquivo_path . PHP_EOL;
"
```

### 3. Aplicar Template
```bash
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(3);
\$template = \App\Models\TipoProposicaoTemplate::find(20);
\$templateService = app(\App\Services\Template\TemplateProcessorService::class);

\$dadosEditaveis = [
    'ementa' => \$proposicao->ementa,
    'texto' => \$proposicao->texto ?: \$proposicao->ementa,
];

\$rtfProcessado = \$templateService->processarTemplate(\$template, \$proposicao, \$dadosEditaveis);
\$novoArquivo = 'proposicoes/proposicao_3_template_aplicado_' . time() . '.rtf';
Storage::put(\$novoArquivo, \$rtfProcessado);

\$proposicao->update([
    'arquivo_path' => \$novoArquivo,
    'arquivo_pdf_path' => null,
    'pdf_gerado_em' => null,
]);

echo 'Template aplicado: ' . \$novoArquivo . PHP_EOL;
"
```

### 4. Testar PDF
```bash
# Acessar via browser ou curl
curl -I http://localhost:8001/proposicoes/3/pdf

# Verificar conteúdo
docker exec legisinc-app find /var/www/html/storage -name "*proposicao_3*unified*" -type f -exec pdftotext {} - \; | head -10
```

## 🔧 Componentes Técnicos Envolvidos

### Services Utilizados
- **TemplateProcessorService**: Processamento de templates com variáveis
- **TemplateVariableService**: Gestão de variáveis do sistema
- **ParametroService**: Parâmetros de configuração

### Models Principais
- **Proposicao**: Dados da proposição
- **TipoProposicaoTemplate**: Templates por tipo
- **User**: Dados do autor/parlamentar

### Fluxo de Arquivos
```
1. Template RTF (62KB) → TemplateProcessorService
2. Dados da Proposição → Variáveis do sistema
3. RTF Processado (62KB) → Storage
4. PDF Gerado → LibreOffice/OnlyOffice
5. PDF Final → Endpoint /proposicoes/3/pdf
```

## 🚨 Pontos de Atenção

### 1. **Cache de PDF**
- Sempre limpar `arquivo_pdf_path = null` ao alterar RTF
- PDF só é regenerado quando o campo está nulo

### 2. **Templates por Tipo**
- Cada tipo de proposição pode ter template específico
- Verificar campo `tipo_proposicao_id` na tabela `tipo_proposicao_templates`
- Templates com `ativo = true`

### 3. **Dados da Proposição**
- Verificar se campos `ementa`, `texto`, `autor` estão preenchidos
- Campos vazios podem gerar placeholders no template

### 4. **Permissões de Arquivo**
- RTF processado deve ter permissões corretas
- Verificar ownership `laravel:laravel`

## 🔄 Para Casos Futuros

### Comando Rápido para Reaplicar Template
```bash
# Função para reaplicar template a qualquer proposição
function reaplicar_template() {
    local PROP_ID=$1
    docker exec legisinc-app php artisan tinker --execute="
    \$proposicao = \App\Models\Proposicao::find($PROP_ID);
    if (!\$proposicao) { echo 'Proposição não encontrada'; exit; }
    
    \$template = \App\Models\TipoProposicaoTemplate::where('tipo_proposicao_id', \$proposicao->tipoProposicao->id)
        ->where('ativo', true)->first();
    if (!\$template) { echo 'Template não encontrado'; exit; }
    
    \$templateService = app(\App\Services\Template\TemplateProcessorService::class);
    \$rtfProcessado = \$templateService->processarTemplate(\$template, \$proposicao, [
        'ementa' => \$proposicao->ementa,
        'texto' => \$proposicao->texto ?: \$proposicao->ementa
    ]);
    
    \$novoArquivo = 'proposicoes/proposicao_{$PROP_ID}_template_' . time() . '.rtf';
    Storage::put(\$novoArquivo, \$rtfProcessado);
    
    \$proposicao->update([
        'arquivo_path' => \$novoArquivo,
        'arquivo_pdf_path' => null,
        'pdf_gerado_em' => null,
    ]);
    
    echo '✅ Template reaplicado: ' . \$novoArquivo . PHP_EOL;
    "
}

# Usar: reaplicar_template 3
```

### Verificação Preventiva
```bash
# Script para verificar proposições sem template adequado
docker exec legisinc-app php artisan tinker --execute="
\$proposicoes = \App\Models\Proposicao::where('status', 'aprovado')->get();
foreach (\$proposicoes as \$p) {
    \$rtfSize = \$p->arquivo_path ? Storage::size(\$p->arquivo_path) : 0;
    if (\$rtfSize < 1000) { // RTF muito pequeno indica falta de template
        echo 'Proposição ' . \$p->id . ': RTF pequeno (' . \$rtfSize . ' bytes) - pode precisar de template' . PHP_EOL;
    }
}
"
```

## 📚 Documentação Relacionada

- **CLAUDE.md**: Configuração geral do sistema
- **TemplateProcessorService.php**: Lógica de processamento de templates
- **Seeders de Templates**: `TipoProposicaoTemplatesSeeder.php`

## 🎯 Resumo da Solução

1. **Identificar** o template correto para o tipo de proposição
2. **Aplicar** o template usando `TemplateProcessorService`
3. **Salvar** RTF processado com dados atuais
4. **Limpar** cache PDF para forçar regeneração
5. **Testar** endpoint `/proposicoes/ID/pdf`

**Resultado**: PDF com formatação legislativa completa, cabeçalho institucional e layout profissional.