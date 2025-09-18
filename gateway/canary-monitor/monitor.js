// Canary Deployment Monitor
// Monitora métricas e controla % do canary automaticamente

const express = require('express');
const axios = require('axios');
const fs = require('fs');
const path = require('path');

const app = express();
app.use(express.json());

const PORT = 3003;
const TRAEFIK_API = process.env.TRAEFIK_API || 'http://localhost:8090';
const PROMETHEUS_API = process.env.PROMETHEUS_API || 'http://localhost:9090';

// Estado do canary
let canaryState = {
  enabled: true,
  percentage: 1,  // Começar com 1%
  target_percentage: 1,
  last_update: new Date().toISOString(),
  health: 'unknown',
  metrics: {
    error_rate: 0,
    avg_latency: 0,
    request_count: 0
  },
  rollback_triggered: false,
  auto_scaling: true
};

// Histórico de métricas
let metricsHistory = [];

// ====================================
// FUNÇÕES DE MONITORAMENTO
// ====================================

async function getCanaryMetrics() {
  try {
    // Query Prometheus para métricas da Nova API
    const errorRateQuery = `
      rate(traefik_service_requests_total{service="nova-api-svc@docker",code=~"5.."}[5m]) /
      rate(traefik_service_requests_total{service="nova-api-svc@docker"}[5m]) * 100
    `;

    const latencyQuery = `
      histogram_quantile(0.95,
        rate(traefik_service_request_duration_seconds_bucket{service="nova-api-svc@docker"}[5m])
      ) * 1000
    `;

    const requestCountQuery = `
      sum(rate(traefik_service_requests_total{service="nova-api-svc@docker"}[5m]))
    `;

    // Fazer queries ao Prometheus (simulado por enquanto)
    const metrics = {
      error_rate: Math.random() * 2,  // 0-2% erro
      avg_latency: 100 + Math.random() * 50,  // 100-150ms
      request_count: Math.floor(Math.random() * 10) + 1,  // 1-10 req/s
      timestamp: new Date().toISOString()
    };

    canaryState.metrics = metrics;
    metricsHistory.push(metrics);

    // Manter apenas últimas 100 medições
    if (metricsHistory.length > 100) {
      metricsHistory = metricsHistory.slice(-100);
    }

    return metrics;
  } catch (error) {
    console.error('Erro ao coletar métricas:', error.message);
    return null;
  }
}

async function evaluateCanaryHealth() {
  const metrics = await getCanaryMetrics();
  if (!metrics) return 'unknown';

  // Critérios de saúde
  const healthCriteria = {
    error_rate_threshold: 5.0,      // Max 5% de erro
    latency_threshold: 500,         // Max 500ms latência
    min_requests: 1                 // Min 1 req para avaliar
  };

  let health = 'healthy';
  let issues = [];

  if (metrics.error_rate > healthCriteria.error_rate_threshold) {
    health = 'unhealthy';
    issues.push(`Error rate too high: ${metrics.error_rate.toFixed(2)}%`);
  }

  if (metrics.avg_latency > healthCriteria.latency_threshold) {
    health = 'degraded';
    issues.push(`Latency too high: ${metrics.avg_latency.toFixed(0)}ms`);
  }

  if (metrics.request_count < healthCriteria.min_requests) {
    health = 'insufficient_data';
    issues.push('Not enough requests to evaluate');
  }

  canaryState.health = health;
  canaryState.issues = issues;

  console.log(`[CANARY HEALTH] ${health.toUpperCase()} - ${issues.join(', ') || 'All good'}`);

  return health;
}

async function autoScaleCanary() {
  if (!canaryState.auto_scaling || canaryState.rollback_triggered) return;

  const health = await evaluateCanaryHealth();

  switch (health) {
    case 'healthy':
      // Aumentar gradualmente se estável por 5 minutos
      if (canaryState.percentage < 100) {
        const nextPercentage = Math.min(canaryState.percentage * 2, 100);
        await updateCanaryPercentage(nextPercentage);
      }
      break;

    case 'degraded':
      // Manter atual, não escalar
      console.log(`[CANARY] Mantendo ${canaryState.percentage}% devido à degradação`);
      break;

    case 'unhealthy':
      // Rollback para 0%
      console.log(`[CANARY] ROLLBACK! Health: ${health}`);
      await rollbackCanary();
      break;

    case 'insufficient_data':
      // Aguardar mais dados
      console.log(`[CANARY] Aguardando mais dados para avaliação`);
      break;
  }
}

async function updateCanaryPercentage(newPercentage) {
  try {
    console.log(`[CANARY] Atualizando de ${canaryState.percentage}% para ${newPercentage}%`);

    // Atualizar configuração do Traefik
    const newConfig = await generateTraefikConfig(newPercentage);
    await updateTraefikConfig(newConfig);

    canaryState.percentage = newPercentage;
    canaryState.target_percentage = newPercentage;
    canaryState.last_update = new Date().toISOString();

    // Log da mudança
    logCanaryChange(`Updated canary to ${newPercentage}%`);

    return true;
  } catch (error) {
    console.error('Erro ao atualizar canary:', error.message);
    return false;
  }
}

async function rollbackCanary() {
  try {
    console.log('[CANARY] EXECUTANDO ROLLBACK DE EMERGÊNCIA!');

    canaryState.rollback_triggered = true;
    canaryState.auto_scaling = false;

    await updateCanaryPercentage(0);

    // Notificar equipe (simulado)
    console.log('[ALERT] Canary deployment foi revertido devido a problemas de saúde');

    logCanaryChange('EMERGENCY ROLLBACK - Health issues detected');

    return true;
  } catch (error) {
    console.error('ERRO CRÍTICO no rollback:', error.message);
    return false;
  }
}

function generateTraefikConfig(percentage) {
  return `
http:
  services:
    parlamentares-weighted:
      weighted:
        services:
          - name: "nova-api-svc@docker"
            weight: ${percentage}
          - name: "laravel-svc@docker"
            weight: ${100 - percentage}
`;
}

async function updateTraefikConfig(config) {
  // Em produção, salvaria no arquivo que Traefik monitora
  const configPath = '/etc/traefik/canary/canary-routes.yml';
  console.log(`[CONFIG] Atualizando configuração Traefik: ${canaryState.percentage}%`);

  // Por agora, apenas simular
  return true;
}

function logCanaryChange(message) {
  const logEntry = {
    timestamp: new Date().toISOString(),
    message,
    state: { ...canaryState },
    metrics: canaryState.metrics
  };

  const logFile = path.join(__dirname, 'logs', 'canary.log');
  fs.mkdirSync(path.dirname(logFile), { recursive: true });
  fs.appendFileSync(logFile, JSON.stringify(logEntry) + '\n');
}

// ====================================
// ENDPOINTS da API
// ====================================

// Status do canary
app.get('/status', (req, res) => {
  res.json({
    ...canaryState,
    uptime: process.uptime(),
    version: '1.0.0'
  });
});

// Métricas históricas
app.get('/metrics/history', (req, res) => {
  const limit = parseInt(req.query.limit) || 50;
  res.json(metricsHistory.slice(-limit));
});

// Atualizar % do canary manualmente
app.post('/canary/update', async (req, res) => {
  const { percentage } = req.body;

  if (percentage < 0 || percentage > 100) {
    return res.status(400).json({ error: 'Percentage deve estar entre 0 e 100' });
  }

  const success = await updateCanaryPercentage(percentage);

  res.json({
    success,
    message: `Canary ${success ? 'atualizado' : 'falhou'} para ${percentage}%`,
    current_state: canaryState
  });
});

// Rollback manual
app.post('/canary/rollback', async (req, res) => {
  const success = await rollbackCanary();

  res.json({
    success,
    message: success ? 'Rollback executado' : 'Rollback falhou',
    current_state: canaryState
  });
});

// Habilitar/desabilitar auto scaling
app.post('/canary/autoscale', (req, res) => {
  const { enabled } = req.body;
  canaryState.auto_scaling = enabled;
  canaryState.rollback_triggered = false;  // Reset flag

  res.json({
    message: `Auto scaling ${enabled ? 'habilitado' : 'desabilitado'}`,
    current_state: canaryState
  });
});

// Health check
app.get('/health', (req, res) => {
  res.json({
    status: 'healthy',
    service: 'canary-monitor',
    canary_health: canaryState.health,
    uptime: process.uptime()
  });
});

// ====================================
// INICIALIZAÇÃO
// ====================================

// Monitoramento automático a cada 30 segundos
setInterval(async () => {
  try {
    await autoScaleCanary();
  } catch (error) {
    console.error('Erro no monitoramento automático:', error.message);
  }
}, 30000);  // 30 segundos

// Iniciar servidor
app.listen(PORT, () => {
  console.log(`🔍 Canary Monitor rodando na porta ${PORT}`);
  console.log(`📊 Status: http://localhost:${PORT}/status`);
  console.log(`📈 Métricas: http://localhost:${PORT}/metrics/history`);
  console.log(`🎛️ Controle manual: POST /canary/update {percentage: 10}`);
  console.log(`⚠️ Rollback: POST /canary/rollback`);
  console.log(`🤖 Auto scaling: ${canaryState.auto_scaling ? 'HABILITADO' : 'DESABILITADO'}`);

  // Log inicial
  logCanaryChange(`Canary Monitor iniciado com ${canaryState.percentage}%`);
});

// Graceful shutdown
process.on('SIGTERM', () => {
  console.log('🛑 Canary Monitor sendo finalizado...');
  logCanaryChange('Canary Monitor shutting down');
  process.exit(0);
});

module.exports = { canaryState, updateCanaryPercentage, rollbackCanary };