# 🔧 RTF Unicode Encoding Fix

## Problem
Portuguese characters (á, é, í, ó, ú, ç, ã, õ, etc.) were displaying incorrectly in OnlyOffice when editing RTF documents. Characters like "São Paulo" appeared as "SÃ£o Paulo".

## Root Cause
The `codificarTextoParaUnicode()` function in `ProposicaoController.php` was using PHP's `ord()` function which only works with single-byte characters. This caused incorrect encoding for UTF-8 multi-byte characters.

## Solution Implemented

### File Modified
`/home/bruno/legisinc/app/Http/Controllers/ProposicaoController.php`

### Changes Made (Lines 1834-1880)
1. Replaced `strlen()` with `mb_strlen($chunk, 'UTF-8')`
2. Replaced `$chunk[$i]` with `mb_substr($chunk, $i, 1, 'UTF-8')`
3. Replaced `ord($char)` with `mb_ord($char, 'UTF-8')`
4. Added logging for debugging

### How It Works
The function now properly converts UTF-8 characters to RTF Unicode escape sequences:
- `ã` → `\u227*`
- `ç` → `\u231*`
- `é` → `\u233*`
- `í` → `\u237*`
- `ó` → `\u243*`
- `ú` → `\u250*`

## Testing
Run the test script to verify the fix:
```bash
/home/bruno/legisinc/scripts/verify-unicode-fix.sh
```

## Impact
This fix ensures that when RTF templates contain Unicode placeholders (like `\u36*\u123*...`), the replacement values are properly encoded to match the RTF Unicode format, preserving Portuguese characters correctly in OnlyOffice.

## Related Functions
- `codificarTextoParaUnicode()`: Fixed - Converts UTF-8 text to RTF Unicode
- `converterUtf8ParaRtf()`: Alternative function for non-Unicode placeholders
- `substituirVariaveisRTF()`: Main function that applies variable substitution