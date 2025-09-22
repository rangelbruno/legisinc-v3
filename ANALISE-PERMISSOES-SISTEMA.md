# 📋 Análise Completa de Permissões do Sistema LegisInc

## 🔍 Resumo Executivo

Este documento apresenta uma análise detalhada de todas as permissões do sistema LegisInc, desde a pasta storage do Laravel até os containers Docker, identificando problemas e propondo soluções para garantir segurança e funcionamento adequado.

## 🗂️ 1. Estrutura de Permissões - Pasta Storage

### 1.1 Diretório Principal
```
📁 /home/bruno/legisinc-v2/storage/ (bruno:bruno 775)
├── 📁 app/ (bruno:bruno 775)
├── 📁 backups/ (bruno:bruno 775)
├── 📁 fonts/ (bruno:bruno 775)
├── 📁 framework/ (bruno:bruno 775)
└── 📁 logs/ (bruno:bruno 775)
```

### 1.2 Subdiretórios Críticos

#### **storage/app/private/**
```
📁 private/ (bruno:bruno 775)
├── 📁 proposicoes/ (bruno:bruno 775)
│   ├── 📁 pdfs/1/ (bruno:bruno 700) ⚠️ PROBLEMA
│   ├── 📁 pdfs/2/ (bruno:bruno 775) ✅
│   └── 📁 pdfs/[outros]/ (bruno:bruno 775) ✅
├── 📁 certificados-digitais/ (bruno:bruno 775) ✅
└── 📁 templates/ (bruno:bruno 775) ✅
```

#### **storage/framework/cache/**
```
📁 cache/ (bruno:bruno 775)
├── 📁 data/ (bruno:bruno 775)
│   ├── 📁 de/b9/ (root:root 755) ⚠️ PROBLEMA
│   ├── 📁 82/72/ (root:root 755) ⚠️ PROBLEMA
│   └── 📁 [outros]/ (bruno:bruno 755) ✅
└── 📁 pdf-assinatura/ (bruno:bruno 775) ✅
```

## 🐳 2. Containers Docker - Análise de Usuários

### 2.1 Container Principal (legisinc-app)

#### **Usuários e Processos**
```
ROOT PROCESS:     root (UID 0) - supervisord, php-fpm master
NGINX WORKERS:    nginx (UID configurado)
PHP-FPM WORKERS:  laravel:laravel (UID 1000:1000)
WEBSERVER USER:   laravel (UID 1000)
```

#### **Configuração PHP-FPM**
```ini
user = laravel
group = laravel
```

#### **Storage Mapeado no Container**
```
/var/www/html/storage/ → laravel:laravel (1000:1000)
```

### 2.2 Container OnlyOffice (legisinc-onlyoffice)

#### **Usuários**
```
ROOT PROCESS:   root (UID 0)
SERVICE USER:   ds:ds (UID 109:112)
DATA DIRECTORY: /var/lib/onlyoffice/documentserver/ (ds:ds)
LOG DIRECTORY:  /var/log/onlyoffice/ (ds:ds)
```

## 🔗 3. Mapeamento de UIDs/GIDs

### 3.1 Correspondência Host ↔ Container
```
HOST SYSTEM          →  CONTAINER (legisinc-app)
bruno (1000:1000)    →  laravel (1000:1000)     ✅ MATCH
root (0:0)           →  root (0:0)              ✅ MATCH
```

### 3.2 Vantagens do Mapeamento
- ✅ Arquivos criados no container mantêm ownership correto no host
- ✅ Permissões preservadas entre host e container
- ✅ Facilita backup e manutenção

## 📄 4. Análise de Arquivos PDF e Documentos

### 4.1 Permissões dos PDFs
```bash
# PDFs Normais (maioria)
-rw-rw-r-- bruno:bruno (664) ✅ CORRETO

# PDFs com Problemas
-rw-r--r-- root:root (644)   ⚠️ PROBLEMA
```

### 4.2 Localização dos PDFs
```
📁 storage/app/private/proposicoes/pdfs/
├── 📁 1/ - proposicao_1.pdf (bruno:bruno 664) ✅
├── 📁 2/ - proposicao_2.pdf (root:root 644) ⚠️
├── 📁 3/ - múltiplos PDFs (bruno:bruno 664) ✅
└── 📁 [outros]/ - status variado
```

## 🔐 5. Certificados Digitais

### 5.1 Localização e Permissões
```
📁 storage/app/private/certificados-digitais/
└── 📄 certificado_2_1758331089.pfx (bruno:bruno 664) ✅
```

### 5.2 Segurança dos Certificados
- ✅ Localização em pasta private/
- ✅ Permissões adequadas (664)
- ✅ Acesso controlado via aplicação
- ✅ Fora do webroot público

## ⚠️ 6. Problemas Identificados

### 6.1 Críticos

1. **Pasta com Permissão Restritiva**
   ```
   storage/app/private/proposicoes/pdfs/1/ (700)
   ```
   - **Problema:** Apenas owner pode acessar
   - **Impacto:** Webserver pode não conseguir servir arquivos
   - **Solução:** Alterar para 755

2. **Arquivos Cache como Root**
   ```
   storage/framework/cache/data/de/b9/ (root:root)
   storage/framework/cache/data/82/72/ (root:root)
   ```
   - **Problema:** Comandos executados como root
   - **Impacto:** Problemas de permissão em operações futuras
   - **Solução:** Chown para bruno:bruno

### 6.2 Menores

3. **PDF com Owner Root**
   ```
   storage/app/private/proposicoes/pdfs/2/proposicao_2.pdf (root:root)
   ```
   - **Problema:** Gerado por processo root
   - **Impacto:** Inconsistência de ownership
   - **Solução:** Chown para bruno:bruno

## 🛠️ 7. Soluções Recomendadas

### 7.1 Correções Imediatas

```bash
# 1. Corrigir pasta restritiva
chmod 755 storage/app/private/proposicoes/pdfs/1/

# 2. Corrigir ownership do cache
sudo chown -R bruno:bruno storage/framework/cache/data/

# 3. Corrigir PDF específico
sudo chown bruno:bruno storage/app/private/proposicoes/pdfs/2/proposicao_2.pdf

# 4. Normalizar todas as permissões
find storage/ -type d -exec chmod 755 {} \;
find storage/ -type f -exec chmod 644 {} \;
```

### 7.2 Script de Verificação

```bash
#!/bin/bash
# script/check-permissions.sh

echo "🔍 Verificando permissões do sistema..."

# Verificar pastas com permissões restritivas
echo "📁 Pastas com permissão 700:"
find storage/ -type d -perm 700

# Verificar arquivos/pastas com owner root
echo "👑 Arquivos/pastas com owner root:"
find storage/ ! -user bruno

# Verificar consistência PHP-FPM
echo "🐘 Usuário PHP-FPM:"
docker exec legisinc-app ps aux | grep php-fpm | head -1

echo "✅ Verificação concluída!"
```

### 7.3 Prevenção de Problemas

1. **Evitar comandos como root**
   - Usar sempre `docker exec legisinc-app` (roda como laravel)
   - Evitar `sudo` desnecessário no host

2. **Monitoramento automático**
   - Script de verificação diária
   - Alertas para arquivos com owner incorreto

3. **Padronização de processos**
   - Documentar procedimentos de deploy
   - Estabelecer ownership padrão para novos arquivos

## ✅ 8. Aspectos Funcionais Corretos

### 8.1 Segurança Adequada

- ✅ **PHP-FPM não roda como root** (roda como laravel)
- ✅ **OnlyOffice isolado** com usuário próprio (ds)
- ✅ **Certificados protegidos** em pasta private
- ✅ **Mapeamento UID consistente** (1000:1000)

### 8.2 Arquitetura Sólida

- ✅ **Separação de containers** bem definida
- ✅ **Volumes mapeados corretamente**
- ✅ **Processo de escalabilidade** preservado
- ✅ **Backup facilitado** devido ao ownership consistente

## 📊 9. Resumo por Severidade

### 🔴 Alta Prioridade
1. Pasta `pdfs/1/` com permissão 700
2. Arquivos cache com owner root

### 🟡 Média Prioridade
1. PDF específico com owner root
2. Implementar script de monitoramento

### 🟢 Baixa Prioridade
1. Padronização geral de permissões
2. Documentação de procedimentos

## 🔄 10. Procedimento de Manutenção

### 10.1 Verificação Semanal
```bash
# Executar script de verificação
./scripts/check-permissions.sh

# Corrigir problemas encontrados
./scripts/fix-permissions.sh
```

### 10.2 Após Deploy
```bash
# Garantir ownership correto
sudo chown -R bruno:bruno storage/
find storage/ -type d -exec chmod 755 {} \;
find storage/ -type f -exec chmod 644 {} \;
```

## 📞 11. Contatos e Referências

**Responsáveis:**
- Administração do Sistema: Bruno
- Desenvolvimento: Equipe Laravel
- Infraestrutura: Docker/OnlyOffice

**Documentos Relacionados:**
- `docker-compose.yml` - Configuração de containers
- `COMO-USAR-MCP-ONLYOFFICE.md` - Integração OnlyOffice
- `MELHORIAS-AUTOMATICAS.md` - Melhorias implementadas

---

**Última atualização:** 2025-09-21
**Versão:** 1.0
**Status:** 🟡 Requer correções identificadas