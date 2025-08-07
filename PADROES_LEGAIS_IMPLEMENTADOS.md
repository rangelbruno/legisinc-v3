# 🏛️ Padrões Legais Implementados

Sistema configurado para atender aos padrões de documentos oficiais brasileiros conforme LC 95/1998 e normas internacionais.

## ✅ Conformidades Implementadas

### 1. **Lei Complementar 95/1998** - Estrutura Obrigatória
- **Epígrafe**: Formatação automática conforme tipo e numeração unificada
- **Ementa**: Validação de frase única com verbo no indicativo
- **Preâmbulo**: Fórmula de promulgação automática baseada na origem
- **Corpo Articulado**: Numeração sequencial de artigos, parágrafos, incisos e alíneas
- **Cláusula de Vigência**: Geração automática conforme configuração

### 2. **Numeração Unificada** (Padrão desde 2019)
- Sistema unificado por tipo e ano legislativo
- Numeração automática com zeros à esquerda
- Controle de duplicação e lacunas
- Estatísticas por tipo de proposição
- Formato: `TIPO Nº 001/2025`

### 3. **Metadados Internacionais**
- **Dublin Core**: 15 elementos obrigatórios + extensões DCTERMS
- **LexML Brasil**: URN padronizada `urn:lex:autoridade:tipo:numero;ano`
- **OAI-PMH**: Exposição para harvesting automático
- **Akoma Ntoso XML**: Padrão OASIS para documentos legais

### 4. **Validações Automáticas**
- Verificação de conformidade com LC 95/1998
- Validação de linguagem simples para acessibilidade
- Controle de qualidade com pontuação percentual
- Alertas para não conformidades

### 5. **Acessibilidade Digital**
- Suporte a PDF/UA-1 e PDF/UA-2
- Verificação de contraste WCAG 2.1 AA
- Texto alternativo automático para imagens
- Estrutura semântica para leitores de tela

## 📁 Arquivos Criados

### Seeders
- `ParametrosPadroesLegaisSeeder.php` - Configurações dos padrões legais

### Services
- `TemplateEstruturadorService.php` - Estruturação conforme LC 95/1998
- `TemplateMetadadosService.php` - Dublin Core, LexML e Akoma Ntoso
- `TemplateValidadorLegalService.php` - Validações automáticas
- `TemplateNumeracaoService.php` - Sistema de numeração unificada

### Commands
- `TestarPadroesLegaisCommand.php` - Teste completo do sistema

## 🎯 Configurações Disponíveis

### Estrutura Legal
- Formato de epígrafe (3 opções)
- Padrão de ementa
- Preâmbulo personalizado
- Numeração de artigos (ordinal/cardinal)
- Cláusula de vigência (5 tipos)

### Numeração Unificada
- Sistema de numeração (3 tipos)
- Reiniciar por ano legislativo
- Dígitos mínimos (com zeros)
- Data de início do ano fiscal

### Metadados
- Autoridade LexML
- Habilitar Dublin Core
- URL OAI-PMH
- Interoperabilidade

### Acessibilidade
- PDF/UA habilitado
- Alt text automático
- Linguagem simples
- Contraste WCAG

### Assinatura Digital
- Padrão PAdES-B-LTA
- ICP-Brasil ou eIDAS
- Carimbo de tempo
- URL de verificação

## 🔧 Como Usar

### 1. Executar Seeder
```bash
php artisan db:seed --class=ParametrosPadroesLegaisSeeder
```

### 2. Testar Sistema
```bash
php artisan template:testar-padroes --tipo=projeto_lei_ordinaria
```

### 3. Usar nos Services

```php
// Estruturação automática
$estruturador = app(TemplateEstruturadorService::class);
$estrutura = $estruturador->estruturarProposicao($dados, $tipo);

// Validação legal
$validador = app(TemplateValidadorLegalService::class);
$resultado = $validador->validarProposicaoCompleta($dados, $tipo);

// Metadados
$metadados = app(TemplateMetadadosService::class);
$dublinCore = $metadados->gerarDublinCore($proposicao);
$xmlAkoma = $metadados->gerarAkomaNtosoXML($proposicao);

// Numeração
$numeracao = app(TemplateNumeracaoService::class);
$proximoNumero = $numeracao->obterProximoNumero($tipo);
```

## 📊 Exemplo de Saída

```
🏛️ Sistema configurado com padrões internacionais!

✅ LC 95/1998 - Estrutura obrigatória
✅ Numeração unificada por tipo e ano  
✅ Metadados Dublin Core
✅ Identificadores LexML URN
✅ XML Akoma Ntoso (OASIS)
✅ Validações automáticas
✅ Acessibilidade WCAG/PDF-UA
✅ Formatação padronizada

📈 Qualidade: 100% | Status: Aprovado
```

## 🌐 Padrões Atendidos

### Nacionais
- **LC 95/1998** - Elaboração, redação, alteração e consolidação das leis
- **Decreto 9.191/2017** - Normas e diretrizes para elaboração
- **LexML Brasil** - Padrão de identificação de documentos jurídicos
- **ICP-Brasil** - Assinatura digital

### Internacionais  
- **Dublin Core** - Metadados bibliográficos
- **Akoma Ntoso 1.0** - OASIS Legal Document Markup Language
- **WCAG 2.1 AA** - Acessibilidade web
- **PDF/UA** - Universal Accessibility
- **eIDAS** - European digital signature standards

## 🔄 Próximos Passos

1. **Integração com Editor**: Conectar com OnlyOffice
2. **Templates RTF/DOCX**: Gerar templates físicos  
3. **API REST**: Endpoints para validação e metadados
4. **Dashboard**: Interface para configurações
5. **Relatórios**: Estatísticas de conformidade

---

**Conformidade**: ✅ LC 95/1998 | ✅ LexML | ✅ Dublin Core | ✅ Akoma Ntoso | ✅ WCAG 2.1 AA