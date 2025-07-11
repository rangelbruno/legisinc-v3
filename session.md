# MÓDULO 5 - SESSÕES E XML EXPORT - INSTRUÇÕES PARA CLAUDE CODE

## OBJETIVO DO MÓDULO
Criar um sistema de gestão de sessões parlamentares que gera XMLs padronizados para exportação a um sistema externo de votação. O foco é exclusivamente no gerenciamento de sessões e geração de documentos XML - NÃO vamos implementar votação interna.

## FUNCIONALIDADES PRINCIPAIS

### 1. GESTÃO DE SESSÕES PARLAMENTARES
- **CRUD completo**: Criar, visualizar, editar e excluir sessões
- **Tipos de sessão**: Ordinária (id: 8), Extraordinária (id: 9), Solene (id: 10)
- **Campos obrigatórios**: número, ano, data, hora, tipo
- **Status de sessão**: preparacao → agendada → exportada → concluida

### 2. GESTÃO DE MATÉRIAS POR SESSÃO
- **Adicionar matérias**: Associar projetos, requerimentos, indicações à sessão
- **Tipos de matéria**: 
  - Correspondência Recebida (id: 109)
  - Projeto de Lei (id: 135)
  - Projeto de Resolução (id: 138)
  - Requerimento (id: 140)
  - Indicação (id: 141)
- **Fases de tramitação**: Leitura (13), 1ª Discussão (14), 2ª Discussão (15), etc.
- **Metadados**: autor, regime, quorum, arquivos anexos

### 3. GERAÇÃO DE XML PADRONIZADO
- **Dois tipos de documento**: 
  - Expediente (id: 144) - contém Leitura e 1ª Discussão
  - Ordem do Dia (id: 145) - contém discussões e votações
- **Estrutura XML**: Seguir exatamente o padrão fornecido nos exemplos
- **Agrupamento**: Matérias agrupadas por fase de tramitação

### 4. EXPORTAÇÃO PARA SISTEMA EXTERNO
- **Preview XML**: Visualizar antes de exportar
- **Envio**: Transmitir XML para sistema de votação externo
- **Histórico**: Registrar todas as exportações realizadas
- **Backup**: Armazenar cópias locais dos XMLs gerados

## ANÁLISE DO PADRÃO XML

### ESTRUTURA DA SESSÃO (Elemento Raiz)
```xml
<sessao id="{session_id}">
  <!-- Tipo da sessão -->
  <tipo id="{type_id}">
    <descricao>{session_type_name}</descricao>
  </tipo>
  
  <!-- Identificação da sessão -->
  <numero>{session_number}</numero>
  <ano>{year}</ano>
  <data>{date_yyyy-mm-dd}</data>
  <hora>{time_hh:mm:ss}.0000000-03:00</hora>
  <horaInicial>{start_time}</horaInicial>
  <horaFinal>{end_time}</horaFinal>
  
  <!-- Documento da sessão (Expediente ou Ordem do Dia) -->
  <sessao-documento id="{document_id}">
    <tipo id="{document_type_id}">
      <descricao>{document_type_name}</descricao>
    </tipo>
    <numero>{session_number}</numero>
    <ano>{year}</ano>
    <data>{date}</data>
    <observacoes>{observations}</observacoes>
  </sessao-documento>
  
  <!-- Fases com matérias agrupadas -->
  <fases>
    <fase id="{phase_id}" valor="{phase_name}">
      <materias>
        <!-- Lista de matérias desta fase -->
      </materias>
    </fase>
  </fases>
</sessao>
```

### ESTRUTURA DA MATÉRIA (Dentro de cada fase)
```xml
<materia id="{matter_id}">
  <!-- Tipo da matéria -->
  <tipo id="{matter_type_id}">
    <descricao>{matter_type_name}</descricao>
  </tipo>
  
  <!-- Identificação da matéria -->
  <numero>{matter_number}</numero>
  <ano>{year}</ano>
  <data>{date_yyyy-mm-dd}</data>
  <descricao>{matter_description}</descricao>
  <assunto>{matter_subject}</assunto>
  
  <!-- Regime de tramitação (opcional) -->
  <regime id="{regime_id}">
    <descricao>{regime_name}</descricao>
  </regime>
  
  <!-- Quorum necessário (opcional) -->
  <quorum id="{quorum_id}">
    <descricao>{quorum_name}</descricao>
  </quorum>
  
  <!-- Autoria (pode ter múltiplos autores) -->
  <autoria>
    <autor id="{author_id}">
      <nome>{author_full_name}</nome>
      <apelido>{author_nickname}</apelido>
      <usar-apelido>{true|false}</usar-apelido>
      <iniciativa>{initiative_type}</iniciativa>
    </autor>
  </autoria>
  
  <!-- Arquivos anexos (opcional) -->
  <arquivos>
    <arquivo id="{file_id}">
      <descricao>{file_description}</descricao>
      <data>{file_date_iso}</data>
      <tamanho>{file_size_bytes}</tamanho>
      <extensao>{file_extension}</extensao>
      <url>{file_url}</url>
      <ordem>{file_order}</ordem>
    </arquivo>
  </arquivos>
  
  <!-- Composição/Classificação (opcional) -->
  <composicao id="{composition_id}">
    <descricao>{composition_name}</descricao>
  </composicao>
</materia>
```

## DADOS DE REFERÊNCIA OBRIGATÓRIOS

### TIPOS DE SESSÃO
```
ID: 8  | Descrição: Ordinária
ID: 9  | Descrição: Extraordinária  
ID: 10 | Descrição: Solene
```

### TIPOS DE DOCUMENTO
```
ID: 144 | Descrição: Expediente
ID: 145 | Descrição: Ordem do dia
```

### TIPOS DE MATÉRIA
```
ID: 109 | Descrição: Correspondência Recebida
ID: 135 | Descrição: Projeto de Lei
ID: 138 | Descrição: Projeto de Resolução
ID: 140 | Descrição: Requerimento
ID: 141 | Descrição: Indicação
```

### FASES DE TRAMITAÇÃO
```
ID: 13 | Valor: Leitura
ID: 14 | Valor: 1ª Discussão
ID: 15 | Valor: 2ª Discussão
ID: 16 | Valor: 3ª Discussão
ID: 17 | Valor: Votação Final
```

### REGIMES DE TRAMITAÇÃO
```
ID: 6 | Descrição: Ordinário
ID: 7 | Descrição: Urgência
ID: 8 | Descrição: Urgência Urgentíssima
```

### TIPOS DE QUORUM
```
ID: 28 | Descrição: Maioria simples
ID: 29 | Descrição: Maioria absoluta
ID: 30 | Descrição: Dois terços
```

## REGRAS DE NEGÓCIO

### AGRUPAMENTO POR DOCUMENTO
- **Expediente**: Deve conter matérias em fase "Leitura" (13) e "1ª Discussão" (14)
- **Ordem do Dia**: Deve conter matérias em fases de discussão e votação (14, 15, 16, 17)

### VALIDAÇÕES OBRIGATÓRIAS
- Sessão deve ter pelo menos 1 matéria para ser exportada
- XML deve ser válido antes da exportação
- Matérias devem ter autor definido
- Números e anos devem ser consistentes
- Datas devem estar no formato correto

### CAMPOS OPCIONAIS VS OBRIGATÓRIOS
**Obrigatórios**: tipo, numero, ano, data, descricao, assunto
**Opcionais**: regime, quorum, arquivos, observacoes, horaInicial, horaFinal

## ARQUITETURA TÉCNICA ESPERADA

### SERVICES NECESSÁRIOS
1. **SessionService**: CRUD de sessões usando NodeApiClient
2. **MatterService**: Gestão de matérias dentro das sessões  
3. **XmlGeneratorService**: Geração dos XMLs seguindo o padrão
4. **XmlExportService**: Exportação e histórico

### CONTROLLERS NECESSÁRIOS
1. **SessionController**: Interface web para gestão de sessões
2. **XmlExportController**: Operações de exportação e preview

### VIEWS NECESSÁRIAS
1. **sessions/index.blade.php**: Lista de sessões com botões de exportação
2. **sessions/create.blade.php**: Formulário de criação
3. **sessions/edit.blade.php**: Formulário de edição  
4. **sessions/show.blade.php**: Detalhes da sessão + gestão de matérias
5. **xml/session.blade.php**: Template para geração do XML

### EXTENSÕES DO NODEAPICLIENT
```php
// Métodos que devem ser adicionados ao NodeApiClient existente
public function getSessions($filters = [])
public function createSession($data)
public function updateSession($id, $data)
public function getSessionMatters($sessionId)
public function addMatterToSession($sessionId, $data)
public function generateSessionXml($sessionId, $documentType)
public function exportSessionXml($sessionId, $xmlData)
```

### EXTENSÕES DO MOCKAPICONTROLLER
```php
// Métodos que devem ser adicionados ao MockApiController existente
public function sessions()
public function createSession(Request $request)
public function sessionMatters($sessionId)
public function generateSessionXml(Request $request, $sessionId)
public function exportSessionXml(Request $request, $sessionId)
```

## INTERFACE DO USUÁRIO ESPERADA

### TELA PRINCIPAL (Lista de Sessões)
- **Tabela** com: Número/Ano, Data/Hora, Tipo, Qtd Matérias, Status
- **Botões por linha**: Ver, Editar, Exportar Expediente, Exportar Ordem do Dia
- **Botão global**: Nova Sessão
- **Indicadores visuais**: Status colorido, contador de matérias

### TELA DE DETALHES DA SESSÃO
- **Cabeçalho**: Informações da sessão + botões de ação
- **Seção Matérias**: Tabela com matérias + botão "Adicionar Matéria"
- **Modal**: Formulário para adicionar/editar matérias
- **Seção Exportações**: Histórico de XMLs exportados

### FORMULÁRIOS
- **Sessão**: Tipo, Número, Ano, Data, Hora, Observações
- **Matéria**: Tipo, Número, Ano, Descrição, Assunto, Autor, Fase, Regime, Quorum

## FLUXO DE TRABALHO ESPERADO

### 1. CRIAÇÃO DE SESSÃO
1. Usuário clica "Nova Sessão"
2. Preenche formulário (tipo, número, ano, data, hora)
3. Sistema valida e cria sessão com status "preparacao"
4. Redireciona para detalhes da sessão

### 2. ADIÇÃO DE MATÉRIAS
1. Na tela de detalhes, clica "Adicionar Matéria"
2. Modal abre com formulário
3. Seleciona tipo, preenche dados, escolhe fase
4. Sistema adiciona matéria à sessão

### 3. EXPORTAÇÃO
1. Usuário clica "Exportar Expediente" ou "Exportar Ordem do Dia"
2. Sistema gera XML agrupando matérias por fase
3. Opcionalmente mostra preview
4. Confirma exportação
5. Envia XML para sistema externo
6. Registra no histórico
7. Atualiza status da sessão

## INSTRUÇÕES ESPECÍFICAS PARA IMPLEMENTAÇÃO

### COMEÇAR SEMPRE POR:
1. **SessionService**: Implementar CRUD básico com NodeApiClient
2. **SessionController**: Interface web básica
3. **Views básicas**: index, create, edit, show
4. **MockApiController**: Endpoints para desenvolvimento

### USAR SEMPRE:
- **Padrão NodeApiClient**: Seguir estrutura existente do projeto
- **Metronic Template**: Aproveitar componentes já disponíveis
- **Laravel Validation**: Validar todos os inputs
- **Blade Components**: Criar componentes reutilizáveis
- **Error Handling**: Tratar exceções adequadamente

### NÃO IMPLEMENTAR:
- Sistema de votação interno
- Blockchain para votos
- Presença digital com QR Code
- Dashboard em tempo real
- Funcionalidades que não sejam gestão + exportação XML

### PRIORIDADES DE DESENVOLVIMENTO:
1. **Semana 1**: CRUD de sessões + interface básica
2. **Semana 2**: Gestão de matérias dentro das sessões
3. **Semana 3**: Geração de XML + templates Blade
4. **Semana 4**: Exportação + histórico + testes

## PONTOS DE ATENÇÃO CRÍTICOS

### XML DEVE SER EXATO
- Respeitar case-sensitive nos nomes dos elementos
- Usar IDs numéricos conforme especificado
- Formato de data: YYYY-MM-DD
- Formato de hora: HH:MM:SS.0000000-03:00
- Encoding: UTF-8

### AGRUPAMENTO CORRETO
- Expediente: apenas fases 13 (Leitura) e 14 (1ª Discussão)
- Ordem do Dia: fases 14, 15, 16, 17 (discussões e votações)
- Matérias devem estar na fase correta

### DADOS OBRIGATÓRIOS
- Todo XML deve ter pelo menos uma matéria
- Toda matéria deve ter autor definido
- IDs devem ser consistentes
- Validar antes de exportar

## EXEMPLO DE USO TÍPICO

1. **Secretário cria sessão**: "37ª Sessão Ordinária de 2024, 02/12/2024 às 17h"
2. **Adiciona matérias**: 3 Projetos de Lei na 1ª Discussão, 2 Requerimentos na Leitura
3. **Gera Expediente**: XML com matérias de Leitura agrupadas
4. **Gera Ordem do Dia**: XML com matérias de 1ª Discussão agrupadas  
5. **Exporta ambos**: Envia para sistema de votação externo
6. **Acompanha histórico**: Verifica se exportação foi bem-sucedida

## RESULTADO ESPERADO

Um módulo funcional que permita:
- ✅ Gestão completa de sessões parlamentares
- ✅ Adição/remoção de matérias por sessão
- ✅ Geração de XML padronizado e válido
- ✅ Exportação confiável para sistema externo
- ✅ Interface intuitiva e responsiva
- ✅ Histórico completo de operações

**IMPORTANTE**: O foco é gestão de documentos e geração de XML, não votação. O sistema externo é quem vai gerenciar as votações propriamente ditas.