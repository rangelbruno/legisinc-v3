# Correção de Caracteres Unicode RTF para o Legislativo

## Problema Identificado
O usuário Legislativo ao abrir documentos no OnlyOffice estava recebendo conteúdo com caracteres especiais mal formatados (ex: `\u231*` ao invés de `ç`).

## Causa do Problema
O método `extrairConteudoRTF` no `OnlyOfficeService.php` não estava processando corretamente os caracteres Unicode RTF antes de remover os comandos de controle RTF, fazendo com que a extração perdesse os caracteres acentuados.

## Solução Aplicada

### Arquivo Modificado
`app/Services/OnlyOffice/OnlyOfficeService.php` - método `extrairConteudoRTF` (linhas 3302-3393)

### Mudanças Principais

1. **Processamento de Unicode ANTES de remover controles RTF**
   - Agora processa `\uN` e `\uN*` como primeira etapa
   - Trata números negativos (complemento de 2^16)
   - Usa `mb_chr()` para conversão correta

2. **Ordem de processamento otimizada**
   ```php
   // 1º - Processar Unicode (ANTES de qualquer outra coisa)
   $content = preg_replace_callback('/\\\\u(-?[0-9]+)\\*?/', ...);
   
   // 2º - Converter quebras de linha
   $content = str_replace(['\\par ', '\\par', '\\line'], "\n", $content);
   
   // 3º - Remover cabeçalhos e comandos RTF
   // 4º - Limpar e formatar
   ```

3. **Melhor tratamento de caracteres especiais RTF**
   - Adicionado mapeamento para aspas, bullets, traços
   - Preservação de estrutura do documento
   - Logs detalhados para debug

## Resultado

### Antes da Correção
```
C\u194*MARA MUNICIPAL → CMARA MUNICIPAL (sem Â)
Mo\u231*\u227*o → Moo (sem ç e ã)
```

### Após a Correção
```
C\u194*MARA MUNICIPAL → CÂMARA MUNICIPAL ✅
Mo\u231*\u227*o → Moção ✅
```

## Testes Realizados

### Teste de Extração RTF
- ✅ Todos os caracteres portugueses extraídos corretamente
- ✅ Acentuação preservada (á, é, í, ó, ú, â, ê, ô, ã, õ, ç)
- ✅ Caracteres especiais (Nº, bullets, aspas)
- ✅ Estrutura do documento mantida

### Caracteres Testados com Sucesso
- CÂMARA, Praça, República
- MOÇÃO, Nº, congratulações
- educação, importância, esforços
- incansáveis, avaliações, excelência
- Plenário, João

## Impacto

### Parlamentar
✅ Continua funcionando normalmente com template universal

### Legislativo
✅ Agora recebe documentos com acentuação correta
✅ Pode editar e salvar sem perder caracteres especiais
✅ Conteúdo extraído corretamente para o banco de dados

## Scripts de Teste
- `test_rtf_extraction.php` - Teste básico de extração
- `test_rtf_complete_flow.php` - Teste completo com verificações

## Conclusão
A solução aplicada garante que tanto o Parlamentar quanto o Legislativo possam trabalhar com documentos RTF contendo caracteres Unicode (acentuação portuguesa) sem problemas de codificação.