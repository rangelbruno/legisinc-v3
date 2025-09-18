// Shadow Traffic Comparator
// Compara respostas entre Laravel (produção) e Nova API (shadow)

const express = require('express');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = 3002;

// Estado do comparador
let stats = {
  total_comparisons: 0,
  identical_responses: 0,
  different_responses: 0,
  nova_api_errors: 0,
  laravel_errors: 0,
  started_at: new Date().toISOString()
};

// Logs de divergências
const logFile = path.join(__dirname, 'logs', 'comparisons.json');

// Garantir que diretório de logs existe
if (!fs.existsSync(path.dirname(logFile))) {
  fs.mkdirSync(path.dirname(logFile), { recursive: true });
}

// ====================================
// FUNÇÕES AUXILIARES
// ====================================

function logComparison(data) {
  const logEntry = {
    timestamp: new Date().toISOString(),
    ...data
  };

  // Append ao arquivo de log
  fs.appendFileSync(logFile, JSON.stringify(logEntry) + '\n');
}

function compareResponses(laravel, novaApi, endpoint) {
  stats.total_comparisons++;

  // Verificar se ambas são sucessos
  const laravelSuccess = laravel.status >= 200 && laravel.status < 300;
  const novaApiSuccess = novaApi.status >= 200 && novaApi.status < 300;

  if (!laravelSuccess) {
    stats.laravel_errors++;
  }

  if (!novaApiSuccess) {
    stats.nova_api_errors++;
  }

  // Comparar JSONs (ignorar campos de timestamp)
  let identical = false;
  let differences = [];

  try {
    if (laravelSuccess && novaApiSuccess) {
      const laravelData = typeof laravel.data === 'string' ?
        JSON.parse(laravel.data) : laravel.data;
      const novaApiData = typeof novaApi.data === 'string' ?
        JSON.parse(novaApi.data) : novaApi.data;

      // Remover campos que podem variar
      const normalize = (obj) => {
        const normalized = JSON.parse(JSON.stringify(obj));
        if (normalized.meta) {
          delete normalized.meta.timestamp;
          delete normalized.meta.source;
        }
        return normalized;
      };

      const normalizedLaravel = normalize(laravelData);
      const normalizedNovaApi = normalize(novaApiData);

      const laravelStr = JSON.stringify(normalizedLaravel, Object.keys(normalizedLaravel).sort());
      const novaApiStr = JSON.stringify(normalizedNovaApi, Object.keys(normalizedNovaApi).sort());

      identical = laravelStr === novaApiStr;

      if (!identical) {
        differences = [
          'Response structure differs',
          `Laravel keys: ${Object.keys(normalizedLaravel).join(', ')}`,
          `Nova API keys: ${Object.keys(normalizedNovaApi).join(', ')}`
        ];
      }
    }
  } catch (error) {
    differences.push(`JSON parse error: ${error.message}`);
  }

  if (identical) {
    stats.identical_responses++;
  } else {
    stats.different_responses++;
  }

  // Log detalhado se houver diferenças
  if (!identical || !laravelSuccess || !novaApiSuccess) {
    logComparison({
      endpoint,
      identical,
      differences,
      laravel: {
        status: laravel.status,
        response_size: laravel.data ? laravel.data.length : 0,
        success: laravelSuccess
      },
      nova_api: {
        status: novaApi.status,
        response_size: novaApi.data ? novaApi.data.length : 0,
        success: novaApiSuccess
      },
      stats: { ...stats }
    });
  }

  return {
    identical,
    differences,
    stats: { ...stats }
  };
}

// ====================================
// ENDPOINTS
// ====================================

// Status do comparador
app.get('/status', (req, res) => {
  const successRate = stats.total_comparisons > 0 ?
    (stats.identical_responses / stats.total_comparisons * 100).toFixed(2) : 0;

  res.json({
    status: 'running',
    uptime: Math.floor((Date.now() - new Date(stats.started_at)) / 1000),
    success_rate: `${successRate}%`,
    ...stats
  });
});

// Comparação manual de endpoints
app.post('/compare/:endpoint', express.json(), async (req, res) => {
  const endpoint = req.params.endpoint;
  const { laravel_response, nova_api_response } = req.body;

  if (!laravel_response || !nova_api_response) {
    return res.status(400).json({
      error: 'Necessário laravel_response e nova_api_response no body'
    });
  }

  const result = compareResponses(laravel_response, nova_api_response, endpoint);

  res.json({
    endpoint,
    comparison_result: result
  });
});

// Logs recentes
app.get('/logs/:lines?', (req, res) => {
  const lines = parseInt(req.params.lines) || 50;

  try {
    if (!fs.existsSync(logFile)) {
      return res.json([]);
    }

    const content = fs.readFileSync(logFile, 'utf-8');
    const logLines = content.trim().split('\n').filter(line => line);
    const recentLines = logLines.slice(-lines);

    const logs = recentLines.map(line => {
      try {
        return JSON.parse(line);
      } catch {
        return { raw: line };
      }
    });

    res.json(logs);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Limpar logs
app.delete('/logs', (req, res) => {
  try {
    if (fs.existsSync(logFile)) {
      fs.unlinkSync(logFile);
    }

    // Reset stats
    stats = {
      total_comparisons: 0,
      identical_responses: 0,
      different_responses: 0,
      nova_api_errors: 0,
      laravel_errors: 0,
      started_at: new Date().toISOString()
    };

    res.json({ message: 'Logs limpos e stats resetados' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Health check
app.get('/health', (req, res) => {
  res.json({
    status: 'healthy',
    service: 'shadow-comparator',
    uptime: Math.floor((Date.now() - new Date(stats.started_at)) / 1000)
  });
});

// ====================================
// INICIALIZAÇÃO
// ====================================

app.listen(PORT, () => {
  console.log(`🔍 Shadow Comparator rodando na porta ${PORT}`);
  console.log(`📊 Status: http://localhost:${PORT}/status`);
  console.log(`📝 Logs: http://localhost:${PORT}/logs`);
  console.log(`❤️ Health: http://localhost:${PORT}/health`);
});

// Export para testes
module.exports = { compareResponses, stats };