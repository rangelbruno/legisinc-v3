# Estrutura de Organização S3 - PDFs de Proposições

## 📁 Nova Estrutura Hierárquica

```
legisinc/
└── proposicoes/
    └── pdfs/
        └── YYYY/          # Ano (ex: 2025)
            └── MM/        # Mês (ex: 01)
                └── DD/    # Dia (ex: 23)
                    └── {proposicao_id}/    # ID da proposição
                        ├── manual/         # Exportações manuais
                        ├── automatic/      # Exportações automáticas (aprovação)
                        ├── upload/         # Uploads diretos de arquivo
                        └── download/       # Downloads via URL OnlyOffice
```

## 🎯 Exemplos de Caminhos

### Exportação Manual
```
proposicoes/pdfs/2025/01/23/1/manual/proposicao_1_manual_1758637500.pdf
```

### Exportação Automática (Aprovação)
```
proposicoes/pdfs/2025/01/23/1/automatic/proposicao_1_auto_1758637500.pdf
```

### Upload Direto
```
proposicoes/pdfs/2025/01/23/1/upload/proposicao_1_upload_1758637500.pdf
```

### Download OnlyOffice
```
proposicoes/pdfs/2025/01/23/1/download/proposicao_1_download_1758637500.pdf
```

## ✅ Vantagens da Nova Estrutura

### 1. **Organização Temporal**
- Facilita busca por período
- Permite limpeza automática por data
- Backup incremental por tempo

### 2. **Separação por Tipo**
- `manual/` - Exportações manuais pelo usuário
- `automatic/` - Exportações automáticas na aprovação
- `upload/` - Uploads diretos de arquivos
- `download/` - Downloads via URL do OnlyOffice

### 3. **Escalabilidade**
- Evita muitos arquivos numa pasta
- Performance melhor em listagens
- Facilita manutenção

### 4. **Auditoria e Rastreabilidade**
- Fácil identificação da origem do arquivo
- Controle de versões por timestamp
- Logs mais organizados

## 🔧 Métodos Atualizados

### OnlyOfficeController.php
- `exportarPDFParaS3()` - Exportação manual
- `exportarPDFParaS3Automatico()` - Exportação automática
- `uploadPDFToS3FromFile()` - Upload direto
- `downloadPDFFromOnlyOfficeAndUploadToS3()` - Download OnlyOffice

## 📊 Nomenclatura de Arquivos

Padrão: `proposicao_{id}_{tipo}_{timestamp}.pdf`

- **{id}** - ID da proposição
- **{tipo}** - manual, auto, upload, download
- **{timestamp}** - Unix timestamp para unicidade

## 🗂️ Exemplo de Estrutura Completa

```
legisinc/
└── proposicoes/
    └── pdfs/
        └── 2025/
            ├── 01/
            │   ├── 20/
            │   │   └── 1/
            │   │       ├── manual/
            │   │       │   └── proposicao_1_manual_1758500000.pdf
            │   │       └── automatic/
            │   │           └── proposicao_1_auto_1758510000.pdf
            │   └── 23/
            │       └── 2/
            │           ├── upload/
            │           │   └── proposicao_2_upload_1758637500.pdf
            │           └── download/
            │               └── proposicao_2_download_1758637600.pdf
            └── 02/
                └── 01/
                    └── 3/
                        └── manual/
                            └── proposicao_3_manual_1759000000.pdf
```

Esta estrutura segue as melhores práticas de organização de arquivos em cloud storage, facilitando manutenção, backup e auditoria.