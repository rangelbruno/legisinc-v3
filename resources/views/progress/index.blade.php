<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Progresso do Projeto - Sistema de Tramitação Parlamentar 2.0</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .progress-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        .progress-card {
            transition: transform 0.3s ease;
        }
        .progress-card:hover {
            transform: translateY(-5px);
        }
        .module-status {
            position: relative;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .module-status.completed {
            background-color: #dcfce7;
            color: #166534;
        }
        .module-status.planned {
            background-color: #fef3c7;
            color: #92400e;
        }
        .module-status.in_development {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
        }
    </style>
</head>

<body id="kt_body" class="page-bg">
    <div class="d-flex flex-column flex-root">
        <!-- Header -->
        <div class="progress-header">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h1 class="display-4 fw-bolder mb-2">Sistema de Tramitação Parlamentar 2.0</h1>
                        <p class="fs-4 opacity-75">Acompanhamento do Desenvolvimento</p>
                        @if(isset($progressData['lastUpdate']))
                            <p class="fs-6 opacity-50">Última atualização: {{ $progressData['lastUpdate'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container my-5">
            <!-- Overview Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">{{ $progressData['overview']['implemented_modules'] ?? 0 }}</div>
                    <div class="stat-label">Módulos Implementados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $progressData['overview']['percentage'] ?? 0 }}%</div>
                    <div class="stat-label">Progresso Geral</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $progressData['overview']['total_modules'] ?? 0 }}</div>
                    <div class="stat-label">Total de Módulos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $progressData['overview']['base_structure']['percentage'] ?? 0 }}%</div>
                    <div class="stat-label">Estrutura Base</div>
                </div>
            </div>

            <div class="row">
                <!-- Gráfico de Progresso Geral -->
                <div class="col-lg-6 mb-4">
                    <div class="card progress-card">
                        <div class="card-header">
                            <h3 class="card-title">Progresso por Status</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico por Categoria -->
                <div class="col-lg-6 mb-4">
                    <div class="card progress-card">
                        <div class="card-header">
                            <h3 class="card-title">Progresso por Categoria</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cobertura Técnica -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card progress-card">
                        <div class="card-header">
                            <h3 class="card-title">Cobertura Técnica</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="technicalChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Módulos -->
            <div class="row">
                <div class="col-12">
                    <div class="card progress-card">
                        <div class="card-header">
                            <h3 class="card-title">Módulos do Sistema</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nome do Módulo</th>
                                            <th>Categoria</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($progressData['modules'] as $module)
                                            <tr>
                                                <td>{{ $module['number'] }}</td>
                                                <td>{{ $module['name'] }}</td>
                                                <td>
                                                    @switch($module['category'])
                                                        @case('core_business')
                                                            <span class="badge badge-primary">Core Business</span>
                                                            @break
                                                        @case('infrastructure')
                                                            <span class="badge badge-info">Infraestrutura</span>
                                                            @break
                                                        @case('innovation')
                                                            <span class="badge badge-warning">Inovação</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span class="module-status {{ $module['status'] }}">
                                                        @switch($module['status'])
                                                            @case('completed')
                                                                ✅ Completo
                                                                @break
                                                            @case('in_development')
                                                                🚧 Em Desenvolvimento
                                                                @break
                                                            @case('planned')
                                                                📋 Planejado
                                                                @break
                                                        @endswitch
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navegação -->
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <a href="{{ route('api-docs.index') }}" class="btn btn-info btn-lg me-3">
                        <i class="fas fa-code"></i> Documentação da API
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Voltar para Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    
    <script>
        // Configuração dos gráficos
        const progressData = JSON.parse('{!! addslashes(json_encode($progressData)) !!}');
        
        // Gráfico de Status
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Implementados', 'Em Desenvolvimento', 'Planejados'],
                datasets: [{
                    data: [
                        progressData.statistics?.by_status?.implemented?.count || 0,
                        progressData.statistics?.by_status?.in_development?.count || 0,
                        progressData.statistics?.by_status?.planned?.count || 0
                    ],
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de Categoria
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: ['Core Business', 'Infraestrutura', 'Inovação'],
                datasets: [{
                    label: 'Implementados',
                    data: [
                        progressData.statistics?.by_category?.core_business?.implemented || 0,
                        progressData.statistics?.by_category?.infrastructure?.implemented || 0,
                        progressData.statistics?.by_category?.innovation?.implemented || 0
                    ],
                    backgroundColor: '#667eea'
                }, {
                    label: 'Total',
                    data: [
                        progressData.statistics?.by_category?.core_business?.total || 0,
                        progressData.statistics?.by_category?.infrastructure?.total || 0,
                        progressData.statistics?.by_category?.innovation?.total || 0
                    ],
                    backgroundColor: '#e5e7eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Cobertura Técnica
        const technicalCtx = document.getElementById('technicalChart').getContext('2d');
        const technicalChart = new Chart(technicalCtx, {
            type: 'radar',
            data: {
                labels: ['Backend', 'Frontend', 'APIs', 'Database', 'Auth', 'Docker'],
                datasets: [{
                    label: 'Cobertura (%)',
                    data: [
                        progressData.technical?.coverage?.backend || 0,
                        progressData.technical?.coverage?.frontend || 0,
                        progressData.technical?.coverage?.apis || 0,
                        progressData.technical?.coverage?.database || 0,
                        progressData.technical?.coverage?.auth || 0,
                        progressData.technical?.coverage?.docker || 0
                    ],
                    backgroundColor: 'rgba(102, 126, 234, 0.2)',
                    borderColor: '#667eea',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 