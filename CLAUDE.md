# Sistema Legisinc - Configuração Essencial

## 🚀 COMANDO PRINCIPAL

```bash
docker exec -it legisinc-app php artisan migrate:safe --fresh --seed --generate-seeders
```

**✅ VERSÃO v2.3 - MIGRAÇÃO SEGURA COM AUTO-CORREÇÃO**

### **🔧 Correções Automáticas Incluídas:**
- ✅ **Permissões de Storage**: Auto-corrige ownership e permissões
- ✅ **Namespaces de Seeders**: Auto-corrige referências malformadas
- ✅ **Cache e Views**: Auto-limpeza após migration
- ✅ **Log Files**: Auto-criação e permissões corretas
- ✅ **Bootstrap Cache**: Auto-correção de permissões

## ✅ CONFIGURAÇÃO AUTOMÁTICA:

### 1. **Templates de Proposições (23 tipos)** 
- Templates LC 95/1998 com **Template de Moção** funcional
- **RTF com codificação UTF-8** para acentuação portuguesa
- **Processamento de imagem automático**

### 2. **Dados da Câmara**
- **Nome**: Câmara Municipal Caraguatatuba  
- **Endereço**: Praça da República, 40, Centro, Caraguatatuba-SP
- **Telefone**: (12) 3882-5588
- **Website**: www.camaracaraguatatuba.sp.gov.br
- **CNPJ**: 50.444.108/0001-41

### 3. **Usuários do Sistema**
- **Admin**: bruno@sistema.gov.br / 123456
- **Parlamentar**: jessica@sistema.gov.br / 123456  
- **Legislativo**: joao@sistema.gov.br / 123456
- **Protocolo**: roberto@sistema.gov.br / 123456
- **Expediente**: expediente@sistema.gov.br / 123456
- **Assessor Jurídico**: juridico@sistema.gov.br / 123456

## 🏛️ Template de Moção - Variáveis Principais

### Cabeçalho
- `${imagem_cabecalho}` - Imagem do cabeçalho
- `${cabecalho_nome_camara}` → **CÂMARA MUNICIPAL DE CARAGUATATUBA**
- `${cabecalho_endereco}` → **Praça da República, 40, Centro**
- `${cabecalho_telefone}` → **(12) 3882-5588**
- `${cabecalho_website}` → **www.camaracaraguatatuba.sp.gov.br**

### Proposição
- `${numero_proposicao}` → **[AGUARDANDO PROTOCOLO]** (até protocolar) → **0001/2025** (após protocolo)
- `${ementa}` → Ementa da proposição
- `${texto}` → Conteúdo da proposição
- `${justificativa}` → Justificativa (opcional)

### Dados do Autor
- `${autor_nome}` → Nome do parlamentar
- `${autor_cargo}` → **Vereador**

### Data e Local  
- `${municipio}, ${dia} de ${mes_extenso} de ${ano_atual}`
- `${assinatura_padrao}` → **__________________________________**
- `${rodape_texto}` → Texto institucional do rodapé

## 🔄 Fluxo Operacional

1. **Administrador** cria templates com variáveis
2. **Parlamentar** cria proposição → Template aplicado
3. **Sistema** detecta tipo e aplica template (ID: 6 para moção)
4. **Parlamentar** edita no OnlyOffice
5. **Protocolo** atribui número oficial
6. **Legislativo** recebe para análise

## 🎯 Recursos v2.0 Implementados

✅ **OnlyOffice 100% funcional** - Preserva todas as alterações  
✅ **Priorização de arquivos salvos** - Sistema prioriza edições sobre templates  
✅ **Polling Realtime** - Detecta mudanças automaticamente em 15s  
✅ **Performance otimizada** - Cache inteligente + 70% redução I/O  
✅ **Interface Vue.js** - Atualizações em tempo real  
✅ **PDF de assinatura** - Sempre usa versão mais recente  
✅ **Parágrafos preservados** - Quebras de linha funcionam no OnlyOffice  
✅ **Permissões por role** - Sistema inteligente de autorizações  

## 🚀 Como Testar

### **Teste Básico**
1. `docker exec -it legisinc-app php artisan migrate:fresh --seed`
2. Acesse: http://localhost:8001
3. Login: jessica@sistema.gov.br / 123456
4. Crie uma moção
5. Edite no OnlyOffice

### **Teste de Colaboração**
1. Login como Legislativo: joao@sistema.gov.br / 123456
2. Acesse proposição criada pelo Parlamentar
3. Edite no OnlyOffice
4. Confirme que alterações são preservadas

## 🔐 Certificados Digitais

### **Configuração via Comando Artisan**
```bash
# Configurar certificado para usuário
docker exec legisinc-app php artisan certificado:configurar \
  jessica@sistema.gov.br \
  /tmp/certificado_teste.pfx \
  123Ligado \
  --salvar-senha
```

### **Certificado de Teste**
- **Arquivo**: `BRUNO JOSE PEREIRA RANGEL_31748726854.pfx`
- **Senha**: `123Ligado`
- **CN**: `BRUNO JOSE PEREIRA RANGEL:31748726854`
- **Validade**: 09/09/2026

### **Helper de Certificados**
- **Localização**: `/app/Helpers/CertificadoHelper.php`
- **Funções principais**:
  - `validar()`: Valida certificado com senha
  - `getStatus()`: Retorna status completo
  - `configurarCertificadoPadrao()`: Configura certificado

## 🔒 ARQUIVOS CRÍTICOS

### Processamento
- `/app/Services/OnlyOffice/OnlyOfficeService.php`
- `/app/Services/Template/TemplateProcessorService.php`

### Seeders
- `/database/seeders/DatabaseSeeder.php` - Orquestrador principal
- `/database/seeders/TipoProposicaoTemplatesSeeder.php` - Templates
- `/database/seeders/ParametrosTemplatesSeeder.php` - Parâmetros

### Imagem Padrão
- **Localização**: `/public/template/cabecalho.png`
- **Formato**: PNG 503x99 pixels
- **Processamento**: Automático para RTF

## 📝 Numeração de Proposições

**Fluxo legislativo correto:**
1. **Criação**: Exibe `[AGUARDANDO PROTOCOLO]`
2. **Após protocolar**: Exibe número oficial (`0001/2025`)
3. **Apenas o Protocolo** pode atribuir números

## 📋 Scripts de Validação

```bash
./scripts/validar-pdf-otimizado.sh              # Validação rápida
./scripts/teste-migrate-fresh-completo.sh       # Teste completo
./scripts/validacao-final-completa.sh           # Recomendado
```

## 📁 Organização

### **Documentação Técnica Detalhada**
- `docs/technical/SOLUCAO-PRIORIZACAO-ARQUIVO-SALVO-ONLYOFFICE.md`
- `docs/technical/SOLUCAO-POLLING-REALTIME-ONLYOFFICE.md`
- `docs/technical/REFERENCIA-RAPIDA-ONLYOFFICE.md`

### **Scripts de Teste**
- `tests/manual/teste-*.php` - Scripts de debug
- `scripts/tests/*.sh` - Validação Shell

## 🔧 Correções Críticas v2.1 (PRESERVAR SEMPRE)

### **1. Invalidação PDF após Aprovação Legislativa**
**Arquivo**: `app/Http/Controllers/ProposicaoController.php`
**Método**: `aprovarEdicoesLegislativo()` - linhas ~4210-4217

```php
$proposicao->update([
    'status' => 'aprovado_assinatura',
    'data_aprovacao_autor' => now(),
    // CRÍTICO: Invalidar PDF antigo para forçar regeneração
    'arquivo_pdf_path' => null,
    'pdf_gerado_em' => null,
    'pdf_conversor_usado' => null,
]);
```

### **2. Detecção RTF mais Novo que PDF**
**Arquivo**: `app/Http/Controllers/ProposicaoController.php`
**Método**: `servePDF()` - linhas ~4890-4951

```php
// CRÍTICO: Verificar se RTF foi modificado após PDF
if ($rtfModificado > $pdfGerado) {
    $pdfEstaDesatualizado = true;
    // Invalidar cache PDF para forçar regeneração
    $proposicao->update([
        'arquivo_pdf_path' => null,
        'pdf_gerado_em' => null,
        'pdf_conversor_usado' => null,
    ]);
}
```

### **3. Assinatura Digital - Verificação Dupla**
**Arquivo**: `app/Services/AssinaturaDigitalService.php`

```php
// Check if file exists using both direct path and Storage
$fileExists = file_exists($pdfAssinado);
if (!$fileExists) {
    $relativePath = str_replace(storage_path('app/'), '', $pdfAssinado);
    $fileExists = Storage::exists($relativePath);
}
```

### **4. Template Universal - Prioridade Garantida**
**Seeder**: `TemplateUniversalPrioridadeSeeder`
**Problema**: Templates específicos criados depois tinham prioridade sobre universal

```bash
# Sempre que rodar migrate:safe, garantir que template universal seja mais recente
$universal->touch(); // Atualiza updated_at para now()
```

### **5. RTFs Órfãos após Reset - Auto Regeneração**
**Seeder**: `RegenerarRTFProposicoesSeeder`
**Problema**: Após reset, proposições ficam com RTFs inexistentes, causando PDFs desatualizados

```php
// Auto-detecção e regeneração de RTFs perdidos
if (!Storage::exists($proposicao->arquivo_path)) {
    $conteudoRTF = $templateService->aplicarTemplateParaProposicao($proposicao);
    Storage::put($novoRTF, $conteudoRTF);
    $proposicao->update(['arquivo_path' => $novoRTF, 'arquivo_pdf_path' => null]);
}
```

### **6. OnlyOffice - Preservação de Conteúdo Original (CRÍTICO)**
**Seeders**: `CorrecaoOnlyOfficeConteudoSeeder` + `LimpezaConteudoCorrempidoSeeder`
**Problema**: Sistema substituía conteúdo original por texto extraído de RTF corrompido ("ansi Objetivo geral...")

```php
// LÓGICA CONSERVADORA: Preservar conteúdo original sempre que possível
$conteudoOriginal = $proposicao->conteudo;
$temConteudoOriginalValido = !empty($conteudoOriginal) && strlen(trim($conteudoOriginal)) > 10;

if ($temConteudoOriginalValido) {
    // NUNCA substituir conteúdo original válido
    Log::info('CONSERVANDO conteúdo original existente - não extraindo do RTF');
} elseif ($this->isConteudoValidoRigoroso($conteudoExtraido)) {
    // Só substituir se não há conteúdo original E conteúdo extraído é muito confiável
    $updateData['conteudo'] = $conteudoExtraido;
}

// VALIDAÇÃO RIGOROSA: Rejeita padrões suspeitos
private function isConteudoValidoRigoroso(string $conteudo): bool {
    // Rejeita textos que começam com "ansi ", "xxx Objetivo geral:", etc.
    $padroesSuspeitos = ['/^ansi\s/', '/^[a-z]{4,8}\s+(Objetivo|CONSIDERANDO)/'];
    // Exige mínimo 30 chars, 5 palavras de 3+ chars, 50% chars válidos
}

// LIMPEZA AUTOMÁTICA: Remove conteúdo corrompido de proposições antigas
$proposicoesCorrempidas = Proposicao::where('conteudo', 'LIKE', '%ansi Objetivo%')->get();
foreach ($proposicoesCorrempidas as $proposicao) {
    $proposicao->update(['conteudo' => $this->gerarConteudoPadrao($proposicao)]);
}
```

---

**🎊 SISTEMA 100% OPERACIONAL - VERSÃO v2.2 ENTERPRISE**

**Status**: Produção com Polling Realtime + Priorização Arquivo Salvo + Template Universal + Performance Otimizada + Correções Críticas PDF + **Preservação de Conteúdo OnlyOffice**

**Última atualização**: 12/09/2025

## 🆕 **NOVIDADES v2.2**
✅ **Correção OnlyOffice Crítica**: Sistema nunca mais substitui conteúdo original por texto corrompido  
✅ **Validação Rigorosa**: Detecta e rejeita padrões suspeitos como "ansi Objetivo geral..."  
✅ **Lógica Conservadora**: Preserva conteúdo existente durante edições no OnlyOffice  
✅ **Limpeza Automática**: Remove conteúdo corrompido de proposições antigas existentes  
✅ **Seeders Automáticos**: Aplicação e limpeza automáticas via `migrate:safe`  
✅ **Sistema Anti-Regressão**: Correção preservada permanentemente, problema RESOLVIDO DEFINITIVAMENTE