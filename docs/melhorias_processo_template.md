# Melhoria do Sistema de Templates - Proposta Técnica

## 🔍 Análise do Problema Atual

### **Problemas Identificados:**

1. **Perda de Formatação**: Templates com imagens, estilos e formatação complexa são "achatados" em texto simples
2. **Processamento Inadequado**: Substituição de variáveis destrói a estrutura original do documento
3. **Limitação de Variáveis**: Sistema atual só consegue substituir texto simples, não preserva contexto visual
4. **Performance**: Processamento em tempo real causa latência desnecessária

### **Fluxo Atual (Problemático):**
```
Template.docx → Processamento PHP → Substituição Texto → Novo Document → OnlyOffice
     ↓              ↓                    ↓                 ↓
  [Formatado]   [Parsing]          [Texto Simples]    [Sem Formatação]
```

---

## 🚀 Solução Proposta: Template Físico + Metadados

### **Nova Arquitetura:**
```
Template Original → Cópia Física → Substituição OnlyOffice → Documento Final
      ↓               ↓                   ↓                    ↓
  [Formatado]    [Preservado]        [API OnlyOffice]     [Formatado]
```

### **Estrutura de Tabelas Proposta:**

```sql
-- Tabela principal de templates
CREATE TABLE documento_templates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    tipo_proposicao_id BIGINT,
    arquivo_original_path VARCHAR(500), -- Template físico original
    arquivo_modelo_path VARCHAR(500),   -- Cópia de trabalho
    variaveis_mapeamento JSON,          -- Mapeamento de variáveis
    configuracao_onlyoffice JSON,       -- Config específica do OnlyOffice
    ativo BOOLEAN DEFAULT TRUE,
    created_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (tipo_proposicao_id) REFERENCES tipo_proposicoes(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Tabela de instâncias de template (por proposição)
CREATE TABLE proposicao_template_instances (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    proposicao_id BIGINT,
    template_id BIGINT,
    arquivo_instance_path VARCHAR(500), -- Arquivo específico da proposição
    variaveis_preenchidas JSON,         -- Valores preenchidos pelo parlamentar
    status ENUM('preparando', 'pronto', 'editando', 'finalizado'),
    document_key VARCHAR(255) UNIQUE,   -- Chave única OnlyOffice
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (proposicao_id) REFERENCES proposicoes(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES documento_templates(id),
    UNIQUE KEY unique_proposicao_template (proposicao_id, template_id)
);

-- Tabela de variáveis do template
CREATE TABLE template_variaveis (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    template_id BIGINT,
    nome_variavel VARCHAR(100),         -- Ex: {ementa}, {texto}
    tipo ENUM('sistema', 'editavel'),   -- Sistema=automática, Editavel=preenchida pelo user
    descricao VARCHAR(255),
    obrigatoria BOOLEAN DEFAULT FALSE,
    valor_padrao TEXT,
    validacao_regex VARCHAR(255),
    created_at TIMESTAMP,
    
    FOREIGN KEY (template_id) REFERENCES documento_templates(id) ON DELETE CASCADE,
    UNIQUE KEY unique_template_variavel (template_id, nome_variavel)
);
```

---

## 🔧 Implementação da Nova Arquitetura

### **1. Service de Gestão de Templates**

```php
<?php

class TemplateDocumentService
{
    public function criarTemplate(array $data, UploadedFile $arquivo): DocumentoTemplate
    {
        DB::beginTransaction();
        
        try {
            // 1. Salvar arquivo original (preservado)
            $arquivoOriginalPath = $this->salvarArquivoOriginal($arquivo);
            
            // 2. Criar cópia de trabalho
            $arquivoModeloPath = $this->criarCopiaTrabalho($arquivoOriginalPath);
            
            // 3. Criar registro no banco
            $template = DocumentoTemplate::create([
                'nome' => $data['nome'],
                'descricao' => $data['descricao'],
                'tipo_proposicao_id' => $data['tipo_proposicao_id'],
                'arquivo_original_path' => $arquivoOriginalPath,
                'arquivo_modelo_path' => $arquivoModeloPath,
                'variaveis_mapeamento' => $this->extrairVariaveis($arquivoOriginalPath),
                'created_by' => Auth::id()
            ]);
            
            // 4. Processar variáveis encontradas
            $this->processarVariaveisTemplate($template);
            
            DB::commit();
            return $template;
            
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    private function extrairVariaveis(string $arquivoPath): array
    {
        // Usar biblioteca para ler DOCX e encontrar {variáveis}
        $phpWord = IOFactory::load(storage_path('app/public/' . $arquivoPath));
        $variaveisEncontradas = [];
        
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $texto = $this->extrairTextoElemento($element);
                preg_match_all('/\{([^}]+)\}/', $texto, $matches);
                $variaveisEncontradas = array_merge($variaveisEncontradas, $matches[1]);
            }
        }
        
        return array_unique($variaveisEncontradas);
    }
    
    private function processarVariaveisTemplate(DocumentoTemplate $template): void
    {
        $variaveisSistema = [
            'data', 'nome_parlamentar', 'cargo_parlamentar', 'email_parlamentar',
            'data_extenso', 'mes_atual', 'ano_atual', 'dia_atual', 'hora_atual',
            'numero_proposicao', 'tipo_proposicao', 'nome_municipio', 'nome_camara',
            'legislatura_atual', 'sessao_legislativa'
        ];
        
        foreach ($template->variaveis_mapeamento as $nomeVariavel) {
            $tipo = in_array($nomeVariavel, $variaveisSistema) ? 'sistema' : 'editavel';
            
            TemplateVariavel::create([
                'template_id' => $template->id,
                'nome_variavel' => $nomeVariavel,
                'tipo' => $tipo,
                'obrigatoria' => in_array($nomeVariavel, ['ementa', 'texto'])
            ]);
        }
    }
}
```

### **2. Service de Instância de Template**

```php
<?php

class TemplateInstanceService
{
    public function criarInstanciaTemplate(int $proposicaoId, int $templateId): ProposicaoTemplateInstance
    {
        $template = DocumentoTemplate::findOrFail($templateId);
        $proposicao = Proposicao::findOrFail($proposicaoId);
        
        // 1. Criar cópia física específica para esta proposição
        $arquivoInstancePath = $this->criarCopiaProposicao($template, $proposicao);
        
        // 2. Gerar document key única para OnlyOffice
        $documentKey = $this->gerarDocumentKey($proposicaoId, $templateId);
        
        // 3. Criar registro da instância
        return ProposicaoTemplateInstance::create([
            'proposicao_id' => $proposicaoId,
            'template_id' => $templateId,
            'arquivo_instance_path' => $arquivoInstancePath,
            'document_key' => $documentKey,
            'status' => 'preparando'
        ]);
    }
    
    public function processarVariaveisInstance(
        ProposicaoTemplateInstance $instance, 
        array $variaveisPreenchidas
    ): void {
        // 1. Mesclar variáveis do sistema + preenchidas
        $todasVariaveis = array_merge(
            $this->obterVariaveisSistema($instance->proposicao),
            $variaveisPreenchidas
        );
        
        // 2. Usar OnlyOffice Document Builder API para substituição
        $this->substituirVariaveisViaOnlyOffice($instance, $todasVariaveis);
        
        // 3. Atualizar status
        $instance->update([
            'variaveis_preenchidas' => $variaveisPreenchidas,
            'status' => 'pronto'
        ]);
    }
    
    private function substituirVariaveisViaOnlyOffice(
        ProposicaoTemplateInstance $instance, 
        array $variaveis
    ): void {
        // Usar OnlyOffice Document Builder API para substituição preservando formatação
        $builderScript = $this->gerarScriptSubstituicao($variaveis);
        
        $response = Http::post(config('onlyoffice.builder_url'), [
            'document' => base64_encode(Storage::get($instance->arquivo_instance_path)),
            'script' => $builderScript,
            'outputFormat' => 'docx'
        ]);
        
        if ($response->successful()) {
            $documentoProcessado = base64_decode($response->json('document'));
            Storage::put($instance->arquivo_instance_path, $documentoProcessado);
        }
    }
    
    private function gerarScriptSubstituicao(array $variaveis): string
    {
        $substituicoes = [];
        foreach ($variaveis as $nome => $valor) {
            $substituicoes[] = "oDocument.SearchAndReplace('{{{$nome}}}', '{$valor}', true);";
        }
        
        return "
            var oDocument = Api.GetDocument();
            " . implode("\n", $substituicoes) . "
            builder.SaveFile('docx', 'output.docx');
            builder.CloseFile();
        ";
    }
}
```

### **3. Controller Atualizado**

```php
<?php

class ProposicaoController extends Controller
{
    protected TemplateInstanceService $templateInstanceService;
    
    public function selecionarTemplate(Request $request, int $proposicaoId)
    {
        $request->validate([
            'template_id' => 'required|exists:documento_templates,id'
        ]);
        
        $proposicao = Proposicao::findOrFail($proposicaoId);
        $template = DocumentoTemplate::with('variaveis')->findOrFail($request->template_id);
        
        // Buscar variáveis editáveis do template
        $variaveisEditaveis = $template->variaveis()
            ->where('tipo', 'editavel')
            ->get();
        
        return view('proposicoes.preencher-variaveis', [
            'proposicao' => $proposicao,
            'template' => $template,
            'variaveis' => $variaveisEditaveis
        ]);
    }
    
    public function processarTemplate(Request $request, int $proposicaoId)
    {
        $proposicao = Proposicao::findOrFail($proposicaoId);
        $templateId = $request->input('template_id');
        
        // 1. Criar instância do template
        $instance = $this->templateInstanceService->criarInstanciaTemplate(
            $proposicaoId, 
            $templateId
        );
        
        // 2. Processar variáveis
        $variaveisPreenchidas = $request->input('variaveis', []);
        $this->templateInstanceService->processarVariaveisInstance(
            $instance, 
            $variaveisPreenchidas
        );
        
        // 3. Redirecionar para OnlyOffice
        return redirect()->route('proposicoes.editar-onlyoffice', [
            'proposicao' => $proposicaoId,
            'instance' => $instance->id
        ]);
    }
    
    public function editarOnlyOffice(int $proposicaoId, int $instanceId)
    {
        $instance = ProposicaoTemplateInstance::with(['proposicao', 'template'])
            ->findOrFail($instanceId);
        
        // Verificar se arquivo está pronto
        if ($instance->status !== 'pronto') {
            return back()->withErrors('Template ainda não está pronto para edição.');
        }
        
        // Configurar OnlyOffice
        $config = [
            "document" => [
                "fileType" => "docx",
                "key" => $instance->document_key,
                "title" => "Proposição {$instance->proposicao->numero} - {$instance->template->nome}",
                "url" => route('onlyoffice.serve-instance', $instance->id),
            ],
            "editorConfig" => [
                "callbackUrl" => route('api.onlyoffice.callback.instance', $instance->id),
                "mode" => "edit",
                "lang" => "pt-BR",
                "user" => [
                    "id" => Auth::id(),
                    "name" => Auth::user()->name
                ]
            ]
        ];
        
        // Atualizar status para editando
        $instance->update(['status' => 'editando']);
        
        return view('proposicoes.editar-onlyoffice', [
            'config' => $config,
            'proposicao' => $instance->proposicao,
            'instance' => $instance
        ]);
    }
}
```

---

## 🔄 Fluxo Otimizado

### **1. Criação pelo Administrador:**
```
Admin Upload → Arquivo Original → Análise Variáveis → Metadados BD
     ↓              ↓                   ↓                ↓
  [Template]   [Preservado]        [Automático]      [Catalogado]
```

### **2. Uso pelo Parlamentar:**
```
Seleção → Cópia Física → Preenchimento → OnlyOffice Builder → Editor
   ↓          ↓             ↓              ↓                ↓
[Template] [Instância]  [Variáveis]   [Substituição]   [Formatado]
```

### **3. Vantagens da Nova Abordagem:**

✅ **Preservação Total**: Formatação, imagens, estilos mantidos  
✅ **Performance**: Cópia física direta, sem processamento PHP  
✅ **Escalabilidade**: Instâncias independentes por proposição  
✅ **Flexibilidade**: OnlyOffice Builder API para substituições complexas  
✅ **Auditoria**: Histórico completo de templates e instâncias  
✅ **Backup**: Arquivo original sempre preservado  

---

## 📋 Implementação por Fases

### **Fase 1 - Estrutura Base (Sprint 1)**
- ✅ Criar tabelas `documento_templates`, `proposicao_template_instances`, `template_variaveis`
- ✅ Implementar `TemplateDocumentService`
- ✅ Migrar templates existentes para nova estrutura

### **Fase 2 - Instâncias e Processamento (Sprint 2)**
- ✅ Implementar `TemplateInstanceService`
- ✅ Integrar OnlyOffice Document Builder API
- ✅ Criar rotas para servir instâncias de templates

### **Fase 3 - Interface e UX (Sprint 3)**
- ✅ Atualizar views para nova estrutura
- ✅ Implementar preview de templates
- ✅ Dashboard de gestão de templates

### **Fase 4 - Otimizações (Sprint 4)**
- ✅ Cache de templates mais utilizados
- ✅ Limpeza automática de instâncias antigas
- ✅ Métricas e monitoramento

---

## 🎯 Resultado Final

Com essa abordagem, o parlamentar terá:

1. **Template Completo**: Formatação, imagens, estilos preservados
2. **Edição Rica**: OnlyOffice com todas as funcionalidades
3. **Performance**: Carregamento instantâneo
4. **Flexibilidade**: Pode modificar livremente após substituição de variáveis

O sistema se torna muito mais robusto e profissional, adequado para um ambiente legislativo real.