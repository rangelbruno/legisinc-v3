# Setup de Roles para o Sistema de Usuários

## Problema
O erro "There is no role named `PARLAMENTAR` for guard `web`" indica que as roles necessárias não foram criadas no banco de dados.

## Solução Rápida

### Opção 1: Comando Artisan (Recomendado)
```bash
php artisan roles:ensure
```

Este comando irá:
- Verificar se todas as roles necessárias existem
- Criar as roles que estão faltando
- Listar todas as roles disponíveis

### Opção 2: Executar o Seeder
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### Opção 3: Reset completo (se necessário)
```bash
php artisan migrate:fresh --seed
```

## Roles Necessárias
O sistema precisa das seguintes roles:
- `ADMIN` - Administrador
- `LEGISLATIVO` - Servidor Legislativo  
- `PARLAMENTAR` - Parlamentar
- `RELATOR` - Relator
- `PROTOCOLO` - Protocolo
- `ASSESSOR` - Assessor
- `CIDADAO_VERIFICADO` - Cidadão Verificado
- `PUBLICO` - Público

## Verificação
Para verificar se as roles foram criadas:
```bash
php artisan tinker
>>> \Spatie\Permission\Models\Role::pluck('name')->toArray();
```

## Notas
- O UserService agora cria automaticamente roles que não existem
- Os formulários incluem melhor tratamento de erros com alertas visuais
- O comando `roles:ensure` pode ser executado quantas vezes necessário sem problemas