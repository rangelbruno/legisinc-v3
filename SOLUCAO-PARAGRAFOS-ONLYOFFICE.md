# 📝 SOLUÇÃO: Preservação de Parágrafos no OnlyOffice

## ✅ PROBLEMA RESOLVIDO

**Situação Anterior:** Quando um usuário criava uma proposição com texto contendo múltiplos parágrafos, ao abrir no editor OnlyOffice, todo o texto aparecia em uma única linha, sem respeitar as quebras de linha originais.

**Causa:** A função `converterParaRTF()` no `TemplateProcessorService.php` não estava tratando as quebras de linha (`\n` e `\r\n`), apenas convertendo caracteres Unicode para o formato RTF.

## 🔧 CORREÇÃO IMPLEMENTADA

### Arquivo Modificado:
`/app/Services/Template/TemplateProcessorService.php` (linhas 283-311)

### Mudança Aplicada:
```php
private function converterParaRTF(string $texto): string
{
    $textoProcessado = '';
    $length = mb_strlen($texto, 'UTF-8');
    
    for ($i = 0; $i < $length; $i++) {
        $char = mb_substr($texto, $i, 1, 'UTF-8');
        $codepoint = mb_ord($char, 'UTF-8');
        
        // NOVA FUNCIONALIDADE: Tratar quebras de linha
        if ($char === "\n") {
            $textoProcessado .= '\\par ';  // Converter \n para parágrafo RTF
        } else if ($char === "\r") {
            // Ignorar \r se for seguido de \n (Windows line ending)
            if ($i + 1 < $length && mb_substr($texto, $i + 1, 1, 'UTF-8') === "\n") {
                continue;
            }
            $textoProcessado .= '\\par ';
        } else if ($codepoint > 127) {
            // Caracteres Unicode (acentuação portuguesa)
            $textoProcessado .= '\\u' . $codepoint . '*';
        } else {
            // Caracteres ASCII normais
            $textoProcessado .= $char;
        }
    }
    
    return $textoProcessado;
}
```

## 🎯 FLUXO CORRIGIDO

1. **Criação da Proposição** (`/proposicoes/create`)
   - Usuário insere texto com múltiplos parágrafos no campo `texto_principal`
   - Texto é salvo no banco com quebras de linha preservadas (`\n`)

2. **Abertura no OnlyOffice** (`/proposicoes/{id}/onlyoffice/editor-parlamentar`)
   - Template é processado pelo `TemplateProcessorService`
   - Função `converterParaRTF()` converte cada `\n` em `\par` (parágrafo RTF)
   - OnlyOffice recebe documento RTF com marcadores de parágrafo corretos

3. **Resultado Visual**
   - Texto aparece com parágrafos separados no editor
   - Formatação original é preservada
   - Usuário pode continuar editando mantendo a estrutura

## 📊 TESTE DE VALIDAÇÃO

### Script de Teste:
`/home/bruno/legisinc/test-paragrafos-simples.php`

### Resultado do Teste:
```
✅ SUCESSO: Quebras de linha foram convertidas para \par!
   Marcadores \par encontrados: 4
   O texto será exibido com parágrafos separados no OnlyOffice.
```

### Comando de Teste:
```bash
docker exec legisinc-app php test-paragrafos-simples.php
```

## 🚀 COMO TESTAR MANUALMENTE

1. **Login como Parlamentar:**
   - URL: http://localhost:8001/login
   - Email: jessica@sistema.gov.br
   - Senha: 123456

2. **Criar Nova Proposição:**
   - Acessar: http://localhost:8001/proposicoes/create?tipo=mocao
   - Preencher Ementa
   - Escolher "Preencher manualmente"
   - No campo "Texto Principal", inserir texto com múltiplos parágrafos:
   ```
   Primeiro parágrafo do texto.
   
   Segundo parágrafo com mais conteúdo.
   
   Terceiro parágrafo final.
   ```

3. **Verificar no Editor:**
   - Clicar em "Continuar"
   - Na página da proposição, clicar em "Continuar Editando"
   - **Verificar:** O texto deve aparecer com os 3 parágrafos separados

## 💡 DETALHES TÉCNICOS

### Formato RTF:
- `\par` = Marcador de fim de parágrafo no formato RTF
- `\u225*` = Caractere Unicode (ex: "á" = código 225)
- OnlyOffice interpreta corretamente esses marcadores

### Compatibilidade:
- ✅ Windows line endings (`\r\n`)
- ✅ Unix line endings (`\n`)
- ✅ Mac classic line endings (`\r`)
- ✅ Múltiplas quebras consecutivas (linhas em branco)

## 🔄 PRESERVAÇÃO DA CORREÇÃO

Esta correção é permanente e será preservada após:
- `docker exec -it legisinc-app php artisan migrate:fresh --seed`
- Reinicialização do container
- Deploy em produção

**Arquivo crítico:** `/app/Services/Template/TemplateProcessorService.php`

## 📝 NOTAS ADICIONAIS

1. **Acentuação:** A função também preserva corretamente caracteres acentuados (português)
2. **Performance:** Processamento eficiente usando `mb_*` functions para UTF-8
3. **Retrocompatibilidade:** Não afeta documentos existentes

---

**Status:** ✅ IMPLEMENTADO E TESTADO  
**Data:** 23/08/2025  
**Versão:** 1.0
