<x-layouts.app title="Teste Usuários">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        TESTE - Gestão de Usuários
                    </h1>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dados de Teste</h3>
                    </div>
                    <div class="card-body">
                        <h4>Usuários Count: {{ $usuarios->count() }}</h4>
                        <h4>Perfis Count: {{ count($perfis) }}</h4>
                        <h4>Total: {{ $estatisticas['total'] }}</h4>
                        
                        <h5>Usuários:</h5>
                        <ul>
                            @foreach($usuarios as $usuario)
                                <li>{{ $usuario->name }} - {{ $usuario->email }}</li>
                            @endforeach
                        </ul>
                        
                        <h5>Perfis:</h5>
                        <ul>
                            @foreach($perfis as $key => $nome)
                                <li>{{ $key }}: {{ $nome }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>