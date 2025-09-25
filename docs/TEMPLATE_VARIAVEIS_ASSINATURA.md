# Sistema de Variáveis de Template para Assinatura Digital

## 📋 Visão Geral

O sistema de templates agora suporta variáveis especiais que são automaticamente substituídas quando o parlamentar assina digitalmente o documento. Isso permite criar templates universais que se adaptam dinamicamente ao processo de assinatura.

## 🔤 Variáveis Disponíveis

### Variáveis Básicas

| Variável | Descrição | Exemplo de Saída |
|----------|-----------|------------------|
| `[NUMERO]` | Número da proposição | 001 |
| `[ANO]` | Ano da proposição | 2025 |
| `[PROTOCOLO]` | Número do protocolo | 2025/001234 |
| `[DATA_HORA]` | Data e hora do protocolo | 25/09/2025 14:30 |
| `[NOME_COMPLETO]` | Nome completo do assinante | João da Silva |
| `[CODIGO_VALIDACAO]` | Código único de validação | A7CA-9537-1505-BD94 |

### Variáveis de Assinatura

| Variável | Descrição | Uso |
|----------|-----------|-----|
| `[ASSINATURA_PARLAMENTAR]` | Placeholder para assinatura | Substituído após assinatura digital |
| `[QRCODE_VALIDACAO]` | QR Code de validação | Gera QR Code para verificação |
| `[CARIMBO_ASSINATURA]` | Carimbo completo | Inclui todos os elementos de assinatura |
| `[ASSINATURA_DIGITAL]` | Bloco de assinatura | Versão formatada da assinatura |

## 📝 Como Usar no Template

### 1. Template Básico com Assinatura

```rtf
CÂMARA MUNICIPAL DE CARAGUATATUBA
Estado de São Paulo

INDICAÇÃO Nº [NUMERO]/[ANO]

Senhor Presidente,

[TEXTO_PROPOSICAO]

JUSTIFICATIVA:
[JUSTIFICATIVA]

Caraguatatuba, [DATA_EXTENSO]

[ASSINATURA_PARLAMENTAR]
```

### 2. Template com QR Code de Validação

```rtf
DOCUMENTO OFICIAL

Protocolo: [PROTOCOLO]
Data: [DATA_HORA]

[CONTEUDO_PRINCIPAL]

[QRCODE_VALIDACAO]

Para validar este documento, use o código: [CODIGO_VALIDACAO]
```

### 3. Template com Carimbo Completo

```rtf
PROPOSIÇÃO LEGISLATIVA

[CABECALHO]

[TEXTO_PRINCIPAL]

[CARIMBO_ASSINATURA]
```

## 🔄 Fluxo de Substituição

### Antes da Assinatura
```
[ASSINATURA_PARLAMENTAR]
↓
_______________________________________
[ASSINATURA DO PARLAMENTAR]

Nome: João da Silva
Cargo: Vereador(a)
Data: ___/___/_____
```

### Após a Assinatura Digital
```
[ASSINATURA_PARLAMENTAR]
↓
╔════════════════════════════════════════════════════════╗
║         ASSINADO DIGITALMENTE POR                     ║
║                                                        ║
║  João da Silva                                        ║
║  25/09/2025 14:30                                     ║
║                                                        ║
║  Este documento foi assinado digitalmente usando      ║
║  certificado digital ICP-Brasil                       ║
╚════════════════════════════════════════════════════════╝
```

## 🎯 Variável [CARIMBO_ASSINATURA]

Esta variável gera um carimbo completo com todos os elementos:

```
╔═══════════════════════════════════════════════════════════════════════════╗
║                     CARIMBO DE ASSINATURA DIGITAL                        ║
╠═══════════════════════════════════════════════════════════════════════════╣
║                                                                           ║
║  INDICAÇÃO Nº 001/2025                                                  ║
║  Protocolo: 2025/001234                                                 ║
║                                                                           ║
║  ┌────────────────────────────────────────┬─────────────────────┐      ║
║  │ ASSINANTE:                             │    [QR CODE]        │      ║
║  │                                         │                     │      ║
║  │ João da Silva                          │    Escaneie para    │      ║
║  │ Vereador(a)                            │    validar          │      ║
║  │                                         │                     │      ║
║  │ DATA/HORA:                              │                     │      ║
║  │ 25/09/2025 14:30:00 UTC-3              │                     │      ║
║  │                                         └─────────────────────┘      ║
║  │ CÓDIGO DE VALIDAÇÃO:                                                 ║
║  │ A7CA-9537-1505-BD94                                                 ║
║  │                                                                       ║
║  │ Este documento foi assinado digitalmente de acordo com a             ║
║  │ Medida Provisória nº 2.200-2/2001 (ICP-Brasil)                      ║
║  └───────────────────────────────────────────────────────────────────┘   ║
║                                                                           ║
║  Para validar este documento, acesse:                                    ║
║  https://sistema.camaracaragua.sp.gov.br/conferir_assinatura            ║
║  e informe o código de validação acima.                                  ║
║                                                                           ║
╚═══════════════════════════════════════════════════════════════════════════╝
```

## 🔐 Segurança

- **Código de Validação**: Gerado automaticamente no formato A7CA-9537-1505-BD94
- **QR Code**: Contém URL direta para validação online
- **Hash SHA-256**: Garante integridade do documento
- **ICP-Brasil**: Conformidade com legislação brasileira

## 💡 Boas Práticas

### 1. Posicionamento da Assinatura
- Sempre coloque `[ASSINATURA_PARLAMENTAR]` no final do documento
- Use `[QRCODE_VALIDACAO]` próximo à assinatura para facilitar validação

### 2. Validação
- Inclua sempre `[CODIGO_VALIDACAO]` em local visível
- Forneça instruções claras de como validar o documento

### 3. Template Universal
```rtf
[CABECALHO_INSTITUCIONAL]

TIPO: [TIPO_PROPOSICAO]
NÚMERO: [NUMERO]/[ANO]

[TEXTO_PRINCIPAL]

[JUSTIFICATIVA]

Data: [DATA_EXTENSO]
Local: [MUNICIPIO], [UF]

[ASSINATURA_PARLAMENTAR]

Código de Validação: [CODIGO_VALIDACAO]
```

## 🚀 Integração com PAdES

Quando o documento é assinado digitalmente:

1. **Template Variables Service** processa todas as variáveis
2. **PAdES Signature Service** adiciona elementos visuais
3. **Elemento A**: Painel de assinatura lateral (130pt × altura)
4. **Elemento B**: Faixa vertical com texto dinâmico (22pt largura)

### Exemplo de Integração

```php
// No OnlyOffice ao salvar
$rtfContent = $templateVariableService->replaceVariablesInRtf(
    $rtfContent,
    $proposicao,
    ['nome_assinante' => $user->name]
);

// Na assinatura PAdES
$pdfWithSignature = $padesAppearanceService->createSignaturePanel(
    $pdfPath,
    $proposicao,
    $signatureData,
    $verificationUrl
);
```

## 📊 Variáveis no Ciclo de Vida

| Etapa | Variáveis Processadas |
|-------|----------------------|
| Criação | `[NUMERO]`, `[ANO]`, `[TIPO_PROPOSICAO]` |
| Edição | Todas as variáveis de conteúdo |
| Aprovação | `[DATA_HORA]`, status updates |
| Assinatura | `[ASSINATURA_PARLAMENTAR]`, `[QRCODE_VALIDACAO]`, `[CARIMBO_ASSINATURA]` |
| Protocolo | `[PROTOCOLO]`, `[DATA_HORA]` |

## 🔍 Verificação de Assinatura

O sistema gera automaticamente:
- **UUID único** para cada documento assinado
- **URL de verificação** pública
- **Código de validação** em formato padrão
- **QR Code** para acesso rápido

### Endpoint de Verificação
```
https://sistema.camaracaragua.sp.gov.br/conferir_assinatura
```

### Parâmetros
- `codigo`: Código de validação (ex: A7CA-9537-1505-BD94)
- `uuid`: UUID do documento (alternativa)

## 📝 Notas Técnicas

- Variáveis são processadas em RTF e PDF
- Substituição ocorre em tempo real
- Compatível com OnlyOffice e exportação S3
- Integrado com DocumentWorkflowLog para auditoria

---

**Versão**: 1.0.0
**Última Atualização**: 25/09/2025
**Autor**: Sistema LegisInc v2