# Setup Inicial do Sistema - LegisInc

## Após Executar Migrations

Para que o sistema funcione completamente, execute os seeders necessários:

### 1. Tipos de Proposição (Obrigatório)
```bash
php artisan db:seed --class=TipoProposicaoSeeder
```

Este seeder cria os 8 tipos padrão de proposições legislativas:
- Projeto de Lei Ordinária
- Projeto de Lei Complementar  
- Proposta de Emenda Constitucional
- Projeto de Decreto Legislativo
- Projeto de Resolução
- Indicação
- Requerimento
- Moção

### 2. Verificar Setup
```bash
php artisan tinker
# No tinker:
App\Models\TipoProposicao::count(); // Deve retornar 8
```

### 3. Outros Seeders (Opcionais)
```bash
# Se existirem outros seeders, execute conforme necessário
php artisan db:seed
```

## Funcionalidades Habilitadas

Após executar os seeders:
- ✅ Dropdown de tipos de proposição funcional
- ✅ Criação de modelos com associação
- ✅ Sistema de documentos completo
- ✅ Interface administrativa operacional

## Solução de Problemas

### Dropdown vazio na criação de modelos
```bash
# Causa: Tipos de proposição não cadastrados
# Solução:
php artisan db:seed --class=TipoProposicaoSeeder
```

### Erro de tabela não existe
```bash
# Causa: Migrations não executadas
# Solução:
php artisan migrate
```