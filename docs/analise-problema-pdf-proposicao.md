# Análise do Problema de Geração de PDF na Proposição

## 📋 Contexto do Problema

Quando o usuário Legislativo aprova uma proposição e tenta visualizar o PDF, o documento gerado não mantém a formatação do template universal, perdendo:
- Imagem do cabeçalho
- Formatação do template
- Estrutura visual definida no RTF

## 🔄 Fluxo Atual do Sistema

### 1. **Criação da Proposição (Parlamentar)**
```
Usuário: jessica@sistema.gov.br
├── Cria proposição tipo "Projeto de Lei Ordinária"
├── Seleciona método: Texto Personalizado
├── Sistema aplica template universal
├── Edita no OnlyOffice
└── Salva arquivo: proposicoes/proposicao_1_1756994322.rtf
```

### 2. **Envio para Legislativo**
```
Status: enviado_legislativo
├── Legislativo recebe proposição
├── Abre no OnlyOffice ("Revisar no Documento")
├── Template universal carregado corretamente
├── Faz edições necessárias
├── Salva alterações (RTF atualizado)
└── Precisa Ctrl+F5 para ver mudanças (cache)
```

### 3. **Aprovação pelo Legislativo**
```
Status: aprovado
├── Clica em "Aprovar para Proposição"
├── Sistema muda status
├── NÃO gera PDF automaticamente ❌
└── Botão "Visualizar PDF" disponível
```

### 4. **Tentativa de Visualização do PDF**
```
Rota: /proposicoes/1/pdf
└── ProposicaoController::servePDF()
    ├── Busca PDF existente via encontrarPDFMaisRecente()
    │   ├── Verifica arquivo_pdf_path no BD (vazio)
    │   └── Busca em diretórios físicos (não encontra)
    └── Retorna erro 404 ou gera PDF incorreto
```

## 🔍 Análise Técnica Detalhada

### Método `servePDF()` (linha 4850)
```php
public function servePDF(Proposicao $proposicao)
{
    // 1. Busca PDF existente
    $pdfPath = $this->encontrarPDFMaisRecente($proposicao);
    
    if (!$pdfPath) {
        abort(404, 'PDF não encontrado');
    }
}
```

### Método `encontrarPDFMaisRecente()` (linha 6369)
```php
private function encontrarPDFMaisRecente($proposicao)
{
    // 1. Verifica campo arquivo_pdf_path (está vazio)
    if (!empty($proposicao->arquivo_pdf_path)) {
        // ...
    }
    
    // 2. Busca PDFs em diretórios
    $diretoriosParaBuscar = [
        storage_path("app/proposicoes/pdfs/{$proposicao->id}/"),
        storage_path("app/private/proposicoes/pdfs/{$proposicao->id}/"),
        storage_path("app/public/proposicoes/pdfs/{$proposicao->id}/")
    ];
    
    // Não encontra nenhum PDF
    return null;
}
```

### Fallback: `criarPDFComDomPDF()` (linha 4917)
```php
private function criarPDFComDomPDF($caminhoPdfAbsoluto, $proposicao)
{
    // TENTATIVA 1: Conversão direta RTF → PDF
    if ($this->libreOfficeDisponivel()) {
        if ($this->converterArquivoParaPDFDireto($rtfPath, $pdfPath)) {
            return; // Sucesso
        }
    }
    
    // TENTATIVA 2: DomPDF (perde formatação)
    $html = $this->gerarHTMLParaPDF($proposicao, $conteudo);
    $pdf = Pdf::loadHTML($html);
    file_put_contents($caminhoPdfAbsoluto, $pdf->output());
}
```

## ❌ Problemas Identificados

### 1. **PDF Nunca é Gerado na Aprovação**
- Quando Legislativo aprova, apenas muda status
- Não há trigger para gerar PDF com conteúdo editado
- Campo `arquivo_pdf_path` permanece vazio

### 2. **LibreOffice Não Está Instalado**
```bash
docker exec legisinc-app which libreoffice
# Retorna erro - não encontrado
```

### 3. **DomPDF Não Processa RTF**
- DomPDF só aceita HTML
- Conversão RTF → HTML perde:
  - Imagens incorporadas
  - Formatação complexa
  - Estrutura do template

### 4. **Template Universal Não é Preservado**
```
RTF Original (OnlyOffice)
├── Imagem cabeçalho ✓
├── Formatação rica ✓
└── Variáveis substituídas ✓

PDF Gerado (DomPDF)
├── Imagem cabeçalho ✗
├── Texto simples ✗
└── HTML básico ✗
```

## 🎯 Impacto no Usuário

1. **Parlamentar**: Cria documento com template correto
2. **Legislativo**: Edita mantendo formatação
3. **Visualização PDF**: Perde toda formatação e imagem
4. **Documento Final**: Não representa o trabalho realizado

## 🔧 Soluções Possíveis

### Solução 1: Instalar LibreOffice (Recomendada)
```dockerfile
RUN apk add --no-cache libreoffice
```
- Conversão direta RTF → PDF
- Preserva 100% da formatação
- Igual ao "Salvar como PDF" do OnlyOffice

### Solução 2: Integração OnlyOffice API
```php
// Solicitar PDF diretamente do OnlyOffice
$onlyofficeService->exportToPDF($proposicao->arquivo_path);
```

### Solução 3: Gerar PDF na Aprovação
```php
public function aprovar($proposicao)
{
    // ... aprovação ...
    $this->gerarPDFDefinitivo($proposicao);
    $proposicao->update(['arquivo_pdf_path' => $pdfPath]);
}
```

## 📊 Estado Atual do Banco de Dados

```sql
Proposicao ID: 1
├── arquivo_path: proposicoes/proposicao_1_1756994322.rtf ✓
├── arquivo_pdf_path: NULL ✗
├── pdf_path: NULL ✗
├── pdf_assinado_path: NULL ✗
└── status: aprovado
```

## 🚨 Conclusão

O sistema está gerando PDF usando DomPDF como fallback porque:
1. LibreOffice não está instalado para conversão direta
2. PDF não é gerado no momento da aprovação
3. DomPDF não consegue processar o RTF complexo do OnlyOffice

**Resultado**: PDF sem formatação e sem imagem do template universal.

## 📝 Recomendações Imediatas

1. **Instalar LibreOffice no container**
2. **Gerar PDF automaticamente na aprovação**
3. **Salvar caminho do PDF no banco de dados**
4. **Implementar cache de PDFs gerados**

---

*Documento gerado em: 04/09/2025*
*Análise baseada no fluxo real do sistema em produção*