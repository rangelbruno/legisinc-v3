# 🛡️ Sistema de Preservação Automática v2.0

## 🎯 **PROBLEMA RESOLVIDO**

**ANTES:** `migrate:fresh --seed` apagava todas as melhorias manuais feitas no código  
**DEPOIS:** Sistema detecta e preserva automaticamente TODAS as melhorias

## 🚀 **CONFIGURAÇÃO INICIAL (Execute uma vez)**

```bash
# Dar permissão e executar script de configuração
chmod +x scripts/setup-auto-preservation.sh
./scripts/setup-auto-preservation.sh
```

## 💡 **NOVO WORKFLOW**

### ❌ **NUNCA MAIS USE:**
```bash
docker exec -it legisinc-app php artisan migrate:fresh --seed
```

### ✅ **SEMPRE USE:**
```bash
./migrate-safe
```

## 🔧 **Comandos Disponíveis**

### **No Host (Recomendado)**
```bash
./migrate-safe              # Comando principal - substitui migrate:fresh --seed  
./auto-detect-changes       # Detectar alterações manualmente
```

### **Dentro do Container**
```bash
migrate-safe                # Comando completo com preservação
migrate-detect              # Detectar e gerar seeders automaticamente  
migrate-backup              # Fazer backup manual
migrate-restore             # Restaurar backup manual
```

### **Comandos Artisan Completos**
```bash
# Sistema completo de preservação
php artisan migrate:safe --fresh --seed --generate-seeders

# Detectar alterações e gerar seeders
php artisan melhorias:generate --auto

# Backup/restore manual
php artisan backup:dados-criticos
php artisan backup:dados-criticos --restore
```

## 🧠 **Como Funciona o Sistema Inteligente**

### **1. Detecção Automática**
- Monitora arquivos críticos automaticamente
- Calcula hash SHA256 de conteúdo + metadata
- Detecta novos arquivos, modificações e remoções
- Registra alterações em tabela `melhorias_tracking`

### **2. Preservação Inteligente** 
- Faz backup físico dos arquivos alterados
- Cria seeders específicos para cada conjunto de alterações
- Atualiza DatabaseSeeder automaticamente
- Mantém histórico completo de modificações

### **3. Restauração Automática**
- Executa ANTES de seeders que podem sobrescrever
- Restaura DEPOIS de seeders que sobrescrevem
- Compara hashes para detectar sobrescrita
- Restore inteligente apenas do que foi alterado

### **4. Rastreamento Completo**
- Tabela dedicada para tracking de alterações
- Metadata completa (tamanho, data, tipo)
- Histórico de todas as preservações
- Auditoria completa de modificações

## 📁 **Arquivos Monitorados Automaticamente**

- `app/Http/Controllers/ProposicaoAssinaturaController.php`
- `app/Http/Controllers/ProposicaoProtocoloController.php`
- `app/Services/OnlyOffice/OnlyOfficeService.php`  
- `app/Services/Template/TemplateProcessorService.php`
- `app/Services/Template/TemplateVariableService.php`
- `app/Models/Proposicao.php`
- `config/dompdf.php`
- `resources/views/proposicoes/**/*.blade.php` (pattern recursivo)

## 📊 **Monitoramento e Auditoria**

### **Ver Alterações Detectadas**
```bash
docker exec -it legisinc-app php artisan melhorias:generate --detect
```

### **Histórico de Preservações**
```sql
SELECT 
    arquivo,
    tipo,
    created_at,
    preservado
FROM melhorias_tracking 
ORDER BY created_at DESC 
LIMIT 20;
```

### **Status dos Backups**
```bash
ls -la storage/app/smart-preservation/
ls -la storage/app/melhorias-backup/
ls -la storage/backups/
```

### **Ver Seeders Gerados**
```bash
ls -la database/seeders/PreservarMelhorias*Seeder.php
```

## 🎯 **Fluxo Completo de Trabalho**

### **1. Desenvolvimento Normal**
```bash
# Faça suas melhorias normalmente
vim app/Http/Controllers/ProposicaoAssinaturaController.php
vim app/Services/OnlyOffice/OnlyOfficeService.php
# ... outras modificações
```

### **2. Migration Segura**
```bash
# Uma única linha substitui todo o processo
./migrate-safe
```

### **3. O que Acontece Automaticamente:**
1. ✅ **Detecção**: Sistema detecta suas melhorias
2. ✅ **Backup**: Faz backup físico dos arquivos  
3. ✅ **Geração**: Cria seeders de preservação
4. ✅ **Migration**: Executa migrate:fresh --seed
5. ✅ **Restauração**: Recupera suas melhorias
6. ✅ **Validação**: Confirma que tudo foi preservado

### **4. Resultado:**
- 🎊 **Banco zerado e reconfigurado**
- 🛡️ **Todas as suas melhorias preservadas**  
- 📋 **Histórico completo documentado**
- 🚀 **Sistema 100% operacional**

## ⚙️ **Configurações Avançadas**

### **Adicionar Novos Arquivos para Monitoramento**
Edite `app/Console/Commands/GenerateMelhoriasSeeders.php`:
```php
private $arquivosMonitorados = [
    // ... arquivos existentes
    'novo/arquivo/para/monitorar.php',
    'resources/views/custom/**/*.blade.php'
];
```

### **Configurar Detecção Automática por Cron**
```bash
# Adicionar ao crontab
0 */6 * * * cd /path/to/project && ./auto-detect-changes
```

### **Backup Personalizado**
```bash
# Fazer backup de arquivos específicos
php artisan backup:dados-criticos --files="app/Models,config/custom"
```

## 🚨 **Troubleshooting**

### **Problema: Melhorias perdidas**
```bash
# Restaurar último backup
docker exec -it legisinc-app php artisan backup:dados-criticos --restore

# Ou executar restore inteligente  
docker exec -it legisinc-app php artisan db:seed --class=SmartPreservationSeeder
```

### **Problema: Sistema não detecta alterações**
```bash
# Forçar detecção manual
docker exec -it legisinc-app php artisan melhorias:generate --detect --create-seeder

# Verificar permissões
chmod -R 755 storage/app/smart-preservation/
chmod -R 755 storage/app/melhorias-backup/
```

### **Problema: Seeders não funcionam**
```bash
# Re-executar configuração
./scripts/setup-auto-preservation.sh

# Verificar seeders existentes
ls -la database/seeders/SmartPreservationSeeder.php
```

## 📈 **Benefícios do Sistema**

### **✅ Benefícios Técnicos**
- **Zero Perda**: Impossível perder melhorias
- **Automático**: Sem intervenção manual necessária
- **Inteligente**: Só preserva o que realmente mudou  
- **Rastreável**: Histórico completo auditável
- **Seguro**: Múltiplas camadas de backup
- **Escalável**: Fácil adicionar novos arquivos

### **✅ Benefícios de Produtividade**  
- **Fluxo Único**: Um comando substitui workflow complexo
- **Confiança**: Trabalhe sem medo de perder código
- **Rapidez**: Restore automático em segundos
- **Documentação**: Auto-gera documentação de mudanças
- **Debugging**: Fácil comparar versões anteriores

### **✅ Benefícios de Manutenção**
- **Versionamento**: Histórico de todas as alterações
- **Auditoria**: Rastreamento completo de modificações  
- **Recovery**: Múltiplos pontos de restauração
- **Flexibilidade**: Fácil customizar monitoramento
- **Integração**: Funciona com sistema existente

## 📋 **Estrutura de Arquivos Criada**

```
projeto/
├── app/Console/Commands/
│   ├── GenerateMelhoriasSeeders.php     # Gerador automático
│   └── MigrateWithPreservation.php      # Migration segura
├── database/seeders/  
│   ├── SmartPreservationSeeder.php      # Preservação inteligente
│   └── PreservarMelhorias*Seeder.php    # Seeders gerados automaticamente
├── storage/app/
│   ├── smart-preservation/              # Backups inteligentes
│   ├── melhorias-backup/                # Backups por arquivo
│   ├── melhorias-hashes/                # Hashes para detecção
│   └── smart-preservation-config.json   # Configuração do sistema
├── scripts/
│   └── setup-auto-preservation.sh       # Script de configuração
├── migrate-safe*                        # Comando wrapper host
├── auto-detect-changes*                 # Detecção manual
├── SISTEMA-PRESERVACAO-AUTOMATICA.md    # Esta documentação
├── MELHORIAS-AUTOMATICAS.md             # Log de melhorias detectadas
└── PRESERVACAO-AUTOMATICA.md            # Guia rápido
```

---

## 🎊 **RESULTADO FINAL**

**Nunca mais perca melhorias com `migrate:fresh --seed`!**

### **Antes (Problemático):**
1. Fazer melhorias no código  
2. Executar `migrate:fresh --seed`
3. 😱 **Todas as melhorias perdidas!**
4. Refazer tudo manualmente  

### **Depois (Automático):**
1. Fazer melhorias no código
2. Executar `./migrate-safe`  
3. 🎉 **Melhorias preservadas automaticamente!**
4. Sistema funcionando perfeitamente

**O sistema funciona de forma totalmente transparente e inteligente! 🚀**