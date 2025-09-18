# 🚀 LegisInc - Sistema de Gestão Legislativa

## 🐳 **Configuração com Docker Compose**

### **1. Iniciar o Sistema**
```bash
# Iniciar todos os containers
docker compose up -d

# Aguardar containers ficarem saudáveis (postgres, redis, onlyoffice)
# O sistema verifica automaticamente a saúde dos serviços
```

### **2. Configuração Inicial (Primeira Execução)**
```bash
# Instalar dependências (se necessário)
docker exec legisinc-app composer install

# Executar migração segura com seeders
docker exec legisinc-app php artisan migrate:safe --fresh --seed --generate-seeders
```

### ✅ **Comando Principal - Migrate Safe v2.3**
```bash
docker exec legisinc-app php artisan migrate:safe --fresh --seed --generate-seeders
```

**🛡️ Sistema de Migração Segura v2.3 inclui:**
- ✅ **Auto-correção de permissões** de storage
- ✅ **Auto-correção de namespaces** de seeders
- ✅ **Auto-limpeza** de cache e views após migration
- ✅ **Auto-criação** de log files com permissões corretas
- ✅ **Auto-preservação** de melhorias e correções críticas
- ✅ **23 Templates de Proposições** configurados automaticamente
- ✅ **Dados da Câmara** de Caraguatatuba pré-configurados
- ✅ **6 Usuários do sistema** com diferentes roles

## 🎯 **Acesso ao Sistema**

### **URLs de Acesso:**
- **Sistema Principal:** http://localhost:8001
- **OnlyOffice:** http://localhost:8088 (integrado automaticamente)

### **Credenciais de Acesso:**
| Usuário | E-mail | Senha | Permissões |
|---------|--------|-------|------------|
| **Admin** | bruno@sistema.gov.br | 123456 | Administração completa |
| **Parlamentar** | jessica@sistema.gov.br | 123456 | Criar/editar proposições |
| **Legislativo** | joao@sistema.gov.br | 123456 | Revisar proposições |
| **Protocolo** | roberto@sistema.gov.br | 123456 | Protocolar documentos |
| **Expediente** | expediente@sistema.gov.br | 123456 | Gestão de expediente |
| **Assessor Jurídico** | juridico@sistema.gov.br | 123456 | Parecer jurídico |

## 🏛️ **Dados da Câmara Configurados**
- **Nome:** Câmara Municipal de Caraguatatuba
- **Endereço:** Praça da República, 40, Centro, Caraguatatuba-SP
- **Telefone:** (12) 3882-5588
- **Website:** www.camaracaraguatatuba.sp.gov.br
- **CNPJ:** 50.444.108/0001-41

## 🐳 **Arquitetura Docker**

### **Containers em Execução:**
```yaml
legisinc-app         # Aplicação Laravel (PHP 8.2 + Nginx)
legisinc-postgres    # Banco de dados PostgreSQL 16
legisinc-redis       # Cache Redis 7-alpine
legisinc-onlyoffice  # Editor de documentos OnlyOffice
```

### **Volumes Persistentes:**
- `postgres_data` - Dados do banco de dados
- `redis_data` - Cache Redis
- `onlyoffice_data` - Documentos OnlyOffice
- `onlyoffice_logs` - Logs do OnlyOffice
- `onlyoffice_cache` - Cache do OnlyOffice

### **Rede:**
- `legisinc_network` - Rede bridge para comunicação entre containers

## ✨ **Funcionalidades Principais**

### **📝 Gestão de Proposições:**
- Criação com templates pré-configurados
- Editor OnlyOffice integrado
- Fluxo completo de tramitação
- Assinatura digital com certificado
- Geração automática de PDFs

### **🔄 Fluxo de Trabalho:**
1. **Parlamentar** cria proposição
2. **Sistema** aplica template automaticamente
3. **OnlyOffice** permite edição colaborativa
4. **Protocolo** atribui número oficial
5. **Legislativo** revisa e aprova
6. **Assinatura digital** finaliza o processo

### **🎨 Templates Disponíveis:**
- 23 tipos de proposições configurados
- Variáveis dinâmicas automáticas
- Cabeçalho institucional com brasão
- Formatação LC 95/1998
- RTF com codificação UTF-8

## 🔧 **Comandos Úteis**

### **Gerenciamento de Containers:**
```bash
# Ver status dos containers
docker compose ps

# Ver logs da aplicação
docker compose logs -f app

# Reiniciar todos os serviços
docker compose restart

# Parar todos os serviços
docker compose down

# Remover tudo (incluindo volumes)
docker compose down -v
```

### **Comandos Artisan:**
```bash
# Limpar cache
docker exec legisinc-app php artisan optimize:clear

# Gerar chave da aplicação
docker exec legisinc-app php artisan key:generate

# Ver rotas disponíveis
docker exec legisinc-app php artisan route:list

# Executar tinker (console interativo)
docker exec legisinc-app php artisan tinker
```

### **Backup e Restauração:**
```bash
# Backup do banco de dados
docker exec legisinc-postgres pg_dump -U legisinc legisinc_db > backup.sql

# Restaurar banco de dados
docker exec -i legisinc-postgres psql -U legisinc legisinc_db < backup.sql
```

## 🚀 **Troubleshooting**

### **Problema: Container não inicia**
```bash
# Verificar logs
docker compose logs app

# Reconstruir imagem
docker compose build --no-cache app
docker compose up -d
```

### **Problema: Erro de permissões**
```bash
# Corrigir permissões do storage
docker exec legisinc-app chmod -R 775 storage bootstrap/cache
docker exec legisinc-app chown -R laravel:laravel storage bootstrap/cache
```

### **Problema: OnlyOffice não conecta**
```bash
# Verificar status do OnlyOffice
docker compose ps onlyoffice

# Reiniciar OnlyOffice
docker compose restart onlyoffice
```

## 📚 **Documentação Adicional**
- Detalhes técnicos em `/docs/technical/`
- Scripts de teste em `/scripts/tests/`
- Configurações em `/CLAUDE.md`