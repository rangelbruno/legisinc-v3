# Fix para Constraints NOT NULL em Parlamentars

## Problema
Erro: `SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "telefone" of relation "parlamentars" violates not-null constraint`

## Solução

### Executar a migração que torna os campos opcionais:
```bash
php artisan migrate
```

Esta migração irá tornar os seguintes campos nullable na tabela `parlamentars`:
- `telefone` - pode ser NULL
- `data_nascimento` - pode ser NULL  
- `email` - pode ser NULL

### Verificar se a migração foi aplicada:
```bash
php artisan migrate:status
```

Procure pela migração: `2025_07_25_025011_make_parlamentars_fields_nullable`

## Campos Afetados
Após a migração, estes campos não serão mais obrigatórios:
- **telefone**: Pode ser deixado em branco no formulário
- **data_nascimento**: Campo opcional
- **email**: Pode usar o email do usuário ou deixar vazio

## Teste
Após executar a migração, tente criar um usuário parlamentar novamente:
1. Vá para `/usuarios/create`
2. Selecione perfil "Parlamentar"
3. Preencha os campos obrigatórios
4. Deixe telefone e data de nascimento vazios (se desejar)
5. Submeta o formulário

O erro de constraint NOT NULL não deve mais ocorrer.