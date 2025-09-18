const express = require('express');
const axios = require('axios');
const app = express();
const port = process.env.PORT || 3003;

app.use(express.json());

let metrics = {
  canary: { requests: 0, errors: 0, avgResponseTime: 0 },
  stable: { requests: 0, errors: 0, avgResponseTime: 0 }
};

app.get('/health', (req, res) => {
  res.json({ status: 'ok', service: 'canary-monitor' });
});

app.get('/metrics', (req, res) => {
  res.json(metrics);
});

async function monitorEndpoint(url, type) {
  const start = Date.now();
  try {
    await axios.get(`${url}/health`);
    const responseTime = Date.now() - start;

    metrics[type].requests++;
    metrics[type].avgResponseTime =
      (metrics[type].avgResponseTime + responseTime) / 2;

    console.log(`${type} - OK (${responseTime}ms)`);
  } catch (error) {
    metrics[type].errors++;
    console.log(`${type} - ERROR: ${error.message}`);
  }
}

setInterval(() => {
  const canaryUrl = process.env.CANARY_URL || 'http://app:80';
  const stableUrl = process.env.STABLE_URL || 'http://nginx-shadow:80';

  monitorEndpoint(canaryUrl, 'canary');
  monitorEndpoint(stableUrl, 'stable');
}, parseInt(process.env.METRICS_INTERVAL) || 30000);

app.listen(port, () => {
  console.log(`Canary monitor running on port ${port}`);
});