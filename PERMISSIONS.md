# Gestão de Permissões - Laravel/Docker

Este documento descreve as soluções implementadas para corrigir automaticamente problemas de permissões que podem ocorrer após executar comandos como `migrate:fresh --seed`.

## 🚨 Problema

Após executar `docker exec -it legisinc-app php artisan migrate:fresh --seed`, podem ocorrer erros de permissão como:

```
file_put_contents(/var/www/html/storage/framework/cache/data/...): Permission denied
```

## ✅ Soluções Implementadas

### 1. Comando Artisan: `fix:permissions`

```bash
# Corrigir permissões manualmente
docker exec legisinc-app php artisan fix:permissions
```

**O que faz:**
- Detecta automaticamente se está em ambiente Docker
- Corrige ownership para usuário `laravel:laravel`
- Define permissões corretas (755/775) nos diretórios críticos
- Limpa caches automaticamente

### 2. Script Wrapper Completo

```bash
# Executar migrate:fresh --seed com correção automática
docker exec -it legisinc-app bash -c './scripts/migrate-fresh-with-permissions.sh'
```

**Processo automatizado:**
1. Executa `migrate:fresh --seed`
2. Automaticamente corrige permissões
3. Verifica sucesso de ambas operações

### 3. Script Shell Independente

```bash
# Apenas corrigir permissões via shell
docker exec legisinc-app ./scripts/fix-permissions.sh
```

## 🔧 Integração Automática

As correções de permissão foram integradas no `composer.json`:

```json
{
  "scripts": {
    "post-create-project-cmd": [
      "@php artisan key:generate --ansi",
      "@php artisan migrate --graceful --ansi",
      "@php artisan fix:permissions"
    ],
    "post-migrate": [
      "@php artisan fix:permissions"
    ]
  }
}
```

## 📂 Diretórios Gerenciados

O sistema corrige automaticamente as permissões dos seguintes diretórios:

- `storage/framework/cache/data/` - Cache do Laravel
- `storage/framework/sessions/` - Sessões
- `storage/framework/views/` - Views compiladas
- `storage/logs/` - Logs da aplicação
- `storage/app/` - Arquivos da aplicação
- `bootstrap/cache/` - Cache de bootstrap

## 🐳 Considerações Docker

- **Usuário PHP-FPM**: `laravel`
- **Permissões padrão**: 755 (diretórios), 775 (escrita necessária)
- **Ownership**: `laravel:laravel`

## 🔄 Uso Recomendado

Para uma nova instalação/reset completo:

```bash
# Método recomendado
docker exec -it legisinc-app bash -c './scripts/migrate-fresh-with-permissions.sh'

# Ou método manual
docker exec -it legisinc-app php artisan migrate:fresh --seed
docker exec legisinc-app php artisan fix:permissions
```

## 🚀 Deployment

Em ambientes de produção, o comando `fix:permissions` pode ser adicionado aos scripts de deploy para garantir permissões corretas após atualizações.