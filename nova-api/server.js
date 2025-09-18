const express = require('express');
const cors = require('cors');
const morgan = require('morgan');

const app = express();
const PORT = process.env.PORT || 3001;

// Middleware
app.use(cors());
app.use(express.json());
app.use(morgan('combined'));

// Request logging para shadow/canary traffic
app.use((req, res, next) => {
  const isShadow = req.headers['x-shadow-request'] === 'true';
  const requestId = `req-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

  req.requestId = requestId;
  req.isShadow = isShadow;

  // Log estruturado
  if (isShadow) {
    console.log(`[SHADOW ${requestId}] ${req.method} ${req.path}`, {
      query: req.query,
      timestamp: new Date().toISOString()
    });
  } else {
    console.log(`[CANARY ${requestId}] ${req.method} ${req.path} - TRÁFEGO REAL!`, {
      query: req.query,
      timestamp: new Date().toISOString()
    });
  }

  next();
});

// ====================================
// HEALTH CHECK
// ====================================
app.get('/health', (req, res) => {
  res.json({
    status: 'healthy',
    service: 'nova-api',
    version: '2.0.0',
    timestamp: new Date().toISOString(),
    mode: req.isShadow ? 'shadow' : 'canary',
    checks: {
      api: { status: 'ok', message: 'Nova API funcionando' },
      memory: {
        status: 'ok',
        usage: `${Math.round(process.memoryUsage().heapUsed / 1024 / 1024)}MB`
      }
    }
  });
});

// ====================================
// ENDPOINT PRODUÇÃO: parlamentares/buscar
// 100% COMPATÍVEL COM LARAVEL
// ====================================
app.get('/api/parlamentares/buscar', (req, res) => {
  const query = req.query.q || '';

  // Validação EXATAMENTE igual ao Laravel
  if (query.length > 0 && query.length < 2) {
    return res.json({
      success: false,
      message: "Termo de busca deve ter pelo menos 2 caracteres",
      parlamentares: []
    });
  }

  // Simular comportamento atual do Laravel (sempre vazio)
  // Em produção real, faria query no banco
  const parlamentares = [];

  // Formato EXATAMENTE compatível com Laravel
  res.json({
    success: true,
    parlamentares: parlamentares,
    total: parlamentares.length,
    message: parlamentares.length === 0 ?
      "Nenhum parlamentar encontrado" :
      `${parlamentares.length} parlamentar(es) encontrado(s)`
  });
});

// ====================================
// ENDPOINT TESTE: tipos-proposicao
// ====================================
app.get('/api/tipos-proposicao', (req, res) => {
  const tipos = [
    {
      id: 1,
      nome: "Projeto de Lei",
      sigla: "PL",
      descricao: "Projeto de Lei Ordinária",
      ativo: true,
      created_at: "2024-01-01T00:00:00.000Z",
      updated_at: "2024-01-01T00:00:00.000Z"
    },
    {
      id: 2,
      nome: "Projeto de Lei Complementar",
      sigla: "PLC",
      descricao: "Projeto de Lei Complementar",
      ativo: true,
      created_at: "2024-01-01T00:00:00.000Z",
      updated_at: "2024-01-01T00:00:00.000Z"
    },
    {
      id: 6,
      nome: "Moção",
      sigla: "MOC",
      descricao: "Moção de diversos tipos",
      ativo: true,
      created_at: "2024-01-01T00:00:00.000Z",
      updated_at: "2024-01-01T00:00:00.000Z"
    }
  ];

  res.json({
    success: true,
    data: tipos,
    meta: {
      total: tipos.length,
      source: 'nova-api',
      timestamp: new Date().toISOString()
    }
  });
});

// ====================================
// CATCH ALL para outras rotas
// ====================================
app.use('*', (req, res) => {
  console.log(`[${req.isShadow ? 'SHADOW' : 'CANARY'} ${req.requestId}] Rota não implementada: ${req.method} ${req.originalUrl}`);

  res.status(404).json({
    success: false,
    error: 'Endpoint não implementado na Nova API',
    method: req.method,
    path: req.originalUrl,
    source: 'nova-api',
    mode: req.isShadow ? 'shadow' : 'canary'
  });
});

// Error handler
app.use((err, req, res, next) => {
  console.error(`[ERROR ${req.requestId}] Erro na Nova API:`, err);

  res.status(500).json({
    success: false,
    error: 'Erro interno da Nova API',
    message: err.message,
    source: 'nova-api',
    requestId: req.requestId
  });
});

// Iniciar servidor
app.listen(PORT, '0.0.0.0', () => {
  console.log(`🚀 Nova API v2.0.0 rodando na porta ${PORT}`);
  console.log(`📊 Health check: http://localhost:${PORT}/health`);
  console.log(`🔍 Shadow/Canary traffic logging habilitado`);
  console.log(`🎯 Pronto para canary deployment!`);
});

// Graceful shutdown
process.on('SIGTERM', () => {
  console.log('🛑 Nova API sendo finalizada...');
  process.exit(0);
});

// Metrics endpoint para Prometheus (futuro)
app.get('/metrics', (req, res) => {
  res.set('Content-Type', 'text/plain');
  res.send('# Nova API Metrics\n# TODO: Implementar métricas Prometheus\n');
});