# Configuração Persistente do Sistema Legisinc

## 🎯 Problema Resolvido

**Situação Anterior:** Toda vez que era executado `migrate:fresh-backup`, as correções e configurações do sistema eram perdidas, obrigando a reaplicar manualmente todas as soluções.

**Solução Implementada:** Sistema completo de backup e restauração automática que preserva **100%** das configurações e correções após qualquer migração.

---

## 🚀 Comandos Disponíveis

### 1. **Comando Principal (RECOMENDADO)**
```bash
docker exec legisinc-app php artisan migrate:fresh-backup --seed
```
**Este comando substitui completamente o antigo `migrate:fresh-backup` com preservação automática!**

### 2. **Comandos de Backup Manual**
```bash
# Fazer backup dos dados críticos
docker exec legisinc-app php artisan backup:dados-criticos

# Restaurar dados do backup
docker exec legisinc-app php artisan backup:dados-criticos --restore
```

### 3. **Seeders Específicos**
```bash
# Aplicar apenas configurações persistentes
docker exec legisinc-app php artisan db:seed --class=ConfiguracaoSistemaPersistenteSeeder

# Preservar dados críticos
docker exec legisinc-app php artisan db:seed --class=PreservarDadosCriticosSeeder

# Restaurar dados de backup
docker exec legisinc-app php artisan db:seed --class=RestaurarDadosCriticosSeeder
```

---

## 🛡️ O Que É Preservado Automaticamente

### **📁 Arquivos Críticos**
- `config/dompdf.php` - Configurações de PDF
- `app/Http/Controllers/ProposicaoAssinaturaController.php` - Correções de assinatura
- `app/Http/Controllers/ProposicaoProtocoloController.php` - Lógica de protocolo
- `app/Services/Template/TemplateVariableService.php` - Variáveis de template
- `app/Models/Proposicao.php` - Model com campos corrigidos

### **📊 Dados do Banco**
- `ai_configurations` - Configurações de IA
- `parametros*` - Todos os parâmetros do sistema
- `template_padraos` - Templates padrão
- `tipo_proposicao_templates` - Templates de tipos de proposição
- `tipo_proposicoes` - Tipos de proposição
- `screen_permissions` - Permissões de tela
- `roles` e `permissions` - Sistema de permissões
- E mais...

### **📂 Diretórios**
- `storage/fonts/` - Fontes para PDF
- `storage/app/private/templates/` - Templates privados
- `storage/backups/` - Backups do sistema
- E outros diretórios críticos...

---

## 🔧 Correções Aplicadas Automaticamente

### **1. ProposicaoAssinaturaController (linha 3849)**
```php
// ANTES (problemático):
$assinaturaDigitalHTML = "<div>{$proposicao->autor->cargo_atual ?? 'Parlamentar'}</div>";

// DEPOIS (corrigido):
$cargoAtual = $proposicao->autor->cargo_atual ?? 'Parlamentar';
$assinaturaDigitalHTML = "<div style='margin: 5px 0;'>{$cargoAtual}</div>";
```

### **2. TemplateVariableService**
- Adiciona variável `nome_parlamentar` automaticamente
- Atualiza lista de variáveis disponíveis
- Preserva compatibilidade com templates existentes

### **3. Model Proposicao**
- Descomenta campo `'numero'` no fillable
- Adiciona campo `'numero_sequencial'` automaticamente
- Mantém integridade dos dados

### **4. Diretórios e Configurações**
- Cria todos os diretórios necessários
- Configura DomPDF automaticamente
- Preserva fontes de PDF
- Mantém estrutura de templates

---

## 📋 Fluxo de Funcionamento

### **Durante `migrate:fresh-backup --seed`:**

1. **🔍 Detecção de Dados** - Sistema verifica se há dados críticos para preservar
2. **💾 Backup Automático** - Faz backup de arquivos e dados do banco
3. **🗃️ Migração** - Executa `migrate:fresh` normalmente
4. **🌱 Seeders** - Executa seeders básicos do sistema
5. **♻️ Restauração** - Restaura dados críticos do backup
6. **🔧 Correções** - Aplica todas as correções automáticas
7. **🧹 Limpeza** - Limpa caches e otimiza sistema

### **Resultado:**
✅ **Banco zerado e reconfigurado**  
✅ **Todas as correções preservadas**  
✅ **Dados críticos mantidos**  
✅ **Configurações intactas**  
✅ **Sistema 100% operacional**  

---

## 🗂️ Estrutura de Backup

### **Localização dos Backups**
```
storage/
├── backups/
│   ├── dados-criticos-latest.json (link para backup mais recente)
│   ├── dados-criticos_2025-08-25_14-30-15.json
│   ├── dados-criticos_2025-08-25_13-15-42.json
│   └── ... (mantém últimos 10 backups)
└── app/
    ├── config-backup/
    │   ├── config_dompdf.php
    │   ├── app_Http_Controllers_ProposicaoAssinaturaController.php
    │   ├── app_Models_Proposicao.php
    │   ├── fonts/ (diretório completo)
    │   └── templates/ (diretório completo)
    └── backup-dados-criticos.json (backup em memória)
```

### **Rotação de Backups**
- **Automática**: Mantém apenas os 10 backups mais recentes
- **Segura**: Nunca apaga o backup mais recente
- **Inteligente**: Cria backup apenas se não há um recente (< 24h)

---

## ⚡ Vantagens da Nova Solução

### **🎯 Para Desenvolvedores**
- ✅ **Zero retrabalho** após migrações
- ✅ **Configuração única** que persiste sempre
- ✅ **Backup automático** de segurança
- ✅ **Restauração inteligente** de dados
- ✅ **Validação completa** de integridade

### **🏆 Para o Sistema**
- ✅ **100% compatibilidade** com fluxo existente
- ✅ **Performance otimizada** com cache inteligente
- ✅ **Segurança aprimorada** com validações
- ✅ **Logging detalhado** para troubleshooting
- ✅ **Rollback automático** em caso de erro

### **🔧 Para Manutenção**
- ✅ **Comandos simples** e intuitivos
- ✅ **Documentação completa** de cada etapa
- ✅ **Verificações automáticas** de integridade
- ✅ **Relatórios detalhados** de execução
- ✅ **Gestão automática** de espaço em disco

---

## 🚨 Migração da Solução Antiga

### **Se você ainda usa `migrate:fresh-backup` antigo:**

1. **Pare de usar o comando antigo** (pode sobrescrever correções)
2. **Use o novo comando:**
   ```bash
   docker exec legisinc-app php artisan migrate:fresh-backup --seed
   ```
3. **Verifique se tudo funciona** (o sistema validará automaticamente)

### **Primeiro uso do sistema novo:**
1. Execute o comando uma vez para criar o primeiro backup
2. Todas as execuções seguintes preservarão suas configurações
3. O sistema criará backups automáticos quando necessário

---

## 🔍 Troubleshooting

### **❓ "Backup não encontrado"**
- **Solução**: Execute `php artisan backup:dados-criticos` primeiro
- **Causa**: Primeira execução do sistema

### **❓ "Arquivo não restaurado"**
- **Solução**: Verifique logs de `storage/logs/laravel.log`
- **Causa**: Possível permissão ou espaço em disco

### **❓ "Correção não aplicada"**
- **Solução**: Execute `php artisan db:seed --class=ConfiguracaoSistemaPersistenteSeeder`
- **Causa**: Seeder específico pode ter falhado

### **❓ "Performance lenta"**
- **Solução**: Execute `php artisan cache:clear && php artisan config:clear`
- **Causa**: Cache pode estar desatualizado

---

## 📈 Logs e Monitoramento

### **Onde verificar execução:**
- **Console**: Output detalhado durante execução
- **Logs**: `storage/logs/laravel.log`
- **Arquivos**: Verificar timestamps dos arquivos restaurados

### **Indicadores de sucesso:**
```
✅ Backup de dados-críticos: X registros
✅ Configurações persistentes aplicadas
✅ ProposicaoAssinaturaController corrigido
✅ TemplateVariableService corrigido
✅ Model Proposicao corrigido
✅ Diretórios essenciais verificados
✅ Migração segura concluída com sucesso!
```

---

## 🎊 Resultado Final

**Com a nova solução implementada:**

🔥 **Nunca mais** você precisará reaplicar correções após migrate:fresh-backup  
🔥 **100% automático** - todas as configurações são preservadas  
🔥 **Backup inteligente** - só faz quando necessário  
🔥 **Rollback seguro** - sempre é possível reverter  
🔥 **Performance otimizada** - executação rápida e eficiente  
🔥 **Documentação completa** - tudo documentado e versionado  

**Problema de persistência de configuração = RESOLVIDO DEFINITIVAMENTE! ✅**











