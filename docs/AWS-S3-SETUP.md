# Configuração AWS S3 para Exportação de PDFs

## 🔐 Configuração Segura

### 1. Variáveis de Ambiente

As credenciais AWS devem ser configuradas no arquivo `.env.local` (que **NÃO** deve ser commitado):

```bash
# Copie o arquivo exemplo
cp .env.local.example .env.local

# Edite com suas credenciais reais
AWS_ACCESS_KEY_ID=sua_access_key_aqui
AWS_SECRET_ACCESS_KEY=sua_secret_key_aqui
AWS_DEFAULT_REGION=sa-east-1
AWS_BUCKET=seu_bucket
AWS_ENDPOINT_URL=https://s3.sa-east-1.amazonaws.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### 2. Permissões IAM Necessárias

O usuário AWS deve ter as seguintes permissões no bucket:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::seu-bucket-name",
                "arn:aws:s3:::seu-bucket-name/*"
            ]
        }
    ]
}
```

### 3. Estrutura de Arquivos S3

Os PDFs são organizados da seguinte forma:

```
bucket/
└── proposicoes/
    └── pdfs/
        └── YYYY/          # Ano
            └── MM/        # Mês
                └── DD/    # Dia
                    └── {id}/      # ID da proposição
                        ├── manual/      # Exportações manuais
                        ├── automatic/   # Exportações automáticas
                        ├── upload/      # Uploads diretos
                        └── download/    # Downloads OnlyOffice
```

### 4. Funcionalidades Implementadas

- ✅ **Exportação Manual**: Botão "Exportar PDF para S3" no editor
- ✅ **Exportação Automática**: Durante aprovação de proposições
- ✅ **Estrutura Organizada**: Hierárquica por data e tipo
- ✅ **URLs Temporárias**: Válidas por 1 hora
- ✅ **Logs Detalhados**: Para auditoria e debugging

### 5. Segurança

- ❌ **NUNCA** comite credenciais AWS no repositório
- ✅ Use `.env.local` para desenvolvimento local
- ✅ Use variáveis de ambiente do servidor em produção
- ✅ Implemente rotação regular de credenciais
- ✅ Use IAM roles em EC2/ECS quando possível

### 6. Teste da Configuração

Execute o script de teste para verificar a conectividade:

```bash
php test-s3-connection.php
```

### 7. Resolução de Problemas

**Erro de permissões**: Verifique as policies IAM
**Erro de conectividade**: Verifique região e endpoint
**Arquivo não encontrado**: Verifique se `.env.local` existe e está configurado

Para mais detalhes, consulte a documentação em `docs/EXPORTACAO-PDF-ONLYOFFICE.md`.