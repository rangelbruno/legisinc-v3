# 🚀 LegisInc - Sistema de Gestão Legislativa

## 📋 **Comandos Principais**

### ⚠️ **IMPORTANTE: Sistema de Backup Automático**

O sistema agora possui **backup automático** dos dados críticos (proposições, protocolos, assinaturas) que são **preservados automaticamente** durante `migrate:fresh --seed`.

### ✅ **1. Rodar do zero COM BACKUP AUTOMÁTICO (Recomendado)**
```bash
 ./migrate-safe
  OU
  docker exec legisinc-app php artisan migrate:safe --fresh --seed --generate-seeders
```

**🛡️ Proteção:** Este comando agora inclui:
- `ConfiguracaoSistemaPersistenteSeeder` - Configurações do sistema
- `PreservarDadosCriticosSeeder` - Backup automático dos dados
- `RestaurarDadosCriticosSeeder` - Restauração automática dos dados

### 🔧 **2. Comandos de Backup e Restauração**
```bash
# Fazer backup manual dos dados críticos
docker exec -it legisinc-app php artisan backup:dados-criticos

# Forçar backup (mesmo se recente)
docker exec -it legisinc-app php artisan backup:dados-criticos --force

# Restaurar dados críticos manualmente
docker exec -it legisinc-app php artisan db:seed --class=RestaurarDadosCriticosSeeder
```

### 🔧 **3. Executar apenas o seeder de configuração (se necessário)**
```bash
docker exec -it legisinc-app php artisan db:seed --class=ConfiguracaoSistemaPersistenteSeeder
```

## 🛡️ **Sistema de Proteção Completo**

### **🔄 Backup e Restauração Automática:**
- ✅ **Backup automático** de proposições, protocolos e assinaturas
- ✅ **Preservação de PDFs** gerados anteriormente
- ✅ **Restauração automática** após `migrate:fresh --seed`
- ✅ **Metadados de controle** para auditoria

### **⚙️ Configuração Persistente Automática:**
- ✅ **Configura DomPDF** com as configurações corretas
- ✅ **Cria diretórios** de armazenamento necessários
- ✅ **Configura fontes** para geração de PDFs
- ✅ **Verifica integridade** dos arquivos do sistema
- ✅ **Configura permissões** adequadas
- ✅ **Mantém templates** padrão funcionando

### **O que é preservado automaticamente:**
- **Dados Críticos:** Proposições, protocolos, assinaturas digitais
- **Arquivos:** PDFs gerados anteriormente
- **Configurações:** DomPDF (`config/dompdf.php`)
- **Recursos:** Diretório de fontes (`storage/fonts`)
- **Estrutura:** Armazenamento (`storage/app/proposicoes/`)
- **Templates:** Padrão (`storage/app/private/templates/`)
- **Segurança:** Permissões de arquivos e diretórios


Principais (destacadas em azul):
  - ${assinatura_digital_info} - Bloco completo da assinatura digital
  - ${qrcode_html} - QR Code para consulta do documento

  Configuráveis:
  - ${assinatura_posicao} - Posição da assinatura (centro, direita, esquerda)
  - ${assinatura_texto} - Texto da assinatura digital
  - ${qrcode_posicao} - Posição do QR Code (centro, direita, esquerda)
  - ${qrcode_texto} - Texto do QR Code
  - ${qrcode_tamanho} - Tamanho do QR Code em pixels
  - ${qrcode_url_formato} - URL de consulta formatada

## 🔧 **Correções Implementadas**

### **✅ Problemas Resolvidos:**
1. **Erro de sintaxe** no `ProposicaoAssinaturaController` (operador `??` em strings)
2. **Fallback para PDFs** quando não há arquivos DOCX disponíveis
3. **Configuração automática** do DomPDF e fontes
4. **Persistência de configurações** entre execuções de `migrate:fresh --seed`

### **📄 Sistema de Fallback para PDFs:**
- **Prioridade 1:** Busca arquivos DOCX para conversão
- **Prioridade 2:** Usa PDFs existentes como fallback
- **Prioridade 3:** Gera PDF via DomPDF com conteúdo do banco

## 🚀 **Como usar:**

### **1. Templates e Variáveis:**
1. Acesse http://localhost:8001/admin/templates
2. Escolha um tipo de proposição e clique em "Editar Template"
3. No painel lateral "Variáveis Disponíveis", procure pela seção "ASSINATURA DIGITAL & QR CODE"
4. Clique nas variáveis para copiá-las
5. Use Ctrl+V para colar no documento

### **2. Geração de PDFs:**
- **Automática:** O sistema gera PDFs automaticamente ao protocolar proposições
- **Manual:** Use o comando `php artisan proposicao:regenerar-pdf {id}` para regenerar PDFs específicos
- **Fallback:** Se não houver DOCX, o sistema usa PDFs existentes automaticamente

### **3. Manutenção:**
- **✅ RECOMENDADO:** Execute `docker exec -it legisinc-app php artisan migrate:fresh-backup --seed`
- **❌ EVITAR:** `docker exec -it legisinc-app php artisan migrate:fresh --seed` (pode perder dados)
- **🛡️ Proteção:** Todos os dados críticos são preservados automaticamente
- **⚙️ Configuração:** O sistema se auto-configura na primeira execução
- **💾 Backup:** Dados salvos em `storage/backups/` para auditoria