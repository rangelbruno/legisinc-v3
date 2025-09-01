# 🚨 Solução: Problema de Codificação no Template Universal OnlyOffice

## 📋 Descrição do Problema
Ao acessar `/admin/templates/universal/editor/1`, o OnlyOffice apresentava um diálogo solicitando escolha de codificação (TXT options - Unicode UTF-8) em vez de abrir diretamente o editor. Isso ocorria porque o sistema estava enviando o arquivo como texto simples (TXT) em vez de RTF formatado.

### Sintomas
- Diálogo "Choose TXT options" aparecia ao abrir o editor
- Menu suspenso com "Encoding: Unicode (UTF-8)"
- Visualização mostrando texto sem formatação
- Editor não reconhecia o documento como RTF

## 🔍 Diagnóstico

### 1. Causa Raiz Identificada
```php
// ❌ PROBLEMA: Controller configurado para TXT
'fileType' => 'txt',  // linha 123 do TemplateUniversalController
$formato = 'txt';      // linha 267
$contentType = 'text/plain; charset=utf-8';  // linha 268
```

### 2. Fluxo Problemático
```
Template RTF → Controller (TXT) → OnlyOffice → Diálogo de Codificação
                    ↑
            [CONVERSÃO INCORRETA]
```

### 3. Impacto
- OnlyOffice interpretava RTF como texto simples
- Caracteres acentuados podiam aparecer corrompidos
- Formatação RTF era perdida
- Experiência do usuário prejudicada

## 🔧 Solução Implementada

### Arquivo Modificado
```
/home/bruno/legisinc/app/Http/Controllers/Admin/TemplateUniversalController.php
```

### 1. Correção do Formato de Arquivo
```php
// ✅ CORREÇÃO: Configurar como RTF
return [
    'document' => [
        'fileType' => 'rtf',  // Mudado de 'txt' para 'rtf'
        'key' => $documentKey,
        'title' => $template->nome,
        'url' => $documentUrl,
        // ...
    ]
];
```

### 2. Método Download Corrigido
```php
// ✅ CORREÇÃO: Retornar RTF com codificação UTF-8
public function download(TemplateUniversal $template)
{
    // ... validações ...
    
    // Garantir que o conteúdo seja RTF válido
    $conteudoArquivo = $this->garantirRTFValido($conteudoArquivo);
    
    // Aplicar codificação UTF-8 correta para caracteres especiais
    $conteudoArquivo = $this->converterUtf8ParaRtf($conteudoArquivo);
    
    $formato = 'rtf';  // Mudado de 'txt' para 'rtf'
    $contentType = 'application/rtf; charset=utf-8';  // Content-Type correto
    $nomeArquivo = \Illuminate\Support\Str::slug($template->nome) . '.rtf';
    
    return response($conteudoArquivo, 200, [
        'Content-Type' => $contentType,
        'Content-Disposition' => 'inline; filename="' . $nomeArquivo . '"',
        // ...
    ]);
}
```

### 3. Métodos de Codificação UTF-8 Adicionados
```php
/**
 * Converter UTF-8 para RTF com codificação Unicode correta
 */
private function converterUtf8ParaRtf($texto)
{
    $textoLimpo = $this->limparTextoCorrupto($texto);
    $resultado = '';
    $length = mb_strlen($textoLimpo, 'UTF-8');

    for ($i = 0; $i < $length; $i++) {
        $char = mb_substr($textoLimpo, $i, 1, 'UTF-8');
        $codepoint = mb_ord($char, 'UTF-8');

        if ($codepoint > 127) {
            // Converter para escape Unicode RTF
            $resultado .= '\u'.$codepoint.'*';
        } else {
            $resultado .= $char;
        }
    }

    return $resultado;
}

/**
 * Limpar texto corrupto e normalizar para UTF-8
 */
private function limparTextoCorrupto($texto)
{
    // Dicionário de correções para sequências corrompidas
    $correcoes = [
        'SÃ£o' => 'São',
        'JosÃ©' => 'José',
        'EndereÃ§o' => 'Endereço',
        'CÃ¢mara' => 'Câmara',
        // ... mais correções ...
    ];

    $textoCorrigido = str_replace(array_keys($correcoes), array_values($correcoes), $texto);
    
    // Detectar e corrigir double-encoding UTF-8
    if (mb_check_encoding($textoCorrigido, 'UTF-8')) {
        $tentativaDecodificada = mb_convert_encoding(
            mb_convert_encoding($textoCorrigido, 'ISO-8859-1', 'UTF-8'),
            'UTF-8',
            'ISO-8859-1'
        );

        if (substr_count($tentativaDecodificada, 'Ã') < substr_count($textoCorrigido, 'Ã')) {
            $textoCorrigido = $tentativaDecodificada;
        }
    }

    // Garantir UTF-8 válido
    if (!mb_check_encoding($textoCorrigido, 'UTF-8')) {
        $textoCorrigido = mb_convert_encoding($textoCorrigido, 'UTF-8', 'auto');
    }

    return $textoCorrigido;
}
```

## 📊 Tabela de Conversão Unicode RTF

| Caractere | Unicode | RTF Sequence | Status |
|-----------|---------|--------------|--------|
| á | U+00E1 | `\u225*` | ✅ Funcionando |
| é | U+00E9 | `\u233*` | ✅ Funcionando |
| í | U+00ED | `\u237*` | ✅ Funcionando |
| ó | U+00F3 | `\u243*` | ✅ Funcionando |
| ú | U+00FA | `\u250*` | ✅ Funcionando |
| ã | U+00E3 | `\u227*` | ✅ Funcionando |
| õ | U+00F5 | `\u245*` | ✅ Funcionando |
| ç | U+00E7 | `\u231*` | ✅ Funcionando |
| â | U+00E2 | `\u226*` | ✅ Funcionando |
| ê | U+00EA | `\u234*` | ✅ Funcionando |
| ô | U+00F4 | `\u244*` | ✅ Funcionando |

## 🧪 Como Testar

### 1. Script de Teste Automatizado
```bash
# Executar teste de validação RTF
docker exec legisinc-app php test_template_universal_rtf.php
```

Resultado esperado:
```
✅ Template encontrado: Template Universal de Proposições
📄 Análise do conteúdo:
   Começa com {\rtf: SIM ✅
   Contém \ansicpg65001: SIM ✅
🔤 Análise de acentuação:
   ✅ Encontrado '\u227*' (conversão de 'ã')
   ✅ Encontrado '\u231*' (conversão de 'ç')
```

### 2. Teste Manual no Sistema
1. Limpar cache: `docker exec legisinc-app php artisan cache:clear`
2. Acessar: http://localhost:8001/admin/templates/universal/editor/1
3. Verificar que o editor abre diretamente sem diálogo de codificação
4. Confirmar que caracteres acentuados aparecem corretamente
5. Editar texto com acentos e salvar
6. Reabrir para verificar persistência

### 3. Verificação via Logs
```bash
# Monitorar logs durante acesso ao editor
tail -f storage/logs/laravel.log | grep -i "template universal"
```

## 🔄 Processo de Debug (Para Futuros Problemas)

### 1. Verificar Configuração do OnlyOffice
```php
// No TemplateUniversalController, verificar:
'fileType' => 'rtf',  // DEVE ser 'rtf', não 'txt'
'documentType' => 'word',  // Tipo de documento
```

### 2. Verificar Headers HTTP
```bash
# Usar curl para verificar headers
curl -I "http://localhost:8001/api/templates/universal/1/download"
```
Deve retornar:
```
Content-Type: application/rtf; charset=utf-8
```

### 3. Validar Conteúdo RTF
```php
// Verificar se o conteúdo começa com RTF header
$conteudo = $response->getContent();
$ehRTF = strpos($conteudo, '{\rtf1') === 0;  // Deve ser true
$temUTF8 = strpos($conteudo, '\ansicpg65001') !== false;  // Deve ser true
```

### 4. Testar Conversão de Caracteres
```php
// Teste rápido de conversão
$texto = "São Paulo - José da Câmara";
$controller = new TemplateUniversalController(...);
$convertido = $controller->converterUtf8ParaRtf($texto);
echo $convertido;
// Deve mostrar: S\u227*o Paulo - Jos\u233* da C\u226*mara
```

## ⚠️ Pontos de Atenção

### 1. Cache do OnlyOffice
- O OnlyOffice mantém cache baseado no `document_key`
- Se alterações não aparecem, resetar o document_key:
```bash
docker exec legisinc-app php artisan tinker --execute="
\$template = \App\Models\TemplateUniversal::find(1);
\$template->document_key = 'reset_' . time();
\$template->save();
"
```

### 2. Compatibilidade de Formato
- OnlyOffice aceita RTF mas converte internamente para DOCX
- Salvar sempre mantém o formato RTF no banco
- Callbacks do OnlyOffice podem retornar DOCX

### 3. Encoding Múltiplo
- Banco de dados: UTF-8
- PHP: UTF-8 com funções mb_*
- RTF: Unicode sequences (\uXXX*)
- HTTP: charset=utf-8

## ✅ Checklist de Resolução

- [x] Identificar formato incorreto (TXT vs RTF)
- [x] Corrigir fileType na configuração OnlyOffice
- [x] Ajustar Content-Type para application/rtf
- [x] Implementar conversão UTF-8 para Unicode RTF
- [x] Adicionar limpeza de texto corrupto
- [x] Testar com caracteres acentuados portugueses
- [x] Validar que não aparece mais diálogo de codificação
- [x] Documentar solução

## 🆘 Suporte

Se o problema persistir após aplicar esta solução:

1. **Verificar versão do OnlyOffice**:
```bash
docker exec legisinc-onlyoffice documentserver-version
```

2. **Limpar todos os caches**:
```bash
docker exec legisinc-app php artisan cache:clear
docker exec legisinc-app php artisan config:clear
docker exec legisinc-app php artisan view:clear
```

3. **Verificar logs do OnlyOffice**:
```bash
docker logs legisinc-onlyoffice --tail 100
```

4. **Validar arquivo RTF gerado**:
```bash
# Baixar e abrir em editor local
curl "http://localhost:8001/api/templates/universal/1/download" -o template.rtf
# Abrir template.rtf no WordPad ou LibreOffice
```

## 📚 Referências Técnicas

### Funções PHP Importantes
- `mb_strlen($string, 'UTF-8')`: Conta caracteres UTF-8
- `mb_substr($string, $start, $length, 'UTF-8')`: Extrai substring UTF-8
- `mb_ord($char, 'UTF-8')`: Obtém codepoint Unicode
- `mb_check_encoding($string, 'UTF-8')`: Valida encoding UTF-8

### Formato RTF
- Header: `{\rtf1\ansi\ansicpg65001\deff0`
- Unicode: `\uN*` onde N é o codepoint decimal
- Code page UTF-8: `\ansicpg65001`

### OnlyOffice Document Server
- Suporta formatos: DOCX, XLSX, PPTX, RTF, TXT, ODT, ODS, ODP
- Converte internamente para Office Open XML
- Mantém cache baseado em document_key

---

**Última atualização**: 31/08/2025  
**Problema resolvido por**: Claude Code  
**Versão da solução**: 1.0  
**Status**: ✅ RESOLVIDO E TESTADO