#!/bin/bash

# Script de Configuração Automática do Sistema de Preservação v2.0
# Executa uma única vez para configurar tudo

echo "🛡️ Configurando Sistema de Preservação Automática v2.0"
echo "=============================================="

# Verificar se estamos no diretório correto
if [[ ! -f "artisan" ]]; then
    echo "❌ Execute este script na raiz do projeto Laravel"
    exit 1
fi

# 1. Criar aliases no bashrc do container
echo "📝 Configurando aliases..."

docker exec -it legisinc-app bash -c "
cat >> ~/.bashrc << 'EOF'

# ====== SISTEMA DE PRESERVAÇÃO AUTOMÁTICA ======
alias migrate-safe='php artisan migrate:safe --fresh --seed --generate-seeders'
alias migrate-detect='php artisan melhorias:generate --auto'
alias migrate-backup='php artisan backup:dados-criticos'
alias migrate-restore='php artisan backup:dados-criticos --restore'
alias migrate-fresh-safe='php artisan migrate:safe --fresh --seed --generate-seeders'

# Alias de conveniência
alias ms='migrate-safe'
alias md='migrate-detect'
alias mb='migrate-backup'  
alias mr='migrate-restore'
EOF
"

# 2. Criar comando wrapper no host
echo "🔧 Criando comando wrapper no host..."

cat > ./migrate-safe << 'EOF'
#!/bin/bash
echo "🛡️ Executando Migration Segura com Preservação Automática"
echo "=========================================================="
docker exec -it legisinc-app php artisan migrate:safe --fresh --seed --generate-seeders
EOF

chmod +x ./migrate-safe

# 3. Configurar cron job para detecção automática (opcional)
echo "⏰ Configurando detecção automática..."

cat > ./auto-detect-changes << 'EOF'
#!/bin/bash
# Script para detectar alterações automaticamente
echo "🔍 Detectando alterações automaticamente..."
docker exec -it legisinc-app php artisan melhorias:generate --detect --create-seeder
EOF

chmod +x ./auto-detect-changes

# 4. Criar documentação de uso
cat > ./PRESERVACAO-AUTOMATICA.md << 'EOF'
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
EOF

echo ""
echo "✅ Configuração concluída!"
echo ""
echo "📋 Próximos passos:"
echo "1. Execute: ./migrate-safe (em vez de migrate:fresh --seed)"
echo "2. Suas melhorias serão preservadas automaticamente" 
echo "3. Consulte PRESERVACAO-AUTOMATICA.md para detalhes"
echo ""
echo "🎉 Sistema de Preservação Automática ativo!"