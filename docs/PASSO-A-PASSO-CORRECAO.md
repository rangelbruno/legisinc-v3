# Passo a Passo: Como Usar o Novo Sistema de Preservação

## 🚀 Guia Prático de Uso

### **Passo 1: Parar de usar o comando antigo**
```bash
# ❌ NÃO USE MAIS ESTE:
docker exec legisinc-app php artisan migrate:fresh-backup --seed

# ❌ ESSE COMANDO VAI SOBRESCREVER SUAS CORREÇÕES!
```

### **Passo 2: Usar o novo comando seguro**
```bash
# ✅ USE SEMPRE ESTE AGORA:
docker exec legisinc-app php artisan migrate:fresh-backup --seed
```

**IMPORTANTE**: O comando é o mesmo, mas agora usa a nova classe `MigrateFreshComBackup` que preserva tudo!

### **Passo 3: Primeira execução (criar backup inicial)**
```bash
# Se nunca executou o novo sistema, fazer backup manual primeiro:
docker exec legisinc-app php artisan backup:dados-criticos

# Depois executar a migração:
docker exec legisinc-app php artisan migrate:fresh-backup --seed
```

### **Passo 4: Verificar se funcionou**
Após a execução, você deve ver:
```
🔄 Iniciando migração segura com backup...
💾 Fazendo backup de dados críticos...
  ✓ Backup de parametros: X registros
  ✓ Backup de tipo_proposicao_templates: X registros
  [...]
📦 Executando migrate:fresh...
🌱 Executando seeders...
♻️ Restaurando dados críticos...
📂 Restaurando configurações...
🔧 Aplicando configurações persistentes...
  ✓ ProposicaoAssinaturaController corrigido
  ✓ TemplateVariableService corrigido
  ✓ Model Proposicao corrigido
✅ Migração segura concluída com sucesso!
```

---

## 🔧 Comandos de Manutenção

### **Fazer backup manual**
```bash
docker exec legisinc-app php artisan backup:dados-criticos
```

### **Restaurar de backup**
```bash
docker exec legisinc-app php artisan backup:dados-criticos --restore
```

### **Aplicar apenas correções**
```bash
docker exec legisinc-app php artisan db:seed --class=ConfiguracaoSistemaPersistenteSeeder
```

### **Listar backups disponíveis**
```bash
ls -la storage/backups/dados-criticos_*.json
```

---

## 🎯 Casos de Uso Comuns

### **Cenário 1: Desenvolvimento diário**
```bash
# Sempre que precisar resetar o banco:
docker exec legisinc-app php artisan migrate:fresh-backup --seed

# ✅ Todas suas correções estarão lá após a execução!
```

### **Cenário 2: Algo deu errado**
```bash
# Se por algum motivo as correções não foram aplicadas:
docker exec legisinc-app php artisan db:seed --class=ConfiguracaoSistemaPersistenteSeeder

# Ou restaurar backup:
docker exec legisinc-app php artisan backup:dados-criticos --restore
```

### **Cenário 3: Deploy/Produção**
```bash
# Em produção, sempre fazer backup antes:
docker exec legisinc-app php artisan backup:dados-criticos

# Depois executar a migração segura:
docker exec legisinc-app php artisan migrate:fresh-backup --seed
```

---

## ✅ Checklist de Validação

Após executar o novo comando, verifique se:

### **📁 Arquivos**
- [ ] `app/Http/Controllers/ProposicaoAssinaturaController.php` - linha 3849 corrigida
- [ ] `app/Services/Template/TemplateVariableService.php` - tem `nome_parlamentar`  
- [ ] `app/Models/Proposicao.php` - campo `numero` descomentado
- [ ] `config/dompdf.php` - existe e configurado

### **🗂️ Diretórios**
- [ ] `storage/backups/` - existe com backups
- [ ] `storage/fonts/` - existe  
- [ ] `storage/app/private/templates/` - existe

### **🌐 Sistema Web**
- [ ] Login funciona: http://localhost:8001
- [ ] Criar proposição funciona
- [ ] OnlyOffice abre e salva
- [ ] PDF de assinatura gera corretamente

---

## 🚨 Se Algo Der Errado

### **Problema: "Backup não encontrado"**
```bash
# Criar backup inicial:
docker exec legisinc-app php artisan backup:dados-criticos
```

### **Problema: "Comando não existe"**
```bash
# Verificar se os novos arquivos existem:
ls -la app/Console/Commands/
# Deve mostrar:
# - MigrateFreshComBackup.php  
# - BackupDadosCriticos.php
```

### **Problema: "Correções não aplicadas"**
```bash
# Executar seeder específico:
docker exec legisinc-app php artisan db:seed --class=ConfiguracaoSistemaPersistenteSeeder
```

### **Problema: "Performance lenta"**
```bash
# Limpar caches:
docker exec legisinc-app php artisan cache:clear
docker exec legisinc-app php artisan config:clear
```

---

## 📊 Monitoramento

### **Ver logs de execução:**
```bash
docker exec legisinc-app tail -f storage/logs/laravel.log
```

### **Verificar tamanho dos backups:**
```bash
du -sh storage/backups/
```

### **Listar últimos backups:**
```bash
ls -ltr storage/backups/dados-criticos_*.json | tail -5
```

---

## 🎊 Resultado Esperado

**Com o sistema funcionando corretamente:**

✅ **NUNCA MAIS** você vai precisar reaplicar correções  
✅ **MIGRAÇÃO SEGURA** com preservação automática  
✅ **BACKUP INTELIGENTE** que só executa quando necessário  
✅ **ROLLBACK GARANTIDO** se algo der errado  
✅ **PERFORMANCE OTIMIZADA** com cache e validações  
✅ **DOCUMENTAÇÃO COMPLETA** de todo o processo  

**🔥 Problema do migrate:fresh-backup = RESOLVIDO PARA SEMPRE! 🔥**
















