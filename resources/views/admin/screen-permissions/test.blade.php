<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Simples - Permissões</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Teste Simples - Sistema de Permissões</h1>
        
        <div class="alert alert-info">
            <h4>Debug Info:</h4>
            <p>Roles count: {{ is_countable($roles) ? count($roles) : 'not countable' }}</p>
            <p>Roles type: {{ gettype($roles) }}</p>
            <p>First role: {{ json_encode($roles->first() ?? 'none') }}</p>
        </div>

        <div class="row">
            @if($roles && count($roles) > 0)
                @foreach($roles as $role)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ $role['name'] ?? 'No name' }}</h5>
                            <p>{{ $role['label'] ?? 'No label' }}</p>
                            <button class="btn btn-primary test-btn" data-role="{{ $role['name'] ?? '' }}">
                                Teste {{ $role['name'] ?? 'Unknown' }}
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="alert alert-warning">
                        Nenhum role encontrado!
                    </div>
                </div>
            @endif
        </div>

        <!-- Painel de teste -->
        <div id="test-panel" class="card mt-4" style="display: none;">
            <div class="card-header">
                <h5>Painel de Teste: <span id="test-role-name"></span></h5>
            </div>
            <div class="card-body">
                <p id="test-role-description"></p>
                <div id="test-output"></div>
                <button id="test-save" class="btn btn-success">Testar Salvar</button>
            </div>
        </div>

        <!-- Console de debug -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>Debug Console</h5>
            </div>
            <div class="card-body">
                <pre id="debug-console"></pre>
            </div>
        </div>
    </div>

    <script>
    let debugLog = '';
    
    function addLog(message) {
        debugLog += new Date().toLocaleTimeString() + ': ' + message + '\n';
        document.getElementById('debug-console').textContent = debugLog;
        console.log(message);
    }

    document.addEventListener('DOMContentLoaded', function() {
        addLog('DOM carregado');
        
        const roles = @json($roles);
        addLog('Roles recebidos: ' + JSON.stringify(roles));
        
        const testPanel = document.getElementById('test-panel');
        
        // Event listeners para botões de teste
        document.querySelectorAll('.test-btn').forEach(btn => {
            addLog('Adicionando listener para botão: ' + btn.dataset.role);
            
            btn.addEventListener('click', function() {
                const role = this.dataset.role;
                addLog('Botão clicado para role: ' + role);
                
                try {
                    // Testar se o role existe nos dados
                    const roleData = roles[role];
                    addLog('RoleData encontrado: ' + JSON.stringify(roleData));
                    
                    if (!roleData) {
                        addLog('ERRO: Role não encontrado nos dados!');
                        return;
                    }
                    
                    // Atualizar painel
                    document.getElementById('test-role-name').textContent = roleData.label || role;
                    document.getElementById('test-role-description').textContent = roleData.description || 'Sem descrição';
                    
                    // Mostrar painel
                    testPanel.style.display = 'block';
                    addLog('Painel mostrado com sucesso');
                    
                    // Testar chamada para API
                    addLog('Testando chamada para API...');
                    fetch(`/admin/screen-permissions/role/${role}`)
                        .then(response => {
                            addLog('Response status: ' + response.status);
                            return response.json();
                        })
                        .then(data => {
                            addLog('API Response: ' + JSON.stringify(data));
                            document.getElementById('test-output').innerHTML = `
                                <div class="alert alert-success">
                                    <strong>API Response:</strong><br>
                                    <pre>${JSON.stringify(data, null, 2)}</pre>
                                </div>
                            `;
                        })
                        .catch(error => {
                            addLog('ERRO na API: ' + error.message);
                            document.getElementById('test-output').innerHTML = `
                                <div class="alert alert-danger">
                                    <strong>Erro na API:</strong> ${error.message}
                                </div>
                            `;
                        });
                        
                } catch (error) {
                    addLog('ERRO JavaScript: ' + error.message);
                }
            });
        });
        
        // Testar função de salvar
        document.getElementById('test-save').addEventListener('click', function() {
            addLog('Testando função de salvar...');
            
            const testData = {
                role: 'ADMIN',
                permissions: {
                    'dashboard': true,
                    'test.route': true
                }
            };
            
            addLog('Enviando dados: ' + JSON.stringify(testData));
            
            fetch('/admin/screen-permissions/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(testData)
            })
            .then(response => {
                addLog('Save response status: ' + response.status);
                return response.json();
            })
            .then(data => {
                addLog('Save response: ' + JSON.stringify(data));
                alert('Teste de salvamento: ' + (data.success ? 'SUCESSO' : 'ERRO'));
            })
            .catch(error => {
                addLog('ERRO no save: ' + error.message);
                alert('Erro no teste de salvamento: ' + error.message);
            });
        });
        
        addLog('Inicialização completa');
    });
    </script>
</body>
</html>