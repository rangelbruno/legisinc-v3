# 🚨 Solução: Problema de Acentuação no OnlyOffice

## 📋 Descrição do Problema
Caracteres acentuados em português (á, é, í, ó, ú, ç, ã, õ, etc.) apareciam corrompidos no editor OnlyOffice ao editar documentos RTF. Por exemplo:
- "São Paulo" aparecia como "SÃ£o Paulo"
- "José" aparecia como "JosÃ©"
- "Endereço" aparecia como "EndereÃ§o"

## 🔍 Diagnóstico

### 1. Sintomas Identificados
- Caracteres UTF-8 sendo interpretados incorretamente
- Padrão típico de double-encoding (UTF-8 → Latin-1)
- Problema ocorria especificamente com templates RTF no OnlyOffice

### 2. Fluxo do Problema
```
Banco de Dados (UTF-8) → Laravel (UTF-8) → RTF Template → OnlyOffice
                                              ↑
                                     [PROBLEMA AQUI]
```

### 3. Causa Raiz
O arquivo RTF usa sequências Unicode especiais para caracteres não-ASCII:
- RTF espera: `\u227*` para "ã"
- PHP estava gerando: bytes UTF-8 raw ou códigos incorretos

## 🔧 Solução Implementada

### Arquivo Modificado
```
/home/bruno/legisinc/app/Http/Controllers/ProposicaoController.php
```

### Problema no Código Original
```php
// ❌ CÓDIGO COM PROBLEMA (linhas ~1847-1849)
for ($i = 0; $i < strlen($chunk); $i++) {
    $char = $chunk[$i];        // Não funciona com UTF-8 multi-byte
    $codigo = ord($char);       // ord() só lê 1 byte, não UTF-8
    
    if ($codigo > 127) {
        $textoProcessado .= '\\u' . $codigo . '*';
    }
}
```

### Solução Aplicada
```php
// ✅ CÓDIGO CORRIGIDO (linhas 1852-1862)
$length = mb_strlen($chunk, 'UTF-8');
for ($i = 0; $i < $length; $i++) {
    $char = mb_substr($chunk, $i, 1, 'UTF-8');  // Extrai caractere UTF-8 corretamente
    $codepoint = mb_ord($char, 'UTF-8');        // Obtém codepoint Unicode real
    
    if ($codepoint > 127) {
        $textoProcessado .= '\\u' . $codepoint . '*';  // Gera sequência RTF correta
    } else {
        $textoProcessado .= $char;
    }
}
```

### Mudanças Técnicas
| Função Antiga | Função Nova | Motivo |
|--------------|-------------|---------|
| `strlen()` | `mb_strlen(..., 'UTF-8')` | Conta caracteres UTF-8 corretamente |
| `$string[$i]` | `mb_substr(..., $i, 1, 'UTF-8')` | Extrai caractere multi-byte |
| `ord()` | `mb_ord(..., 'UTF-8')` | Obtém codepoint Unicode real |

## 📊 Tabela de Conversão RTF Unicode

| Caractere | Unicode | RTF Sequence |
|-----------|---------|--------------|
| á | U+00E1 | `\u225*` |
| à | U+00E0 | `\u224*` |
| ã | U+00E3 | `\u227*` |
| â | U+00E2 | `\u226*` |
| é | U+00E9 | `\u233*` |
| ê | U+00EA | `\u234*` |
| í | U+00ED | `\u237*` |
| ó | U+00F3 | `\u243*` |
| õ | U+00F5 | `\u245*` |
| ô | U+00F4 | `\u244*` |
| ú | U+00FA | `\u250*` |
| ç | U+00E7 | `\u231*` |
| Ç | U+00C7 | `\u199*` |
| ° | U+00B0 | `\u176*` |
| º | U+00BA | `\u186*` |
| ª | U+00AA | `\u170*` |
| © | U+00A9 | `\u169*` |

## 🧪 Como Testar

### 1. Script de Teste Automatizado
```bash
# Execute o script de verificação
/home/bruno/legisinc/scripts/verify-unicode-fix.sh
```

### 2. Teste Manual no Sistema
1. Acesse o sistema Legisinc
2. Crie ou edite uma proposição
3. Insira texto com acentos: "São Paulo", "José", "Câmara Municipal"
4. Abra no editor OnlyOffice
5. Verifique se os caracteres aparecem corretamente

### 3. Verificar nos Logs
```bash
# Monitorar logs em tempo real
tail -f /home/bruno/legisinc/storage/logs/laravel.log | grep -E "Codificando texto|Unicode RTF"
```

Procure por mensagens como:
- "Codificando texto para Unicode RTF"
- "Texto codificado para Unicode RTF"

## 🔄 Processo de Debug (Para Futuros Problemas)

### 1. Verificar Encoding do Banco
```sql
-- No PostgreSQL
SELECT datname, encoding, datcollate, datctype 
FROM pg_database 
WHERE datname = 'legisinc';
```
Deve retornar `UTF8`.

### 2. Verificar Template RTF
```bash
# Ver cabeçalho do RTF
head -5 /home/bruno/legisinc/storage/app/templates/template_1.rtf
```
Procure por: `\ansicpg65001` (UTF-8 code page)

### 3. Testar Conversão PHP
```php
<?php
// Teste rápido
$texto = "São Paulo";
echo "UTF-8 bytes: " . bin2hex($texto) . "\n";
echo "mb_ord('ã'): " . mb_ord('ã', 'UTF-8') . "\n";  // Deve ser 227
echo "RTF: \\u227*\n";
?>
```

### 4. Verificar Fluxo Completo
```bash
# Limpar logs
truncate -s 0 /home/bruno/legisinc/storage/logs/laravel.log

# Criar/editar proposição com acentos

# Verificar logs
grep -A5 -B5 "São Paulo\|José\|Endereço" /home/bruno/legisinc/storage/logs/laravel.log
```

## ⚠️ Pontos de Atenção

### 1. Diferentes Contextos de Substituição
O sistema tem dois métodos de codificação:
- `converterUtf8ParaRtf()`: Para placeholders normais (`${variavel}`)
- `codificarTextoParaUnicode()`: Para placeholders Unicode (`\u36*\u123*...`)

### 2. Configuração do OnlyOffice
Verifique no `docker-compose.yml`:
```yaml
environment:
  - DOCUMENT_SERVER_LOCALE=C.UTF-8
  - LC_ALL=C.UTF-8
  - LANG=C.UTF-8
```

### 3. Configuração do Laravel
No arquivo `.env`:
```env
DB_CHARSET=UTF8
APP_LOCALE=pt_BR
```

## 📚 Referências Técnicas

### Funções PHP Importantes
- `mb_strlen($string, 'UTF-8')`: Conta caracteres UTF-8
- `mb_substr($string, $start, $length, 'UTF-8')`: Extrai substring UTF-8
- `mb_ord($char, 'UTF-8')`: Obtém codepoint Unicode
- `mb_chr($codepoint, 'UTF-8')`: Cria caractere do codepoint

### Formato RTF Unicode
- Sintaxe: `\uN*` onde N é o codepoint decimal
- Exemplo: `ã` (U+00E3 = 227) → `\u227*`
- O asterisco `*` indica fim da sequência

## ✅ Checklist de Resolução

- [x] Identificar caracteres corrompidos no OnlyOffice
- [x] Verificar encoding do banco de dados (UTF-8)
- [x] Localizar função de conversão no código
- [x] Substituir funções single-byte por multi-byte
- [x] Adicionar logs para debug
- [x] Testar com caracteres portugueses
- [x] Documentar solução

## 🆘 Suporte

Se o problema persistir após aplicar esta solução:

1. **Verifique os logs**:
   ```bash
   tail -100 /home/bruno/legisinc/storage/logs/laravel.log
   ```

2. **Execute o diagnóstico**:
   ```bash
   /home/bruno/legisinc/scripts/verify-unicode-fix.sh
   ```

3. **Revise as funções**:
   - `codificarTextoParaUnicode()` (linha ~1834)
   - `converterUtf8ParaRtf()` (linha ~2894)
   - `substituirVariaveisRTF()` (linha ~1525)

---

**Última atualização**: 03/08/2025
**Problema resolvido por**: Claude Code
**Versão da solução**: 1.0