# API Documentation - Sistema de Tramitação Parlamentar 2.0

## Visão Geral

Esta documentação define as especificações da API externa que será consumida pelo sistema Laravel através do `NodeApiClient`. O sistema possui 20 módulos principais com mais de 360 funcionalidades, organizados em uma arquitetura de microserviços.

## Informações Gerais

- **Base URL**: `http://localhost:3000` (desenvolvimento) / `https://api.parlamentar.gov.br` (produção)
- **Protocolo**: HTTP/HTTPS
- **Formato**: JSON
- **Autenticação**: JWT Bearer Token
- **Versionamento**: `/v1/`
- **Rate Limiting**: 1000 requests/hour por usuário

## Estrutura de Resposta Padrão

### Resposta de Sucesso
```json
{
  "success": true,
  "data": {
    // Dados da resposta
  },
  "meta": {
    "total": 100,
    "page": 1,
    "per_page": 15,
    "total_pages": 7
  },
  "timestamp": "2025-07-08T10:30:00Z"
}
```

### Resposta de Erro
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Dados inválidos fornecidos",
    "details": [
      {
        "field": "email",
        "message": "O campo email é obrigatório"
      }
    ]
  },
  "timestamp": "2025-07-08T10:30:00Z"
}
```

## Códigos de Status HTTP

| Código | Significado | Uso |
|--------|-------------|-----|
| 200 | OK | Operação realizada com sucesso |
| 201 | Created | Recurso criado com sucesso |
| 400 | Bad Request | Dados inválidos na requisição |
| 401 | Unauthorized | Token inválido ou expirado |
| 403 | Forbidden | Sem permissão para acessar o recurso |
| 404 | Not Found | Recurso não encontrado |
| 422 | Unprocessable Entity | Erro de validação |
| 500 | Internal Server Error | Erro interno do servidor |

---

# 1. MÓDULO DE AUTENTICAÇÃO E IDENTIDADE DIGITAL

## 1.1 Autenticação Básica

### POST /auth/login
Realiza login no sistema

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "usuario@exemplo.com",
  "password": "senha123",
  "remember": true,
  "device_info": {
    "device_id": "uuid-device",
    "platform": "web",
    "user_agent": "Mozilla/5.0..."
  }
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "name": "João Silva",
      "email": "joao@exemplo.com",
      "profile": "PARLAMENTAR",
      "permissions": ["parlamentar.read", "parlamentar.write"],
      "avatar": "https://exemplo.com/avatar.jpg",
      "two_factor_enabled": true,
      "blockchain_wallet": "0x1234567890abcdef"
    }
  }
}
```

### POST /auth/register
Registra novo usuário

**Request Body:**
```json
{
  "name": "Maria Santos",
  "email": "maria@exemplo.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "cpf": "123.456.789-00",
  "phone": "+55 11 99999-9999",
  "profile": "CIDADAO_VERIFICADO",
  "accept_terms": true
}
```

### POST /auth/logout
Invalida token atual

**Headers:**
```
Authorization: Bearer {token}
```

### POST /auth/refresh
Renova token de acesso

**Request Body:**
```json
{
  "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

## 1.2 Autenticação Avançada

### POST /auth/2fa/enable
Habilita autenticação de dois fatores

### POST /auth/2fa/verify
Verifica código 2FA

### POST /auth/govbr/callback
Callback para integração gov.br

### POST /auth/certificate/validate
Valida certificado digital ICP-Brasil

---

# 2. MÓDULO DE GESTÃO DE USUÁRIOS

## 2.1 CRUD de Usuários

### GET /users
Lista usuários com filtros

**Query Parameters:**
```
?page=1&per_page=15&search=joão&profile=PARLAMENTAR&status=active&sort=created_at&order=desc
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "João Silva",
      "email": "joao@exemplo.com",
      "profile": "PARLAMENTAR",
      "status": "active",
      "last_login": "2025-07-08T10:00:00Z",
      "created_at": "2025-01-01T00:00:00Z",
      "permissions": ["parlamentar.read", "parlamentar.write"],
      "behavior_score": 85,
      "fraud_alerts": 0
    }
  ],
  "meta": {
    "total": 100,
    "page": 1,
    "per_page": 15,
    "total_pages": 7
  }
}
```

### GET /users/{id}
Obtém usuário específico

### POST /users
Cria novo usuário

### PUT /users/{id}
Atualiza usuário

### DELETE /users/{id}
Remove usuário

## 2.2 Análise Comportamental

### GET /users/{id}/behavior
Análise comportamental do usuário

### POST /users/{id}/fraud-check
Verificação de fraude

### GET /users/analytics/predictive
Perfis preditivos

---

# 3. MÓDULO DE PARLAMENTARES E ESTRUTURA

## 3.1 Parlamentares

### GET /parlamentares
Lista parlamentares

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 10,
      "name": "João Silva",
      "partido": "PT",
      "cargo": "Vereador",
      "mandato": {
        "inicio": "2021-01-01",
        "fim": "2024-12-31",
        "situacao": "ativo"
      },
      "comissoes": [
        {
          "id": 1,
          "nome": "Educação",
          "cargo": "Presidente"
        }
      ],
      "projetos_apresentados": 15,
      "votos_realizados": 230,
      "presenca_sessoes": 85.5,
      "avatar": "https://exemplo.com/avatar.jpg",
      "contato": {
        "email": "joao@camara.gov.br",
        "telefone": "+55 11 99999-9999",
        "gabinete": "Sala 101"
      },
      "redes_sociais": {
        "twitter": "@joaosilva",
        "facebook": "joaosilva",
        "instagram": "@joaosilva"
      },
      "biografia": "Vereador eleito em 2020...",
      "propostas_campanha": ["Educação", "Saúde", "Transporte"]
    }
  ]
}
```

### GET /parlamentares/{id}
Detalhes de parlamentar específico

### POST /parlamentares
Cria novo parlamentar

### PUT /parlamentares/{id}
Atualiza parlamentar

### GET /parlamentares/{id}/dashboard
Dashboard do parlamentar

### GET /parlamentares/{id}/workspace
Workspace digital do parlamentar

## 3.2 Partidos

### GET /partidos
Lista partidos políticos

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "sigla": "PT",
      "nome": "Partido dos Trabalhadores",
      "numero": 13,
      "presidente": "João Silva",
      "fundacao": "1980-02-10",
      "ideologia": "Centro-esquerda",
      "parlamentares_count": 12,
      "projetos_count": 45,
      "votos_favor": 234,
      "votos_contra": 89,
      "bancada": [
        {
          "id": 1,
          "nome": "João Silva",
          "cargo": "Líder da Bancada"
        }
      ],
      "logo": "https://exemplo.com/logo-pt.png",
      "site": "https://pt.org.br",
      "contato": {
        "email": "contato@pt.org.br",
        "telefone": "+55 11 3333-3333"
      }
    }
  ]
}
```

### GET /partidos/{id}
Detalhes de partido específico

### POST /partidos
Cria novo partido

### PUT /partidos/{id}
Atualiza partido

## 3.3 Mesa Diretora

### GET /mesa-diretora
Composição da mesa diretora

### POST /mesa-diretora/eleicao
Processo de eleição

### GET /mesa-diretora/atas
Atas da mesa diretora

## 3.4 Comissões

### GET /comissoes
Lista comissões

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nome": "Comissão de Educação",
      "tipo": "permanente",
      "status": "ativa",
      "presidente": {
        "id": 1,
        "nome": "João Silva"
      },
      "vice_presidente": {
        "id": 2,
        "nome": "Maria Santos"
      },
      "membros": [
        {
          "id": 1,
          "nome": "João Silva",
          "cargo": "Presidente"
        },
        {
          "id": 2,
          "nome": "Maria Santos",
          "cargo": "Vice-Presidente"
        }
      ],
      "reunioes_agendadas": 5,
      "projetos_tramitando": 12,
      "pareceres_pendentes": 8,
      "calendario": [
        {
          "data": "2025-07-10",
          "horario": "14:00",
          "pauta": "Análise do Projeto 001/2025"
        }
      ],
      "contato": {
        "email": "educacao@camara.gov.br",
        "telefone": "+55 11 3333-3333"
      }
    }
  ]
}
```

### GET /comissoes/{id}
Detalhes de comissão específica

### POST /comissoes
Cria nova comissão

### PUT /comissoes/{id}
Atualiza comissão

### GET /comissoes/{id}/reunioes
Reuniões da comissão

### POST /comissoes/{id}/reunioes
Agenda nova reunião

---

# 4. MÓDULO DE DOCUMENTOS E PROJETOS

## 4.1 Projetos de Lei

### GET /projetos
Lista projetos de lei

**Query Parameters:**
```
?page=1&per_page=15&search=educação&tipo=PL&status=tramitando&autor=1&ano=2025&sort=created_at&order=desc
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "numero": "001/2025",
      "tipo": "PL",
      "titulo": "Projeto de Lei sobre Educação Digital",
      "ementa": "Dispõe sobre a implementação de educação digital nas escolas municipais",
      "status": "tramitando",
      "fase_atual": "Comissão de Educação",
      "urgencia": "normal",
      "autor": {
        "id": 1,
        "nome": "João Silva",
        "tipo": "parlamentar"
      },
      "data_apresentacao": "2025-01-15",
      "data_prazo": "2025-12-15",
      "tags": ["educação", "digital", "tecnologia"],
      "texto_integral": "https://docs.camara.gov.br/001-2025.pdf",
      "anexos": [
        {
          "id": 1,
          "nome": "Justificativa.pdf",
          "url": "https://docs.camara.gov.br/anexo-1.pdf"
        }
      ],
      "tramitacao": [
        {
          "data": "2025-01-15",
          "local": "Mesa Diretora",
          "acao": "Apresentado",
          "observacoes": "Projeto protocolado"
        },
        {
          "data": "2025-01-20",
          "local": "Comissão de Educação",
          "acao": "Distribuído",
          "observacoes": "Encaminhado para parecer"
        }
      ],
      "relator": {
        "id": 2,
        "nome": "Maria Santos",
        "prazo_parecer": "2025-02-15"
      },
      "votos": {
        "favor": 12,
        "contra": 3,
        "abstencao": 2
      },
      "impacto_financeiro": {
        "tem_impacto": true,
        "valor_estimado": 1000000.00,
        "fonte_recurso": "Orçamento Municipal"
      }
    }
  ]
}
```

### GET /projetos/{id}
Detalhes de projeto específico

### POST /projetos
Cria novo projeto

### PUT /projetos/{id}
Atualiza projeto

### DELETE /projetos/{id}
Remove projeto

### GET /projetos/{id}/tramitacao
Histórico de tramitação

### POST /projetos/{id}/tramitacao
Adiciona tramitação

### GET /projetos/{id}/emendas
Emendas do projeto

### POST /projetos/{id}/emendas
Adiciona emenda

## 4.2 Relatórios e Pareceres

### GET /relatorias
Lista relatórias

### GET /relatorias/{id}/parecer
Parecer da relatoria

### POST /relatorias/{id}/parecer
Submete parecer

### GET /projetos/{id}/pareceres
Pareceres do projeto

## 4.3 Arquivo Digital

### GET /arquivo/documentos
Busca no arquivo histórico

### GET /arquivo/documentos/{id}
Documento específico

### POST /arquivo/upload
Upload de documento

---

# 5. MÓDULO DE SESSÕES E VOTAÇÃO

## 5.1 Sessões Plenárias

### GET /sessoes
Lista sessões

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "numero": "001/2025",
      "tipo": "ordinaria",
      "data": "2025-07-10",
      "horario_inicio": "14:00",
      "horario_fim": "18:00",
      "status": "agendada",
      "presidente": {
        "id": 1,
        "nome": "João Silva"
      },
      "secretario": {
        "id": 2,
        "nome": "Maria Santos"
      },
      "pauta": [
        {
          "ordem": 1,
          "tipo": "projeto",
          "projeto_id": 1,
          "titulo": "PL 001/2025 - Educação Digital",
          "autor": "João Silva",
          "tempo_estimado": 30
        }
      ],
      "presencas": [
        {
          "parlamentar_id": 1,
          "nome": "João Silva",
          "presente": true,
          "horario_chegada": "14:00",
          "horario_saida": null
        }
      ],
      "votacoes": [
        {
          "projeto_id": 1,
          "resultado": "aprovado",
          "votos_favor": 12,
          "votos_contra": 3,
          "abstencoes": 2
        }
      ],
      "ata": {
        "id": 1,
        "url": "https://docs.camara.gov.br/ata-001.pdf",
        "aprovada": true
      },
      "transmissao": {
        "ao_vivo": true,
        "url_stream": "https://stream.camara.gov.br/live",
        "gravacao": "https://videos.camara.gov.br/sessao-001"
      },
      "quorum": {
        "minimo": 9,
        "presente": 17,
        "suficiente": true
      }
    }
  ]
}
```

### GET /sessoes/{id}
Detalhes de sessão específica

### POST /sessoes
Cria nova sessão

### PUT /sessoes/{id}
Atualiza sessão

### POST /sessoes/{id}/iniciar
Inicia sessão

### POST /sessoes/{id}/encerrar
Encerra sessão

### GET /sessoes/{id}/pauta
Pauta da sessão

### POST /sessoes/{id}/pauta
Adiciona item à pauta

### GET /sessoes/{id}/presenca
Controle de presença

### POST /sessoes/{id}/presenca
Registra presença

## 5.2 Votações

### GET /votacoes
Lista votações

### GET /votacoes/{id}
Detalhes de votação

### POST /votacoes
Cria nova votação

### POST /votacoes/{id}/votar
Registra voto

**Request Body:**
```json
{
  "parlamentar_id": 1,
  "voto": "favor", // favor, contra, abstencao
  "justificativa": "Voto favorável pela importância da educação digital",
  "blockchain_hash": "0x1234567890abcdef"
}
```

### GET /votacoes/{id}/resultado
Resultado da votação

### GET /parlamentares/{id}/votos
Histórico de votos do parlamentar

---

# 6. MÓDULO DE COMISSÕES DIGITAIS

## 6.1 Reuniões de Comissão

### GET /comissoes/{id}/reunioes
Reuniões da comissão

### POST /comissoes/{id}/reunioes
Agenda reunião

### GET /reunioes/{id}
Detalhes da reunião

### POST /reunioes/{id}/iniciar
Inicia reunião

### POST /reunioes/{id}/encerrar
Encerra reunião

### GET /reunioes/{id}/ata
Ata da reunião

### POST /reunioes/{id}/ata
Submete ata

---

# 7. MÓDULO DE TRANSPARÊNCIA E ENGAJAMENTO

## 7.1 Portal do Cidadão

### GET /transparencia/parlamentares
Dados públicos dos parlamentares

### GET /transparencia/projetos
Projetos em tramitação

### GET /transparencia/votacoes
Votações públicas

### GET /transparencia/gastos
Gastos públicos

### GET /transparencia/agenda
Agenda pública

## 7.2 Participação Cidadã

### GET /participacao/consultas
Consultas públicas

### POST /participacao/consultas/{id}/responder
Responde consulta

### GET /participacao/audiencias
Audiências públicas

### POST /participacao/audiencias/{id}/inscrever
Inscreve em audiência

### GET /participacao/propostas
Propostas cidadãs

### POST /participacao/propostas
Submete proposta

---

# 8. MÓDULO DE ANALYTICS E INTELIGÊNCIA (INTEGRAÇÃO)

## 8.1 Exportação de Dados

### POST /analytics/export
Exporta dados para análise

**Request Body:**
```json
{
  "tipo": "votacoes",
  "periodo": {
    "inicio": "2025-01-01",
    "fim": "2025-12-31"
  },
  "formato": "json",
  "filtros": {
    "parlamentar_id": [1, 2, 3],
    "projeto_tipo": ["PL", "PEC"]
  }
}
```

### GET /analytics/widgets/{type}
Dados para widgets

### POST /analytics/webhook
Recebe webhooks do sistema de analytics

## 8.2 Dashboards

### GET /dashboards/parlamentar/{id}
Dashboard do parlamentar

### GET /dashboards/geral
Dashboard geral

### GET /dashboards/transparencia
Dashboard de transparência

---

# 9. MÓDULO DE APIs E INTEGRAÇÕES

## 9.1 API Management

### GET /api/endpoints
Lista endpoints disponíveis

### POST /api/keys
Gera chave de API

### GET /api/usage
Uso da API

### GET /api/docs
Documentação da API

---

# 10. MÓDULO DE NOTIFICAÇÕES E COMUNICAÇÃO

## 10.1 Notificações

### GET /notificacoes
Lista notificações do usuário

### POST /notificacoes
Cria notificação

### PUT /notificacoes/{id}/lida
Marca como lida

### GET /notificacoes/configuracoes
Configurações de notificação

### PUT /notificacoes/configuracoes
Atualiza configurações

## 10.2 Comunicação

### GET /mensagens
Lista mensagens

### POST /mensagens
Envia mensagem

### GET /mensagens/{id}
Detalhes da mensagem

---

# 11. MÓDULO DE SEGURANÇA E COMPLIANCE

## 11.1 Logs de Auditoria

### GET /auditoria/logs
Logs de auditoria

### GET /auditoria/relatorio
Relatório de auditoria

### POST /auditoria/evento
Registra evento

## 11.2 Compliance

### GET /compliance/status
Status de compliance

### GET /compliance/relatorio
Relatório de compliance

---

# 12. MÓDULO DE BLOCKCHAIN E AUDITORIA

## 12.1 Blockchain

### GET /blockchain/transacoes
Transações blockchain

### POST /blockchain/validar
Valida transação

### GET /blockchain/explorer
Explorador blockchain

---

# 13. MÓDULO DE COMUNICAÇÃO E COLABORAÇÃO

## 13.1 Rede Social Parlamentar

### GET /social/feed
Feed de atividades

### POST /social/post
Publica post

### GET /social/parlamentares/{id}/posts
Posts do parlamentar

---

# 14. MÓDULO DE EDUCAÇÃO E CAPACITAÇÃO

## 14.1 Cursos

### GET /educacao/cursos
Lista cursos

### POST /educacao/cursos/{id}/inscrever
Inscreve em curso

### GET /educacao/progresso
Progresso do usuário

---

# 15. MÓDULO DE INTELIGÊNCIA ARTIFICIAL

## 15.1 Assistente IA

### POST /ai/chat
Chat com assistente

### POST /ai/sugerir
Sugestões da IA

### GET /ai/historico
Histórico de interações

## 15.2 Análise de Texto

### POST /ai/analisar-projeto
Análise de projeto

### POST /ai/resumir
Resumo de documento

---

# 16. MÓDULO DE GESTÃO DE CRISES

## 16.1 Alertas

### GET /crises/alertas
Lista alertas

### POST /crises/alertas
Cria alerta

### GET /crises/planos
Planos de contingência

---

# 17. MÓDULO DE INOVAÇÃO E LABORATÓRIO

## 17.1 Experimentos

### GET /lab/experimentos
Lista experimentos

### POST /lab/experimentos
Cria experimento

### GET /lab/resultados
Resultados dos experimentos

---

# 18. MÓDULO DE SUSTENTABILIDADE

## 18.1 Métricas Ambientais

### GET /sustentabilidade/metricas
Métricas ambientais

### GET /sustentabilidade/relatorio
Relatório de sustentabilidade

---

# 19. MÓDULO DE ACESSIBILIDADE AVANÇADA

## 19.1 Recursos de Acessibilidade

### GET /acessibilidade/recursos
Recursos disponíveis

### POST /acessibilidade/configurar
Configura acessibilidade

---

# 20. MÓDULO DE GAMIFICAÇÃO E ENGAJAMENTO

## 20.1 Gamificação

### GET /gamificacao/pontuacao
Pontuação do usuário

### GET /gamificacao/ranking
Ranking de participação

### POST /gamificacao/acao
Registra ação do usuário

### GET /gamificacao/conquistas
Conquistas do usuário

---

# Webhooks

## Eventos Disponíveis

### Autenticação
- `user.login`
- `user.logout`
- `user.register`

### Projetos
- `projeto.created`
- `projeto.updated`
- `projeto.tramitacao.updated`
- `projeto.votacao.iniciada`
- `projeto.votacao.finalizada`

### Sessões
- `sessao.iniciada`
- `sessao.encerrada`
- `votacao.iniciada`
- `votacao.finalizada`

### Usuários
- `user.created`
- `user.updated`
- `user.deleted`

## Configuração de Webhook

### POST /webhooks
Registra webhook

**Request Body:**
```json
{
  "url": "https://meu-sistema.com/webhook",
  "events": ["projeto.created", "votacao.finalizada"],
  "secret": "minha-chave-secreta",
  "active": true
}
```

### Estrutura do Payload

```json
{
  "event": "projeto.created",
  "data": {
    "id": 1,
    "numero": "001/2025",
    "titulo": "Projeto de Lei sobre Educação Digital"
  },
  "timestamp": "2025-07-08T10:30:00Z",
  "signature": "sha256=..."
}
```

---

# Autenticação e Autorização

## Perfis de Usuário

| Perfil | Código | Permissões |
|--------|--------|------------|
| Administrador | `ADMIN` | Acesso total |
| Legislativo | `LEGISLATIVO` | Gerenciamento técnico |
| Parlamentar | `PARLAMENTAR` | Projetos e votações |
| Relator | `RELATOR` | Pareceres e relatórias |
| Assessor | `ASSESSOR` | Apoio parlamentar |
| Cidadão Verificado | `CIDADAO_VERIFICADO` | Participação cidadã |
| Público | `PUBLICO` | Consulta pública |

## Permissões

As permissões seguem o padrão: `recurso.acao`

Exemplos:
- `parlamentar.read`
- `projeto.write`
- `votacao.create`
- `sessao.manage`

---

# Integração com Sistema Laravel

## NodeApiClient

O sistema Laravel utiliza o `NodeApiClient` para comunicação:

```php
// Exemplo de uso
$client = new NodeApiClient();
$response = $client->get('/parlamentares');
$parlamentares = $response->data;
```

## Configuração de Ambiente

```env
# Desenvolvimento
API_MODE=external
API_BASE_URL=http://localhost:3000

# Produção
API_MODE=external
API_BASE_URL=https://api.parlamentar.gov.br
```

---

# Considerações Técnicas

## Rate Limiting
- 1000 requests/hour por usuário autenticado
- 100 requests/hour para usuários não autenticados
- Headers de resposta: `X-RateLimit-Limit`, `X-RateLimit-Remaining`

## Paginação
- Padrão: 15 itens por página
- Máximo: 100 itens por página
- Parâmetros: `page`, `per_page`

## Cache
- Cache de 5 minutos para dados públicos
- Cache de 1 minuto para dados privados
- Headers: `Cache-Control`, `ETag`

## Monitoramento
- Logs de acesso
- Métricas de performance
- Alertas de erro

## Segurança
- HTTPS obrigatório
- Validação de entrada
- Sanitização de dados
- Audit logs
- Rate limiting
- CORS configurado

---
