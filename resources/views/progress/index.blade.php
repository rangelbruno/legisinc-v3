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
    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .timeline {
            position: relative;
        }
        .timeline-item {
            display: flex;
            position: relative;
            margin-bottom: 0;
        }
        .timeline-line {
            position: absolute;
            left: 20px;
            top: 40px;
            bottom: -20px;
            width: 1px;
            background-color: #e1e5e9;
        }
        .timeline-item:last-child .timeline-line {
            display: none;
        }
        .timeline-icon {
            flex-shrink: 0;
            margin-right: 1rem;
        }
        .timeline-content {
            flex: 1;
            padding-top: 0.25rem;
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

        .gantt-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            overflow-x: auto;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .chart-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 100;
            display: flex;
            gap: 5px;
        }
        
        .chart-controls .btn {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 4px;
        }
        
        .zoom-controls {
            background: rgba(255,255,255,0.95);
            border-radius: 8px;
            padding: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        .gantt-header {
            margin-bottom: 20px;
        }
        
        .gantt-header h4 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .gantt-header p {
            color: #6b7280;
            margin: 0;
        }
        
        /* Custom styling for Frappe Gantt */
        .gantt-container svg {
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }
        
        .gantt-container .bar {
            rx: 6 !important;
            ry: 6 !important;
            stroke: #ffffff;
            stroke-width: 1;
        }
        
        .gantt-container .bar-label {
            fill: #000000 !important;
            font-weight: 900 !important;
            font-size: 14px !important;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.9) !important;
            stroke: rgba(255,255,255,0.8) !important;
            stroke-width: 0.5px !important;
        }
        
        .gantt-container .bar-group {
            cursor: pointer !important;
        }
        
        .gantt-container .bar-group:hover .bar {
            stroke-width: 2 !important;
            filter: brightness(110%);
        }
        
        .gantt-container .bar-group:hover .bar-label {
            font-weight: 800 !important;
            font-size: 15px !important;
        }
        
        /* Melhorias adicionais para legibilidade */
        .gantt-container text.bar-label {
            paint-order: stroke fill;
            stroke: white !important;
            stroke-width: 3px !important;
            stroke-linejoin: round !important;
            fill: #000000 !important;
            font-family: "Inter", Arial, sans-serif !important;
            font-weight: 900 !important;
        }
        
        .gantt-container .bar-wrapper {
            cursor: pointer !important;
        }
        
        .gantt-container .bar-wrapper:hover text.bar-label {
            stroke-width: 4px !important;
            font-size: 15px !important;
        }
        
        @media (max-width: 768px) {
            .progress-header {
                padding: 1.5rem 1rem;
            }
            .progress-header h1 {
                font-size: 1.75rem;
            }
            .progress-header p {
                font-size: 1rem;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .gantt-container {
                padding: 15px;
            }
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
                        <h1 class="display-4 fw-bolder mb-2">{{ $progressData['title'] ?? 'Sistema de Tramitação Parlamentar 2.0' }}</h1>
                        <p class="fs-4 opacity-75">{{ $progressData['subtitle'] ?? 'Acompanhamento do Desenvolvimento' }}</p>
                        @if(isset($progressData['lastUpdate']))
                            <p class="fs-6 opacity-50">Última atualização: {{ $progressData['lastUpdate'] }}</p>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('api-docs.index') }}" class="btn btn-outline-light btn-sm me-2">
                                <i class="fas fa-code"></i> Documentação da API
                            </a>
                            <a href="{{ route('documentation.index') }}" class="btn btn-outline-light btn-sm me-2">
                                <i class="fas fa-book"></i> Documentação do Sistema
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-sign-in-alt"></i> Voltar para Login
                            </a>
                        </div>
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
                    <div class="stat-number">{{ $progressData['overview']['tr_coverage']['percentage'] ?? 0 }}%</div>
                    <div class="stat-label">Cobertura do TR</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $progressData['overview']['base_structure']['percentage'] ?? 0 }}%</div>
                    <div class="stat-label">Estrutura Base</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $progressData['overview']['tr_coverage']['remaining'] ?? 0 }}</div>
                    <div class="stat-label">Módulos Restantes TR</div>
                </div>
            </div>

            <!-- Cronograma de Gantt TR -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card progress-card">
                        <div class="card-header">
                            <h3 class="card-title">🗺️ Cronograma TR - Setembro/Outubro 2025</h3>
                            <div class="card-toolbar d-flex align-items-center gap-2">
                                <span class="badge badge-success">Meta: 2025-10-07</span>
                                
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="changeGanttView('Day')">
                                        <i class="fas fa-calendar-day"></i> Diário
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="changeGanttView('Week')" id="weekViewBtn">
                                        <i class="fas fa-calendar-week"></i> Semanal
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="changeGanttView('Month')">
                                        <i class="fas fa-calendar"></i> Mensal
                                    </button>
                                </div>
                                
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-warning" onclick="toggleGanttFullscreen()" id="ganttFullscreenBtn">
                                        <i class="fas fa-expand"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="exportGanttAsPNG()">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="gantt-container">
                                <div class="gantt-header">
                                    <h4>Cronograma de Desenvolvimento TR</h4>
                                    <p>Planejamento de 5 semanas para implementação dos módulos obrigatórios do Termo de Referência</p>
                                </div>
                                <div id="gantt"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Gráfico de Progresso TR -->
                <div class="col-lg-6 mb-4">
                    <div class="card progress-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Cobertura do TR</h3>
                            <div class="chart-controls">
                                <div class="zoom-controls">
                                    <button class="btn btn-sm btn-outline-primary" onclick="resetChart('trChart')">
                                        <i class="fas fa-home"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="toggleChartType('trChart')">
                                        <i class="fas fa-chart-pie"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="trChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico por Status -->
                <div class="col-lg-6 mb-4">
                    <div class="card progress-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Módulos por Status</h3>
                            <div class="chart-controls">
                                <div class="zoom-controls">
                                    <button class="btn btn-sm btn-outline-primary" onclick="resetChart('statusChart')">
                                        <i class="fas fa-home"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="toggleChartType('statusChart')">
                                        <i class="fas fa-chart-bar"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cobertura Técnica -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card progress-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Cobertura Técnica</h3>
                            <div class="chart-controls">
                                <div class="zoom-controls">
                                    <button class="btn btn-sm btn-outline-primary" onclick="resetChart('technicalChart')">
                                        <i class="fas fa-home"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="toggleChartType('technicalChart')">
                                        <i class="fas fa-chart-area"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" onclick="animateChart('technicalChart')">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </div>
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
                                                <td>
                                                    {{ $module['name'] }}
                                                    @if(isset($module['description']))
                                                        <br><small class="text-muted">{{ $module['description'] }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @switch($module['category'])
                                                        @case('implemented')
                                                            <span class="badge badge-success">Core Implementado</span>
                                                            @break
                                                        @case('new_modules')
                                                            <span class="badge badge-primary">Novos Módulos TR</span>
                                                            @break
                                                        @case('adjustments')
                                                            <span class="badge badge-warning">Ajustes/Finalizar</span>
                                                            @break
                                                        @case('in_development')
                                                            <span class="badge badge-info">Em Desenvolvimento</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span class="module-status {{ $module['status'] }}">
                                                        @switch($module['status'])
                                                            @case('completed')
                                                                ✅ Implementado
                                                                @break
                                                            @case('in_development')
                                                                🚧 Em Desenvolvimento
                                                                @break
                                                            @case('planned')
                                                                📋 Planejado para TR
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

            <!-- Botões de Navegação -->
            
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    
    <script>
        // Configuração dos gráficos
        const progressData = JSON.parse('{!! addslashes(json_encode($progressData)) !!}');
        
        // Gráfico de Cobertura do TR
        const trCtx = document.getElementById('trChart').getContext('2d');
        const trChart = new Chart(trCtx, {
            type: 'doughnut',
            data: {
                labels: ['Implementados TR', 'Restantes TR'],
                datasets: [{
                    data: [
                        progressData.statistics?.tr_coverage?.completed || 9,
                        progressData.statistics?.tr_coverage?.remaining || 8
                    ],
                    backgroundColor: [
                        'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                        'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)'
                    ],
                    borderWidth: 3,
                    borderColor: ['#ffffff', '#ffffff'],
                    hoverBackgroundColor: ['#059669', '#d97706'],
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: '53% do TR concluído',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: 20
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: 2000
                }
            }
        });
        
        // Gráfico de Status
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Implementados', 'Em Desenvolvimento', 'Planejados', 'Ajustes'],
                datasets: [{
                    data: [
                        progressData.statistics?.by_status?.implemented?.count || 9,
                        progressData.statistics?.by_status?.in_development?.count || 7,
                        progressData.statistics?.by_status?.planned?.count || 8,
                        progressData.statistics?.by_status?.adjustments?.count || 1
                    ],
                    backgroundColor: [
                        '#10b981', // Verde - Implementados
                        '#3b82f6', // Azul - Em Desenvolvimento  
                        '#f59e0b', // Amarelo - Planejados
                        '#8b5cf6'  // Roxo - Ajustes
                    ],
                    borderWidth: 3,
                    borderColor: ['#ffffff', '#ffffff', '#ffffff', '#ffffff'],
                    hoverBackgroundColor: ['#059669', '#2563eb', '#d97706', '#7c3aed'],
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 13,
                                weight: 'bold'
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: 1500
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
                    backgroundColor: 'rgba(102, 126, 234, 0.3)',
                    borderColor: '#667eea',
                    borderWidth: 3,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#4f46e5',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 20,
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            color: '#6b7280'
                        },
                        pointLabels: {
                            font: {
                                size: 13,
                                weight: 'bold'
                            },
                            color: '#374151'
                        },
                        grid: {
                            color: 'rgba(107, 114, 128, 0.3)'
                        },
                        angleLines: {
                            color: 'rgba(107, 114, 128, 0.3)'
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });

        // Gráfico de Gantt
        const ganttData = @json($progressData['schedule']['gantt_tasks'] ?? []);
        
        console.log('Frappe Gantt disponível:', typeof Gantt !== 'undefined');
        console.log('Dados do Gantt:', ganttData);
        
        if (ganttData.length > 0) {
            try {
                // Adicionar cores personalizadas aos dados
                const colorMap = {
                    'administrativo': '#10b981',
                    'protocolo': '#3b82f6',
                    'processo_eletronico': '#f59e0b',
                    'normas_juridicas': '#8b5cf6',
                    'plenario_presenca': '#ef4444',
                    'app_plenario': '#06b6d4',
                    'tce_comunicacao': '#f97316',
                    'portal_transparencia': '#84cc16',
                    'testes_integracao': '#ec4899',
                    'homologacao': '#6366f1'
                };
                
                // Aplicar cores aos dados
                ganttData.forEach(task => {
                    task.custom_class = `task-${task.id}`;
                    task.color = colorMap[task.id] || '#667eea';
                });

                const gantt = new Gantt("#gantt", ganttData, {
                    header_height: 50,
                    column_width: 30,
                    step: 24,
                    view_modes: ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'],
                    bar_height: 22,
                    bar_corner_radius: 4,
                    arrow_curve: 5,
                    padding: 18,
                    view_mode: 'Week',
                    date_format: 'YYYY-MM-DD',
                    popup_trigger: 'none', // Desabilitar popup completamente
                    custom_popup_html: null,
                    on_click: function (task) {
                        console.log('Clique capturado pelo Gantt:', task.name);
                        showTaskDetails(task);
                        return false; // Prevenir outros handlers
                    },
                    on_date_change: function(task, start, end) {
                        console.log('Data alterada:', task, start, end);
                    },
                    on_progress_change: function(task, progress) {
                        console.log('Progresso alterado:', task, progress);
                    },
                    on_view_change: function(mode) {
                        console.log('Visão alterada:', mode);
                        // Reaplicar cores após mudança de view
                        setTimeout(() => {
                            applyGanttColors();
                        }, 300);
                    }
                });
                console.log('Gantt criado com sucesso');
                
                // Armazenar instância do Gantt globalmente
                window.ganttInstance = gantt;
                
                // Aplicar cores após renderização
                setTimeout(() => {
                    applyGanttColors();
                    setupGanttEventDelegation();
                }, 1000);
                
            } catch (error) {
                console.error('Erro ao criar Gantt:', error);
                document.getElementById('gantt').innerHTML = '<div class="text-center text-danger py-4">Erro ao carregar cronograma: ' + error.message + '</div>';
            }

            // Marcar botão de visão semanal como ativo por padrão
            document.getElementById('weekViewBtn').classList.add('active');
        } else {
            document.getElementById('gantt').innerHTML = '<div class="text-center text-muted py-4">Nenhum dado de cronograma disponível</div>';
        }
        
        // Fallback: Se Frappe Gantt não carregar, mostrar cronograma em tabela
        setTimeout(() => {
            if (document.querySelector('#gantt svg') === null && ganttData.length > 0) {
                console.log('Gantt não renderizou, usando fallback de tabela');
                let tableHTML = `
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i> 
                        Exibindo cronograma em formato de tabela (fallback)
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Módulo</th>
                                    <th>Data Início</th>
                                    <th>Data Fim</th>
                                    <th>Progresso</th>
                                    <th>Dependências</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                ganttData.forEach(task => {
                    const startDate = new Date(task.start).toLocaleDateString('pt-BR');
                    const endDate = new Date(task.end).toLocaleDateString('pt-BR');
                    const deps = task.dependencies ? task.dependencies.replace(/,/g, ', ') : 'Nenhuma';
                    
                    tableHTML += `
                        <tr>
                            <td>
                                <strong>${task.name}</strong>
                                <br><small class="text-muted">${task.description}</small>
                            </td>
                            <td>${startDate}</td>
                            <td>${endDate}</td>
                            <td>
                                <div class="progress" style="width: 80px; height: 20px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: ${task.progress}%" 
                                         aria-valuenow="${task.progress}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        ${task.progress}%
                                    </div>
                                </div>
                            </td>
                            <td><small>${deps}</small></td>
                        </tr>
                    `;
                });
                
                tableHTML += `
                            </tbody>
                        </table>
                    </div>
                `;
                
                document.getElementById('gantt').innerHTML = tableHTML;
            }
        }, 2000);
        
        // Variáveis globais para armazenar instâncias dos gráficos
        window.chartInstances = {
            trChart: trChart,
            statusChart: statusChart,
            technicalChart: technicalChart
        };
        
        // Função para resetar gráfico
        function resetChart(chartId) {
            const chart = window.chartInstances[chartId];
            if (chart) {
                chart.resetZoom();
                chart.update('none');
            }
        }
        
        // Função para alternar tipo de gráfico
        function toggleChartType(chartId) {
            const chart = window.chartInstances[chartId];
            if (!chart) return;
            
            const currentType = chart.config.type;
            let newType;
            
            switch (chartId) {
                case 'trChart':
                case 'statusChart':
                    newType = currentType === 'doughnut' ? 'bar' : 'doughnut';
                    break;
                case 'technicalChart':
                    newType = currentType === 'radar' ? 'line' : 'radar';
                    break;
            }
            
            if (newType) {
                chart.config.type = newType;
                
                // Ajustar opções específicas por tipo
                if (newType === 'bar') {
                    chart.options.scales = {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: { size: 12, weight: 'bold' }
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 12, weight: 'bold' }
                            }
                        }
                    };
                } else if (newType === 'doughnut') {
                    delete chart.options.scales;
                } else if (newType === 'line') {
                    chart.options.scales = {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 20,
                                font: { size: 12, weight: 'bold' }
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 12, weight: 'bold' }
                            }
                        }
                    };
                }
                
                chart.update('active');
            }
        }
        
        // Função para animar gráfico
        function animateChart(chartId) {
            const chart = window.chartInstances[chartId];
            if (chart) {
                chart.update('active');
            }
        }
        
        // Adicionar interatividade com zoom usando mouse wheel
        Object.values(window.chartInstances).forEach(chart => {
            if (chart && chart.canvas) {
                chart.canvas.addEventListener('wheel', function(e) {
                    e.preventDefault();
                    
                    const rect = chart.canvas.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    const zoom = e.deltaY < 0 ? 1.1 : 0.9;
                    
                    // Para gráficos que suportam zoom
                    if (chart.config.type === 'line' || chart.config.type === 'bar') {
                        const scaleX = chart.scales.x;
                        const scaleY = chart.scales.y;
                        
                        if (scaleX && scaleY) {
                            const xValue = scaleX.getValueForPixel(x);
                            const yValue = scaleY.getValueForPixel(y);
                            
                            // Implementar zoom básico
                            chart.update('none');
                        }
                    }
                }, { passive: false }); // Explicitly set passive to false since we need preventDefault
            }
        });
        
        // Função para aplicar cores ao Gantt
        function applyGanttColors() {
            const colorMap = {
                'administrativo': '#10b981',
                'protocolo': '#3b82f6', 
                'processo_eletronico': '#f59e0b',
                'normas_juridicas': '#8b5cf6',
                'plenario_presenca': '#ef4444',
                'app_plenario': '#06b6d4',
                'tce_comunicacao': '#f97316',
                'portal_transparencia': '#84cc16',
                'testes_integracao': '#ec4899',
                'homologacao': '#6366f1'
            };
            
            // Limpar listeners anteriores para evitar duplicação
            document.querySelectorAll('.bar-group, .bar-wrapper, .bar, .bar-progress, .bar-label').forEach(el => {
                const newEl = el.cloneNode(true);
                if (el.parentNode) {
                    el.parentNode.replaceChild(newEl, el);
                }
            });
            
            // Aplicar cores e eventos de clique
            ganttData.forEach((task, index) => {
                // Buscar por elementos usando diferentes seletores
                const barWrappers = document.querySelectorAll('.bar-wrapper');
                const barGroups = document.querySelectorAll('.bar-group');
                
                if (barWrappers[index] || barGroups[index]) {
                    const wrapper = barWrappers[index];
                    const group = barGroups[index];
                    const targetElement = wrapper || group;
                    
                    if (!targetElement) return;
                    
                    const progressBar = targetElement.querySelector('.bar-progress');
                    const bar = targetElement.querySelector('.bar');
                    const label = targetElement.querySelector('.bar-label');
                    
                    // Capturar a cor no escopo correto
                    const taskColor = colorMap[task.id] || '#667eea';
                    
                    if (progressBar && bar) {
                        progressBar.style.fill = taskColor;
                        bar.style.fill = taskColor;
                        bar.style.opacity = '0.3';
                        bar.style.stroke = '#ffffff';
                        bar.style.strokeWidth = '2';
                    }
                    
                    // Melhorar legibilidade do texto
                    if (label) {
                        label.style.fill = '#000000';
                        label.style.fontWeight = '900';
                        label.style.fontSize = '14px';
                        label.style.textShadow = '1px 1px 1px rgba(255,255,255,0.8)';
                        label.style.stroke = 'rgba(255,255,255,0.8)';
                        label.style.strokeWidth = '0.5px';
                    }
                    
                    // Função para hover enter (closure para capturar taskColor)
                    function handleMouseEnter() {
                        if (progressBar && bar) {
                            try {
                                progressBar.style.fill = shadeColor(taskColor, -20);
                                bar.style.opacity = '0.6';
                                bar.style.strokeWidth = '3';
                            } catch(e) {
                                console.warn('Erro no hover enter:', e);
                            }
                        }
                        if (label) {
                            label.style.fontSize = '15px';
                            label.style.fontWeight = '900';
                        }
                    }
                    
                    // Função para hover leave (closure para capturar taskColor)
                    function handleMouseLeave() {
                        if (progressBar && bar) {
                            progressBar.style.fill = taskColor;
                            bar.style.opacity = '0.3';
                            bar.style.strokeWidth = '2';
                        }
                        if (label) {
                            label.style.fontSize = '14px';
                            label.style.fontWeight = '900';
                        }
                    }
                    
                    // Função para clique (closure para capturar task)
                    function handleClick(e) {
                        e.stopPropagation();
                        console.log('Clique detectado no elemento:', task.name);
                        showTaskDetails(task);
                    }
                    
                    // Adicionar eventos aos elementos
                    [targetElement, progressBar, bar, label].forEach(element => {
                        if (element) {
                            element.style.cursor = 'pointer';
                            element.addEventListener('click', handleClick);
                            element.addEventListener('mouseenter', handleMouseEnter);
                            element.addEventListener('mouseleave', handleMouseLeave);
                        }
                    });
                }
            });
            
            console.log('Cores e eventos aplicados ao Gantt');
        }
        
        // Função para configurar delegação de eventos no Gantt
        function setupGanttEventDelegation() {
            const ganttContainer = document.getElementById('gantt');
            if (!ganttContainer) return;
            
            // Remover listeners anteriores
            ganttContainer.removeEventListener('click', handleGanttClick);
            
            // Adicionar listener delegado
            ganttContainer.addEventListener('click', handleGanttClick);
            
            console.log('Event delegation configurado para o Gantt');
        }
        
        // Handler para cliques no Gantt
        function handleGanttClick(event) {
            console.log('Clique detectado no Gantt container');
            
            // Encontrar o elemento bar-wrapper mais próximo
            let element = event.target;
            let barWrapper = null;
            let attempts = 0;
            
            while (element && attempts < 10) {
                if (element.classList && (element.classList.contains('bar-wrapper') || 
                    element.classList.contains('bar-group') ||
                    element.classList.contains('bar') ||
                    element.classList.contains('bar-progress') ||
                    element.classList.contains('bar-label'))) {
                    barWrapper = element.closest('.bar-wrapper') || element;
                    break;
                }
                element = element.parentElement;
                attempts++;
            }
            
            if (barWrapper) {
                // Extrair ID da tarefa do atributo data-id ou class
                let taskId = barWrapper.getAttribute('data-id');
                if (!taskId) {
                    // Tentar extrair do className
                    const classes = barWrapper.className.split(' ');
                    const taskClass = classes.find(cls => cls.startsWith('task-'));
                    if (taskClass) {
                        taskId = taskClass.replace('task-', '');
                    }
                }
                
                if (taskId) {
                    console.log('Task ID encontrado:', taskId);
                    
                    // Encontrar a tarefa nos dados
                    const task = ganttData.find(t => t.id === taskId);
                    if (task) {
                        console.log('Tarefa encontrada:', task.name);
                        showTaskDetails(task);
                    } else {
                        console.log('Tarefa não encontrada nos dados');
                    }
                } else {
                    console.log('Task ID não encontrado no elemento');
                }
            } else {
                console.log('Elemento bar-wrapper não encontrado');
            }
        }
        
        // Função auxiliar para escurecer cor
        function shadeColor(color, percent) {
            var R = parseInt(color.substring(1,3),16);
            var G = parseInt(color.substring(3,5),16);
            var B = parseInt(color.substring(5,7),16);
            
            R = parseInt(R * (100 + percent) / 100);
            G = parseInt(G * (100 + percent) / 100);
            B = parseInt(B * (100 + percent) / 100);
            
            R = (R<255)?R:255;  
            G = (G<255)?G:255;  
            B = (B<255)?B:255;  
            
            R = (R>0)?R:0;  
            G = (G>0)?G:0;  
            B = (B>0)?B:0;  
            
            var RR = ((R.toString(16).length==1)?"0"+R.toString(16):R.toString(16));
            var GG = ((G.toString(16).length==1)?"0"+G.toString(16):G.toString(16));
            var BB = ((B.toString(16).length==1)?"0"+B.toString(16):B.toString(16));
            
            return "#"+RR+GG+BB;
        }
        
        // Função para mostrar detalhes da tarefa no SweetAlert
        function showTaskDetails(task) {
            const start_date = task._start ? task._start.toLocaleDateString('pt-BR') : 'N/A';
            const end_date = task._end ? task._end.toLocaleDateString('pt-BR') : 'N/A';
            const duration = task._start && task._end ? 
                Math.ceil((task._end - task._start) / (1000 * 60 * 60 * 24)) + ' dias' : 'N/A';
            
            const colorMap = {
                'administrativo': '#10b981',
                'protocolo': '#3b82f6',
                'processo_eletronico': '#f59e0b',
                'normas_juridicas': '#8b5cf6',
                'plenario_presenca': '#ef4444',
                'app_plenario': '#06b6d4',
                'tce_comunicacao': '#f97316',
                'portal_transparencia': '#84cc16',
                'testes_integracao': '#ec4899',
                'homologacao': '#6366f1'
            };
            
            const taskColor = colorMap[task.id] || '#667eea';
            
            Swal.fire({
                title: task.name,
                html: `
                    <div style="text-align: left; line-height: 1.6;">
                        <div style="background: ${taskColor}; color: white; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                            <strong>📋 ${task.name}</strong>
                        </div>
                        
                        <p><strong>📝 Descrição:</strong><br>
                        ${task.description || 'Sem descrição disponível'}</p>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 15px 0;">
                            <div style="background: #f3f4f6; padding: 10px; border-radius: 6px;">
                                <strong>📅 Início:</strong><br>${start_date}
                            </div>
                            <div style="background: #f3f4f6; padding: 10px; border-radius: 6px;">
                                <strong>🏁 Término:</strong><br>${end_date}
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 15px 0;">
                            <div style="background: #e0f2fe; padding: 10px; border-radius: 6px;">
                                <strong>⏱️ Duração:</strong><br>${duration}
                            </div>
                            <div style="background: #f0f9ff; padding: 10px; border-radius: 6px;">
                                <strong>📈 Progresso:</strong><br>${task.progress}%
                            </div>
                        </div>
                        
                        ${task.dependencies && task.dependencies.length > 0 ? `
                            <div style="background: #fef3c7; padding: 10px; border-radius: 6px; margin: 15px 0;">
                                <strong>🔗 Dependências:</strong><br>
                                <small>${typeof task.dependencies === 'string' ? task.dependencies.replace(/,/g, ', ') : task.dependencies}</small>
                            </div>
                        ` : ''}
                        
                        <div style="margin-top: 15px; font-size: 12px; color: #6b7280;">
                            💡 Este módulo faz parte do Cronograma TR 2025
                        </div>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Fechar',
                confirmButtonColor: taskColor,
                width: 600,
                padding: '20px',
                background: '#fff',
                backdrop: 'rgba(0,0,0,0.4)'
            });
        }
        
        // Funções específicas para o Gantt
        function changeGanttView(mode) {
            if (window.ganttInstance) {
                window.ganttInstance.change_view_mode(mode);
                
                // Atualizar botões ativos
                document.querySelectorAll('.btn-group button').forEach(btn => {
                    btn.classList.remove('active');
                });
                event.target.classList.add('active');
                
                // Reaplicar cores após mudança de view
                setTimeout(() => {
                    applyGanttColors();
                }, 500);
            }
        }
        
        function toggleGanttFullscreen() {
            const ganttContainer = document.querySelector('.gantt-container').parentElement.parentElement;
            const btn = document.getElementById('ganttFullscreenBtn');
            
            if (!document.fullscreenElement) {
                ganttContainer.requestFullscreen().then(() => {
                    btn.innerHTML = '<i class="fas fa-compress"></i>';
                    ganttContainer.style.position = 'fixed';
                    ganttContainer.style.top = '0';
                    ganttContainer.style.left = '0';
                    ganttContainer.style.width = '100vw';
                    ganttContainer.style.height = '100vh';
                    ganttContainer.style.zIndex = '9999';
                    ganttContainer.style.backgroundColor = 'white';
                }).catch(err => {
                    console.log('Erro ao entrar em fullscreen:', err);
                });
            } else {
                document.exitFullscreen().then(() => {
                    btn.innerHTML = '<i class="fas fa-expand"></i>';
                    ganttContainer.style.position = '';
                    ganttContainer.style.top = '';
                    ganttContainer.style.left = '';
                    ganttContainer.style.width = '';
                    ganttContainer.style.height = '';
                    ganttContainer.style.zIndex = '';
                    ganttContainer.style.backgroundColor = '';
                });
            }
        }
        
        function exportGanttAsPNG() {
            const ganttSvg = document.querySelector('.gantt-container svg');
            if (ganttSvg) {
                // Criar canvas temporário
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const data = new XMLSerializer().serializeToString(ganttSvg);
                const svg = new Blob([data], {type: 'image/svg+xml;charset=utf-8'});
                const url = URL.createObjectURL(svg);
                
                const img = new Image();
                img.onload = function() {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);
                    
                    // Download
                    const link = document.createElement('a');
                    link.download = 'cronograma-tr-gantt.png';
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                    
                    URL.revokeObjectURL(url);
                };
                img.src = url;
            }
        }
        
        // Adicionar gradiente SVG para o Gantt
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const ganttSvg = document.querySelector('.gantt-container svg');
                if (ganttSvg) {
                    const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
                    const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
                    gradient.setAttribute('id', 'ganttGradient');
                    gradient.setAttribute('x1', '0%');
                    gradient.setAttribute('y1', '0%');
                    gradient.setAttribute('x2', '100%');
                    gradient.setAttribute('y2', '0%');
                    
                    const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
                    stop1.setAttribute('offset', '0%');
                    stop1.setAttribute('stop-color', '#667eea');
                    
                    const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
                    stop2.setAttribute('offset', '100%');
                    stop2.setAttribute('stop-color', '#764ba2');
                    
                    gradient.appendChild(stop1);
                    gradient.appendChild(stop2);
                    defs.appendChild(gradient);
                    ganttSvg.insertBefore(defs, ganttSvg.firstChild);
                }
            }, 3000);
        });
    </script>
</body>
</html> 