# DASHBOARD PROTOCOLO - SISTEMA PARLAMENTAR 2.0

## 🎯 VISÃO GERAL

Dashboard focado e objetivo para o perfil **PROTOCOLO**, com as informações essenciais para o trabalho diário de protocolação e distribuição de proposições. Interface limpa e funcional.

---

## 📋 SEÇÕES PRINCIPAIS (Layout Compacto)

### 1. **FILA DE PROTOCOLAÇÃO** (Primeira dobra)

#### 📥 Proposições Aguardando Protocolo
- **Lista Prioritária**
  - Proposições assinadas esperando protocolo
  - Ordenação por data de envio (FIFO)
  - Status: Urgente, Normal, Atrasada
  - Autor e tipo de proposição

#### ⚡ Ações Rápidas
- **Botão "Protocolar"** direto na lista
- **Protocolo em Lote** (seleção múltipla)
- **Filtros Rápidos**: Hoje, Esta semana, Atrasadas
- **Busca**: Por autor, número, tipo

---

### 2. **MÉTRICAS DO DIA**

#### 📊 Resumo de Atividade (Cards Simples)
```
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│ 📋 HOJE         │ │ ⏰ MÉDIA        │ │ 📈 PENDENTES    │
│ 15 Protocoladas │ │ 8min por item   │ │ 23 na fila      │
└─────────────────┘ └─────────────────┘ └─────────────────┘
```

#### 📅 Resumo Semanal
- Total protocolado esta semana
- Comparativo com semana anterior
- Meta semanal vs realizado

---

### 3. **HISTÓRICO RECENTE**

#### 📋 Últimas Protocolações (Lista Simples)
```
✅ PL 2024/0123 - João Silva - 10:30 - CCJ, CFIN
✅ PLP 2024/0089 - Maria Santos - 10:15 - CCJ, CEDU  
✅ PEC 2024/0007 - Carlos Lima - 09:45 - CCJ, Especial
⏱️ PL 2024/0124 - Ana Costa - Em andamento...
```

#### 🔍 Detalhes Básicos
- Número de protocolo gerado
- Comissões distribuídas
- Horário de protocolo
- Status atual

---

### 4. **COMISSÕES E DISTRIBUIÇÃO**

#### 🏛️ Status das Comissões (Overview Rápido)
```
CCJ: ████████████████████ 20 pendentes
CFIN: ██████████████ 14 pendentes  
CEDU: ██████████ 10 pendentes
CSAU: ████████ 8 pendentes
```

#### ⚙️ Distribuição Automática
- **Sugestões do Sistema**: Comissões baseadas no assunto
- **Regras Pré-definidas**: Tipos → Comissões automáticas
- **Exceções**: Casos que precisam análise manual

---

### 5. **ALERTAS E PENDÊNCIAS**

#### 🚨 Alertas Importantes
- **Vermelhos**: Proposições com mais de 24h sem protocolo
- **Amarelos**: Verificações pendentes
- **Azuis**: Novas proposições recebidas

#### ⏰ Prazos e SLA
- SLA de protocolação: 4 horas úteis
- Indicador visual de cumprimento
- Alertas próximo ao vencimento

---

## 🎨 LAYOUT SIMPLIFICADO

### Interface Única (Uma página)
```
┌─────────────────────────────────────────────────────────┐
│  🚨 ALERTAS: 2 Urgentes | 5 Normais | SLA: 95% ✅      │
├─────────────────────────────────────────────────────────┤
│  📥 FILA DE PROTOCOLAÇÃO (Tabela Principal)            │
│  ┌─────┬──────────┬─────────────┬────────┬──────────┐  │
│  │ ⚡  │ Tipo/Num │ Autor       │ Envio  │ Ação     │  │
│  ├─────┼──────────┼─────────────┼────────┼──────────┤  │
│  │ 🔴  │ PL/0125  │ João Silva  │ 08:30  │[Protocolar]│  │
│  │ 🟡  │ PLP/0090 │ Maria Costa │ 09:15  │[Protocolar]│  │
│  │ 🟢  │ PEC/0008 │ Carlos Lima │ 10:00  │[Protocolar]│  │
│  └─────┴──────────┴─────────────┴────────┴──────────┘  │
├─────────────────────────────────────────────────────────┤
│  📊 MÉTRICAS HOJE: 15 Protocoladas | 8min média       │
├─────────────────────────────────────────────────────────┤
│  📋 ÚLTIMAS PROTOCOLAÇÕES (5 mais recentes)            │
└─────────────────────────────────────────────────────────┘
```

### Paleta de Cores Simples
- **🔴 Urgente**: Mais de 24h
- **🟡 Atenção**: Mais de 4h  
- **🟢 Normal**: Dentro do prazo
- **✅ Concluído**: Protocolado

---

## 📊 WIDGETS ESPECÍFICOS

### 1. **Gráfico de Barras - Protocolações por Dia**
```
Seg ████████████ 12
Ter ██████████████ 15  
Qua ████████ 8
Qui ██████████████████ 18
Sex ██████████ 10
```

### 2. **Medidor de SLA**
```
SLA Protocolo: 95% ✅
├─────────────────────────┤
0%    75%    90%    100%
      ⚠️     ✅
```

### 3. **Top 5 Comissões Mais Demandadas**
```
1. CCJ             ████████████████████ 45
2. CFIN            ████████████████ 32
3. CEDU            ████████████ 28
4. CSAU            ██████████ 22
5. CULT            ████████ 18
```

---

## 🔔 NOTIFICAÇÕES SIMPLIFICADAS

### Alertas de Tela
- **Pop-up**: Nova proposição recebida
- **Badge**: Número de pendências
- **Som**: Alertas críticos (mais de 24h)

### Relatórios Básicos
- **Diário**: PDF com protocolações do dia
- **Semanal**: Resumo de produtividade
- **Mensal**: Estatísticas gerais

---

## 📱 VERSÃO MOBILE

### App Simplificado
- Lista de pendências
- Botão de protocolo rápido
- Scanner QR Code (futuro)
- Notificações push

---

## 🎯 KPIs ESSENCIAIS (Apenas 5)

### 📊 Métricas Core
1. **Proposições Protocoladas/Dia**
2. **Tempo Médio de Protocolação**
3. **SLA de Atendimento (4h)**
4. **Backlog Atual**
5. **Taxa de Erro/Retrabalho**

---

## 🔧 FUNCIONALIDADES SIMPLES

### Ações Principais
- **Protocolar Individual**: Um clique
- **Protocolo em Lote**: Seleção múltipla
- **Visualizar Documento**: Preview rápido
- **Distribuir Comissões**: Sugestão automática
- **Gerar Relatório**: PDF simples

### Filtros Básicos
- **Por Data**: Hoje, Semana, Mês
- **Por Status**: Pendente, Atrasado, Urgente
- **Por Tipo**: PL, PLP, PEC, etc.
- **Por Autor**: Busca rápida

---

## 🚀 IMPLEMENTAÇÃO TÉCNICA

### Controller Simples
```php
class ProtocoloDashboardController extends Controller
{
    public function index()
    {
        $data = [
            'fila_protocolo' => $this->protocoloService->getFilaProtocolo(),
            'metricas_hoje' => $this->protocoloService->getMetricasHoje(),
            'ultimas_protocolacoes' => $this->protocoloService->getUltimas(5),
            'alertas' => $this->alertaService->getProtocoloAlertas()
        ];
        
        return view('protocolo.dashboard', compact('data'));
    }
    
    public function protocolar($id)
    {
        return $this->protocoloService->protocollarProposicao($id);
    }
}
```

### APIs Mínimas
```php
// Apenas as essenciais
Route::group(['prefix' => 'api/protocolo'], function() {
    Route::get('/fila', [ProtocoloApiController::class, 'fila']);
    Route::post('/protocolar/{id}', [ProtocoloApiController::class, 'protocolar']);
    Route::get('/metricas', [ProtocoloApiController::class, 'metricas']);
});
```

### Integração NodeApiClient
```php
class ProtocoloService
{
    public function getFilaProtocolo()
    {
        return $this->nodeApiClient->get('/protocolo/fila');
    }
    
    public function protocolarProposicao($id)
    {
        return $this->nodeApiClient->post("/protocolo/{$id}/protocolar");
    }
}
```

---

## 🎨 CARACTERÍSTICAS DO DESIGN

### Princípios
- **Simplicidade**: Máximo 5 informações por tela
- **Eficiência**: 2 cliques máximo para protocolar
- **Clareza**: Status visual claro
- **Responsivo**: Funciona em tablet/desktop

### Interface Limpa
- Muito espaço em branco
- Fontes grandes e legíveis
- Botões grandes para touch
- Cores contrastantes

---

**Dashboard do Protocolo = Simples, Rápido e Eficiente! 🚀**

Foco total na operação diária sem distrações desnecessárias.