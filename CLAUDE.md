# Sistema Legisinc - Configuração Essencial

## 🚀 COMANDO PRINCIPAL

```bash
docker exec -it legisinc-app php artisan migrate:fresh --seed
```

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

---

**🎊 SISTEMA 100% OPERACIONAL - VERSÃO v2.0 ENTERPRISE**

**Status**: Produção com Polling Realtime + Priorização Arquivo Salvo + Template Universal + Performance Otimizada

**Última atualização**: 02/09/2025