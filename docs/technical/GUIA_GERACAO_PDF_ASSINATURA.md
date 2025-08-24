# 📋 Guia: Geração de PDF para Assinatura Digital

## 🎯 **Objetivo**
Este documento serve como referência para diagnosticar e corrigir problemas na geração de PDF para assinatura digital quando o documento não reflete as edições feitas pelo Legislativo no OnlyOffice.

---

## 🔄 **Fluxo Correto do Sistema**

### **1. Criação Inicial (Parlamentar)**
```
Parlamentar → Cria proposição → Seleciona tipo → Template aplicado → Edita no OnlyOffice → Envia para Legislativo
```

### **2. Edição pelo Legislativo**
```
Legislativo → Recebe proposição → Edita no OnlyOffice → Salva alterações → Retorna para Parlamentar
```

### **3. Geração de PDF para Assinatura** ⭐
```
Sistema → Busca arquivo DOCX editado → Converte para PDF → Apresenta para assinatura
```

---

## 🔍 **Como Diagnosticar Problemas**

### **Passo 1: Verificar Status da Proposição**
```sql
-- Executar no banco de dados
SELECT id, tipo, status, arquivo_path, LENGTH(conteudo) as conteudo_length 
FROM proposicoes 
WHERE id = [ID_DA_PROPOSICAO];
```

**Status esperados para PDF de assinatura:**
- `retornado_legislativo` ✅
- `aprovado_assinatura` ✅

### **Passo 2: Verificar Existência do Arquivo DOCX**
```bash
# Dentro do container Docker
docker exec legisinc-app find storage/app -name "*proposicao_[ID]*" -type f
```

**Locais onde o arquivo deve estar:**
- `storage/app/private/proposicoes/proposicao_[ID]_[timestamp].docx` ✅
- `storage/app/proposicoes/proposicao_[ID]_[timestamp].docx` ✅

### **Passo 3: Testar Conversão LibreOffice**
```bash
# Criar script de teste
cat > test_pdf_conversion.php << 'EOF'
<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$proposicao = \App\Models\Proposicao::find([ID_DA_PROPOSICAO]);
$arquivoPath = $proposicao->arquivo_path;

$locaisParaBuscar = [
    storage_path('app/' . $arquivoPath),
    storage_path('app/private/' . $arquivoPath),
    storage_path('app/public/' . $arquivoPath),
];

$arquivoEncontrado = null;
foreach ($locaisParaBuscar as $caminho) {
    if (file_exists($caminho)) {
        $arquivoEncontrado = $caminho;
        echo "Arquivo encontrado: $caminho\n";
        echo "Tamanho: " . filesize($caminho) . " bytes\n";
        break;
    }
}

if (!$arquivoEncontrado) {
    echo "ERRO: Arquivo não encontrado!\n";
    exit;
}

// Teste de conversão
exec('which libreoffice', $output, $returnCode);
if ($returnCode === 0) {
    $tempDir = sys_get_temp_dir();
    $tempFile = $tempDir . '/test_conversion.docx';
    $outputPdf = $tempDir . '/test_conversion.pdf';
    
    copy($arquivoEncontrado, $tempFile);
    
    $comando = sprintf(
        'libreoffice --headless --convert-to pdf --outdir %s %s',
        escapeshellarg($tempDir),
        escapeshellarg($tempFile)
    );
    
    exec($comando, $output, $returnCode);
    
    if (file_exists($outputPdf)) {
        echo "✅ Conversão bem-sucedida! PDF: " . filesize($outputPdf) . " bytes\n";
        unlink($tempFile);
        unlink($outputPdf);
    } else {
        echo "❌ Falha na conversão. Return code: $returnCode\n";
    }
} else {
    echo "❌ LibreOffice não disponível\n";
}
EOF

docker exec legisinc-app php test_pdf_conversion.php
```

---

## 🛠️ **Código de Geração de PDF**

### **Controller Principal: ProposicaoAssinaturaController**

**Método de entrada:**
```php
public function assinar(Proposicao $proposicao)
{
    // Verifica status válido
    if (!in_array($proposicao->status, ['aprovado_assinatura', 'retornado_legislativo'])) {
        abort(403, 'Proposição não está disponível para assinatura.');
    }

    // SEMPRE regenerar PDF para garantir dados corretos
    $this->gerarPDFParaAssinatura($proposicao);
    
    return view('proposicoes.assinatura.assinar', compact('proposicao'));
}
```

**Método principal de geração:**
```php
private function gerarPDFParaAssinatura(Proposicao $proposicao): void
{
    $nomePdf = 'proposicao_' . $proposicao->id . '.pdf';
    $diretorioPdf = 'proposicoes/pdfs/' . $proposicao->id;
    $caminhoPdfRelativo = $diretorioPdf . '/' . $nomePdf;
    $caminhoPdfAbsoluto = storage_path('app/' . $caminhoPdfRelativo);

    // Garantir que o diretório existe
    if (!is_dir(dirname($caminhoPdfAbsoluto))) {
        mkdir(dirname($caminhoPdfAbsoluto), 0755, true);
    }

    // PRIORIZAR arquivo editado pelo Legislativo
    $this->criarPDFDoArquivoEditado($caminhoPdfAbsoluto, $proposicao);
    
    // Atualizar proposição com caminho do PDF
    $proposicao->arquivo_pdf_path = $caminhoPdfRelativo;
    $proposicao->save();
}
```

**Lógica de conversão:**
```php
private function criarPDFDoArquivoEditado(string $caminhoPdfAbsoluto, Proposicao $proposicao): void
{
    // PRIORIDADE 1: Arquivo DOCX editado pelo Legislativo
    if ($proposicao->arquivo_path && 
        in_array($proposicao->status, ['aprovado_assinatura', 'retornado_legislativo'])) {
        
        $locaisParaBuscar = [
            storage_path('app/' . $proposicao->arquivo_path),
            storage_path('app/private/' . $proposicao->arquivo_path),
            storage_path('app/public/' . $proposicao->arquivo_path),
            '/var/www/html/storage/app/' . $proposicao->arquivo_path,
            '/var/www/html/storage/app/private/' . $proposicao->arquivo_path,
        ];
        
        $arquivoEncontrado = null;
        foreach ($locaisParaBuscar as $caminho) {
            if (file_exists($caminho)) {
                $arquivoEncontrado = $caminho;
                break;
            }
        }
        
        // Conversão direta DOCX → PDF com LibreOffice
        if ($arquivoEncontrado && str_contains($proposicao->arquivo_path, '.docx')) {
            $tempFile = sys_get_temp_dir() . '/proposicao_' . $proposicao->id . '_temp.docx';
            copy($arquivoEncontrado, $tempFile);
            
            $comando = sprintf(
                'libreoffice --headless --convert-to pdf --outdir %s %s',
                escapeshellarg(dirname($caminhoPdfAbsoluto)),
                escapeshellarg($tempFile)
            );
            
            exec($comando, $output, $returnCode);
            
            $expectedPdfPath = dirname($caminhoPdfAbsoluto) . '/' . 
                              pathinfo($tempFile, PATHINFO_FILENAME) . '.pdf';
            
            if ($returnCode === 0 && file_exists($expectedPdfPath)) {
                rename($expectedPdfPath, $caminhoPdfAbsoluto);
                unlink($tempFile);
                return; // ✅ SUCESSO!
            }
        }
    }
    
    // FALLBACK: Método alternativo se conversão direta falhar
    $this->criarPDFFallback($caminhoPdfAbsoluto, $proposicao);
}
```

---

## 🚨 **Problemas Comuns e Soluções**

### **Problema 1: PDF mostra template genérico**
**Causa:** Sistema não encontra arquivo DOCX editado  
**Solução:**
```bash
# Verificar se arquivo existe
docker exec legisinc-app find storage/app -name "*proposicao_[ID]*" -type f

# Se não existe, verificar logs do OnlyOffice callback
docker exec legisinc-app tail -50 storage/logs/laravel.log | grep "proposicao_[ID]"
```

### **Problema 2: LibreOffice não converte**
**Causa:** LibreOffice não instalado ou com problemas  
**Solução:**
```bash
# Verificar instalação
docker exec legisinc-app which libreoffice

# Testar conversão manual
docker exec legisinc-app libreoffice --headless --convert-to pdf --outdir /tmp /caminho/para/arquivo.docx
```

### **Problema 3: Arquivo corrompido**
**Causa:** DOCX corrompido ou incompleto  
**Solução:**
```bash
# Verificar integridade do arquivo
docker exec legisinc-app file storage/app/private/proposicoes/arquivo.docx

# Tentar extrair conteúdo
docker exec legisinc-app unzip -l storage/app/private/proposicoes/arquivo.docx
```

### **Problema 4: Permissões incorretas**
**Causa:** Sistema sem permissão para criar PDF  
**Solução:**
```bash
# Verificar permissões
docker exec legisinc-app ls -la storage/app/proposicoes/pdfs/

# Corrigir permissões
docker exec legisinc-app chmod -R 755 storage/app/proposicoes/
docker exec legisinc-app chown -R www-data:www-data storage/app/proposicoes/
```

---

## 🔧 **Comandos de Diagnóstico Rápido**

### **Verificação Completa**
```bash
# 1. Status da proposição
docker exec legisinc-postgres psql -U postgres -d legisinc -c "
SELECT id, tipo, status, arquivo_path, arquivo_pdf_path 
FROM proposicoes WHERE id = [ID];"

# 2. Arquivos existentes
docker exec legisinc-app find storage/app -name "*proposicao_[ID]*" -type f

# 3. LibreOffice disponível
docker exec legisinc-app which libreoffice

# 4. Logs recentes
docker exec legisinc-app tail -20 storage/logs/laravel.log | grep -E "(PDF|proposicao_[ID])"

# 5. Testar geração manual
docker exec legisinc-app php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$proposicao = \App\Models\Proposicao::find([ID]);
\$controller = new \App\Http\Controllers\ProposicaoAssinaturaController();
\$reflection = new ReflectionClass(\$controller);
\$method = \$reflection->getMethod('gerarPDFParaAssinatura');
\$method->setAccessible(true);
\$method->invoke(\$controller, \$proposicao);
echo \"PDF gerado: {\$proposicao->arquivo_pdf_path}\n\";
"
```

---

## 📁 **Estrutura de Arquivos**

```
storage/app/
├── private/
│   └── proposicoes/
│       └── proposicao_[ID]_[timestamp].docx  ← Arquivo editado pelo Legislativo
├── proposicoes/
│   └── pdfs/
│       └── [ID]/
│           └── proposicao_[ID].pdf           ← PDF para assinatura
└── public/
    └── proposicoes/
        └── [arquivos antigos ou temporários]
```

---

## ✅ **Checklist de Verificação**

- [ ] Status da proposição é `retornado_legislativo` ou `aprovado_assinatura`
- [ ] Arquivo DOCX existe em `storage/app/private/proposicoes/`
- [ ] LibreOffice está instalado e funcionando
- [ ] Diretório `storage/app/proposicoes/pdfs/[ID]/` existe e tem permissões
- [ ] Logs não mostram erros de conversão
- [ ] PDF gerado tem tamanho > 0 bytes
- [ ] PDF contém conteúdo editado pelo Legislativo (não template genérico)

---

## 📞 **Em Caso de Problemas Persistentes**

1. **Verificar todos os itens do checklist acima**
2. **Executar comandos de diagnóstico rápido**
3. **Verificar logs para erros específicos**
4. **Testar conversão manual com LibreOffice**
5. **Se necessário, regenerar arquivo DOCX no OnlyOffice**

---

**📅 Última atualização:** 16/08/2025  
**🔧 Versão do sistema:** v1.3 (Performance Otimizada)  
**✅ Status:** Funcionando corretamente