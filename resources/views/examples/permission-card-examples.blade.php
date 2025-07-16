@extends('components.layouts.app')

@section('title', 'Exemplos de Uso - Permission Card')

@section('content')
<div class="container-fluid">
    <div class="row mb-8">
        <div class="col-12">
            <h1 class="text-gray-900 fw-bold mb-4">Exemplos de Uso do Componente Permission Card</h1>
            <p class="text-gray-600 fs-5">Veja como usar o componente <code>&lt;x-permission-card&gt;</code> em diferentes cenários.</p>
        </div>
    </div>

    <!-- Exemplo 1: Uso Básico -->
    <div class="row mb-10">
        <div class="col-12">
            <h2 class="text-gray-800 fw-bold mb-4">1. Uso Básico</h2>
            <p class="text-gray-600 mb-6">Card simples com permissões básicas para gerenciamento de usuários.</p>
            
            <div class="d-flex flex-wrap gap-6">
                <x-permission-card 
                    :module="[
                        'value' => 'usuarios',
                        'label' => 'Usuários',
                        'color' => 'primary',
                        'iconClass' => 'ki-duotone ki-profile-circle',
                        'routes' => [
                            'users.index' => 'Listar Usuários',
                            'users.create' => 'Criar Usuário',
                            'users.edit' => 'Editar Usuário',
                            'users.show' => 'Visualizar Usuário'
                        ]
                    ]" 
                />
            </div>
        </div>
    </div>

    <!-- Exemplo 2: Card Somente Leitura -->
    <div class="row mb-10">
        <div class="col-12">
            <h2 class="text-gray-800 fw-bold mb-4">2. Card Somente Leitura</h2>
            <p class="text-gray-600 mb-6">Card desabilitado para visualização apenas (sem edição).</p>
            
            <div class="d-flex flex-wrap gap-6">
                <x-permission-card 
                    :module="[
                        'value' => 'relatorios',
                        'label' => 'Relatórios',
                        'color' => 'info',
                        'iconClass' => 'ki-duotone ki-chart-line',
                        'routes' => [
                            'reports.sales' => 'Relatório de Vendas',
                            'reports.users' => 'Relatório de Usuários',
                            'reports.analytics' => 'Analytics'
                        ]
                    ]"
                    :readonly="true"
                />
            </div>
        </div>
    </div>

    <!-- Exemplo 3: Card Sem Ações -->
    <div class="row mb-10">
        <div class="col-12">
            <h2 class="text-gray-800 fw-bold mb-4">3. Card Sem Ações</h2>
            <p class="text-gray-600 mb-6">Card que não mostra botões de ação (Criar, Editar, Excluir).</p>
            
            <div class="d-flex flex-wrap gap-6">
                <x-permission-card 
                    :module="[
                        'value' => 'dashboard',
                        'label' => 'Dashboard',
                        'color' => 'success',
                        'iconClass' => 'ki-duotone ki-element-11',
                        'routes' => [
                            'dashboard' => 'Painel Principal',
                            'dashboard.stats' => 'Estatísticas',
                            'dashboard.widgets' => 'Widgets'
                        ]
                    ]"
                    :show-actions="false"
                />
            </div>
        </div>
    </div>

    <!-- Exemplo 4: Diferentes Tamanhos -->
    <div class="row mb-10">
        <div class="col-12">
            <h2 class="text-gray-800 fw-bold mb-4">4. Diferentes Tamanhos</h2>
            <p class="text-gray-600 mb-6">Cards em diferentes tamanhos: pequeno, padrão e grande.</p>
            
            <div class="d-flex flex-wrap gap-6">
                <!-- Card Pequeno -->
                <x-permission-card 
                    :module="[
                        'value' => 'config',
                        'label' => 'Configurações',
                        'color' => 'warning',
                        'iconClass' => 'ki-duotone ki-setting-2',
                        'routes' => [
                            'config.general' => 'Geral',
                            'config.security' => 'Segurança'
                        ]
                    ]"
                    size="small"
                />

                <!-- Card Padrão -->
                <x-permission-card 
                    :module="[
                        'value' => 'produtos',
                        'label' => 'Produtos',
                        'color' => 'primary',
                        'iconClass' => 'ki-duotone ki-delivery-3',
                        'routes' => [
                            'products.index' => 'Listar Produtos',
                            'products.create' => 'Criar Produto',
                            'products.edit' => 'Editar Produto'
                        ]
                    ]"
                    size="default"
                />

                <!-- Card Grande -->
                <x-permission-card 
                    :module="[
                        'value' => 'vendas',
                        'label' => 'Vendas',
                        'color' => 'success',
                        'iconClass' => 'ki-duotone ki-dollar',
                        'routes' => [
                            'sales.index' => 'Listar Vendas',
                            'sales.create' => 'Nova Venda',
                            'sales.edit' => 'Editar Venda',
                            'sales.report' => 'Relatório de Vendas'
                        ]
                    ]"
                    size="large"
                />
            </div>
        </div>
    </div>

    <!-- Exemplo 5: Tema Escuro -->
    <div class="row mb-10">
        <div class="col-12">
            <h2 class="text-gray-800 fw-bold mb-4">5. Tema Escuro</h2>
            <p class="text-gray-600 mb-6">Cards com tema escuro para interfaces dark mode.</p>
            
            <div class="d-flex flex-wrap gap-6">
                <x-permission-card 
                    :module="[
                        'value' => 'admin',
                        'label' => 'Administração',
                        'color' => 'danger',
                        'iconClass' => 'ki-duotone ki-shield-tick',
                        'routes' => [
                            'admin.users' => 'Gerenciar Usuários',
                            'admin.permissions' => 'Permissões',
                            'admin.logs' => 'Logs do Sistema'
                        ]
                    ]"
                    theme="dark"
                />
            </div>
        </div>
    </div>

    <!-- Exemplo 6: Card com Conteúdo Personalizado -->
    <div class="row mb-10">
        <div class="col-12">
            <h2 class="text-gray-800 fw-bold mb-4">6. Card com Conteúdo Personalizado</h2>
            <p class="text-gray-600 mb-6">Card que aceita conteúdo customizado através de slots.</p>
            
            <div class="d-flex flex-wrap gap-6">
                <x-permission-card 
                    :module="[
                        'value' => 'notificacoes',
                        'label' => 'Notificações',
                        'color' => 'info',
                        'iconClass' => 'ki-duotone ki-notification-bing',
                        'routes' => [
                            'notifications.index' => 'Listar Notificações',
                            'notifications.create' => 'Criar Notificação'
                        ]
                    ]"
                >
                    <div class="alert alert-info d-flex align-items-center p-3">
                        <i class="ki-duotone ki-information fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div>
                            <strong>Informação:</strong> Este módulo permite gerenciar notificações push e emails.
                        </div>
                    </div>
                </x-permission-card>
            </div>
        </div>
    </div>

    <!-- Exemplo 7: Card Vazio -->
    <div class="row mb-10">
        <div class="col-12">
            <h2 class="text-gray-800 fw-bold mb-4">7. Card Vazio</h2>
            <p class="text-gray-600 mb-6">Card sem permissões para demonstrar estado vazio.</p>
            
            <div class="d-flex flex-wrap gap-6">
                <x-permission-card 
                    :module="[
                        'value' => 'em_construcao',
                        'label' => 'Em Construção',
                        'color' => 'secondary',
                        'iconClass' => 'ki-duotone ki-setting-3',
                        'routes' => []
                    ]"
                />
            </div>
        </div>
    </div>

    <!-- Código de Exemplo -->
    <div class="row mb-10">
        <div class="col-12">
            <h2 class="text-gray-800 fw-bold mb-4">💻 Código de Exemplo</h2>
            <div class="card bg-light">
                <div class="card-body">
                    <pre class="text-gray-700"><code>&lt;!-- Uso básico --&gt;
&lt;x-permission-card 
    :module="[
        'value' => 'usuarios',
        'label' => 'Usuários',
        'color' => 'primary',
        'iconClass' => 'ki-duotone ki-profile-circle',
        'routes' => [
            'users.index' => 'Listar Usuários',
            'users.create' => 'Criar Usuário'
        ]
    ]" 
/&gt;

&lt;!-- Com opções personalizadas --&gt;
&lt;x-permission-card 
    :module="$moduleData"
    :show-actions="true"
    :readonly="false"
    size="large"
    theme="dark"
&gt;
    &lt;!-- Conteúdo personalizado --&gt;
    &lt;div class="alert alert-info"&gt;
        Informação adicional sobre o módulo
    &lt;/div&gt;
&lt;/x-permission-card&gt;</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Parâmetros Disponíveis -->
    <div class="row mb-10">
        <div class="col-12">
            <h2 class="text-gray-800 fw-bold mb-4">📋 Parâmetros Disponíveis</h2>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Parâmetro</th>
                                    <th>Tipo</th>
                                    <th>Padrão</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>module</code></td>
                                    <td>array</td>
                                    <td>[]</td>
                                    <td>Dados do módulo (value, label, color, iconClass, routes)</td>
                                </tr>
                                <tr>
                                    <td><code>show-actions</code></td>
                                    <td>boolean</td>
                                    <td>true</td>
                                    <td>Exibe botões de ação (Criar, Editar, Excluir)</td>
                                </tr>
                                <tr>
                                    <td><code>readonly</code></td>
                                    <td>boolean</td>
                                    <td>false</td>
                                    <td>Desabilita edição (somente visualização)</td>
                                </tr>
                                <tr>
                                    <td><code>size</code></td>
                                    <td>string</td>
                                    <td>default</td>
                                    <td>Tamanho do card (small, default, large)</td>
                                </tr>
                                <tr>
                                    <td><code>theme</code></td>
                                    <td>string</td>
                                    <td>light</td>
                                    <td>Tema do card (light, dark)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Exemplo de JavaScript para trabalhar com o componente
document.addEventListener('DOMContentLoaded', function() {
    // Detectar mudanças em todos os permission cards
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('permission-switch')) {
            const route = e.target.dataset.route;
            const module = e.target.closest('.permission-card').dataset.module;
            
            console.log(`Permissão alterada - Módulo: ${module}, Rota: ${route}, Ativo: ${e.target.checked}`);
            
            // Aqui você pode adicionar lógica personalizada
            // Por exemplo, salvar automaticamente ou validar dependências
        }
    });
});
</script>
@endpush 