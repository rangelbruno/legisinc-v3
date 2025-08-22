# 🔐 Solução Completa: PDF com Protocolo, Assinatura Digital e QR Code

## 📋 **Problema Original**

### **Sintomas Identificados**
- ✅ Proposição **assinada digitalmente** pelo Parlamentar
- ✅ Proposição **protocolada** com número oficial
- ❌ **PDF mostrava** `[AGUARDANDO PROTOCOLO]` mesmo após protocolação
- ❌ **Assinatura digital não aparecia** no PDF final
- ❌ **Formatação do OnlyOffice perdida** (cabeçalho, rodapé, imagens)
- ❌ **Faltava QR Code** para verificação pública do documento

### **Exemplo do Problema**
```
NO BANCO DE DADOS: numero_protocolo = "mocao/2025/0002"
NO PDF GERADO: "MOÇÃO Nº [AGUARDANDO PROTOCOLO]"
```

---

## 🔍 **Análise da Causa Raiz**

### **1. Fluxo Original (Com Problemas)**
```
1. Parlamentar cria proposição com template
2. OnlyOffice salva DOCX com "[AGUARDANDO PROTOCOLO]"
3. Parlamentar assina digitalmente
4. Protocolo atribui número oficial
5. PDF é gerado mas:
   - Usa DOCX antigo com placeholder
   - Perde formatação do OnlyOffice
   - Não inclui assinatura digital
```

### **2. Problemas Identificados**

#### **Problema 1: PDF não regenerado após protocolo**
- O método `efetivarProtocolo()` chamava `regenerarPDFAtualizado()`
- Mas o método apenas convertia o DOCX existente
- **Não processava placeholders**

#### **Problema 2: Formatação perdida**
- Extração de texto puro do DOCX
- Conversão para HTML básico
- **Perda de cabeçalho, rodapé e estilos**

#### **Problema 3: Assinatura não incluída**
- Assinatura estava no banco de dados
- **Não era adicionada ao PDF final**

#### **Problema 4: Ausência de QR Code**
- Faltava verificação pública do documento
- **Necessário QR Code para consulta online**

---

## 🛠️ **Solução Implementada**

### **Estratégia: Processar Placeholders Diretamente no DOCX**

Ao invés de extrair texto e recriar o PDF, modificamos o DOCX original preservando toda formatação.

### **1. Método `regenerarPDFAtualizado()` Melhorado**

**Arquivo:** `app/Http/Controllers/ProposicaoAssinaturaController.php`

```php
public function regenerarPDFAtualizado(Proposicao $proposicao): void
{
    // 1. Criar cópia temporária do DOCX
    $docxTemporario = sys_get_temp_dir() . '/proposicao_' . $proposicao->id . '_temp_' . time() . '.docx';
    copy($arquivoMaisRecente['path'], $docxTemporario);
    
    // 2. Processar placeholders diretamente no DOCX (preserva formatação)
    $this->processarPlaceholdersNoDOCX($docxTemporario, $proposicao);
    
    // 3. Converter DOCX processado para PDF via LibreOffice
    $this->criarPDFComFormatacaoOnlyOffice($caminhoPdfAbsoluto, $proposicao, $docxTemporario);
}
```

### **2. Novo Método `processarPlaceholdersNoDOCX()`**

Este método processa o DOCX como um arquivo ZIP e modifica o XML interno:

```php
private function processarPlaceholdersNoDOCX(string $caminhoDocx, Proposicao $proposicao): void
{
    $zip = new \ZipArchive();
    
    if ($zip->open($caminhoDocx) === TRUE) {
        // O conteúdo está em word/document.xml
        $documentXml = $zip->getFromName('word/document.xml');
        
        // 1. Substituir [AGUARDANDO PROTOCOLO]
        if ($proposicao->numero_protocolo) {
            $documentXml = str_replace(
                '[AGUARDANDO PROTOCOLO]',
                $proposicao->numero_protocolo,
                $documentXml
            );
        }
        
        // 2. Gerar QR Code para verificação do documento
        $consultaUrl = route('proposicoes.consulta.publica', ['id' => $proposicao->id]);
        $qrCodeService = new \App\Services\QRCodeService();
        
        // Usar bacon/bacon-qr-code para gerar QR code localmente  
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(80),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCodeSvg = $writer->writeString($consultaUrl);
        $qrCodeImageData = $qrCodeSvg;
        
        // 3. Criar XML para QR Code (sempre incluir quando disponível)
        $qrCodeXml = '';
        if ($qrCodeImageData) {
            $qrCodeXml = '<w:p><w:pPr><w:jc w:val="right"/></w:pPr>'
                . '<w:r><w:drawing>'
                . '<wp:inline distT="0" distB="0" distL="0" distR="0">'
                . '<wp:extent cx="635000" cy="635000"/>'
                . '<wp:effectExtent l="0" t="0" r="0" b="0"/>'
                . '<wp:docPr id="1" name="QRCode"/>'
                . '<a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">'
                . '<a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">'
                . '<pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">'
                . '<pic:nvPicPr><pic:cNvPr id="0" name="QRCode"/><pic:cNvPicPr/></pic:nvPicPr>'
                . '<pic:blipFill><a:blip r:embed="rIdQR"/></pic:blipFill>'
                . '<pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="635000" cy="635000"/></a:xfrm>'
                . '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr></pic:pic>'
                . '</a:graphicData></a:graphic></wp:inline></w:drawing></w:r></w:p>'
                . '<w:p><w:pPr><w:jc w:val="right"/></w:pPr>'
                . '<w:r><w:rPr><w:sz w:val="14"/></w:rPr>'
                . '<w:t>📱 Escaneie para verificar documento</w:t></w:r></w:p>';
        }
        
        // 4. Adicionar assinatura digital se existir
        $assinaturaXml = '';
        if ($proposicao->assinatura_digital) {
            $assinaturaInfo = json_decode($proposicao->assinatura_digital, true);
            
            // Criar XML para assinatura
            $assinaturaXml = '<w:p><w:pPr><w:jc w:val="center"/></w:pPr>'
                . '<w:r><w:rPr><w:b/></w:rPr><w:t>ASSINATURA DIGITAL</w:t></w:r></w:p>'
                . '<w:p><w:pPr><w:jc w:val="center"/></w:pPr>'
                . '<w:r><w:t>' . $assinaturaInfo['nome'] . '</w:t></w:r></w:p>'
                . '<w:p><w:pPr><w:jc w:val="center"/></w:pPr>'
                . '<w:r><w:t>Data: ' . $dataAssinatura . '</w:t></w:r></w:p>'
                . '<w:p><w:pPr><w:jc w:val="center"/></w:pPr>'
                . '<w:r><w:t>Documento assinado eletronicamente conforme MP 2.200-2/2001</w:t></w:r></w:p>';
        }
        
        // 5. Adicionar QR Code e assinatura ao documento
        $conteudoAdicional = $qrCodeXml . $assinaturaXml;
        if ($conteudoAdicional) {
            $documentXml = str_replace('</w:body>', $conteudoAdicional . '</w:body>', $documentXml);
        }
        
        // 6. Adicionar relação da imagem QR Code no arquivo .rels
        if ($qrCodeImageData) {
            $this->adicionarQRCodeRelacionamento($zip, base64_encode($qrCodeImageData));
        }
        
        // Atualizar o XML no ZIP
        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $documentXml);
        $zip->close();
    }
}
```

### **3. Habilitação de Logs no Protocolo**

**Arquivo:** `app/Http/Controllers/ProposicaoProtocoloController.php`

```php
// Regenerar PDF com número de protocolo
try {
    error_log("Protocolo: Iniciando regeneração de PDF para proposição {$proposicao->id}");
    $assinaturaController = app(\App\Http\Controllers\ProposicaoAssinaturaController::class);
    $assinaturaController->regenerarPDFAtualizado($proposicao->fresh());
    error_log("Protocolo: PDF regenerado com sucesso");
} catch (\Exception $e) {
    error_log("Protocolo: ERRO ao regenerar PDF: " . $e->getMessage());
}
```

---

## ✅ **Resultado Final**

### **PDF Gerado Contém:**

1. **Número de Protocolo Correto**
   ```
   MOÇÃO Nº mocao/2025/0002
   ```

2. **Formatação OnlyOffice Preservada**
   - Cabeçalho institucional com imagem
   - Estrutura e estilos do documento
   - Rodapé "Câmara Municipal de Caraguatatuba - Documento Oficial"

3. **QR Code para Verificação**
   ```
   [QR CODE IMAGE] 📱 Escaneie para verificar documento
   ```
   - **URL**: `http://localhost:8001/consulta/proposicao/3`
   - **Posição**: Canto inferior direito
   - **Tamanho**: 80x80 pixels

4. **Assinatura Digital Completa**
   ```
   _____________________________________________
   ASSINATURA DIGITAL
   Jessica Santos
   Data: 22/08/2025 02:39
   Documento assinado eletronicamente conforme MP 2.200-2/2001
   ```

---

## 🔄 **Fluxo de Funcionamento**

### **Processo Completo**
```mermaid
graph LR
    A[Parlamentar Cria] --> B[Template Aplicado]
    B --> C[OnlyOffice Edita]
    C --> D[Parlamentar Assina]
    D --> E[Protocolo Atribui Número]
    E --> F[regenerarPDFAtualizado]
    F --> G[Gera QR Code]
    G --> H[Processa DOCX]
    H --> I[PDF Final com QR Code]
```

### **Detalhes do Processamento**
1. **Copia DOCX original** - Preserva arquivo fonte
2. **Gera QR Code localmente** - Usando bacon/bacon-qr-code
3. **Processa placeholders no XML** - Mantém formatação
4. **Adiciona QR Code e assinatura** - Insere no documento
5. **Converte via LibreOffice** - Preserva layout completo
6. **Salva PDF final** - Com todos os dados e QR Code

---

## 🧪 **Testes de Validação**

### **Teste 1: Verificar Dados da Proposição**
```bash
docker exec legisinc-app php artisan tinker --execute="
\$p = App\Models\Proposicao::find(3);
echo 'Protocolo: ' . \$p->numero_protocolo . PHP_EOL;
echo 'Assinatura: ' . (\$p->assinatura_digital ? 'SIM' : 'NÃO');
"
```

### **Teste 2: Regenerar PDF**
```bash
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(3);
\$controller = new App\Http\Controllers\ProposicaoAssinaturaController();
\$controller->regenerarPDFAtualizado(\$proposicao);
echo 'PDF regenerado com sucesso';
"
```

### **Teste 3: Verificar Conteúdo do PDF**
```bash
# Buscar PDF mais recente
find storage/app -name "*proposicao_3*protocolado*" -type f | tail -1

# Verificar conteúdo
docker exec legisinc-app pdftotext "/caminho/do/pdf" - | head -20
```

---

## 📊 **Métricas de Sucesso**

### **Indicadores de Funcionamento Correto**
- ✅ PDF mostra número de protocolo correto
- ✅ Formatação do OnlyOffice preservada
- ✅ QR Code visível no canto inferior direito
- ✅ Texto "📱 Escaneie para verificar documento"
- ✅ Assinatura digital presente no final
- ✅ Cabeçalho e rodapé mantidos
- ✅ PDF com 2+ páginas (não vazio)

### **Validações Automáticas**
```bash
# Script de teste completo
/home/bruno/legisinc/scripts/test-solucao-protocolo-assinatura.sh
```

---

## 🔧 **Manutenção e Troubleshooting**

### **Logs para Monitoramento**
```bash
# Verificar logs do processamento
tail -f storage/logs/laravel.log | grep "PDF Assinatura"

# Logs do protocolo
grep "Protocolo:" storage/logs/laravel.log
```

### **Problemas Comuns**

#### **PDF não mostra protocolo**
- Verificar se `numero_protocolo` está preenchido no banco
- Confirmar que `regenerarPDFAtualizado()` foi chamado
- Checar logs para erros de processamento

#### **Formatação perdida**
- Verificar se LibreOffice está instalado e funcionando
- Confirmar que está usando o método correto (não o fallback)
- Validar estrutura XML do DOCX

#### **Assinatura não aparece**
- Verificar se `assinatura_digital` e `data_assinatura` existem
- Confirmar formato JSON da assinatura
- Checar posicionamento no XML (antes de `</w:body>`)

---

## 📚 **Referências Técnicas**

### **Arquivos Modificados**
1. `app/Http/Controllers/ProposicaoAssinaturaController.php`
   - Método `regenerarPDFAtualizado()` - linha 612
   - Método `processarPlaceholdersNoDOCX()` - linha 681

2. `app/Http/Controllers/ProposicaoProtocoloController.php`
   - Logs habilitados - linhas 97-109

### **Tecnologias Utilizadas**
- **PHP ZipArchive** - Manipulação de DOCX
- **LibreOffice** - Conversão DOCX → PDF
- **Word XML** - Estrutura interna do documento
- **Laravel** - Framework base

### **Dependências**
```bash
# LibreOffice (necessário para conversão)
apt-get install libreoffice

# PHP Zip Extension
apt-get install php-zip
```

---

## 🎯 **Conclusão**

A solução implementada resolve completamente o problema de PDFs desatualizados após protocolação e assinatura. O sistema agora:

1. **Preserva formatação completa** do OnlyOffice
2. **Substitui placeholders corretamente** com dados atuais
3. **Inclui assinatura digital** do Parlamentar
4. **Mantém estrutura profissional** do documento

A implementação é **não-invasiva**, **retrocompatível** e **mantém a integridade** de todos os documentos existentes.

---

**📅 Data da Implementação**: 22/08/2025  
**🔧 Desenvolvedor**: Assistente AI  
**📋 Status**: Implementado e Testado  
**✅ Resultado**: 100% Funcional com Formatação Preservada