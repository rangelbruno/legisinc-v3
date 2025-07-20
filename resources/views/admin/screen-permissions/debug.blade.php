@extends('components.layouts.app')

@section('title', 'Debug Permissões')

@section('content')
<div class="container">
    <h1>Debug do Sistema de Permissões</h1>
    
    <div class="alert alert-info">
        <h4>Dados do Backend:</h4>
        <p><strong>Roles:</strong> {{ $roles->count() }} encontrados</p>
        <p><strong>Modules:</strong> {{ $modules->count() }} encontrados</p>
        <p><strong>Statistics:</strong> {{ json_encode($statistics) }}</p>
    </div>

    <div class="row">
        @foreach($roles as $role)
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5>{{ $role['label'] }}</h5>
                    <p>{{ $role['description'] }}</p>
                    <button class="btn btn-primary configure-btn" data-role="{{ $role['name'] }}">
                        Configurar {{ $role['name'] }}
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div id="debug-output" class="mt-4"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Debug script carregado');
    
    const roles = @json($roles);
    const modules = @json($modules);
    
    console.log('Roles:', roles);
    console.log('Modules:', modules);
    
    document.querySelectorAll('.configure-btn').forEach(btn => {
        console.log('Adicionando listener para:', btn.dataset.role);
        
        btn.addEventListener('click', function() {
            const role = this.dataset.role;
            console.log('Clicado em:', role);
            
            document.getElementById('debug-output').innerHTML = `
                <div class="alert alert-success">
                    <h4>Configurar Role: ${role}</h4>
                    <p>Testando URL: /admin/screen-permissions/role/${role}</p>
                </div>
            `;
            
            // Testar o fetch
            fetch(`/admin/screen-permissions/role/${role}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    document.getElementById('debug-output').innerHTML += `
                        <div class="alert alert-info">
                            <h5>Resposta do servidor:</h5>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('debug-output').innerHTML += `
                        <div class="alert alert-danger">
                            <h5>Erro:</h5>
                            <p>${error.message}</p>
                        </div>
                    `;
                });
        });
    });
});
</script>
@endsection