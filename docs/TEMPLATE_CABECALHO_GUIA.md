# Guia do Sistema de Cabeçalho Padrão para Templates

## 📋 Visão Geral

O sistema de cabeçalho padrão permite configurar uma imagem que será automaticamente incluída em todas as proposições legislativas. Isso facilita a padronização visual e permite que diferentes câmaras municipais utilizem suas próprias identidades visuais.

## 🚀 Como Funciona

### 1. Configuração do Cabeçalho

Acesse: **Administração > Parâmetros > Templates > Configurar**

Ou diretamente pela URL: `/admin/parametros/templates/cabecalho`

### 2. Funcionalidades Disponíveis

#### Upload da Imagem
- **Formatos aceitos**: PNG, JPG, JPEG
- **Tamanho máximo**: 2MB
- **Dimensões recomendadas**: 800x200 pixels
- **Localização**: `/public/template/cabecalho.png` (ou extensão correspondente)

#### Configurações Disponíveis
- **Usar Cabeçalho Padrão**: Liga/desliga a aplicação automática
- **Altura do Cabeçalho**: Define a altura em pixels (50-300px)
- **Posição do Cabeçalho**: 
  - Topo do documento
  - Cabeçalho da página
  - Marca d'água

### 3. Como Trocar o Cabeçalho da Câmara

1. Acesse a tela de configuração de Templates
2. Clique no ícone de editar na imagem atual
3. Selecione sua nova imagem (logo da câmara)
4. Configure as opções desejadas
5. Clique em "Salvar Configurações"

## 🔧 Implementação Técnica

### Variáveis de Template

O sistema adiciona automaticamente a variável `${imagem_cabecalho}` que pode ser usada nos templates:

```rtf
{\pict\wmetafile8\picw15240\pich3810
${imagem_cabecalho}
}
```

### Service Layer

O `TemplateProcessorService` foi modificado para:
- Carregar configurações dos parâmetros
- Substituir automaticamente a variável de cabeçalho
- Aplicar configurações de altura e posição

### Upload de Arquivos

Novo endpoint: `POST /images/upload/cabecalho`
- Valida tipo e tamanho do arquivo
- Salva na pasta `storage/app/public/template/`
- Atualiza automaticamente os parâmetros

## 📁 Estrutura de Arquivos

```
/public/template/
├── cabecalho.png          # Arquivo padrão
└── cabecalho.jpg          # Arquivo customizado (após upload)

/storage/app/public/template/
├── cabecalho.png          # Backup do arquivo customizado
└── cabecalho.jpg          # Arquivo customizado persistente
```

## 🗄️ Estrutura do Banco de Dados

### Tabelas Utilizadas
- `parametros_modulos`: Módulo "Templates"
- `parametros_submodulos`: Submódulo "Cabeçalho"  
- `parametros_campos`: Campos de configuração
- `parametros_valores`: Valores salvos das configurações

### Campos Criados
- `cabecalho_imagem`: Caminho da imagem
- `usar_cabecalho_padrao`: Boolean para ativar/desativar
- `cabecalho_altura`: Altura em pixels
- `cabecalho_posicao`: Posição no documento

## 🎯 Benefícios

### Para Administradores
- Interface visual amigável
- Upload simples de arquivos
- Configuração centralizada
- Aplicação automática em todas as proposições

### Para Diferentes Câmaras
- Personalização fácil da identidade visual
- Apenas trocar uma imagem para mudar todos os documentos
- Manutenção simplificada
- Consistência visual automática

## 🔄 Fluxo de Uso

1. **Instalação Inicial**: Sistema vem com imagem padrão
2. **Customização**: Administrador faz upload da logo da câmara
3. **Aplicação Automática**: Todas as novas proposições usam o novo cabeçalho
4. **Manutenção**: Mudanças futuras são aplicadas centralmente

## 🛠️ Manutenção

### Backup da Configuração
```bash
# Exportar configurações atuais
php artisan parametros:export --modulo=Templates

# Restaurar configurações
php artisan parametros:import backup.json
```

### Limpeza de Cache
```bash
# Limpar cache de parâmetros
php artisan parametros:clear-cache
```

## 📊 Monitoramento

O sistema registra automaticamente:
- Uploads de novas imagens
- Mudanças nas configurações
- Usuário responsável pelas alterações
- Timestamp das modificações

## 🚨 Troubleshooting

### Imagem não aparece
1. Verificar se o arquivo existe em `/public/template/`
2. Verificar permissões da pasta
3. Limpar cache de parâmetros

### Upload falha
1. Verificar tamanho do arquivo (máx 2MB)
2. Verificar formato (PNG, JPG, JPEG)
3. Verificar permissões de escrita

### Configurações não salvam
1. Verificar permissões do usuário
2. Verificar conexão com banco de dados
3. Verificar logs de erro

## 📞 Suporte

Para problemas ou dúvidas:
1. Verificar logs em `/storage/logs/laravel.log`
2. Consultar documentação técnica
3. Contatar suporte técnico

---

**Última atualização**: {{ date('d/m/Y') }}
**Versão**: 1.0.0