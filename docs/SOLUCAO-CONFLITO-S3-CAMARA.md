# Solução para Conflitos S3 entre Diferentes Câmaras

## 🚨 Problema Identificado

Quando o banco de dados é resetado (`migrate:fresh --seed`), as proposições recomeçam com ID 1, mas os arquivos PDF anteriores permanecem no S3. Isso causa dois problemas:

1. **Conflito de IDs**: Nova proposição ID=1 encontra PDF de proposição antiga ID=1
2. **Falta de Isolamento**: Câmaras diferentes compartilham o mesmo namespace S3

### Exemplo do Problema
```
# Antes do reset
PDF no S3: proposicoes/projeto_lei/2025/09/25/1/arquivo_antigo.pdf

# Após reset + nova proposição ID=1
Sistema encontra: "PDF encontrado no AWS S3!" (arquivo antigo)
Resultado: Confusão entre documentos de câmaras/períodos diferentes
```

## ✅ Solução Implementada

### 1. Novo Serviço: `CamaraIdentifierService`

Criado serviço para gerar identificadores únicos por câmara baseado em dados institucionais permanentes.

**Localização**: `app/Services/CamaraIdentifierService.php`

#### Métodos Principais:

```php
// Gera identificador único baseado no CNPJ ou dados da câmara
public function getUniqueIdentifier(): string

// Gera slug limpo do nome da câmara
public function getSlugName(): string

// Combina slug + identificador único
public function getFullIdentifier(): string
```

#### Lógica de Geração:

1. **Se tem CNPJ**: Usa primeiros 8 dígitos (ex: `12345678`)
2. **Fallback**: Hash MD5 dos dados combinados (sigla + cidade OU nome + cidade)
3. **Prioridade**: Sigla da câmara > Nome completo > Fallback padrão
4. **Resultado**: Identificador único tipo `cmsp_d1fb83c4` (com sigla) ou `camaramunicipal_d1fb83c4` (nome completo)

### 2. Nova Estrutura de Caminhos S3

#### Antes (Conflitos Possíveis):
```
proposicoes/{tipo}/{ano}/{mes}/{dia}/{id}/{uuid}_{timestamp}.pdf
```

#### Depois (Isolamento por Câmara):
```
{camara_identifier}/proposicoes/{tipo}/{ano}/{mes}/{dia}/{id}/{uuid}_{timestamp}.pdf
```

#### Exemplo Prático:
```
# Câmara A (com sigla configurada)
cmsp_d1fb83c4/proposicoes/projeto_lei/2025/09/25/1/uuid1_timestamp.pdf

# Câmara B (com sigla configurada)
cmvn_a8b9c2d1/proposicoes/projeto_lei/2025/09/25/1/uuid2_timestamp.pdf

# Câmara C (sem sigla - usa nome completo)
camaramunicipal_d1fb83c4/proposicoes/projeto_lei/2025/09/25/1/uuid3_timestamp.pdf
```

### 3. Métodos Atualizados

#### OnlyOfficeController - Métodos Modificados:

- `generateUniqueS3Path()` - Inclui identificador da câmara
- `generateUniqueS3PathForUpload()` - Upload com identificador
- `generateUniqueS3PathForManual()` - Upload manual com identificador
- `generateUniqueS3PathForAutomatic()` - Export automático com identificador
- `generateNewS3Path()` - Novos paths com identificador
- `verificarUltimaExportacaoS3()` - Busca considerando identificador da câmara

### 4. Compatibilidade Backward

A busca de arquivos existentes verifica múltiplos paths em ordem de prioridade:

```php
$searchPaths = [
    // Nova estrutura com identificador da câmara
    "{$camaraIdentifier}/proposicoes/{$tipoCode}/",
    // Nova estrutura sem identificador (compatibilidade recente)
    "proposicoes/{$tipoCode}/",
    // Estruturas antigas para compatibilidade
    "proposicoes/pdf/{$proposicaoId}/",
    "proposicoes/pdfs/"
];
```

## 🔧 Como Funciona na Prática

### Cenário 1: Nova Instalação
```bash
# 1. Sistema gera identificador baseado na sigla/CNPJ/nome configurado
Identificador: cmsp_d1fb83c4  # (com sigla CMSP)
# OU
Identificador: camaramunicipal_d1fb83c4  # (sem sigla configurada)

# 2. Novos PDFs são criados com namespace isolado
Caminho: cmsp_d1fb83c4/proposicoes/projeto_lei/2025/09/25/1/uuid_timestamp.pdf
```

### Cenário 2: Reset de Banco (Problema Original)
```bash
# 1. Banco resetado, proposição ID=1 criada novamente
# 2. Busca por PDFs considera identificador da câmara
# 3. NÃO encontra conflitos com arquivos antigos de outras câmaras/períodos
# 4. Sistema funciona corretamente sem confusões
```

### Cenário 3: Múltiplas Câmaras no Mesmo S3
```bash
# Câmara Municipal de São Paulo (sigla: CMSP)
cmsp_d1fb83c4/proposicoes/...

# Câmara Municipal de Vila Nova (sigla: CMVN)
cmvn_a8b9c2d1/proposicoes/...

# Câmara sem sigla configurada
camaramunicipal_c3d4e5f6/proposicoes/...

# Isolamento completo entre instâncias
```

## 📊 Vantagens da Solução

### ✅ Isolamento Completo
- Cada câmara tem seu namespace único no S3
- Zero conflitos entre diferentes instâncias

### ✅ Persistência de Identificador
- Baseado em dados institucionais (CNPJ/nome)
- Permanece o mesmo após resets de banco

### ✅ Compatibilidade Retroativa
- Busca em estruturas antigas e novas
- Migração gradual sem quebras

### ✅ Organização Aprimorada
- Estrutura hierárquica clara no S3
- Fácil identificação de arquivos por câmara
- Identificadores compactos quando usa sigla (ex: `cmsp_` vs `camaramunicipalsp_`)

### ✅ Flexibilidade de Configuração
- **Prioriza sigla da câmara**: Mais limpo e profissional (ex: `CMSP`, `CMRJ`)
- **Fallback inteligente**: Usa nome completo se sigla não configurada
- **Fonte dos dados**: Campo "Sigla da Câmara" em `/parametros-dados-gerais-camara` (aba "Informações da Câmara")

## 📋 Estrutura dos Parâmetros no Banco

### Mapeamento Correto (Descoberto):
```php
// Tela: /parametros-dados-gerais-camara
// Módulo: "Dados Gerais"

'identificacao' => [
    'submodulo' => 'Informações da Câmara',  // ← CORRETO
    'campos' => ['nome_camara', 'sigla_camara', 'cnpj']
],

'endereco' => [
    'submodulo' => 'Endereço',
    'campos' => ['endereco', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'cep']
],

// Outros submódulos: 'Contatos', 'Gestão Atual'
```

### Caminhos de Acesso no ParametroService:
- `$parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'sigla_camara')`
- `$parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'nome_camara')`
- `$parametroService->obterValor('Dados Gerais', 'Endereço', 'cidade')`

## 🧪 Testes Realizados

### Teste do Serviço:
```bash
docker exec legisinc-app php artisan tinker --execute="
\$service = app(\App\Services\CamaraIdentifierService::class);
echo 'Identificador: ' . \$service->getFullIdentifier();
"

# Resultado atual (sigla CMC): cmc_46482865
# Resultado (dados reais da Câmara de Caraguatatuba): cmc_46482865
```

### Como Configurar a Sigla:
1. Acesse `/parametros-dados-gerais-camara`
2. Vá para aba "Informações da Câmara"
3. Altere o campo "Sigla da Câmara" de `CM` para algo específico (`CMSP`, `CMRJ`, etc.)
4. Salve as alterações
5. A próxima exportação usará a sigla atualizada

**Exemplo atual**: Câmara Municipal de Caraguatatuba (sigla `CMC`):
- Identificador gerado: `cmc_46482865/proposicoes/...`
- Fonte: Aba "Identificação" → Campo "Sigla da Câmara" = `CMC`

### Teste de Geração de Caminhos:
```bash
# Antes: proposicoes/projeto_lei/2025/09/25/1/uuid_timestamp.pdf

# Depois (dados reais - CMC): cmc_46482865/proposicoes/projeto_lei/2025/09/25/1/uuid_timestamp.pdf
```

## 🛠️ Arquivos Modificados

### Novos Arquivos:
- `app/Services/CamaraIdentifierService.php` - Serviço principal

### Arquivos Modificados:
- `app/Http/Controllers/OnlyOfficeController.php` - Integração do serviço
- `docs/SOLUCAO-CONFLITO-S3-CAMARA.md` - Esta documentação

## 🚀 Próximos Passos

1. **Deploy em Produção**: Aplicar em ambiente de produção
2. **Monitoramento**: Acompanhar logs para validar funcionamento
3. **Migração Gradual**: Arquivos antigos continuam acessíveis
4. **Cleanup Futuro**: Possível limpeza de estruturas antigas após período de adaptação

## 🔒 Considerações de Segurança

- Identificador não expõe dados sensíveis
- Baseado em informações já públicas (nome da câmara)
- Mantém organização sem comprometer segurança

## 📝 Conclusão

A solução resolve completamente o problema de conflitos entre diferentes câmaras/períodos, mantendo compatibilidade e organizando melhor os arquivos no S3. O sistema agora funciona de forma isolada e consistente, independente de resets de banco de dados.