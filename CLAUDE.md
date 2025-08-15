# Sistema Legisinc - Configuração Completa e Definitiva

## 🚀 COMANDO MASTER - RESETAR E CONFIGURAR TUDO

```bash
docker exec -it legisinc-app php artisan migrate:fresh --seed
```

## ✅ O QUE ESTE COMANDO FAZ (100% GARANTIDO):

### 1. **Templates de Proposições (23 tipos)** 
- Cria automaticamente 23 tipos de templates seguindo LC 95/1998
- **Template de Moção** criado com todas as variáveis funcionais
- Arquivos salvos em: `private/templates/`
- **RTF com codificação UTF-8 correta** para acentuação portuguesa
- **Processamento de imagem automático** para admin

### 2. **Dados da Câmara**
- Configura automaticamente os dados padrão:
  - **Nome**: Câmara Municipal Caraguatatuba  
  - **Endereço**: Praça da República, 40, Centro, Caraguatatuba-SP
  - **Telefone**: (12) 3882-5588
  - **Website**: www.camaracaraguatatuba.sp.gov.br
  - **CNPJ**: 50.444.108/0001-41

### 3. **Usuários do Sistema**
- **Admin**: bruno@sistema.gov.br / Senha: 123456
- **Parlamentar**: jessica@sistema.gov.br / Senha: 123456  
- **Legislativo**: joao@sistema.gov.br / Senha: 123456
- **Protocolo**: roberto@sistema.gov.br / Senha: 123456
- **Expediente**: expediente@sistema.gov.br / Senha: 123456
- **Assessor Jurídico**: juridico@sistema.gov.br / Senha: 123456

## 🏛️ Template de Moção - Variáveis Disponíveis

### Cabeçalho
- `${imagem_cabecalho}` - Imagem do cabeçalho
- `${cabecalho_nome_camara}` → **CÂMARA MUNICIPAL DE CARAGUATATUBA**
- `${cabecalho_endereco}` → **Praça da República, 40, Centro**
- `${cabecalho_telefone}` → **(12) 3882-5588**
- `${cabecalho_website}` → **www.camaracaraguatatuba.sp.gov.br**

### Proposição
- `${numero_proposicao}` → **[AGUARDANDO PROTOCOLO]** (até ser protocolado)
- `${numero_proposicao}` → **0001/2025** (após protocolo atribuir número)
- `${ementa}` → Ementa da proposição
- `${texto}` → Conteúdo da proposição (IA ou manual)
- `${justificativa}` → Justificativa (opcional)

### Dados do Autor
- `${autor_nome}` → Nome do parlamentar
- `${autor_cargo}` → **Vereador**

### Data e Local  
- `${municipio}, ${dia} de ${mes_extenso} de ${ano_atual}`
- `${assinatura_padrao}` → **__________________________________**
- `${rodape_texto}` → Texto institucional do rodapé

## 🔄 Fluxo Completo de Funcionamento

1. **Administrador** cria templates com variáveis
2. **Parlamentar** cria proposição tipo "moção"
3. **Sistema** detecta tipo e busca template (ID: 6)
4. **Variáveis** são substituídas (número_proposicao = [AGUARDANDO PROTOCOLO])
5. **Documento** é gerado com estrutura formal
6. **Parlamentar** edita no OnlyOffice com template aplicado
7. **Protocolo** atribui número oficial (ex: 0001/2025)
8. **Sistema** atualiza variável ${numero_proposicao} com número real
9. **Legislativo** recebe documento formatado para análise

## ⚙️ Correções Técnicas Aplicadas

### 1. OnlyOfficeService.php 
- **Template do administrador tem precedência** sobre template ABNT
- **Processamento admin**: Apenas `${imagem_cabecalho}` é convertida para RTF
- **Outras variáveis permanecem como placeholders** em `/admin/templates`
- **Editor parlamentar**: Todas as variáveis são processadas
- **Número de proposição**: Exibe [AGUARDANDO PROTOCOLO] até ser protocolado

### 2. TemplateProcessorService.php
- **Codificação UTF-8 correta** com mb_strlen, mb_substr, mb_ord
- **Conversão RTF Unicode** (\uN*) para acentuação portuguesa
- **Processamento de imagem** PNG/JPG para RTF hexadecimal
- **Método gerarNumeroProposicao**: Verifica `numero_protocolo` antes de gerar

### 3. PreventBackHistory.php
- **Bypass para downloads** do OnlyOffice (BinaryFileResponse/StreamedResponse)

## 📋 Estrutura do Template Final

```rtf
CÂMARA MUNICIPAL DE CARAGUATATUBA
Praça da República, 40, Centro
(12) 3882-5588
www.camaracaraguatatuba.sp.gov.br

MOÇÃO Nº [AGUARDANDO PROTOCOLO]

EMENTA: [Ementa da proposição]

A Câmara Municipal manifesta:

[Texto da proposição criado pelo parlamentar]

[Justificativa se houver]

Resolve dirigir a presente Moção.

Caraguatatuba, 12 de agosto de 2025.

__________________________________
[Nome do Parlamentar]
Vereador
```

## 🎯 Resultado Final Garantido

✅ **Templates funcionando** com todas as variáveis  
✅ **Imagem do cabeçalho** aparecendo corretamente (RTF)
✅ **Acentuação portuguesa** funcionando perfeitamente
✅ **Admin templates**: Imagem + variáveis como placeholders
✅ **Editor parlamentar**: Imagem + todas variáveis substituídas
✅ **Número de proposição**: [AGUARDANDO PROTOCOLO] até protocolar
✅ **Dados da câmara** configurados automaticamente  
✅ **OnlyOffice** integrado e funcional  
✅ **Fluxo parlamentar** → **protocolo** → **legislativo** operacional  
✅ **Permissões** configuradas por perfil  
✅ **Migrate fresh --seed** preserva TODA configuração  

## 🚀 Como Testar

### **Teste Básico de Templates**
1. Execute: `docker exec -it legisinc-app php artisan migrate:fresh --seed`
2. Acesse: http://localhost:8001
3. Login: jessica@sistema.gov.br / 123456 (Parlamentar)
4. Crie uma moção
5. Abra no editor OnlyOffice
6. Verifique se template está aplicado com variáveis substituídas

### **Teste de Salvamento do Legislativo**
7. Faça logout e login: joao@sistema.gov.br / 123456 (Legislativo)
8. Acesse a proposição criada pelo Parlamentar
9. Abra no editor OnlyOffice
10. **Verifique**: Documento carrega com conteúdo do Parlamentar (não template)
11. Faça alterações e salve
12. Reabra o documento
13. **Confirme**: Suas alterações foram preservadas ✅

## 📝 Nota Importante sobre Templates Admin

Após executar `migrate:fresh --seed`, os templates são criados mas a imagem não aparece imediatamente em `/admin/templates`.

**Solução Automática**: O comando já executa automaticamente o processamento das imagens.

**Solução Manual** (se necessário):
```bash
docker exec -it legisinc-app php artisan templates:process-images
```

Isso processa a variável `${imagem_cabecalho}` para RTF em todos os templates, mantendo as outras variáveis como placeholders.

## 🔒 CONFIGURAÇÃO PERMANENTE

### Arquivos Críticos do Sistema:
- `/app/Services/OnlyOffice/OnlyOfficeService.php` - Processamento templates
- `/app/Services/Template/TemplateProcessorService.php` - Variáveis e RTF
- `/database/seeders/TipoProposicaoTemplatesSeeder.php` - Templates base
- `/database/seeders/ParametrosTemplatesSeeder.php` - Parâmetros padrão
- `/database/seeders/DatabaseSeeder.php` - Orquestrador principal

### Imagem Padrão:
- **Localização**: `/public/template/cabecalho.png`
- **Tamanho**: 503x99 pixels
- **Formato**: PNG
- **Processamento**: Automático para RTF

---

## 📌 IMPORTANTE: Numeração de Proposições

**A variável `${numero_proposicao}` segue o fluxo legislativo correto:**

1. **Criação da proposição**: Exibe `[AGUARDANDO PROTOCOLO]`
2. **Após protocolar**: Exibe o número oficial (ex: `0001/2025`)
3. **Apenas o Protocolo** pode atribuir números oficiais
4. **Sistema não gera números automaticamente** (respeitando processo legislativo)

---

## 🔧 CORREÇÕES IMPLEMENTADAS: Salvamento do Legislativo

### ✅ **PROBLEMA RESOLVIDO**: Usuário Legislativo pode salvar alterações

**Situação Anterior**: Legislativo não conseguia salvar edições no OnlyOffice (erro 403 ou arquivos não carregando)

**Correções Aplicadas**:

#### 1. **Lógica de Detecção de Conteúdo IA** (`OnlyOfficeService.php:1847-1856`)
- ❌ **Antes**: Qualquer conteúdo > 200 caracteres era flagado como "IA"
- ✅ **Agora**: Apenas conteúdo com palavras-chave específicas E sem arquivo salvo
- ✅ **Resultado**: Arquivos salvos têm prioridade absoluta sobre templates

#### 2. **Storage Disk Unificado** (`OnlyOfficeService.php:2927-2931`)
- ❌ **Antes**: Callbacks salvavam em disk "private", download buscava em "local"
- ✅ **Agora**: Ambos usam disk "local" consistentemente
- ✅ **Resultado**: Salvamento e carregamento funcionam corretamente

#### 3. **Busca Robusta de Arquivos** (`OnlyOfficeService.php:1898-1914`)
- ❌ **Antes**: Buscava apenas em 2 localizações
- ✅ **Agora**: Verifica 3 localizações (local/private/public)
- ✅ **Resultado**: Encontra arquivos independente de onde foram salvos

### 🎯 **Fluxo Operacional Garantido**

1. **Parlamentar** cria proposição → Template aplicado ✅
2. **OnlyOffice** callback salva arquivo → Arquivo em `storage/app/proposicoes/` ✅
3. **Legislativo** acessa documento → Carrega arquivo salvo (não template) ✅
4. **Legislativo** faz alterações → Callback salva alterações ✅
5. **Próximo acesso** → Carrega arquivo alterado pelo Legislativo ✅

### 📊 **Evidências de Funcionamento**

- **Log Correto**: `"Usando arquivo salvo da proposição" {"tem_conteudo_ia":false}`
- **Download Funcionando**: Arquivos RTF/DOCX editados sendo retornados
- **Callbacks Operacionais**: `"Arquivo atualizado sem modificar conteúdo original"`
- **Colaboração Ativa**: Legislativo pode editar proposições de Parlamentares

### 🔄 **Compatibilidade com migrate:fresh --seed**

Todas as correções estão no código-fonte e são preservadas automaticamente:
- ✅ `OnlyOfficeService.php` - Lógica principal corrigida
- ✅ Nenhuma configuração de banco necessária
- ✅ Funciona imediatamente após o comando

---

**🎊 CONFIGURAÇÃO 100% PRESERVADA APÓS `migrate:fresh --seed`** ✅

**Última atualização**: 15/08/2025
**Versão estável**: v1.2
**Status**: PRODUÇÃO