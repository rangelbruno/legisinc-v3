# Fix para Campo Cargo não Aparecendo Selecionado na Edição

## Problema
Na tela `/parlamentares/{id}/edit`, o campo "Cargo" não aparece selecionado com o valor atual do parlamentar.

## Causa
O problema ocorreu porque:
1. O sistema estava salvando valores de cargo em formato inconsistente (ex: `vereador` vs `Vereador`)
2. Os options do select estavam fazendo comparação exata, então valores com case diferente não eram reconhecidos

## Soluções Implementadas

### 1. Padronização dos Valores de Cargo
**UserController.php** - Agora salva valores padronizados:
- ✅ `Vereador` (em vez de `vereador`)
- ✅ `Presidente da Câmara` (em vez de `presidente`)
- ✅ etc.

### 2. Formulário Mais Robusto
**parlamentares/components/form.blade.php** - Agora faz comparação case-insensitive:
```php
$isSelected = strtolower($currentCargo) == strtolower($value) || $currentCargo == $value;
```

### 3. Migração de Correção de Dados
**2025_07_25_025432_fix_parlamentar_cargo_values.php** - Corrige registros existentes:
```bash
php artisan migrate
```

### 4. Opções Padronizadas
**usuarios/create.blade.php** - Usa os mesmos valores que o form de parlamentares.

## Como Aplicar o Fix

1. **Executar migração para corrigir dados existentes:**
```bash
php artisan migrate
```

2. **Testar a edição:**
   - Vá para `/parlamentares/{id}/edit`
   - O campo "Cargo" deve agora aparecer selecionado corretamente
   - Teste salvar alterações

## Valores de Cargo Padronizados
- `Vereador`
- `Vereadora` 
- `Presidente da Câmara`
- `Vice-Presidente`
- `1º Secretário`
- `2º Secretário`

## Benefícios
- ✅ Campo cargo aparece selecionado na edição
- ✅ Consistência entre formulários de usuário e parlamentar
- ✅ Comparação robusta que funciona independente do case
- ✅ Dados existentes corrigidos automaticamente