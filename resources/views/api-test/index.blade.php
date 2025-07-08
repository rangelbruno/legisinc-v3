<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Client Test Interface</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .config-info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .config-info h3 { margin-top: 0; color: #1976d2; }
        .config-item { margin: 5px 0; }
        .config-item strong { color: #333; }
        .status { padding: 10px; border-radius: 5px; margin: 10px 0; }
        .status.success { background: #d4edda; color: #155724; }
        .status.error { background: #f8d7da; color: #721c24; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .btn.secondary { background: #6c757d; }
        .btn.secondary:hover { background: #545b62; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 API Client Test Interface</h1>
        
        <div class="config-info">
            <h3>📋 Current Configuration</h3>
            <div class="config-item"><strong>Provider:</strong> {{ $config['provider_name'] }}</div>
            <div class="config-item"><strong>Base URL:</strong> {{ $config['base_url'] }}</div>
            <div class="config-item"><strong>Timeout:</strong> {{ $config['timeout'] }}s</div>
            <div class="config-item"><strong>Retries:</strong> {{ $config['retries'] }}</div>
            <div class="config-item"><strong>Cache TTL:</strong> {{ $config['cache_ttl'] }}s</div>
        </div>

        <div>
            <h3>🧪 Quick Tests</h3>
            <button class="btn" onclick="testHealthCheck()">Health Check</button>
            <button class="btn secondary" onclick="testProvider()">Test Provider</button>
            <button class="btn secondary" onclick="showProviderInfo()">Provider Info</button>
        </div>

        <div id="results" style="margin-top: 20px;"></div>

        <div style="margin-top: 30px;">
            <h3>📚 Available Commands</h3>
            <p><strong>Artisan Commands:</strong></p>
            <ul>
                <li><code>php artisan api:test</code> - Basic API test</li>
                <li><code>php artisan api:test --provider=jsonplaceholder</code> - Test specific provider</li>
                <li><code>php artisan api:test --health</code> - Health check only</li>
                <li><code>php artisan api:test-node --full</code> - Full Node.js API test</li>
            </ul>
        </div>
    </div>

    <script>
        function showResult(result, isSuccess = true) {
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = `<div class="status ${isSuccess ? 'success' : 'error'}">${result}</div>`;
        }

        async function testHealthCheck() {
            showResult('Testing health check...', true);
            try {
                const response = await fetch('/api-test/health');
                const data = await response.json();
                if (data.success) {
                    showResult(`✅ Health Check: ${data.healthy ? 'PASSED' : 'FAILED'} - ${data.message}`, data.healthy);
                } else {
                    showResult(`❌ Health Check Failed: ${data.error}`, false);
                }
            } catch (error) {
                showResult(`❌ Request Failed: ${error.message}`, false);
            }
        }

        async function testProvider() {
            showResult('Testing provider...', true);
            try {
                const response = await fetch('/api-test/get', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        endpoint: '/posts/1'
                    })
                });
                const data = await response.json();
                if (data.success) {
                    showResult(`✅ Provider Test: SUCCESS - Status: ${data.response.status_code}`, true);
                } else {
                    showResult(`❌ Provider Test Failed: ${data.error}`, false);
                }
            } catch (error) {
                showResult(`❌ Request Failed: ${error.message}`, false);
            }
        }

        async function showProviderInfo() {
            showResult('Getting provider info...', true);
            try {
                const response = await fetch('/api-test/provider-info');
                const data = await response.json();
                if (data.success) {
                    const providers = data.available_providers.join(', ');
                    showResult(`📋 Available Providers: ${providers}`, true);
                } else {
                    showResult(`❌ Failed to get provider info`, false);
                }
            } catch (error) {
                showResult(`❌ Request Failed: ${error.message}`, false);
            }
        }
    </script>
</body>
</html> 