# Explicação Detalhada - Etapas de Implementação do API Gateway

## 📚 Índice
1. [Visão Geral da Estratégia](#visão-geral)
2. [Etapa 1: Instalação do Gateway](#etapa-1-instalação-do-gateway)
3. [Etapa 2: Shadow Traffic](#etapa-2-shadow-traffic)
4. [Etapa 3: Canary Deployment](#etapa-3-canary-deployment)
5. [Etapa 4: Migração Completa](#etapa-4-migração-completa)
6. [Etapa 5: Interface Administrativa](#etapa-5-interface-administrativa)

## 🎯 Visão Geral da Estratégia {#visão-geral}

### O Problema Atual
```
Hoje: Frontend → Laravel (Monolito)
      Tudo acoplado, difícil de mudar
```

### A Solução Proposta
```
Futuro: Frontend → Gateway → Legacy OU Nova API OU API Externa
        Flexível, pode trocar backend sem tocar no frontend
```

### Como Vamos Chegar Lá (Sem Quebrar Nada)
1. **Colocar um "porteiro" (Gateway)** na frente de tudo
2. **Testar às escondidas** (Shadow Traffic)
3. **Migrar aos poucos** (Canary Deployment)
4. **Trocar gradualmente** endpoint por endpoint

---

## 🚪 Etapa 1: Instalação do Gateway {#etapa-1-instalação-do-gateway}

### O Que É Um API Gateway?

Imagine um **porteiro inteligente** do seu prédio:
- Recebe todas as visitas (requisições)
- Decide para qual apartamento (backend) enviar
- Pode enviar para múltiplos apartamentos
- Controla quem entra e com que frequência

### O Que Faremos Nesta Etapa

#### Dia 1-2: Instalar o Traefik (O Porteiro)

**Antes:**
```
Usuário → Laravel diretamente
```

**Depois:**
```
Usuário → Traefik → Laravel (100% do tráfego ainda)
```

**O que o código faz:**
```yaml
services:
  traefik:
    image: traefik:v3.0  # Baixa o "porteiro"
    ports:
      - "80:80"          # Escuta na porta 80 (web)

  laravel:
    image: legisinc/laravel  # Seu app atual
```

**Comando para rodar:**
```bash
docker-compose up -d
```

**O que acontece:**
- Traefik sobe e começa a receber TODAS as requisições
- Por enquanto, ele apenas repassa tudo para o Laravel
- **Nada muda para o usuário** (mesma URL, mesma resposta)

#### Dia 3: Adicionar Segurança e Monitoramento

**O que adicionamos:**
```yaml
# Rate Limiting (máximo 100 requisições por minuto)
rate-limit:
  average: 100  # Protege contra spam/DDoS

# Headers de Segurança
security-headers:
  X-Frame-Options: DENY  # Previne clickjacking

# Request ID (rastreamento)
X-Request-ID: abc-123-def  # Cada requisição ganha um ID único
```

**Por quê?**
- **Rate Limit:** Evita que alguém faça 10.000 requisições e derrube o sistema
- **Security Headers:** Protege contra ataques comuns (XSS, clickjacking)
- **Request ID:** Permite rastrear uma requisição do início ao fim

#### Dia 4-5: Observabilidade (Ver o que está acontecendo)

**Instalamos Prometheus + Grafana:**
```yaml
prometheus:
  # Coleta métricas a cada 15 segundos

grafana:
  # Mostra gráficos bonitos das métricas
```

**O que você verá no Grafana:**
- Quantas requisições por segundo
- Tempo de resposta (está rápido ou lento?)
- Taxa de erro (quantos 500, 404, etc)
- Saúde do sistema

**Exemplo de métrica:**
```
"Nas últimas 24h:
 - 50.000 requisições
 - 99.8% sucesso
 - Tempo médio: 150ms"
```

### ✅ Resultado da Etapa 1
- Gateway instalado e funcionando
- TODO tráfego passa por ele
- Temos métricas e segurança
- **Zero impacto no usuário**
- **Podemos fazer rollback instantâneo** (só desligar o gateway)

---

## 👻 Etapa 2: Shadow Traffic (Tráfego Fantasma) {#etapa-2-shadow-traffic}

### O Que É Shadow Traffic?

É como ter um **espião invisível**:
- Copia cada requisição
- Envia para a nova API
- **NÃO** usa a resposta da nova API
- Usuário continua recebendo resposta do Laravel

### Para Que Serve?

**Testar sem risco:**
- A nova API está respondendo?
- As respostas são iguais às do Laravel?
- A performance é boa?
- Tem algum erro?

### Como Implementamos

#### Semana 1: Configurar o Espelhamento

**Configuração no Gateway:**
```nginx
location /api/proposicoes {
    mirror /shadow;  # Copia a requisição
    proxy_pass http://laravel;  # Produção continua no Laravel
}

location /shadow {
    internal;  # Não acessível externamente
    proxy_pass http://nova-api;  # Envia cópia para nova API
}
```

**O que acontece:**
1. Usuário faz requisição para `/api/proposicoes`
2. Gateway envia para Laravel (normal)
3. Gateway TAMBÉM envia cópia para Nova API
4. Resposta do Laravel vai para o usuário
5. Resposta da Nova API vai para os logs (análise)

#### Semana 1-2: Análise dos Resultados

**Script de comparação:**
```bash
# Compara respostas do Legacy vs Nova API
./compare-responses.sh

Resultado:
- 1000 requisições analisadas
- 95% idênticas ✅
- 3% diferença em campos opcionais ⚠️
- 2% erro na nova API ❌
```

**O que fazemos com isso:**
- ✅ 95% idênticas = ótimo!
- ⚠️ 3% diferenças = investigar e corrigir
- ❌ 2% erros = DEVE corrigir antes de prosseguir

### ✅ Resultado da Etapa 2
- Nova API testada com tráfego real
- **Zero risco** (usuários nem sabem)
- Identificamos problemas ANTES de migrar
- Temos confiança para próximo passo

---

## 🐤 Etapa 3: Canary Deployment (Implantação Canário) {#etapa-3-canary-deployment}

### O Que É Canary Deployment?

Nome vem dos **canários nas minas de carvão** (detectavam gases tóxicos).

No nosso caso:
- 1% dos usuários usam a nova API (os "canários")
- 99% continuam no Laravel (seguro)
- Se o 1% tiver problemas, voltamos tudo

### Como Funciona na Prática

#### Semana 2: Começar com 1%

**Configuração:**
```yaml
proposicoes-weighted:
  services:
    - name: "nova-api"
      weight: 1    # 1% do tráfego
    - name: "laravel"
      weight: 99   # 99% do tráfego
```

**O que acontece:**
- De cada 100 requisições:
  - 1 vai para Nova API
  - 99 vão para Laravel
- Se a 1 requisição falhar, apenas 1 usuário é afetado

#### Progressão Gradual

**Dia 1:** 1% canary
```
Monitorando...
✅ Sem erros em 4 horas
→ Aumentar para 5%
```

**Dia 2:** 5% canary
```
Monitorando...
✅ Taxa de erro < 0.1%
✅ Latência similar ao Laravel
→ Aumentar para 10%
```

**Dia 3:** 10% canary
```
Monitorando...
✅ 10.000 requisições processadas
✅ Zero reclamações de usuários
→ Aumentar para 25%
```

**Dia 4:** 25% canary
```
Monitorando...
⚠️ Latência aumentou 50ms
→ Investigar e otimizar
→ Manter em 25% por enquanto
```

**Dia 5:** 25% canary (após otimização)
```
✅ Latência normalizada
→ Aumentar para 50%
```

**Semana 3:** 50% → 75% → 100%

### Sistema de Alertas

**Alerta automático se:**
```yaml
# Taxa de erro > 1%
if error_rate > 0.01:
  alert("CRÍTICO: Nova API com muitos erros!")
  rollback_to_legacy()

# Latência > 500ms
if p95_latency > 500ms:
  alert("AVISO: Nova API lenta!")
  reduce_canary_to_10()
```

### Rollback de Emergência (Voltar Tudo)

**Se algo der muito errado:**
```bash
./emergency-rollback.sh

# O que o script faz:
1. Para TODO tráfego para Nova API
2. Envia 100% para Laravel
3. Notifica equipe no Slack
4. Salva logs para análise

Tempo total: 30 segundos
```

### ✅ Resultado da Etapa 3
- Nova API validada com tráfego real crescente
- Problemas detectados e corrigidos gradualmente
- **Rollback sempre disponível**
- Confiança para migração completa

---

## 🚀 Etapa 4: Migração Completa {#etapa-4-migração-completa}

### Estratégia de Migração por Tipo de Endpoint

#### Fase 1: Endpoints Read-Only (Mais Seguros)

**Por que começar com GET?**
- Não alteram dados
- Fácil fazer rollback
- Menor risco ao negócio

**Ordem de migração:**
```
1. GET /api/tipos-proposicao     (dados estáticos)
2. GET /api/parametros           (configurações)
3. GET /api/parlamentares        (listagem simples)
4. GET /api/proposicoes          (listagem com filtros)
```

#### Fase 2: Endpoints de Criação (POST)

**Mais cuidado necessário:**
```
1. POST /api/comentarios         (baixo impacto)
2. POST /api/anexos              (upload de arquivos)
3. POST /api/proposicoes         (crítico - criar gradualmente)
```

#### Fase 3: Endpoints de Atualização (PUT/PATCH)

**Requer sincronização de dados:**
```
1. PUT /api/perfil               (dados do usuário)
2. PUT /api/proposicoes/{id}     (edição de proposições)
```

#### Fase 4: Endpoints Críticos

**Migrar por último:**
```
1. POST /api/protocolar          (gera número oficial)
2. POST /api/assinar             (assinatura digital)
3. POST /api/publicar            (publicação oficial)
```

### Como Migrar Cada Endpoint

**Exemplo: Migrando GET /api/proposicoes**

**Passo 1: Implementar na Nova API**
```javascript
// nova-api/routes/proposicoes.js
app.get('/api/proposicoes', async (req, res) => {
  const proposicoes = await db.query('SELECT * FROM proposicoes');

  // Formato compatível com Laravel
  res.json({
    success: true,
    data: proposicoes,
    meta: { total: proposicoes.length }
  });
});
```

**Passo 2: Testes de Compatibilidade**
```bash
# Comparar resposta Laravel vs Nova API
curl http://laravel/api/proposicoes > laravel.json
curl http://nova-api/api/proposicoes > nova.json
diff laravel.json nova.json

# Deve ser idêntico (exceto timestamps)
```

**Passo 3: Ativar Roteamento**
```yaml
# gateway/routes.yml
/api/proposicoes:
  backend: nova-api  # Mudou de 'laravel' para 'nova-api'
```

**Passo 4: Monitorar**
```
Primeiras 24h após migração:
- Taxa de erro: 0.01% ✅
- Latência P95: 180ms ✅
- Reclamações: 0 ✅
→ Endpoint migrado com sucesso!
```

### ✅ Resultado da Etapa 4
- Todos endpoints migrados gradualmente
- Sistema rodando 100% na Nova API
- Laravel pode ser desligado (ou mantido como backup)

---

## 🎛️ Etapa 5: Interface Administrativa {#etapa-5-interface-administrativa}

### Para Que Serve?

Permitir que **usuários não-técnicos** possam:
- Criar novas regras de negócio
- Modificar fluxos
- Criar endpoints customizados
- Sem precisar programar

### Exemplo Prático

**Cenário:** "Quando uma proposição for aprovada, enviar email e gerar PDF"

**Antes (Código):**
```php
if ($proposicao->status === 'APROVADA') {
    Mail::send(...);
    PDF::generate(...);
}
```

**Depois (Interface Visual):**
```
┌─────────────────────────────┐
│ QUANDO                      │
│ [Proposição] [status] [=] [APROVADA] │
│                             │
│ ENTÃO                       │
│ ✓ Enviar Email para [autor]│
│ ✓ Gerar PDF                │
│ ✓ Notificar Telegram       │
└─────────────────────────────┘
[Salvar Regra]
```

### Opções de Implementação

#### Opção 1: Strapi (Recomendado para Começar)

**O que é:** CMS headless que gera APIs automaticamente

**Como usar:**
```bash
# Instalar
npx create-strapi-app legisinc-cms

# Acessar interface: http://localhost:1337/admin
```

**Na interface visual:**
1. Criar tipo "Proposição"
2. Adicionar campos (titulo, conteudo, status)
3. Clicar "Save"
4. **API criada automaticamente!**

**Resultado:**
```
GET/POST     /api/proposicoes
GET/PUT/DEL  /api/proposicoes/:id
```

#### Opção 2: n8n (Workflows Visuais)

**O que é:** Ferramenta de automação visual (tipo Zapier)

**Exemplo de workflow:**
```
[Webhook] → [Filtro] → [Database] → [Email] → [Resposta]
   ↓           ↓          ↓           ↓          ↓
Recebe    Se aprovada  Salva BD   Notifica   Retorna
request                            autor      sucesso
```

#### Opção 3: Interface Customizada Simples

**Tela de criação de regras:**
```html
<form id="rule-builder">
  <h3>Nova Regra de Negócio</h3>

  <label>Endpoint:</label>
  <input type="text" placeholder="/api/custom/minha-regra">

  <label>Quando:</label>
  <select>
    <option>Campo X = Valor Y</option>
    <option>Usuário tem permissão Z</option>
  </select>

  <label>Então:</label>
  <checkbox>□ Salvar no banco</checkbox>
  <checkbox>□ Enviar email</checkbox>
  <checkbox>□ Chamar API externa</checkbox>

  <button>Criar Endpoint</button>
</form>
```

### Segurança da Interface Administrativa

**Regras importantes:**
1. **Apenas admins** podem criar regras
2. **Sem código arbitrário** (no eval, no exec)
3. **Ações pré-definidas** (não pode fazer "qualquer coisa")
4. **Validação** antes de publicar
5. **Versionamento** de regras (poder voltar versão anterior)

### ✅ Resultado da Etapa 5
- Regras de negócio configuráveis sem código
- Endpoints criados dinamicamente
- Maior autonomia para usuários de negócio
- Menor dependência de desenvolvedores

---

## 📊 Resumo Executivo

### Linha do Tempo Total

| Semana | O Que Fazemos | Risco | Resultado |
|--------|---------------|-------|-----------|
| **1** | Instalar Gateway + Shadow Traffic | Zero | Infraestrutura pronta |
| **2** | Primeiro Canary (1% → 10%) | Baixo | Validação inicial |
| **3** | Canary 25% → 50% | Médio | Metade migrada |
| **4** | Canary 75% → 100% | Médio | Migração completa |
| **5** | Interface Admin | Baixo | Autonomia de negócio |

### Benefícios Finais

1. **Flexibilidade Total**
   - Pode trocar backend sem tocar frontend
   - Pode usar múltiplas APIs/linguagens
   - Pode integrar serviços externos

2. **Segurança na Migração**
   - Shadow traffic = teste sem risco
   - Canary = migração gradual
   - Rollback = volta instantânea

3. **Observabilidade**
   - Métricas em tempo real
   - Alertas automáticos
   - Decisões baseadas em dados

4. **Autonomia**
   - Interface administrativa
   - Regras sem código
   - Menor dependência de TI

### Comandos Essenciais

```bash
# Começar
docker-compose up -d

# Ver métricas
http://localhost:3000 (Grafana)

# Ativar shadow
./enable-shadow.sh /api/endpoint

# Mudar canary
./set-canary.sh /api/endpoint 10

# Emergência
./emergency-rollback.sh
```

### Custos

- **Infraestrutura:** +1 servidor para Gateway (ou usar existente)
- **Ferramentas:** Todas open-source (grátis)
- **Tempo:** 4-5 semanas de implementação
- **Equipe:** 1-2 desenvolvedores

### ROI (Retorno do Investimento)

- **Redução de 70%** no tempo de mudanças futuras
- **Zero downtime** em atualizações
- **Possibilidade** de trocar tecnologia sem refazer tudo
- **Autonomia** para equipe de negócios

---

## 🎯 Próximo Passo Imediato

```bash
# 1. Criar pasta do projeto
mkdir legisinc-gateway
cd legisinc-gateway

# 2. Criar docker-compose.yml (copiar do documento)
vim docker-compose.yml

# 3. Subir gateway
docker-compose up -d

# 4. Testar
curl http://localhost/api/health

# 5. Celebrar! 🎉
echo "Gateway funcionando!"
```

**Tempo para ter o gateway rodando: 30 minutos**

---

*Este documento explica em detalhes cada etapa da migração.*
*Qualquer dúvida, consulte os documentos técnicos complementares.*