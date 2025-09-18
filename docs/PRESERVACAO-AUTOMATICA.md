# 🛡️ Sistema de Preservação Automática v2.0

## 🚀 Comandos Principais

### No Host (Recomendado)
```bash
# Comando principal - substitui migrate:fresh --seed
./migrate-safe

# Detectar alterações manualmente  
./auto-detect-changes
```

### Dentro do Container
```bash
# Comando completo
migrate-safe

# Comandos individuais
migrate-detect    # Detectar e gerar seeders
migrate-backup    # Fazer backup
migrate-restore   # Restaurar backup
```

## 🔧 Como Funciona

1. **Detecção Automática**: Sistema detecta alterações em arquivos críticos
2. **Backup Inteligente**: Preserva automaticamente suas melhorias 
3. **Migration Segura**: Executa migrate:fresh --seed sem perder nada
4. **Restauração Automática**: Restaura melhorias após seeders
5. **Rastreamento**: Mantém histórico de todas as alterações

## 📁 Arquivos Monitorados

- `app/Http/Controllers/ProposicaoAssinaturaController.php`
- `app/Http/Controllers/ProposicaoProtocoloController.php`  
- `app/Services/OnlyOffice/OnlyOfficeService.php`
- `app/Services/Template/TemplateProcessorService.php`
- `app/Services/Template/TemplateVariableService.php`
- `app/Models/Proposicao.php`
- `config/dompdf.php`

## 🎯 Benefícios

✅ **Zero Perda**: Nunca mais perde melhorias  
✅ **Automático**: Funciona sem intervenção manual  
✅ **Inteligente**: Só preserva o que realmente mudou  
✅ **Rastreável**: Histórico completo de alterações  
✅ **Seguro**: Backup antes de qualquer operação  

## 📊 Monitoramento

Verifique alterações detectadas:
```bash
docker exec -it legisinc-app php artisan melhorias:generate --detect
```

Ver histórico de preservações:
```sql
SELECT * FROM melhorias_tracking ORDER BY created_at DESC;
```

## 🔄 Workflow Recomendado

1. Faça suas melhorias normalmente
2. Execute `./migrate-safe` 
3. Sistema detecta e preserva automaticamente
4. Suas melhorias permanecem após migrate:fresh --seed

**Nunca mais execute `migrate:fresh --seed` diretamente!**
